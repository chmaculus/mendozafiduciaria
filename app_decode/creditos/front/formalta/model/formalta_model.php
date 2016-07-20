<?php



class formalta_model extends credito_model {

    function get_credito_from_id($id){
        $this->_db->where("ID = ".$id);
        $credito = $this->_db->get_row("fid_creditos");
        
        $credito['DESEMBOLSOS'] = $this->_db->get_tabla("fid_creditos_desembolsos","ID_CREDITO = ".$id." AND CUOTAS_RESTANTES = -1");
        
        return $credito;
    }
    

    function limpiar_credito(){
        $cred = $this->_id_credito;
        $this->_db->delete("fid_creditos_cuotas", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_desembolsos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_gastos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_eventos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_pagos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_cambiotasas", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_version", "ID_CREDITO_VERSION = " . $cred);
    }
    
    
    function save_operacion_credito(){
        $this->renew_datos();
        
        $credito = $this->_db->get_row("fid_creditos","ID = ".$this->_id_credito);
        $operacion = (isset($credito['ID_OPERACION']) && $credito['ID_OPERACION']) ? $this->_db->get_row("fid_operaciones","ID = ".$credito['ID_OPERACION']) : 0;
        
        $clientes = $credito ? $this->_db->get_tabla("fid_operacion_cliente","ID_OPERACION  = ".$credito['ID_OPERACION']) : array();
        
        $arr_clientes = array();
        foreach($clientes as $cliente){
            $arr_clientes[] = $cliente['ID_CLIENTE'];
        }
        
        
        
        $total = 0;
        $primera_variacion = reset($this->_variaciones);
        
        foreach($this->_variaciones as $variacion){
            if ($variacion['TIPO']==0){
                $total += $variacion['CAPITAL'];
            }
        }
        
        $interes_cuotas = 0;
        
        $compensatorio = $primera_variacion['POR_INT_COMPENSATORIO'];
        $punitorio = $primera_variacion['POR_INT_PUNITORIO'];
        $moratorio = $primera_variacion['POR_INT_MORATORIO'] ;
        $bonificacion = $primera_variacion['POR_INT_SUBSIDIO'];
        $gastos = $primera_variacion['POR_INT_GASTOS'];
        $gastos_min = $primera_variacion['POR_INT_GASTOS_MIN'];
        
        $primera_cuota = reset($this->_cuotas);
        $primer_vencimiento = $primera_cuota['FECHA_VENCIMIENTO'];
        
        echo "ID_CREDITO:".$this->_id_credito;
        
        $credito = array(
            "MONTO_CREDITO" => $total,
            "PLAZO_COMPENSATORIO" => $this->_interese_compensatorio_plazo,
            "PLAZO_MORATORIO" => $this->_interese_moratorio_plazo,
            "PLAZO_PUNITORIO" => $this->_interese_punitorio_plazo,            
            "T_COMPENSATORIO" => $compensatorio,
            "T_PUNITORIO" => $punitorio,
            "T_BONIFICACION" => $bonificacion,
            "T_MORATORIO" => $moratorio,
            "T_GASTOS" => $gastos,
            "T_GASTOS_MIN" => $gastos_min,
            "INTERES_CUOTAS" => $interes_cuotas,
            "INTERES_VTO" => date("Y-m-d",$primer_vencimiento),
            "INTERES_PERIODO" => 09,
            "CAPITAL_CUOTAS" => $interes_cuotas,
            "CAPITAL_VTO" => date("Y-m-d"),
            "CAPITAL_PERIODO" => 0,
            "ID_FIDEICOMISO" =>  $operacion ? $operacion['ID_FIDEICOMISO'] : 0,
            "ID_OPERATORIA" => $operacion ? $operacion['ID_OPERATORIA'] : 0,
            "POSTULANTES" => implode(",", $arr_clientes)
        );
        
        $this->_db->select('RAZON_SOCIAL, CUIT');
        if ($data_clientes = $this->_generar_clientes($credito['POSTULANTES'])) {
            $credito['POSTULANTES_NOMBRES'] = $data_clientes['POSTULANTES_NOMBRES'];
            $credito['POSTULANTES_CUIT'] = $data_clientes['POSTULANTES_CUIT'];
        }
        
        $this->_db->update("fid_creditos",$credito,"ID = ".$this->_id_credito);
        echo $this->_db->last_query();
    }
    
    
    
   //se gneran las cuotas a partir de una variacion en particular
    //si no se pasa el parametro se utiliza la ultima variacion ingresada.

    function generar_cuotas($variacion = false,  $micro = 0) {
        $credito_id = $this->_id_credito;

        $bDb = true;
        $cuotas_restantes = 0;
        if (!$variacion) {
            $this->_db->order_by("FECHA", "desc");
            $variacion = $this->get_row_variaciones();

            $cuotas_restantes = $variacion['CANTIDAD_CUOTAS'] - 1;
        } else {
            $cuotas_restantes = $variacion['CANTIDAD_CUOTAS'];
        }

        //si no quedan cuotas para generar se deuvleve vacio
        if ($cuotas_restantes == 0)
            return array();

        //desactivamos las cuotas a remplazar si existieran
        //si es por db se modifican los registros
        $cantidad_cuotas_anteriores = 0;


        $this->_db->where("_PARENT = 0");
        $todas_cuotas = $this->get_tabla_cuotas();

        //obtenemos las cuotas anteriores y posteriores a la fecha de la variacion
        $cuotas_anteriores = array();
        $cuotas_siguientes = array();
        $cuotas_restantes_cont = $cuotas_restantes;
        foreach ($todas_cuotas as $cuota) {
            if ($cuota['FECHA_INICIO'] < $variacion['FECHA']) {
                $cuotas_anteriores[] = $cuota;
            } else {
                $cuota['CUOTAS_RESTANTES'] = $cuotas_restantes_cont--;
                $cuotas_siguientes[] = $cuota;
            }
        }

        $cantidad_cuotas_iniciadas = count($cuotas_anteriores);

        //obtenemos las cuotas que se borraran para tener las fechas de inicio y finalizacion previamente establecidas.
        $this->_db->delete("fid_creditos_cuotas", "ID_CREDITO = " . $credito_id . " AND FECHA_INICIO > " . $variacion['FECHA'] . " AND ID_VERSION = " . $this->_id_version);



        $IVA = $variacion['IVA'];
        $monto = $variacion['CAPITAL'];
        //cuotas gracia
        $cuotas_gracia = $variacion['CUOTAS_GRACIA'] - $cantidad_cuotas_iniciadas;
        $cuotas_gracia = $cuotas_gracia < 0 ? 0 : $cuotas_gracia;


        $fecha = $variacion['FECHA'];
        $fecha_venvimiento = $variacion['FECHA_INICIO'];
        $periodicidad = $variacion['PERIODICIDAD'];
        $DIA_INICIO = date("d", $fecha_venvimiento);
        $YEAR_INICIO = date("Y", $fecha_venvimiento);
        $MES_INICIO = date("m", $fecha_venvimiento);
        $fecha_venvimiento = mktime(0, 0, 0, $MES_INICIO + ($periodicidad * $cantidad_cuotas_iniciadas), $DIA_INICIO, $YEAR_INICIO);

        $cuotas_amort = $cuotas_restantes - $cuotas_gracia;

        $monto_cuotas = $monto / $cuotas_amort;
        $cuotas_arr = array();

        for ($i = 0; $i < $cuotas_restantes; $i++) {
            $cuotas_arr[$i]['_ACTIVA'] = 1;
            $cuotas_arr[$i]['ID_CREDITO'] = $credito_id;

            $cuotas_arr[$i]['CUOTAS_RESTANTES'] = $cuotas_restantes - $i;

            //comparamos la cuota que se esta realizando con la cuota eliminada
            //para no ingresar fechas diferentes previamente modificadas
            $bcuota_exist = false;
            if (isset($cuotas_siguientes[$i])) {
                if ($cuotas_arr[$i]['CUOTAS_RESTANTES'] == $cuotas_siguientes[$i]['CUOTAS_RESTANTES']) {
                    if ($cuotas_siguientes[$i]['FECHA_INICIO'] > 0) {
                        $bcuota_exist = true;
                        $fecha_inicio = $cuotas_siguientes[$i]['FECHA_INICIO'];
                        $fecha_venvimiento = $cuotas_siguientes[$i]['FECHA_VENCIMIENTO'];
                    }
                }
            }
            if (!$bcuota_exist) {
                $fecha_inicio = $fecha_venvimiento;
                $fecha_venvimiento = mktime(0, 0, 0, date("m", $fecha_venvimiento) + $periodicidad, $DIA_INICIO, date("Y", $fecha_venvimiento));
            }

            $cuotas_arr[$i]['POR_INT_COMPENSATORIO'] = $variacion['POR_INT_COMPENSATORIO'] / 12 * $periodicidad;
            $cuotas_arr[$i]['POR_INT_MORATORIO'] = $variacion['POR_INT_MORATORIO'];
            $cuotas_arr[$i]['POR_INT_PUNITORIO'] = $variacion['POR_INT_PUNITORIO'];

            $cuotas_arr[$i]['FECHA_GENERADA'] = $fecha;

            $cuotas_arr[$i]['FECHA_INICIO'] = $fecha_inicio;
            $cuotas_arr[$i]['FECHA_VENCIMIENTO'] = $fecha_venvimiento;

            $rango = ($fecha_venvimiento - $fecha_inicio) / 86400;


            if ($micro==1){
                $cuotas_arr[$i]['FECHA_ENVIADA'] = $fecha_inicio;
            }
            
            //CUOTAS DE GRACIA
            if ($cuotas_gracia > $i) {

                $monto_restante = $monto;
                $divisor = $cuotas_arr[$i]['CUOTAS_RESTANTES'] - ( $cuotas_gracia + $i );
                $cuotas_arr[$i]['CAPITAL_CUOTA'] = 0;

                $cuotas_arr[$i]['INT_COMPENSATORIO'] = $this->_calcular_interes($monto_restante, $rango, $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA']);
                $cuotas_arr[$i]['INT_COMPENSATORIO_IVA'] = $cuotas_arr[$i]['INT_COMPENSATORIO'] * $IVA;

                //----------------------------------------------------------------------------------
                $INT_SUBSIDIO = $variacion['POR_INT_SUBSIDIO'] ;
                $interes_subsidio = $this->_calcular_interes($monto_restante, $rango, $INT_SUBSIDIO, $variacion['PERIODICIDAD_TASA'], false);

                $cuotas_arr[$i]['INT_COMPENSATORIO_SUBSIDIO'] = $interes_subsidio;
                $cuotas_arr[$i]['INT_COMPENSATORIO_IVA_SUBSIDIO'] = $interes_subsidio * IMP_IVA;
                //----------------------------------------------------------------------------------


                $cuotas_arr[$i]['SALDO_CAPITAL'] = $monto_restante;

                $cuotas_arr[$i]['INT_MORATORIO'] = 0;
                $cuotas_arr[$i]['INT_PUNITORIO'] = 0;
                $cuotas_arr[$i]['ID_VERSION'] = $this->_id_version;
                $cuotas_arr[$i]['_ID_VARIACION'] = $variacion['ID'];
            } else {


                $monto_restante = $monto - $monto_cuotas * ($i - $cuotas_gracia);
                echo "<br/>MONTO:".$monto_restante;
                $divisor = $cuotas_arr[$i]['CUOTAS_RESTANTES'] + ($cantidad_cuotas_anteriores == 0 ? 0 : 1);
                $cuotas_arr[$i]['CAPITAL_CUOTA'] = $monto_restante / $divisor;

                $cuotas_arr[$i]['INT_COMPENSATORIO'] = $this->_calcular_interes($monto_restante, $rango, $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA']);
                $cuotas_arr[$i]['INT_COMPENSATORIO_IVA'] = $cuotas_arr[$i]['INT_COMPENSATORIO'] * $IVA;

                //----------------------------------------------------------------------------------
                $INT_SUBSIDIO = $variacion['POR_INT_SUBSIDIO'];
                $interes_subsidio = $this->_calcular_interes($monto_restante, $rango, $INT_SUBSIDIO, $variacion['PERIODICIDAD_TASA'], false);

                $cuotas_arr[$i]['INT_COMPENSATORIO_SUBSIDIO'] = $interes_subsidio;
                $cuotas_arr[$i]['INT_COMPENSATORIO_IVA_SUBSIDIO'] = $interes_subsidio * IMP_IVA;
                //----------------------------------------------------------------------------------

                $cuotas_arr[$i]['SALDO_CAPITAL'] = $monto_restante;

                $cuotas_arr[$i]['INT_MORATORIO'] = 0;
                $cuotas_arr[$i]['INT_PUNITORIO'] = 0;
                $cuotas_arr[$i]['_ID_VARIACION'] = $variacion['ID'];

                $cuotas_arr[$i]['ID_VERSION'] = $this->_id_version;
            }
        }
        if ( $bDb) {

            foreach ($cuotas_arr as $cuota) {
                
                $this->_db->insert("fid_creditos_cuotas", $cuota);
            }
        }

        if (!$bDb) {
            $cuotas_arr = array_merge($cuotas_arr_prev, $cuotas_arr);
        }

        return $cuotas_arr;
    }
    
    function get_next_id(){
        $this->_db->select("max( ID ) +1 AS ultimo");
        $row = $this->_db->get_row("fid_creditos");
        if ($row['ultimo'] < 1200){
            return 1200;
        }
        else{
            return $row['ultimo'];
        }
    }
    
    
}

?>