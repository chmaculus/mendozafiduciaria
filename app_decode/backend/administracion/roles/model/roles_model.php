<?php

class roles_model extends main_model{
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
                }
            }
        else://editar
            $resp = $this->_db->update($this->_tablamod, $obj['var_ins'], "id='".$iid."'");
            $acc = "edit";
        endif;
        $this->update_rol_update($_SESSION["USER_ROL"],"1");
        $rtn = array(
            "accion"=>$acc,
            "result"=>$resp
        );
        return $rtn;
    }
    /*
    function delobj($id){
        $this->_db->delete($this->_tablamod, "id =' " . $id . "'" );
        $this->update_rol_update($_SESSION["USER_ROL"],"1");
    }
    */
    function get_dependencia_operatoria($tabla, $campo, $valor){
        $this->_db->select('count(*) as cont');
        $rtn = $this->_db->get_tabla( $tabla, $campo. '='.$valor);
        return $rtn;
    }
    
    function guardar_rolmenu($id_rol,$items){
        $this->_db->delete("fid_roles_menu","ID_ROL='".$id_rol."'");
        if ($items){
            $padres = array();
            foreach($items as $it=>$val){
                if ($val["padre"]>0){
                    $padres[] = $val["padre"];
                    $data = array(
                        "ID_ROL"=>$id_rol,
                        "ID_MENU"=>$val["id"]
                    );
                    $this->_db->insert("fid_roles_menu",$data);
                }
            }
            $padres = array_unique($padres);
            if ($padres){
                foreach($padres as $id_p){
                    $data = array(
                        "ID_ROL"=>$id_rol,
                        "ID_MENU"=>$id_p
                    );
                    $this->_db->insert("fid_roles_menu",$data);
                }
                
            }
        }
        $this->update_rol_update($id_rol,"1");
        return true;
    }
    
    function get_rolmenu($id_rol){
        $this->_db->select('rm.* ');
        //$this->_db->order_by("orden","asc");
        $this->_db->join("fid_menu m","m.ID=rm.ID_MENU and m.ESPADRE='0'");
        die();
        $rtn = $this->_db->get_tabla( 'fid_roles_menu rm', "ID_ROL='".$id_rol."'");
        return $rtn;
    }
    
    function guardar_roletapa($id_rol,$items){
        $this->_db->delete("fid_roles_etapas","ID_ROL='".$id_rol."'");
        if ($items){
            foreach($items as $it=>$val){
                if ($val["id"]!='99'){
                    $data = array(
                        "ID_ROL"=>$id_rol,
                        "ID_ETAPA"=>$val["id"]
                    );
                    $this->_db->insert("fid_roles_etapas",$data);
                }
            }
            $this->update_rol_update($id_rol,"1");
        }
        
        return true;
    }
    
    function get_roletapa($id_rol){
        $this->_db->select('rm.* ');
        $this->_db->join("fid_etapas m","m.ID=rm.ID_ETAPA");
        $rtn = $this->_db->get_tabla( 'fid_roles_etapas rm', "ID_ROL='".$id_rol."'");
        return $rtn;
    }
    
    
          
}
?>