<div class="opciones-cuota">
    <input type="hidden" id="hcredito_id" value="<?=$ID_CREDITO?>">
    <input type="hidden" id="hcantidad_cuotas_opciones" value="<?=$CUOTAS_RESTANTES?>">

    <div class="full_field">
        <label>Fecha Vencimiento</label>
        <input type="text" class="fecha_opciones" id="txtFechaVencimientoEdit" value="<?=$FECHA_VENCIMIENTO?>">
    </div>
    
    <div class="full_field">
        <button onclick="guardar_opciones_cuota(<?=$ID_CREDITO?>,<?=$CUOTAS_RESTANTES?>);">Guardar</button>
        <button onclick="cancelar_opciones_cuota();">Cancelar</button>
        
    </div>

</div>