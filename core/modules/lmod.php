<?php


class newmod{
    
    var $_dir = "";
    var $_mod_name = "";
    var $_params = "";
    var $_view = "";
    
    function newmod($modulo, $params){
        $this->_mod_name = $modulo;
        $this->_dir = MODULE_DIRECTORY.$modulo."/";
        $this->_params = $params;
        
        $data = array();
        include($this->_dir."controller.php");
        
        $this->_view = $this->view("view", $data);
        
        $js_init = str_replace(array("_","-"), "", $modulo);
        ?>
            <script type="text/javascript">
                var x = 'init_<?=$js_init?>()';
                var _URL_<?=  strtoupper($js_init)?> = 'modulo.php?rmod=<?=$modulo?>';
                if(typeof (window.init_<?=$js_init?>) == 'function') {
                    eval(x);
                }    

            </script>
            <?php

    }
    
    function view($view, $data=array()){
        extract($data);
        //generamos vista
        ob_start(); 
        @include($this->_dir.$view.".php");

        $rtn = ob_get_contents();
        ob_clean();  
        
        return $rtn;
    }
    
    function show(){
        return $this->_view;
    }
}





