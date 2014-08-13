<div class="saldo_cuotas">
    <ul>
        <li class="titulos-saldos">
            <div class="">
                <span class="splabel">CONCEPTO</span>
                <span class="total">TOTAL</span>
                <span class="cancelado">CANCELADO</span>
                <span class="saldo">SALDO</span>                
            </div>
        </li>

        
        <?php foreach ($cuotas as $cuota) { ?>
            <li>
                <div class="titulo_cuota">CUOTA Nº <?=$cuota['_INFO']['NUM']?> - FECHA DE VENCIMIENTO: <?=date("d/m/Y",$cuota['_INFO']['HASTA'])?></div>
                
                <?php if ($cuota['GASTOS']){?>
                <div class="gastos">
                    <span class="splabel">Gastos</span>
                    <span class="total"><?= round($cuota['GASTOS']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['GASTOS']['TOTAL'] - $cuota['GASTOS']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['GASTOS']['SALDO'],2) ?></span>
                </div>
                <?php } ?>
                
                <?php if (isset($cuota['PUNITORIO'])){?>
                <div class="iva_punitorio">
                    <span class="splabel">Impuestos Interes Punitorio</span>
                    <span class="total"><?= round($cuota['IVA_PUNITORIO']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['IVA_PUNITORIO']['TOTAL'] - $cuota['IVA_PUNITORIO']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['IVA_PUNITORIO']['SALDO'],2) ?></span>
                </div>
                <div class="iva_moratorio">
                    <span class="splabel">Impuestos Interes Moratorio</span>
                    <span class="total"><?= round($cuota['IVA_MORATORIO']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['IVA_MORATORIO']['TOTAL'] - $cuota['IVA_MORATORIO']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['IVA_MORATORIO']['SALDO'],2) ?></span>    
                </div>
                <?php } ?>
                
                <div class="iva_compensatorio">
                    <span class="splabel">Impuestos Interes Compensatorio</span>
                    <span class="total"><?= round($cuota['IVA_COMPENSATORIO']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['IVA_COMPENSATORIO']['TOTAL'] - $cuota['IVA_COMPENSATORIO']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['IVA_COMPENSATORIO']['SALDO'],2) ?></span>
                </div>
                <div class="iva_compensatorio_subsidiado">
                    <span class="splabel">Subsidio Imp. Int. Compensatorio</span>
                    <span class="total"><?= round($cuota['IVA_COMPENSATORIO']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['IVA_COMPENSATORIO']['TOTAL'] - $cuota['IVA_COMPENSATORIO']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['IVA_COMPENSATORIO']['SALDO'],2) ?></span>
                </div>
                <?php if (isset($cuota['PUNITORIO'])){?>
                <div class="punitorio">
                    <span class="splabel">Interes Punitorio</span>
                    <span class="total"><?= round($cuota['PUNITORIO']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['PUNITORIO']['TOTAL'] - $cuota['PUNITORIO']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['PUNITORIO']['SALDO'],2) ?></span>    
                </div>
                <div class="moratorio">
                    <span class="splabel">Interes Moratorio</span>
                    <span class="total"><?= round($cuota['MORATORIO']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['MORATORIO']['TOTAL'] - $cuota['MORATORIO']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['MORATORIO']['SALDO'],2) ?></span>    
                </div>
                <?php } ?>
                
                <div class="compensatorio">
                    <span class="splabel">Interes Compensatorio</span>
                    <span class="total"><?= round($cuota['COMPENSATORIO']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['COMPENSATORIO']['TOTAL'] - $cuota['COMPENSATORIO']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['COMPENSATORIO']['SALDO'],2) ?></span>    
                </div>
                <div class="subsidio_compensatorio">
                    <span class="splabel">Subsidio Int. Compensatorio</span>
                    <span class="total"><?= round($cuota['IVA_COMPENSATORIO']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['IVA_COMPENSATORIO']['TOTAL'] - $cuota['IVA_COMPENSATORIO']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['IVA_COMPENSATORIO']['SALDO'],2) ?></span>
                </div>                
                <div class="capital">
                    <span class="splabel">Capital Cuota</span>
                    <span class="total"><?= round($cuota['CAPITAL']['TOTAL'],2) ?></span>
                    <span class="cancelado"><?= round($cuota['CAPITAL']['TOTAL']) - round($cuota['CAPITAL']['SALDO'],2) ?></span>
                    <span class="saldo"><?= round($cuota['CAPITAL']['SALDO'],2) ?></span>    
                </div>
                
                <div class="resumen-cuota">
                    <div class="saldo">
                        <span class="splabel">AVISO_ENVIADO</span>
                        <span class="total"><?= $cuota['_INFO']['ENVIO'] ? date("d/m/Y",$cuota['_INFO']['ENVIO']) : "-" ?></span>
                    </div>
                    <div class="saldo">
                        <span class="splabel">SALDO DE CUOTA</span>
                        <span class="total"><?= round($cuota['_INFO']['SALDO_CUOTA'],2) ?></span>
                    </div>

                    <div class="saldo">
                        <span class="splabel">IMP. INT. POR MORA</span>
                        <span class="total"><?= round($cuota['_INFO']['TOT_INT_MOR_PUN'],2) ?></span>
                    </div>

                    <div class="saldo">
                        <span class="splabel">INT. POR MORA</span>
                        <span class="total"><?= round($cuota['_INFO']['TOT_IVA_INT_MOR_PUN'],2) ?></span>
                    </div>

                    <div class="saldo">
                        <span class="splabel">TOTAL A PAGAR</span>
                        <span class="total"><?= round($cuota['_INFO']['TOTAL_PAGAR'],2) ?></span>
                    </div>
                    <div class="saldo">
                        <span class="splabel">SALDO DE CAPITAL</span>
                        <span class="total"><?= round($cuota['_INFO']['SALDO_CAPITAL'],2) ?></span>
                    </div>
                </div>
            </li>
            
        <?php } ?>
    </ul>
</div>