<?php
class plug_header{
    public $_css;
    public $_js;
    public $_plug;
    public $_plug_active;
    public $_controller;
    public $_controller_path;

    public function __construct($plug, $controller, $_controller_path = ""){
        $this->_css = array();
        $this->_js = array();
        $this->_plug_active = array();
        $this->_controller = $controller;
        $this->_controller_path = $_controller_path;
        
        $this->_plug = $plug;
    }
    
    public function add($plug_file){

        if (gettype ($plug_file ) == 'array'){
            
            foreach($plug_file as $file){
                $tmp_css = array();
                $tmp_js = array();                
                if (isset($this->_plug[$file]['css'][$this->_controller_path])){
                    foreach($this->_plug[$file]['css'][$this->_controller_path] as $css_file){
                        $tmp_css[] = $css_file;
                    }
                }
                else{
                    foreach($this->_plug[$file]['css'] as $css_file){
                        $tmp_css[] = $css_file;
                    }
                }
                
                
                if (isset($this->_plug[$file]['js'][$this->_controller_path])){
                    foreach($this->_plug[$file]['js'][$this->_controller_path] as $js_file){
                        $tmp_js[] = $js_file;
                    }
                }
                else{
                    foreach($this->_plug[$file]['js'] as $js_file){
                        $tmp_js[] = $js_file;
                    }
                }
               
                $this->_plug_active[] = array("file"=>$file,"css"=>$tmp_css, "js"=>$tmp_js);
            }
            
            //print_array($this->_plug_active);
        }
        else
        if (gettype ($plug_file ) == 'string'){
            
            
            if (isset($this->_plug[$file]['css'][$this->_controller_path])){
                foreach($this->_plug[$plug_file]['css'][$this->_controller_path] as $css_file){
                    $this->_css[] = $css_file;
                }
            }
            else{
                foreach($this->_plug[$plug_file]['css'] as $css_file){
                    $this->_css[] = $css_file;
                }
            }
            
            
            
            if (isset($this->_plug[$file]['js'][$this->_controller_path])){
                foreach($this->_plug[$plug_file]['js'][$this->_controller_path] as $js_file){
                    $this->_js[] = $js_file;
                }
            }
            else{
                foreach($this->_plug[$plug_file]['js'] as $js_file){
                    $this->_js[] = $js_file;
                }
            }
            $this->_plug_active[] = array("file"=>$plug_file,"css"=>$tmp_css, "js"=>$tmp_js);
        }
    }
    
    public function get_script(){
        $rtn_css  = "";
        $rtn_js  = "";
        foreach($this->_plug_active as $plugs){
            foreach($plugs['css'] as $css){
                if (!is_array($css))
                    $rtn_css .= file_get_contents('general/plugin/'.$plugs['file'].'/'.$css);
            }
            
            foreach($plugs['js'] as $js){
                if (!is_array($js))
                    $rtn_js .= file_get_contents('general/plugin/'.$plugs['file'].'/'.$js);
                
            }
        }
        
        if ($rtn_css) {
            $rtn_css = "<style>" . $rtn_css . "</style>";
        }
        
        if ($rtn_js) {
            
            $rtn_js = "<script>" . $rtn_js . "</script>";
        }
        
        return $rtn_css.$rtn_js;
    }
    
    public function render(){
        $rtn  = "";


        foreach($this->_plug_active as $plugs){

            foreach($plugs['css'] as $css){
                if (!is_array($css))
                $rtn .= '
                    <link rel="stylesheet" href="'.URL_SITIO.'general/plugin/'.$plugs['file'].'/'.$css.'" />';
            }
            
            foreach($plugs['js'] as $js){
                if (!is_array($js))
                    $rtn .= '<script src="'.URL_SITIO.'general/plugin/'.$plugs['file'].'/'.$js.'" type="text/javascript"></script>';
            }
        }

        
        return $rtn;
    }
}


?>