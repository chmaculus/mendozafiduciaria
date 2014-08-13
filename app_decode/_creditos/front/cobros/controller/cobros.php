<?php

define('UPLOAD_BANCOS', 'uploads/bancos/');

class cobros extends main_controller {

    function cobros() {
        $this->mod = $this->model("cobros_model");
    }
    
    
    //$vencimiento, si se especifica es la fecha de vencimiento de la cuota a la que deben imputarse los pagos
    function realizar_pago($fecha, $monto){
        
        
        //echo "|REALIZAR_PAGO: fecha:".date("d/m/Y",$fecha)."|Monto:".$monto;
        $this->mod->elimina_eventos_temporales();        
        //en el pago se resta 
        //se genera evento para definir el dia de corte
        
        $this->mod->renew_datos();
        $this->mod->save_last_state(false);
        $this->mod->set_fecha_actual($fecha);
        $ret_evento = $this->mod->generar_evento( array(), true, $fecha);
        
        $ret_evento_id = $ret_evento['ID'];
        
        $ret_reduda = $this->mod->get_deuda($fecha, true);
        //se elimina el evento
        $this->mod->elimina_evento($ret_evento_id );        
        
        
        $pagos = $this->mod->pagar_deuda($ret_reduda, $monto, $fecha);
        $data = array();
        
        
        $this->mod->save_last_state(true);
        $pago_total = 0;
        foreach($pagos as $pago){
            if ($pago['ID_TIPO']==7){
                //genero la variacion corerspondiente al desembolso       
                $pago_total += $pago['MONTO'];
                break;
            }
            if ($pago['ID_TIPO']==10){
                
                //genero la variacion corerspondiente al desembolso     
                $this->mod->renew_datos();
                
                $adelanto_pago = $this->mod->adelantar_pagos( $fecha);
                $pago_total += $adelanto_pago;
                break;
            }
        }
        $data['monto'] = $pago_total;
        $data['TIPO'] = 3;
        
        $ret = $this->mod->generar_evento( $data, true, $fecha);  
        
        $this->mod->assign_id_evento($ret['ID'],3);
        $this->mod->get_segmentos_cuota();
        
    }
    
    function init($credito = 0) {
        // $this->mod->set_credito_active($credito);
        //$this->mod->set_version_active();

        $this->setCss(array("cobros.css"));
        $this->setJs(array("cobros.js"));



        $this->_js_var['ID_CREDITO'] = $credito;
        $this->_js_var['FECHA'] = time();

        $datax = array();
        $datax['main'] = $this->_obtener_main($credito);
        $datax['titulo'] = "Administracion";
        $datax['name_modulo'] = $this->get_controller_name();
        $datax['filtro'] = "";

        $this->render($datax);
    }

    function _obtener_main($credito) {


        //$this->mod->set_credito_active($credito);
        //$this->mod->set_version_active();
        //$this->mod->renew_datos();
//        print_array($cuotas);
        return $this->view("cobros");
    }

    function x_enviar_archivo() {
        $target_filepath = UPLOAD_BANCOS . basename($_FILES['txtArchivo']['name']);

        if (move_uploaded_file($_FILES['txtArchivo']['tmp_name'], $target_filepath)) {
            $id = $this->mod->guardar_archivo_bancario(basename($_FILES['txtArchivo']['name']), time());
            $arr_result = $this->extract_file(basename($_FILES['txtArchivo']['name']));

            $insert = array();
            foreach ($arr_result as $item) {
                $tmp = array();
                $year = substr($item['recaudacion']['FECHA_REC'], 0, 4);
                $mes = substr($item['recaudacion']['FECHA_REC'], 4, 2);
                $dia = substr($item['recaudacion']['FECHA_REC'], 6, 2);
                $tmp ['FECHA_RECEPCION'] = mktime(0, 0, 0, $mes, $dia, $year);

                $year = substr($item['recaudacion']['FECHA_REN'], 0, 4);
                $mes = substr($item['recaudacion']['FECHA_REN'], 4, 2);
                $dia = substr($item['recaudacion']['FECHA_REN'], 6, 2);
                $tmp ['FECHA_RENDICION'] = mktime(0, 0, 0, $mes, $dia, $year);

                $year = substr($item['barcode']['FECHA_VENCIMIENTO'], 0, 4);
                $mes = substr($item['barcode']['FECHA_VENCIMIENTO'], 4, 2);
                $dia = substr($item['barcode']['FECHA_VENCIMIENTO'], 6, 2);
                $tmp ['CREDITO_VENCIMIENTO'] = mktime(0, 0, 0, $mes, $dia, $year);


                $importe = ltrim($item['recaudacion']['IMPORTE'], "0") / 100;
                $importe_credito = ltrim($item['barcode']['IMPORTE'], "0") / 100;
                $id_credito = ltrim($item['barcode']['ID_CREDITO'], "0");

                $tmp['ID_CREDITO'] = $id_credito;
                $tmp['IMPORTE'] = $importe;
                $tmp['CREDITO_IMPORTE'] = $importe_credito;
                $tmp['ID_FILE'] = $id;

                $insert[] = $tmp;   
            }
            $this->mod->guardar_cobros_bancos($insert, $id);
            $this->mod->marcar_archivo_bancario($id);             
        } else {
            
        }
    }

    function extract_file($file) {
        $content = file_get_contents(UPLOAD_BANCOS . $file);

        $content = str_replace(array("\r\n"), array(""), $content);
        $content = str_replace(array("\n"), array(""), $content);


        //$items = explode("\r\n",$content);

        $items = str_split($content, 160);

        //print_array($items);
        $result = array();
        foreach ($items as $item) {
            $item = str_replace(array("\r", "\n"), "", $item);
            $tmp = array();
            $recaudacion = substr($item, 0, 58);

            $tmp['recaudacion'] = array();


            $tmp['recaudacion']['CODENT'] = substr($recaudacion, 0, 10);
            $tmp['recaudacion']['SUC_ORIGEN'] = substr($recaudacion, 10, 4);
            $tmp['recaudacion']['SUC_BCRA'] = substr($recaudacion, 14, 4);
            $tmp['recaudacion']['FECHA_REC'] = substr($recaudacion, 18, 8);
            $tmp['recaudacion']['FECHA_REN'] = substr($recaudacion, 26, 8);
            $tmp['recaudacion']['COD_MOV'] = substr($recaudacion, 34, 2);
            $tmp['recaudacion']['NRO_MOV'] = substr($recaudacion, 36, 6);
            $tmp['recaudacion']['IMPORTE'] = substr($recaudacion, 42, 15);
            $tmp['recaudacion']['MONEDA'] = substr($recaudacion, 57, 1);


            $barcode = substr($item, 58, 80);
            $tmp['barcode']['ID_CREDITO'] = substr($barcode, 4, 8);
            $tmp['barcode']['FECHA_VENCIMIENTO'] = substr($barcode, 12, 8);
            $tmp['barcode']['IMPORTE'] = substr($barcode, 20, 10);
            $result[] = $tmp;


            $cheque = substr($item, 138, 22);
        }

        return $result;
    }

    function x_get_archivos_bancos() {
        $lista = $this->mod->get_archivos_bancarios();
        echo json_encode($lista);
    }

    function x_get_cobro_file() {
        $id = $_POST['id'];
            
        $datos = $this->mod->get_cobros_bancos($id);
        //print_array($datos);
        for($i = 0 ; $i < count($datos) ; $i++){
            $datos[$i]['FECHA_REC'] = date("d/m/Y",$datos[$i]['FECHA_RECEPCION']);
            $datos[$i]['FECHA_REN'] = date("d/m/Y",$datos[$i]['FECHA_RENDICION']);
            $datos[$i]['CREDITO_VENCIMIENTO'] = date("d/m/Y",$datos[$i]['CREDITO_VENCIMIENTO']);
            $datos[$i]['INGRESADO'] = $datos[$i]['FECHA_INGRESADO'] > 0? "ingresado" : "no_ingresado";
        }

        echo $this->view("extract", array("datos" => $datos));
    }

    function x_add_cobros() {
        $cobros = $_POST['cobros'];

        $fecha = time();
        print_array($cobros);
        
        //die();
        foreach ($cobros as $cobro) {
            $ID_CREDITO = $cobro['ID_CREDITO'];
            
            list($d, $m, $y) = explode("/", $cobro['FECHA']);
            $fecha = mktime(0, 0, 0, $m, $d, $y);
            
            list($d, $m, $y) = explode("/", $cobro['CREDITO_VENCIMIENTO']);
            $vencimiento_cuota = mktime(0, 0, 0, $m, $d, $y);
            $importe = $cobro['IMPORTE'];
            
            if ($this->mod->existCredito($ID_CREDITO)){
                $this->mod->set_credito_active($ID_CREDITO);
                $this->mod->set_version_active();
                $this->realizar_pago($fecha, $importe, $vencimiento_cuota); 
                $this->mod->marcar_cobro_bancario( $cobro['ID'], $fecha );
            }
            else{
                echo "llega-----";
            }
            
        }

        print_array($cobros);
    }

}

