<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of creditosopciones
 *
 * @author dw
 */
class creditosopciones extends main_controller {
    
    
    function creditosopciones(){
        $this->mod = $this->model("creditosopciones_model");
    }    
    
    function init($creditos){
        $creditos = explode(",",$creditos);
        $this->_js_array['CREDITOS']= $creditos;
        $this->setJs(array("creditosopciones.js"));
        $this->setCss(array("creditosopciones.css"));
        
        
        $datax['titulo'] = "";
        $datax['main'] = $this->getOpciones();
        $this->render($datax);
    }
    
    function getOpciones(){
        $data = array("banco"=>0,"convenio"=>"");
        if (count($this->_js_array['CREDITOS'])==1){
           
            $this->mod->set_credito_active($this->_js_array['CREDITOS'][0]);
            $this->mod->set_version_active();
            $rtn = $this->mod->get_creditos_opciones();
            $data['banco'] = isset($rtn['banco']['VALOR']) ? $rtn['banco']['VALOR'] : 0;
            $data['convenio'] = isset($rtn['convenio']['VALOR'])?$rtn['convenio']['VALOR'] : "";
            
        }
        return $this->view("opciones_main",$data);
    }
    
    function x_guardar_opciones(){
        $opciones = isset($_POST['opciones'])?$_POST['opciones']:array();
        $creditos = $_POST['creditos'];
        
        $this->mod->guardar_opcion($creditos, $opciones);
    }
    //put your code here
}
