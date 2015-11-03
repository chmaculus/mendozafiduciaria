<?php

class carpetas_model extends main_model {

    public $_tablamod = "fid_operaciones";
    public $_tablamodtra = "fid_traza";
    public $_tablamodusu = "fid_usuarios";
    public $_tablamodusucli = "fid_operacion_cliente";
    public $_tablamodcli = "fid_clientes";

    function get_obj($id) {
        if (!$id)
            return array();
        $this->_db->where("id = '" . $id . "'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        if ($rtn) {
            $obj_operatoria = $this->_db->get_tabla('fid_operatorias', "ID='" . $rtn[0]["ID_OPERATORIA"] . "'");
            $rtn[0]["obj_operatoria"] = $obj_operatoria[0];
        }
//        print_r($rtn);
        return $rtn;
    }

    function getestadocopia($idope) {
        $this->_db->select("ESTADO");
        $rtn = $this->_db->get_tabla('fid_traza', 'ID_OPERACION="' . $idope . '" AND OBSERVACION="NOTIFICACION" AND DESCRIPCION="PETICION DE CONFIRMACION DE COPIA DE CONTRATO EN LEGALES" AND (ESTADO<>"4")');
        //log_this('eeeeee.log', $this->_db->last_query());
        if ($rtn) {
            return $rtn[0]["ESTADO"];
        } else {
            return 0;
        }
    }

    function verificarcuit($cuit) {
        if ($cuit != '') {
            $this->_db->select("ID");
            $rtn = $this->_db->get_tabla("fid_clientes", "CUIT='" . $cuit . "'");
            return $rtn;
        } else {
            return false;
        }
    }

    function get_entidades($id) {
        $this->_db->select("ID_TIPO");
        $this->_db->where("ID_ENTIDAD = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_entidadestipo');
        return $rtn;
    }

    function get_legales_et4($idope) {
        $this->_db->select("DESTINO");
        $rtn = $this->_db->get_tabla('fid_traza', " OBSERVACION='ENVIADO' AND ETAPA='4' AND ID_OPERACION='" . $idope . "' ");
        //log_this('eeeeee.log', $this->_db->last_query());
        if ($rtn) {
            return $rtn[0]["DESTINO"];
        } else {
            return -1;
        }
    }

    function get_solicitud_de_credito($idope) {
        //$this->_db->select("ID_TIPO");
        $this->_db->where("ID_OPERACION = '" . $idope . "'");
        $rtn = $this->_db->get_tabla('fid_creditos');
        return $rtn;
    }

    function get_tienecuotas($idcredito) {
        $this->_db->select("count(*) as cont");
        $this->_db->where("ID_CREDITO='" . $idcredito . "'");
        $rtn = $this->_db->get_tabla('fid_creditos_cuotas');
        //log_this('eeeeee.log', $this->_db->last_query());
        return $rtn[0]["cont"];
    }

    function get_carterade($id) {
        $this->_db->select(" CONCAT(NOMBRE,' ', APELLIDO) as nombrecarterade ");
        $rtn = $this->_db->get_tabla('fid_usuarios', "ID='" . $id . "'");
        log_this('get_carterade.log', $this->_db->last_query());
        return $rtn;
    }

    function get_etapaactual($id) {
        $this->_db->select(" NOMBRE ");
        $rtn = $this->_db->get_tabla('fid_etapas', "ID='" . $id . "'");
        log_this('get_etapaactual.log', $this->_db->last_query());
        return $rtn;
    }

    function get_proceso($id) {
        $this->_db->select(" NOMBRE ");
        $rtn = $this->_db->get_tabla('fid_procesos', "ID='" . $id . "'");
        log_this('get_proceso.log', $this->_db->last_query());
        return $rtn;
    }

    function guardar_etiqueta($semilla, $etiqueta) {
        $rtn = $this->_db->insert('fid_upload_etiqueta', array("SEMILLA" => $semilla, "ETIQUETA" => $etiqueta));
        return $rtn;
    }

    function get_traza_aux1($idope) {
        $rtn = $this->_db->get_tabla('fid_traza', "ID_OPERACION='" . $idope . "' AND AUX1='1'");
        if ($rtn)
            return count($rtn);
        else
            return 0;
    }

    function guardar_adjunto_gar($id_garantia, $id_usuario, $etiqueta, $nombre) {
        $rtn = $this->_db->insert('fid_garantia_adjunto', array("ID_GARANTIA" => $id_garantia, "ID_USUARIO" => $id_usuario, "NOMBRE" => $nombre, "ETIQUETA" => $etiqueta, "CREATEDON" => "[NOW()]"));
        //log_this( 'eeeeee.log', $this->_db->last_query() );
        return $rtn;
    }

    function get_jefeope($idope) {
        $this->_db->select("o.JEFEOP,o.COORDOPE");
        $this->_db->join("fid_operatorias o", "o.ID=op.ID_OPERATORIA");
        $rtn = $this->_db->get_tabla('fid_operaciones op', "op.ID='" . $idope . "'");
        //log_this('eeeeee.log', $this->_db->last_query());
        return $rtn;
    }

    function delupload($idope, $ruta) {
        $this->_db->delete("fid_operatoria_adjunto", "ID_OPERATORIA='" . $idope . "' AND NOMBRE='" . $ruta . "'");
        return 1;
    }

    function delupload_ope($idope, $ruta) {
        $this->_db->delete("fid_operacion_adjunto", "ID_OPERACION='" . $idope . "' AND NOMBRE='" . $ruta . "'");
        return 1;
    }

    function delupload_req($idnotareq, $ruta) {
        $this->_db->delete("fid_nota_req_adjunto", "ID_NOTA_REQ='" . $idnotareq . "' AND NOMBRE='" . $ruta . "'");
        return 1;
    }

    function delupload_gar($idgar, $ruta) {
        $this->_db->delete("fid_garantia_adjunto", "ID_GARANTIA='" . $idgar . "' AND NOMBRE='" . $ruta . "'");
        return 1;
    }

    function get_uploads($id) {
        $this->_db->select("a.*,a.ID as IID, a.ID_USUARIO as USUARIO, CONCAT(u.NOMBRE,' ',u.APELLIDO) AS USUARIO_NOMBRE, e.NOMBRE AS ETAPA");
        $this->_db->where("ID_OPERACION = '" . $id . "'");
        $this->_db->join("fid_usuarios u", "u.ID=a.ID_USUARIO");
        $this->_db->join("fid_etapas e", "e.ID=a.ID_ETAPA");
        $rtn = $this->_db->get_tabla('fid_operacion_adjunto a');
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function get_uploads_req($id) {
        $this->_db->where("ID_NOTA_REQ = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_nota_req_adjunto');
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function get_uploads_gar($idgar) {
        $this->_db->select("ga.*,CONCAT(u.NOMBRE,' ',u.APELLIDO) AS USUARIO_NOMBRE");
        $this->_db->where("ID_GARANTIA = '" . $idgar . "'");
        $this->_db->join("fid_usuarios u", "u.ID=ga.ID_USUARIO");
        $rtn = $this->_db->get_tabla('fid_garantia_adjunto ga');
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function get_uploads_operatoria($id) {
        $rtn = $this->_db->get_tabla($this->_tablamod, "ID='" . $id . "'");
        if ($rtn) {
            $idoperatoria = $rtn[0]['ID_OPERATORIA'];
            $rtnope = $this->_db->get_tabla("fid_operatoria_adjunto", "ID_OPERATORIA='" . $idoperatoria . "'");
            //log_this('xxxxx111.log', $this->_db->last_query() );
            return $rtnope;
        }
        return false;
    }

    function get_reqs($id) {
        $this->_db->where("ID_OPERACION = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_nota_req');
        return $rtn;
    }

    function get_req($id) {
        $this->_db->where("ID = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_nota_req');
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function get_infoadd($id) {
        $this->_db->where("ID_OPERACION = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_operacion_infoadicional');
        return $rtn;
    }

    function get_fid_entidades($id) {
        $rtn = $this->_db->get_tabla('fid_fideicomiso_entidades', "ID_FIDEICOMISO='" . $id . "'");
        return $rtn;
    }

    function get_operacion_checklist($idope) {
        $rtn = $this->_db->get_tabla('fid_operacion_checklist', "ID_OPERACION='" . $idope . "' and ESTADO='1'");
        return $rtn;
    }

    function get_comite_checklist($idope) {
        $rtn = $this->_db->get_tabla('fid_operacion_condicionesprevias', "ID_OPERACION='" . $idope . "' and ESTADO='1' and TIPO='1'");
        return $rtn;
    }

    function get_desembolso_checklist($idope) {
        $rtn = $this->_db->get_tabla('fid_operacion_condicionesprevias', "ID_OPERACION='" . $idope . "' and ESTADO='1'  and TIPO='2'");
        //log_this('rrrrrrr.log',$this->_db->last_query() );
        return $rtn;
    }

    function getentidad_select($idp) {
        $rtn = $this->_db->select("ID,NOMBRE");
        $rtn = $this->_db->join("fid_entidades e", "e.id=et.id_entidad");
        $rtn = $this->_db->get_tabla("fid_entidadestipo et", "id_tipo='" . $idp . "'");
        return $rtn;
    }

    function get_clientes_ope($id) {
        $this->_db->select("ID_CLIENTE");
        $this->_db->where("ID_OPERACION = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_operacion_cliente');
        return $rtn;
    }

    function get_desembolsos_creditos($id_credito) {
        $rtn = $this->_db->get_tabla('fid_creditos_desembolsos', "ID_CREDITO='" . $id_credito . "'");
        return $rtn;
    }

    function get_credito_ope($id_credito, $id_operacion) {
        $rtn = $this->_db->get_tabla('fid_creditos', "ID='" . $id_credito . "' AND ID_OPERACION='" . $id_operacion . "'");
        if ($rtn) {
            $rtn = $rtn[0];

            $this->_db->limit(1, 1);
            $this->_db->select("PERIODICIDAD_TASA");
            $rtn2 = $this->_db->get_tabla('fid_creditos_eventos', "ID_CREDITO=$id_credito");

            if ($rtn2) {
                $rtn['PERIODICIDAD_TASA'] = $rtn2[0]['PERIODICIDAD_TASA'];
            } else {
                $rtn['PERIODICIDAD_TASA'] = 0;
            }


            return $rtn;
        }
        return false;
    }

    function get_suma_desembolsos($id) {
        $this->_db->select("sum(DES_MONTO) AS DES_MONTO");
        $this->_db->where("ID_OPERACION = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_sol_desembolso');
        return $rtn[0]["DES_MONTO"] ? $rtn[0]["DES_MONTO"] : '0';
    }

    function get_suma_garantias_c($id) {
        $this->_db->select("sum(VALOR_GARANTIA) AS SUMGAR");
        $this->_db->where("ID_OPERACION = '" . $id . "' AND ID_ESTADO='6'"); // CONSTITUIDAS
        $rtn = $this->_db->get_tabla('fid_garantias');
        return $rtn[0]["SUMGAR"] ? $rtn[0]["SUMGAR"] : '0';
    }

    function get_clientes_ope_nombre($id) {
        $this->_db->select("oc.ID_CLIENTE, c.CORREO as CORREO, c.RAZON_SOCIAL as RAZON_SOCIAL,c.CUIT as CUIT, op.NOMBRE as OPERATORIA, civa.CONDICION AS CONDIVA, c.DIRECCION AS DOMICILIO, d.LOCALIDAD AS DEPARTAMENTO, op.TASA_SUBSIDIADA as TASA_SUBSIDIADA, op.TASA_INTERES_COMPENSATORIA as TASA_INTERES_COMPENSATORIA, op.TASA_INTERES_MORATORIA as TASA_INTERES_MORATORIA, op.TASA_INTERES_POR_PUNITORIOS as TASA_INTERES_POR_PUNITORIOS, o.DESTINO as DESTINO, o.ID as CODIGO ");
        $this->_db->join("fid_clientes c", "c.ID=oc.ID_CLIENTE");
        $this->_db->join("fid_operaciones o", "o.ID=oc.ID_OPERACION");
        $this->_db->join("fid_operatorias op", "op.ID=o.ID_OPERATORIA");
        $this->_db->join("fid_cliente_condicion_iva civa", "civa.ID=c.ID_CONDICION_IVA", "LEFT");
        $this->_db->join("fid_localidades d", "d.ID=o.ID_DEPARTAMENTO");
        $this->_db->where("ID_OPERACION = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_operacion_cliente oc');
        //log_this('xxxxx.log', $this->_db->last_query() );


        $cad_c = "";
        $cad_cuit = "";
        $cad_email = "";
        $cad_civa = "";
        $cad_operatoria = "";
        if ($rtn) {
            foreach ($rtn as $r) {
                $cad_c .= $r["RAZON_SOCIAL"] . ", ";
                $cad_cuit .= $r["CUIT"] . ", ";
                $cad_email .= $r["CORREO"] . ", ";
            }
            $cad_c = substr($cad_c, 0, -2);
            $cad_cuit = substr($cad_cuit, 0, -2);
            $cad_email = substr($cad_email, 0, -2);
            $cad_operatoria = $r["OPERATORIA"];
            $cad_civa = $r["CONDIVA"];
        }
        $cad = array(
            "cad_c" => $cad_c,
            "cad_cuit" => $cad_cuit,
            "cad_operatoria" => $cad_operatoria,
            "cad_civa" => $cad_civa,
            "cad_email" => $cad_email,
            "cad_obj" => $rtn
        );

        return $cad;
    }

    function get_etapas_ope($id) {
        //$this->_db->select("ID_CLIENTE");
        $this->_db->where("ID_OPERACION = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_operacion_etapas');
        return $rtn;
    }

    function update_soldese($idsol, $obj, $id_ope_actual) {
        $resp = $this->_db->update('fid_sol_desembolso', $obj, "ID='" . $idsol . "'");
        //actualizar aux1 = 1
        $this->_db->update('fid_traza', array("AUX1" => "0"), "ID_OPERACION='" . $id_ope_actual . "' AND AUX1='1'");
        return $resp;
    }

    function actualizar_operacion_desistir($idope, $estado) {
        $etapa_desistir = 16;
        $resp = $this->_db->update('fid_operaciones', array("ID_ESTADO" => $estado, "ID_ETAPA_ACTUAL" => $etapa_desistir), "ID='" . $idope . "'");
        return $resp;
    }

    function get_sol_desembolso($id) {
        $this->_db->where("ID = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_sol_desembolso');
        return $rtn;
    }

    function get_garantia_obj($id) {
        $this->_db->where("ID = '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_garantias');
        return $rtn;
    }

    function getform_actualizarestadogarantia() {
        $rtn = $this->_db->get_tabla('fid_garantia_estado');
        return $rtn;
    }

    function actualizar_garantia($id, $arr_obj) {
        $rtn = $this->_db->update("fid_garantias", $arr_obj, "ID='" . $id . "'");
        return $rtn;
    }

    function sendreq($obj, $adjuntos, $autor_req, $notificar_operaciones_carpeta_respondida = 0) {

        $iid = $obj["idreqh"];
        unset($obj["idreqh"]);
        //IDOPE, ASUNTO, DESC
        $fecha_actual = "";
        if (isset($obj['FCREA']) && $obj['FCREA'])
            $fecha_actual = deFecha_a_base($obj['FCREA']);
        $fecha_resp = "";
        if (isset($obj['FREC']) && $obj['FREC'])
            $fecha_resp = deFecha_a_base($obj['FREC']);
        //$fecha_actual = date("Y-m-d H:i:s");
        //$fecha_actual = date("Y-m-d H:i:s",  strtotime($obj['FCREA']));
        $id_new = $iid;
        if ($iid == 0) { //agregar
            $obj_na = array();
            $obj['FOJAS'] = "";
            $obj['REMITENTE'] = $_SESSION["USERADM"];
            $obj['DESTINATARIO'] = "BENEFICIARIO";
            $obj['PROPIETARIO'] = "BENEFICIARIO";
            $obj['FCREA'] = $fecha_actual;
            $obj['FREC'] = $fecha_resp;
            $obj['FTRA'] = "";
            $obj['TIPO'] = "0";

            $resp = $this->_db->insert('fid_nota_req', $obj);
            $id_new = $resp;
            $obj['ID'] = $id_new;
            $acc = "add";


            if ($autor_req > 0) {
                $this->_db->select('CARTERADE,ID_ETAPA_ACTUAL,ID');
                $reg = $this->_db->get_tabla($this->_tablamod, "ID='" . $obj['ID_OPERACION'] . "'");
                if ($reg) {
                    $carterade = $reg[0]["CARTERADE"];
                }
                //obtener etapa origen
                $this->_db->select('ETAPA_ORIGEN');
                $regt = $this->_db->get_tabla("fid_traza", "ID_OPERACION='" . $obj['ID_OPERACION'] . "' AND ACTIVO='1'");
                $etapa_origen = 99;
                if ($regt) {
                    $etapa_origen = $regt[0]["ETAPA_ORIGEN"];
                }
                //insertar traza que servira para notificacion al jefe de op
                $arr_traza = array(
                    "ID_OPERACION" => $obj['ID_OPERACION'],
                    "ESTADO" => 9, //autorizacion de req
                    "CARTERADE" => $carterade,
                    "DESTINO" => $carterade,
                    "OBSERVACION" => 'AUTORIZACION DE REQUERIMIENTO',
                    "DESCRIPCION" => 'SE PIDE AUTORIZACION PARA REQUERIMIENTO',
                    "ETAPA" => $id_new, //aca uso este campo para guardar el id del req
                    "ETAPA_ORIGEN" => $etapa_origen,
                    "FECHA" => $fecha_actual,
                    "LEIDO" => 1,
                    "NOTIF" => 1,
                    "AUTOR_REQ" => $autor_req
                );
                $this->_db->insert('fid_traza', $arr_traza);
            }
        } else {
            $obj['FREC'] = $fecha_resp;
            unset($obj['FCREA']);

            $resp = $this->_db->update('fid_nota_req', $obj, "ID='" . $iid . "'");
            $obj_na = $obj;

            if ($notificar_operaciones_carpeta_respondida == 1) {
                $this->_db->select('CARTERADE,ID_ETAPA_ACTUAL,ID');
                $reg = $this->_db->get_tabla($this->_tablamod, "ID='" . $obj['ID_OPERACION'] . "'");
                if ($reg) {
                    $carterade = $reg[0]["CARTERADE"];
                }
                //obtener etapa origen
                $this->_db->select('ETAPA_ORIGEN');
                $regt = $this->_db->get_tabla("fid_traza", "ID_OPERACION='" . $obj['ID_OPERACION'] . "' AND ACTIVO='1'");
                $etapa_origen = 99;
                if ($regt) {
                    $etapa_origen = $regt[0]["ETAPA_ORIGEN"];
                }

                //insertar traza que servira para notificacion al jefe de op
                $arr_traza = array(
                    "ID_OPERACION" => $obj['ID_OPERACION'],
                    "ESTADO" => 10, //notificar al jefe de operaciones que se respondio su req
                    "CARTERADE" => $carterade,
                    "DESTINO" => $carterade,
                    "OBSERVACION" => 'AUTORIZACION DE REQUERIMIENTO', // usaremos el mismo asunto, pero en realidad no es autorizacion, sino una noftificacion al jefe de op
                    "DESCRIPCION" => 'SE NOTIFICA AL JEFE DE OPERACIONES QUE RESPONDIERON A SU REQUERIMIENTO',
                    "ETAPA" => $id_new, //aca uso este campo para guardar el id del req
                    "ETAPA_ORIGEN" => $etapa_origen,
                    "FECHA" => $fecha_actual,
                    "LEIDO" => 1,
                    "NOTIF" => 1,
                    "ACTIVO" => 0,
                    "AUTOR_REQ" => '0'
                );
                $this->_db->insert('fid_traza', $arr_traza);
            }


//            log_this('g.log',$this->_db->last_query() );

            $obj = $this->_db->get_tabla("fid_nota_req", "ID='" . $iid . "'");
            if ($obj) {
                $obj['FREC'] = $fecha_resp;
                $obj[0]['FREC'] = $fecha_resp;
            }

            $acc = "edit";
            //estado
            if ($obj[0]["ESTADO"] == 0)
                $estado = "Emitido";
            if ($obj[0]["ESTADO"] == 1)
                $estado = "Pendiente";
            elseif ($obj[0]["ESTADO"] == 2)
                $estado = "Enviado";
            elseif ($obj[0]["ESTADO"] == 3)
                $estado = "Respondido";
            elseif ($obj[0]["ESTADO"] == 4)
                $estado = "Aceptado";
            elseif ($obj[0]["ESTADO"] == 5)
                $estado = "Rechazado";
            $obj[0]["ESTADO"] = $estado;
        }

        if (isset($obj['FCREA']))
            $fcrea = strtotime($obj['FCREA']) == false ? '-' : date("d/m/Y", strtotime($obj['FCREA']));
        else
            $fcrea = "";

        if (isset($obj['FREC']))
            $frec = strtotime($obj['FREC']) == false ? '-' : date("d/m/Y", strtotime($obj['FREC']));
        else
            $frec = "";

        if (isset($obj['FTRA']))
            $ftra = strtotime($obj['FTRA']) == false ? '-' : date("d/m/Y", strtotime($obj['FTRA']));
        else
            $ftra = "";
        $obj['FCREA'] = $fcrea;
        $obj['FREC'] = $frec;
        $obj['FTRA'] = $ftra;

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

        if (count($obj_na) > 0 && $obj_na["estado"] == 3):
            $reg = $this->_db->get_tabla('fid_nota_req', "ID='" . $id_new . "'");

            $remitente = 0;
            if ($reg):
                $remitente = $reg[0]["REMITENTE"];
                $id_op = $reg[0]["ID_OPERACION"];
            endif;

            $obj_na["ASUNTO"] = "Respuesta a Requerimiento " . $id_new;
            $obj_na["PROPIETARIO"] = $_SESSION["USERADM"];
            $obj_na["ID_OPERACION"] = "0";
            $obj_na["REMITENTE"] = $_SESSION["USER_NA"];
            $obj_na["ENVIADOA"] = $remitente;

            $obj_na["FOJAS"] = $id_op; // AQUI GUARDAMOS EL ID OPERACION
            //Preguntar si existe Nota Automatica
            //Insertar Nota Automatica Respuesta a un Requerimiento
            $obj_na["TIPO"] = 1; // notas
            $resp = $this->_db->insert('fid_nota_req', $obj_na);
            //log_this( 'wwwwww.log', $this->_db->last_query() );
            //insertar notificacion de envio de nota
            $this->guardar_traza_nota_carp($resp, 'ENVIAR NOTA', 'ENVIO DE NOTA A DESTINATARIO', $remitente);

        endif;

        $rtn = array(
            "accion" => $acc,
            "result" => $obj
        );
//        print_r($rtn);die();
        return $rtn;
    }

    /*     * ***************************************************************** */
    /*     * ********************ESTADO ACEPTADO******************************** */
    /*     * ***************************************************************** */

    function sendreq_acep($obj, $adjuntos, $autor_req, $notificar_operaciones_carpeta_respondida = 0) {

        $iid = $obj["idreqh"];
        unset($obj["idreqh"]);
        //IDOPE, ASUNTO, DESC
        $fecha_actual = "";
        if (isset($obj['FCREA']) && $obj['FCREA'])
            $fecha_actual = deFecha_a_base($obj['FCREA']);
        $fecha_resp = "";
        if (isset($obj['FREC']) && $obj['FREC'])
            $fecha_resp = deFecha_a_base($obj['FREC']);

        $id_new = $iid;
        if ($iid == 0) { //agregar
            $obj_na = array();
            $obj['FOJAS'] = "";
            $obj['REMITENTE'] = $_SESSION["USERADM"];
            $obj['DESTINATARIO'] = "BENEFICIARIO";
            $obj['PROPIETARIO'] = "BENEFICIARIO";
            $obj['FCREA'] = $fecha_actual;
            $obj['FREC'] = $fecha_resp;
            $obj['FTRA'] = "";
            $obj['TIPO'] = "0";

            $resp = $this->_db->insert('fid_nota_req', $obj);
            $id_new = $resp;
            $obj['ID'] = $id_new;
            $acc = "add";

            if ($autor_req > 0) {
                $this->_db->select('CARTERADE,ID_ETAPA_ACTUAL,ID');
                $reg = $this->_db->get_tabla($this->_tablamod, "ID='" . $obj['ID_OPERACION'] . "'");
                if ($reg) {
                    $carterade = $reg[0]["CARTERADE"];
                }
                //obtener etapa origen
                $this->_db->select('ETAPA_ORIGEN');
                $regt = $this->_db->get_tabla("fid_traza", "ID_OPERACION='" . $obj['ID_OPERACION'] . "' AND ACTIVO='1'");
                $etapa_origen = 99;
                if ($regt) {
                    $etapa_origen = $regt[0]["ETAPA_ORIGEN"];
                }
                //insertar traza que servira para notificacion al jefe de op
                $arr_traza = array(
                    "ID_OPERACION" => $obj['ID_OPERACION'],
                    "ESTADO" => 9, //autorizacion de req
                    "CARTERADE" => $carterade,
                    "DESTINO" => $carterade,
                    "OBSERVACION" => 'AUTORIZACION DE REQUERIMIENTO',
                    "DESCRIPCION" => 'SE PIDE AUTORIZACION PARA REQUERIMIENTO',
                    "ETAPA" => $id_new, //aca uso este campo para guardar el id del req
                    "ETAPA_ORIGEN" => $etapa_origen,
                    "FECHA" => $fecha_actual,
                    "LEIDO" => 1,
                    "NOTIF" => 1,
                    "AUTOR_REQ" => $autor_req
                );
                $this->_db->insert('fid_traza', $arr_traza);
            }
        } else {
            $obj['FREC'] = $fecha_resp;
            unset($obj['FCREA']);

            $resp = $this->_db->update('fid_nota_req', $obj, "ID='" . $iid . "'");
            $obj_na = $obj;
            if ($notificar_operaciones_carpeta_respondida == 1) {
                $this->_db->select('CARTERADE,ID_ETAPA_ACTUAL,ID');
                $reg = $this->_db->get_tabla($this->_tablamod, "ID='" . $obj['ID_OPERACION'] . "'");
                if ($reg) {
                    $carterade = $reg[0]["CARTERADE"];
                }
                //obtener etapa origen
                $this->_db->select('ETAPA_ORIGEN');
                $regt = $this->_db->get_tabla("fid_traza", "ID_OPERACION='" . $obj['ID_OPERACION'] . "' AND ACTIVO='1'");
                $etapa_origen = 99;
                if ($regt) {
                    $etapa_origen = $regt[0]["ETAPA_ORIGEN"];
                }
                //insertar traza que servira para notificacion al jefe de op
                $arr_traza = array(
                    "ID_OPERACION" => $obj['ID_OPERACION'],
                    "ESTADO" => 10, //notificar al jefe de operaciones que se respondio su req
                    "CARTERADE" => $carterade,
                    "DESTINO" => $carterade,
                    "OBSERVACION" => 'AUTORIZACION DE REQUERIMIENTO', // usaremos el mismo asunto, pero en realidad no es autorizacion, sino una noftificacion al jefe de op
                    "DESCRIPCION" => 'SE NOTIFICA AL JEFE DE OPERACIONES QUE RESPONDIERON A SU REQUERIMIENTO',
                    "ETAPA" => $id_new, //aca uso este campo para guardar el id del req
                    "ETAPA_ORIGEN" => $etapa_origen,
                    "FECHA" => $fecha_actual,
                    "LEIDO" => 1,
                    "NOTIF" => 1,
                    "ACTIVO" => 0,
                    "AUTOR_REQ" => '0'
                );
                $this->_db->insert('fid_traza', $arr_traza);
            }

            $obj = $this->_db->get_tabla("fid_nota_req", "ID='" . $iid . "'");
            if ($obj) {
                $obj['FREC'] = $fecha_resp;
                $obj[0]['FREC'] = $fecha_resp;
            }
            $acc = "edit";
            //estado
            if ($obj[0]["ESTADO"] == 0)
                $estado = "Emitido";
            if ($obj[0]["ESTADO"] == 1)
                $estado = "Pendiente";
            elseif ($obj[0]["ESTADO"] == 2)
                $estado = "Enviado";
            elseif ($obj[0]["ESTADO"] == 3)
                $estado = "Respondido";
            elseif ($obj[0]["ESTADO"] == 4)
                $estado = "Aceptado";
            elseif ($obj[0]["ESTADO"] == 5)
                $estado = "Rechazado";
            $obj[0]["ESTADO"] = $estado;
        }

        if (isset($obj['FCREA']))
            $fcrea = strtotime($obj['FCREA']) == false ? '-' : date("d/m/Y", strtotime($obj['FCREA']));
        else
            $fcrea = "";

        if (isset($obj['FREC']))
            $frec = strtotime($obj['FREC']) == false ? '-' : date("d/m/Y", strtotime($obj['FREC']));
        else
            $frec = "";

        if (isset($obj['FTRA']))
            $ftra = strtotime($obj['FTRA']) == false ? '-' : date("d/m/Y", strtotime($obj['FTRA']));
        else
            $ftra = "";
        $obj['FCREA'] = $fcrea;
        $obj['FREC'] = $frec;
        $obj['FTRA'] = $ftra;

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

        if (count($obj_na) > 0 && $obj_na["estado"] == 3):
            $reg = $this->_db->get_tabla('fid_nota_req', "ID='" . $id_new . "'");

            $remitente = 0;
            if ($reg):
                $remitente = $reg[0]["REMITENTE"];
                $id_op = $reg[0]["ID_OPERACION"];
            endif;

            $obj_na["ASUNTO"] = "Respuesta a Requerimiento " . $id_new;
            $obj_na["PROPIETARIO"] = $_SESSION["USERADM"];
            $obj_na["ID_OPERACION"] = "0";
            $obj_na["REMITENTE"] = $_SESSION["USER_NA"];
            $obj_na["ENVIADOA"] = $remitente;

            $obj_na["FOJAS"] = $id_op; // AQUI GUARDAMOS EL ID OPERACION
            //Preguntar si existe Nota Automatica
            //Insertar Nota Automatica Respuesta a un Requerimiento
            $obj_na["TIPO"] = 1; // notas
            $resp = $this->_db->insert('fid_nota_req', $obj_na);
            //log_this( 'wwwwww.log', $this->_db->last_query() );
            //insertar notificacion de envio de nota
            $this->guardar_traza_nota_carp($resp, 'REQUERIMIENTO ACEPTADO', 'ENVIO DE REQUERIMIENTO', $remitente);

        endif;

        $rtn = array(
            "accion" => $acc,
            "result" => $obj
        );
//        print_r($rtn);die();
        return $rtn;
    }

    /*     * ***************************************************************** */
    /*     * ********************FIN ESTADO ACEPTADO************************** */
    /*     * ***************************************************************** */

    /*     * ***************************************************************** */
    /*     * ********************ESTADO RECHAZADO******************************** */
    /*     * ***************************************************************** */

    function sendreq_rechaz($obj, $adjuntos, $autor_req, $notificar_operaciones_carpeta_respondida = 0) {

        $iid = $obj["idreqh"];
        unset($obj["idreqh"]);
        //IDOPE, ASUNTO, DESC
        $fecha_actual = "";
        if (isset($obj['FCREA']) && $obj['FCREA'])
            $fecha_actual = deFecha_a_base($obj['FCREA']);
        $fecha_resp = "";
        if (isset($obj['FREC']) && $obj['FREC'])
            $fecha_resp = deFecha_a_base($obj['FREC']);

        $id_new = $iid;
        if ($iid == 0) { //agregar
            $obj_na = array();
            $obj['FOJAS'] = "";
            $obj['REMITENTE'] = $_SESSION["USERADM"];
            $obj['DESTINATARIO'] = "BENEFICIARIO";
            $obj['PROPIETARIO'] = "BENEFICIARIO";
            $obj['FCREA'] = $fecha_actual;
            $obj['FREC'] = $fecha_resp;
            $obj['FTRA'] = "";
            $obj['TIPO'] = "0";

            $resp = $this->_db->insert('fid_nota_req', $obj);
            $id_new = $resp;
            $obj['ID'] = $id_new;
            $acc = "add";


            if ($autor_req > 0) {
                $this->_db->select('CARTERADE,ID_ETAPA_ACTUAL,ID');
                $reg = $this->_db->get_tabla($this->_tablamod, "ID='" . $obj['ID_OPERACION'] . "'");
                if ($reg) {
                    $carterade = $reg[0]["CARTERADE"];
                }
                //obtener etapa origen
                $this->_db->select('ETAPA_ORIGEN');
                $regt = $this->_db->get_tabla("fid_traza", "ID_OPERACION='" . $obj['ID_OPERACION'] . "' AND ACTIVO='1'");
                $etapa_origen = 0;
                if ($regt) {
                    $etapa_origen = $regt[0]["ETAPA_ORIGEN"];
                }
                //insertar traza que servira para notificacion al jefe de op
                $arr_traza = array(
                    "ID_OPERACION" => $obj['ID_OPERACION'],
                    "ESTADO" => 9, //autorizacion de req
                    "CARTERADE" => $carterade,
                    "DESTINO" => $carterade,
                    "OBSERVACION" => 'AUTORIZACION DE REQUERIMIENTO',
                    "DESCRIPCION" => 'SE PIDE AUTORIZACION PARA REQUERIMIENTO',
                    "ETAPA" => $id_new, //aca uso este campo para guardar el id del req
                    "ETAPA_ORIGEN" => $etapa_origen,
                    "FECHA" => $fecha_actual,
                    "LEIDO" => 1,
                    "NOTIF" => 1,
                    "AUTOR_REQ" => $autor_req
                );

                $this->_db->insert('fid_traza', $arr_traza);
            }
        } else {
            $obj['FREC'] = $fecha_resp;
            unset($obj['FCREA']);

            $resp = $this->_db->update('fid_nota_req', $obj, "ID='" . $iid . "'");
            $obj_na = $obj;

            if ($notificar_operaciones_carpeta_respondida == 1) {
                $this->_db->select('CARTERADE,ID_ETAPA_ACTUAL,ID');
                $reg = $this->_db->get_tabla($this->_tablamod, "ID='" . $obj['ID_OPERACION'] . "'");
                if ($reg) {
                    $carterade = $reg[0]["CARTERADE"];
                }
                //obtener etapa origen
                $this->_db->select('ETAPA_ORIGEN');
                $regt = $this->_db->get_tabla("fid_traza", "ID_OPERACION='" . $obj['ID_OPERACION'] . "' AND ACTIVO='1'");
                $etapa_origen = 0;
//                if ($regt){
//                    $etapa_origen = $regt[0]["ETAPA_ORIGEN"];
//                }
                //insertar traza que servira para notificacion al jefe de op
                $arr_traza = array(
                    "ID_OPERACION" => $obj['ID_OPERACION'],
                    "ESTADO" => 9, //notificar al jefe de operaciones que se respondio su req
                    "CARTERADE" => $carterade,
                    "DESTINO" => $carterade,
                    "OBSERVACION" => 'RECHAZO DE REQUERIMIENTO', // usaremos el mismo asunto, pero en realidad no es autorizacion, sino una noftificacion al jefe de op
                    "DESCRIPCION" => 'SE NOTIFICA AL COORDINADOR DE OPERACIONES QUE RECHAZARON A SU REQUERIMIENTO',
                    "ETAPA" => $id_new, //aca uso este campo para guardar el id del req
                    "ETAPA_ORIGEN" => $etapa_origen,
                    "FECHA" => $fecha_actual,
                    "LEIDO" => 1,
                    "NOTIF" => 1,
                    "ACTIVO" => 0,
                    "AUTOR_REQ" => '0'
                );
                $this->_db->insert('fid_traza', $arr_traza);
            }

            $obj = $this->_db->get_tabla("fid_nota_req", "ID='" . $iid . "'");
            if ($obj) {
                $obj['FREC'] = $fecha_resp;
                $obj[0]['FREC'] = $fecha_resp;
            }

            $acc = "edit";
            //estado
            if ($obj[0]["ESTADO"] == 0)
                $estado = "Emitido";
            if ($obj[0]["ESTADO"] == 1)
                $estado = "Pendiente";
            elseif ($obj[0]["ESTADO"] == 2)
                $estado = "Enviado";
            elseif ($obj[0]["ESTADO"] == 3)
                $estado = "Respondido";
            elseif ($obj[0]["ESTADO"] == 4)
                $estado = "Aceptado";
            elseif ($obj[0]["ESTADO"] == 5)
                $estado = "Rechazado";
            $obj[0]["ESTADO"] = $estado;
        }

        if (isset($obj['FCREA']))
            $fcrea = strtotime($obj['FCREA']) == false ? '-' : date("d/m/Y", strtotime($obj['FCREA']));
        else
            $fcrea = "";

        if (isset($obj['FREC']))
            $frec = strtotime($obj['FREC']) == false ? '-' : date("d/m/Y", strtotime($obj['FREC']));
        else
            $frec = "";

        if (isset($obj['FTRA']))
            $ftra = strtotime($obj['FTRA']) == false ? '-' : date("d/m/Y", strtotime($obj['FTRA']));
        else
            $ftra = "";
        $obj['FCREA'] = $fcrea;
        $obj['FREC'] = $frec;
        $obj['FTRA'] = $ftra;

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
        if (count($obj_na) > 0 && $obj_na["estado"] == 3):
            $reg = $this->_db->get_tabla('fid_nota_req', "ID='" . $id_new . "'");

            $remitente = 0;
            if ($reg):
                $remitente = $reg[0]["REMITENTE"];
                $id_op = $reg[0]["ID_OPERACION"];
            endif;

            $obj_na["ASUNTO"] = "Respuesta a Requerimiento " . $id_new;
            $obj_na["PROPIETARIO"] = $_SESSION["USERADM"];
            $obj_na["ID_OPERACION"] = "0";
            $obj_na["REMITENTE"] = $_SESSION["USER_NA"];
            $obj_na["ENVIADOA"] = $remitente;

//        echo "HASTA LA VISTA 7";die();
            $obj_na["FOJAS"] = $id_op; // AQUI GUARDAMOS EL ID OPERACION
            //Preguntar si existe Requerimientos 
            //Insertar Requerimiento
            $obj_na["TIPO"] = 0; // requerimientos
            $resp = $this->_db->insert('fid_nota_req', $obj_na);
            //log_this( 'wwwwww.log', $this->_db->last_query() );
            //insertar notificacion de envio de nota
            $this->guardar_traza_nota_carp($resp, 'ENVIAR REQUERIMIENTO', 'ENVIO DE REQUERIMIENTO A DESTINATARIO', $remitente);

        endif;

        $rtn = array(
            "accion" => $acc,
            "result" => $obj
        );
//        print_r($rtn);die();
        return $rtn;
    }

    /*     * ***************************************************************** */
    /*     * ********************FIN ESTADO RECHAZADO************************** */
    /*     * ***************************************************************** */

    /*     * ***************************************************************** */
    /*     * *******************Obtener datos mail del coordinador************ */
    /*     * ***************************************************************** */

    function obtener_datos_mail($valorid, $valorOperacion) {
        //Se obtienen los datos del coordinador      
        $this->_db->select('*');
        $reg = $this->_db->get_tabla($this->_tablamodtra, "id_operacion='" . $valorOperacion . "' AND etapa='" . $valorid . "'");
        $valor_a_comparar = $reg[0]["CARTERADE"];
        $this->_db->select('nombre,apellido,email');
        $us = $this->_db->get_tabla($this->_tablamodusu, "ID='" . $reg[0]['CARTERADE'] . "'");
        return $us;
    }
    function obtener_datos_cliente($valorOperacion){
        //Se obtienen los datos del coordinador      
        $this->_db->select('*');
        $cli = $this->_db->get_tabla($this->_tablamodusucli, "id_operacion='".$valorOperacion. "'");
        $this->_db->select('*');
        $cliDatos = $this->_db->get_tabla($this->_tablamodcli, "id='".$valorOperacion. "'");
        return $cliDatos;
    }
    function actualizar_valores($id_oper_sem){
        //Se obtienen los datos del coordinador      
        $lomismo = $id_oper_sem;
//        $this->_db->select('*');
//        $cli = $this->_db->get_tabla($this->_tablamodusucli, "id_operacion='".$valorOperacion. "'");
//        $this->_db->select('*');
//        $cliDatos = $this->_db->get_tabla($this->_tablamodcli, "id='".$valorOperacion. "'");
        return $lomismo;
    }

    /*     * ***************************************************************** */
    /*     * *******************fin obtener datos mail del coordinador******** */
    /*     * ***************************************************************** */

    function guardar_traza_nota_carp($id_req_nota, $observacion, $descripcion, $destinatario = 0) {

        $fecha_actual = date("Y-m-d H:i:s");
        /*
          $propietario = isset($_POST['PROPIETARIO'])?$_POST['PROPIETARIO']:0;
          $tid = isset($_POST['tid'])?$_POST['tid']:0;
         */

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
        $this->actualizar_notas_activo_cero($id_req_nota);
        $obj = $this->guardar_traza_nota($arr_traza);
        //cambiar leido de la traza ex activa
        //$this->cambiar_estado_antigua_traza_nota($tid);
        //echo $obj;
    }

    function update_req($idr, $obj) {
        $rtn = $this->_db->update('fid_nota_req', $obj, "ID='" . $idr . "'");
        //log_this('eeeeee.log', $this->_db->last_query());
        return $rtn;
    }

    function sendobj($obj, $checklist, $checklist_sel, $postulantes, $arr_obs, $arr_chk, $id_etapa_actual, $carterade, $adjuntos, $arr_infoadd, $arr_itemscom, $checkedItems_deudas) {

        $fecha_actual = date("Y-m-d H:i:s");

        if ($arr_obs)
            $arr_obs = $arr_obs[0];
        if ($arr_chk)
            $arr_chk = $arr_chk[0];

        $iid = $obj["id"];
        unset($obj["id"]);

        $id_new = $iid;
        $obj['UPDATEDON'] = $fecha_actual;
        //$obj['ID_PROCESO'] = 1;
        //$obj['USUARIO'] = $_SESSION["USERADM"];

        $new_arr_obs = array();
        $new_arr_chk = array();
        //si usuario actual es el CARTERADE
        $new_arr_obs["obs_checlist"] = -1;
        $new_arr_obs["obs_cinicial"] = -1;
        $new_arr_obs["obs_patrimoniales"] = -1;
        $new_arr_obs["obs_legales"] = -1;
        $new_arr_obs["obs_tecnico"] = -1;
        $new_arr_obs["obs_elevacion"] = -1;
        $new_arr_obs["obs_comite"] = -1;
        $new_arr_obs["obs_contrato"] = -1;

        $new_arr_chk["chk_checklist"] = -1;
        $new_arr_chk["chk_cinicial"] = -1;
        $new_arr_chk["chk_patrimoniales"] = -1;
        $new_arr_chk["chk_legales"] = -1;
        $new_arr_chk["chk_tecnicos"] = -1;
        $new_arr_chk["chk_elevacion"] = -1;
        $new_arr_chk["chk_comite"] = -1;
        $new_arr_chk["chk_rcontrato"] = -1;
        $new_arr_chk["chk_fcontrato"] = -1;
        $new_arr_chk["chk_altacredito"] = -1;

        if ($id_etapa_actual == '1' && ($carterade == $_SESSION["USERADM"] || ($carterade == 0 && $_SESSION["USER_ROL"] == 9))) {
            $new_arr_obs["obs_checlist"] = $arr_obs["obs_checlist"];
            $new_arr_chk["chk_checklist"] = $arr_chk["chk_checklist"];
        }

        if ($id_etapa_actual == '2' && $carterade == $_SESSION["USERADM"]) {
            $new_arr_obs["obs_cinicial"] = $arr_obs["obs_cinicial"];
            $new_arr_chk["chk_cinicial"] = $arr_chk["chk_cinicial"];
        }

        if ($id_etapa_actual == '3' && $carterade == $_SESSION["USERADM"]) {
            $new_arr_chk["chk_legales"] = $arr_chk["chk_legales"];
            $new_arr_chk["chk_patrimoniales"] = $arr_chk["chk_patrimoniales"];
            $new_arr_chk["chk_tecnicos"] = $arr_chk["chk_tecnicos"];
        }

        if (($id_etapa_actual == '3' || $id_etapa_actual == '4') && $carterade == $_SESSION["USERADM"]) {
            $new_arr_obs["obs_legales"] = $arr_obs["obs_legales"];
        }
        if (($id_etapa_actual == '3' || $id_etapa_actual == '5') && $carterade == $_SESSION["USERADM"]) {
            $new_arr_obs["obs_patrimoniales"] = $arr_obs["obs_patrimoniales"];
        }
        if (($id_etapa_actual == '3' || $id_etapa_actual == '6') && $carterade == $_SESSION["USERADM"]) {
            $new_arr_obs["obs_tecnico"] = $arr_obs["obs_tecnico"];
        }
        if ($id_etapa_actual == '8' && $carterade == $_SESSION["USERADM"]) {
            $new_arr_obs["obs_elevacion"] = $arr_obs["obs_elevacion"];
            $new_arr_chk["chk_elevacion"] = $arr_chk["chk_elevacion"];
        }
        if ($id_etapa_actual == '9' && $carterade == $_SESSION["USERADM"]) {
            $new_arr_obs["obs_comite"] = $arr_obs["obs_comite"];
            $new_arr_chk["chk_comite"] = $arr_chk["chk_comite"];
        }
        if ($id_etapa_actual == '10' && $carterade == $_SESSION["USERADM"]) {
            $new_arr_chk["chk_rcontrato"] = $arr_chk["chk_rcontrato"];
        }
        if ($id_etapa_actual == '11' && $carterade == $_SESSION["USERADM"]) {
            $new_arr_obs["obs_contrato"] = $arr_obs["obs_contrato"];
            $new_arr_chk["chk_fcontrato"] = $arr_chk["chk_fcontrato"];
        }
        if ($id_etapa_actual == '12' && $carterade == $_SESSION["USERADM"] && $_SESSION["USER_ROL"] == 20) {
            $new_arr_chk["chk_altacredito"] = $arr_chk["chk_altacredito"];
        }
         
        if ($iid == 0)://agregar
            $obj['CREATEDON'] = $obj['UPDATEDON'];
            $obj['CARTERADE'] = $carterade;

            $this->_db->select("ID");
            $this->_db->order_by("ID", "asc");
            $this->_db->limit(0, 1);
            $id_primera_etapa = $this->_db->get_tabla("fid_etapas", false);
            if ($id_primera_etapa) {
                $id_primera_etapa = $id_primera_etapa[0]["ID"];
            } else {
                $id_primera_etapa = 0;
            }
            $this->_db->reset();
            $obj['ID_ETAPA_ACTUAL'] = $id_primera_etapa;

            //si esta todo chekeado, setear encartera de = mesa entrada y actualizar fecha
            //$resp = $this->_db->insert( 'fid_operaciones', $obj );
            if ($arr_chk["chk_checklist"] == 1) {
                
            } else {
                $obj['CARTERADE'] = "";
            }
            $resp = $this->_db->insert('fid_operaciones', $obj);
            //sino, guardar con cartera de en vacio

            $obj_operatoria = $this->_db->get_tabla('fid_operatorias', "ID='" . $obj["ID_OPERATORIA"] . "'");
            $obj_rtn = $this->_db->get_tabla('fid_operaciones', "ID='" . $resp . "'");
            $obj_rtn[0]["obj_operatoria"] = $obj_operatoria[0];
            $obj_rtn = $obj_rtn[0];

            $acc = "add";
            $id_new = $resp;
            $this->procesar_etapas($id_new, $arr_obs, $arr_chk, $modo = 'agregar', $obj['ID_PROCESO']);

            $activo_traza = 1;
            if ($_SESSION["USER_ROL"] == 9) { //mesa de entrada
                //$activo_traza = 0;
            }

            //traza inicial
            $arr_traza = array(
                "ID_OPERACION" => $id_new,
                "ESTADO" => 1,
                "CARTERADE" => $_SESSION["USERADM"],
                "DESTINO" => '',
                "OBSERVACION" => 'CREACION',
                "DESCRIPCION" => 'CARPETA CREADA',
                "ETAPA" => '1',
                "ETAPA_REAL" => '1',
                "FECHA" => $fecha_actual,
                "ACTIVO" => $activo_traza,
                "ETAPA_ORIGEN" => 0
            );
            
            $this->_db->insert('fid_traza', $arr_traza);

             $id_traza = $this->_db->query("select ID from fid_traza WHERE id_operacion='".$arr_traza['ID_OPERACION']. "' order by id DESC LIMIT 1");
//             $id_traza = $this->_db->select("ID");
//             $id_traza = $this->_db->order_by(" id DESC LIMIT 1");
//             $id_traza = $this->_db->get_tabla("fid_traza", "WHERE id_operacion='".$arr_traza['ID_OPERACION']. "'");
//            echo $id_traza[0]['ID'];die(" NUMERO Q NECESITO");
             /*
             * Aqui se guarda en la tabla fid_semaforo el comienzo de la etapa
             * con la fecha de carga y la fecha asignada para el aviso
             */
//            $fecha_aviso = strtotime ( '+24 hour' , strtotime ( $fecha_actual ) ) ;
//            $fecha_aviso = date ( 'Y-m-j H:i:s' , $fecha_aviso );
//            $arr_semaforo = array(
//                "ID_CARPETA" => $id_new,
//                "ID_ETAPA" => 1,
//                "FECHA_CARGA" => $fecha_actual,
//                "MENSAJE_ALERTA" =>'La carpeta fue creada hace 24HS',
//                "FECHA_AVISO" => $fecha_aviso,
//                "ID_TRAZA"=>  $id_traza[0]['ID'],
//                "HAB"=>1
//                );
////                print_r($arr_semaforo);die();
//            $this->_db->insert('fid_semaforo', $arr_semaforo);
            /*
             * Fin de carga sobre semaforo
             */
                        
            
            if ($_SESSION["USER_ROL"] == 10) { //si usuario es jefe de op, actualizar etapa actual
                $this->_db->update("fid_operaciones", array("ID_ETAPA_ACTUAL" => "2"), 'ID="' . $id_new . '"');
            }

            //garantias en proceso 2
            //garantias en proceso 2

            if ($obj['ID_PROCESO'] == '2'):
                //actualizar las garantias con 9999
                $this->_db->update('fid_garantias', array("ID_OPERACION" => $id_new), "ID_OPERACION='9999'");
            endif;


        else://editar

            unset($obj["OBSERVACIONES"], $obj["FEC"], $obj["ESTADO"]);

            if ($new_arr_chk["chk_checklist"] == 1 && $_SESSION["USER_ROL"] == 9) {
                //ACTUALIZAR CREATE Y UPDATE
                $obj['UPDATEDON'] = $fecha_actual;
                $obj['CREATEDON'] = $obj['UPDATEDON'];
                $obj['CARTERADE'] = $_SESSION["USERADM"];
            }

            $resp = $this->_db->update($this->_tablamod, $obj, "id='" . $iid . "'");
             log_this('updateTablaOperaciones.log', $this->_db->last_query() );
        
            $acc = "edit";
            $obj_rtn = array();
            //log_this('qqqqqq.log', print_r($obj,1));
            $this->procesar_etapas($id_new, $new_arr_obs, $new_arr_chk, $modo = 'editar', $obj['ID_PROCESO']);
        endif;

        if ($id_etapa_actual == '1' && ($carterade == $_SESSION["USERADM"] || $carterade == '0')) {
            //delete
            $this->_db->delete("fid_operacion_checklist", "ID_OPERACION='" . $id_new . "'");
            if ($checklist):
                foreach ($checklist as $key => $value):
                    $this->_db->insert('fid_operacion_checklist', array("ID_OPERACION" => $id_new, "ID_CHECKLIST" => $value, "ESTADO" => "0", "USUARIO" => "fpretell", "CREATEDON" => $obj['UPDATEDON'], "UPDATEDON" => $obj['UPDATEDON']));
                    //log_this('qqqqqq.log', $this->_db->last_query() );
                endforeach;
            endif;
            //marcar las seleccionadas
            if ($checklist_sel):
                foreach ($checklist_sel as $key => $value):
                    $this->_db->update('fid_operacion_checklist', array("ESTADO" => "1"), "ID_OPERACION='" . $id_new . "' AND ID_CHECKLIST='" . $value . "'");
                    //log_this('qqqqqq.log', $this->_db->last_query() );
                endforeach;
            endif;
        }
        //log_this('qqqqqq.log', '////////////////////////////////////////////////////////////' );
        //deudas
        if ($checkedItems_deudas):
            $this->_db->update('fid_operaciones', array("DEUDA_FYTC" => 0, "DEUDA_MF" => 0), "ID='" . $id_new . "'");
            foreach ($checkedItems_deudas as $key):
                if ($key == '1') {
                    $this->_db->update('fid_operaciones', array("DEUDA_FYTC" => 1), "ID='" . $id_new . "'");
                }
                if ($key == '2') {
                    $this->_db->update('fid_operaciones', array("DEUDA_MF" => 1), "ID='" . $id_new . "'");
                }
            endforeach;
        endif;

        //postulantes
        $this->_db->delete("fid_operacion_cliente", "ID_OPERACION='" . $id_new . "'");
        if ($postulantes):

            $cad_p = "";
            foreach ($postulantes as $key => $value):
                $this->_db->insert('fid_operacion_cliente', array("ID_OPERACION" => $id_new, "ID_CLIENTE" => $value));
                $this->_db->select('CUIT,RAZON_SOCIAL');
                $postu = $this->_db->get_tabla('fid_clientes', "ID='" . $value . "'");
                if ($postu) {
                    $cad_p .= $postu[0]["RAZON_SOCIAL"] . " - " . $postu[0]["CUIT"] . " , ";
                }
            endforeach;
            $cad_p = substr($cad_p, 0, -3);
            $this->_db->update('fid_operaciones', array("BENEF" => $cad_p), "ID='" . $id_new . "'");
        endif;

        if ($adjuntos):
            foreach ($adjuntos as $key => $value):
                //consultamos la semilla de la tabla fid_upload_etiqueta,
                $sem = isset($value['nombre_tmp']) ? $value['nombre_tmp'] : "";
                $etiq = $this->_db->get_tabla('fid_upload_etiqueta', "SEMILLA='" . $sem . "'");
                $etiketa = "";
                if ($etiq) {
                    $etiketa = $etiq[0]["ETIQUETA"];
                }

                $arr_ins = array(
                    "ID_OPERACION" => $id_new,
                    "NOMBRE" => PATH_OPERACIONES . $id_new . "/" . $value['nombre'],
                    "ID_USUARIO" => $_SESSION["USERADM"],
                    "ID_ETAPA" => $value['etapa'],
                    "CREATEDON" => "[NOW()]",
                    "DESCRIPCION" => $etiketa
                );
                $this->_db->insert('fid_operacion_adjunto', $arr_ins);
                //mover aarchivo
                $origen = $value['nombre_tmp'];
                $destino = PATH_OPERACIONES . $id_new . "/" . $value['nombre'];
                mover($origen, $destino);
            endforeach;
        endif;

        $comite_macta = 0;
        if (( $id_etapa_actual == '9' || $id_etapa_actual == '10' || $id_etapa_actual == '11') && $carterade == $_SESSION["USERADM"]) {
            if ($arr_infoadd) {
                //delete
                $this->_db->delete("fid_operacion_infoadicional", "ID_OPERACION='" . $id_new . "' AND ETAPA='" . $id_etapa_actual . "'");
                foreach ($arr_infoadd as $key => $value):
                    if ($value["etapa"] == $id_etapa_actual) {
                        $arr_ins = array(
                            "ETAPA" => $value["etapa"],
                            "ID_OPERACION" => $id_new,
                            "NOMBRE" => $value["nombre"],
                            "VALOR" => $value["valor"]
                        );
                        $this->_db->insert('fid_operacion_infoadicional', $arr_ins);
                        if ($value["nombre"] == 'comite_macta') {
                            $comite_macta = $value["valor"];
                        }
                    }
                endforeach;
            }
        }


        $cambiar_estado_a_contrato = 0;

        if (($id_etapa_actual == '9' && $obj['ID_ESTADO'] == 5 && $obj['ID_PROCESO'] == '2')) {

            $cambiar_estado_a_contrato = 1;
            /*

              //preguntar por garantias
              $swg = -99;
              $swg1 = 0;
              $this->_db->select('count(*) as cont');
              $rtn = $this->_db->get_tabla("fid_garantias","ID_OPERACION='".$id_new."' and ID_ESTADO='1'");
              if ($rtn){
              if ($rtn[0]["cont"]==0){
              $swg = 0;   // no tiene garantias en evaluacion
              }else{
              $swg = -1;  // si tiene garantias en evaluacion
              }
              }else{
              $swg = -2; //no hay garantias
              }

              // no tiene garantias en evaluacion, devolvemos suma de las garantias aceptadas
              $this->_db->select('sum(VALOR_GARANTIA) as suma');
              $rtn1 = $this->_db->get_tabla("fid_garantias","ID_OPERACION='".$id_new."' and ID_ESTADO='5'");
              if ($rtn1){
              $dat_sw_gar =  $rtn1[0]['suma'];
              }

              //monto de
              //if (maprobado>dat_sw_gar){
              if ($comite_macta>$dat_sw_gar){
              $swg1=1;
              }

              if ($swg==0 && $swg1==0){
              $cambiar_estado_a_contrato = 1;
              }
             */
        }


        if (($id_etapa_actual == '9' && $obj['ID_ESTADO'] == 5)) {
            //preguntar por garantias
            $swg = -99;
            $swg1 = 0;
            $this->_db->select('count(*) as cont');
            $rtn = $this->_db->get_tabla("fid_garantias", "ID_OPERACION='" . $id_new . "' and ID_ESTADO='1'");
            if ($rtn) {
                if ($rtn[0]["cont"] == 0) {
                    $swg = 0;   // no tiene garantias en evaluacion
                } else {
                    $swg = -1;  // si tiene garantias en evaluacion
                }
            } else {
                $swg = -2; //no hay garantias
            }

            // no tiene garantias en evaluacion, devolvemos suma de las garantias aceptadas
            $this->_db->select('sum(VALOR_GARANTIA) as suma');
            $rtn1 = $this->_db->get_tabla("fid_garantias", "ID_OPERACION='" . $id_new . "' and ID_ESTADO='5'");
            if ($rtn1) {
                $dat_sw_gar = $rtn1[0]['suma'];
            }

            //monto de 
            //if (maprobado>dat_sw_gar){
            if ($comite_macta > $dat_sw_gar) {
                $swg1 = 1;
            }

            if ($swg == 0 && $swg1 == 0) {
                $cambiar_estado_a_contrato = 1;
            }
        }

        $cambiar_estado_a_altacredito = 0;
        if (( isset($arr_chk["chk_fcontrato"]) && $obj['ID_PROCESO'] == '1' && $id_etapa_actual == '11' && $arr_chk["chk_fcontrato"] == 1)) {
            $cambiar_estado_a_altacredito = 1;
        }

        //proceso 2 // cambiar estado a Desembolsos Ac. (id=14)	
        $cambiar_estado_a_desembolsosAc = 0;
        if (( isset($arr_chk["chk_altacredito"]) && $obj['ID_PROCESO'] == '2' && $id_etapa_actual == '12' && $arr_chk["chk_altacredito"] == 1)) {
            $cambiar_estado_a_desembolsosAc = 1;
        }

        /*
          if ( (  isset($arr_chk["chk_fcontrato"]) && $obj['ID_PROCESO']=='2' && $id_etapa_actual=='11' && $arr_chk["chk_fcontrato"] == 1 ) ){
          $cambiar_estado_a_altacredito = 1;
          }
         */

        /*

          backup

          if ( ($id_etapa_actual=='9' or $id_etapa_actual=='10' or $id_etapa_actual=='11' or $id_etapa_actual=='13') && $carterade==$_SESSION["USERADM"]){
          if ( $arr_itemscom ){
          //delete
          $tipodel = 0;
          if ($id_etapa_actual==9 || $id_etapa_actual==11)
          $tipodel = 1;
          elseif($id_etapa_actual==10 || $id_etapa_actual==13)
          $tipodel = 2;

          if ($tipodel>0)
          $this->_db->delete( "fid_operacion_condicionesprevias", "ID_OPERACION='".$id_new."' AND TIPO='".$tipodel."'" );
          else
          $this->_db->delete( "fid_operacion_condicionesprevias", "ID_OPERACION='".$id_new."'" );

          foreach ($arr_itemscom as $key=>$value):
          $insertar=0;
          if ($value["tipo"]==1 && ($id_etapa_actual=='9' || $id_etapa_actual=='11') ){
          $insertar=1;
          }elseif($value["tipo"]==2 && ($id_etapa_actual=='10' || $id_etapa_actual=='13') ){
          $insertar=1;
          }
          if ($insertar==1){
          $arr_ins= array(
          "ID_OPERACION"=>$id_new,
          "TIPO"=>$value["tipo"],
          "VALOR"=>$value["label"],
          "ESTADO"=>$value["seleccionado"]
          );
          $this->_db->insert( 'fid_operacion_condicionesprevias', $arr_ins );
          //log_this('aaaaa.log',$this->_db->last_query());
          }

          endforeach;

          //preguntar si existe un previo

          $prev = $this->_db->get_tabla("fid_operacion_condicionesprevias","ID_OPERACION='".$id_new."' AND VALOR='Entrega de Original de Contrato a Legales'");
          if (!$prev){
          $arr_ins_orig= array(
          "ID_OPERACION"=>$id_new,
          "TIPO"=>'2',
          "VALOR"=>"Entrega de Original de Contrato a Legales",
          "ESTADO"=>'0'
          );
          $this->_db->insert( 'fid_operacion_condicionesprevias', $arr_ins_orig );
          //log_this('aaaaa.log',$this->_db->last_query());
          }


          }
          }
         */

        //log_this('aaaaa.log',$id_etapa_actual . '--' . $carterade . '--' . $_SESSION["USERADM"] );


        if (($id_etapa_actual == '10' || $id_etapa_actual == '9' || $id_etapa_actual == '13') && $carterade == $_SESSION["USERADM"]) {
            if ($arr_itemscom) {
                //delete
                $this->_db->delete("fid_operacion_condicionesprevias", "ID_OPERACION='" . $id_new . "'");



                foreach ($arr_itemscom as $key => $value):
                    $estado_n = $value["seleccionado"];
                    if ($id_etapa_actual == '10' && $value["tipo"] == '2') {// cuando estamos en conf contrato, solo debemos guardar cond de contrato
                        $estado_n = "0";
                    }
                    $arr_ins = array(
                        "ID_OPERACION" => $id_new,
                        "TIPO" => $value["tipo"],
                        "VALOR" => $value["label"],
                        "ESTADO" => $estado_n
                    );
                    $this->_db->insert('fid_operacion_condicionesprevias', $arr_ins);
                    //log_this('aaaaa.log',$this->_db->last_query());

                endforeach;

                //preguntar si existe un previo
                /*
                  $prev = $this->_db->get_tabla("fid_operacion_condicionesprevias","ID_OPERACION='".$id_new."' AND VALOR='Entrega de Original de Contrato a Legales'");
                  if (!$prev){
                  $arr_ins_orig= array(
                  "ID_OPERACION"=>$id_new,
                  "TIPO"=>'2',
                  "VALOR"=>"Entrega de Original de Contrato a Legales",
                  "ESTADO"=>'0'
                  );
                  $this->_db->insert( 'fid_operacion_condicionesprevias', $arr_ins_orig );
                  //log_this('aaaaa.log',$this->_db->last_query());
                  }
                 */
            }
        }


        if ($cambiar_estado_a_contrato == 1) {
            //actualizar operaciones
            $this->_db->update($this->_tablamod, array("ID_ETAPA_ACTUAL" => "10"), "ID='" . $id_new . "'");
            //actualizar operaciones etapas
            $this->_db->update("fid_operacion_etapas", array("ESTADO" => "1"), "ID_OPERACION='" . $id_new . "' AND ID_ETAPA='9'");
        }

        if ($cambiar_estado_a_altacredito == 1) {
            //actualizar operaciones
            $this->_db->update($this->_tablamod, array("ID_ETAPA_ACTUAL" => "12"), "ID='" . $id_new . "'");
            //actualizar operaciones etapas
            $this->_db->update("fid_operacion_etapas", array("ESTADO" => "1"), "ID_OPERACION='" . $id_new . "' AND ID_ETAPA='11'");
        }

        if ($cambiar_estado_a_desembolsosAc == 1) {
            //actualizar operaciones
            $this->_db->update($this->_tablamod, array("ID_ETAPA_ACTUAL" => "14"), "ID='" . $id_new . "'");
            //actualizar operaciones etapas
            $this->_db->update("fid_operacion_etapas", array("ESTADO" => "1"), "ID_OPERACION='" . $id_new . "' AND ID_ETAPA='11'");
        }


        $rtn = array(
            "accion" => $acc,
            "result" => $resp,
            "obj_rtn" => $obj_rtn
        );
        
        return $rtn;
    }

    function procesar_etapas($id_new, $arr_obs, $arr_chk, $modo = 'editar', $id_proceso) {

        //etapas
        $proceso = $id_proceso;
        if ($modo != 'editar')
            $this->_db->delete("fid_operacion_etapas", "ID_OPERACION='" . $id_new . "'");

        //obtener las etapas de este proceso
        $lst_procesos_etapas = $this->_db->get_tabla("fid_procesos_etapas", "ID_PROCESO='" . $proceso . "'");
        if ($lst_procesos_etapas) {
            foreach ($lst_procesos_etapas as $pet) {

                switch ($pet["ID_ETAPA"]) {
                    case "1":
                        $obs = isset($arr_obs["obs_checlist"]) ? $arr_obs["obs_checlist"] : '';
                        $id_chk = isset($arr_chk["chk_checklist"]) ? $arr_chk["chk_checklist"] : '';
                        break;
                    case "2":
                        $obs = isset($arr_obs["obs_cinicial"]) ? $arr_obs["obs_cinicial"] : '';
                        $id_chk = isset($arr_chk["chk_cinicial"]) ? $arr_chk["chk_cinicial"] : '';
                        break;
                    case "3":
                        $obs = "";
                        $id_chk = "0";
                        break;
                    case "4":
                        $obs = isset($arr_obs["obs_legales"]) ? $arr_obs["obs_legales"] : '';
                        $id_chk = isset($arr_chk["chk_legales"]) ? $arr_chk["chk_legales"] : '';
                        break;
                    case "5":
                        $obs = isset($arr_obs["obs_patrimoniales"]) ? $arr_obs["obs_patrimoniales"] : '';
                        $id_chk = isset($arr_chk["chk_patrimoniales"]) ? $arr_chk["chk_patrimoniales"] : '';
                        break;
                    case "6":
                        $obs = isset($arr_obs["obs_tecnico"]) ? $arr_obs["obs_tecnico"] : '';
                        $id_chk = isset($arr_chk["chk_tecnicos"]) ? $arr_chk["chk_tecnicos"] : '';
                        break;
                    case "7":
                        $obs = "";
                        $id_chk = "0";
                        break;
                    case "8":
                        $obs = isset($arr_obs["obs_elevacion"]) ? $arr_obs["obs_elevacion"] : '';
                        $id_chk = isset($arr_chk["chk_elevacion"]) ? $arr_chk["chk_elevacion"] : '';
                        break;
                    case "9":
                        $obs = isset($arr_obs["obs_comite"]) ? $arr_obs["obs_comite"] : '';
                        $id_chk = isset($arr_chk["chk_comite"]) ? $arr_chk["chk_comite"] : '';
                        break;
                    case "10":
                        $obs = "";
                        $id_chk = isset($arr_chk["chk_rcontrato"]) ? $arr_chk["chk_rcontrato"] : '';
                        break;
                    case "11":
                        $obs = isset($arr_obs["obs_contrato"]) ? $arr_obs["obs_contrato"] : '';
                        $id_chk = isset($arr_chk["chk_fcontrato"]) ? $arr_chk["chk_fcontrato"] : '';
                        break;
                    case "12":
                        $obs = "";
                        $id_chk = isset($arr_chk["chk_altacredito"]) ? $arr_chk["chk_altacredito"] : '';
                        break;
                    case "13":
                        $obs = "";
                        $id_chk = "";

                    default:
                        break;
                }
                if ($modo <> 'editar') {

                    $arr_obj = array(
                        "ID_OPERACION" => $id_new,
                        "ID_ETAPA" => $pet["ID_ETAPA"],
                        "ESTADO" => $id_chk,
                        "OBSERVACION" => $obs
                    );
                    $this->_db->insert('fid_operacion_etapas', $arr_obj);
                    //log_this('yyyy.log', $this->_db->last_query() );
                } else {
                    //editar
                    if ($obs <> -1) {
                        $arr_obj = array(
                            "OBSERVACION" => $obs
                        );
                        $this->_db->update('fid_operacion_etapas', $arr_obj, "ID_OPERACION='" . $id_new . "' AND ID_ETAPA='" . $pet["ID_ETAPA"] . "'");
                        //log_this("qqqqqq.log",$this->_db->last_query() );
                    }

                    if ($id_chk <> -1) {
                        $arr_obj = array(
                            "ESTADO" => $id_chk,
                        );
                        $this->_db->update('fid_operacion_etapas', $arr_obj, "ID_OPERACION='" . $id_new . "' AND ID_ETAPA='" . $pet["ID_ETAPA"] . "'");
                    }
                }
            }
        }
    }

    function delobj($id) {
        $this->_db->delete($this->_tablamod, "id =' " . $id . "'");
        //borrar fid_operacion_checklist
        $this->_db->delete("fid_operacion_checklist", "ID_OPERACION='" . $id . "'");

        //borrar fid_operacion_cliente
        $this->_db->delete("fid_operacion_cliente", "ID_OPERACION='" . $id . "'");

        //borrar fid_operacion_etapas
        $this->_db->delete("fid_operacion_etapas", "ID_OPERACION='" . $id . "'");

        //borrar traza
        $this->_db->delete("fid_traza", "ID_OPERACION='" . $id . "'");

        //borrar garantias
        $this->_db->delete("fid_garantias", "ID_OPERACION='" . $id . "'");

        //borrar condiciones previas
        $this->_db->delete("fid_operacion_condicionesprevias", "ID_OPERACION='" . $id . "'");

        //borrar info adicional (datos extra en cada uno de los formularios)
        $this->_db->delete("fid_operacion_infoadicional", "ID_OPERACION='" . $id . "'");

        //borrar solicitudes de desembolso
        $this->_db->delete("fid_sol_desembolso", "ID_OPERACION='" . $id . "'");


        //borrar desembolsos de credito
        $this->_db->delete("fid_creditos_desembolsos", "ID_CREDITO IN (SELECT ID FROM fid_creditos WHERE ID_OPERACION='')");

        //borrar solicitudes de alta credito
        $this->_db->delete("fid_creditos", "ID_OPERACION='" . $id . "'");

        //borrar fisicos de fid_nota_req_adjunto
        $this->_db->select("NOMBRE");
        $tmp = $this->_db->get_tabla("fid_nota_req_adjunto", "ID_NOTA_REQ IN (select id from fid_nota_req where ID_OPERACION='" . $id . "')");
        if ($tmp) {
            foreach ($tmp as $arch) {
                @unlink($arch["NOMBRE"]);
            }
            $partes_ruta = pathinfo($tmp[0]["NOMBRE"]);
            borrar_directorio($partes_ruta['dirname'], true);
        }

        //borrar adjuntos de nota requerimiento
        $this->_db->delete("fid_nota_req_adjunto", "ID_NOTA_REQ IN (select id from fid_nota_req where ID_OPERACION='" . $id . "')");

        //borrar nota requerimiento
        $this->_db->delete("fid_nota_req", "ID_OPERACION='" . $id . "'");

        //borrar adjuntos (tambien fisico)
        $lst_uploads = $this->get_arruploads($id);
        if ($lst_uploads) {
            foreach ($lst_uploads as $rsu) {
                @unlink($rsu["NOMBRE"]);
            }
            //borrar directorio
            $partes_ruta = pathinfo($lst_uploads[0]["NOMBRE"]);
            borrar_directorio($partes_ruta['dirname'], true);
        }
        $this->_db->delete("fid_operacion_adjunto", "ID_OPERACION='" . $id . "'");
    }

    function get_provincias() {
        $rtn = $this->_db->get_tabla("fid_provincias");
        return $rtn;
    }

    function cancelar_solicitud($idope) {
        $fecha_actual = date("Y-m-d H:i:s");
        //obtener cartera de
        $this->_db->select('CARTERADE,ID_ETAPA_ACTUAL,ID');
        $reg = $this->_db->get_tabla($this->_tablamod, "ID='" . $idope . "'");
        if ($reg) {
            $carterade = $reg[0]["CARTERADE"];
            //$this->_db->update( 'fid_traza', array("LEIDO"=>"0"), "ID='".$reg[0]["ID"]."'" );
        }

        //obtener etapa origen
        $this->_db->select('ETAPA_ORIGEN');
        $regt = $this->_db->get_tabla("fid_traza", "ID_OPERACION='" . $idope . "' AND ACTIVO='1'");
        $etapa_origen = 99;
        if ($regt) {
            $etapa_origen = $regt[0]["ETAPA_ORIGEN"];
        }

        $obj_ed = array(
            "ACTIVO" => "0"
        );
        $this->_db->update("fid_traza", $obj_ed, "ID_OPERACION='" . $idope . "'");
        //insertar en traza el registro de cancelado
        $arr_traza = array(
            "ID_OPERACION" => $idope,
            "ESTADO" => 4, //rechazado
            "CARTERADE" => $carterade,
            "DESTINO" => $carterade,
            "OBSERVACION" => 'RECHAZADA',
            "DESCRIPCION" => 'CARPETA RECHAZADA',
            "ETAPA" => $etapa_origen,
            "ETAPA_ORIGEN" => $etapa_origen,
            "FECHA" => $fecha_actual,
            "LEIDO" => 1
        );
        $this->_db->insert('fid_traza', $arr_traza);

        //actualizar la operacion (carterade y etapa actual )
        $obj_edo = array(
            "ID_ETAPA_ACTUAL" => $etapa_origen,
            "ENVIADOA" => '0'
        );
        $this->_db->update($this->_tablamod, $obj_edo, "ID='" . $idope . "'");

        //return $rtn;
    }

    function cancelar_autorizacion($idope) {
        $fecha_actual = date("Y-m-d H:i:s");
        //obtener cartera de
        $this->_db->select('CARTERADE,ID_ETAPA_ACTUAL,ID');
        $reg = $this->_db->get_tabla($this->_tablamod, "ID='" . $idope . "'");
        if ($reg) {
            $carterade = $reg[0]["CARTERADE"];
            $this->_db->update('fid_traza', array("LEIDO" => "0"), "ID='" . $reg[0]["ID"] . "'");
        }

        //obtener etapa origen
        $this->_db->select('ETAPA_ORIGEN,ID');
        $regt = $this->_db->get_tabla("fid_traza", "ID_OPERACION='" . $idope . "' AND ACTIVO='1'");
        $etapa_origen = 99;
        if ($regt) {
            $etapa_origen = $regt[0]["ETAPA_ORIGEN"];
            $idt = $regt[0]["ID"];
            //log_this( 'wwwwwww.log', print_r($regt,1) );
            //actualizar
            $this->_db->update('fid_traza', array("LEIDO" => "0"), "ID='" . $idt . "'");
            //log_this( 'wwwwwww.log', $this->_db->last_query() );
        }

        $obj_ed = array(
            "ACTIVO" => "0",
                //"LEIDO"=>"1"
        );
        $this->_db->update("fid_traza", $obj_ed, "ID_OPERACION='" . $idope . "'");
        //insertar en traza el registro de no autorizado
        $arr_traza = array(
            "ID_OPERACION" => $idope,
            "ESTADO" => 8, //no autorizado
            "CARTERADE" => $carterade,
            "DESTINO" => $carterade,
            "OBSERVACION" => 'NO AUTORIZADA',
            "DESCRIPCION" => 'CARPETA NO AUTORIZADA',
            "ETAPA" => $etapa_origen,
            "ETAPA_ORIGEN" => $etapa_origen,
            "FECHA" => $fecha_actual,
            "LEIDO" => 1
        );
        $this->_db->insert('fid_traza', $arr_traza);

        //actualizar la operacion (carterade y etapa actual )
        $obj_edo = array(
            "ID_ETAPA_ACTUAL" => $etapa_origen,
            "ENVIADOA" => '0'
        );
        $this->_db->update($this->_tablamod, $obj_edo, "ID='" . $idope . "'");

        //return $rtn;
    }

    function recuperar_carpeta($idope) {
        $fecha_actual = date("Y-m-d H:i:s");
        //obtener cartera de
        $this->_db->select('CARTERADE,ID_ETAPA_ACTUAL');
        $reg = $this->_db->get_tabla($this->_tablamod, "ID='" . $idope . "'");
        if ($reg) {
            $carterade = $reg[0]["CARTERADE"];
        }

        //obtener etapa origen
        $this->_db->select('ETAPA_ORIGEN');
        $regt = $this->_db->get_tabla("fid_traza", "ID_OPERACION='" . $idope . "' AND ACTIVO='1'");
        log_this('xxxxx2.log', $this->_db->last_query());
        $etapa_origen = 15;
        if ($regt) {
            $etapa_origen = $regt[0]["ETAPA_ORIGEN"];
        }

        $obj_ed = array(
            "ACTIVO" => "0",
            "LEIDO" => "0"
        );
        $this->_db->update("fid_traza", $obj_ed, "ID_OPERACION='" . $idope . "'");
        //insertar en traza el registro de cancelado
        $arr_traza = array(
            "ID_OPERACION" => $idope,
            "ESTADO" => 6, //recuperado
            "CARTERADE" => $carterade,
            "DESTINO" => $carterade,
            "OBSERVACION" => 'RECUPERADA',
            "DESCRIPCION" => 'CARPETA RECUPERADA',
            "ETAPA" => $etapa_origen,
            "ETAPA_ORIGEN" => $etapa_origen,
            "FECHA" => $fecha_actual,
            "LEIDO" => 0
        );
        $this->_db->insert('fid_traza', $arr_traza);

        //actualizar la operacion (carterade y etapa actual )
        $etapa_origen = 15;
        $obj_edo = array(
            "ID_ETAPA_ACTUAL" => $etapa_origen,
            "ENVIADOA" => '0'
        );
        $this->_db->update($this->_tablamod, $obj_edo, "ID='" . $idope . "'");

        //return $rtn;
    }

    function cancelar_nota($idnr, $contMotivo) {
        $fecha_actual = date("Y-m-d H:i:s");
        $enviadoa = 0;
        //obtener cartera de
        $this->_db->select('PROPIETARIO,ID, ENVIADOA');
        $reg = $this->_db->get_tabla('fid_nota_req', "ID='" . $idnr . "'");
        if ($reg) {
            $carterade = $reg[0]["PROPIETARIO"];
            $this->_db->update('fid_traza', array("LEIDO" => "0"), "NOTA='" . $reg[0]["ID"] . "'");
            $this->_db->update('fid_nota_req', array("ENVIADOA" => "0"), "ID='" . $idnr . "'");
        }

        $enviadoa = $reg[0]["ENVIADOA"];
        $this->_db->select('NOMBRE,APELLIDO');
        $ususend = $this->_db->get_tabla('fid_usuarios', "ID='" . $enviadoa . "'");


        $obj_ed = array(
            "ACTIVO" => "0"
        );
        $this->_db->update("fid_traza", $obj_ed, "NOTA='" . $idnr . "'");
        //insertar en traza el registro de cancelado
        $arr_traza = array(
            "ID_OPERACION" => 0,
            "ESTADO" => 4, //rechazado
            "CARTERADE" => $carterade,
            "DESTINO" => $carterade,
            "OBSERVACION" => 'RECHAZADA',
            "DESCRIPCION" => 'NOTA RECHAZADA POR ' . $ususend[0]['NOMBRE'] . ' ' . $ususend[0]['APELLIDO'] . ': ' . $contMotivo,
            "ETAPA" => 0,
            "ETAPA_ORIGEN" => 0,
            "FECHA" => $fecha_actual,
            "NOTA" => $idnr,
            "NOTIF" => 1,
            "LEIDO" => 1
        );
        $this->_db->insert('fid_traza', $arr_traza);
        //return $rtn;
    }

    function cancelar_requerimiento($idnr, $contMotivo) {
        $fecha_actual = date("Y-m-d H:i:s");
        $enviadoa = 0;
        //obtener cartera de
        $this->_db->select('PROPIETARIO,ID, ENVIADOA');
        $reg = $this->_db->get_tabla('fid_nota_req', "ID='" . $idnr . "'");
        if ($reg) {
            $carterade = $reg[0]["PROPIETARIO"];
            $this->_db->update('fid_traza', array("LEIDO" => "0"), "NOTA='" . $reg[0]["ID"] . "'");
            $this->_db->update('fid_nota_req', array("ENVIADOA" => "0"), "ID='" . $idnr . "'");
        }

        $enviadoa = $reg[0]["ENVIADOA"];
        $this->_db->select('NOMBRE,APELLIDO');
        $ususend = $this->_db->get_tabla('fid_usuarios', "ID='" . $enviadoa . "'");


        $obj_ed = array(
            "ACTIVO" => "0"
        );
        $this->_db->update("fid_traza", $obj_ed, "NOTA='" . $idnr . "'");
        //insertar en traza el registro de cancelado
        $arr_traza = array(
            "ID_OPERACION" => 0,
            "ESTADO" => 4, //rechazado
            "CARTERADE" => $carterade,
            "DESTINO" => $carterade,
            "OBSERVACION" => 'RECHAZADA',
            "DESCRIPCION" => 'NOTA RECHAZADA POR ' . $ususend[0]['NOMBRE'] . ' ' . $ususend[0]['APELLIDO'] . ': ' . $contMotivo,
            "ETAPA" => 0,
            "ETAPA_ORIGEN" => 0,
            "FECHA" => $fecha_actual,
            "NOTA" => $idnr,
            "NOTIF" => 1,
            "LEIDO" => 1
        );
        $this->_db->insert('fid_traza', $arr_traza);
        //return $rtn;
    }

    function get_arruploads($id) {
        $this->_db->select("NOMBRE");
        $rtn = $this->_db->get_tabla("fid_operacion_adjunto", "ID_OPERACION='" . $id . "'");
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

    function insertar_traza_notif($arr_traza) {
        $rtn = $this->_db->insert('fid_traza', $arr_traza);
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }




    function actualizar_operacion($id, $arr, $arr_traza) {
        //get etapa_real
        
//        print_r($arr);die("DDDDDDDDDDDDDDDDDDDDDDDIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE");
        
        $tmp = $this->_db->get_tabla('fid_traza', "ID_OPERACION='" . $id . "' and activo='1'");

        //log_this('xxxxx11.log', print_r($tmp,1) );
        //log_this('xxxxx.log', $this->_db->last_query() );
        $etapa_real = 0;
        if ($tmp) {
            $etapa_real = $tmp[0]["ETAPA_REAL"];
        }
        $etapa_real++;
        //$arr_traza["ETAPA_REAL"] = $etapa_real;

        $rtn = $this->_db->update($this->_tablamod, $arr, "ID='" . $id . "'");
        //log_this('xxxxx.log', $this->_db->last_query() );
        //traza
        $obj_ed = array(
            "ACTIVO" => "0",
//            "DESTINO"=>$arr['ENVIADOA'],	
        );
        $this->_db->update("fid_traza", $obj_ed, "ID_OPERACION='" . $id . "'");
        
         /*
         * Aqui se guarda en la tabla fid_semaforo el comienzo de la etapa
         * con la fecha de carga y la fecha asignada para el aviso
         */
           $obj_semaforo = array(
//                "ID_NOTIFICAR"=>$arr['ENVIADOA'],
                "ID_NOTIFICAR"=>62,
                );

           $this->_db->update('fid_semaforo', $obj_semaforo,"id_carpeta='" . $id . "' AND id_etapa=1");
            /*
             * Fin de carga sobre semaforo
             */
        
        //log_this('xxxxx.log', $this->_db->last_query() );
        $this->_db->insert('fid_traza', $arr_traza);
        log_this('actualizarOperacionTraza.log', $this->_db->last_query() );
        return $rtn;
    }

    function actualizar_operacion_atras($id, $arr) {

        $rtn = $this->_db->update($this->_tablamod, $arr, "ID='" . $id . "'");
        //log_this('xxxxx.log', $this->_db->last_query() );
        //traza
        $obj_ed = array(
            "ACTIVO" => "0"
        );
        $this->_db->update("fid_traza", $obj_ed, "ID_OPERACION='" . $id . "'");
        //log_this('xxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function gettipobeneficiario() {
        $rtn = $this->_db->select("ID,TIPO");
        $rtn = $this->_db->get_tabla("fid_cliente_tipo");
        return $rtn;
    }

    function getenviar_a($arr_send) {
        $cad_where = "";
        $sw = 0;
        foreach ($arr_send as $send) {
            if (is_array($send)):
                $a = $send["area"];
                $p = $send["puesto"];
                $cad_where = "u.ID_AREA='" . $a . "' and u.ID_PUESTO='" . $p . "'";
                break;
            elseif (is_numeric($send)):
                $sw = 1;
                $cad_where .= $send . ",";
            endif;
        }

        if (strlen($cad_where) > 0 and $sw == 1) {
            $cad_where = substr($cad_where, 0, -1);
            $cad_where = "u.ID_AREA IN (" . $cad_where . ")";
        }

        $rtn = $this->_db->select("u.ID as IID,NOMBRE,APELLIDO,a.DENOMINACION AS AREA, p.DENOMINACION AS PUESTO, a.ETAPA AS ETAPA");
        $this->_db->join("fid_xpuestos p", "p.ID=u.ID_PUESTO");
        $this->_db->join("fid_xareas a", "a.ID=u.ID_AREA");
        $this->_db->order_by("AREA,PUESTO");
        $rtn = $this->_db->get_tabla("fid_usuarios u", $cad_where);
        //log_this('xxxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function getetapas_menor($etapa, $idope, $proceso = '1') {
        $this->_db->select("e.NOMBRE, etapa as ETAPA");
        $this->_db->join("fid_etapas e", "e.ID=t.ETAPA");
        $this->_db->group_by("etapa");
        $this->_db->order_by("etapa");
        $rtn = $this->_db->get_tabla("fid_traza t", " (OBSERVACION='ACEPTADO' OR OBSERVACION='CREACION') AND ID_OPERACION =  '" . $idope . "'");
        //log_this('xxxxxxx33.log', $this->_db->last_query() );
        return $rtn;
    }

    function getenviar_a1($arr_area, $puesto_in) {

        $cad_area = "";
        if (is_array($arr_area)) {
            foreach ($arr_area as $area) {
                $cad_area .= $area . ",";
            }
            $cad_area = substr($cad_area, 0, -1);
        } else if (is_numeric($arr_area)) {
            $cad_area = $arr_area;
        }

        if (is_array($puesto_in)) {
            //,7 as puesto_in1
            $i = 0;
            $cadsel1 = "";
            foreach ($puesto_in as $pin) {
                if ($i > 0) {
                    $cadsel1 .= $pin . " as puesto_in" . $i . ",";
                } else {
                    $cadsel1 .= $pin . " as puesto_in,";
                }
                //$this->_db->select('*,'.$puesto_in.' as puesto_in');
                $i++;
            }
            $cadsel1 = substr($cadsel1, 0, -1);
            $this->_db->select('*,' . $cadsel1);
        } else {
            if ($puesto_in) {
                $this->_db->select('*,' . $puesto_in . ' as puesto_in');
            }
        }
        //log_this('eeeeee.log', $cadsel1 );
        $rtn = $this->_db->get_tabla("fid_xareas u", "ID IN (" . $cad_area . ")" /* $cad_area */);
        //log_this('yyyyyy.log', $this->_db->last_query() );
        return $rtn;
    }
    
    
    
    
    
    function lanzar_alertas() {  
/*Busca fechas vencidas*/
        $fechaAct = date('Y-m-d H:i:s');
        $rtn = $this->_db->get_tabla("fid_semaforo u", "FECHA_AVISO<='".$fechaAct."'  AND HAB=1");
//        $rtn = $this->_db->get_tabla("fid_semaforo u", "FECHA_AVISO<='2015-10-31 17:30:45'  AND HAB=1");
//        print_r($rtn);die();
        return $rtn;
    }

    function ver_semaforo($operacion,$etapa_actualizar) {  
         $rtn = $this->_db->query("SELECT  *  FROM fid_semaforo WHERE id_carpeta='".$operacion."' "
                 . "AND id_etapa='".$etapa_actualizar."' ORDER BY id DESC");
           
         $arr_datos_sem = array(
                    "HAB"=>0
                );
         
         foreach ($rtn as $sem) {
             $this->_db->update('fid_semaforo', $arr_datos_sem,"ID='".$sem['ID']."'");
            }
//         return $rtn;
    }
    function repetir_alertas() {  
    /*Busca fechas vencidas*/
        $fechaAct = date('Y-m-d H:i:s');
        $rtn = $this->_db->get_tabla("fid_semaforo u", "FECHA_REPETIR<='".$fechaAct."'  "
                . "AND HAB=2");
//        $rtn = $this->_db->get_tabla("fid_semaforo u", "FECHA_REPETIR<='2015-11-01 17:30:45'  "
//                . "AND HAB=2");
        return $rtn;
    }
    
    function en_carterade($operacion){
        $rtn = $this->_db->query("SELECT CARTERADE FROM fid_traza "
                  . "WHERE id_operacion='".$operacion."' AND estado=3 AND etapa=2");
        return $rtn;
    }
    function traza_operacion($operacion){
        $rtn = $this->_db->query("select ID from fid_traza "
                     . "WHERE id_operacion='".$operacion. "' order by id DESC LIMIT 1");
        return $rtn;
    }
    function insertar_traza_semaforo($arr_semaforo){
        $rtn = $this->_db->insert('fid_semaforo', $arr_semaforo);
        
        return $rtn;
    }
    function insertar_traza_semaforo_dos($arr_semaforo_dos){
        $rtn = $this->_db->insert('fid_semaforo', $arr_semaforo_dos);
        
        return $rtn;
    }
    function avisar_gerente($operacion){
        $rtn = $this->_db->query("SELECT ID_NOTIFICAR FROM fid_semaforo "
                . "WHERE id_carpeta = '".$operacion."' AND id_etapa = 2");
        
        
        return $rtn;
    }
    function notificar_gerente(){
        $rtn = $this->_db->query("SELECT ID,USERNAME FROM fid_usuarios WHERE id_rol=19 AND id_area=4");
        return $rtn;
    }
    function obtener_nombre($id_obtener){
        $rtn = $this->_db->query("SELECT ID,USERNAME FROM fid_usuarios WHERE id='".$id_obtener."'");
        return $rtn;
    }
    function notificar_gerente_administracion(){
        $rtn = $this->_db->query("SELECT * FROM fid_usuarios WHERE id_rol=23 AND id_area=9");
        return $rtn;
    }
    
     function gerente_legales() {  
        $rtn = $this->_db->query("SELECT ID FROM fid_usuarios WHERE id_rol=12 AND id_area=5");
        return $rtn;
    }
     function jefe_administracion() {  
        $rtn = $this->_db->query("SELECT ID FROM fid_usuarios WHERE id_rol=23 AND id_area=9");
        return $rtn;
    }
    function jefe_operaciones($operacion) {
        $rtn = $this->_db->query("SELECT t.CARTERADE,u.USERNAME FROM fid_traza t 
                                JOIN fid_usuarios AS u ON t.carterade = u.id 
                                WHERE t.id_operacion='".$operacion."' AND u.id_rol=10 AND u.id_area=4  
                                GROUP BY t.CARTERADE
                                ");
        return $rtn;
    }

    function guardar_traza_alertas($obj) {
/* Las fechas que encuentas las trae y actualiza semaforo cambiando la fecha*/        
/* Se inserta una nueva traza con el parametro SEM en 1 asi se muestra la notificacion*/     
    $valor_id = $obj[0]['ID'];  
    $valores_insert = $obj[0]['ID_NOTIFICAR'];
    $fechaActual = date("Y-m-d H:i:s");
    $contador = 0;
    foreach ($obj as $value) {
        $contador = $contador + 1;
    $fecha_aviso = $value['FECHA_AVISO'];
    $fecha_repetir = strtotime ( '+24 hour' , strtotime ( $fecha_aviso ) ) ;
    $fecha_repetir = date ( 'Y-m-j H:i:s' , $fecha_repetir );
    
//    print_r($obj);die("VIENA LA FECHAAAAAAAAA");
    
    $arr_datos_traza = array(
                    "ID_OPERACION"=> $value['ID_CARPETA'],
                    "CARTERADE"=>$value['CARTERADE'],
                    "ETAPA"=>$value['ID_ETAPA'],
                    "DESTINO"=>$valores_insert,
                    "DESCRIPCION"=>$value['MENSAJE_ALERTA'],
                    "FECHA"=> $fechaActual,
                    "OBSERVACION"=> "AVISO",
                    "ESTADO"=>22
                 );
                $arr_datos_sem = array(
                    "HAB"=>2,
                    "FECHA_REPETIR"=>$fecha_repetir
                );
            $rtn = $this->_db->insert('fid_traza', $arr_datos_traza);
            $rtn = $this->_db->update('fid_semaforo', $arr_datos_sem,"ID='".$valor_id."'");
            }
            echo "CONTADOR = ".$contador;
}
    function guardar_traza_alertas_repetir($obj_repetir) {
    $valores_insert = $obj_repetir[0]['ID_NOTIFICAR'];
    $fechaActual = date("Y-m-d H:i:s");
    $fecha_repetir = strtotime ( '+24 hour' , strtotime ( $fechaActual ) ) ;
    $valor_id = $obj_repetir[0]['ID'];  
    
//    print_r($obj_repetir);die("REPETIRRRR");
    foreach ($obj_repetir as $value) {
    
    $fecha_aviso = $value['FECHA_REPETIR'];
    $fecha_repetir = strtotime ( '+24 hour' , strtotime ( $fecha_aviso ) ) ;
    $fecha_repetir = date ( 'Y-m-j H:i:s' , $fecha_repetir );
                
                
                
                $arr_datos_traza = array(
                    "ID_OPERACION"=> $value['ID_CARPETA'],
                    "CARTERADE"=>$value['CARTERADE'],
                    "ETAPA"=>$value['ID_ETAPA'],
                    "DESTINO"=>$valores_insert,
                    "DESCRIPCION"=>"Han transcurrido mas de 24hs a partir del tiempo limite.",
                    "FECHA"=> $fechaActual,
                    "OBSERVACION"=> "AVISO",
                    "ESTADO"=>22
                    );
                $arr_datos_sem = array(
                    "FECHA_REPETIR"=>$fecha_repetir
                );
//                echo $arr_datos_traza['ID_OPERACION']." - ";
//                print_r($arr_datos_sem);die("LPK");
            $rtn = $this->_db->insert('fid_traza', $arr_datos_traza);
            $rtn = $this->_db->update('fid_semaforo', $arr_datos_sem,"ID='".$value['ID']."'");
            }
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

        $rtn = $this->_db->select("u.ID as IID,NOMBRE,APELLIDO,a.DENOMINACION AS AREA, p.DENOMINACION AS PUESTO, a.ETAPA AS ETAPA, u.ID_PUESTO AS PUESTOID");
        $this->_db->join("fid_xpuestos p", "p.ID=u.ID_PUESTO");
        $this->_db->join("fid_xareas a", "a.ID=u.ID_AREA");
        $this->_db->order_by("AREA,PUESTO");
        $rtn = $this->_db->get_tabla("fid_usuarios u", $cad_where);
        //log_this('xxxxxx.log', $this->_db->last_query() );
        return $rtn;
    }

    function getetapas_menor2($etapa, $id_operacion, $proceso = '1') {

        $cad_where = "u.ID IN ( SELECT CARTERADE FROM fid_traza WHERE (OBSERVACION='ACEPTADO' OR OBSERVACION='CREACION') and ID_OPERACION='" . $id_operacion . "' AND ETAPA='" . $etapa . "' )";
        $rtn = $this->_db->select("u.ID as IID,NOMBRE,APELLIDO,a.DENOMINACION AS AREA, p.DENOMINACION AS PUESTO, a.ETAPA AS ETAPA, u.ID_PUESTO AS PUESTOID, " . $etapa . " as ETAPA_OLD");
        $this->_db->join("fid_xpuestos p", "p.ID=u.ID_PUESTO");
        $this->_db->join("fid_xareas a", "a.ID=u.ID_AREA");
        $this->_db->order_by("AREA,PUESTO");
        $rtn = $this->_db->get_tabla("fid_usuarios u", $cad_where);
        //log_this('yyyyyy.log', $this->_db->last_query() );
        return $rtn;
    }

    function getgerentes() {
        //devuelve los id de gerente de ope y gerente de finanza
        //gope
        $this->_db->select("ID,ID_AREA");
        $rtn = $this->_db->get_tabla("fid_usuarios", "(ID_AREA=4 AND ID_PUESTO=4) || (ID_AREA=9 AND ID_PUESTO=17)");

        if ($rtn) {
            $tmp = array();
            foreach ($rtn as $r) {
                if ($r["ID_AREA"] == '4') {
                    $tmp["GOPERACIONES"] = $r["ID"];
                } elseif ($r["ID_AREA"] == '9') {
                    $tmp["GFINANZAS"] = $r["ID"];
                }
            }
        }

        return $tmp;
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

    function get_arr_operatoria($idope) {
        $rtn = $this->_db->get_tabla("fid_operatorias", "ID='" . $idope . "'");
        return $rtn;
    }

    function getoperatoria($idf) {
        //ID_PROCESO
        $this->_db->select("o.ID,o.NOMBRE, o.TOPE_PESOS, o.ID_PROCESO");
        $this->_db->join("fid_operatorias o ", "fo.ID_OPERATORIA=o.ID");
        $rtn = $this->_db->get_tabla("fid_fideicomiso_operatorias fo", "ID_FIDEICOMISO='" . $idf . "'");
        return $rtn;
    }

    function get_garobjeto($idf) {
        $this->_db->select("go.ID,go.DESCRIPCION");
        $this->_db->join("fid_garantia_tipo gt ", "go.ID_GARANTIA_TIPO=gt.ID");
        $rtn = $this->_db->get_tabla("fid_garantia_tipo_objeto go", "ID_GARANTIA_TIPO='" . $idf . "'");
        //log_this('yyyyyy.log', $this->_db->last_query() );
        return $rtn;
    }

    function get_checklist_ope($id_operatoria = "") {
        $this->_db->select("c.ID AS ID, c.NOMBRE AS NOMBRE");
        $this->_db->join("fid_checklist c", "c.ID=oc.ID_OPERATORIA");
        $rtn = $this->_db->get_tabla("fid_operatoria_checklist oc", "oc.ID_OPERATORIA='" . $id_operatoria . "'");
        return $rtn;
    }

    function gettipo_garantia() {
        $rtn = $this->_db->get_tabla("fid_garantia_tipo");
        return $rtn;
    }

    function getestado_garantia() {
        $rtn = $this->_db->get_tabla("fid_garantia_estado");
        return $rtn;
    }

    function get_num_garantias($idope) {
        $this->_db->select('count(*) as cont');
        $rtn = $this->_db->get_tabla("fid_garantias", "ID_OPERACION='" . $idope . "'");
        //log_this('xxxxx.log', $this->_db->last_query() );
        if ($rtn)
            return $rtn[0]["cont"];
        else
            return false;
    }

    function get_gar_const($idope) {
        $this->_db->select('count(*) as cont');
        $rtn = $this->_db->get_tabla("fid_garantias", "ID_OPERACION='" . $idope . "' and ID_ESTADO='6'");
        //log_this('xxxxx.log', $this->_db->last_query() );
        if ($rtn)
            return $rtn[0]["cont"];
        else
            return false;
    }

    function get_gar_comite($idope) {

        $this->_db->select('count(*) as cont');
        $rtn = $this->_db->get_tabla("fid_garantias", "ID_OPERACION='" . $idope . "' and ID_ESTADO='1'");
        //log_this('xxxxx.log', $this->_db->last_query() );
        if ($rtn) {
            if ($rtn[0]["cont"] == 0) {
                // no tiene garantias en evaluacion, devolvemos suma de las garantias aceptadas
                $this->_db->select('sum(VALOR_GARANTIA) as suma');
                $rtn1 = $this->_db->get_tabla("fid_garantias", "ID_OPERACION='" . $idope . "' and ID_ESTADO='5'");
                //log_this('xxxxx.log', $this->_db->last_query() );
                if ($rtn1) {
                    return $rtn1[0]['suma'];
                }
                //return 0;   // no tiene garantias en evaluacion
            } else {
                return -1;  // si tiene garantias en evaluacion
            }
        } else {
            return -2; //no hay garantias
        }
    }

    function get_condicionesprevias($where = "") {
        $rtn = $this->_db->get_tabla("fid_operacion_condicionesprevias", $where);
        return $rtn;
    }

    function get_id_credito($idope) {
        $this->_db->select("ID");
        $rtn = $this->_db->get_tabla("fid_creditos", "ID_OPERACION='" . $idope . "'");
        if ($rtn)
            return $rtn[0]["ID"];
        return false;
    }

    function guardar_altacredito($obj, $desembolsos) {
        $iid = $obj["id"];
        unset($obj["id"]);

        if (isset($obj['CAPITAL_VTO']) && $obj['CAPITAL_VTO']) {
            $f1 = deFecha_a_base($obj['CAPITAL_VTO']);
            unset($obj['CAPITAL_VTO']);
            $obj['CAPITAL_VTO'] = $f1;
        }

        if (isset($obj['INTERES_VTO']) && $obj['INTERES_VTO']) {
            $f2 = deFecha_a_base($obj['INTERES_VTO']);
            unset($obj['INTERES_VTO']);
            $obj['INTERES_VTO'] = $f2;
        }

        if ($iid == 0)://agregar
            $acc = "add";

            $this->_db->limit(0, 1);
            $this->_db->order_by("id", "desc");
            $resp = $this->_db->get_tabla('fid_creditos');

            if ($resp) {
                $obj['ID'] = $resp[0]['ID'] + 1;
            } else {
                $obj['ID'] = 1200;
            }

            if (!isset($obj['PLAZO_COMPENSATORIO']) && !isset($obj['PLAZO_MORATORIO']) && !isset($obj['PLAZO_PUNITORIO'])) {
                $obj['PLAZO_COMPENSATORIO'] = 360;
                $obj['PLAZO_MORATORIO'] = 365;
                $obj['PLAZO_PUNITORIO'] = 365;
            }

            $resp = $this->_db->insert('fid_creditos', $obj);
            $iid = $resp;
        //log_this('xxxxx.log', $this->_db->last_query() );
        else://editar
            $resp = $this->_db->update('fid_creditos', $obj, "ID='" . $iid . "'");
            $acc = "edit";
        //log_this('xxxxx.log', $this->_db->last_query() );
        endif;

        //desembolsos
        $this->_db->delete("fid_creditos_desembolsos", "ID_CREDITO='" . $iid . "'");
        if ($desembolsos):

            list($d, $m, $y) = explode("/", date("d/m/Y"));
            $mk = mktime(0, 0, 0, $m, $d, $y);

            foreach ($desembolsos as $valor):
                $arr_ins = array(
                    "ID_CREDITO" => $iid,
                    "CUOTAS_RESTANTES" => "-1",
                    "FECHA" => $mk,
                    "MONTO" => $valor["monto"],
                    "ID_VARIACION" => "0",
                    "OBS" => $valor["obs"]
                );
                $this->_db->insert('fid_creditos_desembolsos', $arr_ins);
            endforeach;
        endif;

        $rtn = array(
            "accion" => $acc,
            "result" => $resp
        );
        return $rtn;
    }

    function guardar_soldesem($obj) {
        $iid = $obj["id"];
        unset($obj["id"]);

        $obj['CREATEDON'] = "NOW()";

        if ($iid == 0)://agregar
            $acc = "add";
            $resp = $this->_db->insert('fid_sol_desembolso', $obj);
            $iid = $resp;
        //log_this('xxxxx.log', 'ADD: '+$this->_db->last_query() );
        else://editar
            $resp = $this->_db->update('fid_sol_desembolso', $obj, "ID='" . $iid . "'");
            $acc = "edit";
        //log_this('xxxxx.log', 'EDIT: '+$this->_db->last_query() );
        endif;

        $rtn = array(
            "accion" => $acc,
            "result" => $resp
        );
        return $rtn;
    }

    function guardar_garantia($obj) {
        $iid = $obj["id"];
        unset($obj["id"]);

        if (isset($obj['FECHA_DESDE']) && $obj['FECHA_DESDE'])
            $obj['FECHA_DESDE'] = deFecha_a_base($obj['FECHA_DESDE']);

        if (isset($obj['FECHA_HASTA']) && $obj['FECHA_HASTA'])
            $obj['FECHA_HASTA'] = deFecha_a_base($obj['FECHA_HASTA']);

        if (isset($obj['TASA_F1']) && $obj['TASA_F1'])
            $obj['TASA_F1'] = deFecha_a_base($obj['TASA_F1']);

        if (isset($obj['TASA_F2']) && $obj['TASA_F2'])
            $obj['TASA_F2'] = deFecha_a_base($obj['TASA_F2']);

        if ($iid == 0)://agregar
            $acc = "add";
            $resp = $this->_db->insert('fid_garantias', $obj);
            $iid = $resp;
        //log_this('xxxxx.log', $this->_db->last_query() );
        else://editar
            $resp = $this->_db->update('fid_garantias', $obj, "ID='" . $iid . "'");
            $acc = "edit";
        //log_this('xxxxx.log', $this->_db->last_query() );
        endif;

        $rtn = array(
            "accion" => $acc,
            "result" => $resp
        );
        return $rtn;
    }

    function get_id_accion_pendiente($idope) {
        $this->_db->select("AUTOR,DESTINO");
        $destino = $this->_db->get_tabla("fid_traza", "ACTIVO='1' AND OBSERVACION='ENVIADO' AND ACTIVO='1' AND LEIDO='1' AND ID_OPERACION='" . $idope . "'");

        if ($destino) {
            if ($destino[0]["AUTOR"] > 0) {
                return $destino[0]["AUTOR"];
            } elseif ($destino[0]["DESTINO"] > 0) {
                return $destino[0]["DESTINO"];
            }
        }
        return 0;
    }

    function get_obj_cli($id) {

        if (!$id)
            return array();
        $this->_db->where("id = '" . $id . "'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
    }

    function get_contactos_cli($id) {
        $this->_db->where("ID_CLIENTE= '" . $id . "'");
        $rtn = $this->_db->get_tabla('fid_cliente_contactos');
        return $rtn;
    }

    function sendobjcli($obj) {

        $iid = $obj["id"];
        $contactos = isset($obj["contactos"]) ? $obj["contactos"] : array();

        unset($obj["id"]);
        unset($obj["contactos"]);

        if ($iid == 0)://agregar
            $resp = $this->_db->insert('fid_clientes', $obj);
            $acc = "add";
            $id_new = $resp;
        else://editar
            $resp = $this->_db->update('fid_clientes', $obj, "id='" . $iid . "'");
            $acc = "edit";
            $id_new = $iid;
        endif;

        //borrar previos
        $this->_db->delete('fid_cliente_contactos', "ID_CLIENTE='" . $id_new . "'");
        //contactos
        if ($contactos):
            foreach ($contactos as $cont):
                $obj = array(
                    "ID_CLIENTE" => $id_new,
                    "CONTACTO" => $cont['con'],
                    "TELEFONO" => $cont['tel'],
                    "CORREO" => $cont['ema']
                );
                $this->_db->insert('fid_cliente_contactos', $obj);
            endforeach;
        else:
            //si array es vacio, borrar el contacto principal
            $this->_db->update('fid_clientes', array("CONTACTO" => "", "CORREO" => "", "TELEFONO" => ""), "ID='" . $id_new . "'");
        endif;

        $rtn = array(
            "accion" => $acc,
            "result" => $resp
        );
        return $rtn;
    }

}


?>