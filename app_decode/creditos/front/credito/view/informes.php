<!--Form-->
<div class="content-form">
    <div id="customForm" >
        <div class="grid-1" id="frmagregar">
            <div class="title-grid"><div id="label_action">Parametros</div>Generales</div>
            <div class="content-gird">
                <div class="field_opc-informe">
                    <span class="">Fecha Actual</span><br/>
                    <input type="text" class="fecha" id="txtFechaInformes" value="<?=date("d-m-Y")?>">
                </div>
                <div class="field_opc-informe">
                    <span class=""><br/>Proyección Teórica</span>&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" id="chkIntereses"/>
                </div>
                <div class="field_opc-informe">
                    <span class=""><br/>Mostrar Planchado</span>&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" id="chkPlanchado"/>
                </div>
                <div class="field_opc-informe">
                    <span class="">
                        <input type="hidden" value="" id="calculo_cuota" />
                    </span><br/>
                    <button onclick="actualizar_informe();">Actualizar Informe</button>
                </div>
            </div>
        </div>
    </div>

    <!--Form end-->
    <div id="vtab">
        <ul>
            <li class="checklist etapa_ready" ><span>Desembolsos</span></li>
            <li class="inicial etapa_ready"><span>Estado Cuotas</span></li>
            <li class="garantia etapa_ready"><span>Cobranzas</span></li>
            <li class="gastos etapa_ready"><span>Gastos</span></li>
            <li class="tasas etapa_ready"><span>Tasas</span></li>
            <li class="reporte etapa_ready"><span>Reporte</span></li>
        </ul>

        <div class="vtabinfo" >
        </div>
        <div class="vtabinfo" >
        </div>
        <div class="vtabinfo" >
        </div>
        <div class="vtabinfo" >
        </div>
        <div class="vtabinfo" >
        </div>
    </div>
</div>