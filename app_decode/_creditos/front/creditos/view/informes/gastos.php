<ul class="lista-informe desembolsos">
    <li class="titulo">
        <span class="detalle-gasto">DETALLE</span>
        <span class="fecha-desembolso">FECHA CARGA</span>
        <span class="monto-desembolso">MONTO</span>
        <span class="monto-desembolso">CANCELADO</span>
        <span class="monto-desembolso">SALDO</span>
    </li>    
<?php foreach($gastos as $gasto){ ?>
    <li class="datos">
        <span class="detalle-gasto"><?=$gasto['CONCEPTO']?></span>
        <span class="fecha-desembolso"><?=$gasto['FECHA_CARGA']?></span>
        <span class="monto-desembolso">$<?=number_format($gasto['MONTO'],2)?></span>
        <span class="monto-desembolso">$<?=number_format($gasto['PAGADO'],2)?></span>
        <span class="monto-desembolso">$<?=number_format($gasto['SALDO'],2)?></span>
        
    </li>
<?php } ?>
</ul>
