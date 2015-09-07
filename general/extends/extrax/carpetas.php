<?php
    require_once('connect.php');
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getFacturasCuva' ){

        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";
        $idope = isset($_GET["idope"])?$_GET["idope"]:'0';
        $idpro = isset($_GET["idpro"])?$_GET["idpro"]:'0';
        $idestado = isset($_GET["estado"])?$_GET["estado"]:'0';
        $idtipo = isset($_GET["idtipo"])?$_GET["idtipo"]:'0';
        
        $cad_campos = "f.ID, c.CUIT, c.RAZON_SOCIAL";
        $arr_campos = explode(', ', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = substr($cad_like,0,-2);
        
        $cnn->select("IFNULL(CONCAT(u1.NOMBRE,' ',u1.APELLIDO), '-') AS USU_CARGA, IFNULL(CONCAT(u2.NOMBRE,' ',u2.APELLIDO), '-') AS USU_CHEQUEO, f.ID as IID,f.ID as ID,f.TOTAL AS TOTAL, f.IVA AS IVA, f.NETO AS NETO, f.PRECIO AS PRECIO, fe.NOMBRE AS ESTADO, f.OBSERVACIONES as OBSERVACIONES, f.IMP_ERROR_TEXTO as IMP_ERROR_TEXTO, f.KGRS AS KGRS, d.LOCALIDAD AS DEPARTAMENTO, b.NOMBRE AS BODEGA, f.NUMERO AS NUMERO, DATE_FORMAT(f.FECHA, '%d/%m/%Y') as FECHA, c.RAZON_SOCIAL AS CLIENTE,c.CUIT AS CUIT, c.CBU AS CBU, civa.CONDICION AS CONDIVA, ciibb.CONDICION AS CONDIIBB, date(f.CREATEDON) as CREATEDON");
        $cnn->join("fid_clientes c","c.ID=f.ID_CLIENTE","left");
        $cnn->join("fid_cliente_condicion_iva civa","civa.ID=c.ID_CONDICION_IVA","left");
        $cnn->join("fid_cliente_condicion_iibb ciibb","ciibb.ID=c.ID_CONDICION_IIBB","left");
        $cnn->join("fid_bodegas b","b.ID=f.ID_BODEGA","left");
        $cnn->join("fid_localidades d","d.ID=b.ID_DEPARTAMENTO","left");
        $cnn->join("fid_cu_factura_estados fe","fe.ID=f.ID_ESTADO","left");
        $cnn->join("fid_usuarios u1","u1.ID=f.USU_CARGA","left");
        $cnn->join("fid_usuarios u2","u2.ID=f.USU_CHEQUEO","left");
        
        if ($idestado>0){
            $cad_where = "( ".$cad_like . ") and f.ID_ESTADO = '" . $idestado . "'";
        }else{
            //$cad_where .= " and f.ID_ESTADO <> '12'";
            $cad_where = "( ".$cad_like . ") and f.ID_PROVINCIA='". $idpro . "' and f.ID_ESTADO <> '12'";
        }
        $cad_where .= " AND TIPO=" . $idtipo ;
        
        $rtn = $cnn->get_tabla("fid_cu_factura f", $cad_where );
        //file_put_contents('zzzzzz.log',$cnn->last_query() );
        
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getTrazabilidadNota' ){
        
        $idnota = isset($_GET["idnota"])?$_GET["idnota"]:'0';
        $cnn->select("t.DESCRIPCION,u.USERNAME AS USUARIO,t.FECHA AS FECHA");
        $cnn->join("fid_usuarios u","u.ID=t.CARTERADE");
        $cnn->order_by("t.ID","ASC");
        
        $cnn->where( "NOTA='".$idnota."'" );
        $rtn = $cnn->get_tabla("fid_traza t");
        //file_put_contents('aaaaa.log',$cnn->last_query() );
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getNotas' ){
        
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";
        $iduser = $_GET["iduser"]?$_GET["iduser"]:'';
        $idoper = $_GET["idoper"]?$_GET["idoper"]:'';

        //$cad_campos = "nr.ID, nr.ASUNTO, nr.DESCRIPCION, nr.DESTINATARIO, ID_OPERACION, u.NOMBRE, u.APELLIDO, FCREA, IF( nr.ESTADO = '0','Pendiente','Respondido')";
        $cad_campos = "nr.ID, nr.ASUNTO, nr.DESCRIPCION, nr.DESTINATARIO, ID_OPERACION, u.NOMBRE, u.APELLIDO, FCREA";
        $arr_campos = explode(', ', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = substr($cad_like,0,-2);
        
        $cnn->select("nr.ENVIADOA as ENVIADOAID, nr.FOJAS, nr.PROPIETARIO, nr.REMITENTE, nr.ID, nr.ASUNTO, nr.DESCRIPCION, nr.DESTINATARIO,ID_OPERACION, CONCAT(u2.NOMBRE,' ',u2.APELLIDO) AS PROPIETARIO_NOMBRE, CONCAT(u.NOMBRE,' ',u.APELLIDO) AS DESTINATARIO_NOMBRE, DATE_FORMAT(FCREA, '%d/%m/%Y') AS FCREA, CONCAT(u1.NOMBRE,' ',u1.APELLIDO) AS ENVIADOA ");
        $cnn->join("fid_usuarios u","u.ID=nr.DESTINATARIO","left");
        $cnn->join("fid_usuarios u1","u1.ID=nr.ENVIADOA","left");
        $cnn->join("fid_usuarios u2","u2.ID=nr.PROPIETARIO","left");
        if ($iduser){
            $cnn->where( "(".$cad_like.") and nr.PROPIETARIO='".$iduser."' and (nr.TIPO='1')" );//SOLO LAS ENVIADAS AL SOLICITANTE
        }else{
            $cnn->where( "(".$cad_like.") and (nr.TIPO='1')" );//SOLO LAS ENVIADAS AL SOLICITANTE
        }  
        
        if ($idoper){
            $cnn->where( "(".$cad_like.") and nr.ID_OPERACION='".$idoper."' and (nr.TIPO='1')" );//SOLO LAS ENVIADAS AL SOLICITANTE
        }else{
            $cnn->where( "(".$cad_like.") and (nr.TIPO='1')" );//SOLO LAS ENVIADAS AL SOLICITANTE
        }  
        
        $rtn = $cnn->get_tabla("fid_nota_req nr");
        
        //file_put_contents('aaaaa.log',$cnn->last_query() );
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    
    
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getGarantias' ){
        $idope = isset($_GET["idope"])?$_GET["idope"]:'0';
        
        $cnn->select("g.*,gt.TIPO AS TIPOG, ge.ESTADO AS ESTADOG, g.ID_ESTADO as ESTADOGARNUM");
        $cnn->where( "ID_OPERACION='".$idope."'" );
        $cnn->join("fid_garantia_tipo gt","gt.ID=g.ID_TIPO");
        $cnn->join("fid_garantia_estado ge","ge.ID=g.ID_ESTADO");
        $rtn = $cnn->get_tabla("fid_garantias g");
        //file_put_contents('aaaaa.log',$cnn->last_query() );
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getSolDesembolsos' ){
        $idope = isset($_GET["idope"])?$_GET["idope"]:'0';
        //IF( nr.ESTADO =  '2',  'Pendiente',  'Respondido' ) AS ESTADONR
                
        $cnn->select("*,IF( ESTADO =  '1',  'Emitido',  'Pendiente' ) AS ESTADONR");
        $cnn->where( "ID_OPERACION='".$idope."'" );
        $rtn = $cnn->get_tabla("fid_sol_desembolso");
        //file_put_contents('aaaaa.log',$cnn->last_query() );
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getTrazabilidad' ){
        
        $idope = isset($_GET["idope"])?$_GET["idope"]:'0';
        $cnn->select("t.DESCRIPCION,e.NOMBRE AS ETAPA,u.USERNAME AS USUARIO,t.FECHA AS FECHA, CONCAT(u1.NOMBRE,' ',u1.APELLIDO) AS AUTOR_NOMBRE, CONCAT(u2.NOMBRE,' ',u2.APELLIDO) AS AUTOR1_NOMBRE");
        $cnn->join("fid_etapas e","e.ID=t.ETAPA","LEFT");
        $cnn->join("fid_usuarios u","u.ID=t.CARTERADE");
        $cnn->join("fid_usuarios u1","u1.ID=t.AUTOR","LEFT");
        $cnn->join("fid_usuarios u2","u2.ID=t.AUTOR1","LEFT");
        //$cnn->group_by("CARTERADE, ETAPA_REAL");
        $cnn->order_by("t.ID","ASC");
        
        //$cnn->where( "ID_OPERACION='".$idope."' AND ETAPA>0 AND OBSERVACION<>'AUTORIZADO'" );
        $cnn->where( "ID_OPERACION='".$idope."' AND OBSERVACION<>'AUTORIZACION DE REQUERIMIENTO'" );
        $rtn = $cnn->get_tabla("fid_traza t");
        //file_put_contents('aaaaa.log',$cnn->last_query() );
        echo trim(json_encode($rtn?$rtn:array()));
        die();
        
    }
    
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getRequerimientos' ){
       
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";
        $cad_campos = "nr.ID, nr.ASUNTO, o.BENEF, nr.DESCRIPCION, nr.DESTINATARIO, ID_OPERACION, u.NOMBRE, u.APELLIDO, FCREA, IF( nr.ESTADO = '0','Pendiente','Respondido')";
        $arr_campos = explode(', ', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = substr($cad_like,0,-2);
        
        $cnn->select("nr.ESTADO, o.BENEF AS BENEF, nr.REMITENTE, nr.ID, nr.ASUNTO, nr.DESCRIPCION, nr.DESTINATARIO, ID_OPERACION, CONCAT(u.NOMBRE,' ',u.APELLIDO) AS REMITENTE_NOMBRE, DATE_FORMAT(FCREA, '%d/%m/%Y') AS FCREA,  IF( nr.ESTADO =  '1',  'Pendiente de respuesta',  'Respondido' ) AS ESTADONR ");
        $cnn->join("fid_usuarios u","u.ID=nr.REMITENTE",'left');
        $cnn->join("fid_operaciones o","o.ID=nr.ID_OPERACION",'left');
        $cnn->where( "(".$cad_like.") and (nr.TIPO='0') and (nr.ESTADO in ('1','3','4') )" ); // SOLO LAS ENVIADAS AL SOLICITANTE
        $rtn = $cnn->get_tabla("fid_nota_req nr");
        
        //file_put_contents('aaaaa.log',$cnn->last_query() );
        
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getCondicionesPrevias' ){
        $id_operacion = $_GET["id_operacion"];
        $tipo = isset($_GET["tipo"])?$_GET["tipo"]:'1';
        //$cnn->select("c.ID AS ID, c.NOMBRE AS NOMBRE");
        //$cnn->join("fid_checklist c","c.ID=oc.ID_CHECKLIST");
        $rtn = $cnn->get_tabla("fid_operacion_condicionesprevias","ID_OPERACION='".$id_operacion."' AND TIPO='".$tipo."'");
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }
    
    if (isset($_GET["accion"]) && $_GET["accion"]=='getCarpetas' ){
        
        $iduser = $_GET["iduser"]?$_GET["iduser"]:'';
        $tipo = $_GET["tipo"]?$_GET["tipo"]:'';
        
        $word = isset($_GET["name_startsWith"])?$_GET["name_startsWith"]:"";
            
        $cad_campos = "o.ID,e.nombre,op.NOMBRE,o.MONTO_SOLICITADO,u.USERNAME,o.BENEF,fid.NOMBRE,e.nombre,u.NOMBRE,u1.NOMBRE,u.APELLIDO,u1.APELLIDO,u2.NOMBRE,u2.APELLIDO,ia.NOMBRE";
        
        $arr_campos = explode(',', $cad_campos);
        $cad_like = "";
        foreach($arr_campos as $tp){
            $cad_like .= " " . $tp ." LIKE '%".$word."%' OR";
        }
        $cad_like = "(".substr($cad_like,0,-2) . ")";
        
        if ($tipo=='pendiente'){
            if ($iduser){
                $cad_like = "o.ENVIADOA='".$iduser."' AND " . $cad_like;
            }
        }else{
            if ($iduser){
                $cad_like = "o.CARTERADE='".$iduser."' AND " . $cad_like;
            }
        }
        
        //IFNULL(e2.ID_ETAPA, 0) as e2_operacion, IFNULL(e2.ESTADO,0) as e2_estado, et2.NOMBRE AS ET2,
        $cnn->select("o.ID AS IDOPE,op.NOMBRE AS OPERATORIA,o.MONTO_SOLICITADO,ia.VALOR AS MONTO_APROBADO,
                        IFNULL(CONCAT(u2.NOMBRE,' ',u2.APELLIDO), '-') AS AUTOR,
                        IFNULL(CONCAT(u3.NOMBRE,' ',u3.APELLIDO), '-') AS AUTOR1,
                        IFNULL(CONCAT(u.NOMBRE,' ',u.APELLIDO), '-') AS ENCARTERADE,
                        IFNULL(CONCAT(u1.NOMBRE,' ',u1.APELLIDO), '-') AS ENVIADOA,
                        IFNULL(u1.USERNAME, '-') as ENVIADOA1,
                        IFNULL(fid.NOMBRE, '-') as FIDEICOMISO,
                        IFNULL(e1.ID_ETAPA, 0) as e1_operacion, IFNULL(e1.ESTADO,0) as e1_estado, et1.NOMBRE AS ET1,
                        IFNULL(e2.ID_ETAPA, 0) as e2_operacion, IFNULL(e2.ESTADO,0) as e2_estado, et2.NOMBRE AS ET2,
                        IFNULL(e4.ID_ETAPA, 0) as e4_operacion, IFNULL(e4.ESTADO,0) as e4_estado, et4.NOMBRE AS ET4,
                        IFNULL(e5.ID_ETAPA, 0) as e5_operacion, IFNULL(e5.ESTADO,0) as e5_estado, et5.NOMBRE AS ET5,
                        IFNULL(e6.ID_ETAPA, 0) as e6_operacion, IFNULL(e6.ESTADO,0) as e6_estado, et6.NOMBRE AS ET6,
                        IFNULL(e7.ID_ETAPA, 0) as e7_operacion, IFNULL(e7.ESTADO,0) as e7_estado, et7.NOMBRE AS ET7,
                        IFNULL(e8.ID_ETAPA, 0) as e8_operacion, IFNULL(e8.ESTADO,0) as e8_estado, et8.NOMBRE AS ET8,
                        IFNULL(e9.ID_ETAPA, 0) as e9_operacion, IFNULL(e9.ESTADO,0) as e9_estado, et9.NOMBRE AS ET9,
                       IFNULL(e10.ID_ETAPA, 0) as e10_operacion, IFNULL(e10.ESTADO,0) as e10_estado, et10.NOMBRE AS ET10,
                        o.BENEF as BENEFICIARIO, o.CARGAH as CARGAH,
                        IF(o.ID_ESTADO='20','Desistida',e.nombre) as ETAPA_ACTUAL
                        
                        "
                    );
        
        $cnn->join("fid_etapas e","e.ID=o.ID_ETAPA_ACTUAL","left");
        $cnn->join("fid_operatorias op","op.ID=o.ID_OPERATORIA","left");
        $cnn->join("fid_fideicomiso fid","fid.ID=o.ID_FIDEICOMISO","left");
        $cnn->join("fid_usuarios u","u.ID=o.CARTERADE","left");
        $cnn->join("fid_usuarios u1","u1.ID=o.ENVIADOA","left");
        
        
                
        
        $cnn->join("fid_operacion_etapas e1","o.ID = e1.ID_OPERACION AND e1.ID_ETAPA = 1","left");
        $cnn->join("fid_etapas et1","et1.ID = 1","left");
        $cnn->join("fid_operacion_etapas e2","o.ID = e2.ID_OPERACION AND e2.ID_ETAPA = 2","left");
        $cnn->join("fid_etapas et2","et2.ID = 2","left");
        /*
        $cnn->join("fid_operacion_etapas e3","o.ID = e3.ID_OPERACION AND e3.ID_ETAPA = 3","left");
        $cnn->join("fid_etapas et3","et3.ID = 3","left");
        */
        $cnn->join("fid_operacion_etapas e4","o.ID = e4.ID_OPERACION AND e4.ID_ETAPA = 4","left");
        $cnn->join("fid_etapas et4","et4.ID = 4","left");
        $cnn->join("fid_operacion_etapas e5","o.ID = e5.ID_OPERACION AND e5.ID_ETAPA = 5","left");
        $cnn->join("fid_etapas et5","et5.ID = 5","left");
        $cnn->join("fid_operacion_etapas e6","o.ID = e6.ID_OPERACION AND e6.ID_ETAPA = 6","left");
        $cnn->join("fid_etapas et6","et6.ID = 6","left");
        $cnn->join("fid_operacion_etapas e7","o.ID = e7.ID_OPERACION AND e7.ID_ETAPA = 7","left");
        $cnn->join("fid_etapas et7","et7.ID = 7","left");
        $cnn->join("fid_operacion_etapas e8","o.ID = e8.ID_OPERACION AND e8.ID_ETAPA = 8","left");
        $cnn->join("fid_etapas et8","et8.ID = 8","left");
        $cnn->join("fid_operacion_etapas e9","o.ID = e9.ID_OPERACION AND e9.ID_ETAPA = 9","left");
        $cnn->join("fid_etapas et9","et9.ID = 9","left");
        
        $cnn->join("fid_operacion_etapas e10","o.ID = e10.ID_OPERACION AND e10.ID_ETAPA = 10","left");
        $cnn->join("fid_etapas et10","et10.ID = 10","left");
        
        $cnn->join("fid_traza tr1","tr1.ID_OPERACION = o.ID and tr1.ACTIVO=1 AND AUTOR_REQ=0","left");
        $cnn->join("fid_usuarios u2","u2.ID=tr1.AUTOR","left");
        
        $cnn->join("fid_usuarios u3","u3.ID=tr1.AUTOR1","left");
        
        
        $cnn->join("fid_operacion_infoadicional ia","ia.ID_OPERACION=o.ID and ia.NOMBRE='comite_macta'","left");
        
        //$cnn->join("fid_operacion_cliente oc1","oc1.ID_OPERACION=o.ID ","left");
        
        
        
        $rtn = $cnn->order_by("IDOPE","DESC");
                
        
        $cnn->where( $cad_like );
        $rtn = $cnn->get_tabla("fid_operaciones o");
        
        //file_put_contents('qqqq.log', $cnn->last_query() );
        
        echo trim(json_encode($rtn?$rtn:array()));
        die();
    }

?>