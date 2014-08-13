<?php
class busqueda_model extends main_model{
    function set_modulo($modulo){
        $tablas = array(
            "clientes" => "fid_clientes",
            "permisos" => "fid_permisos"
        );
        $this->_tablamod = $tablas[$modulo];
    }
   
    function get_obj($busqueda){
        $this->_db->where("Modulo like  '%".$busqueda."%'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
   }
   
   function get_permisos($filtro){
       $this->_db->where("PERMISO like '%".$filtro."%' OR MODULO like '%".$filtro."%'");
       $items = $this->_db->get_tabla("fid_permisos");
       return $items;
               
   }

}

?>
