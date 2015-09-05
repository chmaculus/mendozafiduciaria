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
            <?php if(isset($obj_js)): ?>
            <input type="hidden" id="_arr_obj_n" value='<?php echo $obj_js ?>' />
            <?php endif; ?>
            
            <script type='text/javascript'>
            <?php 
                    //log_this($file, $var)
                    if(isset($obj_js)):
                        echo "var _array_obj = ". $obj_js . ";\n";
                    endif;
            ?>
            </script>

            <?php if(is_array($lst_roles)): ?>
            <div class="elem elem_med" style="margin-left: 21px;">
                <label>Rol:</label>
                <div class="indent">
                    <select class="validate[required] chzn-select medium-select select" id="id_rol" onchange="actualiza_ss(this.value)" data-prompt-position="topLeft">
                        <option value="">Elegir Rol</option>
                        <?php foreach($lst_roles as $rs_rol): ?>
                        <option value="<?php echo $rs_rol["ID"] ?>"><?php echo $rs_rol["DENOMINACION"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>

            <div class="cont_tabla">
                <div class="CSSTableGenerator">
                    <table >
                        <tr>
                            <td></td>
                            <td>&nbsp;Menu&nbsp;</td>
                            <td>&nbsp;&nbsp;&nbsp;Alta&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;&nbsp;Baja&nbsp;&nbsp;</td>
                            <td>&nbsp;Modif&nbsp;</td>
                            <td>&nbsp;Ver&nbsp;</td>
                            <td>Exportar</td>
                            <td>&nbsp;Otros&nbsp;</td>
                        </tr>
                        <?php if(is_array($lst_permisos)): ?>
                        <?php foreach($lst_permisos as $rs_per): ?>
                        <tr data-idp="<?php echo $rs_per["ID"] ?>">
                            <td><?php echo $rs_per["MODULO"] .' - ' . $rs_per["PERMISO"] ?></td>
                            <td><input type="checkbox" value="1"></td>
                            <td><input type="checkbox" value="1"></td>
                            <td><input type="checkbox" value="1"></td>
                            <td><input type="checkbox" value="1"></td>
                            <td><input type="checkbox" value="1"></td>
                            <td><input type="checkbox" value="1"></td>
                            <td>
                                <input type="checkbox" value="1">
                                <?php 
                                    if($rs_per["ID"]==10){?>
                                <div class="activo-letras" style="font-size: 10px;">(Encargado OP)</div>
                                <?php         
                                    }else{
                                ?>   
                                <div class="activo-letras" style="font-size: 10px;"></div>
                                 <?php
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif;  ?>
                    </table>
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