<?php




class CalculosCredito {

    private $_credito;

    function CalculosCredito(Credito $credito) {
        $this->_credito = $credito;
    }

    function getDetalleItemResult($fecha) {
        $fe = new FiltroEventos($this->_credito->getEventos());
        $recuperosCollection = $fe->getEventosTipo(EVENTO_RECUPERO, false,$fecha);
        $result = new DetalleItemResult();

        foreach ($recuperosCollection as $recupero) {
            $itemCollection = $recupero->getRecuperoItems();
            foreach ($itemCollection as $item) {
                $monto = round($item->getMonto(),2);
                switch ($item->getTipo()) {
                    case PAGO_CAPITAL: $result->capital += $monto;
                        break;
                    case PAGO_COMPENSATORIO: $result->compensatorio += $monto;
                        break;
                    case PAGO_IVA_COMPENSATORIO: $result->compensatorio_iva += $monto;
                        break;
                    case PAGO_MORATORIO: $result->moratorio += $monto;
                        break;
                    case PAGO_IVA_MORATORIO: $result->moratorio_iva += $monto;
                        break;
                    case PAGO_PUNITORIO: $result->punitorio += $monto;
                        break;
                    case PAGO_IVA_PUNITORIO: $result->punitorio_iva += $monto;
                        break;
                }
            }
        }
        return $result;
    }
    
    function payDeuda($fecha, $monto = 0){
        
        $recuperoItemCollection = new RecuperoItemCollection();
        $cuotasCollection = $this->_credito->getCuotas();
        
        $montoDisponible = $monto;
        $eventoRecupero = new EventoRecupero($fecha, $monto);
        
        //se recorren las cuotas
        $i = 0;
        
        foreach($cuotasCollection as $cuota){
            //se ignoran las cuotas ya canceladas
            if ($cuota->getEstadoPago()===ESTADO_CUOTA_CANCELADA)
                continue;
            
            
            //se calcula el estado de la cuota al momento de pago ya que puede haber modificado
            //sus condiciones en base al cambio de fecha producido por un adelanto de capital
            
            $calculosCuota = new CalculosCuota($cuota);

            $calculosCuota->makeFIDEstado($fecha, TIPO_DEVENGAMIENTO_FORZAR_CUOTA_ACTUAL);

            //obtenemos informacion de lo devengado
            $estadoCuota = $cuota->getCuotaEstado();
                        
            //obtenemos lo pagado hasta el momento
            $calculoCuota = new CalculosCuota($cuota);
            $detallesCancelados = $calculoCuota->getRecuperoItemResult($fecha);


            //obtenemos montos a pagar por cada concepto en esta cuota
            $pagos[PAGO_GASTOS] = $estadoCuota->gasto - $detallesCancelados->gasto;
            
            $pagos[PAGO_IVA_MORATORIO] = $estadoCuota->moratorio_iva - $detallesCancelados->moratorio_iva;
            $pagos[PAGO_IVA_PUNITORIO] = $estadoCuota->punitorio_iva - $detallesCancelados->punitorio_iva;
            
            $descuentoBonificadoIva = $estadoCuota->bonificacion_iva ;
            $pagos[PAGO_IVA_COMPENSATORIO] = $estadoCuota->compensatorio_iva - $detallesCancelados->compensatorio_iva - $descuentoBonificadoIva ;
            
            $pagos[PAGO_MORATORIO] = $estadoCuota->moratorio - $detallesCancelados->moratorio;
            $pagos[PAGO_PUNITORIO] = $estadoCuota->punitorio - $detallesCancelados->punitorio;
            
            $descuentoBonificado = $estadoCuota->bonificacion ;
            $pagos[PAGO_COMPENSATORIO] = $estadoCuota->compensatorio - $detallesCancelados->compensatorio - $descuentoBonificado;
            $pagos[PAGO_CAPITAL] = $estadoCuota->capital - $detallesCancelados->capital;
            
            foreach($pagos as $key=>$value){
                //el monto se va restando del valor
                //si el monto a pagar es mayor al disponible solo se paga el monto disponible
                $montoPagoItem = $value > $montoDisponible ? $montoDisponible : $value;
                $montoPagoItem = $montoPagoItem < 0 ? 0 : $montoPagoItem;
                
                $itemsPago = array(
                    PAGO_PUNITORIO=>'punitorio',
                    PAGO_MORATORIO=>'moratorio',
                    PAGO_COMPENSATORIO=>'compensatorio',
                    PAGO_IVA_PUNITORIO=>'iva punitorio',
                    PAGO_IVA_MORATORIO=>'iva moratorio',
                    PAGO_IVA_COMPENSATORIO=>'iva compensatorio',
                    PAGO_GASTOS=>'gastos',
                    PAGO_CAPITAL=>'capital'
                    );
                $pago = new RecuperoItem($eventoRecupero, $cuota, $key, $montoPagoItem );
                $recuperoItemCollection->addRecuperoItem($pago);

                $montoDisponible -= $montoPagoItem;
                //evaluamos si la cuota tiene capital
                if ($cuota->getCapital() && $key===PAGO_CAPITAL){

                    //si tiene capital se evalua si se ha completado el capital para cancelar la cuota
                    if ($value===$montoPagoItem){
                        
                        //adelantamos fecha de cuota
                        $cuota->setEstadoPago(ESTADO_CUOTA_CANCELADA);
                        
                        //se deberia verificar las condiciones para permitir el cambio de fecha
                        if ($fecha < $cuota->getRango()->getEnd()){
                            $calculosCuota->changeVencimientoCuota($fecha);
                        }
                    }
                }

                if (!$cuota->getCapital() && $key===PAGO_COMPENSATORIO){
                    if ($value===$montoPagoItem){
                        $cuota->setEstadoPago(ESTADO_CUOTA_CANCELADA);
                    }
                }
            }

            $eventoRecupero->setRecuperoItems($recuperoItemCollection);        
            $eventoRecupero->updated();        
            //se cancela segun el orden especifico de FID
            
        }
        return $eventoRecupero;
         
    }

    function getSaldoCapital($fecha = false, $log = false) {

        $fe = new FiltroEventos($this->_credito->getEventos());
        $fecha = $fecha ? $fecha : NO_FECHA;

        //MODIFICADO 15-08-2013
        $capital_inicial = 0;


        //obtenemos todas las cuotas que hayan iniciado
        $cuotas = array();
        $FECHA_VENCIMIENTO_ULTIMA_CUOTA = 0;
        $FECHA_INICIO_ULTIMA_CUOTA = 0;
        $cuotasCopia = $this->_credito->getCuotas();
        $cantidadCuotasTotal = $cuotasCopia->size();
        $keyCuotaBack = $cuotasCopia->key();
        foreach ($cuotasCopia as $cuota) {
            $cuotas[] = $cuota;
            $FECHA_VENCIMIENTO_ULTIMA_CUOTA = $cuota->getEventoVencimiento()->getFecha();
            $FECHA_INICIO_ULTIMA_CUOTA = $cuota->getRango()->getStart();
            if ($FECHA_VENCIMIENTO_ULTIMA_CUOTA > $fecha) {
                break;
            }
        }
        $cuotasCopia->setKey($keyCuotaBack);

        $cuotasCollectionFecha = new CuotasCollection($cuotas);
        $desembolsosCollectionFecha = $fe->getEventosTipo(EVENTO_DESEMBOLSO, false, $FECHA_VENCIMIENTO_ULTIMA_CUOTA);


        $desembolsos = array();
        $AMORTIZACION_TEORICA_ACUMULADA = 0;

        //RECORREMOS CADA UNA DE LAS CUOTAS
        $audi = "";

        $AMORTIZACION_CUOTA = 0;
        $DESEMBOLSOS_ACUMULADOS = 0;
        $AMORTIZACION_CUOTA_ACTUAL = 0;
        $SALDO_TEORICO = 0;

        $contadorCuota = 0;
        $cuotasCollectionFecha ->rewind();
        foreach ($cuotasCollectionFecha as $cuota) {
                        

            $fAmortizacionCuota = 0;
            $fDesembolsos = 0;
           // $AMORTIZACION_TEORICA_ACUMULADA = $cuota->getCapital();
            
            //buscamos desemmbolsos
            foreach ($desembolsosCollectionFecha as $desembolso) {

                //anteriores dentro de la fecha la cuota
                if ($desembolso->getFecha() <= $cuota->getEventoVencimiento()->getFecha() &&
                        $desembolso->getFecha() <= $fecha  && $desembolso->getFecha() >= $cuota->getRango()->getStart()) {
                    $fDesembolsos += $desembolso->getTotal();
                }
            }
             
            $DESEMBOLSOS_ACUMULADOS += $fDesembolsos;
            if ($cuota->getCapital() > 0) {
                
                //$AMORTIZACION_TEORICA_ACUMULADA = $AMORTIZACION_TEORICA_ACUMULADA  > 0 ? $AMORTIZACION_TEORICA_ACUMULADA  : $cuota->getCapital() ;
                //cuotas restantes
                $divisor = $cuota->getCuotasRestantes();
                $AMORTIZACION_CUOTA = ( $DESEMBOLSOS_ACUMULADOS - $AMORTIZACION_TEORICA_ACUMULADA) / ($divisor );
                $fAmortizacionCuota = $AMORTIZACION_CUOTA;
            } else {
                $AMORTIZACION_CUOTA = 0;
            }

            $AMORTIZACION_CUOTA_ACTUAL = $fAmortizacionCuota;

            $SALDO_TEORICO = $DESEMBOLSOS_ACUMULADOS - $AMORTIZACION_TEORICA_ACUMULADA;
            $AMORTIZACION_TEORICA_ACUMULADA += $AMORTIZACION_CUOTA;
            $contadorCuota ++;
            
        }

        //la ultima cuota de las seleccionadas es la cuota de la fecha actual
        $cuota = $cuotasCollectionFecha->last();

        //  $AMORTIZACION_CUOTA_ACTUAL = $cuota['E_AMORTIZACION'] == 0 ? $AMORTIZACION_CUOTA_ACTUAL : $cuota['E_AMORTIZACION'];

        $desembolsosCollection = $fe->getEventosTipo(array(EVENTO_DESEMBOLSO));
        $total_desembolso_real = 0;
        $total_desembolso_teorico = 0;
        foreach ($desembolsosCollection as $desembolso) {

            //DESEMBOLSO A LA FECHA
            if ($desembolso->getFecha() <= $fecha) {
                
                $total_desembolso_real += $desembolso->getTotal();
            }

            //DESEMBOLSO AL FINAL DE LA CUOTA
            if ($desembolso->getFecha() <= $cuota->getEventoVencimiento()->getFecha()) {
                $total_desembolso_teorico += $desembolso->getTotal();
            }
        }
        $recupero = $this->getDetalleItemResult($fecha);

        $total_pagos = $recupero->capital;

        $SALDO_CAPITAL = $total_desembolso_real - $total_pagos;
        $SALDO_TEORICO_PAGO = $SALDO_TEORICO - $total_pagos;
        $SALDO = $SALDO_TEORICO_PAGO < $SALDO_CAPITAL ? $SALDO_TEORICO : $SALDO_CAPITAL;

        $rtn = array(
            "DESEMBOLSOS_CUOTA" => $total_desembolso_teorico,
            "DESEMBOLSOS" => $total_desembolso_real,
            "INICIAL" => $capital_inicial,
            "PAGOS" => $total_pagos,
            "AMORTIZACION_CUOTA" => $AMORTIZACION_CUOTA_ACTUAL,
            "SALDO" => $SALDO,
            "BASE_CALCULO" => $SALDO_TEORICO < $SALDO_CAPITAL ? $SALDO_TEORICO : $SALDO_CAPITAL,
            "SALDO_REAL" => $SALDO_CAPITAL,
            "SALDO_TEORICO" => $SALDO_TEORICO,
            "TIPO" => $SALDO_TEORICO < $SALDO_CAPITAL ? "T" : "R",
            "FECHA" => date("d/m/Y", $fecha));
        return $rtn;
    }

}

class EventoFactory {

    var $_evento;

    function getEvento() {
        return $this->_evento;
    }

    function EventoFactory($cuotasCollection, $evento) {

        switch ($evento['TIPO']) {
            case EVENTO_INICIAL:

                $moratorio = $evento['POR_INT_MORATORIO'];
                $compensatorio = $evento['POR_INT_COMPENSATORIO'];
                $punitorio = $evento['POR_INT_PUNITORIO'];
                $bonificacion = $evento['POR_INT_SUBSIDIO'];

                $this->_evento = new EventoInicial($evento['FECHA'], $compensatorio, $punitorio, $moratorio, $bonificacion);
                $this->_evento->setID($evento['ID']);
                break;

            case EVENTO_DESEMBOLSO:

                $this->_evento = new EventoDesembolso($evento['FECHA']+1, $evento['ASOC']['MONTO']);
                $this->_evento->setID($evento['ID']);
                break;

            case EVENTO_TASA:
                //print_array($evento);
                $moratorio = $evento['ASOC']['MORATORIO'];
                $compensatorio = $evento['ASOC']['COMPENSATORIO'];
                $punitorio = $evento['ASOC']['PUNITORIO'];
                $bonificacion = $evento['ASOC']['SUBSIDIO'];
                $this->_evento = new EventoTasaModificacion($evento['FECHA']+1, $compensatorio, $punitorio, $moratorio, $bonificacion);
                $this->_evento->setID($evento['ID']);
                break;

            case EVENTO_VENCIMIENTO:

                break;

            case EVENTO_RECUPERO:
                $recuperoItemCollection = new RecuperoItemCollection();

                $this->_evento = new EventoRecupero($evento['FECHA']+1, 0);

                foreach ($evento['ASOC'] as $item) {
                    $cuota = $cuotasCollection->getCuotaByCuotasRestante($item['CUOTAS_RESTANTES']);
                    $pago = new RecuperoItem($this->_evento, $cuota, $item['ID_TIPO'], $item['MONTO']);

                    $recuperoItemCollection->addRecuperoItem($pago);
                }
                $this->_evento->setRecuperoItems($recuperoItemCollection);
                $this->_evento->setID($evento['ID']);
                break;
        }
    }

}

?>
