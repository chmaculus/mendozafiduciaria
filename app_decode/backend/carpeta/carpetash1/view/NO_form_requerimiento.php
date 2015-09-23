<div id="frm_reqs" class="tab_b">
        
    <input type="hidden" id="idreqh" value="<?php echo isset($obj_req["ID"])?$obj_req["ID"]:""?>" />
    <input type="hidden" id="estadoh" value="<?php echo isset($obj_req["ESTADO"])?$obj_req["ESTADO"]:""?>" />
    <input type="hidden" id="femish" value="<?php echo isset($obj_req["FCREA"])?convertirFecha($obj_req["FCREA"]):""?>" />
    <input type="hidden" id="fresph" value="<?php echo isset($obj_req["FREC"])?convertirFecha($obj_req["FREC"]):""?>" />
    <input type="hidden" id="ahora" value="<?php echo isset($ahora)?$ahora:""?>" />
    <input type="hidden" id="idreq_usoh" value="<?php echo isset($obj_req["ID"])?$obj_req["ID"]:""?>" />
    <input type="hidden" id="_dir_sitio" value="<?php echo isset($_dir_sitio)?$_dir_sitio:""?>" />
    
    <div class="title_adj adj_cent">Requerimientos</div>
    
    <div class="elem elem_med_gar">
        <label>Fecha Emisión:</label>
        <div class="indent formtext">
            <input type="text" class="" title="Ingrese Fecha" id="femis" value="" maxlength="10">
        </div>
    </div>
    
    <div class="elem elem_med" >
        <label>Estado:</label>
        <div class="indent">
        <select class="chzn-select medium-select select" id="estadoreq" data-prompt-position="topLeft">
                <option value="">Elegir Estado</option>
                <option value="0">Pendiente Autorizacion</option>
                <option value="2">Emitido</option>
                <option value="3">Respondido</option>
                <option value="4">Aceptado</option>
                <option value="6">Rechazado</option>
        </select>   
        </div>
    </div>
    
    <div class="elem elem_med_cond">
        <label class="der">Asunto:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="Ingrese asunto" id="req_asunto" value="<?php echo isset($obj_req["ASUNTO"])?$obj_req["ASUNTO"]:""?>">
        </div>
    </div>
    
    <div class="elem elemod1">
        <label class="ancho100 elemod2">Observaciones:</label>
        <div class="formtext">
            <textarea  class="medium elemod3" id="req_descripcion" name="req_descripcion" rows="5"><?php echo isset($obj_req["DESCRIPCION"])?$obj_req["DESCRIPCION"]:""?></textarea>
        </div>
    </div>
    
    <div class="elem">
            <label>Fecha Respuesta:</label>
            <div class="indent formtext">
                <input type="text" class="" title="Ingrese Fecha" id="fresp" value="" readonly="readonly"> 
            </div>
    </div>
    
    <div class="elem">
        <label class="ancho100 elemod2">Respuesta:</label>
        <div class="formtext">
            <textarea  class="medium elemod3"  id="req_respuesta" name="req_respuesta" rows="5"><?php echo isset($obj_req["RESPUESTA"])?$obj_req["RESPUESTA"]:""?></textarea>
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
        <form id="upload_file2" action="backend/carpeta/carpetas/get_file_req" target="enviar_archivo" method="post" enctype="multipart/form-data">
            <div class="uploader black">
                <input type="text"  class="filename" readonly="readonly" id="lblfile">
                <input type="button" class="button_files " value="Examinar...">
                <input type="file" name="imagen" id="imagen">
                <input type="hidden" name="etapa" id="etapa" value="1">
                <input type="hidden" name="semilla" id="semilla" value="<?php echo $_semilla ?>">
                <input type="hidden" name="req_etiquetah" id="req_etiquetah" value="">
                
                <?php if (isset($entidad["ID"])): ?>
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
    
    
    <?php //mostrar solo cuando esta logueado el jefe o el cordinador de OP ?>
    
    <?php if ($_no_save!='no_save'): ?>
    <?php if ( $_SESSION["USER_ROL"]=='10' || $_SESSION["USER_ROL"]=='11' || $_SESSION["USER_ROL"]=='9'): ?>
    <?php if ( isset($obj_req["ESTADO"]) && ( $obj_req["ESTADO"]!='4' && $obj_req["ESTADO"]!='6')  ): ?>
    <div class="elem elempie">
        <div class="indent">
            <?php if ( isset($obj_req["ESTADO"]) && $obj_req["ESTADO"]=='3'): ?>
            <div class="button-a red btn_insuf"><span>Insuficiente</span></div>
            <div class="button-a blue btn_suf"><span class="">Suficiente</span></div>
            <?php endif; ?>
            
            <?php if ( isset($obj_req["ESTADO"]) && isset($obj_req["REMITENTE"]) && $obj_req["ESTADO"]=='2' && $obj_req["REMITENTE"]==$_SESSION["USERADM"]): ?>
            <div class="button-a blue btn_env_user"><span class="">Enviar al usuario</span></div>
            <?php endif; ?>

            <?php if ( isset($obj_req["ESTADO"]) && $obj_req["ESTADO"]!='2' ): ?>
            <div class="button-a blue send_req"><span>Guardar Requerimiento</span></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ( !isset($obj_req["ESTADO"]) ): ?>
    <div class="elem elempie">
        <div class="indent">
            <div class="button-a blue send_req"><span>Guardar Requerimiento</span></div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    
    <?php endif; ?>

</div>