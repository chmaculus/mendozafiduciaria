<?php

class notificaciones extends main_controller{
    
    function notificaciones(){
        $this->mod = $this->model("notificaciones_model");
    }
    
    function init( $iid = 0 ){
        
        //$this->_get_loged();
        $this->setCss( array("init.css") );
        $this->setJs( array( "init.js") );
        
        $datax = array();
        $datax['main'] = $this->_obtener_main( $iid );
        $datax['titulo']= "Administracion";
        
        //$this->_js_var['_bb'] = 'bbbbbbb';
        $this->set_layout("module.php");            
        
        $this->render($datax);
    }
    
    function _obtener_main( $iid ){
        $getid = $_SESSION["USERADM"];
        
        if ($iid>0)
            $getid = $iid;
        $data['carpetas_pendientes'] = $this->get_carpetas_pendientes( $getid );
        return $this->view("notificaciones", $data);
    }
    
    function get_carpetas_pendientes($id){
        $obj = $this->mod->get_carpetas_pendientes($id);
        $tmp = $obj?$obj:array();
        return $tmp;
    }
    
    function x_cambiar_leido_traza(){
        $idt = $_POST["idt"];
        $obj_arr = isset($_POST["obj_arr"])?$_POST["obj_arr"]:array();
        $obj = $this->mod->cambiar_leido_traza($idt, $obj_arr);
        return $obj;
    }
    
    function x_traza_autor(){
        $idt = $_POST["idt"];
        $para_aux1 = $_POST["para_aux1"];
        $respuesta = $_POST["respuesta"];
        $par_su2 = isset($_POST["par_su2"])&& $_POST["par_su2"]==1?$_POST["par_su2"]:0;
        $obj = $this->mod->traza_autor($idt,$respuesta,$para_aux1,$par_su2);
        return $obj;
    }
    
    function x_send_traza(){
        $obj = $_POST["obj"];
        $obj["FECHA"] = date('Y-m-d H:i:s');
        $obj["CARTERADE"] = $_SESSION["USERADM"];
        
        $id_ope = $obj["ID_OPERACION"];
        
        $destino = $this->mod->get_destino($id_ope);
        
        //log_this('qqqq.log',print_r($destino,1));
        
        if ($destino>0 && $destino != $_SESSION["USERADM"] ){
            $obj["CARTERADE"] = $destino;
        }else{
            $obj["CARTERADE"] = $_SESSION["USERADM"];
        }
        
        $rtn = $this->mod->send_traza($obj);
        echo $rtn?$rtn:'-1';
    }
    
    function x_get_carpetas_pendientes_cont(){
        $rtn = $this->mod->get_carpetas_pendientes_cont($_SESSION["USERADM"]);
        echo $rtn?$rtn:'-1';
    }
    
    function x_cargar_motivo_rechazo(){
        $contMotivo = $_POST["contMotivo"];
        $idNot = $_POST["idNot"];
       
  $rtn = $this->mod->cargar_motivo_rechazo($idNot, $contMotivo);
           return $rtn;
    }
    
}