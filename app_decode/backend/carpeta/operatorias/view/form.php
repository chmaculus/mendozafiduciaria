<!--Form-->
 <div class="content-form">
    <form method="post" enctype="multipart/form-data" id="customForm" >
    <div class="grid-1" id="frmagregar">
       <div class="title-grid"><div id="label_action">Agregar</div>Operatoria</div>
       <div class="content-gird">
       <div class="form">

                <input type="hidden" id="idh" value="<?php echo isset($entidad["ID"])?$entidad["ID"]:''; ?>" />
                <input type="hidden" id="val_ok" value="0" />
                <input type="hidden" id="val_entidadesh" value="<?php echo $cad ?>" />
                <input type="hidden" id="provinciah" value="<?php echo isset($entidad["ID_PROVINCIA"])?$entidad["ID_PROVINCIA"]:"" ?>" />
                <input type="hidden" id="localidadh" value="<?php echo isset($entidad["ID_DEPARTAMENTO"])?$entidad["ID_DEPARTAMENTO"]:"" ?>" />

                <input type="hidden" id="operatoriah1" value="<?php echo isset($entidad["ID_TIPO_OPERATORIA"])?$entidad["ID_TIPO_OPERATORIA"]:"" ?>" />
                <input type="hidden" id="localidadh1" value="<?php echo isset($entidad["ID_DEPARTAMENTO"])?$entidad["ID_DEPARTAMENTO"]:"" ?>" />
                <input type="hidden" id="finih" value="<?php echo isset($entidad["FECHA_INICIO"])?$entidad["FECHA_INICIO"]:"" ?>" />
                <input type="hidden" id="ffinh" value="<?php echo isset($entidad["FECHA_FIN"])?$entidad["FECHA_FIN"]:"" ?>" />

                <input type="hidden" id="desh" value="<?php echo isset($entidad["DESEMBOLSOS"])?$entidad["DESEMBOLSOS"]:"" ?>" />
                <input type="hidden" id="devh" value="<?php echo isset($entidad["DEVOLUCIONES"])?$entidad["DEVOLUCIONES"]:"" ?>" />
                <input type="hidden" id="perh" value="<?php echo isset($entidad["PERIODICIDAD"])?$entidad["PERIODICIDAD"]:"" ?>" />
                
                <input type="hidden" id="id_procesoh" value="<?php echo isset($entidad["ID_PROCESO"])?$entidad["ID_PROCESO"]:"" ?>" />
                <input type="hidden" id="jefeopeh" value="<?php echo isset($entidad["JEFEOP"])?$entidad["JEFEOP"]:"" ?>" />
                <input type="hidden" id="cordopeh" value="<?php echo isset($entidad["COORDOPE"])?$entidad["COORDOPE"]:"" ?>" />

                <input type="hidden" id="a_ent" value="<?php echo $_array_checklist ?>" />
                <script type='text/javascript'>
                <?php 
                        echo "var _array_checklist = ". $_array_checklist . ";\n";
                ?>
                </script>

                <div class="elem">
                        <label>Nombre:</label>
                        <div class="indent formtext">
                            <input type="text" class="validate[required] medium" title="Ingrese Nombre" id="nombre" value="<?php echo isset($entidad["NOMBRE"])?$entidad["NOMBRE"]:"" ?>"> 
                        </div>
                </div>

                <div class="elem">
                        <label>Descripcion:</label>
                        <div class="indent formtext">
                            <input type="text" class="medium tip-right" title="Ingrese Descripcion" id="descripcion" value="<?php echo isset($entidad["DESCRIPCION"])?$entidad["DESCRIPCION"]:"" ?>"> 
                        </div>
                </div>

                <?php if(is_array($lst_tipo_operatoria)): ?>
                <div class="elem elem_med" >
                    <label>Tipo:</label>
                    <div class="indent">
                    <select class="validate[required] chzn-select medium-select select" id="tipoope" data-prompt-position="topLeft">
                        <option value="">Elegir Tipo Operatoria</option>
                        <?php foreach($lst_tipo_operatoria as $rs_prov): ?>
                        <option data-connection="<?php echo $rs_prov["ID"] ?>" value="<?php echo $rs_prov["ID"] ?>"><?php echo $rs_prov["TIPO"] ?></option>
                        <?php endforeach; ?>
                    </select>   
                    </div>
                </div>
                <?php endif; ?>

                <div class="elem elem_med">
                        <label>Tope:</label>
                        <div class="indent formtext">
                            <input type="text" class="" title="Ingrese Tope" id="tope" value="<?php echo isset($entidad["TOPE_PESOS"])?$entidad["TOPE_PESOS"]:"" ?>"> 
                        </div>
                </div>
                
                <div class="elem elem_med" >
                    <label>Proceso:</label>
                    <div class="indent">
                    <select class="chzn-select medium-select select" id="id_proceso" data-prompt-position="topLeft">
                        <option value="1">Normal</option>
                        <option value="2">Acotado "A"</option>
                    </select>
                    </div>
                </div>
                
                
                <?php if(is_array($lst_jefeope)): ?>
                <div class="elem elem_med" >
                    <label>Jefe Operac.:</label>
                    <div class="indent">
                    <select class="validate[required] chzn-select medium-select select" id="jefeope" data-prompt-position="topLeft">
                        <option value="">Elegir Jefe Operaciones</option>
                        <?php foreach($lst_jefeope as $rs_jope): ?>
                        <option value="<?php echo $rs_jope["ID"] ?>"><?php echo $rs_jope["NOMBRE"] . " " . $rs_jope["APELLIDO"] ?></option>
                        <?php endforeach; ?>
                    </select>   
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(is_array($lst_coope)): ?>
                <div class="elem elem_med" >
                    <label>Coord. Operac.:</label>
                    <div class="indent">
                        <select class="chzn-select medium-select select" id="cordope" data-prompt-position="topLeft">
                            <option value="">Elegir Coord. Operaciones</option>
                            <?php foreach($lst_coope as $rs_cope): ?>
                            <option value="<?php echo $rs_cope["ID"] ?>"><?php echo $rs_cope["NOMBRE"] . " " . $rs_cope["APELLIDO"] ?></option>
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
              <li><a href="#tabs-1"><img width="20" src="http://localhost/fideicomiso/general/css/images/32x32/User.png" borde=""/>Créditos</a></li>
              <li><a href="#tabs-2"><img width="20" src="http://localhost/fideicomiso/general/css/images/32x32/System.png" borde=""/>Adjuntos</a></li>
              <li><a href="#tabs-3"><img width="20" src="http://localhost/fideicomiso/general/css/images/32x32/Template.png" borde=""/>Checklist</a></li>
          </ul>
      </div>
      <div class="content-gird">
          <div id="tabs-1">
              <div class="form_cuentbanco">
                  <div class="elem elem_med">
                      <label>Banco Convenio:</label>
                        <div class="indent formtext">
                            <?php
                            $banco = isset($entidad["BANCO"]) ? $entidad["BANCO"] : 0;
                            ?>
                            <select id="bancoope" class="chzn-select medium-select select">
                                <option data-connection="0" value="0" <?=($banco == 0) ? 'selected="selected"' : "" ?>>Sin banco</option>
                                <option data-connection="1" value="1" <?=($banco == 1) ? 'selected="selected"' : "" ?>>Banco Nacion</option>
                                <option data-connection="2" value="2" <?=($banco == 2) ? 'selected="selected"' : "" ?>>Banco Superville</option>
                            </select>
                        </div>
                  </div>
                  
                  <div class="elem elem_med">
                      <label>IVA:</label>
                      <div class="indent formtext">
                          <input type="text" class="validate[required]" title="Ingrese Tasa de Interés Compensatoria" id="ivaope" value="<?php echo isset($entidad["IVA"])?$entidad["IVA"]:"" ?>"> 
                      </div>
                  </div>
                  
                  <div class="elem elem_med">
                      <label>Tasa de Interés Compensatoria:</label>
                      <div class="indent formtext">
                          <input type="text" class="" title="Ingrese Tasa de Interés Compensatoria" id="tasa_ic" value="<?php echo isset($entidad["TASA_INTERES_COMPENSATORIA"])?$entidad["TASA_INTERES_COMPENSATORIA"]:"" ?>"> 
                      </div>
                  </div>

                  <div class="elem elem_med">
                      <label>Tasa de Interés Moratoria:</label>
                      <div class="indent formtext">
                          <input type="text" class="" title="Ingrese Tasa de Interés Moratoria" id="tasa_im" value="<?php echo isset($entidad["TASA_INTERES_MORATORIA"])?$entidad["TASA_INTERES_MORATORIA"]:"" ?>">
                      </div>
                  </div>

                  <div class="elem elem_med">
                      <label>Tasa de Interés por Punitorios:</label>
                      <div class="indent formtext">
                          <input type="text" class="" title="Ingrese Tasa de Interés por Punitorios" id="tasa_ip" value="<?php echo isset($entidad["TASA_INTERES_POR_PUNITORIOS"])?$entidad["TASA_INTERES_POR_PUNITORIOS"]:"" ?>">
                      </div>
                  </div>

                  <div class="elem elem_med">
                      <label>Tasa Subsidiada:</label>
                      <div class="indent formtext">
                          <input type="text" class="" title="Ingrese Tasa Subsidiada" id="tasa_is" value="<?php echo isset($entidad["TASA_SUBSIDIADA"])?$entidad["TASA_SUBSIDIADA"]:"" ?>"> 
                      </div>
                  </div>

                  <div class="elem elem_med">
                      <label>Desembolsos:</label>
                      <div class="indent formtext">
                          <div id='desembolsos'></div>
                      </div>
                  </div>

                  <div class="elem elem_med">
                      <label>Devoluciones:</label>
                      <div class="indent formtext">
                          <div id='devoluciones'></div>
                      </div>
                  </div>


                  <div class="elem elem_med">
                      <label>Periodicidad (meses):</label>
                      <div class="indent formtext">
                          <div id='periodicidad'></div>
                      </div>
                  </div>
                  
                  <div class="cb" /><hr />
                  
                  <div class="elem">
                      <label style="float:none">CONDICIONES DE CRÉDITOS:</label>
                  </div>
                  
                  <div class="elem elem_med">
                      <label>Actualización de compensatorios:</label>
                      <div class="indent formtext">
                          <input type="checkbox" id="act_compens" value="1" <?=isset($entidad["ACT_COMPENS"]) && $entidad["ACT_COMPENS"] ? 'checked="checked"' : '' ?> /> 
                      </div>
                  </div>

                  
                  <?php if ($imputacion_tasas) { ?>
                  <div class="cb" /><hr />

                  <div class="elem elem_med">
                      <label>Impactar cambio de tasas en créditos:</label>
                      <div class="indent formtext">
                          <input type="checkbox" id="imp_tasas"> 
                      </div>
                  </div>

                  <div class="elem elem_med">
                      <label>Fecha cambio de tasas créditos:</label>
                      <div class="indent formtext">
                          <input type="text" class="fecha" title="Ingrese fecha de cambio de tasas" id="fec_imp_tasas" value="<?=date('d-m-Y')?>"> 
                      </div>
                  </div>
                  <?php } ?>
              </div>
          </div>

          <div id="tabs-2">
                <div class="elem myfile" id="fot_car">
                      <label>Adjunto:</label>
                      <div class="indent">
                      </div> 
                </div>
                <ul class="lista_ents">
                <?php 
                    if(is_array($lst_uploads)):
                        foreach($lst_uploads as $rs_up):
                            echo '<li data-ruta="'.$rs_up['NOMBRE'].'" data-identidad="'.$rs_up['ID_OPERATORIA'].'">'.basename($rs_up['NOMBRE']).'</li><a class="download_file" href="general/extends/extra/download.php?file='.$rs_up['NOMBRE'].'" title="Descargar Archivo"></a>';
                        endforeach;
                    endif;
                ?>
                </ul>

          </div>

          <div id="tabs-3">
              <div class="checklist_cont">
                  <div data-gridname="listbox" style="display:none" class="refresgrid">Refresh Grid</div>
                  <div id="listbox"></div>
              </div>
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

    
    
     
<form id="upload_file1" action="backend/carpeta/operatorias/get_file1" target="enviar_archivo" method="post" enctype="multipart/form-data">
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