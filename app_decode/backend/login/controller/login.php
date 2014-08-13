<?php

class login extends main_controller{
    function login(){
        $this->mod = $this->model("login_model");
    }
    
    function init(){
        
        $this->constructor();
        //$this->_get_loged();
        $this->setCss( array("init.css") );
        $this->setJs( array( "init.js") );
        
        $this->setPlug(array("jalerts"));
        $this->setPlug(array("validation"));
        
        $datax = array();
        $datax['main'] = $this->_obtener_main();
        $datax['titulo']= "Administracion";

        $this->set_layout("login_main.php");            
        $this->render($datax);
        
        
    }

    function _obtener_main(){
        $data['fecha'] = $this->get_fecha();
        $data['modulo'] =  "";
        
        return $this->view("login", $data);
    }
    
    function x_send_login(){
               
        $user = $_POST['user'];
        $pass = $_POST['pass'];
              
        $ret = $this->mod->send_login($user, $pass);
        
        if ($ret){
            $_SESSION["USERADM"] = $ret[0]['ID'];
            $_SESSION["USER_NOMBRE"] = $ret[0]['NOMBRE'];
            $_SESSION["USER_APELLIDO"] = $ret[0]['APELLIDO'];
            $_SESSION["USER_NA"] = $ret[0]['NOMBRE'] . " " . $ret[0]['APELLIDO'];
            $_SESSION["USER_USERNAME"] = $ret[0]['USERNAME'];
            $_SESSION["USER_ROL"] = $ret[0]['ID_ROL'];
            $_SESSION["USER_AREA"] = $ret[0]['ID_AREA'];
            $_SESSION["USER_PUESTO"] = $ret[0]['ID_PUESTO'];
            $_SESSION["USER_SU_1"] = $ret[0]['SU_1'];//carga vigente
            $_SESSION["USER_SU_2"] = $ret[0]['SU_2'];//edicion vigente
            $_SESSION["USER_SU_3"] = $ret[0]['SU_3'];//vestirse de
            $_SESSION["USER_SU_4"] = $ret[0]['SU_4'];//hacia atras
            $_SESSION["USER_SU_5"] = $ret[0]['SU_5'];//carga historica
            $_SESSION["USER_SU_6"] = $ret[0]['SU_6'];//edicion historica
            
            //permisos
            $tmp = $this->mod->get_permisos($ret[0]['ID']);
            $arr_permisos = array();
            if ($tmp){
                foreach ( $tmp as $rolespermisos) {
                    $arr_permisos[$rolespermisos["ID_PERMISO"]] = array(
                        "MOSTRAR"=>$rolespermisos["MOSTRAR"],
                        "ALTA"=>$rolespermisos["ALTA"],
                        "BAJA"=>$rolespermisos["BAJA"],
                        "MODIFICACION"=>$rolespermisos["MODIFICACION"],
                        "EXPORTAR"=>$rolespermisos["EXPORTAR"],
                        "OTROS"=>$rolespermisos["OTROS"],
                        "VER"=>$rolespermisos["VER"]
                    );

                    /*
                    $arr_permisos[$rolespermisos["ID_PERMISO"]] = array(
                        "MOSTRAR"=>1,
                        "ALTA"=>1,
                        "BAJA"=>1,
                        "MODIFICACION"=>1,
                        "EXPORTAR"=>1,
                        "OTROS"=>1,
                        "VER"=>1
                    );*/
                    
                }
            }
            
            //menu
            $tmp = $this->mod->get_permisos_menu($ret[0]['ID']);
            $arr_permisos_menu = array();
            if ($tmp){
                foreach ( $tmp as $key=>$rolesmenu) {
                    $arr_permisos_menu[] = $rolesmenu["ID_MENU"];
                }
            }
            
            //etapas
            $tmp = $this->mod->get_permisos_etapas($ret[0]['ID']);
            $arr_permisos_etapas = array();
            if ($tmp){
                foreach ( $tmp as $key=>$rolesetapas) {
                    $arr_permisos_etapas[] = $rolesetapas["ID_ETAPA"];
                }
            }
            
            $_SESSION["USER_PERMISOS_ETAPAS"] = $arr_permisos_etapas;
            $_SESSION["USER_PERMISOS_MENU"] = $arr_permisos_menu;
            $_SESSION["USER_PERMISOS"] = $arr_permisos;
            //$menu_array = $this->x_get_menu();
            $menu_array = $this->x_get_menu();
            $_SESSION["USER_MENU"] = $menu_array["menu"];
            $_SESSION["MODS"] = $menu_array["mods"];
           
        }
        
        echo trim(json_encode($ret?$ret[0]:array()));
    }
    
    function logout(){
        unset( $_SESSION["USERADM"],$_SESSION['_LOGIN'],$_SESSION['USER_ROL'],$_SESSION['REDIR'] );
        js_redirect("/".URL_PATH);
    }
    
    

}