<div class="scroll-evolucion">
    <div class="pagos-evolucion">

        <!--TITULOS -->
        <div class="col-titulos">
            <div class="t2">
                <div class="full tit">&nbsp;</div>
                <div class="full subtit"><span class="col3">&nbsp</span></div>
            </div>

            <?php
            
            $evento_last = end($eventos);
            foreach ($eventos as $evento) { ?>      
                <div class="t1">
                    <div class="full">
                        <span class="col3">
                            <?= ($evento['T'] == 1 ? "VENC." : "COBR.") . " - " . date("d/m/Y", $evento['FECHA']) ?>
                        </span>
                    </div>
                </div>
            <?php } ?>
            <div class="t1">
                <div class="full total">
                    <span class="co3">Totales</span>
                </div>
            </div>
        </div>

        <!--CAPITAL -->
        <div class="col-titulos">
            <div class="t2">
                <div class="full tit">Capital</div>
                <div class="full subtit">
                    <span class="col">Deveng.</span>
                    <span class="col">Subs.</span>
                    <span class="col">Pago.</span>
                </div>
            </div>

            <?php 
             $total_total_col1 = $total_total_col2 = $total_total_col3 = 0;
            foreach ($eventos as $evento) { ?>      
                <div class="t1">
                    <div class="full">
                        <span class="col"><?= $evento['T'] == 1 ? number_format($evento['VALORES']['CAPITAL']['TOTAL'], 2,",",".") : "" ?></span>
                        <span class="col"></span>
                        <span class="col"><?= $evento['T'] == 3 ? number_format($evento['PAGOS'][7], 2,",",".") : "" ?></span>
                    </div>
                </div>
            <?php } ?>
            <div class="t1">
                <div class="full total">
                    <span class="col "><?=  number_format($total_total_col1,2,",",".")?></span>
                    <span class="col ">0,00</span>
                    <span class="col "><?=  number_format($total_total_col3,2,",",".")?></span>
                </div>
            </div>
        </div>

        <!-- INTERESES COMPENSATORIOS -->
        <div class="col-titulos">
            <div class="t2">
                <div class="full tit">Int Compensatorio</div>
                <div class="full subtit">
                    <span class="col">Deveng.</span>
                    <span class="col">Subs.</span>
                    <span class="col">Pago.</span>
                </div>
            </div>

            <?php 
            $ant_compensatorio = 0;
            $ant_pago_compensatorio = 0;
            $total_total_col1 = $total_total_col2 = $total_total_col3 = 0;
            foreach ($eventos as $evento) {
                $total_compensatorio = 0;
                
                if ($evento['FECHA'] <= $evento['VALORES']['_INFO']['HASTA'] ){
                    $acum_total_compensatorio = $evento['VALORES']['COMPENSATORIO']['TOTAL'];
                    $total_compensatorio = $acum_total_compensatorio - $ant_compensatorio;
                    
                    $ant_compensatorio += $total_compensatorio;
                }

                $pago_compensatorio = 0;
                $total_total_col1 += $total_compensatorio;
                
                //solo pagos
                $pago_compensatorio = 0;
                if ($evento['T']==3){
                    $pago_compensatorio = $evento['PAGOS'][PAGO_COMPENSATORIO];
                    
                }
                $total_total_col3 += $pago_compensatorio;
            /*    $ant_pago_compensatorio += $acum_pago_compensatorio ;
                
                $pago_compensatorio = $acum_pago_compensatorio - $ant_pago_compensatorio;
                
                $ant_compensatorio = $total_compensatorio ; 
                $total_total_col1 += $total_compensatorio ;
                if ($evento['T'] == 3){
                    $acum_pago_compensatorio = $evento['PAGOS'][PAGO_COMPENSATORIO];
                    $pago_compensatorio = $acum_pago_compensatorio - $ant_pago_compensatorio;
                    $ant_pago_compensatorio += $acum_pago_compensatorio ;
                    $total_total_col3 += $pago_compensatorio;
                }
                
                */
                ?>      
                <div class="t1">
                    <div class="full">
                        <span class="col"><?= number_format($total_compensatorio, 2,",",".")?></span>
                        <span class="col"></span>
                        <span class="col"><?= $evento['T'] == 3 ? number_format($pago_compensatorio, 2,",",".") : "" ?></span>
                    </div>
                </div>
            <?php } ?>
            <div class="t1">
                <div class="full total">
                    <span class="col "><?=  number_format($total_total_col1,2,",",".")?></span>
                    <span class="col ">0,00</span>
                    <span class="col "><?=  number_format($total_total_col3,2,",",".")?></span>
                </div>
            </div>
        </div>

        <!-- INTERESES MORATORIOS -->
        <div class="col-titulos">
            <div class="t2">
                <div class="full tit">Int Moratorio</div>
                <div class="full subtit">
                    <span class="col">Deveng.</span>
                    <span class="col">Subs.</span>
                    <span class="col">Pago.</span>
                </div>
            </div>

            <?php
            $ant_moratorio = 0;
            $total_total_col1 = $total_total_col2 = $total_total_col3 = 0;
            foreach ($eventos as $evento) {
                $total_moratorio = 0;
                
                if ($evento['FECHA'] > $evento['VALORES']['_INFO']['HASTA'] ){
                    $total_moratorio = $evento['VALORES']['MORATORIO']['TOTAL'];
                    $total_moratorio -= $ant_moratorio;
                }

                $ant_moratorio = $total_moratorio ; 
                
                $total_total_col1 += $total_moratorio;
                if ($evento['T'] == 3){
                    $total_total_col3 += $evento['PAGOS'][PAGO_MORATORIO];
                }
                
            ?>      
                <div class="t1">
                    <div class="full">
                        <span class="col"><?= $evento['T'] == 3 ? number_format($total_moratorio, 2,",",".") : "" ?></span>
                        <span class="col"></span>
                        <span class="col"><?= $evento['T'] == 3 ? number_format($evento['PAGOS'][PAGO_MORATORIO], 2,",",".") : "" ?></span>
                    </div>
                </div>
            <?php } ?>
            <div class="t1">
                <div class="full total">
                    <span class="col "><?=  number_format($total_total_col1,2,",",".")?></span>
                    <span class="col ">0,00</span>
                    <span class="col "><?=  number_format($total_total_col3,2,",",".")?></span>
                </div>
            </div>
        </div>

        <!-- INTERESES PUNITORIOS -->
        <div class="col-titulos">
            <div class="t2">
                <div class="full tit">Int Punitorios</div>
                <div class="full subtit">
                    <span class="col">Deveng.</span>
                    <span class="col">Subs.</span>
                    <span class="col">Pago.</span>
                </div>
            </div>

            <?php 
            
            $ant_punitorio = 0;
            $total_total_col1 = $total_total_col2 = $total_total_col3 = 0;
            foreach ($eventos as $evento) {
                $total_punitorio = 0;
                
                if ($evento['FECHA'] > $evento['VALORES']['_INFO']['HASTA'] ){
                    $total_punitorio = $evento['VALORES']['PUNITORIO']['TOTAL'];
                    $total_punitorio -= $ant_punitorio;
                }

                
                $ant_punitorio = $total_punitorio ;       
                
                $total_total_col1 +=   $total_punitorio;  
                if ($evento['T'] == 3 ){
                    $total_total_col3 +=   $evento['PAGOS'][PAGO_PUNITORIO];  
                }
                
                
                ?>      
                <div class="t1">
                    <div class="full">
                        <span class="col"><?= $evento['T'] == 3 ? number_format($total_punitorio, 2,",",".") : "" ?></span>
                        <span class="col"></span>
                        <span class="col"><?= $evento['T'] == 3 ? number_format($evento['PAGOS'][PAGO_PUNITORIO], 2,",",".") : "" ?></span>
                    </div>
                </div>
            <?php } ?>
            <div class="t1">
                <div class="full total">
                    <span class="col "><?=  number_format($total_total_col1,2)?></span>
                    <span class="col ">0,00</span>
                    <span class="col "><?=  number_format($total_total_col3,2)?></span>
                </div>
            </div>
        </div>

        <!-- IVA-->
        <div class="col-titulos">
            <div class="t2">
                <div class="full tit">IVA</div>
                <div class="full subtit">
                    <span class="col">Deveng.</span>
                    <span class="col">Subs.</span>
                    <span class="col">Pago.</span>
                </div>
            </div>

            <?php
            $ant_iva = 0;
            $ant_pagado_iva = 0;
            $total_total_col1 = $total_total_col2 = $total_total_col3 = 0;
            foreach ($eventos as $evento) {
                
                $iva_punitorio = $evento['VALORES']['IVA_PUNITORIO']['TOTAL'];
                $iva_moratorio = $evento['VALORES']['IVA_MORATORIO']['TOTAL'];
                $iva_compensatorio = $evento['VALORES']['IVA_COMPENSATORIO']['TOTAL'];
                $total_iva_acum = $iva_moratorio + $iva_punitorio + $iva_compensatorio;
                $total_iva  = $total_iva_acum -$ant_iva;
                $ant_iva  += $total_iva;                

                $iva_punitorio_pagado = 0;
                $iva_moratorio_pagado = 0;
                $iva_compensatorio_pagado = 0;                
                if ($evento['T'] == 3){
                    
                    $iva_punitorio_pagado = $evento['PAGOS'][PAGO_IVA_PUNITORIO];
                    $iva_moratorio_pagado = $evento['PAGOS'][PAGO_IVA_MORATORIO];
                    $iva_compensatorio_pagado = $evento['PAGOS'][PAGO_IVA_COMPENSATORIO];                
                }
                
                $total_pagado = $iva_punitorio_pagado + $iva_moratorio_pagado + $iva_compensatorio_pagado;
/*                $iva_punitorio_pagado = $evento['VALORES']['IVA_PUNITORIO']['PAGOS'];
                $iva_moratorio_pagado = $evento['VALORES']['IVA_MORATORIO']['PAGOS'];
                $iva_compensatorio_pagado = $evento['VALORES']['IVA_COMPENSATORIO']['PAGOS'];
                $total_pagado_acum = $iva_moratorio_pagado + $iva_punitorio_pagado + $iva_compensatorio_pagado;
                
                $total_pagado = $total_pagado_acum - $ant_pagado_iva;
                echo $total_pagado."-";
                $ant_pagado_iva = $total_pagado ;
                */
                
                
                $total_total_col1 = $total_iva_acum;
                $total_total_col3 += $total_pagado;                

                ?>      
                <div class="t1">
                    <div class="full">
                        <span class="col"><?= number_format($total_iva, 2)?></span>
                        <span class="col"></span>
                        <span class="col"><?= $evento['T'] == 3 ? number_format($total_pagado, 2,",",".") : "" ?></span>
                    </div>
                </div>
            <?php } ?>
            <div class="t1">
                <div class="full total">
                    <span class="col "><?=  number_format($total_total_col1,2,",",".")?></span>
                    <span class="col ">0,00</span>
                    <span class="col "><?=  number_format($total_total_col3,2,",",".")?></span>
                </div>
            </div>
        </div>

        <!-- TOTAL-->
        <div class="col-titulos">
            <div class="t2">
                <div class="full tit">TOTAL</div>
                <div class="full subtit">
                    <span class="col">Deveng.</span>
                    <span class="col">Subs.</span>
                    <span class="col">Pago.</span>
                </div>
            </div>

            <?php
            $total_total_col1 = $total_total_col2 = $total_total_col3 = 0;
            $total_pagado_anterior = 0;
            foreach ($eventos as $evento) {
                
                $iva_punitorio = $evento['VALORES']['IVA_PUNITORIO']['TOTAL'];
                $iva_moratorio = $evento['VALORES']['IVA_MORATORIO']['TOTAL'];
                $iva_compensatorio = $evento['VALORES']['IVA_COMPENSATORIO']['TOTAL'];
                $total_iva = $iva_moratorio + $iva_punitorio + $iva_compensatorio;

                $total_pagado = 0;
                $total_capital = 0;
                if ($evento['T']==3){
                    $iva_punitorio_pagado = $evento['PAGOS'][PAGO_IVA_PUNITORIO];
                    $iva_moratorio_pagado = $evento['PAGOS'][PAGO_IVA_MORATORIO];
                    $iva_compensatorio_pagado = $evento['PAGOS'][PAGO_IVA_COMPENSATORIO];

                    $pago_punitorio = $evento['PAGOS'][PAGO_PUNITORIO];
                    $pago_compensatorio = $evento['PAGOS'][PAGO_COMPENSATORIO];
                    $pago_moratorio = $evento['PAGOS'][PAGO_MORATORIO];
                    $pago_capital = $evento['PAGOS'][PAGO_CAPITAL];

                    $total_pagado_iva = $iva_moratorio_pagado + $iva_punitorio_pagado + $iva_compensatorio_pagado;
                    $total_pagado = $total_pagado_iva + $pago_punitorio + $pago_compensatorio + $pago_moratorio + $pago_capital;
                    
                }
                else{
                    
                }
                
                if ($evento['FECHA'] >= $evento['VALORES']['_INFO']['HASTA']){
                    $total_capital = $evento['VALORES']['CAPITAL']['TOTAL'];
                }
                
                
                $total_compensatorio = $evento['VALORES']['COMPENSATORIO']['TOTAL'];
                $total_moratorio = $evento['VALORES']['MORATORIO']['TOTAL'];
                $total_punitorio = $evento['VALORES']['PUNITORIO']['TOTAL'];
                
                $total_acumulado = $total_iva + $total_capital + $total_compensatorio + $total_moratorio + $total_punitorio;
                
                $total = $total_acumulado -  $total_pagado_anterior;
                $total_pagado_anterior += $total ;
                
                $total_total_col1 += $total;
                $total_total_col3 += $total_pagado;
                
                ?>      
                <div class="t1 ">
                    <div class="full">
                        <span class="col"><?= $evento['T'] == 3 || true ? number_format($total, 2,",",".") : "" ?></span>
                        <span class="col"></span>
                        <span class="col"><?= $evento['T'] == 3 || true ? number_format($total_pagado, 2,",",".") : "" ?></span>
                    </div>
                </div>
            <?php } ?>
            <div class="t1">
                <div class="full total">
                    <span class="col "><?=  number_format($total_total_col1,2,",",".")?></span>
                    <span class="col ">0,00</span>
                    <span class="col "><?=  number_format($total_total_col3,2,",",".")?></span>
                </div>
            </div>
        </div>
    </div>
</div>