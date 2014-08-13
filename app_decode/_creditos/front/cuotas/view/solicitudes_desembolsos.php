<span class="indent titulo-lista-desembolso-solicitado">DESEMBOLSOS SOLICITADOS</span>
<ul id="ul-sol-desembolsos" class="indent">
    <li class="lista-desmbolsos-solicitados-titulo">
        <span class="numero">NUMERO</span>
        <span class="monto">MONTO</span>
        <span class="letras">LETRAS</span>
        <span class="obs">OBSERVACIONES</span>
    </li>
    <?php foreach ($desembolsos as $desembolso) { ?>
        <li class="lista-desmbolsos-solicitados">
            <span class="numero"><?= $desembolso['DES_NUMERO'] ?></span>
            <span class="monto"><?= $desembolso['DES_MONTO'] ?></span>
            <span class="letras"><?= $desembolso['DES_LETRAS'] ?></span>
            <span class="obs"><?= $desembolso['OBS'] ?></span>
        </li>
    <?php } ?>
</ul>
<div class="btnList indent">
    <div class="button-a blue " onclick="volver_desembolsos_solicitados();">
        <span>Volver</span>
    </div>
</div>


<?php
?>
