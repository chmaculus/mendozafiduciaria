<?php

//include("core/helper/util.php");




//inicia configuracion en $_config
$_config = array();
get_conf($_config);

$_plug = array();
include("general/plugin/plugin.php");
get_plugin($_plug);



$__params = isset($_GET['url']) ? $_GET['url'] : "main";
$segmentos_url = $segmentos_tmp = explode("/",$__params);
$segmentos = $segmentos_tmp = explode("/",$__params);


//busca que porcion de los segmentos representan path real

/*
if ($segmentos[0]=='css'){
    include("332222298.php");
    die();
}
*/

//inicializa clase controller
include("core/class/controller.php");

//inicializa clase model
include("core/class/model.php");

//funcion de llamada a controller
include("core/structure.php");

//inicializa clases para agregar JavaScript
include("core/header/js.php");

//inicializa clases para agregar CSS
include("core/header/css.php");

//inicializa clases para agregar CSS
include("core/header/plug.php");


list($m,$y) = explode("/",date("m/Y"));
$key_set_month = ($m."154"*$y-$m."2"-$y."4" + ceil(16784975 / $m)).($m."852"*$y-$m."2"-$y."4" + ceil(22548794 / $m));

$key = file_exists("key") ? file_get_contents("key") : 0;
$keys = explode("|",$key);

$blic = true;
$keys_always= "a8d5ekf78d6e5ws7xq5";
foreach($keys as $key){
    if ($key!=$key_set_month && $key!=$keys_always){
        $blic = false;
    }
    else{
        $blic = true;
        break;
    }
}

if (!$blic){
    echo "LICENCIA EXPIRADA";
    die();
}


//define ruta de busqueda de controlador activo
//////////
//se busca en las rutas establecidas
////////
$_arr_path_found = false;

$_param = "";

//base para comparar


$bfound = false;
$substr_count = substr_count($__params, "/");
$path_count = substr_count($segmentos[0], "/");



if (!( isset($_POST) && count($_POST)>0) ){
     foreach ($_config['route'] as $path => $route) {

        //si tiene la misma cantidad de segmentos
        if (substr_count($path, "/") == $substr_count - $path_count) {

            //cantidad de parametros
            $cant_param = substr_count($path, "(:any)");


            //cantidad de segmentos
            //base para comparar

            $segmento_base = array_slice($segmentos, 0, count($segmentos) - $cant_param);
            $segmento_base = implode("/", $segmento_base);


            $segmentos_path = explode("/", $path);
            $path_base = array_slice($segmentos_path, 0, count($segmentos_path) - $cant_param);
            $path_base = implode("/", $path_base);


            $param = array_slice($segmentos, count($segmentos_path) - $cant_param);
            // $param = implode("/", $param);
            //compara que el inicio de route encontrado con la mimsa cantidad de segmentos se correponda
            //con el segmento base de nuestra url
            $bfound = false;

            if (strpos($segmento_base, $path_base) === 0) {
                
                $bfound = true;
                for ($i = 0; $i < count($param); $i++) {
                    $route = str_replace("[" . ($i + 1) . "]", $param[$i], $route);
                }
            }
            $_arr_path_found = $route;
            if ($bfound)
                break;
        }
    }
 }
 
 

//si ha sido encontrado en el array de routes
$op = array();
if ($bfound){
    $op = explode("/",$_arr_path_found);

}
else{
    $op = $segmentos;
}
$segmentos = $segmentos_tmp = $op;



//buscamos el path real
$pathfound = false;

for($i = count($segmentos)-1 ; $i > 0  ; $i--){
    
    $path = implode("/",$segmentos_tmp);
    

    if (is_dir(MAIN_APP_DIRECTORY.$path)){
        //
        $pathfound = implode("/",array_splice($segmentos, 0,$i+1));
        array_unshift($segmentos,$pathfound);        
        break;
    }
    else{

    }
    array_pop($segmentos_tmp);
    
}


$op = $segmentos;

if (count($op)>2 ){
    $_param = array_slice($op, 2); 

}


$script_ini= '';

if (isset($op[0])){
    $path_count = explode("/",$op[0]);
    
    $_controller = array_pop($path_count);
    
    $_controller_path = $op[0];

}
else{
    $_controller = false;
    $_controller_path = false;
}
//$_controller = isset($op[0]) ? array_pop(explode("/",$op[0])) : false;
$_function = isset($op[1]) ? $op[1] : $_config['default_function'];

$bajax = (strpos ($_function,"x_")===false) ? false : true;

$plug_string = "";
foreach($_plug as $key=>$val){
    $plug_string .= "__plugs['".$key."'] = {
        css : '".$_plug[$key]['css']."',
        js : '".$_plug[$key]['js']."',
        dir : '".GENERAL_PLUG_DIR.$key."/'
    };
        ";
} 

if (!$bajax) {

    $script_ini = '
        <script type="text/javascript">
            var _' . $_controller . ' = { URL : "' . $_controller_path . '" };
            var _url_sitio = "' . URL_SITIO . '";
            var __plugs = [];
            '.$plug_string .'
        </script>
    ';
}

//variable que contiene los elementos utilizados en todo el sistema
$global = array(
    '_function' => $_function,
    '_param' => $_param,
    '_segmentos' => $segmentos_url,
    '_controller' => $_controller,
    '_controller_path' => $_controller_path,
    '_ajax' => $_config['ajax'],
    '_js' => new js_header($_config['js'], $_controller_path),
    '_css' => new css_header($_config['css'], $_controller_path),
    '_plug' => new plug_header($_plug, $_controller, $_controller_path),
 //   '_model' => $_config['model'],
    '_layout' =>  $_config['default_layout'],
    '_bajax' =>  $bajax,
    'script_ini' =>  $script_ini,
    'js_var' =>  array(),
    'js_array' =>  array()
);

//inicializa origenes de datos de modelos (mysql, soap)
if (isset($_config['access'])){
    foreach($_config['access'] as $access){
        include("core/model/".$access);
    }
}


if($_controller){
    controller($_controller, $global, $bajax);
    
}
    


?>