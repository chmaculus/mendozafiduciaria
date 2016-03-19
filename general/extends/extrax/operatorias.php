<?php

    require_once('connect.php');
   
    if (isset($_GET["accion"]) && $_GET["accion"]=='getOperatorias' ){
        
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";

        $cad_campos = "fi.NOMBRE,o.ID,ot.TIPO,o.NOMBRE,o.DESCRIPCION,TOPE_PESOS,TASA_INTERES_COMPENSATORIA,TASA_INTERES_MORATORIA,TASA_INTERES_POR_PUNITORIOS,TASA_SUBSIDIADA,DESEMBOLSOS,DEVOLUCIONES,PERIODICIDAD";
        $arr_campos = explode(',', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = substr($cad_like,0,-2);
        
        $cnn->select("o.ID,fi.NOMBRE as FIDEI, ot.TIPO,o.NOMBRE,o.DESCRIPCION,TOPE_PESOS,TASA_INTERES_COMPENSATORIA,TASA_INTERES_MORATORIA,TASA_INTERES_POR_PUNITORIOS,TASA_SUBSIDIADA,DESEMBOLSOS,DEVOLUCIONES,PERIODICIDAD");
        $cnn->join("fid_operacion_tipo ot","ot.ID=o.ID_TIPO_OPERATORIA");
        $cnn->join("fid_fideicomiso_operatorias fo","fo.ID_OPERATORIA=o.ID",'LEFT');
        $cnn->join("fid_fideicomiso fi","fi.ID=fo.ID_FIDEICOMISO",'LEFT');
        $cnn->where( $cad_like );
        $rtn = $cnn->get_tabla("fid_operatorias o");
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getOperatoriasChecklist' ){
        $id_operatoria = $_GET["id_operatoria"];
        $cnn->select("c.ID AS ID, c.NOMBRE AS NOMBRE");
        $cnn->join("fid_checklist c","c.ID=oc.ID_CHECKLIST");
        $rtn = $cnn->get_tabla("fid_operatoria_checklist oc","oc.ID_OPERATORIA='".$id_operatoria."'");
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    if (isset($_GET["accion"]) && $_GET["accion"]=='getOperatoriasChecklistVino' ){
//        $id_operatoria = $_GET["id_operatoria"];
        $cnn->select("*");
        $rtn = $cnn->get_tabla("fid_checklist_vino");
//        var_dump($rtn);die();
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getDeudas' ){
        
        $rtn = array(array("ID"=>"1","NOMBRE"=>"Verificación Deuda FTyC"),array("ID"=>"2","NOMBRE"=>"Verificación Deuda Mendoza Fiduciaria"));
        echo trim(json_encode($rtn?$rtn:array()));
        die();
        
    }
    
    

?>