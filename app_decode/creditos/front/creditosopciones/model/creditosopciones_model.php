<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of creditosopciones_model
 *
 * @author dw
 */
class creditosopciones_model extends credito_model {

    function guardar_opcion($creditos = array(), $opciones = array()) {
        foreach ($creditos as $credito) {
            if ($opciones){
                foreach ($opciones as $key => $value) {
                    $this->_db->where("CREDITO_ID =  " . $credito);
                    $this->_db->where("CLAVE = '" . $key . "'");
                    $opc = $this->_db->get_row("fid_creditos_opciones");

                    if ($opc) {
                        $this->_db->update("fid_creditos_opciones", array("VALOR" => $value), "CREDITO_ID = " . $credito . " AND CLAVE = '" . $key . "'");
                    } else {
                        $this->_db->insert("fid_creditos_opciones", array(
                            "CREDITO_ID" => $credito,
                            "CLAVE" => $key,
                            "VALOR" => $value,
                                )
                        );
                    }
                }
            }
        }
    }
}
