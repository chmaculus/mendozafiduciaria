<?php

class dashboard extends main_controller{
    
    function dashboard(){
        $this->mod = $this->model("login_model");
    }
    
    function init($vini=''){
        
        if ($vini==2){
            //login normal
            $_SESSION['REDIR'] = -1;
        }else{
            
        }
        $this->constructor();
        
        if ( !isset($_SESSION["USERADM"]))
            header("Location: " . '/'.URL_PATH);
        
        //$this->_get_loged();
        $this->setCss( array("init.css") );
        $this->setJs( array( "init.js") );
        $this->setPlug( array("jalerts","fancybox"));
        
        $datax = array();
        $datax['main'] = $this->_obtener_main();
        $datax['titulo']= "Administracion";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->set_layout("dashboard.php");
        
        $this->render($datax);
    }
    

    
    function _obtener_main(){
                
        $data['fecha'] = $this->get_fecha();
        $data['modulo'] =  "";
        
        return $this->view("dashboard", $data);
    }
    
    
}


