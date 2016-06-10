<style>
    table {border-collapse: collapse;width: 100%;margin-top: 25px;}
    th{text-align: center;padding: 8px;}
    td {text-align: left;padding: 8px;}
    tr:nth-child(even){background-color: #f2f2f2}
    th {background-color: #a4bed4;color: white;}
    #cambio_titularidad_true{display: none; margin-top: 8px;}
</style>
<ul class="toolbar">
    <li class="tb_atras" data-top="lis_editar"><div>Regresar</div></li>
    <li class="tb_regresar_ope" data-top="ope_volver"><div>Regresar</div></li>
    <li class="tb_ver" data-top='inicio'><div>Inicio</div></li>
</ul>
<input type="hidden" id="cant-cuotas-f" value=""/>
<div class="cambioEstados_form">
    <div class="elem elem_med_cond">
        <label class="der">N° Factura:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="numFactura" id="numFactura" value="" >
        </div>
    </div>

    <div class="elem elem_med"><label class="der">Estado Factura:</label><div class="indent" id="estado-factura">
            <select class="chzn-select medium-select select" id="estFact">
                <option value="1">Cargada</option>
                <option value="5">Pago Solicitado</option>
                <option value="9">Pagada o Pago Rechazado</option>
                <option value="10">Anulada</option>
            </select>
        </div>
    </div>

    <div style="margin-top:50px;" class="clear"></div>
    <div id="estado-cuota">
    </div>

    <input id="send-estado" name="send" type="submit" class="button-a blue send" value="Actualizar Estado">
</div>
<div id="wpopup"></div>