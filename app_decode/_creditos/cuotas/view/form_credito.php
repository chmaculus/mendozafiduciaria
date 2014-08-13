<div class="content-cuotas">
    <div class="form_cuotas">
        <div class="full_field">
            <span class="">Fecha Actual</span>
            <input type="text" class="fecha" id="txtFecha">
        </div>

        <div class="full_field">
            <span class="">Accion</span>
            <select id="txtEvento" onchange="cambiar_accion();" onkeyup="cambiar_accion()">
                <option value="0">Mostrar Estado</option>
                <option value="1">Desembolso</option>
                <option value="2">Gasto</option>
                <option value="3">Cambio de Tasa</option>
                
                <option value="4">Recupero</option>
                <option value="5">Generar Cuota</option>
                <option value="6">Enviar Cuotas</option>
                
                <option value="7">Cambio de Tasa Subsidio</option>
            </select>
        </div>

        <div class="full_field" id="div-monto">
            <span class="" id="spMonto">Monto</span>
            <input type="text"  id="txtMonto">        
        </div>

        <button onclick="agregar_variacion();">Calcular</button>
        <button onclick="mostrar_credito();">Actualizar</button>
    </div>
    <div class="div-result">
        <?php include($path_view."lista_cuotas.php");?>
    </div>
</div>