<?php
require_once(MODULE_DIRECTORY.'tcpdf/tcpdf.php');
require_once(MODULE_DIRECTORY.'tcpdf/tcpdf_barcodes_1d.php');

class cuotas extends main_controller{
    function cuotas(){
        parent::__construct();
        //resetlog();
        $this->mod = $this->model("credito_model_test");
    }
    
    function init($credito = 0, $mod = 1){
       // $this->mod->set_credito_active($credito);
        //$this->mod->set_version_active();
        
        $this->setCss( array("creditos.css","cuotas.css", "opciones_cuota.css", "form_generar.css") );
        $this->setPlug( array("fancybox","jqgrid","jalerts"));
        $this->setJs( array( "creditos.js","generar.js") );
        
        $this->_js_var['ID_CREDITO'] = $credito;
        $this->_js_var['FECHA'] = time();
        $this->_js_var['MODIFICAR'] = $mod;
        
        $datax = array();
        $datax['main'] = $this->_obtener_main($credito);
        
        $estado = $this->mod->get_estado_credito();
        $this->_js_var['ESTADO']  = $estado;
        
        $datax['titulo']= "Administracion";
        $datax['name_modulo'] = $this->get_controller_name();
        $datax['filtro'] = "";

        $this->render($datax);
    }
    
    function _obtener_main($credito){
        if ($credito==0){
            return $this->view("form_generar", array());
        }
        else{
            $fupdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - (3600 * 24 - 5 * 60)); //dejamos 5 min para
            $this->mod->fupdate_credito($credito, $fupdate);
            
            $this->mod->set_credito_active($credito);
            $this->mod->set_version_active();
            
            $versiones = $this->mod->get_versiones();

            $this->mod->renew_datos();
            
            $this->_js_array['VERSIONES'] = $versiones;
         
            $cuotas = $this->mod->get_cuotas_credito();
            
            $cuotas['versiones'] = $versiones;
            

            return $this->view("form_credito", $cuotas);
        }
    }
    
    function _get_cuotas($ret = false){
        $cuotas = $this->mod->get_cuotas_credito();
        if (!$ret){
            echo $this->view("lista_cuotas",$cuotas);
        }
        else{
            return $this->view("lista_cuotas",$cuotas);
        }
    }
    
    
    
    function x_set_pago(){
        if (!$this->_x_set_pago($_POST['credito_id'], $_POST['fecha'], $_POST['monto'], $_POST['version_id'])) {
            die('-1');
        }
        
        $this->mod->renew_datos();
        echo $this->_get_cuotas();
    }
    
    function _x_set_pago($credito_id, $fecha, $monto, $version) {
        
        if ($this->mod->set_credito_active($credito_id)) {
            $monto = (float) $monto;
            
            if (!$monto || $monto <= 0) {
                die("-2");
            }
            
            $this->mod->set_version_active($version);
            $this->mod->renew_datos();

            $monto_credito = $this->mod->get_monto_credito();
            $desembolsos = $this->mod->get_desembolsos(0);
            $desembolsado = 0;
            
            foreach($desembolsos as $desembolso){
                $desembolsado += $desembolso['MONTO'];
            }
            
            if ($desembolsado == 0 || ($monto_credito - $desembolsado)) {
                return FALSE;
            }
            
            $this->mod->set_fecha_actual($fecha);
            $pagos = $this->mod->desimputar_pago();
            
            $this->mod->realizar_pago($fecha,  $monto);
            
            if ($pagos) {
                set_time_limit(0);
                foreach($pagos as $pago){
                    $this->mod->realizar_pago($pago['fecha'], $pago['monto']);
                }
            }
                
            return TRUE;
        }
        return FALSE;
    }
    
    
    function x_agregar_desembolso(){
        $credito_id = $_POST['credito_id'];
        $version_id = $_POST['version_id'];
        $tipo = $_POST['tipo'];
        
        $data = array();
        $data['monto'] = $_POST['monto'];
        
        $this->mod->set_credito_active($credito_id);
        if (!$this->mod->validar_monto_desembolsos($data['monto'])) {
            die('-2');
        }
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version_id);
        $desembolso_solicitud = isset($_POST['desembolso']) ? $_POST['desembolso'] : 0;
        
        
        if ($desembolso_solicitud){
            $this->mod->agregar_desembolso_solicitado($desembolso_solicitud);
        }

        
        $reset = isset($_POST['reset']) ? $_POST['reset'] : 0;
        
        
        if ($tipo==EVENTO_DESEMBOLSO){
            $data['TIPO'] = EVENTO_DESEMBOLSO;
        }
        else{
            $data['TIPO'] = EVENTO_AJUSTE;
        }
        
        
        $fecha = $_POST['fecha'];

        if (!$this->_verificar_desembolsos_teoricos( $reset)){
            echo "-1";
        }
        else{
      
            $cuotas_restantes = $this->mod->get_cuotas_restantes( $fecha);
            //evaluamos que existan desembolsos reales anteriores al vencimiento de la primera cuota
            //si no que este desembolso sea mayor al desembolso
            
            $fecha_desembolso_nuevo = $this->mod->verificar_desembolsos_inciales($fecha);
                    
            if ( $fecha_desembolso_nuevo!==true){
                //si no existe generamos un desembolso minimo sobre el vencimiento de la primera cuota

                //genero la variacion corerspondiente al desembolso
                $ret = $this->mod->generar_evento( $data, true, $fecha_desembolso_nuevo);
                

                //agrego el registro desembolso a la db   
                $data['monto'] = 0;
                $this->mod->agregar_desembolso( $data['monto'], $cuotas_restantes, $fecha_desembolso_nuevo);
                $this->mod->assign_id_evento($ret['ID'],EVENTO_DESEMBOLSO);                
            }
            $data['monto'] = $_POST['monto'];
            
            //genero la variacion corerspondiente al desembolso
            $ret = $this->mod->generar_evento( $data, true, $fecha);


            //agrego el registro desembolso a la db        
            $this->mod->agregar_desembolso( $data['monto'], $cuotas_restantes, $fecha);
            $this->mod->assign_id_evento($ret['ID'],EVENTO_DESEMBOLSO);
            
            
            //se verifica si la cuota a la fecha dada esta planchada.. de ser asi le saca el planchado 
            //y recalcula los pagos desde esa fecha
            if ( ($fecha_planchado = $this->mod->modificar_planchado($fecha)) > 0 ){
                //se debe reimputar los pagos desde la fecha de planchado
                $this->_recalcular_pagos($fecha_planchado);
            }

            $this->mod->get_segmentos_cuota();
            $this->mod->renew_datos();
            
            $this->mod->set_fecha_actual($fecha);
            $pagos = $this->mod->desimputar_pago();
            
            if ($pagos) {
                set_time_limit(0);
                foreach($pagos as $pago){
                    $this->mod->realizar_pago($pago['fecha'], $pago['monto']);
                }
            }
            
            echo $this->_get_cuotas();
        }
    }
    
    function _verificar_desembolsos_teoricos( $reset = 0){
        $desembolsos_teoricos = $this->mod->get_desembolsos_teoricos();
        if ($desembolsos_teoricos && !$reset){
            return false;
        }
        else{
            //eliminamos desembolsos teoricos si existen
            if ($desembolsos_teoricos && $reset){
                $this->mod->eliminar_desembolsos_teoricos($desembolsos_teoricos);
            }
            return true;
        }
    }
    
    function x_agregar_cambiotasa($retornar = FALSE){
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        if (!$this->_verificar_desembolsos_teoricos( false) && !$retornar){
            echo "-1";
            return;
        }        
        
        $data = array();
        $data['por_int_compensatorio'] = $_POST['tasa'];
        $data['por_int_subsidio'] = $_POST['subsidio'];
        $data['por_int_moratorio'] = $_POST['moratorio'];
        $data['por_int_punitorio'] = $_POST['punitorio'];
        
        $fecha = $_POST['fecha'];
        $desimputar = FALSE;
        
        if ($retornar) {
            $this->mod->set_fecha_actual($fecha);
            if ($this->mod->verificiar_eventos_pagos_posteriores()) {
                //return;
                $desimputar =TRUE;
            }
        }
        
        $desimputar = $desimputar ? $desimputar : (isset($_POST['reimputar']) && $_POST['reimputar']) ? TRUE : FALSE;
            
        $data['TIPO'] = EVENTO_TASA;
        //genero la variacion corerspondiente al desembolso
        $ret = $this->mod->generar_evento( $data, true, $fecha);
        
        //agrego el registro desembolso a la db 
        //$cuotas_restantes = $this->mod->get_cuotas_restantes( $fecha);
        $cuotas_restantes = $this->mod->get_cuotas_restantes_pago();
        
        $this->mod->agregar_tasa( $data['por_int_compensatorio'], $data['por_int_subsidio'],$data['por_int_moratorio'],$data['por_int_punitorio'],$cuotas_restantes, $fecha);
        $this->mod->assign_id_evento($ret['ID'],EVENTO_TASA);
        
        if ($desimputar) {
            set_time_limit(0);
            $this->mod->set_credito_active($credito_id);
            $this->mod->set_version_active($version);

            $this->mod->renew_datos();

            $this->mod->save_last_state(true);
            $this->mod->set_fecha_actual($fecha);
            $pagos = $this->mod->desimputar_pago();

            //$data = $this->mod->agregar_version($fecha, 1, "VERSION CAMBIO TASA X OP");
            //$version_id = $data['VERSION'];
            $version_id = $version;

            $this->mod->set_version_active($version_id);
            $this->mod->make_active_version();
            foreach($pagos as $pago){
                $this->mod->realizar_pago($pago['fecha'], $pago['monto']);
            }
        }
            
        if ($retornar) {
            return;
        }
        //se verifica si la cuota a la fecha dada esta planchada.. de ser asi le saca el planchado 
        //y recalcula los pagos desde esa fecha
        if ( ($fecha_planchado = $this->mod->modificar_planchado($fecha)) > 0 ){
            //se debe reimputar los pagos desde la fecha de planchado
            $this->_recalcular_pagos($fecha_planchado);
        }
        
        $this->mod->renew_datos();        
        $this->mod->get_segmentos_cuota();
            
        echo $this->_get_cuotas();
    }
    
    function impactar_tasas() {
        set_time_limit(0);
        if(!isset($_SESSION['CAMBIO_TASAS'])) {
            header('Location:/' . URL_PATH);
        }
        
        $creditos = $this->mod->get_creditos_by_operatoria($_SESSION['CAMBIO_TASAS']['OPERATORIA']['id']);
        
        if ($creditos) {
            $tasa_comp = $_SESSION['CAMBIO_TASAS']['OPERATORIA']['TASA_INTERES_COMPENSATORIA'];
            $tasa_subs = $_SESSION['CAMBIO_TASAS']['OPERATORIA']['TASA_SUBSIDIADA'];
            $tasa_mora = $_SESSION['CAMBIO_TASAS']['OPERATORIA']['TASA_INTERES_MORATORIA'];
            $tasa_pun = $_SESSION['CAMBIO_TASAS']['OPERATORIA']['TASA_INTERES_POR_PUNITORIOS'];
            
            
            $_POST['fecha'] = $_SESSION['CAMBIO_TASAS']['FECHA'];
            
            $cambiar_valores = FALSE;
            if ($tasa_comp === -1 || $tasa_subs === -1 || $tasa_mora === -1 || $tasa_pun === -1) {
                $cambiar_valores = TRUE;
            }
            
            foreach ($creditos as $credito) {
                $tasas = $this->mod->getTasas($credito['ID'], $_POST['fecha']);
                if ($tasas) {
                    $_POST['tasa'] = $tasa_comp >= 0 ? $tasa_comp : $tasas['POR_INT_COMPENSATORIO'];
                    $_POST['subsidio'] = $tasa_subs >= 0 ? $tasa_subs : $tasas['POR_INT_SUBSIDIO'];
                    $_POST['moratorio'] = $tasa_mora >= 0 ? $tasa_mora : $tasas['POR_INT_MORATORIO'];
                    $_POST['punitorio'] = $tasa_pun >= 0 ? $tasa_pun : $tasas['POR_INT_PUNITORIO'];
                
                    if ($cambiar_valores) {
                        $tasas = $this->mod->getCambiosTasasCredito($credito['ID'], $_POST['fecha']);
                    
                        if ($tasa_comp === -1) {
                            $_POST['tasa'] = $tasas['POR_INT_COMPENSATORIO'];
                        }
                        if ($tasa_subs === -1) {
                            $_POST['subsidio'] = $tasas['POR_INT_SUBSIDIO'];
                        }
                        if ($tasa_mora === -1) {
                            $_POST['moratorio'] = $tasas['POR_INT_MORATORIO'];
                        }
                        if ($tasa_pun === -1) {
                            $_POST['punitorio'] = $tasas['POR_INT_PUNITORIO'];
                        }
                    }
                    $_POST['credito_id'] = $credito['ID'];
                    $_POST['version_id'] = $credito['ID_VERSION'];
                    
                    $this->x_agregar_cambiotasa(TRUE);
                }
            }
        }
        
        echo trim(json_encode($_SESSION['CAMBIO_TASAS']['RESULTADO']));
        //unset($_SESSION['CAMBIO_TASAS']);
        die();
    }
    
    private function _reimputar($credito_id, $version = FALSE, $fecha = FALSE) {
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version);

        $this->mod->renew_datos();

        $this->mod->save_last_state(true);
        
        if (!$fecha) {
            $cuotas = $this->mod->get_cuotas_credito();
            if ($cuotas) {
                $fecha = $cuotas['RESULT'][0]['FECHA_INICIO'];
            }
        }
        
        $this->mod->set_fecha_actual($fecha);
        $pagos = $this->mod->desimputar_pago();

        //$data = $this->mod->agregar_version($fecha, 1, "VERSION CAMBIO TASA X OP");
        //$version_id = $data['VERSION'];
        $version_id = $version;

        $this->mod->set_version_active($version_id);
        $this->mod->make_active_version();
        foreach($pagos as $pago){
            $this->mod->realizar_pago($pago['fecha'], $pago['monto']);
        }
    }
    
    function reimputar_pagos_creditos($id = NULL) {
        $id = (int) $id;
        if ($id == NULL || !is_int($id)) {
            die("error!");
        }
                
        $creditos = array($id);
        set_time_limit(0);
        
        foreach ($creditos as $credito_id) {
            $this->_reimputar($credito_id);
            die();
        }
    }
                
    function x_segmentar(){
        $credito_id = $_POST['credito_id'];
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        $this->segmentar($credito_id, $version );
        echo $this->_get_cuotas();
    }
    function segmentar($cred, $version = 0){

        $this->mod->set_credito_active($cred);
        $this->mod->set_version_active($version);

        $this->mod->renew_datos();

        /* $dia = 10;
        $mes = 9;
        $year = 2014;
        $fecha = mktime(0,0,0,$mes, $dia, $year);
        */
        $this->mod->get_segmentos_cuota(  );
    }
    
    function x_agregar_gasto(){
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        
        if (!$this->_verificar_desembolsos_teoricos( false)){
            echo "-1";
            return;
        }     
        
        $gasto = $_POST['monto'];
        $descripcion = $_POST['descripcion'];
        $fecha = $_POST['fecha'];
        
        $this->mod->set_fecha_actual($fecha - 1);
        $pagos = $this->mod->desimputar_pago();
        
        $this->mod->agregar_gasto( $gasto, $fecha, $descripcion);
        if ($pagos) {
            set_time_limit(0);
            foreach($pagos as $pago){
                $this->mod->realizar_pago($pago['fecha'], $pago['monto']);
            }
        }
        
        $this->mod->get_segmentos_cuota();
        $this->mod->renew_datos();
        
        echo $this->_get_cuotas();
        
    }
    
    function x_eliminar_variacion(){
        $id_variacion = $_POST['id_variacion'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        $this->mod->renew_datos();
        $variacion = $this->mod->eliminar_variacion($id_variacion);
        
        $this->mod->renew_datos();
        $this->mod->get_segmentos_cuota();
        
        $this->_recalcular_pagos($variacion['FECHA'] );
        
        echo $this->_get_cuotas();
    }
    
    function x_eliminar_gasto(){
        $credito_id = $_POST['credito_id'];
        $gasto_id = $_POST['gasto'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        $this->mod->renew_datos();
        
        
        $gasto = $this->mod->eliminar_gasto($gasto_id);
        $this->mod->get_segmentos_cuota();
        
        $this->_recalcular_pagos($gasto['FECHA'] );

        echo $this->_get_cuotas();
    }
    
    function x_generar_cuota(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        $this->mod->renew_datos();
        $this->mod->add_single_cuota($fecha);

        $this->segmentar($credito_id, $version);

        echo $this->_get_cuotas();

    }
    
    function x_actualizar_lista(){
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        $this->mod->renew_datos();
        
        echo $this->_get_cuotas();
    }
    
    function x_obtener_pago(){
        $credito_id = $_POST['credito_id'];
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        
        $id_variacion = $_POST['id_variacion'];
        $this->mod->renew_datos();
        $cuotas = $this->mod->get_pago($id_variacion);
        //print_array($cuotas);
        echo $this->view("pago",array("cuotas"=>$cuotas ));
    }
    
    function x_guardar_opciones_cuota(){
        $inicio = $_POST['fecha_inicio'];
        $vencimiento = $_POST['fecha_vencimiento'];
        $cuotas_restantes = $_POST['cuotas_restantes'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        $this->mod->renew_datos();
        
        
        $this->mod->modificar_fecha_cuota( $cuotas_restantes, $inicio, $vencimiento);
    }
    function x_abrir_opciones_cuota(){
        $credito_id = $_POST['credito_id'];
        $cuotas_restantes = $_POST['cuotas_restantes'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        $this->mod->renew_datos();
        $cuota = $this->mod->get_cuota($cuotas_restantes);
        
        echo $this->view("opciones_cuota",$cuota);
    }    
    
    function x_enviar_cuota(){
        $credito_id = $_POST['credito_id'];
        $fecha = $_POST['fecha'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        
        if (!$this->_verificar_desembolsos_teoricos( false)){
            echo "-1";
            return;
        }    
        
        $this->mod->set_fecha_actual($fecha);
        $this->mod->enviar_cuota();
    }
    
    
    
    function x_get_desembolsos_teoricos(){
        $credito_id = $_POST['id_credito'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        
        $desembolsos = $this->mod->get_desembolsos_teoricos();
        echo json_encode($desembolsos);
    }

    
    function x_eliminar_version(){
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version);
        
        $this->mod->renew_datos();
        $this->mod->eliminar_version();
        $this->mod->set_version_active();
        $versiones = $this->mod->get_versiones();
        echo json_encode($versiones);
        
        
    }
    

    
    function x_agregar_version(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version);
        
        
        $this->mod->renew_datos();
        $data = $this->mod->agregar_version($fecha, 1, "VERSION AGREGADA");
        logthis("nueva_version", $data);
        $version_id = $data['VERSION'];
        
        
        $this->mod->set_version_active($version_id);
        $this->mod->make_active_version();
        $this->mod->renew_datos();
        
        logthis("nueva_version_2", $version_id);
        $this->mod->save_last_state(true);
        $this->mod->set_fecha_actual($fecha);
        
        $rtn = array();
        $rtn['VERSIONES'] = $this->mod->get_versiones();
        $rtn['VERSION_ID'] = $version_id;
        echo json_encode($rtn);
    }
    
    
    function x_leer_desembolsos_pendientes(){
        $credito_id = $_POST['credito_id'];
        $this->mod->set_credito_active($credito_id);
        $desembolsos = $this->mod->leer_desembolsos_pendientes();
        $data['view'] = $this->view("solicitudes_desembolsos", array("desembolsos"=>$desembolsos));
        $data['desembolsos'] = $desembolsos;
        echo json_encode($data);
        
    }
    
    function x_getDetalleCuotas(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];      
        $version = $_POST['version_id'];
        $cuotas = $this->mod->getCuotasCredito($credito_id, $version, $fecha);
        
        $data['datos_cuota'] = "";
        $data['cuotas'] = "";
        $data['rtn'] = "";
        $data['fecha_vencimiento_subsidio'] = "";
        //$data['html'] = $this->view("lista_cuotas_obj",array("cuotas"=>$cuotas) );
        echo $this->view("lista_cuotas_obj",array("cuotas"=>$cuotas) );
        //echo json_encode($data);
    }
    
   
    function x_get_detalle_cuotas(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        $this->mod->elimina_eventos_temporales(); 
        
        
        $this->mod->renew_datos();

        
        //AL MOTRAR DETALLE DE CUOTAS NO SE MODIFICA EL ESTADO DE NINGUNA CUOTA
        $this->mod->save_last_state(false);
        
        //en el pago se resta 
        //se genera evento para definir el dia de corte
        $ret_evento = $this->mod->generar_evento( array(), true, $fecha);
        $this->mod->set_fecha_actual($fecha);
        $this->mod->renew_datos();
        $ret_reduda = $this->mod->get_deuda( $fecha);
        
        
        
        $data['datos_cuota'] = $this->view("info_cuotas/saldo_cuota", $ret_reduda);
        
        $data['cuotas'] = $ret_reduda['cuotas'];
        $data['rtn'] = $ret_reduda['rtn'];
        $data['fecha_vencimiento_subsidio'] = $ret_reduda['fecha_reimputacion'];

        $data['html'] =  $this->_get_cuotas(true);
        
        //echo $this->_get_cuotas();
        echo json_encode($data);
    }    
    
    //recuperacion de pagos y cambios
/*    function x_recalcular_pagos(){
        resetlog();
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version); 
        
        if (!$this->_verificar_desembolsos_teoricos( false)){
            echo "-1";
            return;
        }     
        
        $this->mod->renew_datos();
        $tasas = $this->mod->get_tasas_fecha($fecha);
        
        $data = array();
        $data['por_int_compensatorio'] = $tasas['COMPENSATORIO'];
        $data['por_int_subsidio'] = 0;
        $data['por_int_moratorio'] = $tasas['MORATORIO'];
        $data['por_int_punitorio'] = $tasas['PUNITORIO'];
        $data['TIPO'] = 2;
        
        //genero la variacion corerspondiente al desembolso
        $ret = $this->mod->generar_evento( $data, true, $fecha);
        
        //agrego el registro desembolso a la db 
        $cuotas_restantes = $this->mod->get_cuotas_restantes( $fecha);
        $this->mod->agregar_tasa( $data['por_int_compensatorio'], $data['por_int_subsidio'],$data['por_int_moratorio'],$data['por_int_punitorio'],$cuotas_restantes, $fecha);
        $this->mod->assign_id_evento($ret['ID'],2);
        
        
        //$this->_recalcular_pagos($fecha);
        
        $this->mod->renew_datos();
        echo $this->_get_cuotas();
    }*/
    
    //recuperacion de pagos y cambios
    function x_recalcular_pago_imputaciones(){
     //   resetlog();
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version); 

        $this->mod->renew_datos();
        $this->_recalcular_pagos($fecha);
        
        $this->mod->renew_datos();
        //echo $this->_get_cuotas();
    }
    
    function _recalcular_pagos($fecha = false){
        set_time_limit(0);
        $this->mod->renew_datos();
        
        $this->mod->save_last_state(true);
        $this->mod->set_fecha_actual($fecha);
        

        $pagos = $this->mod->desimputar_pago();
        
        foreach($pagos as $pago){
            $this->mod->realizar_pago($pago['fecha'], $pago['monto']);
        }
    }
    
    function x_get_tasas_fecha(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version);        
        
        $this->mod->renew_datos();
        
        $tasas = $this->mod->get_tasas_fecha($fecha);
        
        echo json_encode($tasas);
    }
    
    
    function x_verificar_eventos_posteriores(){
        
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version);
        
        $this->mod->renew_datos();
        
        $this->mod->set_fecha_actual($fecha);
        if ($this->mod->verificiar_eventos_pagos_posteriores()){
            echo "1";
        }
        else{
            echo "0";
        }
    }
    
    function x_verificar_desembolsos_reales(){
       $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version);            
        
        if (!$this->_verificar_desembolsos_teoricos( false)){
            echo "-1";
            return;
        }          
    }

    
    function __borrar_desde($fecha = NO_FECHA){
        $this->mod->set_fecha_actual($fecha);
        $this->mod->renew_datos();
        $this->mod->eliminar_todo_desde();
    }
    
    function x_eliminar_eventos_posteriores(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version);          
        $this->__borrar_desde($fecha);
    }
    
    
    function x_make_active_version(){
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version); 
        
        $this->mod->make_active_version();
    }
    
    function x_generar_eventos(){
        $id = $_POST['credito'];;
        $cmodel = $this->load_model("clases_model");
        $credito = $cmodel->getCreditoClass($id);
        
        $html = $this->view("result_events",array("credito"=>$credito));        
        
        $nombre = "_tmp/".$id."_".date("Y-m-d").".html";
        file_put_contents($nombre, $html);
        echo  $nombre ;        
    }
    
    
    function x_generar_chequera(){
        $id_credito = $_POST['credito'];;
        $credito = $_POST['credito'];
        $fecha = $_POST['fecha'];
        //resetlog();
        /*$cuotas_arr = array();
        
        $this->mod->set_credito_active($credito);
        $this->mod->set_version_active();
        $this->mod->renew_datos();
        $cuotas = $this->mod->get_cuotas_credito();
*/
        
        
        $this->mod->set_credito_active($id_credito);
        $this->mod->set_version_active();        
        
        $this->mod->set_fecha_actual($fecha);
        $this->mod->set_fecha_calculo();
        
        
        $this->mod->renew_datos();
        
        $this->mod->save_last_state(false);
        
        
        //chequera = proyeccion teorica
        $this->mod->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);    
        
        $this->mod->generar_evento( array(), true, $fecha);
        
        
        //segundo parametro: recalcular datos
        //tercer parametro true para forzar la deuda con el compensatorio total
        
        $ret_reuda= $this->mod->get_deuda($fecha, true );
        
        $cuotas = $ret_reuda['cuotas'];

        
        /*
        

        $arr_cuotas_tmp = array();
        foreach($cuotas['RESULT'] as $cuota){
            if ($cuota['FECHA_INICIO'] >= $fecha){
                $arr_cuotas_tmp[] = $cuota;
            }
        }
        $creditos[] = array("CUOTAS"=>$arr_cuotas_tmp,"CREDITO"=>$credito);
        
        */
       

        // Set some content to print
        $head = "
            <style >
@media all {
	.page-break	{ display: none; }
}

@page {
    size: A4;
        margin-left: 1.15cm;
        margin-right: 1.15cm;
        margin-bottom: 0cm;
        margin-top: 0cm;        
}

.codbar{font-size:0;position:relative;}
.codbar div{-webkit-print-color-adjust:exact}
.cc1{width:55%;padding-right:15px}
.cc1 table{border-collapse: collapse;}
.cc2{width:45%;text-align:center;}

@media print {
    .page {

        border: initial;
        border-radius: initial;
        width: initial;
        min-height: initial;
        box-shadow: initial;
        background: initial;
        page-break-after: always;
    }
	
}         
  @media screen, print {
  .page-break	{ display: block; page-break-before: always; }
    table tr{
        width:100%;
        clear: both;
        font-family: sans-serif;
    }
  
    table tr,
    table tr td,
    table{
        margin: 0px;
        padding:0px;
    }
    
    table{
        width:100%;
    }

    table td{
        width: 12.5% ;
    }


    .datos{font-weight:bold;}
    .detalle td{
        border-bottom:1px solid black;
        font-size:9pt;
    }

    .detalle td:nth-child(2){text-align:right;}

    .newpage{
        width: 210mm !important;
        border-bottom: 1px solid black;
    }
    
    .header{
        width:210mm;
        
        font-size:8pt;
    }
    .div-header{
        padding-top:10mm;
        height:32mm;
        width: 210mm;
        display: block;
        overflow: hidden;
        font-size:8pt;
    }    
    .header h1{
        font-size:14pt;
    }    

    .content-cuotas{
        width:190mm;
        overfloat: hidden;
        font-size:8pt;
    }
    .div-content-cuotas{
        height: 254mm;
        width: 210mm;
        display: block;
        overflow: hidden;
        font-size:8pt;
    }
    
    .footer{
        width:200mm;
    }
    
    .footer .left{
        float: let
    }
    .footer .right{
        float: right;
        font-size:70%;
    }
    
    .footer .normal{
        font-size:80%;
    }

    .footer .small{
        font-size:50%;
    }
    .footer .midd{
        font-size:65%;
    }

    .div-footer{
        height: 30mm;
        width: 210mm;
        display: block;
        overflow: hidden;
        font-size:8pt;
    }
    hr.end_cuota,
    .end_cuota td{
        border-top:1px dotted #888;
    }

    .div-footer span{
        font-size:8pt;
    }

    .c2{width:25%}
    .c3{width:37.5%;height:17mm;vertical-align:top}
    .c5{width:62.5%}
    .c6{width:75%}
    .c7{width:87.5%}
    .c8{width:100%}
    .cc1,.cc2{border-bottom:1px dotted #888;}

  }
</style><table  >";
        
     //   $left_footer = $this->mod->getLeftFooter();
        //$left_footer = $this->getLeftFooter();
        
        $opcionesCredito = $this->mod->get_creditos_opciones();
        $banco = 1;
        if (isset($opcionesCredito['banco'])){
            if ($opcionesCredito['banco']['VALOR']==2){
                $banco = 2;
            }
        }
        
        switch($banco){
            case 1:
                $left_footer = 
        '<p class="normal"><b>Convenio: BNA 5321</b><br/>
        Domicilio de pago: Sucursales del Banco de la Nación Argentina<br/>
        </p>
        <p class="normal">El Banco actúa únicamente como Agente Recaudador, no asumiendo otra responsabilidad y obligación que exceda tal carácter.<br/>
        La falta de pago a su vencimiento provocará devengamiento de intereses moratorios y punitorios hasta la fecha de la efectiva cancelación,<br/>
        conforme lo establecido en el contrato de prestamo y sin perjuicio de otros derechos que correspondan, quedando sin efecto la imputación que figura en la presente boleta.</p>
        <p class="normal"><b>Otras medios de pagos:</b> sucursales de Rapipago</p>';

                $right_footer = 'Fecha de Emisión: '.date("d/m/Y H:i:s");                
                break;
            case 2:
        $left_footer = 
'<p class="midd"><span class="normal"><b>Convenio Supervielle - Cta cte: 2540221 - CBU: 0270066310025402210014</b> </span><br/>
<span class="normal">Clientes con cuenta en Banco Supervielle: Cualquier Sucursal Banco Supervielle.<br/>
No clientes del Banco Supervielle:  Anexos Banco Supervielle (Municipalidad Capital y Godoy cruz), Consejo profesional de Cs económicas. (Mendoza Centro-San Martin-Tunuyan).</span>
</p>
<p class="small"  style="margin-top: -9px;display: block; ">El Banco actúa únicamente como Agente Recaudador, no asumiendo otra responsabilidad y obligación que exceda tal carácter.<br/>
La falta de pago a su vencimiento provocará devengamiento de intereses moratorios y punitorios hasta la fecha de la efectiva cancelación,<br/>
conforme lo establecido en el contrato de prestamo y sin perjuicio de otros derechos que correspondan, quedando sin efecto la imputación que figura en la presente boleta.</p>';

        $right_footer = 'Fecha de Emisión: '.date("d/m/Y H:i:s");                
                break;
        }
        
     


        $html = "";
        $html = $head;

        $clientes = $this->mod->get_clientes_credito();
        $credito_arr = array(
            'DOMICILIO'=>'',
            'CUIT'=>'',
            'RAZONSOCIAL'=>''
            );
        if ($clientes) {
            $credito_arr['DOMICILIO'] = $clientes[0]['DIRECCION']." - ".$clientes[0]['LOCALIDAD']." - ".$clientes[0]['PROVINCIA'];
            $credito_arr['CUIT'] = $clientes[0]['CUIT'];
            $credito_arr['RAZONSOCIAL'] = $clientes[0]['RAZON_SOCIAL'];
        }


//        $cuotas = $credito['CUOTAS'];
        ////$cuotas = $this->mod->get_cuotas_desde();
        $header = '<table >';
        $header .= "<tr><td class='c8' colspan='8' align='center'><h1>FIDEICOMISO MICROEMPRENDIMIENTO</h1></td></tr>";

        $header .= "<tr>
            <td class='c7' colspan='7'>Appellido y Nombre o Razón Social: <span class='datos'>".$credito_arr['RAZONSOCIAL']."</span></td>
            <td >Nro de Cred.<br/><span class='datos'>".$credito."</span></td>
                </tr>";            
        $header .= "<tr>
            <td class='c2' colspan='2'>Cuit Nro.<br/><span class='datos'> ".$credito_arr['CUIT']."</span></td>
            <td class='c6' colspan='6'>Domicilio - Departamento - Provincia.<br/><span class='datos'>".$credito_arr['DOMICILIO']."</span></td>
                </tr></table>";            

        $footer = '<table  >
            <tr>
                <td class="c7">
                    <span class="left">'.$left_footer.'</span>
                </td>
                <td class="c1" valign="top">
                    <span class="right">'.$right_footer.'</span>
                </td>
            </tr></table>';
        if (isset($cuotas)){
            $i = 0;
            $page = '';
            $cant_cuotas = count($cuotas);
  
            $page .= '<tr><td colspan="8">&nbsp;</td></tr>';
        
            foreach($cuotas as $cuota){
                if (!isset($cuota['IVA_GASTOS']['SALDO'])) {
                    $cuota['IVA_GASTOS']['SALDO'] = 0;
                }
                $page .= '<tr class="cc"><td class="cc1"><table>';
                $page .= '<tr class="detalle"><td colspan="7"><span class="datos">DETALLE - Cuota Nº'.($cant_cuotas  - $cuota['CUOTAS_RESTANTES'] + 1).'</span></td><td ><span class="datos">IMPORTE</span></td></tr>';
                $page .= '<tr class="detalle"><td colspan="7">Capital</td><td >$'.number_format($cuota['CAPITAL']['SALDO'],2,",",".").'</td></tr>';
                $page .= '<tr class="detalle"><td colspan="7">Intereses Compensatorios</td><td >$'.number_format($cuota['COMPENSATORIO']['SALDO'],2,",",".").'</td></tr>';
                $int_mor = $cuota['MORATORIO']['SALDO'] + $cuota['PUNITORIO']['SALDO'];
               if ($int_mor< 0.10) $int_mor= 0;
                $page .= '<tr class="detalle"><td colspan="7">Intereses Moratorios y Punitorios</td><td >$'.number_format($int_mor ,2,",",".").'</td></tr>';
                $iva = $cuota['IVA_COMPENSATORIO']['SALDO']  + ($cuota['IVA_MORATORIO']['SALDO'])  + ($cuota['IVA_PUNITORIO']['SALDO']) +  $cuota['IVA_GASTOS']['SALDO'];
                if ($iva< 0.10) $iva= 0;
                $page .= '<tr class="detalle"><td colspan="7">IVA</td><td >$'.number_format($iva,2,",",".").'</td></tr>';
                $total = $cuota['CAPITAL']['SALDO'] +$cuota['COMPENSATORIO']['SALDO'] + $cuota['MORATORIO']['SALDO'] + $cuota['PUNITORIO']['SALDO']+ $iva;
                if (isset($cuota['GASTOS_VARIOS']['SALDO'])) {
                    $page .= '<tr class="detalle"><td colspan="7">Gastos Varios</td><td >$'.number_format($cuota['GASTOS_VARIOS']['SALDO'],2,",",".").'</td></tr>';
                    $total += $cuota['GASTOS_VARIOS']['SALDO'];
                } else {
                    $cuota['GASTOS_VARIOS']['SALDO'] = 0;
                }

                
                if ($total < 0.10) $total = 0;
                $page .= '</table></td><td class="cc2"><table style="padding:15px 5px">';
                
                $code = $this->mod->_generar_codbar($cuota['ID'], $total);
                $barcode = new TCPDFBarcode($code, "C128");
                $html_code = $barcode->getBarcodeHTML(1.5,35,'black');
                //echo $html_code ;
                $page .= '<tr>
                    <td colspan="4">' . $html_code . $code . '</td><td >&nbsp;</td></tr>
                    <tr><td colspan="2" align="center" valign="top"><span class="datos"  >Vencimiento <br/>'.date("d/m/Y",$cuota['_INFO']['HASTA']).'</span></td>
                    <td colspan="2"  align="right" valign="top"><span class="datos"  >Total <br/>$'.number_format($total,2,",",".").'</span></td>
                    </tr>
                    </table></td></tr>';

                $i++;
                if ($i%7==0){

                    $html_page = '
                        <table class="newpage"  >
                            <tr class="header"><td class="c8" colspan="8"><div class="div-header">'.$header.'</div></td></tr>
                            <tr class="content-cuotas"><td class="c8" colspan="8"><div class="div-content-cuotas"><table>'.$page.'</table></div></td></tr>
                            <tr class="footer"><td class="c8" colspan="8"><div class="div-footer">'.$footer.'</div></td></tr>
                        </table>
                        <div class="page-break"></div>';
                    
                    $html .= $html_page;
                    
                    $page = '';
                    

                    
                }
                        
            }
            
            if ($i%7 > 0){
                    $html_page = '
                        <table class="newpage"  >
                            <tr class="header"><td class="c8" colspan="8"><div class="div-header">'.$header.'</div></td></tr>
                            <tr class="content-cuotas"><td class="c8" colspan="8"><div class="div-content-cuotas"><table>'.$page.'</table></div></td></tr>
                            <tr class="footer"><td class="c8" colspan="8"><div class="div-footer">'.$footer.'</div></td></tr>
                        </table>
                        <div class="page-break"></div>';
                    $html .= $html_page;
            }

        }
        $html .= "</table>";
   

        
        
        $nombre = "chequeras/".$id_credito."_".date("Y-m-d").".html";
        file_put_contents($nombre, $html);
        echo  $nombre ;
       // echo file_get_contents($mic.".html");
        

    }
    
    function x_guardar_pagos_excel() {
        if ($this->_guardar_pagos_excel()) {
            $_SESSION['msg_ok'] = "El proceso de importación de pagos ha finalizado";
        }
        
        header('Location:/' . URL_PATH . 'creditos/front/creditos');
        die();
    }
    
    function _guardar_pagos_excel() {
        $_SESSION['msg_err'] = "";
        $excel = $_FILES['fexcel'];
        if (isset($excel['tmp_name']) && is_file($excel['tmp_name'])) {
            $dir = 'backup/importaciones/' ;
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            
            $file_import = $dir . 'pagos_' . date('Ymd_Hi_') . $excel['name'];
            move_uploaded_file($excel['tmp_name'], $file_import);

            require_once(MODULE_DIRECTORY . 'PHPExcel/PHPExcel.php');
            require_once(MODULE_DIRECTORY . 'PHPExcel/PHPExcel/Reader/Excel2007.php');
            
            $objReader = new PHPExcel_Reader_Excel2007();
            if ($objPHPExcel = $objReader->load($file_import)) {
                $_SESSION['msg_err'] = "";
                set_time_limit(0);
                $err = "";
                $objPHPExcel->setActiveSheetIndex(0);
                $arr_creditos = array();
                $creditos_err = array();
                $cuit_creditos = array();
                
                //cambios de tasas
                $cambios_tasas = array();
                /*for ($j = 2; $j <= $objPHPExcel->getActiveSheet()->getHighestDataRow(); $j++) {
                    $tmp_ct = $objPHPExcel->getActiveSheet()->getCell("H" . $j)->getCalculatedValue();
                    if ($tmp_ct) {
                        $tmp_ct = strtotime(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($tmp_ct) + 86400));
                        $cambios_tasas[$tmp_ct]['TC'] = $objPHPExcel->getActiveSheet()->getCell("I" . $j)->getCalculatedValue();
                        $cambios_tasas[$tmp_ct]['TS'] = $objPHPExcel->getActiveSheet()->getCell("J" . $j)->getCalculatedValue();
                        $cambios_tasas[$tmp_ct]['TP'] = $objPHPExcel->getActiveSheet()->getCell("K" . $j)->getCalculatedValue();
                        $cambios_tasas[$tmp_ct]['TM'] = $objPHPExcel->getActiveSheet()->getCell("L" . $j)->getCalculatedValue();
                    } else {
                        break;
                    }
                }*/
                
                for ($j = 2; $j <= $objPHPExcel->getActiveSheet()->getHighestDataRow(); $j++) {
                    $credito_id = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getCalculatedValue();
                    if (!$credito_id) {
                        
                        //voy a buscar si anteriormente se habían cargado créditos y buscar por cuit del postulante
                        $cuit = str_replace(array("\\", "/"), "\n", $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getCalculatedValue());
                        
                        if (!$cuit) {
                            continue;
                        }
                        
                        $cuit = explode("\n", $cuit);
                        
                        $_cuits = array();
                        foreach ($cuit as $c) {
                            $_cuits[] = trim(str_replace("-", "", trim($c)));
                        }
                        
                        $_cuits = implode("/", $_cuits);
                        
                        if (isset($_SESSION['creditos_importados'][$_cuits]) && $_SESSION['creditos_importados'][$_cuits]) {
                            $credito_id = $_SESSION['creditos_importados'][$_cuits];
                            $cuit_creditos[$credito_id] = $_cuits;
                        } else {
                            //busco el cliente por el cuit, luego busco 
                            $credito_id = $this->mod->buscarCreditoPorCuit($_cuits);
                        }
                    }
                    
                    if ($credito_id) {
                        $fpago = PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("E" . $j)->getCalculatedValue()) + 86400;
                        $fpago = strtotime(date('Y-m-d', $fpago));
                        $_fpago = (int)$fpago;
                        
                        /*if (isset($arr_creditos[$credito_id][$_fpago])) {
                            $_fpago += $j;
                        }*/
                        
                        if (!isset($arr_creditos[$credito_id]) ) {
                            foreach ($cambios_tasas as $k_ct => $tmp_ct) {
                                $arr_creditos[$credito_id][$k_ct-1] = $tmp_ct;
                            }
                        }
                        
                        if (isset($arr_creditos[$credito_id][$_fpago])) {
                            $arr_creditos[$credito_id][$_fpago]['PAGO'] += (float) $objPHPExcel->getActiveSheet()->getCell("F" . $j)->getCalculatedValue();
                        } else {
                            $arr_creditos[$credito_id][$_fpago] = array(
                                'FP' => $fpago,
                                'PAGO' => (float) $objPHPExcel->getActiveSheet()->getCell("F" . $j)->getCalculatedValue()
                                );
                        }
                    } else {
                        $cuit = str_replace(array("\\", "/"), "\n", $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getCalculatedValue());
                        if ($cuit) {
                            $creditos_err[$cuit] = $cuit;
                        }
                    }
                }
                
                foreach ($arr_creditos as $credito_id=>$creditos) { //ordenamos los pagos
                    ksort($creditos);
                    $arr_creditos[$credito_id] = $creditos;
                }
                
                if (count($creditos_err)>0) {
                    $_SESSION['msg_err'] = "Los siguientes créditos no se imputaron pagos: " . implode(", ", $creditos_err);
                }
                
                foreach ($arr_creditos as $credito_id=>$creditos) {
                    //obtener array de cuotas
                    $this->mod->clear();
                    if ($this->mod->set_credito_active($credito_id)) {
                        $ultimo_pago = $this->mod->obtener_ultimo_pago();
                        $this->mod->set_version_active();
                        $this->mod->renew_datos();
                        
                        
                        $monto_credito = $this->mod->get_monto_credito();
                        $desembolsos = $this->mod->get_desembolsos(0);
                        $desembolsado = 0;

                        foreach($desembolsos as $desembolso){
                            $desembolsado += $desembolso['MONTO'];
                        }
                        
                        if ($desembolsado == 0 || ($monto_credito - $desembolsado)) {
                            $err .= "El crédito $credito_id no se imputaron pagos porque no tiene el 100% de los desembolsos<br />";
                        } else {
                            $pagos_no = 0;
                            $pagos_sindatos = 0;
                            foreach ($creditos as $fec => $pago) {
                                $fec = $pago['FP'];
                                if (isset($pago['TC'])) {
                                    $data = array();
                                    $data['por_int_compensatorio'] = $pago['TC'];
                                    $data['por_int_subsidio'] = $pago['TS'];
                                    $data['por_int_moratorio'] = $pago['TM'];
                                    $data['por_int_punitorio'] = $pago['TP'];
                                    $data['TIPO'] = EVENTO_TASA;

                                    $ret = $this->mod->generar_evento($data, true, $fec + 1);
                                    $cuotas_restantes = $this->mod->get_cuotas_restantes_pago();

                                    $this->mod->agregar_tasa($pago['TC'], $pago['TS'],$pago['TM'],$pago['TP'], $cuotas_restantes, $fec + 1);
                                    $this->mod->assign_id_evento($ret['ID'],EVENTO_TASA);
                                } elseif (!$ultimo_pago || ($ultimo_pago && $ultimo_pago['FECHA'] < $pago['FP'])) {
                                    if ($pago['FP'] && $pago['PAGO']) {
                                        $this->mod->realizar_pago($pago['FP'], $pago['PAGO']);
                                    } else {
                                        ++$pagos_sindatos;
                                    }
                                } else {
                                    ++$pagos_no;
                                }
                            }

                            if ($pagos_sindatos) {
                                $err .= "El crédito $credito_id ($pagos_sindatos) no se imputaron porque faltan datos<br />";
                            }
                            if ($pagos_no) {
                                $err .= "El crédito $credito_id ($pagos_no) no se imputaron por fechas anteriores al último pago realizado<br />";
                            }
                        }
                        
                        if(isset($cuit_creditos[$credito_id])) {
                            $cuit = $cuit_creditos[$credito_id];
                            unset($_SESSION['creditos_importados'][$cuit]);
                        }
                    } else {
                        $err .= "El crédito $credito_id no existe<br />";
                    }
                }
                
                if ($err) {
                    $_SESSION['msg_err'] .= $err;
                }
                
                RETURN TRUE;
            }
            
        } else {
            $_SESSION['msg_err'] = "Hubo un problema al cargar el archivo";
        }
        
        return FALSE;
    }
    
    
    function x_emitir_una_cuota() {
        $credito_id = $_POST['credito_id'];
        $fecha = $_POST['fecha'];
        $ret_reuda = FALSE;
        if ($this->mod->set_credito_active($credito_id)) {
            $this->mod->set_version_active();        

            $this->mod->set_fecha_actual($fecha);
            $this->mod->set_fecha_calculo();


            $this->mod->renew_datos();
            $this->mod->emitir_una_cuota($fecha);

            $this->mod->save_last_state(false);


            //chequera = proyeccion teorica
            $this->mod->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);    

            $this->mod->generar_evento(array(), true, $fecha);


            //segundo parametro: recalcular datos
            //tercer parametro true para forzar la deuda con el compensatorio total

            $ret_reuda = $this->mod->get_deuda($fecha, true);
            $ret_reuda['fecha_actual'] = $fecha;
            echo $this->view("cuota",$ret_reuda);
        
        } else {
            echo '-1';
        }
        die();
    }
    
    function x_refinanciacion_caida() {
        $credito_id = $_POST['credito_id'];
        $fecha = strtotime(date('Y-m-d'));
        
        if ($this->mod->set_credito_active($credito_id)) {
            $this->mod->refinanciacion_caida();
            echo "1";
        }
        die();
    }
    
    function update_pagos() {
        set_time_limit(0);
        $this->mod->updateFechaPago();
    }

    function x_add_cobros() {
        $cobros = $_POST['cobros'];

        $fecha = time();
        
        set_time_limit(0);
        //die();
        foreach ($cobros as $cobro) {
            $ID_CREDITO = $cobro['ID_CREDITO'];

            list($d, $m, $y) = explode("/", $cobro['FECHA']);
            $fecha = mktime(0, 0, 0, $m, $d, $y);

            list($d, $m, $y) = explode("/", $cobro['CREDITO_VENCIMIENTO']);
            $vencimiento_cuota = mktime(0, 0, 0, $m, $d, $y);

            if ($version =$this->mod->existCredito($ID_CREDITO)) {
                if ($this->_x_set_pago($ID_CREDITO, $fecha, $cobro['IMPORTE'], $version['ID_VERSION'])) {
                    $this->mod->marcar_cobro_bancario($cobro['ID'], $fecha);
                }
            } else {
                echo "llega-----";
            }
        }
    }
    
    function x_credito_caido() {
        $credito_id = $_POST['credito_id'];
        $fecha = $_POST['fecha'];
        $ret_reuda = FALSE;
        if ($this->mod->set_credito_active($credito_id)) {
            $this->mod->set_version_active();        

            $this->mod->set_fecha_actual($fecha);
            $this->mod->set_fecha_calculo();


            $this->mod->renew_datos();
            $this->mod->emitir_credito_caido($fecha);

            $this->mod->save_last_state(false);


            //chequera = proyeccion teorica
            $this->mod->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);    

            $this->mod->generar_evento(array(), true, $fecha);


            //segundo parametro: recalcular datos
            //tercer parametro true para forzar la deuda con el compensatorio total

            $ret_reuda = $this->mod->get_deuda($fecha, true);
            $ret_reuda['fecha_actual'] = $fecha;
            echo $this->view("credito_caido",$ret_reuda);
        
        } else {
            echo '-1';
        }
        die();
    }
    
    function x_imp_cambiotasas() {
        set_time_limit(0);
        if ($tasas = $this->mod->get_cambiotasa_by_id($_POST['id'])) {
            $tasa_comp = $tasas['COMPENSATORIO'];
            $tasa_subs = $tasas['SUBSIDIO'];
            $tasa_mora = $tasas['MORATORIO'];
            $tasa_pun = $tasas['PUNITORIO'];
            $fecha = $tasas['FECHA'];
            
            if (($rtn = $this->mod->validar_cambiotasa($_POST['credito_id'], $fecha)) < 0) {
                echo json_encode(array('result' => $rtn));
                exit();
            }

            $cambiar_valores = FALSE;
            if ($tasa_comp === -1 || $tasa_subs === -1 || $tasa_mora === -1 || $tasa_pun === -1) {
                $cambiar_valores = TRUE;
            }
            
            if ($tasas = $this->mod->getTasas($_POST['credito_id'], $fecha)) {
                $_POST['tasa'] = $tasa_comp >= 0 ? $tasa_comp : $tasas['POR_INT_COMPENSATORIO'];
                $_POST['subsidio'] = $tasa_subs >= 0 ? $tasa_subs : $tasas['POR_INT_SUBSIDIO'];
                $_POST['moratorio'] = $tasa_mora >= 0 ? $tasa_mora : $tasas['POR_INT_MORATORIO'];
                $_POST['punitorio'] = $tasa_pun >= 0 ? $tasa_pun : $tasas['POR_INT_PUNITORIO'];

                if ($cambiar_valores) {
                    $tasas = $this->mod->getCambiosTasasCredito($credito['ID'], $fecha);

                    if ($tasa_comp === -1) {
                        $_POST['tasa'] = $tasas['POR_INT_COMPENSATORIO'];
                    }
                    if ($tasa_subs === -1) {
                        $_POST['subsidio'] = $tasas['POR_INT_SUBSIDIO'];
                    }
                    if ($tasa_mora === -1) {
                        $_POST['moratorio'] = $tasas['POR_INT_MORATORIO'];
                    }
                    if ($tasa_pun === -1) {
                        $_POST['punitorio'] = $tasas['POR_INT_PUNITORIO'];
                    }
                }
                $_POST['credito_id'] = $_POST['credito_id'];
                $_POST['version_id'] = $tasas['ID_VERSION'];
                $_POST['fecha'] = $fecha;

                $this->x_agregar_cambiotasa(TRUE);
                echo "FIN";
            }
            
        }
    }
    
}


// extend TCPF with custom functions
class MYPDF extends TCPDF {

    public function MultiRow($left, $right) {
        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)

        $page_start = $this->getPage();
        $y_start = $this->GetY();

        // write the left cell
        $this->MultiCell(130, 0, $left, 1, 'R', 1, 2, '', '', true, 0);

        $page_end_1 = $this->getPage();
        $y_end_1 = $this->GetY();

        $this->setPage($page_start);

        // write the right cell
        $this->MultiCell(0, 0, $right, 1, 'J', 0, 1, $this->GetX() ,$y_start, true, 0);

        $page_end_2 = $this->getPage();
        $y_end_2 = $this->GetY();

        // set the new row position by case
        if (max($page_end_1,$page_end_2) == $page_start) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 == $page_end_2) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 > $page_end_2) {
            $ynew = $y_end_1;
        } else {
            $ynew = $y_end_2;
        }

        $this->setPage(max($page_end_1,$page_end_2));
        $this->SetXY($this->GetX(),$ynew);
    }

}
