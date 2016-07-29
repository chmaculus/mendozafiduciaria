<?php //die(); ?>
<ul class="toolbar">

    <li class="tb_add" data-top='nuevo-credito'  id="liAlta"><div>Nuevo Credito</div></li>
    <li class="listado-credito tb_edi" id="liVer" data-top='ver' data-loc="creditos/front/credito" ><div>Ver</div></li>
    <li class="listado-credito tb_edi" id="liModificacion" data-top='editar'><div>Ver/Editar</div></li>
    <li class="listado-credito tb_edi" id="liOpcion" data-top='opciones'><div>Opciones</div></li>
    <?php if($permisos['BAJA']) { ?><li class="listado-credito tb_del" id="liBaja2" data-top='eliminar'><div>Eliminar</div></li><?php } ?>
    
    <div class=" tb_sep"></div>
    <li class="tb_exportar" data-top='exportar'><div>Exportar</div></li>
    <li class="tb_mora" data-top='moratorias'><div>Reportes</div></li>
    <li class="tb_todas" data-top='cobros'><div>Cobros</div></li>
    <li class="tb_todas" data-top='eventos'><div>Eventos Multiples</div></li>
    <li class="tb_imp"><div>Importar</div></li>
    <li class="tb_lis" data-top='listado'><div>Listado</div></li>
</ul>
<div id="mn-reportes">
    <table style="width:100%;z-index:1999;position:relative">
        <tr>
            <td><label>Fideicomisos: </label>
                <select id="ffid" multiple="multiple" size="1" data-placeholder="Seleccione una opción">
                    <?php foreach($fideicomisos as $it) { ?>
                    <option value="<?=$it['ID'];?>"><?=$it['NOMBRE'];?></option>
                    <?php } ?>
                </select>
            </td>
            <td><label>Fecha desde: </label><input type="text" class="fecha" id="fdesde" /></td>
            <td><label>Fecha hasta: </label><input type="text" class="fecha" id="fhasta" /></td>
            <td></td>
        </tr>
    </table>
    <ul id="btn-reportes">
        <li onclick="reporte1()">Estadística de mora</li>
        <li onclick="reporte2()">Notificaciones</li>
        <li onclick="reporte3()">Proyección de cobranzas</li>
        <li onclick="reporte4()">Caducidad</li>
    </ul>
</div>

<div id="msgs">
    <?php
    if (isset($_SESSION['msg_importacion_ok'])) {
        ?>
        <div id="msg_ok"><?= $_SESSION['msg_importacion_ok'] ?></div>
        <script type="text/javascript">
            verificar();
        </script>
        <?php
        unset($_SESSION['msg_importacion_ok']);
    }

    if (isset($_SESSION['msg_err'])) {
        ?>
        <div id="msg_err"><?= $_SESSION['msg_err'] ?></div>
        <?php
        unset($_SESSION['msg_err']);
    }
    ?>
</div>
<div class="form-import">
    <form action="creditos/front/formaltabase/x_guardar_creditos_excel" method="post" enctype="multipart/form-data">
        <h2>Importar créditos desde Excel</h2>
        <div class="c8">
            <input type="file" name="fexcel" />
            <input type="submit" class="btnImp" name="import_excel" value="IMPORTAR DATOS" onclick="importarExcel()" />
        </div>
    </form>
    <?php /*
    falta terminar -- no fue solicitado por el cliente --
    <form action="creditos/front/estructura/x_guardar_cuotas_excel" method="post" enctype="multipart/form-data">
        <div class="row c12 grupo">
            <div class="c3">
                <span class="titulo-seccion">
                    Importar cuotas desde Excel
                </span>
            </div>
            <div class="c8">
                <div class="row">
                    <div class="c5">
                        <input type="file" name="fexcel" id="fexcel" />
                    </div>
                    <div class="c7">
                        <input type="submit" class="blue" id="btnImportar" name="import_excel" value="IMPORTAR" onclick="importarExcel()" />
                    </div>
                </div>
            </div>
        </div>
    </form>
     * 
     */
    ?>
    <form action="creditos/front/cuotas/x_guardar_pagos_excel" method="post" enctype="multipart/form-data">
        <h2>Importar pagos desde Excel <span>(este proceso puede demorar varios minutos)</span></h2>
        <div class="c8">
            <input type="file" name="fexcel" />
            <input type="submit" class="btnImp" name="import_excel" value="IMPORTAR PAGOS" onclick="importarExcel()" />
        </div>
    </form>
    <form action="creditos/front/formaltabase/x_importar_desembolsos" method="post" enctype="multipart/form-data">
        <h2>Importar desembolsos desde Excel</h2>
        <div class="c8">
            <input type="file" name="fexcel" />
            <input type="submit" class="btnImp" name="import_excel" value="IMPORTAR DESEMBOLSOS" onclick="importarExcel()" />
        </div>
    </form>
</div>

<div id="jqxgrid"></div>
<div id="wpopup"></div>
