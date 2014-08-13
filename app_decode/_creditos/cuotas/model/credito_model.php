<?php

define("IMP_IVA", 0.21);

class credito_model extends main_model {

    var $_i = 0;

    function assign_id_evento($id_evento, $tipo){
        switch($tipo){
            case 1:
                $this->_db->update("fid_creditos_desembolsos", array("ID_VARIACION"=>$id_evento),"ID_VARIACION = 999999");
                break;
            case 2:
                $this->_db->update("fid_cambiotasas", array("ID_VARIACION"=>$id_evento),"ID_VARIACION = 999999");
                break;
            case 3:
                $this->_db->update("fid_pagos", array("ID_VARIACION"=>$id_evento),"ID_VARIACION = 999999");
                break;
        }
    }

    function obtener_variaciones_credito($credito_id = false) {

        if ($credito_id) {
            $this->_db->order_by("FECHA", "asc");
            $this->_db->where("ID_CREDITO =" . $credito_id);
        }
        $variaciones = $this->_db->get_tabla("fid_credito_variaciones");
        return $variaciones;
    }

    
    //se gneran las cuotas a partir de una variacion en particular
    //si no se pasa el parametro se utiliza la ultima variacion ingresada.
    
    function generar_cuotas($credito_id, $variacion = false, $cuotas_arr_prev = array()) {

        $bDb = true;
        $cuotas_restantes = 0;
        if (!$variacion) {
            $this->_db->order_by("FECHA", "desc");
            $this->_db->where("ID_CREDITO =" . $credito_id);
            $variacion = $this->_db->get_row("fid_credito_variaciones");
            $cuotas_restantes = $variacion['CANTIDAD_CUOTAS'] - 1;
            echo "CUOTAS_RESTANTES1:".$cuotas_restantes;
        }
        else{
            $cuotas_restantes = $variacion['CANTIDAD_CUOTAS']  ;
            echo "CUOTAS_RESTANTES2:".$cuotas_restantes;
        }

        //si no quedan cuotas para generar se deuvleve vacio
        if ($cuotas_restantes==0) 
            return array();
        
        //desactivamos las cuotas a remplazar si existieran
        //si es por db se modifican los registros
        $cantidad_cuotas_anteriores = 0;
        if (!$cuotas_arr_prev) {
            $this->_db->where("ID_CREDITO = " . $credito_id);
//            $this->_db->where("FECHA_INICIO < " . $variacion['FECHA'] . " AND _PARENT = 0");
            $this->_db->where("_PARENT = 0");
            $todas_cuotas = $this->_db->get_tabla("fid_cuotas");
            
            //obtenemos las cuotas anteriores y posteriores a la fecha de la variacion
            $cuotas_anteriores = array();
            $cuotas_siguientes = array();
            $cuotas_restantes_cont = $cuotas_restantes;
            foreach($todas_cuotas as $cuota){
                if ($cuota['FECHA_INICIO'] < $variacion['FECHA']){
                    $cuotas_anteriores[] = $cuota;
                }
                else{
                    $cuota['CUOTAS_RESTANTES'] = $cuotas_restantes_cont--;
                    $cuotas_siguientes[] = $cuota;
                }
            }
/*            echo "<br/>CUOTAS_ANTERIORES:";
            print_array($cuotas_anteriores);
            echo "<br/>CUOTAS_SIGUIENTES:";
            print_array($cuotas_siguientes);*/
            $cantidad_cuotas_iniciadas = count($cuotas_anteriores);

            //obtenemos las cuotasque se borraran para tener las fechas de inicio y finalizacion previamente establecidas.
            
            
            $this->_db->delete("fid_cuotas", "ID_CREDITO = " . $credito_id . " AND FECHA_INICIO > " . $variacion['FECHA']);
        }  //si es por array se eliminan todos los elementos desde la fecha de la variacion en adelante
        else {
            $bDb = false;
            //si no es un array se busca en la db las cuotas para inicializar el array prev.
            if ($cuotas_arr_prev === true) {

                $this->_db->where("ID_CREDITO = " . $credito_id . " AND _ACTIVA = 1 AND _PARENT = 0");
                $cuotas_arr_prev = $this->_db->get_tabla("fid_cuotas");
            }

            for ($i = 0; $i < count($cuotas_arr_prev); $i++) {
                if ($cuotas_arr_prev[$i]['FECHA_VENCIMIENTO'] > $variacion['FECHA']) {
                    array_splice($cuotas_arr_prev, $i);
                    break;
                }
            }
        }


        $IVA = $variacion['IVA'];
        $monto = $variacion['CAPITAL'];

        //cuotas gracia
        $cuotas_gracia = $variacion['CUOTAS_GRACIA'] - $cantidad_cuotas_iniciadas ;
        $cuotas_gracia   = $cuotas_gracia  < 0 ? 0 : $cuotas_gracia  ;
        
        
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
            if (isset($cuotas_siguientes[$i])){
                if ($cuotas_arr[$i]['CUOTAS_RESTANTES']==$cuotas_siguientes[$i]['CUOTAS_RESTANTES']){
                    if ($cuotas_siguientes[$i]['FECHA_INICIO'] > 0){
                        $bcuota_exist = true;
                        $fecha_inicio = $cuotas_siguientes[$i]['FECHA_INICIO'];
                        $fecha_venvimiento = $cuotas_siguientes[$i]['FECHA_VENCIMIENTO'];
                    }
                }
            }
            if (!$bcuota_exist){
                $fecha_inicio = $fecha_venvimiento;
                $fecha_venvimiento = mktime(0, 0, 0, date("m", $fecha_venvimiento) + $periodicidad, $DIA_INICIO, date("Y", $fecha_venvimiento));
            }

            $cuotas_arr[$i]['POR_INT_COMPENSATORIO'] = $variacion['POR_INT_COMPENSATORIO'] / 12 * $periodicidad;
            $cuotas_arr[$i]['POR_INT_MORATORIO'] = $variacion['POR_INT_MORATORIO'];
            $cuotas_arr[$i]['POR_INT_PUNITORIO'] = $variacion['POR_INT_PUNITORIO'];

            $cuotas_arr[$i]['FECHA_GENERADA'] = $fecha;
            
            $cuotas_arr[$i]['FECHA_INICIO'] = $fecha_inicio;
            $cuotas_arr[$i]['FECHA_VENCIMIENTO'] = $fecha_venvimiento ;

            if ($cuotas_gracia > $i) {

                $monto_restante = $monto;
                $divisor = $cuotas_arr[$i]['CUOTAS_RESTANTES'] - ( $cuotas_gracia + $i );
                $cuotas_arr[$i]['CAPITAL_CUOTA'] = 0;

                $cuotas_arr[$i]['INT_COMPENSATORIO'] = $this->calcular_interes($monto_restante, 30 * $variacion['PERIODICIDAD'], $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA']);
                $cuotas_arr[$i]['INT_COMPENSATORIO_IVA'] = $cuotas_arr[$i]['INT_COMPENSATORIO'] * $IVA;
                $cuotas_arr[$i]['SALDO_CAPITAL'] = $monto_restante;

                $cuotas_arr[$i]['INT_MORATORIO'] = 0;
                $cuotas_arr[$i]['INT_PUNITORIO'] = 0;
                $cuotas_arr[$i]['_ID_VARIACION'] = $variacion['ID'];
            } else {

                $monto_restante = $monto - $monto_cuotas * ($i - $cuotas_gracia);
                $divisor = $cuotas_arr[$i]['CUOTAS_RESTANTES'] + ($cantidad_cuotas_anteriores == 0 ? 0 : 1);
                $cuotas_arr[$i]['CAPITAL_CUOTA'] = $monto_restante / $divisor;

                $cuotas_arr[$i]['INT_COMPENSATORIO'] = $this->calcular_interes($monto_restante, 30 * $variacion['PERIODICIDAD'], $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA']);
                $cuotas_arr[$i]['INT_COMPENSATORIO_IVA'] = $cuotas_arr[$i]['INT_COMPENSATORIO'] * $IVA;
                $cuotas_arr[$i]['SALDO_CAPITAL'] = $monto_restante;

                $cuotas_arr[$i]['INT_MORATORIO'] = 0;
                $cuotas_arr[$i]['INT_PUNITORIO'] = 0;
                $cuotas_arr[$i]['_ID_VARIACION'] = $variacion['ID'];
            }
        }
        if (!$cuotas_arr_prev && $bDb) {

            foreach ($cuotas_arr as $cuota) {
                $this->_db->insert("fid_cuotas", $cuota);
            }
        }

        if (!$bDb) {
            $cuotas_arr = array_merge($cuotas_arr_prev, $cuotas_arr);
        }

        return $cuotas_arr;
    }

    function generar_evento(
    $id, $data, $update = false, $fecha = false) {

        $fecha = (!$fecha) ? time() : $fecha;

        //si se activa la opcion update toma los datos de la variacion anterior y actualiza con el paramentro data
        //los campos que corresponden
        if ($update) {
            $desembolso = isset($data['monto']) ? $data['monto'] : 0;
            $data['TIPO'] = isset($data['TIPO']) ? $data['TIPO'] : 5000;
            $data['monto'] = $desembolso;

            //variacion anterior por fecha
            $this->_db->order_by("FECHA", "desc");
            $this->_db->where("FECHA < " . $fecha);
            $this->_db->where("ID_CREDITO = " . $id);
            $variacion = $this->_db->get_row("fid_credito_variaciones");

            //si el tipo es mayor a 0 (no es inicial)
            if ($data['TIPO'] > 0) {

                //si no encuentra varacion anterior
                if (!$variacion) {
                    $this->_db->order_by("FECHA", "asc");
                    $this->_db->where("ID_CREDITO = " . $id);
                    $variacion = $this->_db->get_row("fid_credito_variaciones");

                    //si encuentra una variacion posterior se modifica a la fecha del evento actual
                    if ($variacion) {
                        $this->_db->update("fid_credito_variaciones", array("FECHA" => $fecha - 1000, "FECHA_INICIO" => $fecha - 1000), "ID = " . $variacion['ID']);
                        $variacion['FECHA'] = $fecha - 1000;
                    }
                }
            }
            if (!$variacion)
                return false;

            //las cuotas restantes se evaluan desde el inicio del credito
            $this->_db->order_by("CUOTAS_RESTANTES", "desc");
            $this->_db->where("ID_CREDITO = " . $id);
            $this->_db->where("_PARENT = 0");
            $this->_db->where("FECHA_INICIO < " . ($fecha + 1));
            $cuotas = $this->_db->get_tabla("fid_cuotas");
            
            //si no existen cuotas iniciales se busca la primer cuota existente
            if (!$cuotas){
                $this->_db->order_by("CUOTAS_RESTANTES", "desc");
                $this->_db->where("ID_CREDITO = " . $id);
                $this->_db->where("_PARENT = 0");
                $this->_db->where("FECHA_INICIO >= " . ($fecha ));
                $cuotas = $this->_db->get_tabla("fid_cuotas");
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

            //$data['cuotas_gracia'] = (key_exists('cuotas_gracia', $data)) ? $data['cuotas_gracia'] : $variacion['CUOTAS_GRACIA'];
            $data['cuotas_gracia'] = $variacion['CUOTAS_GRACIA'];

            $data['cuotas'] = (key_exists('cuotas', $data)) ? $data['cuotas'] : $cuotas[count($cuotas) - 1]['CUOTAS_RESTANTES'];
        }
        $data['TIPO'] = isset($data['TIPO']) ? $data['TIPO'] : 0;
        $data['ESTADO'] = isset($data['ESTADO']) ? $data['ESTADO'] : 0;

        //si el tipo es mayor a 0 significa que no es el primero y al primero, si existe se debe modificar el monto a 0 ya que es solo un capital teorico para simulacion
        if ($data['TIPO'] > 0) {
            $this->_db->update("fid_credito_variaciones", array("CAPITAL" => 0), "ID_CREDITO = " . $id . " AND TIPO = 0");
        }
        $ret = array(
            "ID_CREDITO" => $id,
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
            "FECHA" => $fecha
        );
        $id = $this->_db->insert("fid_credito_variaciones", $ret);
        $ret['ID'] = $id;

        return $ret;
    }

    function elimina_evento($id) {
        $this->_db->delete("fid_credito_variaciones", "ID = " . $id);
    }

    function eliminar_tmp_events($id_credito) {
        $this->_db->delete("fid_credito_variaciones", "ID_CREDITO = " . $id_credito . " AND TIPO = 5000");
    }

    function get_cuotas_credito($credito) {
        $this->_db->order_by("CUOTAS_RESTANTES", "desc");
        $this->_db->where("ID_CREDITO = " . $credito . " AND _ACTIVA = 1 AND _PARENT = 0 ");
        $cuotas = $this->_db->get_tabla("fid_cuotas");

        //obtenemos primer desemmbolso
        $this->_db->order_by("FECHA","asc");
        $primer_desembolso = $this->_db->get_row("fid_credito_variaciones","Tipo = 1 AND ID_CREDITO = ".$credito);
        
        $res = array("CANTIDAD" => count($cuotas));

        $arr_result = array();

        $cantidad_cuotas = count($cuotas);
        
        for ($x = 0; $x < $cantidad_cuotas; $x++) {
            
            if ($x==0 && $primer_desembolso){
                $cuotas[$x]['FECHA_INICIO'] = $primer_desembolso['FECHA'];
            }

            $saldo_arr = $this->_get_saldo_capital($credito, $cuotas[$x]['FECHA_INICIO'] + 1000, true);
            //$cuotas[$x]['SALDO'] = $saldo_arr['SALDO_TEORICO'];
            $cuotas[$x]['SALDO'] = $saldo_arr['SALDO'];

            $cuotas[$x]['SALDO_CAPITAL_TEORICO'] = $saldo_arr['SALDO_TEORICO'];
            $cuotas[$x]['AMORTIZACION_TEORICA'] = $saldo_arr['AMORTIZACION_CUOTA'];
            $cuotas[$x]['CAPITAL_CUOTA'] = $saldo_arr['AMORTIZACION_CUOTA'];
            $cuota = $cuotas[$x];
            
            $this->_db->select("c.*, cv.FECHA, cv.ID, cv.TIPO");
            $this->_db->order_by("c.FECHA_INICIO", "asc");
            $this->_db->where("c._ID_VARIACION = cv.ID");

            $this->_db->where("FECHA >= ".$cuotas[$x]['FECHA_INICIO']);
            
            $this->_db->where("c.FECHA_VENCIMIENTO <= ".$cuotas[$x]['FECHA_VENCIMIENTO']);
            $this->_db->where("_PARENT = " . $cuota['ID']);
            $this->_db->join("fid_credito_variaciones cv", "c._ID_VARIACION = cv.ID AND cv.FECHA >= c.FECHA_INICIO AND cv.FECHA <= c.FECHA_VENCIMIENTO");
            $segmentos = $this->_db->get_tabla("fid_cuotas c");

            $this->_db->where("FECHA >= ".$cuotas[$x]['FECHA_INICIO']);
            
            $this->_db->where("FECHA <= ".$cuotas[$x]['FECHA_VENCIMIENTO']);
            $this->_db->where("ID_CREDITO = ".$credito);
            $gastos = $this->_db->get_tabla("fid_gastos");
            $cuota['SEGMENTOS'] = array();

            $desde = $cuota['FECHA_INICIO'];
            if ($segmentos){
                if ($x==0){
                    //$this->_db->where("FECHA >= ".$primer_desembolso['FECHA']);
                    $desde = $primer_desembolso['FECHA'];
                }
                else{
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
                        $this->_db->join("fid_credito_variaciones cv", "cv.ID = d.ID_VARIACION");
                        $this->_db->where("cv.ID = ".$segmento['_ID_VARIACION']);
                        $desembolso = $this->_db->get_row("fid_creditos_desembolsos d");
                        if ($desembolso) {
                            $tipo = 'Desembolso';
                            $segmento['MONTO'] = $desembolso ? $desembolso['MONTO'] : 0;
                            $segmento['MONTO'] = " $" . $segmento['MONTO'];
                        }
                        break;

                    //CAMBIO TASA
                    case 2:
                        $this->_db->join("fid_credito_variaciones cv", "cv.ID = t.ID_VARIACION");
                        $this->_db->where("t.FECHA = " . $segmento['FECHA']);
                        $this->_db->where("t.CUOTAS_RESTANTES = " . $segmento['CUOTAS_RESTANTES']);
                        $tasa = $this->_db->get_row("fid_cambiotasas t");
                        $tipo = 'Cambio Tasa';
                        $segmento['MONTO'] = 0;
                        if ($tasa) {
                            $tipo = 'Cambio Tasa';
                            $segmento['MONTO'] = $tasa ? $tasa['TASA'] : 0;
                            $segmento['MONTO'] = " %" . $segmento['MONTO'];
                        }
                        break;

                    //RECUPERO
                    case 3:
                        
                        $this->_db->select("SUM(MONTO) as SUMA_MONTO");
                        $this->_db->join("fid_credito_variaciones cv", "cv.ID = p.ID_VARIACION");
                        $this->_db->where("p.ID_VARIACION = " . $segmento['_ID_VARIACION']);
                        $this->_db->group_by("p.ID_VARIACION");

                        $pagos = $this->_db->get_row("fid_pagos p");

                        $segmento['MONTO'] = 0;
                        $tipo = 'Recupero';
                        if ($pagos) {

                            $tipo = 'Recupero';
                            $segmento['MONTO'] = $pagos ? $pagos['SUMA_MONTO'] : 0;
                            $segmento['MONTO'] = ' $' . round($segmento['MONTO'], 2);
                        }
                        break;
                }

                $hasta = $segmento['FECHA_VENCIMIENTO'];
                $segmento['FECHA_INICIO'] = $desde;
                $segmento['DIAS_TRANSCURRIDOS'] = floor(($hasta - $desde) / (3600 * 24));
                $segmento['TIPO'] = $tipo;

                $desde = $hasta;
                
                $cuota['SEGMENTOS'][] = $segmento;
            }
            $cuota['GASTOS'] = $gastos;
            $arr_result[] = $cuota;
        }
        
        $ultima_cuota = end($cuotas);

        $this->_db->where("FECHA > " . $ultima_cuota['FECHA_VENCIMIENTO']);
        $this->_db->where("ID_CREDITO = " . $credito);
        $variaciones = $this->_db->get_tabla("fid_credito_variaciones");
        
        
        for ($i = 0; $i < count($variaciones); $i++) {
            $variacion = $variaciones[$i];
            $this->_db->select("SUM(MONTO) as SUMA_MONTO");
            $this->_db->join("fid_credito_variaciones cv", "cv.ID = p.ID_VARIACION");
            $this->_db->where("p.ID_CREDITO = " . $credito);
            $this->_db->where("p.ID_VARIACION = " . $variacion['ID']);
            $this->_db->group_by("p.ID_VARIACION");

            $pagos = $this->_db->get_row("fid_pagos p");
            $monto = 0;
            $variaciones[$i]['TOTAL'] = 0;
            $variaciones[$i]['TIPO'] = -1;            
            if ($pagos) {
                $tipo = 'Recupero';
                $monto = $pagos ? $pagos['SUMA_MONTO'] : 0;
            
                $monto = " $" . round($monto, 2);



                $variaciones[$i]['TOTAL'] = $monto;
                $variaciones[$i]['TIPO'] = $tipo;
            }
        }


        
        $res['RESULT'] = $arr_result;
        $res['VARIACIONES'] = $variaciones;
        $res['PRIMER_DESEMBOLSO'] = $primer_desembolso;
        return $res;
    }

    function get_deuda($credito_id, $fecha = false) {

        $IVA = IMP_IVA;

        $fecha = !$fecha ? time() : $fecha;
        
        //obtenemos gastos
        $this->_db->where("ID_CREDITO = " . $credito_id);
        $this->_db->where("ESTADO = 0");
        $this->_db->where("FECHA < " . $fecha);
        $gastos = $this->_db->get_tabla("fid_gastos");

        //obtenemos cuotas
        $this->_db->where("ID_CREDITO = " . $credito_id);
        $this->_db->order_by("FECHA", "asc");
        $variacion_inicial = $this->_db->get_row("fid_credito_variaciones c");


        $periodicidad = $variacion_inicial ['PERIODICIDAD'];
        $arr_deuda = array("gastos" => array(), "cuotas" => array());


        //GASTOS
        $gastos_arr = array();
        foreach ($gastos as $gasto) {
            $pago_gasto = $this->_db->get_tabla("fid_pagos", "ID_CREDITO = " . $credito_id . " AND ID_TIPO = 8 AND CUOTAS_RESTANTES = " . $gasto['ID']);
            
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

        $cuotas = $this->get_segmentos_cuota($credito_id, $fecha);

        
        for ($i = 0; $i < count($cuotas); $i++) {
            //buscamos los gastos correspondientes a la fecha de la cuota
            $arr_gastos = array();
            for($g = 0 ; $g < count($gastos_arr) ; $g++ ){
                if ($gastos_arr[$g]['ROW']['FECHA'] >= $cuotas[$i]['FECHA_INICIO'] && 
                    $gastos_arr[$g]['ROW']['FECHA'] <= $cuotas[$i]['FECHA_VENCIMIENTO'] ){
                    unset($gastos_arr['ROW']);
                    $arr_gastos = $gastos_arr[$g];
                    
                    break;
                }
            }
            $cuota = $cuotas[$i];

            $arr_saldo = $this->_get_saldo_capital($credito_id, $cuota['FECHA_VENCIMIENTO']-1, true);
            $SALDO_CAPITAL = $arr_saldo['SALDO'];


            $CUOTAS_RESTANTES = $cuota['CUOTAS_RESTANTES'];
            $dif_dias = ceil(($fecha - $cuota['FECHA_VENCIMIENTO']) / (60 * 60 * 24));
            //CAPITAL (7)
            $this->_db->where("ID_CREDITO = " . $credito_id . " AND ID_TIPO = 7");
            $this->_db->where("CUOTAS_RESTANTES = " . $CUOTAS_RESTANTES);
            $this->_db->where("FECHA <= " . $fecha);

            $pago = $this->_db->get_tabla("fid_pagos");
            
            $arr_capital = array(
                "TOTAL" => $arr_saldo['AMORTIZACION_CUOTA'],
                "PAGOS" => $pago,
                "TIPO" => 7,
                "SALDO" => $arr_saldo['AMORTIZACION_CUOTA'] - $this->_get_saldo($pago));



            if ($cuota['ESTADO'] == 1) {
                //IVA COMPENSATORIO
                $arr_iva_compensatorio = array(
                    "TOTAL" => $cuota['INT_COMPENSATORIO'] * $IVA,
                    "PAGOS" => $pago,
                    "TIPO" => 1,
                    "SALDO" => 0);

                //COMPENSATORIO
                $arr_compensatorio = array(
                    "TOTAL" => $cuota['INT_COMPENSATORIO'],
                    "PAGOS" => $pago,
                    "TIPO" => 2,
                    "SALDO" => 0);
                //saldo de capital de la cuota + saldo de los intereses compensatorio e impuestos no cancelados
                $SALDO_CUOTA = $arr_capital['SALDO'] + $arr_compensatorio['SALDO'] + $arr_iva_compensatorio['SALDO'];

                $arr_iva_punitorio = array(
                    "TOTAL" => $cuota['INT_PUNITORIO'] * $IVA,
                    "PAGOS" => $pago,
                    "TIPO" => 1,
                    "SALDO" => 0);
                $arr_iva_moratorio = array(
                    "TOTAL" => $cuota['INT_MORATORIO'] * $IVA,
                    "PAGOS" => $pago,
                    "TIPO" => 2,
                    "SALDO" => 0);
                $arr_punitorio = array(
                    "TOTAL" => $cuota['INT_PUNITORIO'],
                    "PAGOS" => $pago,
                    "TIPO" => 4,
                    "SALDO" => 0);
                $arr_moratorio = array(
                    "TOTAL" => $cuota['INT_MORATORIO'],
                    "PAGOS" => $pago,
                    "TIPO" => 5,
                    "SALDO" => 0);
            } else {

                //IVA COMPENSATORIO
                $this->_db->where("FECHA <= ".$fecha);
                $this->_db->where("ID_CREDITO = " . $credito_id . " AND ID_TIPO = 3 AND CUOTAS_RESTANTES = " . $CUOTAS_RESTANTES);                
                $pago = $this->_db->get_tabla("fid_pagos");

                //se calcula si ya ha sido informada la cuota
                $total = $cuota['INT_COMPENSATORIO_IVA'];
                if ($cuota['FECHA_ENVIADA'] > 0) {
                    if ($fecha >= $cuota['FECHA_ENVIADA']) {
                        $total = $SALDO_CAPITAL * ( ($cuota['POR_INT_COMPENSATORIO'] / 100) / (12 / $periodicidad) ) * $IVA;
                        ;
                    }
                }

                $arr_iva_compensatorio = array(
                    "TOTAL" => $total,
                    "PAGOS" => $pago,
                    "TIPO" => 3,
                    "SALDO" => $total - $this->_get_saldo($pago));

                //COMPENSATORIO
                $this->_db->where("FECHA <= ".$fecha);
                $this->_db->where("ID_CREDITO = " . $credito_id . " AND ID_TIPO = 6 AND CUOTAS_RESTANTES = " . $CUOTAS_RESTANTES);                
                $pago = $this->_db->get_tabla("fid_pagos");
                //se calcula si ya ha sido informada la cuota
                $total = $cuota['INT_COMPENSATORIO'];
                if ($cuota['FECHA_ENVIADA'] > 0) {
                    if ($fecha >= $cuota['FECHA_ENVIADA']) {
                        $total = $SALDO_CAPITAL * ( ($cuota['POR_INT_COMPENSATORIO'] / 100) / (12 / $periodicidad) );
                    }
                }

                $arr_compensatorio = array(
                    "TOTAL" => $total,
                    "PAGOS" => $pago,
                    "TIPO" => 6,
                    "SALDO" => round($total - $this->_get_saldo($pago) ,2) );

                //saldo de capital de la cuota + saldo de los intereses compensatorio e impuestos no cancelados
                $SALDO_CUOTA = $arr_capital['SALDO'] + $arr_compensatorio['SALDO'] + $arr_iva_compensatorio['SALDO'];
//                echo "<br/>DIAS: " . $dif_dias . " - CAPITAL_SALDO:" . $arr_capital['SALDO'] . " - COMPENSATORIO:" . $arr_compensatorio['SALDO'] . " - IVACOMPENSATORIO:" . $arr_iva_compensatorio['SALDO'];

/*                foreach($cuota['SEGMENTOS'] as $segmento){
                    
                }*/
                //IVA PUNITORIO
                $this->_db->where("FECHA <= ".$fecha);
                $this->_db->where("ID_CREDITO = " . $credito_id . " AND ID_TIPO = 1 AND CUOTAS_RESTANTES = " . $CUOTAS_RESTANTES);
                $pago = $this->_db->get_tabla("fid_pagos");
                
                $tmp = $cuota['INT_PUNITORIO'] ;
                $tmp = $tmp * $IVA;

                $tmp = $tmp < 0 ? 0 : $tmp;
                $saldo = round($this->_get_saldo($pago), 2);
                $tmp = round($tmp, 2) == 0 ? $saldo : $tmp;
                $arr_iva_punitorio = array(
                    "TOTAL" => $tmp,
                    "PAGOS" => $pago,
                    "TIPO" => 1,
                    "SALDO" => ($tmp) - $saldo);

                //IVA MORATORIO
                $this->_db->where("FECHA <= ".$fecha);
                $this->_db->where("ID_CREDITO = " . $credito_id . " AND ID_TIPO = 2 AND CUOTAS_RESTANTES = " . $CUOTAS_RESTANTES);          
                $pago = $this->_db->get_tabla("fid_pagos");
                
                $tmp = $cuota['INT_MORATORIO'] ;
                
                $tmp = $tmp * $IVA;
                $tmp = $tmp < 0 ? 0 : $tmp;
                $saldo = round($this->_get_saldo($pago), 2);
                $tmp = round($tmp, 2) == 0 ? $saldo : $tmp;
                $arr_iva_moratorio = array(
                    "TOTAL" => $tmp,
                    "PAGOS" => $pago,
                    "TIPO" => 2,
                    "SALDO" => ($tmp) - $saldo);

                //PUNITORIO
                $this->_db->where("FECHA <= ".$fecha);
                $this->_db->where("ID_CREDITO = " . $credito_id . " AND ID_TIPO = 4 AND CUOTAS_RESTANTES = " . $CUOTAS_RESTANTES);
                $pago = $this->_db->get_tabla("fid_pagos");
                $tmp = $cuota['INT_PUNITORIO'] ;
                $tmp = $tmp < 0 ? 0 : $tmp;
                $saldo = round($this->_get_saldo($pago), 2);
                $tmp = round($tmp, 2) == 0 ? $saldo : $tmp;
                $arr_punitorio = array(
                    "TOTAL" => $tmp,
                    "PAGOS" => $pago,
                    "TIPO" => 4,
                    "SALDO" => ($tmp) - $saldo);

                //MORATORIO
                $this->_db->where("FECHA <= ".$fecha);
                $this->_db->where("ID_CREDITO = " . $credito_id . " AND ID_TIPO = 5 AND CUOTAS_RESTANTES = " . $CUOTAS_RESTANTES);                
                $pago = $this->_db->get_tabla("fid_pagos");
                
                $tmp = $cuota['INT_MORATORIO'] ;

                $tmp = $tmp < 0 ? 0 : $tmp;
                $saldo = $this->_get_saldo($pago);
                $tmp = round($tmp, 2) == 0 ? $saldo : $tmp;
                $arr_moratorio = array(
                    "TOTAL" => $tmp,
                    "PAGOS" => $pago,
                    "TIPO" => 5,
                    "SALDO" => ($tmp) - $saldo);
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
                        "NUM" => $variacion_inicial['CANTIDAD_CUOTAS'] - $cuota['CUOTAS_RESTANTES']  + 1,
                        "ENVIO" => $cuota['FECHA_ENVIADA'],
                        "DESDE" => $cuota['FECHA_INICIO'],
                        "HASTA" => $cuota['FECHA_VENCIMIENTO'],
                        "ESTADO" => $cuota['ESTADO'],
                        "ESTADO" => $cuota['ESTADO'],
                        "DIF_DIAS" => $dif_dias,
                        "SALDO_CAPITAL" => $SALDO_CAPITAL,
                        "SALDO_CUOTA" => $SALDO_CUOTA,
                        "TOT_INT_MOR_PUN" => $arr_punitorio['SALDO'] + $arr_moratorio['SALDO'],
                        "TOT_IVA_INT_MOR_PUN" => $arr_iva_punitorio['SALDO'] + $arr_iva_moratorio['SALDO'],
                        "TOTAL_PAGAR" => $SALDO_CUOTA + $arr_iva_punitorio['SALDO'] + $arr_iva_moratorio['SALDO'] + $arr_punitorio['SALDO'] + $arr_moratorio['SALDO']
                    )
                );
            } else {
                $arr_deuda['cuotas'][] = array(
                    "GASTOS" => $arr_gastos,
                    "IVA_COMPENSATORIO" => $arr_iva_compensatorio,
                    "COMPENSATORIO" => $arr_compensatorio,
                    "CAPITAL" => $arr_capital,
                    "ID" => $cuota['ID'],
                    "CUOTAS_RESTANTES" => $cuota['CUOTAS_RESTANTES'],
                    "_INFO" =>
                    array(
                        "NUM" =>  $variacion_inicial['CANTIDAD_CUOTAS'] - $cuota['CUOTAS_RESTANTES']   + 1,
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

    function _get_saldo($pagos) {
        $total = 0;
        foreach ($pagos as $pago) {
            $total += $pago['MONTO'];
        }
        return $total;
    }

    function pagar_deuda($arr_deuda, $monto, $fecha, $id_credito = 1) {
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
                    "ID_VARIACION" => "999999"
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
                        "ID_VARIACION" => "999999"
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
        
        //TIPO 10 es un monto no asignado.
        if ($monto > 0){
            $arr_pago[] = array(
                "ID_CREDITO" => $id_credito,
                "FECHA" => $fecha,
                "ID_TIPO" => 10,
                "MONTO" => $monto,
                "CUOTAS_RESTANTES" => 0,
                "ID_VARIACION" => "999999"
            );
        }
        
        //TIPO 11 es monto asignado a subsidio (porcentaje pago de compensatorio e impuesto compensatorio)
        /*
        */
        
        foreach ($arr_pago as $pago) {
            if ((round($pago['MONTO'], 2) > 0))
                $this->_db->insert("fid_pagos", $pago);
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
            $this->_db->update("fid_cuotas", $int_array, "ID = " . $cuota['ID']);
        }
        return $arr_pago;
    }

    function agregar_desembolso($id_credito, $desembolso, $cuotas_restantes, $fecha = false) {

        $fecha = !$fecha ? time() : $fecha;
        $this->_db->insert("fid_creditos_desembolsos", array(
            "ID_CREDITO" => $id_credito,
            "MONTO" => $desembolso,
            "FECHA" => $fecha,
            "CUOTAS_RESTANTES" => $cuotas_restantes,
            "ID_VARIACION" => "999999"
        ));
    }

    function agregar_tasa($id_credito, $tasa, $cuotas_restantes, $fecha = false) {

        $fecha = !$fecha ? time() : $fecha;
        $this->_db->insert("fid_cambiotasas", array(
            "ID_CREDITO" => $id_credito,
            "TASA" => $tasa,
            "FECHA" => $fecha,
            "CUOTAS_RESTANTES" => $cuotas_restantes,
            "ID_VARIACION" => "999999"
        ));
        
        
        
    }

    function agregar_gasto($id_credito, $monto, $fecha = false) {

        $fecha = !$fecha ? time() : $fecha;
        $this->_db->insert("fid_gastos", array(
            "ID_CREDITO" => $id_credito,
            "MONTO" => $monto,
            "CONCEPTO" => "prueba de gastos",
            "ESTADO" => 0,
            "FECHA" => $fecha,
            "ID_VARIACION" => "999999"
            ));
    }

    function borrar_credito($cred) {
        $this->_db->delete("fid_cuotas", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_creditos_desembolsos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_gastos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_credito_variaciones", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_pagos", "ID_CREDITO = " . $cred);
        $this->_db->delete("fid_cambiotasas", "ID_CREDITO = " . $cred);
    }

    function guardar_cuotas($credito_id, $arr_cuotas) {
        $this->_db->delete("fid_cuotas", "ID_CREDITO = " . $credito_id);

        foreach ($arr_cuotas as $cuota) {
            unset($cuota['ID']);
            $this->_db->insert("fid_cuotas", $cuota);
        }
        $this->get_segmentos_cuota($credito_id);
    }

    function get_segmentos_cuota($id_credito, $fecha = false) {

        //buscamos solo cuotas activas
        $this->_db->order_by("FECHA_INICIO", "asc");
        $this->_db->where("ID_CREDITO = " . $id_credito);
        $this->_db->where("_ACTIVA = 1");
        $this->_db->where("_PARENT = 0");
        $this->_db->where("ESTADO not in (4,5,1)");

        if ($fecha) {
            $this->_db->where("FECHA_INICIO < " . $fecha);
        }

        //si no exiten cuotas previas se generan
        $cuotas = $this->_db->get_tabla("fid_cuotas");
        if (!$cuotas) {
            $this->_db->order_by("FECHA", "ASC");
            $variacion = $this->_db->get_row("fid_credito_variaciones", "ID_CREDITO = " . $id_credito);
            $cuotas = $this->generar_cuotas($id_credito, $variacion, true);

            $this->guardar_cuotas($id_credito, $cuotas);

            die();
        }

        //$arr_cuotas = array();
        $arr_cuotas = array();

        foreach ($cuotas as $cuota) {
            $this->_get_saldo_cuota($cuota['ID'], true);

            $cuotas_make = $this->make_cuota($id_credito, $cuota['ID'], $fecha);
            $segmentos = $cuotas_make['SEGMENTOS'];
            $tmp = $this->join_segmento_cuota($cuota['ID'], $fecha);
            if (!$tmp)
                continue;
            
            $tmp['SEGMENTOS'] = $segmentos ;
            $arr_cuotas[] = $tmp;
        }
        
        return $arr_cuotas;
        //return $mk;
    }

    function join_segmento_cuota($cuota_id, $fecha) {

        $this->_db->where("_PARENT = " . $cuota_id);
        if ($fecha) {
            $this->_db->where("FECHA_VENCIMIENTO <= " . $fecha);
        }
        $this->_db->select("c.*, cv.TIPO, cv.CAPITAL, cv.POR_INT_COMPENSATORIO");
        $this->_db->where("cv.ESTADO > -1");
        $this->_db->order_by("FECHA", "ASC");
        $this->_db->join("fid_credito_variaciones cv", "cv.ID = c._ID_VARIACION");
        $segmentos = $this->_db->get_tabla("fid_cuotas c");


        $this->_db->select("c.*");
        $this->_db->order_by("cv.FECHA", "asc");
        $this->_db->where("cv.ESTADO > -1");
        $this->_db->join("fid_credito_variaciones cv", "cv.ID = c._ID_VARIACION");
        $cuota = $this->_db->get_row("fid_cuotas c", "c.ID = " . $cuota_id);


        if (!$cuota)
            return false;

        $id_credito = $cuota['ID_CREDITO'];
        $cuota['INT_COMPENSATORIO'] = 0;
        $cuota['INT_COMPENSATORIO_IVA'] = 0;

        //cuotas anteriores
        $this->_db->where("ID_CREDITO = " . $cuota['ID_CREDITO']);
        $this->_db->where("_PARENT = 0");
        $this->_db->where("FECHA_VENCIMIENTO < " . $cuota['FECHA_INICIO']);
        $cuotas_anteriores = $this->_db->get_tabla("fid_cuotas");
        $cantidad_cuotas_anteriores = count($cuotas_anteriores);

        $CANTIDAD_CUOTAS_GRACIA = 0; 

        if ($segmentos) {
            //calculamos saldo de capital
            $capital_arr = $this->_get_saldo_capital($id_credito, $cuota['FECHA_INICIO'], true);
            $SALDO_CAPITAL = $capital_arr['SALDO'];

            //calculamos saldo de capital
            $capital_arr = $this->_get_saldo_capital($id_credito, $cuota['FECHA_VENCIMIENTO'], true);

            $cuota['CAPITAL_CUOTA'] = $capital_arr['AMORTIZACION_CUOTA'];
            $cuota['SALDO_CAPITAL'] = $SALDO_CAPITAL;
            
            $int_punitorio = 0;
            $int_moratorio = 0;
            foreach ($segmentos as $segmento) {
                //se suman los intereses de los segmentos de la cuota y se actualiza en la cuota
                $cuota['INT_COMPENSATORIO'] += $segmento['INT_COMPENSATORIO'];
                $cuota['INT_COMPENSATORIO_IVA'] += $segmento['INT_COMPENSATORIO_IVA'];

                $int_moratorio += $segmento['INT_MORATORIO'];
                $int_punitorio += $segmento['INT_PUNITORIO'];
                
                $cuota['POR_INT_COMPENSATORIO'] = $segmento['POR_INT_COMPENSATORIO'];
                //if ($cuota_id==15667) echo $cuota['INT_PUNITORIO']."--<br/>";
            }
            $cuota['INT_MORATORIO'] = $int_moratorio;
            $cuota['INT_PUNITORIO'] = $int_punitorio;

        } else {
            $this->_db->order_by("FECHA", "desc");
            $this->_db->where("FECHA < " . $cuota['FECHA_VENCIMIENTO']);
            $this->_db->where("ID_CREDITO = " . $cuota['ID_CREDITO']);
            $variacion = $this->_db->get_row("fid_credito_variaciones");

            $capital_arr = $this->_get_saldo_capital($id_credito, $cuota['FECHA_INICIO'], true);

            $SALDO_CAPITAL = $capital_arr['SALDO'];
            $cuota['CAPITAL_CUOTA'] = ($capital_arr['DESEMBOLSOS'] + $capital_arr['INICIAL']) / ( $cuota['CUOTAS_RESTANTES'] + $cantidad_cuotas_anteriores - $CANTIDAD_CUOTAS_GRACIA);
            $cuota['SALDO_CAPITAL'] = $SALDO_CAPITAL;

            $int_compensatorio = $this->calcular_interes($SALDO_CAPITAL, 30 * $variacion['PERIODICIDAD'], $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA']);

            $cuota['INT_COMPENSATORIO'] = $int_compensatorio;
            $cuota['INT_COMPENSATORIO_IVA'] += $int_compensatorio * $variacion['IVA'];
        }

//        print_array($cuota);
        logthis("CUOTA_".$cuota_id);
        $this->_db->update("fid_cuotas", $cuota, "ID = " . $cuota_id);
        return $cuota;
    }

    function make_cuota($id_credito, $cuota_id, $fecha = false) {
        
        $fecha_get = $fecha;
        $this->_db->delete("fid_cuotas", "_PARENT = " . $cuota_id);

        //$cuotas = $this->_db->get_row("fid_cuotas","ID_CREDITO= ".$id_credito);
        $this->_db->order_by("FECHA_INICIO","ASC");
        $cuotas = $this->_db->get_tabla("fid_cuotas", "ID_CREDITO= " . $id_credito . " AND _PARENT = 0");
        $cantidad_cuotas = $cuotas[0]['CUOTAS_RESTANTES'];
        $cuota = array();
        foreach ($cuotas as $cuota_row) {
            if ($cuota_row['ID'] == $cuota_id) {
                $cuota = $cuota_row;
            }
        }

        if ($cuota) {
//            echo "<br/>------------------------------------------------<br/>CUOTAS RESTANTES:". $cuota['CUOTAS_RESTANTES']."<br/>----------------------------------------<br/>";
            //si no hay fecha especificada se utiliza la fecha de vencimiento de la cuota
            if (!$fecha) {
                $fecha = $cuota['FECHA_VENCIMIENTO'];
            }
            
            $bultima_cuota = false;
            $bprimera_cuota = false;
            
            //si es la primera cuota
            if ($cuota['CUOTAS_RESTANTES'] == $cantidad_cuotas){
                $bprimera_cuota = true;
            }            
            else{
                $this->_db->where("FECHA > " . $cuota['FECHA_INICIO']);
            }

            //si es la ultima cuota no se evalua el fin de la cuota para los segmentos
            if ($cuota['CUOTAS_RESTANTES'] == 1) {
                $bultima_cuota = true;
            }
            
            if (!$bultima_cuota){
                if ($cuota['FECHA_VENCIMIENTO'] < $fecha){
                    $fecha = $cuota['FECHA_VENCIMIENTO'];
                }                
                //$this->_db->where("FECHA <= " . $fecha);
            }
            
            $subcuotas = array();
            $this->_db->order_by("FECHA", "asc");
            $this->_db->where("ID_CREDITO = " . $id_credito);

            $this->_db->where("TIPO > 0");
            $this->_db->where("ESTADO > -1");
            
            $variaciones = $this->_db->get_tabla("fid_credito_variaciones");
            $bfirst = false;
            
            //print_array($variaciones);
            //si es la primera cuota la fecha de inicio de la cuota es la primera variacion(desembolso)
            if ($variaciones && $bprimera_cuota){
                $primera_variacion = array_shift($variaciones) ;
                $cuota['FECHA_INICIO'] = $primera_variacion['FECHA'];
            }
            //obtenemos la ultima variacion que afecto a la cuota si no es la primera
            if ($variaciones && !$bprimera_cuota){
                $this->_db->where("ID_CREDITO = ".$id_credito);
                $this->_db->where("FECHA <=  ".$cuota['FECHA_INICIO']);
                $this->_db->order_by("FECHA","desc");
                $primera_variacion = $this->_db->get_row("fid_credito_variaciones" );
            }
            foreach ($variaciones as $variacion) {
                //si la variacion supera la fecha de vencimiento de la cuenta se genera otro segmento
                //el segmento generado tendra el campo _ACTIVA = 2 el cual se usara para no calcular los intereses
                //compensatorios
                
                if ($variacion['FECHA'] > $cuota['FECHA_VENCIMIENTO'] && !$bfirst && $cuota['ESTADO']!=1  ){
                    
                    $bfirst = true;
                    $tmp_cuota = $cuota;
                    $tmp_cuota['TMP']['VARIACION'] = $variacion;  
                    $tmp_cuota['TMP']['VARIACION']['FECHA'] = $cuota['FECHA_VENCIMIENTO'];
                    $subcuotas[] = $tmp_cuota;
                }    
                
                //las cuotas siguientes al segmento generado no se usara para no calcular los intereses
                //compensatorios
                $tmp = $cuota;
                if ($bfirst){
                    $tmp['_ACTIVA'] = -2;
                }                
                $tmp['TMP']['VARIACION'] = $variacion;    
                $subcuotas[] = $tmp;
            }
            
            //con las condiciones anteriores de capital y de intereses
            $segmentos = array();
            if ($subcuotas) {
                $INTERES_COMPENSATORIO = 0;
                $IVA_INTERES_COMPENSATORIO = 0;
                $bfin_segmento  = false;

                $INT_SUBSIDIO_ACUMULADO = 0;
                $IVA_INT_SUBSIDIO_ACUMULADO = 0;                
                
                for ($i = 0; $i < count($subcuotas) && !$bfin_segmento ; $i++) {
                    $tmp = $subcuotas[$i];
                    
                    $bprimer_segmento = false;
                    if ( $i==0){
                        $bprimer_segmento = true;
                    }
                    
                    //si es ultimo segmento
                    $bultimo_segmento = false;
                    if ( ($i+1) == count($subcuotas) ){
                        $bultimo_segmento = true;
                    }
                    
                    //no es primera cuota y es el primer segmento, arrancamos el segmento en el inicio de cuota
                    if ($bprimer_segmento){
                        $tmp_fecha_inicio = $cuota['FECHA_INICIO'];
                        $INT_SUBSIDIO =  $primera_variacion['POR_INT_SUBSIDIO'] * $primera_variacion['POR_INT_COMPENSATORIO'] / 100;
                        $INTERES_COMPENSATORIO_VARIACION = $primera_variacion['POR_INT_COMPENSATORIO']  ;
                        $PERIODICIDAD_TASA_VARIACION = $primera_variacion['PERIODICIDAD_TASA'];
                        $PLAZO_PAGO = $primera_variacion['PLAZO_PAGO'];
                    }
                    else{
                        $tmp_fecha_inicio  = $subcuotas[$i - 1]['TMP']['VARIACION']['FECHA'];
                        $INT_SUBSIDIO = $subcuotas[$i - 1]['TMP']['VARIACION']['POR_INT_SUBSIDIO'] * $subcuotas[$i - 1]['TMP']['VARIACION']['POR_INT_COMPENSATORIO'] / 100;
                        $INTERES_COMPENSATORIO_VARIACION = $subcuotas[$i - 1]['TMP']['VARIACION']['POR_INT_COMPENSATORIO'];
                        $PERIODICIDAD_TASA_VARIACION = $subcuotas[$i - 1]['TMP']['VARIACION']['PERIODICIDAD_TASA'];
                        $PLAZO_PAGO = $subcuotas[$i - 1]['TMP']['VARIACION']['PLAZO_PAGO'];
                    }
                    
                    
                    //verificamos la existencia de subsidio para hacer calculo de dias de anulacion de subsidio
                    if ($INT_SUBSIDIO > 0){
                        
                    }

                    $tmp['FECHA_INICIO_REAL'] = $tmp_fecha_inicio;
                    $tmp_fecha_vencimiento = $subcuotas[$i ]['TMP']['VARIACION']['FECHA'];
                    
                    $tmp['FECHA_VENCIMIENTO_REAL'] = $tmp_fecha_vencimiento;

                    $fecha_inicio = $tmp_fecha_inicio;
                    $fecha_vencimiento = $tmp_fecha_vencimiento;
                    
                    //fecha vencimiento de segmento: si es el ultimo segmento la fecha de vencimiento
                    //es la misma de la cuota, de lo contrario es la siguiente variacion
                    
                    //segmentos anteriores al ultimo
                    $tmp_fecha_vencimiento = 0;
                    $tmp_fecha_inicio = 0;
                    
                    $tmp['FECHA_INICIO'] = $fecha_inicio;
                    $tmp['FECHA_VENCIMIENTO'] = $fecha_vencimiento;
                    //echo "<br/>".date("d/m/Y",$fecha_inicio) .">". date("d/m/Y",$fecha_vencimiento)."<br/>" ;
                    $capital_arr = $this->_get_saldo_capital($id_credito, $fecha_vencimiento-1, true,$cuota['CUOTAS_RESTANTES']==2);
                    //print_array($capital_arr );
                    $SALDO_CAPITAL = $capital_arr['SALDO'];
                    
                    $tmp['_PARENT'] = $cuota['ID'];
                    $tmp['_ESTADO'] = 5;
                    if ($bprimer_segmento){
                        $tmp['_ID_VARIACION'] = $primera_variacion['ID'];
                    }
                    else{
                        $tmp['_ID_VARIACION'] = $subcuotas[$i-1]['TMP']['VARIACION']['ID'];
                    }
                    $tmp['_ID_VARIACION'] = $subcuotas[$i]['TMP']['VARIACION']['ID'];
                    //se debe buscar los datos de la cuota pura original
                    $rango = ($tmp['FECHA_VENCIMIENTO'] - $tmp['FECHA_INICIO']) / (24 * 60 * 60);
                    
                    $rango = $rango < 0 ? 0 : round($rango);
                    
                    $tmp['CAPITAL_CUOTA'] = $capital_arr['AMORTIZACION_CUOTA'];

                    $tmp['POR_INT_COMPENSATORIO'] = 0;
                    $tmp['INT_COMPENSATORIO'] = 0;

                    
                    $tmp['INT_COMPENSATORIO_SUBSIDIO'] = 0;
                    

                    $INT_MORATORIO = 0;
                    $INT_PUNITORIO = 0;                    
                    
                    //si activa = -2 no se calculan los intereses compensatorios
                    //sucede cuando los segmentos se encuentran dentro de la cuota
                    if ($tmp['_ACTIVA'] != -2){
                        
                        //interese compuesto
                        if ($PERIODICIDAD_TASA_VARIACION > 0) {
                            $tmp['POR_INT_COMPENSATORIO'] = $rango / $PERIODICIDAD_TASA_VARIACION;
                            
                            $interes = $this->calcular_interes($SALDO_CAPITAL, $rango, $INTERES_COMPENSATORIO_VARIACION, $PERIODICIDAD_TASA_VARIACION,  $cuota['CUOTAS_RESTANTES']==2);
                            $interes_subsidio = $this->calcular_interes($SALDO_CAPITAL, $rango, $INT_SUBSIDIO , $PERIODICIDAD_TASA_VARIACION,  $cuota['CUOTAS_RESTANTES']==6);
                            
                            $tmp['INT_COMPENSATORIO'] = $interes;
           
                            

                        } else { //interes simple
                            $tmp['POR_INT_COMPENSATORIO'] = ($INTERES_COMPENSATORIO_VARIACION / 360) * $rango;
                            $tmp['INT_COMPENSATORIO'] = $INTERES_COMPENSATORIO_VARIACION * $tmp['CAPITAL_CUOTA'] / 100;
                        }
                        
                        $INT_SUBSIDIO_ACUMULADO += $interes_subsidio;
                        $IVA_INT_SUBSIDIO_ACUMULADO += ($interes_subsidio * $tmp['TMP']['VARIACION']['IVA']);
                        
                        $INTERES_COMPENSATORIO += ($tmp['INT_COMPENSATORIO']);
                        $IVA_INTERES_COMPENSATORIO += ($tmp['INT_COMPENSATORIO'] * $tmp['TMP']['VARIACION']['IVA']);
                        
                    } 
                    else{
                        
                    }
                    

                    //sucede cuando es la ultima cuota y los segmentos se encuentran por encima de la fecha de vencimiento
                    //o cuando es no es la ultima cuota pero la fecha de calculo es superior a la fecha de vencimiento
                    $not_enter = false;
                    
                    //SEGMENTO DENTRO DE LA CUOTA y ES ULTIMA CUOTA
                    if ($tmp['_ACTIVA']!=-2 && $bultima_cuota) $not_enter = true;
                    
                    //CUOTA CANCELADA
                    if ($cuota['ESTADO']==1) $not_enter = true;
                    
                    //FECHA DE FINALIZACION DEL SEGMENTO es igual a la FECHA DE INICIO DE LA CUOTA
                    if ($fecha_vencimiento == $fecha_inicio) $not_enter = true;
                    
                    
                    if ($fecha_vencimiento  == $cuota['FECHA_VENCIMIENTO'] && $tmp['_ACTIVA']!=-2) $not_enter = true;
                    
              //    echo "<br/>FECHA_GET:".date("d/m/Y",$fecha_get)."<br/>";
              //    echo "<br/>FECHA_VENCIMiENTO_CUOTA:".date("d/m/Y",$cuota['FECHA_VENCIMIENTO'])."<br/>";
              //    echo "<br/>FECHA_VENCIMIENTO_SEGMENTO:".date("d/m/Y",$fecha_vencimiento)."<br/>";
                    if (!$not_enter && ($tmp['_ACTIVA']==-2 || ($fecha_vencimiento  == $cuota['FECHA_VENCIMIENTO'] && $fecha_get > $fecha_vencimiento) ) ){
                        $fecha_calculo = $fecha_inicio;
                        
                        //buscamos los pagos de intereses compensatorio para calcular el saldo de cuota.
                        $this->_db->select("sum(MONTO) as TOTAL");
                        $this->_db->where("ID_CREDITO = ".$id_credito." AND CUOTAS_RESTANTES = ".$cuota['CUOTAS_RESTANTES']);
                        $this->_db->where("FECHA <=  ".$fecha_calculo);
                        $this->_db->where("ID_TIPO in (3,6,7)");
                        $total = $this->_db->get_row("fid_pagos");
                        
                        
                        $FIN_PLAZO_PAGO_SUBSIDIO = $cuota['FECHA_VENCIMIENTO'] + ($PLAZO_PAGO * 60 * 60 * 24) ;
                        //evaluamos si se han vencido los plazos para cumplir con el subsidio
                        if ($fecha_get > $FIN_PLAZO_PAGO_SUBSIDIO){
                            
                    //        echo "---".date("d/m/Y",$FIN_PLAZO_PAGO_SUBSIDIO)."---";
                      //      echo "VENCIDA CUOTA";
                            
                        }
                        else{
                            //SE HACE LA ACTUALIZACION DE LOS VALORES DE LA BONFIICACION.
                            $INTERES_COMPENSATORIO = $INTERES_COMPENSATORIO - $INT_SUBSIDIO_ACUMULADO;
                            $IVA_INTERES_COMPENSATORIO = $IVA_INTERES_COMPENSATORIO - $IVA_INT_SUBSIDIO_ACUMULADO;
                        }
                        
                        
                        //obtenemos la amorizacion de cuota correspondiente a la fecha de vencimiento
                        $capital_arr = $this->_get_saldo_capital($id_credito, $cuota['FECHA_VENCIMIENTO']-1, true);

                        $SALDO_CUOTA = $capital_arr['AMORTIZACION_CUOTA'] + $INTERES_COMPENSATORIO + $IVA_INTERES_COMPENSATORIO - $total['TOTAL'];
                        
                        //si llegamos a 0 en saldo de cuota no se siguen procesando los segmentos siguietnes o si el rango
                        //es 0
                        if (round($SALDO_CUOTA)==0 || round($rango)==0 ){
                            $bfin_segmento = true;
                            //echo "INT COMP:".$INTERES_COMPENSATORIO."<br/>";
                            //continue;
                        }
                        else{
                            $INT_MORATORIO = $SALDO_CUOTA * (1 + ($cuota['POR_INT_MORATORIO'] / 100) * $rango / 365 ) - $SALDO_CUOTA;
                            $INT_PUNITORIO = $SALDO_CUOTA * (1 + ($cuota['POR_INT_PUNITORIO'] / 100) * $rango / 365 ) - $SALDO_CUOTA;
                            
                            
                        }
                        
                        //si es capital teorico debe restarse tambien los pagos de capital
                        //si es capital real ya se resto en la cuenta de capita real y no se vuelve a restar los pagos realizados
                        //$SALDO_CUOTA = $SALDO_CUOTA  - $capital_arr['PAGOS'];
                        
                        //echo "<br/>FECHA_CALCULO:".date("d/m/Y",$cuota['FECHA_VENCIMIENTO'])." - RANGO:".$rango."<br/>";
                        //echo "<br/>SALDO CUOTA:".$capital_arr['AMORTIZACION_CUOTA']." + INT_COMP_TOTAL:".$INTERES_COMPENSATORIO." + IVA_COM_TOTAL:".$IVA_INTERES_COMPENSATORIO." - PAGO:".$total['TOTAL']." = ".$SALDO_CUOTA."<br/>";
                        //echo "<br/>SALDO CUOTA:".$SALDO_CUOTA." + INT_COMP_TOTAL:".$INTERES_COMPENSATORIO." + IVA_COM_TOTAL:".$IVA_INTERES_COMPENSATORIO." - PAGO:".$total['TOTAL']." = ".$SALDO_CUOTA."<br/>";
                        //-------------------------AGREGADO 15-07
                        //-------------------------
//                        $INT_MORATORIO = $SALDO_CUOTA * (1 + ($cuota['POR_INT_MORATORIO'] / 100) * $rango / 365 ) - $SALDO_CUOTA;
  //                      $INT_PUNITORIO = $SALDO_CUOTA * (1 + ($cuota['POR_INT_PUNITORIO'] / 100) * $rango / 365 ) - $SALDO_CUOTA;
                        
                        //echo "<br/>";
                        //echo "INTERES MORATORIO:".$INT_MORATORIO."<br/>";
                        //echo "INTERES PUNITORIO:".$INT_PUNITORIO."<br/>";
                    }
                
                    $tmp['INT_MORATORIO'] = $INT_MORATORIO ;
                    $tmp['INT_PUNITORIO'] = $INT_PUNITORIO ;
                    
                    //-------------------------AGREGADO 15-07
                    //$tmp['INT_COMPENSATORIO'] = $INTERES_COMPENSATORIO;
                    //$tmp['INT_COMPENSATORIO_IVA'] = $IVA_INTERES_COMPENSATORIO;
                    //----------------------------------------------
                    //echo $INTERES_COMPENSATORIO." - ".$tmp['INT_COMPENSATORIO']."<br/>";
                    $tmp['INT_COMPENSATORIO_IVA'] = $tmp['INT_COMPENSATORIO'] * IMP_IVA;
                    
                    $tmp['DIAS'] = $rango;
                    $segmentos[] = $tmp;
                }
            }
            unset($cuota['ID']);
            $cuota_segmento = array();
            foreach ($segmentos as $segmento) {
                $cuota_segmento = $cuota;
                unset($cuota_segmento['ID']);
                //buscamos la fecha real que no se ha modificado por los calculos de interes
                $segmento['FECHA_INICIO'] = $segmento['FECHA_INICIO_REAL'];
                $segmento['FECHA_VENCIMIENTO'] = $segmento['FECHA_VENCIMIENTO_REAL'];
                $cuota_segmento['_PARENT'] = $cuota_id;
                $cuota_segmento['_ACTIVA'] = 0;

                $cuota_segmento['_ID_VARIACION'] = $segmento['_ID_VARIACION'];
                $cuota_segmento['INT_PUNITORIO'] = $segmento['INT_PUNITORIO'];
                $cuota_segmento['INT_MORATORIO'] = $segmento['INT_MORATORIO'];
                $cuota_segmento['SALDO_CAPITAL'] = $segmento['SALDO_CAPITAL'];
                $cuota_segmento['CAPITAL_CUOTA'] = $segmento['CAPITAL_CUOTA'];
                $cuota_segmento['INT_COMPENSATORIO'] = $segmento['INT_COMPENSATORIO'];
                $cuota_segmento['FECHA_INICIO'] = $segmento['FECHA_INICIO'];
                $cuota_segmento['FECHA_VENCIMIENTO'] = $segmento['FECHA_VENCIMIENTO'];
                $cuota_segmento['INT_COMPENSATORIO_IVA'] = $segmento['INT_COMPENSATORIO'] * IMP_IVA;
                $this->_db->insert("fid_cuotas", $cuota_segmento);
            }
            $cuota['SEGMENTOS'] = $segmentos;
        }
        return $cuota;
    }

    function calcular_interes($monto, $dias, $interes = 10, $periodicidad = 60, $log = false) {

        /*  $interes = 10;
          $monto = 1000;
          $dias = 50;
          $periodicidad = 60;
         */

        if ($log) {
            logthis("__" . $this->i++, $monto . "," . floor($dias) . "," . $interes . "," . $periodicidad);
        }

        $dias = floor($dias);
        $interes = $interes / 100;
        //0.5 = 5 / 100;
        $base = 1 + ($interes * $periodicidad / 360);
        //  1.083 =   1 + ( 0.5 * 60 / 360);
        $exponente = $dias / $periodicidad;
        //0.3833 = 23 / 60

        $rtn = $monto * pow($base, $exponente) - $monto;
        //
        if ($log) {
            logthis("RET__" . $this->i++, $rtn);
        }
        return $rtn;
    }

    function eliminar_variacion($id_variacion) {
        $variacion = $this->_db->get_row("fid_credito_variaciones", "ID = " . $id_variacion);

        $bdel = false;
        switch ($variacion['TIPO']) {
            case 1:
                //desembolso
                $bdel = true;
                $this->_db->delete("fid_creditos_desembolsos", "ID_VARIACION = " . $variacion['ID']);
                break;
            case 2:
                //cambio tasa
                $bdel = true;
                $this->_db->delete("fid_pagos", "ID_VARIACION = " . $variacion['ID']);
                break;
            case 3:
                //recupero
                //se deben quitar los estados en 1 de las cuotas afectadas a los pagos eliminados
                $this->_db->group_by("CUOTAS_RESTANTES");
                $pagos = $this->_db->get_tabla("fid_pagos","ID_VARIACION = " . $variacion['ID']);
                foreach($pagos as $pago){
                    $this->_db->update("fid_cuotas",array("ESTADO"=>0),"CUOTAS_RESTANTES = ".$pago['CUOTAS_RESTANTES']);
                }
                
                $this->_db->delete("fid_pagos", "ID_VARIACION = " . $variacion['ID']);
                $bdel = true;
                /* $cuotas_afectadas = $this->_db->get_tabla("fid_cuotas","_ID_VARIACION = ".$id_variacion);
                  foreach($cuotas_afectadas  as $cuota){
                  $this->_db->delete("fid_pagos","CUOTAS_RESTANTES = ".$cuota['CUOTAS_RESTANTES']." AND ID_CREDITO = ".$cuota['ID_CREDITO']);
                  } */
                break;
        }

        if ($bdel) {
            $this->_rewrite_variaciones($id_variacion);
            $this->_db->delete("fid_credito_variaciones", "ID = " . $id_variacion);
        }
        return $variacion;
    }

    function _rewrite_variaciones($id = false) {

        if ($id) {

            //buscamos variacion eliminada
            $variacion = $this->_db->get_row("fid_credito_variaciones", "ID = " . $id);

            //buscamos variacion anterior
            $this->_db->order_by("FECHA", "desc");
            $this->_db->where("FECHA < " . $variacion['FECHA']);
            $variacion_anterior = $this->_db->get_row("fid_credito_variaciones");

            if ($variacion_anterior) {
                switch ($variacion['TIPO']) {
                    case 1:
                    case 3:

                        $resto = $variacion_anterior['CAPITAL'] - $variacion['CAPITAL'];
                        $this->_db->update("fid_credito_variaciones", array("CAPITAL" => "[CAPITAL + " . $resto . "]"), "FECHA > " . $variacion['FECHA']);
                        break;
                    case 2:
                        $resto = $variacion_anterior['POR_INT_COMPENSATORIO'] - $variacion['POR_INT_COMPENSATORIO'];
                        $this->_db->update("fid_credito_variaciones", array("POR_INT_COMPENSATORIO" => "[POR_INT_COMPENSATORIO + " . $resto . "]"), "FECHA > " . $variacion['FECHA']);
                        break;
                }
            } else {
                return false;
            }
        } else {
            /*      $this->_db->order_by("FECHA","asc");
              $this->_db->where("ID_CREDITO = ".$credito_id);
              $variacion = $this->_db->get_row("fid_credito_variaciones"); */
        }
    }

    function _rewite_cuota($credito_id, $cuotas_restantes) {
        $this->_db->where("_ACTIVA = 1");
        $this->_db->where("_PARENT = 0");
        $this->_db->where("ID_CREDITO = " . $credito_id . " AND CUOTAS_RESTANTES = " . $cuotas_restantes);


        $ultima_cuota = $this->_db->get_row("fid_cuotas", "ID_CREDITO = " . $credito_id . " AND CUOTAS_RESTANTES = " . $cuotas_restantes);
    }

    function get_cuotas_restantes($credito_id, $fecha) {
        $this->_db->where("ID_CREDITO = " . $credito_id);
        $this->_db->order_by("FECHA_INICIO", "desc");
        $this->_db->where("FECHA_INICIO <=   " . $fecha);
        $cuota = $this->_db->get_row("fid_cuotas");

        return $cuota ? $cuota['CUOTAS_RESTANTES'] : 0;
    }

    function add_single_cuota($credito_id, $fecha) {
        //borramos las cuotas siguiente a la fecha dada
        $this->_db->delete("fid_cuotas", "_PARENT = 0 AND FECHA_INICIO > " . $fecha);


        $this->_db->where("ID_CREDITO = " . $credito_id);
        $this->_db->where("FECHA_INICIO  <= " . $fecha . " AND FECHA_VENCIMIENTO >= " . $fecha);
        $this->_db->where("_PARENT = 0 AND _ACTIVA = 1");
        $this->_db->order_by("FECHA_INICIO", "asc");
        $cuotas = $this->_db->get_tabla("fid_cuotas");

        if ($cuotas) {
            //cantidad de cuotas anteriores
            //   $cant_cuotas_anteriores = count($cuotas);
            //sobre la ultima cuota existente hacemos todos los calculos necesarios para establecer el
            //saldo de capital
            $ultima_cuota = end($cuotas);
            if ($ultima_cuota['CUOTAS_RESTANTES'] < 2)
                return;

            //sobre la primera variacion
            $this->_db->where("ID_CREDITO = " . $credito_id);
            $this->_db->where("FECHA < " . $ultima_cuota['FECHA_VENCIMIENTO']);
            $this->_db->order_by("FECHA", "ASC");
            $variacion = $this->_db->get_row("fid_credito_variaciones");

            $cuotas_restantes = $ultima_cuota['CUOTAS_RESTANTES'];

            $arr_result = $this->_get_saldo_capital($credito_id, $ultima_cuota['FECHA_VENCIMIENTO'], true);
            $saldo_capital = $arr_result['SALDO'];

            $capital_cuota = ($arr_result['INICIAL'] + $arr_result['DESEMBOLSOS']) / $cuotas_restantes;
            $interes_compensatorio = $this->calcular_interes($saldo_capital, 30 * $variacion['PERIODICIDAD'], $variacion['POR_INT_COMPENSATORIO'], $variacion['PERIODICIDAD_TASA']);


            $periodicidad = $variacion['PERIODICIDAD'];
            $DIA_INICIO = date("d", $ultima_cuota['FECHA_INICIO']);
            $MES_VENCIMIENTO = (date("m", $ultima_cuota['FECHA_INICIO']) + ($periodicidad * 2));

            $fecha_venvimiento = mktime(0, 0, 0, $MES_VENCIMIENTO, $DIA_INICIO, date("Y", $ultima_cuota['FECHA_VENCIMIENTO']));

            //restamos una cuota sobre la ultima cuota

            $cuota = array(
                "ID_CREDITO" => $credito_id,
                "SALDO_CAPITAL" => $saldo_capital,
                "CAPITAL_CUOTA" => $capital_cuota,
                "CUOTAS_RESTANTES" => $cuotas_restantes - 1,
                "POR_INT_COMPENSATORIO" => $variacion['POR_INT_COMPENSATORIO'],
                "POR_INT_MORATORIO" => $variacion['POR_INT_MORATORIO'],
                "POR_INT_PUNITORIO" => $variacion['POR_INT_PUNITORIO'],
                "INT_COMPENSATORIO" => $interes_compensatorio,
                "INT_COMPENSATORIO_IVA" => $interes_compensatorio * $variacion['IVA'],
                "INT_MORATORIO" => 0,
                "INT_PUNITORIO" => 0,
                "FECHA_INICIO" => $ultima_cuota['FECHA_VENCIMIENTO'] ,
                "FECHA_GENERADA" => $fecha,
                "FECHA_VENCIMIENTO" => $fecha_venvimiento,
                "ESTADO" => 0,
                "_PARENT" => 0,
                "_ACTIVA" => 1,
                "_ID_VARIACION" => $variacion['ID'],
            );
            //calcular_interes($monto, $dias, $interes = 10, $periodicidad = 60){
//_PARENT 	_ACTIVA 	_ID_VARIACION
            $this->_db->insert("fid_cuotas", $cuota);
        }
    }

    function _get_saldo_cuota($cuota_id, $update = false) {

        $cuota = $this->_db->get_row("fid_cuotas", "ID = " . $cuota_id);
        $saldo_cuota = $cuota['CAPITAL_CUOTA'];

        $this->_db->select("SUM(MONTO) as TOTAL");
        $this->_db->where("ID_CREDITO = " . $cuota['ID_CREDITO']);
        $this->_db->where("ID_TIPO = 7");
        $this->_db->where("CUOTAS_RESTANTES = " . $cuota['CUOTAS_RESTANTES']);
        $total_row = $this->_db->get_row("fid_pagos");

        $total_pago_capital = $total_row['TOTAL'];

        $saldo = $saldo_cuota - $total_pago_capital;
        //echo "<br/><br/>PAGO: " . $total_pago_capital . " -CAPITAL_CUOTA: " . $saldo_cuota . "  - SALDO:" . $saldo . "---<br/><br/>";
        //Si hay capital cargado en la cuota (no es tiempo de gracia) verificamos el saldo de la cuota
        if ($cuota['CAPITAL_CUOTA'] > 0) {
            if (round($saldo, 2) == 0) {
                $this->_db->update("fid_cuotas", array("ESTADO" => 1), "ID = " . $cuota_id);
            } else {
                $this->_db->update("fid_cuotas", array("ESTADO" => 0), "ID = " . $cuota_id);
            }
        }
        return round($saldo, 2);
    }
    
    function _get_saldo_capital($credito_id, $fecha = false, $arr_result = false, $log = false) {
        //variacion inicial
        $this->_db->order_by("FECHA", "asc");
        $this->_db->where("ID_CREDITO = " . $credito_id);
        $this->_db->where("TIPO = 0");
        $variacion_inicial_arr = $this->_db->get_row("fid_credito_variaciones");

        $capital_inicial = $variacion_inicial_arr['CAPITAL'];
        $CUOTAS_GRACIA = $variacion_inicial_arr['CUOTAS_GRACIA'];

        //obtenemos todas las cuotas que hayan iniciado
        $this->_db->where("_PARENT = 0");
        $this->_db->where("FECHA_INICIO <= " . $fecha);
        $this->_db->order_by("FECHA_INICIO", "asc");
        $this->_db->where("ID_CREDITO = " . $credito_id);
        $cuotas = $this->_db->get_tabla("fid_cuotas");
        if ($log){
//            echo "<br>--------<br/>".$cuotas[count($cuotas)-1]['CUOTAS_RESTANTES']."<br/>";
         //   print_array($cuotas);
        }
        //si no se encuentra el inicio de ninguna cuota se busca desde la primera cuota
        if (!$cuotas){
            $this->_db->where("_PARENT = 0");
            $this->_db->order_by("FECHA_INICIO", "asc");
            $this->_db->where("ID_CREDITO = " . $credito_id);
            $cuota = $this->_db->get_row("fid_cuotas");
            $cuotas[] = $cuota;
        }
        //OBTENEMOS CUOTAS RESTANTES POR VENCER
        $ultima_cuota = end($cuotas);
        if (!$cuotas){
        }
        //obtenemos todos los desembolsos
        $this->_db->order_by("FECHA", "asc");
        $this->_db->where("FECHA <= " . $cuotas[count($cuotas) - 1]['FECHA_VENCIMIENTO']);
        $this->_db->where("ID_CREDITO = " . $credito_id);
        
        $desembolsos = $this->_db->get_tabla("fid_creditos_desembolsos");
        //Si el capital inicial esta definido mayor a 0 (es decir todavia no se realiza ningun desembolso), se toma el capital inicial como unico
        //desembolso, para poder continuar los calculos normalmente en simulacion, de lo contrario se ignora.
        if ($capital_inicial > 0){
            $desembolsos = array();
            $desembolsos[] = array("FECHA"=>$variacion_inicial_arr['FECHA'], "MONTO"=>$capital_inicial);
        }
        
        
        $AMORTIZACION_TEORICA_ACUMULADA = 0;;

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
            for ($d = $init; $d < count($desembolsos); $d++) {
                if ($desembolsos[$d]['FECHA'] <= $cuotas[$c]['FECHA_VENCIMIENTO'] &&
                        $desembolsos[$d]['FECHA'] <= $fecha) {
                    $audi .= "<br/>--DESEMB:" . $desembolsos[$d]['MONTO'];
                    $cuotas[$c]['DESEMBOLSOS'] += $desembolsos[$d]['MONTO'];
                    $desembolsos[$d]['MONTO'] = 0;
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
                $cuotas[$c]['AMORTIZACION_CUOTA'] =  $AMORTIZACION_CUOTA;//( ($DESEMBOLSOS_ACUMULADOS - $AMORTIZACION_TEORICA_ACUMULADA) / $divisor) ;
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
        
    
        //DESEMBOLSO A LA FECHA
        $this->_db->select("SUM(MONTO) as TOTAL");
        $this->_db->where("ID_CREDITO = " . $credito_id);
        if ($fecha) {
            $this->_db->where("FECHA <= " . $fecha);
        }
        $desembolso_arr = $this->_db->get_row("fid_creditos_desembolsos");
        $total_desembolso_real = $desembolso_arr['TOTAL'];

        //DESEMBOLSO AL FINAL DE LA CUOTA
        $this->_db->select("SUM(MONTO) as TOTAL");
        $this->_db->where("ID_CREDITO = " . $credito_id);
        if ($fecha) {
            $this->_db->where("FECHA <= " . $cuota['FECHA_VENCIMIENTO']);
        }
        $desembolso_arr = $this->_db->get_row("fid_creditos_desembolsos");
        $total_desembolso_teorico = $desembolso_arr['TOTAL'];

        $this->_db->select("SUM(MONTO) as TOTAL");
        $this->_db->where("ID_CREDITO = " . $credito_id);
        $this->_db->where("ID_TIPO = 7");
        if ($fecha) {
            $this->_db->where("FECHA <= " . $fecha);
        }
        $pagos_arr = $this->_db->get_row("fid_pagos");

        if ($log){
//            echo $this->_db->last_query();
//            print_array($pagos_arr);
        }
//        $total_pagos = round($pagos_arr['TOTAL'],2);
        $total_pagos = $pagos_arr['TOTAL'];

        $SALDO_CAPITAL = $total_desembolso_real - $total_pagos;
        $SALDO_TEORICO_PAGO = $SALDO_TEORICO - $total_pagos;
        $SALDO = $SALDO_TEORICO_PAGO < $SALDO_CAPITAL ? $SALDO_TEORICO : $SALDO_CAPITAL;
        if ($log){
            
        }
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
                "SALDO_REAL" => $SALDO_CAPITAL,
                "SALDO_TEORICO" => $SALDO_TEORICO,
                "TIPO" => $SALDO_TEORICO < $SALDO_CAPITAL ? "T" : "R"
             //   "SALDO_CUOTA" => $SALDO_CUOTA
            );
            if ($log) {
            //    print_array($rtn);
            //    echo date("d/m/Y", $fecha) . "-" . "|" . $AMORTIZACION_CUOTA_ACTUAL . "|" . $audi . "<br/>";
            }
            return $rtn;
        }
    }
    
    function get_pago($id_variacion) {
        $variacion = $this->_db->get_row("fid_credito_variaciones", "ID = " . $id_variacion);
        $arr_pago = array();
        $this->_db->order_by("FECHA_INICIO","ASC");
        $cuotas = $this->_db->get_tabla("fid_cuotas","_PARENT = 0 AND ID_CREDITO = ".$variacion['ID_CREDITO']);
        
        
        $variacion_inicial = $this->_db->get_row("fid_credito_variaciones", "ID_CREDITO = ".$variacion['ID_CREDITO']." AND TIPO = 0");
        $cantidad_cuotas = $variacion_inicial['CANTIDAD_CUOTAS'] ;
        
        $noimputado  = array();
        
        
        if ($variacion['TIPO'] == 3) {
            
            $pagos = $this->_db->get_tabla("fid_pagos", "ID_VARIACION = " . $variacion['ID']);
            
            foreach ($pagos as $pago) {
                $indice = $cantidad_cuotas  - $pago['CUOTAS_RESTANTES'];
                $signo = $pago['VENCIDO']==1 ? "RESTA" : "SUMA";
                switch ($pago['ID_TIPO']) {
                    case 1:
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_MORATORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_MORATORIO']['DETALLE'] = "IVA Int. Punitorio";
                        
                        break;
                    case 2:
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_PUNITORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_PUNITORIO']['DETALLE'] = "IVA Int. Moratorio";
                        break;
                    case 3:
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_COMPENSATORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['IVA_COMPENSATORIO']['DETALLE'] = "IVA Int. Compensatorio";
                        break;
                    case 4:
                        $cuotas[$indice]['PAGOS'][$signo]['PUNITORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['PUNITORIO']['DETALLE'] = "Int. Punitorios";
                        break;
                    case 5:
                        $cuotas[$indice]['PAGOS'][$signo]['MORATORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        $cuotas[$indice]['PAGOS'][$signo]['MORATORIO']['DETALLE'] = "Int. Moratorios";
                        break;
                    case 6:
                        $cuotas[$indice]['PAGOS'][$signo]['COMPENSATORIO']['DETALLE'] = "Int. Compensatorios";
                        $cuotas[$indice]['PAGOS'][$signo]['COMPENSATORIO']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        break;
                    case 7:
                        $cuotas[$indice]['PAGOS'][$signo]['CAPITAL']['DETALLE'] = "CAPITAL";
                        $cuotas[$indice]['PAGOS'][$signo]['CAPITAL']['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        break;
                    case 10:
                        $noimputado['DETALLE'] = "NO IMPUTADO";
                        $noimputado['MONTO'] = number_format(round($pago['MONTO'], 2), 2);
                        break;
                    case 8:
                        
                        $pago_gasto = $this->_db->get_row("fid_gastos","ID = ".$pago['CUOTAS_RESTANTES']);
                        
                        for($i = 0 ; $i < count($cuotas) ; $i++){
                            
                            if ($pago_gasto['FECHA'] >= $cuotas[$i]['FECHA_INICIO'] && 
                                $pago_gasto['FECHA'] <= $cuotas[$i]['FECHA_VENCIMIENTO'] ){

                                $indice = $cantidad_cuotas  - $cuotas[$i]['CUOTAS_RESTANTES'];
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
        $cuotas[0]['NOIMPUTADO'] = $noimputado;
        return $cuotas;
    }
    
    function modificar_fecha_cuota($credito_id, $cuotas_restantes = 0, $fecha_inicio = false, $fecha_vencimiento = false) {

        $cuotas_anteriores = $this->_db->get_tabla("fid_cuotas", "ID_CREDITO = " . $credito_id . " AND CUOTAS_RESTANTES > " . $cuotas_restantes);

        $MODIF = array();
        if ($fecha_inicio) {
            list($d, $m, $y) = explode("/", date("d/m/Y", $fecha_inicio));

            $MODIF['FECHA_INICIO'] = mktime(0, 0, 0, $m, $d, $y);
            $fecha_vencimiento_anterior = $MODIF['FECHA_INICIO'] ;

            //si existen cuotas anteriores se modifica la fecha de vencimiento
            if ($cuotas_anteriores) {
                $this->_db->update("fid_cuotas", array("FECHA_VENCIMIENTO" => $fecha_vencimiento_anterior), "ID_CREDITO = " . $credito_id . " AND CUOTAS_RESTANTES = " . ($cuotas_restantes + 1));
            } else {
                $this->_db->update("fid_credito_variaciones", array("FECHA_INICIO" => $fecha_inicio, "FECHA" => $fecha_inicio), "TIPO = 0");
            }
        }
        if ($fecha_vencimiento) {
            list($d, $m, $y) = explode("/", date("d/m/Y", $fecha_vencimiento));
            //$fecha_vencimiento_tmp = mktime(23, 59, 59, $m, $d, $y);
            $fecha_vencimiento_tmp = mktime(0, 0, 0, $m, $d, $y);
            $fecha_inicio_seguiente = $fecha_vencimiento_tmp ;
            $this->_db->update("fid_cuotas", array("FECHA_INICIO" => $fecha_inicio_seguiente), "ID_CREDITO = " . $credito_id . " AND CUOTAS_RESTANTES = " . ($cuotas_restantes - 1));
            $MODIF['FECHA_VENCIMIENTO'] = $fecha_vencimiento;
        }
        $this->_db->update("fid_cuotas", $MODIF, "ID_CREDITO = " . $credito_id . " AND CUOTAS_RESTANTES = " . $cuotas_restantes);
    }

    function get_cuota($credito_id, $cuotas_restantes) {
        $cuota = $this->_db->get_row("fid_cuotas", "ID_CREDITO = " . $credito_id . " AND CUOTAS_RESTANTES = " . $cuotas_restantes . " AND  _PARENT = 0");
        return $cuota;
    }

    function enviar_cuota($credito_id, $fecha) {
        $this->_db->order_by("FECHA_INICIO", "ASC");
        $this->_db->where("_PARENT = 0 AND _ACTIVA = 1 AND ESTADO = 0");
        $this->_db->where("ID_CREDITO = " . $credito_id);
        $this->_db->where("FECHA_VENCIMIENTO > " . $fecha);
        $cuota = $this->_db->get_row("fid_cuotas");

        $this->_db->update("fid_cuotas", array("FECHA_ENVIADA" => $fecha), "ID = " . $cuota['ID']);
    }
    
    function array_insert($array, $elem, $pos ){
        
        $arr1 = array_splice($array, 0,$pos) ;
        $arr2 = array_splice($array, 0, count($array) ) ;

        return array_merge($arr1,$elem, $arr2);
    }
        
    function get_saldo_cuota($cuota_id, $fecha = false){
        $fecha = $fecha ? $fecha : time();
        
        
        
        $this->_db->where("_PARENT = ".$cuota_id);
        $segmentos = $this->_db->get_tabla("fid_cuotas");
        foreach($segmentos as $segmento){
            
        }
        
    }    

    function elimina_eventos_temporales($credito_id){
        $this->_db->delete("fid_credito_variaciones","ID_CREDITO = ".$credito_id." AND TIPO = 5000");
    }
        
    function adelantar_pagos($credito_id, $fecha){
//        resetlog();
        $this->_db->select("SUM(MONTO) as TOTAL, FECHA, ID_VARIACION");
        $this->_db->where("ID_TIPO = 10");
        $this->_db->where("ID_CREDITO = ".$credito_id);
        $pagos = $this->_db->get_row("fid_pagos");
        $TOTAL = $pagos['TOTAL'];
        $ID_VARIACION = $pagos['ID_VARIACION'];
        
        $this->_db->order_by("FECHA_INICIO","ASC");
        $this->_db->where("FECHA_INICIO >= ".$fecha);
        $this->_db->where("ESTADO = 0 AND _PARENT = 0");
        $this->_db->where("ID_CREDITO = ".$credito_id);
        $cuotas = $this->_db->get_tabla("fid_cuotas");
        
        logthis("TOTAL",$TOTAL);
        logthis("CUOTAS IMPAGAS",$cuotas);
        
        $arr_pago = array();
        $total_pago = 0;
        foreach($cuotas as $cuota){
            
            $TIPO = 10;
            if ($TOTAL > 0){
                $TIPO = 7;
                $capital_arr = $this->_get_saldo_capital($credito_id, $cuota['FECHA_VENCIMIENTO']-1, true );
                logthis("CUOTAS IMPAGAS_".$cuota['CUOTAS_RESTANTES'],$capital_arr);
                $SALDO = $capital_arr['AMORTIZACION_CUOTA'];
                $monto = $SALDO > $TOTAL ? $TOTAL : $SALDO;                 
            }
            else{
                $monto = $TOTAL;
            }
            if ($monto > 0){
                $arr_pago[] = array(
                    "ID_CREDITO"=>$credito_id,
                    "FECHA"=>$fecha,
                    "ID_TIPO"=>$TIPO,
                    "MONTO"=>$monto,
                    "CUOTAS_RESTANTES"=>$cuota['CUOTAS_RESTANTES'],
                    "ID_VARIACION"=>$ID_VARIACION,
                ); 
                $total_pago += $monto;
                $TOTAL -= $monto;                
            }

        }
        
        $this->_db->delete("fid_pagos","ID_CREDITO = ".$credito_id." AND ID_TIPO = 10");
        foreach($arr_pago as $pago){
            $this->_db->insert("fid_pagos",$pago);
        }
        

        
        return $total_pago;
    }
    
    
    
    function cancelar_pagos_subsidiados($cuota_id, $cuota = array()){
        if ($cuota){
            
        }
        else{
            
            $this->_db->where("ID = ".$cuota_id);
            $cuota = $this->_db->get_row("fid_cuotas");
        }
        
        //SI YA EXISTE CANCELACION DE PAGOS
        $this->_db->where("p.ID_CREDITO = ".$cuota['ID_CREDITO']);
        $this->_db->where("p.cuotas_restantes  = ".$cuota['CUOTAS_RESTANTES']);
        $this->_db->where("p.VENCIDO = 1");
        $pagos = $this->_db->get_tabla("fid_pagos p");
        
        if ($pagos){
            return false;
        }
        
        $this->_db->select("MONTO, cv.FECHA, p.CUOTAS_RESTANTES, ID_TIPO, ID_VARIACION, cv.ID_CREDITO");
        $this->_db->where("cv.TIPO = 3");
        $this->_db->where("p.ID_CREDITO = ".$cuota['ID_CREDITO']);
        $this->_db->where("p.cuotas_restantes  = ".$cuota['CUOTAS_RESTANTES']);
        $this->_db->join("fid_credito_variaciones cv","cv.ID = p.ID_VARIACION");
        $pagos = $this->_db->get_tabla("fid_pagos p");
        
        
        $arr_insert = array();
        foreach($pagos as $pago){
            $arr_insert[] = array(
                "MONTO"=>$pago['MONTO'] * -1,
                "FECHA"=>$pago['FECHA'] ,
                "ID_TIPO"=>$pago['ID_TIPO'] ,
                "CUOTAS_RESTANTES"=>$cuota['CUOTAS_RESTANTES'] ,
                "ID_CREDITO"=>$cuota['ID_CREDITO'] ,
                "ID_VARIACION"=>$pago['ID_VARIACION'] ,
                "VENCIDO"=>1 
            );
        }
        
        $this->_db->insert("fid_pagos",$arr_insert, array("ID"));
        
        
    }
}

?>