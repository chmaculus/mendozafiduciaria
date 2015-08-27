<!--Form-->
 <div class="content-form">
    <form method="post" enctype="multipart/form-data" id="customForm" >
    <div class="grid-1" id="frmagregar">
       <div class="title-grid"><div id="label_action">Agregar</div>Carpeta</div>
       <div class="content-gird">
       <div class="form">

            <input type="hidden" id="idh" value="<?php echo isset($entidad["ID"])?$entidad["ID"]:''; ?>" />
            <input type="hidden" id="val_ok" value="0" />
            <input type="hidden" id="val_clientesh" value="<?php echo $cad ?>" />
            <input type="hidden" id="provinciah" value="<?php echo isset($entidad["ID_PROVINCIA"])?$entidad["ID_PROVINCIA"]:"" ?>" />
            <input type="hidden" id="localidadh" value="<?php echo isset($entidad["ID_DEPARTAMENTO"])?$entidad["ID_DEPARTAMENTO"]:"" ?>" />
            <input type="hidden" id="fideicomisoh" value="<?php echo isset($entidad["ID_FIDEICOMISO"])?$entidad["ID_FIDEICOMISO"]:"" ?>" />
            <input type="hidden" id="operatoriah" value="<?php echo isset($entidad["ID_OPERATORIA"])?$entidad["ID_OPERATORIA"]:"" ?>" />
            
            <input type="hidden" id="operatoriah1" value="<?php echo isset($entidad["ID_TIPO_OPERATORIA"])?$entidad["ID_TIPO_OPERATORIA"]:"" ?>" />
            <input type="hidden" id="localidadh1" value="<?php echo isset($entidad["ID_DEPARTAMENTO"])?$entidad["ID_DEPARTAMENTO"]:"" ?>" />
            <input type="hidden" id="comite_facta_h" value="<?php echo isset($acta_comite_facta)?$acta_comite_facta:"" ?>" />
            <input type="hidden" id="contratof_h" value="<?php echo isset($contrato_f)?$contrato_f:"" ?>" />
            
            <input type="hidden" id="minuta_fentregam_h" value="<?php echo isset($minuta_fentregam)?$minuta_fentregam:"" ?>" />
            <input type="hidden" id="minuta_fdevm_h" value="<?php echo isset($minuta_fdevm)?$minuta_fdevm:"" ?>" />
            <input type="hidden" id="escribanoh" value="<?php echo isset($minuta_escribano)?$minuta_escribano:"" ?>" />
            
            
            <input type="hidden" id="legales_et4" value="<?php echo isset($legales_et4)?$legales_et4:"" ?>" />
            
            

            <input type="hidden" id="desh" value="<?php echo isset($entidad["DESEMBOLSOS"])?$entidad["DESEMBOLSOS"]:"" ?>" />
            <input type="hidden" id="devh" value="<?php echo isset($entidad["DEVOLUCIONES"])?$entidad["DEVOLUCIONES"]:"" ?>" />
            <input type="hidden" id="perh" value="<?php echo isset($entidad["PERIODICIDAD"])?$entidad["PERIODICIDAD"]:"" ?>" />
            
            <input type="hidden" id="jefeope_h" value="<?php echo isset($jefe_ope)?$jefe_ope:"" ?>" />
            
            <input type="hidden" id="a_ent" value="<?php echo $_array_checklist ?>" />
            <script type='text/javascript'>
            <?php
            
                    echo "var _array_checklist = ". $_array_checklist . ";\n";
                    echo "var _array_checklist_comite = ". $_array_checklist_comite . ";\n";
                    echo "var _array_checklist_desembolso = ". $_array_checklist_desembolso . ";\n";
                    echo "var _array_permisos_etapas = ". $_permisos_etapas . ";\n";
                    //echo "var _legales_et4 = ". $legales_et4 . ";\n";
                    
                    if(isset($obj_js)):
                        echo "var _array_obj = ". $obj_js . ";\n";
                    endif;
                    if(isset($_arr_operatoria_js)):
                        echo "var _array_operatoria = ". $_arr_operatoria_js . ";\n";
                    endif;
                    
                    
                    
            ?>
            </script>

            <div class="elem elem_med">
                <label>Codigo:</label>
                <div class="indent formtext">
                    <input type="text" title="Código" id="codigo" value="<?php echo $_ultimo_valor ?>" readonly >
                </div>
            </div>

            <div class="elem elem_med" >
                <label></label>
                <div class="indent" style="visibility: hidden">
                    <input type="text" />
                </div>
            </div>

            <?php if(is_array($lst_fideicomisos)): ?>
            <div class="elem elem_med" >
                <label>Fideicomiso:</label>
                <div class="indent">
                    <select class="validate[required] chzn-select medium-select select" id="fideicomiso" data-prompt-position="topLeft">
                        <option value="">Elegir Fideicomiso</option>
                        <?php foreach($lst_fideicomisos as $rs_fid): ?>
                        <option data-connection="<?php echo $rs_fid["ID"] ?>" value="<?php echo $rs_fid["ID"] ?>"><?php echo $rs_fid["NOMBRE"] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>

            <div class="elem elem_med">
                <label>Operatoria:</label>
                <div class="indent" id="div_operatoria">
                 <select class="chzn-select medium-select2 select" id="operatoria">
                     <option value="">Elegir Operatoria</option>
                 </select>
                </div>
            </div>

            <?php if(is_array($lst_provincias)): ?>
            <div class="elem elem_med" >
                <label>Provincia:</label>
                <div class="indent">
                <select class="validate[required] chzn-select medium-select select" id="provincia" data-prompt-position="topLeft">
                    <option value="">Elegir Provincia</option>
                    <?php foreach($lst_provincias as $rs_prov): ?>
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
                    <label class="der">Monto Solicitado:</label>
                    <div class="indent formtext">
                        <input type="text" class="tip-right" title="Ingrese Monto Solicitado" id="montosol" data-prompt-position="centerRight" value="<?php echo (isset($entidad['MONTO_SOLICITADO'])? $entidad['MONTO_SOLICITADO']:"" ) ?>" <?php echo (isset($entidad['ID'])? "readonly":"" ) ?>> 
                    </div>
            </div>

            <div class="elem elem_med">
                    <label class="der">Destino:</label>
                    <div class="indent formtext">
                        <input type="text" class="validate[required] tip-right" title="Ingrese Destino" id="destino" value="<?php echo (isset($entidad['DESTINO'])? $entidad['DESTINO']:"" ) ?>" data-prompt-position="topLeft" <?php echo (isset($entidad['ID'])? "readonly":"" ) ?>>
                    </div>
            </div>

            <div class="elem">
                <label>Postulante/s:</label>
                <?php if ($lst_clientes): ?>
                <div class="indent">
                <select class="validate[required] chzn-select medium-select5 select" multiple data-placeholder="Selecciones Postulante/s" id="clientes">
                    <?php foreach($lst_clientes as $rs_ent): ?>
                    <option value="<?php echo $rs_ent["ID"] ?>"><?php echo $rs_ent["RAZON_SOCIAL"] ?> - <?php echo $rs_ent["CUIT"] ?></option>
                    <?php endforeach; ?>
                </select> 
                </div>
                <!--
                <div class="agregarc">+</div>
                -->
                <?php endif; ?>
            </div>


            <div class="elem elem_med">
                    <label class="der">En Cartera de:</label>
                    <div class="indent formtext">
                        <input type="text" readonly title="" id="carteradetxt" value="<?php echo (isset($carterade)? $carterade:"--" ) ?>"> 
                    </div>
            </div>

            <div class="elem elem_med">
                    <label class="der">Etapa Actual:</label>
                    <div class="indent formtext">
                        <input type="text" readonly title="" id="etapaactual" value="<?php echo (isset($etapaactual)? $etapaactual:"Inscripción" ) ?>">
                    </div>
            </div>
            
            <!--
            <div class="elem elem_med">
                    <label class="der">Proceso:</label>
                    <div class="indent formtext">
                        <input type="text" readonly title="" id="id_proceso" value="<?php echo (isset($id_proceso)? $id_proceso:"Normal" ) ?>">
                    </div>
            </div>
            -->
            
            <div class="clear"> </div>
         </div>
       </div>
    </div>
    <!--Form end-->
    
    <?php if( isset($entidad['ID_PROCESO']) && $entidad['ID_PROCESO']=='1' ): ?>
    <div id="vtab">
        <ul>
            <li eta="1" class="checklist <?php if (isset($array_ope_etapas[1]) && $array_ope_etapas[1]==1) echo "etapa_ready" ?>"><span>Inscripción</span></li>
            <li eta="2" class="inicial <?php if (isset($array_ope_etapas[2]) && $array_ope_etapas[2]==1) echo "etapa_ready" ?>"><span>Control Inicial</span></li>
            <li eta="7" class="garantia"><span>Garantías</span></li>
            <li eta="3" class="sectores <?php if ((isset($array_ope_etapas[4]) && isset($array_ope_etapas[5]) && isset($array_ope_etapas[6]) ) && $array_ope_etapas[4]>=1 && $array_ope_etapas[5]>=1 && $array_ope_etapas[6]>=1) echo "etapa_ready" ?>" ><span>Sectores de Análisis</span></li>
            <li eta="8" class="elevacion <?php if (isset($array_ope_etapas[8]) && $array_ope_etapas[8]==1) echo "etapa_ready" ?>"><span>Elevación</span></li>
            <li eta="9" class="comite <?php if (isset($array_ope_etapas[9]) && $array_ope_etapas[9]==1) echo "etapa_ready" ?>"><span>Comité</span></li>
            <li eta="10" class="c_contrato <?php if (isset($array_ope_etapas[10]) && $array_ope_etapas[10]==1) echo "etapa_ready" ?>"><span>Conf. Contrato</span></li>
            <li eta="11" class="contrato <?php if (isset($array_ope_etapas[11]) && $array_ope_etapas[11]==1) echo "etapa_ready" ?>"><span>Firma Contrato</span></li>
            <li eta="12" class="altacredito <?php if (isset($array_ope_etapas[12]) && $array_ope_etapas[12]==1) echo "etapa_ready" ?>"><span>Alta de Crédito</span></li>
            <li eta="13" class="desembolsos <?php if (isset($array_ope_etapas[13]) && $array_ope_etapas[13]==1) echo "etapa_ready" ?>"><span>Desembolsos</span></li>
        </ul>
        
        <div class="vtabinfo" data-etapa="1"><!-- etapa inscripcion  -->
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("1",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Seleccione alguna Operatoria.</p></div>
            <div style="float: left;" id="listbox"></div>
            <div class="elem">
                    <label class="ancho100">Observación:</label>
                    <div class="formtext">
                        <textarea  class="medium_txtarea"  id="obs_checlist" name="obs_checlist" rows="5"><?php echo (isset($array_obs['CHECKLIST']) ? $array_obs['CHECKLIST']:""); ?></textarea>
                    </div>
            </div>

            <p class="div_chk" id="div_chk_checklist">
                <input type="checkbox" id="chk_checklist" name="chk_checklist" value="2" class="styled" <?php echo (isset($array_chk['CHECKLIST']) && $array_chk['CHECKLIST']==1 ? 'checked="checked"':'') ?>/> <label for="chk_checklist">Dar conformidad</label>
            </p>
            
            <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
        
        
        <div class="vtabinfo" data-etapa="2"><!-- etapa inscripcion  -->
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            
            <?php if (in_array("2",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
            
            <div id="listbox_deudas"></div>
            
            <p class="div_chk div_chk_deudas">
                <input type="checkbox" id="chk_cinicial" name="chk_cinicial" value="2" class="styled" <?php echo (isset($array_chk['CINICIAL']) && $array_chk['CINICIAL']==1 ? 'checked="checked"':'') ?>/> <label for="chk_cinicial">Dar conformidad</label>
            </p>
            
            
            
            <div class="elem">
                    <label class="ancho100">Observación:</label>
                    <div class="formtext">
                        <textarea  class="medium_txtarea"  id="obs_cinicial" name="obs_cinicial" rows="5"><?php echo (isset($array_obs['CINICIAL']) ? $array_obs['CINICIAL']:""); ?></textarea>
                    </div>
            </div>
            
            <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
            
        </div>
        
        <div class="vtabinfo" data-etapa="7">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("6",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
            
            <div class="titulo_condprev">Garantias</div>
            <div data-gridname="jqxgrid_garantias" class="refresgrid">Refresh Grid</div>
            <div id="jqxgrid_garantias"></div>
            
            <div class="suma_aportes">
                <span class="t1">En Evaluación :</span>$<span id="suma_aporte_1"></span>
                <span class="t1">Aprobadas :</span>$<span id="suma_aporte_5"></span>
                <span class="t1">Constituidas :</span>$<span id="suma_aporte_6"></span>
            </div>
            
            
            
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
            
            
            
            
        </div>
        
        <div class="vtabinfo" data-etapa="3">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if ( in_array("4",$permisos_etapas) or in_array("5",$permisos_etapas) or in_array("6",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin'  or 1): ?>
            <div class="grid-1 tab" id="tabs">
                <div class="title-grid tabs">
                    <ul class="tabNavigation">
                        <?php if (in_array("4",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                        <li><a href="#tabs-1"><img width="20" src="general/css/images/32x32/legales.png" borde=""/>Legales</a></li>
                        <?php endif; ?>
                        <?php if (in_array("5",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                        <li><a href="#tabs-2"><img width="20" src="general/css/images/32x32/casa.png" borde=""/>Patrimoniales</a></li>
                        <?php endif; ?>
                        <?php if (in_array("6",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                        <li><a href="#tabs-3"><img width="20" src="general/css/images/32x32/tecnico.png" borde=""/>Técnicos</a></li>
                        <?php endif; ?>
                        
                        <?php //if( (isset($obj_js) && ( isset($entidad["CARTERADE"]) && ($entidad["CARTERADE"] == $_SESSION["USERADM"]) ) )|| $_SESSION["USER_USERNAME"]=='admin'): ?>
                    </ul>
                </div>
                <div class="content-gird">
                    <?php if (in_array("4",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                    <div id="tabs-1" data-etapab="4" class="tab_b">
                        <p class="div_chk chk_opera">
                            <input type="checkbox" id="chk_legales" name="chk_legales" value="2" class="styled" <?php echo (isset($array_chk['LEGALES']) && $array_chk['LEGALES']==1 ? 'checked="checked"':'') ?>/> <label for="chk_legales">Dar conformidad</label>
                        </p>
                        <p class="div_chk chk_opera">
                            <input type="checkbox" id="chk_legales_nc" name="chk_legales_nc" value="2" class="styled" <?php echo (isset($array_chk['LEGALES']) && $array_chk['LEGALES']==2 ? 'checked="checked"':'') ?>/> <label for="chk_legales_nc">No Corresponde</label>
                        </p>
                        <div class="elem">
                                <label class="ancho100">Observaciones:</label>
                                <div class="formtext">
                                    <textarea  class="medium"  id="obs_legales" name="obs_legales" rows="5"><?php echo (isset($array_obs['LEGALES']) ? $array_obs['LEGALES']:""); ?></textarea>
                                </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (in_array("5",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                    <div id="tabs-2" data-etapab="5" class="tab_b">
                        <p class="div_chk chk_opera">
                            <input type="checkbox" name="chk_patrimoniales" id="chk_patrimoniales" value="2" class="styled" <?php echo (isset($array_chk['PATRIMONIAL']) && $array_chk['PATRIMONIAL']==1 ? 'checked="checked"':'') ?>/> <label for="chk_patrimoniales">Dar conformidad</label>
                        </p>
                        <p class="div_chk chk_opera">
                            <input type="checkbox" id="chk_patrimoniales_nc" name="chk_patrimoniales_nc" value="2" class="styled" <?php echo (isset($array_chk['PATRIMONIAL']) && $array_chk['PATRIMONIAL']==2 ? 'checked="checked"':'') ?>/> <label for="chk_patrimoniales_nc">No Corresponde</label>
                        </p>
                        <div class="elem">
                                <label class="ancho100">Observaciones:</label>
                                <div class="formtext">
                                    <textarea  class="medium"  id="obs_patrimoniales" name="obs_patrimoniales" rows="5"><?php echo (isset($array_obs['PATRIMONIAL']) ? $array_obs['PATRIMONIAL']:""); ?></textarea>
                                </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (in_array("6",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                    <div id="tabs-3"  data-etapab="6" class="tab_b">
                        <p class="div_chk chk_opera">
                            <input type="checkbox" id="chk_tecnicos" name="chk_tecnicos" value="2" class="styled" <?php echo (isset($array_chk['TECNICO']) && $array_chk['TECNICO']==1 ? 'checked="checked"':'') ?>/> <label for="chk_tecnicos">Dar conformidad</label>
                        </p>
                        <p class="div_chk chk_opera">
                            <input type="checkbox" id="chk_tecnicos_nc" name="chk_tecnicos_nc" value="2" class="styled" <?php echo (isset($array_chk['TECNICO']) && $array_chk['TECNICO']==2 ? 'checked="checked"':'') ?>/> <label for="chk_tecnicos_nc">No Corresponde</label>
                        </p>
                        <div class="elem">
                            <label class="ancho100">Observaciones:</label>
                            <div class="formtext">
                                <textarea  class="medium"  id="obs_tecnico" name="obs_tecnico" rows="5"><?php echo (isset($array_obs['TECNICO']) ? $array_obs['TECNICO']:""); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
                <div class="add_file">Agregar Adjunto</div>
                
                <?php else:?>
                <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
                
                
                
        </div>
        
        <div class="vtabinfo" data-etapa="8">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("7",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
            <p class="div_chk">
                <input type="checkbox" id="chk_elevacion" name="chk_elevacion" value="2" class="styled" <?php echo (isset($array_chk['ELEVACION']) && $array_chk['ELEVACION']==1 ? 'checked="checked"':'') ?>> <label for="chk_elevacion">Dar conformidad</label>
            </p>
            <div class="elem">
                <label class="ancho100">Observación:</label>
                <div class="formtext">
                    <textarea  class="medium" id="obs_elevacion" name="obs_elevacion" rows="5"><?php echo (isset($array_obs['ELEVACION']) ? $array_obs['ELEVACION']:""); ?></textarea>
                </div>
            </div>
            
            <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
        
        <div class="vtabinfo" data-etapa="9">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("8",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                <p class="div_chk chkcom">
                    <input type="radio" id="opt_comite1" name="opt_comite" value="1" class="styled" <?php echo isset( $entidad["ID_ESTADO"] ) && $entidad["ID_ESTADO"]==2 ? 'checked="checked"' : "" ?>/> <label for="opt_comite1">Pendiente</label>
                </p>

                <p class="div_chk chkcom">
                    <input type="radio" id="opt_comite2" name="opt_comite" value="2" class="styled" <?php echo isset( $entidad["ID_ESTADO"] ) && $entidad["ID_ESTADO"]==5 ? 'checked="checked"' : "" ?>/> <label for="opt_comite2">Aceptada</label>
                </p>

                <p class="div_chk chkcom">
                    <input type="radio" id="opt_comite3" name="opt_comite" value="3" class="styled" <?php echo isset( $entidad["ID_ESTADO"] ) && $entidad["ID_ESTADO"]==3 ? 'checked="checked"' : "" ?>/> <label for="opt_comite3">Rechazada</label>
                </p>

                <div class="elem elem_med" style="margin-right:10px; width: 260px;">
                    <label class="der">Nro Acta:</label>
                    <div class="indent formtext">
                        <input type="text" class="tip-right" title="Ingrese Nro Acta" id="nacta" value="<?php echo (isset($acta_comite_nacta) ? $acta_comite_nacta:""); ?>">
                    </div>
                </div>
                <div class="elem elem_med" style="margin-right:10px; width: 260px;">
                    <label>Fecha Acta:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="" title="Ingrese Fecha Acta" id="facta" value="<?php echo isset($acta_comite_facta)?$acta_comite_facta:"" ?>"> 
                    </div>
                </div>
                <div class="elem" style="width:830px;float:left;">
                        <label class="ancho100 obs_comite_mod" style="width:106px!important">Observación:</label>
                        <div class="formtext">
                            <textarea class="medium_txtarea" id="obs_comite" name="obs_comite" style="margin-left:0px;" rows="5"><?php echo (isset($array_obs['COMITE']) ? $array_obs['COMITE']:""); ?></textarea>
                        </div>
                </div>

                <div class="elem elem_med" style="width:560px;">
                        <label class="der">Monto Aprobado:</label>
                        <div class="indent formtext">
                            <input maxlength="10" type="text" class="tip-right" title="Ingrese Monto Aprobado" id="macta" value="<?php echo isset($acta_comite_macta)?$acta_comite_macta:"" ?>">
                        </div>
                </div>
                
                <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && ($entidad["ID_ETAPA_ACTUAL"]!='10' ) ): ?>
                <div class="div_pcont">
                    <div class="titulo_condprev">Condiciones Previas al Contrato</div>
                    <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && ($entidad["ID_ETAPA_ACTUAL"]!='13' ) ): ?>
                    <div class="form_condiciones">
                        <div class="elem elem_med_cond">
                            <label>Agregar:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Condición" id="condicion" value="">
                            </div>
                        </div>
                    </div>
                    <div class="lista_opciones add" id="add_cond"></div>
                    <div class="lista_opciones del" id="del_cond"></div>
                    <?php endif; ?>
                    
                    

                    <div id="listbox_cond"></div>
                </div>
                <?php endif; ?>
                
                
                <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && $entidad["ID_ETAPA_ACTUAL"]!='10' && $entidad["ID_ETAPA_ACTUAL"]!='13'): ?>
                <div class="div_pdese">
                    <div class="titulo_condprev">Condiciones Previas a los desembolsos</div>
                    <div class="form_condiciones_dese">
                        <div class="elem elem_med_cond">
                            <label>Agregar:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Condición" id="condicion_dese" value="">
                            </div>
                        </div>
                    </div>
                    <div class="lista_opciones add" id="add_cond_dese"></div>
                    <div class="lista_opciones del" id="del_cond_dese"></div>

                    
                    <div id="listbox_cond_dese"></div>
                </div>
                <?php endif; ?>
                
                
                
                <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
        
        
        <div class="vtabinfo" data-etapa="10">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("7",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                <p class="div_chk" id="div_chk_checklist1">
                    <input type="checkbox" id="chk_rcontrato" name="chk_rcontrato" value="2" class="styled" <?php echo (isset($array_chk['RCONTRATO']) && $array_chk['RCONTRATO']==1 ? 'checked="checked"':'') ?>/> <label for="chk_rcontrato">Dar conformidad</label>
                </p>
                
                <?php if(is_array($lst_escribanos)): ?>
                <div class="elem elem_med" >
                    <label>Escribano:</label>
                    <div class="indent">
                    <select class="chzn-select .medium-select2a select" id="escribano">
                        <option value="">Elegir Escribano</option>
                        <?php foreach($lst_escribanos as $rs_fid): ?>
                        <option value="<?php echo $rs_fid["ID"] ?>"><?php echo $rs_fid["NOMBRE"] ?></option>
                        <?php endforeach; ?>
                    </select>   
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="elem elem_med">
                    <label>Fecha Entrega:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="" title="Fecha Entrega" id="fentregam" value="<?php echo isset($minuta_fentregam)?$minuta_fentregam:"" ?>">
                    </div>
                </div>
                
                <div class="elem elem_med">
                    <label>Fecha Devolución:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="" title="Fecha Devolución" id="fdevm" value="<?php echo isset($minuta_fdevm)?$minuta_fdevm:"" ?>">
                    </div>
                </div>
                
                    
                <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && $entidad["ID_ETAPA_ACTUAL"]=='10'): ?>
                <div class="titulo_condprev">Revisar Condiciones Previas al Contrato</div>
                <div id="listbox_cond"></div>
                
                <div class="titulo_condprev">Incorporar - Condiciones Previas a los desembolsos</div>
                <div id="listbox_cond_dese"></div>
                <?php endif; ?>                   
                
                
                
                <div class="add_file pos_btn fix01">Agregar Adjunto</div>
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
         
        
        <div class="vtabinfo" data-etapa="11">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("9",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                
                <div class="alert red hideit"><p><span class="red-icon"></span>Recuerde que las garantias deben estar ya Constituidas en esta etapa.</p></div>
                
                <?php if(isset($_estado_copia) && $_estado_copia==3): ?>
                <p class="div_chk" id="div_chk_checklist2">
                    <input type="checkbox" id="chk_fcontrato" name="chk_fcontrato" value="2" class="styled" <?php echo (isset($array_chk['FCONTRATO']) && $array_chk['FCONTRATO']==1 ? 'checked="checked"':'') ?>/> <label for="chk_fcontrato">Dar conformidad</label>
                </p>
                <?php endif; ?>
                
            
                <div class="elem elem_med">
                    <label>Fecha:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="" title="Ingrese Fecha" id="cffirma" value="<?php echo isset($contrato_f)?$contrato_f:"" ?>"> 
                    </div>
                </div>
            
                <div class="elem elem_med">
                    <label class="der">Monto:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="tip-right" title="Ingrese Monto Aprobado" id="cmaprob" value="<?php echo (isset($contrato_m) ? $contrato_m:""); ?>">
                    </div>
                </div>
            
                <div class="elem elem_med" style="width:680px;">
                    <label class="ancho100  obs_comite_mod">Observaciones:</label>
                    <div class="formtext">
                        <textarea class="medium_txtarea" id="obs_contrato" name="obs_contrato" rows="5"><?php echo (isset($array_obs['FCONTRATO']) ? $array_obs['FCONTRATO']:""); ?></textarea>
                    </div>
                </div>

                <?php if (isset($_estado_copia) && ($_estado_copia==0 or $_estado_copia==4 )  ): ?>
                <div id="copia_contrato" class="btn_form pos_btn">Entrega de Contrato a Legales</div>
                <?php elseif(isset($_estado_copia) && $_estado_copia==1): ?>
                <div id="copia_contrato_estado" class="btn_form pos_btn">Contrato legales (Esperando respuesta)</div>
                <?php elseif(isset($_estado_copia) && $_estado_copia==3): ?>
                <div id="copia_contrato_estado" class="btn_form pos_btn">Contrato legales (Respuesta Positiva)</div>
                <?php endif; ?>
                <?php if(isset($_estado_copia) && $_estado_copia==3): ?>
                <span id="text_fc" class="verde">* Esta respuesta es requisito para pasar a la siguiente etapa.</span>
                <?php else: ?>
                <span id="text_fc" class="rojo">* Esta respuesta es requisito para pasar a la siguiente etapa.</span>
                <?php endif; ?>
                <div class="add_file" style="margin-top:-20px!important;">Agregar Adjunto</div>
            <?php else:?>
                <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
        
        <div class="vtabinfo" data-etapa="12">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("9",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                
            <div>
                <input type="button" value="Alta de Crédito" id="agregar_altacredito" data-credito="<?php echo isset($id_credito)?$id_credito:"" ?>" data-idope="<?php echo isset($entidad["ID"])?$entidad["ID"]:''; ?>" />
            </div>
            
            <p class="div_chk" id="div_chk_checklist3">
                <input type="checkbox" id="chk_altacredito" name="chk_altacredito" value="2" class="styled" <?php echo (isset($array_chk['ALTACREDITO']) && $array_chk['ALTACREDITO']==1 ? 'checked="checked"':'') ?>/> <label for="chk_altacredito">Dar conformidad</label>
            </p>
            <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
                <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
        
        <div class="vtabinfo" data-etapa="13">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("9",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
            
            <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && $entidad["ID_ETAPA_ACTUAL"]=='13'): ?>
                <div class="titulo_condprev">Revisar Condiciones Previas a los desembolsos3</div>
                
            <?php endif; ?>
            <div id="listbox_cond_dese"></div>
            
            <div class="dese_info">
                <span>Leyenda: </span>
                <div class="dese1"></div>
                <span>Cumplido</span>
                
                <div class="dese3"></div>
                <span>No cumplido</span>
                
                <div class="dese2"></div>
                <span>No corresponde</span>
            </div>
            
            
            <p class="div_chk1a" id="div_chk_checklist1a" style="float: left;display: block;width: 924px">
                <?php if(isset($_estado_copia) && $_estado_copia==3): ?>
                    <input disabled="true" type="checkbox" id="chk_copiacontrato" value="1" class="styled" <?php echo (isset($_estado_copia) && $_estado_copia==3 ? 'checked="checked"':'') ?>/> <label for="chk_copiacontrato" class="verde">Copia de contrato en legales</label>
                <?php else: ?>
                    <input disabled="true" type="checkbox" id="chk_copiacontrato" value="1" class="styled" <?php echo (isset($_estado_copia) && $_estado_copia==3 ? 'checked="checked"':'') ?>/> <label for="chk_copiacontrato" class="rojo">Copia de contrato en legales</label>
                <?php endif; ?>
            </p>
            
                            
            <div class="titulo_condprev">Solicitudes de Desembolso</div>
            <div data-gridname="jqxgrid_soldesem" class="refresgrid" style="float:left;">Refresh Grid</div>
            <div id="jqxgrid_soldesem"></div>
                
            
            <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
                <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
       
        
    </div>
    
    <?php else: ?>
    
    
    
    
    
    
    
    <div id="vtab" data-idproceso='2'>
        <ul>
            <li class="checklist <?php if (isset($array_ope_etapas[1]) && $array_ope_etapas[1]==1) echo "etapa_ready" ?>"><span>Inscripción</span></li>
            <li class="garantia"><span>Garantías</span></li>
            <li class="comite <?php if (isset($array_ope_etapas[9]) && $array_ope_etapas[9]==1) echo "etapa_ready" ?>"><span>Comité</span></li>
            <li class="c_contrato <?php if (isset($array_ope_etapas[10]) && $array_ope_etapas[10]==1) echo "etapa_ready" ?>"><span>Conf. Contrato</span></li>
            <li class="desembolsos <?php if (isset($array_ope_etapas[13]) && $array_ope_etapas[13]==1) echo "etapa_ready" ?>"><span>Desembolsos</span></li>
            
            
            <li class="contrato <?php if (isset($array_ope_etapas[11]) && $array_ope_etapas[11]==1) echo "etapa_ready" ?>"><span>Firma Contrato</span></li>
            <li class="altacredito <?php if (isset($array_ope_etapas[12]) && $array_ope_etapas[12]==1) echo "etapa_ready" ?>"><span>Alta de Crédito</span></li>
            
        </ul>
        
        <div class="vtabinfo" data-etapa="1"><!-- etapa inscripcion  -->
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("1",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Seleccione alguna Operatoria.</p></div>
            <div style="float: left;" id="listbox"></div>
            <div class="elem">
                    <label class="ancho100">Observación:</label>
                    <div class="formtext">
                        <textarea  class="medium_txtarea"  id="obs_checlist" name="obs_checlist" rows="5"><?php echo (isset($array_obs['CHECKLIST']) ? $array_obs['CHECKLIST']:""); ?></textarea>
                    </div>
            </div>

            <p class="div_chk" id="div_chk_checklist">
                <input type="checkbox" id="chk_checklist" name="chk_checklist" value="2" class="styled" <?php echo (isset($array_chk['CHECKLIST']) && $array_chk['CHECKLIST']==1 ? 'checked="checked"':'') ?>/> <label for="chk_checklist">Dar conformidad</label>
            </p>
            
            <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
                
        <div class="vtabinfo" data-etapa="7">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("6",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
            
            <div class="titulo_condprev">Garantias</div>
            <div data-gridname="jqxgrid_traza" class="refresgrid">Refresh Grid</div>
            <div id="jqxgrid_garantias"></div>
            
            <div class="suma_aportes">
                <span class="t1">En Evaluación :</span>$<span id="suma_aporte_1"></span>
                <span class="t1">Aprobadas :</span>$<span id="suma_aporte_5"></span>
                <span class="t1">Constituidas :</span>$<span id="suma_aporte_6"></span>
            </div>
            
            
            
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
            
        </div>
        
        <div class="vtabinfo" data-etapa="9">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("8",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                <p class="div_chk chkcom">
                    <input type="radio" id="opt_comite1" name="opt_comite" value="1" class="styled" <?php echo isset( $entidad["ID_ESTADO"] ) && $entidad["ID_ESTADO"]==2 ? 'checked="checked"' : "" ?>/> <label for="opt_comite1">Pendiente</label>
                </p>

                <p class="div_chk chkcom">
                    <input type="radio" id="opt_comite2" name="opt_comite" value="2" class="styled" <?php echo isset( $entidad["ID_ESTADO"] ) && $entidad["ID_ESTADO"]==5 ? 'checked="checked"' : "" ?>/> <label for="opt_comite2">Aceptada</label>
                </p>

                <p class="div_chk chkcom">
                    <input type="radio" id="opt_comite3" name="opt_comite" value="3" class="styled" <?php echo isset( $entidad["ID_ESTADO"] ) && $entidad["ID_ESTADO"]==3 ? 'checked="checked"' : "" ?>/> <label for="opt_comite3">Rechazada</label>
                </p>

                <div class="elem elem_med">
                    <label class="der">Nro Acta:</label>
                    <div class="indent formtext">
                        <input type="text" class="tip-right" title="Ingrese Nro Acta" id="nacta" value="<?php echo (isset($acta_comite_nacta) ? $acta_comite_nacta:""); ?>">
                    </div>
                </div>
                <div class="elem elem_med">
                    <label>Fecha Acta:</label>
                    <div class="indent formtext">
                        <input type="text" class="" title="Ingrese Fecha Acta" id="facta" value="<?php echo isset($acta_comite_facta)?$acta_comite_facta:"" ?>"> 
                    </div>
                </div>
                <div class="elem">
                        <label class="ancho100 obs_comite_mod">Observación:</label>
                        <div class="formtext">
                            <textarea class="medium_txtarea"  id="obs_comite" name="obs_comite" rows="5"><?php echo (isset($array_obs['COMITE']) ? $array_obs['COMITE']:""); ?></textarea>
                        </div>
                </div>

                <div class="elem elem_med" style="width:560px;">
                        <label class="der">Monto Aprobado:</label>
                        <div class="indent formtext">
                            <input maxlength="10" type="text" class="tip-right" title="Ingrese Monto Aprobado" id="macta" value="<?php echo isset($acta_comite_macta)?$acta_comite_macta:"" ?>">
                        </div>
                </div>
                
                <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && ($entidad["ID_ETAPA_ACTUAL"]!='10' ) ): ?>
                <div class="div_pcont">
                    <div class="titulo_condprev">Condiciones Previas al Contrato</div>
                    <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && ($entidad["ID_ETAPA_ACTUAL"]!='13' ) ): ?>
                    <div class="form_condiciones">
                        <div class="elem elem_med_cond">
                            <label>Agregar:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Condición" id="condicion" value="">
                            </div>
                        </div>
                    </div>
                    <div class="lista_opciones add" id="add_cond"></div>
                    <div class="lista_opciones del" id="del_cond"></div>
                    <?php endif; ?>
                    
                    

                    <div id="listbox_cond"></div>
                </div>
                <?php endif; ?>
                
                
                <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && $entidad["ID_ETAPA_ACTUAL"]!='10' && $entidad["ID_ETAPA_ACTUAL"]!='13'): ?>
                <div class="div_pdese">
                    <div class="titulo_condprev">Condiciones Previas a los desembolsos</div>
                    <div class="form_condiciones_dese">
                        <div class="elem elem_med_cond">
                            <label>Agregar:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Condición" id="condicion_dese" value="">
                            </div>
                        </div>
                    </div>
                    <div class="lista_opciones add" id="add_cond_dese"></div>
                    <div class="lista_opciones del" id="del_cond_dese"></div>

                    
                    <div id="listbox_cond_dese"></div>
                </div>
                <?php endif; ?>
                
                
                
                <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
        
        
        <div class="vtabinfo" data-etapa="10">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("7",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                <p class="div_chk" id="div_chk_checklist1">
                    <input type="checkbox" id="chk_rcontrato" name="chk_rcontrato" value="2" class="styled" <?php echo (isset($array_chk['RCONTRATO']) && $array_chk['RCONTRATO']==1 ? 'checked="checked"':'') ?>/> <label for="chk_rcontrato">Dar conformidad</label>
                </p>
                
                <?php if(is_array($lst_escribanos)): ?>
                <div class="elem elem_med" >
                    <label>Escribano:</label>
                    <div class="indent">
                    <select class="chzn-select .medium-select2a select" id="escribano">
                        <option value="">Elegir Escribano</option>
                        <?php foreach($lst_escribanos as $rs_fid): ?>
                        <option value="<?php echo $rs_fid["ID"] ?>"><?php echo $rs_fid["NOMBRE"] ?></option>
                        <?php endforeach; ?>
                    </select>   
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="elem elem_med">
                    <label>Fecha Entrega:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="" title="Fecha Entrega" id="fentregam" value="<?php echo isset($minuta_fentregam)?$minuta_fentregam:"" ?>">
                    </div>
                </div>
                
                <div class="elem elem_med">
                    <label>Fecha Devolución:</label>
                    <div class="indent formtext">
                        <input type="text" class="" title="Fecha Devolución" id="fdevm" value="<?php echo isset($minuta_fdevm)?$minuta_fdevm:"" ?>">
                    </div>
                </div>
                
                    
                <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && $entidad["ID_ETAPA_ACTUAL"]=='10'): ?>
                <div class="titulo_condprev">Revisar Condiciones Previas al Contrato</div>
                <div id="listbox_cond"></div>
                
                <div class="titulo_condprev">Incorporar - Condiciones Previas a los desembolsos</div>
                <div id="listbox_cond_dese"></div>
                <?php endif; ?>                   
                
                <div class="add_file">Agregar Adjunto</div>
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
         
        
        <div class="vtabinfo" data-etapa="13">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("9",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
            
            <?php if ( isset($entidad["ID_ETAPA_ACTUAL"]) && $entidad["ID_ETAPA_ACTUAL"]=='13'): ?>
                <div class="titulo_condprev">Revisar Condiciones Previas a los desembolsos</div>
            <?php endif; ?>
            <div id="listbox_cond_dese"></div>
            
            <div class="dese_info">
                <span>Leyenda: </span>
                <div class="dese1"></div>
                <span>Cumplido</span>
                
                <div class="dese3"></div>
                <span>No cumplido</span>
                
                <div class="dese2"></div>
                <span>No corresponde</span>
            </div>
                            
            <div class="titulo_condprev">Solicitudes de Desembolso</div>
            <div data-gridname="jqxgrid_soldesem" class="refresgrid">Refresh Grid</div>
            <div id="jqxgrid_soldesem"></div>
                
            
            <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
                <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
        
        
        
        <div class="vtabinfo" data-etapa="11">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("9",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                
                
                
                <p class="div_chk" id="div_chk_checklist2">
                    <input type="checkbox" id="chk_fcontrato" name="chk_fcontrato" value="2" class="styled" <?php echo (isset($array_chk['FCONTRATO']) && $array_chk['FCONTRATO']==1 ? 'checked="checked"':'') ?>/> <label for="chk_fcontrato">Dar conformidad</label>
                </p>
            
                <div class="elem elem_med">
                    <label>Fecha:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="" title="Ingrese Fecha" id="cffirma" value="<?php echo isset($contrato_f)?$contrato_f:"" ?>"> 
                    </div>
                </div>
            
                <div class="elem elem_med">
                    <label class="der">Monto:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="tip-right" title="Ingrese Monto Aprobado" id="cmaprob" value="<?php echo (isset($contrato_m) ? $contrato_m:""); ?>">
                    </div>
                </div>
            
                <div class="elem elem_med" style="width:680px;">
                    <label class="ancho100  obs_comite_mod">Observaciones:</label>
                    <div class="formtext">
                        <textarea class="medium_txtarea" id="obs_contrato" name="obs_contrato" rows="5"><?php echo (isset($array_obs['FCONTRATO']) ? $array_obs['FCONTRATO']:""); ?></textarea>
                    </div>
                </div>
                
                
                
                <div class="add_file">Agregar Adjunto</div>
            <?php else:?>
                <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
        
        <div class="vtabinfo" data-etapa="12">
            <!--<div class="ocultame_etapa"><span>Contenido Bloqueado</span></div>-->
            <?php if (in_array("9",$permisos_etapas) or $_SESSION["USER_USERNAME"]=='admin' or 1): ?>
                
            <div>
                <input type="button" value="Alta de Crédito" id="agregar_altacredito" data-credito="<?php echo isset($id_credito)?$id_credito:"" ?>" data-idope="<?php echo isset($entidad["ID"])?$entidad["ID"]:''; ?>" />
            </div>
            
            <p class="div_chk" id="div_chk_checklist3">
                <input type="checkbox" id="chk_altacredito" name="chk_altacredito" value="2" class="styled" <?php echo (isset($array_chk['ALTACREDITO']) && $array_chk['ALTACREDITO']==1 ? 'checked="checked"':'') ?>/> <label for="chk_altacredito">Dar conformidad</label>
            </p>
            <div class="add_file">Agregar Adjunto</div>
            
            <?php else:?>
                <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
        
        
       
        
    </div>
    
    <?php endif; ?>
                    
    
    
    <div class="grid-1 grid_adjuntos">
        <div class="title-grid">Adjuntos <div class="ver_todos">Ver Todos</div><span></span></div>
        <div class="content-gird" style="display: block;">
            <ul class="lista_adjuntos">
            <?php
                  if( isset($lst_uploads) && is_array($lst_uploads) ):
                      foreach($lst_uploads as $rs_up):
                          echo '<li data-usuario="'.$rs_up['USUARIO'].'" data-iid="'. $rs_up['IID'] .'" class="eta-'.$rs_up['ID_ETAPA'].'" data-descripcion="'.$rs_up['DESCRIPCION'].'" data-etapa="'.$rs_up['ID_ETAPA'].'" data-ruta="'.$rs_up['NOMBRE'].'" data-identidad="'.$rs_up['ID_OPERACION'].'">'.basename($rs_up['NOMBRE']).' - Subido por: '.$rs_up['USUARIO_NOMBRE'].' - Etapa: '.$rs_up["ETAPA"].' <span>'. date("d/m/Y", strtotime($rs_up['CREATEDON'])) .'</span></li><a class="download_file" href="general/extends/extra/download.php?file='.$rs_up['NOMBRE'].'" title="Descargar Archivo"></a>
                                <a class="delete_file" title="Borrar Archivo"></a>
                                ';
                      endforeach;
                  endif;
            ?>
            </ul>
            <div class="clear"></div>
       </div>
    </div>
    
    <div class="grid-1 grid_adj_ope">
        <div class="title-grid">Adjuntos de Operatoria<span></span></div>
        <div class="content-gird" style="display: block;">
            <ul class="lista_adj_ope">
            <?php
                  if( isset($lst_uploads_operatoria) && is_array($lst_uploads_operatoria) ):
                      foreach($lst_uploads_operatoria as $rs_up):
                          echo '<li data-ruta="'.$rs_up['NOMBRE'].'" data-identidad="'.$rs_up['ID_OPERATORIA'].'">'.basename($rs_up['NOMBRE']).' <span>(etiketaa)</span></li><a class="download_file" href="general/extends/extra/download.php?file='.$rs_up['NOMBRE'].'" title="Descargar Archivo"></a>';
                      endforeach;
                  endif;
            ?>
            </ul>
            <div class="clear"></div>
       </div>
    </div>
    
    <div class="grid-1 grid_reqs">
        <?php $_pp = (isset($_SESSION["USER_PERMISOS"][10]['ALTA']) && $_SESSION["USER_PERMISOS"][10]['ALTA']) || (isset($_SESSION["USER_PERMISOS"][11]['ALTA']) && $_SESSION["USER_PERMISOS"][11]['ALTA']); ?>
        <div class="title-grid">Requerimientos <?php if ($_pp): ?><div class="agregar_req">Agregar Requerimientos</div><?php endif; ?><span></span></div>
        <div class="content-gird" style="display: block;">
            <ul class="lista_reqs">
            <?php
                if( isset($lst_reqs) && is_array($lst_reqs) ):
                    if (count($lst_reqs)>0)
                        echo '<li class="li_cabezera">Id<span class="fet">'. "F. Tratamiento" .'</span><span class="fer">'. "F. Recepción" .'</span><span class="fem">'. "F. Emisión" .'</span><span class="reqest">Estado</span><span class="reqiid">Asunto</span></li>';
                    
                    foreach($lst_reqs as $rs_up):
                        //$fcrea = strtotime( $rs_up["FCREA"])==false?'-':date( "d/m/Y", strtotime($rs_up["FCREA"]) ) . " _ ";
                        $fcrea = strtotime( $rs_up["FCREA"])==false || $rs_up["FCREA"]=='0000-00-00 00:00:00' ?'-':date( "d/m/Y", strtotime($rs_up["FCREA"]) );
                        //$frec  = strtotime( $rs_up["FREC"]) ==false ?'-':date( "d/m/Y", strtotime($rs_up["FREC"]) ) . " __ ";
                        $frec  = strtotime( $rs_up["FREC"]) ==false || $rs_up["FREC"]=='0000-00-00 00:00:00' ?'-':date( "d/m/Y", strtotime($rs_up["FREC"]) );
                        //$ftra  = strtotime( $rs_up["FTRA"]) ==false?'-':date( "d/m/Y", strtotime($rs_up["FTRA"]) ) . " ___ ";
                        $ftra  = strtotime( $rs_up["FTRA"]) ==false || $rs_up["FTRA"]=='0000-00-00 00:00:00' ?'-':date( "d/m/Y", strtotime($rs_up["FTRA"]) );
                        $estado = "Emitido" ;
                        if ( $rs_up["ESTADO"]==0 )
                            $estado = "Pendiente Autorización" ;
                        if ( $rs_up["ESTADO"]==1 )
                            $estado = "Enviado (Pendiente Respuesta)";
                        elseif($rs_up["ESTADO"]==2)
                            $estado = "Autorizado" ;
                        elseif($rs_up["ESTADO"]==3)
                            $estado = "Respondido";
                        elseif($rs_up["ESTADO"]==4)
                            $estado = "Suficiente" ;
                        elseif($rs_up["ESTADO"]==5)
                            $estado = "Insuficiente" ;
                        elseif($rs_up["ESTADO"]==6)
                            $estado = "No autorizado" ;
                        
                        if ($estado=='Enviado')
                            echo '<li class="ya_enviado" data-idr="'.$rs_up["ID"].'"><span class="filr_iid">'. $rs_up["ID"] .'</span><span class="filr_asunto">'.$rs_up["ASUNTO"].'</span><span class="filr_estado">'. $estado .'</span><span>'. $fcrea .'</span><span>'. $frec .'</span><span>'. $ftra .'</span></li>';
                        else
                            echo '<li data-remitente="'.$rs_up["REMITENTE"].'" data-idr="'.$rs_up["ID"].'"><span class="filr_iid">'. $rs_up["ID"] .'</span><span class="filr_asunto">'.$rs_up["ASUNTO"].'</span><span class="filr_estado">'. $estado .'</span><span>'. $fcrea .'</span><span>'. $frec .'</span><span>'. $ftra .'</span></li>';
                    endforeach;
                endif;
                
            ?>
            </ul>
            <div class="clear"></div>
       </div>
    </div>
    
    <div class="grid-1 grid_reqs">
        <div class="title-grid">Notas Vinculadas<span></span></div>
        <div class="content-gird" style="display: block;">
            
            <div data-gridname="jqxgrid_notas" class="refresgrid">Refresh Grid</div>
            <div id="jqxgrid_notas"></div>
            <div class="clear"></div>
       </div>
    </div>
    
    
    <div class="grid-1 grid_adj_ope">
        <div class="title-grid">Trazabilidad <div data-gridname="jqxgrid_traza" class="refresgrid">Refresh Grid</div><span></span></div>
        <div class="content-gird" style="display: block;">
            
            <div id="jqxgrid_traza"></div>
            
            <div class="clear"></div>
        </div>
    </div>
    
    
    <div class="elem elempie">
        <div class="indent">
            
            <?php if ( isset($entidad["CARTERADE"]) && ($entidad["CARTERADE"] == $_SESSION["USERADM"]) && 0): ?>
                <?php if ( isset($entidad["ENVIADOA"]) && ($entidad["ENVIADOA"]==0 ) ): ?>
                <input id="asignarr" name="asignarr" type="submit" class="button-a gray asignar" value="Enviar"> &nbsp;&nbsp;
                <input id="send" name="send" type="submit" class="button-a gray send" value="Guardar"> &nbsp;&nbsp;
                <?php endif; ?>
            <?php elseif(!isset($entidad["CARTERADE"]) && 0):?>
                <input id="asignarr" name="asignarr" type="submit" class="button-a gray asignar" value="Enviar"> &nbsp;&nbsp;
                <input id="send" name="send" type="submit" class="button-a gray send" value="Guardar"> &nbsp;&nbsp;
            <?php elseif( isset($entidad["CARTERADE"]) && $entidad["CARTERADE"]==0 && $_SESSION["USER_ROL"]==9 && 0 ): ?>
                <input id="asignarr" name="asignarr" type="submit" class="button-a gray asignar" value="Enviar"> &nbsp;&nbsp;
                <input id="send" name="send" type="submit" class="button-a gray send" value="Guardar"> &nbsp;&nbsp;
            <?php endif; ?>
                <input style="display:none" id="asignarr" name="asignarr" type="submit" class="button-a gray asignar" value="Enviar"> &nbsp;&nbsp;
                <input style="display:none" id="send" name="send" type="submit" class="button-a gray send" value="Guardar"> &nbsp;&nbsp;
            
        </div>
    </div>
    
</form>
       
</div>