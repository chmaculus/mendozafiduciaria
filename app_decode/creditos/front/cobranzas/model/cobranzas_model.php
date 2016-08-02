<?php

class cobranzas_model extends credito_model {

    public function get_cuotas_a_facturar($fecha = FALSE, $credito_id = FALSE) {
        $this->_db->select("c.ID, c.IVA, POSTULANTES_NOMBRES, POSTULANTES_CUIT AS CUIT, ifnull(o.NOMBRE,' - ') as OPERATORIA, cc.CUOTAS_RESTANTES, "
                . "cc.CAPITAL_CUOTA, cc.INT_COMPENSATORIO, cc.INT_COMPENSATORIO_IVA, cc.INT_COMPENSATORIO_SUBSIDIO, cc.INT_MORATORIO, cc.INT_PUNITORIO, FECHA_VENCIMIENTO, "
                . "(SELECT MAX(CUOTAS_RESTANTES) FROM fid_creditos_cuotas WHERE ID_CREDITO = c.ID) AS TOTAL_CUOTAS");
        $this->_db->join('fid_creditos_cuotas cc', "c.ID = cc.ID_CREDITO AND cc.FECHA_INICIO < '$fecha' AND cc.FECHA_VENCIMIENTO > '$fecha'");
        $this->_db->join("fid_operatorias o", "o.ID = c.ID_OPERATORIA", "left");
        $this->_db->join("fid_creditos_cobranzas cf", "cf.ID_CREDITO = c.ID AND cc.CUOTAS_RESTANTES = cf.CUOTAS_RESTANTES", "left");
        if ($credito_id) {
            $this->_db->where('c.ID = ' . $credito_id);
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

    public function enviar_a_facturar($fecha_credito, $creditos) {
        $fecha = date('Y-m-d H:i:s');
        foreach ($creditos as $credito_id) {
            if ($credito = $this->get_cuotas_a_facturar($fecha_credito, $credito_id)) {
                $arr = array(
                    'ID_CREDITO' => $credito_id,
                    'CUOTAS_RESTANTES' => $credito['CUOTAS_RESTANTES'],
                    'NRO_CUOTA' => $credito['TOTAL_CUOTAS'] - $credito['CUOTAS_RESTANTES'] + 1,
                    'CAPITAL_CUOTA' => $credito['CAPITAL_CUOTA'],
                    'INT_COMPENSATORIO' => $credito['INT_COMPENSATORIO'],
                    'INT_COMPENSATORIO_IVA' => $credito['INT_COMPENSATORIO_IVA'],
                    'FECHA' => $fecha,
                );

                $this->_db->insert('fid_creditos_cobranzas', $arr);
            }
        }

        return FALSE;
    }

}
