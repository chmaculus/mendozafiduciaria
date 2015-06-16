<ul class="lista-informe cuotas">
    <li class="titulo">
        <span class="numero-desembolso">NUMERO</span>
        <span class="fecha-desembolso">VENCIMIENTO</span>
        <span class="monto-desembolso">ESTADO</span>
        <span class="porcentaje-desembolso">SALDO CUOTA</span>
    </li>    
<?php 
//variables dentro de cuotas
$total_cuota_capital = 0;
$total_cuota_compensatorio = 0;
$total_cuota_moratorio = 0;
$total_cuota_punitorio = 0;
$total_cuota_iva = 0;
$total_cuota_iva_subsidio = 0;

$pagado_cuota_capital = 0;
$pagado_cuota_compensatorio = 0;
$pagado_cuota_moratorio = 0;
$pagado_cuota_punitorio = 0;
$pagado_cuota_iva = 0;

$saldo_cuota_capital = 0;
$saldo_cuota_compensatorio = 0;
$saldo_cuota_moratorio = 0;
$saldo_cuota_punitorio = 0;
$saldo_cuota_iva = 0;

//variables acumuladores totales
$total_cuota_total = 0;
$total_cuota_saldo = 0;
$total_cuota_pagado = 0;
$total_cuota_subsidio = 0;
$total_cuota_iva_subsidiado = 0;
 


$total_cuota_gastos = 0;
//print_array($cuotas);
foreach($cuotas as $cuota){
    $bpunitorio = isset($cuota['PUNITORIO']['TOTAL']) ; 
    $bmoratorio= isset($cuota['MORATORIO']['TOTAL']) ; 
    
    $iva_moratorio_total = isset($cuota['IVA_MORATORIO']['TOTAL']) ? $cuota['IVA_MORATORIO']['TOTAL'] : 0 ; 
    $iva_punitorio_total = isset($cuota['IVA_PUNITORIO']['TOTAL']) ? $cuota['IVA_PUNITORIO']['TOTAL'] : 0 ; 
    $iva_compensatorio_total = isset($cuota['IVA_COMPENSATORIO']['TOTAL']) ? $cuota['IVA_COMPENSATORIO']['TOTAL'] : 0 ; 
    
    $iva_moratorio_saldo = isset($cuota['IVA_MORATORIO']['SALDO']) ? $cuota['IVA_MORATORIO']['SALDO'] : 0 ; 
    $iva_punitorio_saldo = isset($cuota['IVA_PUNITORIO']['SALDO']) ? $cuota['IVA_PUNITORIO']['SALDO'] : 0 ;     
    $iva_compensatorio_saldo = isset($cuota['IVA_COMPENSATORIO']['SALDO']) ? $cuota['IVA_COMPENSATORIO']['SALDO'] : 0 ;     
    
    $iva_moratorio_pagado = $cuota['IVA_MORATORIO']['PAGOS']; 
    //echo "<br/>IVAMOR t/s:".$iva_moratorio_total ."/". $iva_punitorio_saldo  ."-";
    $iva_punitorio_pagado = $iva_punitorio_total - $iva_punitorio_saldo;
    $iva_compensatorio_pagado = $cuota['IVA_COMPENSATORIO']['PAGOS'];//$iva_compensatorio_total - $iva_compensatorio_saldo ;
    
    $iva_subsidiado = $cuota['_INFO']['IVA_COMPENSATORIO_SUBSIDIO']['TOTAL'];
    $compensatorio_subsidiado = $cuota['_INFO']['COMPENSATORIO_SUBSIDIO']['TOTAL'];
    
    $iva_pagado = $iva_moratorio_pagado + $iva_punitorio_pagado + $iva_compensatorio_pagado ;
    //echo "<br/>IVA m/p/c:".$iva_moratorio_pagado ."/". $iva_punitorio_pagado ."/". $iva_compensatorio_pagado ."-";
    $iva_total = $iva_moratorio_total + $iva_punitorio_total + $iva_compensatorio_total ;
    $iva_saldo = $iva_moratorio_saldo + $iva_punitorio_saldo + $iva_compensatorio_saldo;
    
    $total_moratorio = 0;
    $pagado_moratorio = 0;
    $saldo_moratorio = 0;                    

    $total_punitorio = 0;
    $pagado_punitorio = 0;
    $saldo_punitorio = 0;                    

    $total_compensatorio = $cuota['COMPENSATORIO']['TOTAL'];
    $pagado_compenstorio = $cuota['COMPENSATORIO']['PAGOS'];//($cuota['COMPENSATORIO']['TOTAL'] - $cuota['COMPENSATORIO']['SALDO'] );
    $saldo_compensatorio = $cuota['COMPENSATORIO']['SALDO'];                    
     
    $total_capital = $cuota['CAPITAL']['TOTAL'];
    $pagado_capital = $cuota['CAPITAL']['TOTAL'] - $cuota['CAPITAL']['SALDO'];
    $saldo_capital = $total_capital - $pagado_capital;//$cuota['CAPITAL']['SALDO'];                    
    $total_iva_subsidio = $iva_subsidiado;                   

    
    $total_cuota_capital += $total_capital;
    $total_cuota_compensatorio += $total_compensatorio;
    $total_cuota_moratorio += $total_moratorio;
    $total_cuota_punitorio += $total_punitorio;
    $total_cuota_iva += $iva_total;
    $total_cuota_iva_subsidio += $iva_subsidiado;;
    
    $pagado_cuota_capital += $pagado_capital;
    $pagado_cuota_compensatorio += $pagado_compenstorio;
    $pagado_cuota_moratorio += $pagado_moratorio;
    $pagado_cuota_punitorio += $pagado_punitorio;
    $pagado_cuota_iva += $iva_pagado;

    $saldo_cuota_capital += $saldo_capital;
    $saldo_cuota_compensatorio += $saldo_compensatorio;
    $saldo_cuota_moratorio += $saldo_moratorio;
    $saldo_cuota_punitorio += $saldo_punitorio;
    $saldo_cuota_iva += $iva_saldo;    
    
    
    $total_cuota_gastos = 0; 
    
    $total_a_pagar = number_format(abs(round($cuota['_INFO']['TOTAL_PAGAR'],2)),1);
    
    $estado = "Pendiente";
    $class = "";
    
    if ($cuota['_INFO']['HASTA'] < $fecha_actual){
        if ($total_a_pagar > 0){
            $estado = "Mora";
            $class = "mora";
        }
        else{
            $estado = "Cancelada";
            $class = "cancelada";
        }
    }
    ?>
    <li class="datos <?=$class?>" data-id="<?=$cuota['ID']?>" >
        <span class="numero-desembolso"><?=$cuota['_INFO']['NUM']?></span>
        <span class="fecha-desembolso"><?=date("d/m/Y",$cuota['_INFO']['HASTA'])?></span>
        <span class="monto-desembolso"><?=$estado?></span>
        <span class="porcentaje-desembolso">$<?=$total_a_pagar ?></span>
        <span class="opcion ampliar">( + )</span>
        <span class="opcion evolucion" >( Evolucion + )</span>
        <div class="especificaciones">
            <ul>
                <li>
                    <span class="titulo-estado titulo-esp resumido">Concepto</span>
                    <span class="titulo-estado resumido">Devengado</span>
                    <span class="titulo-estado resumido">Subsidio</span>
                    <span class="titulo-estado resumido">Pagado</span>
                    <span class="titulo-estado ">Saldo</span>
                </li>
                <?php if ($bmoratorio){
                    $total_moratorio = $cuota['MORATORIO']['TOTAL'];
                    $pagado_moratorio = $cuota['MORATORIO']['TOTAL'] - $cuota['MORATORIO']['SALDO'];
                    $saldo_moratorio = $cuota['MORATORIO']['SALDO'];                    
                    ?>
                <li>
                    <span class="titulo-esp ">Interes Moratorio</span>
                    <span class="dato-esp"><?=number_format($total_moratorio,2,",",".")?></span>
                    <span class="dato-esp res">0</span>
                    <span class="dato-esp"><?=number_format($pagado_moratorio,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format(abs(round($saldo_moratorio,2)),2,",",".")?></span>
                </li>
                <?php }?>
                <?php if ($bpunitorio){
                    $total_punitorio = $cuota['PUNITORIO']['TOTAL'];
                    $pagado_punitorio = $cuota['PUNITORIO']['TOTAL'] - $cuota['PUNITORIO']['SALDO'];
                    $saldo_punitorio = $cuota['PUNITORIO']['SALDO'];
                    ?>
                <li>
                    <span class="titulo-esp ">Interes Punitorio</span>
                    <span class="dato-esp"><?=number_format($total_punitorio,2,",",".")?></span>
                    <span class="dato-esp res">0</span>
                    <span class="dato-esp"><?=number_format($pagado_punitorio,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format(abs(round($saldo_punitorio,2)),2,",",".")?></span>
                </li>
                <?php }?>
                <li>
                    <span class="titulo-esp ">Interes Compensatorio</span>
                    <span class="dato-esp"><?=number_format($total_compensatorio,2,",",".")?></span>
                    <span class="dato-esp res"><?=number_format($cuota['_INFO']['COMPENSATORIO_SUBSIDIO']['TOTAL'],2,",",".")?></span>
                    <span class="dato-esp"><?=number_format($pagado_compenstorio,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format(abs(round($saldo_compensatorio,2)),2,",",".") ?></span>
                </li>
                <li>
                    <span class="titulo-esp ">Capital</span>
                    <span class="dato-esp"><?=number_format($total_capital,2,",",".")?></span>
                    <span class="dato-esp res">0</span>
                    <span class="dato-esp"><?=number_format($pagado_capital,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format(abs(round($saldo_capital,2)),2,",",".")?></span>
                </li>
                <li>
                    <span class="titulo-esp ">IVA</span>
                    <span class="dato-esp"><?=number_format($iva_total,2,",",".")?></span>
                    <span class="dato-esp res"><?=number_format($iva_subsidiado,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format($iva_pagado,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format(abs(round($iva_saldo,2)),2,",",".")?></span>
                </li>
                
                <?php
                $total_total = $total_moratorio + $total_punitorio + $total_compensatorio + $total_capital + $iva_total;
                $total_saldo = $saldo_moratorio + $saldo_punitorio + $saldo_compensatorio + $saldo_capital + $iva_saldo;
                $total_pagado = $pagado_moratorio + $pagado_punitorio + $pagado_compenstorio + $pagado_capital + $iva_pagado;
                $total_subsidio =$cuota['_INFO']['COMPENSATORIO_SUBSIDIO']['TOTAL'] + $iva_subsidiado;
                
                $total_cuota_total += $total_total;
                $total_cuota_saldo += $total_saldo;
                $total_cuota_pagado += $total_pagado;
                $total_cuota_subsidio += $total_subsidio;
                $total_cuota_iva_subsidio += $total_iva_subsidio;
                
                ?>
                
                <li class="totales-cuota">
                    <span class="titulo-esp ">TOTALES CUOTA</span>
                    <span class="dato-esp total"><?=number_format($total_total,2,",",".")  ?></span>
                    <span class="dato-esp total res"><?=number_format($total_subsidio,2,",",".")  ?></span>
                    <span class="dato-esp total"><?=number_format($total_pagado,2,",",".")  ?></span>
                    <span class="dato-esp total"><?=number_format(abs(round($total_saldo,2)),2,",",".")  ?></span>
                </li>
            </ul>
            
        </div>
        <div class="evolucion">
            
        </div>
    </li>
<?php } 

?>
    <li class="datos totales">
        <span class="porcentaje-desembolso">SALDO TOTAL</span>
        <span class="porcentaje-desembolso">$<?= number_format($total_cuota_saldo,2)?></span>
        <span class="opcion ampliar">( + )</span>
        <div class="especificaciones">
            <ul>
                <li>
                    <span class="titulo-estado titulo-esp resumido">Concepto</span>
                    <span class="titulo-estado resumido">Devengado</span>
                    <span class="titulo-estado resumido">Subsidio</span>
                    <span class="titulo-estado resumido">Pagado</span>
                    <span class="titulo-estado ">Saldo</span>
                </li>
                <li>
                    <span class="titulo-esp ">Interes Moratorio</span>
                    <span class="dato-esp"><?=number_format($total_cuota_moratorio,2,",",".")?></span>
                    <span class="dato-esp res">0</span>
                    <span class="dato-esp"><?=number_format($pagado_cuota_moratorio,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format($saldo_cuota_moratorio,2,",",".")?></span>
                </li>
                <li>
                    <span class="titulo-esp ">Interes Punitorio</span>
                    <span class="dato-esp"><?=number_format($total_cuota_punitorio,2,",",".")?></span>
                    <span class="dato-esp res">0</span>
                    <span class="dato-esp"><?=number_format($pagado_cuota_punitorio,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format($saldo_cuota_punitorio,2,",",".")?></span>
                </li>
                <li>
                    <span class="titulo-esp ">Interes Compensatorio</span>
                    <span class="dato-esp"><?=number_format($total_cuota_compensatorio,2,",",".")?></span>
                    <span class="dato-esp res"><?=number_format($total_cuota_subsidio,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format($pagado_cuota_compensatorio,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format($saldo_cuota_compensatorio,2,",",".") ?></span>
                </li>
                <li>
                    <span class="titulo-esp ">Capital</span>
                    <span class="dato-esp"><?=number_format($total_cuota_capital,2,",",".")?></span>
                    <span class="dato-esp res">0</span>
                    <span class="dato-esp"><?=number_format($pagado_cuota_capital,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format($saldo_cuota_capital,2,",",".")?></span>
                </li>
                <li>
                    <span class="titulo-esp ">IVA</span>
                    <span class="dato-esp"><?=number_format($total_cuota_iva,2,",",".")?></span>
                    <span class="dato-esp res"><?=number_format($total_cuota_iva_subsidio,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format($pagado_cuota_iva,2,",",".")?></span>
                    <span class="dato-esp"><?=number_format($saldo_cuota_iva,2,",",".")?></span>
                </li>
                
                
                <li class="totales-cuota">
                    <span class="titulo-esp ">TOTALES GENERALES</span>
                    <span class="dato-esp total"><?=number_format($total_cuota_total,2,",",".")  ?></span>
                    <span class="dato-esp total res"><?=number_format($total_cuota_subsidio,2,",",".")  ?></span>
                    <span class="dato-esp total"><?=number_format($total_cuota_pagado,2,",",".")  ?></span>
                    <span class="dato-esp total"><?=number_format($total_cuota_saldo,2,",",".")  ?></span>
                </li>
            </ul>
            
        </div>
    </li>
</ul>
