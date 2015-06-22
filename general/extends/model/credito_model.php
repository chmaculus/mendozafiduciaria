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
    var $_bsave = true;
    var $_ultimo_vencimiento_subsidio = 0;
    var $_tipo_devengamiento = TIPO_DEVENGAMIENTO_AUTO;
    var $_forzar_no_devengamiento = false;
    var $_estado_credito = ESTADO_CREDITO_NORMAL;
    var $_blog = false;
    var $_periodicidad = 60;
    
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
        log_this("sql.txt", "aca\n");
        return $items;
    }

    function _get_versiones_recursive($credito = 0, $parent = 0) {
        $this->_db->where("ID_CREDITO_VERSION = " . $credito);
        $this->_db->where("PARENT_ID = " . $parent);
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
    function get_deuda($fecha = false, $renew = true) {



        $IVA = IMP_IVA;

        $fecha = !$fecha ? time() : $fecha;

        //obtenemos gastos
        $this->_db->where("ESTADO = 0");
        $this->_db->where("FECHA < " . $fecha);
        $gastos = $this->get_tabla_gastos();
        
        
        $variacion_inicial = reset($this->_variaciones);
        
        $arr_deuda = array("gastos" => array(), "cuotas" => array(), "rtn" => 1, "fecha_reimputacion" => 0);

        //GASTOS
        $gastos_arr = array();
        foreach ($gastos as $gasto) {
            $this->_db->where("ID_TIPO = 8 AND CUOTAS_RESTANTES = " . $gasto['ID']);
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

        if ($renew) {
            $this->get_segmentos_cuota();
            $this->renew_datos($fecha);
        }

        $cuotas = $this->_cuotas;

        foreach ($cuotas as $cuota) {
            $pago = $this->_pagos[$cuota['CUOTAS_RESTANTES']];
            
            //buscamos los gastos correspondientes a la fecha de la cuota
            $arr_gastos = array();
            for ($g = 0; $g < count($gastos_arr); $g++) {
                if ($gastos_arr[$g]['ROW']['FECHA'] >= $cuota['FECHA_INICIO'] &&
                        $gastos_arr[$g]['ROW']['FECHA'] <= $cuota['FECHA_VENCIMIENTO']) {
                    unset($gastos_arr['ROW']);
                    $arr_gastos = $gastos_arr[$g];

                    break;
                }
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
                
                if ($cuota['ESTADO'] == PLAZO_SUBSIDIO_VENCIDO) {
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
                
                if($arr_compensatorio['SALDO'] < 0.5) $arr_compensatorio['SALDO'] = 0;
                
                $arr_compensatorio_subsidio = array(
                    "TOTAL" => $COMPENSATORIO_SUBSIDIO, //$subsidio
                    "PAGOS" => 0, //$subsidio
                    "SALDO" => 0, //$subsidio
                    "TIPO" => 11, //$subsidio
                );

                //saldo de capital de la cuota + saldo de los intereses compensatorio e impuestos no cancelados
                $SALDO_CUOTA = $arr_capital['SALDO'] + $arr_compensatorio['SALDO'] + $arr_iva_compensatorio['SALDO'];
                
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
            if ($cuota['FECHA_VENCIMIENTO'] < $fecha) {

                $arr_deuda['cuotas'][] = array(
                    "GASTOS" => $arr_gastos,
                    "IVA_PUNITORIO" => $arr_iva_punitorio,
                    "IVA_MORATORIO" => $arr_iva_moratorio,
                    "IVA_COMPENSATORIO" => $arr_iva_compensatorio,
                    "PUNITORIO" => $arr_punitorio,
                    "MORATORIO" => $arr_moratorio,
                    "COMPENSATORIO" => $arr_compensatorio,
                    "CAPITAL" => $arr_capital,
                    "ID" => $cuota['ID'],
                    "CUOTAS_RESTANTES" => $cuota['CUOTAS_RESTANTES'],
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
                        "TOTAL_PAGAR" => $SALDO_CUOTA + $arr_iva_punitorio['SALDO'] + $arr_iva_moratorio['SALDO'] + $arr_punitorio['SALDO'] + $arr_moratorio['SALDO']
                    )
                    );
                if ($cuota['ESTADO'] == PLAZO_SUBSIDIO_VENCIDO && $cuota['INT_COMPENSATORIO_SUBSIDIO'] > 0) {

                    $arr_deuda['rtn'] = PLAZO_SUBSIDIO_VENCIDO;
                    $arr_deuda['fecha_reimputacion'] = $cuota['FECHA_INICIO'] + 1;
                }
            } else {
                
                $arr_deuda['cuotas'][] = array(
                    "GASTOS" => $arr_gastos,
                    "IVA_PUNITORIO" => $arr_iva_punitorio,
                    "IVA_MORATORIO" => $arr_iva_moratorio,
                    "PUNITORIO" => $arr_punitorio,
                    "MORATORIO" => $arr_moratorio,
                    "IVA_COMPENSATORIO" => $arr_iva_compensatorio,
                    "COMPENSATORIO" => $arr_compensatorio,
                    "CAPITAL" => $arr_capital,
                    "ID" => $cuota['ID'],
                    "CUOTAS_RESTANTES" => $cuota['CUOTAS_RESTANTES'],
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
                        "TOTAL_PAGAR" => $SALDO_CUOTA
                    )
                );
            }
        }

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
        $cred = $this->_id_credito;
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
    function get_segmentos_cuota($fecha = NO_FECHA, $renew = true) {

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


        $fecha_get = $fecha;
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
            foreach ($this->_variaciones as $variacion) {

                if ($variacion['FECHA'] >= $FECHA_INICIO_VARIACION && $variacion['ESTADO'] > -1 && $variacion['FECHA'] <= $fecha_get) {

                    $variaciones[] = $variacion;
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

            if ($subcuotas) {

                $INTERES_COMPENSATORIO = 0;
                $IVA_INTERES_COMPENSATORIO = 0;
                $bfin_segmento = false;

                $INT_SUBSIDIO_ACUMULADO = 0;
                $IVA_INT_SUBSIDIO_ACUMULADO = 0;


                for ($i = 0; $i < count($subcuotas) && !$bfin_segmento; $i++) {
                    unset($subcuotas[$i]['CHILDREN']);
                    unset($subcuotas[$i]['TMP']['VARIACION']['ASOC']);
                    $tmp = $subcuotas[$i];

                    $bprimer_segmento = false;
                    if ($i == 0) {
                        $bprimer_segmento = true;
                    }

                    //si es ultimo segmento
                    $bultimo_segmento = false;
                    if (($i + 1) == count($subcuotas)) {
                        $bultimo_segmento = true;
                    }

                    //no es primera cuota y es el primer segmento, arrancamos el segmento en el inicio de cuota
                    if ($bprimer_segmento) {

                        $tmp_fecha_inicio = $cuota['FECHA_INICIO'];
                        $INT_SUBSIDIO = $primera_variacion['POR_INT_SUBSIDIO'];
                        $INTERES_COMPENSATORIO_VARIACION = $primera_variacion['POR_INT_COMPENSATORIO'];
                        $firstVariacion = reset($this->_variaciones);
                        $PERIODICIDAD_TASA_VARIACION = $firstVariacion['PERIODICIDAD_TASA'];

                        $PLAZO_PAGO = $primera_variacion['PLAZO_PAGO'];
                    } else {
                        $tmp_fecha_inicio = $subcuotas[$i - 1]['TMP']['VARIACION']['FECHA'];

                        $INT_SUBSIDIO = $subcuotas[$i - 1]['TMP']['VARIACION']['POR_INT_SUBSIDIO'];

                        $INTERES_COMPENSATORIO_VARIACION = $subcuotas[$i - 1]['TMP']['VARIACION']['POR_INT_COMPENSATORIO'];

                        $firstVariacion = reset($this->_variaciones);
                        $PERIODICIDAD_TASA_VARIACION = $firstVariacion['PERIODICIDAD_TASA'];
                        ;
                        //$PERIODICIDAD_TASA_VARIACION = $subcuotas[$i - 1]['TMP']['VARIACION']['PERIODICIDAD_TASA'];
                        $PLAZO_PAGO = $subcuotas[$i - 1]['TMP']['VARIACION']['PLAZO_PAGO'];
                    }


                    $tmp['FECHA_INICIO_REAL'] = $tmp_fecha_inicio;
                    $tmp['FECHA_VENCIMIENTO_REAL'] = $subcuotas[$i]['TMP']['VARIACION']['FECHA'];


                    $fecha_inicio = $tmp_fecha_inicio;
                    $fecha_vencimiento = $subcuotas[$i]['TMP']['VARIACION']['FECHA'];

                    //fecha vencimiento de segmento: si es el ultimo segmento la fecha de vencimiento
                    //es la misma de la cuota, de lo contrario es la siguiente variacion
                    //segmentos anteriores al ultimo
                    $tmp_fecha_inicio = 0;

                    $tmp['FECHA_INICIO'] = $fecha_inicio;
                    $tmp['FECHA_VENCIMIENTO'] = $fecha_vencimiento;
                    
                    
                    $fecha_saldo_capital = $fecha_vencimiento - 1;

                    /*if ($fecha_saldo_capital > $cuota['FECHA_VENCIMIENTO']) {
                        break;
                    }*/
                    
                    $capital_arr = $this->_get_saldo_capital($fecha_saldo_capital, true, false);
                    $SALDO_CAPITAL = $capital_arr['BASE_CALCULO'];
                    $tmp['_PARENT'] = $cuota['ID'];
                    $tmp['_ESTADO'] = 5;
                    if ($bprimer_segmento) {
                        $tmp['_ID_VARIACION'] = $primera_variacion['ID'];
                    } else {
                        $tmp['_ID_VARIACION'] = $subcuotas[$i - 1]['TMP']['VARIACION']['ID'];
                    }
                    $tmp['_ID_VARIACION'] = $subcuotas[$i]['TMP']['VARIACION']['ID'];

                    //si es el ultimo segmento, y la fecha de vencimiento de este segmento es menor a la fecha 
                    //de vencimiento de la cuota, se evalua la fecha de envio
                    //si la fecha de envio es menor a la fecha de vencimiento del segmento
                    $fecha_vencimiento_rango = $tmp['FECHA_VENCIMIENTO'];

                    $rango_tmp = ($fecha_vencimiento_rango - $tmp['FECHA_INICIO']) / (24 * 60 * 60);
                    $rango = $rango_tmp < 0 ? 0 : round($rango_tmp);
                    

                    $tmp['CAPITAL_CUOTA'] = $capital_arr['AMORTIZACION_CUOTA'];
                    $tmp['POR_INT_COMPENSATORIO'] = 0;
                    $tmp['INT_COMPENSATORIO'] = 0;
                    $tmp['INT_COMPENSATORIO_SUBSIDIO'] = 0;
                    
                    $INT_MORATORIO = 0;
                    $INT_PUNITORIO = 0;

                    //si activa = -2 no se calculan los intereses compensatorios
                    //sucede cuando los segmentos se encuentran dentro de la cuota pero 
                    //a futuro de del vencimiento, cuando la cuota no esta cancelada y suceden eventos
                    //que modifican el saldo de esta cuota (mas adelante en el vencimiento)
                    if ($tmp['_ACTIVA'] != -2) {


                        $interes_subsidio = 0;

                        //interese compuesto
                        if ($PERIODICIDAD_TASA_VARIACION > 0) {

                            $tmp['POR_INT_COMPENSATORIO'] = $rango / $PERIODICIDAD_TASA_VARIACION;
                            $interes = $this->_calcular_interes($SALDO_CAPITAL, $rango, $INTERES_COMPENSATORIO_VARIACION, $PERIODICIDAD_TASA_VARIACION, $cuota['CUOTAS_RESTANTES'] == 16);
                            $interes_subsidio = $this->_calcular_interes($SALDO_CAPITAL, $rango, $INT_SUBSIDIO, $PERIODICIDAD_TASA_VARIACION, $cuota['CUOTAS_RESTANTES'] == 16);

                            
                            $tmp['INT_COMPENSATORIO_SUBSIDIO'] = $interes_subsidio;
                            if (!$INTERES_COMPENSATORIO) {
                                $tmp['INT_COMPENSATORIO'] = $interes;
                            }
                            
                           
                        }
                    } else { //interes simple
                        $tmp['POR_INT_COMPENSATORIO'] = ($INTERES_COMPENSATORIO_VARIACION / $this->_interese_compensatorio_plazo) * $rango;
                        if (!$INTERES_COMPENSATORIO) {
                            $tmp['INT_COMPENSATORIO'] = $INTERES_COMPENSATORIO_VARIACION * $tmp['CAPITAL_CUOTA'] / 100;
                        }
                    }
                    

                    $INT_SUBSIDIO_ACUMULADO += $interes_subsidio;
                    $IVA_INT_SUBSIDIO_ACUMULADO += ($interes_subsidio * $tmp['TMP']['VARIACION']['IVA']);

                    $INTERES_COMPENSATORIO += ($tmp['INT_COMPENSATORIO']);
                    $IVA_INTERES_COMPENSATORIO += ($tmp['INT_COMPENSATORIO'] * $tmp['TMP']['VARIACION']['IVA']);

                    //sucede cuando es la ultima cuota y los segmentos se encuentran por encima de la fecha de vencimiento
                    //o cuando es no es la ultima cuota pero la fecha de calculo es superior a la fecha de vencimiento
                    $not_enter = false;

                    //SEGMENTO DENTRO DE LA CUOTA y ES ULTIMA CUOTA
                    if ($tmp['_ACTIVA'] != -2 && $bultima_cuota)
                        $not_enter = true;

                    //CUOTA CANCELADA
                    /*     if ($cuota['ESTADO'] == 1)
                      $not_enter = true; */

                    //FECHA DE FINALIZACION DEL SEGMENTO es igual a la FECHA DE INICIO DE LA CUOTA
                    if ($fecha_vencimiento == $fecha_inicio) {
                        $not_enter = true;
                    }



                    if ($fecha_vencimiento == $cuota['FECHA_VENCIMIENTO'] && $tmp['_ACTIVA'] != -2) {
                        $not_enter = true;
                    }


                    if (!$not_enter && ($tmp['_ACTIVA'] == -2 || ($fecha_vencimiento == $cuota['FECHA_VENCIMIENTO'] && $fecha_get > $fecha_vencimiento) )) {
                        $fecha_calculo = $fecha_inicio;

                        //se calculan todos los pagos hasta la fecha de ingreso al metodo
                        $pagos_arr = $this->_get_pagos_tipo($fecha_calculo, true);

                        $pagos = $pagos_arr[$cuota['CUOTAS_RESTANTES']];

                        $total = $pagos[PAGO_CAPITAL] + $pagos[PAGO_IVA_COMPENSATORIO] + $pagos[PAGO_COMPENSATORIO];


                        $FIN_PLAZO_PAGO_SUBSIDIO = $cuota['FECHA_VENCIMIENTO'] + ($PLAZO_PAGO * 60 * 60 * 24);
                        //evaluamos si se han vencido los plazos para cumplir con el subsidio

                        $tmp['ESTADO'] = 0;
                        if ($fecha_get > $FIN_PLAZO_PAGO_SUBSIDIO && ($IVA_INT_SUBSIDIO_ACUMULADO + $INT_SUBSIDIO_ACUMULADO > 0)) {
                            $tmp['ESTADO'] = PLAZO_SUBSIDIO_VENCIDO;
                        }

                        //SE HACE LA ACTUALIZACION DE LOS VALORES DE LA BONFIICACION.
                        $INTERES_COMPENSATORIO2 = $INTERES_COMPENSATORIO - $INT_SUBSIDIO_ACUMULADO;
                        $IVA_INTERES_COMPENSATORIO2 = $IVA_INTERES_COMPENSATORIO - $IVA_INT_SUBSIDIO_ACUMULADO;

                        //obtenemos la amorizacion de cuota correspondiente a la fecha de vencimiento
                        $capital_arr = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO'] - 1, true, $cuota['CUOTAS_RESTANTES'] == 4);

                        $SALDO_CUOTA = $capital_arr['AMORTIZACION_CUOTA'] + $INTERES_COMPENSATORIO2 + $IVA_INTERES_COMPENSATORIO2 - $total;
                        


                        //si la cuota no ha llegado a estado 1 y supero la fecha de vencimiento de subsidio
                        //pasa a estado 5 
                        $cuota['ESTADO'] = $cuota['ESTADO'] == 1 ? 1 : 0;
                        $tmp['ESTADO'] = 0;


                        if ($fecha_get > $FIN_PLAZO_PAGO_SUBSIDIO) {

                            $this->_ultimo_vencimiento_subsidio = $FIN_PLAZO_PAGO_SUBSIDIO;
                            $estado = 5;
                            $cuota['ESTADO'] = $estado;
                            $tmp['ESTADO'] = $estado;
                        } else {
                            if ($cuota['CUOTAS_RESTANTES'] == 2) {
                                
                            }
                        }

                        //si llegamos a 0 en saldo de cuota no se siguen procesando los segmentos siguietnes o si el rango
                        //es 0
                        if (round($SALDO_CUOTA) == 0 || round($rango) == 0) {
                            $bfin_segmento = true;

                            //si el saldo de cuota es 0
                            if (round($SALDO_CUOTA) == 0) {
                                $estado = 1;
                                $cuota['ESTADO'] = $estado;
                                $tmp['ESTADO'] = 1;
                                $this->_db->update("fid_creditos_cuotas", array("ESTADO" => $estado), "ID = " . $cuota['ID']);
                            }
                        } else {

                            //calculo de tipo de intereses moratorios y punitorios
                            //cambio 21112013 no importa si es microcredito o no, siempre se calculan int moratorios y punitorios
                            if ($this->_tipo_credito == TIPO_MICROCREDITO && false) {
                                $INT_MORATORIO = 0;
                                $INT_PUNITORIO = 0;
                            } else {
                                //el interes de calculo del interes moratorio no se realizar en caso 

                                $INT_MORATORIO = $SALDO_CUOTA * (1 + ($cuota['POR_INT_MORATORIO'] / 100) * $rango / $this->_interese_moratorio_plazo ) - $SALDO_CUOTA;
                                $INT_PUNITORIO = $SALDO_CUOTA * (1 + ($cuota['POR_INT_PUNITORIO'] / 100) * $rango / $this->_interese_punitorio_plazo) - $SALDO_CUOTA;
                            }
                        }
                    }

                    $tmp['INT_MORATORIO'] = $INT_MORATORIO;
                    $tmp['INT_PUNITORIO'] = $INT_PUNITORIO;

                    $tmp['INT_COMPENSATORIO_IVA_SUBSIDIO'] = $tmp['INT_COMPENSATORIO_SUBSIDIO'] * IMP_IVA;
                    $tmp['INT_COMPENSATORIO_IVA'] = $tmp['INT_COMPENSATORIO'] * IMP_IVA;

                    $tmp['DIAS'] = $rango;
                    $segmentos[] = $tmp;
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

                $cuota_segmento['POR_INT_COMPENSATORIO'] = $segmento['TMP']['VARIACION']['POR_INT_COMPENSATORIO'];
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
                $cuota_segmento['INT_COMPENSATORIO_IVA_SUBSIDIO'] = $segmento['INT_COMPENSATORIO_SUBSIDIO'] * IMP_IVA;
                $cuota_segmento['INT_COMPENSATORIO_IVA'] = $segmento['INT_COMPENSATORIO'] * IMP_IVA;

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
            if ($cuota_item['FECHA_VENCIMIENTO'] <= $fecha) {
                $segmentos[] = $cuota_item;
            }
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

            $capital_arr = $this->_get_saldo_capital($cuota['FECHA_INICIO'], true);
            $SALDO_CAPITAL = $capital_arr['SALDO'];


            //calculamos saldo de capital
            $capital_arr = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO'], true);

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

            $capital_arr = $this->_get_saldo_capital($cuota['FECHA_INICIO'], true);
            $inicial = $capital_arr['INICIAL'];
            $desembolsos = $capital_arr['DESEMBOLSOS'];
            $SALDO_CAPITAL = $capital_arr['BASE_CALCULO'];

            $capital_arr = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO'], true);
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

            $int_compensatorio = $this->_calcular_interes($SALDO_CAPITAL, $rango, $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA'], $blog);
            $INT_SUBSIDIO = $variacion['POR_INT_SUBSIDIO'];

            $int_compensatorio_subsidio = $this->_calcular_interes($SALDO_CAPITAL, $rango, $INT_SUBSIDIO, $variacion['PERIODICIDAD_TASA'], $blog);
            $cuota['INT_COMPENSATORIO_SUBSIDIO'] = $int_compensatorio_subsidio;
            $cuota['INT_COMPENSATORIO_IVA_SUBSIDIO'] = $int_compensatorio_subsidio * $variacion['IVA'];
            $cuota['INT_COMPENSATORIO'] = $int_compensatorio;
            $cuota['INT_COMPENSATORIO_IVA'] = $int_compensatorio * $variacion['IVA'];


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
                "INT_COMPENSATORIO_IVA" => $interes_compensatorio * $variacion['IVA'],
                "INT_COMPENSATORIO_SUBSIDIO" => $interes_compensatorio_subsidio,
                "INT_COMPENSATORIO_IVA_SUBSIDIO" => $interes_compensatorio_subsidio * IMP_IVA,
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

        $fecha = $fecha ? $fecha : NO_FECHA;


        //MODIFICADO 15-08-2013
        $capital_inicial = 0; //$variacion_inicial_arr['CAPITAL'];

        $CUOTAS_GRACIA = $variacion_inicial_arr['CUOTAS_GRACIA'];

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

        $this->_db->delete("fid_creditos_pagos", "ID_CREDITO = " . $this->_id_credito . " AND ID_TIPO = " . PAGO_ADELANTADO);
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

                $or_where[] = "((cv.ID_VERSION = " . $version . " AND cv.FECHA >= " . $fecha . " AND cv.FECHA < " . $ultimo['FECHA_VERSION'] . ") )";
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
        $WHERE_FUNC = ' g.ID_VERSION  = (SELECT max( ID_VERSION ) FROM fid_creditos_gastos g2  ) ';
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
        $rtn = $this->_db->get_row("fid_creditos_version", "ID_CREDITO_VERSION = " . $id_credito);
        return $rtn;
    }

    function set_credito_active($id_credito) {
        $this->_id_credito = $id_credito;

        $this->_cuotas = array();
        $this->_variaciones = array();

        $row_credito = $this->_db->get_row("fid_creditos", "ID = " . $id_credito);
        if ($row_credito) {
            $this->_credito = $row_credito;
            $this->_interese_compensatorio_plazo = $row_credito['PLAZO_COMPENSATORIO'];
            $this->_interese_moratorio_plazo = $row_credito['PLAZO_MORATORIO'];
            $this->_interese_punitorio_plazo = $row_credito['PLAZO_PUNITORIO'];
            $this->_total_credito = $row_credito['MONTO_CREDITO'];
            $this->_estado_credito = $row_credito['CREDITO_ESTADO'];
            $this->_tipo_credito = $row_credito['TIPO_CREDITO'];
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
                        "FECHA_VERSION" => $fecha,
                        "ID_CREDITO_VERSION" => $this->_id_credito,
                        "TIPO_VERSION" => 1,
                        "DESCRIPCION_VERSION" => "Inicial",
                        "PARENT_ID" => 0,
                        "UPDATE_TIME" => time()
                    );
                    $id_version = $this->_db->insert("fid_creditos_version", $version);
                    $this->_id_version = $id_version;
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
        $this->_db->order_by("FECHA");
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
                "TIPO" => 3);
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

        for ($i = 1; $i < 11; $i++) {
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

    function get_desembolsos($fecha = NO_FECHA) {
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
                );
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

                if ($variacion['ASOC']) {
                    $total_monto = 0;
                    foreach ($variacion['ASOC'] as $valor) {
                        $total_monto += $valor['MONTO'];
                    }
                }

                $pagos[] = array(
                    "NUMERO" => $i++,
                    "MONTO" => $total_monto,
                    "FECHA" => date("d/m/Y", $variacion['FECHA']),
                    "ID_PAGO" => $variacion['ID']
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
            if ($variacion['FECHA'] >= $fecha && $variacion['TIPO'] == EVENTO_RECUPERO) {
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
                    'fecha' => $variacion['FECHA']
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

}

?>