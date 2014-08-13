<?php

class cuotas extends main_controller{
    function cuotas(){
        $this->mod = $this->model("credito_model");
    }
    
    function init($credito = 0){
    
        $this->setCss( array("creditos.css","cuotas.css", "opciones_cuota.css") );
        $this->setPlug( array("fancybox"));
        $this->setPlug( array("jqgrid"));
        $this->setJs( array( "creditos.js") );
        
        
        
        $this->_js_var['ID_CREDITO'] = $credito;
        
        $datax = array();
        $datax['main'] = $this->_obtener_main($credito);
        $datax['titulo']= "Administracion";
        $datax['name_modulo'] = $this->get_controller_name();
        $datax['filtro'] = "";

        
        $this->render($datax);
    }
    
    function _obtener_main($credito){
        if ($credito==0){
            return $this->view("form_generar", array());
        }
        else{
            $cuotas = $this->mod->get_cuotas_credito($credito);
            return $this->view("form_credito", $cuotas);
        }
    }
    
    function _get_cuotas($id_credito){
        $cuotas = $this->mod->get_cuotas_credito($id_credito);
        echo $this->view("lista_cuotas",$cuotas);
    }
    
    
    function x_generar_cuotas(){
        $data['fecha'] = $_POST['fecha'];
        
        //se calcula la fecha de inicio de la primera cuota sobre la fecha del primer vencimiento y la periodicidad
        list($d,$m,$y) = explode("/",date("d/m/Y",$_POST['fecha_inicio']));
        $data['fecha_inicio'] = (mktime(0,0,0,$m - $_POST['periodicidad'] , $d , $y)) ;
        
        $data['cuotas'] = $_POST['cuotas'];
        $data['cuotas_gracia'] = $_POST['cuotas_gracia'];
        $data['monto'] = $_POST['monto'];
        $data['por_int_compensatorio'] = $_POST['int_compensatorio'];
        $data['por_int_subsidio'] = $_POST['int_subsidio'];
        $data['plazo_pago'] = $_POST['plazo_pago'];
        $data['por_int_punitorio'] = $_POST['int_punitorio'];
        $data['por_int_moratorio'] = $_POST['int_moratorio'];
        $data['periodicidad'] = $_POST['periodicidad'];
        $data['periodicidad_tasa'] = $_POST['periodicidad_tasa'];
        $data['TIPO'] = 0;
        $data['iva'] = key_exists('iva', $_POST) ? $_POST['iva'] :  0.21;
        
        $credito_id = key_exists('credito_id', $_POST) ? $_POST['credito_id'] : 1 ; 

        $val = $this->mod->generar_evento($credito_id, $data, false, $data['fecha']);
        $this->mod->generar_cuotas($credito_id,$val);
    
    }
    
    
    function x_set_pago(){
        
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $monto = $_POST['monto'];
        resetlog();
        
        $this->realizar_pago($fecha, $credito_id, $monto);
        
        echo $this->_get_cuotas($credito_id);

    }
    
    function realizar_pago($fecha,$credito_id, $monto){
          $this->mod->elimina_eventos_temporales($credito_id);        
        //en el pago se resta 
        //se genera evento para definir el dia de corte
        $ret_evento = $this->mod->generar_evento($credito_id, array(), true, $fecha);
        
        $ret_evento_id = $ret_evento['ID'];
        $ret_reduda = $this->mod->get_deuda($credito_id, $fecha);
        //se elimina el evento
        $this->mod->elimina_evento($ret_evento_id );        
        logthis("DEUDAS",$ret_reduda);
        //si el monto es 0 solo se mostrara la deuda

        $pagos = $this->mod->pagar_deuda($ret_reduda, $monto, $fecha, $credito_id);
        logthis("PAGOS",$pagos );
        $data = array();

        $pago_total = 0;
        foreach($pagos as $pago){
            if ($pago['ID_TIPO']==7){
                //genero la variacion corerspondiente al desembolso       
                $pago_total += $pago['MONTO'];
                break;
            }
            if ($pago['ID_TIPO']==10){
                //genero la variacion corerspondiente al desembolso       
                $adelanto_pago = $this->mod->adelantar_pagos($credito_id, $fecha);
                $pago_total += $adelanto_pago;
                break;
            }
        }
        $data['monto'] = $pago_total;
        $data['TIPO'] = 3;
        $ret = $this->mod->generar_evento($credito_id, $data, true, $fecha);        
        $this->mod->assign_id_evento($ret['ID'],3);
        $this->mod->get_segmentos_cuota($credito_id);
        
    }
    
    function x_get_detalle_cuotas(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        resetlog();
        
        $this->mod->elimina_eventos_temporales($credito_id);        
        //en el pago se resta 
        //se genera evento para definir el dia de corte
        $ret_evento = $this->mod->generar_evento($credito_id, array(), true, $fecha);
        
        $ret_evento_id = $ret_evento['ID'];
        $ret_reduda = $this->mod->get_deuda($credito_id, $fecha);
        //se elimina el evento
        $this->mod->elimina_evento($ret_evento_id );        
        logthis("DEUDAS",$ret_reduda);

        $this->mod->get_segmentos_cuota($credito_id);
        
        $data['datos_cuota'] = $this->view("info_cuotas/saldo_cuota", $ret_reduda);
        
        $data['cuotas'] = $ret_reduda['cuotas'];
        echo $this->view("info_cuotas/main_info",$data);
    }
    
    function x_agregar_desembolso(){
        $credito_id = $_POST['credito_id'];
        $data = array();
        $data['monto'] = $_POST['monto'];
        $data['TIPO'] = 1;
        $fecha = $_POST['fecha'];

        //genero la variacion corerspondiente al desembolso
        $ret = $this->mod->generar_evento($credito_id, $data, true, $fecha);
        $cuotas_restantes = $this->mod->get_cuotas_restantes($credito_id, $fecha);
        
        //agrego el registro desembolso a la db        
        $this->mod->agregar_desembolso($credito_id, $data['monto'], $cuotas_restantes, $fecha);
        $this->mod->assign_id_evento($ret['ID'],1);
        $this->mod->get_segmentos_cuota($credito_id);
       // $this->mod->add_single_cuota($credito_id, $fecha);
        $this->mod->generar_cuotas($credito_id);
        echo $this->_get_cuotas($credito_id);
    }
    
    
    function x_agregar_cambiotasa(){
        $credito_id = $_POST['credito_id'];
        $data = array();
        $data['por_int_compensatorio'] = $_POST['tasa'];
        $fecha = $_POST['fecha'];
        $data['TIPO'] = 2;
        
        //genero la variacion corerspondiente al desembolso
        $ret = $this->mod->generar_evento($credito_id, $data, true, $fecha);
        
        //agrego el registro desembolso a la db 
        $cuotas_restantes = $this->mod->get_cuotas_restantes($credito_id, $fecha);
        $this->mod->agregar_tasa($credito_id, $data['por_int_compensatorio'], $cuotas_restantes, $fecha);
        $this->mod->assign_id_evento($ret['ID'],2);
        $this->mod->get_segmentos_cuota($credito_id);
        echo $this->_get_cuotas($credito_id);
    }

    function borrar_credito($cred){
        resetlog();
        $this->mod->borrar_credito($cred);
      //  echo $this->_get_cuotas($cred);
    }
    function segmentar($cred){
        resetlog();
        $dia = 10;
        $mes = 9;
        $year = 2013;
        $fecha = mktime(0,0,0,$mes, $dia, $year);
        $this->mod->get_segmentos_cuota($cred, $fecha );
    }
    
    function x_agregar_gasto(){
        $credito_id = $_POST['credito_id'];
        $data = array();
        $gasto = $_POST['monto'];
        $fecha = $_POST['fecha'];
        
        $this->mod->agregar_gasto($credito_id, $gasto, $fecha);
        echo $this->_get_cuotas($credito_id);
    }
    
    function x_eliminar_variacion(){
        resetlog();
        $id_variacion = $_POST['id_variacion'];
        $credito_id = $_POST['credito_id'];
        $variacion = $this->mod->eliminar_variacion($id_variacion);
        $this->mod->get_segmentos_cuota($credito_id);
        //$this->mod->add_single_cuota($credito_id, $variacion['FECHA']);
        $this->mod->generar_cuotas($credito_id);
        echo $this->_get_cuotas($credito_id);
    }
    
    function x_generar_cuota(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $ret = $this->mod->generar_cuotas($credito_id );
//        print_array($ret);
        //$this->mod->add_single_cuota($credito_id, $fecha);
    }
    
    function x_actualizar_lista(){
        $credito_id = $_POST['credito_id'];
        echo $this->_get_cuotas($credito_id);
    }
    
    function x_obtener_pago(){
        $id_variacion = $_POST['id_variacion'];
        $cuotas = $this->mod->get_pago($id_variacion);
        echo $this->view("pago",array("cuotas"=>$cuotas ));
    }
    
    function x_guardar_opciones_cuota(){
        $inicio = $_POST['fecha_inicio'];
        $vencimiento = $_POST['fecha_vencimiento'];
        $cuotas_restantes = $_POST['cuotas_restantes'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->modificar_fecha_cuota($credito_id, $cuotas_restantes, $inicio, $vencimiento);
    }
    function x_abrir_opciones_cuota(){
        $id_credito = $_POST['credito_id'];
        $cuotas_restantes = $_POST['cuotas_restantes'];
        $cuota = $this->mod->get_cuota($id_credito, $cuotas_restantes);
        
        echo $this->view("opciones_cuota",$cuota);
    }    
    
    function x_enviar_cuota(){
        $id_credito = $_POST['credito_id'];
        $fecha = $_POST['fecha'];
        $this->mod->enviar_cuota($id_credito, $fecha);
        //$this->mod->enviar_cuota($id_credito, $fecha);
        
    }
    
    
    
    
    function iins(){
        $ret = $this->mod->cancelar_pagos_subsidiados(29557);
        if ($ret){
            
        }
    }
    
    
    
    
    
}



