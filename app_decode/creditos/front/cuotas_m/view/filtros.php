    <div class="content-result">
        <div class="field-buttons">
            <div class="button-a blue " onclick="imprimir_frame();"><span>Imprimir Chequeras</span></div>
            <div class="button-a blue " onclick="salir_chequera();"><span>Volver</span></div>
        </div>
        <iframe id="frmPrint" name="frmPrint"  src="html.html"></iframe>
        
    </div>

<div class="content-cuotas">

    <div class="form_cuotas">
        <span class="titulo-ingreso-eventos">INGRESO DE EVENTOS MULTIPLES</span>
        <div class="field_fecha">
            <span class="">Fecha Actual</span><br/>
            <input type="text" class="fecha" id="txtFecha" value="<?=date("d-m-Y")?>">
            <label id="txtFecha2" value=""></label>
        </div>
        
        
        <div class="field-filtros">
            <div class="field-descripcion" id="div-filtros">
                <span class="" >Desde</span><br/>
                <input type="text" class="fecha" id="txtFechaDesde">        
            </div>
            <div class="field-descripcion" id="div-filtros">
                <span class="" >Hasta</span><br/>
                <input type="text" class="fecha" id="txtFechaHasta">        
            </div>
            
            <button class="button-search " onclick="filtrar_cuotas();">Filtrar</button>
        </div>    
        
        
        <div class="full_field_alta">
            
            <div class="field-accion">
                <span class="">Accion</span><br/>
                <select id="txtEvento" onchange="cambiar_accion();" onkeyup="cambiar_accion();">
                    <option value="0">Mostrar Estado</option>
                    <option value="3">Cambio de Tasa</option>
                    <option value="6">Enviar Cuotas</option>
                    <option value="8">Generar Chequeras</option>
                </select>
            </div>

            <div class="field-monto" id="div-monto">
                <span class="" id="spMonto">Monto</span><br/>
                <input type="text"  id="txtMonto">        
            </div>
            <div class="field-monto field_tasas" id="div-monto">
                <span class="" id="spMonto">Subsidio</span><br/>
                <input type="text"  id="txtSubsidio">        
            </div>
            <div class="field-monto field_tasas" id="div-monto">
                <span class="" id="spMonto">Moratorio</span><br/>
                <input type="text"  id="txtMoratorio">        
            </div>
            <div class="field-monto field_tasas" id="div-monto">
                <span class="" id="spMonto">Punitorio</span><br/>
                <input type="text"  id="txtPunitorio">        
            </div>

            <div class="field-descripcion" id="div-descripcion">
                <span class="" id="spMonto">Descripcion</span><br/>
                <input type="text"  id="txtDescripcion">        
            </div>
            

        </div>

        <div class="field-buttons">
            <div class="button-a blue " onclick="agregar_variacion();">
                <span>Aceptar</span>
            </div>
        </div>

        <div id="eventos-pendientes" class="eventos-pendientes">
        </div>        
    </div>
    

    
    <div class="div-result">
        
    </div>


</div>