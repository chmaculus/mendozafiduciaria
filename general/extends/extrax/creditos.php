<?php
    date_default_timezone_set("America/Argentina/Mendoza");
    require_once('connect.php');
     
    if (isset($_GET["accion"]) && $_GET["accion"]=='getCreditos' ){
        
        $idope = isset($_GET["idope"])?$_GET["idope"]:'0';

        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";
        
        
        //$cnn->select("t.DESCRIPCION,e.NOMBRE AS ETAPA,u.USERNAME AS USUARIO,MIN(t.FECHA) AS FECHA1,MAX(t.FECHA) AS FECHA2");
        
        list($d,$m,$y) = explode("-",date("d-m-Y"));
        $time = mktime(0,0,0,$m,$d,$y);
        
        $cnn->where("t.CREDITO_ESTADO <> ".ESTADO_CREDITO_ELIMINADO);
        $cnn->select("t.ID as ID_CREDITO, ifnull(f.NOMBRE,' - ') as FIDEICOMISO, ifnull(o.NOMBRE,' - ') as OPERATORIA, POSTULANTES, ID_OPERACION, ifnull(e.CREDITO_ESTADO,0) as CR_ESTADO, t.CREDITO_ESTADO, ifnull(SUM(td.MONTO),0) AS DESEMBOLSOS, ifnull(SUM(tp.MONTO),0) AS PAGOS ");
        $cnn->order_by("t.ID","DESC");
        $cnn->join("fid_fideicomiso f","f.ID = t.ID_FIDEICOMISO", "left");
        $cnn->join("fid_operatorias o","o.ID = t.ID_OPERATORIA", "left");
        $cnn->join("fid_creditos_desembolsos td","td.ID_CREDITO=t.ID", "left");
        $cnn->join("fid_creditos_pagos tp","tp.ID_CREDITO=t.ID", "left");
        $cnn->join("fid_creditos_extra e","e.CREDITO_ID = t.ID AND CREDITO_ESTADO_FECHA = ".$time , "left");
        $cnn->group_by("t.ID");
        
        $creditos = $cnn->get_tabla("fid_creditos t");
        //echo $cnn->last_query();
        //print_array($creditos);
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
            
            $cuit = isset($clientes[0]['CUIT']) ? preg_replace("/[^0-9]/", "", $clientes[0]['CUIT']) : '';
            
            $tomadores = implode(", ",$cl);
            if (startsWith($credito['ID_CREDITO'], $word) || 
                strpos($tomadores , $word) !==false   || strlen($word)==0
            ){
                $estado = 'AL DIA';
                if ($credito['CR_ESTADO']==-1) $estado = 'DEUDA';
                if ($credito['CR_ESTADO']==0) $estado = 'SIN VERIFICAR';
                if ($credito['CREDITO_ESTADO'] == ESTADO_CREDITO_CADUCADO) $estado = 'CADUCADO';
                if ($credito['CREDITO_ESTADO'] == ESTADO_CREDITO_PRORROGADO) $estado = 'PRORROGADO';
                if ($credito['CREDITO_ESTADO'] == ESTADO_CREDITO_DESISTIDO) $estado = 'DESISTIDO';
                
                $rtn[]=array(
                    "ID_CREDITO" =>$credito['ID_CREDITO'],
                    "TOMADORES"=>$tomadores ,
                    "OPERATORIA"=>$credito['OPERATORIA'],
                    "FIDEICOMISO"=>$credito['FIDEICOMISO'],
                    "ESTADO"=>$estado,
                    "CARPETA"=>$credito['ID_OPERACION'] ? $credito['ID_OPERACION'] : " - ",
                    "CUIT"=>$cuit,
                    "DESEMBOLSOS"=>round($credito['DESEMBOLSOS'], 2),
                    "PAGOS"=>round($credito['PAGOS'], 2)
                    
                );
            }
            
            

        }
        //file_put_contents("log.log", $cnn->last_query());
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
?>