<div class="lista-extract">
    <div class="tith"><?php echo $datos['titulo_cobro']; ?></div>
    <div class="opciones_extract">
        <button onclick="agregar_cobros_seleccionados();">Agregar Seleccionados</button>
        <button id="btnInvertir" onclick="invertir_seleccion();">Invertir Seleccion</button>
    </div>

    <ul class="titulo">
        <li class="">
            <span class="credito">CRÉDITO</span>
            <span class="razon_social">RAZÓN SOCIAL</span>
            <span class="fecha_rec">FECHA RECEPCION</span>
            <span class="fecha_ren">FECHA RENDICION</span>

            <span class="importe">IMPORTE</span>
            <span class="importe">IMP.CREDITO</span>
            <span class="importe">VENCIMIENTO</span>
            <span class="opciones_chk"><input id="chkTodos" onclick="seleccionar_todos();" type="checkbox" checked="true"/></span>
        </li>
    </ul>
    <ul class="datos">
        <?php foreach ($datos as $dato) { ?>
            <?php if (isset($dato['ID'])) { ?>
            <li class="<?= $dato['INGRESADO'] . ($dato['PAGADO'] ? ' tiene_pag' : '') ?>" data-id="<?= $dato['ID'] ?>" data-idcredito="<?= $dato['ID_CREDITO'] ?>" data-importe="<?= $dato['IMPORTE'] ?>" data-fecha="<?= $dato['FECHA_REC'] ?>" data-cvencimiento="<?= $dato['CREDITO_VENCIMIENTO'] ?>" <?= $dato['PAGADO'] ? 'title="Hay un pago ingresado"' : '' ?>>
                <span class="credito"><?= $dato['ID_CREDITO'] ?></span>
                <span class="razon_social"><?= ($dato['RAZON_SOCIAL'] ? ((strlen($dato['RAZON_SOCIAL']) > 18) ? substr($dato['RAZON_SOCIAL'], 0, 15) . '...' : $dato['RAZON_SOCIAL']) : '&nbsp;') ?></span>
                <span class="fecha_rec"><?= $dato['FECHA_REC'] ?></span>
                <span class="fecha_ren"><?= $dato['FECHA_REN'] ?></span>

                <span class="importe dinero"><span><?= number_format((float) $dato['IMPORTE'], 2, ',', '.') ?></span></span>
                <span class="importe dinero"><span ><?= number_format((float) $dato['CREDITO_IMPORTE'], 2, ',', '.') ?></span></span>
                <span class="importe"><?= $dato['CREDITO_VENCIMIENTO'] ?></span>
                <span class="opciones_chk"><input type="checkbox"<?= $dato['PAGADO'] ? '' : ' checked="checked"' ?><?= ((float) $dato['IMPORTE']) ? '' : ' disabled="disabled"' ?>/></span>
            </li>
            <?php } ?>
        <?php } ?>
    </ul>    
</div>