<div id="frm_reqs" class="tab_b">
    
    <input type="hidden" id="idreqh" value="<?php echo isset($obj_req["ID"])?$obj_req["ID"]:""?>" />
    <?php if ( isset($hora_mostrar) && $hora_mostrar ): ?>
    <input type="hidden" id="femish" value="<?php echo isset($hora_mostrar)?$hora_mostrar:""?>" />
    <?php else: ?>
    <input type="hidden" id="femish" value="<?php echo isset($obj_req["FCREA"])?convertirFecha($obj_req["FCREA"]):""?>" />
    <?php endif; ?>
    
    <input type="hidden" id="estadoh" value="<?php echo isset($obj_req["ESTADO"])?$obj_req["ESTADO"]:""?>" />
    <input type="hidden" id="destinatarioh" value="<?php echo isset($obj_req["DESTINATARIO"])?$obj_req["DESTINATARIO"]:""?>" />
        
    <!--
    <input type="hidden" id="fresph" value="<?php echo isset($obj_req["FREC"])?convertirFecha($obj_req["FREC"]):""?>" />
    <input type="hidden" id="idreq_usoh" value="<?php echo isset($obj_req["ID"])?$obj_req["ID"]:""?>" />
    -->
    
    <div class="title_adj">Agregar Nota</div>
    
    <div class="elem elem_med_gar">
        <label>Fecha Presentación:</label>
        <div class="indent formtext">
            <input type="text" class="" title="Ingrese Fecha" id="femis" value="" readonly="readonly">
        </div>
    </div>
    
    
    <div class="elem elem_med_cond">
        <label class="der">Asunto:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="Ingrese asunto" id="req_asunto" value="<?php echo isset($obj_req["ASUNTO"])?$obj_req["ASUNTO"]:""?>">
        </div>
    </div>
    
    <div class="elem elem_med_cond">
        <label class="der">Remitente:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="Ingrese asunto" id="req_remitente" value="<?php echo isset($obj_req["REMITENTE"])?$obj_req["REMITENTE"]:""?>">
        </div>
    </div>
    
    <div class="elem elemod1">
        <label class="ancho100 elemod2">Observaciones:</label>
        <div class="formtext">
            <textarea  class="medium elemod3" id="req_descripcion" name="req_descripcion" rows="5"><?php echo isset($obj_req["DESCRIPCION"])?$obj_req["DESCRIPCION"]:""?></textarea>
        </div>
    </div>
    
    
    
    
    <div class="elem elem_med_cond">
        <label class="der">Adjunto:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="Ingrese etiqueta" id="req_etiqueta" value="">
        </div>
    </div>
    
    <div class="elem myfile" id="fot_car">
        <div class="indent"></div>
        <form id="upload_file2" action="backend/carpeta/notas/get_file_notas" target="enviar_archivo" method="post" enctype="multipart/form-data">
            <div class="uploader black">
                <input type="text"  class="filename" readonly="readonly" id="lblfile">
                <input type="button" class="button_files " value="Examinar...">
                <input type="file" name="imagen" id="imagen">
                <input type="hidden" name="etapa" id="etapa" value="1">
                <input type="hidden" name="semilla" id="semilla" value="<?php echo $_semilla ?>">
                <input type="hidden" name="req_etiquetah" id="req_etiquetah" value="">
                
                <?php 
                if (isset($entidad["ID"])): ?>
                <input type="hidden" name="id_edit" id="id_edit" value="<?php echo "22"//$entidad["ID"] ?>"/>
                <?php else: ?>
                <input type="hidden" name="id_edit" id="id_edit" value="0"/>
                <?php endif; ?>
            </div>
            <input id="btnSubirfile" name="btnSubirfile" type="submit" class="button-a dark-blue" value="Upload" /> &nbsp;&nbsp;
        </form>
        <iframe name="enviar_archivo" id="enviar_archivo"></iframe>
    </div>
        
    <div class="lista_adj_r">
        <span>Adjuntos:</span>
        <ul class="lista_reqs_adj">
            <?php
                if( isset($lst_uploads_req) && is_array($lst_uploads_req) ):
                    $cont = 0;
                    foreach($lst_uploads_req as $rs_up):
                        $cont++;
                        echo '<li class="subido" data-nom="'.$rs_up["NOMBRE"].'">'.$rs_up["DESCRIPCION"].'</li><a href="#">Delete</a>';
                    endforeach;
                endif;
            ?>

        </ul>
        <div class="clear"></div>
    </div>
    
    
    <div class="grid-1 grid_adj_ope">
        <div class="title-grid">Trazabilidad<span></span></div>
        <div class="content-gird" style="display: block;">
            
            <div id="jqxgrid_traza"></div>
            
            <div class="clear"></div>
        </div>
    </div>
    
    
    <div class="elem elempie">
        <div class="indent">
            <div class="button-a blue send_nota"><span>Guardar Nota</span></div>
        </div>
    </div>
    
    
    
    
    

</div>