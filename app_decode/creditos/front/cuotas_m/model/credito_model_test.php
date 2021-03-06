<?php

define("NUMERO_CONVENIO",5321);


class credito_model_test extends credito_model {
    
    
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
            // foreach($cuota[''])
            //se agrega un primer segmento virtual que representa el primer desembolso
            if ($x == 0 && false) {
                $tmp_segmento = $primer_desembolso;
                $tmp_segmento['_ID_VARIACION'] = $primer_desembolso['ID'];
                $tmp_segmento['FECHA_VENCIMIENTO'] = $primer_desembolso['FECHA'];
                $tmp_segmento['FECHA_INICIO'] = $primer_desembolso['FECHA'];
                $tmp_segmento['INT_COMPENSATORIO_IVA'] = 0;
                $tmp_segmento['INT_COMPENSATORIO'] = 0;
                $tmp_segmento['ASOC'] = $variaciones[$child['_ID_VARIACION']]['ASOC'];
                //$segmentos = array_merge(array($tmp_segmento), $segmentos);
            }

            $this->_db->where("FECHA >= " . $cuota['FECHA_INICIO']);
            $this->_db->where("FECHA <= " . $cuota['FECHA_VENCIMIENTO']);
            $gastos = $this->get_tabla_gastos();

            $cuotas[$index]['SEGMENTOS'] = array();
            $desde = $cuota['FECHA_INICIO'];
            if ($segmentos) {
                if ($x == 0) {
                    //$this->_db->where("FECHA >= ".$primer_desembolso['FECHA']);
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
    
    
    function leer_desembolsos_pendientes(){
        $credito_id = $this->_id_credito;
        $this->_db->where("C.ID = ".$credito_id." AND ESTADO = 1");
        $this->_db->select("d.*, c.ID as ID_CREDITO");
        $this->_db->join("fid_creditos c","c.ID_OPERACION = d.ID_OPERACION");
        $desembolsos = $this->_db->get_tabla("fid_sol_desembolso d");
        return $desembolsos;
    }
    
    function agregar_desembolso_solicitado($desembolso){
        $this->_db->update("fid_sol_desembolso",array("ESTADO"=>2),"ID = ".$desembolso['ID']);
    }
    
    
    function pagar_deuda($arr_deuda, $monto, $fecha) {
        $id_credito = $this->_id_credito;
        $arr_pago = array();

        if ($monto == 0)
            return;

        //pagamos gasto
        foreach ($arr_deuda['gastos'] as $key => $val) {
            $pago = $monto <= $val['SALDO'] ? $monto : $val['SALDO'];
            if ($pago > 0) {
                $arr_pago[] = array(
                    "ID_CREDITO" => $id_credito,
                    "FECHA" => $fecha,
                    "ID_TIPO" => 8,
                    "MONTO" => $monto <= $val['SALDO'] ? $monto : $val['SALDO'],
                    "CUOTAS_RESTANTES" => $val['ID'],
                    "ID_VARIACION" => TEMP_ID
                );

                $monto = $monto <= $val['SALDO'] ? 0 : $monto - $val['SALDO'];
            }
        }

        //pagamos cuotas
        for ($x = 0; $x < count($arr_deuda['cuotas']); $x++) {
            unset($arr_deuda['cuotas'][$x]['GASTOS']);
            $cuota = $arr_deuda['cuotas'][$x];

            $bmoratorio = false;
            $bpunitorio = false;
            $bcompensatorio = false;
            $bcapital = false;
            foreach ($cuota as $key => $val) {
                if ($monto == 0)
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
                            case 5: //Moratorio
                                $bmoratorio = true;
                                break;
                            case 4: //punitorio
                                $bpunitorio = true;
                                break;
                            case 6: //compensatorio
                                $bcompensatorio = true;
                                break;
                            case 7: //capital
                                $bcapital = true;
                                break;
                        }
                    }
                    $monto = $monto >= $val['SALDO'] ? $monto - $val['SALDO'] : 0;
                }
            }
            //si el capital es mayor a 0 (cuota con amortizacion de capital) la cuota queda cancelada
            //si estan cancelados todos los items.
            if ($arr_deuda['cuotas'][$x]['CAPITAL'] == 0) {
                if ($bmoratorio && $bpunitorio && $bcompensatorio && $bcapital) {
                    $arr_deuda['cuotas'][$x]['_INFO']['ESTADO'] = 1;
                }
            }
            ////si el capital es 0 (cuota de gracia) la cuota queda cancelada
            //sin contar el capital.
            else {
                if ($bmoratorio && $bpunitorio && $bcompensatorio) {
                    $arr_deuda['cuotas'][$x]['_INFO']['ESTADO'] = 1;
                }
            }
        }

        //TIPO PAGO_ADELANTADO es un monto no asignado.
        if ($monto > 0) {
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
        return $arr_pago;
    }





    function borrar_credito() {
        $cred = $this->_id_credito;
        $this->_db->delete("fid_creditos_cuotas", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_desembolsos", "ID_CREDITO = " . $cred." AND ID_VARIACION > 0");
        $this->_db->delete("fid_creditos_gastos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_eventos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_pagos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_cambiotasas", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_version", "ID_CREDITO_VERSION = " . $cred);
    }
    
    function borrar_credito_soft(){
        $cred = $this->_id_credito;
        $this->_db->update("fid_creditos", array("CREDITO_ESTADO"=>1),"ID = ".$cred);
    }    
    
    function generar_chequera($fecha = false, $fecha_desde = false, $fecha_hasta = false){
        $fecha = $fecha ? $fecha : time();
        $fecha_desde = $fecha_desde ? $fecha_desde : time();
        $fecha_hasta = $fecha_hasta ? $fecha_hasta : NO_FECHA;
        $this->renew_datos();
        
        foreach($this->_cuotas as $cuota){
            if ($cuota['FECHA_VENCIMIENTO'] >= $fecha_desde && $cuota['FECHA_VENCIMIENTO'] <= $fecha_hasta){
                $cuota['FECHA_ENVIADA'] = $fecha;
                
                $this->_generar_codbar($cuota['ID']);
                if ($this->_bsave){
                    $this->_db->update("fid_creditos_cuotas",array("FECHA_ENVIADA"=>$fecha),"ID = ".$cuota['ID']);
                }
            }
        }
    }
    
    function _generar_codbar($id){

        $this->renew_datos();
        $cuota = $this->_cuotas[$id];
        
        
        $num_convenio = str_pad(NUMERO_CONVENIO, 4, "0", STR_PAD_LEFT);
        $num_credito = str_pad($cuota['ID_CREDITO'], 8, "0", STR_PAD_LEFT);
        
        $fecha_vencimiento = date("Ymd",$cuota['FECHA_VENCIMIENTO']);
        
        $capital = $cuota['E_AMORTIZACION'] > 0 ? $cuota['E_AMORTIZACION'] : $cuota['CAPITAL_CUOTA'];
        
        $importe = $capital + $cuota['INT_COMPENSATORIO'] + $cuota['INT_COMPENSATORIO_IVA'];
        
        $importe = str_pad(number_format($importe,2,".",'') * 100,10,0,STR_PAD_LEFT);
        
        $codebar = $num_convenio.$num_credito.$fecha_vencimiento.$importe;
        
        
        
        $items_arr = str_split($codebar , 1);

        $str_tmp= str_repeat("12", count($items_arr) );
        $items2 = substr($str_tmp, 0,count($items_arr)) ;
        $items2_arr = str_split($items2 , 1);
        
        
        $res = array();
        
        $total = 0;
        for($i = 0 ; $i < count($items_arr) ; $i++){
            $tmp = $items_arr[$i] * $items2_arr[$i];
            $tmp = $tmp > 9 ? $tmp-9 : $tmp;
            $total += $tmp;
            $res[$i] = $tmp;
        }
        
        $total_2 = $total % 10;
        
        $dv = 0;
        if ($total_2 > 0){
            $dv = 10 - $total_2;
        }
        
        $codebar .= $dv;
        return $codebar;
        
    }
    
}

?>