<?php

// Include the main TCPDF library (search for installation path).
require_once(MODULE_DIRECTORY.'tcpdf/tcpdf.php');
require_once(MODULE_DIRECTORY.'tcpdf/tcpdf_barcodes_1d.php');


class cuotas_m extends main_controller{
    function cuotas_m(){
        $this->mod = $this->model("credito_model_test");
    }
    
    function init($creditos = array()){

       // $this->mod->set_credito_active($credito);
        //$this->mod->set_version_active();
        
        $this->setCss( array("creditos.css","cuotas.css", "opciones_cuota.css", "form_generar.css") );
        $this->setPlug( array("fancybox","jqgrid","jalerts"));
        $this->setJs( array( "creditos_m.js") );
        
        $this->_js_array['ID_CREDITO'] = $creditos;
        $this->_js_var['FECHA'] = time();
        
        $datax = array();
        $datax['main'] = $this->_obtener_main($creditos);
        $datax['titulo']= "Administracion";
        $datax['name_modulo'] = $this->get_controller_name();
        $datax['filtro'] = "";

        
        $this->render($datax);
    }
    
    function _obtener_main($creditos){
        return $this->view("filtros");
    }
    
    function _get_cuotas(){
        
        $cuotas = $this->mod->get_cuotas_credito();
        echo $this->view("lista_cuotas",$cuotas);
    }
    
    
    
    
    function x_set_pago(){
        
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        
        $monto = $_POST['monto'];
        resetlog();
        
        $this->realizar_pago($fecha,  $monto);
        
        echo $this->_get_cuotas();
    }
    
    function realizar_pago($fecha, $monto){
        
        $this->mod->elimina_eventos_temporales();        
        //en el pago se resta 
        //se genera evento para definir el dia de corte
        $ret_evento = $this->mod->generar_evento( array(), true, $fecha);
        
        $ret_evento_id = $ret_evento['ID'];
        $this->mod->renew_datos();
        $ret_reduda = $this->mod->get_deuda($fecha);
        
        //se elimina el evento
        $this->mod->elimina_evento($ret_evento_id );        
        logthis("DEUDAS",$ret_reduda);
        
        //si el monto es 0 solo se mostrara la deuda
        $pagos = $this->mod->pagar_deuda($ret_reduda, $monto, $fecha);
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
    
    function x_agregar_desembolso(){
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active();
        $desembolso_solicitud = isset($_POST['desembolso']) ? $_POST['desembolso'] : 0;
        
        
        if ($desembolso_solicitud){
            $this->mod->agregar_desembolso_solicitado($desembolso_solicitud);
        }
        

        $data = array();
        $data['monto'] = $_POST['monto'];
        $reset = isset($_POST['reset']) ? $_POST['reset'] : 0;
        $data['TIPO'] = 1;
        
        $fecha = $_POST['fecha'];

        if (!$this->_verificar_desembolsos_teoricos( $reset)){
            echo "-1";
        }
        else{
            //genero la variacion corerspondiente al desembolso
            $ret = $this->mod->generar_evento( $data, true, $fecha);
            $cuotas_restantes = $this->mod->get_cuotas_restantes( $fecha);

            //agrego el registro desembolso a la db        
            $this->mod->agregar_desembolso( $data['monto'], $cuotas_restantes, $fecha);
            $this->mod->assign_id_evento($ret['ID'],1);

            $this->mod->get_segmentos_cuota();
            
            $this->mod->renew_datos();
            echo $this->_get_cuotas();
        }
    }
    
    function _verificar_desembolsos_teoricos( $reset = 0){
        $desembolsos_teoricos = $this->mod->get_desembolsos_teoricos();
        if ($desembolsos_teoricos && !$reset){
            return false;
        }
        else{
            //eliminamos desembolsos teoricos si existen
            if ($desembolsos_teoricos && $reset){
                $this->mod->eliminar_desembolsos_teoricos($desembolsos_teoricos);
            }
            
            return true;
        }
    }
    
    
    function x_agregar_cambiotasa(){
        $creditos = $_POST['credito_id'];
        
        
        $version = $_POST['version_id'];
        

        $data = array();
        $data['por_int_compensatorio'] = $_POST['tasa'];
        $data['por_int_subsidio'] = $_POST['subsidio'];
        $data['por_int_moratorio'] = $_POST['moratorio'];
        $data['por_int_punitorio'] = $_POST['punitorio'];
        
        $fecha = $_POST['fecha'];
        $data['TIPO'] = 2;
        
        foreach($creditos as $credito){
            $this->mod->set_credito_active($credito);
            $this->mod->set_version_active();
            
            //genero la variacion corerspondiente al desembolso
            $ret = $this->mod->generar_evento( $data, true, $fecha);

            //agrego el registro desembolso a la db 
            $cuotas_restantes = $this->mod->get_cuotas_restantes( $fecha);
            $this->mod->agregar_tasa( $data['por_int_compensatorio'], $data['por_int_subsidio'],$data['por_int_moratorio'],$data['por_int_punitorio'],$cuotas_restantes, $fecha);
            $this->mod->assign_id_evento($ret['ID'],2);
            $this->mod->get_segmentos_cuota($fecha);
        }

        echo $this->_get_cuotas();
    }

    
    function x_segmentar(){
        $credito_id = $_POST['credito_id'];
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        $this->segmentar($credito_id, $version );
        echo $this->_get_cuotas();
    }
    function segmentar($cred, $version = 0){

        $this->mod->set_credito_active($cred);
        $this->mod->set_version_active($version);

        $this->mod->renew_datos();
        resetlog();
        $dia = 10;
        $mes = 9;
        $year = 2014;
        $fecha = mktime(0,0,0,$mes, $dia, $year);
        $this->mod->get_segmentos_cuota( $fecha );
    }
    

    
    function x_eliminar_variacion(){
        resetlog();
        $id_variacion = $_POST['id_variacion'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $version = $this->mod->set_version_active($version);


        $this->mod->renew_datos();
        
        $this->mod->eliminar_variacion($id_variacion);
        $this->mod->get_segmentos_cuota();
        
        //$this->mod->add_single_cuota();
        //$this->mod->generar_cuotas();
        echo $this->_get_cuotas();
    }
    
    function x_eliminar_gasto(){
        resetlog();
        $credito_id = $_POST['credito_id'];
        $gasto_id = $_POST['gasto'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $version = $this->mod->set_version_active($version);

        $this->mod->renew_datos();
        
        
        $this->mod->eliminar_gasto($gasto_id);
        $this->mod->get_segmentos_cuota();
        
        //$this->mod->add_single_cuota();
        //$this->mod->generar_cuotas();
        echo $this->_get_cuotas();
    }
    
    function x_generar_cuota(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        $this->mod->renew_datos();
        $this->mod->add_single_cuota($fecha);

       // $this->segmentar($credito_id, $version);

        echo $this->_get_cuotas();
//        print_array($ret);
        //$this->mod->add_single_cuota($credito_id, $fecha);
    }
    
    function x_actualizar_lista(){
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        $this->mod->renew_datos();
        
        echo $this->_get_cuotas();
    }
    
    function x_obtener_pago(){
        $credito_id = $_POST['credito_id'];
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        
        $id_variacion = $_POST['id_variacion'];
        $this->mod->renew_datos();
        $cuotas = $this->mod->get_pago($id_variacion);
        //print_array($cuotas);
        echo $this->view("pago",array("cuotas"=>$cuotas ));
    }
    
    function x_guardar_opciones_cuota(){
        $inicio = $_POST['fecha_inicio'];
        $vencimiento = $_POST['fecha_vencimiento'];
        $cuotas_restantes = $_POST['cuotas_restantes'];
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);
        $this->mod->renew_datos();
        
        
        $this->mod->modificar_fecha_cuota( $cuotas_restantes, $inicio, $vencimiento);
    }
    function x_abrir_opciones_cuota(){
        $credito_id = $_POST['credito_id'];
        $cuotas_restantes = $_POST['cuotas_restantes'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        $cuota = $this->mod->get_cuota($cuotas_restantes);
        
        echo $this->view("opciones_cuota",$cuota);
    }    
    
    function x_enviar_cuota(){
        $credito_id = $_POST['credito_id'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        
        $fecha = $_POST['fecha'];
        $this->mod->enviar_cuota( $fecha);
        //$this->mod->enviar_cuota($id_credito, $fecha);
        
    }
    
    
    
    function x_get_desembolsos_teoricos(){
        $credito_id = $_POST['id_credito'];
        
        $this->mod->set_credito_active($credito_id);
        $version = $_POST['version_id'];
        $this->mod->set_version_active($version);

        
        $desembolsos = $this->mod->get_desembolsos_teoricos();
        echo json_encode($desembolsos);
    }
    /*
    function _verificar_cuotas_canceladas( $fecha = false){
        $this->mod->verificar_cuotas_canceladas( $fecha);
    }
     * 
     */
    
    function x_eliminar_version(){
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        $this->mod->set_credito_active($credito_id);
        $this->mod->set_version_active($version);
        
        $this->mod->renew_datos();
        $this->mod->eliminar_version();
        
    }
    
    function iins(){
        $ret = $this->mod->cancelar_pagos_subsidiados(29557);
        if ($ret){
            
        }
    }
    
    function x_agregar_version(){
        $this->mod->agregar_version($data['fecha'], 1, "VERSION INICIAL");
    }
    
    
    function x_leer_desembolsos_pendientes(){
        $credito_id = $_POST['credito_id'];
        $this->mod->set_credito_active($credito_id);
        $desembolsos = $this->mod->leer_desembolsos_pendientes();
        $data['view'] = $this->view("solicitudes_desembolsos", array("desembolsos"=>$desembolsos));
        $data['desembolsos'] = $desembolsos;
        echo json_encode($data);
    }
    
    
    function x_filtrar_cuotas(){
        $creditos = $_POST['creditos'];
        $desde = $_POST['desde'];
        $hasta = $_POST['hasta'];
        
        $cuotas_arr = array();
        foreach($creditos as $credito){
            $this->mod->set_credito_active($credito);
            $this->mod->set_version_active();
            $this->mod->renew_datos();
            $cuotas = $this->mod->get_cuotas_credito();
            
            $arr_cuotas_tmp = array();
            foreach($cuotas['RESULT'] as $cuota){
                if ($cuota['FECHA_INICIO'] >= $desde && $cuota['FECHA_VENCIMIENTO'] <= $hasta){
                    $arr_cuotas_tmp[] = $cuota;
                }
            }
            $cuotas_arr[] = array("CUOTAS"=>$arr_cuotas_tmp,"CREDITO"=>$credito);
        }
        
        //print_array($cuotas_arr);
        $rtn['view'] = $this->view("lista_cuotas",array("RESULT"=>$cuotas_arr));
        $rtn['cuotas'] = $cuotas_arr;
        echo json_encode($rtn);
    }
    
    function x_modificar_vencimiento(){
        $_POST['creditos'];
        $cuotas = $_POST['cuotas'];
        $fecha_vencimiento = $_POST['fecha_vencimiento'];
        
        $arr_cuotas = array();
        
        //PROBAR
        return;
        foreach($cuotas as $cuotas_credito){
            if (count($cuotas_credito['CUOTAS']) ){
                $this->mod->set_credito_active($cuotas_credito['CREDITO']);
                $this->mod->set_version_active();
                $this->mod->renew_datos();

                $this->mod->modificar_fecha_cuota( $cuotas_credito['CUOTAS'][0]['CUOTAS_RESTANTES'], false, $fecha_vencimiento );                
                $arr_cuotas[] = $cuotas_credito['CUOTAS'][0];
            }
        }
    }
    
    function x_agregar_gasto(){
        $fecha = $_POST['fecha'];
        $creditos = $_POST['creditos'];
        $monto = $_POST['monto'];
        $descripcion = $_POST['descripcion'];
        
        
        echo "fecha:".$fecha." - creditos:".implode(",",$creditos)." - monto:".$monto." - concepto:".$descripcion;

        foreach($creditos as $credito){
            $this->mod->set_credito_active($credito);
            $this->mod->set_version_active();        
            $this->mod->agregar_gasto($monto, $fecha, $descripcion );
        }
        
    }
    
    function x_generar_chequera(){
        $creditos = $_POST['cuotas'];
        $fecha = $_POST['fecha'];
        $desde = $_POST['fecha_desde'];
        $hasta = $_POST['fecha_hasta'];
        

        $html ="<style>
            .datos{font-weight:bolder;}
            .detalle td{border-bottom:1px solid black;}</style>";
        
        foreach($creditos as $credito){
            $this->mod->set_credito_active($credito['CREDITO']);
            //echo "CREDITO_ID:".$id."---";
            $this->mod->set_version_active();            
            
            $clientes = $this->mod->get_clientes_credito();
            
            
            
            $credito['DOMICILIO'] = $clientes[0]['DIRECCION']." - ".$clientes[0]['LOCALIDAD']." - ".$clientes[0]['PROVINCIA'];
            $credito['CUIT'] = $clientes[0]['CUIT'];
            $credito['RAZONSOCIAL'] = $clientes[0]['RAZON_SOCIAL'];
            

            $html .= "<table>";
            $html .= "<tr><td colspan='8' align='center'><h1>FIDEICOMISO MICROEMPRENDIMIENTO</h1></td></tr>";
            
            $html .= "<tr>
                <td colspan='7'>Appellido y Nombre o Razón Social: <span class='datos'>".$credito['RAZONSOCIAL']."</span></td>
                <td >Nro de Cred.<br/><span class='datos'>".$credito['CREDITO']."</span></td>
                    </tr>";            
            $html .= "<tr>
                <td colspan='3' align='center'>Cuit Nro.<br/><span class='datos'> ".$credito['CUIT']."</span></td>
                <td colspan='5'>Domicilio - Departamento - Provincia.<br/><span class='datos'>".$credito['DOMICILIO']."</span></td>
                    </tr>";            
            
            if (isset($credito['CUOTAS'])){
                foreach($credito['CUOTAS'] as $cuota){
                    $code = $this->mod->_generar_codbar($cuota['ID']);
                    $html .= '<tr><td colspan="8">&nbsp;</td></tr>';
                    $html .= '<tr class="detalle"><td colspan="7"><span class="datos">DETALLE</span></td><td ><span class="datos">IMPORTE</span></td></tr>';
                    $html .= '<tr class="detalle"><td colspan="7">Capital</td><td >'.number_format($cuota['AMORTIZACION_TEORICA'],2).'</td></tr>';
                    $html .= '<tr class="detalle"><td colspan="7">Intereses Compensatorios</td><td >'.number_format($cuota['INT_COMPENSATORIO'],2).'</td></tr>';
                    $html .= '<tr class="detalle"><td colspan="7">Interese Moratorios y Punitorios</td><td >'.number_format($cuota['INT_MORATORIO'] + $cuota['INT_PUNITORIO'],2).'</td></tr>';
                    $iva = $cuota['INT_MORATORIO']  + $cuota['INT_COMPENSATORIO_IVA']  + ($cuota['POR_INT_MORATORIO'] * IMP_IVA)  + ($cuota['POR_INT_PUNITORIO'] * IMP_IVA) ;
                    $html .= '<tr class="detalle"><td colspan="7">IVA</td><td >'.number_format($iva).'</td></tr>';
                    $total = $cuota['AMORTIZACION_TEORICA'] + $cuota['INT_COMPENSATORIO'] + $cuota['INT_MORATORIO'] + $cuota['INT_PUNITORIO'] + $iva;
                    
                    $barcode = new TCPDFBarcode($code, "C128");
                    $html_code = $barcode->getBarcodeHTML(2,45,'black');
                    //echo $html_code ;
                    $html .= "<tr><td colspan='8'>&nbsp;</td></tr>";
                    $html .= '<tr>
                        <td colspan="3">'.$html_code .'<br/>'.$code.'</td><td >&nbsp;</td>
                        <td colspan="2" align="center"><span class="datos">Vencimiento <br/>'.date("d/m/Y",$cuota['FECHA_VENCIMIENTO']).'</span></td>
                        <td colspan="2" align="right"><span class="datos">Total <br/>'.number_format($total,2).'</span></td>
                        </tr>
                        <tr><td colspan="8">&nbsp;</td></tr>';
                }
            }
            $html .= "</table>";
        }        
        
        


        // Print text using writeHTMLCell()
   //     $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
     //   $pdf->Output('log/example_001.pdf', 'F');
        file_put_contents("html.html", $html);

        echo $html;
        
        
        
        /*
        $this->mod->save_last_state(false);
        foreach($creditos as $credito){
            $this->mod->set_credito_active($credito);
            $this->mod->set_version_active();     
            
            $this->mod->generar_chequera($fecha, $desde, $hasta);
        }*/
    }
    
    
    function x_get_tasas_fecha(){
        $fecha = $_POST['fecha'];
        $credito_id = $_POST['credito_id'];
        $version = $_POST['version_id'];
        
        $this->mod->set_credito_active($credito_id[0]);
        $this->mod->set_version_active($version);        
        
        $this->mod->renew_datos();
        
        $tasas = $this->mod->get_tasas_fecha($fecha);
        
        echo json_encode($tasas);
    }

    
        
    
}



// extend TCPF with custom functions
class MYPDF extends TCPDF {

    public function MultiRow($left, $right) {
        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0)

        $page_start = $this->getPage();
        $y_start = $this->GetY();

        // write the left cell
        $this->MultiCell(130, 0, $left, 1, 'R', 1, 2, '', '', true, 0);

        $page_end_1 = $this->getPage();
        $y_end_1 = $this->GetY();

        $this->setPage($page_start);

        // write the right cell
        $this->MultiCell(0, 0, $right, 1, 'J', 0, 1, $this->GetX() ,$y_start, true, 0);

        $page_end_2 = $this->getPage();
        $y_end_2 = $this->GetY();

        // set the new row position by case
        if (max($page_end_1,$page_end_2) == $page_start) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 == $page_end_2) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif ($page_end_1 > $page_end_2) {
            $ynew = $y_end_1;
        } else {
            $ynew = $y_end_2;
        }

        $this->setPage(max($page_end_1,$page_end_2));
        $this->SetXY($this->GetX(),$ynew);
    }

}
