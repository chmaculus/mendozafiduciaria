<?php

//ini_set('display_errors',1);
include("config/constant.php");
global $encod;

//config inicial
define("TEMP_PATH","_tmp/");
define("UPLOAD_PATH","images/");
define("USERNAME","FRovello");

$url = $_SERVER['HTTP_HOST'];
define("URL_BASE",$url);
//define("URL_PATH","fideicomiso_prod/");
define("URL_PATH","fideicomiso/");
define("URL_SITIO", 'http://'.$url.'/'.URL_PATH);

define("MODULE_DIRECTORY", "general/modules/");
define("FILE_EXPORTAR", URL_SITIO."general/extends/extra/exportar.php");

define("DEVELOPER_MODE",TRUE);
define('TIEMPO_SESSION','1440');
define('NOMBRE_SESSION','nosesion');

define('PERMISOS_ALL','0');
define('PERMISOS_MENU_ALL','0');


define('GENERAL_HELPER', 'general/helper/');
define('GENERAL_PLUG_DIR', 'general/plugin/');
define('GENERAL_CSS_DIR', 'general/css/');
define('GENERAL_JS_DIR', 'general/js/');


define("PATH_OPERATORIAS","uploads/operatorias/");
define("PATH_FIDEICOMISOS","uploads/fideicomisos/");
define("PATH_OPERACIONES","uploads/operaciones/");
define("PATH_REQUERIMIENTOS","uploads/requerimientos/");
define("PATH_GARANTIAS","uploads/garantias/");


define ("ARR_MODULOS", serialize (array ("permisos", "usuarios", "entidades", "clientes", "operatorias", "fideicomisos", "carpetas")));


define('MYSQL_SERVER', 'localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');
//define('MYSQL_DATABASE', 'fiduciaria_prod');
define('MYSQL_DATABASE', 'fiduciaria');


define('SQL_SERVER', 'SVDESARROLLO\SQL2008R2');
define('SQL_USER', 'sa');
define('SQL_PASS', 'Xxzz@2014');
define('SQL_DATABASE', 'MENDOZA_FID');


/*
define('SQL_SERVER', 'MZADB\SQL2008');
define('SQL_USER', 'debo_head');
define('SQL_PASS', 'debo');
define('SQL_DATABASE', 'DEBO_HEAD');
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
    
    $_config["database"]["server"] = MYSQL_SERVER;
    $_config["database"]["user"] = MYSQL_USER;
    $_config["database"]["password"] = MYSQL_PASS;
    $_config["database"]["database"] = MYSQL_DATABASE;
    
    $_config["database"]["serversql"] = SQL_SERVER;
    $_config["database"]["usersql"] = SQL_USER;
    $_config["database"]["passwordsql"] = SQL_PASS;
    $_config["database"]["databasesql"] = SQL_DATABASE;

    /*
    $_config["soap"]['clientes'] = array(
        "wsdl"=>null,
        "location" => "http://localhost/wstercero/clientes",
        "uri" => "clientes",
        "login" => "",
        "password" => "",
    );
    */
    
    $_config['access'][] = "mysql.php";
    $_config['access'][] = "sqlsrv.php";
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
        //"backend/carpeta/carpetas/(:any)"=>"backend/carpeta/carpetas/init/[1]"
    );
    
    $_config['ajax']['encoding'] = "Content-type: text/html; charset=UTF-8";
    
    $_config['plugin'] = array(
        //"jalerts","chosen","datatables","validation","fancybox", "jqgrid","quicksearch","numeric"
    );
    
    // $_config['model'] = "model_ws";
    $_config['default_layout'] = "admin_main.php";
    
    define('_DIR_IMPRIMIR','_tmp/imprimir/');
    
}

?>