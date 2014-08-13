<div class="div_upload_frm">
        <div class="title_adj">Agregar Adjunto</div>
        
        <div class="elem elem_med_cond">
            <label class="der">Etiqueta:</label>
            <div class="indent formtext">
                <input type="text" class="tip-right" title="Ingrese etiqueta" id="req_etiqueta" value="">
            </div>
        </div>
        
        <div class="elem myfile" id="fot_car">
            <div class="indent"></div> 
            <form id="upload_file1" action="backend/carpeta/carpetas/get_file1_gar" target="enviar_archivo" method="post" enctype="multipart/form-data">
                <div class="uploader black">
                    <input type="text"  class="filename" readonly="readonly" id="lblfile"/>
                    <input type="button" class="button_files " value="Examinar..."/>
                    <input type="file" name="imagen" id="imagen"/>
                    <input type="hidden" name="semilla" id="semilla" value="<?php echo $_semilla ?>"/>
                    <input type="hidden" name="req_etiquetah" id="req_etiquetah" value="">
                    <?php if (isset($entidad["ID"])): ?>
                    <input type="hidden" name="id_edit" id="id_edit" value="<?php echo "22"//$entidad["ID"] ?>"/>
                    <?php else: ?>
                    <input type="hidden" name="id_edit" id="id_edit" value="0"/>
                    <?php endif; ?>
                    <input type="hidden" name="id_garantia" id="id_garantia" value="<?php echo $id_garantia ?>"/>
                </div>
                <input id="btnSubirfile" name="btnSubirfile" type="submit" class="button-a dark-blue" value="Upload" /> &nbsp;&nbsp;
            </form>
        <iframe name="enviar_archivo" id="enviar_archivo"></iframe>
        </div>
</div>