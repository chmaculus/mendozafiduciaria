<style>
    input {
        background-image: url("../../../../../general/css/img/button.png");
        background-position: 0 -60px;
        border: 1px solid #438ac2;
        border-radius: 5px;
        padding:3px 5px;
    }
    body{
        text-align: center;
    }
</style>
<div id="div-credito-info">
    <table width="100%">
        <tr>
            <td>Tomador</td>
            <td><?php echo $credito['POSTULANTES_NOMBRES'] ?></td>
        </tr>
        <tr>
            <td>Recibo</td>
            <td><?php echo $pago[0]['RESUMEN']['RECIVO'] ?></td>
        </tr>
        <tr>
            <td>Fecha</td>
            <td><?php echo $pago[0]['RESUMEN']['FECHA_IMPUTACION'] ?></td>
        </tr>
        <tr>
            <td>Monto</td>
            <td><?php echo $pago[0]['RESUMEN']['TOTAL'] ?></td>
        </tr>
    </table>
</div>
<input type="button" value="Imprimir" onclick="printDiv('div-credito-info')" />

<script type="text/javascript">
    function printDiv(divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
    }
</script>