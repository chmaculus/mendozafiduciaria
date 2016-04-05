<ul class="toolbar">
    <li class="tb_atras" data-top="lis_editar"><div>Regresar</div></li>
    <li class="tb_ver" data-top='inicio'><div>Inicio</div></li>
    <li class="tb_search" data-top='search'><div>Buscar Cuit</div></li>
    <li class="tb_fil" data-top='nueva_f'><div>Nueva Factura</div></li>
    <li class="tb_save" data-top="lis_guardar_enviar"><div>Guardar Cliente</div></li>
    <li class="tb_save1" data-top="lis_guardar_fact"><div>Guardar Factura</div></li>
    <!--    <li class="tb_edi" data-top='edi'><div>Editar</div></li> -->
</ul>


<div class="elem elem_med_cond" style="margin-left:32px;">
    <label class="der">CUIT:</label>
    <div class="indent formtext" style="width: 231px;clear: none;float: left;margin-left: 115px!important;">
        <input style="width: 200px;" type="text" class="tip-right" title="Ingrese asunto" id="cuit_busqueda" value="" maxlength="11" >
    </div>
    <div class="elem elempie" style="float: left;clear: none;margin-left: 1px;position: relative;top: -16px;width: 164px;">
        <div class="indent">
            <div class="button-a blue consultar"><span>Consultar</span></div>
        </div>
    </div>
</div>

<div class="env_form">
    <div class="elem elem_med_cond">
        <label class="der">Nombre/Razón Social:</label>
        <div class="indent formtext">
            <input type="hidden" id="id_buscar">
            <input type="text" class="tip-right" title="Ingrese asunto" id="nombre" value="<?php echo isset($obj_req["REMITENTE"]) ? $obj_req["REMITENTE"] : "" ?>">
        </div>
    </div>

    <input type="hidden" id="provinciah" value="" />
    <input type="hidden" id="localidadh" value="" />

    <div class="elem elem_med">
        <label class="der">CUIT:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="CUIT" id="cuit" value="" maxlength="11"> 
        </div>
    </div>

    <div class="elem elem_med">
        <label class="der">CBU:</label>
        <div class="indent formtext">
            <input maxlength="22" type="text" class="validate[required] tip-right" title="" id="cbu" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" data-prompt-position="topLeft" <?php echo (isset($entidad['ID']) ? "readonly" : "" ) ?>>
        </div>
    </div>

    <?php if (isset($lst_condicioniva) && is_array($lst_condicioniva)): ?>
        <div class="elem elem_med">
            <label>Cond. Iva:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" id="condicioniva">
                    <option value="">Elegir Condicion Iva</option>
                    <?php foreach ($lst_condicioniva as $rs_iva): ?>
                        <option data-connection="<?php echo $rs_iva["ID"] ?>" value="<?php echo $rs_iva["ID"] ?>"><?php echo $rs_iva["CONDICION"] ?></option>
                    <?php endforeach; ?>
                </select>   
            </div>
        </div>
    <?php endif; ?>

    <?php if (is_array($lst_condicioniibb)): ?>
        <div class="elem elem_med">
            <label>Condicion IIBB:</label>
            <div class="indent">
                <select class=" chzn-select medium-select select" id="condicioniibb">
                    <option value="">Condicion IIBB</option>
                    <?php foreach ($lst_condicioniibb as $rs_iibb): ?>
                        <option data-connection="<?php echo $rs_iibb["ID"] ?>" value="<?php echo $rs_iibb["ID"] ?>"><?php echo $rs_iibb["CONDICION"] ?></option>
                    <?php endforeach; ?>
                </select>   
            </div>
        </div>
    <?php endif; ?>

    <div class="elem elem_med">
        <label class="der">Inscr. IIBB:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="insciibb" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" data-prompt-position="topLeft" <?php echo (isset($entidad['ID']) ? "readonly" : "" ) ?>>
        </div>
    </div>




    <div class="elem elem_med_cond">
        <label class="der">Domicilio:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="direccion" value="">
        </div>
    </div>


    <?php if (is_array($lst_provincias)): ?>
        <div class="elem elem_med" >
            <label>Provincia:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" id="provincia">
                    <option value="">Elegir Provincia</option>
                    <?php foreach ($lst_provincias as $rs_prov): ?>
                        <option data-connection="<?php echo $rs_prov["ID"] ?>" value="<?php echo $rs_prov["ID"] ?>"><?php echo $rs_prov["PROVINCIA"] ?></option>
                    <?php endforeach; ?>
                </select>   
            </div>
        </div>
    <?php endif; ?>

    <div class="elem elem_med">
        <label>Localidad:</label>
        <div class="indent" id="div_subrubro">
            <select class="chzn-select medium-select2 select" id="subrubro" data-prompt-position="topLeft">
                <option value="">Elegir Subrubro</option>
            </select>
        </div>
    </div>

    <div class="elem elem_med">
        <label class="der">Teléfono:</label>
        <div class="indent formtext">
            <input type="text" title="" id="telefono" value=""> 
        </div>
    </div>

    <div class="elem elem_med">
        <label class="der">Email:</label>
        <div class="indent formtext">
            <input type="text" title="" id="correo" value="">
        </div>
    </div>

    <div class="elem">
        <label class="ancho100">Observaciones:</label>
        <div class="formtext">
            <textarea  class="medium"  id="observacion" name="observacion" rows="5"></textarea>
        </div>
    </div>

    <!--<input id="send" name="send" type="submit" class="button-a blue send" value="Guardar">-->
    <!--<input id="send_cliente" name="send" type="submit" class="button-a blue send_cliente" value="Guardar">-->
    <input id="nuevafactura" name="nuevafactura" type="submit" class="button-a blue send" value="Nueva Factura" style="margin-right: 10px;">

</div>

<div class="nuevafact_form">
    <input type="hidden" id="idh" value="<?php echo isset($entidad["ID"]) ? $entidad["ID"] : ''; ?>" />
    <div class="elem elem_med_cond">
        <label class="der">N° Operatoria:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="numOperatoria" id="numOperatoria" value="" readonly maxlength="11">
        </div>
    </div>
    <div class="elem elem_med">
        <label class="der">Nro Viñedo:</label>
        <div class="indent formtext">
            <input maxlength="12" type="text" class="tip-right" title="" id="numVinedo" data-prompt-position="centerRight" value=""> 
        </div>
    </div>
    <div class="elem elem_med">
        <label class="der">Nro. RUT:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="numRut" data-prompt-position="centerRight" value=""> 
        </div>
    </div>
    <div class="elem elem_med_cond">
        <label class="der">CUIT:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="nombre" id="cuitform" value="" readonly maxlength="11">
        </div>
    </div>
    <div class="elem elem_med_cond">
        <label class="der">Nombre/Razón Social:</label>
        <div class="indent formtext">
            <input type="text" title="nombre" id="nombre2" readonly>
        </div>
    </div>
    <input type="hidden" id="provinciah" value="" />
    <input type="hidden" id="localidadh" value="" />
    <div class="elem elem_med">
        <label class="der">Nro Factura:</label>
        <div class="indent formtext">
            <input maxlength="12" type="text" class="tip-right" title="" id="numero" data-prompt-position="centerRight" value="<?php echo (isset($entidad['MONTO_SOLICITADO']) ? $entidad['MONTO_SOLICITADO'] : "" ) ?>" <?php echo (isset($entidad['ID']) ? "readonly" : "" ) ?>> 
        </div>
    </div>
    <div class="elem elem_med">
        <label class="der">Fecha:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="fecha" data-prompt-position="centerRight" value="<?php echo (isset($entidad['MONTO_SOLICITADO']) ? $entidad['MONTO_SOLICITADO'] : "" ) ?>" <?php echo (isset($entidad['ID']) ? "readonly" : "" ) ?>> 
        </div>
    </div>
    <div class="elem elem_med">
        <label class="der">CAI:</label>
        <div class="indent formtext">
            <input maxlength="14" type="text" class="validate[required] tip-right" title="Ingrese CAI" id="cai" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" data-prompt-position="topLeft" <?php echo (isset($entidad['ID']) ? "readonly" : "" ) ?>>
        </div>
    </div>
    <div class="elem elem_med">
        <label class="der">Fecha Vto:</label>
        <div class="indent formtext">
            <input type="text" class="validate[required] tip-right" title="Ingrese Destino" id="fechavto" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" data-prompt-position="topLeft" <?php echo (isset($entidad['ID']) ? "readonly" : "" ) ?>>
        </div>
    </div>
    <?php if (isset($lst_bodegas_vino) && is_array($lst_bodegas_vino)): ?>
        <div class="elem elem_med">
            <label>Bodega:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" id="bodega">
                    <option value="">Elegir Bodega</option>
                    <?php // foreach($lst_bodegas as $rs_iva): ?>
                    <?php foreach ($lst_bodegas_vino as $rs_iva): ?>
                        <option data-local="<?php echo $rs_iva["PROVINCIA"] ?>" data-connection="<?php echo $rs_iva["ID"] ?>" value="<?php echo $rs_iva["ID"] ?>"><?php echo $rs_iva["NOMBRE"] ?></option>
                    <?php endforeach; ?>
                </select>   
            </div>
        </div>
    <?php endif; ?>
    <div class="elem elem_med">
        <label class="der">Provincia:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="prov_bodega" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" readonly>
        </div>
    </div>
    <!--    <div class="elem elem_med">
                <label class="der">Departamento:</label>
                <div class="indent formtext">
                    <input type="text" class="tip-right" title="" id="dto_bodega" value="<?php // echo (isset($entidad['DESTINO'])? $entidad['DESTINO']:"" )   ?>" readonly>
                </div>
        </div>-->
    <div class="elem elem_med">
        <label class="der">Litros:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="ltros" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" >
        </div>
    </div>
    <div class="elem elem_med">
        <label class="der">Azucar:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="azucar" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" >
        </div>
    </div>
    <div class="elem elem_med">
        <label class="der">Precio:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="precio" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>">
        </div>
    </div>
    <div class="elem elem_med">
        <label class="der">Neto:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="neto" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" readonly>
        </div>
    </div>

    <div class="elem elem_med">
        <label class="der">Iva:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="iva" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" readonly>
        </div>
    </div>

    <div class="elem elem_med">
        <label class="der">Porcentaje Iva:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="porcentaje_iva">
        </div>
    </div>

    <div class="elem elem_med">
        <label class="der">Total:</label>
        <div class="indent formtext">
            <input type="text" class="tip-right" title="" id="total" value="<?php echo (isset($entidad['DESTINO']) ? $entidad['DESTINO'] : "" ) ?>" readonly>
        </div>
    </div>

    <?php if (isset($lst_formulas) && is_array($lst_formulas)): ?>
        <div class="elem elem_med">
            <label>Formula:</label>
            <div class="indent">
                <select class="chzn-select medium-select select" id="formula">
                    <option value="">Elegir Formula</option>
                    <?php foreach ($lst_formulas as $rs_form): ?>
                        <option value="<?php echo $rs_form["idFormula"] ?>"><?php echo "Fórmula" . $rs_form["idFormula"] ?></option>
                    <?php endforeach; ?>
                </select>   
            </div>
        </div>
    <?php endif; ?>

    <div class="elem">
        <label class="ancho100">Observaciones:</label>
        <div class="formtext">
            <textarea  class="medium"  id="observacion_fact" name="observacion_fact" rows="5"></textarea>
        </div>
    </div>

    <br><br><br>
    <div style="margin-top:10px;" class="clear"></div>
    <div class="elem elem_med">
        <label>Cambio de Titularidad:</label>
        <div class="indent formtext">
            <input type="checkbox" id="cambio_titularidad" name="cambio_titularidad" value="1"/> 
        </div>
    </div>
    <br><br>
    <br><br>
    <div id="titularidadHistorial">
    <label>Historial Cambio de Titularidad:</label>
    <div id="jqxgridtitularidad">
    </div>
    </div>
    <input id="send" name="send" type="submit" class="button-a blue send" value="Guardar">
    <input id="nuevafactura" name="nuevafactura" type="submit" class="button-a blue send" value="Nueva Factura" style="margin-right: 10px;">

</div>

<div id="wpopup"></div>

