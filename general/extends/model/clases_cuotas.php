<?php

define('ESTADO_CUOTA_BONIFICACION_VENCIDA',5);
define('ESTADO_CUOTA_CANCELADA',1);
define('ESTADO_CUOTA_PENDIENTE',0);

class CuotasCollection extends ClassIterator{
    /**
     * 
     * @param Cuota $cuota
     */
    function addCuota(Cuota $cuota){
        $this->_arr[] = $cuota;
    }
    /**
     * 
     * @param array $cuotas
     * 
     */
    function CuotasCollection($cuotas = array()){
        $this->_arr = $cuotas;
    }
    
    /**
     * 
     * @param int $cuotas_restantes
     * @return Cuota
     * 
     */
    function getCuotaByCuotasRestante($cuotas_restantes){
         $total = count($this->_arr);
         $index = $total -  $cuotas_restantes;
         $this->_position = $index;
         //print_array($this->_cuotas);
         return $this->_arr[$this->_position];
    }
}


class Rango {
    protected $_fecha_desde;
    protected $_fecha_hasta;
    
    /**
     * 
     * @param int $fecha_inicio
     * @param int $fecha_fin
     */
    function Rango($fecha_inicio, $fecha_fin){
        $this->_fecha_desde = $fecha_inicio;
        $this->_fecha_hasta = $fecha_fin;
    }
    
    /**
     * Retorna fecha de inicio
     * @return int 
     * 
     */
    function getStart(){
        return $this->_fecha_desde;
    }
    
    /**
     * Retorna fecha de Finalizacion
     * @return int 
     * 
     */
    function getEnd(){
        return $this->_fecha_hasta;
    }
    
}

class Cuota  implements SplObserver{
    private $_id;
    
    private $_cuotasRestantes;
    
    private $_estado;
    
    //evento de vencimiento
    private $_vencimiento;
    
    //evento asignado de tasa de inicio
    private $_eventoTasa = null;
    
    //pagos realizados en esta cuota
    private $_pagosCollection;
    
    //eventos asignados a esta cuota
    private $_eventosCollection;
    
    //rango de entrada y salida
    private $_rango;
    
    //capital asignado a la cuota
    private $_capital;
    
    //referencia al credito al que pertenece;
    private $_credito;
    
    //Evento planchado
    private $_eventoEnvio;
    
    
    private $_estadoCuota = null;
    
    const ESTADO_CUOTA_BONIFICACION_VENCIDA = 5;
    const ESTADO_CANCELADA = 1;
    const ESTADO_PENDIENTE = 0;
    
    function Cuota($id = 0, $capital = 0, $credito = null, $cuotasRestantes = 1){
        $this->_id = $id;
        $this->_capital = $capital;
        $this->_pagosCollection = new RecuperoItemCollection();
        $this->_eventosCollection = new EventosCollection();
        $this->_credito = $credito;
        $this->_estadoCuota = new CuotaEstado();
        $this->_eventoEnvio = null;
        $this->_estado = ESTADO_CUOTA_PENDIENTE;
        $this->_cuotasRestantes = $cuotasRestantes;
    }

    /**
     * Modifica estado de la cuota
     * @param int $estado 
     */    
    function setEstadoPago($estado){
        $this->_estado = $estado;
    }
    
    /**
     * Obtiene estado de la cuota
     * @return int
     */    
    function getEstadoPago(){
        return $this->_estado;
    }
    
    /**
     * Notifica cambios en lista de eventos (uso interno)
     * @param SplSubject $subject 
     */    
    function update(SplSubject $subject) {
        $this->_actualizarEvento($subject);
    }
    
    /**
     * Obtiene capital de la cuota
     * @return double
     */    
    function getCapital(){
        return $this->_capital;
    }
    
    /**
     * Modifica enlace a credito (usointerno)
     * @param Credito $credito
     */
    function setCredito(Credito $credito){
        $this->_credito = $credito;
    }
    
    /**
     * Obtiene Credito de la cuota
     * @return Credito
     */
    function getCredito(){
        return $this->_credito;
    }
    
    private function _actualizarEvento(EventosCollection $eventoCollection){

        $eventoActual = $eventoCollection->current();
        $i = 0;
        if ($eventoActual->getTipo()===EVENTO_TASA || $eventoActual->getTipo()===EVENTO_INICIAL){
            $fechaEventoTasaPrevio = 0;
            
            if ($this->_eventoTasa!=null ){
                $fechaEventoTasaPrevio = $this->_eventoTasa->getFecha();
            }
            
            if ($fechaEventoTasaPrevio <= $eventoActual->getFecha() &&
                $eventoActual->getFecha() < $this->_rango->getStart()){
                $this->_eventoTasa = $eventoActual;
            }
        }
    }
    
    /**
     * Setea Rango (clase Rango desde, hasta)
     * @param Rango
     */
    function setRango(Rango $rango){
        $this->_rango = $rango;
        if ($this->_eventoEnvio===null){
            $this->_eventoEnvio = new EventoEnvio($rango->getEnd());
        }
    }
    
    /**
     * obtiene Rango (clase Rango desde, hasta)
     * @return Rango
     */    
    function getRango(){
        return $this->_rango;
    }
    
    function setVencimiento(EventoVencimiento $vencimiento){
        $this->setVencimientoFecha($vencimiento);
    }
    
    /**
     * obtiene Evento vencimiento
     * @return EventoVencimiento
     */    
    function getEventoVencimiento(){
        return new EventoVencimiento($this->_rango->getEnd(), $this);
    }
    
    function setVencimientoFecha(EventoVencimiento $vencimiento){
        $this->_vencimiento = $vencimiento;
        $this->setVencimientoFecha($this->_vencimiento->getFecha());
    }
    

    /**
     * obtiene items de recupero que afectan a esta cuota
     * @return RecuperoItems
     */    
    function getRecuperoItems(){
        return $this->_pagosCollection ;
    }
    
    /**
     * Asigna lista de items de recupero que afectan a esta cuota
     * @param RecuperoItemCollection $pagoCollection
     */       
    function setRecuperoItemCollection(RecuperoItemCollection $pagoCollection){
        $this->_pagosCollection = $pagoCollection;
    }
    
    /**
     * Agrega item recupero que afecta a esta cuota
     * @param RecuperoItem $itemPago
     */       
    function addRecuperoItem(RecuperoItem $itemPago){
        $this->_pagosCollection->addRecuperoItem($itemPago);
    }
    
    /**
     * obtiene evento con variables de tasas
     * @return EventoInicial
     */      
    function getEventoTasaModificacion(){
        return $this->_eventoTasa;
    }   
    
    /**
     * asigna evento con variables de tasas
     * @param EventoInicial $evento
     */         
    function setEventoTasaModificacion($evento){
        $this->_eventoTasa = $evento;
    }
    
    /**
     * Guarda valores devengados de cuota
     * @param CuotaEstado $cuotaEstado
     */       
    function setCuotaEstado(CuotaEstado $cuotaEstado){
        $this->_estadoCuota = $cuotaEstado;
    }
    
    /**
     * Obtiene valores guardados devengados de cuota
     * @return CuotaEstado
     */         
    function getCuotaEstado(){
        return $this->_estadoCuota;
    }
    
    /**
     * Genera evento de envio en la fecha dada
     * @param int  $fechaEnvio
     */     
    function setFechaEnvio($fechaEnvio){
        $this->_eventoEnvio = new EventoEnvio($fechaEnvio);
    }
    
    
    /**
     * Obtiene fecha de envio
     * @return int  
     */     
    function getFechaEnvio(){
        return $this->_eventoEnvio->getFecha();
    }

    /**
     * Obtiene evento  de envio
     * @return EventoEnvio  
     */     
    function getEventoEnvio(){
        return $this->_eventoEnvio;
    }
    
    /**
     * Obtiene cuotas restantes
     * @return int  
     */      
    function getCuotasRestantes(){
        return $this->_cuotasRestantes;
    }
}


class CuotaEstado extends GenericInfo{
}




?>
