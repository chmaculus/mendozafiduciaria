<?php

class estructura extends main_controller{
    function estructura(){
        $this->mod = $this->model("estructura_model");
    }
    
    function init($credito = 0, $mod = 1){
       // $this->mod->set_credito_active($credito);
        //$this->mod->set_version_active();
        
        $this->setCss( array("estructura.css") );
        $this->setJs( array("estructura.js") );
        
        
        
        $this->_js_var['ID_CREDITO'] = $credito;
        $this->_js_var['FECHA'] = time();
        $this->_js_var['MODIFICAR'] = $mod;
        
        $datax = array();
        $datax['main'] = $this->_obtener_main($credito);
        $datax['titulo']= "Administracion";
        $datax['name_modulo'] = $this->get_controller_name();
        $datax['filtro'] = "";

        $this->render($datax);
    }
    
    function _obtener_main($credito){
        
        
        $this->mod->set_credito_active($credito);
        $this->mod->set_version_active();

        $this->mod->renew_datos();
            
        $cuotas = $this->mod->get_cuotas_estructura();
        $this->_js_array['CUOTAS'] = $cuotas;
        $cantidad_cuotas = count($cuotas);
        
//        print_array($cuotas);
        return $this->view("estructura", array("cuotas"=>$cuotas,"cantidad"=>$cantidad_cuotas));
    }
    
    
    
    
    function x_guardar_montos(){
        $cuotas = $_POST['cuotas'];
        $credito_id = $_POST['credito_id'];
        $this->mod->save_estructura($cuotas);
        $this->_recalcular_pagos($credito_id, NO_FECHA);
        $this->mod->get_segmentos_cuota();
        
    }
    
    function x_guardar_cuotas_excel() {
        
    }
    
    function _guardar_cuotas_excel() {
        $_SESSION['msg_err'] = "";
        $excel = $_FILES['fexcel'];
        if (isset($excel['tmp_name']) && is_file($excel['tmp_name'])) {

            require_once(MODULE_DIRECTORY . 'PHPExcel/PHPExcel.php');
            require_once(MODULE_DIRECTORY . 'PHPExcel/PHPExcel/Reader/Excel2007.php');
            
            $objReader = new PHPExcel_Reader_Excel2007();
            if ($objPHPExcel = $objReader->load($excel['tmp_name'])) {
                $objPHPExcel->setActiveSheetIndex(0);
                $arr_creditos = array();
                
                for ($j = 7; $j <= $objPHPExcel->getActiveSheet()->getHighestDataRow(); $j++) {
                    $credito_id = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getCalculatedValue();
                    if (!$credito_id) {
                        break;
                    }
                    
                    $arr_creditos[$credito_id]['FV'] = $credito_id = $objPHPExcel->getActiveSheet()->getCell("D" . $j)->getCalculatedValue();
                    $arr_creditos[$credito_id]['MONTO'] = $credito_id = $objPHPExcel->getActiveSheet()->getCell("E" . $j)->getCalculatedValue();
                }
                
                foreach ($arr_creditos as $credito) {
                    //obtener array de cuotas
                    $this->mod->set_credito_active($credito);
                    $this->mod->set_version_active();
                    $this->mod->renew_datos();
                    $cuotas = $this->mod->get_cuotas_estructura();
                    
                    print_r($cuotas);
                    $_POST = array();
                    die();
                }
                die("aca");
            }
        }
        
        $_SESSION['msg_err'] = "Hubo un problema al cargar el archivo";
        return FALSE;
    }
    
    function x_guardar_opciones_cuota(){
     //   $inicio = $_POST['fecha_inicio'];
        $vencimiento = $_POST['fecha_vencimiento'];
        $cuotas_restantes = $_POST['cuotas_restantes'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        $this->mod->renew_datos();
        
        
        $this->mod->modificar_fecha_cuota( $cuotas_restantes, false, $vencimiento);
        
        $this->mod->get_segmentos_cuota();
        $this->mod->renew_datos();
        $this->_recalcular_pagos(NO_FECHA);
        $this->mod->get_segmentos_cuota();
        
    }
    function x_abrir_opciones_cuota(){
        $credito_id = $_POST['credito_id'];
        $cuotas_restantes = $_POST['cuotas_restantes'];
        
        $this->mod->set_credito_active($credito_id);
        //$version = $_POST['version_id'];
        $this->mod->set_version_active();
        $this->mod->renew_datos();
        $cuota = $this->mod->get_cuota($cuotas_restantes);
        //print_array($cuota);
        echo $this->view("opciones_cuota",$cuota);
    }       
    
    function _recalcular_pagos($credito_id, $fecha = false){
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active();
        $this->mod->renew_datos();
        
        $this->mod->save_last_state(true);
        $this->mod->set_fecha_actual($fecha);
        
        $pagos = $this->mod->desimputar_pago();
        
        foreach($pagos as $pago){
            $this->realizar_pago($pago['fecha'], $pago['monto']);
        }
    }    
    
}





