<?php
class entidades_model extends main_model{
    public $_tablamod = "fid_entidades";
    
    function get_obj($id){
               
        if (!$id) return array();
        $this->_db->where("id = '".$id   ."'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
        
    }
    
    function get_entidades($id){
        $this->_db->select("ID_TIPO");
        $this->_db->where("ID_ENTIDAD = '".$id   ."'");
        $rtn = $this->_db->get_tabla('fid_entidadestipo');
        return $rtn;
        
    }
    
    function sendobj($obj , $obj_tipo_entidades){
        $iid = $obj["id"];
        unset($obj["id"]);
//        var_dump($obj);die("ACA EL OBJETO A GUARDAR");
        if ($iid==0)://agregar
            $resp = $this->_db->insert($this->_tablamod, $obj);
            $acc = "add";
            $id_new = $resp;
            //borrar las que tenia antes , insertar tipo entidades
            $this->_db->delete('fid_entidadestipo',"ID_ENTIDAD='".$id_new."'");
            if ($obj_tipo_entidades):
                foreach ($obj_tipo_entidades as $te):
                    $this->_db->insert('fid_entidadestipo', array("ID_ENTIDAD"=>$id_new,"ID_TIPO"=>$te));
                endforeach;
            endif;
        else://editar
            unset($obj["OBSERVACIONES"],$obj["FEC"],$obj["ESTADO"]);
            //borrar las que tenia antes , insertar tipo entidades
            $this->_db->delete('fid_entidadestipo',"ID_ENTIDAD='".$iid."'");
            if ($obj_tipo_entidades):
                foreach ($obj_tipo_entidades as $te):
                    $this->_db->insert('fid_entidadestipo', array("ID_ENTIDAD"=>$iid,"ID_TIPO"=>$te));
                endforeach;
            endif;
            
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
        //borrar tb los hijos en entidadestipo
        $this->_db->delete("fid_entidadestipo", "id_entidad='" . $id . "'" );
        
    }
    
    function delobj_detalle($id,$idt){
        $this->_db->delete('fid_entidadestipo', "id_entidad ='" . $id . "' and id_tipo = '".$idt."'" );
    }
    
    function get_provincias(){
        $rtn = $this->_db->get_tabla("fid_provincias");
        return $rtn;
    }
    function get_localidades($id_provincia){
        $rtn = $this->_db->get_tabla("fid_localidades","ID_PROVINCIA=".$id_provincia);
        return $rtn;
    }
    
    
    function get_condicioniva(){
        $rtn = $this->_db->select("ID,CONDICION");
        $rtn = $this->_db->get_tabla("fid_cliente_condicion_iva");
        return $rtn;
    }
    
    function get_condicioniibb(){
        $rtn = $this->_db->select("ID,CONDICION");
        $rtn = $this->_db->get_tabla(" fid_cliente_condicion_iibb");
        return $rtn;
    }
    function gettipobeneficiario(){
        $rtn = $this->_db->select("ID,TIPO");
        $rtn = $this->_db->get_tabla("fid_cliente_tipo");
        return $rtn;
    }
    
    function get_info_grid(){
        //$this->_db->select("ets.nombre as entidad_tipo,ets.id as idt,e.nombre as entidad,e.telefono as telefono, e.id as id, e.cuit as cuit, e.descripcion as descripcion, e.domicilio as domicilio, e.mail as mail, e.situacion_iva as situacion_iva, e.situacion_iibb as situacion_iibb ");
        $this->_db->select("ets.nombre as entidad_tipo,ets.id as idt, e.nombre as entidad, e.cuit as cuit, e.* ");
        $this->_db->join("fid_entidades e","et.id_entidad=e.id",'inner');
        $this->_db->join("fid_entidades_tipos ets","et.id_tipo=ets.id",'inner');
        $this->_db->order_by("entidad");
        $rtn = $this->_db->get_tabla("fid_entidadestipo et");
        return $rtn;
    }
    
    function get_tipos_entidades(){
        $this->_db->select("ID,NOMBRE");
        $this->_db->order_by("NOMBRE", "ASC");
        $rtn = $this->_db->get_tabla("fid_entidades_tipos");
        return $rtn;
    }
        
    function prev_consulta($cols){
        $tb = $this->_tablamod;
        $this->_db->select("$tb.ID, $tb.RAZON_SOCIAL, $tb.CONTACTO, $tb.ID as opciones");
    }
    
    function getlocalidad($idp){
        $rtn = $this->_db->get_tabla("fid_localidades","ID_PROVINCIA='". $idp . "'");
        return $rtn;
    }
    
    function update_tipos_entidades( $id, $nombre ){
        $arr_edit = array(
                        "ID"=>$id,
                        "NOMBRE"=>$nombre
                    );
        
        $rtn = $this->_db->update("fid_entidades_tipos",$arr_edit,"ID='". $id . "'");
        return $rtn;
        
    }
    
    function delete_tipos_entidades($id){
        $rtn = $this->_db->delete("fid_entidades_tipos","ID='". $id . "'");
        return $rtn;
    }
    
    function add_tipos_entidades(){
        $arr_ins = array(
            "NOMBRE"=>'Nuevo Registro',
            "ESTADO"=>'1',
        );
        $id = $this->_db->insert("fid_entidades_tipos",$arr_ins);
        
        $this->_db->select('ID,NOMBRE');
        $rtn = $this->_db->get_tabla("fid_entidades_tipos",'ID='.$id);
        return $rtn;
    }
      
}

?>