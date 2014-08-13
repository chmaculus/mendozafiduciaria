  <div id="tabs_cuotas" class="content-cuotas-detalle">


            <ul >
                <li>Resumen</li>
                <?php foreach ($cuotas as $cuota) { ?>                
                <li>Cuota Nº <?=$cuota['_INFO']['NUM']?> - <?=date("d/m/Y",$cuota['_INFO']['HASTA'])?></li>
                <?php } ?>                
            </ul>



            <div >
                Resumen
            </div>
<?php foreach ($cuotas as $cuota) { ?>
            <div >
          
            </div>
<?php } ?>            

        </div>


      <?=$datos_cuota?>