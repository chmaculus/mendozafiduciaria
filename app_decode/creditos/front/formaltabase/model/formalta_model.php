<?php



class formalta_model extends credito_model {

    function get_credito_from_id($id){
        $this->_db->where("ID = ".$id);
        $credito = $this->_db->get_row("fid_creditos");
        
        $credito['DESEMBOLSOS'] = $this->_db->get_tabla("fid_creditos_desembolsos","ID_CREDITO = ".$id." AND CUOTAS_RESTANTES = -1");
        
        return $credito;
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
    
    function get_fideicomisos(){
        return $this->_db->get_tabla("fid_fideicomiso");
    }
    
    
    
    function get_operatorias_alta($id){
        $this->_db->select("o.*");
        $this->_db->join("fid_fideicomiso_operatorias fo","fo.ID_OPERATORIA = o.ID");
        return $this->_db->get_tabla("fid_operatorias o","fo.ID_FIDEICOMISO = ".$id);
    }
    
    function get_postulantes(){
        $clientes = $this->_db->get_tabla("fid_clientes");
        return $clientes;
    }
    
    function set_postulantes($postulantes){
        $this->_postulantes = $postulantes;
    }
    
    function set_fideicomiso($id){
        $this->_id_fideicomiso = $id;
    }
    
    function set_operatoria($id){
        $this->_id_operatoria = $id;
    }
    


    function save_operacion_credito(){
        
        if (!isset($_SESSION['USERADM']) || !$_SESSION['USERADM']) {
            return FALSE;
        }
        
        /* GUARDAMOS AUDITORIA */
        $array = array(
            'ID_USUARIO' => $_SESSION['USERADM'],
            'TABLA' => "creditos",
            'ACCION' => "A",
            'Registro' => $this->_id_credito
        );
        $this->_db->insert("fid_auditoria", $array);
        
        $this->renew_datos();
        
        $operacion = $this->_db->get_tabla("fid_creditos","ID = ".$this->_id_credito);
        //$operacion = $this->_db->get_tabla("fid_creditos","ID_OPERACION = ".$this->_id_credito);
        
        //si existe la operacion no se guarda nada
        if ($operacion){
            return;
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
        $gastosMin = $primera_variacion['POR_INT_GASTOS_MIN'];
        
        $primera_cuota = reset($this->_cuotas);
        $primer_vencimiento = $primera_cuota['FECHA_VENCIMIENTO'];
        
        $this->_postulantes = is_array($this->_postulantes) ? $this->_postulantes : array();
        
        $credito = array(
            "ID"=>$this->_id_credito,
            "ID_OPERACION" => 0,
            "ACTIVIDAD" => "",
            "MONTO_CREDITO" => $total,
            "MONTO_APORTE" => 0,
            "MONTO_OTRO" => 0,
            "MONTO_TOTAL" => 0,
            "MONTO_CREDITO_POR" => 0,
            "MONTO_APORTE_POR" => 0,
            "MONTO_OTRO_POR" => 0,
            "MONTO_TOTAL_POR" => 0,
            "PLAZO_COMPENSATORIO" => $this->_interese_compensatorio_plazo,
            "PLAZO_MORATORIO" => $this->_interese_moratorio_plazo,
            "PLAZO_PUNITORIO" => $this->_interese_punitorio_plazo,
            "T_COMPENSATORIO" => $compensatorio,
            "T_PUNITORIO" => $punitorio,
            "T_BONIFICACION" => $bonificacion,
            "T_MORATORIO" => $moratorio,
            "T_GASTOS" => $gastos,
            "T_GASTOS_MIN" => $gastosMin,
            "INTERES_CUOTAS" => $interes_cuotas,
            "INTERES_VTO" => date("Y-m-d",$primer_vencimiento),
            "INTERES_PERIODO" => 09,
            "CAPITAL_CUOTAS" => $interes_cuotas,
            "CAPITAL_VTO" => date("Y-m-d"),
            "CAPITAL_PERIODO" => 0,
            "ID_FIDEICOMISO" => $this->_id_fideicomiso,
            "ID_OPERATORIA" => $this->_id_operatoria,
            "POSTULANTES" => implode("|",$this->_postulantes),
            "POSTULANTES_NOMBRES" => '',
            "POSTULANTES_CUIT" => '',
            "TIPO_CREDITO" => $this->_tipo_credito
        );
        
        $this->_db->select('RAZON_SOCIAL, CUIT');
        if ($data_clientes = $this->_generar_clientes($credito['POSTULANTES'])) {
            $credito['POSTULANTES_NOMBRES'] = $data_clientes['POSTULANTES_NOMBRES'];
            $credito['POSTULANTES_CUIT'] = $data_clientes['POSTULANTES_CUIT'];
        }
        
        $this->_db->insert("fid_creditos",$credito);
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
    
    function generar_clientes() {
        if ($creditos = $this->_db->get_tabla("fid_creditos")) {
            foreach ($creditos as $credito) {
                if ($data_clientes = $this->_generar_clientes($credito['POSTULANTES'])) {
                    $this->_db->update('fid_creditos', $data_clientes, 'ID = ' . $credito['ID']);
                }
            }
        }
    }
    
    
   //se gneran las cuotas a partir de una variacion en particular
    //si no se pasa el parametro se utiliza la ultima variacion ingresada.

    function generar_cuotas($variacion = false,  $micro = 0, $ret=TRUE) {
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
        $fecha_venvimiento = $this->obtener_fecha_vencimiento($DIA_INICIO, $fecha_venvimiento, $variacion['PERIODICIDAD_TASA'] * $cantidad_cuotas_iniciadas, $variacion['PERIODICIDAD']);

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
                $fecha_venvimiento = $this->obtener_fecha_vencimiento($DIA_INICIO, $fecha_venvimiento, $variacion['PERIODICIDAD_TASA'], $variacion['PERIODICIDAD']);
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
                if ($ret) {
                    echo "<br/>MONTO:".$monto_restante;
                }
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
    
    public function getFideicomisoId($fideicomiso) {
        $fideicomiso =  str_replace("  ", " ", str_replace(",", " ", $fideicomiso));
        $fideicomiso =  "%".str_replace(" ", "%", $fideicomiso)."%";
        
        $this->_db->select("ID");
        $this->_db->where("NOMBRE LIKE '$fideicomiso'");
        
        if ($result = $this->_db->get_row("fid_fideicomiso")) {
            return $result['ID'];
        } else {
            return 0;
        }
    }
    
    public function getOperatoriaId ($operatoria) {
        $operatoria =  str_replace("  ", " ", str_replace(",", " ", $operatoria));
        $operatoria =  "%".str_replace(" ", "%", $operatoria)."%";
        
        $this->_db->select("ID");
        $this->_db->where("NOMBRE LIKE '$operatoria'");
        
        if ($result = $this->_db->get_row("fid_operatorias")) {
            return $result['ID'];
        } else {
            return 0;
        }
    }
    
    public function getClienteIdCUIT ($cuit) {
        $cuit =  str_replace(" ", "", str_replace("-", "", $cuit));
        
        $this->_db->select("ID");
        $this->_db->where("REPLACE(CUIT, '-', '')  LIKE '$cuit'");
        
        if ($result = $this->_db->get_row("fid_clientes")) {
            return $result['ID'];
        } else {
            return 0;
        }
    }
    
    function guardar_postulante($cliente) {
        return $this->_db->insert("fid_clientes", $cliente);
    }
   
    function obtener_fecha_vencimiento($dia_inicio, $fecha, $periodicidad_tasa, $periodicidad=1) {
        switch ($periodicidad_tasa) {
            case 30:
                $mes = date('m', $fecha) + $periodicidad;
                $anio = date('Y', $fecha);
                if ($mes>12) {
                    $mes-=12;
                    $anio++;
                }
                //echo $anio;die();
                $fecha_vencimiento = $anio . "-" . $mes;
                $dia_mes = date('t', strtotime($fecha_vencimiento));
                if ($dia_inicio > $dia_mes) {
                    $dia_inicio = $dia_mes;
                }

                $fecha_vencimiento = strtotime($fecha_vencimiento . "-" . $dia_inicio);
                break;

            default :
                $fecha_vencimiento = $fecha + ($periodicidad_tasa * 3600 * 24 * $periodicidad);
                break;
        }



        return $fecha_vencimiento;
    }
    
    function get_operatoria_tasas($id, $fecha) {
        $id =  (int) $id;
        $this->_db->select("*");
        $rtn = $this->_db->get_row("fid_operatorias", "ID=" . $id);
        if ($rtn) {
            $arr = array(
                'IVA' => $rtn['IVA'],
                'COMPENSATORIO' => $rtn['TASA_INTERES_COMPENSATORIA'],
                'PUNITORIO' => $rtn['TASA_INTERES_POR_PUNITORIOS'],
                'MORATORIO' => $rtn['TASA_INTERES_MORATORIA'],
                'SUBSIDIO' => $rtn['TASA_SUBSIDIADA']
            );
            
            if ($fecha) {
                $this->_db->select("FECHA, IVA, COMPENSATORIO, SUBSIDIO, MORATORIO, PUNITORIO");
                $this->_db->join("fid_operatoria_cambiotasas oc","oc.ID_OPERATORIA = o.ID AND oc.FECHA<=" . $fecha);
                $this->_db->order_by("FECHA", "ASC");
                $rtn = $this->_db->get_tabla("fid_operatorias o", "ID=" . $id);
                
                foreach ($rtn as $r) {
                    if ($r['COMPENSATORIO'] >= 0) {
                        $arr['COMPENSATORIO'] = $r['COMPENSATORIO'];
                    }
                    if ($r['SUBSIDIO'] >= 0) {
                        $arr['SUBSIDIO'] = $r['SUBSIDIO'];
                    }
                    if ($r['MORATORIO'] >= 0) {
                        $arr['MORATORIO'] = $r['MORATORIO'];
                    }
                    if ($r['PUNITORIO'] >= 0) {
                        $arr['PUNITORIO'] = $r['PUNITORIO'];
                    }
                }
            }
            
            return $arr;
        }
    }
    
}

?>