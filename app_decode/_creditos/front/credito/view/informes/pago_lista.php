<div class="content-pago">
    
    <div class="resumen-pago">
        <div class="linea-pago">
            <span class="titulo-linea-pago">RECIBO</span>
            <span class="datos-linea-pago"><?=$cuotas[0]['RESUMEN']['RECIVO']?></span>
        </div>
        <div class="linea-pago">
            <span class="titulo-linea-pago">MONTO</span>
            <span class="datos-linea-pago"><?=number_format($cuotas[0]['RESUMEN']['TOTAL'],2,",",".");?></span>
        </div>
        <div class="linea-pago">
            <span class="titulo-linea-pago">FECHA IMPUTACION</span>
            <span class="datos-linea-pago"><?=$cuotas[0]['RESUMEN']['FECHA_IMPUTACION']?></span>
        </div>
        <div class="linea-pago">
            <span class="titulo-linea-pago">FECHA CARGA</span>
            <span class="datos-linea-pago"><?=$cuotas[0]['RESUMEN']['FECHA_CARGA']?></span>
        </div>
    </div>
    
    
    
<?php if ($cuotas[0]['GASTOS']) { ?>    
    
    <ul class="pagos-ul">
        <li class="titulo-pagos-cuota">
            <span class="pago-cuota">GASTOS</span>
        </li>        
        <li class="titulo-pagos">
            <span class="detalle-gasto">DETALLE</span>
            <span class="monto-gasto">MONTO</span>
        </li>
<?php
        foreach ($cuotas[0]['GASTOS'] as $gasto) {

            
            $monto = isset($gasto['MONTO']) ? $gasto['MONTO'] : 0;
            $descripcion = isset($gasto['CONCEPTO']) ? $gasto['CONCEPTO'] : "";
?>        
        
        <li class="titulo-datos">
            <span class="detalle-gasto"><?=$descripcion?></span>
            <span class="monto-gasto">$<?=number_format($monto,2,",",".");?></span>
        </li>
        <?php } ?>
        <li class="total-pago">
            <span class="subtotal-cuota-titulo">SUBTOTAL</span>
            <span class="subtotal-cuota-dato"><?=$cuotas[0]['RESUMEN']['TOTAL_GASTOS']?></span>
        </li>        
    </ul>
<?php } ?>
    <ul class="pagos-ul">
        <li class="titulo-pagos-cuota">
            <span class="pago-cuota">CUOTAS</span>
        </li>
        <li class="titulo-pagos">
            <span class="cuota">CUOTA</span>
            <span class="capital-pago">CAPITAL</span>
            <span class="compensatorio-pago">I.COMP.</span>
            <span class="moratorio-pago">I.MORAT.</span>
            <span class="punitorio-pago">I.PUNIT.</span>
            <span class="iva-pago">IVA</span>
            <span class="subtotal-pago">SUBTOTAL</span>

        </li>
        <?php
        $total_capital = 0;
        $total_compensatorio = 0;
        $total_moratorio = 0;
        $total_punitorio = 0;
        $total_iva = 0;
        foreach ($cuotas as $cuota) {
            if (!isset($cuota['PAGOS']))
                continue;

            $pago = $cuota['PAGOS'];
            $capital = isset($pago['SUMA']['CAPITAL']) ? $pago['SUMA']['CAPITAL']['MONTO'] : 0;
            $moratorio = isset($pago['SUMA']['MORATORIO']) ? $pago['SUMA']['MORATORIO']['MONTO'] : 0;
            $punitorio = isset($pago['SUMA']['PUNITORIO']) ? $pago['SUMA']['PUNITORIO']['MONTO'] : 0;
            $compensatorio = isset($pago['SUMA']['COMPENSATORIO']) ? $pago['SUMA']['COMPENSATORIO']['MONTO'] : 0;
            $iva_compensatorio = isset($pago['SUMA']['IVA_COMPENSATORIO']) ? $pago['SUMA']['IVA_COMPENSATORIO']['MONTO'] : 0;
            $iva_moratorio = isset($pago['SUMA']['IVA_MORATORIO']) ? $pago['SUMA']['IVA_MORATORIO']['MONTO'] : 0;
            $iva_punitorio = isset($pago['SUMA']['IVA_PUNITORIO']) ? $pago['SUMA']['IVA_PUNITORIO']['MONTO'] : 0;
            $iva_pago = $iva_compensatorio + $iva_moratorio + $iva_punitorio;
            $total = $capital + $iva_pago + $compensatorio + $moratorio + $punitorio;
            $total_capital += $capital;
            $total_compensatorio += $compensatorio;
            $total_moratorio += $moratorio;
            $total_punitorio += $punitorio;
            $total_iva += $iva_pago;
            
            ?>
            <li class="datos-pagos">
                <span class="cuota"><?= $cuota['NUM'] ?></span>
                <span class="capital-pago">$<?= number_format($capital,2,",",".") ?></span>
                <span class="compensatorio-pago"><?= number_format($compensatorio,2,",",".") ?></span>
                <span class="moratorio-pago"><?= number_format($moratorio,2,",",".") ?></span>
                <span class="punitorio-pago"><?= number_format($punitorio,2,",","."); ?></span>
                <span class="iva-pago"><?= number_format($iva_pago,2,",",".") ?></span>
                <span class="subtotal-pago">
                    <?= $total ?>
                </span>

            </li>
        <?php } ?>
          <li class="datos-pagos subtotales">
                <span class="cuota">TOTALES</span>
                <span class="capital-pago"><?=  number_format($total_capital,2,",",".")?></span>
                <span class="compensatorio-pago"><?= number_format($total_compensatorio,2,",",".") ?></span>
                <span class="moratorio-pago"><?= number_format($total_moratorio,2,",",".") ?></span>
                <span class="punitorio-pago"><?= number_format($total_punitorio,2,",",".") ?></span>
                <span class="iva-pago"><?= number_format($total_iva,2,",",".") ?></span>
                <span class="subtotal-pago">
                    <?=number_format($cuotas[0]['RESUMEN']['TOTAL_CUOTAS'],2,",",".")?>
                </span>

            </li>            
            <li class="total-pago">
                <span class="subtotal-cuota-titulo">SUBTOTAL</span>
                <span class="subtotal-cuota-dato"><?=number_format($cuotas[0]['RESUMEN']['TOTAL_CUOTAS'],2,",",".")?></span>
            </li>
    </ul>
</div>