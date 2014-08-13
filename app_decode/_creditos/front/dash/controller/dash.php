<?php



class dash extends main_controller {

    function dash() {
        $this->mod = $this->model("dash_model");
    }

    function init($credito = 0) {
        // $this->mod->set_credito_active($credito);
        //$this->mod->set_version_active();

        $this->setCss(array("dash.css"));
        $this->setJs(array("dash.js"));



        $this->_js_var['ID_CREDITO'] = $credito;
        $this->_js_var['FECHA'] = time();

        $datax = array();
        $datax['main'] = $this->_obtener_main($credito);
        $datax['titulo'] = "Administracion";
        $datax['name_modulo'] = $this->get_controller_name();
        $datax['filtro'] = "";

        $this->render($datax);
    }

    function _obtener_main($credito) {


        //$this->mod->set_credito_active($credito);
        //$this->mod->set_version_active();
        //$this->mod->renew_datos();
//        print_array($cuotas);
        return $this->view("dash");
    }
    
    function _obtener_vencimientos($fecha = false){
        $this->mod->get_desembolsos_pendientes($fecha = false);
    }
    
    function _obtener_desembolsos($fecha = false){
        $desembolsos = $this->mod->get_desembolsos_pendientes();
        
    }
    
    function _obtener_perdida_subsidio($fecha = false){
        
    }


}

    

