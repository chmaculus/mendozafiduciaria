<?php

class credito extends main_controller{
    function credito(){
        $this->mod = $this->model("credito_informes_model");
    }
    
    function init($id_credito, $editar = 0){
        resetlog();
        //$this->constructor();
        if ( !isset($_SESSION["USERADM"]))
            header("Location: " . '/'.URL_PATH);
        //$this->_get_loged();
        $this->setCss( array("init.css","informes.css","informes_cuota.css") );
        //$this->setJs( array( "init.js",'forms.js') );
        $this->setJs( array( "creditos.js") );
        $this->setPlug( array("chosen","jalerts","numeric","validation","fancybox","jqgrid","jmenu","table2excel"));
        
        $id_permiso = 12;
        $arr_permiso_mod = isset($_SESSION["USER_PERMISOS"][$id_permiso])?$_SESSION["USER_PERMISOS"][$id_permiso]:0;
        
        //if(PERMISOS_ALL==1){
            $arr_permiso_mod = array
            (
                "MOSTRAR" => 1,
                "ALTA" => 1,
                "BAJA" => 1,
                "MODIFICACION" => 1,
                "EXPORTAR" => 1,
                "OTROS" => 1
            );
        //}
        
        $this->_js_var['editar'] = $editar;
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
        
        $datax = array();
        $datax['main'] = $this->_obtener_main($arr_permiso_mod, $id_credito);
        $datax['titulo']= "Administracion";
        $datax['etiqueta_modulo'] = "Carpetas";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USUARIO_SESION_ACTUAL'] = $_SESSION["USERADM"];
        $this->_js_var['_USER_AREA'] = $_SESSION["USER_AREA"];
        $this->_js_var['_USER_PUESTO'] = $_SESSION["USER_PUESTO"];
        $this->_js_var['_USER_PUESTO'] = $_SESSION["USER_PUESTO"];
        $this->_js_var['ID'] = $id_credito;

        $this->_js_var['FECHA'] = time();
        
        $this->render($datax);
        //etapas
    }

    function _obtener_main($arr_permiso_mod, $id_credito){
        $data['fecha'] = $this->get_fecha();

        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ',$data['hora_actual']));
        $data['hora_bd'] = $data['hora_actual'];
        
        $arr_campos = array("DENOMINACION");
            
        
        if (!$this->mod->set_credito_active($id_credito)) {
            header("Location: " . '/'.URL_PATH.'/creditos/front/creditos');
        }
        
        $this->mod->set_version_active();
        $this->mod->renew_datos();
        
            
        $data['info'] = $this->mod->get_datos_credito();
        //print_array($data['info'] );
      //  die();
        
        /* permiso mostrar */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("creditos", $data['info']);
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
        $chequera = isset($_POST['chequera']) ? $_POST['chequera'] : 0;
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active();
        
        $this->mod->renew_datos();
        
        $monto_credito = $this->mod->get_monto_credito();
        
        $desembolsos = $this->mod->get_desembolsos(!$chequera ? $fecha : 0);    
        
        $desembolsado = 0;
        foreach($desembolsos as $desembolso){
            $desembolsado += $desembolso['MONTO'];
        }
        
        echo $this->view("informes/desembolsos", array("desembolsos"=>$desembolsos,"MONTO_CREDITO"=>$monto_credito,"DESEMBOLSADO"=>$desembolsado ) );
    }
    
    function x_get_cobranzas(){
        $credito_id = $_POST['credito_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        $chequera = isset($_POST['chequera']) ? $_POST['chequera'] : 0;
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active();
        
        $this->mod->renew_datos();
        
        $pagos = $this->mod->get_pagos(!$chequera ? $fecha : false);    
        
        echo $this->view("informes/pagos", array("pagos"=>$pagos) );
    }
    
    function x_obtener_pago(){
        $credito_id = $_POST['credito_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        
        $this->mod->set_credito_active($credito_id);
        //$version = $_POST['version_id'];
        $this->mod->set_version_active();
        
        $id_variacion = $_POST['evento_id'];
        $this->mod->renew_datos();
        $cuotas = $this->mod->get_pago($id_variacion);
        //print_array($cuotas);
        echo $this->view("informes/pago_lista",array("cuotas"=>$cuotas ));
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
    
    function x_obtener_cuotas(){
        $simulacion_credito = FALSE;
        if (isset($_POST['simulacion']) && $_POST['simulacion'] && isset($_SESSION['simulacion_credito'])) {
            $simulacion_credito = $_POST;
            $_POST['credito_id'] = $_SESSION['simulacion_credito'];
            $_POST['chequera'] = true;
            $_POST['planchado'] = false;
        }
        
        $credito_id = $_POST['credito_id'];
        $chequera = $_POST['chequera'];
        $planchado = $_POST['planchado'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        
        $this->mod->set_credito_active($credito_id);
        //$version = $_POST['version_id'];
        $version = $this->mod->set_version_active();

        resetlog();
        
        $this->mod->set_fecha_actual($fecha);
        $this->mod->set_fecha_calculo();
        
        
        $this->mod->renew_datos();
        
        $this->mod->save_last_state(false);
        
        
        //chequera = proyeccion teorica
        if ($chequera==1){
            
            $this->mod->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);    
        }
        else{
            
            
            $this->mod->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_DEVENGAR_A_FECHA);
            if ($planchado==1){
                
                $this->mod->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_AUTO);
            }
        }
        
        
        $this->mod->generar_evento( array(), true, $fecha);
        
        //segundo parametro: recalcular datos
        //tercer parametro true para forzar la deuda con el compensatorio total
        
        $ret_reuda= $this->mod->get_deuda($fecha, true );
        logthis("cuotas",$ret_reuda);

        $ret_reuda['fecha_actual'] = $fecha;
        echo $this->view("informes/cuotas",$ret_reuda);
        
        if (isset($_SESSION['simulacion_credito']) && $_SESSION['simulacion_credito']) {
            $this->mod->borrar_credito();
            unset($_SESSION['simulacion_credito']);
        }
        //print_array($ret_reuda);
        
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
    
    
    function x_obtener_tasas(){
        $credito_id = $_POST['credito_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        $chequera = $_POST['chequera'];
        
        $this->mod->set_credito_active($credito_id);
        //$version = $_POST['version_id'];
        $this->mod->set_version_active();
        
        $this->mod->renew_datos();
        
        
        $tasas = $this->mod->obtener_tasas(!$chequera ? $fecha : false);
        
        echo $this->view("informes/tasas",array("tasas"=>$tasas) );        
    }
    
    function x_eliminar_credito(){

        $credito_id = $_POST['credito_id'];
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active();
        $this->mod->borrar_credito();
    }
    
    function x_reporte_credito() {
        $this->setJs( array( "excelexport.js") );
        
        $credito_id = $_POST['credito_id'];
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : time();
        
        $this->mod->set_credito_active($credito_id);
        $version = $this->mod->set_version_active();

        $info = $this->mod->get_datos_credito();
        
        resetlog();
        
        $this->mod->set_fecha_actual($fecha);
        $this->mod->set_fecha_calculo();
        $this->mod->renew_datos();
        $this->mod->save_last_state(false);
            
        $this->mod->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);
        
        $this->mod->generar_evento( array(), true, $fecha);
        
        $ret_deuda= $this->mod->get_deuda($fecha, true );
        
        $arra_res = array();
        
        $total_comp = 0;
        $total_iva_comp = 0;
        $capital_cuota = 0;
        $total_cuota = 0;
        $pago_total = 0;
        $saldo_cuota = 0;
        $total_int_mor_pun = 0;
        $total_int_mor_pun_iva = 0;
        $total_saldo_mora = 0;
        $total_saldo = 0;
        $total_comp_act = 0;
        $total_comp_act_iva = 0;
        
        $desembolsos = $this->mod->get_desembolsos();
        
        if (isset($ret_deuda['cuotas']) && $ret_deuda['cuotas']) {
            $arr_pagos = $this->mod->get_pagos();
            
            foreach ($ret_deuda['cuotas'] as $k => $item) {
                $cuota = array();
                $fecha_comp = isset($arr_pagos[$k]['FECHA2']) ? $arr_pagos[$k]['FECHA2'] : strtotime(date('Y-m-d'));
                
                $cuota['CONCEPTO'] = ($k+1) . "º cuota";
                $cuota['SALDO'] = number_format($item['_INFO']['SALDO_CAPITAL'], 2, ",", "");
                $cuota['FECHA'] = date('d/m/Y', $item['_INFO']['HASTA']);
                $cuota['VENCIDA'] = ($item['_INFO']['HASTA'] < $fecha_comp) ? 'SI' : 'NO';
                $cuota['INT_COMPENSATORIO'] = number_format($item['COMPENSATORIO']['TOTAL'], 2, ",", "");
                $total_comp += $item['COMPENSATORIO']['TOTAL'];
                $cuota['INT_COMPENSATORIO_IVA'] = number_format($item['IVA_COMPENSATORIO']['TOTAL'], 2, ",", "");
                if (isset($item['COMPENSATORIO_ACT'])) {
                    $cuota['COMPENSATORIO_ACT'] = number_format($item['COMPENSATORIO_ACT'], 2, ",", "");
                    $total_comp_act += $item['COMPENSATORIO_ACT'];
                    
                    $iva_comp_act = $item['COMPENSATORIO_ACT'] * $item['IVA_COMPENSATORIO']['TOTAL'] / $item['COMPENSATORIO']['TOTAL'];
                    $total_comp_act_iva += $iva_comp_act;
                    $cuota['COMPENSATORIO_ACT_IVA'] = number_format($iva_comp_act, 2, ",", "");
                } else {
                    $cuota['COMPENSATORIO_ACT'] = '';
                    $cuota['COMPENSATORIO_ACT_IVA'] = '';
                }
                $total_iva_comp += $item['IVA_COMPENSATORIO']['TOTAL'];
                $pagos = $item['CAPITAL']['TOTAL'] + $item['COMPENSATORIO']['TOTAL'] + $item['IVA_COMPENSATORIO']['TOTAL'];
                $total_cuota += $pagos;
                $cuota['CUOTA'] = number_format($pagos, 2, ",", "");
                
                $pagos += $item['PUNITORIO']['TOTAL'] + $item['IVA_PUNITORIO']['TOTAL'] + $item['MORATORIO']['TOTAL'] + $item['IVA_MORATORIO']['TOTAL'];
                $cuota['SALDO_MORA'] = number_format($pagos, 2, ",", "");
                $total_saldo_mora += $pagos;
                
                $_saldo = $item['CAPITAL']['SALDO'] + $item['COMPENSATORIO']['SALDO'] + $item['IVA_COMPENSATORIO']['SALDO'];
                $_saldo += $item['PUNITORIO']['SALDO'] + $item['IVA_PUNITORIO']['SALDO'] + $item['MORATORIO']['SALDO'] + $item['IVA_MORATORIO']['SALDO'];
                $_saldo = $_saldo < 0.01 ? 0 : $_saldo;
                $total_saldo += $_saldo;
                $cuota['SALDO_CUOTA'] = number_format($_saldo, 2, ",", "");
                
                
                
                if (isset($arr_pagos[$k])) {
                    $cuota['PAGO_MONTO'] = number_format($arr_pagos[$k]['MONTO'], 2, ",", "");
                    $cuota['PAGO_FECHA'] = date('d/m/Y', $arr_pagos[$k]['FECHA2']);
                    $pago_total += $arr_pagos[$k]['MONTO'];
                }
                
                $cuota['INT_MORA_PUN'] = $item['PUNITORIO']['TOTAL'] + $item['MORATORIO']['TOTAL'];
                $total_int_mor_pun += $cuota['INT_MORA_PUN'];
                $cuota['DIAS_MORAS'] = $cuota['INT_MORA_PUN'] > 0.01 ? (int) $item['DIAS_MORAS'] : '';
                $cuota['INT_MORA_PUN'] = number_format($cuota['INT_MORA_PUN'], 2, ",", "");
                
                $cuota['INT_MORA_PUN_IVA'] = $item['IVA_PUNITORIO']['TOTAL'] + $item['IVA_MORATORIO']['TOTAL'];
                $total_int_mor_pun_iva += $cuota['INT_MORA_PUN_IVA'];
                $cuota['INT_MORA_PUN_IVA'] = number_format($cuota['INT_MORA_PUN_IVA'], 2, ",", "");
                
                $arra_res[] = $cuota;
            }
            
            $capital_cuota = $ret_deuda['cuotas'][0]['_INFO']['SALDO_CAPITAL'];
        }
        
        $totales = array();
        $totales['INT_COMPENSATORIO'] = number_format($total_comp, 2, ",", "");
        $totales['INT_COMPENSATORIO_IVA'] = number_format($total_iva_comp, 2, ",", "");
        $totales['COMPENSATORIO_ACT'] = number_format($total_comp_act, 2, ",", "");
        $totales['COMPENSATORIO_ACT_IVA'] = number_format($total_comp_act_iva, 2, ",", "");
        $totales['CUOTA'] = number_format($total_cuota, 2, ",", "");
        $totales['PAGO_MONTO'] = number_format($pago_total, 2, ",", "");
        $totales['SALDO_CUOTA'] = number_format($saldo_cuota, 2, ",", "");
        $totales['INT_MORA_PUN'] = number_format($total_int_mor_pun, 2, ",", "");
        $totales['INT_MORA_PUN_IVA'] = number_format($total_int_mor_pun_iva, 2, ",", "");
        $totales['SALDO_MORA'] = number_format($total_saldo_mora, 2, ",", "");
        $totales['SALDO_CUOTA'] = number_format($total_saldo, 2, ",", "");
        

        $ret_deuda['fecha_actual'] = $fecha;
        
        echo $this->view("informes/reporte_credito", array('info' => $info, 'array_credito' => $arra_res, 'totales_credito' => $totales, "desembolsos" => $desembolsos));
    }

}
