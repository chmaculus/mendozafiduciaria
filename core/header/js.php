<?php

class js_header {

    private $_js;
    public $_def_js;
    public $_script;
    public $_controller;

    public function __construct($js, $controller) {
        $this->_js = array();
        $this->_def_js = $js;
        $this->_controller = $controller;
    }

    public function add($js_file, $general = false, $path = "") {
        if ($general) {
            if (gettype($js_file) == 'array')
                $this->_def_js = $js_file;
            if (gettype($js_file) == 'string')
                $this->_def_js[] = $js_file;
        }else {
            if (gettype($js_file) == 'array'){
                foreach($js_file as $file){
                    $this->_js[] = $path.$file;
                }
            }
            if (gettype($js_file) == 'string')
                $this->_js[] = $path.$js_file;
        }
    }
    
    function add_script($script){
        $this->_script = $script;
    }

    public function get_script_js() {
        $rtn = "";
        foreach ($this->_js as $js) {
            
            if (strpos($js, "/") !== false) {
                if (file_exists($js)) {
                    $rtn .= file_get_contents($js);
                }
            } else {

                if (file_exists(MAIN_APP_DIRECTORY.'/' . $this->_controller . '/js/' . $js))
                    $rtn .= file_get_contents(URL_SITIO.MAIN_APP_DIRECTORY.'/' . $this->_controller . '/js/' . $js);
            }
        }
        if ($rtn) {
            
            $rtn = "<script>" . $rtn . "</script>";
        }
        
        return $rtn;
    }

    public function render() {
        $rtn = "";
        if (file_exists("core/js/")) {

            foreach (scandir("core/js/") as $file) {
                if (strpos($file, ".js") !== false) {
                    $rtn .= '<script src="'.URL_SITIO.'core/js/' . $file . '" type="text/javascript"></script>
                    ';
                }
            }
        }        
        $rtn .= $this->_script;
        foreach ($this->_def_js as $js) {
            if (file_exists("general/js/" . $js))
                $rtn .= '<script src="'.URL_SITIO.'general/js/' . $js . '" type="text/javascript"></script>
            ';
        }
        foreach ($this->_js as $js) {
            if (strpos($js, "/") !== false) {
                if (file_exists($js)) {
                    $rtn .= '<script src="' . $js . '" type="text/javascript"></script>
                    ';
                }
            } else {

                if (file_exists(MAIN_APP_DIRECTORY. $this->_controller . "/js/" . $js))
                    $rtn .= '<script src="'.URL_SITIO.MAIN_APP_DIRECTORY. $this->_controller . '/js/' . $js . '" type="text/javascript"></script>
                ';
            }
        }



        return $rtn;
    }

}

?>