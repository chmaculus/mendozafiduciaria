<ul class="toolbar">
    <li class="tb_ver" data-top='inicio'><div>Inicio</div></li>
    <li class="tb_ver" data-top='impor_procesar'><div>Procesar</div></li>
    <li class="tb_ver" data-top='impor_revision'><div>En Revisión</div></li>
</ul>

<div class="elem myfile" id="fot_car">
      <label>Adjunto:</label>
      <div class="indent">
        <form id="upload_file1" action="backend/carpeta/comprauva/get_file1" target="enviar_archivo" method="post" enctype="multipart/form-data">
            <div class="uploader black">
                <input type="text"  class="filename" readonly="readonly" id="lblfile"/>
                <input type="button" class="button_files " value="Examinar..."/>
                <input type="file" name="imagen" id="imagen"/>
                <input type="hidden" name="semilla" id="semilla" value=""/>
            </div>
            </br>
            <input id="btnSubirfile" name="btnSubirfile" type="submit" class="button-a dark-blue" value="Upload" /> &nbsp;&nbsp;
            <?php // if(isset($lst_entidades) && is_array($lst_entidades)){ ?>
            <!--<div id="mensaje-entidades">Entidad</div>-->
            <!--<select id="tipo_entidades" name="entidad">-->
                <!--<option id="0" name="tipo_entidad" value="">Seleccione el tipo de entidad...</option>-->
                <?php // foreach($lst_entidades as $rs_ent){ ?>
                <!--<option id="<?php // echo $rs_ent["ID"] ?>" name="tipo_entidad" value="<?php // echo $rs_ent["ID"] ?>">-->
                    <?php // echo $rs_ent["NOMBRE"] ?></option>
                <?php // } ?>
            <!--</select>-->
                <?php // } ?>
        </form>
            <?php if(isset($lst_entidades) && is_array($lst_entidades)){ ?>
            <div id="mensaje-entidades">Entidad</div>
            <select id="tipo_entidades" name="entidad">
                <option id="0" name="tipo_entidad" value="">Seleccione el tipo de entidad...</option>
                <?php foreach($lst_entidades as $rs_ent){ ?>
                <option id="<?php echo $rs_ent["ID"] ?>" name="tipo_entidad" value="<?php echo $rs_ent["ID"] ?>">
                    <?php echo $rs_ent["NOMBRE"] ?></option>
                <?php } ?>
            </select>
                <?php } ?>
      </div>
</div>
<iframe name="enviar_archivo" id="enviar_archivo"></iframe>


<div style="margin-top:-10px;" class="lista_arch">
<?php echo listar_archivos('_tmp/importar/'); ?>
</div>