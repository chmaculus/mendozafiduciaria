<?php

//credito 101 cantidad de cuotas de capital no coincide con cuotas de gracia - arreglado para pruebas

include("general/extends/model/clases.php");

class formalta_model extends credito_model {

    function getObject($id_credito, $fecha = false) {


        $this->_cuotas = $this->_to_array_cuotas();
        $this->_variaciones = $this->_to_array_variaciones();


        $primera_variacion = reset($this->_variaciones);

        $credito = new Credito($id_credito, $primera_variacion['PERIODICIDAD_TASA'], $this->_interese_compensatorio_plazo, $this->_interese_compensatorio_plazo, $this->_interese_moratorio_plazo, $this->_interese_punitorio_plazo, $primera_variacion['PLAZO_PAGO'], $this->_id_version
        );
        //$credito->setTipoCredito($this->_tipo_credito);
        $credito->setTipoCredito(Credito::TIPO_MICROCREDITO);
        $credito->ready();
        $cantidadCuotas = count($this->_cuotas);
        $i = 0;
        foreach ($this->_cuotas as $_cuota) {

            $cuotasRestantes = $cantidadCuotas - $i++;
            // echo "CR:".$cuotasRestantes."-".$_cuota['CAPITAL_CUOTA']."<br/>";
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

        $tasa = $cuotaInicial->getEventoTasaModificacion();
        if (!$tasa) {
            $cuotaInicial->setEventoTasaModificacion($eventosCollection->rewind()->current());
            $tasa = $cuotaInicial->getEventoTasaModificacion();
        }

        return $credito;
    }

    function getCuotasEventos($id_credito, $fecha = false) {
        if (!$fecha) {
            $fecha = mktime(0, 0, 0, 10, 15, 2011);
        }
        $this->set_credito_active($id_credito);
        $this->set_version_active();

        $credito = $this->getObject($id_credito);
        $credito->setFechaCalculo($fecha);

        $cuotasCollection = $credito->getCuotas();
        $eventosCollection = $credito->getEventos();


        /*      foreach($cuotasCollection as $cuota){
          $fevento = new FiltroEventos($eventosCollection);
          $eventosCuota = $fevento->getEventosTipo(false, $cuota->getRango()->getStart()+1,$cuota->getRango()->getEnd());

          $eventosArray = $eventosCuota->getEventosArray();
          foreach($eventosArray as $evento){
          //print_array($evento);
          echo $evento->getTipo()." - ".date("d-m-Y",$evento->getFecha());
          switch($evento->getTipo()){
          case 4000:

          break;
          default:
          // echo date("d-m-Y",$evento->getFecha());
          }
          echo "<br/>";
          }
          } */
        return $cuotasCollection;
    }

    function getCreditoClass($id_credito, $fecha = false) {
        if (!$fecha) {
            $fecha = time();
        }
        $this->set_credito_active($id_credito);
        $this->set_version_active();

        $credito = $this->getObject($id_credito, $fecha);

        return $credito;
    }

    function initClassModel($id_credito, $fecha = false) {

        $t1 = microtime();
        if (!$fecha) {
            $fecha = mktime(0, 0, 0, 10, 15, 2011);
        }
        echo date("d/m/Y", $fecha) . "<br/>";
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
        //    $calculoCredito = new CalculosCredito($credito);
        /*        $fecha2 = mktime(0,0,0,5,30,2011);
          $saldo = $this->_get_saldo_capital($fecha2, true);
          print_array($saldo);
          $saldo = $calculoCredito->getSaldoCapital($fecha2, true);
          print_array($saldo);
          die(); */

        if (true) {

            foreach ($cuotasCollection as $cuota) {
                $fevento = new FiltroEventos($eventosCollection);
                $eventosCuota = $fevento->getEventosTipo(false, $cuota->getRango()->getStart() + 1, $cuota->getRango()->getEnd());

                echo "------------------------------------------------------<br/>";
                $eventosArray = $eventosCuota->getEventosArray();
                foreach ($eventosArray as $evento) {
                    //print_array($evento);
                    echo $evento->getTipo() . " - " . date("d-m-Y", $evento->getFecha());
                    switch ($evento->getTipo()) {
                        case 4000:

                            break;
                        default:
                        // echo date("d-m-Y",$evento->getFecha());
                    }
                    echo "<br/>";
                }
                //print_array($eventosArray);

                /*                $calculosCuota = new CalculosCuota($cuota);  
                  $fecha = mktime(0, 0, 0, 3, 30, 2014);
                  $calculosCuota->makeFIDEstado($fecha);

                  print_array($cuota->getCuotaEstado()); */
            }
        }


        if (false) {
            //modificacion tasa
            $fecha = mktime(0, 0, 0, 1, 1, 2014);
            $eventoTasaNueva = new EventoTasaModificacion($fecha, 5, 12, 14, 10);
            $eventoTasaNueva->setID(0);
            $eventosCollection->addEvento($eventoTasaNueva);
        }


        if (false) {
            //Desembolsos nuevos
            $fecha = mktime(0, 0, 0, 1, 1, 2014);
            $eventoDesembolso = new EventoDesembolso($fecha, 1000);
            $eventoDesembolso->setID(0);
            $credito->addEvento($eventoDesembolso);

            $fecha = mktime(0, 0, 0, 2, 5, 2014);
            $eventoDesembolso = new EventoDesembolso($fecha, 2000);
            $eventoDesembolso->setID(0);
            $credito->addEvento($eventoDesembolso);
        }

        if (false) {
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
        //     $SqldbCredito = new Sqldb($credito, $this->_db);
        //     $SqldbCredito->sincronizeEventos();
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

    function getCreditosDeudores($fecha = false) {
        //$this->_db->limit(0, 10);
        $this->_db->select("c.*,CREDITO_ESTADO_FECHA, e.CREDITO_ESTADO as C_ESTADO");
        $this->_db->join("fid_creditos_extra e","e.CREDITO_ID = c.ID ","left");
        $creditos = $this->_db->get_tabla("fid_creditos c");
        if (!$creditos) return;
        
        $fecha = $fecha ? $fecha : time();
        foreach ($creditos as $creditoRow) {
            if ($creditoRow['CREDITO_ESTADO_FECHA']==$fecha){
                continue;
            }
               
            echo "CREDITOID:".$creditoRow['ID']."<br/>";
            $credito = $this->getCreditoClass($creditoRow['ID'], $fecha);
            $this->getCreditoDeuda($credito, $fecha);
            
        }
    }

    function getCreditoDeuda($credito, $fecha) {
      //  echo date("d/m/Y", $fecha);
        $credito->setFechaCalculo($fecha);
        $cuotasCollection = $credito->getCuotas();
        $cantidadCuotas = $cuotasCollection->size();
        
        $bDeuda = false;
        
        foreach ($cuotasCollection as $cuota) {
            $calculosCuota = new CalculosCuota($cuota);

            $calculosCuota->makeFIDEstado($fecha,TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);

            //obtenemos informacion de lo devengado
            $estadoCuota = $cuota->getCuotaEstado();
                        
            //obtenemos lo pagado hasta el momento
            $calculoCuota = new CalculosCuota($cuota);
            $detallesCancelados = $calculoCuota->getRecuperoItemResult($fecha);
            
           //obtenemos montos a pagar por cada concepto en esta cuota
            $estado['Gasto'] = $estadoCuota->gasto - $detallesCancelados->gasto;
            
            $estado['moratorio_iva']  = $estadoCuota->moratorio_iva -$detallesCancelados->moratorio_iva;
            $estado['punitorio_iva'] = $estadoCuota->punitorio_iva -$detallesCancelados->punitorio_iva;
            
            $descuentoBonificadoIva = $estadoCuota->bonificacion_iva ;
            $estado['compensatorio_iva'] = $estadoCuota->compensatorio_iva - $detallesCancelados->compensatorio_iva - $descuentoBonificadoIva ;
            
            $estado['moratorio'] = $estadoCuota->moratorio -$detallesCancelados->moratorio ;
            $estado['punitorio'] = $estadoCuota->punitorio -$detallesCancelados->punitorio;
            
            $descuentoBonificado = $estadoCuota->bonificacion ;
            $estado['compensatorio'] = $estadoCuota->compensatorio - $detallesCancelados->compensatorio - $descuentoBonificado;
            $estado['capital'] = $estadoCuota->capital - $detallesCancelados->capital;            
            
            if ($estado['moratorio'] > 0.5){
                
                $credito->setEstado(time(),-1,$cantidadCuotas  - $cuota->getCuotasRestantes() + 1);
                $creditoDB = new sqldb($credito,$this->_db);
                $creditoDB->sincronizeCredito();
                $bDeuda = true;
                break;
            }
        //    print_array($estadoCuota);
        //    print_array($detallesCancelados);
        //    print_array($estado);
        //    echo "--------------------------------------------------------------";
        }
        if (!$bDeuda ){
            $credito->setEstado(time(),1,0);
            $creditoDB = new sqldb($credito,$this->_db);
            $creditoDB->sincronizeCredito();
        }
    }

}

?>