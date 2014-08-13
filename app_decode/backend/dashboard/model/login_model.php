<?php
class login_model extends main_model{

    
    function send_login($user, $pass){
        $rtn = $this->_soap['clientes']->send_func("login_user",$user, $pass);
        return $rtn;
    }
    
}

?>
