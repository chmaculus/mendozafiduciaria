<?php
class operatoria_model extends main_model{
    public $_tablamod = "fid_operatorias";
    
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
    
    function delupload($idope,$ruta){
        $this->_db->delete("fid_operatoria_adjunto","ID_OPERATORIA='".$idope."' AND NOMBRE='".$ruta."'");
        return 1;
    }
    
    function get_uploads($id){
        $this->_db->where("ID_OPERATORIA = '".$id   ."'");
        $rtn = $this->_db->get_tabla('fid_operatoria_adjunto');
        return $rtn;
    }
    
    function get_fid_entidades($id){
        $rtn = $this->_db->get_tabla('fid_fideicomiso_entidades',"ID_FIDEICOMISO='".$id."'");
        return $rtn;
    }
    
    function get_operatoria_checklist($idope){
        $rtn = $this->_db->get_tabla('fid_operatoria_checklist',"ID_OPERATORIA='".$idope."'");
        return $rtn;
    }    
    
    function getentidad_select($idp){
        $rtn = $this->_db->select("ID,NOMBRE");
        $rtn = $this->_db->join("fid_entidades e","e.id=et.id_entidad");
        $rtn = $this->_db->get_tabla("fid_entidadestipo et","id_tipo='". $idp . "'");
        return $rtn;
        
    }
    
    
    function sendobj($obj, $checklist, $adjuntos){
        $iid = $obj["id"];
        unset($obj["id"]);
        $id_new = $iid;
        if ($iid==0)://agregar
            $resp = $this->_db->insert($this->_tablamod, $obj);
            $acc = "add";
            $id_new = $resp;
        else://editar
            unset($obj["OBSERVACIONES"],$obj["FEC"],$obj["ESTADO"]);
            $this->_db->delete('fid_fideicomiso_entidades',"ID_FIDEICOMISO='".$iid."'");
            $resp = $this->_db->update($this->_tablamod, $obj, "id='".$iid."'");
            
            $acc = "edit";
        endif;
        
        //delete
        $this->_db->delete( "fid_operatoria_checklist", "ID_OPERATORIA='".$id_new."'" );
        if ($checklist):
            foreach ($checklist as $key=>$value):
                $this->_db->insert('fid_operatoria_checklist', array("ID_OPERATORIA"=>$id_new,"ID_CHECKLIST"=>$value));
            endforeach;
        endif;
        
        //delete
        //$this->_db->delete( "fid_operatoria_adjunto", "ID_OPERATORIA='".$id_new."'" );
        if ($adjuntos):
            foreach ($adjuntos as $key=>$value):
                $this->_db->insert('fid_operatoria_adjunto', array("ID_OPERATORIA"=>$id_new,"NOMBRE"=>  PATH_OPERATORIAS . $id_new . "/" . $value['nombre'] ));
                
                //mover aarchivo
                $origen = $value['nombre_tmp'];
                $destino = PATH_OPERATORIAS . $id_new . "/" . $value['nombre'];
                
                //log_this('xxxxx.log', $origen . "--" . $destino );
                mover($origen, $destino );
                        
                //if (move_uploaded_file( $origen, $destino )){
                  //  log_this('zzzzz.log', 'zzzzzzz' );
                //}
                
                
            endforeach;
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
        $this->_db->delete("fid_operatoria_checklist", "ID_OPERATORIA='" . $id . "'" );
        
        //borrar fisico
        $lst_uploads = $this->get_arruploads($id);
        if($lst_uploads){
            foreach ($lst_uploads as $rsu){
                unlink($rsu["NOMBRE"]);
            }
        }        
        $this->_db->delete("fid_operatoria_adjunto", "ID_OPERATORIA='" . $id . "'" );
        
    }
    
    function get_provincias(){
        $rtn = $this->_db->get_tabla("fid_provincias");
        return $rtn;
    }
    
    function get_arruploads($id){
        $this->_db->select("NOMBRE");
        $rtn = $this->_db->get_tabla("fid_operatoria_adjunto","ID_OPERATORIA='".$id."'");
        return $rtn;
    }
    
    function get_condicioniva(){
        $this->_db->select("ID,CONDICION");
        $rtn = $this->_db->get_tabla("fid_cliente_condicion_iva");
        return $rtn;
    }
    
    function get_condicioniibb(){
        $this->_db->select("ID,CONDICION");
        $rtn = $this->_db->get_tabla(" fid_cliente_condicion_iibb");
        return $rtn;
    }
    
    function get_jefeope(){
        $this->_db->select("ID,NOMBRE,APELLIDO");
        $rtn = $this->_db->get_tabla("fid_usuarios", "ID_PUESTO='6'");
        return $rtn;
    }
    
    function get_cope(){
        $this->_db->select("ID,NOMBRE,APELLIDO");
        $rtn = $this->_db->get_tabla("fid_usuarios", "ID_PUESTO='7'");
        return $rtn;
    }
    
    function gettipobeneficiario(){
        $rtn = $this->_db->select("ID,TIPO");
        $rtn = $this->_db->get_tabla("fid_cliente_tipo");
        return $rtn;
    }
    
    function get_info_grid(){
        $this->_db->select("ot.TIPO as OTTIPO, o.*");
        $this->_db->join("fid_operacion_tipo ot","ot.ID=o.ID_TIPO_OPERATORIA");
        $rtn = $this->_db->get_tabla("fid_operatorias o");
        return $rtn;
    }
    
    function get_tipos_entidades($where=""){
        $this->_db->select("ID,NOMBRE");
        $rtn = $this->_db->get_tabla("fid_entidades_tipos",$where);
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
    /*
    function get_dependencia_operatoria($tabla, $campo, $valor){
        $this->_db->select('count(*) as cont');
        $rtn = $this->_db->get_tabla( $tabla, $campo. '='.$valor);
        return $rtn;
    }
    */
          
}
?>