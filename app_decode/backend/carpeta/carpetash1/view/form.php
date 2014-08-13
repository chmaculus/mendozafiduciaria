<?php
    //$aaaa = deFecha_a_base('27/12/2013');
    //log_this('log/qqqqqqqq.log',print_r($lst_desemb,1));
    
    $monto1 = ""; $monto2 = ""; $monto3 = "";
    $fecha1 = ""; $fecha2 = ""; $fecha3 = "";

    if (count($lst_desemb)){
        $monto1 = isset($lst_desemb[0]["MONTO"])?$lst_desemb[0]["MONTO"]:"";
        $monto2 = isset($lst_desemb[1]["MONTO"])?$lst_desemb[1]["MONTO"]:"";
        $monto3 = isset($lst_desemb[2]["MONTO"])?$lst_desemb[2]["MONTO"]:"";
        
        

        $fecha1 = isset($lst_desemb[0]["FECHA"])?$lst_desemb[0]["FECHA"]:"";
        $fecha2 = isset($lst_desemb[1]["FECHA"])?$lst_desemb[1]["FECHA"]:"";
        $fecha3 = isset($lst_desemb[2]["FECHA"])?$lst_desemb[2]["FECHA"]:"";
        
        //log_this('log/qqqqqqqq.log',print_r($fecha3,1));

        if($fecha1)
            $fecha1  = date('Y', $fecha1);
        
        if($fecha2)
            $fecha2  = date('Y', $fecha2);
        
        if($fecha3)
            $fecha3  = date('Y', $fecha3);
        
    }
    
?>
<script>
    var _fecha1 = '<?php echo $fecha1 ?>';
    var _fecha2 = '<?php echo $fecha2 ?>';
    var _fecha3 = '<?php echo $fecha3 ?>';
    var _fechac = '<?php echo $fecha3 ?>';

</script>

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
                    <label class="der">Monto Contrato:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="tip-right" title="Ingrese Monto" id="montosol" data-prompt-position="centerRight" value="<?php echo (isset($entidad['MONTO_SOLICITADO'])? $entidad['MONTO_SOLICITADO']:"" ) ?>" <?php echo (isset($entidad['ID'])? "readonly":"" ) ?>> 
                    </div>
            </div>
            
            <div class="elem elem_med">
                <label>Fecha Contrato:</label>
                <div class="indent formtext">
                    <input maxlength="10" type="text" class="" title="Ingrese Fecha Contrato" id="fcontrato" value="<?php echo isset($entidad["FECHA"])?$entidad["FECHA"]:"" ?>"> 
                </div>
            </div>

            <?php if(is_array($lst_fideicomisos)): ?>
            <div class="elem elem_med" >
                <label>Estado:</label>
                <div class="indent">
                    <select class="validate[required] chzn-select medium-select select" id="estado" data-prompt-position="topLeft">
                        <option value="29">Cancelado</option>
                        <option value="2">Vigente</option>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="elem elem_med">
                <label class="der">Destino:</label>
                <div class="indent formtext">
                    <input type="text" class="validate[required] tip-right" title="Ingrese Destino" id="destino" value="<?php echo (isset($entidad['DESTINO'])? $entidad['DESTINO']:"" ) ?>" data-prompt-position="topLeft" <?php echo (isset($entidad['ID'])? "readonly":"" ) ?>>
                </div>
            </div>
            
            
            
            <?php if(is_array($lst_fideicomisos)): ?>
            <div class="elem elem_med">
                <label>Año:</label>
                <div class="indent">
                    <select class="chzn-select medium-select select" id="anio1">
                        <option value="">Elegir</option>
                        <option value="2013">2013</option>
                        <option value="2012">2012</option>
                        <option value="2011">2011</option>
                        <option value="2010">2010</option>
                        <option value="2009">2009</option>
                        <option value="2008">2008</option>
                        <option value="2007">2007</option>
                        <option value="2006">2006</option>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="elem elem_med">
                    <label class="der">Monto:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="tip-right" title="Ingrese Monto Solicitado" id="montoa1" data-prompt-position="centerRight" value="<?php echo $monto1 ?>">
                    </div>
            </div>
            
            
            
            <?php if(is_array($lst_fideicomisos)): ?>
            <div class="elem elem_med">
                <label>Año:</label>
                <div class="indent">
                    <select class="chzn-select medium-select select" id="anio2">
                        <option value="">Elegir</option>
                        <option value="2013">2013</option>
                        <option value="2012">2012</option>
                        <option value="2011">2011</option>
                        <option value="2010">2010</option>
                        <option value="2009">2009</option>
                        <option value="2008">2008</option>
                        <option value="2007">2007</option>
                        <option value="2006">2006</option>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="elem elem_med">
                    <label class="der">Monto:</label>
                    <div class="indent formtext">
                        <input maxlength="10" type="text" class="tip-right" title="Ingrese Monto Solicitado" id="montoa2" data-prompt-position="centerRight" value="<?php echo $monto2 ?>" > 
                    </div>
            </div>
            
            <?php if(is_array($lst_fideicomisos)): ?>
            <div class="elem elem_med">
                <label>Año:</label>
                <div class="indent">
                    <select class="chzn-select medium-select select" id="anio3">
                        <option value="">Elegir</option>
                        <option value="2013">2013</option>
                        <option value="2012">2012</option>
                        <option value="2011">2011</option>
                        <option value="2010">2010</option>
                        <option value="2009">2009</option>
                        <option value="2008">2008</option>
                        <option value="2007">2007</option>
                        <option value="2006">2006</option>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="elem elem_med">
                <label class="der">Monto:</label>
                <div class="indent formtext">
                    <input maxlength="10" type="text" class="tip-right" title="Ingrese Monto Solicitado" id="montoa3" data-prompt-position="centerRight" value="<?php echo $monto3 ?>">
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
                <?php endif; ?>
            </div>
            
            <div class="clear"> </div>
         </div>
       </div>
    </div>
    <!--Form end-->
    
    <div id="vtab">
        <ul>
            <li class="garantia <?php echo "etapa_ready" ?>"><span>Garantías y Adjuntos</span></li>
        </ul>
        
        
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
            <div class="add_file" style="position:relative; top:40px;">Agregar Adjunto</div>
            <?php else:?>
            <div class="alert red hideit"><p><span class="red-icon"></span>Usted no tiene permisos para ver esta Sección.</p></div>
            <?php endif; ?>
        </div>
    </div>
    
    
    <div class="grid-1 grid_adjuntos">
        <div class="title-grid">Adjuntos <div class="ver_todos"></div><span></span></div>
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


<div style="display:none;" id="div_altadecredito"></div>

