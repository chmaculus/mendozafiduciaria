<?php
class fideicomiso_model extends main_model{
    public $_tablamod = "fid_fideicomiso";
    
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
    
    function get_arruploads($id){
        $this->_db->select("NOMBRE");
        $rtn = $this->_db->get_tabla("fid_fideicomiso_adjunto","ID_FIDEICOMISO='".$id."'");
        return $rtn;
    }
    
    function delupload($idfid,$ruta){
        $this->_db->delete("fid_fideicomiso_adjunto","ID_FIDEICOMISO='".$idfid."' AND NOMBRE='".$ruta."'");
        return 1;
    }
    
    function get_operatoriasf($id){
        $this->_db->select("fo.*,o.NOMBRE AS nom_ope");
        $this->_db->join('fid_operatorias o','o.ID=fo.ID_OPERATORIA');
        $rtn = $this->_db->get_tabla('fid_fideicomiso_operatorias fo',"fo.ID_FIDEICOMISO='".$id."'");
        return $rtn;
    }
    
    function get_array_bancos_e($id){
        $rtn = $this->_db->get_tabla(' fid_fideicomiso_bancos',"ID_FIDEICOMISO='".$id."'");
        return $rtn;
    }
    
    function get_array_aportes_e($id){
        $this->_db->select("IF( fa.ORIGEN = '2','Fiduciante','Inversor') as ORIGEN_DAT, e.NOMBRE AS NOMBRE_DAT, fa.OBSERVACIONES AS OBS1, fa.FECHA as FECHA, fa.NOMBRE AS NOMBREA, fa.APORTE AS APORTE, fa.ORIGEN AS ORIGEN");
        $this->_db->join("fid_fideicomiso f","f.ID=fa.ID_FIDEICOMISO");
        $this->_db->join("fid_entidades e","e.ID=fa.NOMBRE");
        $rtn = $this->_db->get_tabla('fid_fideicomiso_aportes fa',"ID_FIDEICOMISO='".$id."'");
        //log_this('yyyyyyy.log', $this->_db->last_query() );
        return $rtn;
    }
    
    function getoperatorias(){
        $rtn = $this->_db->get_tabla('fid_operatorias');
        return $rtn;
    }
    
    function getoperatorias_filtro(){
        $rtn = $this->_db->get_tabla('fid_operatorias', "ID NOT IN (SELECT ID_OPERATORIA FROM fid_fideicomiso_operatorias)");
        return $rtn;
    }
    
     function get_uploads($id){
        $this->_db->where("ID_FIDEICOMISO = '".$id   ."'");
        $rtn = $this->_db->get_tabla('fid_fideicomiso_adjunto');
        return $rtn;
    }
    
    
    function get_fid_entidades($id){
        $rtn = $this->_db->get_tabla('fid_fideicomiso_entidades',"ID_FIDEICOMISO='".$id."'");
        return $rtn;
    }
    
    function getentidad_select($idp){
        $this->_db->select("ID,NOMBRE");
        $this->_db->join("fid_entidades e","e.id=et.id_entidad");
        $rtn = $this->_db->get_tabla("fid_entidadestipo et","id_tipo='". $idp . "'");
        return $rtn;
        
    }
    
    
    function sendobj($obj, $entidades, $adjuntos, $operatorias, $bancos, $aportes){
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
        
        if ($entidades):
            foreach ($entidades as $key=>$value):
                foreach($value as $valor):
                    $this->_db->insert('fid_fideicomiso_entidades', array("ID_FIDEICOMISO"=>$id_new,"ID_ENTIDAD"=>$valor,"TIPO"=>$key));
                endforeach;
            endforeach;
        endif;
        
        if ($adjuntos):
            foreach ($adjuntos as $key=>$value):
                $this->_db->insert('fid_fideicomiso_adjunto', array("ID_FIDEICOMISO"=>$id_new,"NOMBRE"=>  PATH_FIDEICOMISOS . $id_new . "/" . $value['nombre'] ));
                //mover aarchivo
                $origen = $value['nombre_tmp'];
                $destino = PATH_FIDEICOMISOS . $id_new . "/" . $value['nombre'];
                mover($origen, $destino );
            endforeach;
        endif;
        
        $this->_db->delete('fid_fideicomiso_bancos',"ID_FIDEICOMISO='".$id_new."'");
        if ($bancos):
            foreach ($bancos as $key=>$value):
                $arr_ins = array(
                        "ID_FIDEICOMISO"=>$id_new,
                        "BANCO"=>  $value["BANCO"],
                        "TITULAR"=>  $value["TITULAR"],
                        "CUIT"=>  $value["CUIT"],
                        "NROCUENTA"=>  $value["NROCUENTA"],
                        "CBU"=>  $value["CBU"]
                    );
                $this->_db->insert('fid_fideicomiso_bancos', $arr_ins );
            endforeach;
        endif;
        
        //borrar
        $this->_db->delete('fid_fideicomiso_operatorias',"ID_FIDEICOMISO='".$id_new."'");
        if ($operatorias):
            foreach ($operatorias as $key=>$value):
                $this->_db->insert('fid_fideicomiso_operatorias', array("ID_FIDEICOMISO"=>$id_new,"ID_OPERATORIA"=> $value ));
            endforeach;
        endif;
        
        //log_this('xxxxxxx.log',print_r($aportes,1));
        
        //borrar
        $this->_db->delete('fid_fideicomiso_aportes',"ID_FIDEICOMISO='".$id_new."'");
        if ($aportes):
            foreach ($aportes as $key=>$value):
                $arr_ins = array(
                        "ID_FIDEICOMISO"=>$id_new,
                        "ORIGEN"=>  $value["ORIGEN"],
                        "NOMBRE"=>  $value["NOMBREA"],
                        "APORTE"=>  $value["APORTE"],
                        "FECHA"=>  $value["FECHA"],
                        "OBSERVACIONES"=>  $value["OBSERVACION"]
                    );
                $this->_db->insert('fid_fideicomiso_aportes', $arr_ins );
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
        //entidades
        $this->_db->delete("fid_fideicomiso_entidades", "ID_FIDEICOMISO='" . $id . "'" );
        //operatorias
        $this->_db->delete("fid_fideicomiso_operatorias", "ID_FIDEICOMISO='" . $id . "'" );
        //bancos
        $this->_db->delete("fid_fideicomiso_bancos", "ID_FIDEICOMISO='" . $id . "'" );
        //aportes
        $this->_db->delete("fid_fideicomiso_aportes", "ID_FIDEICOMISO='" . $id . "'" );
        //entidades
        $this->_db->delete("fid_fideicomiso_entidades", "ID_FIDEICOMISO='" . $id . "'" );
        
        //borrar upload fisico
        $lst_uploads = $this->get_arruploads($id);
        if($lst_uploads){
            foreach ($lst_uploads as $rsu){
                unlink($rsu["NOMBRE"]);
            }
            //borrar directorio
            $partes_ruta = pathinfo($lst_uploads[0]["NOMBRE"]);
            borrar_directorio($partes_ruta['dirname'], true);
        }
        $this->_db->delete("fid_fideicomiso_adjunto", "ID_FIDEICOMISO='" . $id . "'" );
        
    }
    
    function delobj_detalle($id,$idt){
        $this->_db->delete('fid_entidadestipo', "id_entidad ='" . $id . "' and id_tipo = '".$idt."'" );
    }
    
    function get_provincias(){
        $rtn = $this->_db->get_tabla("fid_provincias");
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
        
        $this->_db->select("f.*,p.PROVINCIA AS PROVINCIA, l.LOCALIDAD AS DEPARTAMENTO");
        $this->_db->join("fid_provincias p","p.ID=f.ID_PROVINCIA");
        $this->_db->join("fid_localidades l","l.ID=f.ID_DEPARTAMENTO","left");
        $rtn = $this->_db->get_tabla("fid_fideicomiso f");
        //log_this('eeeeee.log',  $this->_db->last_query() );
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
    
     function getnombreorigen($idp){
        $this->_db->select("e.ID as ido,e.NOMBRE as nombreo");
        $this->_db->join('fid_entidades e','e.ID=et.ID_ENTIDAD');
        $rtn = $this->_db->get_tabla("fid_entidadestipo et","et.ID_TIPO='". $idp . "'");
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