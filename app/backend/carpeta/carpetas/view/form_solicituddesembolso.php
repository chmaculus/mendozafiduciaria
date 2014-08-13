<div class="content-form_desem" id="form_desem">
        <input type="hidden" id="_suma_desembolsosh" value="<?php echo isset($suma_desemb)?$suma_desemb:"0" ?>" />
        <input type="hidden" id="_suma_garantiash" value="<?php echo isset($suma_garantiasc)?$suma_garantiasc:"0" ?>" />
        <div class="title">Solicitud de Desembolso</div>
        
        <div class="elem elem_med_desem">
            <label>Titular:</label>
            <div class="indent formtext">
                <input type="text" class="" readonly="readonly" id="desem_titular" value="<?php echo isset($nom_clientes)?$nom_clientes:"" ?>">
            </div>
        </div>
        
        <div class="elem elem_med_desem">
            <label>Legajo:</label>
            <div class="indent formtext">
                <input type="text" class="" readonly="readonly" id="desem_legajo" value="<?php echo isset($cad_obj["cad_obj"][0]["CODIGO"])?$cad_obj["cad_obj"][0]["CODIGO"]:"" ?>">
            </div>
        </div>
        
        <div class="elem elem_med_desem">
            <label>Línea:</label>
            <div class="indent formtext">
                <input type="text" class="" readonly="readonly" id="desem_linea" value="<?php echo isset($cad_operatoria)?$cad_operatoria:"" ?>">
            </div>
        </div>
        
        <div class="elem elem_med_desem save">
            <label>Proyecto:</label>
            <div class="indent formtext">
                <input data-campo="PROYECTO" readonly="readonly"  type="text" class="" id="desem_proyecto" value="<?php echo isset($cad_obj["cad_obj"][0]["DESTINO"])?$cad_obj["cad_obj"][0]["DESTINO"]:"" ?>">
            </div>
        </div>
        
        <div class="elem elem_med_alta">
            <label>DESEMBOLSOS:</label>
        </div>
        
        <div class="elem elem_med_desembolso save">
            <label>Nº:</label>
            <div class="indent formtext">
                <input data-campo="DES_NUMERO" type="text" class="alta_requerido" id="desem_numero" value="<?php echo isset($datos_sol["0"]["DES_NUMERO"])?$datos_sol["0"]["DES_NUMERO"]:'' ?>">
            </div>
        </div>

        <div class="elem elem_med_gar save">
            <label>Cantidad:</label>
            <div class="indent formtext">
                <input data-campo="DES_MONTO" type="text" class="alta_requerido" id="desem_monto" value="<?php echo isset($datos_sol["0"]["DES_MONTO"])?$datos_sol["0"]["DES_MONTO"]:'' ?>">
            </div>
        </div>
        
        <div class="elem elem_med_desem save">
            <label>Monto Letras:</label>
            <div class="indent formtext">
                <input data-campo="DES_LETRAS" type="text" class="alta_requerido" id="desem_montoletras" value="<?php echo isset($datos_sol["0"]["DES_LETRAS"])?$datos_sol["0"]["DES_LETRAS"]:'' ?>">
            </div>
        </div>
        
        <div class="elem save">
            <label class="ancho100">Observaciones:</label>
            <div class="formtext">
                <textarea data-campo="OBS" class="medium_txtarea_desem alta_requerido"  id="desem_obs" name="obs_desembolso" rows="5"><?php echo isset($datos_sol["0"]["OBS"])?$datos_sol["0"]["OBS"]:'' ?></textarea>
            </div>
        </div>
        
        <?php if ( !isset($datos_sol[0]["ID"])): ?>
        <div class="elem elempie">
            <div class="indent">
                <div class="button-a blue send_solicdesembolso"><span>Guardar Formulario</span></div>
            </div>
        </div>
        <?php endif; ?>

</div>