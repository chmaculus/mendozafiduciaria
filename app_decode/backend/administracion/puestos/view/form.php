<!--Form-->
 <div class="content-form">
    <form method="post" enctype="multipart/form-data" id="customForm" >
    <div class="grid-1" id="frmagregar">
       <div class="title-grid"><div id="label_action">Agregar</div>Puesto</div>
       <div class="content-gird">
       <div class="form">

            <input type="hidden" id="idh" value="<?php echo isset($entidad["ID"])?$entidad["ID"]:''; ?>" />
            <input type="hidden" id="val_ok" value="0" />
            <input type="hidden" id="val_entidadesh" value="<?php echo $cad ?>" />
            <script type='text/javascript'>
            <?php 
                    if(isset($obj_js)):
                        echo "var _array_obj = ". $obj_js . ";\n";
                    endif;
            ?>
            </script>
           
            <?php if(is_array($lst_areas)): ?>
            <div class="elem elem_med" >
                <label>Area:</label>
                <div class="indent">
                <select class="validate[required] chzn-select medium-select select" id="id_area">
                    <option value="">Elegir Area</option>
                    <?php foreach($lst_areas as $rs_area): ?>
                    <option value="<?php echo $rs_area["ID"] ?>"><?php echo $rs_area["DENOMINACION"] ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>
            <?php endif; ?>
                        
            <div class="elem elem_med">
                <label class="der">Denominación:</label>
                <div class="indent">
                    <input type="text" title="Ingrese Denominacion" class="validate[required] texto_form" id="denominacion" value="<?php echo (isset($entidad['DENOMINACION'])? $entidad['DENOMINACION']:"" ) ?>">
                </div>
            </div>
            
            <div class="clear"> </div>
         </div>
       </div>
    </div>
    <!--Form end-->
    
    <div class="elem elempie">
        <div class="indent">
          <input id="send" name="send" type="submit" class="button-a gray" value="Enviar" /> &nbsp;&nbsp;
          <button class="button-a dark-blue" id="btnClear">Limpiar</button>  
        </div>
    </div>
                 
</form>
       
</div>