<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_db
 *
 * @author Fderico Cordoba
 */
class sqldb {

    protected $_credito;
    protected $_db;

    /**
     * 
     * 
     * @param Credito $credito
     * @param type $db Conexion sql a base de datos
     */
    function sqldb(Credito $credito, $db) {
        $this->_credito = $credito;
        $this->_db = $db;
    }

    /**
     * Guadar los cambios en clases modificadas en todo el credito
     * @param boolean $emulate si esta en TRUE no guarda datos. Util para testing
     */
    function sincronizeAll($emulate = false) {
        $this->sincronizeCredito($emulate);
        $this->sincronizeCuotas($emulate);
        $this->sincronizeEventos($emulate);
    }

    /**
     * Guarda cambios de eventos modificados
     * @param boolean $emulate si esta en TRUE no guarda datos. Util para testing
     */
    function sincronizeEventos($emulate = false) {
        $eventosCollection = $this->_credito->getEventos();
        $cuotasCollection = $this->_credito->getCuotas();

        $cuotaRestante = $cuotasCollection->size();
        $moratorio = 0;
        $punitorio = 0;
        $bonificacion = 0;
        $compensatorio = 0;

        foreach ($eventosCollection as $evento) {
            $bmod = false;

            switch ($evento->getTipo()) {
                case EVENTO_VENCIMIENTO:
                    $cuotaRestante--;
                    break;
                case EVENTO_INICIAL:
                    $compensatorio = $evento->getCompensatorio();
                    $bonificacion = $evento->getBonificacion();
                    $moratorio = $evento->getMoratorio();
                    $punitorio = $evento->getPunitorio();
                    break;
                case EVENTO_TASA:
                    $id = $evento->getID();
                    $bmod = $evento->isModified();

                    $compensatorio = $evento->getCompensatorio();
                    $bonificacion = $evento->getBonificacion();
                    $moratorio = $evento->getMoratorio();
                    $punitorio = $evento->getPunitorio();

                    //solo sincroniza si se ha modificado
                    if ($bmod && !$emulate) {
                        $data['POR_INT_COMPENSATORIO'] = $compensatorio;
                        $data['POR_INT_SUBSIDIO'] = $bonificacion;
                        $data['POR_INT_MORATORIO'] = $moratorio;
                        $data['POR_INT_PUNITORIO'] = $punitorio;
                        $data['FECHA'] = $evento->getFecha();
                        $data['CANTIDAD_CUOTAS'] = $cuotaRestante;
                        $data['ID_CREDITO'] = $this->_credito->getID();
                        $data['ID_VERSION'] = $this->_credito->getVersionID();
                        $data['IVA'] = IMP_IVA;
                        $data['TIPO'] = EVENTO_TASA;

                        $datosAsoc['ID_CREDITO'] = $this->_credito->getID();
                        $datosAsoc['CUOTAS_RESTANTES'] = $cuotaRestante;
                        $datosAsoc['FECHA'] = $evento->getFecha();
                        $datosAsoc['COMPENSATORIO'] = $evento->getCompensatorio();
                        $datosAsoc['SUBSIDIO'] = $evento->getBonificacion();
                        $datosAsoc['MORATORIO'] = $evento->getMoratorio();
                        $datosAsoc['PUNITORIO'] = $evento->getPunitorio();
                        //insert o update
                        if ($id == 0) {

                            $id = $this->_db->insert("fid_creditos_eventos", $data);
                            $datosAsoc['ID_VARIACION'] = $id;
                            $this->_db->insert("fid_creditos_cambiotasas", $datosAsoc);
                        } else {
                            $this->_db->update("fid_creditos_eventos", $data, "ID = " . $id);

                            $datosAsoc['ID_VARIACION'] = $id;
                            $this->_db->insert("fid_creditos_cambiotasas", $datosAsoc);
                        }
                    }
                    break;
                case EVENTO_RECUPERO:
                    $id = $evento->getID();
                    $bmod = $evento->isModified();

                    //solo sincroniza si se ha modificado
                    if ($bmod) {
                        $data['POR_INT_COMPENSATORIO'] = $compensatorio;
                        $data['POR_INT_SUBSIDIO'] = $bonificacion;
                        $data['POR_INT_MORATORIO'] = $moratorio;
                        $data['POR_INT_PUNITORIO'] = $punitorio;
                        $data['FECHA'] = $evento->getFecha();
                        $data['CANTIDAD_CUOTAS'] = $cuotaRestante;
                        $data['ID_CREDITO'] = $this->_credito->getID();
                        $data['ID_VERSION'] = $this->_credito->getVersionID();
                        $data['IVA'] = IMP_IVA;
                        $data['TIPO'] = EVENTO_RECUPERO;

                        $total = 0;
                        //insert o update
                        if ($id == 0) {
                            if (!$emulate) {
                                $id = $this->_db->insert("fid_creditos_eventos", $data);
                            }
                            $datosAsoc['ID_VARIACION'] = $id;
                            $itemsRecupero = $evento->getRecuperoItems();
                            foreach ($itemsRecupero as $item) {

                                //se guarda si el item es mayor a 0
                                if (round($item->getMonto(), 2) > 0) {

                                    $dataPago = array(
                                        "ID_CREDITO" => $this->_credito->getID(),
                                        "FECHA" => $evento->getFecha(),
                                        "ID_TIPO" => $item->getTipo(),
                                        "MONTO" => $item->getMonto(),
                                        "CUOTAS_RESTANTES" => $item->getCuota()->getCuotasRestantes(),
                                        "ID_VARIACION" => $id,
                                        "VENCIDO" => 0,
                                    );
                                    if (!$emulate) {
                                        $this->_db->insert("fid_creditos_pagos", $dataPago);
                                    } else {
                                        print_array($dataPago);
                                    }
                                }
                            }
                        } else {
                            $this->_db->update("fid_creditos_eventos", $data, "ID = " . $id);

                            $datosAsoc['ID_VARIACION'] = $id;
                            $this->_db->insert("fid_creditos_cambiotasas", $datosAsoc);
                        }
                    }

                    break;
                case EVENTO_DESEMBOLSO:
                    $id = $evento->getID();
                    $bmod = $evento->isModified();

                    //solo sincroniza si se ha modificado
                    if ($bmod) {
                        $data['POR_INT_COMPENSATORIO'] = $compensatorio;
                        $data['POR_INT_SUBSIDIO'] = $bonificacion;
                        $data['POR_INT_MORATORIO'] = $moratorio;
                        $data['POR_INT_PUNITORIO'] = $punitorio;
                        $data['FECHA'] = $evento->getFecha();
                        $data['CANTIDAD_CUOTAS'] = $cuotaRestante;
                        $data['ID_CREDITO'] = $this->_credito->getID();
                        $data['ID_VERSION'] = $this->_credito->getVersionID();
                        $data['IVA'] = IMP_IVA;
                        $data['TIPO'] = EVENTO_DESEMBOLSO;

                        $datosAsoc['ID_CREDITO'] = $this->_credito->getID();
                        $datosAsoc['CUOTAS_RESTANTES'] = $cuotaRestante;
                        $datosAsoc['FECHA'] = $evento->getFecha();
                        $datosAsoc['MONTO'] = $evento->getTotal();
                        if ($id == 0) {

                            $id = $this->_db->insert("fid_creditos_eventos", $data);
                            $datosAsoc['ID_VARIACION'] = $id;
                            $this->_db->insert("fid_creditos_desembolsos", $datosAsoc);
                        } else {
                            $this->_db->update("fid_creditos_eventos", $data, "ID = " . $id);

                            $datosAsoc['ID_VARIACION'] = $id;
                            $this->_db->insert("fid_creditos_desembolsos", $datosAsoc);
                        }
                    }
                    break;
                case EVENTO_AJUSTE:
                    $id = $evento->getID();
                    $bmod = $evento->isModified();

                    //solo sincroniza si se ha modificado
                    if ($bmod) {
                        $data['POR_INT_COMPENSATORIO'] = $compensatorio;
                        $data['POR_INT_SUBSIDIO'] = $bonificacion;
                        $data['POR_INT_MORATORIO'] = $moratorio;
                        $data['POR_INT_PUNITORIO'] = $punitorio;
                        $data['FECHA'] = $evento->getFecha();
                        $data['CANTIDAD_CUOTAS'] = $cuotaRestante;
                        $data['ID_CREDITO'] = $this->_credito->getID();
                        $data['ID_VERSION'] = $this->_credito->getVersionID();
                        $data['IVA'] = IMP_IVA;
                        $data['TIPO'] = EVENTO_AJUSTE;

                        $datosAsoc['ID_CREDITO'] = $this->_credito->getID();
                        $datosAsoc['CUOTAS_RESTANTES'] = $cuotaRestante;
                        $datosAsoc['FECHA'] = $evento->getFecha();
                        $datosAsoc['MONTO'] = $evento->getTotal();
                        if ($id == 0) {

                            $id = $this->_db->insert("fid_creditos_eventos", $data);
                            $datosAsoc['ID_VARIACION'] = $id;
                            $this->_db->insert("fid_creditos_desembolsos", $datosAsoc);
                        } else {
                            $this->_db->update("fid_creditos_eventos", $data, "ID = " . $id);

                            $datosAsoc['ID_VARIACION'] = $id;
                            $this->_db->insert("fid_creditos_desembolsos", $datosAsoc);
                        }
                    }
                    break;
                case EVENTO_GASTO:
                    break;
            }

            if ($bmod) {
                
            }
        }
    }

    /**
     * Guarda cambios en cuotas modificadas
     * @param boolean $emulate
     */
    function sincronizeCuotas($emulate = false) {
        
    }

    /**
     * 
     * Guarda cambios en Evento modificado
     * @param boolean $emulate
     */
    function sincronizeCredito($emulate = false) {
        $estado = $this->_credito->getEstado();
        
        if (!$estado) {
            return;
        }
        
        if (!$emulate){
            $extraRow = $this->_db->get_row("fid_creditos_extra","CREDITO_ID = ".$this->_credito->getID());
            
            $datos = $estado ;
            
            
            if ($extraRow){
                $this->_db->update("fid_creditos_extra",$datos,"CREDITO_ID = ".$this->_credito->getID());
            }
            else{
                $datos['CREDITO_ID'] = $this->_credito->getID();
                $this->_db->insert("fid_creditos_extra",$datos);
            }
            
        }
    }

}

?>
