<?php
//credito 101 cantidad de cuotas de capital no coincide con cuotas de gracia - arreglado para pruebas


class clases_model extends credito_model {

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
                
        $tasa = $cuotaInicial ->getEventoTasaModificacion();
        if (!$tasa) {
            $cuotaInicial->setEventoTasaModificacion($eventosCollection->rewind()->current());
            $tasa = $cuotaInicial->getEventoTasaModificacion();
        }
        
        return $credito;
    }
    
    

    
    function getCreditoClass($id_credito, $fecha = false){
        if (!$fecha) {
            $fecha = mktime(0, 0, 0, 10, 15, 2011);
        }
        $this->set_credito_active($id_credito);
        $this->set_version_active();
        
        $credito = $this->getObject($id_credito);
        
        return $credito;
    }
    
}
