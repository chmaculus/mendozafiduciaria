<?php 
$arr_permiso_mod = isset($_SESSION["USER_PERMISOS"][12])?$_SESSION["USER_PERMISOS"][12]:0;
$arr_permiso_mod = ($_SESSION["USERADM"] && $arr_permiso_mod && $arr_permiso_mod['BAJA'] && $arr_permiso_mod['MODIFICACION']) ? TRUE : FALSE;
?>
<ul class="lista-informe pagos">
    <li class="titulo">
        <span class="numero-paago">NUMERO</span>
        <span class="fecha-pago">FECHA</span>
        <span class="monto-pago">MONTO</span>
        <span class="recibo-recibo">RECIBO</span>
        <span class="recibo-ver">Ver</span>
        <?php if($arr_permiso_mod) { ?><span class="recibo-eliminar">Eliminar</span><?php } ?>
    </li>    
<?php foreach($pagos as $pago){ ?>
    <li class="datos pago">
        <span class="numero-pago"><?=$pago['NUMERO']?></span>
        <span class="fecha-pago"><?=$pago['FECHA']?></span>
        <span class="monto-pago">$<?=number_format($pago['MONTO'],2,",",".")?></span>
        <span class="recibo-recibo"><?=$pago['RECIBO']?></span>
        <span class="recibo-ver"><button onclick="ver_print(<?=$pago['ID_PAGO']?>, this);"><i class="fa fa-print"></i></button><button onclick="ver_detalle(<?=$pago['ID_PAGO']?>, this);">( + )</button></span>
        <?php if($arr_permiso_mod) { ?><span class="recibo-eliminar"><button onclick="eliminar_pago(<?=$pago['ID_PAGO']?>);">( x )</button></span><?php } ?>
        <div class="detalle-pago-lista"></div>
    </li>
<?php } ?>
</ul>

