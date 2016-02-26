<ul class="lista-informe pagos">
    <li class="titulo">
        <span class="numero-paago">NUMERO</span>
        <span class="fecha-pago">FECHA</span>
        <span class="monto-pago">MONTO</span>
        <span class="recibo-recibo">RECIBO</span>
        <span class="recibo-ver">Ver</span>
        <?php if($_SESSION["USERADM"]) { ?><span class="recibo-eliminar">Eliminar</span><?php } ?>
    </li>    
<?php foreach($pagos as $pago){ ?>
    <li class="datos pago">
        <span class="numero-pago"><?=$pago['NUMERO']?></span>
        <span class="fecha-pago"><?=$pago['FECHA']?></span>
        <span class="monto-pago">$<?=number_format($pago['MONTO'],2,",",".")?></span>
        <span class="recibo-recibo">0001122456-01</span>
        <span class="recibo-ver"><button onclick="ver_detalle(<?=$pago['ID_PAGO']?>, this);">( + )</button></span>
        <?php if($_SESSION["USERADM"]) { ?><span class="recibo-eliminar"><button onclick="eliminar_pago(<?=$pago['ID_PAGO']?>);">( x )</button></span><?php } ?>
        <div class="detalle-pago-lista"></div>
    </li>
<?php } ?>
</ul>

