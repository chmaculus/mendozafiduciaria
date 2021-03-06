<?php
class notificaciones_model extends main_model{
    function set_modulo($modulo){
        $tablas = array(
            "clientes" => "fid_clientes",
            "permisos" => "fid_permisos"
        );
        $this->_tablamod = $tablas[$modulo];
    }
   
    function get_destino($idope){
        $this->_db->select("DESTINO");
        $destino = $this->_db->get_tabla("fid_traza", "ACTIVO='1' AND OBSERVACION='ENVIADO' AND ACTIVO='1' AND ID_OPERACION='".$idope."'");
        if ($destino){
            return $destino[0]["DESTINO"];
        }else{
            return 0;
        }
            
    }
    
    
    function get_obj($busqueda){
        $this->_db->where("Modulo like  '%".$busqueda."%'");
        $rtn = $this->_db->get_tabla($this->_tablamod);
        return $rtn;
    }
   
    function get_permisos($filtro){
       $this->_db->where("PERMISO like '%".$filtro."%' OR MODULO like '%".$filtro."%'");
       $items = $this->_db->get_tabla("fid_permisos");
       return $items;
    }
    
    function send_traza($obj){
        $obj_del= array(
            "ACTIVO"=>"0"
        );
        //actualizar carterade , de operaciones
        if($obj["CARTERADE"]){
            $this->_db->update("fid_operaciones",array("CARTERADE"=>$obj["CARTERADE"],"ENVIADOA"=>0) ,"ID='".$obj['ID_OPERACION']."'");
        }

        //poner el ultimo movimiento como activo
        $this->_db->update("fid_traza",$obj_del,"ID_OPERACION='".$obj['ID_OPERACION']."'");
        $iid = $this->_db->insert("fid_traza",$obj);
        return $iid;
    }
   
    function get_carpetas_pendientes($id){
        $this->_db->select('ot.NOMBRE, o.ID, t.CARTERADE,u.NOMBRE as UNOM,u.APELLIDO AS UAPE,t.ESTADO AS TESTADO, t.ID AS TID, t.NOTIF AS TNOTIF, t.AUTOR AS TAUTOR, t.AUTOR_REQ AS TAUTOR_REQ, t.ETAPA AS TETAPA, t.NOTA AS TNOTA, t.OBSERVACION AS TOBSERVACION, t.DESCRIPCION AS TDESCRIPCION, t.ETAPA_ORIGEN AS TETAPA_ORIGEN');
        $this->_db->join("fid_operaciones o","o.id=t.ID_OPERACION",'left');
        $this->_db->join("fid_operatorias ot","ot.id=o.ID_OPERATORIA",'left');
        $this->_db->join("fid_usuarios u","t.CARTERADE=u.ID");
        //$this->_db->join("fid_usuarios u1","t.CARTERADE=u1.ID");
        //$this->_db->join("fid_nota_req nr","t.NOTA=nr.ID","LEFT");
        $items = $this->_db->get_tabla("fid_traza t","t.DESTINO='".$id."' AND AUTOR=''  AND AUTOR_REQ='' AND (  (ACTIVO=1 AND LEIDO='1') OR (NOTIF=1 AND LEIDO='1') ) or (t.AUTOR='" . $id . "' and t.LEIDO='1' ) or (AUTOR_REQ='" . $id . "' AND LEIDO='1')");
        //log_this('zzzzz222.log',$this->_db->last_query() );
        
        $ret = array();
        $c=0;
        if($items){
            foreach($items as $i){
                
                $ret[$c]['ID'] = $i["ID"];
                $ret[$c]['OPERATORIA'] = $i["NOMBRE"];
                $ret[$c]['CLIENTE'] = '';
                $ret[$c]['ENVIA'] = $i["UNOM"] . " " . $i["UAPE"];
                $ret[$c]['TESTADO'] =  $i["TESTADO"];
                $ret[$c]['TID'] =  $i["TID"];
                $ret[$c]['TNOTIF'] =  $i["TNOTIF"];
                $ret[$c]['TAUTOR'] =  $i["TAUTOR"];
                $ret[$c]['TAUTOR_REQ'] =  $i["TAUTOR_REQ"];
                $ret[$c]['TETAPA'] =  $i["TETAPA"];
                $ret[$c]['TNOTA'] =  $i["TNOTA"];
                $ret[$c]['TOBSERVACION'] =  $i["TOBSERVACION"];
                $ret[$c]['TETAPA_ORIGEN'] =  $i["TETAPA_ORIGEN"];
                
                $this->_db->select("NOMBRE");
                $name_etapa = $this->_db->get_tabla("fid_etapas","ID='".$i["TETAPA_ORIGEN"]."'");
                if ($name_etapa){
                    $cad_etapa = $name_etapa[0]["NOMBRE"];                    
                }                
                
                $ret[$c]['TDESCRIPCION'] = "";
                if ($i["TDESCRIPCION"]=='AREA DE ANALISIS ENVIO LA CARPETA AL COORDINADOR'){
                    $arr_tmp = explode("AREA DE ANALISIS",$i["TDESCRIPCION"]);
                    $dato1 = substr($i["TDESCRIPCION"],0,16);
                    $dato2 = trim($arr_tmp[1]);
                    $ret[$c]['TDESCRIPCION'] =  $dato1 . " (".$cad_etapa.") " . $dato2;
                }elseif($i["TDESCRIPCION"]=='PETICION DE CONFIRMACION DE COPIA DE CONTRATO EN LEGALES'){
                    $ret[$c]['TDESCRIPCION'] = $i["TDESCRIPCION"];
                }
                
                $this->_db->select('c.RAZON_SOCIAL');
                $this->_db->limit('0','1');
                $this->_db->join("fid_clientes c","c.id=oc.ID_CLIENTE");
                $cliente = $this->_db->get_tabla("fid_operacion_cliente oc","ID_OPERACION='".$i["ID"]."'");
                if ($cliente){
                    $ret[$c]['CLIENTE'] = $cliente[0]["RAZON_SOCIAL"];
                }
                $c++;
            }
        }
        return $ret;
    }
    
    
    function get_carpetas_pendientes_cont($id){
        $this->_db->select('count(*) as CONT');
        $this->_db->join("fid_operaciones o","o.id=t.ID_OPERACION","left");
        $this->_db->join("fid_operatorias ot","ot.id=o.ID_OPERATORIA","left");
        //$this->_db->join("fid_nota_req nr","t.NOTA=nr.ID","LEFT");
        $this->_db->join("fid_usuarios u","t.CARTERADE=u.ID");
        $items = $this->_db->get_tabla("fid_traza t","t.DESTINO='".$id."' AND AUTOR='' AND AUTOR_REQ='' AND (  (ACTIVO=1 AND LEIDO='1') OR (NOTIF=1 AND LEIDO='1') ) or (t.AUTOR='" . $id . "' and t.LEIDO='1' ) or (AUTOR_REQ='" . $id . "' AND LEIDO='1')");
        
        //log_this('zzzzz111.log',$this->_db->last_query() );
        
        $ret = 0;
        if ($items){
            $ret = $items[0]["CONT"];
        }
        return $ret;
    }
    
    
    function cambiar_leido_traza($idt, $obj_opt=array() ){
        if (count($obj_opt)>0){
            $upd = $this->_db->update("fid_traza", $obj_opt, "ID='".$idt."'");
        }else{
            $upd = $this->_db->update("fid_traza", array("LEIDO"=>"0"), "ID='".$idt."'");
        }
        //log_this( 'zzzzz.log', $this->_db->last_query() );
        return $upd;
    }
    
    function traza_autor( $idt, $resp = 2, $para_aux1=0, $par_su2 ){
        
        $fecha_actual = date("Y-m-d H:i:s");
        $this->cambiar_leido_traza($idt);
        //insertar registro igual
        $reg = $this->_db->get_tabla("fid_traza","ID='".$idt."'");
        //log_this( 'zzzzz.log', print_r($reg,1) );
        if ($reg){
            $reg = $reg[0];
            $this->_db->update("fid_traza",array("ACTIVO"=>"0"),"ID_OPERACION='".$reg["ID_OPERACION"]."'");
            //preguntar si hay autor1
            //log_this( 'zzzzz.log', $this->_db->last_query() );
            
            if($reg["AUTOR_REQ"]>0){
                //$resp = 1 o 2
                if ($resp == 1){
                    //SE USA ETAPA_ORIGEN PARA INDICAR SI SE APRUEBA O NO LA AUTORIZACION DE REQ
                    //acepta
                    $this->_db->update("fid_traza",array("LEIDO"=>"1","ETAPA_ORIGEN"=>"1","AUTOR_REQ"=>"0"),"ID='".$idt."'");
                    //log_this('zzzzz.log',$this->_db->last_query() );
                    //actualizar a estado = 2 el req numero: etapa
                    $this->_db->update("fid_nota_req",array("ESTADO"=>"2"), "ID='" . $reg["ETAPA"] . "'");
                    //log_this('zzzzz.log',$this->_db->last_query() );
                }elseif ($resp == 0){
                    //SE USA ETAPA_ORIGEN PARA INDICAR SI SE APRUEBA O NO LA AUTORIZACION DE REQ
                    //acepta
                    $this->_db->update("fid_traza",array("LEIDO"=>"1","ETAPA_ORIGEN"=>"0","AUTOR_REQ"=>"0"),"ID='".$idt."'");
                    //log_this('zzzzz.log',$this->_db->last_query() );
                    //actualizar a estado = 2 el req numero: etapa
                    $this->_db->update("fid_nota_req",array("ESTADO"=>"6"), "ID='" . $reg["ETAPA"] . "'");
                    //log_this('zzzzz.log',$this->_db->last_query() );
                }
                
                
                $ins= array();
            }else if($reg["AUTOR2"]>0){
                //insertar una traza igual, pero sin autor1
                $arr_ins= array(
                    "ID_OPERACION"=>$reg["ID_OPERACION"],
                    "ESTADO"=>$reg["ESTADO"],
                    "CARTERADE"=>$reg["CARTERADE"],
                    "DESTINO"=>$reg["DESTINO"],
                    "OBSERVACION"=>$reg["OBSERVACION"],
                    "DESCRIPCION"=>$reg["DESCRIPCION"],
                    "ETAPA"=>$reg["ETAPA"],
                    "ETAPA_ORIGEN"=>$reg["ETAPA_ORIGEN"],
                    "ETAPA_REAL"=>$reg["ETAPA_ORIGEN"],
                    "FECHA"=>$fecha_actual,
                    "ACTIVO"=>$reg["ACTIVO"],
                    "LEIDO"=>'1',
                    "NOTIF"=>$reg["NOTIF"],
                    "AUTOR"=>$reg["AUTOR1"],
                    "AUTOR1"=>$reg["AUTOR2"],
                    "AUTOR2"=>""
                );
                $ins = $this->_db->insert("fid_traza", $arr_ins );
            }else if($reg["AUTOR1"]>0){
                //insertar una traza igual, pero sin autor1
                $arr_ins= array(
                    "ID_OPERACION"=>$reg["ID_OPERACION"],
                    "ESTADO"=>$reg["ESTADO"],
                    "CARTERADE"=>$reg["CARTERADE"],
                    "DESTINO"=>$reg["DESTINO"],
                    "OBSERVACION"=>$reg["OBSERVACION"],
                    "DESCRIPCION"=>$reg["DESCRIPCION"],
                    "ETAPA"=>$reg["ETAPA"],
                    "ETAPA_ORIGEN"=>$reg["ETAPA_ORIGEN"],
                    "ETAPA_REAL"=>$reg["ETAPA_ORIGEN"],
                    "FECHA"=>$fecha_actual,
                    "ACTIVO"=>$reg["ACTIVO"],
                    "LEIDO"=>'1',
                    "NOTIF"=>$reg["NOTIF"],
                    "AUTOR"=>$reg["AUTOR1"],
                    "AUTOR1"=>""
                );
                $ins = $this->_db->insert("fid_traza", $arr_ins );
            }else{
                $arr_ins= array(
                    "ID_OPERACION"=>$reg["ID_OPERACION"],
                    "ESTADO"=>$reg["ESTADO"],
                    "CARTERADE"=>$reg["CARTERADE"],
                    "DESTINO"=>$reg["DESTINO"],
                    "OBSERVACION"=>"AUTORIZADO",
                    "DESCRIPCION"=>"SE AUTORIZO EL PASE DE LA CARPETA",
                    "ETAPA"=>$reg["ETAPA"],
                    "ETAPA_ORIGEN"=>$reg["ETAPA_ORIGEN"],
                    "ETAPA_REAL"=>$reg["ETAPA_ORIGEN"],
                    "FECHA"=>$fecha_actual,
                    "ACTIVO"=>"1",
                    "LEIDO"=>"1",
                    "NOTIF"=>"0",
                    "AUTOR"=>""
                );
                if ($par_su2==1){
                    $arr_ins["DESCRIPCION"] = "SE AUTORIZO EL PASE DE LA CARPETA (CON SU2)";
                }
                
                if ($para_aux1==1){
                    $arr_ins["AUX1"] = 1;
                }
                                
                $ins = $this->_db->insert("fid_traza", $arr_ins );
            }
            
        }
        return $ins;
    }
    
    
}

?>
