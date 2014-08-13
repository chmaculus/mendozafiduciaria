<div class="content-formchk frm_garantia">
        <input type="hidden" id="tipo_garantiah" value="<?php echo isset($datos_gar["0"]["ID_TIPO"])?$datos_gar["0"]["ID_TIPO"]:'0' ?>" />
        <input type="hidden" id="objetoh" value="<?php echo isset($datos_gar["0"]["ID_OBJETO"])?$datos_gar["0"]["ID_OBJETO"]:'0' ?>" />
        
        <input type="hidden" id="id_tipoh" value="<?php echo isset($datos_gar["0"]["ID_TIPO"])?$datos_gar["0"]["ID_TIPO"]:'0' ?>" />
        <input type="hidden" id="id_objetoh" value="<?php echo isset($datos_gar["0"]["ID_OBJETO"])?$datos_gar["0"]["ID_OBJETO"]:'0' ?>" />
        <input type="hidden" id="id_estadoh" value="<?php echo isset($datos_gar["0"]["ID_ESTADO"])?$datos_gar["0"]["ID_ESTADO"]:'0' ?>" />
        <input type="hidden" id="id_tasadorh" value="<?php echo isset($datos_gar["0"]["ID_TASADOR"])?$datos_gar["0"]["ID_TASADOR"]:'0' ?>" />
        
        <input type="hidden" id="id_tipodato1h" value="<?php echo isset($datos_gar["0"]["TIPO_DATO_1"])?$datos_gar["0"]["TIPO_DATO_1"]:'0' ?>" />
        <input type="hidden" id="id_tipodato2h" value="<?php echo isset($datos_gar["0"]["TIPO_DATO_2"])?$datos_gar["0"]["TIPO_DATO_2"]:'0' ?>" />
        <input type="hidden" id="id_tipodato3h" value="<?php echo isset($datos_gar["0"]["TIPO_DATO_3"])?$datos_gar["0"]["TIPO_DATO_3"]:'0' ?>" />
        
        <input type="hidden" id="fechadesdeh" value="<?php echo isset($datos_gar["0"]["FECHA_DESDE"])?convertirFecha($datos_gar["0"]["FECHA_DESDE"]):"0" ?>" />
        <input type="hidden" id="fechahastah" value="<?php echo isset($datos_gar["0"]["FECHA_HASTA"])?convertirFecha($datos_gar["0"]["FECHA_HASTA"]):"0" ?>" />
        
        <input type="hidden" id="tasaf1h" value="<?php echo isset($datos_gar["0"]["TASA_F1"])?convertirFecha($datos_gar["0"]["TASA_F1"]):"0" ?>" />
        <input type="hidden" id="tasaf2h" value="<?php echo isset($datos_gar["0"]["TASA_F1"])?convertirFecha($datos_gar["0"]["TASA_F1"]):"0" ?>" />
        
        
        <div class="title">Garantias</div>
    
        <div class="elem elem_med_gar">
                <label class="der">Operación:</label>
                <div class="indent formtext">
                    <input type="text" class="tip-right" id="operacion" value="<?php echo isset($datos_gar["0"]["ID_OPERACION"])?$datos_gar["0"]["ID_OPERACION"]:'' ?>" readonly>
                </div>
        </div>
    
        <div class="elem elem_med_cli">
                <label class="der">Titular:</label>
                <div class="indent formtext">
                    <input type="text" class="tip-right" id="gar_cliente" value="<?php echo isset($nom_clientes)?$nom_clientes." ":"" ?>" readonly> 
                </div>
        </div>
        
        <?php if(is_array($lst_tipo_garantia)): ?>
        <div class="elem elem_med_gar" >
            <label>Tipo de Garantia:</label>
            <div class="indent">
            <select class="chzn-select medium-select1 select" id="tipo_garantia">
                <option value="">Elegir tipo de garantia</option>
                <?php foreach($lst_tipo_garantia as $rs_gar): ?>
                <option data-connection="<?php echo $rs_gar["ID"] ?>" value="<?php echo $rs_gar["ID"] ?>"><?php echo $rs_gar["TIPO"] ?></option>
                <?php endforeach; ?>
            </select>   
            </div>
        </div>
        <?php endif; ?>
        
        
        <div class="elem elem_med" >
            <label></label>
            <div class="indent" style="visibility: hidden">
                <input type="text" />
            </div>
        </div>
        
        
        <div class="elem elem_med">
            <label>Objeto:</label>
            <div class="indent" id="div_objeto">
             <select class="chzn-select medium-select1 select" id="objeto">
                <option value="">Elegir Objeto</option>
            </select>
            </div>
        </div>
        
        <div class="elem elem_med" >
            <label></label>
            <div class="indent" style="visibility: hidden">
                <input type="text" />
            </div>
        </div>
    
        <?php if(is_array($lst_estado)): ?>
        <div class="elem elem_med_gar" >
            <label>Estado:</label>
            <div class="indent">
            <select class="chzn-select medium-select4 select" id="estado_garantia">
                <option value="">Elegir estado</option>
                <?php foreach($lst_estado as $rs_est): ?>
                <option value="<?php echo $rs_est["ID"] ?>"><?php echo $rs_est["ESTADO"] ?></option>
                <?php endforeach; ?>
            </select>   
            </div>
        </div>
        <?php endif; ?>
        
        
        <div class="elem elem_med mod1">
            <label>Fecha Inicio:</label>
            <div class="indent formtext">
                <input maxlength="10" type="text" class="" title="Ingrese Fecha Inicio" id="fini" value="<?php echo isset($entidad["FECHA_INICIO"])?$entidad["FECHA_INICIO"]:"" ?>"> 
            </div>
        </div>

        <div class="elem elem_med">
            <label>Fecha Fin:</label>
            <div class="indent formtext">
                <input maxlength="10" type="text" class="" title="Ingrese Fecha Fin" id="ffin" value="<?php echo isset($entidad["FECHA_FIN"])?$entidad["FECHA_FIN"]:"" ?>"> 
            </div>
        </div>

        <?php if(is_array($lst_tasadores)): ?>
        <div class="elem elem_med_gar" >
            <label>Tasadores:</label>
            <div class="indent">
                <select class="chzn-select medium-select1 select" id="tasador">
                    <option value="">Elegir tasador</option>
                    <?php foreach($lst_tasadores as $rs_tas): ?>
                    <option value="<?php echo $rs_tas["ID"] ?>"><?php echo $rs_tas["NOMBRE"] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="elem elem_med mod1">
            <label>Solicitud Tasación:</label>
            <div class="indent formtext">
                <input type="text" class="" title="Ingrese Fecha Inicio" id="soltas" value=""> 
            </div>
        </div>

        <div class="elem elem_med">
            <label>Presen. Tasación:</label>
            <div class="indent formtext">
                <input type="text" class="" title="Ingrese Fecha Fin" id="pretas" value=""> 
            </div>
        </div>
        
 
        <div class="elem elem_med_gar" style="clear:left;">
                <label>Valor Tasación:</label>
                <div class="indent formtext">
                    <input type="text" class="" title="" id="valortas" value="<?php echo isset($datos_gar["0"]["TAS_VALOR"])?$datos_gar["0"]["TAS_VALOR"]:'' ?>"> 
                </div>
        </div>
        
        <div class="elem elem_med_gar">
                <label>Aforo(%):</label>
                <div class="indent formtext">
                    <input type="text" class="" title="" id="aforo" value="<?php echo isset($datos_gar["0"]["TAS_AFORO"])?$datos_gar["0"]["TAS_AFORO"]:'' ?>"> 
                </div>
        </div>
        
        <div class="elem elem_med_gar">
                <label>Valor Garantía:</label>
                <div class="indent formtext">
                    <input type="text" class="" title="" id="valorgar" value="<?php echo isset($datos_gar["0"]["VALOR_GARANTIA"])?$datos_gar["0"]["VALOR_GARANTIA"]:'' ?>" readonly> 
                </div>
        </div>
        
        
        
        <div class="elem elem_med_gar" >
            <label>Tipo Dato1:</label>
            <div class="indent">
            <select class="chzn-select medium-select2 select" id="tipodato1">
                <option value="">(Vacio)</option>
                <option value="1">Patente</option>
                <option value="2">Cuit</option>
            </select>   
            </div>
        </div>
        
        <div class="elem elem_med_gar"></div>

        <div class="elem elem_med_gar">
            <label>Dato 1:</label>
            <div class="indent formtext">
                <input type="text" class="" title="Ingrese" id="dato1" value="<?php echo isset($datos_gar["0"]["DATO_1"])?$datos_gar["0"]["DATO_1"]:'' ?>"> 
            </div>
        </div>
        
        
        <div class="elem elem_med_gar" >
            <label>Tipo Dato2:</label>
            <div class="indent">
            <select class="chzn-select medium-select2 select" id="tipodato2">
                <option value="">(Vacio)</option>
                <option value="1">Patente</option>
                <option value="2">Cuit</option>
            </select>   
            </div>
        </div>
        
        <div class="elem elem_med_gar"></div>

        <div class="elem elem_med_gar">
            <label>Dato 2:</label>
            <div class="indent formtext">
                <input type="text" class="" title="Ingrese" id="dato2" value="<?php echo isset($datos_gar["0"]["DATO_2"])?$datos_gar["0"]["DATO_2"]:'' ?>"> 
            </div>
        </div>
        
        <div class="elem elem_med_gar" >
            <label>Tipo Dato3:</label>
            <div class="indent">
            <select class="chzn-select medium-select2 select" id="tipodato3">
                <option value="">(Vacio)</option>
                <option value="1">Patente</option>
                <option value="2">Cuit</option>
            </select>   
            </div>
        </div>
        
        <div class="elem elem_med_gar"></div>

        <div class="elem elem_med_gar">
            <label>Dato 3:</label>
            <div class="indent formtext">
                <input type="text" class="" title="Ingrese" id="dato3" value="<?php echo isset($datos_gar["0"]["DATO_3"])?$datos_gar["0"]["DATO_3"]:'' ?>"> 
            </div>
        </div>
        
        <div class="grid-1 grid_adjuntos_gar">
            <div class="title-grid">Adjuntos</div>
            <div class="content-gird" style="display: block;">
                <ul class="lista_adjuntos">
                <?php
                      if( isset($lst_uploads_gar) && is_array($lst_uploads_gar) ):
                          foreach($lst_uploads_gar as $rs_up):
                              echo '<li class="eta-'.$rs_up['ID'].'" data-descripcion="'.$rs_up['NOMBRE'].'" data-ruta="'.$rs_up['NOMBRE'].'" data-identidad="'.$rs_up['ID_GARANTIA'].'">'.basename($rs_up['NOMBRE']).' - Subido por: '.$rs_up['USUARIO_NOMBRE'].' <span>'. date("d/m/Y", strtotime($rs_up['CREATEDON'])) .'</span></li><a class="download_file" href="general/extends/extra/download.php?file='.$rs_up['NOMBRE'].'" title="Descargar Archivo"></a>';
                          endforeach;
                      endif;
                ?>
                </ul>
                <div class="clear"></div>
           </div>
        </div>
        
        
        
        <div class="elem elempie">
            <div class="indent">
                <div class="button-a blue send_guardargarantia"><span>Guardar Garantia</span></div>
            </div>
        </div>
        

</div>

