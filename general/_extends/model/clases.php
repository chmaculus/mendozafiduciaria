<?php


include("general/extends/model/clases_credito.php");
include("general/extends/model/clases_cuotas.php");

include("general/extends/model/clases_eventos.php");

include("general/extends/model/calculos_creditos.php");
include("general/extends/model/calculos_cuotas.php");

include("general/extends/model/class_db.php");
/*
class RecuperoItemCollection implements Iterator {

    private $_pagos;
    private $_position = 0;

    function RecuperoItemCollection($pagos = array()) {
        $this->_pagos = $pagos;
    }

    function addRecuperoItem(RecuperoItem $pago) {
        $this->_pagos[] = $pago;
    }

    function next() {
        $this->_position++;
    }

    function rewind() {
        $this->_position = 0;
    }

    function valid() {
        if ($this->_position >= 0 && $this->_position < count($this->_pagos)) {
            return true;
        }
        return false;
    }

    function size() {
        return count($this->_pagos);
    }

    function key() {
        return $this->_position;
    }

    function current() {
        return $this->_pagos[$this->_position];
    }

}
 * 

class EventosCollection extends ClassIterator implements SplSubject {

    private $_position = 0;
    private $_eventos = array();
    private $_bnotify;
    private $_observers = array();

    function EventosCollection($eventos = array()) {
        $this->_eventos = $eventos;
        $this->_bnotify = false;
    }

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

    function getEventosArray() {
        return $this->_eventos;
    }

    function notify() {
        if (!$this->_bnotify)
            return;

        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }

    function setNotify($bnotify) {
        $this->_bnotify = $bnotify;
    }

    function attach(SplObserver $observer) {
        $this->_observers[] = $observer;
    }

    function detach(SplObserver $observer) {
        $key = array_search($observer, $this->_observers, true);
        if ($key) {
            unset($this->_observers[$key]);
        }
    }

    function removeEventoMarca() {
        for ($i = 0; $i < count($this->_eventos); $i++) {
            if ($this->_eventos[$i]->getTipo() == EVENTO_INFORME) {
                array_splice($this->_eventos, $i, 1);
                if ($this->_position >= count($this->_eventos)) {
                    $this->_position = count($this->_eventos) - 1;
                }
                break;
            }
        }
    }

    function size() {
        return count($this->_eventos);
    }

    function getCount() {
        return count($this->_eventos);
    }

    function current() {
        return $this->_eventos[$this->_position];
    }

    function next() {
        $this->_position++;
        return $this;
    }

    function key() {
        return $this->_position;
    }

    function valid() {
        if ($this->_position >= 0 && $this->_position < count($this->_eventos)) {
            return true;
        }
        return false;
    }

    function rewind() {
        $this->_position = 0;
        return $this;
    }

    function addEvento(Evento $evento) {
        //agrega el evento en la posicion correcta ordenado por fecha
        $i = 0;
        $bfound = false;
        $eventoPrimero = reset($this->_eventos);
        foreach ($this->_eventos as $_evento) {

            //si el evento inicial no es el primero se le pone la misma 
            //fecha que el primero y se lo pasa al inicio del array
            if ($evento->getTipo() === EVENTO_INICIAL && $i > 0) {

                $evento->setFecha($eventoPrimero['FECHA'] - 1);
                array_splice($this->_eventos, 0, 0, array($evento));
                break;
            }

            //el evento se coloca en la posicion correspondiente segun la fecha
            if ($evento->getFecha() < $_evento->getFecha()) {
                echo "inserta " . $i . "--" . $evento->getTipo() . "<br/>";
                array_splice($this->_eventos, $i, 0, array($evento));
                $bfound = true;
                break;
            }
            $i++;
        }
        $this->_position = $i;
        if (!$bfound) {
            $this->_eventos[] = $evento;
        }


        $this->notify();
    }

    function setKey($key) {
        $this->_position = $key;
    }

}



*/

?>