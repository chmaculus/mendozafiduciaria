<div class="content-cuotas-variaciones">
    <ul>
        <?php $i = 1;
        foreach ($cuotas as $cuota) { ?>
            <li>
                <span class="fecha_span"><?= date("d/m/Y", $cuota['FECHA_INICIO']) ?></span>
                <span class="fecha_span"><?= date("d/m/Y", $cuota['FECHA_VENCIMIENTO']) ?></span>
                <span class="fecha_span"><?= $cuota['CAPITAL_CUOTA'] ?></span>
                <span class="fecha_span"><?= $cuota['CAPITAL_AMORTIZAR'] ?></span>
                <span class="fecha_span"><?= $cuota['INT_COMPENSATORIO'] ?></span>
            </li>
<?php } ?>
    </ul>
</div>