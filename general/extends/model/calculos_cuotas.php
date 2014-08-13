<?php


class CalculosCuota {

    private $_cuota;
    private $_credito;
    private $_eventos;
    

    /**
     * 
     * @param Cuota $cuota
     */
    function CalculosCuota(Cuota $cuota) {
        $this->_cuota = $cuota;
        $this->_credito = $cuota->getCredito();
        $this->_eventos = $this->_credito->getEventos();
    }
    
    /**
     * Cambia el vencimiento de la fecha actual y modifica la fecha de inicio de la siguiente
     * @param int $fecha
     */
    function changeVencimientoCuota($fecha){
        $rango = $this->_cuota->getRango();
        $inicio = $rango->getStart();
        $fin = $rango->getEnd();
        
        $this->_cuota->setRango(new Rango($inicio, $fecha));
        $keyCuotaBackup = $this->_credito->getCuotas()->key();
        $cuotasCollection = $this->_credito->getCuotas();
        foreach($cuotasCollection as $cuota){
            if ($cuota->getRango()->getStart()===$fin){
                $cuota->setRango(new Rango($fecha, $cuota->getRango()->getEnd()));
                break;
            }
        }
        $this->_credito->getCuotas()->setKey($keyCuotaBackup);
    }

    /**
     * 
     * Retorna los pagos realizados en esta cuota
     * @param int $fecha
     * @return \DetalleItemResult
     */
    function getRecuperoItemResult($fecha = false) {
        $itemCollection = $this->_cuota->getRecuperoItems();
        $result = new DetalleItemResult();
        foreach ($itemCollection as $item) {
            $fecha_evento = $item->getEventoRecupero()->getFecha();
            if (!$fecha || $fecha_evento <= $fecha) {
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

    private function _getfilteredFIDEventos($fecha, $tipoDevengamiento = TIPO_DEVENGAMIENTO_AUTO) {
        $fechaCalculo = $fecha;
        $bEnvio = false;
        $bPosterior = false;
        $fechaEnvio = $this->_cuota->getFechaEnvio();

        $tipoCredito = $this->_credito->getTipoCredito();
        switch ($tipoDevengamiento) {
            case TIPO_DEVENGAMIENTO_AUTO:break;
            case TIPO_DEVENGAMIENTO_DEVENGAR_A_FECHA:break;
            case TIPO_DEVENGAMIENTO_FORZAR_CUOTA_ACTUAL:

                //solo aplicable a microcreditos
                if ($tipoCredito == TIPO_MICROCREDITO) {

                    
                    //solo aplica para una fecha que este dentro de la cuota
                    if ($fecha <= $this->_cuota->getRango()->getEnd() && 
                          $fecha >= $this->_cuota->getRango()->getStart()  
                            ) {
                        
                        //la fecha de calculo es al final de la cuota
                        $bEnvio = true;
                    }
                }
                break;

            case TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO:
                $bPosterior = true;
                break;
        }



        $this->_eventos->rewind();
        //buscamos los eventos desde el inicio de la cuota y continuamos con el loop
        //hasta que la cuota este cancelada o hasta el ultimo evento
        //filtrando segun politica de fid
        $eventosArray = array();
        foreach ($this->_eventos as $evento) {
            if ($evento->getFecha() >= $this->_cuota->getRango()->getStart() 
                    ) {
                
                if (!($evento->getFecha() <= $fecha || $bPosterior) ){
                    break;
                }
                
                $badd = true;

                //sacamos los eventos que no tendran relevancia
                if ($evento->getTipo() === EVENTO_GASTO) {
                    $badd = false;
                }


                //si ocurre un evento antes que termine la cuota y despues
                //de la fecha de envio se cancela el envio;
                if ($bEnvio) {
                    
                    if (!($evento->getFecha() > $fechaEnvio &&
                         $fechaEnvio < $this->_cuota->getRango()->getEnd())) {
                        //s cancela con la fecha de envio en el vencimiento
                        
                        $this->_cuota->setFechaEnvio($fechaCalculo);
                    }
                }


                if ($badd) {
                    $eventosArray[] = $evento;
                }
            }
        }
        $eventosCollection = new EventosCollection($eventosArray);
        return $eventosCollection;
    }

    /**
     * Retorna array con datos de estado de las cuotas 
     * @param int $fecha
     * @param int $tipoDevengamiento
     * @return array
     */
    function makeFIDEstado($fecha = false, $tipoDevengamiento = 1) {

        $eventosCollection = $this->_getfilteredFIDEventos($fecha, $tipoDevengamiento);

        $detalleItemResult = array();

        if ($eventosCollection->getCount() == 0)
            return array();

        //evento al momento de arranque de la cuota de porcentajes de tasa
        $eventoInicialTasa = $this->_cuota->getEventoTasaModificacion();

        //inicializamos el estado de la cuota
        $this->_cuota->setCuotaEstado(new CuotaEstado());
        $this->_cuota->getCuotaEstado()->capital = $this->_cuota->getCapital();

        $this->_recursiveSegmentoResult($eventosCollection, $eventoInicialTasa, $detalleItemResult);
        return $detalleItemResult;
    }
    
    


    //Eventos de la cuota
    //Evento de intereses afectado
    //Array detalle de resultado
    private function _recursiveSegmentoResult(EventosCollection $eventos, EventoInicial $eventoTasa, &$detalleItemResultArray) {
        $eventoActual = $eventos->current();
        //termina cuando llega al evento informe


        if ($eventoActual->getTipo() == EVENTO_INFORME) {
            return;
        }

        //evaluamos si existe un proximo evento
        if ($eventos->next()->valid()) {
            $eventoSiguiente = $eventos->current();

        
        
            //calculamos monto al momento de calcular los interes por la cantidad de dias 
            //transcurridos
            $calculosCredito = new CalculosCredito($this->_credito);

            $rango = ($eventoSiguiente->getFecha() - $eventoActual->getFecha()) / (24 * 60 * 60);
            
            //segun la politica actual de FID se hacen calculos diferentes antes y despues del 
            //vencimiento de la cuota
            //Los dias anteriores al vencimiento se calculan intereses compensatorios y bonificacion
            $detalleResult = new DetalleItemResult();
            if ($eventoSiguiente->getFecha() <= $this->_cuota->getRango()->getEnd()) {
                
                $blog = false;
                $fechaEnvio = $this->_cuota->getFechaEnvio();
                if ($eventoSiguiente->getTipo() == EVENTO_INFORME) {
                    if ($eventoSiguiente->getFecha() >= $fechaEnvio ){
                        
                        $eventoSiguiente->setFecha($this->_cuota->getRango()->getEnd())  ;
                        $rango = ($eventoSiguiente->getFecha() - $eventoActual->getFecha()) / (24 * 60 * 60);
                        $blog = true;
                    }
                }
            
                //calculamos interes compensatorio
                //la base de calculo se calcula al momento del evento que ocurre antes
                //del vencimiento de la cuota
                $log = false;
              
                $resultSaldo = $calculosCredito->getSaldoCapital($eventoActual->getFecha(),$log);
                
                $monto_actual = $resultSaldo['BASE_CALCULO'];
                $monto_interes = $this->calcular_interes(
                        $monto_actual, round($rango), $eventoTasa->getCompensatorio(), $this->_credito->getPeriodicidad(), $this->_credito->getCompensatorioPlazo());
                
                
                $monto_bonificacion = $this->calcular_interes(
                        $monto_actual, $rango, $eventoTasa->getBonificacion(), $this->_credito->getPeriodicidad(), $this->_credito->getCompensatorioPlazo());

                
                
                $detalleResult->compensatorio = $monto_interes;
                $detalleResult->compensatorio_iva = $monto_interes * IMP_IVA;
                $detalleResult->bonificacion = $monto_bonificacion;
                $detalleResult->bonificacion_iva = $monto_bonificacion *IMP_IVA;
                
            } else {
                if ($this->_credito->getEventoInforme()->getFecha() <= $eventoActual->getFecha()) {
                    return;
                }          
                
                //la base de calculo se calcula al final de la cuota a partir del vencimiento
                //de la cuota

                $resultSaldo = $calculosCredito->getSaldoCapital($this->_cuota->getRango()->getEnd(), false);
                

                //se calculan todos los pagos a la fecha del evento solo de la cuota actual
                //no se toma el ultimo pago para tener el dato de los intereses punitorios y moratorios antes del pago
                $detalleItemResultPago = $this->getRecuperoItemResult($eventoSiguiente->getFecha()-1);

                $totalPagos = $detalleItemResultPago->capital + $detalleItemResultPago->compensatorio_iva + $detalleItemResultPago->compensatorio;

                //SE HACE LA ACTUALIZACION DE LOS VALORES DE LA BONFIICACION.
                $acumuladoCompensatorio = $this->_cuota->getCuotaEstado()->compensatorio - $this->_cuota->getCuotaEstado()->bonificacion;
                $acumuladoIvaCompensatorio = $this->_cuota->getCuotaEstado()->compensatorio_iva - $this->_cuota->getCuotaEstado()->bonificacion_iva;

                $saldoCuota = ($resultSaldo['AMORTIZACION_CUOTA'] + $acumuladoCompensatorio + $acumuladoIvaCompensatorio - $totalPagos);



                //si el saldo de cuota es 0 la cuota esta saldada y no hay que hacer mas calculos
                if ($saldoCuota <= 0) {
                    $this->_cuota->setEstadoPago(ESTADO_CUOTA_CANCELADA);
                    return;
                }
                
                //evaluacion del vencimiento de la bonificacion si tuviera
                if ($eventoTasa->getBonificacion() > 0){
                    $fechaInforme = $this->_credito->getEventoInforme()->getFecha() ;
                    $diasVencimiento = $this->_credito->getBonificacionVencimiento();
                    $difDias = $fechaInforme - $this->_cuota->getRango()->getEnd();
                    
                    if ($difDias > $diasVencimiento){
                        $this->_cuota->setEstadoPago(ESTADO_CUOTA_BONIFICACION_VENCIDA);
                        return;
                    }
                }                

                $monto_moratorio = $saldoCuota * (1 + ($eventoTasa->getMoratorio() / 100) * $rango / $this->_credito->getMoratorioPlazo()) - $saldoCuota;
                $monto_punitorio = $saldoCuota * (1 + ($eventoTasa->getPunitorio() / 100) * $rango / $this->_credito->getPunitorioPlazo()) - $saldoCuota;

                                    
                $detalleResult->moratorio = $monto_moratorio;
                $detalleResult->moratorio_iva = $monto_moratorio * IMP_IVA;
                $detalleResult->punitorio = $monto_punitorio;
                $detalleResult->punitorio_iva = $monto_punitorio * IMP_IVA;
            }
            

            //hacemos sumatorio de valores en el estado de la cuota
            $this->_cuota->getCuotaEstado()->moratorio += $detalleResult->moratorio;
            $this->_cuota->getCuotaEstado()->moratorio_iva += $detalleResult->moratorio_iva;
            $this->_cuota->getCuotaEstado()->punitorio += $detalleResult->punitorio;
            $this->_cuota->getCuotaEstado()->punitorio_iva += $detalleResult->punitorio_iva;

            $this->_cuota->getCuotaEstado()->compensatorio += $detalleResult->compensatorio;
            $this->_cuota->getCuotaEstado()->compensatorio_iva += $detalleResult->compensatorio_iva;
            $this->_cuota->getCuotaEstado()->bonificacion += $detalleResult->bonificacion;
            $this->_cuota->getCuotaEstado()->bonificacion_iva += $detalleResult->bonificacion_iva;
            
            


            $detalleItemResultArray[] = $detalleResult;


            //si el evento evaluado es un evento de cambio de tasa sera el proximo evento de tasa
            //en la recursividad
            if ($eventoSiguiente->getTipo() == EVENTO_TASA) {
                $eventoTasa = $eventoSiguiente;
            }
            //recursion
            $this->_recursiveSegmentoResult($eventos, $eventoTasa, $detalleItemResultArray);        
        }
        
    }
    /**
     * Retorna resultado de calculo de intereses segun FID
     * 
     * @param double $monto
     * @param int $dias
     * @param double $interes
     * @param int $periodicidad
     * @param int $dias_year
     * @return type
     */
    static function calcular_interes($monto, $dias, $interes = 10, $periodicidad = 60, $dias_year = 360) {
        $blog = false;

        $dias = floor($dias);
        $interes = $interes / 100;
        //0.5 = 5 / 100;
        $base = 1 + ($interes * $periodicidad / $dias_year);
        //  1.083 =   1 + ( 0.5 * 60 / 360);
        $exponente = $dias / $periodicidad;
        //0.3833 = 23 / 60

        $rtn = $monto * pow($base, $exponente) - $monto;

        return $rtn;
    }
}

?>
