<!--Form-->
 <div class="content-form">
        <form method="post" enctype="multipart/form-data" id="customForm" >
        <div class="grid-1" id="frmagregar">
           <div class="title-grid"><div id="label_action">Agregar</div>Fideicomiso</div>
           <div class="content-gird">
           <div class="form">
                
                    <input type="hidden" id="idh" value="<?php echo isset($entidad["ID"])?$entidad["ID"]:''; ?>" />
                    <input type="hidden" id="val_ok" value="0" />
                    <input type="hidden" id="val_entidadesh" value="<?php echo $cad ?>" />
                    <input type="hidden" id="provinciah" value="<?php echo isset($entidad["ID_PROVINCIA"])?$entidad["ID_PROVINCIA"]:"" ?>" />
                    <input type="hidden" id="localidadh" value="<?php echo isset($entidad["ID_DEPARTAMENTO"])?$entidad["ID_DEPARTAMENTO"]:"" ?>" />
                    
                    <input type="hidden" id="origenh" />
                    <input type="hidden" id="nombreh" />
                    
                    <input type="hidden" id="provinciah1" value="<?php echo isset($entidad["ID_PROVINCIA"])?$entidad["ID_PROVINCIA"]:"" ?>" />
                    <input type="hidden" id="localidadh1" value="<?php echo isset($entidad["ID_DEPARTAMENTO"])?$entidad["ID_DEPARTAMENTO"]:"" ?>" />
                    <input type="hidden" id="finih" value="<?php echo isset($entidad["FECHA_INICIO"])?$entidad["FECHA_INICIO"]:"" ?>" />
                    <input type="hidden" id="ffinh" value="<?php echo isset($entidad["FECHA_FIN"])?$entidad["FECHA_FIN"]:"" ?>" />
                    <input type="hidden" id="a_ent" value="<?php echo $_array_entidades ?>" />
                    <script type='text/javascript'>
                    <?php 
                            echo "var javascript_array = ". $_array_entidades . ";\n";
                            echo "var _array_bancos_e = ". $_array_bancos_e . ";\n";
                            echo "var _array_aportes_e = ". $_array_aportes_e . ";\n";
                    ?>
                    </script>

                    <div class="elem">
                            <label>Nombre:</label>
                            <div class="indent formtext">
                                <input type="text" class="validate[required] medium" title="Ingrese Nombre" id="nombre" value="<?php echo isset($entidad["NOMBRE"])?$entidad["NOMBRE"]:"" ?>"> 
                            </div>
                    </div>
                                       
                    
                    <div class="elem elem_med">
                            <label>Fecha Inicio:</label>
                            <div class="indent formtext">
                                <input maxlength="10" type="text" class="validate[required,funcCall[validarFecha]]" title="Ingrese Fecha Inicio" id="fini" value=""> 
                            </div>
                    </div>
                    
                    <div class="elem elem_med">
                            <label>Fecha Fin:</label>
                            <div class="indent formtext">
                                <input type="text" maxlength="10" class="validate[required,funcCall[validarFecha]]" title="Ingrese Fecha Fin" id="ffin" value="<?php echo isset($entidad["FECHA_FIN"])?$entidad["FECHA_FIN"]:"" ?>"> 
                            </div>
                    </div>
                                        
                    <div class="elem">
                            <label>Descripcion:</label>
                            <div class="indent formtext">
                                <input type="text" class="medium tip-right" title="Ingrese Descripcion" id="descripcion" value="<?php echo isset($entidad["DESCRIPCION"])?$entidad["DESCRIPCION"]:"" ?>"> 
                            </div>
                    </div>
                    
                    <div class="elem elem_med">
                            <label>CUIT:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Cuit" maxlength="11" id="cuit" value="<?php echo isset($entidad["CUIT"])?$entidad["CUIT"]:"" ?>">
                            </div>
                    </div>
                    
                    <div class="elem elem_med">
                            <label>Monto Máximo:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Localidad" id="montom" value="<?php echo isset($entidad["MONTOMAX"])?$entidad["MONTOMAX"]:"" ?>"> 
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
                    
                    
                 
    		 <div class="clear"> </div>
             </div>
           </div>
        </div>
     
     
     
        <!--Form end-->
        
        
        <!--Tabs-->
          <div class="grid-1 tab" id="tabs">
            <div class="title-grid tabs">
                <ul class="tabNavigation">
                    <li><a href="#tabs-1"><img width="20" src="http://localhost/fideicomiso/general/css/images/32x32/User.png" borde=""/>Entidades</a></li>
                    <li><a href="#tabs-2"><img width="20" src="http://localhost/fideicomiso/general/css/images/32x32/System.png" borde=""/>Operatoria</a></li>
                    <li><a href="#tabs-3"><img width="20" src="http://localhost/fideicomiso/general/css/images/32x32/Template.png" borde=""/>Cuentas Bancarias</a></li>
                    <li><a href="#tabs-4"><img width="20" src="http://localhost/fideicomiso/general/css/images/32x32/Forward.png" borde=""/>Aportes</a></li>   
                    <li><a href="#tabs-5"><img width="20" src="http://localhost/fideicomiso/general/css/images/32x32/Security.png" borde=""/>Adjuntos</a></li>
                </ul>
             </div>
            <div class="content-gird">   
                <div id="tabs-1">
                    <div class="contenedor_entidades">
                        <?php if ($lst_tipoentidades): ?>
                        <?php foreach ($lst_tipoentidades as $rs): ?>
                            <p><input id="<?php echo $rs["ID"] ?>" class="radio" type="radio" name="2" value="<?php echo $rs["ID"] ?>" class="styled" /> <label for="<?php echo $rs["ID"] ?>"><?php echo $rs["NOMBRE"] ?></label></p>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="contenedor_entidades2">
                        <select class="chzn-select medium-select1 select" id="entidades_sel" data-prompt-position="topLeft">
                            <option value="">Elegir Entidad</option>
                        </select>
                    </div>   
                    <div class="lista_opciones add"></div>
                    <ul class="lista_ents"></ul>
                </div>
                
                
                <div id="tabs-2">
                    
                    
                    <div class="contenedor_operatorias e3">
                        <?php if(is_array($lst_operatorias)): ?>
                        <div class="elem">
                            <label>Operatoria:</label>
                            <div class="indent">
                            <select class="chzn-select large-select select" id="operatorias" data-prompt-position="topLeft">
                                <option value="">Elegir Operatoria</option>
                                <?php foreach($lst_operatorias as $rs_ope): ?>
                                <option data-connection="<?php echo $rs_ope["ID"] ?>" value="<?php echo $rs_ope["ID"] ?>"><?php echo $rs_ope["NOMBRE"] ?></option>
                                <?php endforeach; ?>
                            </select>   
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="lista_opciones addope"></div>
                    <ul class="lista_ope">
                        <?php 
                            if( isset($lst_operatorias_f) && is_array($lst_operatorias_f) ):
                                foreach($lst_operatorias_f as $rs_op):
                                    echo '<li data-identidad="'.$rs_op['ID_OPERATORIA'].'">'.$rs_op['nom_ope'].'</li>';
                                endforeach;
                            endif;
                        ?>
                    </ul>
                    
                </div>
                
                
                
                <div id="tabs-3">
                    <div class="form_cuentbanco">
                        <div class="elem elem_med">
                            <label>Banco:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Banco" id="banco_banco" value=""> 
                            </div>
                        </div>

                        <div class="elem elem_med">
                            <label>Firmantes:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Titular" id="banco_titular" value=""> 
                            </div>
                        </div>
                        
                        <div class="elem elem_med">
                            <label>Cuit:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Cuit" id="banco_cuit" value=""> 
                                
                            </div>
                        </div>

                        <div class="elem elem_med">
                            <label>Nro. Cuenta:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Nro Cuenta" id="banco_nrocuenta" value=""> 
                            </div>
                        </div>
                        
                        <div class="elem elem_med">
                            <label>CBU:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese CBU" id="banco_cbu" value=""> 
                            </div>
                        </div>
                        
                        <div class="lista_opciones add" id="add_banco"></div>
                        
                        <div id="jqxgridbancos"></div>
                        
                        
                    </div>
                </div>
                
                
                <div id="tabs-4">
                    <div class="form_aportes">
                        <div class="elem elem_med" >
                            <label>Origen:</label>
                            <div class="indent">
                            <select class="chzn-select medium-select select" id="aporte_origen">
                                <option value="">Elegir Origen</option>
                                <option data-connection="2" value="2">Fiduciante</option>
                                <option data-connection="21" value="21">Inversor</option>
                            </select>   
                            </div>
                        </div>

                        <div class="elem elem_med" >
                            <label>Nombre:</label>
                            <div class="indent" id="div_nombreorigen">
                            <select class="chzn-select medium-select select" id="aporte_nombre">
                                <option value="">Elegir Entidad</option>
                            </select>   
                            </div>
                        </div>
                        
                        <div class="elem elem_med">
                            <label>Aporte:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Aporte" id="aporte_aporte" value=""> 
                            </div>
                        </div>
                        
                        <div class="elem elem_med">
                            <label>Fecha Aporte:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Fecha Aporte" id="aporte_fecha" value=""> 
                            </div>
                    </div>
                        

                        <div class="elem elem_med">
                            <label>Observación:</label>
                            <div class="indent formtext">
                                <input type="text" class="" title="Ingrese Observación" id="aporte_obs" value=""> 
                            </div>
                        </div>
                        
                        <div class="lista_opciones add" id="add_aporte"></div>
                        
                        <div id="jqxgridaportes"></div>
                        <div class="suma_aportes"><span>Total:</span>$<span id="suma_aporte"></span></div>
                        
                        
                    </div>
                </div>
                <div id="tabs-5">
                    <div class="elem myfile" id="fot_car">
                        <label>Adjunto:</label>
                        <div class="indent">
                        </div> 
                    </div>
                    <ul class="lista_uploads">
                    <?php 
                      if( isset($lst_uploads) && is_array($lst_uploads) ):
                          foreach($lst_uploads as $rs_up):
                              echo '<li data-ruta="'.$rs_up['NOMBRE'].'" data-identidad="'.$rs_up['ID_FIDEICOMISO'].'">'.basename($rs_up['NOMBRE']).'</li><a class="download_file" href="general/extends/extra/download.php?file='.$rs_up['NOMBRE'].'" title="Descargar Archivo"></a>';
                          endforeach;
                      endif;
                    ?>
                    </ul>
                </div>
                
              </div> 
           </div>
		 <!--Tabs end-->
                    <div class="elem elempie">
                        <div class="indent">
                          <input id="send" name="send" type="submit" class="button-a gray" value="Enviar" /> &nbsp;&nbsp;
                          <button class="button-a dark-blue" id="btnClear">Limpiar</button>  
                        </div>
                    </div>
                 
            </form>
          
        <form id="upload_file1" action="backend/carpeta/fideicomiso/get_file1" target="enviar_archivo" method="post" enctype="multipart/form-data">
            <div class="uploader black">
                <input type="text"  class="filename" readonly="readonly" id="lblfile"/>
                <input type="button" class="button_files " value="Examinar..."/>
                <input type="file" name="imagen" id="imagen"/>
                <input type="hidden" name="semilla" id="semilla" value="<?php echo $_semilla ?>"/>
                <?php if (isset($entidad["ID"])): ?>
                <input type="hidden" name="id_edit" id="id_edit" value="<?php echo $entidad["ID"] ?>"/>
                <?php else: ?>
                <input type="hidden" name="id_edit" id="id_edit" value="0"/>
                <?php endif; ?>
            </div>
            <input id="btnSubirfile" name="btnSubirfile" type="submit" class="button-a dark-blue" value="Upload" /> &nbsp;&nbsp;
        </form>
        <iframe name="enviar_archivo" id="enviar_archivo"></iframe>
                 
       
</div>