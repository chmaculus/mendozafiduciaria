<?php

class cobranzas_model extends credito_model {

    protected $fd;

    public function init_log($operacion, $fecha) {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL);

        $_SESSION ['FILE_SCRIPT'] = __FILE__;
        if (!($this->fd = fopen('log\\' . $operacion . '_' . $fecha . '.log', 'a+'))) {
            die("No se puede crear archivo log");
        }
        
        $init = '';
        switch ($operacion) {
            case 'facturacion_creditos':
                $init = 'Se inicia el proceso de envio a facturación';
                break;
            case '':
                $init = 'Se inicia el proceso de paso de recuperos a contabilidad';
                break;
            case 'facturacion_control':
                $init = 'Se inicia el proceso de control de reimputación';
                break;
            default:
                $init = 'Se inicia el proceso: ' . $operacion;
                break;
        }
        
        $this->_log_creditos($init);
    }

    private function _log_creditos($msg) {
        if ($this->fd) {
            fwrite($this->fd, date('Y-m-d H:i:s') . ": $msg\n");
        }
    }

    public function getCliente($credito) {
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
                    $this->_log_creditos("Cliente ({$credito['ID']} - $cui_cli) no tiene registros de facturación");
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

        return array('id_cliente' => $id_cliente, 'id_cliente_ho' => $id_cliente_ho);
    }

    public function get_cuotas_a_facturar($credito_id = FALSE, $fecha = FALSE) {
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
        $this->_db->join("fid_cr_cont_cobranzas cf", "cf.ID_CREDITO = c.ID AND cc.CUOTAS_RESTANTES = cf.CUOTAS_RESTANTES", "left");
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
        $this->_db->join("fid_cr_cont_cobranzas cf", "cf.ID_CREDITO = c.ID AND cc.CUOTAS_RESTANTES = cf.CUOTAS_RESTANTES", "left");
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
        if (!($result = $this->getCliente($credito))) {
            return FALSE;
        }
        $id_cliente = $result['id_cliente'];
        $id_cliente_ho = $result['id_cliente_ho'];

        $num_cuota = $credito['TOTAL_CUOTAS'] + 1 - $credito['CUOTAS_RESTANTES'];
        $rtn = $this->_dbsql->query("SELECT TOP 1 NUM_CREDITO FROM A_CREDITOS_CONTABLE WHERE NUM_CREDITO={$credito['ID']} AND CUOTA={$num_cuota} AND PROCESO_R_C='C'");

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
            'ID_EVENTO' => 0,
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

            $this->_db->insert('fid_cr_cont_cobranzas', $arr);
        } else {
            $this->_log_creditos("Crédito ({$credito['ID']}) no pudo ser guardado en la base de datos SQL");
            return FALSE;
        }
    }

    public function cerrar_generar_factura($error = FALSE) {
        if ($error)
            $this->_log_creditos('ERROR y no finaliza el proceso de envio para facturación');
        else
            $this->_log_creditos('Se finaliza correctamente el proceso de envio para facturación');
        
        fclose($this->fd);
    }

    public function cerrar_envio_recuperos($error = FALSE) {
        if ($error)
            $this->_log_creditos('ERROR y no finaliza el proceso de envio de recuperos');
        else
            $this->_log_creditos('Se finaliza correctamente el proceso de envio de recuperos');
        
        fclose($this->fd);
    }

    public function cerrar_control_anulados($error = FALSE) {
        if ($error)
            $this->_log_creditos('ERROR y no finaliza el proceso de control de reimputación');
        else
            $this->_log_creditos('Se finaliza correctamente el proceso de control de reimputación');
        
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
    
    public function get_creditos_pagos($fecha_pago) {
        $this->_db->select("c.ID, c.POSTULANTES, POSTULANTES_NOMBRES, POSTULANTES_CUIT AS CUIT, f.ID_CONTABLE AS ID_CONT_FID");
        $this->_db->join("fid_fideicomiso f", "f.ID = c.ID_FIDEICOMISO", "inner");
        $this->_db->join('fid_creditos_pagos cp', "cp.ID_CREDITO = c.ID AND cp.FECHA < '$fecha_pago'");
        $this->_db->join("fid_cr_cont_recuperos cr", "cr.ID_CREDITO = c.ID AND cr.FECHA = cp.FECHA AND cr.ANU=0", "left");
        $this->_db->where('cr.FECHA IS NULL');
        $this->_db->group_by('cp.ID_CREDITO');
        $this->_db->order_by('cp.FECHA');
        return $this->_db->get_tabla('fid_creditos c');
    }

    public function get_recuperos($fecha_pago, $credito_id = FALSE) {
        $this->_db->select("c.ID, f.ID_CONTABLE AS ID_CONT_FID, ROUND(SUM(cp.MONTO), 2) as MONTO, "
                . "cp.FECHA AS FECHA_PAGO, cr.FECHA, (SELECT MAX(CUOTAS_RESTANTES) FROM fid_creditos_cuotas WHERE ID_CREDITO = c.ID) AS TOTAL_CUOTAS, cp.CUOTAS_RESTANTES, "
                . "ID_FIDEICOMISO, ID_OPERATORIA, ID_VARIACION AS ID_EVENTO, "
                . "SUM(CASE WHEN cp.ID_TIPO = " . PAGO_CAPITAL . " THEN cp.MONTO ELSE 0 END) AS CAPITAL_CUOTA, "
                . "SUM(CASE WHEN cp.ID_TIPO = " . PAGO_COMPENSATORIO . " THEN cp.MONTO ELSE 0 END) AS INT_COMPENSATORIO, "
                . "SUM(CASE WHEN cp.ID_TIPO = " . PAGO_MORATORIO . " THEN cp.MONTO ELSE 0 END) AS INT_MORATORIO, "
                . "SUM(CASE WHEN cp.ID_TIPO = " . PAGO_PUNITORIO . " THEN cp.MONTO ELSE 0 END) AS INT_PUNITORIO, "
                . "SUM(CASE WHEN cp.ID_TIPO = " . PAGO_IVA_COMPENSATORIO . " THEN cp.MONTO ELSE 0 END) AS IVA_COMPENSATORIO, "
                . "SUM(CASE WHEN cp.ID_TIPO = " . PAGO_IVA_MORATORIO . " THEN cp.MONTO ELSE 0 END) AS IVA_MORATORIO, "
                . "SUM(CASE WHEN cp.ID_TIPO = " . PAGO_IVA_PUNITORIO . " THEN cp.MONTO ELSE 0 END) AS IVA_PUNITORIO, "
                . "SUM(CASE WHEN cp.ID_TIPO = " . PAGO_GASTOS . " THEN cp.MONTO ELSE 0 END) AS GASTOS");
        $this->_db->join("fid_fideicomiso f", "f.ID = c.ID_FIDEICOMISO", "inner");
        $this->_db->join('fid_creditos_pagos cp', "cp.ID_CREDITO = c.ID AND cp.FECHA < '$fecha_pago'");
        $this->_db->join("fid_cr_cont_recuperos cr", "cr.ID_CREDITO = c.ID AND cr.FECHA = cp.FECHA AND cr.ANU=0", "left");
        if ($credito_id) {
            $this->_db->where('c.ID = ' . $credito_id);
        } else {
            $this->_db->where('c.ID > 0');
        }
        $this->_db->where('CREDITO_ESTADO = ' . ESTADO_CREDITO_NORMAL);
        $this->_db->where('cr.FECHA IS NULL');
        $this->_db->where('(SELECT SUM(MONTO) FROM fid_creditos_desembolsos WHERE ID_CREDITO = c.ID) > 0');
        $this->_db->group_by('cp.ID_CREDITO, cp.ID_VARIACION');
        $this->_db->order_by('cp.FECHA');
        $rtn = $this->_db->get_tabla('fid_creditos c');
        
        return $rtn;
    }

    public function generar_factura_r($cliente, $credito, $fecha_ope) {
        $num_cuota = $credito['TOTAL_CUOTAS'] + 1 - $credito['CUOTAS_RESTANTES'];
        $rtn = $this->_dbsql->query("SELECT TOP 1 NUM_CREDITO FROM A_CREDITOS_CONTABLE WHERE NUM_CREDITO={$credito['ID']} AND ID_EVENTO={$credito['ID_EVENTO']} AND PROCESO_R_C='R'");

        if ($rtn) {
            $this->_log_creditos("Crédito ({$credito['ID']}) C{$num_cuota} ya tiene registro de facturación");
            return FALSE;
        }

        $_cuota = array(
            'ID_EVENTO' => $credito['ID_EVENTO'],
            'NUM_CREDITO' => $credito['ID'],
            'ID_FIDEICOMISO' => $credito['ID_FIDEICOMISO'],
            'ID_OPERATORIA' => $credito['ID_OPERATORIA'],
            'ID_CLIENTE_CREDITOS' => $cliente['id_cliente'],
            'ID_CLIENTE_HO' => $cliente['id_cliente_ho'],
            'CUOTA' => $num_cuota,
            'FECHA_AAAA_PERIODO' => date('Y', $credito['FECHA_PAGO']),
            'FECHA_MM_PERIODO' => date('m', $credito['FECHA_PAGO']),
            'FECHA_DD_PERIODO' => date('d', $credito['FECHA_PAGO']),
            'CAPITAL' => round($credito['CAPITAL_CUOTA'], 2),
            'INT_COMP' => round($credito['INT_COMPENSATORIO'], 2),
            'IVA_COMP' => round($credito['IVA_COMPENSATORIO'], 2),
            'INT_MOR' => round($credito['INT_MORATORIO'], 2),
            'IVA_MOR' => round($credito['IVA_MORATORIO'], 2),
            'INT_PUN' => round($credito['INT_PUNITORIO'], 2),
            'IVA_PUN' => round($credito['IVA_PUNITORIO'], 2),
            'GASTOS' => round($credito['GASTOS'], 2),
            'PROCESO_R_C' => 'R',
            'ESTADO_PROCESO' => 1,
            'FECHA_PROCESO' => str_replace('-', '', $fecha_ope),
        );
        
        $this->_dbsql->init();
        if ($this->_dbsql->insert('A_CREDITOS_CONTABLE', $_cuota)) {
            $id_ho = $this->_dbsql->query('SELECT MAX(ID) AS ID FROM A_CREDITOS_CONTABLE');
            $arr = array(
                'ID_CREDITO' => $credito['ID'],
                'ID_EVENTO' => $credito['ID_EVENTO'],
                'MONTO' => $credito['MONTO'],
                'FECHA' => $credito['FECHA_PAGO'],
                'FEC_OPE' => $fecha_ope
            );

            if (!$this->_db->insert('fid_cr_cont_recuperos', $arr)) {
                $this->_dbsql->delete('A_CREDITOS_CONTABLE', "NUM_CREDITO={$credito['ID']} AND ID_EVENTO={$credito['ID_EVENTO']}");
            }
        } else {
            $this->_log_creditos("Crédito ({$credito['ID']}) no pudo ser guardado en la base de datos SQL");
            return FALSE;
        }
    }
    
    public function controlar_anulados() {
        $this->_db->select('cr.ID_EVENTO, cr.ID_CREDITO');
        $this->_db->join("fid_creditos_pagos cp", "cr.ID_EVENTO = cp.ID_VARIACION", "left");
        $this->_db->where('ANU=0 AND cp.ID IS NULL');
        $this->_db->group_by('cp.ID_VARIACION');
        $pagos_eliminados = $this->_db->get_tabla('fid_cr_cont_recuperos cr');
        
        if ($pagos_eliminados) {
            foreach ($pagos_eliminados as $pg_el) {
                $rtn = $this->_dbsql->query("SELECT TOP 1 ESTADO_PROCESO FROM A_CREDITOS_CONTABLE WHERE NUM_CREDITO={$pg_el['ID_CREDITO']} AND ID_EVENTO={$pg_el['ID_EVENTO']} AND PROCESO_R_C='R'");
                
                if ($rtn && $rtn[0]['ESTADO_PROCESO'] == 1) {
                    //si no ha sido procesado se elimina
                    $res = $this->_dbsql->delete("A_CREDITOS_CONTABLE", "NUM_CREDITO={$pg_el['ID_CREDITO']} AND ID_EVENTO={$pg_el['ID_EVENTO']} AND PROCESO_R_C='R'");
                } elseif ($rtn && $rtn[0]['ESTADO_PROCESO'] == 2) {
                    //si se ha procesado se hace un update para que se haga una NC
                    $res = $this->_dbsql->update("A_CREDITOS_CONTABLE", array('ESTADO_PROCESO' =>9),  "NUM_CREDITO={$pg_el['ID_CREDITO']} AND ID_EVENTO={$pg_el['ID_EVENTO']} AND PROCESO_R_C='R'");
                } else {
                    $res = TRUE;
                }
                
                if ($res) {
                    $this->_db->update('fid_cr_cont_recuperos', array('ANU' => TRUE), "ID_CREDITO={$pg_el['ID_CREDITO']} AND ID_EVENTO={$pg_el['ID_EVENTO']}");
                }
            }
        }
    }
    
    public function cerrar_generar_recibo() {
        echo "\n" . 'Finaliza el proceso de recibos';
        $this->_log_creditos('Se finaliza el proceso de envio para recibos');
        fclose($this->fd);
    }
    
    public function control_proceso_creditos($todo) {
        $rtn = $this->_dbsql->query("SELECT TOP 1 * FROM A_CREDITOS_PROCESOS");
        if ($rtn) {
            $rtn = $rtn[0];
            if ($rtn['CONTABILIDAD']) {
                return FALSE;
            }
            if ($todo && $rtn['CREDITO']) {
                return FALSE;
            }
            $this->_dbsql->query("UPDATE A_CREDITOS_PROCESOS SET CREDITO=1");
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function finalizar_proceso_creditos() {
        $rtn = $this->_dbsql->query("UPDATE A_CREDITOS_PROCESOS SET CREDITO=0");
    }

}
