<style>
    table {border-collapse: collapse;width: 100%;margin-top: 25px;}
    th{text-align: center;padding: 8px;}
    td {text-align: left;padding: 8px;}
    tr:nth-child(even){background-color: #f2f2f2}
    th {background-color: #a4bed4;color: white;}
</style>
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
        <label class="der">Descripción:</label>
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
        <label class="der">Litros Máximo:</label>
        <div class="indent formtext">
            <input type="text" title="Litros maximo" id="listrosMax" >
        </div>
    </div>
    <div class="elem elem_med">
        <label>Precios por Litro:</label>
        <div class="indent">
            <div class="elem elem_med">
                <label>Precio Contado (1 cuota):</label>
                <div class="indent formtext">
                    <input type="text" class="tip-right" title="Precio Contado" id="opeP1" value=""  maxlength="20">
                </div>
            </div>
            <div class="elem elem_med">
                <label>Precio en 2 cuotas:</label>
                <div class="indent formtext">
                    <input type="text" class="tip-right" title="Precio en 2 cuotas" id="opeP2" value=""  maxlength="20">
                </div>
            </div>
            <div class="elem elem_med">
                <label>Precio en 3 cuotas:</label>
                <div class="indent formtext">
                    <input type="text" class="tip-right" title="Precio en 3 cuotas" id="opeP3" value=""  maxlength="20">
                </div>
            </div>
            <div class="elem elem_med">
                <label>Precio en 4 cuotas:</label>
                <div class="indent formtext">
                    <input type="text" class="tip-right" title="Precio en 4 cuotas" id="opeP4" value=""  maxlength="20">
                </div>
            </div>
            <div class="elem elem_med">
                <label>Precio en 5 cuotas:</label>
                <div class="indent formtext">
                    <input type="text" class="tip-right" title="Precio en 5 cuotas" id="opeP5" value=""  maxlength="20">
                </div>
            </div>
            <div class="elem elem_med">
                <label>Precio en 6 cuotas:</label>
                <div class="indent formtext">
                    <input type="text" class="tip-right" title="Precio en 6 cuotas" id="opeP6" value=""  maxlength="20">
                </div>
            </div>
        </div>
    </div>
    <div style="margin-top:10px;" class="clear"></div>
    <hr style="margin-top:40px; margin-left: auto; margin-right: auto; height: 0; width: 60%;">
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
    <div id="info-proveedores">
        <!--<br>-->
        <p style="font-weight:bold;">Ingrese el límite de Litros y el maximo de Hectareas de la grilla</p>
        <div id="jqxgrid_proveedores" style="margin-left:20px; margin-top: 5px;">
        </div>
    </div>
    <div class="clear"></div>
    
    <hr style="margin-top:20px; margin-left: auto; margin-right: auto; height: 0; width: 60%;">
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
    <div id="info-bodegas">
        <!--</br>-->
        <p style="font-weight:bold;">Ingrese los litros en la columna LIMITE LTRS de la grilla</p>
        <div id="jqxgrid_bodegas" style="margin-left:20px; margin-top: 5px;">
        </div>
    </div>
    <div class="clear"></div>
    <hr style="margin-top:20px; margin-left: auto; margin-right: auto; height: 0; width: 60%;">
    <div id="check_datos" style="width: 100%;">
        <div class="elem elem_med_cond" style="width: 420px;float: left;clear: none!important;">
            <label class="der">Titular:</label>
            <div class="indent formtext">
                <input type="text" class="tip-right" title="titular" id="opeTitular" value=""  maxlength="64">
            </div>
        </div>
        <div class="elem elem_med_cond" style="width: 370px;float: left;clear: none!important;">
            <label class="der">Cuit:</label>
            <div class="indent formtext">
                <input type="text" title="Cuit" id="opeCuit" maxlength="100">
            </div>
        </div>
        <div class="elem elem_med_cond" style="width: 420px;float: left;clear: none!important;">
            <label class="der">N° Viñedo:</label>
            <div class="indent formtext">
                <input type="text" class="tip-right" title="numVinedo" id="numVinedo" value=""  maxlength="64">
            </div>
        </div>
        <div class="elem elem_med_cond" style="width: 370px;float: left;clear: none!important;">
            <label class="der">Litros Ofrecidos:</label>
            <div class="indent formtext">
                <input type="text" title="Listros Ofrecidos" id="litrosOfrecidos" maxlength="100">
            </div>
        </div>
        <div class="elem elem_med_cond" style="width: 420px;float: left;clear: none!important;">
            <label class="der">Hectareas Declaradas:</label>
            <div class="indent formtext">
                <input type="text" class="tip-right" title="Hectareas Declaradas" id="hectDeclaradas" value=""  maxlength="64">
            </div>
        </div>
        <div class="elem elem_med_cond" style="width: 370px;float: left;clear: none!important;">
            <label class="der">BGA Depositaria:</label>
            <div class="indent formtext">
                <input type="text" title="BGA Depositaria" id="bgaDep" maxlength="100">
            </div>
        </div>
        <div class="elem elem_med_cond" style="width: 420px;float: left;clear: none!important;">
            <label class="der">Dept. Bodega:</label>
            <div class="indent formtext">
                <input type="text" class="tip-right" title="Dept. Bodega" id="deptBodega" value=""  maxlength="64">
            </div>
        </div>
        <div class="elem elem_med_cond" style="width: 370px;float: left;clear: none!important;">
            <label class="der">N° INV(Bodega):</label>
            <div class="indent formtext">
                <input type="text" title="N INV Bodega" id="numINVBodega" maxlength="100">
            </div>
        </div>
        <div class="elem elem_med_cond" style="width: 420px;float: left;clear: none!important;">
            <label class="der">Telefono:</label>
            <div class="indent formtext">
                <input type="text" class="tip-right" title="telefono" id="opetelefono" value=""  maxlength="64">
            </div>
        </div>
        <div class="elem elem_med_cond" style="width: 370px;float: left;clear: none!important;">
            <label class="der">Correo Electronico:</label>
            <div class="indent formtext">
                <input type="text" title="correo electronico" id="opeCorreo" maxlength="100">
            </div>
        </div>
<!--        <div class="elem elem_med_cond">
            <label>Seleccionar Persona:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" id="tipoPersona" onchange="verPersona()" data-placeholder="Persona">
                    <option value="Humana">Humana</option>
                    <option value="Juridica">Juridica</option>
                </select>   
            </div>
    </div>-->
    <div class="elem elem_med_cond">
        <label>Seleccionar Persona:</label>
        <div class="indent">
            <select class="chzn-select medium-select select" id="tipoPersona" onchange="verPersona()" data-placeholder="Persona">
                <option value="Humana">Humana</option>
                <option value="Juridica">Jurídica</option>
            </select>   
        </div>
    </div>
    <table id="humana">
        <tr>
            <th class="numCheck" style="width: 5%;">N°</th>
            <th>DATOS</th>
            <th>OPCION</th>
        </tr>
        <?php foreach ($lst_checkHumana as $it) { ?>
            <tr class="op">
                <td class="numCheck"><?php echo $it['ID']; ?></td>
                <td><?php echo $it['DESCRIPCION']; ?></td>
                <td> <select class="opeOpcion">
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                        <option value="NC" selected="selected">N/C</option>
                    </select>
                </td>        
            </tr>
        <?php } ?>
    </table>
    <table id="juridica">
        <tr>
            <th class="numCheck">N°</th>
            <th>DATOS</th>
            <th>OPCION</th>
        </tr>
        <?php foreach ($lst_checkJuridica as $it) { ?>
            <tr class="op">
                <td class="numCheck"><?php echo $it['ID']; ?></td>
                <td><?php echo $it['DESCRIPCION']; ?></td>
                <td> <select class="opeOpcion">
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                        <option value="NC" selected="selected">N/C</option>
                    </select>
                </td>        
            </tr>
        <?php } ?>
    </table>
    <input id="send" name="send" type="submit" class="button-a blue send" style="margin-top: 25px;" value="Guardar">
    <input id="send_edit" name="send_edit" type="submit" class="button-a blue send_edit" style="margin-top: 25px;" value="Guardar Cambios">
</div>
</div>

<!--<div id="wpopup"></div>-->

