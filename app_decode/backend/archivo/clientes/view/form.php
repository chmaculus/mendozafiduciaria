<div class="content-form">
        <!--Form-->
        <a name="anchorTarget" id="anchorTarget"></a>
                               
        <div class="grid-1" id="frmagregar">
           <div class="title-grid"><div id="label_action">Agregar</div> <?php echo $etiqueta_mod ?></div>
           <div class="content-gird">
           <div class="form">
                <form method="post" enctype="multipart/form-data" id="customForm" >
                    <input type="hidden" id="idh" value="<?php echo (isset($entidad["ID"])?$entidad["ID"]:"") ?>" />
                    <input type="hidden" id="val_ok" value="0" />
                    <input type="hidden" id="provinciah" value="0" />
                    <input type="hidden" id="localidadh" value="0" />
                    
                    <input type="hidden" id="faltah" value="<?php echo (isset($entidad["FECHA_ALTA"])?$entidad["FECHA_ALTA"]:"") ?>" />
                    <input type="hidden" id="civae" value="<?php echo (isset($entidad["ID_CONDICION_IVA"])?$entidad["ID_CONDICION_IVA"]:"") ?>" />
                    <input type="hidden" id="cibbe" value="<?php echo (isset($entidad["ID_CONDICION_IIBB"])?$entidad["ID_CONDICION_IIBB"]:"") ?>" />
                    <input type="hidden" id="prove" value="<?php echo (isset($entidad["ID_PROVINCIA"])?$entidad["ID_PROVINCIA"]:"") ?>" />
                    <input type="hidden" id="locae" value="<?php echo (isset($entidad["ID_DEPARTAMENTO"])?$entidad["ID_DEPARTAMENTO"]:"") ?>" />
                    <input type="hidden" id="id_tipoe" value="<?php echo (isset($entidad["ID_TIPO"])?$entidad["ID_TIPO"]:"") ?>" />
                                         
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
                           <input type="hidden" class="validate[required]" id="loc" value="" />
                           <div class="indent" id="div_subrubro">
                                <select class="chzn-select medium-select select" id="subrubro" data-prompt-position="topLeft">
                                   <option value="">Elegir Subrubro</option>
                                </select>
                           </div>
                    </div>
                                       
                    <?php if(is_array($lst_condicioniva)): ?>
                    <div class="elem elem_med">
                        <label>Condicion Iva:</label>
                        <div class="indent">
                        <select class=" chzn-select medium-select select" id="condicioniva" data-prompt-position="centerRight">
                            <option value="">Elegir Condicion Iva</option>
                            <?php foreach($lst_condicioniva as $rs_iva): ?>
                            <option data-connection="<?php echo $rs_iva["ID"] ?>" value="<?php echo $rs_iva["ID"] ?>"><?php echo $rs_iva["CONDICION"] ?></option>
                            <?php endforeach; ?>
                        </select>   
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(is_array($lst_condicioniibb)): ?>
                    <div class="elem elem_med">
                        <label>Condicion IIBB:</label>
                        <div class="indent">
                        <select class=" chzn-select medium-select select" id="condicioniibb" data-prompt-position="centerRight">
                            <option value="">Elegir Tipo Cliente</option>
                            <?php foreach($lst_condicioniibb as $rs_iibb): ?>
                            <option data-connection="<?php echo $rs_iibb["ID"] ?>" value="<?php echo $rs_iibb["ID"] ?>"><?php echo $rs_iibb["CONDICION"] ?></option>
                            <?php endforeach; ?>
                        </select>   
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="elem">
                            <label>Razón Social:</label>
                            <div class="indent formtext">
                                <input type="text" class="validate[required] medium" title="Ingrese Valor" id="raz" data-prompt-position="topLeft" value="<?php echo (isset($entidad['RAZON_SOCIAL'])?$entidad['RAZON_SOCIAL']:"") ?>"> 
                            </div>
                     </div>
                    
                    <div class="elem elem_med">
                            <label class="der">Inscripción IIBB:</label>
                            <div class="indent formtext">
                                <input type="text" class="tip-right" title="Ingrese Valor" id="insiibb" data-prompt-position="centerRight" value="<?php echo (isset($entidad['INSCRIPCION_IIBB'])? $entidad['INSCRIPCION_IIBB']:"" ) ?>"> 
                            </div>
                    </div>
                    
                    <div class="elem elem_med">
                            <label class="der">Inscripción INV:</label>
                            <div class="indent formtext">
                                <input type="text" class="tip-right" title="Ingrese Valor" id="insinv" data-prompt-position="centerRight" value="<?php echo (isset($entidad['INSCRIPCION_IIBB'])? $entidad['ID_INV']:"" ) ?>"> 
                            </div>
                    </div>
                    
                    <div class="elem">
                            <label>Dirección:</label>
                            <div class="indent formtext">
                                <input type="text" class="validate[required] medium" title="Ingrese Valor" id="dir" data-prompt-position="topLeft" value="<?php echo (isset($entidad['DIRECCION'])?$entidad['DIRECCION']:"") ?>"> 
                            </div>
                     </div>
                    
                    
                    <div class="elem elem_med">
                            <label>CUIT:</label>
                            <div class="indent formtext">
                                <input type="text" class="validate[required,custom[cuit]] tip-right" title="Ingrese Valor" id="cuit" data-prompt-position="centerRight" maxlength="11" value="<?php echo (isset($entidad['CUIT'])?$entidad['CUIT']:"") ?>">
                            </div>
                     </div>
                    
                    <div class="elem elem_med">
                            <label>CBU:</label>
                            <div class="indent formtext">
                                <input type="text" class="tip-right" title="Ingrese Valor" id="cbu" data-prompt-position="centerRight" value="<?php echo ( isset($entidad['CBU'])?$entidad['CBU']:"") ?>"> 
                            </div>
                    </div>
                    
                    
                    <div class="elem">
                            <label>Observación:</label>
                            <div class="indent formtext">
                                <textarea  class="medium"  id="obs" name="obs" rows="5"><?php echo (isset($entidad['OBSERVACION']) ? $entidad['OBSERVACION']:""); ?></textarea>
                            </div>
                    </div>
                    
                    <div class="elem">
                            <label>Fecha de Alta:</label>
                            <div class="indent formtext">
                                <input type="text" class="medium tip-right" title="Ingrese Valor" id="falta" readonly value="<?php echo $hora_mostrar ?>">
                            </div>
                            <div class="div_pidepass"><span>Contenido Bloqueado</span></div>
                    </div>
                    <div class="validate[custom[contacto]] group" id="contactos">
                        <div class="elem">
                            <label>Contactos:</label>
                            <span style="margin:0 0 0 5px">Debe ingresar al menos un contacto, y debe contener nombre y algún otro campo más</span>
                        </div>
                        <input type="hidden" class="validate[required]" value="" id="contactosForm" />
                    <?php if ($contactos): ?>
                        <?php foreach ($contactos as $cont): ?>
                        <div class="elem_group">
                            
                            <div class="elem elem_med">
                                    <label>Contacto:</label>
                                    <div class="indent formtext">
                                        <input type="text" class=" tip-right" title="Ingrese Contacto" id="con" data-prompt-position="centerRight" value="<?php echo $cont['CONTACTO'] ?>"> 
                                    </div>
                             </div>

                            <div class="elem elem_med">
                                    <label class="der">Teléfono:</label>
                                    <div class="indent formtext">
                                        <input type="text" class=" tip-right" title="Ingrese Teléfono" id="tel" data-prompt-position="centerRight" value="<?php echo $cont['TELEFONO'] ?>"> 
                                    </div>
                             </div>

                            <div class="elem elem_med">
                                    <label class="der">Teléfono Celular:</label>
                                    <div class="indent formtext">
                                        <input type="text" class=" tip-right" title="Ingrese Valor" id="tel2" data-prompt-position="centerRight" value="<?php echo $cont['TEL_CEL'] ?>"> 
                                    </div>
                             </div>

                            <div class="elem elem_med">
                                    <label class="der">Teléfono Trabajo:</label>
                                    <div class="indent formtext">
                                        <input type="text" class=" tip-right" title="Ingrese Valor" id="tel3" data-prompt-position="centerRight" value="<?php echo $cont['TEL_TRAB'] ?>"> 
                                    </div>
                             </div>

                            <div class="elem elem_med">
                                    <label class="der">Correo:</label>
                                    <div class="indent formtext">
                                        <input type="text" class="tip-right" title="Ingrese Correo" id="ema" data-prompt-position="centerRight" value="<?php echo $cont['CORREO'] ?>"> 
                                    </div>
                            </div>
                            <div class="elem_cerrar"></div>
                            
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="elem_group">
                            
                            <div class="elem elem_med">
                                    <label>Contacto:</label>
                                    <div class="indent formtext">
                                        <input type="text" class=" tip-right" title="Ingrese Valor" id="con" data-prompt-position="centerRight" value="<?php echo (isset($entidad['CONTACTO'])?$entidad['CONTACTO']:"") ?>"> 
                                    </div>
                             </div>

                            <div class="elem elem_med">
                                    <label class="der">Teléfono Fijo:</label>
                                    <div class="indent formtext">
                                        <input type="text" class=" tip-right" title="Ingrese Valor" id="tel" data-prompt-position="centerRight" value="<?php echo (isset($entidad['TELEFONO'])?$entidad['TELEFONO']:"") ?>"> 
                                    </div>
                             </div>

                            <div class="elem elem_med">
                                    <label class="der">Teléfono Celular:</label>
                                    <div class="indent formtext">
                                        <input type="text" class=" tip-right" title="Ingrese Valor" id="tel2" data-prompt-position="centerRight" value="<?php echo (isset($entidad['TELEFONO'])?$entidad['TELEFONO']:"") ?>"> 
                                    </div>
                             </div>

                            <div class="elem elem_med">
                                    <label class="der">Teléfono Trabajo:</label>
                                    <div class="indent formtext">
                                        <input type="text" class=" tip-right" title="Ingrese Valor" id="tel3" data-prompt-position="centerRight" value="<?php echo (isset($entidad['TELEFONO'])?$entidad['TELEFONO']:"") ?>"> 
                                    </div>
                             </div>

                            <div class="elem elem_med">
                                    <label class="der">Correo:</label>
                                    <div class="indent formtext">
                                        <input type="text" class="tip-right" title="Ingrese Valor" id="ema" data-prompt-position="centerRight" value="<?php echo (isset($entidad['CORREO'])?$entidad['CORREO']:"") ?>"> 
                                    </div>
                            </div>
                            <div class="elem_cerrar"></div>
                            
                        </div>
                    <?php endif; ?>
                    </div>
                    
                    <div class="group_more">Agregar Contacto</div>
                        
                     
                     <div class="elem btns">
                          <input id="send" name="send" type="submit" class="button-a gray" value="Enviar" /> &nbsp;&nbsp;
                          <button class="button-a dark-blue" id="btnClear">Limpiar</button>  
                     </div>
               
                 </form>
                 
    		 <div class="clear"> </div>
             </div>
           </div>
        </div>
        <!--Form end-->
        
        <div id="obs_cob">
            <span>Ingrese Clave:</span>
            <input type="password" id="clave_desb" data-nivel="3" />
            <div>
                <button onclick="ingresar_pwd();">Enviar</button>
                <button onclick="salir_pwd();">Cerrar</button>
            </div>
        </div>
       
</div>


