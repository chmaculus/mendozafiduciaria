<ul class="toolbar" id="barra_normal">
    <li class="tb_add" data-top='add'><div>Agregar</div></li>
    <li class="tb_edi" data-top='edi'><div>Editar</div></li>
    <li class="tb_ver" data-ver='ver' data-top='edi'><div>Ver</div></li>
    <!-- <li class="tb_del" data-top='del'><div>Eliminar</div></li>-->
    
    <?php if ($_SESSION["USER_SU_1"]==1 || $_SESSION["USER_SU_2"]==1): ?>
        <div class="tb_sep"></div>
    <?php endif; ?>
    
    <?php if ($_SESSION["USER_SU_1"]==1): ?>
    <li class="tb_chist su_hist" data-top="lis_su_hist"><div>Carga Vigente</div></li>
    <?php endif; ?>
    
    <?php if ($_SESSION["USER_SU_2"]==1): ?>
    <li class="tb_edi_hist su_hist" data-top="lis_su_edihist"><div>Editar Vigente</div></li>
    <?php endif; ?>
    
    <?php if ($_SESSION["USER_SU_5"]==1): ?>
    <li class="tb_chist su_hist" data-top="lis_su_hist1"><div>Carga Histórica</div></li>
    <?php endif; ?>
    
    <?php if ($_SESSION["USER_SU_6"]==1): ?>
    <li class="tb_edi_hist su_hist" data-top="lis_su_edihist1"><div>Editar Histórica</div></li>
    <?php endif; ?>
    
    <div class="tb_sep"></div>
    <li class="sub tb_todas"><div>Todas</div></li>
    <li class="sub tb_miscar"><div>Mis Carpetas</div></li>
    <li class="sub tb_cart"><div>Cartera</div></li>
    <li class="sub tb_pend"><div>Pendientes</div></li>
    <li class="sub tb_autor"><div>Autorizar</div></li>

    <div class="tb_sep"></div>
    <li class="tb_lis" data-top="lis"><div>Listado</div></li>
    <li class="tb_des" data-top='des'><div>Recuperar</div></li>
    <li class="tb_desis" data-top='desis'><div>Desistir</div></li>
    <li class="tb_exp" data-top='exp'><div>Exportar</div></li>
</ul>

<ul class="toolbar" id="barra_editar">
    <li class="tb_atras" data-top="lis_editar"><div>Regresar</div></li>
    <li class="tb_save send1" data-top="lis_guardar"><div>Guardar/Enviar</div></li>
    <?php if ($_SESSION["USER_SU_4"]==1): ?>
    <li class="tb_haciaatras su_1" data-top="lis_su_1"><div>Asignar Atras</div></li>
    <?php endif; ?>
    <?php if ($_SESSION["USER_SU_3"]==1): ?>
    <li class="tb_vestirse su_2" data-top="lis_su_2"><div>Vestirse</div></li>
    <?php endif; ?>
    
    <!--<li class="tb_save" data-top="lis_libre"><div>Pase Libre</div></li>
    <li class="tb_save asignar1" data-top="lis_guardar_enviar"><div>Guardar y Enviar</div></li>
    <li class="tb_save asignar_pase" data-top="nodata"><div>Pase</div></li>-->
</ul>

<div id="jqxgrid"></div>
<div id="wpopup"></div>