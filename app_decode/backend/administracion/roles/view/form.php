<!--Form-->
 <div class="content-form">
    <form method="post" enctype="multipart/form-data" id="customForm" >
    <div class="grid-1" id="frmagregar">
       <div class="title-grid"><div id="label_action">Agregar</div>Usuario</div>
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
                <select class="validate[required] chzn-select medium-select select" id="id_rol" data-prompt-position="CenterRight">
                    <option value="">Elegir Rol</option>
                    <?php foreach($lst_roles as $rs_rol): ?>
                    <option value="<?php echo $rs_rol["ID"] ?>"><?php echo $rs_rol["DENOMINACION"] ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="elem elem_med">
                <label class="der">Email:</label>
                <div class="indent formtext">
                    <input type="text" class="validate[required,custom[email]] texto_user" title="Ingrese email" id="email" data-prompt-position="topRight" value="<?php echo (isset($entidad['EMAIL'])? $entidad['EMAIL']:"" ) ?>"> 
                </div>
            </div>
           
            <div class="elem elem_med">
                <label class="der">Username:</label>
                <div class="indent">
                    <input type="text" title="Ingrese Username" class="validate[required,funcCall[checkUSERNAME]] texto_user" id="username" data-prompt-position="centerRight" value="<?php echo (isset($entidad['USERNAME'])? $entidad['USERNAME']:"" ) ?>"> 
                </div>
            </div>
            
            <div class="elem elem_med">
                <label class="der">Clave:</label>
                <div class="indent">
                    <input type="password" title="Ingrese Clave" class="texto_user" id="clave" data-prompt-position="centerRight" value="">
                </div>
            </div>
            
            <div class="elem elem_med">
                <label class="der">Nombre:</label>
                <div class="indent">
                    <input type="text" title="Ingrese Nombre" class="texto_form" id="nombre" value="<?php echo (isset($entidad['NOMBRE'])? $entidad['NOMBRE']:"" ) ?>"> 
                </div>
            </div>
            
            <div class="elem elem_med">
                <label class="der">Apellido:</label>
                <div class="indent formtext">
                    <input type="text" class="texto_form" title="Ingrese Apellid" id="apellido" data-prompt-position="centerRight" value="<?php echo (isset($entidad['APELLIDO'])? $entidad['APELLIDO']:"" ) ?>"> 
                </div>
            </div>
            
            <div class="notapie">Username: Entre 4 y 16 caracteres. Permite min??sculas y digitos.</div>
            
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