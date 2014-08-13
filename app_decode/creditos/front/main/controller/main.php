<?php

class main extends main_controller {

    function main() {
        $this->mod = $this->model("formalta_model");
    }

    function init($id = 0) {
        $this->mod->initClassModel($id);
        //etapas
    }
    function listaCuotas($id = 0) {
        $credito = $this->mod->getCreditoClass($id);

        echo $this->view("result_events",array("credito"=>$credito));
        //etapas
    }
    
    function deudas($fecha = false){
        
        if ($fecha){
            list($d,$m,$y) = explode("-",$fecha);
            $fecha = mktime(0,0,0,$m,$d,$y);
        }
        else{
            $fecha = time();
            list($d,$m,$y) = explode("-",date("d-m-Y",$fecha) );
            $fecha = mktime(0,0,0,$m,$d,$y);            
        }
        
        
        
        
        $this->mod->getCreditosDeudores($fecha);
    }
}
