<?php
class RecuperoItemCollection extends ClassIterator {


    /**
     * 
     * @param array $pagos
     */
    function RecuperoItemCollection($pagos = array()) {
        $this->_arr = $pagos;
    }

    /**
     * Agrega item de recupero a collection
     * @param RecuperoItem $pago
     */
    function addRecuperoItem(RecuperoItem $item) {
        $this->_arr[] = $item;
    }
}


abstract class Evento {

    const TIPO_ENVIO = 7;
    const TIPO_GASTO = 5;
    const TIPO_INICIAL = 0;
    const TIPO_DESEMBOLSO = 1;
    const TIPO_TASA = 2;
    const TIPO_RECUPERO = 3;
    const TIPO_VENCIMIENTO = 4000;
    const TIPO_AJUSTE = 4;
    const TIPO_INFORME = 5000;
    
    protected $_fecha;
    protected $_tipo;
    protected $_id;
    protected $_modify;

    /**
     * Retorna fecha del evento
     * @return int
     */
    function getFecha() {
        return $this->_fecha;
    }

    /**
     * Asigna id al Evento
     * @param type $id
     */
    function setID($id) {
        $this->_id = $id;
        if ($id === 0) {
            $this->modify();
        }
    }

    /**
     * Avisa que ha sido modificado desde el mismo evento al modificarse alguna propiedad
     */
    protected function modify() {
        $this->_modify = true;
    }
    
    /**
     * Avisa que ha sido modificado
     */
    function updated(){
        $this->_modify = true;
    }
    
    /**
     * Retorna TRUE si ha sido modificado el evento, FALSE en caso contrario
     * @return boolean
     */
    function isModified() {
        return $this->_modify;
    }

    /**
     * Retorna ID del evento
     * @return type
     */
    function getID() {
        return $this->_id;
    }

    /**
     * Retorna tipo del evento
     * @return int
     */
    function getTipo() {
        return $this->_tipo;
    }

    /**
     * Modifica fecha del evento
     * @param int $fecha
     */
    function setFecha($fecha) {
        $this->_fecha = $fecha;
        $this->modify();
    }
}

class EventosCollection extends ClassIterator implements SplSubject {

    private $_bnotify;
    private $_observers = array();

    /**
     * 
     * @param array $eventos
     */
    function EventosCollection($eventos = array()) {
        $this->_arr = $eventos;
        $this->_bnotify = false;
    }

    /**
     * 
     * @param type $date
     * @return booleanean Retorna 
     */
    function nextToDate($date) {
        if (!$this->valid()) {

            return false;
        }

        $evento = $this->current();

        if ($evento->getFecha() <= $date) {
            $this->next();
            return $evento;
        } else {
            return false;
        }
    }

    /**
     * Obtiene array de eventos
     * @return array
     */
    function getEventosArray() {
        return $this->_arr;
    }

    /**
     * Notifica a los objetos que observan de modificacion de collection 
     */
    function notify() {
        if (!$this->_bnotify)
            return;

        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }

    /**
     * Establece si los objetos observadores seran notificados
     * @param boolean $bnotify
     */
    function setNotify($bnotify) {
        $this->_bnotify = $bnotify;
    }

    /**
     * Agrega elemento observador
     * @param SplObserver $observer
     */
    function attach(SplObserver $observer) {
        $this->_observers[] = $observer;
    }

    /**
     * Quita objeto observador
     * @param SplObserver $observer
     */
    function detach(SplObserver $observer) {
        $key = array_search($observer, $this->_observers, true);
        if ($key) {
            unset($this->_observers[$key]);
        }
    }

    /**
     * Quita EventoMarca de collection
     */
    function removeEventoMarca() {
        for ($i = 0; $i < count($this->_arr); $i++) {
            if ($this->_arr[$i]->getTipo() == EVENTO_INFORME) {
                array_splice($this->_arr, $i, 1);
                if ($this->_position >= count($this->_arr)) {
                    $this->_position = count($this->_arr) - 1;
                }
                break;
            }
        }
    }

    /**
     * Agrega evento a collection y lo ubica en la posicion adecuda por fecha
     * @param Evento $evento
     */
    function addEvento(Evento $evento) {
        //agrega el evento en la posicion correcta ordenado por fecha
        $i = 0;
        $bfound = false;
        $eventoPrimero = reset($this->_arr);
        foreach ($this->_arr as $_evento) {

            //si el evento inicial no es el primero se le pone la misma 
            //fecha que el primero y se lo pasa al inicio del array
            if ($evento->getTipo() === EVENTO_INICIAL && $i > 0) {

                $evento->setFecha($eventoPrimero->getFecha() - 1);
                array_splice($this->_arr, 0, 0, array($evento));
                break;
            }

            //el evento se coloca en la posicion correspondiente segun la fecha
            if ($evento->getFecha() < $_evento->getFecha()) {
                
                array_splice($this->_arr, $i, 0, array($evento));
                $bfound = true;
                break;
            }
            $i++;
        }
        $this->_position = $i;
        if (!$bfound) {
            $this->_arr[] = $evento;
        }

        $this->notify();
    }

}



class EventoInicial extends Evento {

    protected $_compensatorio;
    protected $_punitorio;
    protected $_moratorio;
    protected $_bonificacion;

    /**
     * 
     * @param int $fecha
     * @param double $compensatorio
     * @param double $punitorio
     * @param double $moratorio
     * @param double $bonificacion
     */
    function EventoInicial($fecha, $compensatorio, $punitorio, $moratorio, $bonificacion) {

        $this->_bonificacion = $bonificacion;
        $this->_punitorio = $punitorio;
        $this->_moratorio = $moratorio;
        $this->_compensatorio = $compensatorio;

        $this->_fecha = $fecha;
        $this->_tipo = EVENTO_INICIAL;
    }

    /**
     * Retorna valor de porcentaje de compensatorio
     * @return int
     */    
    function getCompensatorio() {
        return $this->_compensatorio;
    }

    /**
     * Retorna valor de porcentaje de punitorio
     * @return int
     */    
    function getPunitorio() {
        return $this->_punitorio;
    }

    /**
     * Retorna valor de porcentaje de bonificacion
     * @return int
     */
    function getMoratorio() {
        return $this->_moratorio;
    }

    /**
     * Retorna valor de porcentaje de bonificacion
     * @return int 
     */    
    function getBonificacion() {
        return $this->_bonificacion;
    }

    /**
     * Asigna valor de porcentaje de compensatorio
     * @param int $value
     */    
    function setCompensatorio($value) {
        $this->_compensatorio = $value;
        $this->modify();
    }

    /**
     * Asigna valor de porcentaje de punitorio
     * @param int $value
     */    
    function setPunitorio($value) {
        $this->_punitorio = $value;
        $this->modify();
    }

    /**
     * Asigna valor de porcentaje de Moratorio
     * @param int $value
     */    
    function setMoratorio($value) {
        $this->_moratorio = $value;
        $this->modify();
    }

    /**
     * Asigna valor de porcentaje de bonificacion
     * @param int $value
     */
    function setBonificacion($value) {
        $this->_bonificacion = $value;
        $this->modify();
    }
}

class EventoTasaModificacion extends EventoInicial {

    /**
     * 
     * @param type $fecha
     * @param type $compensatorio
     * @param type $punitorio
     * @param type $moratorio
     * @param type $bonificacion
     */
    function EventoTasaModificacion($fecha, $compensatorio, $punitorio, $moratorio, $bonificacion) {
        $this->_fecha = $fecha;
        $this->_bonificacion = $bonificacion;
        $this->_punitorio = $punitorio;
        $this->_moratorio = $moratorio;
        $this->_compensatorio = $compensatorio;
        $this->_tipo = EVENTO_TASA;
    }

  

}

class EventoRecupero extends Evento  {

    private $_monto = 0;
    private $_recuperoItemCollection;

    /**
     * 
     * @param type $fecha
     * @param type $monto
     */
    function EventoRecupero($fecha, $monto = 0) {
        $this->_fecha = $fecha;
        $this->_monto = $monto;

        $this->_tipo = EVENTO_RECUPERO;
        $this->_recuperoItemCollection = new RecuperoItemCollection();
    }

    /**
     * Retorna collection de items
     * @return RecuperoItemCollection
     */
    function getRecuperoItems() {
        return $this->_recuperoItemCollection;
    }

    /**
     * Asigna collection de items
     * @param RecuperoItemCollection $recuperoItemsCollection
     */
    function setRecuperoItems(RecuperoItemCollection $recuperoItemsCollection) {
        $this->_recuperoItemCollection = $recuperoItemsCollection;
    }

    /**
     * Agrega item de pago
     * @param RecuperoItem $recuperoItem
     */
    function addRecuperoItem(RecuperoItem $recuperoItem) {
        $this->_recuperoItemCollection->addItem($recuperoItem);
        $this->modify();
    }

}

class DetalleItemResult extends GenericInfo {
    
}

class RecuperoItem {

    private $_cuota;
    private $_tipo;
    private $_monto;
    private $_cobro;

    /**
     * 
     * @param EventoRecupero $cobro
     * @param Cuota $cuota
     * @param int $tipo
     * @param double $monto
     */
    function RecuperoItem(EventoRecupero $cobro, Cuota $cuota, $tipo, $monto) {

        $this->_cuota = $cuota;
        $this->_cobro = $cobro;

        $this->_tipo = $tipo;
        $this->_monto = $monto;
        $this->_cuota->addRecuperoItem($this);
    }

    /**
     * Retorna evento relacionado al item
     * @return EventoRecupero
     */
    function getEventoRecupero() {
        return $this->_cobro;
    }

    /**
     * Retorna cuota relacionada al item
     * @return Cuota 
     */
    function getCuota() {
        return $this->_cuota;
    }

    /**
     * Retorna tipo de item
     * @return int
     */
    function getTipo() {
        return $this->_tipo;
    }

    /**
     * Retorna de item
     * @return double
     */
    function getMonto() {
        return $this->_monto;
    }

}

class FiltroEventos {

    private $_eventosCollection;

    /**
     * 
     * @param EventosCollection $eventosCollection
     */
    function FiltroEventos(EventosCollection $eventosCollection) {
        $this->_eventosCollection = new EventosCollection($eventosCollection->getEventosArray());
        
    }

    /**
     * Retorna collection de eventos segun parametros de filtro
     * 
     * @param mixed $tipo Tipo puede ser array o int
     * @param int $desde Fecha de inicio de filtrado
     * @param int $hasta Fecha de fin de filtrado
     * @return \EventosCollection
     */
    function getEventosTipo($tipo = false, $desde = false, $hasta = false) {
        $filteredEventosCollection = new EventosCollection();
        $keyBackup = $this->_eventosCollection->key();
        foreach ($this->_eventosCollection as $evento) {
            
            $btipo = false;
            if (!$tipo) {
                $btipo = true;
            } else {
                if (is_array($tipo)) {
                    $tipoEvento = $evento->getTipo();
                    $btipo = in_array($tipoEvento, $tipo);
                } else {
                    $btipo = $evento->getTipo() === $tipo;
                }
            }


            if ($btipo) {
                if (($evento->getFecha() >= $desde || !$desde) && ($evento->getFecha() <= $hasta || !$hasta)) {
                    $filteredEventosCollection->addEvento($evento);
                    
                }
            }
        }
        $filteredEventosCollection->setKey($keyBackup);
        return $filteredEventosCollection;
    }

}

class EventoDesembolso extends Evento  {

    var $_total;
    
    /**
     * 
     * @param int $fecha
     * @param double $total
     */
    function EventoDesembolso($fecha, $total) {
        $this->_fecha = $fecha;
        $this->_total = $total;
        $this->_tipo = EVENTO_DESEMBOLSO;
    }

    /**
     * Retorna total de desembolso
     * @return double
     */
    function getTotal() {
        return $this->_total;
    }

}

class EventoEnvio extends Evento {

    private $_cuota = null;
    /**
     * 
     * @param int $fecha
     */
    function EventoEnvio($fecha) {
        $this->_fecha = $fecha;
        $this->_tipo = EVENTO_ENVIO;
    }

    /**
     * Asigna cuota a evento
     * @param Cuota $cuota
     */
    function setCuota(Cuota $cuota) {
        $this->_cuota = $cuota;
    }

    /**
     * Retorna cuota asignada
     * @return Cuota
     */
    function getCuota() {
        return $this->_cuota;
    }

}

class EventoGasto extends Evento  {

    private $_total;
    private $_descripcion;

    /**
     * 
     * @param int $fecha
     * @param double $total
     * @param String $descripcion
     */
    function EventoGasto(int $fecha, double $total, String $descripcion) {
        $this->_fecha = $fecha;
        $this->_total = $total;
        $this->_descripcion = $descripcion;
        $this->_tipo = EVENTO_GASTO;
    }

}

class EventoMarca extends Evento  {

    /**
     * 
     * @param int $fecha
     */
    function EventoMarca($fecha) {
        $this->_fecha = $fecha;
        $this->_tipo = EVENTO_INFORME;
    }

}

class EventoVencimiento extends Evento  {

    /**
     * 
     * @param int $fecha
     * @param Cuota $cuota
     */
    function EventoVencimiento($fecha, Cuota $cuota = NULL) {
        $this->_fecha = $fecha;
        $this->_tipo = EVENTO_VENCIMIENTO;
    }

}

?>
