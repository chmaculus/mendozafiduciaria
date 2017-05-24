<?php

define("TEMP_ID", -999999);

//from_unixtime();
class credito_model extends main_model {

    var $_i = 0;
    var $_id_operatoria = 0;
    var $_id_fideicomiso = 0;
    var $_postulantes = array();
    var $_id_credito = 0;
    var $_id_version = 0;
    //versiones ancestor, array con fecha y numero de version anteriores
    var $_anc_version = 0;
    var $_anc_version_array = 0;
    var $_fecha_actual = NO_FECHA;
    var $_fecha_calculo = NO_FECHA;
    var $_interese_compensatorio_plazo = 365;
    var $_interese_moratorio_plazo = 365;
    var $_interese_punitorio_plazo = 365;
    var $_total_credito = 0;
    var $_credito = array();
    var $_variaciones = array();
    var $_cuotas = array();
    var $_pagos = array();
    var $_gastos = array();
    var $_tipo_credito = TIPO_CREDITO_NORMAL;
    var $_bsave = TRUE;
    var $_ultimo_vencimiento_subsidio = 0;
    var $_tipo_devengamiento = TIPO_DEVENGAMIENTO_AUTO;
    var $_forzar_no_devengamiento = FALSE;
    var $_estado_credito = ESTADO_CREDITO_NORMAL;
    var $_blog = FALSE;
    var $_periodicidad = 60;
    var $_iva_operatoria = IMP_IVA;
    var $_banco = 0;
    var $_actualizacion_compensatorios = FALSE;
    var $_credito_caido = FALSE;
    var $_suma_act_compens = 0;
    var $_flag_pago_cuota_anterior = FALSE;
    var $_caducado_de = 0;
    var $_prorroga_de = 0;
    var $log_cuotas = -1;
    
    function clear() {
        $this->_i = 0;
        $this->_id_operatoria = 0;
        $this->_id_fideicomiso = 0;
        $this->_postulantes = array();
        $this->_id_credito = 0;
        $this->_id_version = 0;
        $this->_anc_version = 0;
        $this->_anc_version_array = 0;
        $this->_fecha_actual = NO_FECHA;
        $this->_fecha_calculo = NO_FECHA;
        $this->_interese_compensatorio_plazo = 365;
        $this->_interese_moratorio_plazo = 365;
        $this->_interese_punitorio_plazo = 365;
        $this->_total_credito = 0;
        $this->_credito = array();
        $this->_variaciones = array();
        $this->_cuotas = array();
        $this->_pagos = array();
        $this->_gastos = array();
        $this->_tipo_credito = TIPO_CREDITO_NORMAL;
        $this->_bsave = true;
        $this->_ultimo_vencimiento_subsidio = 0;
        $this->_tipo_devengamiento = TIPO_DEVENGAMIENTO_AUTO;
        $this->_forzar_no_devengamiento = false;
        $this->_estado_credito = ESTADO_CREDITO_NORMAL;
        $this->_blog = false;
        $this->_periodicidad = 60;
        $this->_iva_operatoria = IMP_IVA;
        $this->_banco = 0;
        $this->_actualizacion_compensatorios = false;
        $this->_suma_act_compens = 0;
        $this->_flag_pago_cuota_anterior = false;
        $this->_caducado_de = 0;
        $this->_prorroga_de = 0;
    }

    function set_log($log = true) {
        $this->_blog = $log;
    }

    function set_devengamiento_tipo($deveng = TIPO_DEVENGAMIENTO_AUTO) {
        $this->_tipo_devengamiento = $deveng;
    }

    function save_last_state($bsave = true) {
        $this->_bsave = $bsave;
    }

    function verificiar_eventos_posteriores() {

        //se verifica la existencia de eventos descontando los desembolsos teoricos y el evento inicial
        foreach ($this->_variaciones as $variacion) {
            if ($variacion['FECHA'] > $this->_fecha_actual && $variacion['TIPO']) {
                if (!($variacion['TIPO'] == EVENTO_DESEMBOLSO && $variacion['ESTADO'] == 5) && $variacion['TIPO'] != EVENTO_INICIAL) {
                    return true;
                }
            }
        }
        return false;
    }

    function verificiar_eventos_pagos_posteriores() {

        //se verifica la existencia de eventos descontando los desembolsos teoricos y el evento inicial
        foreach ($this->_variaciones as $variacion) {
            if ($variacion['FECHA'] > $this->_fecha_actual && $variacion['TIPO']) {
                if (!($variacion['TIPO'] == EVENTO_DESEMBOLSO && $variacion['ESTADO'] == 5) && $variacion['TIPO'] == EVENTO_RECUPERO) {
                    return true;
                }
            }
        }
        return false;
    }

    //cuando se genera un evento en un registro complementario se genera con un codigo temporal
    //que despues se llena en esta funcion una vez que se obtiene el id del evento de la 
    //tabla principal: fid_creditos_eventos
    function assign_id_evento($id_evento, $tipo) {
        switch ($tipo) {
            case EVENTO_AJUSTE:
            case EVENTO_DESEMBOLSO:
                $this->_db->update("fid_creditos_desembolsos", array("ID_VARIACION" => $id_evento), "ID_VARIACION = " . TEMP_ID);
                break;
            case EVENTO_TASA:
                $this->_db->update("fid_creditos_cambiotasas", array("ID_VARIACION" => $id_evento), "ID_VARIACION = " . TEMP_ID);
                break;
            case EVENTO_RECUPERO:
                $this->_db->update("fid_creditos_pagos", array("ID_VARIACION" => $id_evento), "ID_VARIACION = " . TEMP_ID);
                break;
        }
    }

    //obtiene la lista de versiones en un array en arbol
    function get_versiones() {
        $items = $this->_get_versiones_recursive($this->_id_credito, 0);
        return $items;
    }

    function _get_versiones_recursive($credito = 0, $parent = 0) {
        $this->_db->where("ID_CREDITO_VERSION = " . $credito);
        $this->_db->where("PARENT_ID = " . $parent);
        $this->_db->order_by("ID_CREDITO_VERSION", "DESC");
        $items = $this->_db->get_tabla("fid_creditos_version");
        
        $arr_rtn = array();
        $cantidad = count($items);
        for ($i = 0; $i < $cantidad; $i++) {
            $tmp_arr = $this->_get_versiones_recursive($credito, $items[$i]['ID_VERSION']);
            
            $tmp['label'] = $items[$i]['DESCRIPCION_VERSION'] . "(" . date("d/m/Y", $items[$i]['FECHA_VERSION']) . ")";
            $tmp['value'] = $items[$i]['ID_VERSION'];

            $tmp['selected'] = $items[$i]['ACTIVA'] == 1 ? true : false;
            if ($tmp_arr) {
                $tmp['items'] = $tmp_arr;
            }
            $arr_rtn[] = $tmp;
        }
        return $arr_rtn;
    }
    
    function get_creditos_opciones(){
        $id_credito = $this->_id_credito;
        $this->_db->set_key("CLAVE");
        return $this->_db->get_tabla("fid_creditos_opciones","CREDITO_ID = ".$id_credito);
    }

    //evento en tabla principal en fid_creditos_eventos, devuelve el id generado
    function generar_evento($data, $update = false, $fecha = false, $log = false) {

        $id_credito = $this->_id_credito;
        $id_version = $this->_id_version;

        $fecha = (!$fecha) ? time() : $fecha;
        $this->renew_datos();

        if ($this->_bsave) {
            $this->_db->update("fid_creditos_cuotas", array("ESTADO" => 0), "FECHA_VENCIMIENTO > " . $fecha);
        }



        //si se activa la opcion update toma los datos de la variacion anterior y actualiza con el paramentro data
        //los campos que corresponden
        if ($update) {
            $desembolso = isset($data['monto']) ? $data['monto'] : 0;
            $data['TIPO'] = isset($data['TIPO']) ? $data['TIPO'] : EVENTO_INFORME;
            $data['monto'] = $desembolso;

            //variacion anterior por fecha
            $variacion = array();
            foreach ($this->_variaciones as $var) {
//                if ($var['FECHA'] < $fecha) {
                //CANBIO IMPORTANTE DE SIGNO DE COMPARACION !CUIDADO
                //COMO EVENTO ANTERIOR PARA CLONAR SE UTILIZA UNO QUE PUEDE ESTAR EN LA MISMA FECHA
                if ($var['FECHA'] <= $fecha && $var['ID'] != TEMP_ID) {
                    if ($log) {
                        
                    }

                    $variacion = $var;
                }
            }

            //si el tipo es mayor a 0 (no es inicial)
            if ($data['TIPO'] > EVENTO_INICIAL) {

                //si no encuentra varacion anterior
                if (!$variacion) {
                    $variacion = reset($this->_variaciones);
                }
            }
            if (!$variacion)
                return false;

            //las cuotas restantes se evaluan desde el inicio del credito
            $cuotas = array();
            foreach ($this->_cuotas as $cuota) {
                if ($cuota['FECHA_INICIO'] < ($fecha + 1)) {
                    $cuotas[] = $cuota;
                }
            }

            //si no existen cuotas iniciales se busca la primer cuota existente
            if (!$cuotas) {
                $cuotas[] = reset($this->_cuotas);
            }

            $data['fecha_inicio'] = (key_exists('fecha_inicio', $data)) ? $data['fecha_inicio'] : $variacion['FECHA_INICIO'];
            $data['periodicidad'] = (key_exists('periodicidad', $data)) ? $data['periodicidad'] : $variacion['PERIODICIDAD'];
            $data['por_int_compensatorio'] = (key_exists('por_int_compensatorio', $data)) ? $data['por_int_compensatorio'] : $variacion['POR_INT_COMPENSATORIO'];
            $data['por_int_subsidio'] = (key_exists('por_int_subsidio', $data)) ? $data['por_int_subsidio'] : $variacion['POR_INT_SUBSIDIO'];
            $data['por_int_punitorio'] = (key_exists('por_int_punitorio', $data)) ? $data['por_int_punitorio'] : $variacion['POR_INT_PUNITORIO'];
            $data['por_int_moratorio'] = (key_exists('por_int_moratorio', $data)) ? $data['por_int_moratorio'] : $variacion['POR_INT_MORATORIO'];
            $data['por_int_gastos'] = (key_exists('por_int_gastos', $data)) ? $data['por_int_gastos'] : $variacion['POR_INT_GASTOS'];
            $data['por_int_gastos_min'] = (key_exists('por_int_gastos_min', $data)) ? $data['por_int_gastos_min'] : $variacion['POR_INT_GASTOS_MIN'];
            $data['monto'] = (key_exists('monto', $data)) ? $variacion['CAPITAL'] + $desembolso : $variacion['CAPITAL'];
            $data['iva'] = (key_exists('iva', $data)) ? $data['iva'] : $variacion['IVA'];
            $data['periodicidad_tasa'] = (key_exists('periodicidad_tasa', $data)) ? $data['periodicidad_tasa'] : $variacion['PERIODICIDAD_TASA'];
            $data['plazo_pago'] = (key_exists('plazo_pago', $data)) ? $data['plazo_pago'] : $variacion['PLAZO_PAGO'];

            $data['cuotas_gracia'] = (key_exists('CUOTAS_GRACIA', $data)) ? $data['CUOTAS_GRACIA'] : $variacion['CUOTAS_GRACIA'];
//            $data['cuotas_gracia'] = $variacion['CUOTAS_GRACIA'];

            $data['cuotas'] = (key_exists('cuotas', $data)) ? $data['cuotas'] : $cuotas[count($cuotas) - 1]['CUOTAS_RESTANTES'];
        }
        $data['TIPO'] = isset($data['TIPO']) ? $data['TIPO'] : EVENTO_INICIAL;
        $data['ESTADO'] = isset($data['ESTADO']) ? $data['ESTADO'] : 0;


        $ret = array(
            "ID_CREDITO" => $id_credito,
            "FECHA_INICIO" => $data['fecha_inicio'],
            "PERIODICIDAD" => $data['periodicidad'],
            "CUOTAS_GRACIA" => $data['cuotas_gracia'],
            "POR_INT_COMPENSATORIO" => $data['por_int_compensatorio'],
            "PLAZO_PAGO" => $data['plazo_pago'],
            "POR_INT_SUBSIDIO" => $data['por_int_subsidio'],
            "POR_INT_PUNITORIO" => $data['por_int_punitorio'],
            "POR_INT_MORATORIO" => $data['por_int_moratorio'],
            "POR_INT_GASTOS" => $data['por_int_gastos'],
            "POR_INT_GASTOS_MIN" => $data['por_int_gastos_min'],
            "PERIODICIDAD_TASA" => $data['periodicidad_tasa'],
            "CAPITAL" => $data['monto'],
            "IVA" => $data['iva'],
            "TIPO" => $data['TIPO'],
            "ESTADO" => $data['ESTADO'],
            "CANTIDAD_CUOTAS" => $data['cuotas'],
            "FECHA" => $fecha,
            "ID_VERSION" => $id_version,
            "_FECHA_UPDATE" => time()
        );
        
        if ($this->_bsave) {
            $id = $this->_db->insert("fid_creditos_eventos", $ret);
        } else {
            $id = time();
        }
        $last_variacion = $ret;
        $last_variacion['ID'] = $id;

        $this->_variaciones[$id] = $last_variacion;

        $ret['ID'] = $id;
        if ($log) {
            
        }
        return $ret;
    }

    function elimina_evento($id) {
        if ($this->_bsave) {
            $this->_db->delete("fid_creditos_eventos", "ID = " . $id);
        }

        unset($this->_variaciones[$id]);
    }

    //obtiene un array del estado de todas las cuotas a la fecha dada
    function get_deuda($fecha = false, $renew = true, $monto_pago = 0, $calculo_cuota = 0) {

        $temp_saldo = 0;
        $IVA = $this->_iva_operatoria;

        $fecha = !$fecha ? time() : strtotime(date("Y-m-d", $fecha)) + 86399;

        //obtenemos gastos
        $this->_db->where("ESTADO = 0");
        $this->_db->where("FECHA < " . $fecha);
        $gastos = $this->get_tabla_gastos();
        
        
        $variacion_inicial = reset($this->_variaciones);
        
        $arr_deuda = array("gastos" => array(), "cuotas" => array(), "rtn" => 1, "fecha_reimputacion" => 0);
        
        if ($renew) {
            $this->get_segmentos_cuota(NO_FECHA, true, $calculo_cuota);
            $this->renew_datos($fecha);
        }

        $cuotas = $this->_cuotas;

        //GASTOS
        $gastos_arr = array();
        foreach ($gastos as $gasto) {
            foreach ($cuotas as $cuota) {
                if ($gasto['FECHA'] > $cuota['FECHA_INICIO'] && $gasto['FECHA'] < $cuota['FECHA_VENCIMIENTO']) {
                    $this->_db->where("ID_TIPO = 8 AND CUOTAS_RESTANTES = " . $cuota['CUOTAS_RESTANTES']);
                    $pago_gasto = $this->get_tabla_pagos();

                    $pago_gasto_tmp = array(
                        "TOTAL" => $gasto['MONTO'],
                        "PAGOS" => $pago_gasto,
                        "TIPO" => 8,
                        "SALDO" => $gasto['MONTO'] - $this->_get_saldo($pago_gasto),
                        "ID" => $gasto['ID'],
                        "ROW" => $gasto
                    );
                    $gastos_arr[] = $pago_gasto_tmp;
                    $arr_deuda['gastos'][] = $pago_gasto_tmp;
                }
            }
        }

        foreach ($cuotas as $cuota) {
            $pago = $this->_pagos[$cuota['CUOTAS_RESTANTES']];
            
            //buscamos los gastos correspondientes a la fecha de la cuota
            $arr_gastos = array();
            for ($g = 0; $g < count($gastos_arr); $g++) {
                if ($gastos_arr[$g]['ROW']['FECHA'] > $cuota['FECHA_INICIO'] &&
                        $gastos_arr[$g]['ROW']['FECHA'] <= $cuota['FECHA_VENCIMIENTO']) {
                    unset($gastos_arr['ROW']);
                    $arr_gastos[] = $gastos_arr[$g];
                    //break;
                }
            }
            
            $fecha_desembolso = FALSE;
            
            foreach ($this->_variaciones as $it) {
                if (!$fecha_desembolso && $it['TIPO'] == EVENTO_DESEMBOLSO) {
                    $fecha_desembolso = $it['FECHA'];
                }
            }
            
            if ($cuota['FECHA_VENCIMIENTO'] == $fecha_desembolso) { //fix cálculo en créditos de desembolso=fecha de vencimiento
                $cuota['FECHA_VENCIMIENTO'] += 2;
            }
            
            $arr_saldo = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO'] - 1, true);
            $SALDO_CAPITAL = $arr_saldo['SALDO'];
            
            $dif_dias = ceil(($fecha - $cuota['FECHA_VENCIMIENTO']) / (60 * 60 * 24));
            
            $arr_capital = array(
                "TOTAL" => $arr_saldo['AMORTIZACION_CUOTA'],
                "PAGOS" => $pago[PAGO_CAPITAL],
                "TIPO" => 7,
                "SALDO" => $arr_saldo['AMORTIZACION_CUOTA'] - $pago[PAGO_CAPITAL]);
            
            if ($cuota['ESTADO'] == 1 && false) {
                //IVA COMPENSATORIO
                $arr_iva_compensatorio_subsidio = array(
                    "TOTAL" => $cuota['INT_COMPENSATORIO_IVA_SUBSIDIO'], //$subsidio
                    "PAGOS" => 0, //$subsidio
                    "SALDO" => 0, //$subsidio
                    "TIPO" => 11, //$subsidio
                );

                $arr_compensatorio_subsidio = array(
                    "TOTAL" => $cuota['INT_COMPENSATORIO_IVA_SUBSIDIO'], //$subsidio
                    "PAGOS" => 0, //$subsidio
                    "SALDO" => 0, //$subsidio
                    "TIPO" => 12, //$subsidio
                );

                $arr_iva_compensatorio = array(
                    "TOTAL" => $pago[PAGO_IVA_COMPENSATORIO],
                    "PAGOS" => $pago[PAGO_IVA_COMPENSATORIO],
                    "TIPO" => 1,
                    "SALDO" => 0);

                //COMPENSATORIO
                $arr_compensatorio = array(
                    "TOTAL" => $pago[PAGO_COMPENSATORIO],
                    "PAGOS" => $pago[PAGO_COMPENSATORIO],
                    "TIPO" => 2,
                    "SALDO" => 0);
                //saldo de capital de la cuota + saldo de los intereses compensatorio e impuestos no cancelados
                $SALDO_CUOTA = $arr_capital['SALDO'] + $arr_compensatorio['SALDO'] + $arr_iva_compensatorio['SALDO'];

                $arr_iva_punitorio = array(
                    "TOTAL" => $pago[PAGO_IVA_PUNITORIO],
                    "PAGOS" => $pago[PAGO_IVA_PUNITORIO],
                    "TIPO" => 1,
                    "SALDO" => 0);
                $arr_iva_moratorio = array(
                    "TOTAL" => $pago[PAGO_IVA_MORATORIO],
                    "PAGOS" => $pago[PAGO_IVA_MORATORIO],
                    "TIPO" => 2,
                    "SALDO" => 0);
                $arr_punitorio = array(
                    "TOTAL" => $pago[PAGO_PUNITORIO],
                    "PAGOS" => $pago[PAGO_PUNITORIO],
                    "TIPO" => 4,
                    "SALDO" => 0);
                $arr_moratorio = array(
                    "TOTAL" => $pago[PAGO_MORATORIO],
                    "PAGOS" => $pago[PAGO_MORATORIO],
                    "TIPO" => 5,
                    "SALDO" => 0);
            } else {
                
                //IVA COMPENSATORIO
                //se calcula si ya ha sido informada la cuota



                $total = $cuota['INT_COMPENSATORIO_IVA'];
                $IVA_COMPENSATORIO_SUBSIDIO = $cuota['INT_COMPENSATORIO_IVA_SUBSIDIO'];
                $COMPENSATORIO_SUBSIDIO = $cuota['INT_COMPENSATORIO_SUBSIDIO'];
                
                if ($cuota['ESTADO'] == PLAZO_SUBSIDIO_VENCIDO && $arr_capital['SALDO']) {
                    $IVA_COMPENSATORIO_SUBSIDIO = 0;
                    $COMPENSATORIO_SUBSIDIO = 0;
                }
                
                $arr_iva_compensatorio = array(
                    "TOTAL" => $total,
                    "PAGOS" => $pago[PAGO_IVA_COMPENSATORIO],
                    "TIPO" => 3,
                    "SALDO" => $total - $pago[PAGO_IVA_COMPENSATORIO] - $IVA_COMPENSATORIO_SUBSIDIO);
                
                //if($arr_iva_compensatorio['SALDO'] < 0.5) $arr_iva_compensatorio['SALDO'] = 0;

                $arr_iva_compensatorio_subsidio = array(
                    "TOTAL" => $IVA_COMPENSATORIO_SUBSIDIO, //$subsidio
                    "PAGOS" => 0, //$subsidio
                    "SALDO" => 0, //$subsidio
                    "TIPO" => 11, //$subsidio
                );

                //COMPENSATORIO
                $total = $cuota['INT_COMPENSATORIO'];
                $arr_compensatorio = array(
                    "TOTAL" => $total,
                    "PAGOS" => $pago[PAGO_COMPENSATORIO],
                    "TIPO" => 6,
                    "SALDO" => round($total - $pago[PAGO_COMPENSATORIO] - $COMPENSATORIO_SUBSIDIO, 2));
                
                //if($arr_compensatorio['SALDO'] < 0.5) $arr_compensatorio['SALDO'] = 0;
                
                $arr_compensatorio_subsidio = array(
                    "TOTAL" => $COMPENSATORIO_SUBSIDIO, //$subsidio
                    "PAGOS" => 0, //$subsidio
                    "SALDO" => 0, //$subsidio
                    "TIPO" => 11, //$subsidio
                );

                //saldo de capital de la cuota + saldo de los intereses compensatorio e impuestos no cancelados
                $SALDO_CUOTA = $arr_capital['SALDO'] + $arr_compensatorio['SALDO'] + $arr_iva_compensatorio['SALDO'];
                
                if ($SALDO_CUOTA < 0.5) {
                    $SALDO_CUOTA = 0;
                }
                
                if ($SALDO_CUOTA == 0 && $cuota['FECHA_VENCIMIENTO'] < $fecha) {
                    $cuota['ESTADO'] = 1;
                }

                //IVA PUNITORIO
                $tmp = $cuota['INT_PUNITORIO'];
                $tmp = $tmp * $IVA;

                $tmp = $tmp < 0 ? 0 : $tmp;
                $saldo = round($pago[PAGO_IVA_PUNITORIO], 2);
                $tmp = round($tmp, 2) == 0 ? $saldo : $tmp;
                $arr_iva_punitorio = array(
                    "TOTAL" => $tmp,
                    "PAGOS" => $pago[PAGO_IVA_PUNITORIO],
                    "TIPO" => 1,
                    "SALDO" => ($tmp) - $saldo);

                //IVA MORATORIO
                $tmp = $cuota['INT_MORATORIO'];

                $tmp = $tmp * $IVA;
                $tmp = $tmp < 0 ? 0 : $tmp;
                $saldo = round($pago[PAGO_IVA_MORATORIO], 2);
                $tmp = round($tmp, 2) == 0 ? $saldo : $tmp;


                $arr_iva_moratorio = array(
                    "TOTAL" => $tmp,
                    "PAGOS" => $pago[PAGO_IVA_MORATORIO],
                    "TIPO" => 2,
                    "SALDO" => ($tmp) - $saldo);

                //PUNITORIO
                $tmp = $cuota['INT_PUNITORIO'];


                $tmp = $tmp < 0 ? 0 : $tmp;
                $saldo = $pago[PAGO_PUNITORIO];
                $tmp = round($tmp, 2) == 0 ? $saldo : $tmp;
                $arr_punitorio = array(
                    "TOTAL" => $tmp,
                    "PAGOS" => $pago[PAGO_PUNITORIO],
                    "TIPO" => 4,
                    "SALDO" => ($tmp) - $pago[PAGO_PUNITORIO]);

                //MORATORIO
                $tmp = $cuota['INT_MORATORIO'];

                $tmp = $tmp < 0 ? 0 : $tmp;
                $saldo = $pago[PAGO_MORATORIO];
                $tmp = round($tmp, 2) == 0 ? $saldo : $tmp;
                $arr_moratorio = array(
                    "TOTAL" => $tmp,
                    "PAGOS" => $pago[PAGO_MORATORIO],
                    "TIPO" => 5,
                    "SALDO" => ($tmp) - $pago[PAGO_MORATORIO]);
            }
            
            //dependiendo si la cuota esta vencida o no es el orden de los items
            $total_gastos = 0;
            $total_gastos_pagos = 0;
            
            if ($arr_gastos) {
                foreach( $arr_gastos as $gasto) {
                    $total_gastos += $gasto['TOTAL'];
                    if ($gasto['PAGOS']) {
                        foreach ($gasto['PAGOS'] as $pg_g) {
                            $total_gastos_pagos += $pg_g['MONTO'];
                        }
                    }
                }
            }
            
            $gastos_adm = 0;
            $pago_gasto = 0;
            $pago_iva_gasto = 0;
            if ($this->_credito['T_GASTOS'] > 0) {
                $this->_db->where("ID_TIPO IN (" . PAGO_GASTOS_ADM . ", " . PAGO_IVA_GASTOS_ADM . ") AND CUOTAS_RESTANTES = " . $cuota['CUOTAS_RESTANTES']);
                $_pago_gasto = $this->get_tabla_pagos();
                
                if ($_pago_gasto) {
                    foreach ($_pago_gasto as $pg_g) {
                        switch ($pg_g['ID_TIPO']) {
                            case PAGO_GASTOS_ADM:
                                $pago_gasto += $pg_g['MONTO'];
                                break;
                            case PAGO_IVA_GASTOS_ADM:
                                $pago_iva_gasto += $pg_g['MONTO'];
                                break;
                        }
                    }
                }
                
                $total_gastos_pagos += $pago_gasto;
                
                $TOTAL_CUOTA = $arr_capital['TOTAL'] + $arr_iva_punitorio['TOTAL'] + $arr_iva_moratorio['TOTAL'] + $arr_punitorio['TOTAL'] + $arr_moratorio['TOTAL'] + $arr_compensatorio['TOTAL'] + $arr_iva_compensatorio['TOTAL'] + $total_gastos;
                $gastos_adm = $TOTAL_CUOTA * (1 / (1 - $this->_credito['T_GASTOS'] / 100) - 1);
                if ($TOTAL_CUOTA && $gastos_adm < $this->_credito['T_GASTOS_MIN']) {
                    $gastos_adm = $this->_credito['T_GASTOS_MIN'];
                }
                
                $arr_gastos[] = array(
                    'TOTAL' => $gastos_adm,
                    'PAGOS' => $pago_gasto,
                    'TIPO' => PAGO_GASTOS_ADM,
                    'SALDO' => $gastos_adm - $pago_gasto
                );
            }
            
            $total_gastos += $gastos_adm;
            $saldo_gastos = $total_gastos - $total_gastos_pagos; // volvemos a calcular
            
            $arr_iva_gastos = array(
                "TOTAL" => $gastos_adm * 0.21,
                "PAGOS" => $pago_iva_gasto,
                "TIPO" => PAGO_IVA_GASTOS_ADM,
                "SALDO" => ($gastos_adm * 0.21) - $pago_iva_gasto
                    );
            
            $arr_gastos_varios = array(
                'TOTAL' => $total_gastos,
                'PAGOS' => $total_gastos_pagos,
                'SALDO' => $saldo_gastos 
                );
            
            
            if ($cuota['FECHA_VENCIMIENTO'] < $fecha) {
                $total_a_pagar = $SALDO_CUOTA + $arr_iva_punitorio['SALDO'] + $arr_iva_moratorio['SALDO'] + $arr_punitorio['SALDO'] + $arr_moratorio['SALDO'] + $arr_gastos_varios['SALDO'] + $arr_iva_gastos['SALDO'];
                        
                $arr_deuda['cuotas'][] = array(
                    "GASTOS" => $arr_gastos,
                    "GASTOS_VARIOS" => $arr_gastos_varios,
                    "IVA_GASTOS" => $arr_iva_gastos,
                    "IVA_PUNITORIO" => $arr_iva_punitorio,
                    "IVA_MORATORIO" => $arr_iva_moratorio,
                    "PUNITORIO" => $arr_punitorio,
                    "MORATORIO" => $arr_moratorio,
                    "IVA_COMPENSATORIO" => $arr_iva_compensatorio,
                    "COMPENSATORIO" => $arr_compensatorio,
                    "CAPITAL" => $arr_capital,
                    "ADELANTO" => (isset($this->_pagos[$cuota['CUOTAS_RESTANTES'] - 1][PAGO_ADELANTADO])) ? $this->_pagos[$cuota['CUOTAS_RESTANTES'] - 1][PAGO_ADELANTADO] : 0,
                    "ID" => $cuota['ID'],
                    "COMPENSATORIO_ACT" => $cuota['INT_COMPENSATORIO_ACT'],
                    "CUOTAS_RESTANTES" => $cuota['CUOTAS_RESTANTES'],
                    "DIAS_MORAS" => isset($cuota['DIAS_MORAS']) ? $cuota['DIAS_MORAS'] : '',
                    "_INFO" =>
                    array(
                        "IVA_COMPENSATORIO_SUBSIDIO" => $arr_iva_compensatorio_subsidio,
                        "COMPENSATORIO_SUBSIDIO" => $arr_compensatorio_subsidio,
                        "NUM" => $variacion_inicial['CANTIDAD_CUOTAS'] - $cuota['CUOTAS_RESTANTES'] + 1,
                        "ENVIO" => $cuota['FECHA_ENVIADA'],
                        "DESDE" => $cuota['FECHA_INICIO'],
                        "HASTA" => $cuota['FECHA_VENCIMIENTO'],
                        "ESTADO" => $cuota['ESTADO'],
                        "DIF_DIAS" => $dif_dias,
                        "SALDO_CAPITAL" => $SALDO_CAPITAL,
                        "SALDO_CUOTA" => $SALDO_CUOTA,
                        "TOT_INT_MOR_PUN" => $arr_punitorio['SALDO'] + $arr_moratorio['SALDO'],
                        "TOT_IVA_INT_MOR_PUN" => $arr_iva_punitorio['SALDO'] + $arr_iva_moratorio['SALDO'],
                        "TOTAL_PAGAR" => $total_a_pagar
                    )
                    );
                if ($cuota['ESTADO'] == PLAZO_SUBSIDIO_VENCIDO && $cuota['INT_COMPENSATORIO_SUBSIDIO'] > 0) {

                    $arr_deuda['rtn'] = PLAZO_SUBSIDIO_VENCIDO;
                    $arr_deuda['fecha_reimputacion'] = $cuota['FECHA_INICIO'] + 1;
                }
            } else {
                
                $total_a_pagar = $SALDO_CUOTA + $arr_gastos_varios['SALDO'] + $arr_iva_gastos['SALDO'];
                $arr_deuda['cuotas'][] = array(
                    "GASTOS" => $arr_gastos,
                    "GASTOS_VARIOS" => $arr_gastos_varios,
                    "IVA_GASTOS" => $arr_iva_gastos,
                    "IVA_PUNITORIO" => $arr_iva_punitorio,
                    "IVA_MORATORIO" => $arr_iva_moratorio,
                    "PUNITORIO" => $arr_punitorio,
                    "MORATORIO" => $arr_moratorio,
                    "IVA_COMPENSATORIO" => $arr_iva_compensatorio,
                    "COMPENSATORIO" => $arr_compensatorio,
                    "CAPITAL" => $arr_capital,
                    "ADELANTO" => (isset($this->_pagos[$cuota['CUOTAS_RESTANTES'] - 1][PAGO_ADELANTADO])) ? $this->_pagos[$cuota['CUOTAS_RESTANTES'] - 1][PAGO_ADELANTADO] : 0,
                    "ID" => $cuota['ID'],
                    "COMPENSATORIO_ACT" => isset($cuota['INT_COMPENSATORIO_ACT']) ? $cuota['INT_COMPENSATORIO_ACT'] : 0,
                    "CUOTAS_RESTANTES" => $cuota['CUOTAS_RESTANTES'],
                    "DIAS_MORAS" => isset($cuota['DIAS_MORAS']) ? $cuota['DIAS_MORAS'] : '',
                    "_INFO" =>
                    array(
                        "IVA_COMPENSATORIO_SUBSIDIO" => $arr_iva_compensatorio_subsidio,
                        "COMPENSATORIO_SUBSIDIO" => $arr_compensatorio_subsidio,
                        "NUM" => $variacion_inicial['CANTIDAD_CUOTAS'] - $cuota['CUOTAS_RESTANTES'] + 1,
                        "ENVIO" => $cuota['FECHA_ENVIADA'],
                        "DESDE" => $cuota['FECHA_INICIO'],
                        "HASTA" => $cuota['FECHA_VENCIMIENTO'],
                        "ESTADO" => $cuota['ESTADO'],
                        "DIF_DIAS" => $dif_dias,
                        "SALDO_CAPITAL" => $SALDO_CAPITAL,
                        "SALDO_CUOTA" => $SALDO_CUOTA,
                        "TOT_INT_MOR_PUN" => 0,
                        "TOT_IVA_INT_MOR_PUN" => 0,
                        "TOTAL_PAGAR" => $total_a_pagar
                    )
                );
                /*
                echo $SALDO_CUOTA;
                print_r($arr_gastos_varios);
                print_r($arr_iva_gastos);
                die();*/
                
            }
            
            if ($monto_pago && ($monto_pago + 10) < $temp_saldo) { //+10 para asegurar saldo
                return $arr_deuda;
            }
        }
        
        //print_r($arr_deuda);die();

        return $arr_deuda;
    }

    //function complementaria de calculo de get_deuda
    function _get_saldo($pagos) {
        $total = 0;
        foreach ($pagos as $pago) {
            $total += $pago['MONTO'];
        }
        return $total;
    }

    function agregar_desembolso($desembolso, $cuotas_restantes, $fecha = false) {
        $id_credito = $this->_id_credito;

        $fecha = !$fecha ? time() : $fecha;
        $this->_db->insert("fid_creditos_desembolsos", array(
            "ID_CREDITO" => $id_credito,
            "MONTO" => $desembolso,
            "FECHA" => $fecha,
            "CUOTAS_RESTANTES" => $cuotas_restantes,
            "ID_VARIACION" => TEMP_ID
        ));
    }

    function agregar_tasa($compensatorio, $subsidio, $moratorio, $punitorio, $cuotas_restantes, $fecha = false) {
        $id_credito = $this->_id_credito;
        $fecha = !$fecha ? time() : $fecha;
        $this->_db->insert("fid_creditos_cambiotasas", array(
            "ID_CREDITO" => $id_credito,
            "COMPENSATORIO" => $compensatorio,
            "SUBSIDIO" => $subsidio,
            "MORATORIO" => $moratorio,
            "PUNITORIO" => $punitorio,
            "FECHA" => $fecha,
            "CUOTAS_RESTANTES" => $cuotas_restantes,
            "ID_VARIACION" => TEMP_ID
        ));

        //se recorren todos las variaciones
        foreach ($this->_variaciones as $variacion) {
            
            //los eventos siguientes al cambio de tasa
            if ($variacion['FECHA'] > $fecha) {

                //si encuentra otro cambio de eventos se detiene
                if ($variacion['TIPO'] == EVENTO_TASA) {
                    break;
                }

                $this->_db->update("fid_creditos_eventos", array(
                    "POR_INT_COMPENSATORIO" => $compensatorio,
                    "POR_INT_SUBSIDIO" => $subsidio,
                    "POR_INT_MORATORIO" => $moratorio,
                    "POR_INT_PUNITORIO" => $punitorio
                        ), "ID = " . $variacion['ID']);
            }
        }
    }

    function agregar_gasto($monto, $fecha = false, $concepto = "-SIN-DESCIPCION-") {
        $id_credito = $this->_id_credito;
        $fecha = !$fecha ? time() : $fecha;
        $this->_db->insert("fid_creditos_gastos", array(
            "ID_CREDITO" => $id_credito,
            "MONTO" => $monto,
            "CONCEPTO" => $concepto,
            "ESTADO" => 0,
            "FECHA" => $fecha,
            "ID_VARIACION" => TEMP_ID,
            "ID_VERSION" => $this->_id_version
        ));
    }

    function borrar_credito() {
        
        if (!isset($_SESSION['USERADM']) || !$_SESSION['USERADM']) {
            return FALSE;
        }
        
        
        if ($this->_caducado_de) {
            $this->_db->update("fid_creditos", array("CREDITO_ESTADO" => 0), "ID = " . $this->_caducado_de);
        } elseif ($this->_prorroga_de) {
            $this->_db->update("fid_creditos", array("CREDITO_ESTADO" => 0), "ID = " . $this->_prorroga_de);
        }
        
        $cred = $this->_id_credito;
        $array = array(
            'ID_USUARIO' => $_SESSION['USERADM'],
            'TABLA' => "creditos",
            'ACCION' => "B",
            'Registro' => $cred,
            'FECHA' => date('Y-m-d H:i:s')
        );
        $this->_db->insert("fid_auditoria", $array);
        $this->_db->delete("fid_creditos", "ID = " . $cred);
        $this->_db->delete("fid_creditos_cuotas", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_desembolsos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_gastos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_eventos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_pagos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_cambiotasas", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_version", "ID_CREDITO_VERSION = " . $cred);
    }

    function borrar_credito_soft() {
        $cred = $this->_id_credito;
        $this->_db->update("fid_creditos", array("CREDITO_ESTADO" => ESTADO_CREDITO_ELIMINADO), "ID = " . $cred);
    }

    function guardar_cuotas($arr_cuotas) {
        $credito_id = $this->_id_credito;
        $this->_db->delete("fid_creditos_cuotas", "ID_CREDITO = " . $credito_id);

        foreach ($arr_cuotas as $cuota) {
            unset($cuota['ID']);
            $this->_db->insert("fid_creditos_cuotas", $cuota);
        }
        $this->get_segmentos_cuota();
    }

    //funcion principal donde se setean en los arrays de cuotas y/o la base de datos
    //los datos de los campos de intereses
    function get_segmentos_cuota($fecha = NO_FECHA, $renew = true, $calculo_cuota = 0) {

        if (!$fecha)
            $fecha = $this->_fecha_calculo;

        if ($renew) {
            $this->renew_datos($fecha);
        }
        $cuotas = $this->_cuotas;
        foreach ($cuotas as $cuota) {
            if ($cuota['FECHA_INICIO'] <= $fecha) {
                if ($cuota['ESTADO'] != 1 || true) {

                    //segun la opciones definidas de devengamientos es si se calcula el planchado de la cuota
                    //si se encuentra planchada o estando planchada se calcula normalmente
                    $bplanchado = false;

                    //verificamos si esta planchada
                    if ($cuota['FECHA_ENVIADA'] > 0) {
                        $bplanchado = true;
                    }

                    if ($this->_tipo_devengamiento == TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO) {
                        $bplanchado = true;
                    }

                    //por default la fecha de calculo de la cuota es la fecha ingresada por el usuario
                    //si la cuota se calcula planchada, esta fecha se modifica por el dia del vencimiento de
                    //la cuota
                    $fecha_actual_calculo = $this->_fecha_actual;

                    if ($bplanchado) {

                        //se modifica la fecha limite del calculo
                        $fecha_actual_calculo = $cuota['FECHA_VENCIMIENTO'];

                        //se modifica la fecha de evento de consulta a la fecha de vencimiento de la cuota
                        $ultima_variacion = end($this->_variaciones);
                        $id = $ultima_variacion['ID'];

                        
                        //si es evento informe
                        if ($this->_variaciones[$id]['TIPO'] == EVENTO_INFORME) {
                            
                            //y la fecha de vencimiento de cuota (calculo) es menor al informe
                           // if ($fecha_actual_calculo < $this->_variaciones[$id]['FECHA']) {
                                $fecha_actual_calculo = $this->_variaciones[$id]['FECHA'];
                           // }

                            //           $this->_variaciones[$id]['FECHA'] = $fecha_actual_calculo;
                        }
                    }


                    if ($calculo_cuota && $this->log_cuotas < 0) {
                        $this->log_cuotas = $cuota['ID'] + $calculo_cuota - 1;
                    }

                    //se calculan los segmentos de la cuota segun la fecha establecida
                    $this->_make_segmento_cuota($cuota['ID'], $fecha_actual_calculo);
                    
                    //se unen los segmentos para realizar el calculo de la cuota
                    $tmp = $this->_join_segmento_cuota($cuota['ID'], $fecha_actual_calculo);
                    
                  //  if ($cuota['CUOTAS_RESTANTES']==22)die();
                    if (!$tmp)
                        continue;
                }
            }
            else {
                break;
            }
        }
    }

    //funcion complementaria de get_segmento_cuota, se calculan todas los segmentos de cada cuota,
    //segun los eventos que la afectan
    function _make_segmento_cuota($cuota_id, $fecha = NO_FECHA) {


        $fecha_get = strtotime(date("Y-m-d", $fecha)) + 86399;
        $cuota = $this->_cuotas[$cuota_id];

        $primera_cuota = reset($this->_cuotas);
        $cantidad_cuotas = $primera_cuota['CUOTAS_RESTANTES'];
        $variaciones = array();

        if ($cuota) {
            //si no hay fecha especificada se utiliza la fecha de vencimiento de la cuota
            if (!$fecha) {
                $fecha = $cuota['FECHA_VENCIMIENTO'];
            }



            $bultima_cuota = false;
            $bprimera_cuota = false;

            $FECHA_INICIO_VARIACION = 0;

            //si es la primera cuota
            if ($cuota['CUOTAS_RESTANTES'] == $cantidad_cuotas) {
                $bprimera_cuota = true;
            } else {
                $FECHA_INICIO_VARIACION = $cuota['FECHA_INICIO'];
            }

            $log = "";


            //REVER-------------------------------------------------
            //si es la ultima cuota no se evalua el fin de la cuota para los segmentos
            if ($cuota['CUOTAS_RESTANTES'] == 1) {
                $bultima_cuota = true;
            }

            if (!$bultima_cuota) {
                if ($cuota['FECHA_VENCIMIENTO'] < $fecha) {
                    $fecha = $cuota['FECHA_VENCIMIENTO'];
                }
            }


            reset($this->_variaciones);
            $fecha_1erdesembolso = FALSE;
            foreach ($this->_variaciones as $variacion) {

                if ($variacion['FECHA'] >= $FECHA_INICIO_VARIACION && $variacion['ESTADO'] > -1 && ($variacion['FECHA'] <= $fecha_get || $variacion['TIPO'] == EVENTO_DESEMBOLSO)) {

                    $variaciones[] = $variacion;
                }
                if (!$fecha_1erdesembolso && $variacion['TIPO'] == EVENTO_DESEMBOLSO) {
                    $fecha_1erdesembolso = $variacion['FECHA'];
                }
            }


            //REVER-------------------------------------------------
            $subcuotas = array();

            $bfirst = false;
            $primera_variacion = reset($this->_variaciones);
            //si es la primera cuota la fecha de inicio de la cuota es la primera variacion(desembolso)
            if ($variaciones && $bprimera_cuota) {
                
                $primera_variacion = array_shift($variaciones);
                foreach ($variaciones as $variacion) {
                    //foreach ($this->_variaciones as $variacion) {
                    if ($variacion['TIPO'] == EVENTO_DESEMBOLSO || $variacion['TIPO'] == EVENTO_AJUSTE) {
                        $cuota['FECHA_INICIO'] = $variacion['FECHA'];
                        
                        break;
                    }
                }
            }


            //obtenemos la ultima variacion que afecto a la cuota si no es la primera
            if ($variaciones && !$bprimera_cuota) {

                //se busca en el array completo de eventos que no esta filtrado anteriormente
                //ya que buscamos un evento anterior al inicio de la cuota
                foreach ($this->_variaciones as $variacion) {

                    if ($variacion['FECHA'] <= $cuota['FECHA_INICIO']) {
                        $primera_variacion = $variacion;
                    }
                }
            }
            foreach ($variaciones as $variacion) {
                //si la variacion supera la fecha de vencimiento de la cuenta se genera otro segmento
                //el segmento generado tendra el campo _ACTIVA = 2 el cual se usara para no calcular los intereses
                //compensatorios
                if ($variacion['FECHA'] > $cuota['FECHA_VENCIMIENTO'] && !$bfirst) {


                    $bfirst = true;

                    $log.= date("d/m/Y", $variacion['FECHA']) . "-";

                    $tmp_cuota = $cuota;
                    unset($tmp_cuota['CHILDREN']);
                    $tmp_cuota['TMP']['VARIACION'] = $variacion;

                    $tmp_cuota['TMP']['VARIACION']['FECHA'] = $cuota['FECHA_VENCIMIENTO'];
                    $subcuotas[] = $tmp_cuota;
                }



                //las cuotas siguientes al segmento generado no se usara para no calcular los intereses
                //compensatorios
                $tmp = $cuota;
                if ($bfirst) {
                    $tmp['_ACTIVA'] = -2;
                }
                $tmp['TMP']['VARIACION'] = $variacion;
                unset($tmp['CHILDREN']);
                $subcuotas[] = $tmp;
            }
            
            //con las condiciones anteriores de capital y de intereses
            $segmentos = array();
            
            $fecha_get0 = strtotime(date('Y-m-d', $fecha_get). ' 00:00:00');
            $ultimos_pagos = $this->get_ultimo_pago($cuota['CUOTAS_RESTANTES'], $fecha_get0);
            
            $dias_moras = 0;
            $INT_COMPENSATORIO_ACT = 0;
            
            if ($this->_variaciones) {
                $tasa_compensatoria_pagos = 0;
                for ($i = 1; $i <= 7; $i++) {
                    $tasa_compensatoria_pagos += $this->_pagos[$cuota['CUOTAS_RESTANTES']][$i];
                }
                
                $INTERES_COMPENSATORIO = 0;
                $INTERES_COMPENSATORIO_ACT = 0;
                $IVA_INTERES_COMPENSATORIO = 0;
                $bfin_segmento = false;

                $INT_SUBSIDIO_ACUMULADO = 0;
                $IVA_INT_SUBSIDIO_ACUMULADO = 0;
                
                $int_compensatorio_pago = isset($this->_pagos[$cuota['CUOTAS_RESTANTES']][PAGO_COMPENSATORIO]) ? $this->_pagos[$cuota['CUOTAS_RESTANTES']][PAGO_COMPENSATORIO] : 0;
                
                $_rango_tmp = 0;
                $_rango_moratoria = 0;
                $actualizacion_compensatorio = $this->_suma_act_compens;
                $AMORTIZACION_REAL = 0;
                $this->_suma_act_compens = 0;
                
                
                $primer_cuota = !(count($this->_cuotas) - $cuota['CUOTAS_RESTANTES']);
                $evento_informe = FALSE;
                $variaciones = array();
                foreach($this->_variaciones as $variacion) {
                    if ($primer_cuota || ($variacion['FECHA'] > $cuota['FECHA_INICIO'])) {
                        if ($variacion['TIPO'] == EVENTO_INFORME) {
                            $evento_informe = $variacion;
                        } else {
                            $variaciones[] = $variacion;
                        }
                    }
                }
                
                if (!count($variaciones)) {
                    //$variacion = array_pop($this->_variaciones);
                    $variacion['FECHA'] = $cuota['FECHA_INICIO'];
                    $variaciones[] = $variacion;
                }
                
                if ($evento_informe) {
                    $k = 0;
                    foreach ($variaciones as $variacion) {
                        if ($variacion['FECHA'] >= $evento_informe['FECHA']) {
                            array_splice($variaciones, $k, 0, array($evento_informe));
                            break;
                        }
                        ++$k;
                    }
                }
            
                if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                    foreach ($variaciones as $variacion) {
                        echo "{$variacion['FECHA']} - {$variacion['TIPO']}<br />";
                    } 
                }

                
                $_revision_fecha = 0;
                $INTERES_COMPENSATORIO = 0;
                $INTERES_COMPENSATORIO_ACT = 0;
                $IVA_INTERES_COMPENSATORIO = 0;
                $INT_MORATORIO = 0;
                $INT_PUNITORIO = 0;
                $control_fecha_fin = 0;//evitamos que se recalculen intereses incorrectos
                $fecha_variacion_moratorio = $cuota['FECHA_VENCIMIENTO'];
                
                $INTERES_COMPENSATORIO_VARIACION = 0;
                $PERIODICIDAD_TASA_VARIACION = 0;
                $POR_INT_MORATORIO = 0;
                $POR_INT_PUNITORIO = 0;
                $INT_SUBSIDIO = 0;
                $rango_act = 0;
                
                foreach($this->_variaciones as $iv => $variacion) {
                    if ($cuota['FECHA_INICIO'] >= $variacion['FECHA'] && $variacion['TIPO'] != EVENTO_INFORME) {
                        
                        $INTERES_COMPENSATORIO_VARIACION = $variacion['POR_INT_COMPENSATORIO'];
                        $PERIODICIDAD_TASA_VARIACION = $variacion['PERIODICIDAD_TASA'];
                        $POR_INT_MORATORIO = $variacion['POR_INT_MORATORIO'];
                        $POR_INT_PUNITORIO = $variacion['POR_INT_PUNITORIO'];
                        $INT_SUBSIDIO = $variacion['POR_INT_SUBSIDIO'];
                    }
                }
                
                //parchecito para eventos despues de la fecha tengan cambios
                if (count($variaciones) == 1 && $variaciones[0]['FECHA'] < $cuota['FECHA_VENCIMIENTO']) {
                    $variaciones[] = $variaciones[0]; 
                    $variaciones[1]['FECHA'] = $cuota['FECHA_VENCIMIENTO'];
                }
                
                $AMOR_REAL_ACT = 0;
                $total_pagos = 0;
                $dias_moras = 0;
                $_fecha_inicio = $cuota['FECHA_INICIO'];
                
                if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                    echo '<table id="debug"><tr><td colspan="3">CUOTAS RESTANTES: <strong>' . $cuota['CUOTAS_RESTANTES'] . '</strong></td></tr>';
                }
                
                foreach($variaciones as $iv => $variacion) {
                    if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                        echo '<tr>';
                    }
                
                    $tmp = $variacion;
                    
                    $tmp['_ID_VARIACION'] = $variacion['ID'];
                    if (isset($tmp['ASOC'])) {
                        unset($tmp['ASOC']);
                    }
                    
                    
                    
                    $fecha_fin = $cuota['FECHA_VENCIMIENTO'];
                    $fecha_fin2 = date('Y-m-d', $fecha_fin);
                    
                    if ($control_fecha_fin) {
                        $fecha_inicio = $control_fecha_fin;
                    } else {
                        $fecha_inicio = $variacion['FECHA'];
                    }
                    
                    if (!$INTERES_COMPENSATORIO && $fecha_inicio > $cuota['FECHA_INICIO']) {
                        $fecha_inicio = $cuota['FECHA_INICIO'];
                    }
                    
                    $nv = $iv;
                    
                    $flag = TRUE;
                    
                    while(isset($variaciones[$nv]) && $flag) {
                        if ($variaciones[$nv]['TIPO'] == EVENTO_DESEMBOLSO || $variaciones[$nv]['TIPO'] == EVENTO_TASA) {
                            $fecha_fin = $variaciones[$nv]['FECHA'];
                            $flag = FALSE;
                        }
                        $nv++;
                    }
                    
                    if ($variacion['FECHA'] < $_revision_fecha) {
                        //continue;
                    }
                    
                    $_revision_fecha = $variacion['FECHA'];
                    
                    if ($fecha_fin > $cuota['FECHA_VENCIMIENTO']) {
                        $fecha_fin = $cuota['FECHA_VENCIMIENTO'];
                    }
                    
                    $fecha_fin2 = date('Y-m-d', $fecha_fin);
                    $fecha_inicio2 = date('Y-m-d', $fecha_inicio);
                    
                    if ($fecha_fin > $control_fecha_fin) {
                        $control_fecha_fin = $fecha_fin;
                    }
                    
                    $rango_comp = ($fecha_fin - $fecha_inicio) / (24 * 3600);
                    $rango_comp = $rango_comp > 0 ? $rango_comp : 0;
                    $ranto_total_comp = ($cuota['FECHA_VENCIMIENTO'] - $cuota['FECHA_INICIO']) / (24 * 3600);
                    
                    /* RESOLVER RANGOS DE INTERESES COMPENSATORIOS */
                    $rango_comp_real = 0;
                    $calc_rango = ($variacion['TIPO'] == EVENTO_DESEMBOLSO && $variacion['FECHA'] < $cuota['FECHA_VENCIMIENTO']);
                    $calc_rango = $calc_rango || ($variacion['TIPO'] == EVENTO_TASA && $variacion['FECHA'] < $cuota['FECHA_VENCIMIENTO'] && $variacion['ASOC']['COMPENSATORIO'] != $INTERES_COMPENSATORIO_VARIACION);
                    $calc_rango = $calc_rango || ($variacion['FECHA'] >= $cuota['FECHA_VENCIMIENTO']);
                    $calc_rango = $calc_rango || (count($variaciones) - 1 == $iv);
                    
                    if ($calc_rango && $_fecha_inicio) {
                        if (count($variaciones) - 1 == $iv && $variacion['FECHA'] < $cuota['FECHA_VENCIMIENTO']) { //si no hay eventos dsp de la fecha de vencimiento de la cuota
                            $f_rc_fin = $cuota['FECHA_VENCIMIENTO'];
                        } else {
                            $f_rc_fin = ($variacion['FECHA'] > $cuota['FECHA_VENCIMIENTO']) ? $cuota['FECHA_VENCIMIENTO'] : $variacion['FECHA'];
                        }
                        $rango_comp_real = ($f_rc_fin - $_fecha_inicio) / (24 * 3600);
                        $rango_comp_real = $rango_comp_real > 0 ? $rango_comp_real : 0;
                        if ($variacion['FECHA'] >= $cuota['FECHA_VENCIMIENTO'])
                            $_fecha_inicio = 0;
                        else
                            $_fecha_inicio = $f_rc_fin;
                    }
                    
                    
                    $capital_arr = $this->_get_saldo_capital($fecha_inicio, true, false);
                    //$capital_arr = $this->_get_saldo_capital($fecha_inicio, true, false);
                    $SALDO_CAPITAL = $capital_arr['SALDO_TEORICO'];
                    $tmp['CAPITAL_CUOTA'] = $capital_arr['AMORTIZACION_CUOTA'];
                    $tmp['SALDO_CAPITAL'] = $SALDO_CAPITAL;
                    
                    
                    //primero definimos los valores
                    
                    //luego buscamos los valores más correctos a la fecha
                    $tmp_tv = FALSE;
                    
                    $tmp['INT_COMPENSATORIO'] = 0;
                    $tmp['INT_COMPENSATORIO_SUBSIDIO'] = 0;
                    $tmp['INT_MORATORIO'] = $tmp['INT_PUNITORIO'] = 0;
                    /////
                    if($fecha_get >= $cuota['FECHA_VENCIMIENTO'] || $this->_tipo_devengamiento == TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO) {
                        if ($fecha_1erdesembolso == $cuota['FECHA_VENCIMIENTO']) {
                            $cuota['FECHA_VENCIMIENTO'] += 1;
                        }
                    
                        $interes = $rango_comp_real ? $this->_calcular_interes($SALDO_CAPITAL, $rango_comp_real, $INTERES_COMPENSATORIO_VARIACION, $PERIODICIDAD_TASA_VARIACION, $cuota['CUOTAS_RESTANTES'] == 16) : 0;
                        $interes_subsidio = $this->_calcular_interes($SALDO_CAPITAL, $rango_comp_real, $INT_SUBSIDIO, $PERIODICIDAD_TASA_VARIACION, $cuota['CUOTAS_RESTANTES'] == 16);
                        
                        if ($cuota['ID']==$this->log_cuotas && $interes) {
                            echo "<td>" . date("Y-m-d", $variacion['FECHA']) . "<br />" . date("Y-m-d", $cuota['FECHA_VENCIMIENTO']) . "<br>";
                        }

                        $tmp['INT_COMPENSATORIO'] = $interes;
                        $tmp['INT_COMPENSATORIO_SUBSIDIO'] = $interes_subsidio;
                        

                        if ($cuota['ID']==$this->log_cuotas && $tmp['INT_COMPENSATORIO']) {
                            echo "R: {$rango_comp}<br>";
                            echo "S: " . round($SALDO_CAPITAL, 2) ."<br>";
                            echo "IK: " . round($INTERES_COMPENSATORIO_VARIACION, 2) ."<br>";
                            echo "I: " . round($tmp['INT_COMPENSATORIO'], 2) ."<br>";
                            echo "TIPO:{$variacion['TIPO']}</td>";
                        }

                        $INT_SUBSIDIO_ACUMULADO += $interes_subsidio;
                        $IVA_INT_SUBSIDIO_ACUMULADO += ($interes_subsidio * $this->_iva_operatoria);

                        $INTERES_COMPENSATORIO += $tmp['INT_COMPENSATORIO'];
                        $IVA_INTERES_COMPENSATORIO += ($tmp['INT_COMPENSATORIO'] * $this->_iva_operatoria);

                        /*if ($cuota['ID'] == 8943 && $variacion['TIPO'] == EVENTO_INFORME) {
                            echo $variacion['FECHA']."<BR />";
                            echo $fecha_get."<BR />";
                        }*/
                        
                        $total_pagos = $this->_get_pagos_cuota($variacion['FECHA'], $cuota['CUOTAS_RESTANTES']);
                            if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                                echo "<td>{$variacion['TIPO']} - " . date('Y-m-d', $variacion['FECHA']) . "</td>";
                            }

                        //analizar moratorios y punitorios-- y si es actualización de compensatorios
                        if ($variacion['FECHA'] > $cuota['FECHA_VENCIMIENTO'] && ($fecha_get >= $variacion['FECHA'] || $variacion['TIPO'] == EVENTO_INFORME)) {
                            if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                                echo "<td>aca</td>";
                            }
                            //if ($fecha_get > $cuota['FECHA_VENCIMIENTO'] && $variacion['FECHA'] > $cuota['FECHA_VENCIMIENTO']) {
                            //$fecha_get > $cuota['FECHA_VENCIMIENTO'] && $variacion['FECHA'] > $cuota['FECHA_VENCIMIENTO']
                            $rango_int_mor = ($variacion['FECHA'] - $fecha_variacion_moratorio) / (24 * 3600);
                            $rango_int_mor = round($rango_int_mor);
                          
                            $fecha_variacion_moratorio = $variacion['FECHA'];
                            //$capital_arr = $this->_get_saldo_capital($fecha_inicio, true, false);

                            $pagos_arr = $this->_get_pagos_tipo($variacion['FECHA'] - 1, true);
                            $pagos = $pagos_arr[$cuota['CUOTAS_RESTANTES']];

                            $total = $pagos[PAGO_CAPITAL] + $pagos[PAGO_IVA_COMPENSATORIO] + $pagos[PAGO_COMPENSATORIO];

                            $capital_arr = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO'] - 1, true, false);
                            
                            $SALDO_CUOTA = $capital_arr['AMORTIZACION_CUOTA'];
                            if (!$this->_credito_caido) {
                                $SALDO_CUOTA += $INTERES_COMPENSATORIO + $IVA_INTERES_COMPENSATORIO;
                                
                                if ($this->_actualizacion_compensatorios) {
                                    $SALDO_CUOTA -= $AMOR_REAL_ACT;
                                } else {
                                    $SALDO_CUOTA -= $total;
                                }
                                //echo "SALDO CUOTA:<br/>AMOR:{$capital_arr['AMORTIZACION_CUOTA']} <br/>IC:$INTERES_COMPENSATORIO <br/>ICI:$IVA_INTERES_COMPENSATORIO <br/>T: $total<br/>";
                            }
                            
                            if ($SALDO_CUOTA < 0.2) {
                                $SALDO_CUOTA = 0;
                                break;
                            }
                            
                            
                            $tmp['INT_MORATORIO'] = $SALDO_CUOTA * (1 + ($POR_INT_MORATORIO / 100) * $rango_int_mor / $this->_interese_moratorio_plazo ) - $SALDO_CUOTA;
                            $tmp['INT_PUNITORIO'] = $SALDO_CUOTA * (1 + ($POR_INT_PUNITORIO / 100) * $rango_int_mor / $this->_interese_punitorio_plazo) - $SALDO_CUOTA;
                            
                            if ($SALDO_CUOTA) {
                                $dias_moras += $rango_int_mor;
                            }
                            
                            if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                                echo "<td>EVENTO " . $variacion['TIPO'] . "<br />" . date(" d/m/Y", $variacion['FECHA']) . "<br />";
                                echo "PAGOS: " . round($total_pagos, 2) . "<br />AMORT.REAL. $AMOR_REAL_ACT</td>";
                            }

                            if ($this->_actualizacion_compensatorios && $capital_arr['AMORTIZACION_CUOTA']) {
                                
                                $rango_act += $rango_int_mor;
                                //traer el saldo - los pagado del saldo de la cuota
                                //$pagos_arr = $this->_get_pagos_tipo($variacion['FECHA'], true);
                                
                                $SALDO_ACT_COMP = $SALDO_CUOTA;
                                
                                
//                                $total_pagos = 0;
//                                if ($variacion['TIPO'] == EVENTO_RECUPERO) {
//                                    $total_pagos = $this->_get_pagos_cuota($variacion['FECHA'], $cuota['CUOTAS_RESTANTES']);
//                                }
                                
                                
                                
                                if ($SALDO_ACT_COMP > $capital_arr['AMORTIZACION_CUOTA']) {
                                    $SALDO_ACT_COMP = $capital_arr['AMORTIZACION_CUOTA'];
                                }
//                                
//                                if ($SALDO_CUOTA < $capital_arr['AMORTIZACION_CUOTA']) {
//                                    $SALDO_ACT_COMP = $SALDO_CUOTA; // saldo_act no puede ser superior a la amortización
//                                }
                                //$pagos_dif_compens = $pagos[PAGO_COMPENSATORIO] + $pagos[PAGO_IVA_COMPENSATORIO];
                                //$dif_compens = $INTERES_COMPENSATORIO + $IVA_INTERES_COMPENSATORIO - $pagos_dif_compens;
                                
                                if ($SALDO_ACT_COMP > 0.50) { //&& ($dif_compens > 0.5 || $pagos_dif_compens == 0 )
                                    
                                    if (($variacion['TIPO'] == EVENTO_RECUPERO && $total_pagos > 0) || $variacion['TIPO'] == EVENTO_TASA || $variacion['TIPO'] == EVENTO_INFORME) {
                                        if (!isset($INTERES_COMPENSATORIO_VARIACION_ACT)) {
                                            $INTERES_COMPENSATORIO_VARIACION_ACT = $INTERES_COMPENSATORIO_VARIACION;
                                        }
                                        
                                        $interes_act_comp = $this->_calcular_interes($SALDO_ACT_COMP, $rango_act, $INTERES_COMPENSATORIO_VARIACION_ACT, $PERIODICIDAD_TASA_VARIACION, $cuota['CUOTAS_RESTANTES'] == 16);
                                        $tmp['INT_COMPENSATORIO'] += $interes_act_comp;
                                        $INTERES_COMPENSATORIO_ACT += $interes_act_comp;
                                        
//                                        if ($variacion['TIPO'] == EVENTO_RECUPERO) {
//                                            $INTERES_COMPENSATORIO += $INTERES_COMPENSATORIO_ACT;
//                                            $IVA_INTERES_COMPENSATORIO += ($INTERES_COMPENSATORIO_ACT * $this->_iva_operatoria);
//                                            $INTERES_COMPENSATORIO_ACT = 0;
//                                        }

                                        $INT_COMPENSATORIO_ACT += $interes_act_comp;
                                        if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                                            ECHO "<td>CALCULO DE ACT.COMP.<br />";
                                            echo "S: " . round($SALDO_ACT_COMP, 2) . "<br />";
                                            echo "R: $rango_act<br />";
                                            echo "SA: " . round($capital_arr['AMORTIZACION_CUOTA'], 2). "<BR />";
                                            echo "TC: " . round($INTERES_COMPENSATORIO_VARIACION_ACT, 2) ."<BR />";
                                            echo "Total pagos: " . round($total_pagos, 2) ."<BR />";
                                            echo "IAC: " . round($interes_act_comp, 2) ."</td>";
                                        }
                                        $rango_act = 0; //reseteo
                                        $INTERES_COMPENSATORIO_VARIACION_ACT = $variacion['POR_INT_COMPENSATORIO'];
                                    }
                                }
                            }
                            
                            if ($tmp['INT_MORATORIO'] > $INT_MORATORIO) {
                                $INT_MORATORIO = $tmp['INT_MORATORIO'];
                            }
                            if ($tmp['INT_PUNITORIO'] > $INT_PUNITORIO) {
                                $INT_PUNITORIO = $tmp['INT_PUNITORIO'];
                            }

                            if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                                ECHO "<td>CALCULO DE MORATORIOS<br />";
                                echo "S: " . round($SALDO_CUOTA, 2) . "<br />";
                                echo "R: " . round($rango_int_mor, 2) . "<br />";
                                echo "SA: " . round($capital_arr['AMORTIZACION_CUOTA'], 2). "<BR />";
                                echo "TM/TP: " . round($POR_INT_MORATORIO, 2) ." / " . $POR_INT_PUNITORIO ."<br />";
                                echo "IM/P: " . round($tmp['INT_MORATORIO'], 2) . "/" . round($tmp['INT_PUNITORIO'], 2) . "</td>";
                                //print_r($variaciones);die();
                            }
                            
                        }
                        
                        if ($this->_actualizacion_compensatorios && $variacion['TIPO'] == EVENTO_RECUPERO) {
                            $AMOR_REAL_ACT = 0;
                            $amort_pagos = $total_pagos;
                            $int_mp = 0;
                            foreach ($segmentos as $vs) {
                                $int_mp += $vs['INT_MORATORIO'] + $vs['INT_PUNITORIO'];
                        }
                            $int_mp += $tmp['INT_MORATORIO'] + $tmp['INT_PUNITORIO'];
                            $int_mp *= (1 + $this->_iva_operatoria);
                        
                            //amortización de int. moratorios y punitorios
                            if ($amort_pagos > $int_mp) {
                                $amort_pagos -= $int_mp;
                            } else {
                                $amort_pagos = 0;
                            }

                            if ($amort_pagos <= 0) {
                                //dejamos $AMOR_REAL_ACT=0;
                            } elseif ($amort_pagos > $IVA_INTERES_COMPENSATORIO) {
                                $AMOR_REAL_ACT += $IVA_INTERES_COMPENSATORIO;
                                $amort_pagos -= ($IVA_INTERES_COMPENSATORIO + ($INT_COMPENSATORIO_ACT * $this->_iva_operatoria));
                                $amort_pagos = $amort_pagos > 0 ? $amort_pagos : 0;
                                
                                if ($amort_pagos > $INTERES_COMPENSATORIO) {
                                    $AMOR_REAL_ACT += $INTERES_COMPENSATORIO;
                                    $amort_pagos -= ($INTERES_COMPENSATORIO + $INT_COMPENSATORIO_ACT);
                                    $amort_pagos = $amort_pagos > 0 ? $amort_pagos : 0;
                                }
                            }

                            $AMOR_REAL_ACT += $amort_pagos;

                            if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                                echo '<td>AMORT.REAL<br>' . $AMOR_REAL_ACT . '</td>';
                            }
                        }
                        
                        //$dias_moras += $rango_int_mor;
                    }
                    
                    //BUSCAMOS UN CAMBIO DE TASA
                    foreach ($this->_variaciones as $tv) {
                        if ($variacion['FECHA'] >= $tv['FECHA'] && $tv['TIPO'] == EVENTO_TASA && $tv['FECHA'] <= $fecha_get) {
                        //if ($variacion['FECHA'] >= $tv['FECHA'] && $tv['TIPO'] == EVENTO_TASA && $tv['FECHA'] <= $fecha_get && ($tv['FECHA'] <= $cuota['FECHA_VENCIMIENTO'] || isset($tv['CRED_CAIDO']))) {
                            if ($tv['FECHA'] <= $cuota['FECHA_VENCIMIENTO'] || isset($tv['CRED_CAIDO'])) {
                                $INT_SUBSIDIO = $tv['POR_INT_SUBSIDIO'];
                                $INTERES_COMPENSATORIO_VARIACION = $tv['POR_INT_COMPENSATORIO'];
                            }
                            $PERIODICIDAD_TASA_VARIACION = $tv['PERIODICIDAD_TASA'];
                            $POR_INT_MORATORIO = $tv['POR_INT_MORATORIO'];
                            $POR_INT_PUNITORIO = $tv['POR_INT_PUNITORIO'];
                        }
                    }
                    
                    $tmp['INT_COMPENSATORIO_IVA_SUBSIDIO'] = $tmp['INT_COMPENSATORIO_SUBSIDIO'] * $this->_iva_operatoria;
                    $tmp['INT_COMPENSATORIO_IVA'] = $tmp['INT_COMPENSATORIO'] * $this->_iva_operatoria;

                    $tmp['DIAS'] = $rango_comp;
                    
                    $tmp['FECHA_VENCIMIENTO_REAL'] = $fecha_fin;
                    $tmp['FECHA_INICIO_REAL'] = $fecha_inicio;
                    
                    $segmentos[] = $tmp;
                    
                    if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                        echo '</tr>';
                    }
                }
                
                if ($this->log_cuotas && $cuota['ID'] == $this->log_cuotas) {
                    echo '</table><br><br>';
                }
            }
            
            unset($cuota['ID']);
            $cuota_segmento = array();
            //compatibilidad objeto 
            $CHILDREN = array();
            foreach ($segmentos as $segmento) {
                $cuota_segmento = $cuota;

                unset($cuota_segmento['ID']);
                unset($cuota_segmento['TIPO']);
                unset($cuota_segmento['FECHA']);
                unset($cuota_segmento['fi']);
                unset($cuota_segmento['fv']);
                unset($cuota_segmento['f']);
                unset($cuota_segmento['CHILDREN']);

                //buscamos la fecha real que no se ha modificado por los calculos de interes
                $segmento['FECHA_INICIO'] = $segmento['FECHA_INICIO_REAL'];
                $segmento['FECHA_VENCIMIENTO'] = $segmento['FECHA_VENCIMIENTO_REAL'];
                $cuota_segmento['ID_VERSION'] = $this->_id_version;
                $cuota_segmento['_PARENT'] = $cuota_id;
                $cuota_segmento['_ACTIVA'] = 0;

                $cuota_segmento['POR_INT_COMPENSATORIO'] = isset($segmento['POR_INT_COMPENSATORIO']) ? $segmento['POR_INT_COMPENSATORIO'] : 0;
                $cuota_segmento['_ID_VARIACION'] = $segmento['_ID_VARIACION'];
                $cuota_segmento['INT_PUNITORIO'] = $segmento['INT_PUNITORIO'];
                $cuota_segmento['INT_MORATORIO'] = $segmento['INT_MORATORIO'];
                $cuota_segmento['SALDO_CAPITAL'] = $segmento['SALDO_CAPITAL'];
                $cuota_segmento['CAPITAL_CUOTA'] = $segmento['CAPITAL_CUOTA'];
                $cuota_segmento['INT_COMPENSATORIO'] = $segmento['INT_COMPENSATORIO'];

                $cuota_segmento['FECHA_INICIO'] = $segmento['FECHA_INICIO'];
                $cuota_segmento['FECHA_VENCIMIENTO'] = $segmento['FECHA_VENCIMIENTO'];
                $cuota_segmento['ESTADO'] = $segmento['ESTADO'];

                $cuota_segmento['INT_COMPENSATORIO_SUBSIDIO'] = $segmento['INT_COMPENSATORIO_SUBSIDIO'];
                $cuota_segmento['INT_COMPENSATORIO_IVA_SUBSIDIO'] = $segmento['INT_COMPENSATORIO_SUBSIDIO'] * $this->_iva_operatoria;
                $cuota_segmento['INT_COMPENSATORIO_IVA'] = $segmento['INT_COMPENSATORIO'] * $this->_iva_operatoria;

                $new_id_subcuota = uniqid();
                        
                $cuota_segmento['TIPO'] = $this->_variaciones[$segmento['_ID_VARIACION']]['TIPO'];
                $cuota_segmento['FECHA'] = $this->_variaciones[$segmento['_ID_VARIACION']]['FECHA'];
                $cuota_segmento['fi'] = date("d-m-Y", $cuota_segmento['FECHA_INICIO']);
                $cuota_segmento['fv'] = date("d-m-Y", $cuota_segmento['FECHA_VENCIMIENTO']);
                $cuota_segmento['f'] = date("d-m-Y", $cuota_segmento['FECHA']);

                //SOLO SE INSERTAN LAS MODIFICADAS EN LA VERSION
                $CHILDREN[$new_id_subcuota] = $cuota_segmento;
            }

            $ARR_CHLD = array();
            //RECORREMOS LOS NUEVOS Y LOS INSERTAMOS TODOS
            foreach ($CHILDREN as $CH_TMP) {

                unset($CH_TMP['TMP']);
                $ARR_CHLD[] = $CH_TMP;
            }
            $this->_cuotas[$cuota_id]['CHILDREN'] = $ARR_CHLD;
            $this->_cuotas[$cuota_id]['DIAS_MORAS'] = $dias_moras;
            $this->_cuotas[$cuota_id]['INT_COMPENSATORIO_ACT'] = $INT_COMPENSATORIO_ACT;
            
            $this->_cuotas[$cuota_id]['FECHA_INICIO'] = $cuota['FECHA_INICIO'];
            $cuota['CHILDREN'] = $segmentos;
        }
        
        
        
        return $cuota;
    }

    //funcion complementaria de get_segmento_cuota, suma todos los segmentos que componenen la cuota
    //y los calcula para formar el registro de cuota
    function _join_segmento_cuota($cuota_id, $fecha_actual = false) {


        $fecha = $fecha_actual ? $fecha_actual : $this->_fecha_actual;

        $cuota = $this->_cuotas[$cuota_id];

        //obtenemos los segmentos hasta la fecha especificada
        $segmentos = array();


        
        
        foreach ($cuota['CHILDREN'] as $cuota_item) {
           // if ($cuota_item['FECHA_VENCIMIENTO'] <= $fecha || $cuota_item['TIPO'] == EVENTO_DESEMBOLSO) { //también incluimos eventos desembolsos
                $segmentos[] = $cuota_item;
            //}
        }
            
        if (!$cuota)
            return false;

        $bsegmentos = false;

        //se calculan los segmentos si hay segmentos
        if ($segmentos) {

            if ($cuota['FECHA_INICIO'] < $fecha) {
                $bsegmentos = true;
            }

            //si es la cuota actual de calculo
            if ($cuota['FECHA_INICIO'] <= $fecha && $cuota['FECHA_VENCIMIENTO'] > $fecha) {
                
                if ($this->_tipo_credito == TIPO_MICROCREDITO) {
                    $bsegmentos = false;
                }
            }
        }
            
        $cuotas_anteriores = array();
        foreach ($this->_cuotas as $cuota_item) {
            if ($cuota_item['CUOTAS_RESTANTES'] == $cuota['CUOTAS_RESTANTES'])
                break;
            $cuotas_anteriores[] = $cuota_item;
        }

        $cantidad_cuotas_anteriores = count($cuotas_anteriores);

        $CANTIDAD_CUOTAS_GRACIA = 0;
        $fecha_enviada = 0;

        //se calcula el segmento si
        //-existen segmentos y no es microcredito (ya on)
        //- o existen segmentos e independientemente si es microcredito, se fuerza el devengamiento a la fecha de calculo
        //- no ha sido enviada (no ha sido "planchada"
        if ($bsegmentos) {


            /* if ( ($segmentos && $this->_tipo_credito != TIPO_MICROCREDITO) || 
              $segmentos && $this->_forzar_devengamiento_a_fecha ) { */
            //calculamos saldo de capital

            $capital_arr = $this->_get_saldo_capital($cuota['FECHA_INICIO'] + 1, true);
            $SALDO_CAPITAL = $capital_arr['SALDO'];


            //calculamos saldo de capital
           // $capital_arr = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO'], true);

            $cuota['CAPITAL_CUOTA'] = $capital_arr['AMORTIZACION_CUOTA'];
            $cuota['SALDO_CAPITAL'] = $SALDO_CAPITAL;

            $int_punitorio = 0;
            $int_moratorio = 0;


            $cuota['INT_COMPENSATORIO'] = 0;
            $cuota['INT_COMPENSATORIO_IVA'] = 0;
            $cuota['INT_COMPENSATORIO_SUBSIDIO'] = 0;
            $cuota['INT_COMPENSATORIO_IVA_SUBSIDIO'] = 0;
            
            foreach ($segmentos as $segmento) {

                //se suman los intereses de los segmentos de la cuota y se actualiza en la cuota
                $cuota['INT_COMPENSATORIO'] += $segmento['INT_COMPENSATORIO'];
                $cuota['INT_COMPENSATORIO_IVA'] += $segmento['INT_COMPENSATORIO_IVA'];

                //la cuota ingresa en estado 5
                if ($segmento['ESTADO'] == PLAZO_SUBSIDIO_VENCIDO && $cuota['ESTADO'] != 1) {
                    $cuota['ESTADO'] = PLAZO_SUBSIDIO_VENCIDO;
                } else {
                    $cuota['ESTADO'] = $segmento['ESTADO'];
                }


                $cuota['INT_COMPENSATORIO_SUBSIDIO'] += $segmento['INT_COMPENSATORIO_SUBSIDIO'];
                $cuota['INT_COMPENSATORIO_IVA_SUBSIDIO'] += $segmento['INT_COMPENSATORIO_IVA_SUBSIDIO'];

                $int_moratorio += $segmento['INT_MORATORIO'];
                $int_punitorio += $segmento['INT_PUNITORIO'];

                $cuota['POR_INT_COMPENSATORIO'] = $segmento['POR_INT_COMPENSATORIO'];
            }

            $cuota['INT_MORATORIO'] = $int_moratorio;
            $cuota['INT_PUNITORIO'] = $int_punitorio;
        } else {

            $variacion = array();
            //ultima variacion de la cuota
            foreach ($this->_variaciones as $var_item) {
                if ($var_item['FECHA'] < $cuota['FECHA_VENCIMIENTO']) {
                    $variacion = $var_item;
                } else {
                    break;
                }
            }
            
            $capital_arr = $this->_get_saldo_capital($cuota['FECHA_INICIO'] + 1, true);
            $inicial = $capital_arr['INICIAL'];
            $desembolsos = $capital_arr['DESEMBOLSOS'];
            $SALDO_CAPITAL = $capital_arr['SALDO_TEORICO'];

            $capital_arr = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO'] + 1, true);
            $this->_cuotas[$cuota_id]['SALDO_CAPITAL'] = $capital_arr['BASE_CALCULO'];

            /*
             * Calculo del capital correspondiente para la cuota calculada verificando que no es una cueota
             * de interes
             * 
             */

            $variacion_inicial = reset($this->_variaciones);
            $CANTIDAD_CUOTAS_GRACIA = $variacion_inicial['CUOTAS_GRACIA'];
            $cuotas_capital = ( $cuota['CUOTAS_RESTANTES'] + $cantidad_cuotas_anteriores - $CANTIDAD_CUOTAS_GRACIA);

            //modificado, si hay 6 cuotas anteriores y 6 de gracia la cuota actual debe mantener el capital de cuota
            //ya que no estaria dentro de las cuotas de gracia
            if ($cantidad_cuotas_anteriores >= $CANTIDAD_CUOTAS_GRACIA) {

                $cuota['CAPITAL_CUOTA'] = ($desembolsos + $inicial) / ($cuotas_capital );
            } else {
                $cuota['CAPITAL_CUOTA'] = 0;
            }

            /* EN ESTE ELSE SE CALCULA LAS CUOTAS QUE NO TIENEN EVENTOS INTERMEDIOS
             * ES DECIR, CUOTAS QUE SE CALCULAN COMPLETAS
             * ESTAS CUOTAS SEGUN SUS VENCIMIENTOS PUEDEN SER ANTERIORES A LA FECHA DE CALCULO O POSTERIORES
             * SI SON ANTERIORES SE DEBE CALCULAR EL RANGO DE CANTIDAD DE DIAS ENTRE LA FECHA DE INICIO Y LA 
             * FECHA DE VENCIMIENTO,
             * SI SON POSTERIORES EL RANGO ES 0 YA QUE NO SE HAN DEVENGADO INTERESES
             * 
             * SI EL CREDITO ES DE TIPO MICROCREDITO LOS INTERESES SE DEVENGAN POR TODO EL MES EN CALCULO
             */
            $rango = 0;

            //se calcula completo si:
            //- la fecha de calculo es mayor a la fecha de la cuota
            //- El tipo de credito es microcredito y no se hace forzado de devengamiento a la fecha de calculo
            if (
                    ($fecha >= $cuota['FECHA_VENCIMIENTO']) ||
                    ($this->_tipo_credito == TIPO_MICROCREDITO && $this->_tipo_devengamiento != TIPO_DEVENGAMIENTO_DEVENGAR_A_FECHA ) ||
                    ($this->_tipo_credito == TIPO_MICROCREDITO && ($fecha < $cuota['FECHA_VENCIMIENTO'] && $fecha > $cuota['FECHA_INICIO']) ) ||
                    ($this->_tipo_devengamiento == TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO)
            ) {


                //fecha enviada para mostrar
                if ($this->_tipo_credito == TIPO_MICROCREDITO && ($fecha < $cuota['FECHA_VENCIMIENTO'] && $fecha > $cuota['FECHA_INICIO'])) {
                    $fecha_enviada = $cuota['FECHA_INICIO'];
                }
                $rango = ($cuota['FECHA_VENCIMIENTO'] - $cuota['FECHA_INICIO']) / 86400;
            }


            $blog = false;
            
            $desembolsos = $this->get_desembolsos_cuota($cuota['FECHA_INICIO'], $cuota['FECHA_VENCIMIENTO'], TRUE);
            
            $INT_SUBSIDIO = $variacion ? $variacion['POR_INT_SUBSIDIO'] : 0;
            
            $int_compensatorio = 0;
            $int_compensatorio_subsidio = 0;
            if ($desembolsos) {
                $saldo_desembolso = 0;
                //echo "cuota:".$cuota['ID']."<br />";
                
                for($jd=-1; $jd <= count($desembolsos); ++$jd) {
                    $rango =0;
                    
                    if (isset($desembolsos[$jd])){
                        if (isset($desembolsos[$jd+1])){
                            $rango = ($desembolsos[$jd + 1]['FECHA'] - $desembolsos[$jd]['FECHA']) / (24 * 60 * 60);
                        } else {
                            $rango = ($cuota['FECHA_VENCIMIENTO'] - $desembolsos[$jd]['FECHA']) / (24 * 60 * 60);
                        }
                        
                        $saldo_desembolso = $desembolsos[$jd]['SALDO'];
                    } elseif ($jd == -1) {
                        $rango = ($desembolsos[$jd + 1]['FECHA'] - $cuota['FECHA_INICIO']) / (24 * 60 * 60);
                        $saldo_desembolso = $cuota['SALDO_CAPITAL'];
                    }
                    
                    $int_compensatorio += $variacion ? $this->_calcular_interes($saldo_desembolso, $rango, $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA'], $blog) : 0;
                    $int_compensatorio_subsidio += $variacion ? $this->_calcular_interes($saldo_desembolso, $rango, $INT_SUBSIDIO, $variacion['PERIODICIDAD_TASA'], $blog) : 0;
                    /*
                    echo "r:$rango<br />";
                    echo "s:$saldo_desembolso<br />";
                    echo "i:$int_compensatorio<br />";*/
                }

            } else {
                $int_compensatorio = $variacion ? $this->_calcular_interes($SALDO_CAPITAL, $rango, $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA'], $blog) : 0;
                $int_compensatorio_subsidio = $variacion ? $this->_calcular_interes($SALDO_CAPITAL, $rango, $INT_SUBSIDIO, $variacion['PERIODICIDAD_TASA'], $blog) : 0;
                /*
                echo "S:$SALDO_CAPITAL<br />";
                echo "r:$rango<br />";
                echo "i:$int_compensatorio<br /><br />";*/
            }
            
            
            $cuota['INT_COMPENSATORIO_SUBSIDIO'] = $int_compensatorio_subsidio;
            $cuota['INT_COMPENSATORIO_IVA_SUBSIDIO'] = $variacion ? $int_compensatorio_subsidio * $this->_iva_operatoria : 0;
            $cuota['INT_COMPENSATORIO'] = $int_compensatorio;
            $cuota['INT_COMPENSATORIO_IVA'] = $variacion ? $int_compensatorio * $this->_iva_operatoria : 0;


            //calculo de tipo de intereses moratorios y punitorios
            //cambio 21112013 no importa si es microcredito o no, siempre se calculan int moratorios y punitorios
            if ($this->_tipo_credito == TIPO_MICROCREDITO && false) {
                $cuota['INT_MORATORIO'] = 0;
                $cuota['INT_PUNITORIO'] = 0;
            } else {


                if ($fecha >= $cuota['FECHA_VENCIMIENTO']) {

                    $pagos_all = $this->_get_pagos_tipo($fecha, true);
                    $pagos = $pagos_all[$cuota['CUOTAS_RESTANTES']];

                    $total_pagos = $pagos[PAGO_CAPITAL] + $pagos[PAGO_IVA_COMPENSATORIO] + $pagos[PAGO_COMPENSATORIO];
                    $rango = ($fecha - $cuota['FECHA_VENCIMIENTO'] ) / 86400;

                    $compensatorios_total = $cuota['INT_COMPENSATORIO'] - $cuota['INT_COMPENSATORIO_SUBSIDIO'];
                    $compensatorios_iva_total = $cuota['INT_COMPENSATORIO_IVA'] - $cuota['INT_COMPENSATORIO_IVA_SUBSIDIO'];


                    $SALDO_CAPITAL_CUOTA = $cuota['CAPITAL_CUOTA'] + $compensatorios_total + $compensatorios_iva_total - $total_pagos;


                    $cuota['INT_MORATORIO'] = $SALDO_CAPITAL_CUOTA * (1 + ($cuota['POR_INT_MORATORIO'] / 100) * $rango / $this->_interese_moratorio_plazo ) - $SALDO_CAPITAL_CUOTA;
                    $cuota['INT_PUNITORIO'] = $SALDO_CAPITAL_CUOTA * (1 + ($cuota['POR_INT_PUNITORIO'] / 100) * $rango / $this->_interese_punitorio_plazo ) - $SALDO_CAPITAL_CUOTA;
                } else {

                    //si la fecha de calculo es menor a la fecha de vencimiento, moratorios y punitorios van en 0
                    $cuota['INT_MORATORIO'] = 0;
                    $cuota['INT_PUNITORIO'] = 0;
                }
                $cuota['FECHA_ENVIADA'] = $fecha_enviada;
            }
        }

        $this->_cuotas[$cuota_id]['INT_COMPENSATORIO'] = $cuota['INT_COMPENSATORIO'];
        $this->_cuotas[$cuota_id]['INT_COMPENSATORIO_IVA'] = $cuota['INT_COMPENSATORIO_IVA'];
        

        $this->_cuotas[$cuota_id]['INT_COMPENSATORIO_SUBSIDIO'] = $cuota['INT_COMPENSATORIO_SUBSIDIO'];
        $this->_cuotas[$cuota_id]['INT_COMPENSATORIO_IVA_SUBSIDIO'] = $cuota['INT_COMPENSATORIO_IVA_SUBSIDIO'];

        $this->_cuotas[$cuota_id]['INT_PUNITORIO'] = $cuota['INT_PUNITORIO'];
        $this->_cuotas[$cuota_id]['INT_MORATORIO'] = $cuota['INT_MORATORIO'];
        $this->_cuotas[$cuota_id]['SALDO_CAPITAL'] = $cuota['SALDO_CAPITAL'];
        $this->_cuotas[$cuota_id]['CAPITAL_CUOTA'] = $cuota['CAPITAL_CUOTA'];

        $this->_cuotas[$cuota_id]['ESTADO'] = $cuota['ESTADO'] == 5 ? 5 : $this->_cuotas[$cuota_id]['ESTADO'];

        unset($cuota['f']);
        unset($cuota['fi']);
        unset($cuota['fv']);
        unset($cuota['TIPO']);
        unset($cuota['FECHA']);
        unset($cuota['CHILDREN']);
        
        if (isset($cuota['DIAS_MORAS'])) {
            unset($cuota['DIAS_MORAS']);
        }
        

        if ($this->_bsave) {
            $this->_db->update("fid_creditos_cuotas", $cuota, "ID = " . $cuota_id);
        }

        if ($this->_blog) {
            logthis("CUOTA_" . microtime(), $cuota);
        }


        return $cuota;
    }

    //funcion utilitaria que devuelve el interes compuesto segun los parametros enviados
    function _calcular_interes($monto, $dias, $interes = 10, $periodicidad = 60, $log = false) {

        if (!$periodicidad) {
            $periodicidad = $this->_periodicidad;
        }

        $dias = floor($dias);
        $interes = $interes / 100;
        //0.5 = 5 / 100;
        $base = 1 + ($interes * $periodicidad / $this->_interese_compensatorio_plazo);
        //  1.083 =   1 + ( 0.5 * 60 / 360);
        $exponente = $dias / $periodicidad;
        //0.3833 = 23 / 60

        $rtn = $monto * pow($base, $exponente) - $monto;
        


        return $rtn;
    }

    function eliminar_gasto($gasto_id) {
        $gasto = $this->_db->get_row("fid_creditos_gastos", "ID = " . $gasto_id);
        $this->_db->delete("fid_creditos_gastos", "ID = " . $gasto_id);
        return $gasto;
    }

    function eliminar_variacion($id_variacion) {
        $variacion = $this->_variaciones[$id_variacion];

        $bdel = false;
        switch ($variacion['TIPO']) {
            case EVENTO_AJUSTE:
            case EVENTO_DESEMBOLSO:
                //desembolso
                $bdel = true;
                $this->_db->delete("fid_creditos_desembolsos", "ID_VARIACION = " . $variacion['ID']);
                break;
            case EVENTO_TASA:
                //cambio tasa
                $bdel = true;
                $this->_db->delete("fid_creditos_cambiotasas", "ID_VARIACION = " . $id_variacion);
                break;
            case EVENTO_RECUPERO:
                //recupero
                //se deben quitar los estados en 1 de las cuotas afectadas a los pagos eliminados
                $variacion = $this->_variaciones[$id_variacion];

                foreach ($variacion['ASOC'] as $pago) {
                    $this->_db->update("fid_creditos_cuotas", array("ESTADO" => 0), "CUOTAS_RESTANTES = " . $pago['CUOTAS_RESTANTES']);
                }

                $this->_db->delete("fid_creditos_pagos", "ID_VARIACION = " . $id_variacion);
                $bdel = true;
                break;
        }

        if ($bdel) {
            $this->_rewrite_variaciones($id_variacion);
            $this->_db->delete("fid_creditos_eventos", "ID = " . $id_variacion);
        }

        return $variacion;
    }

    function _rewrite_variaciones($id = false) {

        if ($id) {

            $variacion_anterior = reset($this->_variaciones);
            foreach ($this->_variaciones as $var) {

                if ($var['ID'] == $id) {
                    break;
                } else {
                    $variacion_anterior = $var;
                }
            }

            $variacion = $this->_variaciones[$id];

            if ($variacion_anterior) {
                switch ($variacion['TIPO']) {
                    case EVENTO_AJUSTE:
                    case EVENTO_DESEMBOLSO:
                    case EVENTO_RECUPERO:

                        $resto = $variacion_anterior['CAPITAL'] - $variacion['CAPITAL'];
                        $this->_db->update("fid_creditos_eventos", array("CAPITAL" => "[CAPITAL + " . $resto . "]"), "FECHA > " . $variacion['FECHA']);
                        break;
                    case EVENTO_TASA:

                        foreach ($this->_variaciones as $var) {


                            if ($var['FECHA'] >= $variacion['FECHA']) {

                                //si se encuentra una variacion de tipo cambio de tasa se detiene las modificaciones
                                if ($var['TIPO'] == EVENTO_TASA)
                                    break;

                                //recorremos todos los eventos posteriores a la fecha de eliminacion y 
                                //se les modifica el interes compensatorio al valor anterior
                                $this->_variaciones[$var['ID']]['POR_INT_COMPENSATORIO'] = $variacion_anterior['POR_INT_COMPENSATORIO'];
                                $this->_variaciones[$var['ID']]['POR_INT_SUBSIDIO'] = $variacion_anterior['POR_INT_SUBSIDIO'];
                                $this->_variaciones[$var['ID']]['POR_INT_MORATORIO'] = $variacion_anterior['POR_INT_MORATORIO'];
                                $this->_variaciones[$var['ID']]['POR_INT_PUNITORIO'] = $variacion_anterior['POR_INT_PUNITORIO'];

                                $this->_db->update("fid_creditos_eventos", array(
                                    "POR_INT_COMPENSATORIO" => $variacion_anterior['POR_INT_COMPENSATORIO'],
                                    "POR_INT_SUBSIDIO" => $variacion_anterior['POR_INT_SUBSIDIO'],
                                    "POR_INT_MORATORIO" => $variacion_anterior['POR_INT_MORATORIO'],
                                    "POR_INT_PUNITORIO" => $variacion_anterior['POR_INT_PUNITORIO'],
                                        ), "ID > " . $var['ID']);
                            }
                        }

                        break;
                }
            } else {
                return false;
            }
        }
    }

    function _rewite_cuota($credito_id, $cuotas_restantes) {
        $this->_db->where("_ACTIVA = 1");
        $this->_db->where("_PARENT = 0");
        $this->_db->where("ID_CREDITO = " . $credito_id . " AND CUOTAS_RESTANTES = " . $cuotas_restantes);


        $ultima_cuota = $this->_db->get_row("fid_creditos_cuotas", "ID_CREDITO = " . $credito_id . " AND CUOTAS_RESTANTES = " . $cuotas_restantes);
    }

    function get_cuotas_restantes($fecha) {
        $this->_db->order_by("c.FECHA_INICIO", "desc");
        $this->_db->where("c.FECHA_INICIO <=   " . $fecha);
        $cuota = $this->get_row_cuotas();

        return $cuota ? $cuota['CUOTAS_RESTANTES'] : 0;
    }
    
    function get_cuotas_restantes_pago() {
        $this->_db->select('p.CUOTAS_RESTANTES');
        $this->_db->order_by("p.CUOTAS_RESTANTES", "asc");
        $this->_db->limit(0, 1);
        $pago = $this->get_row_pagos();
        
        if ($pago) {
            return $pago['CUOTAS_RESTANTES'];
        }
        
        $this->_db->select('c.CUOTAS_RESTANTES');
        $this->_db->order_by("c.FECHA_INICIO", "asc");
        $cuota = $this->get_row_cuotas();
        
        return $cuota ? $cuota['CUOTAS_RESTANTES'] : 0;
    }

    function add_single_cuota($fecha = false) {

        //se busca la fecha de vencimiento de la ultima cuota afectada
        //borramos las cuotas siguiente a la fecha dada
        $this->_db->delete("fid_creditos_cuotas", "_PARENT = 0 AND FECHA_INICIO > " . $fecha . " and ID_CREDITO = " . $this->_id_credito . " AND ID_VERSION = " . $this->_id_version);

        $ultima_cuota = array();
        foreach ($this->_cuotas as $cuota) {
            if ($cuota['FECHA_INICIO'] <= $fecha && $cuota['FECHA_VENCIMIENTO'] >= $fecha)
                $ultima_cuota = $cuota;
        }

        if ($ultima_cuota) {

            if ($ultima_cuota['CUOTAS_RESTANTES'] < 2)
                return;

            $variacion = reset($this->_variaciones);
            $cantidad_cuotas = $variacion['CANTIDAD_CUOTAS'];
            $cuotas_restantes = $ultima_cuota['CUOTAS_RESTANTES'];
            $cantidad_cuotas_iniciadas = $cantidad_cuotas - ($cantidad_cuotas - $cuotas_restantes) - 1;
            $arr_result = $this->_get_saldo_capital($ultima_cuota['FECHA_VENCIMIENTO'], true);
            $saldo_capital = $arr_result['BASE_CALCULO'];

            $periodicidad = $variacion['PERIODICIDAD'];
            $MES_VENCIMIENTO = (date("m", $ultima_cuota['FECHA_VENCIMIENTO']) );
            $DIA_INICIO = (date("d", $ultima_cuota['FECHA_VENCIMIENTO']) );
            $fecha_venvimiento = mktime(0, 0, 0, $MES_VENCIMIENTO + ($periodicidad * $cantidad_cuotas_iniciadas), $DIA_INICIO, date("Y", $ultima_cuota['FECHA_VENCIMIENTO']));

            $rango = ($fecha_venvimiento - $ultima_cuota['FECHA_VENCIMIENTO']) / 86400;

            $capital_cuota = ($arr_result['INICIAL'] + $arr_result['DESEMBOLSOS']) / $cuotas_restantes;
            $interes_compensatorio = $this->_calcular_interes($saldo_capital, $rango, $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA']);

            //----------------------------------------------------------------------------------
            $INT_SUBSIDIO = $variacion['POR_INT_SUBSIDIO'];
            $interes_subsidio = $this->_calcular_interes($saldo_capital, $rango, $INT_SUBSIDIO, $variacion['PERIODICIDAD_TASA'], false);

            $interes_compensatorio_subsidio = $interes_subsidio;

            //----------------------------------------------------------------------------------            
            //restamos una cuota sobre la ultima cuota

            $cuota = array(
                "ID_CREDITO" => $this->_id_credito,
                "SALDO_CAPITAL" => $saldo_capital,
                "CAPITAL_CUOTA" => $capital_cuota,
                "CUOTAS_RESTANTES" => $cuotas_restantes - 1,
                "POR_INT_COMPENSATORIO" => $variacion['POR_INT_COMPENSATORIO'],
                "POR_INT_MORATORIO" => $variacion['POR_INT_MORATORIO'],
                "POR_INT_PUNITORIO" => $variacion['POR_INT_PUNITORIO'],
                "INT_COMPENSATORIO" => $interes_compensatorio,
                "INT_COMPENSATORIO_IVA" => $interes_compensatorio * $this->_iva_operatoria,
                "INT_COMPENSATORIO_SUBSIDIO" => $interes_compensatorio_subsidio,
                "INT_COMPENSATORIO_IVA_SUBSIDIO" => $interes_compensatorio_subsidio * $this->_iva_operatoria,
                "INT_MORATORIO" => 0,
                "INT_PUNITORIO" => 0,
                "FECHA_INICIO" => $ultima_cuota['FECHA_VENCIMIENTO'],
                "FECHA_GENERADA" => $fecha,
                "FECHA_VENCIMIENTO" => $fecha_venvimiento,
                "ESTADO" => 0,
                "_PARENT" => 0,
                "_ACTIVA" => 1,
                "ID_VERSION" => $this->_id_version,
                "_ID_VARIACION" => $variacion['ID'],
            );

            $this->_db->insert("fid_creditos_cuotas", $cuota);
        }
    }

    //funcion utilitaria que devuelve un array con todos los datos de deuda y desembolsos a la fecha
    //especificada
    function _get_saldo_capital($fecha = false, $arr_result = false, $log = false) {

        $variaciones = $this->_variaciones;
        //variacion inicial
        $variacion_inicial_arr = array();
        foreach ($variaciones as $variacion) {
            if ($variacion['TIPO'] == EVENTO_INICIAL) {
                $variacion_inicial_arr = $variacion;
                break;
            }
        }
        
        $fecha = $fecha ? $fecha : NO_FECHA; //parche error


        //MODIFICADO 15-08-2013
        $capital_inicial = 0; //$variacion_inicial_arr['CAPITAL'];

        $CUOTAS_GRACIA = isset($variacion_inicial_arr['CUOTAS_GRACIA']) ? $variacion_inicial_arr['CUOTAS_GRACIA'] : 0;

        //obtenemos todas las cuotas que hayan iniciado
        $cuotas = array();
        foreach ($this->_cuotas as $cuota) {
            if ($cuota['FECHA_INICIO'] <= $fecha) {
                $cuotas[] = $cuota;
            }
        }


        //si no se encuentra el inicio de ninguna cuota se busca desde la primera cuota
        if (!$cuotas) {
            $cuotas[] = reset($this->_cuotas);
        }
        //OBTENEMOS CUOTAS RESTANTES POR VENCER
        $ultima_cuota = end($cuotas);

        //obtenemos todos los desembolsos
        $desembolsos = array();
        foreach ($variaciones as $variacion) {
            if (isset($variacion['ASOC']['MONTO']) && $variacion['TIPO'] == EVENTO_DESEMBOLSO || $variacion['TIPO'] == EVENTO_AJUSTE &&
                    $variacion['FECHA'] <= $cuotas[count($cuotas) - 1]['FECHA_VENCIMIENTO']) {

                $variacion['MONTO'] = $variacion['ASOC']['MONTO'];
                $desembolsos[] = $variacion;
            }
        }
        
        //Si el capital inicial esta definido mayor a 0 (es decir todavia no se realiza ningun desembolso), se toma el capital inicial como unico
        //desembolso, para poder continuar los calculos normalmente en simulacion, de lo contrario se ignora.
        if ($capital_inicial > 0) {
            $desembolsos[] = array("FECHA" => $variacion_inicial_arr['FECHA'], "ASOC" => array("MONTO" => $capital_inicial));
        }

        $AMORTIZACION_TEORICA_ACUMULADA = 0;

        //RECORREMOS CADA UNA DE LAS CUOTAS
        $audi = "";

        $init = 0;
        $AMORTIZACION_CUOTA = 0;
        $DESEMBOLSOS_ACUMULADOS = 0;
        $AMORTIZACION_CUOTA_ACTUAL = 0;
        $SALDO_TEORICO = 0;
        

        for ($c = 0; $c < count($cuotas); $c++) {

            $cuotas[$c]['AMORTIZACION_CUOTA'] = 0;
            $cuotas[$c]['DESEMBOLSOS'] = 0;

            //buscamos desemmbolsos
            for ($d = $init; $d < count($desembolsos); $d++) {

                //anteriores dentro de la fecha la cuota
                if (isset($desembolsos[$d]['ASOC']['MONTO']) && $desembolsos[$d]['FECHA'] <= $cuotas[$c]['FECHA_VENCIMIENTO'] &&
                        $desembolsos[$d]['FECHA'] <= $fecha) {




                    $cuotas[$c]['DESEMBOLSOS'] += $desembolsos[$d]['ASOC']['MONTO'];
                    $desembolsos[$d]['ASOC']['MONTO'] = 0;
                    $init = $d;
                }
            }
            $audi .= "<br/>DESEMBOLSOS:" . $cuotas[$c]['DESEMBOLSOS'];

            $DESEMBOLSOS_ACUMULADOS += $cuotas[$c]['DESEMBOLSOS'];
            if ($c >= $CUOTAS_GRACIA) {
                $audi .= "<br/>CUOTAS GRACIA: NO";
                $divisor = $cuotas[$c]['CUOTAS_RESTANTES'];
                $AMORTIZACION_CUOTA = ( $DESEMBOLSOS_ACUMULADOS - $AMORTIZACION_TEORICA_ACUMULADA) / $divisor;

                $audi .= "<br/>DIVISOR: " . $divisor;
                $audi .= "<br/>AMORTIZACION CUOTA: " . $AMORTIZACION_CUOTA;
                $audi .= "<br/>AMORTIZACION ACUMULADA VENCIDA: " . $AMORTIZACION_TEORICA_ACUMULADA;
                $audi .= "<br/>DESEMBOLSOS ACUMULADOS: " . $DESEMBOLSOS_ACUMULADOS;
                $cuotas[$c]['AMORTIZACION_CUOTA'] = $AMORTIZACION_CUOTA; //( ($DESEMBOLSOS_ACUMULADOS - $AMORTIZACION_TEORICA_ACUMULADA) / $divisor) ;
            } else {
                $AMORTIZACION_CUOTA = 0;
            }
            
            $ultima_cuota = $cuotas[$c];
            $AMORTIZACION_CUOTA_ACTUAL = $cuotas[$c]['AMORTIZACION_CUOTA'];


            $SALDO_TEORICO = $DESEMBOLSOS_ACUMULADOS - $AMORTIZACION_TEORICA_ACUMULADA;
            $AMORTIZACION_TEORICA_ACUMULADA += $AMORTIZACION_CUOTA;
            $audi .= "<br/>SALDO TEORICO: " . $DESEMBOLSOS_ACUMULADOS;
            $audi .= "<br/><br/>";
        }



        //la ultima cuota de las seleccionadas es la cuota de la fecha actual
        $cuota = $ultima_cuota;

        $AMORTIZACION_CUOTA_ACTUAL = $cuota['E_AMORTIZACION'] == 0 ? $AMORTIZACION_CUOTA_ACTUAL : $cuota['E_AMORTIZACION'];

        //DESEMBOLSO A LA FECHA
        $total_desembolso_real = 0;
        foreach ($variaciones as $variacion) {
            if (isset($variacion['ASOC']['MONTO']) && $variacion['TIPO'] == EVENTO_DESEMBOLSO || $variacion['TIPO'] == EVENTO_AJUSTE) {
                if ($variacion['FECHA'] <= $fecha) {
                    $total_desembolso_real += $variacion['ASOC']['MONTO'];
                }
            }
        }
        
        
        //DESEMBOLSO AL FINAL DE LA CUOTA

        $total_desembolso_teorico = 0;
        foreach ($variaciones as $variacion) {
            if (isset($variacion['ASOC']['MONTO']) && $variacion['TIPO'] == EVENTO_DESEMBOLSO || $variacion['TIPO'] == EVENTO_AJUSTE) {
                if ($variacion['FECHA'] <= $cuota['FECHA_VENCIMIENTO']) {
                    $total_desembolso_teorico += $variacion['ASOC']['MONTO'];
                }
            }
        }


        $recupero = $this->_get_pagos_tipo($fecha, $log);
        if ($log) {
            
        }
        $total_pagos = $recupero['TOTAL'][PAGO_CAPITAL];

        $SALDO_CAPITAL = $total_desembolso_real - $total_pagos;
        $SALDO_TEORICO_PAGO = $SALDO_TEORICO - $total_pagos;
        $SALDO = $SALDO_TEORICO_PAGO < $SALDO_CAPITAL ? $SALDO_TEORICO : $SALDO_CAPITAL;

        if (!$arr_result) {
            return $SALDO_CAPITAL;
        } else {
            $rtn = array(
                "DESEMBOLSOS_CUOTA" => $total_desembolso_teorico,
                "DESEMBOLSOS" => $total_desembolso_real,
                "INICIAL" => $capital_inicial,
                "PAGOS" => $total_pagos,
                "AMORTIZACION_CUOTA" => $AMORTIZACION_CUOTA_ACTUAL,
                "SALDO" => $SALDO,
                "BASE_CALCULO" => $SALDO_TEORICO < $SALDO_CAPITAL ? $SALDO_TEORICO : $SALDO_CAPITAL,
                "SALDO_REAL" => $SALDO_CAPITAL,
                "SALDO_TEORICO" => $SALDO_TEORICO,
                "TIPO" => $SALDO_TEORICO < $SALDO_CAPITAL ? "T" : "R",
                "FECHA" => date("d/m/Y", $fecha)
            );
            if ($log) {
                
            }
            return $rtn;
        }
    }

    //cambia la fecha de vencimiento o de inicio de la cuotas segun los parametros dados
    //el parametro que se pasa en false se ignora
    function modificar_fecha_cuota($cuotas_restantes = 0, $fecha_inicio = false, $fecha_vencimiento = false) {

        //obtenemos cantidad de cuotas
        foreach ($this->_cuotas as $cuota) {
            $cuotas_credito[] = $cuota;
        }


        $MODIF = array();

        //FECHA INICIO CUOTA
        if ($fecha_inicio) {
            list($d, $m, $y) = explode("/", date("d/m/Y", $fecha_inicio));

            $MODIF['FECHA_INICIO'] = mktime(0, 0, 0, $m, $d, $y);
            $fecha_vencimiento_anterior = $MODIF['FECHA_INICIO'];

            //si existen cuotas anteriores se modifica la fecha de vencimiento
            if ($cuotas_credito[0]['CUOTAS_RESTANTES'] > $cuotas_restantes) {

                //verificamos version de la cuota, si no existe la cuota para la version utilizada se hace una copia de la cuota a la version actual.
                $cuota_anterior = array();
                foreach ($cuotas_credito as $cuota_credito) {
                    if (($cuota_credito['CUOTAS_RESTANTES']) == ($cuotas_restantes + 1)) {
                        $cuota_anterior = $cuota_credito;
                        break;
                    }
                }
                $cuota_anterior_id = $cuota_anterior['ID'];

                if ($this->_id_version != $cuota_anterior['ID_VERSION']) {
                    unset($cuota_anterior['ID']);
                    $cuota_anterior['ID_VERSION'] = $this->_id_version;
                    $cuota_anterior_id = $this->_db->insert("fid_creditos_cuotas", $cuota_anterior);
                }

                $this->_db->update("fid_creditos_cuotas", array("FECHA_VENCIMIENTO" => $fecha_vencimiento_anterior), "ID = " . $cuota_anterior_id);
            } else {
                //   $this->_db->update("fid_creditos_eventos", array("FECHA_INICIO" => $fecha_inicio, "FECHA" => $fecha_inicio), "TIPO = 0");
            }
        }

        //FECHA VENCIMIENTO CUOTA        
        if ($fecha_vencimiento) {
            list($d, $m, $y) = explode("/", date("d/m/Y", $fecha_vencimiento));

            $fecha_vencimiento_tmp = mktime(0, 0, 0, $m, $d, $y);
            $fecha_inicio_seguiente = $fecha_vencimiento_tmp;

            //verificamos version de la cuota, si no existe la cuota para la version utilizada se hace una copia de la cuota a la version actual.
            $cuota_siguiente = array();
            foreach ($cuotas_credito as $cuota_credito) {
                if (($cuota_credito['CUOTAS_RESTANTES']) == ($cuotas_restantes - 1)) {
                    $cuota_siguiente = $cuota_credito;
                    break;
                }
            }


            if ($cuota_siguiente) {
                $cuota_siguiente_id = $cuota_siguiente['ID'];
                if ($this->_id_version != $cuota_siguiente['ID_VERSION']) {
                    unset($cuota_siguiente['ID']);
                    $cuota_siguiente['ID_VERSION'] = $this->_id_version;
                    $cuota_siguiente_id = $this->_db->insert("fid_creditos_cuotas", $cuota_siguiente);
                }
                $this->_db->update("fid_creditos_cuotas", array("FECHA_INICIO" => $fecha_inicio_seguiente), "ID = " . $cuota_siguiente_id);
            }
            $MODIF['FECHA_VENCIMIENTO'] = $fecha_vencimiento;
        }

        //verificamos version de la cuota, si no existe la cuota para la version utilizada se hace una copia de la cuota a la version actual.
        $cuota_actual = array();
        foreach ($cuotas_credito as $cuota_credito) {
            if (($cuota_credito['CUOTAS_RESTANTES']) == $cuotas_restantes) {
                $cuota_actual = $cuota_credito;
                break;
            }
        }
        $cuota_actual_id = $cuota_actual['ID'];

        if ($this->_id_version != $cuota_actual['ID_VERSION']) {
            unset($cuota_actual['ID']);
            $cuota_actual['ID_VERSION'] = $this->_id_version;
            $cuota_actual_id = $this->_db->insert("fid_creditos_cuotas", $cuota_actual);
        }
        $this->_db->update("fid_creditos_cuotas", $MODIF, "ID = " . $cuota_actual_id);
    }

    function get_cuota($cuotas_restantes) {

        foreach ($this->_cuotas as $cuota) {
            if ($cuota['CUOTAS_RESTANTES'] == $cuotas_restantes) {
                return $cuota;
            }
        }

        return array();
    }

    function get_estado_credito() {
        return $this->_estado_credito;
    }

    //ultima modificacion, se plancha la cuota correspondiente a la fecha dada
    function enviar_cuota() {

        $fecha = $this->_fecha_actual;
        $cuota_enviar = array();
        foreach ($this->_cuotas as $cuota) {
            if ($fecha > $cuota['FECHA_INICIO']) {
                $cuota_enviar = $cuota;
            } else {
                break;
            }
        }
        $this->_db->update("fid_creditos_cuotas", array("FECHA_ENVIADA" => $fecha), "ID = " . $cuota_enviar['ID']);
    }

    //si encuentra una cuota con fecha de planchado en la fecha especificada modifica la cuota y le saca
    //el planchado
    //retorna fecha de planchado si existe o 0
    function modificar_planchado($fecha = NO_FECHA) {

        foreach ($this->_cuotas as $cuota) {
            if ($cuota['FECHA_INICIO'] <= $fecha && $fecha <= $cuota['FECHA_VENCIMIENTO']) {
                if ($cuota['FECHA_ENVIADA'] > 0) {
                    $this->_db->update("fid_creditos_cuotas", array("FECHA_ENVIADA" => 0), "ID = " . $cuota['ID']);
                    $this->_cuotas[$cuota['ID']]['FECHA_ENVIADA'] = 0;
                    return $cuota['FECHA_ENVIADA'];
                    break;
                }
            }
        }
        return 0;
    }

    function elimina_eventos_temporales() {
        $credito_id = $this->_id_credito;
        $this->_db->delete("fid_creditos_eventos", "ID_CREDITO = " . $credito_id . " AND TIPO = " . EVENTO_INFORME);
    }

    function adelantar_pagos($fecha, $id_variacion = TEMP_ID) {

        $TOTAL = $this->_pagos['TOTAL'][PAGO_ADELANTADO];

        $cuotas = array();
        foreach ($this->_cuotas as $cuota) {
            if ($cuota['FECHA_INICIO'] > $fecha) {
                $cuotas[] = $cuota;
            }
        }

        $arr_pago = array();
        $total_pago = 0;
        foreach ($cuotas as $cuota) {

            $TIPO = PAGO_ADELANTADO;
            if ($TOTAL > 0) {
                $TIPO = 7;
                $capital_arr = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO'] - 1, false);
                $SALDO = $capital_arr['AMORTIZACION_CUOTA'];
                $monto = $SALDO > $TOTAL ? $TOTAL : $SALDO;
            } else {
                $monto = $TOTAL;
            }
            if ($monto > 0) {
                $arr_pago[] = array(
                    "ID_CREDITO" => $this->_id_credito,
                    "FECHA" => $fecha,
                    "ID_TIPO" => $TIPO,
                    "MONTO" => $monto,
                    "CUOTAS_RESTANTES" => $cuota['CUOTAS_RESTANTES'],
                    "ID_VARIACION" => $id_variacion,
                );
                $total_pago += $monto;
                $TOTAL -= $monto;
            }
        }

        //$this->_db->delete("fid_creditos_pagos", "ID_CREDITO = " . $this->_id_credito . " AND ID_TIPO = " . PAGO_ADELANTADO);
        foreach ($arr_pago as $pago) {
            $this->_db->insert("fid_creditos_pagos", $pago);
        }
        return $total_pago;
    }

    function cancelar_pagos_subsidiados($cuota_id, $cuota = array()) {
        if ($cuota) {
            
        } else {

            $this->_db->where("ID = " . $cuota_id);
            $cuota = $this->_db->get_row("fid_creditos_cuotas");
        }

        //SI YA EXISTE CANCELACION DE PAGOS
        $this->_db->where("p.ID_CREDITO = " . $cuota['ID_CREDITO']);
        $this->_db->where("p.cuotas_restantes  = " . $cuota['CUOTAS_RESTANTES']);
        $this->_db->where("p.VENCIDO = 1");
        $pagos = $this->_db->get_tabla("fid_creditos_pagos p");

        if ($pagos) {
            return false;
        }

        $this->_db->select("MONTO, cv.FECHA, p.CUOTAS_RESTANTES, ID_TIPO, ID_VARIACION, cv.ID_CREDITO");
        $this->_db->where("cv.TIPO = " . EVENTO_RECUPERO);
        $this->_db->where("p.ID_CREDITO = " . $cuota['ID_CREDITO']);
        $this->_db->where("p.cuotas_restantes  = " . $cuota['CUOTAS_RESTANTES']);
        $this->_db->join("fid_creditos_eventos cv", "cv.ID = p.ID_VARIACION");
        $pagos = $this->_db->get_tabla("fid_creditos_pagos p");


        $arr_insert = array();
        foreach ($pagos as $pago) {
            $arr_insert[] = array(
                "MONTO" => $pago['MONTO'] * -1,
                "FECHA" => $pago['FECHA'],
                "ID_TIPO" => $pago['ID_TIPO'],
                "CUOTAS_RESTANTES" => $cuota['CUOTAS_RESTANTES'],
                "ID_CREDITO" => $cuota['ID_CREDITO'],
                "ID_VARIACION" => $pago['ID_VARIACION'],
                "VENCIDO" => 1
            );
        }

        $this->_db->insert("fid_creditos_pagos", $arr_insert, array("ID"));
    }

    function get_desembolsos_teoricos() {
        $this->renew_datos();
        $desembolsos_teoricos = array();
        foreach ($this->_variaciones as $variacion) {
            if ($variacion['TIPO'] == 1 && $variacion['ESTADO'] == 5) {
                $desembolsos_teoricos[] = $variacion;
            }
        }
        return $desembolsos_teoricos;
    }

    function eliminar_desembolsos_teoricos($desembolsos_teoricos = array()) {
        if ($desembolsos_teoricos) {
            
        } else {
            $desembolsos_teoricos = $this->get_desembolsos_teoricos();
        }

        foreach ($desembolsos_teoricos as $dt) {
            if ($dt['ASOC']['MONTO'] > 0.01) {
                $this->_db->delete("fid_creditos_eventos", "ID = " . $dt['ID']);
                $this->_db->delete("fid_creditos_desembolsos", "ID_VARIACION= " . $dt['ID']);
            } else {
                $this->_db->update("fid_creditos_eventos", array("ESTADO" => 0), "ID = " . $dt['ID']);
            }
        }
    }

    function get_tabla_variaciones($where = false, $id_version = false) {
        if ($where) {
            $this->_db->where($where);
        }

        if (!$id_version) {
            $id_version = $this->_id_version;
        }

        $this->_db->where("cv.ID_CREDITO = " . $this->_id_credito);
        $this->_db->where("cv.ID_VERSION in ( " . implode(",", $this->_anc_version) . "," . $id_version . ")");


        $ultimo = end($this->_anc_version_array);
        $or_where = array();

        for ($i = 0; $i < count($this->_anc_version_array); $i++) {
            $version = $this->_anc_version_array[$i]['ID_VERSION'];
            $fecha = $this->_anc_version_array[$i]['FECHA_VERSION'];

            //ultimo elemento
            if ($ultimo['ID_VERSION'] == $this->_anc_version_array[$i]['ID_VERSION']) {
                $or_where[] = "(cv.ID_VERSION = " . $version . " AND cv.FECHA >= " . $fecha . ")";
            } else {

                $fecha2 = $this->_anc_version_array[$i + 1]['FECHA_VERSION'];

                if ($fecha == $ultimo['FECHA_VERSION']) {
                    $or_where[] = "((cv.ID_VERSION = " . $version . "))";
                } else {
                    $or_where[] = "((cv.ID_VERSION = " . $version . " AND cv.FECHA >= " . $fecha . " AND cv.FECHA < " . $ultimo['FECHA_VERSION'] . ") )";
                }
                
            }
        }
        $this->_db->where(" (" . implode(" OR ", $or_where) . ") ");


        $variaciones = $this->_db->get_tabla("fid_creditos_eventos cv");



        return $variaciones;
    }

    function get_tabla_cuotas($where = false, $id_version = false) {

        if ($where) {
            $this->_db->where($where);
        }

        if (!$id_version) {
            $id_version = $this->_id_version;
        }
        $this->_db->where("c.ID_CREDITO = " . $this->_id_credito);
        $this->_db->where("c.ID_VERSION in ( " . implode(",", $this->_anc_version) . "," . $id_version . ")");

        $ultimo = end($this->_anc_version_array);
        $or_where = array();
        for ($i = 0; $i < count($this->_anc_version_array); $i++) {
            $version = $this->_anc_version_array[$i]['ID_VERSION'];
            $fecha = $this->_anc_version_array[$i]['FECHA_VERSION'];

            //ultimo elemento
            if ($ultimo['ID_VERSION'] == $this->_anc_version_array[$i]['ID_VERSION']) {
                $or_where[] = "(c.ID_VERSION = " . $version . " AND cv2.FECHA >= " . $fecha . ")";
            } else {
                $fecha2 = $this->_anc_version_array[$i + 1]['FECHA_VERSION'];
                $or_where[] = "(c.ID_VERSION = " . $version . " AND cv2.FECHA >= " . $fecha . " AND cv2.FECHA < " . $ultimo['FECHA_VERSION'] . ")";
            }
        }
        $this->_db->where(implode(" OR ", $or_where));
        $this->_db->join("fid_creditos_eventos cv2", "cv2.ID = c._ID_VARIACION AND cv2.ESTADO >= 0");
        $cuotas = $this->_db->get_tabla("fid_creditos_cuotas c");

        return $cuotas;
    }

    function get_tabla_pagos($where = false, $id_version = false) {
        if ($where) {
            $this->_db->where($where);
        }

        if (!$id_version) {
            $id_version = $this->_id_version;
        }


        $this->_db->where("cv.ID_CREDITO = " . $this->_id_credito);
        $this->_db->where("cv.ID_VERSION <= " . $id_version);

        //funcion de seleccion de versiones
        $ultimo = end($this->_anc_version_array);
        $or_where = array();
        for ($i = 0; $i < count($this->_anc_version_array); $i++) {
            $version = $this->_anc_version_array[$i]['ID_VERSION'];
            $fecha = $this->_anc_version_array[$i]['FECHA_VERSION'];

            //ultimo elemento
            if ($ultimo['ID_VERSION'] == $this->_anc_version_array[$i]['ID_VERSION']) {
                $or_where[] = "(cv.ID_VERSION = " . $version . " AND cv.FECHA >= " . $fecha . ")";
            } else {
                $fecha2 = $this->_anc_version_array[$i + 1]['FECHA_VERSION'];
                $or_where[] = "(cv.ID_VERSION = " . $version . " AND cv.FECHA >= " . $fecha . " AND cv.FECHA < " . $ultimo['FECHA_VERSION'] . ")";
            }
        }
        $this->_db->where(implode(" OR ", $or_where));

        $this->_db->join("fid_creditos_pagos p", "p.ID_VARIACION = cv.ID");
        $pagos = $this->_db->get_tabla("fid_creditos_eventos cv");
        return $pagos;
    }

    function get_tabla_gastos($where = false, $id_version = false) {
        if ($where) {
            $this->_db->where($where);
        }

        if (!$id_version) {
            $id_version = $this->_id_version;
        }

        $this->_db->where("g.ID_CREDITO = " . $this->_id_credito);
        $this->_db->where("g.ID_VERSION <= " . $id_version);

        //funcion de seleccion de versiones
        $WHERE_FUNC = ' g.ID_VERSION  = (SELECT max( ID_VERSION ) FROM fid_creditos_gastos g2 WHERE g2.ID_CREDITO = ' . $this->_id_credito . ') ';
        $this->_db->where($WHERE_FUNC);
        $pagos = $this->_db->get_tabla("fid_creditos_gastos g");
        return $pagos;
    }

    function get_row_variaciones($where = false, $id_credito = false, $id_version = false) {
        $variaciones = $this->get_tabla_variaciones($where, $id_credito, $id_version);
        return $variaciones ? $variaciones[0] : array();
    }

    function get_row_cuotas($where = false, $id_credito = false, $id_version = false) {
        $cuotas = $this->get_tabla_cuotas($where, $id_credito, $id_version);
        return $cuotas ? $cuotas[0] : array();
    }

    function get_row_pagos($where = false, $id_credito = false, $id_version = false) {
        $pagos = $this->get_tabla_pagos($where, $id_credito, $id_version);
        return $pagos ? $pagos[0] : array();
    }

    function existCredito($id_credito) {
        $rtn = $this->_db->get_row("fid_creditos_version", "ID_CREDITO_VERSION = " . $id_credito, 'FECHA_VERSION DESC');
        return $rtn;
    }

    function set_credito_active($id_credito) {
        $this->_id_credito = $id_credito;

        $this->_cuotas = array();
        $this->_variaciones = array();

        $row_credito = $this->_db->get_row("fid_creditos", "ID = " . $id_credito);
        if ($row_credito) {
            $this->_credito = $row_credito;
            $this->_iva_operatoria = $row_credito['IVA'] / 100;
            $this->_interese_compensatorio_plazo = $row_credito['PLAZO_COMPENSATORIO'];
            $this->_interese_moratorio_plazo = $row_credito['PLAZO_MORATORIO'];
            $this->_interese_punitorio_plazo = $row_credito['PLAZO_PUNITORIO'];
            $this->_total_credito = $row_credito['MONTO_CREDITO'];
            $this->_estado_credito = $row_credito['CREDITO_ESTADO'];
            $this->_tipo_credito = $row_credito['TIPO_CREDITO'];
            $this->_caducado_de = $row_credito['ID_CADUCADO'];
            $this->_prorroga_de = $row_credito['ID_PRORROGA'];
            $this->_actualizacion_compensatorios = $row_credito['ACT_COMP'];
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //SELECCION la version indicicada, si no se envia version, se selecciona la version activa
    //si no hay version activa, se selecciona la ultima version y se la activa
    //obtiene toda la candea ancestor de la version dada
    function set_version_active($id_version = false) {

        if (!$id_version) {
            $this->_db->where("ID_CREDITO_VERSION = " . $this->_id_credito);
            $this->_db->where("ACTIVA = 1");
            $version = $this->_db->get_row("fid_creditos_version");
            if (!$version) {
                $this->_db->where("ID_CREDITO_VERSION = " . $this->_id_credito);
                $this->_db->order_by("FECHA_VERSION", "desc");
                $version = $this->_db->get_row("fid_creditos_version");

                if ($version){
                    $this->_db->update("fid_creditos_version", array("ACTIVA" => 1), "ID_VERSION = " . $version['ID_VERSION']);
                }
                else{
        //ETAPA 1
                    $version = array(
                        "FECHA_VERSION" => time(),
                        "ID_CREDITO_VERSION" => $this->_id_credito,
                        "TIPO_VERSION" => 1,
                        "DESCRIPCION_VERSION" => "Inicial",
                        "PARENT_ID" => 0,
                        "ACTIVA" => 1,
                        "UPDATE_TIME" => time()
                    );
                    $id_version = $this->_db->insert("fid_creditos_version", $version);
                    $this->_id_version = $id_version;
                    
                    $this->_db->where("ID_CREDITO_VERSION = " . $this->_id_credito);
                    $this->_db->where("ACTIVA = 1");
                    $version = $this->_db->get_row("fid_creditos_version");
                }
                
            }
            $id_version = $version['ID_VERSION'];
        } else {
            $this->_db->where("ID_VERSION = " . $id_version);
            $version = $this->_db->get_row("fid_creditos_version");
        }


        $this->_version = $version;
        $this->_id_version = $id_version;
        

        //obtiene toda la candea ancestor de la version dada
        //obtiene encadenado todas las versiones
        $arr_version = $this->_db->query('SELECT T2.ID_VERSION, T2.FECHA_VERSION
FROM (
    SELECT
        @r AS _id,
        (SELECT @r := PARENT_ID FROM fid_creditos_version WHERE ID_VERSION = _id) AS PARENT_ID,
        @l := @l + 1 AS lvl
    FROM
        (SELECT @r := ' . $id_version . ', @l := 0) vars,
        fid_creditos_version h
    WHERE @r <> 0) T1
JOIN fid_creditos_version T2
ON T1._id = T2.ID_VERSION
ORDER BY T1.lvl DESC');

        $arr_lista = array();
        foreach ($arr_version as $ver) {
            $arr_lista[] = $ver['ID_VERSION'];
        }

        $this->_anc_version_array = $arr_version;
        $this->_anc_version = $arr_lista;

        return $id_version;
    }

    //3 etapas: 
    //-se genera nuevo registro de version, 
    //-se duplican todas las variaciones posterioes a la fecha de variacion
    //los recuperos se generan nuevamente con un solo pago indefinido
    //retorna version y pagos
    function agregar_version($fecha = false, $tipo = 1, $descripcion = "") {
        $fecha = $fecha ? $fecha : $this->_fecha_actual;

        //ETAPA 1
        $id_version = $this->_db->insert("fid_creditos_version", array(
            "FECHA_VERSION" => $fecha,
            "ID_CREDITO_VERSION" => $this->_id_credito,
            "TIPO_VERSION" => $tipo,
            "DESCRIPCION_VERSION" => $descripcion,
            "PARENT_ID" => $this->_id_version,
            "UPDATE_TIME" => time()
        ));
        $this->_id_version = $id_version;


        //ETAPA 2
        $variaciones = $this->_variaciones;
        $cuotas = $this->_cuotas;

        $pagos = array();
        foreach ($variaciones as $variacion) {
            if ($variacion['FECHA'] >= $fecha) {
                $asoc = array();
                if ($variacion['TIPO'] != EVENTO_INICIAL) {
                    $asoc = $variacion['ASOC'];
                }

                unset($variacion['ASOC']);
                $id_variacion = $variacion['ID'];
                unset($variacion['ID']);

                $variacion['ID_VERSION'] = $id_version;



                if ($variacion['TIPO'] != EVENTO_INICIAL) {
                    //los pagos no se guaran en esta etapa
                    //if ($variacion['TIPO'] != EVENTO_RECUPERO && $variacion['TIPO'] != EVENTO_INICIAL) {
                    $id = $this->_db->insert("fid_creditos_eventos", $variacion);
                    $asoc['ID_VARIACION'] = $id;
                }

                switch ($variaciones[$id_variacion]['TIPO']) {
                    case EVENTO_DESEMBOLSO:
                        unset($asoc['ID']);
                        $this->_db->insert("fid_creditos_desembolsos", $asoc);
                        break;
                    case EVENTO_TASA:
                        unset($asoc['ID']);
                        $this->_db->insert("fid_creditos_cambiotasas", $asoc);
                        break;
                    case EVENTO_RECUPERO:

                        $total = 0;
                        foreach ($asoc as $pago) {
                            $total += $pago['MONTO'];
                        }

                        $this->_db->insert("fid_creditos_pagos", array(
                            "ID_CREDITO" => $this->_id_credito,
                            "FECHA" => $variacion['FECHA'],
                            "ID_TIPO" => 99,
                            "MONTO" => $total,
                            "CUOTAS_RESTANTES" => 0,
                            "ID_VARIACION" => $id
                        ));

                        if ($total > 0) {
                            $pagos[] = array("fecha" => $variacion['FECHA'], "monto" => $total);
                        }
                        break;
                }
            }
        }

        foreach ($cuotas as $cuota) {
            if ($cuota['FECHA_VENCIMIENTO'] >= $fecha) {
                unset($cuota['fi']);
                unset($cuota['fv']);
                unset($cuota['f']);
                unset($cuota['FECHA']);
                unset($cuota['TIPO']);
                unset($cuota['ID']);
                unset($cuota['CHILDREN']);

                $cuota['ID_VERSION'] = $id_version;

                $this->_db->insert("fid_creditos_cuotas", $cuota);
            }
        }

        $rtn = array(
            "VERSION" => $id_version,
            "PAGOS" => $pagos
        );
        return $rtn;
    }

    //builder de cuotas y variaciones, obtiene las cutoas, eventos y pagos segun la version seleccionada 
    //y sus ancestors
    function renew_datos($fecha = NO_FECHA) {

        if ($this->_bsave) {
            $this->_cuotas = $this->_to_array_cuotas();
            $this->_variaciones = $this->_to_array_variaciones();
            $variacion_inicial = reset($this->_variaciones);
            $this->_periodicidad = $variacion_inicial['PERIODICIDAD_TASA'];
        }
        $this->_pagos = $this->_get_pagos_tipo($fecha);
    }

    //funcion complementaria de renew datos
    function _to_array_variaciones() {
        $this->_db->reset();
        $this->_db->set_key("ID");
        $this->_db->order_by("FECHA, cv.ID");
        $this->_db->where("cv.ESTADO >= 0");
        $variaciones = $this->get_tabla_variaciones();
        

        foreach ($variaciones as $variacion) {
            switch ($variaciones[$variacion['ID']]['TIPO']) {
                case EVENTO_AJUSTE:
                    $this->_db->where("ID_VARIACION = " . $variacion['ID']);
                    $row = $this->_db->get_row("fid_creditos_desembolsos");
                    $variaciones[$variacion['ID']]['ASOC'] = $row;
                    break;
                case EVENTO_DESEMBOLSO:
                    $this->_db->where("ID_VARIACION = " . $variacion['ID']);
                    $row = $this->_db->get_row("fid_creditos_desembolsos");
                    $variaciones[$variacion['ID']]['ASOC'] = $row;
                    break;
                case EVENTO_TASA:
                    $this->_db->where("ID_VARIACION = " . $variacion['ID']);
                    $row = $this->_db->get_row("fid_creditos_cambiotasas");
                    $variaciones[$variacion['ID']]['ASOC'] = $row;
                    break;

                case EVENTO_RECUPERO:
                    $this->_db->where("ID_VARIACION = " . $variacion['ID']);
                    $row = $this->_db->get_tabla("fid_creditos_pagos");
                    $variaciones[$variacion['ID']]['ASOC'] = $row;
                    break;
            }
        }

        $this->_db->where("ID_VARIACION = " . TEMP_ID);
        $this->_db->where("ID_CREDITO = " . $this->_id_credito);
        $pagos = $this->_db->get_tabla("fid_creditos_pagos");
        if ($pagos) {
            $variaciones[TEMP_ID] = array(
                "ID" => TEMP_ID,
                "FECHA" => $pagos[0]['FECHA'],
                "ID_CREDITO" => $this->_id_credito,
                "TIPO" => 3,
                "ESTADO" => 0
                );
            $variaciones[TEMP_ID]['ASOC'] = $pagos;
        }

        return $variaciones;
    }

    //funcion complementaria de renew datos
    function _to_array_cuotas() {
        $this->_db->set_key("ID");
        $this->_db->order_by("FECHA_VENCIMIENTO");
        $this->_db->select("c.*, ifnull(cv.TIPO,-1) as TIPO, cv.FECHA, from_unixtime(c.FECHA_INICIO,'%d-%m-%Y') as fi, from_unixtime(FECHA_VENCIMIENTO,'%d-%m-%Y') as fv, from_unixtime(cv2.FECHA,'%d-%m-%Y') as f");
        $this->_db->join("fid_creditos_eventos cv", "cv.ID = c._ID_VARIACION", "left");

        $cuotas = $this->get_tabla_cuotas();

        $arr_cuotas = array();
        $arr_children = array();
        foreach ($cuotas as $cuota) {
            if ($cuota['_PARENT'] == 0) {
                $arr_cuotas[$cuota['ID']] = $cuota;
            } else {
                $arr_children[$cuota['ID']] = $cuota;
            }
        }


        foreach ($arr_cuotas as $cuota) {
            $arr_cuotas[$cuota['ID']]['CHILDREN'] = array();
            foreach ($arr_children as $children) {
                if ($arr_children[$children['ID']]['_PARENT'] == $arr_cuotas[$cuota['ID']]['ID']) {
                    $arr_cuotas[$cuota['ID']]['CHILDREN'][$children['ID']] = $arr_children[$children['ID']];
                }
            }
        }

        return $arr_cuotas;
    }

    //funcion complementaria de renew datos
    function _get_pagos_tipo($fecha = NO_FECHA, $log = false) {
        $variaciones = $this->_variaciones;
        $cuotas = $this->_cuotas;
        $total_pago = array();
        foreach ($cuotas as $cuota) {
            for ($i = 1; $i < 13; $i++) {
                $total_pago[$cuota['CUOTAS_RESTANTES']][$i] = 0;
            }
        }

        for ($i = 1; $i <= 12; $i++) {
            $total_pago['TOTAL'][$i] = 0;
        }
        foreach ($variaciones as $variacion) {
            if ($variacion['TIPO'] == EVENTO_RECUPERO) {

                if ($variacion['FECHA'] <= $fecha) {
                    foreach ($variacion['ASOC'] as $concepto) {

                        if (!isset($total_pago[$concepto['CUOTAS_RESTANTES']][$concepto['ID_TIPO']])) {
                            $total_pago[$concepto['CUOTAS_RESTANTES']][$concepto['ID_TIPO']] = 0;
                        }

                        if ($concepto['ID_TIPO'] == 99) {
                            $total_pago['TOTAL'][99] = $concepto['MONTO'];
                        } else {
                            $total_pago[$concepto['CUOTAS_RESTANTES']][$concepto['ID_TIPO']] += $concepto['MONTO'];
                            $total_pago['TOTAL'][$concepto['ID_TIPO']] += $concepto['MONTO'];
                        }
                    }
                } else {
                    break;
                }
            }
        }

        return $total_pago;
    }
    
    function _get_pagos_cuota($fecha, $cuotas_restantes) {
        $pagos_cuota = 0;
        $variaciones = $this->_variaciones;
        foreach ($this->_variaciones as $var) {
            if (isset($var['ASOC']) && $var['TIPO'] == EVENTO_RECUPERO && $var['FECHA'] <= $fecha) {
                foreach ($var['ASOC'] as $concepto) {
                    if ($concepto['CUOTAS_RESTANTES'] == $cuotas_restantes) {
                        $pagos_cuota += $concepto['MONTO'];
                    }
                }
            }
        }
        
        return $pagos_cuota;
    }

    //el borrado de version, elimina todos los registros generados en esta version y versiones en donde
    //esta version es ancestor
    function eliminar_version() {

        $cred = $this->_id_credito;
        $version = $this->_id_version;

        foreach ($this->_variaciones as $variacion) {
            if ($variacion['ID_VERSION'] >= $version) {
                switch ($variacion['TIPO']) {
                    case EVENTO_DESEMBOLSO:
                        $this->_db->delete("fid_creditos_desembolsos", "ID_VARIACION = " . $variacion['ID']);
                        break;
                    case EVENTO_AJUSTE:
                        $this->_db->delete("fid_creditos_desembolsos", "ID_VARIACION = " . $variacion['ID']);
                        break;
                    case EVENTO_TASA:
                        $this->_db->delete("fid_creditos_cambiotasas", "ID_VARIACION = " . $variacion['ID']);
                        break;
                    case EVENTO_RECUPERO:
                        $this->_db->delete("fid_creditos_pagos", "ID_VARIACION = " . $variacion['ID']);
                        break;
                }
            }
        }

        $this->_db->delete("fid_creditos_cuotas", "ID_CREDITO = " . $cred . " AND ID_VERSION >= " . $version);
        $this->_db->delete("fid_creditos_gastos", "ID_CREDITO = " . $cred . " AND ID_VERSION >= " . $version);
        $this->_db->delete("fid_creditos_eventos", "ID_CREDITO = " . $cred . " AND ID_VERSION >= " . $version);

        //hacemos activa la version padre
        $version_actual = $this->_db->get_row("fid_creditos_version", "ID_VERSION = " . $version);
        if ($version_actual['ACTIVA'] == 1) {
            $this->_db->update("fid_creditos_version", array("ACTIVA" => 1), "ID_VERSION = " . $version_actual['PARENT_ID']);
        }

        $this->_db->delete("fid_creditos_version", "ID_VERSION = " . $version . " AND ID_CREDITO_VERSION = " . $cred);
    }

    function get_desembolsos($fecha = NO_FECHA, $imprimir=FALSE) {
        $desembolsos = array();
        $total_desembolso = 0;
        foreach ($this->_variaciones as $variacion) {
            if (isset($variacion['ASOC']['MONTO']) && $variacion['TIPO'] == EVENTO_DESEMBOLSO && $variacion['ESTADO'] != 5) {
                if ($fecha > 0 && $variacion['FECHA'] > $fecha)
                    break;

                $total_desembolso += $variacion['ASOC']['MONTO'];
            }
        }
        $i = 1;
        foreach ($this->_variaciones as $variacion) {
            
            if (isset($variacion['ASOC']['MONTO']) && $variacion['TIPO'] == EVENTO_DESEMBOLSO && $variacion['ESTADO'] != 5) {
                if ($fecha > 0 && $variacion['FECHA'] > $fecha)
                    break;

                if ($variacion['ASOC']['MONTO'] == 0)
                    continue;

                $por = $variacion['ASOC']['MONTO'] * 100 / $total_desembolso;
                $desembolsos[] = array(
                    "NUMERO" => $i++,
                    "MONTO" => $variacion['ASOC']['MONTO'],
                    "PORCENTAJE" => $por,
                    "FECHA" => date("d/m/Y", $variacion['ASOC']['FECHA']),
                    "FECHA2" => $variacion['ASOC']['FECHA'],
                );
            }
        }
        return $desembolsos;
    }
    
    
    function get_desembolsos_cuota($fecha_desde, $fecha_hasta, $imprimir=FALSE) {
        $desembolsos = array();
        $total_desembolso = 0;
        $i = 1;
        foreach ($this->_variaciones as $variacion) {
            
            if (isset($variacion['ASOC']['MONTO']) && $variacion['TIPO'] == EVENTO_DESEMBOLSO && $variacion['ESTADO'] != 5) {
                if ($fecha_hasta > 0 && $variacion['FECHA'] > $fecha_hasta)
                    break;

                if ($variacion['ASOC']['MONTO'] == 0)
                    continue;
                
                $total_desembolso += $variacion['ASOC']['MONTO'];

                if ($variacion['FECHA'] >= $fecha_desde) {
                    $desembolsos[] = array(
                        "SALDO" => $total_desembolso,
                        "FECHA" => $variacion['ASOC']['FECHA'],
                    );
                }
            }
        }
        return $desembolsos;
    }

    function get_pagos($fecha = false) {
        $pagos = array();
        $i = 1;
        foreach ($this->_variaciones as $variacion) {
            if ($variacion['TIPO'] == EVENTO_RECUPERO AND $variacion['ESTADO'] == 0) {

                if ($fecha > 0 && $variacion['FECHA'] > $fecha)
                    break;

                $total_monto = 0;
                if ($variacion['ASOC']) {
                    foreach ($variacion['ASOC'] as $valor) {
                        $total_monto += $valor['MONTO'];
                    }
                    $recibo = str_pad($this->_id_credito, 10, "0", STR_PAD_LEFT) . '-' . str_pad(count($this->_cuotas) + 1 - $variacion['ASOC'][count($variacion['ASOC'])-1]['CUOTAS_RESTANTES'], 3, "0", STR_PAD_LEFT);
                } else {
                    $recibo = '';
                }

                $pagos[] = array(
                    "NUMERO" => $i++,
                    "MONTO" => $total_monto,
                    "FECHA" => date("d/m/Y", $variacion['FECHA']),
                    "FECHA2" => $variacion['FECHA'],
                    "ID_PAGO" => $variacion['ID'],
                    "RECIBO" => $recibo
                );
            }
        }

        return $pagos;
    }

    function get_gastos($id_credito) {
        $this->_db->where("ID_CREDITO = " . $id_credito);
        $gastos = $this->_db->get_tabla("fid_creditos_gastos");

        $gastos_rtn = array();

        foreach ($gastos as $gasto) {
            $total_pagado = 0;
            foreach ($this->_variaciones as $variacion) {
                if ($variacion['TIPO'] == 3) {
                    foreach ($variacion['ASOC'] as $pago) {
                        if ($pago['ID_TIPO'] == 8 && $pago['CUOTAS_RESTANTES'] == $gasto['ID']) {
                            $total_pagado += $pago['MONTO'];
                        }
                    }
                }
            }

            $gastos_rtn[] = array(
                "MONTO" => $gasto['MONTO'],
                "PAGADO" => $total_pagado,
                "SALDO" => $gasto['MONTO'] - $total_pagado,
                "CONCEPTO" => $gasto['CONCEPTO'],
                "FECHA_CARGA" => date("d/m/Y", $gasto['FECHA']),
            );
        }
        return $gastos_rtn;
    }

    function get_tasas_fecha($fecha) {
        $primera_variacion = reset($this->_variaciones);
        $tasas = array(
            "COMPENSATORIO" => $primera_variacion ['POR_INT_COMPENSATORIO'],
            "SUBSIDIO" => $primera_variacion ['POR_INT_SUBSIDIO'],
            "MORATORIO" => $primera_variacion ['POR_INT_MORATORIO'],
            "PUNITORIO" => $primera_variacion ['POR_INT_PUNITORIO'],
        );
        foreach ($this->_variaciones as $variacion) {
            if ($variacion['FECHA'] <= $fecha) {
                $tasas = array(
                    "COMPENSATORIO" => $variacion['POR_INT_COMPENSATORIO'],
                    "SUBSIDIO" => $variacion['POR_INT_SUBSIDIO'],
                    "MORATORIO" => $variacion['POR_INT_MORATORIO'],
                    "PUNITORIO" => $variacion['POR_INT_PUNITORIO'],
                );
            }
        }
        return $tasas;
    }

    //desde que fecha
    function set_fecha_actual($fecha = false) {

        $fecha = $fecha ? $fecha : time();
        $this->_fecha_actual = $fecha;
    }

    //hasta que fecha
    function set_fecha_calculo($fecha = NO_FECHA) {
        $this->_fecha_calculo = $fecha;
    }

    function set_compensatorio_plazo($interes_plazo) {
        $this->_interese_compensatorio_plazo = $interes_plazo;
    }

    function set_moratorio_plazo($interes_plazo) {
        $this->_interese_moratorio_plazo = $interes_plazo;
    }

    function set_punitorio_plazo($interes_plazo) {
        $this->_interese_punitorio_plazo = $interes_plazo;
    }

    function eliminar_todo_desde() {
        foreach ($this->_variaciones as $variacion) {
            if ($variacion['FECHA'] > $this->_fecha_actual) {
                $this->eliminar_variacion($variacion['ID']);
            }
        }
        $this->renew_datos();
    }

    function desimputar_pago() {
        $fecha = $this->_fecha_actual;

        $pagos = array();
        foreach ($this->_variaciones as $variacion) {
            //Buscamos los pagos desde la fecha actual
            if ($variacion['FECHA'] > $fecha && $variacion['TIPO'] == EVENTO_RECUPERO) {
                $tmp = 0;

                //se suman todos sus conceptos
                foreach ($variacion['ASOC'] as $pago) {
                    $tmp += $pago['MONTO'];
                }
                //cambiamos el estado del pago realizado anterioremente
                $this->_db->delete("fid_creditos_eventos", "ID = " . $variacion['ID']);


                $this->_db->delete("fid_creditos_pagos", "ID_VARIACION = " . $variacion['ID']);

                //generamos un nuevo evento de pago
                $pagos[] = array(
                    'monto' => $tmp,
                    'fecha' => $variacion['FECHA'],
                    'id_evento' => $variacion['ID']
                );
            }
        }


        return $pagos;
    }

    function make_active_version() {
        $this->_db->update("fid_creditos_version", array("ACTIVA" => 0), "ID_VERSION <> " . $this->_id_version);
        $this->_db->update("fid_creditos_version", array("ACTIVA" => 1), "ID_VERSION = " . $this->_id_version);
    }

    function get_clientes_credito() {
        $credito = $this->_db->get_row("fid_creditos", "ID = " . $this->_id_credito);
        

        $postulantes = array();
        if ($credito['ID_OPERACION'] == 0 && $credito['POSTULANTES']) {
            $postulantes = explode("|", $credito['POSTULANTES']);
        } else {

            $rows = $this->_db->get_tabla("fid_operacion_cliente", "ID_OPERACION = " . $credito['ID_OPERACION']);

            if (!$rows) {
                foreach ($rows as $row) {
                    $postulantes[] = $row['ID_CLIENTE'];
                }
            } elseif($credito['POSTULANTES']) {
                $postulantes = explode("|", $credito['POSTULANTES']);
            }
        }
        
        if (count($postulantes)>0) {
            $this->_db->select("c.*,ifnull(d.LOCALIDAD,'-') as LOCALIDAD, ifnull(p.PROVINCIA,'-') as PROVINCIA");
            $this->_db->join("fid_localidades d", "c.ID_DEPARTAMENTO = d.ID", "left");
            $this->_db->join("fid_provincias p", "c.ID_PROVINCIA = p.ID", "left");
            $clientes = $this->_db->get_tabla("fid_clientes c", "c.ID in (" . implode(",", $postulantes) . ")");

            return $clientes;
        }
        
        return FALSE;
    }

    function set_tipo_credito($tipo = TIPO_CREDITO_NORMAL) {
        $this->_tipo_credito = $tipo;
    }

    function fix_tasas() {
        $variacion = reset($this->_variaciones);

        $compensatorio = $variacion['POR_INT_COMPENSATORIO'];
        $subsidio = $variacion['POR_INT_SUBSIDIO'];
        $moratorio = $variacion['POR_INT_MORATORIO'];
        $punitorio = $variacion['POR_INT_PUNITORIO'];
        //se recorren todos las variaciones
        foreach ($this->_variaciones as $variacion) {
            if ($variacion['TIPO'] == EVENTO_INICIAL)
                continue;
            //los eventos siguientes al cambio de tasa
            //si encuentra otro cambio de tasa se detiene
            if ($variacion['TIPO'] == EVENTO_TASA) {
                $compensatorio = $variacion['ASOC']['COMPENSATORIO'];
                $subsidio = $variacion['ASOC']['SUBSIDIO'];
                $moratorio = $variacion['ASOC']['MORATORIO'];
                $punitorio = $variacion['ASOC']['PUNITORIO'];
            }

            $this->_db->update("fid_creditos_eventos", array(
                "POR_INT_COMPENSATORIO" => $compensatorio,
                "POR_INT_SUBSIDIO" => $subsidio,
                "POR_INT_MORATORIO" => $moratorio,
                "POR_INT_PUNITORIO" => $punitorio
                    ), "ID = " . $variacion['ID']);
        }
    }
    
    function buscarCreditoPorCuit($cuit) {
        $arr_cliente = array();
        $cuit =  str_replace(" ", "", str_replace("-", "", $cuit));
        
        $cuit = explode("/", $cuit);
        foreach ($cuit as $c) {
            $this->_db->select("ID");
            $this->_db->where("REPLACE(CUIT, '-', '')  LIKE '$c'");

            if ($result = $this->_db->get_row("fid_clientes")) {
                $arr_cliente[] = $result['ID'];
            }
        }
        
        if(count($arr_cliente)>0) {
            $id_cliente = implode("|", $arr_cliente);
            
            $this->_db->select("ID");
            $this->_db->where("POSTULANTES  = '$id_cliente' AND ID>0");
            $this->_db->where("CREDITO_ESTADO = " . ESTADO_CREDITO_NORMAL);
            if ($result = $this->_db->get_row("fid_creditos")) {
                return $result['ID'];
            }
            
            $this->_db->select("ID");
            $this->_db->where("POSTULANTES  LIKE '%$id_cliente%' AND ID>0");
            $this->_db->where("CREDITO_ESTADO = " . ESTADO_CREDITO_NORMAL);
            $this->_db->order_by("ID", "DESC");
            if ($result = $this->_db->get_row("fid_creditos")) {
                return $result['ID'];
            }
            
        }
        
        return FALSE;
    }
    
    function get_creditos_reporte($ffid=FALSE, $fdesde=FALSE, $fhasta=FALSE) {
        $sql = "";
        if ($ffid && is_array($ffid) && count($ffid)) {
            $sql .= " AND ID_FIDEICOMISO  IN (" . implode(",", $ffid) . ")";
        }
        
       /* if ($fdesde || $fhasta) {
            if ($fdesde) {
                $sql .= " AND CAPITAL_VTO >= '" . date('Y-m-d', $fdesde) . "'";
            }
            if ($fhasta) {
                $sql .= " AND CAPITAL_VTO <= '" . date('Y-m-d', $fhasta) ." 23:59:59'";
            }
        }*/
        
        $this->_db->select('c.ID, ID_FIDEICOMISO, NOMBRE AS FIDEICOMISO, MONTO_CREDITO, INTERES_VTO, RAZON_SOCIAL, cl.CUIT, DIRECCION, COD_POSTAL, TELEFONO, PROVINCIA, LOCALIDAD');
        //$this->_db->where("c.ID IN (SELECT ID_CREDITO FROM fid_creditos_pagos WHERE ID_TIPO IN (". PAGO_MORATORIO .") ) ");
        $this->_db->where("CREDITO_ESTADO = " . ESTADO_CREDITO_NORMAL . " " . $sql);
        $this->_db->join("fid_fideicomiso f", "f.ID=c.ID_FIDEICOMISO", "left");
        $this->_db->join("fid_clientes cl", "cl.ID IN (c.POSTULANTES)", "left");
        $this->_db->join("fid_localidades l", "l.ID = cl.ID_DEPARTAMENTO", "left");
        $this->_db->join("fid_provincias pr", "pr.ID = cl.ID_PROVINCIA", "left");
        $this->_db->order_by("ID", "DESC");
        //$this->_db->limit(0,10);
        return $this->_db->get_tabla("fid_creditos c");
    }
    
    function obtener_ultimo_pago() {
        $this->_db->limit(0, 1);
        $this->_db->where("ID_CREDITO = " . $this->_id_credito);
        $this->_db->order_by("FECHA", "DESC");
        $pago = $this->_db->get_tabla("fid_creditos_pagos");
        
        if($pago) {
            return $pago[0];
        } else {
            return FALSE;
        }
    }
    
    function get_banco() {
        return $this->_banco;
    }
    
    function get_ultimo_pago($cuotas_restantes, $fecha_pago) {
        $this->_db->select('FECHA');
        $this->_db->where("ID_CREDITO = " . $this->_id_credito . " AND CUOTAS_RESTANTES = $cuotas_restantes AND FECHA <= $fecha_pago");
        $this->_db->order_by("FECHA", "DESC");
        $this->_db->group_by("FECHA");
        $pagos = $this->_db->get_tabla("fid_creditos_pagos");
        
        //echo $this->_db->last_query()."<br />";
        
        if($pagos) {
            return $pagos;
        } else {
            return FALSE;
        }
        
    }
    
    function get_todos_pagos() {
        $this->_db->select('*');
        $this->_db->where("ID_CREDITO = " . $this->_id_credito);// . " AND ID_TIPO IN (". PAGO_MORATORIO . "," . PAGO_IVA_MORATORIO . "," . PAGO_CAPITAL . ")");
        $this->_db->order_by("FECHA", "ASC");
        $pagos = $this->_db->get_tabla("fid_creditos_pagos");
        
        if($pagos) {
            return $pagos;
        } else {
            return FALSE;
        }
    }
    
    function get_todos_gastos() {
        $this->_db->select('*');
        $this->_db->where("ID_CREDITO = " . $this->_id_credito);
        $this->_db->order_by("FECHA", "ASC");
        $gastos = $this->_db->get_tabla("fid_creditos_gastos");
        
        if($gastos) {
            return $gastos;
        } else {
            return FALSE;
        }
    }
    
    public function _get_desembolso() {
        $this->_db->select('FECHA, MONTO, CUOTAS_RESTANTES');
        $this->_db->where("ID_CREDITO = " . $this->_id_credito);
        $this->_db->order_by("FECHA", "ASC");
        $desembolsos = $this->_db->get_tabla("fid_creditos_desembolsos");
        
        //echo $this->_db->last_query();
        
        if($desembolsos) {
            return $desembolsos;
        } else {
            return FALSE;
        }
        
    }
    
    public function get_cuotas($fdesde=FALSE, $fhasta=FALSE) {
        if ($fdesde && $fhasta) {
            $where = " AND FECHA_GENERADA>='$fdesde' AND FECHA_GENERADA<='$fhasta'";
        } else {
            $where = '';
        }
        $this->_db->select('CAPITAL_CUOTA, INT_COMPENSATORIO, INT_COMPENSATORIO_IVA, INT_MORATORIO, INT_PUNITORIO, FECHA_VENCIMIENTO, CUOTAS_RESTANTES, CUOTA_AL_DIA, FECHA_PAGO, CUOTA_TOTAL');
        $this->_db->where("ID_CREDITO = " . $this->_id_credito . $where);
        $this->_db->order_by("FECHA_INICIO", "ASC");
        $cuotas = $this->_db->get_tabla("fid_creditos_cuotas");
        
        //echo $this->_db->last_query();die();
        
        if($cuotas) {
            return $cuotas;
        } else {
            return FALSE;
        }
    }
    
    public function get_capital_pagos() {
        $this->_db->select('MONTO, CUOTAS_RESTANTES');
        $this->_db->where("ID_CREDITO = " . $this->_id_credito . " AND ID_TIPO = ". PAGO_CAPITAL);
        $this->_db->order_by("FECHA", "ASC");
        $pagos = $this->_db->get_tabla("fid_creditos_pagos");
        
        if($pagos) {
            return $pagos;
        } else {
            return FALSE;
        }
        
    }
    
    public function caducar_credito($creditoId, $nuevoCreditoId, $fecha) {
        //por el momento no se guarda fecha
        if (!$creditoId) {
            return;
        }
        
        $this->_db->update("fid_creditos", array("CREDITO_ESTADO" => ESTADO_CREDITO_CADUCADO), "ID = " . $creditoId);
        $this->_db->update("fid_creditos", array("ID_CADUCADO" => $creditoId), "ID = " . $nuevoCreditoId);
    }
    
    public function prorrogar_credito($creditoId, $nuevoCreditoId, $fecha) {
        //por el momento no se guarda fecha
        if (!$creditoId) {
            return;
        }
        
        $this->_db->update("fid_creditos", array("CREDITO_ESTADO" => ESTADO_CREDITO_PRORROGADO), "ID = " . $creditoId);
        $this->_db->update("fid_creditos", array("ID_PRORROGA" => $creditoId), "ID = " . $nuevoCreditoId);
    }
    
    public function get_creditos() {
        $fecha = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - (3600*24));
        
        $this->_db->select('ID');
        $this->_db->where("CREDITO_ESTADO <> " . ESTADO_CREDITO_ELIMINADO . " AND FUPDATED < '$fecha'");
        $this->_db->order_by("ID", "ASC");
        return $this->_db->get_tabla("fid_creditos");
    }
    
    public function fupdate_credito($id, $fupdated = FALSE) {
        if ($fupdated) {
            $fupdated = array('FUPDATED' => $fupdated);
        } else {
            $fupdated = array('FUPDATED' => date('Y-m-d H:i:s'));
        }
        $this->_db->update('fid_creditos', $fupdated, "ID={$id}");//marco para no actualizar
    }
    
    public function emitir_una_cuota($fecha = FALSE) {
        //hay que tomar el saldo del crédito, dejar una sola cuota con este valor, y fecha de vencimiento de cuota siguiente a la última cuota paga
        $SALDO_CAPITAL = $this->_total_credito;
        $__cuota = FALSE;
        
        foreach ($this->_cuotas as $id => $cuota) {
            if (isset($this->_pagos[$cuota['CUOTAS_RESTANTES']])) {
                $SALDO_CAPITAL -= $this->_pagos[$cuota['CUOTAS_RESTANTES']][PAGO_CAPITAL];
                
                if (!$__cuota && ($cuota['CAPITAL_CUOTA'] - $this->_pagos[$cuota['CUOTAS_RESTANTES']][PAGO_CAPITAL]) > 1) {
                    $this->_pagos[$cuota['CUOTAS_RESTANTES']][PAGO_CAPITAL];
                    $cuota_vencimiento = $cuota;
                    $__cuota = $id;
                    break;
                }
            }
        }
        
        if (!isset($cuota_vencimiento)) {
            $cuota_vencimiento = end($this->_cuotas);
        }
        
            $SALDO_CAPITAL = round($SALDO_CAPITAL, 1);
            
        $cuota_vencimiento['SALDO_CAPITAL'] = $SALDO_CAPITAL;
        $cuota_vencimiento['CUOTAS_RESTANTES'] = 1;
            $cuota_vencimiento['POR_INT_COMPENSATORIO'] = 1;
        
        $evento_inicial = false;
        $evento_desembolso = false;
        $eventos_tasas = array();
        
        foreach ($this->_variaciones as $variacion) {
            switch ($variacion['TIPO']) {
                case EVENTO_INICIAL:
                    $evento_inicial = $variacion;
                    break;
                case EVENTO_DESEMBOLSO:
                    $evento_desembolso = $variacion;
                    break;
            }
            
            if ($evento_inicial && $evento_desembolso) {
                break;
            }
        }
        
        
        foreach ($this->_variaciones as $variacion) {
            switch ($variacion['TIPO']) {
                case EVENTO_TASA:
                    if ($variacion['FECHA'] < $fecha) {
                        $eventos_tasas[] = $variacion;
                    }
                    break;
            }
        }
        
        $evento_inicial['FECHA'] =  $cuota_vencimiento['FECHA_VENCIMIENTO'];
        $evento_inicial['CAPITAL'] =  $SALDO_CAPITAL;
        $evento_inicial['CUOTAS_GRACIA'] =  0;
        $evento_inicial['CANTIDAD_CUOTAS'] =  1;
        
        $this->_variaciones = array();
        $variacion = $evento_inicial;
            //$variacion['POR_INT_COMPENSATORIO'] = 0;
        $this->_variaciones[$variacion['ID']] = $variacion;
        
        if ($evento_desembolso) {
            $variacion['ID'] = $evento_desembolso['ID'];
            $variacion['TIPO'] = $evento_desembolso['TIPO'];
            $variacion['ID_VERSION'] = $evento_desembolso['ID_VERSION'];
            $evento_desembolso['ASOC']['CUOTAS_RESTANTES'] = 1;
            $evento_desembolso['ASOC']['FECHA'] = $cuota_vencimiento['FECHA_VENCIMIENTO'];
            $evento_desembolso['ASOC']['MONTO'] = $SALDO_CAPITAL;
            $variacion['ASOC'] = $evento_desembolso['ASOC'];
            $this->_variaciones[$variacion['ID']] = $variacion;
        }
        
        $id_var = $variacion['ID'];
        if ($eventos_tasas) {
            foreach ($eventos_tasas as $t) {
                $this->_variaciones[$t['ID']] = $t;
            }
        }
        
        foreach ($this->_variaciones as $k => $v) {
            $this->_variaciones[$k]['POR_INT_COMPENSATORIO'] = 0;
        }
        
        //$cuota_vencimiento['FECHA_VENCIMIENTO'] = strtotime(date('Y-m-d')." 23:59:59");
        
        $cuota_vencimiento['FECHA_VENCIMIENTO'] += 1;
        $this->_actualizacion_compensatorios = 1;
        //EVENTO_DESEMBOLSO
                
        $this->_cuotas = array();
        $this->_cuotas[$__cuota] = $cuota_vencimiento;
        
                
        $this->_pagos = array(
            array(
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => 0,
                12 => 0
                )
            );
        
        
    }
    
    
    public function emitir_credito_caido($fecha = FALSE) {
        //hay que tomar el saldo del crédito, dejar una sola cuota con este valor, y fecha de vencimiento de cuota siguiente a la última cuota paga
        $SALDO_CAPITAL = 0;
        $key_cuota = FALSE;
        $__cuota = FALSE;
        $id_ant = 0;
        
        $_compensatorios = 0;
        $ret_reuda = $this->get_deuda($fecha, true);
        $this->_db->select('FECHA');
        $ultimo_pago = $this->_db->get_row('fid_creditos_pagos', 'ID_CREDITO = ' . $this->_id_credito, 'FECHA DESC');
        $this->_credito_caido = TRUE;
        
        foreach ($ret_reuda['cuotas'] as $k => $cuota) {
            $SALDO_CAPITAL += $cuota['CAPITAL']['SALDO'];
            
            $saldo_cuota = $cuota['GASTOS_VARIOS']['SALDO'] + $cuota['CAPITAL']['SALDO'] + $cuota['COMPENSATORIO']['SALDO'];
            $saldo_cuota += $cuota['MORATORIO']['SALDO'] + $cuota['PUNITORIO']['SALDO'];
            $saldo_cuota += $cuota['IVA_GASTOS']['SALDO'] + $cuota['IVA_COMPENSATORIO']['SALDO'] + $cuota['IVA_PUNITORIO']['SALDO'] + $cuota['IVA_MORATORIO']['SALDO'];
            
            if ($key_cuota === FALSE && $saldo_cuota > 0.5) {
                $__cuota = $cuota['ID'];
                $key_cuota = ($ultimo_pago && $ultimo_pago['FECHA'] > $cuota['_INFO']['HASTA']) ? $ultimo_pago['FECHA'] : $cuota['_INFO']['HASTA'];
                if ($k == 0) {
                    $_compensatorios = $cuota['COMPENSATORIO']['SALDO'] + $cuota['IVA_COMPENSATORIO']['SALDO'];
                    /*if ($_compensatorios < 0.1 && isset($ret_reuda['cuotas'][$k])) {
                        $_compensatorios = $ret_reuda['cuotas'][$k + 1]['COMPENSATORIO']['SALDO'] + $ret_reuda['cuotas'][$k + 1]['IVA_COMPENSATORIO']['SALDO'];
                    }*/
                }
            }
        }
        
        //echo "SALDO_CAPITAL: $SALDO_CAPITAL<br />";
        $SALDO_CAPITAL += $_compensatorios;
        if (!$key_cuota) {
            $key_cuota = $fecha;
        }
        /*
        
        foreach ($this->_cuotas as $id => $cuota) {
            $_compensatorios = $cuota['INT_COMPENSATORIO'] + $cuota['INT_COMPENSATORIO_IVA'];
            if (isset($this->_pagos[$cuota['CUOTAS_RESTANTES']])) {
                $SALDO_CAPITAL -= $this->_pagos[$cuota['CUOTAS_RESTANTES']][PAGO_CAPITAL];
                $_compensatorios -= $this->_pagos[$cuota['CUOTAS_RESTANTES']][PAGO_COMPENSATORIO];
                $_compensatorios -= $this->_pagos[$cuota['CUOTAS_RESTANTES']][PAGO_IVA_COMPENSATORIO];
            }
            
            print_r($cuota);
            
            if ($key_cuota === FALSE && $_compensatorios > 0.2) {
                if (isset($this->_cuotas[$id - 1]))
                    $key_cuota = $this->_cuotas[$id - 1]['FECHA_VENCIMIENTO'];
                else
                    $key_cuota = $cuota['FECHA_VENCIMIENTO'];
                $SALDO_CAPITAL += $_compensatorios;
                $__cuota = $id;
            }
        }
        die;*/
        $SALDO_CAPITAL = round($SALDO_CAPITAL, 2);
            
        $cuota_vencimiento['SALDO_CAPITAL'] = $SALDO_CAPITAL;
        $cuota_vencimiento['CUOTAS_RESTANTES'] = 1;
        $cuota_vencimiento['POR_INT_COMPENSATORIO'] = 1;
        $cuota_vencimiento['FECHA_ENVIADA'] = $key_cuota;
        $cuota_vencimiento['ID'] = $__cuota;
        $cuota_vencimiento['E_AMORTIZACION'] = 0;
        $cuota_vencimiento['ESTADO'] = '';
        $cuota_vencimiento['FECHA_ENVIADA'] = $key_cuota;
        $cuota_vencimiento['FECHA_INICIO'] = $key_cuota;
        $cuota_vencimiento['FECHA_INICIO'] = $key_cuota;
        $cuota_vencimiento['FECHA_VENCIMIENTO'] = $key_cuota;
        
        $evento_inicial = false;
        $evento_desembolso = false;
        $eventos_tasas = array();
        
        foreach ($this->_variaciones as $variacion) {
            $variacion['FECHA_INICIO'] = $key_cuota;
            switch ($variacion['TIPO']) {
                case EVENTO_INICIAL:
                    $evento_inicial = $variacion;
                    break;
                case EVENTO_DESEMBOLSO:
                    $evento_desembolso = $variacion;
                    break;
            }
            
            if ($evento_inicial && $evento_desembolso) {
                break;
            }
        }
        
        foreach ($this->_variaciones as $variacion) {
            switch ($variacion['TIPO']) {
                case EVENTO_TASA:
                    if ($variacion['FECHA'] < $fecha) {
                        $eventos_tasas[] = $variacion;
                    }
                    break;
            }
        }
        
        $evento_inicial['FECHA'] =  $key_cuota - 1;
        $evento_inicial['CAPITAL'] =  $SALDO_CAPITAL;
        $evento_inicial['CUOTAS_GRACIA'] =  0;
        $evento_inicial['CANTIDAD_CUOTAS'] =  1;
        
        $this->_variaciones = array();
        $variacion = $evento_inicial;
            //$variacion['POR_INT_COMPENSATORIO'] = 0;
        $this->_variaciones[$variacion['ID']] = $variacion;
        
        if ($evento_desembolso) {
            $variacion['ID'] = $evento_desembolso['ID'];
            $variacion['TIPO'] = $evento_desembolso['TIPO'];
            $variacion['ID_VERSION'] = $evento_desembolso['ID_VERSION'];
            $evento_desembolso['ASOC']['CUOTAS_RESTANTES'] = 1;
            $evento_desembolso['ASOC']['FECHA'] = $key_cuota;
            $evento_desembolso['ASOC']['MONTO'] = $SALDO_CAPITAL;
            $variacion['ASOC'] = $evento_desembolso['ASOC'];
            $this->_variaciones[$variacion['ID']] = $variacion;
        }
        
        $id_var = $variacion['ID'];
        if ($eventos_tasas) {
            foreach ($eventos_tasas as $t) {
                $this->_variaciones[$t['ID']] = $t;
                $this->_variaciones[$t['ID']]['CRED_CAIDO'] = TRUE;
            }
        }
        
        foreach ($this->_variaciones as $k => $v) {
            if (!$this->_actualizacion_compensatorios) {
                $this->_variaciones[$k]['POR_INT_COMPENSATORIO'] = 0;
            }
        }
        
        $this->_actualizacion_compensatorios = 1;
        //EVENTO_DESEMBOLSO
                
        $this->_cuotas = array();
        $this->_cuotas[$__cuota] = $cuota_vencimiento;
        
                
        $this->_pagos = array(
            array(
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => 0,
                12 => 0
                )
            );
    }
    
    public function updateFechaPago() {
        if ($creditos = $this->get_creditos()) {
            $fecha = strtotime(date('Y-m-d'));
            foreach ($creditos as $credito) {
                $this->clear();
                $this->set_credito_active($credito['ID']);
                $version = $this->set_version_active();
                $this->renew_datos();

                $this->set_fecha_actual($fecha);
                $this->set_fecha_calculo();
                $this->renew_datos($fecha);
                $this->save_last_state(false);

                $this->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);//proyeccion teorica

                $this->generar_evento(array(), true, $fecha);

                $ret_deuda = $this->get_deuda($fecha, true);
                
                $intereses = 0;
                if (isset($ret_deuda['cuotas'])) {
                    foreach($ret_deuda['cuotas'] as $c) {
                        $_intereses = $c['IVA_PUNITORIO']['SALDO'] + $c['IVA_MORATORIO']['SALDO'] + $c['IVA_COMPENSATORIO']['SALDO'] + $c['PUNITORIO']['SALDO'] + $c['MORATORIO']['SALDO'] + $c['COMPENSATORIO']['SALDO'];
                        $total_a_pagar = (isset($c['GASTOS']['SALDO']) ? $c['GASTOS']['SALDO'] : 0) + $c['CAPITAL']['SALDO'] + $_intereses;
                        $_intereses = $c['IVA_PUNITORIO']['TOTAL'] + $c['IVA_MORATORIO']['TOTAL'] + $c['IVA_COMPENSATORIO']['TOTAL'] + $c['PUNITORIO']['TOTAL'] + $c['MORATORIO']['TOTAL'] + $c['COMPENSATORIO']['TOTAL'];
                        $total_cuota = (isset($c['GASTOS']['TOTAL']) ? $c['GASTOS']['TOTAL'] : 0) + $c['CAPITAL']['TOTAL'] + $_intereses;
                        $intereses += $_intereses;
                        $arr_update = array(
                            'CUOTA_AL_DIA' => $total_a_pagar,
                            'CUOTA_TOTAL' => $total_cuota,
                            'INT_COMPENSATORIO' => $c['COMPENSATORIO']['TOTAL'],
                            'INT_COMPENSATORIO_IVA' => $c['IVA_COMPENSATORIO']['TOTAL'],
                            'INT_MORATORIO' => $c['MORATORIO']['TOTAL'] + $c['IVA_MORATORIO']['TOTAL'],
                            'INT_PUNITORIO' => $c['PUNITORIO']['TOTAL'] + $c['IVA_PUNITORIO']['TOTAL']
                        );
                        
                        $this->_db->update('fid_creditos_cuotas', $arr_update, "ID_CREDITO={$credito['ID']} AND CUOTAS_RESTANTES={$c['CUOTAS_RESTANTES']}");
                    }
                    
                    $this->fupdate_credito($credito['ID']);
                }
                
                if ($intereses==0) { //si no tiene intereses debe ser porque no tiene cargado desembolsos
                    $this->_db->update('fid_creditos_cuotas', array('CUOTA_AL_DIA' => -1), "ID_CREDITO={$credito['ID']}");
                }
                
                $this->_db->select('ID_TIPO, CUOTAS_RESTANTES, MONTO, FECHA');
                $this->_db->where("ID_CREDITO=" . $credito['ID']);
                $this->_db->order_by("FECHA", "ASC");
                $_pagos = $this->_db->get_tabla("fid_creditos_pagos");

                if ($_pagos) {
                    $pagos = array();
                    foreach ($_pagos as $pg) {
                        if (isset($pagos[$pg['CUOTAS_RESTANTES']])) {
                            $pagos[$pg['CUOTAS_RESTANTES']]['MONTO'] += ($pg['ID_TIPO'] == PAGO_CAPITAL) ? $pg['MONTO'] : 0;
                            $pagos[$pg['CUOTAS_RESTANTES']]['FECHA'] = $pg['FECHA'];
                        } else {
                            $pagos[$pg['CUOTAS_RESTANTES']]['MONTO'] = ($pg['ID_TIPO'] == PAGO_CAPITAL) ? $pg['MONTO'] : 0;
                            $pagos[$pg['CUOTAS_RESTANTES']]['FECHA'] = $pg['FECHA'];
                            $pagos[$pg['CUOTAS_RESTANTES']]['CUOTAS_RESTANTES'] = $pg['CUOTAS_RESTANTES'];
                        }
                    }

                    foreach ($pagos as $pg) {
                        $this->_db->update(
                                "fid_creditos_cuotas",
                                array("FECHA_PAGO" => $pg['FECHA']),
                                "ID_CREDITO=" . $credito['ID'] . " AND CUOTAS_RESTANTES=" . $pg['CUOTAS_RESTANTES'] . " AND (CAPITAL_CUOTA-" . $pg['MONTO'].")<1"
                                );
                    }
                }
            }
        }
        die("FIN-ACTUALIZADO!");
        
    }
    
    function es_caducado(){
        $this->_db->select("ID_CADUCADO");
        $cad = $this->_db->get_tabla("fid_creditos", "ID=" . $this->_id_credito);
        return isset($cad[0]['ID_CADUCADO']) ? $cad[0]['ID_CADUCADO'] : 0;
    }
    
    function cuotas_restantes_prorroga() {
        $ultimo_pago =  $this->obtener_ultimo_pago(); 
        if ($ultimo_pago) {
            return $ultimo_pago['CUOTAS_RESTANTES'] - 1;
        } else {
            $this->_db->limit(0, 1);
            $this->_db->where("ID_CREDITO = " . $this->_id_credito);
            $this->_db->order_by("CUOTAS_RESTANTES", "DESC");
            $cuotas = $this->_db->get_tabla("fid_creditos_cuotas");
            
            if($cuotas) {
                return $cuotas[0]['CUOTAS_RESTANTES'];
            } else {
                return FALSE;
            }
        }
    }
    
    function eliminar_pagos($id_evento) {
        $this->_db->select("ID_VARIACION");
        $ids_eventos = $this->_db->get_tabla("fid_creditos_pagos", "ID_CREDITO=" . $this->_id_credito . " AND FECHA >= (SELECT FECHA FROM fid_creditos_pagos WHERE ID_CREDITO=" . $this->_id_credito . " AND ID_VARIACION=$id_evento LIMIT 1) GROUP BY ID_VARIACION");
        
        if($ids_eventos) {
            $arr = array();
            foreach ($ids_eventos as $it) {
                $arr[] =  $it['ID_VARIACION'];
            }
            
            $ids_eventos = implode(', ', $arr);
            
            $this->_db->delete("fid_creditos_pagos", "ID_CREDITO=" . $this->_id_credito . " AND ID_VARIACION IN ($ids_eventos)");
            $this->_db->delete("fid_creditos_eventos", "ID_CREDITO=" . $this->_id_credito . " AND ID IN ($ids_eventos)");
            return TRUE;
        }
        
        return FALSE;
    }
    
    function setCambiosTasasOperatoria() {
        if (!$this->_id_operatoria) {
            return;
        }
            
        $cuota = reset($this->_cuotas);
        $fecha = $cuota['FECHA_INICIO'];
        
        $this->_db->select('*');
        $this->_db->where("ID_OPERATORIA = " . $this->_id_operatoria . " AND FECHA >= '$fecha'");
        $this->_db->order_by("FECHA", "ASC");
        $cambiotasas = $this->_db->get_tabla("fid_operatoria_cambiotasas");
        
        if ($cambiotasas) {
            $data = array();
            $data['TIPO'] = EVENTO_TASA;
            
            $primera_variacion = reset($this->_variaciones);
            
            $por_int_compensatorio = $primera_variacion['POR_INT_COMPENSATORIO'];
            $por_int_subsidio = $primera_variacion['POR_INT_SUBSIDIO'];
            $por_int_moratorio = $primera_variacion['POR_INT_MORATORIO'];
            $por_int_punitorio = $primera_variacion['POR_INT_PUNITORIO'];
            
            foreach ($cambiotasas as $ct) {
                if ($ct['COMPENSATORIO'] == -1) {
                    $ct['COMPENSATORIO'] = $por_int_compensatorio;
                } else {
                    $por_int_compensatorio = $ct['COMPENSATORIO'];
                }
                if ($ct['SUBSIDIO'] == -1) {
                    $ct['SUBSIDIO'] = $por_int_subsidio;
                } else {
                    $por_int_subsidio = $ct['SUBSIDIO'];
                }
                if ($ct['MORATORIO'] == -1) {
                    $ct['MORATORIO'] = $por_int_moratorio;
                } else {
                    $por_int_moratorio = $ct['MORATORIO'];
                }
                if ($ct['PUNITORIO'] == -1) {
                    $ct['PUNITORIO'] = $por_int_punitorio;
                } else {
                    $por_int_punitorio = $ct['PUNITORIO'];
                }
                
                $data['por_int_compensatorio'] = $ct['COMPENSATORIO'] ;
                $data['por_int_subsidio'] = $ct['SUBSIDIO'];
                $data['por_int_moratorio'] = $ct['MORATORIO'];
                $data['por_int_punitorio'] = $ct['PUNITORIO'];
                $ret = $this->generar_evento($data, true, $ct['FECHA']);
                $this->agregar_tasa($ct['COMPENSATORIO'], $ct['SUBSIDIO'], $ct['MORATORIO'], $ct['PUNITORIO'], $cuota['CUOTAS_RESTANTES'], $ct['FECHA']);
                $this->assign_id_evento($ret['ID'],EVENTO_TASA);
            }
        }
    }
    
    function getEvento($idEvento) {
        $this->_db->where("ID = " . $idEvento);
        $this->_db->where("ID_CREDITO = " . $this->_id_credito);
        $rtn = $this->_db->get_row("fid_creditos_eventos");
        
        return $rtn;
    }
    
    function realizar_pago($fecha, $monto) {
        $this->elimina_eventos_temporales();


        //se genera evento para definir el dia de corte
        $this->renew_datos();
        $this->save_last_state(false);
        $this->set_fecha_actual($fecha);

        //no adelanta capital.. cancela exclusivamente intereses y luego capital
        $this->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);

        $ret_evento = $this->generar_evento(array(), true, $fecha, true);

        $this->set_log(true);
        $ret_evento_id = $ret_evento['ID'];
        $ret_reduda = $this->get_deuda($fecha, true, $monto);

        //se elimina el evento
        $this->elimina_evento($ret_evento_id);
        $this->set_log(false);

        //si el monto es 0 solo se mostrara la deuda
        $obj_pago = $this->pagar_deuda($ret_reduda, $monto, $fecha);
        
        $pagos = $obj_pago['pagos'];
        


        //en las cuotas canceladas tenemos las cuotas que han sido canceladas en el ultimo pago y la fecha de dicho pago
        //de esta forma podemos adelantar las fechas de vencimiento segun algun criterio a especificar o alguna otra
        //tarea que se necesite
        //$cuotas_canceladas = $obj_pago['cuotas_canceladas'];

        $data = array();


        $this->save_last_state(true);
        $pago_total = 0;

        //recorremos los pagos para verificar anteriormente a la asignacion
        //el pago de adelantos
        $badelanta = false;
        
        $cuotas_restantes = $this->get_cuotas_restantes($fecha);
        foreach ($pagos as $pago) {
            //se puede cargar un tipo de pago para reasignar (lo cual indica cuota cancelada)
            if ($pago['ID_TIPO'] == PAGO_ADELANTADO) {
                $badelanta = true;
                break;
            }


            //o si existe un pago de capital de una cuota siguiente a la cuota correspondiente en la fecha dada
            //es decir, se paga un 5/5 correspondiente a la fecha de la cuota 5, 
            //si se paga capital de  la cuota 4 significa que se ha adelantado capital
            if ($pago['ID_TIPO'] == PAGO_CAPITAL && $cuotas_restantes > $pago['CUOTAS_RESTANTES']) {
                $badelanta = true;
                break;
            }
        }

        if ($badelanta) {
            //se verifica si la cuota a la fecha dada esta planchada.. de ser asi le saca el planchado 
            //y recalcula los pagos desde esa fecha
            if (($fecha_planchado = $this->modificar_planchado($fecha)) > 0) {
                //se debe reimputar los pagos desde la fecha de planchado
                //           $this->_recalcular_pagos($fecha_planchado);
                //           return;
            }
        }

        //recorremos los pagos para asignar los adelantos de pago
        foreach ($pagos as $pago) {
            if ($pago['ID_TIPO'] == PAGO_CAPITAL) {
                $pago_total += $pago['MONTO'];
                break;
            }
            if ($pago['ID_TIPO'] == PAGO_ADELANTADO) {
                $this->renew_datos();

                //$adelanto_pago = $this->adelantar_pagos($fecha);
                //$pago_total += $adelanto_pago;
                break;
            }
        }
        $data['monto'] = $pago_total;
        $data['TIPO'] = EVENTO_RECUPERO;
        $ret = $this->generar_evento($data, true, $fecha);

        $this->assign_id_evento($ret['ID'], EVENTO_RECUPERO);

        $this->get_segmentos_cuota();
    }

    function pagar_deuda($arr_deuda, $monto, $fecha) {
        $id_credito = $this->_id_credito;
        $arr_pago = array();
        
        //print_r($arr_deuda);
        
        $cuotas_canceladas = array();

        if ($monto <= 0)
            return;
        
        //pagamos gasto
        /*foreach ($arr_deuda['gastos'] as $key => $val) {
            $pago = $monto <= $val['SALDO'] ? $monto : $val['SALDO'];
            if ($pago > 0) {
                $arr_pago[] = array(
                    "ID_CREDITO" => $id_credito,
                    "FECHA" => $fecha,
                    "ID_TIPO" => PAGO_GASTOS,
                    "MONTO" => $monto <= $val['SALDO'] ? $monto : $val['SALDO'],
                    "CUOTAS_RESTANTES" => $val['ID'],
                    "ID_VARIACION" => TEMP_ID
                );

                $monto = $monto <= $val['SALDO'] ? 0 : $monto - $val['SALDO'];
            }
        }*/
        
        //pagamos cuotas
        for ($x = 0; $x < count($arr_deuda['cuotas']); $x++) {
            
            
            foreach ($arr_deuda['gastos'] as $key => $val) {
                if ($val['ROW']['FECHA'] <= $arr_deuda['cuotas'][$x]['_INFO']['HASTA']) {
                    $pago = $monto <= $val['SALDO'] ? $monto : $val['SALDO'];
                    
                    if ($pago > 0) {
                        $arr_pago[] = array(
                            "ID_CREDITO" => $id_credito,
                            "FECHA" => $fecha,
                            "ID_TIPO" => PAGO_GASTOS,
                            "MONTO" => $monto <= $val['SALDO'] ? $monto : $val['SALDO'],
                            "CUOTAS_RESTANTES" => $arr_deuda['cuotas'][$x]['CUOTAS_RESTANTES'],
                            "ID_VARIACION" => TEMP_ID
                        );

                        $monto = $monto <= $val['SALDO'] ? 0 : $monto - $val['SALDO'];
                        $arr_deuda['gastos'][$key]['SALDO'] -= $pago;
                    }
                }
            }
            
            //antes de quitar los otros gastos pagamos los gastos administrativos de la cuota
            foreach ($arr_deuda['cuotas'][$x]['GASTOS'] as $key => $val) {
                if ($val['TIPO'] == PAGO_GASTOS_ADM) {
                    $pago = $monto <= $val['SALDO'] ? $monto : $val['SALDO'];
                    if ($pago > 0) {
                        $arr_pago[] = array(
                            "ID_CREDITO" => $id_credito,
                            "FECHA" => $fecha,
                            "ID_TIPO" => PAGO_GASTOS_ADM,
                            "MONTO" => $monto <= $val['SALDO'] ? $monto : $val['SALDO'],
                            "CUOTAS_RESTANTES" => $arr_deuda['cuotas'][$x]['CUOTAS_RESTANTES'],
                            "ID_VARIACION" => TEMP_ID
                        );

                        $monto = $monto <= $val['SALDO'] ? 0 : $monto - $val['SALDO'];
                    }
                }
            }
            
            unset($arr_deuda['cuotas'][$x]['GASTOS']);
            $cuota = $arr_deuda['cuotas'][$x];

            $bmoratorio = false;
            $bpunitorio = false;
            $bcompensatorio = false;
            $bcapital = false;
            $bgastos = false;
            
            foreach ($cuota as $key => $val) {
                if (!isset($val['TIPO'])) {
                    continue;
                }
                if ($monto <= 0.2)
                    break 2;
                if ($key == 'ID' || $key == 'CUOTAS_RESTANTES')
                    break;

                if (startsWith($key, "_"))
                    continue;


                //se carga el pago a montos superiores a 0.01 centavo.
                if ((round($val['SALDO'], 2) > 0)) {
                    //  $monto = $monto <= $val['SALDO'] ? $monto : $val['SALDO'] ;
                    $pago = $monto <= $val['SALDO'] ? $monto : $val['SALDO'];
                    $arr_pago[] = array(
                        "ID_CREDITO" => $id_credito,
                        "FECHA" => $fecha,
                        "ID_TIPO" => $val['TIPO'],
                        "MONTO" => $pago,
                        "CUOTAS_RESTANTES" => $cuota['CUOTAS_RESTANTES'],
                        "ID_VARIACION" => TEMP_ID
                    );
                    
                    //verificamos si el saldo es 0 (que esta cancelado el item)
                    if ($monto >= $val['SALDO']) {
                        switch ($val['TIPO']) {
                            case PAGO_IVA_GASTOS_ADM:
                                $bgastos = true;
                                break;
                            case PAGO_MORATORIO: //Moratorio
                                $bmoratorio = true;
                                $bgastos = true;
                                break;
                            case PAGO_PUNITORIO: //punitorio
                                $bpunitorio = true;
                                $bgastos = true;
                                break;
                            case PAGO_COMPENSATORIO: //compensatorio
                                $bmoratorio = true;
                                $bpunitorio = true;
                                $bcompensatorio = true;
                                $bgastos = true;
                                break;
                            case PAGO_CAPITAL: //capital
                                $bmoratorio = true;
                                $bpunitorio = true;
                                $bcompensatorio = true;                                
                                $bcapital = true;
                                $bgastos = true;
                                break;
                        }
                    }
                    $monto = $monto >= $val['SALDO'] ? $monto - $val['SALDO'] : 0;
                }
            }
            
            //print_r($arr_pago);die();
            
            ////si el capital es 0 (cuota de gracia) la cuota queda cancelada
            //sin contar el capital.
            if ($arr_deuda['cuotas'][$x]['CAPITAL'] == 0) {
                if ($bgastos && $bmoratorio && $bpunitorio && $bcompensatorio && $bcapital) {
                    $arr_deuda['cuotas'][$x]['_INFO']['ESTADO'] = 1;
                }
            }
            
            //si el capital es mayor a 0 (cuota con amortizacion de capital) la cuota queda cancelada
            //si estan cancelados todos los items.            
            else {
                if ($bgastos && $bmoratorio && $bpunitorio && $bcompensatorio) {
                    
                    //las cuotas que han sido canceladas pagando capital se retornan
                    //para cambiarles la fecha de vencimiento si asi corresponde
                    $cuota_paga = $arr_deuda['cuotas'][$x];
                    $cuota_paga['FECHA_CANCELADA'] = $fecha;
                    $cuotas_canceladas[] = $cuota_paga;
                    $arr_deuda['cuotas'][$x]['_INFO']['ESTADO'] = 1;
                }
            }
        }
        
       // print_r($arr_pago);echo $monto;die("aca");

        //TIPO 10 es un monto no asignado.
        if ($monto > 0.2) {
            $restante = array(
                "ID_CREDITO" => $id_credito,
                "FECHA" => $fecha,
                "ID_TIPO" => PAGO_ADELANTADO,
                "MONTO" => $monto,
                "CUOTAS_RESTANTES" => 0,
                "ID_VARIACION" => TEMP_ID
            );


            $arr_pago[] = $restante;
        }
        else{

        }
        
        //INSERCION EN BASE DE DATOS
        foreach ($arr_pago as $pago) {
            if ((round($pago['MONTO'], 2) > 0))
                $this->_db->insert("fid_creditos_pagos", $pago);
        }

        //se recorren las cuotas, las que esten canceladas se ignorarn. Las que no, se evaluan si su saldo de capita
        // es 0, si es asi, se guardan los valores int moratorio y punitorio para no volver a calcularse.
        foreach ($arr_deuda['cuotas'] as $cuota) {
            $int_array = array();
            if (isset($cuota['MORATORIO'])) {
                $int_array["INT_MORATORIO"] = $cuota['MORATORIO']['TOTAL'];
                $int_array["INT_PUNITORIO"] = $cuota['PUNITORIO']['TOTAL'];
            }
            $int_array["ESTADO"] = $cuota['_INFO']['ESTADO'];
            
            
            $this->_db->update("fid_creditos_cuotas", $int_array, "ID = " . $cuota['ID']);
        }
        
        
        //se devuelven los pagos realizados y las cuotas canceladas
        $rtn = array(
            "pagos" => $arr_pago,
            "cuotas_canceladas"=>$cuotas_canceladas
        );
        return $rtn;
    }
    
    function generar_clientes() {
        $this->_db->select('POSTULANTES, ID');
        if ($creditos = $this->_db->get_tabla("fid_creditos")) {
            foreach ($creditos as $credito) {
                if ($data_clientes = $this->_generar_clientes($credito['POSTULANTES'])) {
                    $this->_db->update('fid_creditos', $data_clientes, 'ID = ' . $credito['ID']);
                }
            }
        }
    }
    
    function _generar_clientes($clientes = FALSE) {
        if($clientes) {
            $clientes = $this->_db->get_tabla("fid_clientes", "ID IN (" . str_replace("|", ',', $clientes) . ")");
            if ($clientes) {
                $nombres = array();
                $cuits = array();
                foreach ($clientes as $it_cl) {
                    $nombres[] = $it_cl['RAZON_SOCIAL'];
                    $cuits[] = $it_cl['CUIT'];
                }
                
                return array(
                    'POSTULANTES_NOMBRES' => implode(' | ', $nombres),
                    'POSTULANTES_CUIT' => implode(' | ', $cuits),
                    );
            }
        }
        return FALSE;
    }
    
    public function get_last_cambiotasas($id_operatoria, $fecha) {
        $tasas = FALSE;
        if ($this->_id_credito) {
            $this->_db->select('COMPENSATORIO, SUBSIDIO, MORATORIO, PUNITORIO');
            $tasas = $this->_db->get_row('fid_creditos_cambiotasas', 'ID_CREDITO= ' . $this->_id_credito, 'FECHA ASC');   
        }
        
        if (!$tasas) {
            $this->_db->select('POR_INT_COMPENSATORIO AS COMPENSATORIO, POR_INT_SUBSIDIO AS SUBSIDIO, POR_INT_MORATORIO AS MORATORIO, POR_INT_PUNITORIO AS PUNITORIO');
            $tasas = $this->_db->get_row('fid_creditos_eventos', 'TIPO=1 AND ID_CREDITO= ' . $this->_id_credito, 'FECHA ASC');   
        }
        
        if ($id_operatoria) {
            if ($operatoria = $this->_db->get_row("fid_operatorias", 'ID=' . $id_operatoria)) {
                if (!$tasas) {
                    $tasas = array(
                        'COMPENSATORIO' => $operatoria['TASA_INTERES_COMPENSATORIA'],
                        'MORATORIO' => $operatoria['TASA_INTERES_MORATORIA'],
                        'PUNITORIO' => $operatoria['TASA_INTERES_POR_PUNITORIOS'],
                        'SUBSIDIO' => $operatoria['TASA_SUBSIDIADA']
                        );
                }
                $this->_db->select('*');
                $this->_db->where("ID_OPERATORIA = " . $id_operatoria . " AND FECHA <= '$fecha'");
                $this->_db->order_by("FECHA", "ASC");
                if ($cambiotasas = $this->_db->get_tabla("fid_operatoria_cambiotasas")) {
                    foreach ($cambiotasas as $tasa) {
                        if ($tasa['COMPENSATORIO'] >= 0) {
                            $tasas['COMPENSATORIO'] = $tasa['COMPENSATORIO'];
                        }
                        if ($tasa['MORATORIO'] >= 0) {
                            $tasas['MORATORIO'] = $tasa['MORATORIO'];
                        }
                        if ($tasa['PUNITORIO'] >= 0) {
                            $tasas['PUNITORIO'] = $tasa['PUNITORIO'];
                        }
                        if ($tasa['SUBSIDIO'] >= 0) {
                            $tasas['SUBSIDIO'] = $tasa['SUBSIDIO'];
                        }
                    }
                }
            }
            
        }
        
        return $tasas;
    }
  
}