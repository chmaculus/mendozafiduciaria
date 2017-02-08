<?php

class formaltabase extends main_controller {

    function formaltabase() {
        $this->mod = $this->model("formalta_model");
    }

    function init($id = 0, $credito_caduca=0, $fecha_caduca=0, $tipo_caducidad = FALSE) {

        if (!isset($_SESSION["USERADM"])) {
            header("Location: " . '/' . URL_PATH);
            exit();
        }
        //$this->_get_loged();
        $this->setCss(array("ivory.css", "formalta.css"));
        $this->setJs(array("formalta.js"));
        $this->setPlug( array("chosen","jalerts","numeric","validation","fancybox","jqgrid","jmenu"));

        $id_permiso = 12;
        $arr_permiso_mod = isset($_SESSION["USER_PERMISOS"][$id_permiso]) ? $_SESSION["USER_PERMISOS"][$id_permiso] : 0;

        $arr_permiso_mod = array
            (
            "MOSTRAR" => 1,
            "ALTA" => 1,
            "BAJA" => 1,
            "MODIFICACION" => 1,
            "EXPORTAR" => 1,
            "OTROS" => 1
        );

        $datax = array();
        $datax['main'] = $this->_obtener_main($id, $credito_caduca, $fecha_caduca, $tipo_caducidad);
        $datax['titulo'] = "Administracion";
        $datax['etiqueta_modulo'] = "Carpetas";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USUARIO_SESION_ACTUAL'] = $_SESSION["USERADM"];
        $this->_js_var['_USER_AREA'] = $_SESSION["USER_AREA"];
        $this->_js_var['_USER_PUESTO'] = $_SESSION["USER_PUESTO"];
        $this->_js_var['_USER_ROL'] = $_SESSION["USER_ROL"];

        $this->render($datax);
        //etapas
    }

    function _obtener_main($id, $credito_caduca, $fecha_caduca, $tipo_caducidad) {

        $ultimo = $this->mod->get_next_id();
        
        if ($credito_caduca && $this->mod->set_credito_active($credito_caduca)) {
            $this->mod->set_version_active();
            $this->mod->renew_datos();
            $credito = $this->mod->get_credito_from_id($credito_caduca);
            
            if ($tasas = $this->mod->get_last_cambiotasas($credito['ID_OPERATORIA'], $fecha_caduca)) {
                $credito['T_COMPENSATORIO'] = $tasas['COMPENSATORIO'];
                $credito['T_BONIFICACION'] = $tasas['SUBSIDIO'];
                $credito['T_PUNITORIO'] = $tasas['PUNITORIO'];
                $credito['T_MORATORIO'] = $tasas['MORATORIO'];
            }
            
            $this->mod->set_fecha_actual($fecha_caduca);
            $this->mod->set_fecha_calculo();
            $this->mod->renew_datos();
            if ($tipo_caducidad == 3) {
                $this->mod->emitir_credito_caido($fecha_caduca);
            } else {
                $this->mod->emitir_una_cuota($fecha_caduca);
            }
            
            $this->mod->save_last_state(false);
            $this->mod->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);
            $this->mod->generar_evento(array(), true, $fecha_caduca);
            $ret_reuda = $this->mod->get_deuda($fecha_caduca, true);
            
            $cuotas_restantes = 0;
            
            $credito["ID"] = $ultimo;
            if ($tipo_caducidad == 1) { //1 cuota
                $credito["MONTO_CREDITO"] = number_format($ret_reuda['cuotas'][0]['CAPITAL']['TOTAL'] + $ret_reuda['cuotas'][0]['IVA_PUNITORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['IVA_COMPENSATORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['IVA_MORATORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['PUNITORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['MORATORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['COMPENSATORIO']['TOTAL'], 2, '.', '');
            } elseif ($tipo_caducidad == 2) { //prórroga
                $credito["MONTO_CREDITO"] = number_format($ret_reuda['cuotas'][0]['CAPITAL']['TOTAL'], 2, '.', '');// + $ret_reuda['cuotas'][0]['IVA_COMPENSATORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['COMPENSATORIO']['TOTAL'], 2, '.', '');
            } elseif ($tipo_caducidad == 3) { //credito caido
                $credito["MONTO_CREDITO"] = number_format($ret_reuda['cuotas'][0]['CAPITAL']['TOTAL'] + $ret_reuda['cuotas'][0]['IVA_PUNITORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['IVA_COMPENSATORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['IVA_MORATORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['PUNITORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['MORATORIO']['TOTAL'] + $ret_reuda['cuotas'][0]['COMPENSATORIO']['TOTAL'], 2, '.', '');
            }
            
            $credito['INTERES_VTO'] = date('Y-m-d', strtotime(date('Y-m-d'))+(30*3600*24));
            
            if ($tipo_caducidad == 1 || $tipo_caducidad == 3) {
                $cuotas_restantes = 1;
                $credito['INTERES_VTO'] = date('Y-m-d',$fecha_caduca);
                $credito['DESEMBOLSOS'] = array(
                    array(
                        'MONTO' => $credito['MONTO_CREDITO'],
                        'FECHA' => ($tipo_caducidad == 3) ? $fecha_caduca : $ret_reuda['cuotas'][0]['_INFO']['HASTA']
                    )
                );
            }
            
            if ($tipo_caducidad == 2) {
                $credito['T_COMPENSATORIO'] = 0;
                $cuotas_restantes = $this->mod->cuotas_restantes_prorroga();
            }
            
            $credito["INTERES_CUOTAS"] = $credito["CAPITAL_CUOTAS"] = $cuotas_restantes;
            $credito["ID_CADUCADO"] = $credito_caduca;
            $credito['PRORROGA'] = $tipo_caducidad; //para poner otro estado
            
        } else {

            $credito = array("ID" => $ultimo,
                "ID_OPERACION" => 0,
                "ACTIVIDAD" => "",
                "MONTO_CREDITO" => 10000,
                "MONTO_APORTE" => 0,
                "MONTO_OTRO" => 0,
                "MONTO_TOTAL" => 0,
                "MONTO_CREDITO_POR" => 0,
                "MONTO_APORTE_POR" => 0,
                "MONTO_OTRO_POR" => 0,
                "MONTO_TOTAL_POR" => 0,
                "PLAZO_COMPENSATORIO" => 360,
                "PLAZO_PUNITORIO" => 365,
                "PLAZO_MORATORIO" => 365,
                "T_COMPENSATORIO" => 12,
                "T_PUNITORIO" => 24,
                "T_BONIFICACION" => 0,
                "T_MORATORIO" => 12,
                "T_GASTOS" => 0,
                "T_GASTOS_MIN" => 0,
                "IVA" => 21,
                "INTERES_CUOTAS" => 6,
                "INTERES_VTO" => date("Y-m-d"),
                "INTERES_PERIODO" => 09,
                "CAPITAL_CUOTAS" => 6,
                "CAPITAL_VTO" => date("Y-m-d"),
                "CAPITAL_PERIODO" => 0,
                "DESEMBOLSOS" => array(),
                "POSTULANTES" => 0,
                "ID_OPERATORIO" => 0,
                "ID_FIDEICOMISO" => 0
            );
        }

        if ($id > 0){
            $credito = $this->mod->get_credito_from_id($id);
        }
        
        $credito["CREDITO_CADUCA"] = $credito_caduca;
        $credito["FECHA_CADUCA"] = $fecha_caduca;


        $credito['LPOSTULANTES'] = $this->mod->get_clientes();
        $this->_js_array['FIDEICOMISOS'] = $credito['FIDEICOMISOS'] = $this->mod->get_fideicomisos();
        //print_array($credito );
        $this->_js_array['DESEMBOLSOS'] = $credito['DESEMBOLSOS'];
        $fecha_arr = explode("-", $credito['CAPITAL_VTO']);
        
        list($y, $m, $d) = $fecha_arr;
        $credito['CAPITAL_VTO'] = $d . "-" . $m . "-" . $y;
        $credito['MICRO'] = 0;
        return $this->view("form_generar", array("credito" => $credito));
        /* permiso mostrar */
    }

    function x_guardar_creditos_excel() {
        if (($total = $this->_guardar_creditos_excel())) {
            $_SESSION['msg_ok'] = "Proceso de importación de créditos finalizado, se importaron ($total) créditos";
        }
        
        header('Location:/' . URL_PATH . 'creditos/front/creditos');
        die();
    }
    
    function _guardar_creditos_excel() {
        $_SESSION['msg_err'] = "";
        $excel = $_FILES['fexcel'];
        set_time_limit(0);
        ini_set('memory_limit', '512M');
        if (isset($excel['tmp_name']) && is_file($excel['tmp_name'])) {
            $dir = 'backup/importaciones/' ;
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            
            $file_import = $dir . 'creditos_' . date('Ymd_Hi_') . $excel['name'];
            move_uploaded_file($excel['tmp_name'], $file_import);

            require_once(MODULE_DIRECTORY . 'PHPExcel/PHPExcel.php');
            require_once(MODULE_DIRECTORY . 'PHPExcel/PHPExcel/Reader/Excel2007.php');

            $objReader = new PHPExcel_Reader_Excel2007();
            if ($objPHPExcel = $objReader->load($file_import)) {
                set_time_limit(0);

                $objPHPExcel->setActiveSheetIndex(0);
                $fideicomiso = trim(strip_tags($objPHPExcel->getActiveSheet()->getCell("B1")->getCalculatedValue()));
                if ($fideicomiso) {
                    $fideicomiso = $this->mod->getFideicomisoId($fideicomiso);
                } else {
                    $fideicomiso = 0;
                }

                $operatoria = trim(strip_tags($objPHPExcel->getActiveSheet()->getCell("B2")->getCalculatedValue()));
                if ($operatoria) {
                    $operatoria = $this->mod->getOperatoriaId($operatoria);
                } else {
                    $operatoria = 0;
                }

                $microcredito = str_ireplace("si", "si", trim(strip_tags($objPHPExcel->getActiveSheet()->getCell("B3")->getCalculatedValue())));
                if ($microcredito) {
                    $microcredito = 1;
                } else {
                    $microcredito = 0;
                }

                $err = "";
                
                if (!isset($_SESSION['creditos_importados'])) {
                    $_SESSION['creditos_importados'] = array();
                }
                $total_creditos = 0;

                for ($j = 7; $j <= $objPHPExcel->getActiveSheet()->getHighestDataRow(); $j++) {
                    $cuit = $this->clean_postulantes(str_replace(" ", "|", $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getCalculatedValue()));
                    $_postulantes = $this->clean_postulantes($objPHPExcel->getActiveSheet()->getCell("A" . $j)->getCalculatedValue());
                    if (!$cuit) {
                        continue;
                    }
                    
                    if (count($cuit) != count($_postulantes)) {
                        continue; //a pedido de mercedes se saca la validación 14/06/16
                    }
                    
                    $postulantes = array();
                    $_cuits = array();
                    foreach ($cuit as $cj=>$c) {
                        //$c = trim($c);
                        $c = preg_replace('/[^0-9-]+/', '', $c);
                        
                        if (!($postulante = $this->mod->getClienteIdCUIT($c))) {
                            $cliente = array(
                                'RAZON_SOCIAL' => trim(strip_tags($_postulantes[$cj])),
                                'CUIT' => $c
                            );
                            $postulante = $this->mod->guardar_postulante($cliente);
                        }
                        $postulantes[] = $postulante;
                        $_cuits[] = trim(str_replace("-", "", $c));
                    }
                    
                    $credito_id = trim($objPHPExcel->getActiveSheet()->getCell("C" . $j)->getCalculatedValue());
                    

                    if ($credito_id) {
                        $credito = $this->mod->get_credito_from_id($credito_id);

                        if (!isset($credito['ID'])) {
                            $this->mod->clear();
                            $_POST = array();
                            $campos_validados = TRUE;
                            for ($l = 65; $l < 83; ++$l) {
                                if ($objPHPExcel->getActiveSheet()->getCell(chr($l) . $j)->getCalculatedValue() === "") {
                                    $campos_validados = FALSE;
                                    break;
                                }
                            }
                            
                            if ($campos_validados) {
                                $_POST['fecha'] = '';
                                $_POST['micro'] = $microcredito;
                                $_POST['fecha_inicio'] = PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("D" . $j)->getCalculatedValue()) + 86400;
                                $_POST['credito_id'] = $credito_id;
                                $_POST['int_compensatorio'] = $objPHPExcel->getActiveSheet()->getCell("E" . $j)->getCalculatedValue();
                                if($_POST['int_compensatorio'] && $_POST['int_compensatorio']<1) { //si el valor era porcenta va a traer los datos mal,arreglamos
                                    $_POST['int_compensatorio'] = $_POST['int_compensatorio'] * 100;
                                }

                                $_POST['plazo_compensatorio'] = $objPHPExcel->getActiveSheet()->getCell("F" . $j)->getCalculatedValue();
                                $_POST['int_moratorio'] = $objPHPExcel->getActiveSheet()->getCell("G" . $j)->getCalculatedValue();
                                if($_POST['int_moratorio'] && $_POST['int_moratorio']<1) {
                                    $_POST['int_moratorio'] = $_POST['int_moratorio'] * 100;
                                }

                                $_POST['plazo_moratorio'] = $objPHPExcel->getActiveSheet()->getCell("H" . $j)->getCalculatedValue();
                                $_POST['int_punitorio'] = $objPHPExcel->getActiveSheet()->getCell("I" . $j)->getCalculatedValue();
                                if($_POST['int_punitorio'] && $_POST['int_punitorio']<1) {
                                    $_POST['int_punitorio'] = $_POST['int_punitorio'] * 100;
                                }

                                $_POST['plazo_punitorio'] = $objPHPExcel->getActiveSheet()->getCell("J" . $j)->getCalculatedValue();

                                $_POST['int_subsidio'] = $objPHPExcel->getActiveSheet()->getCell("K" . $j)->getCalculatedValue();
                                if($_POST['int_subsidio'] && $_POST['int_subsidio']<1) {
                                    $_POST['int_subsidio'] = $_POST['int_subsidio'] * 100;
                                }

                                $_POST['cuotas'] = $objPHPExcel->getActiveSheet()->getCell("L" . $j)->getCalculatedValue();
                                $_POST['cuotas_gracia'] = $objPHPExcel->getActiveSheet()->getCell("M" . $j)->getCalculatedValue();

                                $_POST['plazo_pago'] = $objPHPExcel->getActiveSheet()->getCell("P" . $j)->getCalculatedValue();
                                $_POST['periodicidad'] = $objPHPExcel->getActiveSheet()->getCell("O" . $j)->getCalculatedValue();
                                $_POST['periodicidad_tasa'] = $objPHPExcel->getActiveSheet()->getCell("N" . $j)->getCalculatedValue();

                                $_POST['int_subsidio'] = 0;

                                $_POST['total_credito'] = 0;
                                $_POST['clientes'] = $postulantes;
                                $_POST['fideicomiso'] = $fideicomiso;
                                $_POST['operatoria'] = $operatoria;

                                $_POST['desembolsos'] = array();

                                if ($objPHPExcel->getActiveSheet()->getCell("Q" . $j)->getCalculatedValue()) {
                                    $_POST['desembolsos'][] = array(
                                        'fecha' => date('d-m-Y', PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("Q" . $j)->getCalculatedValue()) + 86400),
                                        'monto' => $objPHPExcel->getActiveSheet()->getCell("R" . $j)->getCalculatedValue()
                                    );
                                }

                                if ($objPHPExcel->getActiveSheet()->getCell("S" . $j)->getCalculatedValue()) {
                                    $_POST['desembolsos'][] = array(
                                        'fecha' => date('d-m-Y', PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("S" . $j)->getCalculatedValue()) + 86400),
                                        'monto' => $objPHPExcel->getActiveSheet()->getCell("T" . $j)->getCalculatedValue()
                                    );
                                }

                                if ($objPHPExcel->getActiveSheet()->getCell("U" . $j)->getCalculatedValue()) {
                                    $_POST['desembolsos'][] = array(
                                        'fecha' => date('d-m-Y', PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("U" . $j)->getCalculatedValue()) + 86400),
                                        'monto' => $objPHPExcel->getActiveSheet()->getCell("V" . $j)->getCalculatedValue()
                                    );
                                }

                                if ($objPHPExcel->getActiveSheet()->getCell("W" . $j)->getCalculatedValue()) {
                                    $_POST['desembolsos'][] = array(
                                        'fecha' => date('d-m-Y', PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("W" . $j)->getCalculatedValue()) + 86400),
                                        'monto' => $objPHPExcel->getActiveSheet()->getCell("X" . $j)->getCalculatedValue()
                                    );
                                }

                                foreach ($_POST['desembolsos'] as $des) {
                                    $_POST['total_credito'] += $des['monto'];
                                }

                                if (count($_POST['desembolsos']) > 0) {
                                    $cuit = implode('/', $_cuits);
                                    $_SESSION['creditos_importados'][$cuit] = $credito_id;

                                    $this->x_generar_cuotas(FALSE);
                                    ++$total_creditos;

                                    //guardar los desembolsos
                                    $credito = $_POST;
                                    if (count($credito['desembolsos'])) {
                                        //$this->mod->clear();
                                        $this->mod->set_credito_active($credito['credito_id']);
                                        $this->mod->set_version_active();

                                        $versiones = $this->mod->get_versiones();
                                        if (!isset($versiones[0]['value'])) {
                                           /*echo $credito['credito_id'];
                                           echo "<br />";*/
                                        }
                                        $version = $versiones[0]['value'];
                                        foreach ($credito['desembolsos'] as $desembolso) {
                                            $_POST = array();
                                            $_POST['credito_id'] = $credito['credito_id'];
                                            $_POST['fecha'] = strtotime($desembolso['fecha']);
                                            $_POST['tipo'] = 1;
                                            $_POST['reset'] = 0;
                                            $_POST['version_id'] = $version;
                                            $_POST['monto'] = $desembolso['monto'];

                                            $this->x_agregar_desembolso();
                                        }
                                    }
                                } else {
                                    $err .= "El crédito $credito_id no posee desembolso<br />";
                                }
                            } else {
                                $err .= "El crédito $credito_id faltan datos<br />";
                            }
                            
                        } else {
                            $err .= "El crédito $credito_id ya existe<br />";
                        }
                    }
                }
                
                if ($err) {
                    $_SESSION['msg_err'] = $err;
                }
                
                //si se ha llegado acá debería haber guardado todo bien
                if($total_creditos>0) {
                    return $total_creditos;
                } else {
                    if ($err) $err.="<br />";
                    $err .= "Hubo un inconveniente y no se importaron créditos, verifique el archivo";
                    $_SESSION['msg_err'] = $err;
                    return FALSE;
                }
            } else {
                $_SESSION['msg_err'] = "El archivo no tiene formato de excel";
                return FALSE;
            }

            //return $this->view("form_excel", array("creditos" => $creditos));
        }
        $_SESSION['msg_err'] = "Hubo un problema al cargar el archivo";
        return FALSE;
    }
    
    function x_importar_desembolsos() {
        if ($this->_importar_desembolsos_excel()) {
            $_SESSION['msg_ok'] = "El proceso de importación de desembolsos ha finalizado";
        }
        
        header('Location:/' . URL_PATH . 'creditos/front/creditos');
        die();
    }
    
    function _importar_desembolsos_excel() {
        $_SESSION['msg_err'] = "";
        $excel = $_FILES['fexcel'];
        if (isset($excel['tmp_name']) && is_file($excel['tmp_name'])) {
            $dir = 'backup/importaciones/' ;
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            
            $file_import = $dir . 'desembolso_' . date('Ymd_Hi_') . $excel['name'];
            move_uploaded_file($excel['tmp_name'], $file_import);
            
            require_once(MODULE_DIRECTORY . 'PHPExcel/PHPExcel.php');
            require_once(MODULE_DIRECTORY . 'PHPExcel/PHPExcel/Reader/Excel2007.php');
            
            $objReader = new PHPExcel_Reader_Excel2007();
            if ($objPHPExcel = $objReader->load($file_import)) {
                set_time_limit(0);
                $err = "";
                $objPHPExcel->setActiveSheetIndex(0);
                $arr_creditos = array();
                
                if ($objPHPExcel->getActiveSheet()->getCell("A1")->getCalculatedValue() != 'ID' || $objPHPExcel->getActiveSheet()->getCell("C1")->getCalculatedValue() != 'Desembolso') {
                    $err = "Excel incorrecto";
                    return FALSE;
                }
                
                for ($j = 2; $j <= $objPHPExcel->getActiveSheet()->getHighestDataRow(); $j++) {
                    $credito_id = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getCalculatedValue();
                    $fdesemb = PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("D" . $j)->getCalculatedValue()) + 86400;
                    $monto = $objPHPExcel->getActiveSheet()->getCell("C" . $j)->getCalculatedValue();
                    $arr_creditos[] = array(
                        'ID_CREDITO' => $credito_id,
                        'FECHA' => $fdesemb,
                        'MONTO' => $monto
                    );
                }
                
                
                if ($arr_creditos) {
                    foreach($arr_creditos as $it) {
                        //$this->mod->clear();
                        $this->mod->set_credito_active($it['ID_CREDITO']);
                        $this->mod->set_version_active();

                        $versiones = $this->mod->get_versiones();
                        $version = $versiones[0]['value'];
                        
                        $_POST = array();
                        $_POST['credito_id'] = $it['ID_CREDITO'];
                        $_POST['fecha'] = $it['FECHA'];
                        $_POST['tipo'] = 1;
                        $_POST['reset'] = 0;
                        $_POST['version_id'] = $version;
                        $_POST['monto'] = $it['MONTO'];

                        $this->x_agregar_desembolso();

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
    

    function x_get_operatorias(){
        $id_fideicomiso = $_POST['id_fideicomiso'];
        $operatorias = $this->mod->get_operatorias_alta($id_fideicomiso );
        echo json_encode($operatorias);
        
    }
    
    function x_generar_clientes() {
        $this->mod->generar_clientes();
    }

    function x_generar_cuotas($retorno = TRUE) {
        
        $data['fecha'] = $_POST['fecha'];
        $_SESSION['simulacion_credito'] = FALSE;
        if (isset($_POST['simulacion']) && $_POST['simulacion']) {
            $_POST['credito_caduca'] = FALSE;
            $_SESSION['simulacion_credito'] = $_POST['credito_id'] = 0 - substr(time(), -6);
        }

        //se calcula la fecha de inicio de la primera cuota sobre la fecha del primer vencimiento y la periodicidad
        $primer_vencimiento = $_POST['fecha_inicio'];
        list($d, $m, $y) = explode("/", date("d/m/Y", $_POST['fecha_inicio']));
        $data['fecha_inicio'] = (mktime(0, 0, 0, $m - $_POST['periodicidad'], $d, $y));

        $data['cuotas'] = $_POST['cuotas'];
        $micro = $_POST['micro'];
        $data['cuotas_gracia'] = $_POST['cuotas_gracia'];

        $data['total_credito'] = $_POST['total_credito'];

        $data['por_int_compensatorio'] = $_POST['int_compensatorio'];
        $data['por_int_subsidio'] = $_POST['int_subsidio'];
        $data['plazo_pago'] = $_POST['plazo_pago'];
        $data['por_int_punitorio'] = $_POST['int_punitorio'];
        $data['por_int_moratorio'] = $_POST['int_moratorio'];
        $data['por_int_gastos'] = isset($_POST['int_gastos']) ? $_POST['int_gastos'] : 0;
        $data['por_int_gastos_min'] = isset($_POST['int_gastos']) ? $_POST['int_gastos_min'] : 0;
        $data['periodicidad'] = $_POST['periodicidad'];
        $data['periodicidad_tasa'] = $_POST['periodicidad_tasa'];
        $data['TIPO'] = 0;
        $data['iva'] = key_exists('iva', $_POST) ? $_POST['iva'] : 0.21;
        
        $desembolsos = $_POST['desembolsos'];

        $postulantes = $_POST['clientes'];
        $fideicomiso = $_POST['fideicomiso'];
        $operatoria = $_POST['operatoria'];
        $iva = isset($_POST['tiva']) ? $_POST['tiva'] : IMP_IVA * 100;

        $plazo_compensatorio = $_POST['plazo_compensatorio'];
        $plazo_moratorio = $_POST['plazo_moratorio'];
        $plazo_punitorio = $_POST['plazo_punitorio'];

        $credito_id = key_exists('credito_id', $_POST) ? $_POST['credito_id'] : 1;
        
        $this->mod->set_tipo_credito($micro);
        $this->mod->set_credito_active($credito_id);
        $this->mod->borrar_credito();

        $this->mod->set_compensatorio_plazo($plazo_compensatorio);
        $this->mod->set_moratorio_plazo($plazo_moratorio);
        $this->mod->set_punitorio_plazo($plazo_punitorio);

        $this->mod->set_postulantes($postulantes);
        $this->mod->set_fideicomiso($fideicomiso);
        $this->mod->set_operatoria($operatoria);
        $this->mod->set_iva($iva);


        //si el desembolso inicial es posterior a la fecha de primer vencimiento agregamos un desembolso ficticio al inicio de la cuota de 0
        $desembolso_inicial = reset($desembolsos);
        $monto_incial = $desembolso_inicial['monto'];
        list($d, $m, $y) = explode("-", $desembolso_inicial['fecha']);
        $fecha_desembolso_inicial = mktime(0, 0, 0, $m, $d, $y);


        if ($fecha_desembolso_inicial > $primer_vencimiento) {
            array_unshift($desembolsos, array("fecha" => date("d-m-Y", $primer_vencimiento - 100), "monto" => 0.01));
        }

        $desembolso_inicial = reset($desembolsos);


        $this->mod->set_credito_active($credito_id);
        $this->mod->agregar_version($desembolsos[0]['fecha'], 1, "VERSION INICIAL");
        $this->mod->set_version_active();

        //el primer desembolso genera las cuotas
        //$desembolso_inicial = array_shift($desembolsos);
        list($d, $m, $y) = explode("-", $desembolso_inicial['fecha']);
        $fecha = mktime(0, 0, 0, $m, $d, $y);

        $data['fecha'] = $fecha;
        $data['monto'] = $data['total_credito'];//cambio porque guarda mal el monto total del crédito

        $ret = $this->mod->generar_evento($data, false, $fecha);
        
        
        //la variable microcreditos $micro solo se marca en la tabla fid_creditos y no en las cuotas
        $this->mod->generar_cuotas($ret, 0, $retorno);
        
      /*  $i = 0;

            //incluimos  todos los desembolsos
            foreach ($desembolsos as $desembolso) {

                //generamos fecha formato timestamp
                list($d, $m, $y) = explode("-", $desembolso['fecha']);
                $fecha = mktime(0, 0, 0, $m, $d, $y);

                $data['Tipo'] = $desembolso['monto'];
                $data['monto'] = $desembolso['monto'];

                //genero la variacion corerspondiente al desembolso
                $data['TIPO'] = 1;
                $data['ESTADO'] = 5;
                $ret = $this->mod->generar_evento($data, true, $fecha);
                $cuotas_restantes = $this->mod->get_cuotas_restantes($fecha);

                //agrego el registro desembolso a la db
                $this->mod->agregar_desembolso($data['monto'], $cuotas_restantes, $fecha);
                $this->mod->assign_id_evento($ret['ID'], 1);
        }*/


        $this->mod->set_fecha_actual();
        $this->mod->set_fecha_calculo();

        $this->mod->save_operacion_credito();
        $_POST['credito_caduca'] = isset($_POST['credito_caduca']) ? $_POST['credito_caduca'] : FALSE;
        if ($_POST['credito_caduca'] || $_SESSION['simulacion_credito']) {
            if($_POST['prorroga']) {
                $this->mod->prorrogar_credito($_POST['credito_caduca'], $credito_id, $_POST['fecha_caduca']);
            } else {
                $this->mod->caducar_credito($_POST['credito_caduca'], $credito_id, $_POST['fecha_caduca']);
            }
            
            $versiones = $this->mod->get_versiones();
            $version = $versiones[0]['value'];
            
            foreach ($desembolsos as $desembolso) {
                $_POST = array();
                $_POST['credito_id'] = $credito_id;
                //$_POST['fecha'] = strtotime($desembolso['fecha']) - 1;
                $_POST['fecha'] = strtotime($desembolso['fecha']);
                $_POST['tipo'] = 1;
                $_POST['reset'] = 0;
                $_POST['version_id'] = $version;
                $_POST['monto'] = $desembolso['monto'];

                $this->x_agregar_desembolso();
            }
        }
        
        //buscamos cambios de tasas x operatoria - historial
        $this->mod->setCambiosTasasOperatoria();
    }
    
    function x_agregar_desembolso(){
        $credito_id = $_POST['credito_id'];
        $version_id = $_POST['version_id'];
        $tipo = $_POST['tipo'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version_id);
        $desembolso_solicitud = isset($_POST['desembolso']) ? $_POST['desembolso'] : 0;
        
        
        if ($desembolso_solicitud){
            $this->mod->agregar_desembolso_solicitado($desembolso_solicitud);
        }

        $data = array();
        $data['monto'] = $_POST['monto'];
        $reset = isset($_POST['reset']) ? $_POST['reset'] : 0;
        
        
        if ($tipo==EVENTO_DESEMBOLSO){
            $data['TIPO'] = EVENTO_DESEMBOLSO;
        }
        else{
            $data['TIPO'] = EVENTO_AJUSTE;
        }
        
        $fecha = $_POST['fecha'];
      
        $cuotas_restantes = $this->mod->get_cuotas_restantes( $fecha);
        //evaluamos que existan desembolsos reales anteriores al vencimiento de la primera cuota
        //si no que este desembolso sea mayor al desembolso
        
        $fecha_desembolso_nuevo = $this->mod->verificar_desembolsos_inciales($fecha);
        /*
        if ( $fecha_desembolso_nuevo!==true){
            //si no existe generamos un desembolso minimo sobre el vencimiento de la primera cuota

            //genero la variacion corerspondiente al desembolso
            $ret = $this->mod->generar_evento( $data, true, $fecha_desembolso_nuevo);


            //agrego el registro desembolso a la db   
            $data['monto'] = 0;
            $this->mod->agregar_desembolso( $data['monto'], $cuotas_restantes, $fecha_desembolso_nuevo);
            $this->mod->assign_id_evento($ret['ID'],EVENTO_DESEMBOLSO);                
        }*/
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

        return TRUE;
    }
    
    function x_get_data_operatoria() {
        $id = $_POST['id'];
        $fecha = $_POST['fecha'];
        
        if ($tasas = $this->mod->get_operatoria_tasas($id, $fecha)) {
            echo json_encode($tasas);
        }
        die();
    }
    
    private function clean_postulantes($dato) {
        $dato = explode("|", str_replace(array("\\", "/"), "|", $dato));
        $arr = array();
        foreach ($dato as $v) {
            if (trim($v)) {
                $arr[] = trim($v);
            }
        }
        
        return count($arr) ? $arr : FALSE;
    }

    
}
