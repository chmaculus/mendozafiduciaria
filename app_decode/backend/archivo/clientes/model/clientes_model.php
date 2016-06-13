<?php
class clientes_model extends main_model{
    public $_tablamod = "fid_clientes";

    function get_obj($id){
               
        if (!$id) return array();
        $this->_db->where("id = '".$id   ."'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
        
    }
    
    function get_contactos($id){
        $this->_db->where("ID_CLIENTE= '".$id   ."'");
        $rtn = $this->_db->get_tabla('fid_cliente_contactos');
        return $rtn;
        
    }
    
    function get_entidades($id){
        $this->_db->select("ID_TIPO");
        $this->_db->where("ID_ENTIDAD = '".$id   ."'");
        $rtn = $this->_db->get_tabla('fid_entidadestipo');
        return $rtn;
        
    }
    
    function sendobj($obj){
        $iid = $obj["id"];
        /*damian*/
        $rtn = $this->_db->select("RAZON_SOCIAL,CUIT");
        $rtn = $this->_db->get_tabla("fid_clientes","ID='".$iid."'");
        $nombreSocial = $rtn[0]['RAZON_SOCIAL'];
        $cuitjunto = $rtn[0]['CUIT'];
        $unidos = $nombreSocial." - ".$cuitjunto;
        /**/
        $contactos = isset($obj["contactos"])?$obj["contactos"]:array();
        
        unset($obj["id"]);
        unset($obj["contactos"]);
        
        if ($iid==0)://agregar
            $resp = $this->_db->insert($this->_tablamod, $obj);
            $acc = "add";
            $id_new = $resp;
        else://editar
        $nombreEdit = $obj['RAZON_SOCIAL'];
        $cuitEdit = $obj['CUIT'];
        $unidosEdit = $nombreEdit." - ".$cuitEdit;

            $resp = $this->_db->update($this->_tablamod, $obj, "id='".$iid."'");
           $arr_razon = array(
                        "BENEF"=>$unidosEdit
                    );    
/*damian*/
           $rtn = $this->_db->update("fid_operaciones",$arr_razon,"BENEF like'%".$unidos."%'");
            $acc = "edit";
            $id_new = $iid;
            endif;
        
        //borrar previos
        $this->_db->delete('fid_cliente_contactos', "ID_CLIENTE='" . $id_new . "'");
        //contactos
        if ($contactos):
            foreach($contactos as $cont):
                $obj =  array(
                            "ID_CLIENTE"=>$id_new,
                            "CONTACTO"=>$cont['con'],
                            "TELEFONO"=>$cont['tel'],
                            "TEL_CEL"=>$cont['tel'],
                            "TEL_TRAB"=>$cont['tel'],
                            "CORREO"=>$cont['ema']
                        );
                $this->_db->insert('fid_cliente_contactos', $obj);
            endforeach;
        else:
            //si array es vacio, borrar el contacto principal
            $this->_db->update('fid_clientes', array("CONTACTO"=>"","CORREO"=>"","TELEFONO"=>"","TEL_CEL"=>"","TEL_TRAB"=>""),"ID='" . $id_new . "'");
        endif;
        
        $rtn = array(
            "accion"=>$acc,
            "result"=>$resp
        );
        return $rtn;
    }

    function delobj($id){
        //preguntar si tiene dependencias
        //operaciones
        $ope1 = $this->_db->get_tabla( "fid_operacion_cliente", 'ID_CLIENTE="'.$id.'"');
        $ope2 = $this->_db->get_tabla( "fid_nota_req", 'PROPIETARIO="'.$id.'"');
        $ope3 = $this->_db->get_tabla( "fid_nota_req", 'ENVIADOA="'.$id.'"');
        $ope4 = $this->_db->get_tabla( "fid_creditos", 'POSTULANTES="'.$id.'"');
        
        $sw = 0;
        if (count($ope1)>0){
            $sw = 1;
        }
        if (count($ope2)>0){
            $sw = 1;
        }
        if (count($ope3)>0){
            $sw = 1;
        }
        if (count($ope4)>0){
            $sw = 1;
        }
        
        if ($sw==0){
            $this->_db->delete($this->_tablamod, "id =' " . $id . "'" );
            //contactos
            $this->_db->delete('fid_cliente_contactos', "ID_CLIENTE='" . $id . "'");
            return 1;
        }else{
            return -2;
        }
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
        $rtn = $this->_db->select("c.id,razon_social,DIRECCION,COD_POSTAL,PROVINCIA,LOCALIDAD,TELEFONO,TEL_CEL,TEL_TRAB,CONTACTO,CUIT,CBU,CORREO,ID_INV,INSCRIPCION_IIBB");
        $rtn = $this->_db->join("fid_provincias p","p.id=c.id_provincia");
        $rtn = $this->_db->join("fid_localidades l","l.id=c.id_departamento");
        $rtn = $this->_db->get_tabla("fid_clientes c");
        return $rtn;
    }
    
    function get_tipos_entidades(){
        $this->_db->select("ID,NOMBRE");
        $rtn = $this->_db->get_tabla("fid_entidades_tipos");
        return $rtn;
    }
    
    function getclavenivel($passw, $nivel){
        $retornar = 0;
        $this->_db->select("clave");
        $rtn = $this->_db->get_tabla("fid_accesosclave", "nivel='" . $nivel . "'");
        if ($rtn){//comparar
            if ($rtn[0]["clave"]==$passw){
                $retornar = 1;
            }
        }
        return $retornar;
    }
    
    function  verificarcuit($cuit){
        if ($cuit!=''){
            $this->_db->select("ID");
            $rtn = $this->_db->get_tabla("fid_clientes", "CUIT='".$cuit."'");
            return $rtn;
        }else{
            return false;
        }
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