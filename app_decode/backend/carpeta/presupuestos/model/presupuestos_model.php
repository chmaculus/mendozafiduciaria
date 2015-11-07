<?php

class presupuestos_model extends main_model {

    public $_tablamod = "fid_presupuestos";

    function get_obj($id) {
        if (!$id)
            return array();
        $this->_db->where("ID = '" . $id . "'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
    }

    function get_items($id) {
        if (!$id)
            return array();
        $this->_db->where("ID_PRESUPUESTO = '" . $id . "'");
        $rtn = $this->_db->get_tabla($this->_tablamod . '_items');
        return $rtn;
    }

    function get_info_grid() {
        $rtn = $this->_db->get_tabla("fid_presupuestos");
        if ($rtn) {
            foreach ($rtn as $k => $v) {
                $rtn[$k]['ID_PRES'] = $v['ID'];
            }
        }
        //log_this('eeeeee.log',  $this->_db->last_query() );
        return $rtn;
    }

    function save($id, $items) {
        if (is_array($items) && count($items) > 0) {
            $total_neto = 0;
            $total_iva = 0;
            foreach ($items as $it) {
                $neto = $it[3] * $it[2];
                $total_neto += $neto;
                $total_iva += ($neto * $it[4] / 100);
            }

            $arr = array(
                'NETO' => $total_neto,
                'IVA' => $total_iva,
                'TOTAL' => ($total_neto + $total_iva)
            );

            $id_pres = FALSE;

            if ($id) {
                if ($this->_db->update($this->_tablamod, $arr, "ID = $id")) {
                    $id_pres = $id;
                }
            } else {
                $id_pres = $this->_db->insert($this->_tablamod, $arr);
            }

            if ($id_pres) {
                $this->_db->delete($this->_tablamod . '_items', "ID_PRESUPUESTO = $id");

                foreach ($items as $it) {
                    $arr = array(
                        'ID_PRESUPUESTO' => $id_pres,
                        'DESCRIPCION' => $it[0],
                        'DIVISA' => $it[1],
                        'CAMBIO' => $it[2],
                        'NETO' => $it[3],
                        'IVA' => $it[4],
                    );

                    $this->_db->insert($this->_tablamod . '_items', $arr);
                }
            }

            return 1;
        }

        return 0;
    }

    function delobj($id) {
        if ($id && is_numeric($id)) {
            $this->_db->delete($this->_tablamod, "ID = $id");
            $this->_db->delete($this->_tablamod . '_items', "ID_PRESUPUESTO = $id");

            return 1;
        }
        return 0;
    }

}

?>