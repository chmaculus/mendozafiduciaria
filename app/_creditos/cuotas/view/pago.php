
<?php
foreach ($cuotas as $cuota) {
    if (!isset($cuota['PAGOS']))
        continue;

    $pago = $cuota['PAGOS'];
    ?>
    <div class="cuotas_div_pagos">
        <span class="cuota_title">CUOTA NRO.<?=$cuota['NUM']?></span>

        <ul class="ul-lista-pagos">

            <li>
                <?php if (isset($pago['SUMA']['GASTO'])) { ?>
                    <div class="full_field">
                        <span class="detalle-pago"><?= $pago['SUMA']['GASTO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['SUMA']['GASTO']['MONTO'] ?></span>

                    </div>
                <?php } ?>
                <?php if (isset($pago['RESTA']['GASTO'])) { ?>
                    <div class="full_field resta">
                        <span class="detalle-pago"><?= $pago['RESTA']['GASTO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['RESTA']['GASTO']['MONTO'] ?></span>

                    </div>
                <?php } ?>

                <?php if (isset($pago['SUMA']['IVA_MORATORIO'])) { ?>
                    <div class="full_field">
                        <span class="detalle-pago"><?= $pago['SUMA']['IVA_MORATORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['SUMA']['IVA_MORATORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>
                <?php if (isset($pago['RESTA']['IVA_MORATORIO'])) { ?>
                    <div class="full_field resta">
                        <span class="detalle-pago"><?= $pago['RESTA']['IVA_MORATORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['RESTA']['IVA_MORATORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>

                <?php if (isset($pago['SUMA']['IVA_PUNITORIO'])) { ?>
                    <div class="full_field">
                        <span class="detalle-pago"><?= $pago['SUMA']['IVA_PUNITORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['SUMA']['IVA_PUNITORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>
                <?php if (isset($pago['RESTA']['IVA_PUNITORIO'])) { ?>
                    <div class="full_field resta">
                        <span class="detalle-pago"><?= $pago['RESTA']['IVA_PUNITORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['RESTA']['IVA_PUNITORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>



                <?php if (isset($pago['SUMA']['IVA_COMPENSATORIO'])) { ?>
                    <div class="full_field">
                        <span class="detalle-pago"><?= $pago['SUMA']['IVA_COMPENSATORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['SUMA']['IVA_COMPENSATORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>
                <?php if (isset($pago['RESTA']['IVA_COMPENSATORIO'])) { ?>
                    <div class="full_field resta">
                        <span class="detalle-pago"><?= $pago['RESTA']['IVA_COMPENSATORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['RESTA']['IVA_COMPENSATORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>

                <?php if (isset($pago['SUMA']['PUNITORIO'])) { ?>
                    <div class="full_field">
                        <span class="detalle-pago"><?= $pago['SUMA']['PUNITORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['SUMA']['PUNITORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>
                <?php if (isset($pago['RESTA']['PUNITORIO'])) { ?>
                    <div class="full_field resta">
                        <span class="detalle-pago"><?= $pago['RESTA']['PUNITORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['RESTA']['PUNITORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>

                <?php if (isset($pago['SUMA']['MORATORIO'])) { ?>
                    <div class="full_field">
                        <span class="detalle-pago"><?= $pago['SUMA']['MORATORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['SUMA']['MORATORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>
                <?php if (isset($pago['RESTA']['MORATORIO'])) { ?>
                    <div class="full_field resta">
                        <span class="detalle-pago"><?= $pago['RESTA']['MORATORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['RESTA']['MORATORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>

                <?php if (isset($pago['SUMA']['COMPENSATORIO'])) { ?>
                    <div class="full_field">
                        <span class="detalle-pago"><?= $pago['SUMA']['COMPENSATORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['SUMA']['COMPENSATORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>
                <?php if (isset($pago['RESTA']['COMPENSATORIO'])) { ?>
                    <div class="full_field resta">
                        <span class="detalle-pago"><?= $pago['RESTA']['COMPENSATORIO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['RESTA']['COMPENSATORIO']['MONTO'] ?></span>
                    </div>
                <?php } ?>
                <?php if (isset($pago['SUMA']['CAPITAL'])) { ?>
                    <div class="full_field">
                        <span class="detalle-pago"><?= $pago['SUMA']['CAPITAL']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['SUMA']['CAPITAL']['MONTO'] ?></span>
                    </div>
                <?php } ?>
                <?php if (isset($pago['RESTA']['CAPITAL'])) { ?>
                    <div class="full_field resta">
                        <span class="detalle-pago"><?= $pago['RESTA']['CAPITAL']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $pago['RESTA']['CAPITAL']['MONTO'] ?></span>
                    </div>
                <?php } ?>
            </li>
        </ul>

    </div>    

<?php } ?>
<?php if ($cuotas[0]['NOIMPUTADO']) { ?>
<div class="cuotas_div_pagos">
        <span class="cuota_title">OTROS</span>

            <ul class="ul-lista-pagos">
                <li>
                
                    <div class="full_field">

                        <span class="detalle-pago"><?= $cuotas[0]['NOIMPUTADO']['DETALLE'] ?></span>
                        <span class="monto-pago"><?= $cuotas[0]['NOIMPUTADO']['MONTO'] ?></span>

                    </div>
                    
                </li>
            </ul>
        </div>
<?php } ?>