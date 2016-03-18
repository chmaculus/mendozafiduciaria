<ul class="toolbar">
    <li class="tb_atras" data-top="lis_editar"><div>Regresar</div></li>
    <li class="tb_ver" data-top='inicio'><div>Inicio</div></li>
    <li class="tb_ver" data-top='inicio'><div>Inicio2</div></li>
    <li class="tb_ver" data-top='inicio'><div>Inicio3</div></li>
    <li class="tb_save1" data-top="lis_guardar_ope"><div>Guardar Factura</div></li>
</ul>

<div class="nuevaOpe_form">

    <input type="hidden" id="idh" value="<?php echo isset($entidad["ID"]) ? $entidad["ID"] : ''; ?>" />

    <div class="elem elem_med_cond">
        <label class="der">Nombre:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="cuit" id="opeNombre" value=""  maxlength="11">
        </div>
    </div>
    <div class="elem elem_med_cond">
        <label class="der">Descripcion:</label>
        <div class="indent formtext">
            <input type="text" title="nombre" id="opeDescripcion" >
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
            <input type="text" title="nombre" id="listrosMax" >
        </div>
    </div>

    <div class="elem elem_med">
        <label>Condicion de Pago:</label>
        <div class="indent">
            <select class="chzn-select medium-select select" id="formaPago" data-placeholder="Forma de pago">
                <option value="Contado">Contado</option>
                <option value="Cuotas">Cuotas</option>
            </select>   
        </div>
    </div>

    <div class="elem elem_med">
        <label>Cantidad Cuotas:</label>
        <div class="indent">
            <select class="chzn-select medium-select select" id="cantCuotas">
                <option value="cuotaUno">1</option>
                <option value="cuotaDos">2</option>
                <option value="cuotaTres">3</option>
                <option value="cuotaCuatro">4</option>
                <option value="cuotaCinco">5</option>
                <option value="cuotaSeis">6</option>
            </select>   
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

    <div class="button-a blue asignarLimiteProv"><span>Asignar Limite Litros</span></div>

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
    
    <div class="button-a blue asignarLimiteBod"><span>Asignar Limite Litros</span></div>
    
    <div id="info-bodegas">
</br>
        <p style="font-weight:bold;">Ingrese los litros en la columna LIMITE LTRS de la grilla</p>
        <div id="jqxgrid_bodegas" style="margin-left:20px; margin-top: 5px;">
        </div>
    </div>
<div class="clear"></div>
    <hr style="margin-top:40px; margin-left: auto; margin-right: auto; border: 1px dashed grey; height: 0; width: 60%;">
    
    <div class="elem elem_med_cond">
        <label class="der">Cant. Max. Hectareas:</label>
        <div class="indent formtext">
            <input type="text" title="nombre" id="listrosMaximo" >
        </div>
    </div>

    <div style="margin-top: 25px; margin-left: auto; margin-right: auto;" id="listbox"></div>

    <input id="send" name="send" type="submit" class="button-a blue send" value="Guardar">
    <input id="nuevafactura" name="nuevafactura" type="submit" class="button-a blue send" value="Nueva Factura" style="margin-right: 10px;">

</div>

<div id="wpopup"></div>

