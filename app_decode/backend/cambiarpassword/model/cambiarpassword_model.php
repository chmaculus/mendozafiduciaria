<?php
    class cambiarpassword_model extends main_model{
        
        
        function send_change($iid, $p1, $p2){
            
            if ($p1!=$p2){
                return 0;
            }else{
                $nuevaclave = crypt_blowfish($p2);
            }
            //updatear
            $obj_up =   array(
                            "CLAVE"=>$nuevaclave
                        );
            $resp = $this->_db->update('fid_usuarios', $obj_up, "ID='".$iid."' AND ESTADO = 1");
            if ($resp)
                unset($_SESSION); 
            
            return $resp;
            
        }

    }

?>
