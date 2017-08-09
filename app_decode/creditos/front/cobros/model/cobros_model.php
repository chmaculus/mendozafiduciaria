<?php

class cobros_model extends credito_model{

    function get_cuotas_estructura(){
        $cuotas_rtn = array();
        foreach($this->_cuotas as $cuota){
            $cuota['DATA'] = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO']-1, true);
            $cuotas_rtn[] = $cuota;
        }
//        print_array($cuotas_rtn);
        return $cuotas_rtn;
    }
    
    
    function save_estructura($cuotas){
        foreach($cuotas as $cuota){
            $this->_db->update("fid_creditos_cuotas", 
                    array(
                        "E_AMORTIZACION"=>$cuota['monto']
                    ),"ID = ".$cuota['id']);
        }
    }
    
    
    function guardar_archivo_bancario($archivo, $fecha){
        $id = $this->_db->insert("fid_creditos_bancos_files",array("FECHA_REC"=>$fecha,"ARCHIVO"=>$archivo));
        return $id;
    }
    
    function get_archivos_bancarios(){
        $this->_db->select('bf.ID, FECHA_REC, ARCHIVO, ESTADO, ID_CREDITO');
        $this->_db->join("fid_creditos_bancos_cobros bc","bc.ID_FILE = bf.ID AND FECHA_INGRESADO=0", "left");
        $this->_db->group_by("bf.ID");
        $rtn = $this->_db->get_tabla("fid_creditos_bancos_files bf", FALSE, "FECHA_REC DESC");
        return $rtn;
    }
    
    function get_archivos_bancarios_mes(){
        $caducados = $this->_db->get_row('fid_settings', "variable='pagos_creditos_caducados'");
        if ($caducados && $caducados['valor']) {
            $caducados = (int) $caducados['valor'];
        } else {
            $caducados = FALSE;
        }
        
        $this->_db->select('FECHA_RENDICION');
        $this->_db->group_by("YEAR( FROM_UNIXTIME( FECHA_RENDICION ) ) , MONTH( FROM_UNIXTIME( FECHA_RENDICION ) )");
        $this->_db->order_by("FECHA_RENDICION", "DESC");
        if (!$caducados) {
            $this->_db->join("fid_creditos c", "c.ID=bc.ID_CREDITO AND c.CREDITO_ESTADO=" . ESTADO_CREDITO_NORMAL, 'inner');
        }
        $rtn = $this->_db->get_tabla("fid_creditos_bancos_cobros bc", 'bc.ID_CREDITO > 0');
        return $rtn;
    }
    
    function get_archivo_bancario($id){
        $rtn = $this->_db->get_row("fid_creditos_bancos_files","ID = ".$id);
        return $rtn;
    }
    
    function guardar_cobros_bancos($datos){
        $fecha = time();
        foreach($datos as $dato){
            $dato['FECHA_TOMA'] = $fecha ;
            $dato['FECHA_INGRESADO'] = 0;
            $this->_db->insert("fid_creditos_bancos_cobros",$dato);
        }
    }
    
    function marcar_archivo_bancario($id){
        $this->_db->update("fid_creditos_bancos_files",array("ESTADO"=>time()), "ID = ".$id);
    }
    
    function get_cobros_bancos($id){
        $this->_db->select("cb.*, cl.RAZON_SOCIAL, ARCHIVO");
        
        $this->_db->join("fid_creditos c","cb.ID_CREDITO = c.ID", "left");
        $this->_db->join("fid_clientes cl","cl.ID = c.POSTULANTES", "left");
        $this->_db->join("fid_creditos_bancos_files cf","cb.ID_FILE = cf.ID", "left");
        $datos = $this->_db->get_tabla("fid_creditos_bancos_cobros cb","cb.ID_FILE = ".$id);
        //echo $this->_db->last_query();die();
        return $datos;
    }
    
    function get_cobros_bancos_mes($mes){
//        $caducados = $this->_db->get_row('fid_settings', "variable='pagos_creditos_caducados'");
//        if ($caducados && $caducados['valor']) {
//            $caducados = (int) $caducados['valor'];
//        } else {
//            $caducados = FALSE;
//        }
        
        $this->_db->select("cb.*, cl.RAZON_SOCIAL, c.CREDITO_ESTADO");
        
        $this->_db->join("fid_creditos c","cb.ID_CREDITO = c.ID", "inner");
        $this->_db->join("fid_clientes cl","cl.ID = c.POSTULANTES", "left");
        $year = date('Y', $mes);
        $mes = date('m', $mes);
        $this->_db->order_by("cb.FECHA_RENDICION, cb.CREDITO_VENCIMIENTO", "ASC");
        $datos = $this->_db->get_tabla("fid_creditos_bancos_cobros cb","YEAR( FROM_UNIXTIME( FECHA_RENDICION ) ) = '$year' AND MONTH( FROM_UNIXTIME( FECHA_RENDICION ) ) = '$mes'");
        //echo $this->_db->last_query();die();
        return $datos;
    }
    
    function marcar_cobro_bancario($id, $fecha = false){
        $fecha = $fecha ? $fecha : time();
        $this->_db->update("fid_creditos_bancos_cobros",array("FECHA_INGRESADO"=>$fecha ),"ID = ".$id);
    }
    
    function pagar_deuda($arr_deuda, $monto, $fecha) {
        $id_credito = $this->_id_credito;
        $arr_pago = array();
        
        $cuotas_canceladas = array();

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
                                $bmoratorio = true;
                                $bpunitorio = true;
                                $bcompensatorio = true;
                                break;
                            case 7: //capital
                                $bmoratorio = true;
                                $bpunitorio = true;
                                $bcompensatorio = true;                                
                                $bcapital = true;
                                break;
                        }
                    }
                    $monto = $monto >= $val['SALDO'] ? $monto - $val['SALDO'] : 0;
                }
            }
            ////si el capital es 0 (cuota de gracia) la cuota queda cancelada
            //sin contar el capital.
            if ($arr_deuda['cuotas'][$x]['CAPITAL'] == 0) {
                if ($bmoratorio && $bpunitorio && $bcompensatorio && $bcapital) {
                    $arr_deuda['cuotas'][$x]['_INFO']['ESTADO'] = 1;
                }
            }
            
            //si el capital es mayor a 0 (cuota con amortizacion de capital) la cuota queda cancelada
            //si estan cancelados todos los items.            
            else {
                if ($bmoratorio && $bpunitorio && $bcompensatorio) {
                    
                    //las cuotas que han sido canceladas pagando capital se retornan
                    //para cambiarles la fecha de vencimiento si asi corresponde
                    $cuota_paga = $arr_deuda['cuotas'][$x];
                    $cuota_paga['FECHA_CANCELADA'] = $fecha;
                    $cuotas_canceladas[] = $cuota_paga;
                    $arr_deuda['cuotas'][$x]['_INFO']['ESTADO'] = 1;
                }
            }
        }

        //TIPO 10 es un monto no asignado.
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
    
    function tiene_pagos_imputados($id) {
        $rtn = $this->_db->get_tabla("fid_creditos_bancos_cobros", "ID_FILE=$id AND FECHA_INGRESADO!=0");
        if ($rtn) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function del_cobro($id) {
        $this->_db->delete("fid_creditos_bancos_cobros", "ID_FILE=$id");
        $this->_db->delete("fid_creditos_bancos_files", "ID=$id");
        
        return TRUE;
    }
    
    function get_pagos_creditos($id_credito, $fecha, $monto) {
        $this->_db->select('SUM(MONTO) AS monto');
        $this->_db->group_by('ID_VARIACION');
        $rtn = $this->_db->get_tabla("fid_creditos_pagos", "ID_CREDITO=$id_credito AND FECHA=$fecha");
        
        if (count($rtn)) {
            foreach ($rtn as $it) {
                if (round($it['monto'], 2) == $monto) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    
}

