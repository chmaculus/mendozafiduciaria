<?php

class puestos_model extends main_model{
    public $_tablamod = "fid_xpuestos";
    
    function get_obj($id){
        if (!$id) return array();
        $this->_db->where("id = '".$id   ."'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
    }
    
    function get_roles(){
        $rtn = $this->_db->get_tabla("fid_roles");
        return $rtn;
    }
    
    function get_permisos(){
        $this->_db->order_by('MODULO,PERMISO','ASC');
        $rtn = $this->_db->get_tabla("fid_permisos");
        return $rtn;
    }
    
    function get_info_grid(){
        $this->_db->select("u.*, r.DENOMINACION as ROLNAME");
        $this->_db->join("fid_roles r","r.ID=u.ID_ROL");
        $rtn = $this->_db->get_tabla($this->_tablamod . " u");
        return $rtn;
    }
    
    function sendobj($obj){
        $iid = $obj["id"];
        unset($obj["id"]);
        
        if ($iid==0)://agregar
            $acc = "add";
            $obj['var_ins']['ESTADO'] = '1';
            $resp = $this->_db->insert($this->_tablamod, $obj['var_ins']);
        else://editar
            $resp = $this->_db->update($this->_tablamod, $obj['var_ins'], "id='".$iid."'");
            $acc = "edit";
        endif;
        $rtn = array(
            "accion"=>$acc,
            "result"=>$resp
        );
        return $rtn;
    }
    
    function delobj($id){
        $this->_db->delete($this->_tablamod, "id =' " . $id . "'" );
    }
    
    function get_dependencia_operatoria($tabla, $campo, $valor){
        $this->_db->select('count(*) as cont');
        $rtn = $this->_db->get_tabla( $tabla, $campo. '='.$valor);
        return $rtn;
    }
          
    
    
}
?>