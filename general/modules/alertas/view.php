<div class="alertas-content">
    <div class="content-tabs">
        <span id="tab-cheques" onclick="selectcheque();">Cheques</span>
        <span id="tab-stock" onclick="selectstock();">Stock Minimo</span>
    </div>
    <div class="cheques-content">
        <span class="titulo">Fecha Cobro de Cheques</span>
        <ul>
            <?php foreach($cheques_alerta as $cheque){?>
            <li class="aviso">
                <span class="banco"><?=$cheque['BANCO']?></span>
                <span class="provincia"><?=$cheque['PROVINCIA']?></span>
                <span class="numero"><?=$cheque['numero']?></span>
                <span class="librador"><?=$cheque['Librador']?></span>
                <span class="emision"><?=date("d/m/Y",$cheque['Fecha_emision'])?></span>
                <span class="presentacion"><?=date("d/m/Y",$cheque['Fecha_presentacion'])?></span>
                <span class="total"><?=$cheque['Total']?></span>
            </li>
            <?php } ?>
            <?php foreach($cheques_pasados as $cheque){?>
            <li class="pasados">
                <span class="banco"><?=$cheque['BANCO']?></span>
                <span class="provincia"><?=$cheque['PROVINCIA']?></span>
                <span class="numero"><?=$cheque['numero']?></span>
                <span class="librador"><?=$cheque['Librador']?></span>
                <span class="emision"><?=date("d/m/Y",$cheque['Fecha_emision'])?></span>
                <span class="presentacion"><?=date("d/m/Y",$cheque['Fecha_presentacion'])?></span>
                <span class="total"><?=$cheque['Total']?></span>
            </li>
            <?php } ?>
        </ul>

    </div>
    <div class="stock-content">
        <span class="titulo">Stock Minimo</span>
        <ul>
            <?php foreach($stock_alerta as $stock){?>
            <li class="pasados">
                <span class="codigo"><?=$stock['CodBar']?></span>
                <span class="articulo"><?=$stock['ARTICULO']?></span>
                <span class="lugar"><?=$stock['LUGAR']?></span>
                <span class="stock"><?=$stock['Stock']?></span>
                <span class="stock-minimo"><?=$stock['Stock_minimo']?></span>
            </li>
            <?php } ?>
            <?php foreach($stock_aviso as $stock){?>
            <li class="aviso">
                <span class="codigo"><?=$stock['CodBar']?></span>
                <span class="articulo"><?=$stock['ARTICULO']?></span>
                <span class="lugar"><?=$stock['LUGAR']?></span>
                <span class="stock"><?=$stock['Stock']?></span>
                <span class="stock-minimo"><?=$stock['Stock_minimo']?></span>
            </li>
            <?php } ?>
        </ul>

    </div>
    <div class="foot-mod">
        <button onclick="cerrar_mod();">Cerrar</button>
    </div>
</div>