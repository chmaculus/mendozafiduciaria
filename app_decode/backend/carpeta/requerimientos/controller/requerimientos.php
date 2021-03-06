<?php

class requerimientos extends main_controller{
    function requerimientos(){
        $this->mod = $this->model("requerimientos_model");
    }
    
    function init(){
        $this->constructor();
        if ( !isset($_SESSION["USERADM"]))
            header("Location: " . '/'.URL_PATH);
        //$this->_get_loged();
        $this->setCss( array("init.css") );
        $this->setJs( array( "init.js",'forms.js') );
        $this->setPlug( array("chosen"));
        $this->setPlug( array("jalerts"));
        $this->setPlug( array("numeric"));
        $this->setPlug( array("validation"));
        $this->setPlug( array("fancybox"));
        $this->setPlug( array("jqgrid"));
        
        $id_permiso = 10; // permiso de acceso a este modulo (fid_permisos)
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
        $datax['etiqueta_modulo'] = "Requerimientos";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USER_ROL'] = $_SESSION["USER_ROL"];
        
        $this->render($datax);
    }

    function _obtener_main($arr_permiso_mod){
        
        $data['fecha'] = $this->get_fecha();
        $data['etiqueta_mod'] =  "Requerimiento";

        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ',$data['hora_actual']));
        $data['hora_bd'] = $data['hora_actual'];
        
        //return $this->view("requerimientos", $data);
        /* permiso mostrar */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("requerimientos", $data);
        else
            return $this->view("error404",array(),"backend/dashboard");
        /* permiso mostrar*/
        
    }
    
    function x_marcar_respondida(){
        $iid = $_POST['iid'];
        $idope = $_POST['idope'];
        $remitente = $_POST['remitente'];
        $obj = $this->mod->marcar_respondida( $iid, $idope, $remitente );
        echo $obj;
    }
    
    /*
     
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
    
    function x_get_dependencia_operatoria(){
        $tabla = $_POST['tabla'];
        $campo = $_POST['campo'];
        $valor = $_POST['valor'];
        $obj = $this->mod->get_dependencia($tabla,$campo,$valor);
        echo $obj?$obj[0]['cont']:array();
    }
    
    function x_get_dependencia_checklist(){
        $tabla = $_POST['tabla'];
        $campo = $_POST['campo'];
        $valor = $_POST['valor'];
        $obj = $this->mod->get_dependencia($tabla,$campo,$valor);
        echo $obj?$obj[0]['cont']:array();
    }
    
    function x_sendobj(){
        $obj = $_POST['obj'];
        $checklist = isset($obj['checklist'])?$obj['checklist']:array();
        $adjuntos = isset($obj['adjuntos'])?$obj['adjuntos']:array();
        unset($obj['checklist']);
        unset($obj['adjuntos']);
        
        
        //$obj_tipo_entidades = $_POST['tipo_entidades'];
        
        $rtn = $this->mod->sendobj($obj, $checklist, $adjuntos );
        
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_get_info_grid(){
        $rtn = $this->mod->get_info_grid();
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_get_info_bancos(){
        $rtn = $this->mod->get_info_grid();
        echo trim(json_encode(array()));
    }
    
    function x_get_tipos_entidades($where=""){
        $rtn = $this->mod->get_tipos_entidades($where);
        return trim(json_encode($rtn?$rtn:array()));
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
    
    function x_getentidad_select(){
        
        $idt = $_POST['idt'];
        $lst_sr = $this->mod->getentidad_select( $idt );
        
        $productSelect = new SelectBox('Elegir Entidad');

        if (is_array($lst_sr)):
            $productSelect = new SelectBox('Elegir Entidad');
            $c=1;
            foreach($lst_sr as $rs_sr):
                $productSelect->addItem($rs_sr["ID"],$rs_sr["NOMBRE"]);
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
    
    function x_get_uploads($id){
        $obj = $this->mod->get_uploads($id);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_delupload(){
        $idope = $_POST['idope'];
        $ruta = $_POST['ruta'];
        $this->mod->delupload($idope,$ruta);
        $obj = unlink($ruta);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getform_entidad(){
        $data['datos'] = "";
        echo $this->view("form_operatorias", $data);
    }
    
    function x_getform_checklist(){
        $data['datos'] = "";
        echo $this->view("form_checklist", $data);
    }
    
    function x_getform_addentidad(){
        $obj = isset($_POST['obj'])?$_POST['obj']:array();
        //$data['lst_tipoentidades'] = $this->mod->get_tipos_entidades();
        
        $tmp = $this->mod->get_obj($obj);
        $data['cad'] = "";
        $chk_array = array();
        if ($tmp){
            $data['entidad'] = $tmp[0];
            $lst_operatoria_checklist = $this->mod->get_operatoria_checklist( $tmp[0]['ID'] );
            $cad = array();
                        
            if ($lst_operatoria_checklist){
                foreach($lst_operatoria_checklist as $chk){
                    $cad[] = $chk['ID_CHECKLIST'];
                }
            }
            $data['cad'] = $cad;
            $chk_array = json_encode($data['cad']);
            $data['lst_uploads'] = $this->x_get_uploads( $tmp[0]['ID'] );
        }
        else{
            $data['entidad'] = array("ORGANISMO"=>"","TELEFONO"=>"","CUIT"=>"","DESCRIPCION"=>"","NOMBRE"=>"");
            $data['lst_entidades'] = array('');
            $data['lst_uploads'] = '';
        }
        
        //$data['lst_provincias'] = $this->x_getprovincias();
        $data['lst_tipo_operatoria'] = $this->x_get_tipos_operatoria('',1);
        $data['lst_tipoentidades'] = $this->x_get_tipos_entidades("PAR01='1'");
        $data['lst_tipoentidades'] = json_decode($data['lst_tipoentidades'],true);
        //$data['_array_entidades'] = $ent_array;
        $data['_array_checklist'] = $chk_array;
        $data['_semilla'] = time();
                
                
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
    
    function get_file1(){
        $semilla = $_POST["semilla"];
        $id_edit = $_POST["id_edit"];
        
        if (isset($_FILES['imagen'])){
           
            $archivo['tmp']  = $_FILES["imagen"]["tmp_name"];
            $archivo['size'] = $_FILES["imagen"]["size"];
            $archivo['type'] = $_FILES["imagen"]["type"];
            $archivo['name'] = $_FILES["imagen"]["name"];
            
            $subir = true;
            switch (strtolower($archivo['type'])){
                case "image/jpeg":
                case "image/png":
                case "image/bmp":
                case "image/gif":
                $subir = true;
            }

            if ($id_edit){
                //verificar
                $arr_uploads = $this->mod->get_arruploads($id_edit);
                if ($arr_uploads){
                    foreach ($arr_uploads as $rsu){
                        if ( trim(basename($rsu['NOMBRE']))==$archivo['name']){
                            $subir = false;
                            break;
                        }
                    }
                }
            }
            
            if ($subir == true){
                //$file_name = "img".time(); 
                $file_name = $semilla . "__".$archivo['name'];
                            
                $extencion = substr($archivo['name'], -3);
                $uploaded = TEMP_PATH.$file_name;//.".".$extencion;
                if (file_exists($uploaded)){
                    echo '
                            <script>
                                var nombre = "'.$archivo['name'].'";
                                parent.error_post_upload(nombre);
                            </script>';
                    
                }else{
                    if (@move_uploaded_file($archivo['tmp'], $uploaded)){
                        echo '
                            <script>
                                var nombre = "'.$archivo['name'].'";
                                var nombre_tmp = "'.$uploaded.'";
                                parent.post_upload(nombre,nombre_tmp);
                            </script>';
                    }
                    
                }
                
            }else{
                echo '
                        <script>
                            var nombre = "'.$archivo['name'].'";
                            parent.error_post_upload(nombre);
                        </script>';
                
            }
        }    

    }
    */
    
}
