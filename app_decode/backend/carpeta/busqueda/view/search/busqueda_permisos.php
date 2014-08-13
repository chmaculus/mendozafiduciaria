<div class="main_window_fix busqueda">
    <div class="main-titulo">BUSQUEDA </div>
    <div class="table-content">
        <ul class="title">
            <li>
                <span class="bc_cod">COD</span>
                <span class="bc_nombre">NOMBRE</span>
                <span class="bc_domicilio">DOMICILIO</span>
            </li>
        </ul>
        <div class="content-lista-scroll">
            <ul class="content">
                <?php foreach($items as $item){ ?>
                <li>
                    <span class="bc_cod"><?=$item['ID']?></span>
                    <span class="bc_nombre">&nbsp;<?=$item['PERMISO']?></span>
                    <span class="bc_domicilio">&nbsp;<?=$item['MODULO']?></span>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="full_field">
        <label class="full_field">Filtro<br/><input type="text" id="txtSearchBusqueda" /></label>
    </div>
    <div class="button_form">
        <button onclick="cancelar_busqueda();">Volver</button>
    </div>

</div>

