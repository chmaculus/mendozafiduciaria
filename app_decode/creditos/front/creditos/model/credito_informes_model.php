<?php

class credito_informes_model extends credito_model {

    function get_credito_from_id($id) {
        $this->_db->where("ID = " . $id);
        $credito = $this->_db->get_row("fid_creditos");
    }

    function get_cuotas_credito() {
        $cuotas = $this->_cuotas;

        $variaciones = $this->_variaciones;
        $pagos = $this->_pagos;
        $primer_desembolso = array();


        //     print_array($variaciones);
        //     print_array($cuotas);
        //     die();

        foreach ($variaciones as $variacion) {
            if ($variacion['TIPO'] == 1) {
                $primer_desembolso = $variacion;
                break;
            }
        }

        $arr_result = array();

        $x = 0;

        $ultima_cuota = array();
        foreach ($cuotas as $cuota) {
            $ultima_cuota = $cuota;
            $index = $cuota['ID'];

            if ($x == 0 && $primer_desembolso) {
                $cuotas[$index]['FECHA_INICIO'] = $primer_desembolso['FECHA'];
            }
            $saldo_arr = $this->_get_saldo_capital($cuota['FECHA_INICIO'] + 1000, true);

            $cuotas[$index]['SALDO'] = $saldo_arr['SALDO'];

            $cuotas[$index]['SALDO_CAPITAL_TEORICO'] = $saldo_arr['SALDO_TEORICO'];
            $cuotas[$index]['AMORTIZACION_TEORICA'] = $saldo_arr['AMORTIZACION_CUOTA'];
            $cuotas[$index]['CAPITAL_CUOTA'] = $saldo_arr['AMORTIZACION_CUOTA'];

            $segmentos = array();
            foreach ($cuota['CHILDREN'] as $child) {
                if (
                // $child['FECHA'] >= $cuota['FECHA_INICIO'] &&
                        $child['FECHA'] <= $cuota['FECHA_VENCIMIENTO']
                ) {
                    if (!isset($variaciones[$child['_ID_VARIACION']]['TIPO'])) {
                        
                    }

                    if ($variaciones[$child['_ID_VARIACION']]['TIPO'] == 0) {
                        $variaciones[$child['_ID_VARIACION']]['ASOC'] = array();
                    }
                    if ($variaciones[$child['_ID_VARIACION']]['TIPO'] != 5000) {
                        if (!isset($variaciones[$child['_ID_VARIACION']]['ASOC'])) {
                            
                        } else {
                            $child['ASOC'] = $variaciones[$child['_ID_VARIACION']]['ASOC'];
                        }
                        $segmentos[] = $child;
                    }
                }
            }

            //se agrega un primer segmento virtual que representa el primer desembolso
            if ($x == 0 && false) {
                $tmp_segmento = $primer_desembolso;
                $tmp_segmento['_ID_VARIACION'] = $primer_desembolso['ID'];
                $tmp_segmento['FECHA_VENCIMIENTO'] = $primer_desembolso['FECHA'];
                $tmp_segmento['FECHA_INICIO'] = $primer_desembolso['FECHA'];
                $tmp_segmento['INT_COMPENSATORIO_IVA'] = 0;
                $tmp_segmento['INT_COMPENSATORIO'] = 0;
                $tmp_segmento['ASOC'] = $variaciones[$child['_ID_VARIACION']]['ASOC'];
            }

            $this->_db->where("FECHA >= " . $cuota['FECHA_INICIO']);
            $this->_db->where("FECHA <= " . $cuota['FECHA_VENCIMIENTO']);
            $gastos = $this->get_tabla_gastos();

            $cuotas[$index]['SEGMENTOS'] = array();
            $desde = $cuota['FECHA_INICIO'];
            if ($segmentos) {
                if ($x == 0) {
                    $desde = $primer_desembolso['FECHA'];
                } else {
                    $desde = $segmentos[0]['FECHA_INICIO'];
                }
            }
            for ($i = 0; $i < count($segmentos); $i++) {

                $segmento = $segmentos[$i];
                $tipo = "";
                $segmento['MONTO'] = 0;

                switch ($segmento['TIPO']) {
                    //DESEMBOLSO
                    case 1:
                        $tipo = 'Desembolso';
                        $segmento['MONTO'] = 0;
                        if ($segmento['ASOC']) {
                            $segmento['MONTO'] = "$" . $segmento['ASOC']['MONTO'];
                        }
                        break;
                    //CAMBIO TASA
                    case 2:
                        $tipo = 'Cambio Tasa';
                        $segmento['MONTO'] = 0;
                        if ($segmento['ASOC']) {
                            $segmento['MONTO'] = "%" . $segmento['ASOC']['COMPENSATORIO'];
                        }
                        break;

                    //RECUPERO
                    case 3:
                        $tipo = 'Recupero';
                        $segmento['MONTO'] = 0;
                        if ($segmento['ASOC']) {
                            $total_monto = 0;
                            foreach ($segmento['ASOC'] as $valor) {
                                $total_monto += $valor['MONTO'];
                            }
                            $segmento['MONTO'] = "$" . $total_monto;
                        }

                        break;
                }

                $hasta = $segmento['FECHA_VENCIMIENTO'];
                $segmento['FECHA_INICIO'] = $desde;
                $segmento['DIAS_TRANSCURRIDOS'] = floor(($hasta - $desde) / (3600 * 24));
                $segmento['TIPO'] = $tipo;

                $desde = $hasta;

                $cuotas[$index]['SEGMENTOS'][] = $segmento;
            }
            unset($cuotas[$index]['CHILDREN']);
            $cuotas[$index]['GASTOS'] = $gastos;
            $arr_result[] = $cuotas[$index];


            $x++;
        }
        //  $ultima_cuota = end($cuotas);


        $variaciones = array();
        foreach ($this->_variaciones as $variacion) {
            if (isset($ultima_cuota['FECHA_VENCIMIENTO'])) {
                if ($variacion['FECHA'] > $ultima_cuota['FECHA_VENCIMIENTO']) {
                    $variaciones[] = $variacion;
                }
            }
        }
        for ($i = 0; $i < count($variaciones); $i++) {

            $tipo = 'Recupero';
            $variaciones[$i]['TOTAL'] = 0;
            $variaciones[$i]['TIPO'] = -1;
            if ($variaciones[$i]['ASOC']) {
                $total_monto = 0;
                foreach ($variaciones[$i]['ASOC'] as $valor) {
                    $total_monto += $valor['MONTO'];
                    $tipo = 'Recupero';
                }
                $variaciones[$i]['TOTAL'] = $total_monto;
                $variaciones[$i]['TIPO'] = $tipo;
            }
        }


        $res['RESULT'] = $arr_result;
        $res['VARIACIONES'] = $variaciones;
        $res['PRIMER_DESEMBOLSO'] = $primer_desembolso;
        return $res;
    }

    function get_pago_evento($id_variacion) {
        $pagos = $this->_variaciones[$id_variacion]['ASOC'];

        for ($i = 0; $i < 13; $i++) {
            $pago_rtn[$i] = 0;
        }
        foreach ($pagos as $pago) {
            $pago_rtn[$pago['ID_TIPO']] = $pago['MONTO'];
        }
        return $pago_rtn;
    }

    function get_pago($id_variacion) {
        $variacion = $this->_variaciones[$id_variacion];
        $arr_pago = array();

        foreach ($this->_cuotas as $cuota) {
            $cuotas[] = $cuota;
        }

        $variacion_inicial = reset($this->_variaciones);

        $cantidad_cuotas = $variacion_inicial['CANTIDAD_CUOTAS'];
        $noimputado = array();

        $pagos_gastos = array();


        $fecha_imputacion = date("d/m/Y", $variacion['FECHA']);
        $fecha_carga = date("d/m/Y", $variacion['FECHA']);
        $recivo = "111111-111";

        $total_cuota = 0;
        $total_gastos = 0;
        if ($variacion['TIPO'] == 3) {

            $pagos = $variacion['ASOC'];


            foreach ($pagos as $pago) {
                $indice = $cantidad_cuotas - $pago['CUOTAS_RESTANTES'];

                $signo = $pago['VENCIDO'] == 1 ? "RESTA" : "SUMA";
                switch ($pago['ID_TIPO']) {
                    case 1:
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_PUNITORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_PUNITORIO']['DETALLE'] = "IVA Int. Punitorio";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['IVA_PUNITORIO']['MONTO'];
                        break;
                    case 2:
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_MORATORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_MORATORIO']['DETALLE'] = "IVA Int. Moratorio";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['IVA_MORATORIO']['MONTO'];
                        break;
                    case 3:
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_COMPENSATORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_COMPENSATORIO']['DETALLE'] = "IVA Int. Compensatorio";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['IVA_COMPENSATORIO']['MONTO'];
                        break;
                    case 4:
                        $cuotas[$indice]['PAGOS'][$signo]['PUNITORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['PUNITORIO']['DETALLE'] = "Int. Punitorios";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['PUNITORIO']['MONTO'];
                        break;
                    case 5:
                        $cuotas[$indice]['PAGOS'][$signo]['MORATORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['MORATORIO']['DETALLE'] = "Int. Moratorios";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['MORATORIO']['MONTO'];
                        break;
                    case 6:
                        $cuotas[$indice]['PAGOS'][$signo]['COMPENSATORIO']['DETALLE'] = "Int. Compensatorios";
                        $cuotas[$indice]['PAGOS'][$signo]['COMPENSATORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['COMPENSATORIO']['MONTO'];
                        break;
                    case 7:
                        $cuotas[$indice]['PAGOS'][$signo]['CAPITAL']['DETALLE'] = "CAPITAL";
                        $cuotas[$indice]['PAGOS'][$signo]['CAPITAL']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['CAPITAL']['MONTO'];
                        break;
                    case 10:
                        $noimputado['DETALLE'] = "NO IMPUTADO";
                        $noimputado['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $total_cuota += $noimputado['MONTO'];
                        break;
                    case 8:

                        $pago_gasto = $this->_db->get_row("fid_creditos_gastos", "ID = " . $pago['CUOTAS_RESTANTES']);
                        $total_gastos += $pago['MONTO'];
                        $pagos_gastos[] = array("CONCEPTO" => $pago_gasto['CONCEPTO'], "MONTO" => $pago['MONTO']);

                        for ($i = 0; $i < count($cuotas); $i++) {

                            if ($pago_gasto['FECHA'] >= $cuotas[$i]['FECHA_INICIO'] &&
                                    $pago_gasto['FECHA'] <= $cuotas[$i]['FECHA_VENCIMIENTO']) {

                                $indice = $cantidad_cuotas - $cuotas[$i]['CUOTAS_RESTANTES'];
                                $cuotas[$indice]['PAGOS']['GASTO']['DETALLE'] = "GASTOS";
                                $cuotas[$indice]['PAGOS']['GASTO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);

                                break;
                            }
                        }
                        break;
                }

                $cuotas[$indice]['NUM'] = $indice + 1;
                $arr_pago = array();
            }
        }
        $cuotas[0]['RESUMEN'] = array(
            "TOTAL_CUOTAS" => $total_cuota,
            "TOTAL_GASTOS" => $total_gastos,
            "TOTAL" => $total_cuota + $total_gastos,
            "FECHA_IMPUTACION" => $fecha_imputacion,
            "FECHA_CARGA" => $fecha_carga,
            "RECIVO" => $recivo
        );
        $cuotas[0]['NOIMPUTADO'] = $noimputado;
        $cuotas[0]['GASTOS'] = $pagos_gastos;
        return $cuotas;
    }

    function evolucion_cuota($cuota_id, $fecha) {

        $cuota = $this->_cuotas[$cuota_id];

        $cuotas_restantes = $cuota['CUOTAS_RESTANTES'];
        $cantidad_cuotas = count($this->_cuotas);

        $index = $cantidad_cuotas - $cuotas_restantes;

        $eventos = array();
        $eventos[] = array("FECHA" => $cuota['FECHA_VENCIMIENTO'], "TIPO" => "Vencimiento", "T" => 1, "PAGOS" => array());
        foreach ($this->_variaciones as $variacion) {
            //print_array($variacion);
            if ($variacion['TIPO'] == 3) {

                foreach ($variacion['ASOC'] as $asoc) {
                    if ($asoc['CUOTAS_RESTANTES'] == $cuota['CUOTAS_RESTANTES']) {

                        $eventos[] = array("FECHA" => $variacion['FECHA'], "TIPO" => "Cobranza", "T" => 3, "PAGOS" => $this->get_pago_evento($variacion['ID']));
                        break;
                    }
                }
            }
        }

        function cmp($a, $b) {
            return $a["FECHA"] > $b["FECHA"];
        }

        usort($eventos, "cmp");

        $pagos = array();
        foreach ($eventos as $evento) {
            $this->set_fecha_calculo($fecha);
            if ($evento['FECHA'] <= $fecha) {
                $this->set_fecha_actual($evento['FECHA']);

                if ($evento['T'] == 1) {
                    $this->generar_evento(array(), true, $evento['FECHA']);
                }

                $tmp = $this->get_deuda($evento['FECHA']);
                $pagos[] = array("VALORES" => $tmp['cuotas'][$index], "PAGOS" => $evento['PAGOS'], "FECHA" => $evento['FECHA'], "T" => $evento['T'], "TIPO" => $evento['TIPO']);
            }
        }

        return $pagos;
    }

    function get_monto_credito() {
        return $this->_total_credito;
    }

    function obtener_tasas() {
        $tasas = array();
        $fecha_posterior = time();

        $variacion_inicial = reset($this->_variaciones);
        $tmp = array();
        $tmp = $variacion_inicial;
        $tmp['FECHA_DESDE'] = $variacion_inicial['FECHA'];
        $tmp['FECHA_HASTA'] = $fecha_posterior;
        $tasas[] = $tmp;

        foreach ($this->_variaciones as $variacion) {
            if ($variacion['TIPO'] == 2) {

                if (count($tasas)) {
                    $ultima_tasa = end($tasas);
                    $ultima_tasa['HASTA'] = $variacion['FECHA'];
                }
                $tmp = array();
                $tmp = $variacion;
                $tmp['FECHA_DESDE'] = $variacion['FECHA'];
                $tmp['FECHA_HASTA'] = $fecha_posterior;

                $tasas[] = $tmp;

                $fecha_posterior = $variacion['FECHA'];
            }
        }
        return $tasas;
    }
    
    public function getReporteCreditos() {
        $_creditos = $this->get_creditos_reporte();
        
        if ($_creditos) {
            $creditos_moratorios = array();
            $fecha = time();
            foreach ($_creditos as $credito) {
                $this->set_credito_active($credito['ID']);
                $creditos_moratorios[$credito['ID']] = $credito;
                $creditos_moratorios[$credito['ID']]['DESEMBOLSO'] = $this->_get_desembolso();
                $creditos_moratorios[$credito['ID']]['PAGOS'] = $this->get_moratorias();
                $creditos_moratorios[$credito['ID']]['CUOTAS'] = $this->get_cuotas();
            }
            
            return $creditos_moratorios;
        }
        
        return FALSE;
    }
    /*
    public function getCreditosMoratorios() {
        $_creditos = $this->get_creditos_moratorios();
        
        if ($_creditos) {
            $creditos_moratorios = array();
            $fecha = time();
            foreach ($_creditos as $cr) {
                $credito_id = $cr['ID'];
                
                $this->clear();
                $this->set_credito_active($credito_id);
                $version = $this->set_version_active();
                
                $this->set_fecha_actual($fecha);
                $this->set_fecha_calculo();

                $this->renew_datos();
                
                $desembolsos = $this->get_desembolsos(0);
        
                
                $desembolsado = 0;
                foreach($desembolsos as $desembolso){
                    $desembolsado += $desembolso['MONTO'];
                }
                
                if ($desembolsado == 0) {
                    continue;
                }
                
                $this->save_last_state(false);
                $this->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);
                $this->generar_evento(array(), true, $fecha);
                $ret_deuda = $this->get_deuda($fecha, true );
                
                if (isset($ret_deuda['cuotas'])) {
                    
                    $moratorias = array();
                    foreach ($ret_deuda['cuotas'] as $cc) {
                        if ($cc['_INFO']['HASTA'] < $fecha) {
                            $arr = array(
                                'MONTO' => $cc['MORATORIO']['TOTAL'],
                                'IVA' => $cc['IVA_MORATORIO']['TOTAL'],
                                'PAGO' => $cc['MORATORIO']['PAGOS'] + $cc['IVA_MORATORIO']['PAGOS'],
                                'FECHA' => $cc['_INFO']['HASTA']
                            );
                            $moratorias[] = $arr;
                        }
                        
                    }
                    
                    if (count($moratorias) > 0) {
                        $postulantes = array();
                        $_postulantes = $this->get_clientes_credito();
                        if ($_postulantes) {
                            foreach ($_postulantes as $ps) {
                                $postulantes[] = $ps['RAZON_SOCIAL'];
                            }
                        }
                        
                        if (count($postulantes) > 0) {
                            $postulantes = implode("<br>", $postulantes);
                        } else {
                            $postulantes = "";
                        }
                        
                        $creditos_moratorios[$credito_id]['ID_CREDITO'] = $credito_id;
                        $creditos_moratorios[$credito_id]['INTERES_VTO'] = $cr['INTERES_VTO'];
                        $creditos_moratorios[$credito_id]['MONTO'] = $cr['MONTO_CREDITO'];
                        $creditos_moratorios[$credito_id]['POSTULANTES'] = $postulantes;
                        $creditos_moratorios[$credito_id]['MORATORIAS'] = $moratorias;
                        
                    }
                }
            }
            
            if (count($creditos_moratorios) > 0) {
                return $creditos_moratorios;
            }
        }
        
        return FALSE;
    }
*/    
}
    
?>
