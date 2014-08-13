<?php

    require_once('connect.php');

	 
    if (isset($_GET["accion"]) && $_GET["accion"]=='getCreditos' ){
        
        $idope = isset($_GET["idope"])?$_GET["idope"]:'0';

        
        //$cnn->select("t.DESCRIPCION,e.NOMBRE AS ETAPA,u.USERNAME AS USUARIO,MIN(t.FECHA) AS FECHA1,MAX(t.FECHA) AS FECHA2");
        
        
        $cnn->where("CREDITO_ESTADO = 0");
        $cnn->select("t.ID as ID_CREDITO, ifnull(f.NOMBRE,' - ') as FIDEICOMISO, ifnull(o.NOMBRE,' - ') as OPERATORIA, POSTULANTES, ID_OPERACION ");
        $cnn->order_by("t.ID","ASC");
        $cnn->join("fid_fideicomiso f","f.ID = t.ID_FIDEICOMISO", "left");
        $cnn->join("fid_operatorias o","o.ID = t.ID_OPERATORIA", "left");
        
        $creditos = $cnn->get_tabla("fid_creditos t");
        
        $rtn = array();


        foreach($creditos as $credito){
            $clientes = array();
            if ($credito['POSTULANTES']){
                $clientes = $cnn->get_tabla("fid_clientes","ID in (".str_replace("|", ",", $credito['POSTULANTES']).")");            
            }
            
            $cl = array();
            foreach($clientes as $cliente){
                $cl[] = $cliente['RAZON_SOCIAL'];
            }
            
            
            $rtn[]=array(
                "ID_CREDITO" =>$credito['ID_CREDITO'],
                "TOMADORES"=>implode(", ",$cl),
                "OPERATORIA"=>$credito['OPERATORIA'],
                "FIDEICOMISO"=>$credito['FIDEICOMISO'],
                "CARPETA"=>$credito['ID_OPERACION'] ? $credito['ID_OPERACION'] : " - ",
                "CARPETA"=>" - "
            );
        }
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
?>