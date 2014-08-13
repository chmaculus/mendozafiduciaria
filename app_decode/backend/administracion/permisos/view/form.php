<!--Form-->
 <div class="content-form">
    <form method="post" enctype="multipart/form-data" id="customForm" >
    <div class="grid-1" id="frmagregar">
       <div class="title-grid"><div id="label_action">Agregar</div>Permiso</div>
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

            <?php if(is_array($lst_roles)): ?>
            <div class="elem elem_med" >
                <label>Rol:</label>
                <div class="indent">
                <select class="validate[required] chzn-select medium-select select" id="id_rol" data-prompt-position="topLeft">
                    <option value="">Elegir Rol</option>
                    <?php foreach($lst_roles as $rs_rol): ?>
                    <option value="<?php echo $rs_rol["ID"] ?>"><?php echo $rs_rol["DENOMINACION"] ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>
            <?php endif; ?>

            <?php if(is_array($lst_permisos)): ?>
            <div class="elem elem_med" >
                <label>Permiso:</label>
                <div class="indent">
                <select class="validate[required] chzn-select medium-select select" id="id_permiso" data-prompt-position="topLeft">
                    <option value="">Elegir Permiso</option>
                    <?php foreach($lst_permisos as $rs_per): ?>
                    <option value="<?php echo $rs_per["ID"] ?>"><?php echo $rs_per["MODULO"] . " - " . $rs_per["PERMISO"] ?></option>
                    <?php endforeach; ?>
                </select>   
                </div>
            </div>
            <?php endif; ?>

            <div class="elem elem_med" >
                <label></label>
                <div class="indent">
                    <div class="checklist_cont">
                        <div id="listbox"></div>
                    </div>
                </div>
            </div>
           
            <div class="btn_all">
                <input style='margin-left: 25px;' type="button" value="Quitar selección" id='jqxButton' />
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