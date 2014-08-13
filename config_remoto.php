<?php

ini_set('display_errors',0);
global $encod;

//config inicial

define("TEMP_PATH","_tmp/");
define("UPLOAD_PATH","images/");
define("USERNAME","FRovello");

$url = $_SERVER['HTTP_HOST'];
define("URL_BASE",$url);
define("URL_PATH","mendoza_fiduciaria/");
define("URL_SITIO", 'http://'.$url.'/'.URL_PATH);

define("MODULE_DIRECTORY", "general/modules/");


define("DEVELOPER_MODE",TRUE);
define('TIEMPO_SESSION','1440');
define('NOMBRE_SESSION','nosesion');

define('SQL_SERVER', 'localhost');
define('SQL_USER', 'c18foca');
define('SQL_PASS', 'foca1234');
define('SQL_DATABASE', 'c18foca');

define('GENERAL_PLUG_DIR', 'general/plugin/');
define('GENERAL_CSS_DIR', 'general/css/');
define('GENERAL_JS_DIR', 'general/js/');


define("PATH_OPERATORIAS","uploads/operatorias/");


/*
define('SQL_SERVER', 'SERVER');
define('SQL_USER', 'debo_head');
define('SQL_PASS', 'DEBO');
define('SQL_DATABASE', 'DEBO_HEAD_FP');
*/

define('DEFAULT_FUNCTION_INIT', 'init');


define('LOG_VARS', "log.lg");

if ($encod ){
    define("ENCODED", 1);
    define("MAIN_APP_DIRECTORY", "app/");
}
else{
    define("ENCODED", 0);
    define("MAIN_APP_DIRECTORY", "app_decode/");
}

function get_conf(&$_config){
    
    $_config["default_function"] = DEFAULT_FUNCTION_INIT;
    
    $_config["database"]["server"] = SQL_SERVER;
    $_config["database"]["user"] = SQL_USER;
    $_config["database"]["password"] = SQL_PASS;
    $_config["database"]["database"] = SQL_DATABASE;


    /*
    $_config["soap"]['clientes'] = array(
        "wsdl"=>null,
        "location" => "http://localhost/wstercero/clientes",
        "uri" => "clientes",
        "login" => "",
        "password" => "",
    );
    */
    
    //$_config['access'][] = "sqlsrv.php";
    $_config['access'][] = "mysql.php";
  //  $_config['access'][] = "soap.php";
   
    $_config['js'] = array(
        "jquery.min.js",
        "jquery-ui.min.js",
        "main.js"
        );

    $_config['css'] = array(
        "main.css"
        );

    $_config['route'] = array(
    //    "hola/(:any)/(:any)"=>"tmpx/iniciar/[2]/[1]"
    );
    
    
    $_config['ajax']['encoding'] = "Content-type: text/html; charset=UTF-8";
    
    
    $_config['plugin'] = array(
        "jalerts","chosen","datatables","validation","fancybox", "jqgrid","quicksearch","numeric"
    );
    
   // $_config['model'] = "model_ws";
    $_config['default_layout'] = "admin_main.php";
    
    
    define('_DIR_IMPRIMIR','_tmp/imprimir/');
    
    
    
    
}
?>