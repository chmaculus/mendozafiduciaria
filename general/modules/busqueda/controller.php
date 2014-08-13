<?php
define("PRODUCTO", 1);
define("LUGAR", 9);
define("PRODUCTO_COMPRA", 5);
define("PRODUCTO_LUGAR", 6);
define("PRODUCTO_FABRICADO", 8);
define("PRODUCCION", 12);
define("COMPONENTE", 2);
define("CLIENTE", 3);
define("PROVEEDOR", 4);
define("CUENTAS_CONTABLES", 11);
define("TABLAS_PERMISOS", 20);
define("REMITO", 30);
define("CUENTAS_REF", 40);
define("CHEQUES_NO_CANCELADOS", 50);




$js_var['_callback'] = trim($params['callback']);
$js_var['_text'] = trim($params['text']);

$elementos = array();
switch ($params['tipo']){
    case CHEQUES_NO_CANCELADOS:
        $mysql->select("b.Denominacion as col1, Numero as col2, pc.*, b.Denominacion as Banco, p.Denominacion as Provincia");
        $mysql->join("bancos b","b.ID = pc.BancoID");
        $mysql->join("provincias p","p.ID = pc.ProvinciaID");
        $elementos =  $mysql->get_tabla("pago_cheque pc");        
        
        break;
    case TABLAS_PERMISOS:
        $mysql->select("t.Label as col2,'' as col1, t.*");
        $elementos = $mysql->get_tabla("r_tablas t", "t.Label like '%".$js_var['_text']."%'");
        break;
    case CUENTAS_REF:
        $mysql->select("Denominacion, Denominacion as col2,'' as col1, ID");
        $elementos = $mysql->get_tabla("_c_cuentas", "Denominacion like '%".$js_var['_text']."%'");
        break;
    case LUGAR:
        $mysql->select("l.Denominacion as col1, t.Denominacion as col2, l.*");
        $mysql->join("tipo_lugar t", "t.ID = l.TipoLugarID");
        $elementos = $mysql->get_tabla("lugares l", "l.Denominacion like '%".$js_var['_text']."%'");
        break;
    case REMITO:
        $mysql->select("r.ID as col1, FROM_UNIXTIME(f.Fecha,'%D %M %Y') as col2, r.*");
        $mysql->join("ventas r", "r.FormularioID = f.ID");
        if (strlen($js_var['_text']) )
            $mysql->where("f.Numero = ".$js_var['_text']);

        $mysql->where("r.Remito = 1");
        $mysql->where("r.ClienteID = ".$params['cliente_id']);

        $elementos = $mysql->get_tabla("formularios f");
        break;
    case CUENTAS_CONTABLES:
        $mysql->select("Atajo as col1, CONCAT('(',Codigo,') ',Denominacion) as col2, c_cuentas.*");
        if (is_integer($js_var['_text'])){
            $mysql->where("(Atajo = ".$js_var['_text'].")  ");
        }
        else{
            $mysql->where("Denominacion like '%".$js_var['_text']."%' OR Codigo like '%".$js_var['_text']."%'");
        }
        $elementos = $mysql->get_tabla("c_cuentas");
        break;
    case PRODUCTO_LUGAR:
        $field = "Denominacion";
        
        if($js_var['_text']){
            if (is_numeric($js_var['_text'])){
                $mysql->where("CodInt = ".$js_var['_text']." OR CodBar = ".$js_var['_text']);
            }
            else{
                $mysql->where("Denominacion like '%".$js_var['_text']."%'");
            }
        }
        $precios = '';
        if (isset($params['precio'])){

            $precios = ", Precio".$params['precio']." as Precio";
        }
        $mysql->select("Stock as Cantidad, a.*, a.Denominacion as col1,a.Descripcion as col2, 0 as FormularioID ".$precios)
            ->where("Stock > 0 AND LugarID = ".$params['lugar'])
            ->join("articulos a", "a.ID = s.ArticuloID ");

        if (isset($params['precio'])){
            $mysql->join("lista_precios lp", "lp.ArticuloID = a.ID");
        }
        
        $elementos = $mysql->get_tabla("stock s");
        for($i = 0 ; $i < count($elementos) ; $i++){
            $elementos[$i]['Descripcion'] =
                $elementos[$i]['Descripcion']."- Cantidad: ".$elementos[$i]["Cantidad"];
        }
        break;

    case PRODUCTO_COMPRA :
        /*
select sum(Ingreso)-sum(Egreso) as Cantidad, a.* from compra c
join movimiento_stock ms on c.FormularioID = ms.FormularioID
join articulos a on ArticuloID = a.ID
group by Numero,ArticuloID
        */
        $mysql->select("a.*, a.Denominacion as col1, a.Descripcion as col2, sum(Ingreso)-sum(Egreso) as Cantidad, a.ID as ID, c.FormularioID,c.ProveedorID,c.ProveedorNombre,c.Tipo_factura,c.Tipo_factura_descripcion,c.Numero, c.Sucursal, c.Imputacion, c.Fpago");
        if (is_numeric($js_var['_text'])){
            $mysql->join("movimiento_stock ms", "ms.FormularioID = c.FormularioID");
            $mysql->join("articulos a", "a.ID = ms.ArticuloID");
            $mysql->group_by("Numero,ArticuloID") ;
            $mysql->having("Cantidad > 0");
            $elementos = $mysql->get_tabla("compra c","(CodInt = ".$js_var['_text']." OR CodBar = ".$js_var['_text'].")  AND Nc = 0");
        }
        else{
            $mysql->join("movimiento_stock ms", "ms.FormularioID = c.FormularioID");
            $mysql->join("articulos a", "a.ID = ms.ArticuloID");
            $mysql->group_by("Numero,ArticuloID") ;
            $mysql->having("Cantidad > 0");
            $elementos = $mysql->get_tabla("compra c","Denominacion like '%".$js_var['_text']."%' AND Nc = 0");
        }
        for($i = 0 ; $i < count($elementos) ; $i++){
            $elementos[$i]['col2'] =  
                $elementos[$i]['ProveedorNombre']."-".$elementos[$i]["Sucursal"]."-".
                $elementos[$i]['Tipo_factura_descripcion'].
                $elementos[$i]["Numero"].
                " - Cantidad: ".$elementos[$i]["Cantidad"];
        }
        break;

    case PRODUCTO :
        $field = "Denominacion";
        $mysql->select("a.*, Denominacion as col1, Descripcion as col2");
        if (is_numeric($js_var['_text'])){
            $field = "CodInt";
            $elementos = $mysql->get_tabla("articulos a","CodInt = ".$js_var['_text']." OR CodBar = ".$js_var['_text']);
        }
        else{
            $elementos = $mysql->get_tabla("articulos a","Denominacion like '%".$js_var['_text']."%'");
        }

        break;
    case COMPONENTE :
        $field = "Denominacion";
        $mysql->select("a.*, Denominacion as col1, Descripcion as col2");
        if (is_numeric($js_var['_text'])){
            $field = "CodInt";
            $elementos = $mysql->get_tabla("articulos a" ,"(CodInt = ".$js_var['_text']." OR CodBar = ".$js_var['_text'].")  AND Tipo_componente in (1,2)");
        }
        else{
            $elementos = $mysql->get_tabla("articulos a","Denominacion like '%".$js_var['_text']."%'  AND Tipo_componente in (1,2)");
        }
        break;
    case PRODUCTO_FABRICADO :
        $field = "Denominacion";
        $mysql->select("a.*, Denominacion as col1, Descripcion as col2");
        if (is_numeric($js_var['_text'])){
            $field = "CodInt";
            $elementos = $mysql->get_tabla("articulos a","(CodInt = ".$js_var['_text']." OR CodBar = ".$js_var['_text'].")  AND Tipo_componente in (0)");
        }
        else{
            $elementos = $mysql->get_tabla("articulos a","Denominacion like '%".$js_var['_text']."%'  AND Tipo_componente in (0)");
        }
        break;
    case PRODUCCION :
        
        $field = "Denominacion";
        $mysql->select("a.*, a.Denominacion as col1, a.Descripcion as col2");
        $mysql->join("lugares_productos lp", "a.ID = lp.ArticuloID");
        $mysql->where("LugarID = ".$params['lugar']);
        
        if (is_numeric($js_var['_text'])){
            $field = "CodInt";
            $myslq->where("(CodInt = ".$js_var['_text']." OR CodBar = ".$js_var['_text'].")  ");
            $elementos = $mysql->get_tabla("articulos a");
        }
        else{
            $mysql->where("Denominacion like '%".$js_var['_text']."%'");

            $elementos = $mysql->get_tabla("articulos a");
        }
        break;
    case CLIENTE :
        $mysql->select("Razon_social as Denominacion, Razon_social as col1, Cuil as Descripcion, Cuil as col2,  clientes.ID, Lista, Porcentaje, tipo_iva.Denominacion as Tipo_iva");

        $mysql->join("tipo_iva", "tipo_iva.ID = clientes.Tipo_iva");
        if (is_numeric($js_var['_text'])){
            $field = "Codigo";

            $elementos = $mysql->get_tabla("clientes","clientes.ID = ".$js_var['_text']);
        }
        else{
            $elementos = $mysql->get_tabla("clientes","Razon_social like '%".$js_var['_text']."%'");
        }
        break;
    case PROVEEDOR:
        $field = "Denominacion";
        $mysql->select("Razon_social as Denominacion, Razon_social as col1, Cuil as Descripcion, Cuil as col2, Codigo");
        if (is_numeric($js_var['_text'])){
            $field = "Codigo";

            $elementos = $mysql->get_tabla("proveedores","Codigo = ".$js_var['_text']);
        }
        else{
            $elementos = $mysql->get_tabla("proveedores","Razon_social like '%".$js_var['_text']."%'");
        }
        break;
}

$js_array['_array_search'] = $elementos;

?>