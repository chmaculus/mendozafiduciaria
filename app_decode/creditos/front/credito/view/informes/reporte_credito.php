<style>
    #reporteCredito,#reporteCredito tr,#reporteCredito th,#reporteCredito td,#reporteCredito{border-collapse:collapse}
    #reporte-credito input{padding:5px;background-color:#4162a7;border-radius:8px;box-shadow:2px 2px 5px #222;font-weight:bold;}
</style>
<div id="reporteCredito">
    <?php if ($array_credito) { ?>
    <input type="button" onclick="exportReporte()" value="Exportar" />
    <br />
    <table style="width:100%;">
        <tr><td style="border:0 !important">&nbsp;</td></tr>
        <tr>
            <td style="border:0 !important"></td>
            <td colspan="3" style="border:0.1pt solid #000;background-color:#fece2f"><?=$info['post_str']?></td>
            <td style="border:0 !important"></td><td style="border:0 !important"></td><td style="border:0 !important"></td>
            <td style="border:0 !important"><?=date('d/m/Y', strtotime($info['INTERES_VTO']))?></td>
            <td style="border:0 !important"></td>
            <td style="border:0.1pt solid #000;">Tasa Moratoria:</td>
            <td style="border:0.1pt solid #000;"><?=str_replace(".", ",", $info['T_MORATORIO']) . "%"?></td>
        </tr>
        <tr>
            <td style="border:0 !important"></td>
            <td style="border:0 !important"></td><td style="border:0 !important"></td><td style="border:0 !important"></td><td style="border:0 !important"></td><td style="border:0 !important"></td><td style="border:0 !important"></td><td style="border:0 !important"></td><td style="border:0 !important"></td>
            <td style="border:0.1pt solid #000;">Tasa Punitoria:</td>
            <td style="border:0.1pt solid #000;"><?=str_replace(".", ",", $info['T_PUNITORIO']) . "%"?></td>
        </tr>
        <tr><td style="border:0 !important">&nbsp;</td></tr>
        <tr>
            <th style="border:0 !important"></th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Concepto</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Saldo</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Fecha</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Vencida</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Capital</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Int. Compens.</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">IVA</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">I.C.A.</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">I.C.A. IVA</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Cuota</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Pago Monto</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Pago Fecha</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Días Mora</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Int.Morat. y Pun.</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">IVA</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Saldo Mora</th>
            <th style="border:1pt solid #000;background-color:#ccc;text-align:center">Saldo Cancelación</th>
        </tr>
    <?php if($desembolsos) { foreach($desembolsos as $item) { ?>
        <tr>
            <td style="border:0 !important"></td>
            <td style="border-width:0.1pt 0.1pt 0.1pt 1pt;border-style:solid;border-color:#000"><?=$item['NUMERO'] . "º Desembolso";?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['MONTO'];?></td>
            <td style="border:0.1pt solid #000;"><?=$item['FECHA'];?></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border:0.1pt solid #000;"></td>
            <td style="border-width:0.1pt 1pt 0.1pt 0.1pt;border-style:solid;border-color:#000"></td>
        </tr>

    <?php } } ?>
    <?php foreach($array_credito as $item) { ?>
        <tr>
            <td style="border:0 !important"></td>
            <td style="border-width:0.1pt 0.1pt 0.1pt 1pt;border-style:solid;border-color:#000"><?=$item['CONCEPTO'];?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['SALDO'];?></td>
            <td style="border:0.1pt solid #000;"><?=$item['FECHA'];?></td>
            <td style="border:0.1pt solid #000;"><?=$item['VENCIDA'];?></td>
            <td style="border:0.1pt solid #000;"><?=$item['CAPITAL'];?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['INT_COMPENSATORIO'];?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['INT_COMPENSATORIO_IVA'];?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['COMPENSATORIO_ACT'];?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['COMPENSATORIO_ACT_IVA'];?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['CUOTA'];?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=isset($item['PAGO_MONTO']) ? $item['PAGO_MONTO'] : '';?></td>
            <td style="border:0.1pt solid #000;"><?=isset($item['PAGO_FECHA']) ? $item['PAGO_FECHA'] : '';?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['DIAS_MORAS'];?></td>
            <td style="border:0.1pt solid #000;"><?=$item['INT_MORA_PUN'];?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['INT_MORA_PUN_IVA'];?></td>
            <td style="border:0.1pt solid #000;text-align:right"><?=$item['SALDO_MORA'];?></td>
            <td style="border-width:0.1pt 1pt 0.1pt 0.1px;border-style:solid;border-color:#000;text-align:right"><?=$item['SALDO_CUOTA'];?></td>
        </tr>
    <?php } ?>
        <tr>
            <td style="border:0 !important"></td>
            <td style="border-width:1pt 0 1pt 1pt;border-style:solid;border-color:#000;background-color:#ddd">TOTALES</td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;background-color:#ddd"></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;background-color:#ddd"></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;background-color:#ddd"></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['TOTAL_CAPITAL'];?></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['INT_COMPENSATORIO'];?></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['INT_COMPENSATORIO_IVA'];?></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['COMPENSATORIO_ACT'];?></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['COMPENSATORIO_ACT_IVA'];?></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['CUOTA'];?></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['PAGO_MONTO'];?></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;background-color:#ddd"></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;background-color:#ddd"></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['INT_MORA_PUN'];?></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['INT_MORA_PUN_IVA'];?></td>
            <td style="border-width:1pt 0 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['SALDO_MORA'];?></td>
            <td style="border-width:1pt 1pt 1pt 0;border-style:solid;border-color:#000;text-align:right;background-color:#ddd"><?=$totales_credito['SALDO_CUOTA'];?></td>
        </tr>
    </table>
    <br />
    <input type="button" onclick="exportReporte()" value="Exportar" />
    <?php } ?>
</div>