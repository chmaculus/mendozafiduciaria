<?php if($arr_reporte) { ?>
<style>
    table,table tr,table tr td{border-collapse:collapse}
    table tr td{padding:2px 4px;}
    #wpopup{padding:0 15px}
</style>
<div id="reporteCobranza">
<input type="button" onclick="exportReporteCobranza()" value="Exportar">
<table style="width:100%">
    <tr>
        <td>Fecha desde</td>
        <td><input type="text" value="<?=$fecha_desde ? date('Y-m-d', strtotime($fecha_desde)) : '';?>" /></td>
    </tr>
    <tr>
        <td>Fecha hasta</td>
        <td><input type="text" value="<?=$fecha_hasta ? date('Y-m-d', strtotime($fecha_hasta)) : '';?>" /></td>
    </tr>
    <tr>
        <td style="border:0.1pt solid #000;">Fideicomiso</td>
        <td style="border:0.1pt solid #000;"></td>
<?php foreach($arr_reporte as $item) { ?>
        <td style="border:0.1pt solid #000;background-color:#ccc"><?=$item['NOMBRE']?></td>
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
        <td style="border:0.1pt solid #000;"><?=$item['CANT_CUOTAS']?></td>
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
        <td style="border:0.1pt solid #000;"><?=number_format($item['CANT_CUOTAS_COBRADAS'] * 100 / $item['CANT_CUOTAS'], 2, ",", "")?>%</td>
<?php } ?>
    </tr>
</table>
</div>
<?php } ?>