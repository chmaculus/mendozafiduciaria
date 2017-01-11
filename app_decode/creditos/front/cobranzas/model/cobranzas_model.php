<?php

class cobranzas_model extends credito_model {

    protected $fd;

    public function init_log($fecha) {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL);

        $_SESSION ['FILE_SCRIPT'] = __FILE__;
        if (!($this->fd = fopen('log\facturacion_creditos_' . $fecha . '.log', 'a+'))) {
            die("No se puede crear archivo log");
        }
        $this->_log_creditos('Se inicia el proceso de envio a facturación');
    }

    private function _log_creditos($msg) {
        fwrite($this->fd, date('Y-m-d H:i:s') . ": $msg\n");
    }

    public function get_cuotas_a_facturar($fecha = FALSE, $credito_id = FALSE) {
        $this->_db->join('fid_creditos_cuotas cc', "c.ID = cc.ID_CREDITO AND cc.FECHA_INICIO < '$fecha' AND cc.FECHA_VENCIMIENTO > '$fecha'");
        return $this->_get_cuotas_a_facturar($credito_id);
    }

    public function get_cuotas_a_facturar_hoy($fecha) {
        $this->_db->join('fid_creditos_cuotas cc', "c.ID = cc.ID_CREDITO AND cc.FECHA_VENCIMIENTO = '$fecha'");
        return $this->_get_cuotas_a_facturar();
    }

    public function _get_cuotas_a_facturar($credito_id = FALSE) {
        $this->_db->select("c.ID, c.IVA, POSTULANTES, POSTULANTES_NOMBRES, POSTULANTES_CUIT AS CUIT, ifnull(o.NOMBRE,' - ') as OPERATORIA, cc.CUOTAS_RESTANTES, "
                . "ID_FIDEICOMISO, ID_OPERATORIA, POSTULANTES, f.ID_CONTABLE AS ID_CONT_FID, cc.FECHA_INICIO, FECHA_VENCIMIENTO, "
                . "cc.CAPITAL_CUOTA, cc.INT_COMPENSATORIO, cc.INT_COMPENSATORIO_IVA, cc.INT_COMPENSATORIO_SUBSIDIO, cc.INT_MORATORIO, cc.INT_PUNITORIO, "
                . "(SELECT MAX(CUOTAS_RESTANTES) FROM fid_creditos_cuotas WHERE ID_CREDITO = c.ID) AS TOTAL_CUOTAS, FROM_UNIXTIME(FECHA_VENCIMIENTO) AS FECHA_V_U");
        $this->_db->join("fid_operatorias o", "o.ID = c.ID_OPERATORIA", "left");
        $this->_db->join("fid_fideicomiso f", "f.ID = c.ID_FIDEICOMISO", "inner");
        $this->_db->join("fid_creditos_cobranzas cf", "cf.ID_CREDITO = c.ID AND cc.CUOTAS_RESTANTES = cf.CUOTAS_RESTANTES", "left");
        if ($credito_id) {
            $this->_db->where('c.ID = ' . $credito_id);
        } else {
            $this->_db->where('c.ID > 0');
        }
        $this->_db->where('CREDITO_ESTADO = ' . ESTADO_CREDITO_NORMAL);
        $this->_db->where('cf.CUOTAS_RESTANTES IS NULL');
        $this->_db->where('(SELECT SUM(MONTO) FROM fid_creditos_desembolsos WHERE ID_CREDITO = c.ID) > 0');
        $this->_db->group_by('cc.ID_CREDITO');
        $this->_db->order_by('POSTULANTES_NOMBRES');
        if ($credito_id) {
            $rtn = $this->_db->get_row('fid_creditos c');
        } else {
            $rtn = $this->_db->get_tabla('fid_creditos c');
        }

        if ($rtn) {
            return $rtn;
        } else {
            return array();
        }
    }

    public function get_cuotas_facturadas($fecha = FALSE) {
        $this->_db->select("c.ID, c.IVA, POSTULANTES_NOMBRES, POSTULANTES_CUIT AS CUIT, ifnull(o.NOMBRE,' - ') as OPERATORIA, cc.CUOTAS_RESTANTES, "
                . "cc.CAPITAL_CUOTA, cc.INT_COMPENSATORIO, cc.INT_COMPENSATORIO_IVA, cc.INT_COMPENSATORIO_SUBSIDIO, cc.INT_MORATORIO, cc.INT_PUNITORIO, FECHA_VENCIMIENTO, "
                . "(SELECT MAX(CUOTAS_RESTANTES) FROM fid_creditos_cuotas WHERE ID_CREDITO = c.ID) AS TOTAL_CUOTAS");
        $this->_db->join('fid_creditos_cuotas cc', "c.ID = cc.ID_CREDITO AND cc.FECHA_INICIO < '$fecha' AND cc.FECHA_VENCIMIENTO > '$fecha'");
        $this->_db->join("fid_operatorias o", "o.ID = c.ID_OPERATORIA", "left");
        $this->_db->join("fid_creditos_cobranzas cf", "cf.ID_CREDITO = c.ID AND cc.CUOTAS_RESTANTES = cf.CUOTAS_RESTANTES", "left");
        $this->_db->where('CREDITO_ESTADO = ' . ESTADO_CREDITO_NORMAL);
        $this->_db->where('cf.CUOTAS_RESTANTES IS NOT NULL');
        $this->_db->where('(SELECT SUM(MONTO) FROM fid_creditos_desembolsos WHERE ID_CREDITO = c.ID) > 0');
        $this->_db->group_by('cc.ID_CREDITO');
        $this->_db->order_by('POSTULANTES_NOMBRES');
        $rtn = $this->_db->get_tabla('fid_creditos c');

        if ($rtn) {
            return $rtn;
        } else {
            return array();
        }
    }

    public function generar_factura_c($credito, $fecha) {
        $id_cliente_ho = 0;
        $id_cliente = 0;
        $ids_cliente = explode('|', $credito['POSTULANTES']);
        foreach ($ids_cliente as $id_cli) {
            $this->_db->select("ID_CLIENTE_HO");
            $this->_db->where('ID = ' . $id_cli . ' AND ID_CLIENTE_HO !=0');
            $rtn = $this->_db->get_tabla('fid_clientes');
            if ($rtn) {
                $id_cliente_ho = $rtn[0]['ID_CLIENTE_HO'];
                $id_cliente = $id_cli;
                break;
            }
        }

        if (!$id_cliente_ho) {
            $cuits_cliente = explode('|', $credito['CUIT']);
            foreach ($cuits_cliente as $k => $cui_cli) {
                $id_cli_ho = $this->_dbsql->query("select DISTINCT CLI from amaefact where replace(cui, '-', '') ='$cui_cli' and emp=" . $credito['ID_CONT_FID']);
                if (!$id_cli_ho) {
                    $this->_log_creditos("Cliente ({$credito['ID']} - $cui_cli) no tiene facturas");
                    $id_cli_ho = $this->_dbsql->query("SELECT COD AS CLI FROM clientes WHERE COD > 0 AND REPLACE(CUI, '-', '') = '$cui_cli'");
                    if ($id_cli_ho) {
                        //$this->_log_creditos("Cliente ({$credito['ID']} - $cui_cli) se encontró por la tabla clientes");
                    }
                }
                if ($id_cli_ho && count($id_cli_ho) > 1) {
                    $this->_log_creditos("Cliente ({$credito['ID']} - $cui_cli) tiene más de un registro");

                    /* buscamos cual cliente más parecido */
                    $id_cli_ho = $this->_buscar_cliente($id_cli, $id_cli_ho);
                }

                if ($id_cli_ho) {
                    $id_cliente_ho = $id_cli_ho[0]['CLI'];
                    $id_cliente = $ids_cliente[$k];
                    $this->_db->update("fid_clientes", array('ID_CLIENTE_HO' => $id_cliente_ho), "ID='" . $ids_cliente[$k] . "'");
                    break;
                }
            }
        }

        if (!$id_cliente_ho) {
            $this->_log_creditos("Crédito ({$credito['ID']}) No encontró cliente en el HO");
            return FALSE;
        }

        $num_cuota = $credito['TOTAL_CUOTAS'] + 1 - $credito['CUOTAS_RESTANTES'];
        $rtn = $this->_dbsql->query("SELECT TOP 1 NUM_CREDITO FROM A_CREDITOS_CONTABLE WHERE NUM_CREDITO={$credito['ID']} AND CUOTA={$num_cuota}");

        if ($rtn) {
            $this->_log_creditos("Crédito ({$credito['ID']}) ya tiene registro de facturación");
            return FALSE;
        }

        $gastos = 0;
        $this->_db->select('MONTO');
        $this->_db->where("ID_CREDITO=" . $credito['ID'] . " AND FECHA > '" . $credito['FECHA_INICIO'] . "' AND FECHA <= '" . ($credito['FECHA_VENCIMIENTO'] + (24 * 3600) - 1) . "'");
        $arr_gastos = $this->_db->get_tabla("fid_creditos_gastos g");
        if ($arr_gastos) {
            foreach ($arr_gastos as $g) {
                $gastos += $g['MONTO'];
            }
        }

        $_cuota = array(
            'NUM_CREDITO' => $credito['ID'],
            'ID_FIDEICOMISO' => $credito['ID_FIDEICOMISO'],
            'ID_OPERATORIA' => $credito['ID_OPERATORIA'],
            'ID_CLIENTE_CREDITOS' => $id_cliente,
            'ID_CLIENTE_HO' => $id_cliente_ho,
            'CUOTA' => $num_cuota,
            'FECHA_AAAA_PERIODO' => date('Y', $credito['FECHA_VENCIMIENTO']),
            'FECHA_MM_PERIODO' => date('m', $credito['FECHA_VENCIMIENTO']),
            'FECHA_DD_PERIODO' => date('d', $credito['FECHA_VENCIMIENTO']),
            'CAPITAL' => round($credito['CAPITAL_CUOTA'], 2),
            'INT_COMP' => round($credito['INT_COMPENSATORIO'], 2),
            'IVA_COMP' => round($credito['INT_COMPENSATORIO_IVA'], 2),
            'GASTOS' => round($gastos, 2),
            'PROCESO_R_C' => 'C',
            'ESTADO_PROCESO' => 1,
            'FECHA_PROCESO' => str_replace('-', '', $fecha),
        );

        if ($this->_dbsql->insert('A_CREDITOS_CONTABLE', $_cuota)) {
            $id_ho = $this->_dbsql->query('SELECT MAX(ID) AS ID FROM A_CREDITOS_CONTABLE');
            $arr = array(
                'ID_HO' => $id_ho[0]['ID'],
                'ID_CREDITO' => $credito['ID'],
                'CUOTAS_RESTANTES' => $credito['CUOTAS_RESTANTES'],
                'NRO_CUOTA' => $credito['TOTAL_CUOTAS'] - $credito['CUOTAS_RESTANTES'] + 1,
                'CAPITAL_CUOTA' => $credito['CAPITAL_CUOTA'],
                'INT_COMPENSATORIO' => $credito['INT_COMPENSATORIO'],
                'INT_COMPENSATORIO_IVA' => $credito['INT_COMPENSATORIO_IVA'],
                'FECHA' => $fecha,
            );

            $this->_db->insert('fid_creditos_cobranzas', $arr);
        } else {
            $this->_log_creditos("Crédito ({$credito['ID']}) no pudo ser guardado en la base de datos SQL");
            return FALSE;
        }
    }

    public function cerrar_generar_factura() {
        echo 'Finaliza el proceso';
        $this->_log_creditos('Se finaliza el proceso de envio a facturación');
        fclose($this->fd);
    }

    private function _buscar_cliente($id_cli, $id_cli_ho) {
        $rtn = $this->_db->get_tabla('fid_clientes', 'ID = ' . $id_cli);
        if ($rtn) {
            $nombre = explode(' ', str_replace(',', ' ', trim($rtn[0]['RAZON_SOCIAL'])));
            $nombre = $nombre[0];
            $ids_cli = array();
            foreach ($id_cli_ho as $id_cli) {
                $ids_cli[] = $id_cli['CLI'];
            }

            $ids_cli = implode(',', $ids_cli);
            $rtn = $this->_dbsql->query("SELECT COD AS CLI FROM clientes WHERE COD IN ($ids_cli) AND NOM LIKE '%$nombre%'");
            if ($rtn && count($rtn) == 1) {
                return $rtn;
            }
        } else {
            $this->_log_creditos("Cliente ({$credito['ID']} - $cui_cli) no se encuentra en la tabla clientes");
        }

        return FALSE;
    }

}
