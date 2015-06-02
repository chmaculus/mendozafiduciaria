<ul class="lista-informe pagos">
    <li class="titulo">
        <span class="numero-paago">NUMERO</span>
        <span class="fecha-pago">FECHA</span>
        <span class="monto-pago">MONTO</span>
        <span class="recibo-desembolso">RECIBO</span>
        <span class="recibo-desembolso">Ver</span>
    </li>    
<?php foreach($pagos as $pago){ ?>
    <li class="datos pago">
        <span class="numero-pago"><?=$pago['NUMERO']?></span>
        <span class="fecha-pago"><?=$pago['FECHA']?></span>
        <span class="monto-pago">$<?=number_format($pago['MONTO'],2,",",".")?></span>
        <span class="recibo-desembolso">0001122456-01</span>
        <span class="recibo-desembolso"><button onclick="ver_detalle(<?=$pago['ID_PAGO']?>, this);">( + )</button></span>
        
        <div class="detalle-pago-lista"></div>
    </li>
<?php } ?>
</ul>

