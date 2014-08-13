<?php

class cobros_model extends credito_model{

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
    
    
    function guardar_archivo_bancario($archivo, $fecha){
        $id = $this->_db->insert("fid_creditos_bancos_files",array("FECHA_REC"=>$fecha,"ARCHIVO"=>$archivo));
        return $id;
    }
    
    function get_archivos_bancarios(){
        $rtn = $this->_db->get_tabla("fid_creditos_bancos_files");
        return $rtn;
    }
    
    function get_archivo_bancario($id){
        $rtn = $this->_db->get_row("fid_creditos_bancos_files","ID = ".$id);
        return $rtn;
    }
    
    function guardar_cobros_bancos($datos){
        $fecha = time();
        foreach($datos as $dato){
            $dato['FECHA_TOMA'] = $fecha ;
            $dato['FECHA_INGRESADO'] = 0;
            $this->_db->insert("fid_creditos_bancos_cobros",$dato);
        }
    }
    
    function marcar_archivo_bancario($id){
        $this->_db->update("fid_creditos_bancos_files",array("ESTADO"=>time()), "ID = ".$id);
    }
    
    function get_cobros_bancos($id){
        $datos = $this->_db->get_tabla("fid_creditos_bancos_cobros","ID_FILE = ".$id);
        return $datos;
    }
    
    function marcar_cobro_bancario($id, $fecha = false){
        $fecha = $fecha ? $fecha : time();
        $this->_db->update("fid_creditos_bancos_cobros",array("FECHA_INGRESADO"=>$fecha ),"ID = ".$id);
    }
}


?>
