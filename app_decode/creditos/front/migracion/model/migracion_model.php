<?php
class migracion_model extends credito_model{
    
    //var $dbm = null;
    

    
    function get_operaciones(){
        $dbm = set_new_connection("localhost","root","","fiduciaria5");
        
        
        $dbm->select("c.CUOTAS as CANT_CUOTAS, c.ID as ID, TNA as POR_INT_COMPENSATORIO, PERIODICIDAD_TASA, TASA_MORATORIA AS POR_INT_MORATORIO, TASA_PUNITORIO AS POR_INT_PUNITORIO, MONTO as CAPITAL, RAZON_SOCIAL");
        $dbm->where("oldc.ID_LDC = 9");
        $dbm->join("operacion o","o.ID_CREDITO = c.ID");
        $dbm->join("cliente cl","cl.ID = c.ID_CLIENTE");
        $dbm->join("operacion_ldc oldc","oldc.ID_OPERACION = o.ID");
        $creditos = $dbm->get_tabla("credito c");
        
        for($i = 0 ; $i < count($creditos) ; $i++){

            
            
        }
        
        return $creditos;
        
    }
    
    function get_data_migracion($id_credito){
        $dbm = set_new_connection("localhost","root","","fiduciaria5");
        
        //$this->renew_datos();
        //$this->borrar_credito();        
        
        ///////////////////////////////////////////////////////////
        $dbm->select("o.ID as ID_OPERACION, o.ID_CREDITO as ID_CREDITO, c.LEASING_GRACIA, c.FECHA_INICIO, c.CUOTAS as CANT_CUOTAS, c.ID as ID, TNA as POR_INT_COMPENSATORIO, PERIODICIDAD_TASA, TASA_MORATORIA AS POR_INT_MORATORIO, TASA_PUNITORIO AS POR_INT_PUNITORIO, MONTO as CAPITAL, RAZON_SOCIAL, cl.ID as ID_CLIENTE, c.CUOTAS as Cantidadad_cuotas");
        $dbm->where("oldc.ID_LDC = 9 AND c.ID = ".$id_credito);
        $dbm->join("operacion o","o.ID_CREDITO = c.ID");
        $dbm->join("cliente cl","cl.ID = c.ID_CLIENTE");
        $dbm->join("operacion_ldc oldc","oldc.ID_OPERACION = o.ID");

        
        
        
        $creditos = $dbm->get_row("credito c");        
        
        $cantidad_cuotas = $creditos['Cantidadad_cuotas'];
           
        $dbm->where("cr.ID_CREDITO = ".$creditos['ID']);
        $dbm->order_by("SECUENCIA","asc");
        $cuotas = $dbm->get_tabla("credito_recupero cr");
        
        if (!$cuotas) return;
        
        $desembolsos_arr = array();
        $dbm->where("ID_CREDITO = ".$creditos['ID']);
        $desembolsos = $dbm->get_tabla("credito_desembolso");
        ///////////////////////////////////////////////////////////
        
        $fehca_inicio = new DateTime($creditos['FECHA_INICIO']);
        
        $fecha_init = $fehca_inicio->format("d-m-Y");
        list($d,$m,$y) = explode("-",$fecha_init);
        $fecha_inicio = mktime(0,0,0,$m,$d,$y)- ($cuotas[0]['DIAS'] * 86400 );
        
        //$fecha_inicio = $fehca_inicio->getTimestamp() - ($cuotas[0]['DIAS'] * 86400 );
        $credito_row = array(
            "ID"=>$creditos['ID_CREDITO'],
            "ID_OPERACION" => $creditos['ID_OPERACION'],
            "ACTIVIDAD" => "",
            "MONTO_CREDITO" => $creditos['CAPITAL'],
            "MONTO_APORTE" => 0,
            "MONTO_OTRO" => 0,
            "MONTO_TOTAL" => 0,
            "MONTO_CREDITO_POR" => 0,
            "MONTO_APORTE_POR" => 0,
            "MONTO_OTRO_POR" => 0,
            "MONTO_TOTAL_POR" => 0,
            "PLAZO_COMPENSATORIO" => 360,
            "PLAZO_MORATORIO" => 365,
            "PLAZO_PUNITORIO" => 365,
            "T_COMPENSATORIO" => $creditos['POR_INT_COMPENSATORIO'],
            "T_PUNITORIO" => 7.069,//$creditos['POR_INT_PUNITORIO'],
            "T_BONIFICACION" => 0,//$creditos['POR_INT_COMPENSATORIO'],
            "T_MORATORIO" =>14.138,// $creditos['POR_INT_MORATORIO'],
            "INTERES_CUOTAS" => 0,
            "INTERES_VTO" =>  date("Y-m-d",$fecha_inicio),
            "INTERES_PERIODO" => 0,
            "CAPITAL_CUOTAS" => 0,
            "CAPITAL_VTO" => 0,
            "CAPITAL_PERIODO" => 0,
            "ID_FIDEICOMISO" => 0,
            "ID_OPERATORIA" => 0,
            "POSTULANTES" => $creditos['ID_CLIENTE'],
            "TIPO_CREDITO" => TIPO_MICROCREDITO,
        );       
        $this->_db->insert("fid_creditos", $credito_row);
        
        $creditos['DESEMBOLSOS'] = array();
        foreach($desembolsos as $desembolso){
            list($d,$m,$y) = explode("/",$desembolso['FECHA']);
            $fecha = mktime(0,0,0,$m,$d,$y);

            $desembolsos_arr['ID_CREDITO'] = $creditos['ID'];
            $desembolsos_arr['FECHA'] = $fecha;
            $desembolsos_arr['MONTO'] = $desembolso['MONTO'];
            $creditos['DESEMBOLSOS'][] = $desembolsos_arr;
        }        
        
        //$fecha_inicio = $creditos['FECHA_INICIO'] < $creditos['DESEMBOLSOS'][0]['FECHA'] ? $creditos['FECHA_INICIO'] : $creditos['DESEMBOLSOS'][0]['FECHA'];
        
        
        $fecha = new DateTime($creditos['FECHA_INICIO']);

//        $fecha_inicio = $creditos['DESEMBOLSOS'][0]['FECHA'];//$fecha->getTimestamp();
  //      if ()
        echo "FECHA_INICIO:".date("d/m/Y",$fecha_inicio );
        
        $cuotas_arr = array(
            "ID_CREDITO"=>$creditos['ID'],
            "SALDO_CAPITAL"=>$creditos['CAPITAL'],
            "POR_INT_COMPENSATORIO"=>$credito_row['T_COMPENSATORIO'],
            "POR_INT_MORATORIO"=>$credito_row['T_MORATORIO'],
            "POR_INT_PUNITORIO"=>$credito_row['T_PUNITORIO'],
            "INT_MORATORIO"=>0,
            "INT_PUNITORIO"=>0
        );
        
        $data = array(
            "fecha_inicio"=>$fecha_inicio,
            "cuotas"=>$creditos['CANT_CUOTAS'],
            "fecha"=>$fecha_inicio,
            "por_int_compensatorio"=>$credito_row['T_COMPENSATORIO'],
            "por_int_subsidio"=>0,
            "plazo_pago"=>0,
            "por_int_punitorio"=>$credito_row['T_PUNITORIO'],
            "por_int_moratorio"=>$credito_row['T_MORATORIO'],
            "periodicidad"=>1,
            "periodicidad_tasa"=>$creditos['PERIODICIDAD_TASA'],
            "TIPO"=>0,
            "monto" => $creditos['CAPITAL'],
            "cuotas_gracia" => $creditos['LEASING_GRACIA'],
            "iva"=>0.21,
                );
        
        $credito_id = $creditos['ID'];
        $this->set_credito_active($credito_id);
        
        $this->_id_version = 0;
        $this->agregar_version($fecha_inicio, 1, "VERSION INICIAL");
        $version = $this->set_version_active();        
        $evento = $this->generar_evento($data, false, $fecha_inicio  );
        
        $c = 0 ;
        foreach($cuotas as $cuota){

            $saldo = $creditos['CAPITAL'] - ($cuota['CAPITAL'] * $c);
            list($d,$m,$y) = explode("/",$cuota['FECHA']);
            $fecha_vencimiento = mktime(0,0,0,$m,$d,$y);

            $cuotas_arr['INT_COMPENSATORIO_SUBSIDIO'] = $cuota['BONIFICADO'];

            $cuotas_arr['SALDO_CAPITAL'] = $saldo;
            $cuotas_arr['INT_COMPENSATORIO'] = $cuota['INTERES'];
            $cuotas_arr['INT_COMPENSATORIO_IVA'] = $cuota['IVA'];
            $cuotas_arr['CAPITAL_CUOTA'] = $cuota['CAPITAL'];
            $cuotas_arr['CUOTAS_RESTANTES'] = $cantidad_cuotas  - $cuota['SECUENCIA'] + 1;
            $cuotas_arr['FECHA_ENVIADA'] = 0;
            $cuotas_arr['FECHA_INICIO'] = $fecha_vencimiento  - $cuota['DIAS'] * (60 * 60 * 24);
            $cuotas_arr['FECHA_VENCIMIENTO'] = $fecha_vencimiento  ;
            $cuotas_arr['ESTADO'] = $cuota['ESTADO']=='PAGADO' ? 1 : 0 ;
            $cuotas_arr['_ID_VARIACION'] = $evento['ID'];
            $cuotas_arr['ID_VERSION'] = $version;
            
            $this->_db->insert("fid_creditos_cuotas",$cuotas_arr);
            
            $creditos['CUOTAS'][] = $cuotas_arr;

            $c++;
        }

        foreach($creditos['DESEMBOLSOS'] as $desembolso){
            $fecha = $desembolso['FECHA'];
            $evento_desembolso = $this->generar_evento( array("monto"=>$desembolso['MONTO'],"TIPO"=>1), true, $fecha);
            $cuotas_restantes = $this->get_cuotas_restantes( $fecha);
            $this->agregar_desembolso( $data['monto'], $cuotas_restantes, $fecha);
            $this->assign_id_evento($evento_desembolso['ID'],1);
        }
        return $creditos;


    }
}

?>
