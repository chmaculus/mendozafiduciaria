<?php
class model_xs extends model{
    public function obtener_clientes($id = 0){
        if ($id){
            $this->_db->where("ID = ".$id);
        }
        $rtn = $this->_db->get_tabla("clientes");
        return $rtn;
    }
  
    
}
?>