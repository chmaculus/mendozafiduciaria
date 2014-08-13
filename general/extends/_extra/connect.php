<?php 
    
    $encod = 1;
    require_once('../../../core/helper/util.php');
    t_include("../../../config_enc.php");

    $_config = array();
    get_conf($_config);

    if (isset($_config['access'])){
        foreach($_config['access'] as $access){
            include("../../../core/model/".$access);
        }
        $cnn = $_db;
    }

?>