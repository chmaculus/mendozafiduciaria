<?php
//credito 101 cantidad de cuotas de capital no coincide con cuotas de gracia - arreglado para pruebas

include("general/extends/model/clases.php");

class formalta_model extends credito_model {

    function getObject($id_credito) {
        $this->_cuotas = $this->_to_array_cuotas();
        $this->_variaciones = $this->_to_array_variaciones();


        $primera_variacion = reset($this->_variaciones);

        $credito = new Credito($id_credito, $primera_variacion['PERIODICIDAD_TASA'], 
                $this->_interese_compensatorio_plazo, 
                $this->_interese_compensatorio_plazo,
                $this->_interese_moratorio_plazo, 
                $this->_interese_punitorio_plazo, 
                $primera_variacion['PLAZO_PAGO'],
                $this->_id_version
        );
        //$credito->setTipoCredito($this->_tipo_credito);
        $credito->setTipoCredito(Credito::TIPO_MICROCREDITO);
        $credito->ready();
        $cantidadCuotas = count($this->_cuotas);
        $i = 0;
        foreach ($this->_cuotas as $_cuota) {
            $cuotasRestantes = $cantidadCuotas - $i++; 
            echo "CR:".$cuotasRestantes."-".$_cuota['CAPITAL_CUOTA']."<br/>";
            $cuota = new Cuota($_cuota['ID'], $_cuota['CAPITAL_CUOTA'], $credito, $cuotasRestantes);
            
            $cuota->setRango(new Rango($_cuota['FECHA_INICIO'], $_cuota['FECHA_VENCIMIENTO']));
            $credito->addCuota($cuota);
        }
        $cuotasCollection = $credito->getCuotas();

        foreach ($this->_variaciones as $variacion) {
            $ef = new EventoFactory($cuotasCollection, $variacion);
            $evento = $ef->getEvento();
            $credito->addEvento($evento);
        }
        
        $eventosCollection = $credito->getEventos();
        
        $cuotasCollection->rewind();
        $cuotaInicial = $cuotasCollection->current();
                
        $tasa = $cuotaInicial ->getEventoTasaModificacion();
        if (!$tasa) {
            $cuotaInicial->setEventoTasaModificacion($eventosCollection->rewind()->current());
            $tasa = $cuotaInicial->getEventoTasaModificacion();
        }
        
        return $credito;
    }
    
    

    function initClassModel($id_credito, $fecha = false) {

        $t1 = microtime();
        if (!$fecha) {
            $fecha = mktime(0, 0, 0, 10, 15, 2011);
        }
        echo date("d/m/Y",$fecha)."<br/>";
        $this->set_credito_active($id_credito);
        $this->set_version_active();
        
        $t2 = microtime();

        $credito = $this->getObject($id_credito);
        $credito->setFechaCalculo($fecha);

        $cuotasCollection = $credito->getCuotas();
        $eventosCollection = $credito->getEventos();
  
       // $eventoTasaNueva = new EventoTasaModificacion($fecha, 5, 12, 14, 10);
       // $eventoTasaNueva->setID(0);
//        $eventosCollection->addEvento($eventoTasaNueva);
        $calculoCredito = new CalculosCredito($credito);
/*        $fecha2 = mktime(0,0,0,5,30,2011);
        $saldo = $this->_get_saldo_capital($fecha2, true);
        print_array($saldo);  
        $saldo = $calculoCredito->getSaldoCapital($fecha2, true);
      print_array($saldo);  
die();*/
        
        if (true){
            foreach($cuotasCollection as $cuota){
                $calculosCuota = new CalculosCuota($cuota);  
                $fecha = mktime(0, 0, 0, 3, 30, 2014);
                $calculosCuota->makeFIDEstado($fecha);

                print_array($cuota->getCuotaEstado());
            }
        }


        if (false){
            //modificacion tasa
            $fecha = mktime(0, 0, 0, 1, 1, 2014);
            $eventoTasaNueva = new EventoTasaModificacion($fecha, 5, 12, 14, 10);
            $eventoTasaNueva->setID(0);
            $eventosCollection->addEvento($eventoTasaNueva);            
        }
        
        
        if (false){
            //Desembolsos nuevos
            $fecha = mktime(0, 0, 0, 1, 1, 2014);
            $eventoDesembolso = new EventoDesembolso($fecha, 1000);
            $eventoDesembolso->setID(0);
            $credito->addEvento($eventoDesembolso );            
            
            $fecha = mktime(0, 0, 0, 2, 5, 2014);
            $eventoDesembolso = new EventoDesembolso($fecha, 2000);
            $eventoDesembolso->setID(0);
            $credito->addEvento($eventoDesembolso );            
            
        }
        
        if (false){
            //pagar a fecha
            $eventoRecupero = $calculoCredito->payDeuda($fecha, 1000);       
            $credito->addEvento($eventoRecupero);            
        }

        
        
        /*
        $monto = 0;
        $eventoTasaNueva = new EventoRecupero($fecha, 0);
        $eventoTasaNueva->setID(0);
        $eventosCollection->addEvento($eventoTasaNueva);
        */
        $SqldbCredito = new Sqldb($credito, $this->_db);
        $SqldbCredito->sincronizeEventos();
        die();
        
        
        
        $i = 0;
        foreach ($cuotasCollection as $cuota) {

            $tasa = $cuota->getEventoTasaModificacion();
            if (!$tasa) {
                $cuota->setEventoTasaModificacion($eventosCollection->rewind()->current());
                $tasa = $cuota->getEventoTasaModificacion();
            }

            $tasa = $cuota->getEventoTasaModificacion();


            //$calculoCuota = new CalculosCuota($cuota);

            //$calculoCuota->getFIDSegmentosResult($fecha, TIPO_DEVENGAMIENTO_AUTO);
            //$calculoCuota->getFIDSegmentosResult($fecha, TIPO_DEVENGAMIENTO_FORZAR_CUOTA_ACTUAL);
            //$calculoCuota->makeFIDEstado($fecha, TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);
            //$deuda = $calculoCuota->getRecuperoItemResult($fecha);

            //print_array($deuda);
            //print_array($cuota->getCuotaEstado());
        }
        $t3 = microtime();
        echo "----T2: " . ($t3 - $t2) . "<br/>";        
        $calculoCredito = new CalculosCredito($credito);
        $calculoCredito->payDeuda($fecha, 1000);
        /*
        foreach ($cuotasCollection as $cuota) {
            $tasa = $cuota->getEventoTasaModificacion();
            if (!$tasa) {
                $cuota->setEventoTasaModificacion($eventosCollection->rewind()->current());
                $tasa = $cuota->getEventoTasaModificacion();
            }

            $tasa = $cuota->getEventoTasaModificacion();
            $calculoCuota = new CalculosCuota($cuota);
            $calculoCuota->makeFIDEstado($fecha, TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);
            $deuda = $calculoCuota->getRecuperoItemResult($fecha);
            //print_array($deuda);
            print_array($cuota->getCuotaEstado());
            
        }
*/



    }

}

?>