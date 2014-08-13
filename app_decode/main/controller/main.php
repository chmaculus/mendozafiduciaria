<?php

class main extends main_controller{
    function main(){
        $this->mod = $this->model("home_model");
    }
    
    function init(){
        
        //$this->_get_loged();
        $this->setCss( array("init.css") );
        $this->setJs( array( "init.js" ));
        
        $datax = array();
        $datax['main'] = $this->_obtener_main();
        $datax['titulo']= "Administracion";
        
                
        $this->_js_var['_LOGIN'] = 0;
        
        if (isset($_SESSION["USERADM"]['ID'])):
            $this->_js_var['_LOGIN'] = 1;
            $this->_js_var['_USER'] = $_SESSION["USERADM"]['ID'];
        endif;

        $sw = 1;
        
        if (!isset($_SESSION["USERADM"]))
            $this->set_layout("login_main.php");
        
        if (isset($_SESSION["USERADM"]['ID']))
            header("Location: backend/dashboard");
        
        
        $this->render($datax);
        
    }
    
    function _obtener_main(){
        $data['fecha'] = $this->get_fecha();
        $data['modulo'] = array("1","2");
        
        return $this->view("main", $data);
    }
    
    
    
}