<ul class="lista-informe desembolsos">
    <li class="titulo">
        <span class="numero-desembolso">NUMERO</span>
        <span class="fecha-desembolso">FECHA</span>
        <span class="monto-desembolso">MONTO</span>
        <span class="porcentaje-desembolso">PORCENTAJE</span>
    </li>    
<?php 
$DESEMBOLSADO = 0;
foreach($desembolsos as $desembolso){ 
    $DESEMBOLSADO += $desembolso['MONTO'];?>
    <li class="datos">
        <span class="numero-desembolso"><?=$desembolso['NUMERO']?></span>
        <span class="fecha-desembolso"><?=$desembolso['FECHA']?></span>
        <span class="monto-desembolso">$<?=number_format($desembolso['MONTO'],2,",",".")?></span>
        <span class="porcentaje-desembolso"><?=number_format($desembolso['PORCENTAJE'],2,",",".")?>%</span>
    </li>
<?php } ?>
</ul>

<div class="detalle-informe">
    <div class="line">
        <span class="titulo">MONTO CREDITO</span>
        <span class="data"><?=  number_format($MONTO_CREDITO,2,",",".")?></span>
        <span class="data">100%</span>
    </div>
    <div class="line">
        <span class="titulo">DESEMBOLSADO</span>
        <span class="data"><?=  number_format($DESEMBOLSADO,2,",",".")?></span>
        <span class="data"><?=  number_format($DESEMBOLSADO * 100 / $MONTO_CREDITO,2,",",".")?>%</span>
    </div>
    <div class="line">
        <span class="titulo">A DESEMBOLSAR</span>
        <span class="data"><?=number_format($MONTO_CREDITO -$DESEMBOLSADO,2,",",".")?> </span>
        <span class="data"><?=  number_format(100-($DESEMBOLSADO * 100 / $MONTO_CREDITO),2,",",".")?>%</span>
    </div>
</div>
