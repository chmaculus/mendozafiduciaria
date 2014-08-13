<?php


$alertas = $mysql->get_tabla("alertas");

$cheque_dia = 0;
$stock_porcentaje = 0;
if ($alertas){
    $cheque_dia = $alertas[0]['Cheques'];
    $stock_porcentaje  = (100 + $alertas[0]['Stock']) / 100;
}


//obtenemos cheques no cancelados
$dias_cheques = $cheque_dia * 60 * 60 * 24; 
$hoy = mktime(0,0,0,10,24,2012);
$fecha_alerta = $hoy + $dias_cheques;

$mysql->where("Cancelado = 0");
$mysql->order_by("Fecha_presentacion","desc");
$mysql->where("Fecha_presentacion <= ".$fecha_alerta." AND Fecha_presentacion > ".$hoy);
$mysql->select("pc.*, b.Denominacion as BANCO, p.Denominacion as PROVINCIA");
$mysql->join("bancos b","b.ID = pc.BancoID");
$mysql->join("provincias p","p.ID = pc.ProvinciaID");
$cheques_alerta = $mysql->get_tabla("pago_cheque pc");

$mysql->where("Cancelado = 0");
$mysql->order_by("Fecha_presentacion","desc");
$mysql->where("Fecha_presentacion <= ".$hoy);
$mysql->select("pc.*, b.Denominacion as BANCO, p.Denominacion as PROVINCIA");
$mysql->join("bancos b","b.ID = pc.BancoID");
$mysql->join("provincias p","p.ID = pc.ProvinciaID");
$cheques_pasados = $mysql->get_tabla("pago_cheque pc");


//obtenemos stock
$mysql->select("a.Denominacion as ARTICULO, CodBar,l.Denominacion as LUGAR, Stock, s.Stock_minimo");
$mysql->join("articulos a"," a.ID = s.ArticuloID");
$mysql->join("lugares l"," l.ID = s.LugarID");
$mysql->where("Stock  < s.Stock_minimo ");
$stock_alerta = $mysql->get_tabla("stock s");


$mysql->select("a.Denominacion as ARTICULO, CodBar,l.Denominacion as LUGAR, Stock, s.Stock_minimo");
$mysql->join("articulos a"," a.ID = s.ArticuloID");
$mysql->join("lugares l"," l.ID = s.LugarID");
$mysql->where("Stock  < (s.Stock_minimo * ".$stock_porcentaje.") AND Stock >= s.Stock_minimo");
$stock_aviso = $mysql->get_tabla("stock s");

if (isset($params['onlyalert'])){
    $cantidad_alertas = count($cheques_pasados) + count($stock_alerta) ;
    $cantidad_avisos = count($cheques_alerta) + count($stock_aviso) ;
    include($DIR."alert.php");
    die();
}

?>