<div class="content-result">
    <div class="field-buttons">
        <div class="button-a blue " onclick="imprimir_frame();"><span>Imprimir</span></div>
        <div class="button-a blue " onclick="salir_chequera();"><span>Volver</span></div>
    </div>
    <iframe id="frmPrint" name="frmPrint"  src="html.html"></iframe>

</div>

<div class="content-cuotas">
    <div class="form_cuotas">
        <span class="titulo-ingreso-eventos">INGRESO DE EVENTOS</span>
        <div class="field_fecha">
            <span class="">Fecha Actual</span><br/>
            <input type="text" class="fecha" id="txtFecha" value="<?=date("d-m-Y")?>">
            <label id="txtFecha2" value=""></label>
        </div>
        <div class="full_field_alta">
            <div class="field-accion">
                <span class="">Accion</span><br/>
                <select id="txtEvento" onchange="cambiar_accion();" onkeyup="cambiar_accion();">
                    <option value="0">Mostrar Estado</option>
                    <option value="1" class="editar">Desembolso</option>
                    <option value="2" class="editar">Gasto</option>
                    <option value="3" class="editar">Cambio de Tasa</option>

                    <option value="4" class="editar">Recupero</option>
                    <option value="5" class="editar">Reimputar cuotas</option>
                    <option value="6" class="editar">Enviar Cuotas</option>
                    <option value="7" class="editar">Saldo traspaso</option>
                    <option value="8" class="editar">Generar Chequeras</option>
                    <?php if (isset($vars['DE_CADUCADO']) && !$vars['DE_CADUCADO']) { ?>
                    <option value="9" class="editar">Caducar</option>
                    <?php } else { ?>
                    <option value="10" class="editar">Refinanciacion caida</option>
                    <?php } ?>
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
            <div class="button-a blue " onclick="imprimirEventos();">
                <span>Imprimir</span>
            </div>
         <!--   <div class="button-a blue " onclick="mostrar_credito();">
                <span>Actualizar</span>
            </div>
            <div class="button-a blue " onclick="recalcular_cuotas();">
                <span>Recalcular</span>
            </div>        -->
        </div>

        <div id="eventos-pendientes" class="eventos-pendientes">
        </div>        
    </div>
    
    <div class="versiones_content" >
        <span class="titulo-versiones">VERSIONES</span>
        <div class="wrap_version">
            <span id="spVersionTitulo"></span>
            
            <div class="version_opciones">

                <button onclick="mostrar_credito();">Mostrar Version</button>
                <button onclick="make_active_version();"  class="editar">Cambiar a Activa</button>
                <button onclick="recalcular_cuotas();" >Recargar Version</button>
                <button onclick="eliminar_version();" class="editar">Eliminar Version</button>
            </div>
            <div id='divVersiones'>
            </div>
            
        </div>
    </div>



    <div class="div-result">
        <?php include($path_view . "lista_cuotas.php"); ?>
    </div>
</div>
