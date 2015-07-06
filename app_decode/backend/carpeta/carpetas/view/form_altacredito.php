<div class="content-formchk frm_altadecredito">
        <input type="hidden" id="id_creditoh" value="<?php echo isset($obj_credito["ID"])?$obj_credito["ID"]:"0" ?>" />
        <input type="hidden" id="id_interes_vtoh" value="<?php echo isset($obj_credito["INTERES_VTO"])?convertirFecha($obj_credito["INTERES_VTO"]):"" ?>" />
        <input type="hidden" id="id_capital_vtoh" value="<?php echo isset($obj_credito["CAPITAL_VTO"])?convertirFecha($obj_credito["CAPITAL_VTO"]):"" ?>" />
        
        <input type="hidden" id="interes_periodoh" value="<?php echo isset($obj_credito["INTERES_PERIODO"])?$obj_credito["INTERES_PERIODO"]:"" ?>" />
        <input type="hidden" id="interes_capitalh" value="<?php echo isset($obj_credito["INTERES_CAPITAL"])?$obj_credito["INTERES_CAPITAL"]:"" ?>" />
    
        <div class="title">Alta de Credito</div>
        <div class="elem elem_med_alta">
            <label>Titular:</label>
            <div class="indent formtext">
                <input type="text" class=""  readonly="readonly" id="alta_titular" value="<?php echo isset($nom_clientes)?$nom_clientes:"" ?>">
            </div>
        </div>

        <div class="elem elem_mealta_int_vtod_ac">
            <label>Cuit:</label>
            <div class="indent formtext" style="margin-left: 130px!important;">
                <input type="text" class=""  readonly="readonly" id="alta_cuit" value="<?php echo isset($cuit_clientes)?$cuit_clientes:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_alta">
            <label>Línea de Crédito:</label>
            <div class="indent formtext">
                <input type="text" class=""  readonly="readonly" id="alta_lcredito" value="<?php echo isset($cad_operatoria)?$cad_operatoria:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_ac">
            <label>Condic. Iva:</label>
            <div class="indent formtext">
                <input type="text" class=""  readonly="readonly" id="alta_civa" value="<?php echo isset($cad_civa)?$cad_civa:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_alta">
            <label>Destino del Proyecto:</label>
            <div class="indent formtext">
                <input type="text" class=""  readonly="readonly" id="alta_dest" value="<?php echo isset($cad_obj["cad_obj"][0]["DESTINO"])?$cad_obj["cad_obj"][0]["DESTINO"]:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_ac">
            <label>Legajo Nº:</label>
            <div class="indent formtext">
                <input type="text" class=""  readonly="readonly" id="alta_legajo" value="<?php echo isset($cad_obj["cad_obj"][0]["CODIGO"])?$cad_obj["cad_obj"][0]["CODIGO"]:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_alta">
            <label>Domicilio Legal:</label>
            <div class="indent formtext">
                <input type="text" class=""  readonly="readonly" id="alta_titular" value="">
            </div>
        </div>

        <div class="elem elem_med_ac">
            <label>Fecha de Ingreso:</label>
            <div class="indent formtext">
                <input type="text" class=""  readonly="readonly" id="alta_domlegal" value="">
            </div>
        </div>
        <?php 
            //print_r($cad_obj);
        ?>
        <div class="elem elem_med_alta save">
            <label>Domicilio del Proyecto:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido" id="alta_domproy" value="<?php echo isset($obj_credito["DOM_PROY"])?$obj_credito["DOM_PROY"]:"" ?>" data-campo="DOM_PROY">
            </div>
        </div>

        <div class="elem elem_med_ac">
            <label>Acta de Comité:</label>
            <div class="indent formtext">
                <input type="text" class="" title="Ingrese Acta" readonly="readonly" id="alta_nacta" value="">
            </div>
        </div>

        <div class="elem elem_med_alta">
            <label>Teléfono y Email:</label>
            <div class="indent formtext">
                <input type="text" class=""  readonly="readonly" id="alta_telemail" value="<?php echo isset($cad_email)?$cad_email:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_ac">
            <label>Fec del Acta:</label>
            <div class="indent formtext">
                <input type="text" class="" title="Fecha Acta" readonly="readonly" id="alta_facta" value="">
            </div>
        </div>

        <div class="elem elem_med_alta save">
            <label>Actividad del Titular:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido"  id="alta_actividadtitular" value="<?php echo isset($obj_credito["ACTIVIDAD"])?$obj_credito["ACTIVIDAD"]:"" ?>" data-campo="ACTIVIDAD">
            </div>
        </div>

        <div class="elem elem_med_ac">
            <label>Fec del Contrato:</label>
            <div class="indent formtext">
                <input type="text" class=""  readonly="readonly" id="alta_cffirma" value="">
            </div>
        </div>

        <div class="elem elem_med_alta">
            <label>Departamento:</label>
            <div class="indent formtext">
                <input type="text" class="" title="Ingrese Departamento" readonly="readonly" id="alta_depa" value="<?php echo isset($cad_obj["cad_obj"][0]["DEPARTAMENTO"])?$cad_obj["cad_obj"][0]["DEPARTAMENTO"]:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_alta">
            <label>INVERSIONES DEL PROYECTO:</label>
        </div>

        <div class="elem elem_med_alta save">
            <label>Crédito Mendoza Fid.:</label>
            <div class="indent formtext">
                <input data-campo="MONTO_CREDITO" type="text" class=""  readonly="readonly" id="alta_cmaprob" value="">
            </div>
        </div>

        <div class="elem elem_med_ac save">
            <label>%:</label>
            <div class="indent formtext">
                <input data-campo="MONTO_CREDITO_POR" type="text" readonly="readonly" class="" title="Ingrese" id="alta_por_cmf" value="<?php echo isset($obj_credito["MONTO_CREDITO_POR"])?$obj_credito["MONTO_CREDITO_POR"]:"100.00" ?>">
            </div>
        </div>

        <div class="elem elem_med_alta save">
            <label>Aporte Fondos Propios:</label>
            <div class="indent formtext">
                <input data-campo="MONTO_APORTE" type="text" class="alta_requerido"  id="alta_propios" value="<?php echo isset($obj_credito["MONTO_APORTE"])?$obj_credito["MONTO_APORTE"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac save">
            <label>%:</label>
            <div class="indent formtext">
                <input data-campo="MONTO_APORTE_POR" type="text" class="" readonly="readonly" id="alta_por_propios" value="<?php echo isset($obj_credito["MONTO_APORTE_POR"])?$obj_credito["MONTO_APORTE_POR"]:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_alta save">
            <label>Otros financiamientos:</label>
            <div class="indent formtext">
                <input data-campo="MONTO_OTRO" type="text" class="alta_requerido"  id="alta_otros" value="<?php echo isset($obj_credito["MONTO_OTRO"])?$obj_credito["MONTO_OTRO"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac save">
            <label>%:</label>
            <div class="indent formtext">
                <input data-campo="MONTO_OTRO_POR" type="text" class="" readonly="readonly" id="alta_por_otros" value="<?php echo isset($obj_credito["MONTO_OTRO_POR"])?$obj_credito["MONTO_OTRO_POR"]:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_alta save">
            <label>Monto Total:</label>
            <div class="indent formtext">
                <input data-campo="MONTO_TOTAL" type="text" readonly="readonly" class="alta_requerido"  id="alta_total" value="<?php echo isset($obj_credito["MONTO_TOTAL"])?$obj_credito["MONTO_TOTAL"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac save">
            <label>%:</label>
            <div class="indent formtext">
                <input data-campo="MONTO_TOTAL_POR" type="text" class="" readonly="readonly" id="alta_por_total" value="<?php echo isset($obj_credito["MONTO_TOTAL_POR"])?$obj_credito["MONTO_TOTAL_POR"]:"100.00" ?>">
            </div>
        </div>
        
        

        <div class="elem elem_med_alta">
            <label>CRONOGRAMA ESTIMADO DE DESEMBOLSOS:</label>
        </div>
        
               
        <div class="elem elem_med_alta savedes">
            <label>Descripción:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_obs"  id="alta_desem_desc1" value="<?php echo isset($obj_desembolsos[0]["OBS"])?$obj_desembolsos[0]["OBS"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac savedes">
            <label>Monto:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_monto"  id="alta_desem1" value="<?php echo isset($obj_desembolsos[0]["MONTO"])?$obj_desembolsos[0]["MONTO"]:"" ?>">
            </div>
        </div>

        
        <div class="elem elem_med_alta savedes">
            <label>Descripción:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_obs"  id="alta_desem_desc2" value="<?php echo isset($obj_desembolsos[1]["OBS"])?$obj_desembolsos[1]["OBS"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac savedes">
            <label>Monto:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_monto"  id="alta_desem2" value="<?php echo isset($obj_desembolsos[1]["MONTO"])?$obj_desembolsos[1]["MONTO"]:"" ?>">
            </div>
        </div>

        <div class="elem elem_med_alta savedes">
            <label>Descripción:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_obs"  id="alta_desem_desc3" value="<?php echo isset($obj_desembolsos[2]["OBS"])?$obj_desembolsos[2]["OBS"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac savedes">
            <label>Monto:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_monto"  id="alta_desem3" value="<?php echo isset($obj_desembolsos[2]["MONTO"])?$obj_desembolsos[2]["MONTO"]:"" ?>">
            </div>
        </div>
        
        <div class="elem elem_med_alta savedes">
            <label>Descripción:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_obs"  id="alta_desem_desc4" value="<?php echo isset($obj_desembolsos[3]["OBS"])?$obj_desembolsos[3]["OBS"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac savedes">
            <label>Monto:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_monto"  id="alta_desem4" value="<?php echo isset($obj_desembolsos[3]["MONTO"])?$obj_desembolsos[3]["MONTO"]:"" ?>">
            </div>
        </div>
        
        <div class="elem elem_med_alta savedes">
            <label>Descripción:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_obs"  id="alta_desem_desc5" value="<?php echo isset($obj_desembolsos[4]["OBS"])?$obj_desembolsos[4]["OBS"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac savedes">
            <label>Monto:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_monto"  id="alta_desem5" value="<?php echo isset($obj_desembolsos[4]["MONTO"])?$obj_desembolsos[4]["MONTO"]:"" ?>">
            </div>
        </div>
        
        <div class="elem elem_med_alta savedes">
            <label>Descripción:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_obs"  id="alta_desem_desc6" value="<?php echo isset($obj_desembolsos[5]["OBS"])?$obj_desembolsos[5]["OBS"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac savedes">
            <label>Monto:</label>
            <div class="indent formtext">
                <input type="text" class="alta_requerido des_monto"  id="alta_desem6" value="<?php echo isset($obj_desembolsos[5]["MONTO"])?$obj_desembolsos[5]["MONTO"]:"" ?>">
            </div>
        </div>
        
        
        
        
        <div class="elem elem_med_alta save">
            <label>T.N.A. Compensatorios:</label>
            <div class="indent formtext">
                <?php if(isset($obj_credito["T_COMPENSATORIO"])): ?>
                    <input data-campo="T_COMPENSATORIO" type="text" class="" title="Ingrese a" id="alta_t_compensatorio" value="<?php echo isset($obj_credito["T_COMPENSATORIO"])?$obj_credito["T_COMPENSATORIO"]:"" ?>">
                <?php else: ?>
                    <input data-campo="T_COMPENSATORIO" type="text" class="" title="Ingrese b" id="alta_t_compensatorio" value="<?php echo isset($cad_obj["cad_obj"][0]["TASA_INTERES_COMPENSATORIA"])?$cad_obj["cad_obj"][0]["TASA_INTERES_COMPENSATORIA"]:"" ?>">
                <?php endif; ?>
            </div>
        </div>
        <div class="elem elem_med_ac save">
            <label>Bonif. de Tasa:</label>
            <div class="indent formtext">
                <?php if(isset($obj_credito["T_BONIFICACION"])): ?>
                    <input data-campo="T_BONIFICACION" type="text" class="" title="Ingrese a" id="alta_t_bonif" value="<?php echo isset($obj_credito["T_BONIFICACION"])?$obj_credito["T_BONIFICACION"]:"" ?>">
                <?php else: ?>
                    <input data-campo="T_BONIFICACION" type="text" class="" title="Ingrese b" id="alta_t_bonif" value="<?php echo isset($cad_obj["cad_obj"][0]["TASA_SUBSIDIADA"])?$cad_obj["cad_obj"][0]["TASA_SUBSIDIADA"]:"" ?>">
                <?php endif; ?>
            </div>
        </div>
        
        <div class="elem elem_med_alta save">
            <label>T.N.A. Moratorios:</label>
            <div class="indent formtext">
                <?php if(isset($obj_credito["T_MORATORIO"])): ?>
                    <input data-campo="T_MORATORIO" type="text" class="" title="Ingrese a" id="alta_t_moratorio" value="<?php echo isset($obj_credito["T_MORATORIO"])?$obj_credito["T_MORATORIO"]:"" ?>">
                <?php else: ?>
                    <input data-campo="T_MORATORIO" type="text" class="" title="Ingrese b" id="alta_t_moratorio" value="<?php echo isset($cad_obj["cad_obj"][0]["TASA_INTERES_MORATORIA"])?$cad_obj["cad_obj"][0]["TASA_INTERES_MORATORIA"]:"" ?>">
                <?php endif; ?>
            </div>
        </div>
        <div class="elem elem_med_ac save">
            <label>T.N.A. Punitorios:</label>
            <div class="indent formtext">
                <?php if(isset($obj_credito["T_PUNITORIO"])): ?>
                    <input data-campo="T_PUNITORIO" type="text" class="" title="Ingrese a" id="alta_t_punitorio" value="<?php echo isset($obj_credito["T_PUNITORIO"])?$obj_credito["T_PUNITORIO"]:"" ?>">
                <?php else: ?>
                    <input data-campo="T_PUNITORIO" type="text" class="" title="Ingrese b" id="alta_t_punitorio" value="<?php echo isset($cad_obj["cad_obj"][0]["TASA_INTERES_POR_PUNITORIOS"])?$cad_obj["cad_obj"][0]["TASA_INTERES_POR_PUNITORIOS"]:"" ?>">
                <?php endif; ?>
            </div>
        </div>
        
        
        <div class="elem elem_med_alta">
            <label>AMORTIZACIÓN DE INTERESES:</label>
        </div>
        
        <div class="elem elem_med_alta save">
            <label>Cantidad Cuotas:</label>
            <div class="indent formtext">
                <input data-campo="INTERES_CUOTAS" type="text" class="alta_requerido"  id="alta_int_coutas" value="<?php echo isset($obj_credito["INTERES_CUOTAS"])?$obj_credito["INTERES_CUOTAS"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac save">
            <label>Vto. 1º Cuota:</label>
            <div class="indent formtext">
                <input maxlength="10" data-campo="INTERES_VTO" type="text" class="alta_requerido"  id="alta_int_vto">
            </div>
        </div>
        
        <!--
        <div class="elem elem_med_alta save">
            <label>Periodicidad:</label>
            <div class="indent formtext">
                <input data-campo="INTERES_PERIODO" type="text" class="alta_requerido"  id="alta_int_periodo" value="<?php echo isset($obj_credito["INTERES_PERIODO"])?$obj_credito["INTERES_PERIODO"]:"" ?>">
            </div>
        </div>
        -->
        
        <div class="elem periodicidaddiv" >
            <label>Periodicidad:</label>
            <div class="indent">
            <select class="chzn-select medium-select_altacredito select" id="alta_int_periodo">
                <option value="1">Quincenal</option>
                <option value="2">Mensual</option>
                <option value="3">Trimestral</option>
                <option value="4">Semestral</option>
                <option value="5">Anual</option>
                <option value="6">bianual</option>
            </select>   
            </div>
        </div>
        
        
        <div class="elem elem_med_alta">
            <label>AMORTIZACIÓN DE CAPITAL:</label>
        </div>
        
        <div class="elem elem_med_alta save">
            <label>Cantidad Cuotas:</label>
            <div class="indent formtext">
                <input data-campo="CAPITAL_CUOTAS" type="text" class="alta_requerido"  id="alta_cap_coutas" value="<?php echo isset($obj_credito["CAPITAL_CUOTAS"])?$obj_credito["CAPITAL_CUOTAS"]:"" ?>">
            </div>
        </div>
        <div class="elem elem_med_ac save">
            <label>Vto. 1º Cuota:</label>
            <div class="indent formtext">
                <input maxlength="10" data-campo="CAPITAL_VTO" type="text" class="alta_requerido"  id="alta_cap_vto">
            </div>
        </div>
        
        <!--
        <div class="elem elem_med_alta save">
            <label>Periodicidad:</label>
            <div class="indent formtext">
                <input data-campo="CAPITAL_PERIODO" type="text" class="alta_requerido"  id="alta_cap_periodo" value="<?php echo isset($obj_credito["CAPITAL_PERIODO"])?$obj_credito["CAPITAL_PERIODO"]:"" ?>">
            </div>
        </div>
        -->
        
        <?php 
        $periodicidad = 0;
        $sl1 = $sl2 = $sl3 = $sl4 = $sl5 = $sl6 = '';
        switch ($obj_credito['PERIODICIDAD_TASA']) {
            case 15:
                $sl1 = ' selected="selected"';
                break;
            case 30:
                $sl2 = ' selected="selected"';
                break;
            case 90:
                $sl3 = ' selected="selected"';
                break;
            case 180:
                $sl4 = ' selected="selected"';
                break;
            case 360:
            case 365:
                $sl5 = ' selected="selected"';
                break;
            case 720:
            case 730:
                $sl6 = ' selected="selected"';
                break;
        }
        
        ?>
        
        <div class="elem periodicidaddiv" >
            <label>Periodicidad:</label>
            <div class="indent">
            <select class="chzn-select medium-select_altacredito select" id="alta_cap_periodo">
                <option value="1"<?=$sl1?>>Quincenal</option>
                <option value="2"<?=$sl2?>>Mensual</option>
                <option value="3"<?=$sl3?>>Trimestral</option>
                <option value="4"<?=$sl4?>>Semestral</option>
                <option value="5"<?=$sl5?>>Anual</option>
                <option value="6"<?=$sl6?>>bianual</option>
            </select>   
            </div>
        </div>
        
      
        
        <?php //echo isset($obj_credito["ID"])?$obj_credito["ID"]:"0" ?>
        
        <div class="elem elempie">
            <div class="indent">
                
                <div class="button-a blue send_altacredito"><span>Guardar Formulario</span></div>
                <div class="button-a blue alta_de_credito"><span>Alta de Crédito</span></div>
                
            </div>
        </div>

</div>

<div id="div_altadecredito"></div>