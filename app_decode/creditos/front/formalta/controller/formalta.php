<?php

class formalta extends main_controller {

    function formalta() {
        $this->mod = $this->model("formalta_model");
    }

    function init($id = 0) {
        $this->constructor();
   /*     if (!isset($_SESSION["USERADM"]))
            header("Location: " . '/' . URL_PATH);*/
        //$this->_get_loged();
        $this->setCss(array("ivory.css", "formalta.css"));
        //$this->setJs( array( "init.js",'forms.js') );
        $this->setJs(array("formalta.js"));
        $this->setPlug( array("jalerts"));

        $id_permiso = 3;
        $arr_permiso_mod = isset($_SESSION["USER_PERMISOS"][$id_permiso]) ? $_SESSION["USER_PERMISOS"][$id_permiso] : 0;
        /* permiso alta */
        
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

        $datax = array();
        $datax['main'] = $this->_obtener_main( $id);
        $datax['titulo'] = "Administracion";
        $datax['etiqueta_modulo'] = "Carpetas";
        $datax['name_modulo'] = $this->get_controller_name();
        $this->_js_var['_etiqueta_modulo'] = $datax['etiqueta_modulo'];
        $this->_js_var['_USUARIO_SESION_ACTUAL'] = $_SESSION["USERADM"];
        $this->_js_var['_USER_AREA'] = $_SESSION["USER_AREA"];
        $this->_js_var['_USER_PUESTO'] = $_SESSION["USER_PUESTO"];
        $this->_js_var['_USER_ROL'] = $_SESSION["USER_ROL"];

        $this->render($datax);
        //etapas
    }

    function _obtener_main( $id = 0) {

        $credito = $credito_tmp = array("ID" => 0,
            "ID_OPERACION" => 0,
            "ACTIVIDAD" => "",
            "MONTO_CREDITO" => 10000,
            "MONTO_APORTE" => 0,
            "MONTO_OTRO" => 0,
            "MONTO_TOTAL" => 0,
            "MONTO_CREDITO_POR" => 0,
            "MONTO_APORTE_POR" => 0,
            "MONTO_OTRO_POR" => 0,
            "MONTO_TOTAL_POR" => 0,
            "PLAZO_COMPENSATORIO" => 365,
            "PLAZO_PUNITORIO" => 365,
            "PLAZO_MORATORIO" => 365,
            "T_COMPENSATORIO" => 12,
            "T_PUNITORIO" => 24,
            "T_BONIFICACION" => 0,
            "T_MORATORIO" => 12,
            "T_GASTOS" => 0,
            "INTERES_CUOTAS" => 6,
            "INTERES_VTO" => date("Y-m-d"),
            "INTERES_PERIODO" => 09,
            "CAPITAL_CUOTAS" => 6,
            "CAPITAL_VTO" => date("Y-m-d"),
            "CAPITAL_PERIODO" => 0,
            "DESEMBOLSOS" => array(),
            "POSTULANTES" => 0,
            "ID_OPERATORIO" => 0,
            "ID_FIDEICOMISO" => 0,
        );
        
        if ($id > 0){
            $credito = $this->mod->get_credito_from_id($id);
            if (!$credito){
                $credito = $credito_tmp;
            }
        }
        
        //print_array($credito );
        $this->_js_array['DESEMBOLSOS'] = $credito['DESEMBOLSOS'];
        $fecha_arr = explode("-", $credito['CAPITAL_VTO']);

        list($y, $m, $d) = $fecha_arr;
        $credito['CAPITAL_VTO'] = $d . "-" . $m . "-" . $y;
        $credito['MICRO'] = 0;
        return $this->view("form_generar", array("credito" => $credito));
        /* permiso mostrar */
    }


    function x_generar_cuotas() {

        $data['fecha'] = $_POST['fecha'];

        //se calcula la fecha de inicio de la primera cuota sobre la fecha del primer vencimiento y la periodicidad
        list($d, $m, $y) = explode("/", date("d/m/Y", $_POST['fecha_inicio']));
        $data['fecha_inicio'] = (mktime(0, 0, 0, $m - $_POST['periodicidad'], $d, $y));

        $data['cuotas'] = $_POST['cuotas'];
        $micro  = $_POST['micro'];
        $data['cuotas_gracia'] = $_POST['cuotas_gracia'];
        
        $data['total_credito'] = $_POST['total_credito'];
        
        $data['por_int_compensatorio'] = $_POST['int_compensatorio'];
        $data['por_int_subsidio'] = $_POST['int_subsidio'];
        $data['plazo_pago'] = $_POST['plazo_pago'];
        $data['por_int_punitorio'] = $_POST['int_punitorio'];
        $data['por_int_moratorio'] = $_POST['int_moratorio'];
        $data['por_int_gastos'] = $_POST['int_gastos'];
        $data['periodicidad'] = $_POST['periodicidad'];
        $data['periodicidad_tasa'] = $_POST['periodicidad_tasa'];
        $data['TIPO'] = 0;
        $data['iva'] = key_exists('iva', $_POST) ? $_POST['iva'] : 0.21;
        
        $desembolsos = $_POST['desembolsos'];
        
        $plazo_compensatorio = $_POST['plazo_compensatorio'];
        $plazo_moratorio = $_POST['plazo_moratorio'];
        $plazo_punitorio = $_POST['plazo_punitorio'];

        $credito_id = key_exists('credito_id', $_POST) ? $_POST['credito_id'] : 1;
        
        
        echo "credito_id: ".$credito_id."<br/>";
        
        $this->mod->set_credito_active($credito_id);
        $this->mod->limpiar_credito();
        
        $this->mod->set_compensatorio_plazo($plazo_compensatorio);
        $this->mod->set_moratorio_plazo($plazo_moratorio);
        $this->mod->set_punitorio_plazo($plazo_punitorio);
        
        $desembolso_inicial = reset($desembolsos);
        $monto_incial = $desembolso_inicial['monto'];        
        
      //  $this->mod->set_credito_active($credito_id);
        $this->mod->agregar_version($desembolsos[0]['fecha'], 1, "VERSION INICIAL");
        $this->mod->set_version_active();

        //el primer desembolso genera las cuotas
        //$desembolso_inicial = array_shift($desembolsos);
        list($d, $m, $y) = explode("-", $desembolso_inicial['fecha']);
        $fecha = mktime(0, 0, 0, $m, $d, $y);
        
        $data['fecha'] = $fecha;
        $data['monto'] = $monto_incial ;
        
        $this->mod->set_fecha_actual();
        $this->mod->set_fecha_calculo();
        

        $ret = $this->mod->generar_evento($data, false, $fecha);
        $this->mod->save_operacion_credito();
        
        $this->mod->generar_cuotas($ret  );
/*
        $i = 0;
        
        //incluimos  todos los desembolsos
        foreach ($desembolsos as $desembolso) {

            //generamos fecha formato timestamp
            list($d, $m, $y) = explode("-", $desembolso['fecha']);
            $fecha = mktime(0, 0, 0, $m, $d, $y);

            $data['Tipo'] = $desembolso['monto'];
            $data['monto'] = $desembolso['monto'];

            //genero la variacion corerspondiente al desembolso
            $data['TIPO'] = 1;
            $data['ESTADO'] = 5;
            $ret = $this->mod->generar_evento($data, true, $fecha);
            $cuotas_restantes = $this->mod->get_cuotas_restantes($fecha);

            //agrego el registro desembolso a la db        
            $this->mod->agregar_desembolso($data['monto'], $cuotas_restantes, $fecha);
            $this->mod->assign_id_evento($ret['ID'], 1);
        }*/
    }
    
    
    function guardar_credito(){
        
    }

}
