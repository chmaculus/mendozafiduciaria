<?php
date_default_timezone_set("America/Argentina/Mendoza");
require_once('connect.php');

if (isset($_GET["accion"]) && $_GET["accion"] == 'getCreditos') {

    $idope = isset($_GET["idope"]) ? $_GET["idope"] : '0';

    $word = isset($_GET["name_startsWith"]) ? $_GET["name_startsWith"] : "";


    //$cnn->select("t.DESCRIPCION,e.NOMBRE AS ETAPA,u.USERNAME AS USUARIO,MIN(t.FECHA) AS FECHA1,MAX(t.FECHA) AS FECHA2");

    list($d, $m, $y) = explode("-", date("d-m-Y"));
    $time = mktime(0, 0, 0, $m, $d, $y);

    $cnn->where("t.CREDITO_ESTADO <> " . ESTADO_CREDITO_ELIMINADO);
    $cnn->select("t.ID as ID_CREDITO, ifnull(f.NOMBRE,' - ') as FIDEICOMISO, ifnull(o.NOMBRE,' - ') as OPERATORIA, POSTULANTES_NOMBRES, POSTULANTES_CUIT, ID_OPERACION, ifnull(e.CREDITO_ESTADO,0) as CR_ESTADO, t.CREDITO_ESTADO,"
            . " ifnull((SELECT SUM(MONTO) FROM fid_creditos_desembolsos WHERE ID_CREDITO = t.ID), 0) AS DESEMBOLSOS, "
            . " ifnull((SELECT SUM(MONTO) FROM fid_creditos_pagos WHERE ID_CREDITO = t.ID), 0) AS PAGOS, "
            . " ifnull((SELECT SUM(MONTO) FROM fid_creditos_pagos WHERE ID_CREDITO = t.ID AND ID_TIPO = " . PAGO_CAPITAL . "), 0) AS PAGOS_CAPITAL ");    $cnn->order_by("t.ID", "DESC");
    $cnn->join("fid_fideicomiso f", "f.ID = t.ID_FIDEICOMISO", "left");
    $cnn->join("fid_operatorias o", "o.ID = t.ID_OPERATORIA", "left");
    $cnn->join("fid_creditos_extra e", "e.CREDITO_ID = t.ID AND CREDITO_ESTADO_FECHA = " . $time, "left");
    $cnn->group_by("t.ID");

    $creditos = $cnn->get_tabla("fid_creditos t");
    //echo $cnn->last_query();
    //print_array($creditos);
    $rtn = array();
    foreach ($creditos as $credito) {

        $estado = 'AL DIA';
        if ($credito['CR_ESTADO'] == -1)
            $estado = 'DEUDA';
        if ($credito['CR_ESTADO'] == 0)
            $estado = 'SIN VERIFICAR';
        if ($credito['CREDITO_ESTADO'] == ESTADO_CREDITO_CANCELADO)
            $estado = 'CANCELADO';
        if ($credito['CREDITO_ESTADO'] == ESTADO_CREDITO_CADUCADO)
            $estado = 'CADUCADO';
        if ($credito['CREDITO_ESTADO'] == ESTADO_CREDITO_PRORROGADO)
            $estado = 'PRORROGADO';
        if ($credito['CREDITO_ESTADO'] == ESTADO_CREDITO_DESISTIDO)
            $estado = 'DESISTIDO';

        $rtn[] = array(
            "ID_CREDITO" => $credito['ID_CREDITO'],
            "TOMADORES" => $credito['POSTULANTES_NOMBRES'],
            "OPERATORIA" => $credito['OPERATORIA'],
            "FIDEICOMISO" => $credito['FIDEICOMISO'],
            "ESTADO" => $estado,
            "CARPETA" => $credito['ID_OPERACION'] ? $credito['ID_OPERACION'] : " - ",
            "CUIT" => $credito['POSTULANTES_CUIT'],
            "DESEMBOLSOS" => round($credito['DESEMBOLSOS'], 2),
            "PAGOS" => round($credito['PAGOS'], 2),
            "SALDO_CAPITAL" => round($credito['DESEMBOLSOS'] - $credito['PAGOS_CAPITAL'], 2)
        );
    }
    //file_put_contents("log.log", $cnn->last_query());
    echo trim(json_encode($rtn ? $rtn : array()));
    die();
}