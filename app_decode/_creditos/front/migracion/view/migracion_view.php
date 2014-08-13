<div class="opciones-credito">
    <span onclick="invertir_seleccion();">Invertir Seleccion</span><br/>
    <span onclick="seleccionar_todo();">Seleccionar Todos</span>
    <button onclick="migrar();">Enviar</button>
</div>

<div class="lista-creditos-migracion">
    <ul class="titulo">
        <li>
            <span class="id_credito">ID</span>
            <span class="cliente">Cliente</span>
            <span class="monto">Monto</span>
            <span class="cantidad_cuotas">C.Cuotas</span>
            <span class="seleccionar">Seleccionra</span>
        </li>        
    </ul>
    <ul class="datos">
        <?php foreach($creditos as $credito){ ?>
        <li>
            <span class="id_credito"><?=$credito['ID']?></span>
            <span class="cliente"><?=$credito['RAZON_SOCIAL']?></span>
            <span class="monto"><?=  number_format($credito['CAPITAL'],2)?></span>
            <span class="cantidad_cuotas"><?=$credito['CANT_CUOTAS']?></span>
            <span class="seleccionar"><input type="checkbox" value="<?=$credito['ID']?>" /></span>
        </li>
        <?php } ?>
    </ul>
</div>