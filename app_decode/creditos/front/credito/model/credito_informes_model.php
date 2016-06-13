<?php

class credito_informes_model extends credito_model {

    function get_credito_from_id($id) {
        $this->_db->where("ID = " . $id);
        $credito = $this->_db->get_row("fid_creditos");
    }

    function get_datos_credito() {
        $this->_db->where("ID = " . $this->_id_credito);
        $credito = $this->_db->get_row("fid_creditos");
        $id_operatoria = $credito['ID_OPERATORIA'];
        $id_fideicomiso = $credito['ID_FIDEICOMISO'];
        $primera_variacion = reset($this->_variaciones);

        //print_array($primera_variacion);
        //$credito['PERIODICIDAD'] = $this->_variaciones[0][];
        $credito['PLAZO_PAGO'] = $primera_variacion['PLAZO_PAGO'];
        $credito['PERIODICIDAD'] = $primera_variacion['PERIODICIDAD'];
        $credito['CANTIDAD_CUOTAS'] = $primera_variacion['CANTIDAD_CUOTAS'];
        $credito['CUOTAS_GRACIA'] = $primera_variacion['CUOTAS_GRACIA'];
        $credito['PERIODICIDAD_TASA'] = $primera_variacion['PERIODICIDAD_TASA'];


        if ($credito['ID_OPERACION'] > 0) {

            $operacion = $this->_db->get_row("fid_operaciones", "ID = " . $credito['ID_OPERACION']);


            if ($operacion) {
                $id_operatoria = $operacion['ID_OPERATORIA'];
                $id_fideicomiso = $operacion['ID_FIDEICOMISO'];
            }
        }

        $postulantes = array();
        $post_str = "";
        if (trim($credito['POSTULANTES'])) {
            $postulantes = $this->_db->get_tabla("fid_clientes", "id in (" . str_replace("|", ",", $credito['POSTULANTES']) . ")");
            $post_array = array();
            foreach ($postulantes as $postulante) {
                $post_array[] = $postulante['RAZON_SOCIAL'];
            }
            $post_str = implode(",", $post_array);
        }

        $fideicomiso = array();
        if ($id_fideicomiso > 0) {
            $fideicomiso = $this->_db->get_row("fid_fideicomiso", "id = " . $id_fideicomiso);
            if (!$fideicomiso){
                $fideicomiso = array("NOMBRE"=>' ('.$id_fideicomiso.') ');
            }            
        }

        $operatorias = array();
        if ($id_operatoria > 0) {
            $operatorias = $this->_db->get_row("fid_operatorias", "id = " . $id_operatoria);
            if (!$operatorias){
                $operatorias = array("NOMBRE"=>' ('.$id_operatoria.') ');
            }
        }

        $opciones = $this->get_creditos_opciones();
        
        $credito['POSTULANTES'] = $postulantes;
        $credito['FIDEICOMISO'] = $fideicomiso;
        $credito['OPERATORIAS'] = $operatorias;
        $credito['CONVENIO'] = isset($opciones['convenio'] )? $opciones['convenio']['VALOR']  : "";
        $credito['post_str'] = $post_str;



        return $credito;
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
                        echo "llega";
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

    function get_pago_evento($id_variacion, $cuotas_restantes=FALSE) {
        $pagos = $this->_variaciones[$id_variacion]['ASOC'];
        
        for ($i = 0; $i < 13; $i++) {
            $pago_rtn[$i] = 0;
        }
        foreach ($pagos as $pago) {
            if (!$cuotas_restantes || ($cuotas_restantes && $cuotas_restantes <= $pago['CUOTAS_RESTANTES'])) {
                $pago_rtn[$pago['ID_TIPO']] = $pago['MONTO'];
            }
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
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_PUNITORIO']['MONTO'] = round($pago['MONTO'], 2);
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_PUNITORIO']['DETALLE'] = "IVA Int. Punitorio";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['IVA_PUNITORIO']['MONTO'];
                        break;
                    case 2:
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_MORATORIO']['MONTO'] = round($pago['MONTO'], 2);
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_MORATORIO']['DETALLE'] = "IVA Int. Moratorio";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['IVA_MORATORIO']['MONTO'];
                        break;
                    case 3:
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_COMPENSATORIO']['MONTO'] = round($pago['MONTO'], 2);
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_COMPENSATORIO']['DETALLE'] = "IVA Int. Compensatorio";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['IVA_COMPENSATORIO']['MONTO'];
                        break;
                    case 4:
                        $cuotas[$indice]['PAGOS'][$signo]['PUNITORIO']['MONTO'] = round($pago['MONTO'], 2);
                        $cuotas[$indice]['PAGOS'][$signo]['PUNITORIO']['DETALLE'] = "Int. Punitorios";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['PUNITORIO']['MONTO'];
                        break;
                    case 5:
                        $cuotas[$indice]['PAGOS'][$signo]['MORATORIO']['MONTO'] = round($pago['MONTO'], 2);
                        $cuotas[$indice]['PAGOS'][$signo]['MORATORIO']['DETALLE'] = "Int. Moratorios";
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['MORATORIO']['MONTO'];
                        break;
                    case 6:
                        $cuotas[$indice]['PAGOS'][$signo]['COMPENSATORIO']['DETALLE'] = "Int. Compensatorios";
                        $cuotas[$indice]['PAGOS'][$signo]['COMPENSATORIO']['MONTO'] = round($pago['MONTO'], 2);
                        $total_cuota += $cuotas[$indice]['PAGOS'][$signo]['COMPENSATORIO']['MONTO'];
                        break;
                    case 7:
                        $cuotas[$indice]['PAGOS'][$signo]['CAPITAL']['DETALLE'] = "CAPITAL";
                        $cuotas[$indice]['PAGOS'][$signo]['CAPITAL']['MONTO'] = round($pago['MONTO'], 2);
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
                                $cuotas[$indice]['PAGOS']['GASTO']['MONTO'] = round($pago['MONTO'], 2);

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

                        $eventos[] = array("FECHA" => $variacion['FECHA'], "TIPO" => "Cobranza", "T" => 3, "PAGOS" => $this->get_pago_evento($variacion['ID'], $cuotas_restantes));
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

    function obtener_tasas($fecha = false) {
        $tasas = array();
        
        $fecha = !$fecha ? NO_FECHA : $fecha;
        
        $fecha_posterior = $fecha;

        $variacion_inicial = reset($this->_variaciones);
        
        if ($fecha_posterior <= $variacion_inicial['FECHA']){
            $fecha_posterior = $variacion_inicial['FECHA'];
        }
        
        $tmp = array();
        $tmp = $variacion_inicial;
        $tmp['FECHA_DESDE'] = $variacion_inicial['FECHA'];
        $tmp['FECHA_HASTA'] = $fecha_posterior ;
        $tasas[] = $tmp;

        foreach ($this->_variaciones as $variacion) {
            if ($variacion['TIPO'] == 2 && $variacion['FECHA'] <= $fecha) {

                if (count($tasas)) {
                    $ultima_tasa = end($tasas);
                    $ultima_tasa['HASTA'] = $variacion['FECHA'];
                }
                $tmp = array();
                $tmp = $variacion;
                $tmp['POR_INT_COMPENSATORIO'] = $variacion['ASOC']['COMPENSATORIO'];
                $tmp['POR_INT_SUBSIDIO'] = $variacion['ASOC']['SUBSIDIO'];
                $tmp['POR_INT_MORATORIO'] = $variacion['ASOC']['MORATORIO'];
                $tmp['POR_INT_PUNITORIO'] = $variacion['ASOC']['PUNITORIO'];
                $tmp['FECHA_DESDE'] = $variacion['FECHA'];
                $tmp['FECHA_HASTA'] = 0;

                $tasas[] = $tmp;
                
                if (isset($tasas[count($tasas)-2])) {
                    $tasas[count($tasas)-2]['FECHA_HASTA'] = $variacion['FECHA'];
                }

                $fecha_posterior = $variacion['FECHA'];
            }
        }
        if (count($tasas)) {
            $tasas[count($tasas)-1]['FECHA_HASTA'] = 0;
            //$ultima_tasa['FECHA_HASTA'] = "0";
        }        
        return $tasas;
    }

}
