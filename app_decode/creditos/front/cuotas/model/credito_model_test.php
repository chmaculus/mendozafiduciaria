<?php

define("NUMERO_CONVENIO", 5321);
include("general/extends/model/clases.php");

class credito_model_test extends credito_model {
    
    function getObject($id_credito, $version) {
        $this->set_credito_active($id_credito);
        $this->set_version_active($version);
        
        $this->_cuotas = $this->_to_array_cuotas();
        $this->_variaciones = $this->_to_array_variaciones();


        $primera_variacion = reset($this->_variaciones);

        $credito = new Credito($id_credito, $primera_variacion['PERIODICIDAD_TASA'], 
                $this->_interese_compensatorio_plazo, 
                $this->_interese_moratorio_plazo, 
                $this->_interese_punitorio_plazo, 
                $this->_interese_compensatorio_plazo,
                $primera_variacion['PLAZO_PAGO'],
                $this->_id_version
        );
        $credito->setTipoCredito($this->_tipo_credito);
        $credito->ready();
        foreach ($this->_cuotas as $_cuota) {
            $cuota = new Cuota($_cuota['ID'], $_cuota['CAPITAL_CUOTA']);
            $cuota->setRango(new Rango($_cuota['FECHA_INICIO'], $_cuota['FECHA_VENCIMIENTO']));
            $credito->addCuota($cuota);
        }

        $cuotasCollection = $credito->getCuotas();


        foreach ($this->_variaciones as $variacion) {
            $ef = new EventoFactory($cuotasCollection, $variacion);
            $evento = $ef->getEvento();
            $credito->addEvento($evento);
        }
        
        return $credito;
    }
    
    function getCuotasCredito($id_credito, $version, $fecha){
        $credito = $this->getObject($id_credito, $version);
        $id_credito = $credito->getID();
        $eventos = $credito->getEventos();
        echo "llega";
        $cuotas = $credito->getCuotas();
        
        $totalCuotas = $cuotas->size();
        
        $arr_cuota = array();
        $nroCuota = 1;
        foreach($cuotas as $cuota){
            $cuotaTmp = array(
                "ID_CREDITO"=>$id_credito,
                "CUOTAS_RESTANTES"=>$totalCuotas - $nroCuota + 1,
                "CAPITAL_CUOTA"=>0,
                "FECHA_INICIO"=>$cuota->getRango()->getStart(),
                "FECHA_VENCIMIENTO"=>$cuota->getRango()->getEnd(),
                "POR_INT_COMPENSATORIO"=>0,
                "INT_COMPENSATORIO"=>0,
                "INT_MORATORIO"=>0,
                "INT_PUNITORIO"=>0,
                "SALDO_CAPITAL_TEORICO"=>0,
                "AMORTIZACION_TEORICA"=>0,
                "FECHA_ENVIADA"=>0
            );
            $cuotaTmp['SEGMENTOS'] = array();
            $vencimiento = $cuota->getRango()->getEnd();
            $segmentos = array();
            
            $fechaEventoAnterior = $cuota->getRango()->getStart();
            $segmento = array();
            
            while($evento = $eventos->nextToDate($vencimiento ) ){
                if (!$eventos->valid()) break;
                echo "llega??";
                $fechaEventoActual = $evento->getFecha();
                $dias = ($fechaEventoAnterior - $fechaEventoActual) / (60*60*24) ;
                
                switch($evento->getTipo()){
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                        $segmento = array(
                            "FECHA_INICIO"=>0,
                            "FECHA_VENCIMIENTO"=>0,
                            "INT_COMPENSATORIO"=>0,
                            "INT_COMPENSATORIO_IVA"=>0,
                            "DIAS_TRANSCURRIDOS"=>$dias ,
                            "TIPO"=>$evento->getTipo(),
                            "MONTO"=>0,
                            "_ID_VARIACION"=>$evento->getID()
                    );
                        break;
                }
                switch($evento->getTipo()){
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                        $segmento['TIPO'] = "TIPO";
                        $cuotaTmp['SEGMENTOS'][] = $segmento ;
                }
                
                
            }
            
            $nroCuota++;
            
            $arr_cuota[] = $cuotaTmp;
            
        }
        
        
        return $arr_cuota;
    }
    
    

    function get_cuotas_credito() {

        $cuotas = $this->_cuotas;

        $variaciones = $this->_variaciones;
        $pagos = $this->_pagos;
        $primer_desembolso = array();

         
        foreach ($variaciones as $variacion) {
            if ($variacion['TIPO'] == EVENTO_DESEMBOLSO || $variacion['TIPO'] == EVENTO_AJUSTE) {
                $primer_desembolso = $variacion;
                break;
            }
        }

        $por_int_compensatorio = $variacion["POR_INT_COMPENSATORIO"];
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
            $cuotas[$index]['POR_INT_COMPENSATORIO'] = $por_int_compensatorio;
            

            $segmentos = array();
            foreach ($cuota['CHILDREN'] as $child) {
                if (
                // $child['FECHA'] >= $cuota['FECHA_INICIO'] &&
                        $child['FECHA'] < $cuota['FECHA_VENCIMIENTO']
                ) {
                    
                    //comprobacion si tipo viene vacio
                    if (!isset($variaciones[$child['_ID_VARIACION']]['TIPO'])) {
                        
                    }

                    //si tipo es 0 se agrega ASOC vacio 
                    if ($variaciones[$child['_ID_VARIACION']]['TIPO'] == EVENTO_INICIAL) {
                        $variaciones[$child['_ID_VARIACION']]['ASOC'] = array();
                    }
                    
                    //Si ignora si es temporal
                    if ($variaciones[$child['_ID_VARIACION']]['TIPO'] != 5000) {
                        if (!isset($variaciones[$child['_ID_VARIACION']]['ASOC'])) {
                            
                        } else {
                            $child['ASOC'] = $variaciones[$child['_ID_VARIACION']]['ASOC'];
                        }
                        
                    /*    if ($child['FECHA_VENCIMIENTO'] < $cuota_enviada){
                            $segmentos[] = array(
                                "FECHA_VENCIMIENTO"=>$cuota_enviada,
                                "FECHA_INICIO"=>$cuota_enviada,
                                "INT_COMPENSATORIO_IVA"=>0,
                                "INT_COMPENSATORIO"=>0,
                                "TIPO"=>100
                            );
                            $cuota_enviada = 0;
                        }*/
                        
                        $segmentos[] = $child;
                    }
                }
            }

            //se agrega un primer segmento virtual que representa el primer desembolso
/*            if ($x == 0 && false) {
                $tmp_segmento = $primer_desembolso;
                $tmp_segmento['_ID_VARIACION'] = $primer_desembolso['ID'];
                $tmp_segmento['FECHA_VENCIMIENTO'] = $primer_desembolso['FECHA'];
                $tmp_segmento['FECHA_INICIO'] = $primer_desembolso['FECHA'];
                $tmp_segmento['INT_COMPENSATORIO_IVA'] = 0;
                $tmp_segmento['INT_COMPENSATORIO'] = 0;
                $tmp_segmento['ASOC'] = $variaciones[$child['_ID_VARIACION']]['ASOC'];
                //$segmentos = array_merge(array($tmp_segmento), $segmentos);
            }*/

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

                $bagregar = true;

                switch ($segmento['TIPO']) {
                    //AJUSTE
                    case EVENTO_AJUSTE:
                        $tipo = 'Ajuste Migracion';
                        if ($segmento['ASOC']['MONTO'] == 0) {
                            $bagregar = false;
                        }
                        $segmento['MONTO'] = 0;
                        if ($segmento['ASOC']) {
                            $segmento['MONTO'] = "$" . number_format($segmento['ASOC']['MONTO'], 2);
                        }
                        break;
                    //DESEMBOLSO
                    case EVENTO_DESEMBOLSO:
                        $tipo = 'Desembolso';
                        if ($segmento['ASOC']['MONTO'] == 0) {
                            $bagregar = false;
                        }
                        $segmento['MONTO'] = 0;
                        if ($segmento['ASOC']) {
                            $segmento['MONTO'] = "$" . number_format($segmento['ASOC']['MONTO'], 2);
                        }
                        break;
                    //CAMBIO TASA
                    case EVENTO_TASA:
                        $tipo = 'Cambio Tasa';
                        $segmento['MONTO'] = 0;
                        if ($segmento['ASOC']) {
                            $segmento['MONTO'] = "%" . $segmento['ASOC']['COMPENSATORIO'];
                            $por_int_compensatorio = $segmento['ASOC']['COMPENSATORIO'];
                        }
                        break;

                    //RECUPERO
                    case EVENTO_RECUPERO:
                        $tipo = 'Recupero';
                        $segmento['MONTO'] = 0;
                        if ($segmento['ASOC']) {
                            $total_monto = 0;
                            foreach ($segmento['ASOC'] as $valor) {
                                $total_monto += $valor['MONTO'];
                            }
                            $segmento['MONTO'] = "$" . number_format($total_monto, 2);
                        }

                        break;
                    //RECUPERO
                    case EVENTO_AJUSTE:
                        $tipo = 'Salso traspaso';
                        $segmento['MONTO'] = 0;
                        if ($segmento['ASOC']) {
                            $total_monto = 0;
                            foreach ($segmento['ASOC'] as $valor) {
                                $total_monto += $valor['MONTO'];
                            }
                            $segmento['MONTO'] = "$" . number_format($total_monto, 2);
                        }

                        break;
                }

                $hasta = $segmento['FECHA_VENCIMIENTO'];
                $segmento['FECHA_INICIO'] = $desde;
                $segmento['DIAS_TRANSCURRIDOS'] = floor(($hasta - $desde) / (3600 * 24));
                $segmento['TIPO'] = $tipo;

                $desde = $hasta;
                if ($bagregar) {
                    $cuotas[$index]['SEGMENTOS'][] = $segmento;
                }
            }
            unset($cuotas[$index]['CHILDREN']);
            $cuotas[$index]['GASTOS'] = $gastos;
            $arr_result[] = $cuotas[$index];


            $x++;
        }


        $variaciones = array();
        foreach ($this->_variaciones as $variacion) {
            if (isset($ultima_cuota['FECHA_VENCIMIENTO'])) {
                if ($variacion['FECHA'] > $ultima_cuota['FECHA_VENCIMIENTO']) {
                    $variaciones[] = $variacion;
                }
            }
        }
        //print_array($variaciones);
        for ($i = 0; $i < count($variaciones); $i++) {

            if ($variaciones[$i]['TIPO']==5000) {
                array_splice($variaciones, $i,1);
                break;
            }
            if (!isset($variaciones[$i]['ASOC'])){
                break;
            }
            
            $tipo = 'Recupero';
            $variaciones[$i]['TOTAL'] = 0;
            $variaciones[$i]['TIPO'] = -1;
            
            if ($variaciones[$i]['ASOC']) {
                $total_monto = 0;
                foreach ($variaciones[$i]['ASOC'] as $valor) {
                    if($valor) {
                        $total_monto += $valor['MONTO'];
                        $tipo = 'Recupero';
                    }
                }
                $variaciones[$i]['TOTAL'] = $total_monto;
                $variaciones[$i]['TIPO'] = $tipo;
            }
        }


        $res['RESULT'] = $arr_result;
        $res['VARIACIONES'] = $variaciones;
        $res['PRIMER_DESEMBOLSO'] = $primer_desembolso;
        $res['DE_CADUCADO'] = $this->es_caducado();
        return $res;
    }

    function verificar_desembolsos_inciales($fecha) {


        if (!$this->_cuotas)
            return;

        $cuota = reset($this->_cuotas);
        if ($fecha < $cuota['FECHA_VENCIMIENTO'])
            return true;

        $desembolso_inicial = array();
        foreach ($this->_variaciones as $variacion) {
            if ($variacion['TIPO'] == 1 && $variacion['ESTADO'] == 0) {
                $desembolso_inicial = $variacion;
                break;
            }
        }

        //existen desembolsos
        if ($desembolso_inicial) {

            //obtenemos la primera cuota

            echo "<br/>" . $desembolso_inicial['FECHA'] . "<" . $cuota['FECHA_VENCIMIENTO'] . "<br/>";
            if ($desembolso_inicial['FECHA'] < $cuota['FECHA_VENCIMIENTO']) {
                return true;
            } else {
                return $cuota['FECHA_VENCIMIENTO'] - 1;
            }
        } else {
            return $cuota['FECHA_VENCIMIENTO'] - 1;
        }
    }

    function leer_desembolsos_pendientes() {
        $credito_id = $this->_id_credito;
        $this->_db->where("c.ID = " . $credito_id . " AND ESTADO = 1");
        $this->_db->select("d.*, c.ID as ID_CREDITO");
        $this->_db->join("fid_creditos c", "c.ID_OPERACION = d.ID_OPERACION");
        $desembolsos = $this->_db->get_tabla("fid_sol_desembolso d");
        return $desembolsos;
    }

    function agregar_desembolso_solicitado($desembolso) {
        $this->_db->update("fid_sol_desembolso", array("ESTADO" => 2), "ID = " . $desembolso['ID']);
    }
    
    function borrar_credito() {
        $cred = $this->_id_credito;
        $this->_db->delete("fid_creditos_cuotas", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_desembolsos", "ID_CREDITO = " . $cred . " AND ID_VARIACION > 0");
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

    function _generar_codbar($id, $total) {

        $this->renew_datos();
        $opcionesCredito = $this->get_creditos_opciones();
        $numeroConvenio = isset($opcionesCredito['convenio']) ? $opcionesCredito['convenio']['VALOR'] : NUMERO_CONVENIO;
        
        
        $cuota = $this->_cuotas[$id];

        $num_convenio = str_pad($numeroConvenio, 4, "0", STR_PAD_LEFT);
        $num_credito = str_pad($cuota['ID_CREDITO'], 8, "0", STR_PAD_LEFT);

        $fecha_vencimiento = date("Ymd", $cuota['FECHA_VENCIMIENTO']);

        $capital = $cuota['E_AMORTIZACION'] > 0 ? $cuota['E_AMORTIZACION'] : $cuota['CAPITAL_CUOTA'];

        $importe = $total;
        $importe = str_pad(number_format($importe, 2, ".", '') * 100, 10, 0, STR_PAD_LEFT);

        $codebar = $num_convenio . $num_credito . $fecha_vencimiento . $importe;



        $items_arr = str_split($codebar, 1);

        $str_tmp = str_repeat("12", count($items_arr));
        $items2 = substr($str_tmp, 0, count($items_arr));
        $items2_arr = str_split($items2, 1);


        $res = array();

        $total = 0;
        for ($i = 0; $i < count($items_arr); $i++) {
            $tmp = $items_arr[$i] * $items2_arr[$i];
            $tmp = $tmp > 9 ? $tmp - 9 : $tmp;
            $total += $tmp;
            $res[$i] = $tmp;
        }

        $total_2 = $total % 10;

        $dv = 0;
        if ($total_2 > 0) {
            $dv = 10 - $total_2;
        }

        $codebar .= $dv;
        return $codebar;
    }

    function get_cuotas_desde($fecha) {
        $cuotas_rtn = array();
        foreach ($this->_cuotas as $cuota) {
            if ($cuota['FECHA_VENCIMIENTO'] > $fecha) {
                $cuotas_rtn[] = $cuota;
            }
        }

        return $cuotas_rtn;
    }
    
    function get_creditos_by_operatoria($id_operatoria) {
        $this->_db->select("c.ID, cv.ID_VERSION");
        $this->_db->where("ID_OPERATORIA = '" . $id_operatoria . "'");
        $this->_db->join("fid_creditos_version cv", "cv.ID_CREDITO_VERSION = c.ID");
        $rtn = $this->_db->get_tabla("fid_creditos c");
        
        return $rtn;
    }
    
    function refinanciacion_caida() {
        //eliminamos el credito actual, antes buscamos las cobranzas y gastos, y volvemos a insertar
        $id_credito_caido = $this->_id_credito;
        $id_credito_padre = $this->es_caducado();
        if (!$id_credito_padre) {
            return FALSE;
        }
        $_pagos = $this->get_todos_pagos();
        $pagos = array();
        if ($_pagos) {
            foreach ($_pagos as $pg) {
                if (!isset($pagos[$pg['FECHA']])) {
                    $pagos[$pg['FECHA']] = 0;
                }
                $pagos[$pg['FECHA']] += $pg['MONTO'];
            }
        }
        
        $gastos = $this->get_todos_gastos();
        $this->clear();
        if ($gastos) {
            if ($this->set_credito_active($id_credito_padre)) {
                foreach ($gastos as $fecha => $gasto) {
                    $this->agregar_gasto($gasto['MONTO'], $gasto['FECHA'], $gasto['CONCEPTO']);
                }
            }
        }
        
        if ($pagos) {
            foreach ($pagos as $fecha => $monto) {
                if ($this->set_credito_active($id_credito_padre)) {
                    $version = $this->get_versiones();
                    $version = $version[0]['value'];
                    $this->clear();
                    $this->set_credito_active($id_credito_padre);
                    $this->set_version_active($version);
                    /* 
                    $this->set_version_active($version);
                    
                    $this->elimina_eventos_temporales();
                    $this->renew_datos();
                    
                    //$this->save_last_state(false);
                    //$this->set_fecha_actual($fecha);
                    
                    
                    $this->set_devengamiento_tipo(TIPO_DEVENGAMIENTO_FORZAR_DEVENGAMIENTO);
            
                    
                    $ret_evento = $this->generar_evento( array(), true, $fecha, true);

                    $ret_deuda = $this->get_deuda($fecha);
                    $obj_pago = $this->pagar_deuda($ret_deuda, $monto, $fecha);
                    
                    print_r($obj_pago);
                    
                    
                    $ret = $this->generar_evento( $data, true, $fecha);

                    $this->assign_id_evento($ret['ID'],EVENTO_RECUPERO);
                    
                    print_r($obj_pago);
                    * 
                    */
                    $this->realizar_pago($fecha, $monto);
                }
            }
            
        }
        
        $this->_db->update("fid_creditos", array("CREDITO_ESTADO" => ESTADO_CREDITO_ELIMINADO), "ID = " . $id_credito_caido);
        $this->_db->update("fid_creditos", array("CREDITO_ESTADO" => ESTADO_CUOTA_PENDIENTE), "ID = " . $id_credito_padre);
        
    }
    
    function getCambiosTasasCredito($id, $fecha) {
        $this->_db->select("POR_INT_COMPENSATORIO, POR_INT_SUBSIDIO, POR_INT_MORATORIO, POR_INT_PUNITORIO");
        $this->_db->where("ID_CREDITO = '" . $id . "' AND fecha<=$fecha");
        $this->_db->order_by("FECHA", "DESC");
        $this->_db->limit(0,1);
        $rtn = $this->_db->get_tabla("fid_creditos_eventos");
        if ($rtn) {
            return $rtn[0];
        } else {
            return FALSE;
        }
    }
    
    function getTasas($id, $fecha) {
        $this->_db->select("POR_INT_COMPENSATORIO, POR_INT_SUBSIDIO, POR_INT_MORATORIO, POR_INT_PUNITORIO");
        $this->_db->where("ID_CREDITO = '" . $id . "' AND TIPO=0 AND fecha<=$fecha");
        $rtn = $this->_db->get_tabla("fid_creditos_eventos");
        
        if ($rtn) {
            return $rtn[0];
        } else {
            return FALSE;
        }
            
    }

    function get_monto_credito() {
        return $this->_total_credito;
    }
    
    function marcar_cobro_bancario($id, $fecha = false){
        $fecha = $fecha ? $fecha : time();
        $this->_db->update("fid_creditos_bancos_cobros",array("FECHA_INGRESADO"=>$fecha ),"ID = ".$id);
    }
    

}

?>