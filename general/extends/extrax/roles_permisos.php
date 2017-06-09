<?php

    require_once('connect.php');
    
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getUsuarios' ){
        
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";

        $cad_campos = "u.ID,r.DENOMINACION,USERNAME,NOMBRE,APELLIDO,EMAIL,a.DENOMINACION,p.DENOMINACION";
        $arr_campos = explode(',', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = substr($cad_like,0,-2);
        
        $cnn->select("u.ID,r.DENOMINACION,USERNAME,NOMBRE,APELLIDO,EMAIL,a.DENOMINACION as AREA,p.DENOMINACION as PUESTO,u.ESTADO");
        
        $cnn->join("fid_roles r","r.ID=u.ID_ROL");
        $cnn->join("fid_xareas a","a.ID=u.ID_AREA","left");
        $cnn->join("fid_xpuestos p","p.ID=u.ID_PUESTO","left");
        
        $cnn->where( $cad_like );
        $rtn = $cnn->get_tabla("fid_usuarios u");
        
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    
   
    if (isset($_GET["accion"]) && $_GET["accion"]=='getRolesPermisos' ){
        
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";

        $cad_campos = "rp.ID,r.DENOMINACION,p.MODULO,p.PERMISO";
        $arr_campos = explode(',', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = substr($cad_like,0,-2);
        $cnn->select("rp.ID,r.DENOMINACION,p.MODULO,p.PERMISO,MOSTRAR,ALTA,BAJA,MODIFICACION,EXPORTAR,OTROS");
        
        $cnn->join("fid_roles r","r.ID=rp.ID_ROL");
        $cnn->join("fid_permisos p","p.ID=rp.ID_PERMISO");
        
        $cnn->where( $cad_like );
        $cnn->order_by( "r.DENOMINACION", "ASC" );
        
        $rtn = $cnn->get_tabla("fid_roles_permisos rp");
        
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getRolesPermisos_new' ){
        
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";

        $cad_campos = "rp.ID_ROL,r.DENOMINACION,p.MODULO,p.PERMISO";
        $arr_campos = explode(',', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = substr($cad_like,0,-2);
        $cnn->select("rp.ID_ROL,r.DENOMINACION");
        
        $cnn->join("fid_roles r","r.ID=rp.ID_ROL");
        $cnn->join("fid_permisos p","p.ID=rp.ID_PERMISO");
        
        $cnn->where( $cad_like );
        $cnn->group_by("r.DENOMINACION");
        
        $rtn = $cnn->get_tabla("fid_roles_permisos rp");
        //file_put_contents('aaaaa.log',$cnn->last_query() );
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    
    

?>