<?php

class usuarios extends main_controller{
    function usuarios(){
        $this->mod = $this->model("usuarios_model");
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
        $this->setPlug( array("numeric"));
        $this->setPlug( array("validation"));
        $this->setPlug( array("fancybox"));
        $this->setPlug( array("jqgrid"));
        
        $id_permiso = 16; // permiso de acceso a este modulo (fid_permisos)
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
        $datax['etiqueta_modulo'] = "Usuarios";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        
        $this->render($datax);
    }

    function _obtener_main($arr_permiso_mod){
        $data['fecha'] = $this->get_fecha();
        $data['etiqueta_mod'] =  "Cliente";
        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ',$data['hora_actual']));
        $data['hora_bd'] = $data['hora_actual'];
        $arr_campos = array("NOMBRE","TIPO","DESCRIPCION","TOPE_PESOS","TASA_INTERES_COMPENSATORIA","TASA_INTERES_MORATORIA","TASA_INTERES_POR_PUNITORIOS","TASA_SUBSIDIADA","DESEMBOLSOS","DEVOLUCIONES","PERIODICIDAD");
        $this->_js_array['_campos'] = $arr_campos;
        
        /* permiso mostrar */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("usuarios", $data);
        else
            return $this->view("error404",array(),"backend/dashboard");
        /* permiso mostrar*/
        
    }
    
    function x_getform_addentidad(){
        $obj = isset($_POST['obj'])?$_POST['obj']:array();
        $tmp = $this->mod->get_obj($obj);
        $data['cad'] = "";
        $chk_array = array();
        if ($tmp){
            $data['entidad'] = $tmp[0];
            $data['obj_js'] = json_encode(  $tmp[0] );
        }else{
            $data['entidad'] = array("ORGANISMO"=>"","TELEFONO"=>"","CUIT"=>"","DESCRIPCION"=>"","NOMBRE"=>"");
        }
        $data['lst_roles'] = $this->x_get_roles();
        $data['lst_permisos'] = $this->x_get_permisos();
        $data['lst_areas'] = $this->x_get_areas(1);
        
        echo $this->view("form", $data);
    }
    
    function x_get_roles(){
        $obj = $this->mod->get_roles();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_areas($tipo=0){
        $rtn = $this->mod->get_areas();
        if($tipo==0)
            echo trim(json_encode($rtn?$rtn:array()));
        else
            return $rtn?$rtn:array();
    }
    
    function x_get_rolesp($where="",$tipo=0){
        $rtn = $this->mod->get_rolesp($where);
        if ($tipo==0)
            echo trim(json_encode($rtn?$rtn:array()));
        else
            return ($rtn?$rtn:array());
    }
    
    /*
    function x_get_areas(){
        $rtn = $this->mod->get_areas();
        echo trim(json_encode($rtn?$rtn:array()));
    }
    */
    
    function x_get_permisos(){
        $obj = $this->mod->get_permisos();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_info_grid(){
        $rtn = $this->mod->get_info_grid();
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_sendobj(){
        $obj = $_POST['obj'];
        $rtn = $this->mod->sendobj($obj);
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_delobj(){
        $iid = $_POST['id'];
        $rtn = $this->mod->delobj($iid);
        echo '1';
    }
    
    function x_getform_roles(){
        $data['datos'] = "";
        echo $this->view("roles", $data);
    }
    
    function x_getform_areas(){
        $data['datos'] = "";
        echo $this->view("areas", $data);
    }
    
    function x_getpuestos(){
        $idp = $_POST['idp'];
        $lst_sr = $this->mod->getpuestos($idp);
        
        $productSelect = new SelectBox('Elegir Puesto');

        if (is_array($lst_sr)):
            $productSelect = new SelectBox('Elegir Puesto');
            $c=1;
            foreach($lst_sr as $rs_sr):
                $productSelect->addItem($rs_sr["ID"],$rs_sr["DENOMINACION"]);
                $c++;
            endforeach;
        endif;
        echo json_encode($productSelect);
        die();
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

