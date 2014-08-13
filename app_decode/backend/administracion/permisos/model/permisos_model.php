<?php

class permisos_model extends main_model{
    public $_tablamod = "fid_roles_permisos";
    
    function get_obj($id){
        if (!$id) return array();
        $this->_db->where("ID_ROL = '".$id   ."'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
    }
    
    function get_roles(){
        $rtn = $this->_db->get_tabla("fid_roles");
        return $rtn;
    }
    
    function get_permisos(){
        $this->_db->order_by('ID','ASC');
        $rtn = $this->_db->get_tabla("fid_permisos");
        return $rtn;
    }
    
    function get_info_grid(){
        $this->_db->select("p.*, r.DENOMINACION as ROLNAME, pe.MODULO AS MODULONAME, pe.PERMISO AS PERMISONAME");
        $this->_db->join("fid_roles r","r.ID=p.ID_ROL");
        $this->_db->join("fid_permisos pe","pe.ID=p.ID_PERMISO");
        $rtn = $this->_db->get_tabla("fid_roles_permisos p");
        return $rtn;
    }
    
    function sendobj($obj){
        $iid = $obj["id"];
        unset($obj["id"]);
               
        $arr_tmp = array();
        
        if ($iid==0)://agregar
            $acc = "add";
            
            if ($obj['obj_fin']):
                $this->_db->delete($this->_tablamod,"ID_ROL='".$obj['obj_fin'][0]['ID_ROL']."'");
                foreach( $obj['obj_fin'] as $rsp):
                    $resp = $this->_db->insert($this->_tablamod, $rsp);
                    if ($rsp["MOSTRAR"]==1):
                        $arr_tmp[] = $rsp["ID_PERMISO"];
                    endif;
                endforeach;
            endif;
            
            //recorrer los permisos
            foreach($arr_tmp as $idp){
                $this->_db->select('m.ID,m.padre');
                $this->_db->join('fid_menu m','m.ID=p.CODIGO','INNER');
                $tmp = $this->_db->get_tabla('fid_permisos p', 'p.ID="'.$idp.'"');
                if ($tmp){
                    $arr_fin[] = array("padre"=>$tmp[0]['padre'],"id"=>$tmp[0]['ID']);
                }
            }
            //por cada permiso, obtener el ide de muenu y padre
            $this->guardar_rolmenu( $obj['obj_fin'][0]['ID_ROL'] , $arr_fin );
            
                   
        else://editar
            if ($obj['obj_fin']):
                $this->_db->delete($this->_tablamod,"ID_ROL='".$obj['obj_fin'][0]['ID_ROL']."'");
                foreach( $obj['obj_fin'] as $rsp):
                    $resp = $this->_db->insert($this->_tablamod, $rsp);
                    if ($rsp["MOSTRAR"]==1):
                        $arr_tmp[] = $rsp["ID_PERMISO"];
                    endif;
                endforeach;
            endif;
            //recorrer los permisos
            foreach($arr_tmp as $idp){
                $this->_db->select('m.ID,m.padre');
                $this->_db->join('fid_menu m','m.ID=p.CODIGO','INNER');
                $tmp = $this->_db->get_tabla('fid_permisos p', 'p.ID="'.$idp.'"');
                if ($tmp){
                    $arr_fin[] = array("padre"=>$tmp[0]['padre'],"id"=>$tmp[0]['ID']);
                }
            }
            //por cada permiso, obtener el ide de muenu y padre
            $this->guardar_rolmenu( $obj['obj_fin'][0]['ID_ROL'] , $arr_fin );
            $acc = "edit";
        endif;
        $this->update_rol_update($obj['obj_fin'][0]['ID_ROL'],"1");
        $rtn = array(
            "accion"=>$acc,
            "result"=>$resp
        );
        return $rtn;
    }
    
    function delobj($id){
        $this->update_rol_update($id,"1");
        $this->_db->delete( $this->_tablamod, "ID_ROL =' " . $id . "'" );
        $this->_db->delete("fid_roles_menu","ID_ROL='".$id."'");
    }
    
    function get_dependencia_operatoria($tabla, $campo, $valor){
        $this->_db->select('count(*) as cont');
        $rtn = $this->_db->get_tabla( $tabla, $campo. '='.$valor);
        return $rtn;
    }
    
    
    //necesitamos rol y array de items
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
          
}

?>