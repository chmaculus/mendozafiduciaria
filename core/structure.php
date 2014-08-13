<?php

register_shutdown_function('handleShutdownController', realpath(""));
spl_autoload_register('load_class');

function controller($controller, &$global, $ajax = false) {
    

    if (isset($_POST['controller'])) {
        $app_controller = $_POST['controller'];
    } else {
        $app_controller = $controller;
    }

    $global['_controller_file'] = $app_controller;
    $global['_controller'] = $controller;

    $_function = $global['_function'];
    $_param = $global['_param'];


    
    if (file_exists(MAIN_APP_DIRECTORY . $global['_controller_path'] . "/controller/" . $app_controller . ".php")) {
        t_include(MAIN_APP_DIRECTORY . $global['_controller_path'] . "/controller/" . $app_controller . ".php");
    } else {
        
        include_once("core/view/err_controller.php");
        $e = new Exception();
        $trace = $e->getTrace();
        view_error_controller(MAIN_APP_DIRECTORY . $global['_controller_path'] . "/controller/" . $app_controller . ".php", $controller);
        write_error_log(print_r($trace, true) . "La ruta '".MAIN_APP_DIRECTORY . $global['_controller_path'] . "/controller/" . $app_controller . ".php' no es válidaa");
        exit;
    }

    if (class_exists($app_controller)) {
        $cont = new $app_controller();
    } else {
        include_once("core/view/err_controller.php");
        view_error_class(MAIN_APP_DIRECTORY . $global['_controller_path'] . "/controller/" . $app_controller . ".php", $controller);
        write_error_log("La clase '" . $controller . "' no existe en archivo: '".MAIN_APP_DIRECTORY . $controller . "/controller/" . $app_controller . ".php'");
        exit;
    }

    if ($_function) {


        $init_function = substr($_function, 0, 1);
        if (method_exists($cont, $_function) && $init_function != '_') {

            $cont->get_globals($global);
            if ($_param == false) {

                $ev = '$cont->' . $_function . "();";
            } else {
                
                for ($i = 0; $i < count($_param); $i++) {
                    $_param[$i] = "'" . $_param[$i] . "'";
                }
                $ev = '$cont->' . $_function . '(' . implode(", ", $_param) . ');';
            }
            $cont_eval = @eval($ev);
        } else {
            include_once("core/view/err_controller.php");
            view_error_func(MAIN_APP_DIRECTORY . $global['_controller_path'] . "/controller/" . $app_controller . ".php", $controller);
            write_error_log("Metodo '" . $_function . "' no existe, el metodo es privado o reservado para respuestas ajax");
            exit;
        }
    }
    if (!$ajax) {
        $cont->get_js_vars();
    }

}

function handleShutdownController($path) {
    $ar_err = error_get_last();

    if ($ar_err) {

        if (!isset($_SESSION['FILE'])) {

            $file = $ar_err['file'];
            //        $file  = $_SESSION['FILE'];
            echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error en archivo: " . $file . "<br/>";
            echo $ar_err['message'];
            echo "<br/>En linea " . $ar_err['line'];

            write_error_log("Archivo: " . $file . "\nMensaje: " . $ar_err['message'] . "\nLinea: " . $ar_err['line'], $path . "/log");

            exit;
        } else {
            echo "asd";
        }
    }
}

function write_error_log($err_string, $path = "log") {
    /*if (!is_dir($path)){
        mkdir($path);
    }
    $handle = fopen($path . "/error.log", "a+");

    $f = 0;
    if (file_exists("Log/")) {
        $f = fopen('Log/PHP_ERR_' . date("Y-m-d") . '.log', 'a+');
    }

    fwrite($f, date('Y-m-d H:i:s')." \n\n " . print_r(debug_backtrace(), true) . " \r\n");
    fclose($f);    
    
    if ($handle) {
        $str = date("d/m/Y H:i") . "\n" . $err_string . "\n\n\n";
        fwrite($handle, $str);
        fclose($handle);
    } else {

    }*/
    exit;
}

function view_local_mod($modulo, $params = array()) {
    $gl = $GLOBALS['global'];
    $db = $gl['_db'];


    $view = "general/modules/lmod.php";
    include($view);
}
function local_mod($modulo, $params = array()) {
    $gl = $GLOBALS['global'];
    $db = $gl['_db'];

    while (ob_get_level() > 0)
        ob_end_flush();
    ob_start();
    $view = "general/modules/lmod.php";
    @include($view);
    $this_string = ob_get_contents();
    ob_clean();
    return $this_string;
}

function load_class($class) {

    $cl = explode("_", $class);
    if (count($cl) == 2) {
        switch ($cl[1]) {
            case "controller":

                include("general/extends/controller/" . $class . ".php");
                break;
            case "model":

                include("general/extends/model/" . $class . ".php");
                break;
        }
    }
}


class MyException extends Exception {

    public $file;
    public $line;

    public static function errorHandler($errno, $errstr, $errfile, $errline) {
        $e = new self();
        $e->message = $errstr;
        $e->code = $errno;
        $e->file = $errfile;
        $e->line = $errline;

        $f = 0;
        if (file_exists("Log/")) {
            $f = fopen('Log/PHP_ERR_' . date("Y-m-d") . '.log', 'a+');
        }
        
        fwrite($f, date('Y-m-d H:i:s')." \n\n " . print_r($e, true) . " \r\n");
        fclose($f);

        throw $e;
    }

}

?>