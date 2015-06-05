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
        foreach($pagos['pagos'] as $pago){
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
        $entidad = $_POST['comboEntidad'];
        $target_filepath = UPLOAD_BANCOS . basename($_FILES['txtArchivo']['name']);

        if (move_uploaded_file($_FILES['txtArchivo']['tmp_name'], $target_filepath)) {
            $id = $this->mod->guardar_archivo_bancario(basename($_FILES['txtArchivo']['name']), time());
            $arr_result  = array();
            switch ($entidad){
                case 'Nacion':
                    $arr_result = $this->extract_file_nacion(basename($_FILES['txtArchivo']['name']));
                    break;
                case 'Supervielle':
                    $arr_result = $this->extract_file_supervielle(basename($_FILES['txtArchivo']['name']));
                    break;
                case 'Rapipago':
                    $arr_result = $this->extract_file_rapipago(basename($_FILES['txtArchivo']['name']));
                    break;
            }

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

    function extract_file_nacion($file) {
        $content = file_get_contents(UPLOAD_BANCOS . $file);
        $items = explode("\n",$content);
        
        $result = array();
        $fec_rec = "";
        foreach ($items as $item) {
            $item = trim(strip_tags($item));
            $item = trim(str_replace(array("\r", "\n"), "", $item));
            if (strlen($item)>54) {
                $result[] = $this->extract_file_nacion1($item);
            } elseif(strlen($item)==36) {
                $fec_rec = substr($item, 28, 8);
            } elseif(strlen($item)==54) {
                $result[] = $this->extract_file_nacion2($item, $fec_rec);
            }
        }

        return $result;
    }
    
    function extract_file_nacion1($item) {
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
        
        return $tmp;
    }
    
    function extract_file_nacion2($item, $fec_rec) {
        $tmp = array();
        $recaudacion = $item;

        $tmp['recaudacion'] = array();

        $tmp['recaudacion']['CODENT'] = "";
        $tmp['recaudacion']['SUC_ORIGEN'] = "";
        $tmp['recaudacion']['SUC_BCRA'] = "";
        $tmp['recaudacion']['FECHA_REC'] = $fec_rec;
        $tmp['recaudacion']['FECHA_REN'] = substr($recaudacion, 0, 8);
        $tmp['recaudacion']['COD_MOV'] = "";
        $tmp['recaudacion']['NRO_MOV'] = "";
        $tmp['recaudacion']['IMPORTE'] = substr($recaudacion, 9, 15);
        $tmp['recaudacion']['MONEDA'] = 1;
        $tmp['barcode']['ID_CREDITO'] = substr($recaudacion, 28, 7);
        $tmp['barcode']['FECHA_VENCIMIENTO'] = substr($recaudacion, 35, 8);
        $tmp['barcode']['IMPORTE'] = substr($recaudacion, 44, 10);
        
        return $tmp;
    }
    
    
    function extract_file_supervielle($file) {
        $content = file_get_contents(UPLOAD_BANCOS . $file);

        $content = str_replace(array("\r\n"), array(""), $content);
        $content = str_replace(array("\n"), array(""), $content);


        //$items = explode("\r\n",$content);

        $header = substr($content, 0,73);
        $detalle = substr(strip_tags($content), 73);
        $items = str_split($detalle, 65);


        //print_array($items);
        $result = array();
        foreach ($items as $item) {
            $item = str_replace(array("\r", "\n"), "", $item);
       //     echo $item;
            $tmp = array();
            $recaudacion = substr($item, 0, 65);

            if (substr($recaudacion, 0, 34)==='9999999900000000000000000000000000'){
                break;
            }
            
            $tmp['recaudacion'] = array();

            
            $tmp['recaudacion']['FECHA_REN'] = 0000000;
            $tmp['recaudacion']['FECHA_REC'] = substr($recaudacion, 0, 8);
//            $importe_entero = substr($recaudacion, 8, 13);
//          $importe_decimal = substr($recaudacion, 21, 2);
            $tmp['recaudacion']['IMPORTE'] = substr($recaudacion, 8, 15);

            $barcode = substr($item, 23, 80);
            $tmp['barcode']['ID_CREDITO'] = substr($barcode, 4, 8);
            $tmp['barcode']['FECHA_VENCIMIENTO'] = substr($barcode, 12, 8);
            $tmp['barcode']['IMPORTE'] = substr($barcode, 20, 10);
            $result[] = $tmp;


            $cheque = substr($item, 138, 22);
        }

        return $result;
    }
    
    function extract_file_rapipago($file) {
        $content = file_get_contents(UPLOAD_BANCOS . $file);
        $items = explode("\n",$content);
        
        $result = array();
        $array = array();
        
        foreach ($items as $k=>$item) {
            $item = trim(strip_tags($item));
            $item = trim(str_replace(array("\r", "\n"), "", $item));
            
            $kp = (int) ($k/3);
            $array[$kp][] = $item;
        }
        
        foreach ($array as $item) {
            $tmp = array();
            $tmp['recaudacion'] = array();

            $tmp['recaudacion']['CODENT'] = "";
            $tmp['recaudacion']['SUC_ORIGEN'] = "";
            $tmp['recaudacion']['SUC_BCRA'] = "";
            $tmp['recaudacion']['FECHA_REC'] = substr($tmp[0], 28, 36);
            $tmp['recaudacion']['FECHA_REN'] = substr($tmp[1], 0, 8);
            $tmp['recaudacion']['COD_MOV'] = "";
            $tmp['recaudacion']['NRO_MOV'] = "";
            $tmp['recaudacion']['IMPORTE'] = substr($tmp[1], 8, 23);
            $tmp['recaudacion']['MONEDA'] = 1;
            $tmp['barcode']['ID_CREDITO'] = substr($tmp[0], 0, 8);
            $tmp['barcode']['FECHA_VENCIMIENTO'] = substr($tmp, 35, 8);
            $tmp['barcode']['IMPORTE'] = substr($tmp[], 44, 10);
            
            $result[] = $tmp;
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

    }

}

