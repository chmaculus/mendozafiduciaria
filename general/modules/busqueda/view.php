<div id="main_busqueda">
    <div class="">
        <button onclick="">Encabezado</button>
        <button onclick="">Detalle</button>

    </div>
    <div class="content_busqueda">
        <ul>
        <?php foreach($elementos as $elemento){ ?>
            <li data-id="<?=$elemento['ID']?>">
                <span style="width: 100px;"><?=$elemento['col1']?></span>
                <span style="width: 400px;"><?=$elemento['col2']?></span>
            </li>
        <?php } ?>
        </ul>
    </div>
    <div class="search_bar">
        <input type="text" id="txtSearch"/>
        <button onclick="cancelar_busqueda()">Cancelar</button>
    </div>
</div>

