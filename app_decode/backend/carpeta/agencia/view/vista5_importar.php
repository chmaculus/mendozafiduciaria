<ul class="toolbar">
    <li class="tb_ver" data-top='inicio'><div>Inicio</div></li>
    <li class="tb_ver" data-top='impor_procesar'><div>Procesar</div></li>
    <li class="tb_ver" data-top='impor_revision'><div>En Revisión</div></li>
</ul>

<div class="elem myfile" id="fot_car">
      <label>Adjunto:</label>
      <div class="indent">
        <form id="upload_file1" action="backend/carpeta/compravino/get_file1" target="enviar_archivo" method="post" enctype="multipart/form-data">
            <div class="uploader black">
                <input type="text"  class="filename" readonly="readonly" id="lblfile"/>
                <input type="button" class="button_files " value="Examinar..."/>
                <input type="file" name="imagen" id="imagen"/>
                <input type="hidden" name="semilla" id="semilla" value=""/>
            </div>
            <input id="btnSubirfile" name="btnSubirfile" type="submit" class="button-a dark-blue" value="Upload" /> &nbsp;&nbsp;
        </form>
      </div>
</div>
<?php if($lst_operatorias) { ?>
<div id="op_vino" style="display:none">
    <h2>Seleccione la operatoria</h2>
    <ul id="lst_op_vino">
        <?php foreach ($lst_operatorias as $it_o) { ?>
        <li data-id="<?= $it_o['ID_OPERATORIA']; ?>"><?= $it_o['NOMBRE_OPE']; ?> (<?= date('d/m/Y', strtotime($it_o['FECHA_CRE'])) ?>)</li>
        <?php } ?>
    </ul>
    <button onclick="imp_procesar();" value="Procesar importación">Procesar importación</button>
</div>
<?php } ?>
<iframe name="enviar_archivo" id="enviar_archivo"></iframe>


<div class="lista_arch">
<?php echo listar_archivos('_tmp/importar/'); ?>
</div>