<?php

class dash_model extends credito_model {


    
    function get_desembolsos_pendientes(){
        $this->_db->where("ESTADO = 1");
        $this->_db->select("d.*, c.ID as ID_CREDITO");
        $this->_db->join("fid_creditos c","c.ID_OPERACION = d.ID_OPERACION");
        $desembolsos = $this->_db->get_tabla("fid_sol_desembolso d");
        return $desembolsos;
    }    
}


?>
