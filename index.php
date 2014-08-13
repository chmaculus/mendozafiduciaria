<?php

//ini_set('display_errors',0);

$encod = 0;

date_default_timezone_set("America/Argentina/Mendoza");
error_reporting(E_ALL | E_STRICT);
session_start();


include("core/helper/util.php");

if ($encod ){
    t_include("config_enc.php");
}
else{
    include_once('config.php');
}




if ($encod ){
    t_include("332256458_enc.php");
}
else{
    include("332256458.php");
}





?>