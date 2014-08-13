<?php

class creditos extends main_controller{
    function creditos(){
        $this->mod = $this->model("creditos_model");
    }
    
    function init(){
        
        if ( !isset($_SESSION["USERADM"]))
            header("Location: " . '/'.URL_PATH);
        //$this->_get_loged();
        $this->setCss( array("init.css") );
        $this->setJs( array( "init.js") );
        $this->setPlug( array("chosen"));
        $this->setPlug( array("jalerts"));
        $this->setPlug( array("datatables"));
        $this->setPlug( array("validation"));
                
        $datax = array();
        $datax['main'] = $this->_obtener_main();
        $datax['titulo']= "Administracion";
        $datax['etiqueta_modulo'] = "Creditos";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        //$this->set_layout("admin_main.php");            

        $this->render($datax);
    }

    function _obtener_main(){
        $data['fecha'] = $this->get_fecha();
        $data['modulo'] =  "";
        $data['etiqueta_mod'] =  "Creditos";
                
        return $this->view("creditos", $data);
    }
   
    function x_get_datatable(){
        $data = $this->mod->get_datatable(array("ID","DENOMINACION","ID"));
        echo json_encode($data);
    }     
    
    function x_getobj(){
        $iid = $_POST['id'];
        $obj = $this->mod->get_obj($iid);
        echo trim(json_encode($obj?$obj[0]:array()));
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
    
}