<ul class="toolbar">
    <li class="tb_ver" data-top='inicio'><div>Inicio</div></li>
    <li class="tb_atras_ope" data-top="lis_regresar"><div>Regresar</div></li>
    </ul>

<div class="nuevaOpe_form">
    <input type="hidden" id="idh" value="<?php echo isset($entidad["ID"]) ? $entidad["ID"] : ''; ?>" />
    <div class="elem elem_med_cond" style="width: 420px;float: left;clear: none!important;">
        <label class="der">Nombre:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="nombre" id="opeNombre" value=""  maxlength="64">
        </div>
    </div>
    <div class="elem elem_med_cond" style="width: 370px;float: left;clear: none!important;">
        <label class="der">Descripcion:</label>
        <div class="indent formtext">
            <input type="text" title="descripcion" id="opeDescripcion" maxlength="100">
        </div>
    </div>
    <?php if (isset($lst_usu_coordinadores) && is_array($lst_usu_coordinadores)): ?>
        <div class="elem elem_med">
            <label>Coordinador:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" data-placeholder="Seleccione coordinador de operatoria" id="opeCoordinador">
                    <option value=""></option>
                    <?php foreach ($lst_usu_coordinadores as $value): ?>
                        <option data-local="<?php echo $value["ID"] ?>" data-connection="<?php echo $value["ID"] ?>" value="<?php echo $value["ID"] ?>"><?php echo $value["NOMBRE"] . " " . $value["APELLIDO"] ?></option>
                    <?php endforeach; ?>
                </select>   
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($lst_usu_jefeoperaciones) && is_array($lst_usu_jefeoperaciones)): ?>
        <div class="elem elem_med">
            <label>Jefe:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" id="opeJefe" data-placeholder="Seleccione jefe de operatoria">
                    <option value=""></option>
                    <?php foreach ($lst_usu_jefeoperaciones as $value): ?>
                        <option data-local="<?php echo $value["ID"] ?>" data-connection="<?php echo $value["ID"] ?>" value="<?php echo $value["ID"] ?>"><?php echo $value["NOMBRE"] . " " . $value["APELLIDO"] ?></option>
                    <?php endforeach; ?>
                </select>   
            </div>
        </div>
    <?php endif; ?>
    <div class="elem elem_med_cond">
        <label class="der">Litros Maximo:</label>
        <div class="indent formtext">
            <input type="text" title="Litros maximo" id="listrosMax" >
        </div>
    </div>
    <div class="elem elem_med">
        <label>Condicion de Pago:</label>
        <div class="indent">
            <select class="chzn-select medium-select select" id="formaPago" onchange="verCuotas()" data-placeholder="Forma de pago">
                <option value="Contado">Contado</option>
                <option value="Cuotas">Cuotas</option>
            </select>   
        </div>
    </div>
    <div id="ver-cuotas" style="display: none;">
        <div class="elem elem_med">
            <label>Cantidad Cuotas:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" id="cantCuotas">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>   
            </div>
        </div>
    </div>
    <div style="margin-top:10px;" class="clear"></div>
    <hr style="margin-top:40px; margin-left: auto; margin-right: auto; border: 1px dashed grey; height: 0; width: 60%;">
    <?php if (isset($lst_proveedores) && is_array($lst_proveedores)): ?>
        <div class="elem elem_med">
            <label>Proveedores:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" id="opeProveedores" data-placeholder="Seleccione proveedores" multiple="multiple">
                    <option value=""></option>
                    <?php foreach ($lst_proveedores as $value): ?>
                        <option data-local="<?php echo $value["ID"] ?>" data-connection="<?php echo $value["ID"] ?>" value="<?php echo $value["ID"] ?>"><?php echo $value["RAZON_SOCIAL"] ?></option>
                    <?php endforeach; ?>
                </select>   
            </div>
        </div>
    <?php endif; ?>
    <!--<div class="button-a blue asignarLimiteProv"><span>Asignar Limite Litros</span></div>-->
    <div id="info-proveedores">
        <br>
        <p style="font-weight:bold;">Ingrese el limite de Litros y el maximo de Hectareas de la grilla</p>
        <div id="jqxgrid_proveedores" style="margin-left:20px; margin-top: 5px;">
        </div>
    </div>
    <div class="clear"></div>
    <hr style="margin-top:40px; margin-left: auto; margin-right: auto; border: 1px dashed grey; height: 0; width: 60%;">
    <?php if (isset($lst_bodegas_ope) && is_array($lst_bodegas_ope)): ?>
        <div class="elem elem_med">
            <label>Bodegas:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" id="opeBodega" data-placeholder="Seleccione bodegas" multiple="multiple">
                    <option value=""></option>
                    <?php foreach ($lst_bodegas_ope as $value): ?>
                        <option data-local="<?php echo $value["ID"] ?>" data-connection="<?php echo $value["ID"] ?>" value="<?php echo $value["ID"] ?>"><?php echo $value["NOMBRE"] ?></option>
                    <?php endforeach; ?>
                </select>   
            </div>
        </div>
    <?php endif; ?>
    <!--<div class="button-a blue asignarLimiteBod"><span>Asignar Limite Litros</span></div>-->
    <!--<div id="mas" class="button-a blue"><span>Mas</span></div>-->
    
    <div id="info-bodegas">
        </br>
        <p style="font-weight:bold;">Ingrese los litros en la columna LIMITE LTRS de la grilla</p>
        <div id="jqxgrid_bodegas" style="margin-left:20px; margin-top: 5px;">
        </div>
    </div>
    <div class="clear"></div>
    <hr style="margin-top:40px; margin-left: auto; margin-right: auto; border: 1px dashed grey; height: 0; width: 60%;">
<!--    <div class="elem elem_med_cond">
        <label class="der">Cant. Max. Hectareas:</label>
        <div class="indent formtext">
            <input type="text" title="Max.Hectareas" id="maxHectareas" >
        </div>
    </div>-->
    <div class="elem elem_med_cond">
        <label>Seleccionar Persona:</label>
        <div class="indent">
            <select class="chzn-select medium-select select" id="tipoPersona" onchange="verPersona()" data-placeholder="Persona">
                <option value="Humana">Humana</option>
                <option value="Juridica">Juridica</option>
            </select>   
        </div>
    </div>

    <div style="margin-top: 25px; margin-left: auto; margin-right: auto;" id="listbox_humana"></div>
    <div style="margin-top: 25px; margin-left: auto; margin-right: auto;" id="listbox_juridica"></div>

    <input id="send" name="send" type="submit" class="button-a blue send" style="margin-top: 25px;" value="Guardar">
    <input id="send_edit" name="send_edit" type="submit" class="button-a blue send_edit" style="margin-top: 25px;" value="Guardar Cambios">
</div>

<!--<div id="wpopup"></div>-->

