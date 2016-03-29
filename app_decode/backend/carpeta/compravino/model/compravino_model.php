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


                $this->_db->select("F.ID_CLIENTE AS IDCLIENTE, F.TOTAL AS TOTAL, CUIT, F.ID_BODEGA");
                $this->_db->join("fid_clientes c", "c.ID=f.ID_CLIENTE", 'left');
                $cuit_cli = $this->_db->get_tabla('fid_cu_factura f', "f.ID='" . $id_factura . "'");

                //mendoza fideicomiso 1 y san juan en 26 
                $arra_ins = array(
                    "CUIT" => $cuit_cli[0]['CUIT'],
                    "CODIGO_WEB" => $cuit_cli[0]['IDCLIENTE'],
                    "OPERATORIA" => "2",
                    "IMPORTE" => $cuit_cli[0]['TOTAL'],
                    "LOTE" => $id_lote_new,
                    "IDFACTURAINT" => $id_factura,
                    "NUMFACTURA" => $ciu["NUMERO"],
                    "CODIGO_DEBO" => "",
                    "TIPO" => "OP",
                    "FECHA_PASADO" => date('Ymd h:i:s'),
                    "FECHA_PROCESADO" => "19010101 00:00",
                    "ESTADO" => "1",
                    "BODEGA" => $cuit_cli[0]['ID_BODEGA'],
                    "FORMULA" => $ciu["FORMULA"]
                );
                if ($_POST['provincia'] == 12)
                    $arra_ins["FIDEICOMISO"] = "1";
                else if ($_POST['provincia'] == 17)
                    $arra_ins["FIDEICOMISO"] = "26";
                $return = $this->_dbsql->insert('SOLICITUD_ADM', $arra_ins);
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
        $this->_db->order_by("ID_OPERATORIA","DESC  LIMIT 1");
        $rtn = $this->_db->get_tabla('fid_operatoria_proveedores', "ID_PROVEEDOR= '" . $id_cliente . "'");
        return $rtn;
    }

    function getobj($id_objeto) {

        //$this->_db->select("DATE_FORMAT(), c.CUIT AS CUIT,c.RAZON_SOCIAL AS RAZ,f.*");
        $this->_db->select("c.CUIT AS CUIT,c.RAZON_SOCIAL AS RAZ,f.*");
        $this->_db->join("fid_clientes c", "c.ID=f.ID_CLIENTE");
        $rtn["factura"] = $this->_db->get_tabla('fid_cu_factura f', "f.ID = '" . $id_objeto . "'");
        $rtn["factura"] = $rtn["factura"][0];

        //log_this();


        $rtn["cius"] = $this->_db->get_tabla('fid_cu_ciu', "ID_FACTURA='" . $id_objeto . "'");

        return $rtn;
    }

    function getoperatoria($id_objeto) {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla('fid_operatoria_vino', "ID_OPERATORIA = '" . $id_objeto . "'");
//         log_this('log/lalalalalalallskaldkasl.log', $this->_db->last_query());
        return $rtn;
    }

    function getOperatoriaProveedores($id_objeto) {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla('fid_operatoria_proveedores', "ID_OPERATORIA = '" . $id_objeto . "'");
        return $rtn;
    }

    function getOperatoriaBodegas($id_objeto) {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla('fid_operatoria_bodegas', "ID_OPERATORIA = '" . $id_objeto . "'");
        return $rtn;
    }

    function getProveedoresEdit($id_objeto) {
        $this->_db->select("p.ID_OPERATORIA,c.RAZON_SOCIAL AS RAZON_SOCIAL,p.ID_PROVEEDOR AS ID, p.LIMITE_OPE AS LIMLTRS, p.LIM_OPE_HECT AS MAXHECTAREAS ");
        $this->_db->join("fid_clientes c ", "p.ID_PROVEEDOR=c.ID");
        $rtn = $this->_db->get_tabla('fid_operatoria_proveedores p', "p.ID_OPERATORIA = '" . $id_objeto . "'");
        return $rtn;
    }

    function getBodegasEdit($id_objeto) {
        $this->_db->select("p.ID_OPERATORIA,e.NOMBRE AS NOMBRE,p.ID_BODEGA AS ID, p.LIMITE_OPE AS LIMLTRS ");
        $this->_db->join("fid_entidades e ", "p.ID_BODEGA=e.ID");
        $rtn = $this->_db->get_tabla('fid_operatoria_bodegas p', "p.ID_OPERATORIA = '" . $id_objeto . "'");
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

    function sendOperatoria($nuevoID, $opeNombre, $opeDescripcion, $opeCoordinador, $opeJefe, $listrosMax, $formaPago, $cantCuotas,$tipoPersona) {
        $nuevoID = $nuevoID;
        $fecha_creacion = date('Y-m-d');
        $fecha = date('Y-m-j');
        $nueva_limite = strtotime('+1 year', strtotime($fecha));
        $nueva_limite = date('Y-m-j', $nueva_limite);

//        echo $fecha_creacion . " -- " . $nueva_limite;die;
        $ins_ope = array(
            "ID_OPERATORIA" => $nuevoID,
            "FECHA_CRE" => $fecha_creacion,
            "FECHA_VEN" => $nueva_limite,
            "NOMBRE_OPE" => $opeNombre,
            "DESCRIPCION_OPE" => $opeDescripcion,
            "ID_COORDINADOR_OPE" => $opeCoordinador,
            "ID_JEFE_OPE" => $opeJefe,
            "LTRS_MAX" => $listrosMax,
            "FPAGO" => $formaPago,
            "CANT_CUOTAS" => $cantCuotas,
            "PERSONA" => $tipoPersona,
            "HECT_MAX" => ''
        );
        $this->_db->insert('fid_operatoria_vino', $ins_ope);
    }

    function updateOperatoria($nuevoID, $opeNombre, $opeDescripcion, $opeCoordinador, $opeJefe, $listrosMax, $formaPago, $cantCuotas) {
        $ins_ope = array(
            "NOMBRE_OPE" => $opeNombre,
            "DESCRIPCION_OPE" => $opeDescripcion,
            "ID_COORDINADOR_OPE" => $opeCoordinador,
            "ID_JEFE_OPE" => $opeJefe,
            "LTRS_MAX" => $listrosMax,
            "FPAGO" => $formaPago,
            "CANT_CUOTAS" => $cantCuotas,
            "HECT_MAX" => ''
        );
        $this->_db->update('fid_operatoria_vino', $ins_ope, "ID_OPERATORIA='" . $nuevoID . "'");
    }

    function getIdOperatoria() {
        $this->_db->select("ID_OPERATORIA");
        $this->_db->order_by("ID_OPERATORIA", "DESC LIMIT 1");
        $rtn = $this->_db->get_tabla("fid_operatoria_vino");
        $valor = 0;
        $sumar = 1;
        $devolver = 0;
        if ($rtn) {
            $valor = (int) $rtn[0]['ID_OPERATORIA'];
            $devolver = $valor + $sumar;
        }
        return $devolver;
    }

    function sendProveedores($obj, $nuevoID) {
        foreach ($obj as $value) {
            $ins_proveedor = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_PROVEEDOR" => $value['ID'],
                "LIMITE_OPE" => $value['LIMLTRS'],
                "LIM_OPE_HECT" => $value['MAXHECTAREAS']
            );
            $this->_db->insert('fid_operatoria_proveedores', $ins_proveedor);
        }
    }

    function updateProveedores($obj, $nuevoID) {
        $this->_db->delete("fid_operatoria_proveedores", "ID_OPERATORIA='" . $nuevoID . "'");
        foreach ($obj as $value) {
            $ins_proveedor = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_PROVEEDOR" => $value['ID'],
                "LIMITE_OPE" => $value['LIMLTRS'],
                "LIM_OPE_HECT" => $value['MAXHECTAREAS']
            );
            $this->_db->insert('fid_operatoria_proveedores', $ins_proveedor);
        }
    }

    function sendBodegas($obj, $nuevoID) {
        foreach ($obj as $value) {
            $ins_bodegas = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_BODEGA" => $value['ID'],
                "LIMITE_OPE" => $value['LIMLTRS']
            );
            $this->_db->insert('fid_operatoria_bodegas', $ins_bodegas);
        }
    }

    function sendHumana($obj, $nuevoID) {
        foreach ($obj as $value) {
            $aEntero = 0;
            if($value['valor']=='SI'){
                $aEntero=2;
            }
            if($value['valor']=='NO'){
                $aEntero=1;
            }
            echo $aEntero;
//            $ins_check = array(
//                "ID_OPERATORIA" => $nuevoID,
//                "ID_HUMANA" => $value['numcheck'],
//                "ESTADO" =>$aEntero
//            );
            
//            var_dump($ins_check);
//            $this->_db->insert('fid_operatoria_humana', $ins_check);
        }
            die(" ESTOS SON LOS VALORES -----  ");
    }

    function sendJuridica($obj, $nuevoID) {
        $array_checklist = explode(",", $obj);
        foreach ($obj as $value) {
            $ins_check = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_JURIDICA" => $value,
            );
            $this->_db->insert('fid_operatoria_juridica', $ins_check);
        }
    }

    function updateBodegas($obj, $nuevoID) {
        $this->_db->delete("fid_operatoria_bodegas", "ID_OPERATORIA='" . $nuevoID . "'");
        foreach ($obj as $value) {
            $ins_bodegas = array(
                "ID_OPERATORIA" => $nuevoID,
                "ID_BODEGA" => $value['ID'],
                "LIMITE_OPE" => $value['LIMLTRS']
            );
            $this->_db->insert('fid_operatoria_bodegas', $ins_bodegas);
        }
    }

    function sendobj($obj) {
        //log_this('log/xxxxx.log',print_r($obj,1));
        $iid = $obj["id"];
        $arr_cius = isset($obj["arr_cius"]) ? $obj["arr_cius"] : array();

        $cuit = $obj["CUIT"];
        $cli = $this->_db->get_tabla('fid_clientes', 'CUIT=' . $cuit);

        $cod_cli = $cli[0]['ID'];
        $obj["ID_CLIENTE"] = $cod_cli;
        $cuit_tmp = $obj["CUIT"];

        unset($obj["id"], $obj["CUIT"], $obj["arr_cius"], $obj["update_cius"]);

        if ($iid == 0)://agregar
            $resp = $this->_db->insert('fid_cu_factura', $obj);
            //log_this('log/aaaaa.log', $this->_db->last_query());
            $acc = "add";
            $id_new = $resp;
        else://editar
            $resp = $this->_db->update('fid_cu_factura', $obj, "id='" . $iid . "'");
            //log_this('log/aaaaa.log', $this->_db->last_query());
            $acc = "edit";
            $id_new = $iid;
        endif;

        if ($arr_cius):
            //borrar
            $this->_db->delete("fid_cu_ciu", "ID_FACTURA='" . $id_new . "'");
            $tot = count($arr_cius);
            $c = 0;
            foreach ($arr_cius as $ciu):

                $verif = ($ciu["CHEQUEO"] == '1' or $ciu["CHEQUEO"] == 'true') ? 1 : 0;
                $arr_ins = array(
                    "ID_CLIENTE" => $cod_cli,
                    "CUIT" => $cuit_tmp,
                    "ID_FACTURA" => $id_new,
                    "NUMERO" => $ciu["NUM"],
                    "AZUCAR" => $ciu["AZUCAR"],
                    "KGRS" => $ciu["KGRS"],
                    "INSC" => $ciu["INSC"],
                    "VERIFICADO" => $verif
                );
                $resp = $this->_db->insert('fid_cu_ciu', $arr_ins);
                $c++;
                //log_this('log/qqqqqqq.log', $this->_db->last_query() );
            endforeach;

            if ($c == $tot):
                //update
                $resp = $this->_db->update('fid_cu_factura', array("ID_ESTADO" => '4'), "ID='" . $id_new . "'");
            endif;


        endif;
        $rtn = array(
            "accion" => $acc,
            "result" => $resp
        );
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
    function getChecklistHumana() {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla("fid_checklist_humana");
        return $rtn;
    }
    function getChecklistJuridica() {
        $this->_db->select("*");
        $rtn = $this->_db->get_tabla("fid_checklist_juridica");
        return $rtn;
    }
    
    
//    function getbodegas_vino() {
////SELECT e.ID, e.NOMBRE,b.ID_OPERATORIA,b.LIMITE_OPE,p.PROVINCIA FROM fid_entidades e    
////JOIN fid_operatoria_bodegas b ON(e.ID=b.ID_BODEGA)
////JOIN fid_provincias p ON (e.ID_PROVINCIA=p.ID)
////WHERE b.ID_OPERATORIA=20
////ORDER BY b.ID_OPERATORIA DESC
//        $this->_db->select("e.ID, e.NOMBRE,b.ID_OPERATORIA,b.LIMITE_OPE,p.PROVINCIA");
//        $this->_db->join("fid_operatoria_bodegas b", "e.ID=b.ID_BODEGA");
//        $this->_db->join("fid_provincias p", "e.ID_PROVINCIA=p.ID");
//        $rtn = $this->_db->get_tabla("fid_entidades e","b.ID_OPERATORIA=20");
//        
//        return $rtn;
//    }

    function get_ope_bodegas() {
        $this->_db->select("e.id AS ID, e.nombre AS NOMBRE");
        $this->_db->join("fid_entidades e", "et.id_entidad=e.id");
        $this->_db->join("fid_entidades_tipos ets", "et.id_tipo=ets.id");
        $rtn = $this->_db->get_tabla("fid_entidadestipo et", "ets.ID =24");
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
        } else {
            die("NADA");
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
        } else {
            die("NADA");
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

    function importar_xls($fid_sanjuan, $ope_sanjuan) {
        set_time_limit(0);
        require_once ('general/helper/ClassesPHPExcel/PHPExcel.php');
        require_once ("general/helper/ClassesPHPExcel/PHPExcel/Reader/Excel2007.php");

        $objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load("_tmp/importar/imp_fact.xlsx");
        $objPHPExcel->setActiveSheetIndex(0);

        $i = 2;
        $res = array();

        $k = 0;



        while ($objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue() != '') {

            $iid = $objPHPExcel->getActiveSheet()->getCell("A" . $i)->getValue();
            $cuit = $objPHPExcel->getActiveSheet()->getCell("B" . $i)->getValue();

            $cbu = substr($objPHPExcel->getActiveSheet()->getCell("C" . $i)->getValue(), 0, 22);
            $correo = $objPHPExcel->getActiveSheet()->getCell("D" . $i)->getValue();
            $razonsocial = $objPHPExcel->getActiveSheet()->getCell("E" . $i)->getValue();

            $direccion = $objPHPExcel->getActiveSheet()->getCell("H" . $i)->getValue();
            $telefono = $objPHPExcel->getActiveSheet()->getCell("I" . $i)->getValue();

            $observacion = $objPHPExcel->getActiveSheet()->getCell("K" . $i)->getValue();
            $id_provincia = $objPHPExcel->getActiveSheet()->getCell("L" . $i)->getValue();
            $id_departamento = $objPHPExcel->getActiveSheet()->getCell("M" . $i)->getValue();

            $id_condicion_iva = $objPHPExcel->getActiveSheet()->getCell("N" . $i)->getValue();
            $id_condicion_iibb = $objPHPExcel->getActiveSheet()->getCell("O" . $i)->getValue();
            //$inscripcion_iibb   = $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();

            $cuit = strval($cuit);
            //$cbu = exp_to_dec($cbu);
            $cbu = $cbu;
            $existecli = $this->_db->get_tabla('fid_clientes', 'CUIT="' . $cuit . '"');


            if ($existecli) {
                $id_cliente = $existecli[0]["ID"];
                $id_condicion_iva = $existecli[0]["ID_CONDICION_IVA"];
            } else {
                $arr_ins = array(
                    "CUIT" => $cuit,
                    "RAZON_SOCIAL" => $razonsocial,
                    "FECHA_ALTA" => "",
                    "DIRECCION" => $direccion,
                    "TELEFONO" => $telefono,
                    "OBSERVACION" => $observacion,
                    "ID_PROVINCIA" => $id_provincia,
                    "ID_DEPARTAMENTO" => $id_departamento,
                    "ID_CONDICION_IVA" => $id_condicion_iva,
                    "ID_CONDICION_IIBB" => $id_condicion_iibb,
                    "INSCRIPCION_IIBB" => "",
                    "CBU" => $cbu,
                    "CORREO" => $correo,
                    "CU" => 2
                );
                $id_cliente = $this->_db->insert('fid_clientes', $arr_ins);
                //log_this('log/aaaaaa.log', $this->_db->last_query() );
            }

            $idbodega_xls = $objPHPExcel->getActiveSheet()->getCell("P" . $i)->getValue();
            $existebod = $this->_db->get_tabla('fid_bodegas', 'ID="' . $idbodega_xls . '"');
            if ($existebod) {
                //nada
                $id_bodega = $existebod[0]['ID'];
            } else {
                //insertar
                $nombrebodega = $objPHPExcel->getActiveSheet()->getCell("Q" . $i)->getValue();
                $provinciabodega = $objPHPExcel->getActiveSheet()->getCell("R" . $i)->getValue();
                $localidadbodega = $objPHPExcel->getActiveSheet()->getCell("S" . $i)->getValue();

                $arr_bod = array(
                    "NOMBRE" => $nombrebodega,
                    "ID_PROVINCIA" => $provinciabodega,
                    "ID_DEPARTAMENTO" => $localidadbodega,
                    "ESTADO" => "0"
                );
                $id_bodega = $this->_db->insert('fid_bodegas', $arr_bod);
            }

            // idfactura
            $numero = $objPHPExcel->getActiveSheet()->getCell("T" . $i)->getValue();


            //validar numero de factura
            $existe_fact = $this->_db->get_tabla('fid_cu_factura', "NUMERO=" . $numero . " AND id_cliente=" . $id_cliente);

            if ($existe_fact) {
                $i++;
                $k++;
                continue;
            }


            $fecha = $objPHPExcel->getActiveSheet()->getCell("V" . $i)->getValue();
            $fechavto = $objPHPExcel->getActiveSheet()->getCell("W" . $i)->getValue();
            $cai = $objPHPExcel->getActiveSheet()->getCell("X" . $i)->getValue();
            $kgrs = $objPHPExcel->getActiveSheet()->getCell("Y" . $i)->getValue();
            $azucar = $objPHPExcel->getActiveSheet()->getCell("Z" . $i)->getValue();
            $precio = $objPHPExcel->getActiveSheet()->getCell("AA" . $i)->getValue();
            $neto = $objPHPExcel->getActiveSheet()->getCell("AB" . $i)->getValue();
            $iva = $objPHPExcel->getActiveSheet()->getCell("AC" . $i)->getValue();
            $total = $objPHPExcel->getActiveSheet()->getCell("AD" . $i)->getValue();
            $observaciones = $objPHPExcel->getActiveSheet()->getCell("AE" . $i)->getValue();
            $formula = $objPHPExcel->getActiveSheet()->getCell("AF" . $i)->getValue();

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

            if ($id_provincia == '17') {
                $save_ope = $_ope_sanjuan;
                $save_fid = $_fid_sanjuan;
            } else {
                $save_ope = $_ope_mendoza;
                $save_fid = $_fid_mendoza;
            }

            $arr_fact = array(
                "NUMERO" => $numero,
                "FECHA" => $fecha,
                "FECHAVTO" => $fechavto,
                "CAI" => $cai,
                "ID_ESTADO" => "1",
                "TIPO" => "1",
                "KGRS" => $kgrs,
                "ID_OPERATORIA" => $save_ope,
                "ID_FIDEICOMISO" => $save_fid,
                "AZUCAR" => $azucar,
                "PRECIO" => $precio,
                "NETO" => $neto,
                "IVA" => $iva,
                "TOTAL" => $total,
                "OBSERVACIONES" => $observaciones,
                "ID_CLIENTE" => $id_cliente,
                "ID_BODEGA" => $id_bodega,
                "ID_PROVINCIA" => $id_provincia,
            );

            if (intval($formula) > 0) {
                $arr_fact["FORMULA"] = $formula;
            }

            //validaciones

            $sw_error = 0;
            $arr_error = array();

            $arr_factor = $this->_db->get_tabla('fid_cliente_condicion_iva', "Id = $id_condicion_iva");
            $factor = 0;
            if ($arr_factor) {
                $factor = $arr_factor[0]['VALOR'];
            }

            if (0 and ( $neto != $kgrs * $precio)) {
                $sw_error = 1; //
                $arr_error[] = "Neto observado";
            }

            $iva = round($iva * 1, 2);
            $factor = round(($factor * $neto / 100) * 1, 2);
            //log_this("iv-factor.txt", $iva . " - " . $factor);
            if ((abs($iva - $factor) > 1)) {
                $sw_error = 1;
                $arr_error[] = "Monto IVA observado(viene:$iva - calculado:$factor)";
            }

            if ($total - ($neto + $iva) > 1) {
                $sw_error = 1;
                $arr_error[] = "Total observado";
            }

            //verificar cbu
            $existe_cbu = $this->verificar_cbu($cbu);
            if ($existe_cbu['error']) {
                $sw_error = 1;
                $arr_error[] = $existe_cbu['result'];
            }

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
        rename("_tmp/importar/imp_fact.xlsx", "_tmp/importar/imp_fact_procesado.xlsx");

        return $res;
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

        $num_files = contar_archivos_imp_f();
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

}

?>