<?php

class notas_model extends main_model{
    public $_tablamod = "fid_nota_req";
    
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
    
    function vincular_nr($iidc,$iidnr){
        $resp = $this->_db->update($this->_tablamod, array("ID_OPERACION"=>$iidc), "ID='".$iidnr."'");
        return $resp;
    }
    
    function delupload_nota($idnotareq,$ruta){
        $this->_db->delete("fid_nota_req_adjunto","ID_NOTA_REQ	='".$idnotareq."' AND NOMBRE='".$ruta."'");
        return 1;
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
    
    function get_usuarios(){
        $rtn = $this->_db->get_tabla("fid_usuarios","ESTADO='1'");
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
        
        //borrar adjunto
        
        //borrar fisico
        
        $lst_uploads = $this->get_arruploads($id);
        if($lst_uploads){
            foreach ($lst_uploads as $rsu){
                unlink($rsu["NOMBRE"]);
            }
        }        
        $this->_db->delete('fid_nota_req_adjunto', "ID_NOTA_REQ =' " . $id . "'" );
        $this->_db->delete('fid_traza', "NOTA=' " . $id . "'" );
    }
    
    function get_req($id){
        $this->_db->where( "ID = '" . $id . "'" );
        $rtn = $this->_db->get_tabla('fid_nota_req');
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }
    
    function get_provincias(){
        $rtn = $this->_db->get_tabla("fid_provincias");
        return $rtn;
    }
    
    function get_arruploads($id){
        $this->_db->select("NOMBRE");
        $rtn = $this->_db->get_tabla("fid_nota_req_adjunto","ID_NOTA_REQ='".$id."'");
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
    
    function marcar_respondida( $iid, $idope, $remitente ){
        $fecha_actual = date("Y-m-d H:i:s");
        $arr_mod= array(
            "ESTADO"=>"3"
        );
        $rtn = $this->_db->update("fid_nota_req",$arr_mod, "ID='".$iid."'");
        
        // insertar traza
        //traza inicial
        $arr_traza = array(
            "ID_OPERACION"=>$idope,
            "ESTADO"=>7, // estado respondido
            "CARTERADE"=> $_SESSION["USERADM"],
            "DESTINO"=>$remitente,
            "OBSERVACION"=>'RESPUESTA',
            "DESCRIPCION"=>'RESPUESTA A REQUERIMIENTO',
            "ETAPA"=>'0',
            "FECHA"=>$fecha_actual,
            "ACTIVO"=>'0',
            "ETAPA_ORIGEN"=>0,
            "NOTIF"=>'1',
        );
        $this->_db->insert('fid_traza', $arr_traza );
        
        
        return $rtn;
    }
    
    function get_uploads_notas($id){
        $this->_db->where("ID_NOTA_REQ = '".$id   ."'");
        $rtn = $this->_db->get_tabla('fid_nota_req_adjunto');
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }    
    
    function sendnota($obj, $adjuntos){
        //log_this('zzzzz00.log',print_r($obj,1));
        $fecha_actual = date("Y-m-d H:i:s");
        $obj['FCREA'] = $fecha_actual;
        $obj['PROPIETARIO'] = $_SESSION["USERADM"];
        $iid = $obj["idreqh"];
        unset( $obj["idreqh"] );

        //$fecha_actual = date("Y-m-d H:i:s");
        //$fecha_actual = date("Y-m-d H:i:s",  strtotime($obj['FCREA']));
        $id_new = $iid;
        if ($iid==0){ //agregar
            $obj['FOJAS'] = "";
            $obj['TIPO'] = "1";
            
            $resp = $this->_db->insert( 'fid_nota_req', $obj );
            $id_new = $resp;
            $obj['ID'] = $id_new;
            $acc="add";
            
        }else{
            $resp = $this->_db->update( 'fid_nota_req', $obj, "ID='".$iid."'" );
            $acc="edit";
            //estado
        }
                
        //si el usuario actual es el cordinador, pedir autorizacion de
        //log_this('zzzzz11.log',print_r($obj,1));
                
        if ($adjuntos):
            foreach ($adjuntos as $key=>$value):
                if (isset($value['nombre_tmp'])):
                    //obtener la semilla
                    $sem = isset($value['nombre_tmp'])?$value['nombre_tmp']:"";
                    //consultamos la semilla de la tabla fid_upload_etiqueta,
                    $etiq = $this->_db->get_tabla('fid_upload_etiqueta', "SEMILLA='".$sem."'");
                    $etiketa="";
                    if ($etiq):
                        $etiketa = $etiq[0]["ETIQUETA"];
                    endif;
                    
                    $arr_ins = array(
                            "ID_NOTA_REQ"=>$id_new,
                            "NOMBRE"=>  PATH_REQUERIMIENTOS . $id_new . "/" . $value['nombre'], 
                            "ID_USUARIO"=>$_SESSION["USERADM"], 
                            "DESCRIPCION"=>$etiketa,
                            "CREATEDON"=>"[NOW()]"
                    );
                    $this->_db->insert('fid_nota_req_adjunto', $arr_ins);
                    //borrar etiketa
                    $this->_db->delete('fid_upload_etiqueta', "SEMILLA='".$sem."'");

                    //mover archivo
                    $origen = isset($value['nombre_tmp'])?$value['nombre_tmp']:"";
                    $destino = PATH_REQUERIMIENTOS . $id_new . "/" . $value['nombre'];

                    if ($origen):
                        mover( $origen, $destino );
                    endif;
                    
                endif;
            endforeach;
        endif;
        
        $rtn = array(
            "accion"=>$acc,
            "result"=>$obj
        );
        return $rtn;
    }
    
    function guardar_etiqueta($semilla,$etiqueta){
        $rtn = $this->_db->insert('fid_upload_etiqueta',array("SEMILLA"=>$semilla,"ETIQUETA"=>$etiqueta));
        return $rtn;
    }
    
    
    
    
    
    function getenviar_a1( $arr_area, $puesto_in ){
        //$rtn = $this->_db->get_tabla("fid_xareas ", "ID NOT IN('11','12')");
        $rtn = $this->_db->get_tabla("fid_xareas ", "ID NOT IN('12')");
        return $rtn;
    }
    
    function getvincular( $idusu ){
        $rtn = $this->_db->get_tabla("fid_operaciones", "CARTERADE='".$idusu."'");
        return $rtn;
    }
    
    function getenviar_a2( $arr_send, $puesto_in ){
         
        $cad_where = "";
        $sw=0;
        if (is_array($arr_send)){
            foreach ($arr_send as $send){
                if (is_array($send)):
                    $a=$send["area"];
                    $p=$send["puesto"];
                    $cad_where = "u.ID_AREA='".$a."' and u.ID_PUESTO='".$p."'";
                    break;
                endif;

            }
        }
        else if(is_numeric($arr_send)){
            $sw=1;
            $cad_where .= $arr_send.",";
        }
        
        if ( strlen($cad_where)>0 and $sw==1 ){
            $cad_where = substr($cad_where,0,-1);
            $cad_where = "u.ID_AREA IN (".$cad_where.")";
        }
        
        if ($puesto_in){
            $cad_where .= "and p.ID='".$puesto_in."'";
        }
        
        $this->_db->select("u.ID as IID,NOMBRE,APELLIDO,a.DENOMINACION AS AREA, p.DENOMINACION AS PUESTO, a.ETAPA AS ETAPA, u.ID_PUESTO AS PUESTOID");
        $this->_db->join("fid_xpuestos p","p.ID=u.ID_PUESTO");
        $this->_db->join("fid_xareas a","a.ID=u.ID_AREA");
        $this->_db->order_by("AREA,PUESTO");
        $rtn = $this->_db->get_tabla("fid_usuarios u", $cad_where);
        //log_this('xxxxxx.log', $this->_db->last_query() );
        return $rtn;
        
    }
          
}
?>