<?php

class operatorias extends main_controller{
    function operatorias(){
        $this->mod = $this->model("operatoria_model");
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
        
        $id_permiso = 7; // permiso de acceso a este modulo (fid_permisos)
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
        
        $this->_js_var['_fecha_tasa'] = date('d-m-Y');
                
        $datax = array();
        $datax['main'] = $this->_obtener_main($arr_permiso_mod);
        $datax['titulo']= "Administracion";
        $datax['etiqueta_modulo'] = "Operatorias";
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
        $arr_campos = array("NOMBRE","TIPO","DESCRIPCION","TOPE_PESOS","TASA_INTERES_COMPENSATORIA","TASA_INTERES_MORATORIA","TASA_INTERES_POR_PUNITORIOS","TASA_SUBSIDIADA","DESEMBOLSOS","DEVOLUCIONES","PERIODICIDAD","TASA_GASTOS");
        $this->_js_array['_campos'] = $arr_campos;
        //return $this->view("operatoria", $data);
        /* permiso mostrar */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("operatoria", $data);
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
        
        /*
        $entidades = isset($obj['entidades'])?$obj['entidades']:array();
        unset($obj['entidades']);
          */
        //$obj_tipo_entidades = $_POST['tipo_entidades'];
        $imp_comp = $imp_mora = $imp_pun = $imp_subs = FALSE;
        if (isset($obj['IMP_COMP'])) {
            $imp_comp = $obj['IMP_COMP'];
            unset($obj['IMP_COMP']);
        }
        if (isset($obj['IMP_MORA'])) {
            $imp_mora = $obj['IMP_MORA'];
            unset($obj['IMP_MORA']);
        }
        if (isset($obj['IMP_PUN'])) {
            $imp_pun = $obj['IMP_PUN'];
            unset($obj['IMP_PUN']);
        }
        if (isset($obj['IMP_SUBS'])) {
            $imp_subs = $obj['IMP_SUBS'];
            unset($obj['IMP_SUBS']);
        }

        $impactar_tasas = $imp_comp || $imp_mora || $imp_pun || $imp_subs;
            
        if ($impactar_tasas) {
            $fecha_imp_tasas = $obj['IMP_FTASAS'];
        }
        
        if (isset($obj['IMP_FTASAS'])) {
            unset($obj['IMP_FTASAS']);
        }
        
        $rtn = $this->mod->sendobj($obj, $checklist, $adjuntos );
        
        if ($impactar_tasas) {
            if(!$imp_comp) {
                $obj['TASA_INTERES_COMPENSATORIA'] = -1;
            }
            if(!$imp_mora) {
                $obj['TASA_INTERES_MORATORIA'] = -1;
            }
            if(!$imp_pun) {
                $obj['TASA_INTERES_POR_PUNITORIOS'] = -1;
            }
            if(!$imp_subs) {
                $obj['TASA_SUBSIDIADA'] = -1;
            }
            
            $_SESSION['CAMBIO_TASAS'] = array(
                'OPERATORIA' => $obj,
                'FECHA' => $fecha_imp_tasas,
                'RESULTADO' => $rtn
            );
            
            $this->mod->guardar_cambio_tasa($obj, $fecha_imp_tasas);
            header("Location: " . '/' . URL_PATH . '/creditos/front/cuotas/impactar_tasas');
            exit();
        }
        
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
    
    function x_get_jefeope(){
        $rtn = $this->mod->get_jefeope();
        return $rtn;
    }
    
    function x_get_cope(){
        $rtn = $this->mod->get_cope();
        return $rtn;
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
            $data['imputacion_tasas'] = TRUE;
        }
        else{
            $data['entidad'] = array("ORGANISMO"=>"","TELEFONO"=>"","CUIT"=>"","DESCRIPCION"=>"","NOMBRE"=>"");
            $data['lst_entidades'] = array('');
            $data['lst_uploads'] = '';
            $data['imputacion_tasas'] = FALSE;
        }
        
        //$data['lst_provincias'] = $this->x_getprovincias();
        $data['lst_tipo_operatoria'] = $this->x_get_tipos_operatoria('',1);
        $data['lst_tipoentidades'] = $this->x_get_tipos_entidades("PAR01='1'");
        $data['lst_jefeope'] = $this->x_get_jefeope();
        $data['lst_coope'] = $this->x_get_cope();
        //log_this('wwwwww.log',print_r($data['lst_jefeope'],1));
        
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
    
    
}

class SelectBox{
	public $items = array();
	public $defaultText = '';
		
	public function __construct($default){
		$this->defaultText = $default;
	}
	
	public function addItem($connection = NULL, $name ){
		$this->items[$name] = $connection;
		return $this; 
	}
	
	public function toJSON(){
		return json_encode($this);
	}
}
