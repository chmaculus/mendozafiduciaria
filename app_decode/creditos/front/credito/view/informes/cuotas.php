<style>
    table tr{text-align:left}td.iva{width:65px}
    p.dscr{font-weight:bold;font-size:1.2em;padding:10px 10px 20px;}
</style>
<?php
if (!$desembolsos) {
    echo '<p class="dscr">No hay desembolsos cargados</p>';
    die();
} else {
    $desembolsado = 0;
    foreach($desembolsos as $desembolso){
        $desembolsado += $desembolso['MONTO'];
    }
    
    if ($monto_credito != $desembolsado) {
        echo '<p class="dscr">No están todos los desembolsos cargados</p>';
    }
}
?>
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
$total_cuota_gastos = 0;
$total_cuota_iva_compensatorio = 0;
$total_cuota_iva_moratorio = 0;
$total_cuota_iva_punitorio = 0;

$pagado_cuota_capital = 0;
$pagado_cuota_compensatorio = 0;
$pagado_cuota_moratorio = 0;
$pagado_cuota_punitorio = 0;
$pagado_cuota_iva = 0;
$pagado_cuota_iva_compensatorio = 0;
$pagado_cuota_iva_moratorio = 0;
$pagado_cuota_iva_punitorio = 0;

$saldo_cuota_capital = 0;
$saldo_cuota_compensatorio = 0;
$saldo_cuota_moratorio = 0;
$saldo_cuota_punitorio = 0;
$saldo_cuota_iva = 0;
$saldo_cuota_iva_compensatorio = 0;
$saldo_cuota_iva_moratorio = 0;
$saldo_cuota_iva_punitorio = 0;
$saldo_a_favor = 0;

//variables acumuladores totales
$total_cuota_total = 0;
$total_cuota_saldo = 0;
$total_cuota_pagado = 0;
$total_cuota_subsidio = 0;
$total_cuota_iva_subsidiado = 0;
 
$cuotas_pendientes = 0;

$total_cuota_gastos = 0;
//print_array($cuotas);
foreach($cuotas as $kk=>$cuota){
    $bpunitorio = isset($cuota['PUNITORIO']['TOTAL']) ;
    $bmoratorio= isset($cuota['MORATORIO']['TOTAL']) ;
    
    $iva_moratorio_total = isset($cuota['IVA_MORATORIO']['TOTAL']) ? $cuota['IVA_MORATORIO']['TOTAL'] : 0 ;
    $iva_punitorio_total = isset($cuota['IVA_PUNITORIO']['TOTAL']) ? $cuota['IVA_PUNITORIO']['TOTAL'] : 0 ;
    $iva_compensatorio_total = isset($cuota['IVA_COMPENSATORIO']['TOTAL']) ? $cuota['IVA_COMPENSATORIO']['TOTAL'] : 0 ;
    
    $iva_moratorio_saldo = isset($cuota['IVA_MORATORIO']['TOTAL']) ? $cuota['IVA_MORATORIO']['SALDO'] : 0 ;
    $iva_punitorio_saldo = isset($cuota['IVA_PUNITORIO']['TOTAL']) ? $cuota['IVA_PUNITORIO']['SALDO'] : 0 ;
    $iva_compensatorio_saldo = isset($cuota['IVA_COMPENSATORIO']['TOTAL']) ? $cuota['IVA_COMPENSATORIO']['SALDO'] : 0 ;
    
    $iva_moratorio_pagado = $iva_moratorio_total - $iva_moratorio_saldo;
    //echo "<br/>IVAMOR t/s:".$iva_moratorio_total ."/". $iva_punitorio_saldo  ."-";
    $iva_punitorio_pagado = $iva_punitorio_total - $iva_punitorio_saldo;
    $iva_compensatorio_pagado = $cuota['IVA_COMPENSATORIO']['PAGOS'];//$iva_compensatorio_total - $iva_compensatorio_saldo ;
    
    $iva_subsidiado = $cuota['_INFO']['IVA_COMPENSATORIO_SUBSIDIO']['TOTAL'];
    $compensatorio_subsidiado = $cuota['_INFO']['COMPENSATORIO_SUBSIDIO']['TOTAL'];
    
    $total_gastos = $cuota['GASTOS_VARIOS']['TOTAL'];
    $pagado_gastos = $cuota['GASTOS_VARIOS']['PAGOS'];
    $saldo_gastos = $cuota['GASTOS_VARIOS']['SALDO'];
    
    $iva_gastos_total = $cuota['IVA_GASTOS']['TOTAL'];
    $iva_gastos_pagado = $cuota['IVA_GASTOS']['PAGOS'];
    //if(!isset($cuota['IVA_GASTOS']['SALDO'])) die("aca");
    $iva_gastos_saldo = $cuota['IVA_GASTOS']['SALDO'];
    
    $iva_pagado = $iva_moratorio_pagado + $iva_punitorio_pagado + $iva_compensatorio_pagado + $iva_gastos_pagado;
    //echo "<br/>IVA m/p/c:".$iva_moratorio_pagado ."/". $iva_punitorio_pagado ."/". $iva_compensatorio_pagado ."-";
    $iva_total = $iva_moratorio_total + $iva_punitorio_total + $iva_compensatorio_total + $iva_gastos_total;
    $iva_saldo = $iva_moratorio_saldo + $iva_punitorio_saldo + $iva_compensatorio_saldo + $iva_gastos_saldo;
    
    $total_moratorio = 0;
    $pagado_moratorio = 0;
    $saldo_moratorio = 0;

    $total_punitorio = 0;
    $pagado_punitorio = 0;
    $saldo_punitorio = 0;

    $total_compensatorio = $cuota['COMPENSATORIO']['TOTAL'] - $cuota['COMPENSATORIO_ACT'];
    $pagado_compenstorio = $cuota['COMPENSATORIO']['PAGOS'];//($cuota['COMPENSATORIO']['TOTAL'] - $cuota['COMPENSATORIO']['SALDO'] );
    $saldo_compensatorio = $cuota['COMPENSATORIO']['SALDO'];
    $total_compensatorio_act = $cuota['COMPENSATORIO_ACT'] ? '<br />' . number_format($cuota['COMPENSATORIO_ACT'],2,",",".") : '';
    $iva_compensatorio_act_total = '';
    if ($cuota['COMPENSATORIO_ACT'] && $cuota['COMPENSATORIO']['TOTAL']) {
        $tmp_iva = $iva_compensatorio_total * $cuota['COMPENSATORIO_ACT'] / $cuota['COMPENSATORIO']['TOTAL'];
        $iva_compensatorio_total -= $tmp_iva;
        $tmp_iva = number_format($tmp_iva,2,",",".");
        $iva_compensatorio_act_total = '<br />' . $tmp_iva;
    }
            
    $total_capital = $cuota['CAPITAL']['TOTAL'];
    $pagado_capital = $cuota['CAPITAL']['TOTAL'] - $cuota['CAPITAL']['SALDO'];
    $saldo_capital = $total_capital - $pagado_capital;//$cuota['CAPITAL']['SALDO'];
    $total_iva_subsidio = $iva_subsidiado;

    
    $total_cuota_gastos = 0;
    
    $total_a_pagar = abs(round($cuota['_INFO']['TOTAL_PAGAR'],2));
    $total_a_pagar = ($total_a_pagar > 0.01) ? $total_a_pagar : 0;
    $total_a_pagar = number_format($total_a_pagar,2);
    
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
    
    if ($bmoratorio){
        $total_moratorio = $cuota['MORATORIO']['TOTAL'];
        $pagado_moratorio = $cuota['MORATORIO']['TOTAL'] - $cuota['MORATORIO']['SALDO'];
        $saldo_moratorio = $cuota['MORATORIO']['SALDO'];
    }
    
    if ($bpunitorio){
        $total_punitorio = $cuota['PUNITORIO']['TOTAL'];
        $pagado_punitorio = $cuota['PUNITORIO']['TOTAL'] - $cuota['PUNITORIO']['SALDO'];
        $saldo_punitorio = $cuota['PUNITORIO']['SALDO'];
    }
    
    $pagado_cuota_capital += $pagado_capital;
    $pagado_cuota_compensatorio += $pagado_compenstorio;
    $pagado_cuota_moratorio += $pagado_moratorio;
    $pagado_cuota_punitorio += $pagado_punitorio;
    $pagado_cuota_iva += $iva_pagado;
    $pagado_cuota_iva_compensatorio += $iva_compensatorio_pagado;
    $pagado_cuota_iva_moratorio += $iva_moratorio_pagado;
    $pagado_cuota_iva_punitorio += $iva_punitorio_pagado;

    $saldo_cuota_capital += $saldo_capital;
    $saldo_cuota_compensatorio += $saldo_compensatorio;
    $saldo_cuota_moratorio += $saldo_moratorio;
    $saldo_cuota_punitorio += $saldo_punitorio;
    $saldo_cuota_iva += $iva_saldo > 0 ? $iva_saldo : 0;
    $saldo_cuota_iva_compensatorio += $iva_compensatorio_saldo;
    $saldo_cuota_iva_moratorio += $iva_moratorio_saldo;
    $saldo_cuota_iva_punitorio += $iva_punitorio_saldo;
    $saldo_a_favor += $cuota['ADELANTO'];
    
    
    $total_total = $total_moratorio + $total_punitorio + $total_compensatorio + $total_capital + $total_gastos + $cuota['COMPENSATORIO_ACT'];
    $total_saldo = $saldo_moratorio + $saldo_punitorio + $saldo_compensatorio + $saldo_capital + $saldo_gastos;
    $total_saldo = $total_saldo > 0.5 ? $total_saldo : 0;
    $total_saldo -= $saldo_a_favor;
    $total_pagado = $pagado_moratorio + $pagado_punitorio + $pagado_compenstorio + $pagado_capital + $pagado_gastos;
    $total_subsidio =$cuota['_INFO']['COMPENSATORIO_SUBSIDIO']['TOTAL'] + $iva_subsidiado;
        
    $total_cuota_capital += $total_capital;
    $total_cuota_compensatorio += $total_compensatorio;
    $total_cuota_moratorio += $total_moratorio;
    $total_cuota_punitorio += $total_punitorio;
    $total_cuota_iva += $iva_total;
    $total_cuota_iva_subsidio += $iva_subsidiado;;
    $total_cuota_iva_compensatorio += $iva_compensatorio_total;
    $total_cuota_iva_moratorio += $iva_moratorio_total;
    $total_cuota_iva_punitorio += $iva_punitorio_total;

    $total_cuota_total += $total_total;
    $total_cuota_saldo += $total_saldo;
    $total_cuota_pagado += $total_pagado + $saldo_a_favor;
    $total_cuota_subsidio += $total_subsidio;
    $total_cuota_iva_subsidio += $total_iva_subsidio;
    
             
    if ($total_total > 0 && $total_saldo < 0.05 && $cuotas_pendientes == 0 ) {
        $estado = "Cancelada";
        $class = "cancelada";
    } else {
        ++$cuotas_pendientes;
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
            <table width="100%">
                <tr>
                    <th class="titulo-estado titulo-esp resumido">Concepto</th>
                    <th class="titulo-estado resumido">Devengado</th>
                    <th class="titulo-estado resumido">IVA</th>
                    <th class="titulo-estado resumido">Subsidio</th>
                    <th class="titulo-estado resumido">IVA</th>
                    <th class="titulo-estado resumido">Pagado</th>
                    <th class="titulo-estado resumido">IVA</th>
                    <th class="titulo-estado">Saldo</th>
                    <th class="titulo-estado resumido">IVA</th>
                </tr>
                <?php if ($bmoratorio){        
                    ?>
                <tr>
                    <td class="titulo-esp ">Interes Moratorio</td>
                    <td class="dato-esp"><?=number_format($total_moratorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($iva_moratorio_total,2,",",".")?></td>
                    <td class="dato-esp res"></td>
                    <td class="dato-esp iva"></td>
                    <td class="dato-esp"><?=number_format($pagado_moratorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($iva_moratorio_pagado,2,",",".")?></td>
                    <td class="dato-esp"><?=number_format(abs(round($saldo_moratorio,2)),2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format(abs($iva_moratorio_saldo),2,",",".")?></td>
                </tr>
                <?php }?>
                <?php if ($bpunitorio){
                    ?>
                <tr>
                    <td class="titulo-esp ">Interes Punitorio</td>
                    <td class="dato-esp"><?=number_format($total_punitorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($iva_punitorio_total,2,",",".")?></td>
                    <td class="dato-esp res"></td>
                    <td class="dato-esp iva"></td>
                    <td class="dato-esp"><?=number_format($pagado_punitorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($iva_punitorio_pagado,2,",",".")?></td>
                    <td class="dato-esp"><?=number_format(abs(round($saldo_punitorio,2)),2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format(abs($iva_punitorio_saldo),2,",",".")?></td>
                </li>
                <?php }?>
                <tr>
                    <td class="titulo-esp ">Interes Compensatorio</td>
                    <td class="dato-esp"><?=number_format($total_compensatorio,2,",",".").$total_compensatorio_act?></td>
                    <td class="dato-esp iva"><?=number_format($iva_compensatorio_total,2,",",".").$iva_compensatorio_act_total?></td>
                    <td class="dato-esp res"><?=number_format($cuota['_INFO']['COMPENSATORIO_SUBSIDIO']['TOTAL'],2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($iva_subsidiado,2,",",".")?></td>
                    <td class="dato-esp"><?=number_format($pagado_compenstorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($iva_compensatorio_pagado,2,",",".")?></td>
                    <td class="dato-esp"><?=number_format(abs(round($saldo_compensatorio,2)),2,",",".") ?></td>
                    <td class="dato-esp iva"><?=number_format(abs($iva_compensatorio_saldo),2,",",".")?></td>
                </tr>
                <tr>
                    <td class="titulo-esp ">Capital</td>
                    <td class="dato-esp"><?=number_format($total_capital,2,",",".")?></td>
                    <td class="dato-esp iva"></td>
                    <td class="dato-esp res"></td>
                    <td class="dato-esp iva"></td>
                    <td class="dato-esp"><?=number_format($pagado_capital,2,",",".")?></td>
                    <td class="dato-esp iva"></td>
                    <td class="dato-esp"><?=number_format(abs(round($saldo_capital,2)),2,",",".")?></td>
                    <td class="dato-esp iva"></td>
                </tr>
                <tr>
                    <td class="titulo-esp ">Gastos Varios</td>
                    <td class="dato-esp"><?=number_format($total_gastos,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format(abs(round($iva_gastos_total,2)),2,",",".")?></td>
                    <td class="dato-esp res"></td>
                    <td class="dato-esp iva"></td>
                    <td class="dato-esp"><?=number_format($pagado_gastos,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format(abs(round($iva_gastos_pagado,2)),2,",",".")?></td>
                    <td class="dato-esp"><?=number_format(abs(round($saldo_capital,2)),2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format(abs(round($saldo_gastos,2)),2,",",".") ?></td>
                </tr>
                <tr class="totales-cuota">
                    <td class="titulo-esp ">TOTALES CUOTA</td>
                    <td class="dato-esp total"><?=number_format($total_total,2,",",".")  ?></td>
                    <td class="dato-esp total iva"><?=number_format($iva_total,2,",",".")  ?></td>
                    <td class="dato-esp total res"><?=number_format($total_subsidio,2,",",".")  ?></td>
                    <td class="dato-esp total iva"><?=number_format($iva_subsidiado,2,",",".")  ?></td>
                    <td class="dato-esp total"><?=number_format($total_pagado,2,",",".")  ?></td>
                    <td class="dato-esp total iva"><?=number_format($iva_pagado,2,",",".")  ?></td>
                    <td class="dato-esp total"><?=number_format(abs(round($total_saldo,2)),2,",",".")  ?></td>
                    <td class="dato-esp total iva"><?=number_format(abs(round($iva_saldo,2)),2,",",".")  ?></td>
                </tr>
                <tr class="totales-cuota-c">
                    <td class="titulo-esp ">TOTALES CUOTA</td>
                    <td class="dato-esp total" colspan="2"><?=number_format($total_total + $iva_total,2,",",".")  ?></td>
                    <td class="dato-esp total" colspan="2"><?=number_format($total_subsidio + $iva_subsidiado,2,",",".")  ?></td>
                    <td class="dato-esp total" colspan="2"><?=number_format($total_pagado + $iva_pagado,2,",",".")  ?></td>
                    <td class="dato-esp total" colspan="2"><?=number_format(str_replace(",", "", $total_a_pagar),2,",",".")  ?></td>
                </tr>
            </table>
            
        </div>
        <div class="evolucion">
            
        </div>
    </li>
<?php } 

?>
    <li class="datos totales">
        <span class="porcentaje-desembolso">SALDO TOTAL</span>
        <span class="porcentaje-desembolso">$<?= $saldo_total = number_format($total_cuota_saldo+$saldo_cuota_iva,2) . ($total_cuota_saldo+$saldo_cuota_iva < 0 ? '(a favor)' : '')?></span>
        <span class="opcion ampliar">( + )</span>
        <div class="especificaciones">
            <table width="100%">
                <tr>
                    <th class="titulo-estado titulo-esp resumido">Concepto</th>
                    <th class="titulo-estado resumido">Devengado</th>
                    <th class="titulo-estado resumido">IVA</th>
                    <th class="titulo-estado resumido">Subsidio</th>
                    <th class="titulo-estado resumido">IVA</th>
                    <th class="titulo-estado resumido">Pagado</th>
                    <th class="titulo-estado resumido">IVA</th>
                    <th class="titulo-estado ">Saldo</th>
                    <th class="titulo-estado ">IVA</th>
                </li>
                <tr>
                    <td class="titulo-esp ">Interes Moratorio</td>
                    <td class="dato-esp"><?=number_format($total_cuota_moratorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($total_cuota_iva_moratorio,2,",",".")?></td>
                    <td class="dato-esp res">0</td>
                    <td class="dato-esp iva"></td>
                    <td class="dato-esp"><?=number_format($pagado_cuota_moratorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($pagado_cuota_iva_moratorio,2,",",".")?></td>
                    <td class="dato-esp"><?=number_format($saldo_cuota_moratorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($saldo_cuota_iva_moratorio,2,",",".")?></td>
                </tr>
                <tr>
                    <td class="titulo-esp ">Interes Punitorio</td>
                    <td class="dato-esp"><?=number_format($total_cuota_punitorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($total_cuota_iva_punitorio,2,",",".")?></td>
                    <td class="dato-esp res">0</td>
                    <td class="dato-esp iva"></td>
                    <td class="dato-esp"><?=number_format($pagado_cuota_punitorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($pagado_cuota_iva_punitorio,2,",",".")?></td>
                    <td class="dato-esp"><?=number_format($saldo_cuota_punitorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($saldo_cuota_iva_punitorio,2,",",".")?></td>
                </tr>
                <tr>
                    <td class="titulo-esp ">Interes Compensatorio</td>
                    <td class="dato-esp"><?=number_format($total_cuota_compensatorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($total_cuota_iva_compensatorio,2,",",".")?></td>
                    <td class="dato-esp res"><?=number_format($total_cuota_subsidio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format(0,2,",",".")?></td>
                    <td class="dato-esp"><?=number_format($pagado_cuota_compensatorio,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format($pagado_cuota_iva_compensatorio,2,",",".")?></td>
                    <td class="dato-esp"><?=number_format($saldo_cuota_compensatorio,2,",",".") ?></td>
                    <td class="dato-esp iva"><?=number_format($saldo_cuota_iva_compensatorio,2,",",".") ?></td>
                </tr>
                <tr>
                    <td class="titulo-esp ">Capital</td>
                    <td class="dato-esp"><?=number_format($total_cuota_capital,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format(0,2,",",".")?></td>
                    <td class="dato-esp res">0</td>
                    <td class="dato-esp iva"></td>
                    <td class="dato-esp"><?=number_format($pagado_cuota_capital,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format(0,2,",",".")?></td>
                    <td class="dato-esp"><?=number_format($saldo_cuota_capital,2,",",".")?></td>
                    <td class="dato-esp iva"><?=number_format(0,2,",",".")?></td>
                </tr>
                
                <li class="totales-cuota">
                    <td class="titulo-esp ">TOTALES GENERALES</td>
                    <td class="dato-esp total"><?=number_format($total_cuota_total,2,",",".")  ?></td>
                    <td class="dato-esp total iva"><?=number_format($total_cuota_iva,2,",",".")  ?></td>
                    <td class="dato-esp total res"><?=number_format($total_cuota_subsidio,2,",",".")  ?></td>
                    <td class="dato-esp total iva"><?=number_format($total_cuota_iva_subsidio,2,",",".")  ?></td>
                    <td class="dato-esp total"><?=number_format($total_cuota_pagado,2,",",".")  ?></td>
                    <td class="dato-esp total iva"><?=number_format($pagado_cuota_iva,2,",",".")  ?></td>
                    <td class="dato-esp total"><?=number_format($total_cuota_saldo,2,",",".")  ?></td>
                    <td class="dato-esp total iva"><?=number_format($saldo_cuota_iva,2,",",".")  ?></td>
                </tr>
            </table>
            
        </div>
    </li>
</ul>
