<?php

class busqueda extends main_controller{
    function busqueda(){
        $this->mod = $this->model("busqueda_model");
    }
    
    function init($filtro, $modulo){
        //$this->_get_loged();
        $this->setCss( array("init.css") );
        $this->setJs( array( "init.js") );
        
        $this->setPlug( array("quicksearch"));        
        
        $datax = array();
        $datax['main'] = $this->_obtener_main($filtro, $modulo);
        $datax['titulo']= "Administracion";
        
        $this->_js_var['_MODULO'] = $filtro;
        $this->_js_var['_FILTRO'] = $filtro;
        
        $this->render($datax);
    }
    
    function _obtener_main($filtro, $modulo){
        
        switch($modulo){
            case "clientes" : break;
            case "permisos" : 
                $items = $this->mod->get_permisos($filtro);
                break;
        }
        
        $data['items'] = $items;
        $this->_js_array['ITEMS'] = $items;
        return $this->view("search/busqueda_".$modulo, $data);
    }
    
}


