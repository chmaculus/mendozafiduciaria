<?php


class main_controller extends controller{
    
    function main_controller(){
        load_helper("util");
    }
    
    function get_fecha(){
        $dias = array(
            "Domingo", 
            "Lunes", 
            "Martes", 
            "Miercoles", 
            "Jueves", 
            "Viernes", 
            "Sabado");
        
        $meses = array(
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        );
        
        $dia_semana = $dias[date("w")];
        $mes = $meses[date("n")-1];
        $fecha = $dia_semana.", ".date("d")." de ".$mes." del ".date("Y");
        return $fecha;
    }
    
 
    
    function get_file(){
        if (isset($_FILES['imagen'])){
           
            $archivo['tmp'] = $_FILES["imagen"]["tmp_name"];
            $archivo['size'] = $_FILES["imagen"]["size"];
            $archivo['type']    = $_FILES["imagen"]["type"];
            $archivo['name']  = $_FILES["imagen"]["name"];

            $subir = true;
            switch (strtolower($archivo['type'])){
                case "image/jpeg":
                case "image/png":
                case "image/bmp":
                case "image/gif":
                $subir = true;
            }

            if ($subir){
                //echo $archivo['tmp'];
                $file_name = "img".time(); 
                $extencion = substr($archivo['name'], -3);
                $uploaded = TEMP_PATH.$file_name.".".$extencion;
                if (@move_uploaded_file($archivo['tmp'], $uploaded)){
                     echo  '
    <script type="text/javascript">
        parent.render_image("'.$file_name.".".$extencion.'","'.$uploaded.'");
    </script>
';
                }
            }
        }    

    
    
    }
    
    
    function get_describe($tabla){
        $obj = $this->mod->get_describe($tabla);
        return $obj?$obj:array();
    }
    
    
    function x_get_tipos_operatoria($where="",$tipo=0){
        $rtn = $this->mod->get_tipos_operatoria($where);
        if ($tipo==0)
            echo trim(json_encode($rtn?$rtn:array()));
        else
            return ($rtn?$rtn:array());
    }
    
    
    
    function x_add_tipos_operatoria(){
        $obj = $this->mod->add_tipos_operatoria();
        $tmp = $obj?$obj[0]:array();
        echo json_encode($tmp);
    }
    
    function x_add_rolesp(){
        $obj = $this->mod->add_rolesp();
        $tmp = $obj?$obj[0]:array();
        echo json_encode($tmp);
    }
    
    function x_add_areas(){
        $obj = $this->mod->add_areas();
        $tmp = $obj?$obj[0]:array();
        echo json_encode($tmp);
    }

    
    function x_update_tipos_operatoria(){
        $obj = $_POST['obj'];
        $tipo = $obj["TIPO"];
        $iid = $obj["ID"];
        $obj = $this->mod->update_tipos_operatoria($iid,$tipo);
        echo $obj;
    }
    
    function x_update_rolesp(){
        $obj = $_POST['obj'];
        $denominacion = $obj["DENOMINACION"];
        $iid = $obj["ID"];
        $obj = $this->mod->update_rolesp($iid,$denominacion);
        echo $obj;
    }
    
    function x_update_areasu(){
        $obj = $_POST['obj'];
        $denominacion = $obj["DENOMINACION"];
        $iid = $obj["ID"];
        $obj = $this->mod->update_areasu($iid,$denominacion);
        echo $obj;
    }
    
    function x_delete_areasu(){
        $obj = $_POST['obj'];
        $iid = $obj["ID"];
        $obj = $this->mod->delete_areasu($iid);
        echo $obj;
    }
    
    function x_delete_tipos_operatoria(){
        $obj = $_POST['obj'];
        $iid = $obj["ID"];
        $obj = $this->mod->delete_tipos_operatoria($iid);
        echo $obj;
    }
    
    function x_delete_rolesp(){
        $obj = $_POST['obj'];
        $iid = $obj["ID"];
        $obj = $this->mod->delete_rolesp($iid);
        echo $obj;
    }
    
    function x_delete_facturas_cu(){
        $obj = $_POST['obj'];
        $iid = $obj["IID"];
        $obj = $this->mod->delete_facturas_cu($iid);
        echo $obj;
    }
    
    function x_get_checklist($where=""){
        $rtn = $this->mod->get_checklist($where);
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_update_checklist(){
        $obj = $_POST['obj'];
        $obj = $obj[0];
        $iid = $obj['ID'];
        $id_operatoria = $obj["ID_OPERATORIA"];
        $nombre = $obj["NOMBRE"];
        
        $obj = $this->mod->update_checklist($iid,$id_operatoria,$nombre);
        echo $obj;
    }
    
    function x_add_checklist(){
        $obj = $this->mod->add_checklist();
        $tmp = $obj?$obj[0]:array();
        echo json_encode($tmp);
    }

    function x_delete_checklist(){
        $obj = $_POST['obj'];
        $iid = $obj["ID"];
        $obj = $this->mod->delete_checklist($iid);
        echo $obj;
    }
    
    function x_get_operatorias(){
        $rtn = $this->mod->get_info_grid();
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_borrar_file(){
        $ruta = $_POST['ruta'];
        $obj = unlink($ruta);
        return $obj;
    }
    
    function x_getexportar(){
        $data['datos'] = "";
        $data['url_e'] = FILE_EXPORTAR;
        echo $this->view("exportar", $data);
    }
    
    function x_get_dependencia(){
        $tabla = $_POST['tabla'];
        $campo = $_POST['campo'];
        $valor = $_POST['valor'];
        $obj = $this->mod->get_dependencia($tabla,$campo,$valor);
        echo $obj?$obj[0]['cont']:array();
    }
    
    function x_getfideicomisos(){
        $obj = $this->mod->get_fideicomisos();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_ultimoId($tabla){
        $obj = $this->mod->get_ultimoId($tabla);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getclientes(){
        $obj = $this->mod->get_clientes();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getescribanos(){
        $obj = $this->mod->get_escribanos();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_gettasadores(){
        $obj = $this->mod->get_tasadores();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_menu(){
        $menu = array();
        $arr_tmp1 = array();
        $padres = $this->mod->get_menu_padres();
        $c=0;
        if ($padres){
            foreach($padres as $p){
                $hijos = $this->mod->get_menu_hijos($p["ID"]);
                if ($hijos){
                    $menu[$c]["ID"] = $p["ID"];
                    $menu[$c]["NOMBRE"] = $p["NOMBRE"];
                    $arr_tmp = array();
                    
                    foreach($hijos as $h){
                        $arr_tmp[] = array("ID"=>$h["ID"],"NOMBRE"=>$h["NOMBRE"],"URL"=>$h["URL"]);
                        $arr_t = explode("/",$h["URL"]);
                        $name_mod = end($arr_t);
                        $arr_tmp1[] = array("ID"=>$h["ID"],"URL"=>$name_mod);
                    }
                    $menu[$c]["HIJOS"] = $arr_tmp;
                }
                $c++;
            }
        }
        $menu['menu'] = $menu;
        $menu['mods'] = $arr_tmp1;
        
        //log_this('log/qqqqq.log', print_r($menu,1));
        
        
        $tmp = $menu?$menu:array();
        return $tmp;
    }
    
    function x_get_menu_arbol(){
        $menu = $this->mod->get_menu_arbol();
        $tmp = $menu?$menu:array();
        return $tmp;
    }
    
    function x_get_etapas(){
        $etapas = $this->mod->get_etapas();
        $tmp = $etapas?$etapas:array();
        return $tmp;
    }

    public function constructor(){
        
        if ($this->get_controller_name()=='login'){
            return true;
        }
        
        if ($this->get_controller_name()=='formalta'){
            return true;
        }
        
        //leer estado update de rol
        if (isset($_SESSION["USER_ROL"])){
            $estado_update = $this->mod->get_update_rol( $_SESSION["USER_ROL"] );
            
            if ($estado_update==1){
                $this->mod->update_rol_update($_SESSION["USER_ROL"],"0");
                
                if ( $_SESSION['REDIR']=='-1' ){
                    unset($_SESSION['REDIR']);
                    header("Location: /".URL_PATH."backend/login/logout");
                    die();
                }
                
            }
        }
        
        $this->setPlug( array("jmenu"));
        
        //obtener nombre de modulo
        $name_modulo_local = $this->get_controller_name();
        $modulos = $_SESSION["MODS"];
        if ($modulos and $name_modulo_local<>'dashboard' and $_SESSION["USER_USERNAME"]<>'admin'){
            foreach($modulos as $m=>$v){
                if ($v["URL"]==$name_modulo_local){
                    $sw = 1;
                    $id_modulo = $v["ID"];
                    break;
                }
            }
            
            if (PERMISOS_MENU_ALL!=1){
                //verificiar q tenga permiso
                if ( $name_modulo_local!="carpetash1" && $name_modulo_local!="carpetash" && !in_array($id_modulo,$_SESSION["USER_PERMISOS_MENU"])){
                    
                    //no tiene permiso
                    header("Location: " . '/'.URL_PATH);
                    die();
                }
            }
            
        }
        
        
    }
    
      
}

?>
