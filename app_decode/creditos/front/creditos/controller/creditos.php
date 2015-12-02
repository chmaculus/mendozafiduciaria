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
        $data['permisos'] = $arr_permiso_mod;
        $data['fideicomisos'] = $this->mod->get_fideicomisos();
        
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
        $filtros = $this->_getFiltrosReporte();
        $reporte = $this->mod->getReporteCreditos($filtros[0], $filtros[1], $filtros[2]);
        
        $_cantidad_creditos = 0;
        $_cantidad_creditos_mora = 0;
        $_total_creditos = 0;
        $_montos_vencidos = 0;
        $_monto_mora = 0;
        
        $arr_reporte = array();
        
        if ($reporte) {
            foreach ($reporte as $item) {
                $arr = array();
                if (!$item['DESEMBOLSO'] && !$item['CUOTAS']) {
                    continue;
                }
                
                ++$_cantidad_creditos;
                
                $_moratoria = 0;
                $total_moratoria = 0;
                $total_punitorio = 0;
                $total_iva = 0;
                $cuotas_mora = 0;
                $capital_pagado = 0;
                $total_credito = $item['MONTO_CREDITO'];
                $_total_creditos += $total_credito;
                $monto_mora = 0;
                
                if ($item['CUOTAS']) {
                    $cant_cuotas = count($item['CUOTAS']);
                } else {
                    $cant_cuotas = $item['DESEMBOLSO'][0]['CUOTAS_RESTANTES'];
                }
                $cobranzas = 0;

                if ($item['PAGOS']) {
                    $arr_pagos = array();
                    
                    foreach ($item['PAGOS'] as $v) {
                        if (!isset($arr_pagos[$v['CUOTAS_RESTANTES']])) {
                            $arr_pagos[$v['CUOTAS_RESTANTES']] = array(
                                'MONTO' => 0,
                                'TIENE_MORA' => 0
                            );
                        }
                        
                        $arr_pagos[$v['CUOTAS_RESTANTES']]['MONTO'] += $v['MONTO'];
                        
                        $cobranzas += $v['MONTO'];
                        switch ($v['ID_TIPO']) {
                            case PAGO_IVA_MORATORIO:
                                $total_iva += $v['MONTO'];
                                $total_moratoria += $v['MONTO'];
                                break;
                            case PAGO_MORATORIO:
                                $_moratoria += $v['MONTO'];
                                $total_moratoria += $v['MONTO'];
                                $arr_pagos[$v['CUOTAS_RESTANTES']]['TIENE_MORA'] = 1;
                                //++$cuotas_mora;
                                break;
                            case PAGO_IVA_PUNITORIO:
                            case PAGO_PUNITORIO:
                                $total_punitorio += $v['MONTO'];
                                break;
                            case PAGO_CAPITAL:
                                $capital_pagado += $v['MONTO'];
                                break;
                        }
                    }
                    
                    foreach ($arr_pagos as $arr_pg) {
                        if ($arr_pg['TIENE_MORA']) {
                            ++$cuotas_mora;
                            $monto_mora += $arr_pg['MONTO'];
                        }
                    }
                }
                

                if ($item['CUOTAS']) {

                    foreach ($item['CUOTAS'] as $v) {
                        $total_credito += $v['INT_COMPENSATORIO'] + $v['INT_COMPENSATORIO_IVA'];
                    }
                    
                }
                
                if ($_moratoria) {
                    ++$_cantidad_creditos_mora;
                }
                
                $arr['DEUDOR'] = $item['RAZON_SOCIAL'];
                $arr['CUIT'] = $item['CUIT'];
                $arr['ID'] = $item['ID'];
                $arr['DIRECCION'] = $item['DIRECCION'];
                $arr['PROVINCIA'] = $item['PROVINCIA'];
                $arr['LOCALIDAD'] = $item['LOCALIDAD'];
                $arr['FECHA_DESEMB'] = $item['DESEMBOLSO'] ? date('Y-m-d', $item['DESEMBOLSO'][0]['FECHA']) : '';
                $arr['MONTO_CREDITO'] = $item['MONTO_CREDITO'];
                $arr['SALDO_CAPITAL'] = $_moratoria ? 'Mora' : 'Al Día';
                $arr['SITUACION'] = $_moratoria ? 'Mora' : 'Al Día';
                $arr['SALDO_CAPITAL'] = number_format($item['MONTO_CREDITO'] - $capital_pagado, 2, ".", "");
                $arr['COBRANZAS'] = number_format($cobranzas, 2, ".", "");
                $arr['CANTIDAD_CUOTAS_MORAS'] = $cuotas_mora;
                $arr['MONTO_VENCIDO'] = number_format($total_credito + $total_moratoria + $total_punitorio, 2, ".", "");
                $arr['MONTO_MORA'] = number_format($total_credito + $total_moratoria + $total_punitorio - $cobranzas, 2, ".", "");
                $arr['PORCENTAJE_MORA'] = number_format($cuotas_mora * 100 / $cant_cuotas, 2, ",", ".") . "%";
                $arr['ESTADO'] = '?';
                
                $arr_reporte[] = $arr;
                
                $_monto_mora += $monto_mora;
                $_montos_vencidos += $total_credito + $total_moratoria + $total_punitorio;
            }
            
        }
        
        $_SESSION['cantidad_creditos'] = $_cantidad_creditos;
        $_SESSION['cantidad_creditos_mora'] = $_cantidad_creditos_mora;
        $_SESSION['total_creditos'] = $_total_creditos;
        $_SESSION['montos_vencidos'] = $_montos_vencidos;
        $_SESSION['montos_mora'] = $_monto_mora;
        
        echo trim(json_encode($arr_reporte));
        die();
    }
    
    function fn_resumen_moratorias() {
        $res_mor = array();
        $res_mor[] = array(
            'TOTAL_CREDITOS' => $_SESSION['cantidad_creditos'],
            'MONTOS_VENCIDOS' => "",
            'TOTAL_CREDITOS_MORA' => $_SESSION['cantidad_creditos_mora'],
            'CREDITOS_EFICIENCIA' => number_format($_SESSION['cantidad_creditos_mora'] / $_SESSION['cantidad_creditos'] * 100, 2) . "%"
                );
        
        $res_mor[] = array(
            'TOTAL_CREDITOS' => number_format($_SESSION['total_creditos'], 2, ",", "."),
            'MONTOS_VENCIDOS' => number_format($_SESSION['montos_vencidos'], 2, ",", "."),
            'TOTAL_CREDITOS_MORA' => number_format($_SESSION['montos_mora'], 2, ",", "."),
            'CREDITOS_EFICIENCIA' => number_format($_SESSION['montos_mora']  / $_SESSION['total_creditos'] * 100, 2) . "%"
                );
        
        echo trim(json_encode($res_mor));
        die();
    }
    
    function resumen_moratorias2() {
        $filtros = $this->_getFiltrosReporte();
        $reporte = $this->mod->getReporteCreditos($filtros[0], $filtros[1], $filtros[2]);
        
        $arr_reporte = array();
        
        if ($reporte) {
            foreach ($reporte as $item) {
                $arr = array();
                
                $hoy = strtotime(date('Y-m-d'));
                $nro_cuota_venc = 0;
                $fecha_venc = 0;
                $monto_cuota;
                $capital_pagado = 0;
                $fecha_ult_pago = 0;
                
                
                
                if ($item['PAGOS']) {
                    foreach ($item['PAGOS'] as $v) {
                        if ($v['ID_TIPO'] == PAGO_CAPITAL) {
                            $capital_pagado += $v['MONTO'];
                        }
                    }
                    
                    $fecha_ult_pago = $item['PAGOS'][count($item['PAGOS'])-1]['FECHA'];
                    $cuotas_restantes = $item['PAGOS'][count($item['PAGOS'])-1]['CUOTAS_RESTANTES'] - 1;
                    
                    
                    if ($item['CUOTAS']) {
                        foreach ($item['CUOTAS'] as $ct) {
                            if ($ct['CUOTAS_RESTANTES'] >= $cuotas_restantes) {
                                $fecha_venc = $ct['FECHA_VENCIMIENTO'];
                                ++$nro_cuota_venc;
                                $monto_cuota = $ct['INT_COMPENSATORIO'] + $ct['INT_COMPENSATORIO_IVA'] + $ct['CAPITAL_CUOTA'];
                            }
                        }
                    }
                }
                
                if ($item['CUOTAS'] && $fecha_venc == 0 && $nro_cuota_venc == 0) { //si no hay pagos
                    $ct = $item['CUOTAS'][0];
                    $fecha_venc = $ct['FECHA_VENCIMIENTO'];
                    ++$nro_cuota_venc;
                    $monto_cuota = $ct['INT_COMPENSATORIO'] + $ct['INT_COMPENSATORIO_IVA'] + $ct['CAPITAL_CUOTA'];
                }
                
                $arr['DEUDOR'] = $item['RAZON_SOCIAL'];
                $arr['CUIT'] = $item['CUIT'];
                $arr['ID'] = $item['ID'];
                $arr['FIDEICOMISO'] = $item['FIDEICOMISO'];
                $arr['CUOTA_VENCE'] = $nro_cuota_venc;
                $arr['FECHA_VENCE'] = $fecha_venc ? date('Y-m-d', $fecha_venc) : '';
                $arr['MONTO_CUOTA'] = number_format($monto_cuota, 2, ".", "");
                $arr['SALDO_CAPITAL_MORA'] = number_format($item['MONTO_CREDITO'] - $capital_pagado, 2, ".", "");
                $arr['ESTADO'] = '';
                $arr['FECHA_PAGO'] = $fecha_ult_pago ? date('Y-m-d', $fecha_ult_pago) : '';
                        
                $arr['DIRECCION'] = $item['DIRECCION'];
                $arr['PROVINCIA'] = $item['PROVINCIA'];
                $arr['LOCALIDAD'] = $item['LOCALIDAD'];
                
                
                $arr_reporte[] = $arr;
            }
            
        }
        
        echo trim(json_encode($arr_reporte));
        die();
    }
    
    function resumen_moratorias3() {
        $filtros = $this->_getFiltrosReporte();
        $reporte = $this->mod->getReporteCreditos($filtros[0], $filtros[1], $filtros[2]);
        
        $arr_reporte = array();
        
        if ($reporte) {
            foreach ($reporte as $item) {
                $monto_a_cobrar = 0;
                $total_a_cobrar = 0;
                $monto_mora = 0;
                $cant_mora = 0;
                $cant_creditos_a_cobrar = 0;
                $cuotas_cobradas = 0;
                $cobrado = 0;
                $desembolsos = 0;
                
                if (is_array($item['DESEMBOLSO']) && count($item['DESEMBOLSO']) > 0) {
                    foreach ($item['DESEMBOLSO'] as $des) {
                        if (!$filtros[1] || ($filtros[1] && $filtros[1] <= $des['FECHA'])) {
                            if (!$filtros[2] || ($filtros[2] && $filtros[2] >= $des['FECHA'])) {
                                $desembolsos += $des['MONTO'];
                            }
                        }
                    }
                }
                
                if (count($item['CUOTAS']) > 0) {
                    
                    foreach ($item['CUOTAS'] as $kkk => $cc) {
                        //$monto_a_cobrar += ($cc['CAPITAL_CUOTA'] + $cc['INT_COMPENSATORIO'] + $cc['INT_COMPENSATORIO_IVA']);
                        $pago_cuota = 0;
                        
                        if ($item['PAGOS']) {
                            foreach ($item['PAGOS'] as $pg) {
                                if ($pg['CUOTAS_RESTANTES'] == $cc['CUOTAS_RESTANTES']) {
                                    $pago_cuota += $pg['MONTO'];
                                }
                            }
                        }
                        
                        $tt_cuota = $cc['CAPITAL_CUOTA'] + $cc['INT_COMPENSATORIO'] + $cc['INT_COMPENSATORIO_IVA'] + $cc['INT_MORATORIO'] + $cc['INT_PUNITORIO'];
                        if ($cc['INT_MORATORIO']) {
                            $_monto_mora = $tt_cuota - $pago_cuota;
                            if ($_monto_mora > 0.5) {   
                                $monto_mora += $_monto_mora;
                            }
                        } else {
                            //si no tiene int_moratorio es xq no está vencido
                            $_monto_sin_pagar = $tt_cuota - $pago_cuota;
                            $monto_a_cobrar += $_monto_sin_pagar;
                        }
                        
                        $_monto_sin_pagar = $tt_cuota - $pago_cuota;
                        $total_a_cobrar += $_monto_sin_pagar;
                        
                        if($item['ID']==2067) {
                            //echo $cuotas_mora . "<br />";
                        }
                        
                        if ($pago_cuota) {
                            $cobrado += $pago_cuota;
                            ++$cuotas_cobradas;
                        }
                    }
                    
                    if($monto_a_cobrar) {
                        ++$cant_creditos_a_cobrar;
                    }
                    
                    if($monto_mora) {
                        ++$cant_mora;
                    }
                }
                
                //echo $monto_a_cobrar;die("aca");

                if (isset($arr_reporte[$item['ID_FIDEICOMISO']])) {
                    $arr = $arr_reporte[$item['ID_FIDEICOMISO']];
                    $arr['MONTO_A_COBRAR'] += $monto_a_cobrar;
                    $arr['CANT_CREDITOS_A_COBRAR'] += $cant_creditos_a_cobrar;
                    $arr['COBRADO'] += $cobrado;
                    $arr['CANT_CUOTAS_COBRADAS'] += $cuotas_cobradas;
                    $arr['MONTO_EN_MORA'] += $monto_mora;
                    $arr['CUOTAS_EN_MORA'] += $cant_mora;
                    $arr['TOTAL_OTORGADO'] += $desembolsos;
                    $arr['TOTAL_A_COBRAR'] += $total_a_cobrar;
                    ++$arr['TOTAL_CASOS'];
                } else {
                    $arr = array();
                    $arr['NOMBRE'] = $item['FIDEICOMISO'];
                    $arr['MONTO_A_COBRAR'] = $monto_a_cobrar;
                    $arr['COBRADO'] = $cobrado;
                    $arr['CANT_CUOTAS_COBRADAS'] = $cuotas_cobradas;
                    $arr['CANT_CREDITOS_A_COBRAR'] = $cant_creditos_a_cobrar;
                    $arr['MONTO_EN_MORA'] = $monto_mora;
                    $arr['CUOTAS_EN_MORA'] = $cant_mora;
                    $arr['TOTAL_OTORGADO'] = $desembolsos;
                    $arr['TOTAL_A_COBRAR'] = $total_a_cobrar;
                    $arr['TOTAL_CASOS'] = 1;
                }
                
                $arr_reporte[$item['ID_FIDEICOMISO']] = $arr;
            }
            
        }
        
        echo $this->view("informes/reporte_credito3", array('arr_reporte' => $arr_reporte, 'fecha_desde'=>$filtros[1], 'fecha_hasta'=>$filtros[2]));
    }
    
    function reporte4() {
        $filtros = $this->_getFiltrosReporte();
        $reporte = $this->mod->get_reporte_caducidad($filtros[0], $filtros[1], $filtros[2]);
        
        $_cantidad_creditos = 0;
        $_cantidad_creditos_mora = 0;
        $_total_creditos = 0;
        $_montos_vencidos = 0;
        $_monto_mora = 0;
        
        $arr_reporte = array();
        
        if ($reporte) {
            foreach ($reporte as $item) {
                $arr = array();
                
                $_moratoria = 0;
                $total_moratoria = 0;
                $total_punitorio = 0;
                $total_iva = 0;
                $cuotas_mora = 0;
                $capital_pagado = 0;
                $total_credito = $item['MONTO_CREDITO'];
                $_total_creditos += $total_credito;
                $monto_mora = 0;
                $cobranzas = 0;
                $saldo_credito = 0;
                
                /*if ($item['PAGOS']) {
                    $arr_pagos = array();
                
                    foreach ($item['PAGOS'] as $v) {
                        /*if (!isset($arr_pagos[$v['CUOTAS_RESTANTES']])) {
                            $arr_pagos[$v['CUOTAS_RESTANTES']] = array(
                                'MONTO' => 0,
                                'TIENE_MORA' => 0
                            );
                        }
                        
                        $arr_pagos[$v['CUOTAS_RESTANTES']]['MONTO'] += $v['MONTO'];
                        
                        $cobranzas += $v['MONTO'];* /
                        switch ($v['ID_TIPO']) {
                            /*case PAGO_IVA_MORATORIO:
                                $total_iva += $v['MONTO'];
                                $total_moratoria += $v['MONTO'];
                                break;
                            case PAGO_MORATORIO:
                                $_moratoria += $v['MONTO'];
                                $total_moratoria += $v['MONTO'];
                                $arr_pagos[$v['CUOTAS_RESTANTES']]['TIENE_MORA'] = 1;
                                //++$cuotas_mora;
                                break;
                            case PAGO_IVA_PUNITORIO:
                            case PAGO_PUNITORIO:
                                $total_punitorio += $v['MONTO'];
                                break;* /
                            case PAGO_CAPITAL:
                                $capital_pagado += $v['MONTO'];
                                break;
                        }
                    }
                    /*
                    foreach ($arr_pagos as $arr_pg) {
                        if ($arr_pg['TIENE_MORA']) {
                            ++$cuotas_mora;
                            $monto_mora += $arr_pg['MONTO'];
                        }
                    } * /
                }*/
                
                if ($item['CUOTAS']) {
                    foreach($item['CUOTAS'] as $cc) {
                        if ($cc['FECHA_PAGO'] > 0) {
                            $capital_pagado += $cc['CAPITAL_CUOTA'];
                        }
                        
                        if ( $cc['CUOTA_AL_DIA'] > 0.1) {
                            $saldo_credito += $cc['CUOTA_AL_DIA'];
                        }
                    }
                }
                
                
                if ($_moratoria) {
                    ++$_cantidad_creditos_mora;
                }
                
                $arr['DEUDOR'] = $item['RAZON_SOCIAL'];
                $arr['CUIT'] = $item['CUIT'];
                $arr['ID'] = $item['ID'];
                $arr['DIRECCION'] = $item['DIRECCION'];
                $arr['COD_POSTAL'] = $item['COD_POSTAL'];
                $arr['PROVINCIA'] = $item['PROVINCIA'];
                $arr['LOCALIDAD'] = $item['LOCALIDAD'];
                $arr['FIDEICOMISO'] = $item['FIDEICOMISO'];
                $arr['MONTO_CREDITO'] = $item['MONTO_CREDITO'];
                $arr['SALDO_CAPITAL'] = number_format($item['MONTO_CREDITO'] - $capital_pagado, 2, ".", "");
                $arr['SALDO_CREDITO'] = number_format($saldo_credito, 2, ".", "");
                $arr['CUOTAS_IMPAGAS'] = $item['CUOTAS_RESTANTES'];
                $arr['DIAS_MORAS'] = (strtotime(date('Y-m-d')) - $item['FECHA_VENCIMIENTO']) / (3600 * 24) ;
                $arr['FECHA_1VENC_IMP'] = date('Y-m-d H:i:s', $item['FECHA_VENCIMIENTO']);
                $arr_reporte[] = $arr;
            }
            
        }
        
        echo trim(json_encode($arr_reporte));
        die();
    }
    
    public function _getFiltrosReporte() {
        $ffid = $fdesde = $fhasta = FALSE;
        
        if(isset($_POST['ffid'])) {
            $ffid = $_POST['ffid'];
        }
        
        if(isset($_POST['fdesde']) && $_POST['fdesde']) {
            $fdesde = $_POST['fdesde'];
            list($d, $m, $y) = explode("-", $fdesde);
            $fdesde = mktime(0, 0, 0, $m, $d, $y);
        }
        
        if(isset($_POST['fhasta']) && $_POST['fhasta']) {
            $fhasta = $_POST['fhasta'];
            list($d, $m, $y) = explode("-", $fhasta);
            $fhasta = mktime(0, 0, 0, $m, $d, $y);
        }
        
        if ($fdesde && $fhasta && $fdesde>$fhasta) {
            $aux = $fdesde;
            $fdesde = $fhasta;
            $fhasta = $aux;
        }
        
        return array($ffid, $fdesde, $fhasta);
    }

}
