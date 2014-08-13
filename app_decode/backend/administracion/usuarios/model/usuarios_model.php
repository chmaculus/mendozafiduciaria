<?php

class usuarios_model extends main_model{
    public $_tablamod = "fid_usuarios";
    
    function get_obj($id){
        if (!$id) return array();
        $this->_db->where("id = '".$id   ."'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
    }
    
    function get_roles(){
        $rtn = $this->_db->get_tabla("fid_roles");
        return $rtn;
    }
    
    function get_permisos(){
        $this->_db->order_by('MODULO,PERMISO','ASC');
        $rtn = $this->_db->get_tabla("fid_permisos");
        return $rtn;
    }
    
    function get_info_grid(){
        $this->_db->select("u.*, r.DENOMINACION as ROLNAME");
        $this->_db->join("fid_roles r","r.ID=u.ID_ROL");
        $rtn = $this->_db->get_tabla($this->_tablamod . " u");
        return $rtn;
    }
    
    function sendobj($obj){
        $iid = $obj["id"];
        unset($obj["id"]);
        $id_new = $iid;
        $obj['var_ins']['UPDATEDON'] = date("Y-m-d H:i:s");
        if (isset($obj['var_ins']['CLAVE'])){
            $obj['var_ins']['CLAVE'] = crypt_blowfish($obj['var_ins']['CLAVE']);
        }
        if ($iid==0)://agregar
            $acc = "add";
            $obj['var_ins']['CREATEDON'] = $obj['var_ins']['UPDATEDON'];
            $obj['var_ins']['ESTADO'] = '1';
            $test = $this->_db->get_tabla($this->_tablamod,"USERNAME='".$obj['var_ins']['USERNAME']."'");
            if ($test){
                $resp = '-1'; //username ya existe
            }else{
                $test_e = $this->_db->get_tabla($this->_tablamod,"EMAIL='".$obj['var_ins']['EMAIL']."'");
                if ($test_e){
                    $resp = '-2';//email ya existe
                }else{
                    $resp = $this->_db->insert($this->_tablamod, $obj['var_ins']);
                    $id_new = $resp;
                }
            }
        else://editar
            $resp = $this->_db->update($this->_tablamod, $obj['var_ins'], "id='".$iid."'");
            $acc = "edit";
            
        endif;
        
        //su
        if (isset($obj['_arr_adicionales'])):
            
            $this->_db->update('fid_usuarios', array("SU_1"=>"0","SU_2"=>"0","SU_3"=>"0","SU_4"=>"0","SU_5"=>"0","SU_6"=>"0"), "ID='".$id_new."'");
            //log_this('eeeeeeee.log',$this->_db->last_query());
        
            //actualizar usuario
            if ($obj['_arr_adicionales']['c_hist1']==1):
                $this->_db->update( 'fid_usuarios', array("SU_5"=>"1"), "ID='".$id_new."'" );
            endif;
            if ($obj['_arr_adicionales']['e_hist1']==1):
                $this->_db->update( 'fid_usuarios', array("SU_6"=>"1"), "ID='".$id_new."'" );
            endif;
            if ($obj['_arr_adicionales']['c_hist']==1):
                $this->_db->update( 'fid_usuarios', array("SU_1"=>"1"), "ID='".$id_new."'" );
            endif;
            if ($obj['_arr_adicionales']['e_hist']==1):
                $this->_db->update( 'fid_usuarios', array("SU_2"=>"1"), "ID='".$id_new."'" );
            endif;
            if ($obj['_arr_adicionales']['v_de']==1):
                $this->_db->update( 'fid_usuarios', array("SU_3"=>"1"), "ID='".$id_new."'" );
            endif;
            if ($obj['_arr_adicionales']['h_atras']==1):
                $this->_db->update( 'fid_usuarios', array("SU_4"=>"1"), "ID='".$id_new."'" );
            endif;
        endif;
        
        
        $this->update_rol_update($obj['var_ins']['ID_ROL'],"1");
        $rtn = array(
            "accion"=>$acc,
            "result"=>$resp
        );
        return $rtn;
    }
    
    function delobj($id){
        //$this->update_rol_update($_SESSION["USER_ROL"],"1");
        $this->_db->select("ID_ROL");
        $tmp = $this->_db->get_tabla($this->_tablamod,"id =' " . $id . "'");
        if($tmp){
            $id_rol = $tmp[0]["ID_ROL"];
            $this->update_rol_update($id_rol,"1");
        }
        $this->_db->delete($this->_tablamod, "id =' " . $id . "'" );
    }
    
    function get_dependencia_operatoria($tabla, $campo, $valor){
        $this->_db->select('count(*) as cont');
        $rtn = $this->_db->get_tabla( $tabla, $campo. '='.$valor);
        return $rtn;
    }
    
    
    function getpuestos($ida){
        $rtn = $this->_db->get_tabla("fid_xpuestos","ID_AREA='". $ida . "'");
        //log_this('qqqq.log',$this->_db->last_query());
        return $rtn;
    }
          
    
    
}
?>