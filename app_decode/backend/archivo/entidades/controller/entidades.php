<?php

class entidades extends main_controller{
    function entidades(){
        $this->mod = $this->model("entidades_model");
    }
    
    function init(){
        $this->constructor();
        
        if ( !isset($_SESSION["USERADM"]))
            header("Location: " . '/'.URL_PATH);
        //$this->_get_loged();
        $this->setCss( array("init.css") );
        $this->setJs( array( "init.js") );
        $this->setPlug( array("chosen"));
        $this->setPlug( array("jalerts"));
        $this->setPlug( array("datatables"));
        $this->setPlug( array("validation"));
        $this->setPlug( array("fancybox"));
        $this->setPlug( array("jqgrid"));
        
        $id_permiso = 5; // permiso de acceso a este modulo (fid_permisos)
        $arr_permiso_mod = isset($_SESSION["USER_PERMISOS"][$id_permiso])?$_SESSION["USER_PERMISOS"][$id_permiso]:0;
        if(PERMISOS_ALL==1){
            $arr_permiso_mod = array
            (
                "MOSTRAR" => 1,
                "ALTA" => 1,
                "BAJA" => 1,
                "MODIFICACION" => 1,
                "EXPORTAR" => 1,
                "OTROS" => 1,
                "VER" => 1
            );
        }

        /* permiso alta */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['ALTA'] == 1)
            $this->_js_var['_permiso_alta'] = 1;
        else
            $this->_js_var['_permiso_alta'] = 0;
        /* permiso alta */
        
        /* permiso baja */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['BAJA'] == 1)
            $this->_js_var['_permiso_baja'] = 1;
        else
            $this->_js_var['_permiso_baja'] = 0;
        /* permiso baja */
        
        /* permiso mod */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MODIFICACION'] == 1)
            $this->_js_var['_permiso_modificacion'] = 1;
        else
            $this->_js_var['_permiso_modificacion'] = 0;
        /* permiso mod */
        
        /* permiso ver */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['VER'] == 1)
            $this->_js_var['_permiso_ver'] = 1;
        else
            $this->_js_var['_permiso_ver'] = 0;
        /* permiso ver */
                
        $datax = array();
        $datax['main'] = $this->_obtener_main($arr_permiso_mod);
        $datax['titulo']= "Administracion";
        $datax['etiqueta_modulo'] = "Entidades";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        
        $this->render($datax);
    }

    function _obtener_main($arr_permiso_mod){
        $data['fecha'] = $this->get_fecha();
        $data['etiqueta_mod'] =  "Cliente";
        $data['lst_provincias'] = $this->x_getprovincias();
        $data['lst_condicioniva'] = $this->x_getcondicioniva();
        $data['lst_condicioniibb'] = $this->x_getcondicioniibb();
        $data['lst_tipobeneficiario'] = $this->x_gettipobeneficiario();
        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ',$data['hora_actual']));
        $data['hora_bd'] = $data['hora_actual'];
        //$data['campos'] = $this->get_describe('fid_entidades');
        $tmp = $this->get_describe('fid_entidades');
        
        $arr_campos = array();
        if ($tmp){
            foreach($tmp as $f){
                $arr_excluir = array("ID","OBSERVACIONES","ESTADO","NOMBRE");
                
                if ( !in_array($f['Field'] ,$arr_excluir) ){
                    $arr_campos[] = $f['Field'];
                }
            }
        }
        $this->_js_array['_campos'] = $arr_campos;
        //return $this->view("entidades", $data);
        
        /* permiso mostrar */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("entidades", $data);
        else
            return $this->view("error404",array(),"backend/dashboard");
        /* permiso mostrar*/
        
    }
    
    function x_get_datatable(){
        $data = $this->mod->get_datatable(
                array("ID","RAZON_SOCIAL","CONTACTO"),"ID"
                );
        echo json_encode($data);
    }
    
    function x_getobj(){
        $iid = $_POST['id'];
        $obj = $this->mod->get_obj($iid);
        echo trim(json_encode($obj?$obj[0]:array()));
    }
    
    function x_sendobj(){
        $obj = $_POST['obj'];
        $obj['OBSERVACIONES'] = "";
        $obj['FEC'] = date('d/m/Y H:i:s');
        $obj['ESTADO'] = "1";
        $obj_tipo_entidades = $_POST['tipo_entidades'];
        $rtn = $this->mod->sendobj($obj , $obj_tipo_entidades);
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_get_info_grid(){
        
        $rtn = $this->mod->get_info_grid();
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_get_tipos_entidades(){
        $rtn = $this->mod->get_tipos_entidades();
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_getlocalidad(){
        $idp = $_POST['idp'];
        $lst_sr = $this->mod->getlocalidad($idp);
        
        $productSelect = new SelectBox('Elegir Subrubro');

        if (is_array($lst_sr)):
            $productSelect = new SelectBox('Elegir Subrubro');
            $c=1;
            foreach($lst_sr as $rs_sr):
                $productSelect->addItem($rs_sr["ID"],$rs_sr["LOCALIDAD"]);
                $c++;
            endforeach;
        endif;
        echo json_encode($productSelect);
        die();
    }
    
    function x_delobj(){
        $iid = $_POST['id'];
        $rtn = $this->mod->delobj($iid);
        echo '1';
    }
    
    function x_delobj_detalle(){
        
        $id = $_POST['id'];
        $idt = $_POST['idt'];
        
        $rtn = $this->mod->delobj_detalle($id,$idt);
        echo '1';
    }
    
    function x_getprovincias(){
        $obj = $this->mod->get_provincias();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getcondicioniva(){
        $obj = $this->mod->get_condicioniva();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getcondicioniibb(){
        $obj = $this->mod->get_condicioniibb();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_gettipobeneficiario(){
        $obj = $this->mod->gettipobeneficiario();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getform_entidad(){
        $data['datos'] = "";
        echo $this->view("form_tipoentidad", $data);
    }
    
    function x_getform_addentidad(){
        $obj = isset($_POST['obj'])?$_POST['obj']:array();
                   
        $data['lst_tipoentidades'] = $this->mod->get_tipos_entidades();
        
        $tmp = $this->mod->get_obj($obj);
        $data['cad'] = "";
        if ($tmp){

            $tmp[0]["CUIT"] = str_replace("-", "", $tmp[0]["CUIT"]);
            $tmp[0]["TELEFONO"] = str_replace("-", "", $tmp[0]["TELEFONO"]);
            $tmp[0]["TELEFONO"] = str_replace("(", "", $tmp[0]["TELEFONO"]);
            $tmp[0]["TELEFONO"] = str_replace(")", "", $tmp[0]["TELEFONO"]);
            
            $data['entidad'] = $tmp[0];
            $lst_entidades = $this->mod->get_entidades($tmp[0]['ID']);
            $cad = "";
                        
            if ($lst_entidades){
                foreach($lst_entidades as $ent){
                    $cad .= $ent["ID_TIPO"].",";
                }
                $cad = substr($cad,0,-1);
            }
            $data['cad'] = $cad;
            
        }
        else{
            $data['entidad'] = array("ORGANISMO"=>"","TELEFONO"=>"","CUIT"=>"","DESCRIPCION"=>"","NOMBRE"=>"","SITUACION_IIBB"=>"","SITUACION_IVA"=>"","MAIL"=>"","REPRESENTANTE"=>"","DOMICILIO"=>"");
            $data['lst_entidades'] = array('');
        }
        echo $this->view("form", $data);
        
    }
    
    function x_update_tipos_entidades(){
        $obj = $_POST['obj'];
        
        $nombre = $obj["NOMBRE"];
        $iid = $obj["ID"];
        $obj = $this->mod->update_tipos_entidades($iid,$nombre);
        echo $obj;
    }
    
    function x_delete_tipos_entidades(){
        $obj = $_POST['obj'];
        $iid = $obj["ID"];
        $obj = $this->mod->delete_tipos_entidades($iid);
        echo $obj;
    }
    
    function x_add_tipos_entidades(){
        $obj = $this->mod->add_tipos_entidades();
        $tmp = $obj?$obj[0]:array();
        echo json_encode($tmp);
        
    }
    
}
