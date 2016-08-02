<div id="schCred">
    <input type="text" value="" class="fecha" />
    <input id="btnBuscar" type="button" value="Buscar" />
</div>
<div id="jqxTabs">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active">Cuotas a Facturar</li>
        <li>Cuotas enviadas</li>
    </ul>
    <div role="tabpanel" class="tab-pane" id="grilla_a_facturar" style="padding:0">
        <input id="btnFacturar" type="button" value="Enviar a Facturar" />
        <div id="jqxgrid"></div>
    </div>
    <div role="tabpanel" class="tab-pane" id="grilla_facturado" style="padding:0">
        <div id="jqxgrid2"></div>
    </div>
</div>