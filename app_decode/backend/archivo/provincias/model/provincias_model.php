<?php
class provincias_model extends main_model{
    public $_tablamod = "fid_provincias";
   
    function get_obj($id){
        $this->_db->where("id = '".$id   ."'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
    }
    
    function sendobj($obj){
        $iid = $obj["id"];
        unset($obj["id"]);
                
        if ($iid==0)://agregar
            $resp = $this->_db->insert($this->_tablamod, $obj);
        
            $acc = "add";
        else://editar
            $resp = $this->_db->update($this->_tablamod, $obj, "id='".$iid."'");
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
      
}
?>