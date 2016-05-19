<!--Form-->
<div class="content-form">
    <div class="grid-1" id="frmagregar">
        <div class="title-grid"><div id="label_action">Agregar</div>Entidades</div>
        <div class="content-gird">
            <div class="form">
                <form target="enviar_archivo" method="post" enctype="multipart/form-data" id="customForm" >
                    <input type="hidden" id="idh" value="<?php echo isset($entidad["ID"]) ? $entidad["ID"] : ''; ?>" />
                    <input type="hidden" id="val_ok" value="0" />
                    <input type="hidden" id="val_entidadesh" value="<?php echo $cad ?>" />

                    <div class="elem">
                        <label>Nombre:</label>
                        <div class="indent formtext">
                            <input type="text" class="validate[required] medium" title="Ingrese Nombre" id="nombre" value="<?php echo $entidad["NOMBRE"] ?>"> 
                        </div>
                    </div>

                    <div class="elem">
                        <label>Descripción:</label>
                        <div class="indent formtext">
                            <input type="text" class="medium tip-right" title="Ingrese Descripción" id="descripcion" value="<?php echo $entidad["DESCRIPCION"] ?>"> 
                        </div>
                    </div>

                    <div class="elem elem_med">
                        <label>CUIT:</label>
                        <div class="indent formtext">
                            <input type="text" class="" title="Ingrese CUIT" id="cuit" value="<?php echo $entidad["CUIT"] ?>"> 
                        </div>
                    </div>

                    <div class="elem elem_med">
                        <label>Teléfono:</label>
                        <div class="indent formtext">
                            <input type="text" class="" title="Ingrese teléfono" id="telefono" value="<?php echo $entidad["TELEFONO"] ?>"> 
                        </div>
                    </div>


                    <div class="elem">
                        <label>Entidad:</label>
                        <?php if ($lst_tipoentidades): ?>
                            <div class="indent">
                                <select class="chzn-select medium-select select" multiple data-placeholder="Selecciones Entidad/es" id="tipo_entidades">
                                    <?php foreach ($lst_tipoentidades as $rs_ent): ?>
                                        <option value="<?php echo $rs_ent["ID"] ?>"><?php echo $rs_ent["NOMBRE"] ?></option>
                                    <?php endforeach; ?>
                                </select> 
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="elem">
                        <label>Organismo:</label>
                        <div class="indent formtext">
                            <input type="text" class="medium tip-right" title="Ingrese Organismo" id="organismo" value="<?php echo $entidad["ORGANISMO"] ?>">
                        </div>
                    </div>

                    <div class="elem">
                        <label>Domicilio:</label>
                        <div class="indent formtext">
                            <input type="text" class="medium tip-right" title="Ingrese Domicilio" id="domicilio" value="<?php echo $entidad["DOMICILIO"] ?>">
                        </div>
                    </div>


                        <div class="elem elem_med">
                            <label>Provincia:</label>
                            <div class="indent">
                                <select class="chzn-select medium-select-prov select" id="provincia" style="width: 100px;">
                                    <option value="">Elegir Provincia</option>
                                    <?php if($lst_provincias) { ?>
                                    <?php foreach ($lst_provincias as $rs_prov): ?>
                                        <option data-connection="<?php echo $rs_prov["ID"] ?>" value="<?php echo $rs_prov["ID"] ?>"><?php echo $rs_prov["PROVINCIA"] ?></option>
                                    <?php endforeach; ?>
                                    <?php } ?>
                                </select>   
                            </div>
                        </div>
                    
<!--            <div class="elem elem_med">
                            <label>Localidad:</label>
                            <div class="indent">
                                <select class="chzn-select medium-select-loc select" id="localidad">
                                    <option value="">Elegir Localidad</option>
                                    <?php // if($lst_localidad) { ?>
                                    <?php // foreach ($lst_localidad as $rs_loc): ?>
                                        <option data-local="<?php // echo $rs_iva["LOCAL"]  ?>" data-connection="<?php // echo $rs_iva["ID"]  ?>" value="<?php // echo $rs_iva["ID"]  ?>"><?php // echo $rs_iva["NOMBRE"]  ?></option>
                                        <option data-connection="<?php // echo $rs_loc["ID"] ?>" value="<?php // echo $rs_loc["ID"] ?>"><?php // echo $rs_loc["LOCALIDAD"] ?></option>
                                    <?php // endforeach; ?>
                                    <?php // } ?>
                                </select>   
                            </div>
                        </div>-->

                    <div class="elem elem_med">
                        <label>Representante:</label>
                        <div class="indent formtext">
                            <input type="text" class="" title="Ingrese Representante" id="representante" value="<?php echo $entidad["REPRESENTANTE"] ?>"> 
                        </div>
                    </div>

                    <div class="elem elem_med">
                        <label>Mail:</label>
                        <div class="indent formtext">
                            <input type="text" class="" title="Ingrese Mail" id="mail" value="<?php echo $entidad["MAIL"] ?>"> 
                        </div>
                    </div>

                    <div class="elem elem_med">
                        <label>Situación IVA:</label>
                        <div class="indent formtext">
                            <input type="text" class="" title="Ingrese Situación Iva" id="situacion_iva" value="<?php echo $entidad["SITUACION_IVA"] ?>"> 
                        </div>
                    </div>

                    <div class="elem elem_med">
                        <label>Situación IIBB:</label>
                        <div class="indent formtext">
                            <input type="text" class="" title="Ingrese Situación IIBB" id="situacion_iibb" value="<?php echo $entidad["SITUACION_IIBB"] ?>"> 
                        </div>
                    </div>

                    <div class="elem elem_med">
                        <label>Limite Kgrs:</label>
                        <div class="indent formtext">
                            <input type="text" class="" title="Ingrese limite" id="limite" value="0"> 
                        </div>
                    </div>
                    <div class="elem elem_med">
                        <label>Limite Litros:</label>
                        <div class="indent formtext">
                            <input type="text" class="" title="Ingrese limite" id="limiteLitros" value="0"> 
                        </div>
                    </div>

                    <div class="elem elempie">
                        <div class="indent">
                            <input id="send" name="send" type="submit" class="button-a gray" value="Enviar" /> &nbsp;&nbsp;
                            <button class="button-a dark-blue" id="btnClear">Limpiar</button>  
                            <button class="button-a red to-right" id="btnBorrar">Borrar esta Entidad</button>  

                        </div>
                    </div>

                </form>
                <div class="clear"> </div>
            </div>
        </div>
    </div>
    <!--Form end-->

</div>