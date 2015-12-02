<style>
    table,table tr,table tr td{border-collapse:collapse}
    table tr td{padding:2px 4px;}
    #wpopup{padding:0 15px}
</style>
<?php if($arr_reporte) { ?>
<div id="reporteCobranza">
<input type="button" onclick="exportReporteCobranza()" value="Exportar">
<table style="width:100%">
    <tr>
        <td>Fecha desde</td>
        <td><input type="text" value="<?=$fecha_desde ? date('Y-m-d', $fecha_desde) : '';?>" /></td>
    </tr>
    <tr>
        <td>Fecha hasta</td>
        <td><input type="text" value="<?=$fecha_hasta ? date('Y-m-d', $fecha_hasta) : '';?>" /></td>
    </tr>
    <tr>
        <td style="border:0.1pt solid #000;">Fideicomiso</td>
        <td style="border:0.1pt solid #000;"></td>
<?php foreach($arr_reporte as $k => $item) { ?>
        <td style="border:0.1pt solid #000;background-color:#ccc"><?=$item['NOMBRE'] ? $item['NOMBRE'] : "#" . $k ?></td>
<?php } ?>
    </tr>
    <tr>
        <td rowspan="2" style="border:0.1pt solid #000;">Monto a cobrar</td>
        <td style="border:0.1pt solid #000;">$</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=number_format($item['MONTO_A_COBRAR'], 2, ",", "")?></td>
<?php } ?>
    </tr>
    <tr>
        <td style="border:0.1pt solid #000;">#</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=$item['CANT_CREDITOS_A_COBRAR']?></td>
<?php } ?>
    </tr>
    <tr>
        <td rowspan="2" style="border:0.1pt solid #000;">Monto cobrado</td>
        <td style="border:0.1pt solid #000;">$</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=number_format($item['COBRADO'], 2, ",", "")?></td>
<?php } ?>
    </tr>
    <tr>
        <td style="border:0.1pt solid #000;">#</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=$item['CANT_CUOTAS_COBRADAS']?></td>
<?php } ?>
    </tr>
    <tr>
        <td rowspan="2" style="border:0.1pt solid #000;">Monto en mora</td>
        <td style="border:0.1pt solid #000;">$</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=number_format($item['MONTO_EN_MORA'], 2, ",", "")?></td>
<?php } ?>
    </tr>
    <tr>
        <td style="border:0.1pt solid #000;">#</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=$item['CUOTAS_EN_MORA']?></td>
<?php } ?>
    </tr>
    <tr>
        <td rowspan="2" style="border:0.1pt solid #000;">Eficiencia</td>
        <td style="border:0.1pt solid #000;">$</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=number_format($item['COBRADO'] * 100 / $item['MONTO_A_COBRAR'], 2, ",", "")?>%</td>
<?php } ?>
    </tr>
    <tr>
        <td style="border:0.1pt solid #000;">#</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=number_format($item['CANT_CREDITOS_A_COBRAR'] ? ($item['CANT_CUOTAS_COBRADAS'] * 100 / $item['CANT_CREDITOS_A_COBRAR']) : 0, 2, ",", "")?>%</td>
<?php } ?>
    </tr>
    <tr>
        <td style="border:0.1pt solid #000;">Total Otorgado</td>
        <td style="border:0.1pt solid #000;">$</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=number_format($item['TOTAL_OTORGADO'], 2, ",", "")?></td>
<?php } ?>
    </tr>
    <tr>
        <td style="border:0.1pt solid #000;">Total a Cobrar</td>
        <td style="border:0.1pt solid #000;">$</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=number_format($item['TOTAL_A_COBRAR'], 2, ",", "")?></td>
<?php } ?>
    </tr>
    <tr>
        <td style="border:0.1pt solid #000;">Casos</td>
        <td style="border:0.1pt solid #000;">#</td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;"><?=number_format($item['TOTAL_CASOS'], 2, ",", "")?></td>
<?php } ?>
    </tr>
</table>
</div>
<?php } else { ?>
    <p>No se encontraron créditos</p>
<?php
}
?>