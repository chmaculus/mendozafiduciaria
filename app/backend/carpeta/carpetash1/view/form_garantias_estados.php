<div class="content-formchk_estado">
        <div class="title">Cambiar estado a la Garantia</div>
        <?php if( is_array($lst_gar_estados)): ?>
        <div class="elem elem_med_gar" >
            <label>Estado:</label>
            <div class="indent">
            <select class="chzn-select medium-select2 select" id="change_tipo_garantia">
                
                <?php foreach($lst_gar_estados as $rs_est): ?>
                <option value="<?php echo $rs_est["ID"] ?>"><?php echo $rs_est["ESTADO"] ?></option>
                <?php endforeach; ?>
            </select>   
            </div>
        </div>
        <?php endif; ?>
        
        <div class="elem elempie">
            <div class="indent">
                <div class="button-a blue send_cambiarestado_gar"><span>Cambiar Estado</span></div>
            </div>
        </div>
</div>