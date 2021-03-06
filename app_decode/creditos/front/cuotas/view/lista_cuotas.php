<span class="titulo-ingreso-eventos">LISTADO DE CUOTAS</span>
<div id="div-list-cuotas">
    <ul class="ul-titulo">
        <li>
                <span class="fecha_sp_inicio">INIC.</span>
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
    <ul class="ul-cuotas">
        <?php foreach ($RESULT as $cuota) { 
            $benviado = false;
            ?>
            <li class="cuotas" >
                <div class="linea-cuota">
                    <span class="fecha_sp_inicio" onclick="modificar_opciones_cuota(<?=$cuota['ID_CREDITO'] ?>,<?=$cuota['CUOTAS_RESTANTES'] ?>,event);"><?= date("d/m/Y", $cuota['FECHA_INICIO']) ?></span>
                    <span class="fecha_sp" onclick="modificar_opciones_cuota(<?=$cuota['ID_CREDITO'] ?>,<?=$cuota['CUOTAS_RESTANTES'] ?>,event);"><?= date("d/m/Y", $cuota['FECHA_VENCIMIENTO']) ?></span>
                    <span class="cuota"><?=$cuota['CUOTAS_RESTANTES'] ?></span>
                    <span class="capital"><?= number_format($cuota['CAPITAL_CUOTA'], 2) ?></span>
                    <span class="por_int_compensatorio"><?= number_format($cuota['POR_INT_COMPENSATORIO'], 2,",",".") ?></span>
                    <span class="capital"><?= number_format($cuota['INT_COMPENSATORIO'], 2,",",".") ?></span>
                    <span class="capital"><?= number_format($cuota['INT_MORATORIO'], 2,",",".") ?></span>
                    <span class="capital"><?= number_format($cuota['INT_PUNITORIO'], 2,",",".") ?></span>
                    <span class="capital"><?= number_format($cuota['SALDO_CAPITAL_TEORICO'], 2,",",".") ?></span>
                    <span class="capital"><?= number_format($cuota['AMORTIZACION_TEORICA'], 2,",",".") ?></span>
                </div>

                <?php if ($cuota['SEGMENTOS']) { ?>
                <div class="content-segmento">
                    <ul class="ul-titulo-segmento">
                        <li>
                                <span class="fecha_no_select">FECHA</span>
                                <span class="cuota">INT.COMP.</span>
                                <span class="capital">INT.COMP.IVA</span>
                                <span class="por_int_compensatorio">DIAS</span>
                                <span class="capital">TIPO</span>
                        </li>
                    </ul>            

                    <ul class="segmentos-ul">
                        <?php foreach ($cuota['GASTOS'] as $gasto) { ?>
                        <li class="subcuentas">
                                <span class="cuota_enviada"><?= date("d/m/Y", $gasto['FECHA']) ?></span>
                                <span class="capital"> &nbsp;</span>
                                <span class="capital">&nbsp;</span>
                                <span class="dias">&nbsp;</span>
                                <span class="dias">GASTOS</span>
                                    <span class="capital">$<?= $gasto['MONTO'] ?></span>
                                    <div class="opciones_evento">
                                        <span class="remove" onclick="eliminar_gasto(<?= $gasto['ID'] ?>);">&nbsp;</span>
                                    </div>

                        </li>
                        <?php } 
                        
                        
                        ?>
                        <?php foreach ($cuota['SEGMENTOS'] as $segmento) { ?>
                            <li class="subcuentas">
                                <?php

                                if ($cuota['FECHA_ENVIADA'] <= $segmento['FECHA_VENCIMIENTO'] && $cuota['FECHA_ENVIADA'] >= $segmento['FECHA_INICIO']){ 
                                    $benviado = true;
                                    ?>
                                    <span class="fecha_no_select">ENVIO DE CUOTA: <?= date("d/m/Y", $cuota['FECHA_ENVIADA']) ?></span>
                                <?php } ?>
                                
                                <span class="fecha_no_select"><?= date("d/m/Y", $segmento['FECHA_VENCIMIENTO']) ?></span>
                                <span class="capital"><?= number_format($segmento['INT_COMPENSATORIO'], 2,",",".") ?></span>
                                <span class="capital"><?= number_format($segmento['INT_COMPENSATORIO_IVA'], 2,".",",") ?></span>
                                <span class="dias"><?= $segmento['DIAS_TRANSCURRIDOS'] ?></span>
                                <span class="dias"><?= $segmento['TIPO'] ?></span>
                                <?php if ($segmento['TIPO']) { ?>
                                    <span class="capital"><?= $segmento['MONTO'] ?></span>
                                    <div class="opciones_evento">
                                        <span class="remove" onclick="eliminar_variacion(<?= $segmento['_ID_VARIACION'] ?>);">&nbsp;</span>
                                        <?php if ($segmento['TIPO']==3) { ?>
                                        <span class="edit" onclick="mostrar_variacion(<?= $segmento['_ID_VARIACION'] ?>);">[M]</span>
                                        <?php } ?>
                                    </div>

                                <?php } 
                                
                                ?>

                            </li>
                        <?php } 
                            if ($cuota['FECHA_ENVIADA'] > 0 && !$benviado ){ ?>
                            <span class="cuota_enviada">ENVIO DE CUOTA: <?= date("d/m/Y", $cuota['FECHA_ENVIADA']) ?></span>
                        <?php } ?>
                        
                    </ul>
                </div>
                <?php } ?>

            </li>
        <?php } ?>
            <li class="cuotas" >

                <?php 
                if ($VARIACIONES) { ?>
                    <ul class="segmentos-ul">
                        <?php foreach ($VARIACIONES as $variacion) { ?>
                            <li class="subcuentas">
                                <span class="fecha_sp"><?= date("d/m/Y", $variacion['FECHA']) ?></span>
                                <span class="dias"><?= $variacion['TIPO'] ?></span>

                                
                                <?php if (isset($segmento['TIPO'])) if ($segmento['TIPO']) { ?>
                                    <span class="capital"><?= $variacion['TOTAL'] ?></span>
                                    <div class="opciones_evento">
                                        <span class="remove" onclick="eliminar_variacion(<?= $variacion['ID'] ?>);">&nbsp;</span>
                                        <?php if ($segmento['TIPO']==3) { ?>
                                        <span class="remove" onclick="mostrar_variacion(<?= $variacion['ID'] ?>);">[M]</span>
                                        <?php } ?>
                                    </div>                                                                

                                <?php } ?>

                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>

            </li>        
    </ul>

    <a id="inline" href="#div_opciones_cuotas"></a>

    <div style="display:none">
        <div id="div_opciones_cuotas">
            
        </div>
    </div>

</div>