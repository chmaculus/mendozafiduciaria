<?php

class notas extends main_controller{
    function notas(){
        $this->mod = $this->model("notas_model");
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
        
        $id_permiso = 11; // permiso de acceso a este modulo (fid_permisos)
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
        $datax['etiqueta_modulo'] = "Notas";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USER_ROL'] = $_SESSION["USER_ROL"];
        $this->_js_var['_USUARIO_SESION_ACTUAL'] = $_SESSION["USERADM"];
        
        $this->render($datax);
    }

    function _obtener_main($arr_permiso_mod){
        
        $data['fecha'] = $this->get_fecha();
        $data['etiqueta_mod'] =  "Requerimiento";

        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ',$data['hora_actual']));
        $data['hora_bd'] = $data['hora_actual'];
        
        //return $this->view("notas", $data);
        /* permiso mostrar */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("notas", $data);
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
    
    
    function x_getform_agregar_requerimiento(){
        $data['datos'] = "";
        $data['_semilla'] = time();
        
        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ',$data['hora_actual']));
        $data['lst_usuarios'] = $this->mod->get_usuarios();
        
        $idr = isset( $_POST['idr'] )?$_POST['idr']:'';
        $data['obj_req'] = array();
        if ($idr){ //editar
            //get obj
            $obj_req = $this->mod->get_req($idr);
            if($obj_req)
                $data['obj_req'] = $obj_req[0];
            
            $data['lst_uploads_req'] = $this->x_get_uploads_notas( $idr );
        }
        
        //log_this('qqqqqq.log', print_r($data,1));
        
        echo $this->view("form_notas", $data);
    }
    
    function x_get_uploads_notas($id){
        $obj = $this->mod->get_uploads_notas($id);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_sendnota(){
        $obj = $_POST['obj'];
        $adjuntos = isset($obj['adjuntos'])?$obj['adjuntos']:array();
        unset($obj['adjuntos']);
        $rtn = $this->mod->sendnota( $obj, $adjuntos );
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_delobj(){
        $iid = $_POST['id'];
        $rtn = $this->mod->delobj($iid);
        echo '1';
    }
    
    function x_vincular_nr(){
        $iidc = $_POST['idcarpeta'];
        $iidnr = $_POST['idnr'];
        $rtn = $this->mod->vincular_nr($iidc,$iidnr);
        echo '1';
    }
    
    function x_getenviar_a1(){
        $send = $_POST['area'];
        $puesto_in = isset($_POST['puesto_in'])?$_POST['puesto_in']:'';
        $obj = $this->mod->getenviar_a1( $send, $puesto_in );
        echo trim(json_encode($obj?$obj:array()));
    }
    
    function x_getvincular(){
        $idusu = $_POST['idusu'];
        $obj = $this->mod->getvincular( $idusu );
        echo trim(json_encode($obj?$obj:array()));
    }
    
    function x_guardar_traza_nota(){
        
        $fecha_actual = date("Y-m-d H:i:s");
        
        $id_req_nota = $_POST['id_req_nota'];
        $observacion = $_POST['observacion'];
        $descripcion = $_POST['descripcion'];
        $destinatario = $_POST['destinatario'];
        $propietario = isset($_POST['PROPIETARIO'])?$_POST['PROPIETARIO']:0;
        $tid = isset($_POST['tid'])?$_POST['tid']:0;
        
        $arr_traza = array(
            "ID_OPERACION"=>0,
            "ESTADO"=>1, // estado respondido
            "CARTERADE"=> $_SESSION["USERADM"],
            "DESTINO"=>$destinatario,
            "OBSERVACION"=>$observacion,
            "DESCRIPCION"=>$descripcion,
            "ETAPA"=>'0',
            "FECHA"=>$fecha_actual,
            "ACTIVO"=>'1',
            "ETAPA_ORIGEN"=>0,
            "NOTIF"=>'1',
            "NOTA"=>$id_req_nota
        );
        //actualizar todas las notas con activo=0
        $this->mod->actualizar_notas_activo_cero($id_req_nota);
        $obj = $this->mod->guardar_traza_nota( $arr_traza );
        //cambiar leido de la traza ex activa
        $this->mod->cambiar_estado_antigua_traza_nota($tid);
        
        //actualizar nota (enviado a)
        $arr_nota = array(
            "ENVIADOA"=>$destinatario
        );
        
        if ($propietario>0){
            $arr_nota["PROPIETARIO"] = $propietario;
        }
        
        $this->mod->actualizar_nota($id_req_nota,$arr_nota);
        echo $obj;
    }
    
    
    function x_guardar_traza_paselibre(){
        
        $fecha_actual = date("Y-m-d H:i:s");
        
        $id_req_nota = $_POST['id_req_nota'];
        $observacion = $_POST['observacion'];
        $descripcion = $_POST['descripcion'];
        $destinatario = $_POST['destinatario'];
        $tid = isset($_POST['tid'])?$_POST['tid']:0;
        
        $arr_traza = array(
            "ID_OPERACION"=>0,
            "ESTADO"=>1, // estado respondido
            "CARTERADE"=> $_SESSION["USERADM"],
            "DESTINO"=>$destinatario,
            "OBSERVACION"=>$observacion,
            "DESCRIPCION"=>$descripcion,
            "ETAPA"=>'0',
            "FECHA"=>$fecha_actual,
            "ACTIVO"=>'1',
            "ETAPA_ORIGEN"=>0,
            "NOTIF"=>'1'
        );
        
        $obj = $this->mod->guardar_traza_nota( $arr_traza );
        
        //cambiar leido de la traza ex activa
        $this->mod->cambiar_estado_antigua_traza_nota($tid);
        
        //actualizar nota (enviado a)
        $arr_nota = array(
            "ENVIADOA"=>$destinatario
        );
        $this->mod->actualizar_nota($id_req_nota,$arr_nota);
        echo $obj;
    }
    
    function x_getenviar_a2(){
        $id_area = $_POST['id_area'];
        $puesto_in = isset($_POST['puesto_in'])?$_POST['puesto_in']:'';
        $obj = $this->mod->getenviar_a2( $id_area,$puesto_in );
        echo trim(json_encode($obj?$obj:array()));
    }
    
    function x_delupload_nota(){
        $idope = $_POST['idnotareq'];
        $ruta = $_POST['ruta'];
        $this->mod->delupload_nota($idope,$ruta);
        $obj = unlink($ruta);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
       
    function get_file_notas(){
        $semilla = $_POST["semilla"];
        $id_edit = $_POST["id_edit"];
        $etiqueta = $_POST["req_etiquetah"];
        $etapa = $_POST["etapa"];
        
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
                                var etapa   = "'.$etapa.'";
                                var etiketa = "'.$etiqueta.'";
                                parent.post_upload_req(nombre,nombre_tmp,etapa,etiketa);
                            </script>';
                        //guardar la etiketa
                        //post_upload_req
                        $this->mod->guardar_etiqueta($uploaded,$etiqueta);
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
    
}
