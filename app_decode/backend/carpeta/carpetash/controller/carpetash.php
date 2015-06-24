<?php

class carpetash extends main_controller{
    function carpetash(){
        $this->mod = $this->model("carpetash_model");
    }
    
    function init($val_init='',$val_id=''){
        
        $this->_js_var['_val_id'] = $val_id;
        
        $this->_js_var['_val_init'] = 0;
        if (trim($val_init)==''){
            header("Location: " . '/'.URL_PATH.'/backend/carpeta/carpetas');
            die();
        }else{
            if ($val_init==1){ //add
                $this->_js_var['_val_init'] = $val_init;
            }
        }
        
        
        
        $this->constructor();
        
        
        if ( !isset($_SESSION["USERADM"]))
            header("Location: " . '/'.URL_PATH);
        //$this->_get_loged();
        $this->setCss( array("init.css") );
        //$this->setJs( array( "init.js",'forms.js') );
        $this->setJs( array( "init.js") );
        $this->setPlug( array("chosen","jalerts","numeric","validation","fancybox","jqgrid"));
        
        $id_permiso = 9; // permiso de acceso a este modulo (fid_permisos)
        $arr_permiso_mod = isset($_SESSION["USER_PERMISOS"][$id_permiso])?$_SESSION["USER_PERMISOS"][$id_permiso]:0;
        if(PERMISOS_ALL==1){
            $arr_permiso_mod = array
            (
                "MOSTRAR" => 1,
                "ALTA" => 1,
                "BAJA" => 1,
                "MODIFICACION" => 1,
                "EXPORTAR" => 1,
                "OTROS" => 1
            );
        }

        /* permiso alta */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['ALTA'] == 1)
            $this->_js_var['_permiso_alta'] = 1;
        else
            $this->_js_var['_permiso_alta'] = 0;
        /* permiso alta */
        
        /* permiso baja */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['BAJA'] == 1)
            $this->_js_var['_permiso_baja'] = 1;
        else
            $this->_js_var['_permiso_baja'] = 0;
        /* permiso baja */
        
        /* permiso mod */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MODIFICACION'] == 1)
            $this->_js_var['_permiso_modificacion'] = 1;
        else
            $this->_js_var['_permiso_modificacion'] = 0;
        /* permiso mod */
        
        $datax = array();
        $datax['main'] = $this->_obtener_main($arr_permiso_mod);
        $datax['titulo']= "Administracion";
        $datax['etiqueta_modulo'] = "Carpetas";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USUARIO_SESION_ACTUAL'] = $_SESSION["USERADM"];
        $this->_js_var['_USER_AREA'] = $_SESSION["USER_AREA"];
        $this->_js_var['_USER_PUESTO'] = $_SESSION["USER_PUESTO"];
        $this->_js_var['_USER_ROL'] = $_SESSION["USER_ROL"];
        $this->_js_var['_USER_SU_1'] = $_SESSION["USER_SU_1"];
        $this->_js_var['_USER_SU_2'] = $_SESSION["USER_SU_2"];
        $this->_js_var['_USER_SU_3'] = $_SESSION["USER_SU_3"];
        $this->_js_var['_USER_SU_4'] = $_SESSION["USER_SU_4"];
        
        $this->render($datax);
        //etapas
    }

    function _obtener_main($arr_permiso_mod){
        $data['fecha'] = $this->get_fecha();
        $data['etiqueta_mod'] =  "Cliente";
        $data['lst_provincias'] = $this->x_getprovincias();
        $data['lst_condicioniva'] = $this->x_getcondicioniva();
        $data['lst_condicioniibb'] = $this->x_getcondicioniibb();
        $data['lst_tipobeneficiario'] = $this->x_gettipobeneficiario();
        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ',$data['hora_actual']));
        $data['hora_bd'] = $data['hora_actual'];
        $arr_campos = array("NOMBRE","TIPO","DESCRIPCION","TOPE_PESOS","TASA_INTERES_COMPENSATORIA","TASA_INTERES_MORATORIA","TASA_INTERES_POR_PUNITORIOS","TASA_SUBSIDIADA","DESEMBOLSOS","DEVOLUCIONES","PERIODICIDAD");
        $this->_js_array['_campos'] = $arr_campos;
        
        $tmp = $this->x_getgerentes();
        
        $this->_js_var['_gfinanzas_id'] = $tmp["GFINANZAS"]; //41;
        $this->_js_var['_goperaciones_id'] = $tmp["GOPERACIONES"];//37;
        
        /* permiso mostrar */
        if($_SESSION["USER_ROL"]==1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("carpetas", $data);
        else
            return $this->view("error404",array(),"backend/dashboard");
        /* permiso mostrar*/
    }
    
    function x_get_datatable(){
        $data = $this->mod->get_datatable(
                array("ID","RAZON_SOCIAL","CONTACTO"),"ID"
                );
        echo json_encode($data);
    }
    
    function x_getobj(){
        $iid = $_POST['id'];
        $obj = $this->mod->get_obj($iid);
        echo trim(json_encode($obj?$obj[0]:array()));
    }
    
    function x_get_dependencia_operatoria(){
        $tabla = $_POST['tabla'];
        $campo = $_POST['campo'];
        $valor = $_POST['valor'];
        $obj = $this->mod->get_dependencia($tabla,$campo,$valor);
        echo $obj?$obj[0]['cont']:array();
    }
    
    function x_get_dependencia_checklist(){
        $tabla = $_POST['tabla'];
        $campo = $_POST['campo'];
        $valor = $_POST['valor'];
        $obj = $this->mod->get_dependencia($tabla,$campo,$valor);
        echo $obj?$obj[0]['cont']:array();
    }
    
    function x_sendobj(){
        $obj = $_POST['obj'];
        $checklist_all = isset($obj['CHECK_ALL'])?$obj['CHECK_ALL']:array();
        $checklist_sel = isset($obj['CHECK_SEL'])?$obj['CHECK_SEL']:array();
        $postulantes = isset($obj['POSTULANTES'])?$obj['POSTULANTES']:array();
        $arr_obs = isset($obj['arr_obs'])?$obj['arr_obs']:array();
        $arr_chk = isset($obj['arr_chk'])?$obj['arr_chk']:array();
        $carterade = isset($obj['CARTERADE'])?$obj['CARTERADE']:array();
        $adjuntos = isset($obj['adjuntos'])?$obj['adjuntos']:array();
        $arr_infoadd = isset($obj['arr_infoadd'])?$obj['arr_infoadd']:array();
        $arr_itemscom = isset($obj['arr_itemscom'])?$obj['arr_itemscom']:array();
        $checkedItems_deudas = isset($obj['checkedItems_deudas'])?$obj['checkedItems_deudas']:array();
        unset($obj['tipo_seleccion'],$obj['CHECK_ALL'],$obj['CHECK_SEL'],$obj['POSTULANTES'],$obj['arr_obs'],$obj['arr_chk'],$obj['CARTERADE'],$obj['adjuntos'],$obj['arr_infoadd'],$obj['arr_itemscom'],$obj['checkedItems_deudas']);
        $rtn = $this->mod->sendobj( $obj, $checklist_all, $checklist_sel, $postulantes, $arr_obs, $arr_chk, $obj["ID_ETAPA_ACTUAL"], $carterade, $adjuntos, $arr_infoadd, $arr_itemscom, $checkedItems_deudas );
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_update_req(){
        $idr = $_POST['idr'];
        $obj = $_POST['arr_up'];
        //$obj["FTRA"] = date("Y-m-d H:i:s");
        $fcrea = isset($_POST['fcrea'])?$_POST['fcrea']:0;
        $ftra = isset($_POST['ftra'])?$_POST['ftra']:0;
        if ($fcrea==1){
            $obj["FCREA"] = date("Y-m-d H:i:s");
        }
        if ($ftra==1){
            $obj["FTRA"] = date("Y-m-d H:i:s");
        }
        $rtn = $this->mod->update_req( $idr, $obj );
        echo trim( json_encode( $rtn ? $rtn : array() ) );
    }
    
    function x_sendreq(){
        $obj = $_POST['obj'];
        $adjuntos = isset($obj['adjuntos'])?$obj['adjuntos']:array();
        $autor_req = isset($obj['autor_req'])?$obj['autor_req']:0;
        $notif_ope = isset($_POST['notif_ope'])&&$_POST['notif_ope']==1?$_POST['notif_ope']:0;
        unset($obj['adjuntos'],$obj['autor_req']);
        $rtn = $this->mod->sendreq($obj,$adjuntos,$autor_req,$notif_ope);
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_guardar_altacredito(){
        $obj = $_POST['obj'];
        $desembolsos = isset($_POST['arr_desem'])?$_POST['arr_desem']:array();
        $rtn = $this->mod->guardar_altacredito($obj,$desembolsos);
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_guardar_soldesem(){
        $obj = $_POST['obj'];
        $rtn = $this->mod->guardar_soldesem( $obj );
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_guardar_garantia(){
        $obj = $_POST['obj'];
        $rtn = $this->mod->guardar_garantia( $obj );
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_get_info_grid(){
        $rtn = $this->mod->get_info_grid();
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_get_info_bancos(){
        $rtn = $this->mod->get_info_grid();
        echo trim(json_encode(array()));
    }
    
    function x_get_tipos_entidades($where=""){
        $rtn = $this->mod->get_tipos_entidades($where);
        return trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_getlocalidad(){
        $idp = $_POST['idp'];
        $lst_sr = $this->mod->getlocalidad($idp);
        
        $productSelect = new SelectBox('Elegir Subrubro');

        if (is_array($lst_sr)):
            $productSelect = new SelectBox('Elegir Subrubro');
            $c=1;
            foreach($lst_sr as $rs_sr):
                $productSelect->addItem($rs_sr["ID"],$rs_sr["LOCALIDAD"]);
                $c++;
            endforeach;
        endif;
        echo json_encode($productSelect);
        die();
    }
    
    function x_getoperatoria(){
        $idf = $_POST['idf'];
        $lst_sr = $this->mod->getoperatoria($idf);
        
        $productSelect = new SelectBox_ope('Elegir Operatoria');

        if (is_array($lst_sr)):
            $productSelect = new SelectBox_ope('Elegir Operatoria');
            $c=1;
            foreach($lst_sr as $rs_sr):
                $productSelect->addItem( $rs_sr["ID"], $rs_sr["NOMBRE"], $rs_sr["TOPE_PESOS"], $rs_sr["ID_PROCESO"]);
                $c++;
            endforeach;
        endif;
        echo json_encode($productSelect);
        die();
    }
    
    function x_get_garobjeto(){
        $idgartipo = $_POST['idgartipo'];
        $lst_sr = $this->mod->get_garobjeto($idgartipo);
        
        $productSelect = new SelectBox('Elegir Objeto');

        if (is_array($lst_sr)):
            $productSelect = new SelectBox('Elegir Objeto');
            $c=1;
            foreach($lst_sr as $rs_sr):
                $productSelect->addItem($rs_sr["ID"],$rs_sr["DESCRIPCION"]);
                $c++;
            endforeach;
        endif;
        echo json_encode($productSelect);
        die();
    }
    
    function x_getentidad_select(){
        
        $idt = $_POST['idt'];
        $lst_sr = $this->mod->getentidad_select( $idt );
        
        $productSelect = new SelectBox('Elegir Entidad');

        if (is_array($lst_sr)):
            $productSelect = new SelectBox('Elegir Entidad');
            $c=1;
            foreach($lst_sr as $rs_sr):
                $productSelect->addItem($rs_sr["ID"],$rs_sr["NOMBRE"]);
                $c++;
            endforeach;
        endif;
        
        echo json_encode($productSelect);
        die();
    }
    
    function x_delobj(){
        $iid = $_POST['id'];
        $rtn = $this->mod->delobj($iid);
        echo '1';
    }
        
    function x_getprovincias(){
        $obj = $this->mod->get_provincias();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getetapas_op(){
        $obj = $this->mod->getetapas_op();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getcondicioniva(){
        $obj = $this->mod->get_condicioniva();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_cancelar_solicitud(){
        $idope = $_POST['idope'];
        $obj = $this->mod->cancelar_solicitud( $idope );
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_cancelar_autorizacion(){
        $idope = $_POST['idope'];
        $obj = $this->mod->cancelar_autorizacion( $idope );
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_recuperar_carpeta(){
        $idope = $_POST['idope'];
        $obj = $this->mod->recuperar_carpeta( $idope );
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getcondicioniibb(){
        $obj = $this->mod->get_condicioniibb();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getgerentes(){
        $obj = $this->mod->getgerentes();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_gettipobeneficiario(){
        $obj = $this->mod->gettipobeneficiario();
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_traza_aux1(){
        $id_ope_actual = $_POST['id_ope_actual'];
        $obj = $this->mod->get_traza_aux1($id_ope_actual);
        $tmp = $obj?$obj:0;
        echo $tmp;
    }
    
    function x_getenviar_a(){
        $send = $_POST['arr_enviar'];
        $obj = $this->mod->getenviar_a( $send );
        echo trim(json_encode($obj?$obj:array()));
    }
    
    function x_getenviar_a1(){
        $send = $_POST['area'];
        $puesto_in = isset($_POST['puesto_in'])?$_POST['puesto_in']:'';
        $obj = $this->mod->getenviar_a1( $send, $puesto_in );
        echo trim(json_encode($obj?$obj:array()));
    }
    
    function x_getetapas_menor(){
        $etapa = $_POST['etapa'];
        $idope = $_POST['idope'];
        
        $obj = $this->mod->getetapas_menor( $etapa , $idope );
        echo trim(json_encode($obj?$obj:array()));
    }
    
    function x_getenviar_a2(){
        $id_area = $_POST['id_area'];
        $puesto_in = isset($_POST['puesto_in'])?$_POST['puesto_in']:'';
        $obj = $this->mod->getenviar_a2( $id_area,$puesto_in );
        echo trim(json_encode($obj?$obj:array()));
    }
    
    function x_getetapas_menor2(){
        $etapa = $_POST['etapa'];
        $idope = $_POST['idope'];
        //$puesto_in = isset($_POST['puesto_in'])?$_POST['puesto_in']:'';
        $obj = $this->mod->getetapas_menor2( $etapa, $idope );
        echo trim(json_encode($obj?$obj:array()));
    }
    
    
    function x_get_uploads($id){
        $obj = $this->mod->get_uploads($id);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_uploads_gar($idgar){
        $obj = $this->mod->get_uploads_gar($idgar);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_uploads_req($id){
        $obj = $this->mod->get_uploads_req($id);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_uploads_operatoria($id){
        $obj = $this->mod->get_uploads_operatoria($id);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_reqs( $ido ){
        $obj = $this->mod->get_reqs( $ido );
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_req(){
        $idr = $_POST['idr'];
        $obj = $this->mod->get_req($idr);
        echo trim(json_encode($obj?$obj[0]:array()));
    }
    
    function x_get_infoadd($id){
        $obj = $this->mod->get_infoadd($id);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_delupload_req(){
        $idnotareq = $_POST['idnotareq'];
        $ruta = $_POST['ruta'];
        $this->mod->delupload_req($idnotareq,$ruta);
        $obj = unlink($ruta);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_delupload(){
        $idope = $_POST['idope'];
        $ruta = $_POST['ruta'];
        $this->mod->delupload($idope,$ruta);
        $obj = unlink($ruta);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_delupload_ope(){
        $idope = $_POST['idope'];
        $ruta = $_POST['ruta'];
        $this->mod->delupload_ope($idope,$ruta);
        $obj = unlink($ruta);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    
    function x_delupload_gar(){
        $idgar = $_POST['idgar'];
        $ruta = $_POST['descripcion'];
        $this->mod->delupload_gar($idgar,$ruta);
        $obj = unlink($ruta);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_getform_entidad(){
        $data['datos'] = "";
        echo $this->view("form_operatorias", $data);
    }
    
    function x_getform_agregar_requerimiento(){
        $data['datos'] = "";
        $data['_semilla'] = time();
        $idr = isset( $_POST['idr'] )?$_POST['idr']:'';
        $data['_dir_sitio'] = URL_SITIO;
        $data['_no_save'] = isset( $_POST['no_save'] )?$_POST['no_save']:'';
        $data['obj_req'] = array();
        if ($idr){ //editar
            //get obj
            $obj_req = $this->mod->get_req($idr);
            if($obj_req)
                $data['obj_req'] = $obj_req[0];
            
            $data['lst_uploads_req'] = $this->x_get_uploads_req( $idr );
            //log_this('xxxxx.log', print_r($data['lst_uploads_req'],1) );
        }
        $data['ahora'] = date("d/m/Y");
        echo $this->view("form_requerimiento", $data);
    }
    
    
    function x_getform_adjunto(){
        $data['etapa'] = $_POST['etapa'];
        $data['_semilla'] = time();
        echo $this->view("form_adjuntos", $data);
    }
    
    function x_getform_adjunto_gar(){
        //$data['etapa'] = $_POST['etapa'];
        $data['_semilla'] = time();
        $data['id_garantia'] = $_POST['id_garantia'];
        echo $this->view("form_adjuntos_gar", $data);
    }
    
    function x_getform_checklist(){
        $data['datos'] = "";
        echo $this->view("form_checklist", $data);
    }
    
    
    function x_getform_addentidad(){
        $obj = isset($_POST['obj'])?$_POST['obj']:array();
        //$data['lst_tipoentidades'] = $this->mod->get_tipos_entidades();
        
        $tmp = $this->mod->get_obj($obj);
        $data['cad'] = "";
        $chk_array = array();
        $chk_array_comite = array();
        $chk_array_desembolso = array();
        
        
        $data["legales_et4"] = 0;
        $data['cad'] = "";
        if ($tmp){
            $data['entidad'] = $tmp[0];
            $data['obj_js'] = json_encode(  $tmp[0] );
            $lst_operatoria_checklist = $this->mod->get_operacion_checklist( $tmp[0]['ID'] );
            $lst_comite_checklist = $this->mod->get_comite_checklist( $tmp[0]['ID'] );
            $lst_desembolso_checklist = $this->mod->get_desembolso_checklist( $tmp[0]['ID'] );
            $data['id_credito'] = $this->mod->get_id_credito( $tmp[0]['ID'] );
            
            $data["legales_et4"] = $this->mod->get_legales_et4( $tmp[0]['ID'] );
            
            $cad = array();
            
            $carterade = $this->mod->get_carterade( $tmp[0]['CARTERADE'] );
            $data['carterade'] = isset($carterade[0]['nombrecarterade']) ? $carterade[0]['nombrecarterade'] : '';
            
            $etapaactual = $this->mod->get_etapaactual( $tmp[0]['ID_ETAPA_ACTUAL'] );
            $data['etapaactual'] = $etapaactual ? $etapaactual[0]['NOMBRE'] : "";
            
            $id_proceso = $this->mod->get_proceso( $tmp[0]['ID_PROCESO'] );
            $data['id_proceso'] = $id_proceso ? $id_proceso[0]['NOMBRE'] : "";
            
            //$enviadoa = $this->mod->get_enviadoa( $tmp[0]['ENVIADA_A'] );
            //$data['etapaactual'] = $etapaactual[0]['NOMBRE'];
            
            $data['lst_reqs'] = $this->x_get_reqs( $tmp[0]['ID'] );
            $data['lst_uploads'] = $this->x_get_uploads( $tmp[0]['ID'] );
            $data['lst_uploads_operatoria'] = $this->x_get_uploads_operatoria( $tmp[0]['ID'] );
            $data['lst_infoadd'] = $this->x_get_infoadd( $tmp[0]['ID'] );
            $temp = $this->x_get_jefeope( $tmp[0]['ID'] );
            $data['jefe_ope'] = $temp[0]["JEFEOP"]; // establecer valores por defecto?
            $data['coord_ope'] = $temp[0]["COORDOPE"]; // establecer valores por defecto?
            
            $acta_comite_nacta = "";$acta_comite_facta = "";$acta_comite_macta = "";
            $contrato_f="";$contrato_m="";$minuta_fentregam="";$minuta_fdevm="";$minuta_escribano="";
            if ($data['lst_infoadd']){
                foreach($data['lst_infoadd'] as $ind=>$valor_infoadd){
                    if ($valor_infoadd["NOMBRE"]=='comite_nacta'){
                        $acta_comite_nacta = $valor_infoadd["VALOR"];
                    }elseif($valor_infoadd["NOMBRE"]=='comite_facta'){
                        $acta_comite_facta = $valor_infoadd["VALOR"];
                    }elseif($valor_infoadd["NOMBRE"]=='comite_macta'){
                        $acta_comite_macta = $valor_infoadd["VALOR"];
                    }elseif($valor_infoadd["NOMBRE"]=='contrato_fcon'){
                        $contrato_f = $valor_infoadd["VALOR"];
                    }elseif($valor_infoadd["NOMBRE"]=='contrato_mcon'){
                        $contrato_m = $valor_infoadd["VALOR"];
                    }elseif($valor_infoadd["NOMBRE"]=='minuta_fentregam'){
                        $minuta_fentregam = $valor_infoadd["VALOR"];
                    }elseif($valor_infoadd["NOMBRE"]=='minuta_fdevm'){
                        $minuta_fdevm = $valor_infoadd["VALOR"];
                    }elseif($valor_infoadd["NOMBRE"]=='minuta_escribano'){
                        $minuta_escribano = $valor_infoadd["VALOR"];
                    }
                }
            }
            
            $data['acta_comite_nacta'] = $acta_comite_nacta;
            $data['acta_comite_facta'] = $acta_comite_facta;
            $data['acta_comite_macta'] = $acta_comite_macta;
            
            $data['contrato_f'] = $contrato_f;
            $data['contrato_m'] = $contrato_m;
            
            $data['minuta_fentregam'] = $minuta_fentregam;
            $data['minuta_fdevm'] = $minuta_fdevm;
            $data['minuta_escribano'] = $minuta_escribano;
            
            if ($lst_operatoria_checklist){
                foreach($lst_operatoria_checklist as $chk){
                    $cad[] = $chk['ID_CHECKLIST'];
                }
            }
            $data['cad'] = $cad;
            $chk_array = json_encode($data['cad']);
            
            $cadcom = "";
            if ($lst_comite_checklist){
                foreach($lst_comite_checklist as $chk){
                    $cadcom[] = $chk['ID'];
                }
            }
            $data['cadcom'] = $cadcom;
            $chk_array_comite = json_encode($data['cadcom']);
            
            $caddese = "";
            if ($lst_desembolso_checklist){
                foreach($lst_desembolso_checklist as $chk){
                    $caddese[] = $chk['ID'];
                }
            }
            $data['caddese'] = $caddese;
            $chk_array_desembolso = json_encode($data['caddese']);

            //$lst_desembolso_checklist
            
            
            //obtener la confirmacion de copia de contrato a legales
            $estado_copia =  $this->mod->getestadocopia($tmp[0]['ID']);
            $data['_estado_copia'] = $estado_copia;
            
            $data['_ultimo_valor'] = $obj;
            
            $lst_clientes = $this->mod->get_clientes_ope($tmp[0]['ID']);
            $cad = "";
                        
            if ($lst_clientes){
                foreach($lst_clientes as $cli){
                    $cad .= $cli["ID_CLIENTE"].",";
                }
                $cad = substr($cad,0,-1);
            }
            $data['cad'] = $cad;
            
            //get obs
            $lst_etapas = $this->mod->get_etapas_ope($tmp[0]['ID']);
            $data['array_chk'] = array();
            $data['array_obs'] = array();
            $arr_obs = array();
            $arr_chk = array();
            $arr_etapas_ope = array();
            if ($lst_etapas){
                
                
                foreach($lst_etapas as $et){
                    $arr_etapas_ope[ $et["ID_ETAPA"] ] = $et["ESTADO"];
                    switch ($et["ID_ETAPA"]){
                            case 1:
                                $arr_obs["CHECKLIST"] = $et["OBSERVACION"];
                                $arr_chk["CHECKLIST"] = $et["ESTADO"];
                                break;
                            case 2:
                                $arr_obs["CINICIAL"] = $et["OBSERVACION"];
                                $arr_chk["CINICIAL"] = $et["ESTADO"];
                                break;
                            case 4:
                                $arr_obs["LEGALES"] = $et["OBSERVACION"];
                                $arr_chk["LEGALES"] = $et["ESTADO"];
                                break;
                            case 5:
                                $arr_obs["PATRIMONIAL"] = $et["OBSERVACION"];
                                $arr_chk["PATRIMONIAL"] = $et["ESTADO"];
                                break;
                            case 6:
                                $arr_obs["TECNICO"] = $et["OBSERVACION"];
                                $arr_chk["TECNICO"] = $et["ESTADO"];
                                break;
                            case 7:
                                $arr_obs["GARANTIAS"] = $et["OBSERVACION"];
                                $arr_chk["GARANTIAS"] = $et["ESTADO"];
                                break;
                            case 8:
                                $arr_obs["ELEVACION"] = $et["OBSERVACION"];
                                $arr_chk["ELEVACION"] = $et["ESTADO"];
                                break;
                            case 9:
                                $arr_obs["COMITE"] = $et["OBSERVACION"];
                                $arr_chk["COMITE"] = $et["ESTADO"];
                                break;
                            case 10:
                                $arr_obs["RCONTRATO"] = $et["OBSERVACION"];
                                $arr_chk["RCONTRATO"] = $et["ESTADO"];
                                break;
                            case 11:
                                $arr_obs["FCONTRATO"] = $et["OBSERVACION"];
                                $arr_chk["FCONTRATO"] = $et["ESTADO"];
                                break;
                            case 12:
                                $arr_obs["ALTACREDITO"] = $et["OBSERVACION"];
                                $arr_chk["ALTACREDITO"] = $et["ESTADO"];
                                break;
                            default:
                                break;
                    }
                    
                }
                $data['array_obs'] = $arr_obs;
                $data['array_chk'] = $arr_chk;
                $data['array_ope_etapas'] = $arr_etapas_ope;
                
                
                
            }
            
        }
        else{
            $data['_ultimo_valor'] = $this->x_get_ultimoId('fid_operaciones');
            $data['entidad'] = array("ORGANISMO"=>"","TELEFONO"=>"","CUIT"=>"","DESCRIPCION"=>"","NOMBRE"=>"");
            $data['lst_entidades'] = array('');
            $data['lst_uploads'] = '';
            $data['array_ope_etapas'] = array();
            $data['_estado_copia'] = 0;
        }
        
        $data['lst_provincias'] = $this->x_getprovincias();
        $data['lst_fideicomisos'] = $this->x_getfideicomisos();
        $data['lst_clientes'] = $this->x_getclientes();
        $data['lst_escribanos'] = $this->x_getescribanos();
        
        //$data['_array_entidades'] = $ent_array;
        
        $data['_array_checklist'] = $chk_array;
        $data['_array_checklist_comite'] = $chk_array_comite;
        $data['_array_checklist_desembolso'] = $chk_array_desembolso;        
        
        $data['_semilla'] = time();
        $data['permisos_etapas'] = isset($_SESSION["USER_PERMISOS_ETAPAS"])?$_SESSION["USER_PERMISOS_ETAPAS"]:array();
        $data['_permisos_etapas'] = json_encode($data['permisos_etapas']);
        //$data['id_credito'] = "11";
        
        $data['lst_etapas_op'] = $this->x_getetapas_op();
        
        echo $this->view("form", $data);
        
    }
    
   
    function x_actualizar_operacion_atras(){
        $fecha_actual = date("Y-m-d H:i:s");
        $operacion = $_POST['OPERACION'];
        $etapa = $_POST['ID_ETAPA_ACTUAL'];
        //$usuario = $_POST['USUARIO'];
        $etapa_origen = $_POST['ID_ETAPA_ORIGEN'];
        
        $observacion = $_POST['OBSERVACION'];
        $estado = $_POST['ESTADO'];
        $descripcion = $_POST['DESCRIPCION'];
        $carterade = isset($_POST['CARTERADE'])?$_POST['CARTERADE']:"0";
        
        $arr_up = array(
            "ID_ETAPA_ACTUAL"=>$etapa,
            "ENVIADOA"=>'',
            "CARTERADE"=>$carterade
        );
        
        $obj = $this->mod->actualizar_operacion_atras($operacion, $arr_up);
        echo $obj;
    }

    function x_actualizar_operacion(){
        $fecha_actual = date("Y-m-d H:i:s");
        $operacion = $_POST['OPERACION'];
        $etapa = $_POST['ID_ETAPA_ACTUAL'];
        $usuario = $_POST['USUARIO'];
        $etapa_origen = $_POST['ID_ETAPA_ORIGEN'];
        
        $observacion = $_POST['OBSERVACION'];
        $estado = $_POST['ESTADO'];
        $descripcion = $_POST['DESCRIPCION'];
        $autor = isset($_POST['AUTOR'])?$_POST['AUTOR']:"0";
        $autor1 = isset($_POST['AUTOR1'])?$_POST['AUTOR1']:"0";
        $autor2 = isset($_POST['AUTOR2'])?$_POST['AUTOR2']:"0";
        $notif = isset($_POST['NOTIF'])?$_POST['NOTIF']:"0";
        
        $arr_up = array(
            "ID_ETAPA_ACTUAL"=>$etapa,
            "ENVIADOA"=>$usuario
        );
        $arr_traza = array(
            "ID_OPERACION"=>$operacion,
            "ESTADO"=>$estado,
            "CARTERADE"=> $_SESSION["USERADM"],
            "DESTINO"=>$usuario,
            "OBSERVACION"=>$observacion,
            "DESCRIPCION"=>$descripcion,
            "ETAPA"=>$etapa,
            "FECHA"=>$fecha_actual,
            "ETAPA_ORIGEN"=>$etapa_origen,
            "ETAPA_REAL"=>$etapa_origen,
            "AUTOR"=>$autor,
            "AUTOR1"=>$autor1,
            "AUTOR2"=>$autor2
        );
        
        if ($notif>0){
            $usuario_notif = isset($_POST['usuario_notif'])?$_POST['usuario_notif']:"0";
            //insertar traza de notificacion
            $arr_traza_notif = array(
                "ID_OPERACION"=>$operacion,
                "ESTADO"=>'1',
                "CARTERADE"=> $_SESSION["USERADM"],
                "DESTINO"=>$usuario_notif,
                "OBSERVACION"=>'NOTIFICACION',
                "DESCRIPCION"=>'AREA DE ANALISIS ENVIO LA CARPETA AL COORDINADOR',
                "ETAPA"=>$etapa,
                "FECHA"=>$fecha_actual,
                "ETAPA_ORIGEN"=>$etapa_origen,
                "ETAPA_REAL"=>$etapa_origen,
                "NOTIF"=>$notif
            );
            $this->mod->insertar_traza_notif( $arr_traza_notif );
        }
        $obj = $this->mod->actualizar_operacion($operacion, $arr_up, $arr_traza);
        echo $obj;
    }
    
    
    function x_actualizar_operacion_notif(){
        $fecha_actual = date("Y-m-d H:i:s");
        $operacion = $_POST['OPERACION'];
        $etapa = $_POST['ID_ETAPA_ACTUAL'];
        $usuario = $_POST['USUARIO'];
        $etapa_origen = $_POST['ID_ETAPA_ORIGEN'];
        
        $observacion = $_POST['OBSERVACION'];
        $estado = $_POST['ESTADO'];
        $descripcion = $_POST['DESCRIPCION'];
        
        $autor = isset($_POST['AUTOR'])?$_POST['AUTOR']:"0";
        $autor1 = isset($_POST['AUTOR1'])?$_POST['AUTOR1']:"0";
        $autor2 = isset($_POST['AUTOR2'])?$_POST['AUTOR2']:"0";
        $notif = isset($_POST['NOTIF'])?$_POST['NOTIF']:"0";
        
        if ($notif>0){
            $usuario_notif = isset($_POST['usuario_notif'])?$_POST['usuario_notif']:"0";
            //insertar traza de notificacion
            $arr_traza_notif = array(
                "ID_OPERACION"=>$operacion,
                "ESTADO"=>'1',
                "ACTIVO"=>'0',
                "CARTERADE"=> $_SESSION["USERADM"],
                "DESTINO"=>$usuario_notif,
                "OBSERVACION"=>$observacion,
                "DESCRIPCION"=>$descripcion,
                "ETAPA"=>$etapa,
                "FECHA"=>$fecha_actual,
                "ETAPA_ORIGEN"=>$etapa_origen,
                "ETAPA_REAL"=>$etapa_origen,
                "NOTIF"=>$notif
            );
            $obj = $this->mod->insertar_traza_notif( $arr_traza_notif );
        }
        echo $obj;
    }
    
    function x_paselibre(){
        $fecha_actual = date("Y-m-d H:i:s");
        $operacion = $_POST['OPERACION'];
        $etapa = $_POST['ID_ETAPA_ACTUAL'];
        $usuario = $_POST['USUARIO'];
        $etapa_origen = $_POST['ID_ETAPA_ORIGEN'];
        
        $observacion = $_POST['OBSERVACION'];
        $estado = $_POST['ESTADO'];
        $descripcion = $_POST['DESCRIPCION'];
        
        $arr_up = array(
            "ENVIADOA"=>$usuario
        );
        $arr_traza = array(
            "ID_OPERACION"=>$operacion,
            "ESTADO"=>$estado,
            "CARTERADE"=> $_SESSION["USERADM"],
            "DESTINO"=>$usuario,
            "OBSERVACION"=>$observacion,
            "DESCRIPCION"=>$descripcion,
            "ETAPA"=>$etapa,
            "FECHA"=>$fecha_actual,
            "ETAPA_ORIGEN"=>$etapa_origen
        );
      
        $obj = $this->mod->actualizar_operacion($operacion, $arr_up, $arr_traza);
        echo $obj;
    }

    function x_update_tipos_entidades(){
        $obj = $_POST['obj'];
        
        $nombre = $obj["NOMBRE"];
        $iid = $obj["ID"];
        $obj = $this->mod->update_tipos_entidades($iid,$nombre);
        echo $obj;
    }
    
    function x_delete_tipos_entidades(){
        $obj = $_POST['obj'];
        $iid = $obj["ID"];
        $obj = $this->mod->delete_tipos_entidades($iid);
        echo $obj;
    }
    
    function x_add_tipos_entidades(){
        $obj = $this->mod->add_tipos_entidades();
        $tmp = $obj?$obj[0]:array();
        echo json_encode($tmp);
    }
    
    function get_file1(){
        $semilla = $_POST["semilla"];
        $id_edit = $_POST["id_edit"];
        $etapa = $_POST["etapa"];
        $etiqueta = $_POST["req_etiquetah"];
        
        if (isset($_FILES['imagen'])){
           
            $archivo['tmp']  = $_FILES["imagen"]["tmp_name"];
            $archivo['size'] = $_FILES["imagen"]["size"];
            $archivo['type'] = $_FILES["imagen"]["type"];
            $archivo['name'] = $_FILES["imagen"]["name"];
            
            $subir = true;
            switch (strtolower($archivo['type'])){
                case "image/jpeg":
                case "image/png":
                case "image/bmp":
                case "image/gif":
                $subir = true;
            }

            if ($id_edit){
                //verificar
                $arr_uploads = $this->mod->get_arruploads($id_edit);
                if ($arr_uploads){
                    foreach ($arr_uploads as $rsu){
                        if ( trim(basename($rsu['NOMBRE']))==$archivo['name']){
                            $subir = false;
                            break;
                        }
                    }
                }
            }
            
            if ($subir == true){
                //$file_name = "img".time(); 
                $file_name = $semilla . "__".$archivo['name'];
                            
                $extencion = substr($archivo['name'], -3);
                $uploaded = TEMP_PATH.$file_name;//.".".$extencion;
                if (file_exists($uploaded)){
                    echo '
                            <script>
                                var nombre = "'.$archivo['name'].'";
                                parent.error_post_upload(nombre);
                            </script>';
                    
                }else{
                    if (@move_uploaded_file($archivo['tmp'], $uploaded)){
                        echo '
                            <script>
                                var nombre = "'.$archivo['name'].'";
                                var nombre_tmp = "'.$uploaded.'";
                                var etapa = "'.$etapa.'";
                                parent.post_upload(nombre,nombre_tmp,etapa);
                            </script>';
                            $this->mod->guardar_etiqueta($uploaded,$etiqueta);
                    }
                    
                }
                
            }else{
                echo '
                        <script>
                            var nombre = "'.$archivo['name'].'";
                            parent.error_post_upload(nombre);
                        </script>';
                
            }
        }    

    }
    
    
    function get_file_req(){
        $semilla = $_POST["semilla"];
        $id_edit = $_POST["id_edit"];
        $etiqueta = $_POST["req_etiquetah"];
        $etapa = $_POST["etapa"];
        
        if (isset($_FILES['imagen'])){
           
            $archivo['tmp']  = $_FILES["imagen"]["tmp_name"];
            $archivo['size'] = $_FILES["imagen"]["size"];
            $archivo['type'] = $_FILES["imagen"]["type"];
            $archivo['name'] = $_FILES["imagen"]["name"];
            
            $subir = true;
            switch (strtolower($archivo['type'])){
                case "image/jpeg":
                case "image/png":
                case "image/bmp":
                case "image/gif":
                $subir = true;
            }

            if ($id_edit){
                //verificar
                $arr_uploads = $this->mod->get_arruploads($id_edit);
                if ($arr_uploads){
                    foreach ($arr_uploads as $rsu){
                        if ( trim(basename($rsu['NOMBRE']))==$archivo['name']){
                            $subir = false;
                            break;
                        }
                    }
                }
            }
            
            if ($subir == true){
                //$file_name = "img".time(); 
                $file_name = $semilla . "__".$archivo['name'];
                            
                $extencion = substr($archivo['name'], -3);
                $uploaded = TEMP_PATH.$file_name;//.".".$extencion;
                if (file_exists($uploaded)){
                    echo '
                            <script>
                                var nombre = "'.$archivo['name'].'";
                                parent.error_post_upload(nombre);
                            </script>';
                    
                }else{
                    if (@move_uploaded_file($archivo['tmp'], $uploaded)){
                        echo '
                            <script>
                                var nombre = "'.$archivo['name'].'";
                                var nombre_tmp = "'.$uploaded.'";
                                var etapa   = "'.$etapa.'";
                                var etiketa = "'.$etiqueta.'";
                                parent.post_upload_req(nombre,nombre_tmp,etapa,etiketa);
                            </script>';
                        //guardar la etiketa
                        //post_upload_req
                        $this->mod->guardar_etiqueta($uploaded,$etiqueta);
                    }
                }
                
            }else{
                echo '
                        <script>
                            var nombre = "'.$archivo['name'].'";
                            parent.error_post_upload(nombre);
                        </script>';
                
            }
        }    

    }
    
    function get_file1_gar(){
        $semilla = $_POST["semilla"];
        $id_edit = $_POST["id_edit"];
        $id_garantia = $_POST["id_garantia"];
        $etapa = $_POST["etapa"];
        $etiqueta = $_POST["req_etiquetah"];
        
        if (isset($_FILES['imagen'])){
           
            $archivo['tmp']  = $_FILES["imagen"]["tmp_name"];
            $archivo['size'] = $_FILES["imagen"]["size"];
            $archivo['type'] = $_FILES["imagen"]["type"];
            $archivo['name'] = $_FILES["imagen"]["name"];
            
            $subir = true;
            switch (strtolower($archivo['type'])){
                case "image/jpeg":
                case "image/png":
                case "image/bmp":
                case "image/gif":
                $subir = true;
            }

            if ($id_edit){
                //verificar
                $arr_uploads = $this->mod->get_arruploads($id_edit);
                if ($arr_uploads){
                    foreach ($arr_uploads as $rsu){
                        if ( trim(basename($rsu['NOMBRE']))==$archivo['name']){
                            $subir = false;
                            break;
                        }
                    }
                }
            }
            
            if ($subir == true){
                //$file_name = "img".time(); 
                $file_name = $semilla . "__".$archivo['name'];
                            
                $extencion = substr($archivo['name'], -3);
                $uploaded = PATH_GARANTIAS.$file_name;//.".".$extencion;
                if (file_exists($uploaded)){
                    echo '
                            <script>
                                var nombre = "'.$archivo['name'].'";
                                parent.error_post_upload(nombre);
                            </script>';
                    
                }else{
                    if (@move_uploaded_file($archivo['tmp'], $uploaded)){
                        echo '
                            <script>
                                var nombre = "'.$archivo['name'].'";
                                var nombre_tmp = "'.$uploaded.'";
                                var etapa = "'.$etapa.'";
                                parent.post_upload_gar(nombre,nombre_tmp,etapa);
                            </script>';
                            //$this->mod->guardar_etiqueta($uploaded,$etiqueta);
                            $this->mod->guardar_adjunto_gar( $id_garantia,$_SESSION["USERADM"],$etiqueta, $uploaded );
                    }
                    
                }
                
            }else{
                echo '
                        <script>
                            var nombre = "'.$archivo['name'].'";
                            parent.error_post_upload(nombre);
                        </script>';
                
            }
        }    

    }
    
    function x_get_checklist_ope(){
        $id_operatoria = $_POST["id_operatoria"];
        $rtn = $this->mod->get_checklist_ope($id_operatoria);
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_get_condicionesprevias($where=""){
        $rtn = $this->mod->get_condicionesprevias($where);
        echo trim(json_encode($rtn?$rtn:array()));
    }
    
    function x_update_soldese(){
        $idsol = $_POST["idsol"];
        $id_ope_actual = $_POST["id_ope_actual"];
        
        $obj = $_POST["obj"];
        $rtn = $this->mod->update_soldese($idsol,$obj,$id_ope_actual);
        echo trim(json_encode($rtn?$rtn:array()));
    }

    function x_getform_garantias(){
        $idope = $_POST["opeid"];
        
        $idgar = isset($_POST["idgar"])?$_POST["idgar"]:0;
        //obtener lista de los clientes
        $tmp = $this->mod->get_clientes_ope_nombre($idope);
        $datos_gar = array();
        if ($idgar>0){
            $datos_gar = $this->mod->get_garantia_obj($idgar);
        }
        
        $data['datos'] = "";
        $data['lst_tipo_garantia'] = $this->x_gettipo_garantia();
        $data['lst_estado'] = $this->x_getestado_garantia();
        $data['lst_tasadores'] = $this->x_gettasadores();
        $data['datos_gar'] = $datos_gar;
        $tmp = $this->mod->get_clientes_ope_nombre($idope);
        $nom_clientes = $tmp["cad_c"];
        $data['nom_clientes'] = $nom_clientes;
        $data['lst_uploads_gar'] = $this->x_get_uploads_gar( $idgar );
        
        echo $this->view("form_garantias", $data);
    }
    
    function x_getform_actualizarestadogarantia(){
        $idope = $_POST["opeid"];
        $data['lst_gar_estados'] = $this->mod->getform_actualizarestadogarantia();
        $data['id_operacion'] = $idope;
        echo $this->view("form_garantias_estados", $data);
    }
    
    function x_actualizar_garantia(){
        $idgar = $_POST["idgar"];
        $obj_edit = $_POST["obj"];
        $rtn = $this->mod->actualizar_garantia($idgar, $obj_edit);
        return $rtn;
    }
    
    function x_get_solicitud_de_credito(){
        $idope = $_POST["idope"];
        $obj = $this->mod->get_solicitud_de_credito( $idope );
        echo trim(json_encode($obj?$obj[0]:array()));
    }
    
    function x_get_tienecuotas(){
        $idcredito = $_POST["idcredito"];
        $obj = $this->mod->get_tienecuotas( $idcredito );
        echo trim(json_encode($obj?$obj:array()));
    }
    
    
    function x_getform_altacredito(){
        $idope = $_POST["opeid"];
        $id_credito = $_POST["id_credito"];
        //obtener lista de los clientes
        $tmp = $this->mod->get_clientes_ope_nombre($idope);
        
        $nom_clientes = $tmp["cad_c"];
        $cuit_clientes = $tmp["cad_cuit"];
        $cad_email = $tmp["cad_email"];
        $cad_operatoria = $tmp["cad_operatoria"];
        $cad_civa = $tmp["cad_civa"];
        
        $data['datos'] = "";
        $data['nom_clientes'] = $nom_clientes;
        $data['cuit_clientes'] = $cuit_clientes;
        $data['cad_email'] = $cad_email;
        $data['cad_operatoria'] = $cad_operatoria;
        $data['cad_civa'] = $cad_civa;
        $data['cad_obj'] = $tmp;
        
        
        // con $id_credito sabemos si ya hay una solicitud guardada o no
        $data['obj_credito'] = array();
        $data['obj_desembolsos'] = array();
        if ($id_credito>0){
            $data['obj_credito'] = $this->mod->get_credito_ope( $id_credito, $idope );
            $data['obj_desembolsos'] = $this->mod->get_desembolsos_creditos( $id_credito );
            /*
            $tmp_des = $this->mod->get_desembolsos_creditos( $id_credito );
            $arr_desemb = array();
            if($tmp_des){
                
                foreach($tmp_des as $des){
                    $arr_desemb[] = "";
                    
                }
            }
            */
            
        }
        
        echo $this->view("form_altacredito", $data);
    }
    
    function x_getform_solicituddesembolso(){
        $idope = $_POST["opeid"];
        $idsol = isset($_POST["idsol"])?$_POST["idsol"]:0;
        
        $suma_desemb = $this->mod->get_suma_desembolsos($idope);
        $suma_garantias_c = $this->mod->get_suma_garantias_c($idope);
        //SELECT sum(DES_MONTO) AS DES_MONTO FROM `fid_sol_desembolso` WHERE ID_OPERACION='56'
        
        
        //obtener lista de los clientes
        $tmp = $this->mod->get_clientes_ope_nombre($idope);
        $datos_sol = array();
        if ($idsol>0){
            $datos_sol = $this->mod->get_sol_desembolso($idsol);
        }
        $nom_clientes = $tmp["cad_c"];
        $cuit_clientes = $tmp["cad_cuit"];
        $cad_email = $tmp["cad_email"];
        $cad_operatoria = $tmp["cad_operatoria"];
        $cad_civa = $tmp["cad_civa"];
        
        $data['datos'] = "";
        $data['nom_clientes'] = $nom_clientes;
        $data['cuit_clientes'] = $cuit_clientes;
        $data['cad_email'] = $cad_email;
        $data['cad_operatoria'] = $cad_operatoria;
        $data['cad_civa'] = $cad_civa;
        $data['cad_obj'] = $tmp;
        
        $data['datos_sol'] = $datos_sol;
        $data['suma_desemb'] = $suma_desemb;
        $data['suma_garantiasc'] = $suma_garantias_c;
        
        echo $this->view("form_solicituddesembolso", $data);
    }
    
    function x_gettipo_garantia(){
        $rtn = $this->mod->gettipo_garantia();
        return $rtn;
    }
    
    function x_getestado_garantia(){
        $rtn = $this->mod->getestado_garantia();
        return $rtn;
    }
    
    function x_get_num_garantias(){
        $idope = $_POST["idope"];
        $rtn = $this->mod->get_num_garantias($idope);
        //log_this('yyyyy.log', print_r($rtn,1) );
        echo $rtn;
    }
    
    function x_get_gar_comite(){
        $idope = $_POST["idope"];
        $rtn = $this->mod->get_gar_comite($idope);
        //log_this('yyyyy.log', print_r($rtn,1) );
        echo $rtn;
    }
    
    function x_get_gar_const(){
        $idope = $_POST["idope"];
        $rtn = $this->mod->get_gar_const($idope);
        //log_this('yyyyy.log', print_r($rtn,1) );
        echo $rtn;
    }
    
    function x_get_jefeope( $idope ){
        $obj = $this->mod->get_jefeope( $idope );
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_get_id_accion_pendiente(){
        $idope = $_POST["idope"];
        $tmp = $this->mod->get_id_accion_pendiente($idope);
        //log_this('yyyyy.log', print_r($tmp,1) );
        echo trim(json_encode($tmp?$tmp:array()));
        //return $tmp;
    }
    
}


class SelectBox{
	public $items = array();
	public $defaultText = '';
		
	public function __construct($default){
		$this->defaultText = $default;
	}
	
	public function addItem($connection = NULL, $name ){
		$this->items[$name] = $connection;
		return $this; 
	}
	
	public function toJSON(){
		return json_encode($this);
	}
}


class SelectBox_ope{
	public $items = array();
	public $defaultText = '';
		
	public function __construct($default){
		$this->defaultText = $default;
	}
	
	public function addItem($connection = NULL, $name, $monto, $proceso ){
		$this->items[$name] = $connection.'--'.$monto.'--'.$proceso;
		return $this; 
	}
	
	public function toJSON(){
		return json_encode($this);
	}
}