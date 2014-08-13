<?php

class estructura_model extends credito_model{

    function get_cuotas_estructura(){
        $cuotas_rtn = array();
        foreach($this->_cuotas as $cuota){
            $cuota['DATA'] = $this->_get_saldo_capital($cuota['FECHA_VENCIMIENTO']-1, true);
            $cuotas_rtn[] = $cuota;
        }
//        print_array($cuotas_rtn);
        return $cuotas_rtn;
    }
    
    
    function save_estructura($cuotas){
        foreach($cuotas as $cuota){
            $this->_db->update("fid_creditos_cuotas", 
                    array(
                        "E_AMORTIZACION"=>$cuota['monto']
                    ),"ID = ".$cuota['id']);
        }
    }
    
    function desimputar_pago(){
        $fecha = $this->_fecha_actual;
        
        $pagos = array();
        foreach($this->_variaciones as $variacion){

            if ($variacion['FECHA'] >= $fecha && $variacion['TIPO']==3){
                $tmp = 0;
                foreach($variacion['ASOC'] as $pago){
                   $tmp += $pago['MONTO']; 
                }
                //cambiamos el estado del pago realizado anterioremente
                $this->_db->delete("fid_creditos_eventos","ID = ".$variacion['ID']);
                echo $this->_db ->last_query();
                
                
                $this->_db->delete("fid_creditos_pagos","ID_VARIACION = ".$variacion['ID']);
                echo $this->_db ->last_query();
                
                //generamos un nuevo evento de pago
                $pagos[] =array(
                    'monto' => $tmp,
                    'fecha' => $variacion['FECHA']
                );
            }
        }
        print_array($pagos);
        
        return $pagos;
    }    
    
}


?>
