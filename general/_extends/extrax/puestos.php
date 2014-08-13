<?php

    require_once('connect.php');
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getUsuarios' ){
        
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";

        $cad_campos = "p.ID,p.DENOMINACION,a.DENOMINACION";
        $arr_campos = explode(',', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = substr($cad_like,0,-2);
        $cnn->select("p.ID,p.DENOMINACION,a.DENOMINACION as AREA");
        $cnn->join("fid_xareas a","a.ID=p.ID_AREA");
        $cnn->order_by("AREA","ASC");
        
        $cnn->where( $cad_like );
        $rtn = $cnn->get_tabla("fid_xpuestos p");
        
        echo trim(json_encode($rtn?$rtn:array()));
        die();
        
    }
    

?>