<?php

class agencia extends main_controller {
    function agencia() {
        $this->mod = $this->model("agencia_model");
    }

    function init($provincia = 0, $opcion = 0, $id_objeto = 0) {

        //opcion = 1,2,3
        //1 = Consultar o Agregar // 2 = Listado // 3 = Editar


        $this->constructor();
        if (!isset($_SESSION["USERADM"]))
            header("Location: " . '/' . URL_PATH);
        //$this->_get_loged();
        $this->setCss(array("init.css"));
        $this->setJs(array("init.js", 'forms.js'));
        $this->setPlug(array("chosen"));
        $this->setPlug(array("jalerts"));
        $this->setPlug(array("numeric"));
        $this->setPlug(array("validation"));
        $this->setPlug(array("fancybox"));
        $this->setPlug(array("jqgrid"));

        $id_permiso = 18; // permiso de acceso a este modulo (fid_permisos)
        $arr_permiso_mod = isset($_SESSION["USER_PERMISOS"][$id_permiso]) ? $_SESSION["USER_PERMISOS"][$id_permiso] : 0;
        if (PERMISOS_ALL == 1) {
            $arr_permiso_mod = array
                (
                "MOSTRAR" => 1,
                "ALTA" => 1,
                "BAJA" => 1,
                "MODIFICACION" => 1,
                "EXPORTAR" => 1,
                "OTROS" => 1,
                "VER" => 1
            );
        }

        /* permiso alta */
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['ALTA'] == 1)
            $this->_js_var['_permiso_alta'] = 1;
        else
            $this->_js_var['_permiso_alta'] = 0;
        /* permiso alta */

        /* permiso baja */
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['BAJA'] == 1)
            $this->_js_var['_permiso_baja'] = 1;
        else
            $this->_js_var['_permiso_baja'] = 0;
        /* permiso baja */

        /* permiso mod */
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['MODIFICACION'] == 1)
            $this->_js_var['_permiso_modificacion'] = 1;
        else
            $this->_js_var['_permiso_modificacion'] = 0;
        /* permiso mod */

        /* permiso ver */
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['VER'] == 1)
            $this->_js_var['_permiso_ver'] = 1;
        else
            $this->_js_var['_permiso_ver'] = 0;
        /* permiso ver */

        /* permiso exportar */
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['EXPORTAR'] == 1)
            $this->_js_var['_permiso_exportar'] = 1;
        else
            $this->_js_var['_permiso_exportar'] = 0;
        /* permiso exportar */

        $datax = array();
        $datax['main'] = $this->_obtener_main($arr_permiso_mod, $provincia, $opcion, $id_objeto);
        $datax['titulo'] = "Administracion";
        $datax['etiqueta_modulo'] = "Agencia";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USER_ROL'] = $_SESSION["USER_ROL"];
        $this->_js_var['_USUARIO_SESION_ACTUAL'] = $_SESSION["USERADM"];

        $this->render($datax);
    }

    function _obtener_main($arr_permiso_mod, $provincia, $opcion, $id_objeto) {

        $pruebaLlenar = array();

        $data['fecha'] = $this->get_fecha();
        $data['etiqueta_mod'] = "Requerimiento";

        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ', $data['hora_actual']));
        $data['hora_bd'] = $data['hora_actual'];
        if ($provincia) {
            $data['lst_provincias'] = $this->x_getprovincias();
            $xxx = $data['lst_condicioniva'] = $this->x_getcondicioniva();
            $data['lst_condicioniibb'] = $this->x_getcondicioniibb();
            $data['lst_usu_coordinadores'] = $this->x_getCoordinadores();
            $data['lst_usu_jefeoperaciones'] = $this->x_getJefe();
        }
        $this->_js_var['_provincia'] = $provincia;
        $this->_js_var['_opcion'] = $opcion;
        $this->_js_var['_id_objeto'] = $id_objeto;
        //return $this->view("notas", $data);
        /* permiso mostrar */
        if (1 || $_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['MOSTRAR'] == 1):
            //return $this->view("notas", $data);
            if ($provincia == 4):
                return $this->view("vista6_revision", $data);
            elseif ($provincia == 3):
//                $data['lst_operatorias'] = $this->mod->get_operatorias_importacion();
                return $this->view("vista5_importar", $data);
            elseif ($provincia == 0):
                return $this->view("vista1", $data);
            elseif (($provincia == 12 || $provincia == 17) && ($opcion == 3)):
                return $this->view("vista3", $data);
            elseif (($provincia == 12 || $provincia == 17) && ($opcion == 1)):
                return $this->view("vista3", $data);

            elseif (($provincia == 12 || $provincia == 17) && ($opcion == 2)):
                return $this->view("vista4_listado", $data);
            elseif (($provincia == 12 || $provincia == 17) && ($opcion == 7)):
                return $this->view("vista7_operatoria", $data);
            elseif (($provincia == 12 || $provincia == 17) && ($opcion == 8)):
                $data['lst_fideicomisos'] = $this->mod->get_fideicomisos();
                return $this->view("form_operatoria_nueva", $data);
            elseif (($provincia == 12 || $provincia == 17) && ($opcion == 9)):
                $data['lst_fideicomisos'] = $this->mod->get_fideicomisos();
                return $this->view("form_operatoria_nueva", $data);
            elseif (($provincia == 12 || $provincia == 17) && ($opcion == 10)):
//                $data['lst_fideicomisos'] = $this->mod->get_fideicomisos();
                return $this->view("vista8_estado", $data);
            elseif ($provincia == 12 || $provincia == 17):
                return $this->view("vista2", $data);
            endif;

        else:
            return $this->view("error404", array(), "backend/dashboard");
        /* permiso mostrar */
        endif;
    }

    function x_actualizarT_tmp() {
        $obj = $this->mod->actualizarT_tmp();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getclientessql() {
        $obj = $this->mod->getclientessql();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getbodegas() {
        $obj = $this->mod->get_bodegas();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getbodegas_vino() {
        $id_operatoria = $_SESSION['OPERATORIA'];
        $obj = $this->mod->getbodegas_vino($id_operatoria);
//        $obj = $this->mod->getbodegas_vino();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getOpeBodegas() {
        $obj = $this->mod->get_ope_bodegas();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getCoordinadores() {
        $obj = $this->mod->get_coordinadores();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getJefe() {
        $obj = $this->mod->get_jefes();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getProveedores() {
        $obj = $this->mod->get_proveedores();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getcondicioniibb() {
        $obj = $this->mod->get_condicioniibb();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getcondicioniva() {
        $obj = $this->mod->get_condicioniva();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_marcar_respondida() {
        $iid = $_POST['iid'];
        $idope = $_POST['idope'];
        $remitente = $_POST['remitente'];
        $obj = $this->mod->marcar_respondida($iid, $idope, $remitente);
        echo $obj;
    }

    function x_getobjcliente() {
        $cuit = $_POST['cuit'];
        $rtn = $this->mod->getobjcliente($cuit);
        echo trim(json_encode($rtn ? $rtn[0] : array()));
    }

    function x_getNumOpe() {
//        $_SESSION['OPERATORIA']=0;
        $id_cliente = $_POST['id_cliente'];
        $rtn = $this->mod->getNumOpe($id_cliente);
        $_SESSION['OPERATORIA'] = $rtn[0]['ID_OPERATORIA'];
        echo trim(json_encode($rtn ? $rtn[0] : array()));
    }

    function x_sincronizarAgencia() {
        $datosBuscar = $_POST['datosBuscar'];
        $rtn = $this->mod->sincronizarAgencia($datosBuscar);
//        $_SESSION['OPERATORIA'] = $rtn[0]['ID_OPERATORIA'];
//        echo trim(json_encode($rtn ? $rtn[0] : array()));
    }

    function x_getobj() {
        $id_objeto = $_POST['id_objeto'];
        $rtn = $this->mod->getobj($id_objeto);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getDetalleCu() {
        $id_fac = $_POST['num_fat'];
        $rtn = $this->mod->getDetalleCu($id_fac);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getFormasPago() {
        $html = '';
        $html = '<select class="chzn-select medium-select select" id="fpago-select" onchange="cambiarPrecio()" >
                <option value="">Seleccione forma pago</option>
                <option value="1">1 Cuota</option>
                <option value="2">2 Cuotas</option>
                <option value="3">3 Cuotas</option>
                <option value="4">4 Cuotas</option>
                <option value="5">5 Cuotas</option>
                <option value="6">6 Cuotas</option>';
        $html .= '</select>';
        echo $html;
    }

    function x_verificarCuotas() {
        $num_factura = $_POST['numFactura'];
        $cant_cu = $_POST['cant_cu'];
        $neto = $_POST['neto'];
        $iva = $_POST['iva'];
        $fecha = $_POST['fecha'];
        $rtn = $this->mod->verificarCuotas($num_factura);
        if (!$rtn) {
            $rtn_cuotas = $this->mod->crearCuotas($num_factura, $cant_cu, $neto, $iva, $fecha);
        }
    }

    function x_getTitularidad() {
        $id_objeto = $_POST['num_factura'];
        $rtn = $this->mod->getTitularidad($id_objeto);
        if ($rtn) {
            echo "El usuario " . $rtn[0]['NOMBRE'] . " activo la casilla. Fecha " . $rtn[0]['FECHA'];
        }
    }

    function x_sendPago1() {
        $rtn = $this->mod->sendPago1($_POST);
    }

    function x_sendPago2() {
        $rtn = $this->mod->sendPago2($_POST);
    }

    function x_sendPago3() {
        $rtn = $this->mod->sendPago3($_POST);
    }

    function x_sendPago4() {
        $rtn = $this->mod->sendPago4($_POST);
    }

    function x_sendPago5() {
        $rtn = $this->mod->sendPago5($_POST);
    }

    function x_sendPago6() {
        $rtn = $this->mod->sendPago6($_POST);
    }

    function x_getoperatoria() {
        $id_objeto = $_POST['id_objeto'];
        $rtn = $this->mod->getoperatoria($id_objeto);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getfactura() {
        $id_objeto = $_POST['id_objeto'];
        $rtn = $this->mod->getfactura($id_objeto);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getOperatoriaProveedores() {
        $id_objeto = $_POST['id_operatoria'];
        $rtn = $this->mod->getOperatoriaProveedores($id_objeto);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getOperatoriaBodegas() {
        $id_objeto = $_POST['id_operatoria'];
        $rtn = $this->mod->getOperatoriaBodegas($id_objeto);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getProveedoresEdit() {
        $id_objeto = $_POST['id_operatoria'];
        $rtn = $this->mod->getProveedoresEdit($id_objeto);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getDatoProveedor() {
        $ids_proveedores = $_POST['ids_proveedores'];
        if (empty($_POST['firstColumnData'])) {
            $rtn = $this->mod->getDatoProveedorNuevo($ids_proveedores);
            echo trim(json_encode($rtn ? $rtn : array()));
        } else {
            $firstColumnData = $_POST['firstColumnData'];
            $rtn = $this->mod->getDatoProveedor($ids_proveedores, $firstColumnData);
            echo trim(json_encode($rtn ? $rtn : array()));
        }
    }

    function x_getDatoBodega() {
        $ids_bodegas = $_POST['ids_bodegas'];
        if (empty($_POST['firstColumnData'])) {
            $rtn = $this->mod->getDatoBodegaNueva($ids_bodegas);
            echo trim(json_encode($rtn ? $rtn : array()));
        } else {
            $firstColumnData = $_POST['firstColumnData'];
            $rtn = $this->mod->getDatoBodega($ids_bodegas, $firstColumnData);
            echo trim(json_encode($rtn ? $rtn : array()));
        }
    }

    function x_getBodegasEdit() {
        $id_objeto = $_POST['id_operatoria'];
        $rtn = $this->mod->getBodegasEdit($id_objeto);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_sendCliente() {
        $rtn = $this->mod->sendCliente($_POST);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_sendobj() {
        $obj = $_POST['obj'];
        $cambio_titularidad = false;
        $cambio_titularidad = $_POST['cambio_titularidad'];
        $rtn = $this->mod->sendobj($obj, $cambio_titularidad);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_verificar_enviadas() {
        $obj = $_POST['obj'];
        $rtn = $this->mod->verificar_enviadas($obj);
        echo trim(json_encode($rtn ? $rtn : array()));
    }
    
    function x_guardarlote() {
        $obj = $_POST['obj'];
        $rtn = $this->mod->guardarlote($obj);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_sendobjcli() {
        $obj = $_POST['obj'];
        $rtn = $this->mod->sendobjcli($obj);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getIdOperatoria() {
        $rtn = $this->mod->getIdOperatoria();
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_sendOperatoria() {
        $rtn = $this->mod->sendOperatoria($_POST);
        echo trim(json_encode($rtn));
    }

    function x_updateOperatoria() {
        $rtn = $this->mod->updateOperatoria($_POST);
        echo trim(json_encode($rtn));
    }

    function x_sendProveedores() {
        $obj = $_POST['data_proveedores'];
        $nuevoID = $_POST['nuevoID'];
        $rtn = $this->mod->sendProveedores($obj, $nuevoID);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_updateProveedores() {
        $obj_bod = $_POST['data_bodegas'];
        $obj_prov = $_POST['data_proveedores'];
        $nuevoID = $_POST['nuevoID'];
        $rtn = $this->mod->updateProveedores($obj_bod, $obj_prov, $nuevoID);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getform_importar() {
        $data['datos'] = "";
        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ', $data['hora_actual']));
        echo $this->view("vista5_importar", $data);
    }

    function x_getform_agregar_requerimiento() {
        $data['datos'] = "";
        $data['_semilla'] = time();

        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ', $data['hora_actual']));
        $data['lst_usuarios'] = $this->mod->get_usuarios();

        $idr = isset($_POST['idr']) ? $_POST['idr'] : '';
        $data['obj_req'] = array();
        if ($idr) { //editar
            //get obj
            $obj_req = $this->mod->get_req($idr);
            if ($obj_req)
                $data['obj_req'] = $obj_req[0];

            $data['lst_uploads_req'] = $this->x_get_uploads_notas($idr);
        }
        //log_this('qqqqqq.log', print_r($data,1));
        echo $this->view("form_notas", $data);
    }

    function x_get_uploads_notas($id) {
        $obj = $this->mod->get_uploads_notas($id);
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_sendnota() {
        $obj = $_POST['obj'];
        $adjuntos = isset($obj['adjuntos']) ? $obj['adjuntos'] : array();
        unset($obj['adjuntos']);
        $rtn = $this->mod->sendnota($obj, $adjuntos);
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_delobj() {
        $iid = $_POST['id'];
        $rtn = $this->mod->delobj($iid);
        echo '1';
    }

    function x_vincular_nr() {
        $iidc = $_POST['idcarpeta'];
        $iidnr = $_POST['idnr'];
        $rtn = $this->mod->vincular_nr($iidc, $iidnr);
        echo '1';
    }

    function x_getenviar_a1() {
        $send = $_POST['area'];
        $puesto_in = isset($_POST['puesto_in']) ? $_POST['puesto_in'] : '';
        $obj = $this->mod->getenviar_a1($send, $puesto_in);
        echo trim(json_encode($obj ? $obj : array()));
    }

    function x_getlocalidad() {
        $idp = $_POST['idp'];
        $lst_sr = $this->mod->getlocalidad($idp);

        $productSelect = new SelectBox('Elegir Subrubro');

        if (is_array($lst_sr)):
            $productSelect = new SelectBox('Elegir Subrubro');
            $c = 1;
            foreach ($lst_sr as $rs_sr):
                $productSelect->addItem($rs_sr["ID"], $rs_sr["LOCALIDAD"]);
                $c++;
            endforeach;
        endif;
        echo json_encode($productSelect);
        die();
    }

    function x_getvincular() {
        $idusu = $_POST['idusu'];
        $obj = $this->mod->getvincular($idusu);
        echo trim(json_encode($obj ? $obj : array()));
    }

    function x_guardar_traza_nota() {

        $fecha_actual = date("Y-m-d H:i:s");

        $id_req_nota = $_POST['id_req_nota'];
        $observacion = $_POST['observacion'];
        $descripcion = $_POST['descripcion'];
        $destinatario = $_POST['destinatario'];
        $propietario = isset($_POST['PROPIETARIO']) ? $_POST['PROPIETARIO'] : 0;
        $tid = isset($_POST['tid']) ? $_POST['tid'] : 0;

        $arr_traza = array(
            "ID_OPERACION" => 0,
            "ESTADO" => 1, // estado respondido
            "CARTERADE" => $_SESSION["USERADM"],
            "DESTINO" => $destinatario,
            "OBSERVACION" => $observacion,
            "DESCRIPCION" => $descripcion,
            "ETAPA" => '0',
            "FECHA" => $fecha_actual,
            "ACTIVO" => '1',
            "ETAPA_ORIGEN" => 0,
            "NOTIF" => '1',
            "NOTA" => $id_req_nota
        );
        //actualizar todas las notas con activo=0
        $this->mod->actualizar_notas_activo_cero($id_req_nota);
        $obj = $this->mod->guardar_traza_nota($arr_traza);
        //cambiar leido de la traza ex activa
        $this->mod->cambiar_estado_antigua_traza_nota($tid);

        //actualizar nota (enviado a)
        $arr_nota = array(
            "ENVIADOA" => $destinatario
        );

        if ($propietario > 0) {
            $arr_nota["PROPIETARIO"] = $propietario;
        }

        $this->mod->actualizar_nota($id_req_nota, $arr_nota);
        echo $obj;
    }

    function x_getprovincias() {
        $obj = $this->mod->get_provincias();
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_getProvinciaBodega() {
        $id_prov = $_POST['id'];
        $obj = $this->mod->getProvinciaBodega($id_prov);
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_guardar_traza_paselibre() {

        $fecha_actual = date("Y-m-d H:i:s");

        $id_req_nota = $_POST['id_req_nota'];
        $observacion = $_POST['observacion'];
        $descripcion = $_POST['descripcion'];
        $destinatario = $_POST['destinatario'];
        $tid = isset($_POST['tid']) ? $_POST['tid'] : 0;

        $arr_traza = array(
            "ID_OPERACION" => 0,
            "ESTADO" => 1, // estado respondido
            "CARTERADE" => $_SESSION["USERADM"],
            "DESTINO" => $destinatario,
            "OBSERVACION" => $observacion,
            "DESCRIPCION" => $descripcion,
            "ETAPA" => '0',
            "FECHA" => $fecha_actual,
            "ACTIVO" => '1',
            "ETAPA_ORIGEN" => 0,
            "NOTIF" => '1'
        );

        $obj = $this->mod->guardar_traza_nota($arr_traza);

        //cambiar leido de la traza ex activa
        $this->mod->cambiar_estado_antigua_traza_nota($tid);

        //actualizar nota (enviado a)
        $arr_nota = array(
            "ENVIADOA" => $destinatario
        );
        $this->mod->actualizar_nota($id_req_nota, $arr_nota);
        echo $obj;
    }

    function x_getenviar_a2() {
        $id_area = $_POST['id_area'];
        $puesto_in = isset($_POST['puesto_in']) ? $_POST['puesto_in'] : '';
        $obj = $this->mod->getenviar_a2($id_area, $puesto_in);
        echo trim(json_encode($obj ? $obj : array()));
    }

    function x_verificarcbu() {
        $cbu = $_POST['cbu'];
        $obj = $this->mod->verificarcbu($cbu);
        echo trim(json_encode($obj ? $obj : array()));
    }

    function x_verificarnumfactura() {
        $numero = $_POST['numero'];
        $cuit = $_POST['cuit'];
        $obj = $this->mod->verificarnumfactura($numero,$cuit);
        echo trim(json_encode($obj ? $obj : array()));
    }

    function x_importar_xls() {
//$fid_sanjuan = $_POST['fid_sanjuan'];$ope_sanjuan = $_POST['ope_sanjuan'];
        $preg = $this->mod->validar_archivos_imp_f(); //validar si existe el archivo de la factura
        if ($preg > 0) {
            echo $fact = $this->mod->importar_xls();
        } else {
            echo -1;
        }
    }

    function x_importar_ciu() {
        $obj = $this->mod->importar_ciu();
        echo trim(json_encode($obj ? $obj : array()));
    }

    function x_verificarciu() {
        $nciu = $_POST['nciu'];
        $obj = $this->mod->verificarciu($nciu);
        echo trim(json_encode($obj ? $obj : array()));
    }

    function x_delupload_nota() {
        $idope = $_POST['idnotareq'];
        $ruta = $_POST['ruta'];
        $this->mod->delupload_nota($idope, $ruta);
        $obj = unlink($ruta);
        $tmp = $obj ? $obj : array();
        return $tmp;
    }

    function x_get_info_bancos() {
        $rtn = $this->mod->get_info_grid();
        echo trim(json_encode(array()));
    }

    function get_file1() {
        $semilla = "sem";
        $etapa = "1";
        $etiqueta = "etik";

        if (isset($_FILES['imagen'])) {

            $archivo['tmp'] = $_FILES["imagen"]["tmp_name"];
            $archivo['size'] = $_FILES["imagen"]["size"];
            $archivo['type'] = $_FILES["imagen"]["type"];
            $archivo['name'] = $_FILES["imagen"]["name"];

            $subir = true;
            switch (strtolower($archivo['type'])) {
                case "image/jpeg":
                case "image/png":
                case "image/bmp":
                case "image/gif":
                    $subir = true;
            }

            if ($subir == true) {
                $file_name = FALSE;

                $resultado = stripos($archivo['name'], 'agencia');
                if ($resultado !== FALSE) {
                    $file_name = 'imp_agencia_fact.xlsx';
                }

                $extencion = substr($archivo['name'], -3);
                $uploaded = TEMP_PATH . "importar/" . $file_name; //.".".$extencion;
                if (!$file_name) {
                    echo '<script>
                                var nombre = "' . $archivo['name'] . '";
                                parent.jAlert("El archivo tiene nombre incorrecto");
                            </script>';
                } elseif (file_exists($uploaded)) {
                    echo '
                            <script>
                                var nombre = "' . $archivo['name'] . '";
                                parent.error_post_upload(nombre);
                            </script>';
                } else {
                    if (@move_uploaded_file($archivo['tmp'], $uploaded)) {
                        echo '
                            <script>
                                var nombre = "' . $archivo['name'] . '";
                                var nombre_tmp = "' . $uploaded . '";
                                var etapa = "' . $etapa . '";
                                parent.post_upload(nombre,nombre_tmp,etapa);
                            </script>';
                        $this->mod->guardar_etiqueta($uploaded, $etiqueta);
                    }
                }
            } else {
                echo '
                        <script>
                            var nombre = "' . $archivo['name'] . '";
                            parent.error_post_upload(nombre);
                        </script>';
            }
        }
    }

    function x_testing() {
        $rtn = $this->mod->testing();
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function get_file_notas() {
        $semilla = $_POST["semilla"];
        $id_edit = $_POST["id_edit"];
        $etiqueta = $_POST["req_etiquetah"];
        $etapa = $_POST["etapa"];

        if (isset($_FILES['imagen'])) {

            $archivo['tmp'] = $_FILES["imagen"]["tmp_name"];
            $archivo['size'] = $_FILES["imagen"]["size"];
            $archivo['type'] = $_FILES["imagen"]["type"];
            $archivo['name'] = $_FILES["imagen"]["name"];

            $subir = true;
            switch (strtolower($archivo['type'])) {
                case "image/jpeg":
                case "image/png":
                case "image/bmp":
                case "image/gif":
                    $subir = true;
            }

            if ($id_edit) {
                //verificar
                $arr_uploads = $this->mod->get_arruploads($id_edit);
                if ($arr_uploads) {
                    foreach ($arr_uploads as $rsu) {
                        if (trim(basename($rsu['NOMBRE'])) == $archivo['name']) {
                            $subir = false;
                            break;
                        }
                    }
                }
            }

            if ($subir == true) {
                //$file_name = "img".time(); 
                $file_name = $semilla . "__" . $archivo['name'];

                $extencion = substr($archivo['name'], -3);
                $uploaded = TEMP_PATH . $file_name; //.".".$extencion;
                if (file_exists($uploaded)) {
                    echo '
                            <script>
                                var nombre = "' . $archivo['name'] . '";
                                parent.error_post_upload(nombre);
                            </script>';
                } else {
                    if (@move_uploaded_file($archivo['tmp'], $uploaded)) {
                        echo '
                            <script>
                                var nombre = "' . $archivo['name'] . '";
                                var nombre_tmp = "' . $uploaded . '";
                                var etapa   = "' . $etapa . '";
                                var etiketa = "' . $etiqueta . '";
                                parent.post_upload_req(nombre,nombre_tmp,etapa,etiketa);
                            </script>';
                        //guardar la etiketa
                        //post_upload_req
                        $this->mod->guardar_etiqueta($uploaded, $etiqueta);
                    }
                }
            } else {
                echo '
                        <script>
                            var nombre = "' . $archivo['name'] . '";
                            parent.error_post_upload(nombre);
                        </script>';
            }
        }
    }

    function x_actualizarLista() {

        $tmp = listar_archivos('_tmp/importar/');
        return $tmp;
    }

}

class SelectBox {

    public $items = array();
    public $defaultText = '';

    public function __construct($default) {
        $this->defaultText = $default;
    }

    public function addItem($connection = NULL, $name) {
        $this->items[$name] = $connection;
        return $this;
    }

    public function toJSON() {
        return json_encode($this);
    }

}