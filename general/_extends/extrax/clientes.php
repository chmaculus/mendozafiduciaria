<?php




    require_once('connect.php');
    
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='todosClientes' ){
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";

        $cad_campos = "c.id,razon_social,DIRECCION,PROVINCIA,LOCALIDAD,TELEFONO,CONTACTO,CUIT,CBU,CORREO,ID_INV,INSCRIPCION_IIBB";
        $arr_campos = explode(',', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = substr($cad_like,0,-2);
        $cnn->select("c.id,razon_social,DIRECCION,PROVINCIA,LOCALIDAD,TELEFONO,CONTACTO,CUIT,CBU,CORREO,ID_INV,INSCRIPCION_IIBB");
        $cnn->join("fid_provincias p","p.id=c.id_provincia",'left');
        $cnn->join("fid_localidades l","l.id=c.id_departamento",'left');
        $cnn->where( $cad_like );
        $rtn = $cnn->get_tabla("fid_clientes c");
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getEntidades' ){
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";
 
        $cnn->select("e.id as id,ets.nombre as entidad_tipo,ets.id as idt, e.nombre as entidad, e.cuit as cuit, e.* ");
        $cnn->join("fid_entidades e","et.id_entidad=e.id",'inner');
        $cnn->join("fid_entidades_tipos ets","et.id_tipo=ets.id",'inner');
        $cnn->order_by("entidad");
        //$cnn->where( $cad_like );
        $rtn = $cnn->get_tabla("fid_entidadestipo et");
        //file_put_contents('aaaaa.log',$cnn->last_query() );
        
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    

?>
