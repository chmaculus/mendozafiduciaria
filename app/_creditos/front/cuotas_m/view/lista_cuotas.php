
<div id="div-list-cuotas">
    <ul class="ul-titulo">
        <li>
            <span class="fecha_sp">INIC.</span>
            <span class="fecha_sp">VENC.</span>
            <span class="cuota">C.RES</span>
            <span class="capital">MONTO</span>
            <span class="por_int_compensatorio">% COMP</span>
            <span class="capital">INT.COMP.</span>
            <span class="capital">INT.MOR</span>
            <span class="capital">INT.PUN</span>
            <span class="capital">S.CAPITAL</span>
            <span class="capital">A.TEORICA</span>
        </li>
    </ul>
    
        <?php
        foreach ($RESULT as $credito) { ?>
            <div class="datos-credito">
                <span class="credito_id">CREDITO: <?=$credito['CREDITO']?></span>
            </div>
            <ul class="ul-cuotas">
            <?php
            foreach ($credito['CUOTAS']as $cuota) {
                ?>
                <li class="cuotas" >
                    <div class="linea-cuota">
                        <span class="fecha_sp" onclick="modificar_opciones_cuota(<?= $cuota['ID_CREDITO'] ?>,<?= $cuota['CUOTAS_RESTANTES'] ?>, event);"><?= date("d/m/Y", $cuota['FECHA_INICIO']) ?></span>
                        <span class="fecha_sp" onclick="modificar_opciones_cuota(<?= $cuota['ID_CREDITO'] ?>,<?= $cuota['CUOTAS_RESTANTES'] ?>, event);"><?= date("d/m/Y", $cuota['FECHA_VENCIMIENTO']) ?></span>
                        <span class="cuota"><?= $cuota['CUOTAS_RESTANTES'] ?></span>
                        <span class="capital"><?= number_format($cuota['CAPITAL_CUOTA'], 2) ?></span>
                        <span class="por_int_compensatorio"><?= number_format($cuota['POR_INT_COMPENSATORIO'], 2) ?></span>
                        <span class="capital"><?= number_format($cuota['INT_COMPENSATORIO'], 2) ?></span>
                        <span class="capital"><?= number_format($cuota['INT_MORATORIO'], 2) ?></span>
                        <span class="capital"><?= number_format($cuota['INT_PUNITORIO'], 2) ?></span>
                        <span class="capital"><?= number_format($cuota['SALDO_CAPITAL_TEORICO'], 2) ?></span>
                        <span class="capital"><?= number_format($cuota['AMORTIZACION_TEORICA'], 2) ?></span>
                    </div>
                </li>
            <?php } ?>
            </ul>
        <?php 
        }
        ?>
    

    <a id="inline" href="#div_opciones_cuotas"></a>

    <div style="display:none"><div id="div_opciones_cuotas"></div></div>

</div>