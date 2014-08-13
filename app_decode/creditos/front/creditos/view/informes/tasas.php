<ul class="lista-informe desembolsos">
    <li class="titulo">
        
        <span class="fecha-desembolso">DESDE</span>
        <span class="fecha-desembolso">HASTA</span>
        <span class="tasa-porcentaje">Comp.</span>
        <span class="tasa-porcentaje">Subsidio</span>
        <span class="tasa-porcentaje">Moratorio</span>
        <span class="tasa-porcentaje">Punitorio</span>
    </li>    
<?php foreach($tasas as $tasa){ ?>
    <li class="datos">
        <span class="fecha-desembolso"><?=date("d/m/Y",$tasa['FECHA_DESDE'])?></span>
        <span class="fecha-desembolso"><?=date("d/m/Y",$tasa['FECHA_HASTA'])?></span>
        <span class="tasa-porcentaje"><?=number_format($tasa['POR_INT_COMPENSATORIO'],4)?>%</span>
        <span class="tasa-porcentaje"><?=number_format($tasa['POR_INT_SUBSIDIO'],4)?>%</span>
        <span class="tasa-porcentaje"><?=number_format($tasa['POR_INT_MORATORIO'],4)?>%</span>
        <span class="tasa-porcentaje"><?=number_format($tasa['POR_INT_PUNITORIO'],4)?>%</span>
        
    </li>
<?php } ?>
</ul>
