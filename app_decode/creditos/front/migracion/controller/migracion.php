<?php

class migracion extends main_controller{
    function migracion(){
        $this->mod = $this->model("migracion_model");
    }
    
    function init(){
        $this->constructor();

        //$this->_get_loged();
        $this->setCss( array("migracion.css") );
        //$this->setJs( array( "init.js",'forms.js') );
        $this->setJs( array( "migracion.js") );
        
        
        $datax = array();
        $datax['main'] = $this->_obtener_main();
        $datax['titulo']= "Administracion";
        $datax['etiqueta_modulo'] = "Carpetas";
        $datax['name_modulo'] = $this->get_controller_name();

        
        $this->render($datax);
        //etapas
    }

    function _obtener_main(){

        $creditos = $this->mod->get_operaciones();

        return $this->view("migracion_view",array("creditos"=>$creditos));
    }
    
    function x_migrar_creditos(){
        $creditos = $_POST['creditos'];
        print_array($creditos);
        $datos = array();
        foreach($creditos as $credito){
            $this->mod->set_credito_active($credito);
            $this->mod->borrar_credito();       
            $datos[] = $this->mod->get_data_migracion($credito);
        }
        
        //print_array($datos);
    }
  
    

}
