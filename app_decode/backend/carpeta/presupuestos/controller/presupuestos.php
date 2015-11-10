<?php

class presupuestos extends main_controller {

    function presupuestos() {
        $this->mod = $this->model("presupuestos_model");
    }

    function init() {
        $this->constructor();
        if (!isset($_SESSION["USERADM"]))
            header("Location: " . '/' . URL_PATH);
        //$this->_get_loged();
        $this->setCss(array("init.css"));
        $this->setJs(array("init.js"));
        $this->setPlug(array("chosen"));
        $this->setPlug(array("jalerts"));
        $this->setPlug(array("numeric"));
        $this->setPlug(array("validation"));
        $this->setPlug(array("fancybox"));
        $this->setPlug(array("jqgrid"));

        $id_permiso = 20; // permiso de acceso a este modulo (fid_permisos)
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

        /* permisos */
        $this->_js_var['_permiso_alta'] = ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['ALTA'] == 1) ? 1 : 0;
        $this->_js_var['_permiso_baja'] = ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['BAJA'] == 1) ? 1 : 0;
        $this->_js_var['_permiso_modificacion'] = ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['MODIFICACION'] == 1) ? 1 : 0;
        $this->_js_var['_permiso_ver'] = ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['VER'] == 1) ? 1 : 0;
        $this->_js_var['_permiso_exportar'] = ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['EXPORTAR'] == 1) ? 1 : 0;

        $datax = array();
        $datax['main'] = $this->_obtener_main($arr_permiso_mod);
        $datax['titulo'] = "Administracion";
        $datax['etiqueta_modulo'] = "Presupuestos";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];

        $this->render($datax);
    }

    function _obtener_main($arr_permiso_mod) {
        $data['fecha'] = $this->get_fecha();
        $data['etiqueta_mod'] = "Cliente";
        $data['hora_actual'] = date('d/m/Y H:i:s');
        $data['hora_mostrar'] = current(explode(' ', $data['hora_actual']));
        $data['hora_bd'] = $data['hora_actual'];

        /* permiso mostrar */
        if ($_SESSION["USER_ROL"] == 1 || $arr_permiso_mod['MOSTRAR'] == 1)
            return $this->view("presupuestos", $data);
        else
            return $this->view("error404", array(), "backend/dashboard");
        /* permiso mostrar */
    }

    function x_get_info_grid() {
        $rtn = $this->mod->get_info_grid();
        echo trim(json_encode($rtn ? $rtn : array()));
    }

    function x_getform() {
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $this->setJs(array("init.js"));

        $pres = FALSE;
        if ($id) {
            $pres = $this->mod->get_obj($id);
        }

        $data['cad'] = "";
        $ent_array = array();
        if ($pres) {
            $data['id'] = $id;
            $data['presupuesto'] = $pres;
            $data['items'] = $this->mod->get_items($id);
        }

        echo $this->view("form", $data);
    }

    function x_save() {
        $rtn = array("return" => $this->mod->save($_POST['id'], $_POST['items']));
        echo json_encode($rtn);
        die();
    }

    function x_delobj() {
        echo $this->mod->delobj($_POST['id']);
        die();
    }
    
}