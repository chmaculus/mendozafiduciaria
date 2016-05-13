<?php

set_time_limit(0);

class compravino_model extends main_model {

    public $_tablamod = "fid_nota_req";

    function get_obj($id) {
        if (!$id)
            return array();
        $this->_db->where("id = '" . $id . "'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
    }

    function getDetalleCu($id_fac) {

        if (!$id_fac)
            return array();


        $this->_db->where("NUM_FACTURA ='" . $id_fac . "'");
        $rtn = $this->_db->get_tabla("fid_cu_pagos");
        $rtn_n = array();
        $i = 0;
        foreach ($rtn as $value) {

            if ($value['ESTADO_CUOTA'] == '0') {
                $value['ESTADO_CUOTA'] = 'No enviado';
            } else if ($value['ESTADO_CUOTA'] == '1') {
                $value['ESTADO_CUOTA'] = 'Enviado';
            } else if ($value['ESTADO_CUOTA'] == '2') {
                $value['ESTADO_CUOTA'] = 'Pagado';
            } else {
                $value['ESTADO_CUOTA'] = '';
            }

            $rtn_n[$i] = $value;
            $i++;
        }
        return $rtn_n;
    }

    function get_entidades($id) {
        $this->_db->select("ID_TIPO");
        $this->_db->where("ID_ENTIDAD = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_entidadestipo');
        return $rtn;
    }

//    function getclientessql() {
//        //$rtn = $this->_db->get_tabla('fid_usuarios');
//        $rtn = $this->_dbsql->get_tabla('VENDEDORES');
//        return $rtn;
//    }
    function sincronizarVino($datosBuscar) {
        $j = 0;
        $this->_db->select("IFNULL(CONCAT(u1.NOMBRE,' ',u1.APELLIDO), '-') AS USU_CARGA, 
            IFNULL(CONCAT(u2.NOMBRE,' ',u2.APELLIDO), '-') AS USU_CHEQUEO, f.ID AS IID,f.ID AS ID,f.TOTAL AS TOTAL, f.IVA AS IVA, 
            f.NETO AS NETO, f.PRECIO AS PRECIO, cu.ESTADO_CUOTA,cu.NUM_CUOTA, fe.NOMBRE AS ESTADO, f.OBSERVACIONES AS OBSERVACIONES, 
            f.IMP_ERROR_TEXTO AS IMP_ERROR_TEXTO, f.KGRS AS KGRS, f.LITROS AS LITROS, ent.ID AS ID_BODEGA,ent.NOMBRE AS BODEGA, 
            f.NUMERO AS NUMERO, DATE_FORMAT(f.FECHA, '%d/%m/%Y') AS FECHA, c.RAZON_SOCIAL AS CLIENTE, c.CUIT AS CUIT, c.CBU AS CBU, 
            civa.CONDICION AS CONDIVA, ciibb.CONDICION AS CONDIIBB, DATE(f.CREATEDON) AS CREATEDON, f.ORDEN_PAGO AS ORDEN_PAGO, 
            of.ID_OPERATORIA,f.FORMA_PAGO");
        $this->_db->join("fid_clientes c", "c.ID=f.ID_CLIENTE", "left");
        $this->_db->join("fid_cliente_condicion_iva civa", "civa.ID=c.ID_CONDICION_IVA", "left");
        $this->_db->join("fid_cliente_condicion_iibb ciibb", "ciibb.ID=c.ID_CONDICION_IIBB", "left");
        $this->_db->join("fid_entidades ent", "ent.ID=f.ID_BODEGA", "left");
        $this->_db->join("fid_cu_factura_estados fe", "fe.ID=f.ID_ESTADO", "left");
        $this->_db->join("fid_usuarios u1", "u1.ID=f.USU_CARGA", "left");
        $this->_db->join("fid_usuarios u2", "u2.ID=f.USU_CHEQUEO", "left");
        $this->_db->join("fid_operatoria_vino of", "of.ID_OPERATORIA = f.ID_OPERATORIA", "left");
        $this->_db->join("fid_cu_pagos cu", "cu.NUM_FACTURA=f.NUMERO", "left");
        $fact_enviadas = $this->_db->get_tabla('fid_cu_factura f', "( f.ID LIKE '%%' OR c.CUIT LIKE '%%' OR c.RAZON_SOCIAL LIKE '%%' ) 
            AND f.ID_PROVINCIA='12' AND TIPO=1 AND f.ID_ESTADO='5'");

        foreach ($fact_enviadas as $value) {
            if (is_null($value['NUM_CUOTA'])) {
                //No debe hacer nada si es null
            } else {
                if ($value['ID'] != '' && $value['NUMERO'] != '' && $value['ID_BODEGA'] != '') {

                    $this->_dbsql->select("CUIT,OPERATORIA,LOTE,IDFACTURAINT,NUMFACTURA,TIPO,ESTADO,CCU,UCU,BODEGA,ORDEN_PAGO,FECHA_PROCESADO");
                    $solicitud_adm[$j] = $this->_dbsql->get_tabla("SOLICITUD_ADM", "IDFACTURAINT=" . $value['ID'] .
                            " AND NUMFACTURA=" . $value['NUMERO'] . "" . " AND TIPO='OP' AND UCU=" . $value['NUM_CUOTA'] . " "
                            . "AND BODEGA=" . $value['ID_BODEGA']);

                    if ($solicitud_adm[$j]) {

                        if ($solicitud_adm[$j][0]['TIPO'] == 'OP' && $solicitud_adm[$j][0]['UCU'] != $solicitud_adm[$j][0]['CCU']) {
                            if ($solicitud_adm[$j][0]['ESTADO'] == 2) {
                                $arr_ins_cu = array("ESTADO_CUOTA" => 2, "ORDEN_PAGO" => $solicitud_adm[$j][0]['ORDEN_PAGO']);
                                $this->_db->update('fid_cu_pagos', $arr_ins_cu, " NUM_FACTURA='" . $value['NUMERO'] . "' AND NUM_CUOTA=" . $solicitud_adm[$j][0]['UCU']);
                                $arr_act_fact = array("ID_ESTADO" => 1);
                                $this->_db->update('fid_cu_factura', $arr_act_fact, " NUMERO='" . $value['NUMERO'] . "'");
//                                log_this('log/UPDATE111.log', $this->_db->last_query());
                            }

//            log_this('log/qqqqqqqUpdate2.log', $this->_db->last_query() );
                        } else if ($solicitud_adm[$j][0]['TIPO'] == 'OP' && $solicitud_adm[$j][0]['UCU'] == $solicitud_adm[$j][0]['CCU']) {
                            if ($solicitud_adm[$j][0]['ESTADO'] == 2) {
                                $arr_ins_cu = array("ESTADO_CUOTA" => 2, "ORDEN_PAGO" => $solicitud_adm[$j][0]['ORDEN_PAGO']);
                                $this->_db->update('fid_cu_pagos', $arr_ins_cu, " NUM_FACTURA='" . $value['NUMERO'] . "' AND NUM_CUOTA=" . $solicitud_adm[$j][0]['UCU']);
                                $arr_ins = array("ID_ESTADO" => '9', "ORDEN_PAGO" => $solicitud_adm[$j][0]['ORDEN_PAGO']);
                                $this->_db->update('fid_cu_factura', $arr_ins, " ID=" . $value['ID'] . " AND NUMERO=" . $value['NUMERO'] . " AND ID_BODEGA=" . $value['ID_BODEGA']);
//                            log_this('log/UPDATE111.log', $this->_db->last_query());
                            }
                        }
                    }
                }
                $j++;
            }
        }
    }

    function guardarlote($arr_obj) {

        $id_lote_new = 0;
        if ($arr_obj):
            //insert cabezera
            $id_lote_new = $this->_db->insert('fid_cu_lotes', array("DESCRIPCION" => "descripcion"));

            foreach ($arr_obj as $ciu):

                $id_factura = $ciu["IID"];

                $arr_ins = array(
                    "ID_FACTURA" => $id_factura,
                    "ID_LOTE" => $id_lote_new,
                    "NUMERO_FACTURA" => $ciu["NUMERO"]
                );
                $this->_db->insert('fid_cu_lotespago', $arr_ins);
                //log_this('log/qqqqqqq.log', $this->_db->last_query() );
                $this->_db->update('fid_cu_factura', array("ID_ESTADO" => '5', 'USU_CHEQUEO' => $_SESSION["USERADM"]), "ID='" . $ciu["IID"] . "'");
                $this->_db->select("F.ID_OPERATORIA AS ID_OPERATORIA ,IFNULL(fi.ID_CONTABLE,0) AS ID_CONTABLE,F.ID_CLIENTE AS IDCLIENTE,F.NETO AS NETO,F.IVA AS IVA, F.FORMA_PAGO AS FORMA_PAGO ,
                                F.TOTAL AS TOTAL, c.CUIT, F.ID_BODEGA,OP.ID_FIDEICOMISO AS FIDEICOMISO"); //$this->_db->select("F.ID_CLIENTE AS IDCLIENTE, F.TOTAL AS TOTAL, CUIT, F.ID_BODEGA");
                $this->_db->join("fid_clientes c", "c.ID=f.ID_CLIENTE", 'left');
                $this->_db->join("fid_operatoria_vino OP", "OP.ID_OPERATORIA=F.ID_OPERATORIA", 'left');
                $this->_db->join("fid_fideicomiso fi", "fi.ID = op.ID_FIDEICOMISO", 'left');
                $cuit_cli = $this->_db->get_tabla('fid_cu_factura f', "f.ID='" . $id_factura . "'");

                /* Se hacen dos insert, si se envia la factura por primera vez, se pasa la factura y el valor de la cuota
                 * En caso que ya se haya enviado anteriormente, se pasa solamente la cuota */
                if ((int) $cuit_cli[0]['FORMA_PAGO'] == 1) {
                    $arra_ins = array(
                        "CUIT" => $cuit_cli[0]['CUIT'],
                        "CODIGO_WEB" => $cuit_cli[0]['IDCLIENTE'],
                        "OPERATORIA" => (int) $cuit_cli[0]['ID_OPERATORIA'],
                        "FIDEICOMISO" => (int) $cuit_cli[0]['ID_CONTABLE'],
                        "NETO" => $cuit_cli[0]['NETO'],
                        "IVA" => $cuit_cli[0]['IVA'],
                        "IMPORTE" => $cuit_cli[0]['TOTAL'],
                        "CCU" => (int) $cuit_cli[0]['FORMA_PAGO'], //Cantidad de cuotas
                        "UCU" => 0, // numero de la cuota a pagar
                        "LOTE" => $id_lote_new,
                        "IDFACTURAINT" => $id_factura,
                        "NUMFACTURA" => $ciu["NUMERO"],
                        "CODIGO_DEBO" => "",
                        "TIPO" => "FT",
                        "FECHA_PASADO" => date('Ymd h:i:s'),
                        "FECHA_PROCESADO" => "19010101 00:00",
                        "ESTADO" => "1",
                        "BODEGA" => $cuit_cli[0]['ID_BODEGA']
                    );
                    $this->_dbsql->insert('SOLICITUD_ADM', $arra_ins);

                    $arra_ins_cuota = array(
                        "CUIT" => $cuit_cli[0]['CUIT'],
                        "CODIGO_WEB" => $cuit_cli[0]['IDCLIENTE'],
                        "OPERATORIA" => (int) $cuit_cli[0]['ID_OPERATORIA'],
                        "FIDEICOMISO" => (int) $cuit_cli[0]['ID_CONTABLE'],
                        "NETO" => (float) $cuit_cli[0]['NETO'],
                        "IVA" => (float) $cuit_cli[0]['IVA'],
                        "IMPORTE" => $cuit_cli[0]['TOTAL'],
                        "CCU" => (int) $cuit_cli[0]['FORMA_PAGO'], //Cantidad de cuotas
                        "UCU" => (int) $ciu['NUMCUOTA'], // numero de la cuota a pagar
                        "LOTE" => $id_lote_new,
                        "IDFACTURAINT" => $id_factura,
                        "NUMFACTURA" => $ciu["NUMERO"],
                        "CODIGO_DEBO" => "",
                        "TIPO" => "OP",
                        "FECHA_PASADO" => date('Ymd h:i:s'),
                        "FECHA_PROCESADO" => "19010101 00:00",
                        "ESTADO" => "1",
                        "BODEGA" => $cuit_cli[0]['ID_BODEGA']
                    );
                    $this->_dbsql->insert('SOLICITUD_ADM', $arra_ins_cuota);
                } else if ((int) $cuit_cli[0]['FORMA_PAGO'] >= 1) {
                    if ($ciu['NUMCUOTA'] == '1') {
                        $arra_ins = array(
                            "CUIT" => $cuit_cli[0]['CUIT'],
                            "CODIGO_WEB" => $cuit_cli[0]['IDCLIENTE'],
                            "OPERATORIA" => (int) $cuit_cli[0]['ID_OPERATORIA'],
                            "FIDEICOMISO" => (int) $cuit_cli[0]['ID_CONTABLE'],
                            "NETO" => (float) $cuit_cli[0]['NETO'],
                            "IVA" => (float) $cuit_cli[0]['IVA'],
                            "IMPORTE" => $cuit_cli[0]['TOTAL'],
                            "CCU" => (int) $cuit_cli[0]['FORMA_PAGO'], //Cantidad de cuotas
                            "UCU" => 0, // numero de la cuota a pagar
                            "IDFACTURAINT" => $id_factura,
                            "NUMFACTURA" => $ciu["NUMERO"],
                            "CODIGO_DEBO" => "",
                            "TIPO" => "FT",
                            "FECHA_PASADO" => date('Ymd h:i:s'),
                            "FECHA_PROCESADO" => "19010101 00:00",
                            "LOTE" => $id_lote_new,
                            "ESTADO" => "1",
                            "BODEGA" => $cuit_cli[0]['ID_BODEGA']
                        );
                        $this->_dbsql->insert('SOLICITUD_ADM', $arra_ins);
                        $arra_ins_cuota = array(
                            "CUIT" => $cuit_cli[0]['CUIT'],
                            "CODIGO_WEB" => $cuit_cli[0]['IDCLIENTE'],
                            "OPERATORIA" => (int) $cuit_cli[0]['ID_OPERATORIA'],
                            "FIDEICOMISO" => (int) $cuit_cli[0]['ID_CONTABLE'],
                            "NETO" => (float) $cuit_cli[0]['NETO'],
                            "IVA" => (float) $cuit_cli[0]['IVA'],
                            "IMPORTE" => ((float) $cuit_cli[0]['NETO'] / (int) $cuit_cli[0]['FORMA_PAGO']) + (float) $cuit_cli[0]['IVA'],
                            "CCU" => (int) $cuit_cli[0]['FORMA_PAGO'], //Cantidad de cuotas
                            "UCU" => (int) $ciu['NUMCUOTA'], // numero de la cuota a pagar
                            "LOTE" => $id_lote_new,
                            "IDFACTURAINT" => $id_factura,
                            "NUMFACTURA" => $ciu["NUMERO"],
                            "CODIGO_DEBO" => "",
                            "TIPO" => "OP",
                            "FECHA_PASADO" => date('Ymd h:i:s'),
                            "FECHA_PROCESADO" => "19010101 00:00",
                            "ESTADO" => "1",
                            "BODEGA" => $cuit_cli[0]['ID_BODEGA']
                        );
                        $this->_dbsql->insert('SOLICITUD_ADM', $arra_ins_cuota);
                    } else {
                        $arra_ins_cuota = array(
                            "CUIT" => $cuit_cli[0]['CUIT'],
                            "CODIGO_WEB" => $cuit_cli[0]['IDCLIENTE'],
                            "OPERATORIA" => (int) $cuit_cli[0]['ID_OPERATORIA'],
                            "FIDEICOMISO" => (int) $cuit_cli[0]['ID_CONTABLE'],
                            "NETO" => (float) $cuit_cli[0]['NETO'],
                            "IVA" => (float) $cuit_cli[0]['IVA'],
                            "IMPORTE" => ((float) $cuit_cli[0]['NETO'] / (int) $cuit_cli[0]['FORMA_PAGO']) + (float) $cuit_cli[0]['IVA'],
                            "CCU" => (int) $cuit_cli[0]['FORMA_PAGO'], //Cantidad de cuotas
                            "UCU" => (int) $ciu['NUMCUOTA'], // numero de la cuota a pagar
                            "LOTE" => $id_lote_new,
                            "IDFACTURAINT" => $id_factura,
                            "NUMFACTURA" => $ciu["NUMERO"],
                            "CODIGO_DEBO" => "",
                            "TIPO" => "OP",
                            "FECHA_PASADO" => date('Ymd h:i:s'),
                            "FECHA_PROCESADO" => "19010101 00:00",
                            "ESTADO" => "1",
                            "BODEGA" => $cuit_cli[0]['ID_BODEGA']
                        );
                        $this->_dbsql->insert('SOLICITUD_ADM', $arra_ins_cuota);
                    }
                }
                $this->_db->update('fid_cu_pagos', array("ESTADO_CUOTA" => '1'), "NUM_FACTURA='" . $ciu["NUMERO"] . "' AND NUM_CUOTA=" . (int) $ciu['NUMCUOTA']);
                //file_put_contents("loggg.txt", $return, FILE_APPEND);
                //file_put_contents("loggg.txt", $this->_dbsql->last_query(), FILE_APPEND);
            endforeach;
            $this->actualizarTablasW();

        endif;
        return $id_lote_new;
    }

    function actualizarTablasW() {

        //W_PROVEEDORES
        $rtn = $this->_db->get_tabla('fid_clientes', "CU=1");

        if ($rtn) {
            $this->_dbsql->delete('W_PROVEEDORES');
            foreach ($rtn as $r) {
                $arra_ins = array(
                    "ID" => $r['ID'],
                    "RAZON_SOCIAL" => utf8_decode($r['RAZON_SOCIAL']),
                    "FECHA_ALTA" => $r['FECHA_ALTA'],
                    "ESTADO" => $r['ESTADO'],
                    "INSCRIPCION_IVA" => $r['INSCRIPCION_IVA'],
                    "DIRECCION" => utf8_decode($r['DIRECCION']),
                    "TELEFONO" => $r['TELEFONO'],
                    "CONTACTO" => $r['CONTACTO'],
                    "CUIT" => $r['CUIT'],
                    "CORREO" => $r['CORREO'],
                    "ID_INV" => $r['ID_INV'],
                    "OBSERVACION" => utf8_decode($r['OBSERVACION']),
                    "CBU" => $r['CBU'],
                    "ID_PROVINCIA" => $r['ID_PROVINCIA'],
                    "ID_DEPARTAMENTO" => $r['ID_DEPARTAMENTO'],
                    "ID_CONDICION_IVA" => $r['ID_CONDICION_IVA'],
                    "ID_CONDICION_IIBB" => $r['ID_CONDICION_IIBB'],
                    "INSCRIPCION_IIBB" => $r['INSCRIPCION_IIBB']
                );
                $this->_dbsql->insert('W_PROVEEDORES', $arra_ins);
            }
        }

        //W_OPERATORIA
        $rtn = $this->_db->get_tabla('fid_operatorias');
        if ($rtn) {
            $this->_dbsql->delete('W_OPERATORIA');
            foreach ($rtn as $r) {
                $arra_ins = array(
                    "ID" => $r['ID'],
                    "ID_TIPO_OPERATORIA" => $r['ID_TIPO_OPERATORIA'],
                    "NOMBRE" => utf8_decode($r['NOMBRE']),
                    "DESCRIPCION" => utf8_decode($r['DESCRIPCION']),
                    "TOPE_PESOS" => $r['TOPE_PESOS'],
                    "TASA_INTERES_COMPENSATORIA" => $r['TASA_INTERES_COMPENSATORIA'],
                    "TASA_INTERES_MORATORIA" => $r['TASA_INTERES_MORATORIA'],
                    "TASA_INTERES_POR_PUNITORIOS" => $r['TASA_INTERES_POR_PUNITORIOS'],
                    "TASA_SUBSIDIADA" => $r['TASA_SUBSIDIADA'],
                    "DESEMBOLSOS" => $r['DESEMBOLSOS'],
                    "DEVOLUCIONES" => $r['DEVOLUCIONES'],
                    "PERIODICIDAD" => $r['PERIODICIDAD'],
                    "ID_PROCESO" => $r['ID_PROCESO'],
                    "JEFEOP" => $r['JEFEOP'],
                    "COORDOPE" => $r['COORDOPE']
                );
                $this->_dbsql->insert('W_OPERATORIA', $arra_ins);
            }
        }


        //W_FIDEICOMISOS
        $rtn = $this->_db->get_tabla('fid_fideicomiso');
        if ($rtn) {
            $this->_dbsql->delete('W_FIDEICOMISOS');
            foreach ($rtn as $r) {
                $arra_ins = array(
                    "ID" => $r['ID'],
                    "NOMBRE" => utf8_decode($r['NOMBRE']),
                    "ID_PROVINCIA" => $r['ID_PROVINCIA'],
                    "ID_DEPARTAMENTO" => $r['ID_DEPARTAMENTO'],
                    "FECHA" => $r['FECHA'],
                    "FECHA_INICIO" => $r['FECHA_INICIO'],
                    "FECHA_FIN" => $r['FECHA_FIN'],
                    "DESCRIPCION" => utf8_decode($r['DESCRIPCION']),
                    "OBSERVACIONES" => utf8_decode($r['OBSERVACIONES']),
                    "CUIT" => $r['CUIT'],
                    "MONTOMAX" => $r['MONTOMAX'],
                    "ESTADO" => $r['ESTADO']
                );
                $this->_dbsql->insert('W_FIDEICOMISOS', $arra_ins);
            }
        }

        //W_BODEGAS
        $rtn = $this->_db->get_tabla('fid_bodegas');
        if ($rtn) {
            $this->_dbsql->delete('W_BODEGAS');
            foreach ($rtn as $r) {
                $arra_ins = array(
                    "ID" => $r['ID'],
                    "NOMBRE" => utf8_decode($r['NOMBRE']),
                    "ID_PROVINCIA" => $r['ID_PROVINCIA'],
                    "ID_DEPARTAMENTO" => $r['ID_DEPARTAMENTO'],
                    "ESTADO" => $r['ESTADO']
                );
                $this->_dbsql->insert('W_BODEGAS', $arra_ins);
            }
        }
        //return $rtn;
    }

    function actualizarT_tmp() {

        //W_PROVEEDORES
        $rtn = $this->_db->get_tabla('fid_clientes', "CU=1");

        if ($rtn) {
            $this->_dbsql->delete('W_PROVEEDORES');
            foreach ($rtn as $r) {
                $arra_ins = array(
                    "ID" => $r['ID'],
                    "RAZON_SOCIAL" => utf8_decode($r['RAZON_SOCIAL']),
                    "FECHA_ALTA" => $r['FECHA_ALTA'],
                    "ESTADO" => $r['ESTADO'],
                    "INSCRIPCION_IVA" => $r['INSCRIPCION_IVA'],
                    "DIRECCION" => utf8_decode($r['DIRECCION']),
                    "TELEFONO" => $r['TELEFONO'],
                    "CONTACTO" => $r['CONTACTO'],
                    "CUIT" => $r['CUIT'],
                    "CORREO" => $r['CORREO'],
                    "ID_INV" => $r['ID_INV'],
                    "OBSERVACION" => utf8_decode($r['OBSERVACION']),
                    "CBU" => $r['CBU'],
                    "ID_PROVINCIA" => $r['ID_PROVINCIA'],
                    "ID_DEPARTAMENTO" => $r['ID_DEPARTAMENTO'],
                    "ID_CONDICION_IVA" => $r['ID_CONDICION_IVA'],
                    "ID_CONDICION_IIBB" => $r['ID_CONDICION_IIBB'],
                    "INSCRIPCION_IIBB" => $r['INSCRIPCION_IIBB']
                );
                $this->_dbsql->insert('W_PROVEEDORES', $arra_ins);
            }
        }
    }

    function getobjcliente($cuit) {
        $this->_db->select("c.*, ci.VALOR AS VALOR, ci.CONDICION as CONDICION");
        $this->_db->join("fid_cliente_condicion_iva ci", "ci.ID=c.ID_CONDICION_IVA", 'left');
        $rtn = $this->_db->get_tabla('fid_clientes c', "CUIT = '" . $cuit . "'");
        if ($rtn) {
            $this->_db->update('fid_clientes', array("CU" => '1'), "CUIT = '" . $cuit . "'");
        }
        return $rtn;
    }

    function getNumOpe($id_cliente) {
        $this->_db->select("ID_OPERATORIA");
        $this->_db->order_by("ID_OPERATORIA", "DESC  LIMIT 1");
        $rtn = $this->_db->get_tabla('fid_op_vino_proveedores', "ID_PROVEEDOR= '" . $id_cliente . "'");
        return $rtn;
    }

    function getobj($id_objeto) {
        $this->_db->select("c.CUIT AS CUIT,c.RAZON_SOCIAL AS RAZ,f.*");
        $this->_db->join("fid_clientes c", "c.ID=f.ID_CLIENTE");
        $rtn["factura"] = $this->_db->get_tabla('fid_cu_factura f', "f.ID = '" . $id_objeto . "'");
        $rtn["factura"] = $rtn["factura"][0];
        if ($rtn["factura"]["NUMERO"]) {
            $this->_db->select("CHECK_ESTADO");
            $this->_db->order_by("FECHA", "DESC");
            $array_Check = $this->_db->get_tabla('fid_op_vino_cambio_tit', "ID_FACTURA=" . $rtn["factura"]["NUMERO"]);
            $rtn["CHECK_TITULARIDAD"] = isset($array_Check[0]['CHECK_ESTADO']) ? $array_Check[0]['CHECK_ESTADO'] : 0;
            return $rtn;
        } else {
            $rtn["CHECK_TITULARIDAD"] = 0;
            return $rtn;
        }
    }

    function getoperatoria($id_objeto) {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla('fid_operatoria_vino', "ID_OPERATORIA = '" . $id_objeto . "'");
//         log_this('log/lalalalalalallskaldkasl.log', $this->_db->last_query());
        return $rtn;
    }

    function sendPago1($array_post) {
        if ($array_post['ordenPago1'] == 'Sin Orden') {
            $array_post['ordenPago1'] = '';
        }
        $this->_db->update("fid_cu_factura", array("ID_ESTADO" => $array_post['estFactura']), "NUMERO='" . $array_post['numFactura'] . "'");
        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo1'], "ORDEN_PAGO" => $array_post['ordenPago1']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=1");
    }

    function sendPago2($array_post) {
        if ($array_post['ordenPago1'] == 'Sin Orden') {
            $array_post['ordenPago1'] = '';
        }
        if ($array_post['ordenPago2'] == 'Sin Orden') {
            $array_post['ordenPago2'] = '';
        }
        $this->_db->update("fid_cu_factura", array("ID_ESTADO" => $array_post['estFactura']), "NUMERO='" . $array_post['numFactura'] . "'");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo1'], "ORDEN_PAGO" => $array_post['ordenPago1']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=1");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo2'], "ORDEN_PAGO" => $array_post['ordenPago2']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=2");
    }

    function sendPago3($array_post) {
        if ($array_post['ordenPago1'] == 'Sin Orden') {
            $array_post['ordenPago1'] = '';
        }
        if ($array_post['ordenPago2'] == 'Sin Orden') {
            $array_post['ordenPago2'] = '';
        }
        if ($array_post['ordenPago3'] == 'Sin Orden') {
            $array_post['ordenPago3'] = '';
        }
        $this->_db->update("fid_cu_factura", array("ID_ESTADO" => $array_post['estFactura']), "NUMERO='" . $array_post['numFactura'] . "'");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo1'], "ORDEN_PAGO" => $array_post['ordenPago1']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=1");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo2'], "ORDEN_PAGO" => $array_post['ordenPago2']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=2");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo3'], "ORDEN_PAGO" => $array_post['ordenPago3']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=3");
    }

    function sendPago4($array_post) {
        if ($array_post['ordenPago1'] == 'Sin Orden') {
            $array_post['ordenPago1'] = '';
        }
        if ($array_post['ordenPago2'] == 'Sin Orden') {
            $array_post['ordenPago2'] = '';
        }
        if ($array_post['ordenPago3'] == 'Sin Orden') {
            $array_post['ordenPago3'] = '';
        }
        if ($array_post['ordenPago4'] == 'Sin Orden') {
            $array_post['ordenPago4'] = '';
        }
        $this->_db->update("fid_cu_factura", array("ID_ESTADO" => $array_post['estFactura']), "NUMERO='" . $array_post['numFactura'] . "'");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo1'], "ORDEN_PAGO" => $array_post['ordenPago1']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=1");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo2'], "ORDEN_PAGO" => $array_post['ordenPago2']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=2");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo3'], "ORDEN_PAGO" => $array_post['ordenPago3']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=3");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo4'], "ORDEN_PAGO" => $array_post['ordenPago4']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=4");
    }

    function sendPago5($array_post) {
        if ($array_post['ordenPago1'] == 'Sin Orden') {
            $array_post['ordenPago1'] = '';
        }
        if ($array_post['ordenPago2'] == 'Sin Orden') {
            $array_post['ordenPago2'] = '';
        }
        if ($array_post['ordenPago3'] == 'Sin Orden') {
            $array_post['ordenPago3'] = '';
        }
        if ($array_post['ordenPago4'] == 'Sin Orden') {
            $array_post['ordenPago4'] = '';
        }
        if ($array_post['ordenPago5'] == 'Sin Orden') {
            $array_post['ordenPago5'] = '';
        }
        $this->_db->update("fid_cu_factura", array("ID_ESTADO" => $array_post['estFactura']), "NUMERO='" . $array_post['numFactura'] . "'");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo1'], "ORDEN_PAGO" => $array_post['ordenPago1']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=1");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo2'], "ORDEN_PAGO" => $array_post['ordenPago2']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=2");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo3'], "ORDEN_PAGO" => $array_post['ordenPago3']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=3");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo4'], "ORDEN_PAGO" => $array_post['ordenPago4']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=4");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo5'], "ORDEN_PAGO" => $array_post['ordenPago5']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=5");
    }

    function sendPago6($array_post) {
        if ($array_post['ordenPago1'] == 'Sin Orden') {
            $array_post['ordenPago1'] = '';
        }
        if ($array_post['ordenPago2'] == 'Sin Orden') {
            $array_post['ordenPago2'] = '';
        }
        if ($array_post['ordenPago3'] == 'Sin Orden') {
            $array_post['ordenPago3'] = '';
        }
        if ($array_post['ordenPago4'] == 'Sin Orden') {
            $array_post['ordenPago4'] = '';
        }
        if ($array_post['ordenPago5'] == 'Sin Orden') {
            $array_post['ordenPago5'] = '';
        }
        if ($array_post['ordenPago6'] == 'Sin Orden') {
            $array_post['ordenPago6'] = '';
        }
        $this->_db->update("fid_cu_factura", array("ID_ESTADO" => $array_post['estFactura']), "NUMERO='" . $array_post['numFactura'] . "'");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo1'], "ORDEN_PAGO" => $array_post['ordenPago1']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=1");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo2'], "ORDEN_PAGO" => $array_post['ordenPago2']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=2");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo3'], "ORDEN_PAGO" => $array_post['ordenPago3']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=3");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo4'], "ORDEN_PAGO" => $array_post['ordenPago4']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=4");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo5'], "ORDEN_PAGO" => $array_post['ordenPago5']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=5");

        $this->_db->update("fid_cu_pagos", array("ESTADO_CUOTA" => $array_post['estCuo6'], "ORDEN_PAGO" => $array_post['ordenPago6']), "NUM_FACTURA='" . $array_post['numFactura'] . "' AND NUM_CUOTA=6");
    }

    function getfactura($id_objeto) {
        $this->_db->select("NUMERO");
        $rtn_factura = $this->_db->get_tabla("fid_cu_factura", "ID=" . $id_objeto);
        $rtn_pagos = $this->_db->get_tabla("fid_cu_pagos", "NUM_FACTURA ='" . $rtn_factura[0]['NUMERO'] . "'");

        if ($rtn_pagos) {
            $this->_db->select("f.ID,f.ID_ESTADO,f.NUMERO,f.FORMA_PAGO,c.NUM_CUOTA,c.VALOR_CUOTA,c.ESTADO_CUOTA,f.ORDEN_PAGO");
            $this->_db->join("fid_cu_pagos c", "f.NUMERO=c.NUM_FACTURA");
            $rtn = $this->_db->get_tabla('fid_cu_factura f', "f.ID =" . $id_objeto);
            $rtn_n = array();
            foreach ($rtn as $value) {
                if ($value['ORDEN_PAGO'] == '') {
                    $value['ORDEN_PAGO'] = 'Sin Orden';
                }
                $rtn_n[] = $value;
            }
            return $rtn_n;
        } else {

            $rtn_n = array();
            return $rtn_n;
        }
    }

    function getOperatoriaProveedores($id_objeto) {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla('fid_op_vino_proveedores', "ID_OPERATORIA = '" . $id_objeto . "'");
        return $rtn;
    }

    function getOperatoriaBodegas($id_objeto) {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla('fid_op_vino_bodegas', "ID_OPERATORIA = '" . $id_objeto . "'");
        return $rtn;
    }

    function getProveedoresEdit($id_objeto) {
        $this->_db->select("p.ID_OPERATORIA,c.RAZON_SOCIAL AS RAZON_SOCIAL,p.ID_PROVEEDOR AS ID, p.LIMITE_OPE AS LIMLTRS, p.LIM_OPE_HECT AS MAXHECTAREAS ");
        $this->_db->join("fid_clientes c ", "p.ID_PROVEEDOR=c.ID");
        $rtn = $this->_db->get_tabla('fid_op_vino_proveedores p', "p.ID_OPERATORIA = '" . $id_objeto . "'");
//        log_this('log/updateediiiit.log', $this->_db->last_query());
        return $rtn;
    }

    function getBodegasEdit($id_objeto) {
        $this->_db->select("p.ID_OPERATORIA,e.NOMBRE AS NOMBRE,p.ID_BODEGA AS ID, p.LIMITE_OPE AS LIMLTRS ");
        $this->_db->join("fid_entidades e ", "p.ID_BODEGA=e.ID");
        $rtn = $this->_db->get_tabla('fid_op_vino_bodegas p', "p.ID_OPERATORIA = '" . $id_objeto . "'");
        return $rtn;
    }

    function getProvinciaBodega($id_prov) {
        $this->_db->select("ID,NOMBRE");
        $rtn = $this->_db->get_tabla("fid_provincias", "ID=$id_prov");
        return $rtn;
    }

    function get_provincias() {
        $rtn = $this->_db->get_tabla("fid_provincias");
        return $rtn;
    }

    function vincular_nr($iidc, $iidnr) {
        $resp = $this->_db->update($this->_tablamod, array("ID_OPERACION" => $iidc), "ID='" . $iidnr . "'");
        return $resp;
    }

    function delupload_nota($idnotareq, $ruta) {
        $this->_db->delete("fid_nota_req_adjunto", "ID_NOTA_REQ	='" . $idnotareq . "' AND NOMBRE='" . $ruta . "'");
        return 1;
    }

    function delupload($idope, $ruta) {
        $this->_db->delete("fid_operatoria_adjunto", "ID_OPERATORIA='" . $idope . "' AND NOMBRE='" . $ruta . "'");
        return 1;
    }

    function get_uploads($id) {
        $this->_db->where("ID_OPERATORIA = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_operatoria_adjunto');
        return $rtn;
    }

    function get_fid_entidades($id) {
        $rtn = $this->_db->get_tabla('fid_fideicomiso_entidades', "ID_FIDEICOMISO='" . $id . "'");
        return $rtn;
    }

    function get_operatoria_checklist($idope) {
        $rtn = $this->_db->get_tabla('fid_operatoria_checklist', "ID_OPERATORIA='" . $idope . "'");
        return $rtn;
    }

    function getentidad_select($idp) {
        $rtn = $this->_db->select("ID,NOMBRE");
        $rtn = $this->_db->join("fid_entidades e", "e.id=et.id_entidad");
        $rtn = $this->_db->get_tabla("fid_entidadestipo et", "id_tipo='" . $idp . "'");
        return $rtn;
    }

    function get_usuarios() {
        $rtn = $this->_db->get_tabla("fid_usuarios", "ESTADO='1'");
        return $rtn;
    }

    function sendobjcli($obj) {
        //log_this('log/aaaaa.log',print_r($obj,1));

        $iid = $obj["id"];
        unset($obj["id"]);
        $obj["CU"] = 1;

        if ($iid == 0)://agregar
            $resp = $this->_db->insert('fid_clientes', $obj);
            //log_this('log/aaaaa.log', $this->_db->last_query());
            $acc = "add";
            $id_new = $resp;
        else://editar
            $resp = $this->_db->update('fid_clientes', $obj, "id='" . $iid . "'");
            //log_this('log/aaaaa.log', $this->_db->last_query());
            $acc = "edit";
            $id_new = $iid;
        endif;

        $arr_valor = $this->_db->get_tabla("fid_cliente_condicion_iva", "ID='" . $obj["ID_CONDICION_IVA"] . "'");

        $valor = 0;
        if ($arr_valor) {
            $valor = $arr_valor[0]["VALOR"];
        }

        $rtn = array(
            "accion" => $acc,
            "result" => $resp,
            "valor" => $valor
        );
        return $rtn;
    }

    function sendOperatoria($arr_post) {
        $fecha_creacion = date('Y-m-d');
        $fecha = date('Y-m-j');
        $nueva_limite = strtotime('+1 year', strtotime($fecha));
        $nueva_limite = date('Y-m-j', $nueva_limite);
        $ins_ope = array(
            "ID_OPERATORIA" => $arr_post['nuevoID'],
            "FECHA_CRE" => $fecha_creacion,
            "FECHA_VEN" => $nueva_limite,
            "NOMBRE_OPE" => $arr_post['opeNombre'],
            "DESCRIPCION_OPE" => $arr_post['opeDescripcion'],
            "ID_FIDEICOMISO" => $arr_post['opeFideicomiso'],
            "ID_COORDINADOR_OPE" => $arr_post['opeCoordinador'],
            "ID_JEFE_OPE" => $arr_post['opeJefe'],
            "LTRS_MAX" => $arr_post['listrosMax'],
            "MAX_PESOS" => $arr_post['maxPesos'],
            "CHECKLIST_PERSONA" => implode(',', $arr_post['checklistsPersona']),
            "PRECIO_1" => $arr_post['opePrecio1'],
            "PRECIO_2" => $arr_post['opePrecio2'],
            "PRECIO_3" => $arr_post['opePrecio3'],
            "PRECIO_4" => $arr_post['opePrecio4'],
            "PRECIO_5" => $arr_post['opePrecio5'],
            "PRECIO_6" => $arr_post['opePrecio6'],
//            "FORMA_PAGO" => $arr_post['formaPago'],
            "HECT_MAX" => $arr_post['maxHectareas'],
            "ESTADO_OP" => 1
        );
        if ($this->_db->insert('fid_operatoria_vino', $ins_ope)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function sendCliente($arr_post) {
        $fecha_creacion = date('Y-m-d');
        $fecha = date('Y-m-j');
        $ins_cli = array(
            "RAZON_SOCIAL" => $arr_post['nombre'],
            "FECHA_ALTA" => $fecha_creacion,
            "CUIT" => $arr_post['cuit'],
            "CBU" => $arr_post['cbu'],
            "ID_CONDICION_IVA" => $arr_post['condicioniva'],
            "ID_CONDICION_IIBB" => $arr_post['condicioniibb'],
            "INSCRIPCION_IIBB" => $arr_post['insciibb'],
            "DIRECCION" => $arr_post['direccion'],
            "ID_PROVINCIA" => $arr_post['provincia'],
            "ID_DEPARTAMENTO" => $arr_post['subrubro'],
            "TELEFONO" => $arr_post['telefono'],
            "CORREO" => $arr_post['correo'],
            "OBSERVACION" => $arr_post['observacion']
        );

        $rtn = $this->_db->insert('fid_clientes', $ins_cli);

        return $rtn;
    }

    function updateOperatoria($arr_post) {
        $ins_ope = array(
            "NOMBRE_OPE" => $arr_post['opeNombre'],
            "DESCRIPCION_OPE" => $arr_post['opeDescripcion'],
            "ID_FIDEICOMISO" => $arr_post['opeFideicomiso'],
            "ID_COORDINADOR_OPE" => $arr_post['opeCoordinador'],
            "ID_JEFE_OPE" => $arr_post['opeJefe'],
            "LTRS_MAX" => $arr_post['listrosMax'],
            "MAX_PESOS" => $arr_post['maxPesos'],
            "CHECKLIST_PERSONA" => implode(',', $arr_post['checklistsPersona']),
            "PRECIO_1" => $arr_post['opePrecio1'],
            "PRECIO_2" => $arr_post['opePrecio2'],
            "PRECIO_3" => $arr_post['opePrecio3'],
            "PRECIO_4" => $arr_post['opePrecio4'],
            "PRECIO_5" => $arr_post['opePrecio5'],
            "PRECIO_6" => $arr_post['opePrecio6'],
            "HECT_MAX" => $arr_post['maxHectareas']
        );
        $this->_db->update('fid_operatoria_vino', $ins_ope, "ID_OPERATORIA='" . $arr_post['nuevoID'] . "'");
//        log_this('log/IKKIKIKIKI.log', $this->_db->last_query());
//        if ($this->_db->update('fid_operatoria_vino', $ins_ope, "ID_OPERATORIA='" . $arr_post['nuevoID'] . "'")) {
//            return TRUE;updateProveedores
//        } else {
//            return FALSE;
//        }
    }

    function getIdOperatoria() {
        $this->_db->select("ID_OPERATORIA");
        $this->_db->order_by("ID_OPERATORIA", "DESC LIMIT 1");
        $rtn = $this->_db->get_tabla("fid_operatoria_vino");

        return $rtn ? (int) $rtn[0]['ID_OPERATORIA'] + 1 : 1;
    }

    function sendProveedores($obj, $nuevoID) {
        foreach ($obj as $value) {
            $ins_proveedor = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_PROVEEDOR" => $value['ID'],
                "LIMITE_OPE" => $value['LIMLTRS'],
                "LIM_OPE_HECT" => $value['MAXHECTAREAS']
            );
            $this->_db->insert('fid_op_vino_proveedores', $ins_proveedor);
        }
    }

    function updateProveedores($obj_bod, $obj_prov, $nuevoID) {
        $this->_db->delete("fid_op_vino_proveedores", "ID_OPERATORIA='" . $nuevoID . "'");
        foreach ($obj_prov as $value) {
            $ins_proveedor = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_PROVEEDOR" => $value['ID'],
                "LIMITE_OPE" => $value['LIMLTRS'],
                "LIM_OPE_HECT" => $value['MAXHECTAREAS']
            );
            $this->_db->insert('fid_op_vino_proveedores', $ins_proveedor);
//            log_this('log/UUUUUUU.log', $this->_db->last_query());
        }
        $this->_db->delete("fid_op_vino_bodegas", "ID_OPERATORIA='" . $nuevoID . "'");
        if ($obj_bod && count($obj_bod) > 0) {
            foreach ($obj_bod as $value) {
                $ins_bodegas = array(
                    "ID_OPERATORIA" => $nuevoID,
                    "ID_BODEGA" => $value['ID'],
                    "LIMITE_OPE" => $value['LIMLTRS']
                );
                $this->_db->insert('fid_op_vino_bodegas', $ins_bodegas);
            }
        }
    }

    function sendBodegas($obj, $nuevoID) {
        foreach ($obj as $value) {
            $ins_bodegas = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_BODEGA" => $value['ID'],
                "LIMITE_OPE" => $value['LIMLTRS']
            );
            $this->_db->insert('fid_op_vino_bodegas', $ins_bodegas);
        }
    }

    function sendHumana($obj, $nuevoID) {
        foreach ($obj as $value) {
            $aEntero = 0;
            if ($value['valor'] == 'SI') {
                $aEntero = 2;
            }
            if ($value['valor'] == 'NO') {
                $aEntero = 1;
            }
            $ins_check = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_HUMANA" => $value['numcheck'],
                "ESTADO" => $aEntero
            );
            $this->_db->insert('fid_operatoria_humana', $ins_check);
        }
    }

    function sendJuridica($obj, $nuevoID) {
        foreach ($obj as $value) {
            $aEntero = 0;
            if ($value['valor'] == 'SI') {
                $aEntero = 2;
            }
            if ($value['valor'] == 'NO') {
                $aEntero = 1;
            }
            $ins_check = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_JURIDICA" => $value['numcheck'],
                "ESTADO" => $aEntero
            );
            $this->_db->insert('fid_operatoria_juridica', $ins_check);
        }
    }

//    function sendJuridica($obj, $nuevoID) {//        $array_checklist = explode(",", $obj);//        foreach ($obj as $value) {
//            $ins_check = array(//                "ID_OPERATORIA" => $nuevoID,//                "ID_JURIDICA" => $value,
//            );//            $this->_db->insert('fid_operatoria_juridica', $ins_check);//        }//    }
//    function updateBodegas($obj, $nuevoID) {
//        $this->_db->delete("fid_op_vino_bodegas", "ID_OPERATORIA='" . $nuevoID . "'");
//        if ($obj && count($obj) > 0) {
//            foreach ($obj as $value) {
//                $ins_bodegas = array(
//                    "ID_OPERATORIA" => $nuevoID,
//                    "ID_BODEGA" => $value['ID'],
//                    "LIMITE_OPE" => $value['LIMLTRS']
//                );
//                $this->_db->insert('fid_op_vino_bodegas', $ins_bodegas);
//            }
//        }
//    }

    function sendobj($obj, $cambio_titularidad) {
        $numero_factura = $obj['NUMERO'];
        $iid = $obj["id"];
        $cuit = $obj["CUIT"];
        $cli = $this->_db->get_tabla("fid_clientes", "CUIT='" . $cuit . "'");
        $valor_actual = 0;
        $valor_nuevo = 0;
        $cod_cli = $cli[0]['ID'];
        $obj["ID_CLIENTE"] = $cod_cli;
        $cuit_tmp = $obj["CUIT"];
        unset($obj["id"], $obj["CUIT"], $obj["arr_cius"], $obj["update_cius"]);
        if ($obj['CHECKLIST_PERSONA']) {
            $armando_array = implode(',', $obj['CHECKLIST_PERSONA']);
            $obj['CHECKLIST_PERSONA'] = $armando_array;
        }

        $ins_cuotas1 = array();
        $ins_cuotas2 = array();
        $ins_cuotas3 = array();
        $ins_cuotas4 = array();
        $ins_cuotas5 = array();
        $ins_cuotas6 = array();
        $cuota1 = 0;
        $cuota2 = 0;
        $cuota3 = 0;
        $cuota4 = 0;
        $cuota5 = 0;
        $cuota6 = 0;

        if ($iid == 0) {//agregar
            $resp = $this->_db->insert('fid_cu_factura', $obj);

            $fecha = $obj["FECHA"];

            if ($obj["FORMA_PAGO"] == 1) {
                /*                 * *********************************************************************************************
                 * Forma de pago 1
                 * ********************************************************************************************* */
                $ins_cuotas1['NUM_FACTURA'] = $num_factura;
                $ins_cuotas1['NUM_CUOTA'] = 1;
                $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 2) + (float) $iva;
                $primerVen = 15;
                $otrosVen = 30;
//                if (intval($primerVen) <= 0)return false;
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                for ($i = 1; $habiles < $primerVen; $i++) {
                    $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                        /* Aca se puede agregar un filtrado especifico con dia/mes para feriados. */
                        $habiles++;
                        $ven[$habiles] = $diames;
                    }
                }
                $fecha = date('Ymd', strtotime(end($ven)));
                $ins_cuotas1['FECHA_VEN'] = end($ven);
                $this->_db->insert('fid_cu_pagos', $ins_cuotas1);
            } else if ($obj["FORMA_PAGO"] == 2) {

                /*                 * *********************************************************************************************
                 * Forma de pago 2
                 * ********************************************************************************************* */
                $ins_cuotas1['NUM_FACTURA'] = $num_factura;
                $ins_cuotas1['NUM_CUOTA'] = 1;
                $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 2) + (float) $iva;
                $primerVen = 15;
                $otrosVen = 30;
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                for ($i = 1; $habiles < $primerVen; $i++) {
                    $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                        $habiles++;
                        $ven[$habiles] = $diames;
                    }
                }
                $fecha = date('Ymd', strtotime(end($ven)));
                $ins_cuotas1['FECHA_VEN'] = end($ven);
                $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

                $ins_cuotas2['NUM_FACTURA'] = $num_factura;
                $ins_cuotas2['NUM_CUOTA'] = 2;
                $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 2);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas2['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas2);
            } else if ($obj["FORMA_PAGO"] == 3) {

                /*                 * *********************************************************************************************
                 * Forma de pago 3
                 * ********************************************************************************************* */
                $ins_cuotas1['NUM_FACTURA'] = $num_factura;
                $ins_cuotas1['NUM_CUOTA'] = 1;
                $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 3) + (float) $iva;
                $primerVen = 15;
                $otrosVen = 30;
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                for ($i = 1; $habiles < $primerVen; $i++) {
                    $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                        $habiles++;
                        $ven[$habiles] = $diames;
                    }
                }
                $fecha = date('Ymd', strtotime(end($ven)));
                $ins_cuotas1['FECHA_VEN'] = end($ven);
                $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

                $ins_cuotas2['NUM_FACTURA'] = $num_factura;
                $ins_cuotas2['NUM_CUOTA'] = 2;
                $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 3);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas2['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas2);

                $ins_cuotas3['NUM_FACTURA'] = $num_factura;
                $ins_cuotas3['NUM_CUOTA'] = 3;
                $ins_cuotas3['VALOR_CUOTA'] = ((float) $neto / 3);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas3['FECHA_VEN'] = $fecha;

                $this->_db->insert('fid_cu_pagos', $ins_cuotas3);
            } else if ($obj["FORMA_PAGO"] == 4) {

                /*                 * *********************************************************************************************
                 * Forma de pago 4
                 * ********************************************************************************************* */
                $ins_cuotas1['NUM_FACTURA'] = $num_factura;
                $ins_cuotas1['NUM_CUOTA'] = 1;
                $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 4) + (float) $iva;
                $primerVen = 15;
                $otrosVen = 30;
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                for ($i = 1; $habiles < $primerVen; $i++) {
                    $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                        $habiles++;
                        $ven[$habiles] = $diames;
                    }
                }
                $fecha = date('Ymd', strtotime(end($ven)));
                $ins_cuotas1['FECHA_VEN'] = end($ven);
                $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

                $ins_cuotas2['NUM_FACTURA'] = $num_factura;
                $ins_cuotas2['NUM_CUOTA'] = 2;
                $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 4);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas2['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas2);

                $ins_cuotas3['NUM_FACTURA'] = $num_factura;
                $ins_cuotas3['NUM_CUOTA'] = 3;
                $ins_cuotas3['VALOR_CUOTA'] = ((float) $neto / 4);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas3['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas3);

                $ins_cuotas4['NUM_FACTURA'] = $obj['NUMERO'];
                $ins_cuotas4['NUM_CUOTA'] = 4;
                $ins_cuotas4['VALOR_CUOTA'] = ($obj["NETO"] / 4);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas4['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas4);
            } else if ($obj["FORMA_PAGO"] == 5) {

                /*                 * *********************************************************************************************
                 * Forma de pago 5
                 * ********************************************************************************************* */
                $ins_cuotas1['NUM_FACTURA'] = $num_factura;
                $ins_cuotas1['NUM_CUOTA'] = 1;
                $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 5) + (float) $iva;
                $primerVen = 15;
                $otrosVen = 30;
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                for ($i = 1; $habiles < $primerVen; $i++) {
                    $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                        $habiles++;
                        $ven[$habiles] = $diames;
                    }
                }
                $fecha = date('Ymd', strtotime(end($ven)));
                $ins_cuotas1['FECHA_VEN'] = end($ven);
                $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

                $ins_cuotas2['NUM_FACTURA'] = $num_factura;
                $ins_cuotas2['NUM_CUOTA'] = 2;
                $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 5);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas2['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas2);

                $ins_cuotas3['NUM_FACTURA'] = $num_factura;
                $ins_cuotas3['NUM_CUOTA'] = 3;
                $ins_cuotas3['VALOR_CUOTA'] = ((float) $neto / 5);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }
                $ins_cuotas3['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas3);

                $ins_cuotas4['NUM_FACTURA'] = $obj['NUMERO'];
                $ins_cuotas4['NUM_CUOTA'] = 4;
                $ins_cuotas4['VALOR_CUOTA'] = ($obj["NETO"] / 5);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas4['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas4);

                $ins_cuotas5['NUM_FACTURA'] = $obj['NUMERO'];
                $ins_cuotas5['NUM_CUOTA'] = 5;
                $ins_cuotas5['VALOR_CUOTA'] = ($obj["NETO"] / 5);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }
                $ins_cuotas5['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas5);
            } else if ($obj["FORMA_PAGO"] == 6) {
                /*                 * *********************************************************************************************
                 * Forma de pago 6
                 * ********************************************************************************************* */
                $ins_cuotas1['NUM_FACTURA'] = $num_factura;
                $ins_cuotas1['NUM_CUOTA'] = 1;
                $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 6) + (float) $iva;
                $primerVen = 15;
                $otrosVen = 30;
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                for ($i = 1; $habiles < $primerVen; $i++) {
                    $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                    if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                        $habiles++;
                        $ven[$habiles] = $diames;
                    }
                }
                $fecha = date('Ymd', strtotime(end($ven)));
                $ins_cuotas1['FECHA_VEN'] = end($ven);
                $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

                $ins_cuotas2['NUM_FACTURA'] = $num_factura;
                $ins_cuotas2['NUM_CUOTA'] = 2;
                $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 6);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }
                $ins_cuotas2['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas2);
                $ins_cuotas3['NUM_FACTURA'] = $num_factura;
                $ins_cuotas3['NUM_CUOTA'] = 3;
                $ins_cuotas3['VALOR_CUOTA'] = ((float) $neto / 6);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas3['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas3);

                $ins_cuotas4['NUM_FACTURA'] = $obj['NUMERO'];
                $ins_cuotas4['NUM_CUOTA'] = 4;
                $ins_cuotas4['VALOR_CUOTA'] = ($obj["NETO"] / 6);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas4['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas4);

                $ins_cuotas5['NUM_FACTURA'] = $obj['NUMERO'];
                $ins_cuotas5['NUM_CUOTA'] = 5;
                $ins_cuotas5['VALOR_CUOTA'] = ($obj["NETO"] / 6);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }

                $ins_cuotas5['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas5);

                $ins_cuotas6['NUM_FACTURA'] = $obj['NUMERO'];
                $ins_cuotas6['NUM_CUOTA'] = 6;
                $ins_cuotas6['VALOR_CUOTA'] = ($obj["NETO"] / 6);
                $habiles = 0;
                $selectDias = "";
                $ven = array();
                $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
                $dia = date("w", strtotime($fecha));
                if ($dia == 6) {
                    $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
                }
                if ($dia == 0) {
                    $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
                }
                $ins_cuotas5['FECHA_VEN'] = $fecha;
                $this->_db->insert('fid_cu_pagos', $ins_cuotas6);
            }
            if ($cambio_titularidad == 'true') {
                $arr_cambio_titu = array(
                    "ID_FACTURA" => $numero_factura,
                    "ID_USUARIO" => $_SESSION['USERADM'],
                    "FECHA" => date("Y-m-d H:i:s"),
                    "CHECK_ESTADO" => 1,
                );
                $this->_db->insert('fid_op_vino_cambio_tit', $arr_cambio_titu);
            }

            $acc = "add";
            $id_new = $resp;
        } else {//editar
            $resp = $this->_db->update('fid_cu_factura', $obj, "id='" . $iid . "'");
            $this->_db->select("*");
            $this->_db->order_by("FECHA", "DESC LIMIT 1");
            $titu = $this->_db->get_tabla("fid_op_vino_cambio_tit", "ID_FACTURA=" . $numero_factura);
            if (isset($titu) AND $titu !== '') {
                if ($cambio_titularidad == 'true') {
                    $arr_cambio_titu = array(
                        "ID_FACTURA" => $numero_factura,
                        "ID_USUARIO" => $_SESSION['USERADM'],
                        "FECHA" => date("Y-m-d H:i:s"),
                        "CHECK_ESTADO" => 1);
                    $this->_db->insert('fid_op_vino_cambio_tit', $arr_cambio_titu);
                }
            } else {
                if ($titu[0]['CHECK_ESTADO'] == 0 && $cambio_titularidad == 'true') {//no esta activado y se ha activado
                    $arr_cambio_titu = array(
                        "ID_FACTURA" => $numero_factura,
                        "ID_USUARIO" => $_SESSION['USERADM'],
                        "FECHA" => date("Y-m-d H:i:s"),
                        "CHECK_ESTADO" => 1);
                    $this->_db->insert('fid_op_vino_cambio_tit', $arr_cambio_titu);
                }
            }
            $acc = "edit";
            $id_new = $iid;
        }
        $rtn = array("accion" => $acc, "result" => $id_new);
        //log_this('log/aaaaa.log', $this->_db->last_query());
        return $rtn;
    }

    function crearCuotas($num_factura, $cant_cu, $neto, $iva, $fecha) {
        if ($cant_cu == 1) {
            $ins_cuotas1['NUM_FACTURA'] = $num_factura;
            $cuota1 = (float) $neto + (float) $iva;
            $ins_cuotas1['NUM_CUOTA'] = 1;
            $ins_cuotas1['VALOR_CUOTA'] = $cuota1;

            $primerVen = 15;
//            if (intval($primerVen) <= 0)return false;
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            for ($i = 1; $habiles < $primerVen; $i++) {
                $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                    /* Aca se puede agregar un filtrado especifico con dia/mes para feriados. */
                    $habiles++;
                    $ven[$habiles] = $diames;
                }
            }

            $ins_cuotas1['FECHA_VEN'] = end($ven);

            $this->_db->insert('fid_cu_pagos', $ins_cuotas1);
        } else if ($cant_cu == 2) {
            $ins_cuotas1['NUM_FACTURA'] = $num_factura;
            $ins_cuotas1['NUM_CUOTA'] = 1;
            $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 2) + (float) $iva;
            $primerVen = 15;
            $otrosVen = 30;
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            for ($i = 1; $habiles < $primerVen; $i++) {
                $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                    $habiles++;
                    $ven[$habiles] = $diames;
                }
            }
            $fecha = date('Ymd', strtotime(end($ven)));
            $ins_cuotas1['FECHA_VEN'] = end($ven);
            $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

            $ins_cuotas2['NUM_FACTURA'] = $num_factura;
            $ins_cuotas2['NUM_CUOTA'] = 2;
            $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 2);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas2['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas2);
        } else if ($cant_cu == 3) {


            $ins_cuotas1['NUM_FACTURA'] = $num_factura;
            $ins_cuotas1['NUM_CUOTA'] = 1;
            $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 3) + (float) $iva;
            $primerVen = 15;
            $otrosVen = 30;
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            for ($i = 1; $habiles < $primerVen; $i++) {
                $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                    $habiles++;
                    $ven[$habiles] = $diames;
                }
            }
            $fecha = date('Ymd', strtotime(end($ven)));
            $ins_cuotas1['FECHA_VEN'] = end($ven);
            $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

            $ins_cuotas2['NUM_FACTURA'] = $num_factura;
            $ins_cuotas2['NUM_CUOTA'] = 2;
            $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 3);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }

            $ins_cuotas2['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas2);

            $ins_cuotas3['NUM_FACTURA'] = $num_factura;
            $ins_cuotas3['NUM_CUOTA'] = 3;
            $ins_cuotas3['VALOR_CUOTA'] = ((float) $neto / 3);
            $habiles = 0;
            $selectDias = "";
            $ven = array();

            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }

            $ins_cuotas3['FECHA_VEN'] = $fecha;

            $this->_db->insert('fid_cu_pagos', $ins_cuotas3);
        } else if ($cant_cu == 4) {
            $ins_cuotas1['NUM_FACTURA'] = $num_factura;
            $ins_cuotas1['NUM_CUOTA'] = 1;
            $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 4) + (float) $iva;
            $primerVen = 15;
            $otrosVen = 30;
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            for ($i = 1; $habiles < $primerVen; $i++) {
                $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                    $habiles++;
                    $ven[$habiles] = $diames;
                }
            }
            $fecha = date('Ymd', strtotime(end($ven)));
            $ins_cuotas1['FECHA_VEN'] = end($ven);
            $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

            $ins_cuotas2['NUM_FACTURA'] = $num_factura;
            $ins_cuotas2['NUM_CUOTA'] = 2;
            $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 4);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas2['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas2);

            $ins_cuotas3['NUM_FACTURA'] = $num_factura;
            $ins_cuotas3['NUM_CUOTA'] = 3;
            $ins_cuotas3['VALOR_CUOTA'] = ((float) $neto / 4);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }

            $ins_cuotas3['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas3);

            $ins_cuotas4['NUM_FACTURA'] = $num_factura;
            $ins_cuotas4['NUM_CUOTA'] = 4;
            $ins_cuotas4['VALOR_CUOTA'] = ((float) $neto / 4);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas4['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas4);
        } else if ($cant_cu == 5) {

            $ins_cuotas1['NUM_CUOTA'] = 1;
            $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 5) + (float) $iva;
            $primerVen = 15;
            $otrosVen = 30;
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            for ($i = 1; $habiles < $primerVen; $i++) {
                $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                    $habiles++;
                    $ven[$habiles] = $diames;
                }
            }
            $fecha = date('Ymd', strtotime(end($ven)));
            $ins_cuotas1['FECHA_VEN'] = end($ven);
            $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

            $ins_cuotas2['NUM_FACTURA'] = $num_factura;
            $ins_cuotas2['NUM_CUOTA'] = 2;
            $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 5);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas2['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas2);

            $ins_cuotas3['NUM_FACTURA'] = $num_factura;
            $ins_cuotas3['NUM_CUOTA'] = 3;
            $ins_cuotas3['VALOR_CUOTA'] = ((float) $neto / 5);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas3['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas3);

            $ins_cuotas4['NUM_FACTURA'] = $num_factura;
            $ins_cuotas4['NUM_CUOTA'] = 4;
            $ins_cuotas4['VALOR_CUOTA'] = ((float) $neto / 5);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas4['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas4);

            $ins_cuotas5['NUM_FACTURA'] = $num_factura;
            $ins_cuotas5['NUM_CUOTA'] = 5;
            $ins_cuotas5['VALOR_CUOTA'] = ((float) $neto / 5);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas5['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas5);
        } else if ($cant_cu == 6) {

            $ins_cuotas1['NUM_CUOTA'] = 1;
            $ins_cuotas1['VALOR_CUOTA'] = ((float) $neto / 6) + (float) $iva;
            $primerVen = 15;
            $otrosVen = 30;
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            for ($i = 1; $habiles < $primerVen; $i++) {
                $date = date('N', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                $diames = date('Y-m-d', mktime(0, 0, 0, date('n', strtotime($fecha)), date('d', strtotime($fecha)) + $i, date('Y', strtotime($fecha))));
                if ($date != 6 && $date != 7) { // ME FIJO QUE NO SEA SABADO O DOMINGO
                    /* Aca se puede agregar un filtrado especifico con dia/mes para feriados. */
                    $habiles++;
                    $ven[$habiles] = $diames;
                }
            }
            $fecha = date('Ymd', strtotime(end($ven)));
            $ins_cuotas1['FECHA_VEN'] = end($ven);
            $this->_db->insert('fid_cu_pagos', $ins_cuotas1);

            $ins_cuotas2['NUM_FACTURA'] = $num_factura;
            $ins_cuotas2['NUM_CUOTA'] = 2;
            $ins_cuotas2['VALOR_CUOTA'] = ((float) $neto / 6);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas2['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas2);

            $ins_cuotas3['NUM_FACTURA'] = $num_factura;
            $ins_cuotas3['NUM_CUOTA'] = 3;
            $ins_cuotas3['VALOR_CUOTA'] = ((float) $neto / 6);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas3['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas3);

            $ins_cuotas4['NUM_FACTURA'] = $num_factura;
            $ins_cuotas4['NUM_CUOTA'] = 4;
            $ins_cuotas4['VALOR_CUOTA'] = ((float) $neto / 6);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }
            $ins_cuotas4['FECHA_VEN'] = $fecha;

            $this->_db->insert('fid_cu_pagos', $ins_cuotas4);

            $ins_cuotas5['NUM_FACTURA'] = $num_factura;
            $ins_cuotas5['NUM_CUOTA'] = 5;
            $ins_cuotas5['VALOR_CUOTA'] = ((float) $neto / 6);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }

            $ins_cuotas5['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas5);

            $ins_cuotas6['NUM_FACTURA'] = $num_factura;
            $ins_cuotas6['NUM_CUOTA'] = 6;
            $ins_cuotas6['VALOR_CUOTA'] = ((float) $neto / 6);
            $habiles = 0;
            $selectDias = "";
            $ven = array();
            $fecha = date('Ymd', strtotime('+1 month', strtotime($fecha)));
            $dia = date("w", strtotime($fecha));
            if ($dia == 6) {
                $fecha = date('Ymd', strtotime('+2 days', strtotime($fecha)));
            }
            if ($dia == 0) {
                $fecha = date('Ymd', strtotime('+1 days', strtotime($fecha)));
            }

            $ins_cuotas6['FECHA_VEN'] = $fecha;
            $this->_db->insert('fid_cu_pagos', $ins_cuotas6);
        }
    }

    function getTitularidad($id) {
        $this->_db->select("t.ID_FACTURA,u.NOMBRE,t.FECHA,t.CHECK_ESTADO");
        $this->_db->join("fid_usuarios u", "t.ID_USUARIO=u.ID");
//        $this->_db->order_by("t.FECHA", "DESC");
        $this->_db->where("t.ID_FACTURA=" . $id);
        $rtn = $this->_db->get_tabla("fid_op_vino_cambio_tit t");
        return $rtn;
    }

    function delobj($id) {
        $this->_db->delete($this->_tablamod, "id =' " . $id . "'");
        //borrar adjunto
        //borrar fisico
        $lst_uploads = $this->get_arruploads($id);
        if ($lst_uploads) {
            foreach ($lst_uploads as $rsu) {
                unlink($rsu["NOMBRE"]);
            }
        }
        $this->_db->delete('fid_nota_req_adjunto', "ID_NOTA_REQ =' " . $id . "'");
        $this->_db->delete('fid_traza', "NOTA=' " . $id . "'");
    }

    function get_req($id) {
        $this->_db->where("ID = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_nota_req');
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function get_arruploads($id) {
        $this->_db->select("NOMBRE");
        $rtn = $this->_db->get_tabla("fid_nota_req_adjunto", "ID_NOTA_REQ='" . $id . "'");
        return $rtn;
    }

    function get_condicioniva() {
        $rtn = $this->_db->select("ID,CONDICION");
        $rtn = $this->_db->get_tabla("fid_cliente_condicion_iva");
        return $rtn;
    }

    function get_condicioniibb() {
        $rtn = $this->_db->select("ID,CONDICION");
        $rtn = $this->_db->get_tabla(" fid_cliente_condicion_iibb");
        return $rtn;
    }

    function gettipobeneficiario() {
        $rtn = $this->_db->select("ID,TIPO");
        $rtn = $this->_db->get_tabla("fid_cliente_tipo");
        return $rtn;
    }

    function get_info_grid() {
        $this->_db->select("ot.TIPO as OTTIPO, o.*");
        $this->_db->join("fid_operacion_tipo ot", "ot.ID=o.ID_TIPO_OPERATORIA");
        $rtn = $this->_db->get_tabla("fid_operatorias o");
        return $rtn;
    }

    function get_tipos_entidades($where = "") {
        $this->_db->select("ID,NOMBRE");
        $rtn = $this->_db->get_tabla("fid_entidades_tipos", $where);
        return $rtn;
    }

    function prev_consulta($cols) {
        $tb = $this->_tablamod;
        $this->_db->select("$tb.ID, $tb.RAZON_SOCIAL, $tb.CONTACTO, $tb.ID as opciones");
    }

    function getlocalidad($idp) {
        $rtn = $this->_db->get_tabla("fid_localidades", "ID_PROVINCIA='" . $idp . "'");

        return $rtn;
    }

    function update_tipos_entidades($id, $nombre) {
        $arr_edit = array(
            "ID" => $id,
            "NOMBRE" => $nombre
        );

        $rtn = $this->_db->update("fid_entidades_tipos", $arr_edit, "ID='" . $id . "'");
        return $rtn;
    }

    function delete_tipos_entidades($id) {
        $rtn = $this->_db->delete("fid_entidades_tipos", "ID='" . $id . "'");
        return $rtn;
    }

    function add_tipos_entidades() {
        $arr_ins = array(
            "NOMBRE" => 'Nuevo Registro',
            "ESTADO" => '1',
        );
        $id = $this->_db->insert("fid_entidades_tipos", $arr_ins);

        $this->_db->select('ID,NOMBRE');
        $rtn = $this->_db->get_tabla("fid_entidades_tipos", 'ID=' . $id);
        return $rtn;
    }

    function marcar_respondida($iid, $idope, $remitente) {
        $fecha_actual = date("Y-m-d H:i:s");
        $arr_mod = array(
            "ESTADO" => "3"
        );
        $rtn = $this->_db->update("fid_nota_req", $arr_mod, "ID='" . $iid . "'");

        // insertar traza
        //traza inicial
        $arr_traza = array(
            "ID_OPERACION" => $idope,
            "ESTADO" => 7, // estado respondido
            "CARTERADE" => $_SESSION["USERADM"],
            "DESTINO" => $remitente,
            "OBSERVACION" => 'RESPUESTA',
            "DESCRIPCION" => 'RESPUESTA A REQUERIMIENTO',
            "ETAPA" => '0',
            "FECHA" => $fecha_actual,
            "ACTIVO" => '0',
            "ETAPA_ORIGEN" => 0,
            "NOTIF" => '1',
        );
        $this->_db->insert('fid_traza', $arr_traza);


        return $rtn;
    }

    function get_uploads_notas($id) {
        $this->_db->where("ID_NOTA_REQ = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_nota_req_adjunto');
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function sendnota($obj, $adjuntos) {
        //log_this('zzzzz00.log',print_r($obj,1));
        $fecha_actual = date("Y-m-d H:i:s");
        $obj['FCREA'] = $fecha_actual;
        $obj['PROPIETARIO'] = $_SESSION["USERADM"];
        $iid = $obj["idreqh"];
        unset($obj["idreqh"]);

        //$fecha_actual = date("Y-m-d H:i:s");
        //$fecha_actual = date("Y-m-d H:i:s",  strtotime($obj['FCREA']));
        $id_new = $iid;
        if ($iid == 0) { //agregar
            $obj['FOJAS'] = "";
            $obj['TIPO'] = "1";

            $resp = $this->_db->insert('fid_nota_req', $obj);
            $id_new = $resp;
            $obj['ID'] = $id_new;
            $acc = "add";
        } else {
            $resp = $this->_db->update('fid_nota_req', $obj, "ID='" . $iid . "'");
            $acc = "edit";
            //estado
        }

        //si el usuario actual es el cordinador, pedir autorizacion de
        //log_this('zzzzz11.log',print_r($obj,1));

        if ($adjuntos):
            foreach ($adjuntos as $key => $value):
                if (isset($value['nombre_tmp'])):
                    //obtener la semilla
                    $sem = isset($value['nombre_tmp']) ? $value['nombre_tmp'] : "";
                    //consultamos la semilla de la tabla fid_upload_etiqueta,
                    $etiq = $this->_db->get_tabla('fid_upload_etiqueta', "SEMILLA='" . $sem . "'");
                    $etiketa = "";
                    if ($etiq):
                        $etiketa = $etiq[0]["ETIQUETA"];
                    endif;

                    $arr_ins = array(
                        "ID_NOTA_REQ" => $id_new,
                        "NOMBRE" => PATH_REQUERIMIENTOS . $id_new . "/" . $value['nombre'],
                        "ID_USUARIO" => $_SESSION["USERADM"],
                        "DESCRIPCION" => $etiketa,
                        "CREATEDON" => "[NOW()]"
                    );
                    $this->_db->insert('fid_nota_req_adjunto', $arr_ins);
                    //borrar etiketa
                    $this->_db->delete('fid_upload_etiqueta', "SEMILLA='" . $sem . "'");

                    //mover archivo
                    $origen = isset($value['nombre_tmp']) ? $value['nombre_tmp'] : "";
                    $destino = PATH_REQUERIMIENTOS . $id_new . "/" . $value['nombre'];

                    if ($origen):
                        mover($origen, $destino);
                    endif;

                endif;
            endforeach;
        endif;

        $rtn = array(
            "accion" => $acc,
            "result" => $obj
        );
        return $rtn;
    }

    function guardar_etiqueta($semilla, $etiqueta) {
        $rtn = $this->_db->insert('fid_upload_etiqueta', array("SEMILLA" => $semilla, "ETIQUETA" => $etiqueta));
        return $rtn;
    }

    function verificarcbu($cbu) {
        $rtn = $this->_db->get_tabla('fid_clientes', "CBU=" . $cbu);
        return $rtn;
    }

    function verificarCuotas($num_factura) {
        $rtn = $this->_db->get_tabla("fid_cu_pagos", "NUM_FACTURA='" . $num_factura . "'");
        if (count($rtn) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function verificarnumfactura($numero) {
        $rtn = $this->_db->get_tabla("fid_cu_factura", "NUMERO=" . $numero);
        if (count($rtn) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function verificarciu($nciu) {
        $rtn = $this->_db->get_tabla("fid_cu_ciu", "NUMERO='" . $nciu . "'");
        if (count($rtn) > 1) {
            return 1;
        } else {
            return 0;
        }
    }

    function verificar_cbu($cbu) {

        if (trim($cbu) == '') {
            return array("result" => true, "error" => false);
        }

        $rtn = $this->_db->get_tabla("fid_clientes c", "CBU='" . $cbu . "' group by cuit");
        if (count($rtn) > 1) {
            $result = "CBU Duplicado: cliente(";
            foreach ($rtn as $cliente) {
                $result .= $cliente['RAZON_SOCIAL'] . ", ";
            }
            $result = trim(trim($result), ",") . ")";

            return array("result" => $result, "error" => true);
        } else {
            $result = "CBU Erroneo: Longitud diferente a 22";
            if (count($rtn) == 1 && strlen($rtn[0]['CBU']) != 22)
                return array("result" => $result, "error" => true);
            else
                return array("result" => true, "error" => false);
        }
    }

    function get_bodegas() {
        /*
          select l.LOCALIDAD,b.ID as ID, b.NOMBRE AS NOMBRE
          from fid_bodegas b
          inner join fid_localidades l on l.ID=b.ID_DEPARTAMENTO
         */

        $this->_db->select("l.LOCALIDAD as LOCAL,b.ID as ID, b.NOMBRE AS NOMBRE");
        $this->_db->join("fid_localidades l", "l.ID=b.ID_DEPARTAMENTO");
        $rtn = $this->_db->get_tabla("fid_bodegas b");
        return $rtn;
    }

    function getChecklistOp($id) {
        $this->_db->select("CHECKLIST_PERSONA");
        $rtn = $this->_db->get_tabla("fid_operatoria_vino", "ID_OPERATORIA=$id");
        return $rtn;
    }

    function getChecklistHumana() {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla("fid_op_vino_checklist", "JURIDICA=0 AND ESTADO=1");
        return $rtn;
    }

    function getChecklistHumanaFact($ids) {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla("fid_op_vino_checklist", "JURIDICA=0 AND ESTADO=1 AND ID IN ($ids)");
        return $rtn;
    }

    function getChecklistJuridica() {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla("fid_op_vino_checklist", "JURIDICA=1 AND ESTADO=1");
        return $rtn;
    }

    function getChecklistJuridicaFact($ids) {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla("fid_op_vino_checklist", "JURIDICA=1 AND ESTADO=1 AND ID IN ($ids)");
        return $rtn;
    }

    function getbodegas_vino($id_operatoria) {
        $this->_db->select("e.ID, e.NOMBRE,b.ID_OPERATORIA,b.LIMITE_OPE,p.PROVINCIA");
        $this->_db->join("fid_op_vino_bodegas b", "e.ID=b.ID_BODEGA");
        $this->_db->join("fid_provincias p", "e.ID_PROVINCIA=p.ID");
        $rtn = $this->_db->get_tabla("fid_entidades e", "b.ID_OPERATORIA=$id_operatoria");
        return $rtn;
    }

    function get_ope_bodegas() {
        $this->_db->select("e.ID AS ID,e.NOMBRE AS NOMBRE");
        $this->_db->join("fid_entidadestipo et", "e.ID=et.ID_ENTIDAD");
        $this->_db->join("fid_entidades_tipos ets", "ets.ID=et.ID_TIPO");
        $rtn = $this->_db->get_tabla("fid_entidades e", "ets.ID = (SELECT valor FROM fid_settings WHERE variable='compra_uva_id_tipo_entidad')");
        return $rtn;
    }

    function get_coordinadores() {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla("fid_usuarios");
        return $rtn;
    }

    function get_jefes() {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla("fid_usuarios");
        return $rtn;
    }

    function get_proveedores() {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla("fid_clientes");
        return $rtn;
    }

    function getDatoProveedor($ids_proveedores, $firstColumnData) {

        if (count($ids_proveedores) > count($firstColumnData)) {
            $array_resultado = array();
            $i = 0;
            foreach ($ids_proveedores as $nuevos) {
                $existe = 0;
                foreach ($firstColumnData as $actuales) {
                    if ($nuevos == $actuales) {
                        $existe = 1;
                    }
                }
                if ($existe == 0) {
                    $array_resultado[$i] = $nuevos;
                }
                $i++;
            }
            $prov_ids = "";
            foreach ($array_resultado as $value) {
                $prov_ids .= $value . ",";
            }
            $prov_ids = substr($prov_ids, 0, -1);
            $this->_db->select("ID,RAZON_SOCIAL");
            $rtn = $this->_db->get_tabla("fid_clientes", "ID IN (" . $prov_ids . ")");
            $n_rtn = array();
            $j = 0;
            foreach ($rtn as $value) {
                $n_rtn[$j]['ID'] = $value['ID'];
                $n_rtn[$j]['RAZON_SOCIAL'] = $value['RAZON_SOCIAL'];
                $n_rtn[$j]['ACCION'] = 'AGREGAR';
                $j++;
            }
            return $n_rtn;
        } else if (count($ids_proveedores) < count($firstColumnData)) {
            $array_resultado = array();
            $i = 0;
            foreach ($firstColumnData as $actuales) {
                $existe = 0;
                foreach ($ids_proveedores as $nuevos) {

                    if ($actuales == $nuevos) {
                        $existe = 1;
                    }
                }
                if ($existe == 0) {
                    $array_resultado[$i] = $actuales;
                }
                $i++;
            }
            $prov_ids = "";
            foreach ($array_resultado as $value) {
                $prov_ids .= $value . ",";
            }
            $prov_ids = substr($prov_ids, 0, -1);
            $this->_db->select("ID,RAZON_SOCIAL");
            $rtn = $this->_db->get_tabla("fid_clientes", "ID IN (" . $prov_ids . ")");
            $j = 0;
            foreach ($rtn as $value) {
                $n_rtn[$j]['ID'] = $value['ID'];
                $n_rtn[$j]['RAZON_SOCIAL'] = $value['RAZON_SOCIAL'];
                $n_rtn[$j]['ACCION'] = 'ELIMINAR';
                $j++;
            }
            return $n_rtn;
        } else if (count($firstColumnData) > 0 && $ids_proveedores == 'null') {
            $n_rtn[0]['ID'] = $firstColumnData[0];
            $n_rtn[0]['RAZON_SOCIAL'] = '';
            $n_rtn[0]['ACCION'] = 'ELIMINAR';
            return $n_rtn;
        }
    }

    function getDatoProveedorNuevo($ids_proveedores) {
        $array_resultado = array();
        $i = 0;
        foreach ($ids_proveedores as $nuevos) {

            $array_resultado[$i] = $nuevos;
            $i++;
        }
        $prov_ids = "";
        foreach ($array_resultado as $value) {
            $prov_ids .= $value . ",";
        }
        $prov_ids = substr($prov_ids, 0, -1);
        $this->_db->select("ID,RAZON_SOCIAL");
        $rtn = $this->_db->get_tabla("fid_clientes", "ID IN (" . $prov_ids . ")");
        $n_rtn = array();
        $j = 0;
        foreach ($rtn as $value) {
            $n_rtn[$j]['ID'] = $value['ID'];
            $n_rtn[$j]['RAZON_SOCIAL'] = $value['RAZON_SOCIAL'];
            $n_rtn[$j]['ACCION'] = 'AGREGAR';
            $j++;
        }
        return $n_rtn;
    }

    function getDatoBodega($ids_bodegas, $firstColumnData) {
        if (count($ids_bodegas) > count($firstColumnData)) {
            $array_resultado = array();
            $i = 0;
            foreach ($ids_bodegas as $nuevos) {
                $existe = 0;
                foreach ($firstColumnData as $actuales) {
                    if ($nuevos == $actuales) {
                        $existe = 1;
                    }
                }
                if ($existe == 0) {
                    $array_resultado[$i] = $nuevos;
                }
                $i++;
            }
            $bod_ids = "";
            foreach ($array_resultado as $value) {
                $bod_ids .= $value . ",";
            }
            $bod_ids = substr($bod_ids, 0, -1);
            $this->_db->select("ID,NOMBRE");
            $rtn = $this->_db->get_tabla("fid_entidades", "ID IN (" . $bod_ids . ")");
            $n_rtn = array();
            $j = 0;
            foreach ($rtn as $value) {
                $n_rtn[$j]['ID'] = $value['ID'];
                $n_rtn[$j]['NOMBRE'] = $value['NOMBRE'];
                $n_rtn[$j]['ACCION'] = 'AGREGAR';
                $j++;
            }
            return $n_rtn;
        } else if (count($ids_bodegas) < count($firstColumnData)) {
            $array_resultado = array();
            $i = 0;
            foreach ($firstColumnData as $actuales) {
                $existe = 0;
                foreach ($ids_bodegas as $nuevos) {

                    if ($actuales == $nuevos) {
                        $existe = 1;
                    }
                }
                if ($existe == 0) {
                    $array_resultado[$i] = $actuales;
                }
                $i++;
            }
            $bod_ids = "";
            foreach ($array_resultado as $value) {
                $bod_ids .= $value . ",";
            }
            $bod_ids = substr($bod_ids, 0, -1);
            $this->_db->select("ID,NOMBRE");
            $rtn = $this->_db->get_tabla("fid_entidades", "ID IN (" . $bod_ids . ")");
            $j = 0;
            foreach ($rtn as $value) {
                $n_rtn[$j]['ID'] = $value['ID'];
                $n_rtn[$j]['NOMBRE'] = $value['NOMBRE'];
                $n_rtn[$j]['ACCION'] = 'ELIMINAR';
                $j++;
            }
            return $n_rtn;
        } else if (count($firstColumnData) > 0 && $ids_bodegas == 'null') {
            $n_rtn[0]['ID'] = $firstColumnData[0];
            $n_rtn[0]['NOMBRE'] = '';
            $n_rtn[0]['ACCION'] = 'ELIMINAR';
            return $n_rtn;
        }
    }

    function getDatoBodegaNueva($ids_bodegas) {
        $array_resultado = array();
        $i = 0;
        foreach ($ids_bodegas as $nuevos) {
            $array_resultado[$i] = $nuevos;
            $i++;
        }

        $bod_ids = "";
        foreach ($array_resultado as $value) {
            $bod_ids .= $value . ",";
        }
        $bod_ids = substr($bod_ids, 0, -1);
        $this->_db->select("ID,NOMBRE");
        $rtn = $this->_db->get_tabla("fid_entidades", "ID IN (" . $bod_ids . ")");
        $n_rtn = array();
        $j = 0;
        foreach ($rtn as $value) {
            $n_rtn[$j]['ID'] = $value['ID'];
            $n_rtn[$j]['NOMBRE'] = $value['NOMBRE'];
            $n_rtn[$j]['ACCION'] = 'AGREGAR';
            $j++;
        }
        return $n_rtn;
    }

    function getPagos($id_pagos) {
        $this->_db->select("PRECIO_1,PRECIO_2,PRECIO_3,PRECIO_4,PRECIO_5,PRECIO_6");
        $rtn = $this->_db->get_tabla("fid_operatoria_vino", "ID_OPERATORIA=$id_pagos");
        $n_rtn = array();

        $j = 0;
        foreach ($rtn as $value) {
            if ($value['PRECIO_1'] != 0) {
                $n_rtn[$j]['PRECIO_1'] = $value['PRECIO_1'];
            }
            if ($value['PRECIO_2'] != 0) {
                $n_rtn[$j]['PRECIO_2'] = $value['PRECIO_2'];
            }
            if ($value['PRECIO_3'] != 0) {
                $n_rtn[$j]['PRECIO_3'] = $value['PRECIO_3'];
            }
            if ($value['PRECIO_4'] != 0) {
                $n_rtn[$j]['PRECIO_4'] = $value['PRECIO_4'];
            }
            if ($value['PRECIO_5'] != 0) {
                $n_rtn[$j]['PRECIO_5'] = $value['PRECIO_5'];
            }
            if ($value['PRECIO_6'] != 0) {
                $n_rtn[$j]['PRECIO_6'] = $value['PRECIO_6'];
            }
            $j++;
        }
        return $n_rtn;
    }

//    function getformulassql(){
//        $this->_dbsql->order_by('idFormula');
//        $rtn = $this->_dbsql->get_tabla('pcobypag_pagos');
//        return $rtn;
//    }

    function getenviar_a1($arr_area, $puesto_in) {
        //$rtn = $this->_db->get_tabla("fid_xareas ", "ID NOT IN('11','12')");
        $rtn = $this->_db->get_tabla("fid_xareas ", "ID NOT IN('12')");
        return $rtn;
    }

    function getvincular($idusu) {
        $rtn = $this->_db->get_tabla("fid_operaciones", "CARTERADE='" . $idusu . "'");
        return $rtn;
    }

    function getenviar_a2($arr_send, $puesto_in) {

        $cad_where = "";
        $sw = 0;
        if (is_array($arr_send)) {
            foreach ($arr_send as $send) {
                if (is_array($send)):
                    $a = $send["area"];
                    $p = $send["puesto"];
                    $cad_where = "u.ID_AREA='" . $a . "' and u.ID_PUESTO='" . $p . "'";
                    break;
                endif;
            }
        }
        else if (is_numeric($arr_send)) {
            $sw = 1;
            $cad_where .= $arr_send . ",";
        }

        if (strlen($cad_where) > 0 and $sw == 1) {
            $cad_where = substr($cad_where, 0, -1);
            $cad_where = "u.ID_AREA IN (" . $cad_where . ")";
        }

        if ($puesto_in) {
            $cad_where .= "and p.ID='" . $puesto_in . "'";
        }

        $this->_db->select("u.ID as IID,NOMBRE,APELLIDO,a.DENOMINACION AS AREA, p.DENOMINACION AS PUESTO, a.ETAPA AS ETAPA, u.ID_PUESTO AS PUESTOID");
        $this->_db->join("fid_xpuestos p", "p.ID=u.ID_PUESTO");
        $this->_db->join("fid_xareas a", "a.ID=u.ID_AREA");
        $this->_db->order_by("AREA,PUESTO");
        $rtn = $this->_db->get_tabla("fid_usuarios u", $cad_where);
        //log_this('xxxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function importar_xls($fid_sanjuan, $ope_sanjuan, $id_op_vino) {
        if (!is_file("_tmp/importar/imp_vino_fact.xlsx")) {
            return -1;
        }
        set_time_limit(0);
        require_once ('general/helper/ClassesPHPExcel/PHPExcel.php');
        require_once ("general/helper/ClassesPHPExcel/PHPExcel/Reader/Excel2007.php");

        $objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load("_tmp/importar/imp_vino_fact.xlsx");
        $objPHPExcel->setActiveSheetIndex(0);

        $i = 2;
        $res = array();

        $k = 0;

        $id_tipoentidad_bodega = $this->_db->get_tabla('fid_settings', "variable='compra_uva_id_tipo_entidad'");
        if ($id_tipoentidad_bodega) {
            $id_tipoentidad_bodega = $id_tipoentidad_bodega[0]['valor'];
        } else {
            die("Falta configurar sistema");
        }
        /*
          $id_operatoria = $this->getIdOperatoria();

          $arr_ope = array(
          'ID_OPERATORIA' => $id_operatoria,
          'FECHA_CRE' => date('Y-m-d'),
          'NOMBRE_OPE' => 'Operatoria importada #' . $id_operatoria
          );

          $this->_db->insert('fid_operatoria_vino', $arr_ope); */
        $id_operatoria = $id_op_vino;

        $arr_proveedores = $arr_bodegas = array();
        $total_litros = 0;
        $precios_cuotas = array();

        while ($objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue() != '') {

            $iid = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
            $cuit = $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();

            //$cbu = substr($objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue(), 0, 22);
            $correo = $objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue();
            $razonsocial = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();

            //$direccion = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
            $telefono = $objPHPExcel->getActiveSheet()->getCell("K" . $i)->getValue();

            $observacion = $objPHPExcel->getActiveSheet()->getCell("T" . $i)->getValue();
            //$id_provincia = $objPHPExcel->getActiveSheet()->getCell("M" . $i)->getValue();
            //$id_departamento = $objPHPExcel->getActiveSheet()->getCell("N" . $i)->getValue();

            $id_condicion_iva = $objPHPExcel->getActiveSheet()->getCell("Z" . $i)->getValue();
            $id_condicion_iibb = $objPHPExcel->getActiveSheet()->getCell("AA" . $i)->getValue();
            //$inscripcion_iibb   = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();

            $litros = floatval($objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue());
            if (!$litros) {
                $litros = floatval($objPHPExcel->getActiveSheet()->getCell("E" . $i)->getCalculatedValue());
            }
            $cuit = str_replace('-', '', strval($cuit));
            //$cbu = exp_to_dec($cbu);
            $existecli = $this->_db->get_tabla('fid_clientes', 'CUIT="' . $cuit . '"');

            $estado_fact = $objPHPExcel->getActiveSheet()->getCell("AE" . $i)->getValue();

            switch ($estado_fact) {
                case 'DESISTIDO':
                    $estado_fact = 2;
                    break;
                case 'DESISTIDO':
                    $estado_fact = 2;
                    break;
                case 'RECHAZADO':
                    $estado_fact = 3;
                    break;
                case 'APROBADO':
                case 'APROBADA':
                    $estado_fact = 1;
                    break;
                default:
                    $estado_fact = 1;
                    break;
            }

            if ($existecli) {
                $id_cliente = $existecli[0]["ID"];
                $id_condicion_iva = $existecli[0]["ID_CONDICION_IVA"];
            } else {
                if ($id_condicion_iva && $condicion_iva = $this->_db->get_tabla('fid_cliente_condicion_iva', 'CONDICION LIKE \'%' . str_replace(array(' ', '.'), array('%', '%'), $id_condicion_iva) . '%\'')) {
                    $id_condicion_iva = (int) $condicion_iva[0]['ID'];
                }
                if (!$id_condicion_iva || !is_int($id_condicion_iva)) {
                    $i++;
                    $k++;
                    continue;
                }
                if ($id_condicion_iibb && $condicion_iibb = $this->_db->get_tabla('fid_cliente_condicion_iibb', 'CONDICION LIKE \'%' . str_replace(array(' ', '.'), array('%', '%'), $id_condicion_iibb) . '%\'')) {
                    $id_condicion_iibb = $condicion_iibb[0]['ID'];
                }

                $arr_ins = array(
                    "CUIT" => $cuit,
                    "RAZON_SOCIAL" => $razonsocial,
                    "FECHA_ALTA" => "",
                    //"DIRECCION" => $direccion,
                    "TELEFONO" => $telefono,
                    "OBSERVACION" => $observacion,
                    "ID_PROVINCIA" => 0,
                    "ID_DEPARTAMENTO" => 0,
                    "ID_CONDICION_IVA" => $id_condicion_iva,
                    "ID_CONDICION_IIBB" => $id_condicion_iibb,
                    "INSCRIPCION_IIBB" => "",
                    //"CBU" => $cbu,
                    "CORREO" => $correo,
                    "CU" => 2
                );
                $id_cliente = $this->_db->insert('fid_clientes', $arr_ins);
                //log_this('log/aaaaaa.log', $this->_db->last_query() );
            }

            $bodega_xls = $objPHPExcel->getActiveSheet()->getCell("G" . $i)->getValue();
            $this->_db->select("*");
            $this->_db->join("fid_entidadestipo t", "t.ID_ENTIDAD=e.ID AND t.ID_TIPO=$id_tipoentidad_bodega");

            $idbodega_xls = $this->_db->get_tabla('fid_entidades e', 'NOMBRE LIKE \'' . str_replace(array(' ', '.'), array('%', '%'), $bodega_xls) . '\'');

            if ($idbodega_xls) {
                //nada
                $id_bodega = $idbodega_xls[0]['ID'];
            } else {
                //insertar
                $provinciabodega = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
                $localidadbodega = $objPHPExcel->getActiveSheet()->getCell("I" . $i)->getValue();
                if ($provinciabodega && $id_provinciabodega = $this->_db->get_tabla('fid_provincias', 'PROVINCIA LIKE \'%' . str_replace(array(' ', '.'), array('%', '%'), $provinciabodega) . '%\'')) {
                    $id_provinciabodega = $id_provinciabodega[0]['ID'];
                } else {
                    $id_provinciabodega = 0;
                }

                if ($localidadbodega && $id_localidadbodega = $this->_db->get_tabla('fid_localidades', 'ID_PROVINCIA = ' . $id_provinciabodega . ' AND LOCALIDAD LIKE \'%' . str_replace(array(' ', '.'), array('%', '%'), $localidadbodega) . '%\'')) {
                    $id_localidadbodega = $id_localidadbodega[0]['ID'];
                } else {
                    $id_localidadbodega = 0;
                }

                $arr_bod = array(
                    "NOMBRE" => $bodega_xls,
                    "ID_PROVINCIA" => $id_provinciabodega,
                    //"ID_DEPARTAMENTO" => $id_localidadbodega,
                    "ESTADO" => "0"
                );

                $id_bodega = $this->_db->insert('fid_entidades', $arr_bod);
                $this->_db->insert('fid_entidadestipo', array('ID_ENTIDAD' => $id_bodega, 'ID_TIPO' => $id_tipoentidad_bodega));
            }

            // idfactura
            $numero = $objPHPExcel->getActiveSheet()->getCell("Y" . $i)->getValue();

            //validar numero de factura
            $existe_fact = $numero ? $this->_db->get_tabla('fid_cu_factura', "NUMERO=" . $numero . " AND id_cliente=" . $id_cliente) : FALSE;

            if ($numero && $existe_fact) {
                $i++;
                $k++;
                continue;
            }

            $fecha = $objPHPExcel->getActiveSheet()->getCell("X" . $i)->getValue(); //??
            $fechavto = $objPHPExcel->getActiveSheet()->getCell("W" . $i)->getValue();  //??
            $cai = $objPHPExcel->getActiveSheet()->getCell("X" . $i)->getValue();  //??
            //$precio = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue(); //??
            $neto = floatval($objPHPExcel->getActiveSheet()->getCell("AB" . $i)->getValue());
            if (!$neto) {
                $neto = floatval($objPHPExcel->getActiveSheet()->getCell("AB" . $i)->getCalculatedValue());
            }
            $precio = $litros ? $neto / $litros : 0;
            $iva = floatval($objPHPExcel->getActiveSheet()->getCell("AC" . $i)->getValue());
            if (!$iva) {
                $iva = floatval($objPHPExcel->getActiveSheet()->getCell("AC" . $i)->getCalculatedValue());
            }
            $total = floatval($objPHPExcel->getActiveSheet()->getCell("AD" . $i)->getValue());
            if (!$total) {
                $total = floatval($objPHPExcel->getActiveSheet()->getCell("AD" . $i)->getCalculatedValue());
            }
            $porc_iva = 0;
            if ($total && $neto) {
                $porc_iva = $iva * 100 / $neto;
            }
            $observaciones = $objPHPExcel->getActiveSheet()->getCell("U" . $i)->getValue();
            $observaciones .= $objPHPExcel->getActiveSheet()->getCell("V" . $i)->getValue() ? ' / ' . $objPHPExcel->getActiveSheet()->getCell("V" . $i)->getValue() : '';
            $cuotas = $objPHPExcel->getActiveSheet()->getCell("W" . $i)->getValue();
            if ($neto && $total && !isset($precios_cuotas[$cuotas])) {
                $precios_cuotas[$cuotas] = $precio;
            }

            $nro_vinedo = $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();
            $nro_inv = $objPHPExcel->getActiveSheet()->getCell("J" . $i)->getValue();
            //$formula = $objPHPExcel->getActiveSheet()->getCell("AF" . $i)->getValue();

            if (trim($fecha) == "-   -") {
                $fecha = '';
            } else {
                $fecha = loadDate_excel($fecha);
            }

            if (trim($fechavto) == "-   -") {
                $fechavto = '';
            } else {
                $fechavto = loadDate_excel($fechavto);
            }

            // local
            $_fid_sanjuan = 88;
            $_ope_sanjuan = 99;

            $_fid_mendoza = 66;
            $_ope_mendoza = 77;

            $nolocal = 1;
            if ($nolocal == 1) {
                $_fid_sanjuan = 1;
                $_ope_sanjuan = 16;

                $_fid_mendoza = 1;
                $_ope_mendoza = 16;
            }

            /* if ($id_provincia == '17') {
              $save_ope = $_ope_sanjuan;
              $save_fid = $_fid_sanjuan;
              } else { */
            $save_ope = $_ope_mendoza;
            $save_fid = $_fid_mendoza;
            //}

            $arr_fact = array(
                "NUMERO" => $numero,
                //"FECHAVTO" => $fechavto,
                //"CAI" => $cai,
                "ID_ESTADO" => $estado_fact,
                "TIPO" => "1",
                "LITROS" => $litros,
                "FORMA_PAGO" => $cuotas,
                "VINEDO" => $nro_vinedo,
                "RUT" => $nro_inv,
                "ID_OPERATORIA" => $id_operatoria,
                //"AZUCAR" => $azucar,
                "PRECIO" => $precio,
                "NETO" => $neto,
                "IVA" => $iva,
                "TOTAL" => $total,
                "PORC_IVA" => $porc_iva,
                "OBSERVACIONES" => $observaciones,
                "ID_CLIENTE" => $id_cliente,
                "ID_BODEGA" => $id_bodega,
                "ID_PROVINCIA" => 12, //MENDOZA HARDCODING
            );

            if ($fecha) {
                $arr_fact['FECHA'] = $fecha;
            }

            $arr_bodegas[] = $id_bodega;
            $arr_proveedores[] = $id_cliente;
            $total_litros += $litros;

            /* if (intval($formula) > 0) {
              $arr_fact["FORMULA"] = $formula;
              } */

            //validaciones

            $sw_error = 0;
            $arr_error = array();

            $arr_factor = $this->_db->get_tabla('fid_cliente_condicion_iva', "Id = $id_condicion_iva");
            $factor = 0;
            if ($arr_factor) {
                $factor = $arr_factor[0]['VALOR'];
            }

            /* if (0 and ( $neto != $litros * $precio)) {
              $sw_error = 1; //
              $arr_error[] = "Neto observado";
              } */

            $iva = round($iva * 1, 2);
            $factor = round(($factor * $neto / 100) * 1, 2);
            //log_this("iv-factor.txt", $iva . " - " . $factor);
            /* if ((abs($iva - $factor) > 1)) {
              $sw_error = 1;
              $arr_error[] = "Monto IVA observado(viene:$iva - calculado:$factor)";
              } */

            if ($total - ($neto + $iva) > 1) {
                $sw_error = 1;
                $arr_error[] = "Total observado";
            }

            //verificar cbu
            /* $existe_cbu = $this->verificar_cbu($cbu);
              if ($existe_cbu['error']) {
              $sw_error = 1;
              $arr_error[] = $existe_cbu['result'];
              } */

            //verificar largo cuit
            if (strlen($cuit) != 11) {
                $sw_error = 1;
                $arr_error[] = "Longitud de CUIT Observado";
            }


            if ($sw_error > 0) {
                $arr_fact["ID_ESTADO"] = "12";
                //$arr_fact["IMP_ERROR_COD"] = $sw_error;
                if ($arr_error) {
                    $texto_error = "";
                    foreach ($arr_error as $err) {
                        $texto_error .= $err . "-";
                    }
                    $arr_fact["IMP_ERROR_TEXTO"] = substr($texto_error, 0, -1);
                }
            }

            $resp = $this->_db->insert('fid_cu_factura', $arr_fact);
            //log_this('log/aaaaaa.log', $this->_db->last_query() );
            $res[] = $resp;

            $i++;
            $k++;
            /*
              if ($k==30){
              break;
              } */
        }

        if ($arr_bodegas) {
            $temp_arr = array(
                'ID_OPERATORIA' => $id_operatoria
            );
            foreach ($arr_bodegas as $bod) {
                $temp_arr['ID_BODEGA'] = $bod;
                $this->_db->insert('fid_op_vino_bodegas', $temp_arr);
            }

            $temp_arr = array(
                'ID_OPERATORIA' => $id_operatoria
            );
            foreach ($arr_proveedores as $prov) {
                $temp_arr['ID_PROVEEDOR'] = $prov;
                $this->_db->insert('fid_op_vino_proveedores', $temp_arr);
            }
        }

        $arr_update_factura = array("LTRS_MAX" => $total_litros);
        if (count($precios_cuotas)) {
            $temp_arr = array();
            $arr_precios = array('PRECIO_1', 'PRECIO_2', 'PRECIO_3', 'PRECIO_4', 'PRECIO_5', 'PRECIO_6');
            foreach ($precios_cuotas as $cuota => $precio) {
                if (in_array('PRECIO_' . $cuota, $arr_precios)) {
                    $arr_update_factura['PRECIO_' . $cuota] = $precio;
                }
            }
        }

        $this->_db->update("fid_operatoria_vino", $arr_update_factura, "ID_OPERATORIA = " . $id_operatoria);
        rename("_tmp/importar/imp_vino_fact.xlsx", "_tmp/importar/imp_vino_fact_procesado_" . date('Ymd') . ".xlsx");
        //TRUNCATE TABLE `fid_cu_factura`;TRUNCATE TABLE `fid_operatoria_vino`;TRUNCATE TABLE `fid_op_vino_bodegas`;TRUNCATE TABLE `fid_op_vino_proveedores`;

        return 1;
    }

    function importar_ciu() {
        set_time_limit(0);
        require_once ('general/helper/ClassesPHPExcel/PHPExcel.php');
        require_once ("general/helper/ClassesPHPExcel/PHPExcel/Reader/Excel2007.php");

        $objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load("_tmp/importar/imp_cius.xlsx");
        $objPHPExcel->setActiveSheetIndex(0);

        $i = 2;
        $res = array();
        $suma_kgrs = 0;
        while (trim($objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue()) != '') {

            //$iid            = $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
            $cuit = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
            $cuit = trim($cuit);
            $num_fact = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();
            $numero = $objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue();
            $kgrs = $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();
            $azucar = $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();
            $insc = $objPHPExcel->getActiveSheet()->getCell("F" . $i)->getValue();

            $suma_kgrs += $kgrs;

            $dat_cliente = $this->_db->get_tabla('fid_clientes', 'CUIT="' . $cuit . '"');
            $id_cliente = 0;
            if ($dat_cliente) {
                //log_this('log/yyyyy.log', print_r($dat_cliente,1) );
                if (isset($dat_cliente[0]["ID"])) {
                    $id_cliente = $dat_cliente[0]["ID"];
                } else {
                    //  continue;
                }
            }

            $existe_fact = $this->_db->get_tabla('fid_cu_factura', 'NUMERO="' . $num_fact . '"');
            //log_this('log/zzzzzzz.log',$this->_db->last_query() );

            $existe_ciu = $this->_db->get_tabla('fid_cu_ciu', 'NUMERO="' . $numero . '"');

            if ($existe_fact && !$existe_ciu) {
                $id_fact = $existe_fact[0]["ID"];
                //insertar cius
                $arr_ciu = array(
                    "CUIT" => $cuit,
                    "ID_CLIENTE" => $id_cliente,
                    "ID_FACTURA" => $id_fact,
                    "NUMERO" => $numero,
                    "KGRS" => $kgrs,
                    "AZUCAR" => $azucar,
                    "TIPO" => "1",
                    "VERIFICADO" => "0",
                    "INSC" => $insc
                );
                $resp = $this->_db->insert('fid_cu_ciu', $arr_ciu);
            }

            //validar
            //suma de kgrs(ciu) = krgs de factura
            $i++;
        }

        rename("_tmp/importar/imp_cius.xlsx", "_tmp/importar/imp_cius_procesado.xlsx");
    }

    public function validar_azucar() {
        $this->_db->group_by("ID_FACTURA");
        $facturas = $this->_db->get_tabla("fid_cu_ciu");
        foreach ($facturas as $factura) {
            $fact = $this->_db->get_tabla("fid_cu_ciu", "id_factura = " . $factura['ID_FACTURA']);
            $total_azucar = 0;
            foreach ($fact as $detalle) {
                $total_azucar += ($detalle['KGRS'] * $detalle['AZUCAR']) * 1;
            }
            $promedio_azucar = round(($total_azucar / count($fact)), 2);

            $this->_db->update("fid_cu_factura", array("AZUCAR" => $promedio_azucar), "ID = " . $factura['ID_FACTURA']);
        }
    }

    function validar_archivos_imp() {

        $num_files = contar_archivos_imp();
        if ($num_files == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    function validar_archivos_imp_f() {

        $num_files = contar_archivos_imp_f("_tmp/importar/imp_vino_fact.xlsx");
        if ($num_files == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    function validar_archivos_imp_c() {

        $num_files = contar_archivos_imp_c();
        if ($num_files == 1) {
            return 1;
        } else {
            return 0;
        }
    }

    function get_operatorias() {
        $this->_db->select("ID_OPERATORIA, FECHA_CRE, NOMBRE_OPE, FECHA_CRE");
        return $this->_db->get_tabla("fid_operatoria_vino");
    }

    function get_operatorias_importacion() {
        $this->_db->select("ID_OPERATORIA, FECHA_CRE, NOMBRE_OPE, FECHA_CRE");
        return $this->_db->get_tabla("fid_operatoria_vino", "ESTADO_OP=1", "FECHA_CRE DESC");
    }

}

?>