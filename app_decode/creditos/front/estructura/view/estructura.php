<div class="cuotas_credito">
    <div class="field-buttons top">
        <div class="button-a blue editar-estructura" onclick="agregar_variacion();">
            <span>Guardar</span>
        </div>
    </div>    
    <ul>
        <li>
            <span class="cuota_nro">Num.Cuota</span>
            <span class="cuotas_monto">Monto</span>
            <span class="cuotas_monto">Vencimiento</span>
            <span class="cuotas_monto">Saldo Capital</span>
        </li>        
        <?php 
        $total = 0;
        $total_amortizacion = 0;
        
        foreach($cuotas as $cuota){ 
            $total_amortizacion += $cuota['DATA']['AMORTIZACION_CUOTA'];
        }
        
        foreach($cuotas as $cuota){ 
            $total += $cuota['CAPITAL_CUOTA'];
            ?>
        <li class="datos" data-id="<?=$cuota['ID']?>">
            <span class="cuota_nro"><?=$cantidad - $cuota['CUOTAS_RESTANTES'] + 1?></span>
            <span class="cuotas_monto"><input type="text" value="<?=($total_amortizacion)?$cuota['DATA']['AMORTIZACION_CUOTA']:$cuota['CAPITAL_CUOTA']?>"/></span>
            <span class="cuotas_fecha_modificar" ><?=date("d/m/Y",$cuota['FECHA_VENCIMIENTO'])?></span>
            <span class="cuotas_fecha"><?=$cuota['DATA']['AMORTIZACION_CUOTA']?></span>
        </li>
        <?php } ?>
        <li>
            <span class="cuota_nro">TOTAL EDICION</span>
            <span class="cuotas_monto"><span id="spTotal"></span>
        </li>        
        <li>
            <span class="cuota_nro">TOTAL REAL</span>
            <span class="cuotas_monto"><span id="spTotalReal"><?=$total ?></span>
        </li>        
        
    </ul>
    <div class="field-buttons bottom editar-estructura">
        <div class="button-a blue " onclick="guardar_datos_cuota();">
            <span>Guardar</span>
        </div>
        <div class="button-a blue " onclick="guardar_datos_reset();">
            <span>Reset Montos</span>
        </div>        
    </div>
</div>


    <a id="inline" href="#div_opciones_cuotas"></a>

    <div style="display:none">
        <div id="div_opciones_cuotas">
            
        </div>
    </div>
    


