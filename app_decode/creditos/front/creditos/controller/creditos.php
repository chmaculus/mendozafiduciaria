<?php

class creditos extends main_controller{
    function creditos(){
        $this->mod = $this->model("credito_informes_model");
    }
    
    function init(){
        $this->constructor();
        if ( !isset($_SESSION["USERADM"]))
            header("Location: " . '/'.URL_PATH);
        
        $this->setCss( array("init.css","informes.css","informes_cuota.css") );
        $this->setJs( array( "creditos.js") );
        $this->setPlug( array("chosen","jalerts","numeric","validation","fancybox","jqgrid"));
        
        $arr_permiso_mod = $this->_init();
        $datax = array();
        $datax['main'] = $this->_obtener_main($arr_permiso_mod);
        $datax['titulo']= "Administracion";
        $datax['etiqueta_modulo'] = "Créditos";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USUARIO_SESION_ACTUAL'] = $_SESSION["USERADM"];
        $this->_js_var['_USER_AREA'] = $_SESSION["USER_AREA"];
        $this->_js_var['_USER_PUESTO'] = $_SESSION["USER_PUESTO"];
        $this->_js_var['_USER_ROL'] = $_SESSION["USER_ROL"];

        $this->_js_var['FECHA'] = time();
        
        $this->render($datax);
        //etapas
    }
    
    function _init() {
        $id_permiso = 12;
        $arr_permiso_mod = isset($_SESSION["USER_PERMISOS"][$id_permiso])?$_SESSION["USER_PERMISOS"][$id_permiso]:0;
        $_SESSION["USER_ROL"]=0;
        if(PERMISOS_ALL==1){
            $arr_permiso_mod = array
            (
                "MOSTRAR" => 1,
                "ALTA" => 1,
                "BAJA" => 1,
                "MODIFICACION" => 1,
                "EXPORTAR" => 1,
                "OTROS" => 1
            );
        }
        /* permiso alta */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['ALTA'] == 1)
            $this->_js_var['_permiso_alta'] = 1;
        else
            $this->_js_var['_permiso_alta'] = 0;
        /* permiso alta*/
        
        /* permiso alta */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['BAJA'] == 1)
            $this->_js_var['_permiso_baja'] = 1;
        else
            $this->_js_var['_permiso_baja'] = 0;
        /* permiso alta*/
        
        /* permiso alta */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MODIFICACION'] == 1)
            $this->_js_var['_permiso_modificacion'] = 1;
        else
            $this->_js_var['_permiso_modificacion'] = 0;
        /* permiso alta*/
        
        return $arr_permiso_mod;
    }

    function _obtener_main($arr_permiso_mod){
        $data['fecha'] = $this->get_fecha();

        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ',$data['hora_actual']));
        $data['hora_bd'] = $data['hora_actual'];
        
        $arr_campos = array("DENOMINACION");
        $this->_js_array['_campos'] = array();
        
        /* permiso mostrar */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("creditos", $data);
        else
            return $this->view("error404",array(),"backend/dashboard");
        /* permiso mostrar*/
    }
    
    function x_get_informes(){
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active();
        
        
        
        echo $this->view("informes");
    }
    
    function x_get_desembolsos(){
        $credito_id = $_POST['credito_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active();
        
        $this->mod->renew_datos();
        
        $monto_credito = $this->mod->get_monto_credito();
        
        $desembolsos = $this->mod->get_desembolsos();    
        
        $desembolsado = 0;
        foreach($desembolsos as $desembolso){
            $desembolsado += $desembolso['MONTO'];
        }
        
        echo $this->view("informes/desembolsos", array("desembolsos"=>$desembolsos,"MONTO_CREDITO"=>$monto_credito,"DESEMBOLSADO"=>$desembolsado ) );
    }
    
    function x_get_cobranzas(){
        $credito_id = $_POST['credito_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active();
        
        $this->mod->renew_datos();
        
        $desembolsos = $this->mod->get_pagos();    
        
        echo $this->view("informes/pagos", array("pagos"=>$desembolsos) );
    }
    
    function x_obtener_pago(){
     /*  $credito_id = $_POST['credito_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        
        $this->mod->set_credito_active($credito_id);
        //$version = $_POST['version_id'];
        $this->mod->set_version_active();
        
        $id_variacion = $_POST['evento_id'];
        $this->mod->renew_datos();
        $cuotas = $this->mod->get_pago($id_variacion);
        //print_array($cuotas);
        echo $this->view("informes/pago_lista_",array("cuotas"=>$cuotas ));*/
    }    
    
    
    function x_obtener_gastos(){
        $credito_id = $_POST['credito_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        
        $this->mod->set_credito_active($credito_id);
        //$version = $_POST['version_id'];
        $this->mod->set_version_active();
        
        $this->mod->renew_datos();
        $gastos = $this->mod->get_gastos($credito_id);
        echo $this->view("informes/gastos",array("gastos"=>$gastos));
    }    
    /*
    function x_obtener_cuotas(){
        $credito_id = $_POST['credito_id'];
        $chequera = $_POST['chequera'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        
        $this->mod->set_credito_active($credito_id);
        //$version = $_POST['version_id'];
        $this->mod->set_version_active();

        resetlog();
        
        $this->mod->set_fecha_actual($fecha);
        $this->mod->set_fecha_calculo();
        
        $this->mod->renew_datos();
        $this->mod->save_last_state(false);
        $this->mod->generar_evento( array(), true, $fecha);
        
        //tercer parametro true para forzar la deuda con el compensatorio total
        $ret_reuda= $this->mod->get_deuda($fecha, true, $chequera==1 );
        
        logthis("DEUDA".  microtime(),$ret_reuda);
        $ret_reuda['fecha_actual'] = $fecha;
     
        echo $this->view("informes/cuotas",$ret_reuda);
    }   
    
    function x_get_evolucion_cuota(){
        $credito_id = $_POST['credito_id'];
        $cuota_id = $_POST['cuota_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        
        $this->mod->set_credito_active($credito_id);
        //$version = $_POST['version_id'];
        $version = $this->mod->set_version_active();
        

        $this->mod->renew_datos();
        $this->mod->save_last_state(false);
        
        $eventos = $this->mod->evolucion_cuota($cuota_id, $fecha);
        echo $this->view("informes/cuotas_evolucion",array("eventos"=>$eventos) );
    }
    
    */
    function x_obtener_tasas(){
        $credito_id = $_POST['credito_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        $this->mod->set_credito_active($credito_id);
        //$version = $_POST['version_id'];
        $this->mod->set_version_active();
        
        $this->mod->renew_datos();
        
        
        $tasas = $this->mod->obtener_tasas();
        
        echo $this->view("informes/tasas",array("tasas"=>$tasas) );        
    }
    
    function x_eliminar_credito(){

        $creditos = $_POST['creditos'];
        foreach($creditos as $credito_id){
            $this->mod->set_credito_active($credito_id);
            //$this->mod->set_version_active();
            $this->mod->borrar_credito();
        }
        //$this->mod->borrar_credito_soft();
    }
    
    function borrar_credito(){
        
    }
    
    function resumen_moratorias() {
        set_time_limit(0);
        $this->constructor();
        if ( !isset($_SESSION["USERADM"]))
            header("Location: " . '/'.URL_PATH);
        
        $this->setCss( array("init.css","resumen_cuenta.css") );
        $this->setJs( array( "creditos.js") );
        $this->setPlug( array("chosen","jalerts","numeric","validation","fancybox","jqgrid"));
        
        $arr_permiso_mod = $this->_init();
        $datax = array();
        $datax['main'] = $this->_resumen_moratorias($arr_permiso_mod);
        $datax['titulo']= "Administracion";
        $datax['etiqueta_modulo'] = "Créditos - Moratorias";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USUARIO_SESION_ACTUAL'] = $_SESSION["USERADM"];
        $this->_js_var['_USER_AREA'] = $_SESSION["USER_AREA"];
        $this->_js_var['_USER_PUESTO'] = $_SESSION["USER_PUESTO"];
        $this->_js_var['_USER_ROL'] = $_SESSION["USER_ROL"];

        $this->_js_var['FECHA'] = time();
        
        $this->render($datax);
    }
    
    function _resumen_moratorias($arr_permiso_mod) {
        $creditos_moratorias = $this->mod->getCreditosMoratorios();
        
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("resumen_moratorias", array("creditos_moratorias" => $creditos_moratorias));
        else
            return $this->view("error404",array(),"backend/dashboard");
        
    }

}
