<?php

abstract class ClassIterator implements Iterator {

    protected $_arr;
    protected $_position = 0;

    /**
     * Avanza a la siguiente posicion
     * @return \ClassIterator
     */
    function next() {
        $this->_position++;
        return $this;
    }
    /**
     * Retorna a la primera posicion
     * @return \ClassIterator
     * 
     */
    function rewind() {
        $this->_position = 0;
        return $this;
    }
    /**
     * Valida posicion del puntero
     * @return boolean
     * 
     */
    function valid() {
        if ($this->_position >= 0 && $this->_position < count($this->_arr)) {
            return true;
        }
        return false;
    }

    /**
     * Retorna cantidad elementos en collection
     * @return int 
     * 
     */
    function size() {
        return count($this->_arr);
    }

    /**
     * Retorna cantidad elementos en collection
     * @return int 
     * 
     */
    function getCount() {
        return count($this->_arr);
    }
    
    /**
     * Retorna posicion actual
     * @return int
     */
    function key() {
        return $this->_position;
    }

    /**
     * Retorna elemento actual de collection
     * @return object
     */
    function current() {
        return $this->_arr[$this->_position];
    }
    
    /**
     * 
     * @return object
     */
    function last(){
        $this->_position = count($this->_arr)-1 ;
        return end($this->_arr);
    }    
    
    /**
     * Asigna posición actual
     * @param int  $key
     */
    function setKey($position) {
        $this->_position = $position;
    }    
}


class GenericInfo{
    var $gasto = 0;
    var $compensatorio = 0;
    var $compensatorio_iva = 0;
    var $bonificacion = 0;
    var $bonificacion_iva = 0;
    var $moratorio= 0;
    var $moratorio_iva= 0;
    var $punitorio = 0;
    var $punitorio_iva = 0;
    var $capital = 0;
}


class Credito{
    const TIPO_MICROCREDITO = 1;
    const TIPO_NORMAL = 0;
    
    protected $_id;
    protected $_cuotasCollection;
    protected $_eventosCollection;
    protected $_periodicidad;
    protected $_moratorioPlazo;
    protected $_punitorioPlazo;
    protected $_compensatorioPlazo;
    protected $_bonificacionPlazo;
    
    protected $_bonificacionExpired;
    
    protected $_tipoCredito;
    protected $_eventoInforme = null;
    protected $_versionID = null;
    
    protected $__estado = null;
    
    
    /**
     * 
     * @param int $id
     * @param int $periodicidad
     * @param int $compensatorioPlazo
     * @param int $bonificacionPlazo
     * @param int $moratorioPlazo
     * @param int $punitorioPlazo
     * @param int $bonificacionExpired
     * @param int $versionID
     */
    function credito($id, 
            $periodicidad = 60, 
            $compensatorioPlazo = 360 , 
            $bonificacionPlazo = 360,
            $moratorioPlazo = 365,
            $punitorioPlazo = 365,
            $bonificacionExpired = 60,
            $versionID = 0
            ){
        
        $this->_id = $id;
        $this->_tipoCredito = 0;
        $this->_cuotasCollection = new CuotasCollection();
        $this->_eventosCollection = new EventosCollection();
        $this->_periodicidad = $periodicidad;
        $this->_compensatorioPlazo = $compensatorioPlazo;
        $this->_moratorioPlazo = $moratorioPlazo;
        $this->_punitorioPlazo = $punitorioPlazo;
        $this->_bonificacionPlazo = $bonificacionPlazo;
        $this->_bonificacionExpired = $bonificacionExpired;
        $this->_versionID = $versionID;
    }
    /**
     * Retorna ID version
     * @return int
     */
    function getVersionID(){
        return $this->_versionID;
    }
    
    /**
     * 
     * Comienza notificacion a las cuotas que un evento ha sido agregado
     * para que cada cuota lo acepte o no segun análisis de la cuota
     */
    function ready(){
        $this->_eventosCollection->setNotify(TRUE);
    }
    
    /**
     * Obtiene plazo de vencimiento de bonificacion
     */      
    function getBonificacionVencimiento(){
        return $this->_bonificacionExpired ;
    }
    
    /**
     * Agrega cuota al credito
     * @param Cuota
     */     
    function addCuota(Cuota $cuota){
        $eventoVencimiento = $cuota->getEventoVencimiento();
        $this->_eventosCollection->addEvento($eventoVencimiento);
        $cuota->setCredito($this);
        $this->_cuotasCollection->addCuota($cuota);
        $this->_eventosCollection->attach($cuota);
    }

    /**
     * Agrega evento al credito
     * @param Abstract Evento
     */     
    function addEvento(Evento $evento){
        $this->_eventosCollection->addEvento($evento);
    }
    
    /**
     * Agrega EventoMarca en la fecha especificada
     * @param int $fecha f
     * @return null
     */     
    function setFechaCalculo($fecha){
        $this->_eventosCollection->removeEventoMarca();
        $this->_eventoInforme = new EventoMarca($fecha);
        $this->_eventosCollection->addEvento($this->_eventoInforme);
    }
    
    /**
     * Obtiene todas las cuotas del credito
     * @return CuotasCollection
     */      
    function getCuotas(){
        return $this->_cuotasCollection;
    }
    
    /**
     * Obtiene todos los eventos del credito
     * @return EventosCollection
     */       
    function getEventos(){
        return $this->_eventosCollection;
    }
    
    /**
     * Obtiene el evento de informe
     * @return EventoMarca
     */     
    function getEventoInforme(){
        return $this->_eventoInforme;
    }
    
    /**
     * Obtiene variable periodicidad de credito
     * @return int
     */        
    function getPeriodicidad(){
        return $this->_periodicidad;
    }
    
    /**
     * Obtiene variable plazo moratorio
     * @return int
     */     
    function getMoratorioPlazo(){
        return $this->_moratorioPlazo;
    }

    /**
     * Obtiene variable plazo punitorio
     * @return int
     */  
    function getPunitorioPlazo(){
        return $this->_punitorioPlazo;
    }
    
    /**
     * Obtiene variable plazo compensatorio
     * @return int
     */     
    function getCompensatorioPlazo(){
        return $this->_compensatorioPlazo;
    }
    
    /**
     * Obtiene variable plazo de bonificacion
     * @return int
     */     
    function getBonificacionPlazo(){
        return $this->_bonificacionPlazo;
    }
    
    /**
     * Modifica tipo de credito (TIPO_MICROCREDITO, TIPO_NORMAL)
     * @param int
     */      
    function setTipoCredito($tipo){
        $this->_tipoCredito = $tipo;
    }
    
    /**
     * Obtiene tipo de credito (TIPO_MICROCREDITO, TIPO_NORMAL)
     * @return int
     */      
    function getTipoCredito(){
        return $this->_tipoCredito;
    }
    
    /**
     * Obtiene id de credito
     * @return int
     */
    function getID(){
        return $this->_id;
    }
    
    function setEstado($fecha, $estado, $info = ""){
        list($d,$m,$y) = explode("-",date("d-m-Y",$fecha));
        $fecha = mktime(0,0,0,$m,$d,$y);
        
        $this->_estado = array(
            "CREDITO_ESTADO"=>$estado,
            "CREDITO_ESTADO_FECHA"=>$fecha,
            "CREDITO_ESTADO_INFO"=>$info
            );
    }
    
    function getEstado(){
        return $this->_estado;
    }
    
    
}
