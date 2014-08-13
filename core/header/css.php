<?php

class css_header {

    private $_css;
    public $_controller;
    public $_def_css;

    public function __construct($css, $controller) {
        $this->_css = array();
        $this->_def_css = $css;
        $this->_controller = $controller;
    }

    public function add($css_file, $general = false, $path = "") {
        if ($general) {

            if (gettype($css_file) == 'array')
                $this->_def_css = $css_file;
            if (gettype($css_file) == 'string') {
                $this->_def_css[] = $css_file;
            }
        } else {
            if (gettype($css_file) == 'array') {
                foreach ($css_file as $file) {
                    $this->_css[] = $path . $file;
                }
                $this->_css = array_merge($this->_css, $css_file);
            }
            if (gettype($css_file) == 'string')
                $this->_css[] = $path . $css_file;
        }
    }

    public function get_script_css() {
        $rtn = "";
        foreach ($this->_css as $css) {
            if (strpos($css, "/") !== false) {
                $full_url = URL_SITIO . MAIN_APP_DIRECTORY . '/' . $this->_controller . '/css/';
                if (file_exists($css)) {
                    $tmp = file_get_contents($css);
                    $rtn .= str_replace(array("url('images", 'url("images'), array("url('".$full_url."images",'url("'.$full_url.'images'), $tmp);
                }
            } else {
                $full_url = URL_SITIO . MAIN_APP_DIRECTORY . '/' . $this->_controller . '/css/';
                if (file_exists(URL_SITIO . MAIN_APP_DIRECTORY . '/' . $this->_controller . '/css/' . $css)){
                    $tmp = file_get_contents(URL_SITIO . MAIN_APP_DIRECTORY . '/' . $this->_controller . '/css/' . $css);
                    $rtn .= str_replace(array("url('images"), array("url('".$full_url."images"), $tmp);
                }
            }
        }
        if ($rtn) {
            $rtn = "<style>" . $rtn . "</style>";
        }
        return $rtn;
    }

    public function render() {
        $rtn = "";

        foreach ($this->_def_css as $css) {

            if (file_exists("general/css/" . $css)) {
                $rtn .= '
                    <link rel="stylesheet" href="' . URL_SITIO . 'general/css/' . $css . '" />';
            }
        }
        foreach ($this->_css as $css) {
            if (strpos($css, "/") !== false) {
                if (file_exists($css)) {
                    $rtn .= '
                    <link rel="stylesheet" href="' . $css . '" />';
                }
            } else {
                if (file_exists(MAIN_APP_DIRECTORY .  $this->_controller . '/css/' . $css)) {

                    $rtn .= '
                    <link rel="stylesheet" href="' . URL_SITIO . MAIN_APP_DIRECTORY .  $this->_controller . '/css/' . $css . '" />';
                }
            }
        }

        return $rtn;
    }

}

?>