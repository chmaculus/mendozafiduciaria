<?php

class controller {

    public $_global;
    public $mod;
    public $gmod;
    private $_mod;
    private $_css;
    private $_js;
    private $_plug;
    private $_data;
    public $_js_array;
    public $_js_var;
    private $_layout;
    private $_layout_module;
    private $_controller;
    private $_db;
    private $_param;
    private $_module = false;
    private $_rtn_render = "";

    public function get_globals(&$global) {
        $this->_global = &$global;


        $this->_css = $global['_css'];
        $this->_js = $global['_js'];
        $this->_plug = $global['_plug'];

        if (isset($global['_model'])) {
            $this->gmod = $this->_init_model($global['_model'], true);
        } else {
            $this->gmod = "";
        }

        $this->init_model();
    }

    function get_segmentos() {
        return $this->_global['_segmentos'];
    }

    public function setJs($arr, $general = false) {
        if ($this->_module) {
            $path = MAIN_APP_DIRECTORY . "/" . $this->_global['_controller_path'] . "/js/";
            $this->_js->add($arr, $general, $path);
        } else {
            $this->_js->add($arr, $general);
        }
    }

    public function setCss($arr, $general = false) {
        if ($this->_module) {
            $path = MAIN_APP_DIRECTORY . "/" . $this->_global['_controller_path'] . "/css/";
            $this->_css->add($arr, $general, $path);
        } else {
            $this->_css->add($arr, $general);
        }
    }

    public function setPlug($plug) {
        $this->_plug->add($plug);
    }

    public function init_model() {

        if (isset($this->_mod['model'])) {

            $this->mod = $this->_init_model($this->_mod['model'], $this->_mod['general']);
        }
    }

    public function model($model, $general = false) {

        $this->_mod['model'] = $model;
        $this->_mod['general'] = $general;
    }

    public function load_model($model, $dir = false) {
        $mod = $this->_init_model($model, false, $dir);
        return $mod;
    }

    public function load_gmodel($model) {
        $mod = $this->_init_model($model, true);
        return $mod;
    }

    public function __construct() {
        $this->mod = array();
    }

    public function get_js_vars($bprint = true, $controller = false) {
        $echo = '';

        $echo .= '
                <script type="text/javascript">';
        if ($this->_global['js_var']) {
            foreach ($this->_global['js_var'] as $key => $value) {

                if ($controller) {
                    $echo .= "_" . $controller . "." . $key . " = '" . $value . "';
                        ";
                } else {
                    $echo .= "    var " . $key . " = '" . $value . "';
                        ";
                }
            }
        }

        if ($this->_global['js_array']) {
            foreach ($this->_global['js_array'] as $key => $value) {
                $echo .= "var tmp = '" . json_encode($value) . "';
                    ";
                if ($controller) {
                    $echo .= "_" . $controller . "." . $key . " = eval(tmp);// $.parseJSON(tmp);
                    ";
                } else {
                    $echo .= "var " . $key . " = eval(tmp);//$.parseJSON(tmp);
                        ";
                }
            }
        }

        $echo .= '</script>
                ';

        if ($bprint)
            echo $echo;
        else {
            return $echo;
        }
    }

    public function set_layout($layout) {
        $this->_global['_layout'] = $layout;
        if ($this->_module) {
            $this->_layout_module = $layout;
        } else {
            $this->_layout = $layout;
        }
    }

    public function _init_model($model, $general = false, $dir = false) {
        $controller = $this->_global['_controller'];
        $path = "";
        if ($general) {
            $path = "general/extends/model/";
        } else {

            if (!$dir) {
                $dir = $this->_global['_controller_path'];
                ;
            }

            $path = MAIN_APP_DIRECTORY . $dir . "/model/";
        }

        if (isset($this->_global['_db'])) {
            $db = $this->_global['_db'];
        } else {
            $db = false;
            return false;
        }

        $mod = false;

        //verifica error si existe archivo de modelo creado
        $modelo = $model;

        if (file_exists($path . $model . ".php")) {
            if (!$general) {
                t_include($path . $model . ".php");
            } else {
                @include_once($path . $model . ".php");
            }

            //verifica error de existencia de la clase
            if (class_exists($model)) {
                $mod = new $model($this->_global);
            } else {

                include_once("core/view/err_controller.php");

                view_error_model_class($path . $model . ".php", $model);
                write_error_log("La clase '" . $modelo . "' no existe en archivo " . MAIN_APP_DIRECTORY . "'/" . $controller . "/model/" . $modelo . ".php'");
                exit;
            }
        } else {
            include_once("core/view/err_controller.php");
            view_error_model($controller, $model);
            write_error_log("Error en '" . MAIN_APP_DIRECTORY . "/" . $controller . "/controller/" . $controller . "' El archivo '" . $path . $model . ".php" . "' no existe.'");
            exit;
        }
        return $mod;
    }

    function get_controller_name() {
        return $this->_global['_controller'];
    }

    public function view($view, $vars = array(), $dir = false, $encode = false, $comprobar = false) {

        $bajax = $this->_global['_bajax'];
        if ($bajax) {
            header('Content-type: text/html; charset=utf-8');
        }

        header('Content-type: text/html; charset=utf-8');

        if ($vars)
            extract($vars);

        $controller = $this->_global['_controller_path'];

        //generamos vista
        $path_view = "";
        if ($dir) {
            $path_view = MAIN_APP_DIRECTORY . $dir . "/view/";
        } else {
            $path_view = MAIN_APP_DIRECTORY . $controller . "/view/";
        }


        $vista_err = $view = $path_view . $view . ".php";

        //se verifica que exista el archivo
        if (!file_exists($view)) {
            if (!$comprobar) {
                echo "no existe el archivo de vista " . $vista_err . " en el controlador: " . $controller;
            }
            return;
        }

        ob_start();

        $vista_content = '';
        if ($encode)
            $vista_content = $encode . "
            " . $vista_content;

        $script = '';

        $script_php = '';
        $script_php .= '$PATH_VIEW = "' . $path_view . '";';

        if (isset($vars['_js_array']) || isset($vars['_js_var'])) {

            $script .= '<script type="text/javascript">';
            if (isset($vars['_js_var'])) {
                foreach ($vars['_js_var'] as $key => $val) {
                    $script .= 'var ' . $key . ' = "' . $val . '";';
                }
            }
            if (isset($vars['_js_array'])) {
                foreach ($vars['_js_array'] as $key => $arr) {
                    $script .= 'var ' . $key . ' = [];';
                    foreach ($arr as $item) {
                        $script .= "var tmp = '" . json_encode($item) . "';";
                        $script .= $key . 'eval(tmp);//.push( $.parseJSON(tmp) );';
                    }
                }
            }
            $script .= '</script>';
        }


        @include_once($view);
        $ar_err = error_get_last();

        if ($ar_err) {
            //   $_SESSION['FILE'] = $view;
            include_once("core/view/err_controller.php");
            view_error_warning($view, "www");
            exit;
        }

        $this_string = ob_get_contents();
        ob_clean();
        $this_string .= $script;

        return $this_string;
    }

    //Arrancar app como modulo
    function init_mod($mod) {
        $this->_module = $mod;
        $this->_layout_module = "module.php";
    }

    //Selecciona si se muestra en layout o solo modulo
    public function render($vars) {

        switch ($this->_module) {
            case 2:

                $this->_rtn_render = $this->_module_render($vars);
                break;
            case 1:
                ////segunda opcion insideheader es cuando se carga como modulo sin ajax y los css
                // y los js se cargan dentro del header de la pagina y no como contendio de texto
                $this->_rtn_render = $this->_module_render($vars, true);
                break;

            default:

                $this->_main_render($vars);
                break;
        }
        if ($this->_module == 1) {
            
        } else {
            
        }
    }

    function _module_render($vars, $binsideheader = false) {

        extract($vars);

        $js = $this->_global['_js'];
        $css = $this->_global['_css'];
        $plug = $this->_global['_plug'];

        $controller = $this->_global['_controller'];
        $controller_path = $this->_global['_controller_path'];

        $rtn = "";
        if (!$binsideheader) {
            $rtn .= $plug->get_script();
            $rtn .= $css->get_script_css();
            $rtn .= $js->get_script_js();
        }

        //generamos vista
        ob_start();


        if (file_exists(MAIN_APP_DIRECTORY . $controller_path . "/view/" . $this->_layout_module)) {
            $layout = (MAIN_APP_DIRECTORY . $controller_path . "/view/" . $this->_layout_module);
            @include_once(MAIN_APP_DIRECTORY . $controller_path . "/view/" . $layout);
        } else {
            $layout = "general/layout/module.php";
            @include_once($layout);
        }


        $ar_err = error_get_last();

        if ($ar_err) {
            include_once("core/view/err_controller.php");
            view_error_warning($controller_path . "/view/" . $layout, "www");
            write_error_log("Archivo: " . "general/layout/" . $layout . "\nMensaje: " . $ar_err['message'] . "\nLinea: " . $ar_err['line']);
            exit;
        }

        $rtn .= ob_get_contents();
        ob_clean();
   /*     $script_ini = '
        <script type="text/javascript">
            var _' . $controller . ' = {};
            _' . $controller . ' = generic.copy();
            _' . $controller . '.URL = "' . $controller_path . '" ;
            _' . $controller . '.init();
            _this_app =  _' . $controller . '.copy();
            var generic = new class_generic;
            
        </script>
        
    ';*/
        $script_ini = '
        <script type="text/javascript">
        </script>            
';
        $this->_global['js_array'] = $this->_js_array;
        $this->_global['js_var'] = $this->_js_var;
        $script_ini .= $this->get_js_vars(false, $controller);

        return $script_ini . $rtn;
    }

    function _main_render($vars) {
        extract($vars);


        $layout = $this->_global['_layout'];

        $js = $this->_global['_js'];
        $css = $this->_global['_css'];
        $plug = $this->_global['_plug'];
        $script_ini = $this->_global['script_ini'];
        
        $this->_global['js_array'] = $this->_js_array;
        $this->_global['js_var'] = $this->_js_var;

        $jscript = $this->get_js_vars(false, $this->_global['_controller']);
        $this->_js->add_script($script_ini . $jscript);
        $segmentos = $this->get_segmentos();
        //generamos vista
        ob_start();
        @include_once("general/layout/" . $layout);

        $ar_err = error_get_last();

        if ($ar_err) {
            include_once("core/view/err_controller.php");
            view_error_warning("general/layout/" . $layout, "www");
            write_error_log("Archivo: " . "general/layout/" . $layout . "\nMensaje: " . $ar_err['message'] . "\nLinea: " . $ar_err['line']);

            exit;
        }

        $this_string = ob_get_contents();
        ob_clean();

        echo $this_string . $jscript;
    }

    public function load_app($controller, $param = false, $function_init = DEFAULT_FUNCTION_INIT, $ajax = false) {

        $app_controller = $controller;
        $name_class_controller = $controller;
        //verificamos si existe ruta
        $arr_controller = explode("/", $controller);
        if (count($arr_controller) > 1) {
            $name_class_controller = $arr_controller[count($arr_controller) - 1];
        }

        $controller_path = $ajax ? $this->_global['_controller_path'] : implode("/", $arr_controller);


        $_function = $function_init ? $function_init : $this->_global['_function'];
        $_param = $param;

        if (file_exists(MAIN_APP_DIRECTORY . $controller_path . "/controller/" . $name_class_controller . ".php")) {
            t_include(MAIN_APP_DIRECTORY . $controller_path . "/controller/" . $name_class_controller . ".php");
        } else {
            include_once("core/view/err_controller.php");
            $e = new Exception();
            $trace = $e->getTrace();
            view_error_controller(MAIN_APP_DIRECTORY . $controller_path . "/controller/" . $name_class_controller . ".php", $name_class_controller);
            write_error_log(print_r($trace, true) . "La ruta '" . MAIN_APP_DIRECTORY . $controller_path . "/controller/" . $name_class_controller . ".php' no es válidaa");
            exit;
        }

        if (class_exists($name_class_controller)) {

            $cont = new $name_class_controller();

            //se inicializa en diferentes formas si ingresa por ajax(2) o por php(1)
            if ($ajax) {
                $cont->init_mod(2);
            } else {
                $cont->init_mod(1);
            }
        } else {
            include_once("core/view/err_controller.php");
            view_error_class(MAIN_APP_DIRECTORY . $this->_global['_controller_path'] . "/controller/" . $name_class_controller . ".php", $name_class_controller);
            write_error_log("La clase '" . $name_class_controller . "' no existe en archivo: '" . MAIN_APP_DIRECTORY . $controller_path . "/controller/" . $name_class_controller . ".php'");
            exit;
        }

        if ($_function) {
            $init_function = substr($_function, 0, 1);
            if (method_exists($cont, $_function) && $init_function != '_') {

                $newglobal = $this->_global;

                $newglobal['_controller_path'] = $controller_path;
                $newglobal['_controller'] = $name_class_controller;

                if ($ajax) {
                    $newglobal['_css'] = new css_header(array(), $controller_path);
                    $newglobal['_js'] = new js_header(array(), $controller_path);
                }

                $cont->get_globals($newglobal);


                if ($_param == false) {
                    $ev = '$cont->' . $_function . "();";
                } else {

                    $tmp_params = array();

                    for ($i = 0; $i < count($_param); $i++) {
                        if (!isset($_param[$i]))
                            $_param[$i] = array();

                        if (is_array($_param[$i])) {
                            $tmp_params[$i] = 'array(';
                            $arr_params = array();
                            foreach ($_param[$i] as $key => $val) {

                                $arr_params[] = "'" . $key . "' =>'" . $val . "'";
                            }
                            $tmp_params[$i] .= implode(", ", $arr_params) . ')';
                        } else {


                            $tmp_params[$i] = "'" . $_param[$i] . "'";
                        }
                    }

                    $ev = '$cont->' . $_function . '(' . implode(", ", $tmp_params) . ');';
                }
                //      echo $ev;        
                @eval($ev);


                return $cont->_rtn_render;
            } else {
                include_once("core/view/err_controller.php");
                view_error_func(MAIN_APP_DIRECTORY . $controller_path . "/controller/" . $app_controller . ".php", $controller);
                write_error_log("Metodo '" . $_function . "' no existe, el metodo es privado o reservado para respuestas ajax");
                exit;
            }
        }
    }

    public function x_init_mod() {
        $modulo = $_POST['modulo'];
        $params = isset($_POST['params']) ? $_POST['params'] : false;
        echo $this->load_app($modulo, $params, "init", true);
    }

    public function load_mod($modulo, $params = array()) {
        $mod = MODULE_DIRECTORY . $modulo;
        if (file_exists($mod)) {
            foreach (scandir($mod) as $file) {
                if (strpos($file, ".css")) {
                    $this->_css->add($mod . "/" . $file);
                }
                if (strpos($file, ".js")) {
                    $this->_js->add($mod . "/" . $file);
                }
            }
            include_once("core/modules/lmod.php");
            $mod = new newmod($modulo, $params);
        }
        return $mod->show();
    }

    public function __destruct() {
        // echo "Destruye";
    }

    public function pre_controller() {
        
    }

    public function post_controller() {
        
    }

}

?>