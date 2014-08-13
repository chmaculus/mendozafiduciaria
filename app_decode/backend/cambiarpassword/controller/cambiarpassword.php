<?php

class cambiarpassword extends main_controller{
    function cambiarpassword(){
        $this->mod = $this->model("cambiarpassword_model");
    }
    
    function init(){
        //$this->constructor();
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
        
        return $this->view("cambiarpassword", $data);
    }
    
    function x_send_change(){
        $val1 = $_POST['val1'];
        $val2 = $_POST['val2'];
        $iid = $_SESSION["USERADM"];
        
        $ret = $this->mod->send_change($iid, $val1, $val2);
        if ($ret){
            echo 1;
        }else{
            echo 0;
        }
    }

}