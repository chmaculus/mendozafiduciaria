<?php

define('UPLOAD_BANCOS', 'uploads/bancos/');

class cobranzas extends main_controller {

    function cobranzas() {
        $this->mod = $this->model("cobranzas_model");
    }

    function init() {
        $this->constructor();
        if (!isset($_SESSION["USERADM"]))
            header("Location: " . '/' . URL_PATH);

        $this->setCss(array("init.css"));
        $this->setJs(array("cobranzas.js"));
        $this->setPlug(array("jalerts", "numeric", "validation", "fancybox", "jqgrid"));

        $arr_permiso_mod = $this->_init();
        $datax = array();
        $datax['main'] = $this->_obtener_main($arr_permiso_mod);
        $datax['titulo'] = "Administracion";
        $datax['etiqueta_modulo'] = "Cobranzas de Créditos";
        $datax['name_modulo'] = $this->get_controller_name();

        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USUARIO_SESION_ACTUAL'] = $_SESSION["USERADM"];
        $this->_js_var['_USER_AREA'] = $_SESSION["USER_AREA"];
        $this->_js_var['_USER_PUESTO'] = $_SESSION["USER_PUESTO"];
        $this->_js_var['_USER_ROL'] = $_SESSION["USER_ROL"];
        $this->_js_var['_fecha_proceso'] = date('m-Y');

        $this->render($datax);
    }

    private function _obtener_main($arr_permiso_mod) {
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("cobranzas", array());
        else
            return $this->view("error404", array(), "backend/dashboard");
    }

    private function _init() {
        $id_permiso = 12;
        $arr_permiso_mod = isset($_SESSION["USER_PERMISOS"][$id_permiso]) ? $_SESSION["USER_PERMISOS"][$id_permiso] : 0;
        $_SESSION["USER_ROL"] = 0;
        if (PERMISOS_ALL == 1) {
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
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['ALTA'] == 1)
            $this->_js_var['_permiso_alta'] = 1;
        else
            $this->_js_var['_permiso_alta'] = 0;
        /* permiso alta */

        /* permiso alta */
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['BAJA'] == 1)
            $this->_js_var['_permiso_baja'] = 1;
        else
            $this->_js_var['_permiso_baja'] = 0;
        /* permiso alta */

        /* permiso alta */
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['MODIFICACION'] == 1)
            $this->_js_var['_permiso_modificacion'] = 1;
        else
            $this->_js_var['_permiso_modificacion'] = 0;
        /* permiso alta */

        return $arr_permiso_mod;
    }

    function init_json() {
        $periodo = isset($_POST['fecha']) && $_POST['fecha'] ? strtotime('01-' . $_POST['fecha']) : time();
        echo trim(json_encode($this->mod->get_cuotas_a_facturar($periodo)));
        die;
    }
    
    function init_hoy() {
        set_time_limit(0);
        $fecha = strtotime(date('Y-m-d')) - (24 * 3600); //cambiar por fecha actual
        $fecha_operacion = date('Y-m-d H:i:s');
        
        $this->mod->init_log('facturacion_creditos', $fecha_operacion);
        $creditos = $this->mod->get_cuotas_a_facturar_hoy($fecha);
        if ($creditos) {
            foreach ($creditos as $credito) {
                $this->mod->generar_factura_c($credito, $fecha_operacion);
            }
        }
        $this->mod->cerrar_generar_factura();
        
        $this->mod->init_log('facturacion_recuperos', $fecha_operacion);
        $fecha_pago = strtotime('2016-12-15');
        $creditos = $this->mod->get_creditos_pagos($fecha_pago);
        if ($creditos) {
            foreach ($creditos as $credito) {
                if ($cliente = $this->mod->getCliente($credito, $fecha_pago, $fecha_operacion)) {
                    if ($_creditos = $this->mod->get_recuperos($fecha_pago, $credito['ID'])) {
                        //print_r($_creditos);die;
                        foreach ($_creditos as $_credito) {
                            $this->mod->generar_factura_r($cliente, $_credito, $fecha_operacion);
                        }
                    }
                }
            }
        }
    }

    function facturados_json() {
        set_time_limit(0);
        $periodo = isset($_POST['fecha']) && $_POST['fecha'] ? strtotime('01-' . $_POST['fecha']) : time();
        echo trim(json_encode($this->mod->get_cuotas_facturadas($periodo)));
        die;
    }

    function enviar_facturar() {
        $periodo = isset($_POST['fecha']) && $_POST['fecha'] ? strtotime('01-' . $_POST['fecha']) : time();
        
        $fecha = date('Y-m-d H:i:s');
        foreach ($_POST['creditos'] as $credito_id) {
            if ($credito = $this->get_cuotas_a_facturar($periodo, $credito_id)) {
                $this->mod->generar_factura_c($credito, $fecha);
            }
        }
    }

}
