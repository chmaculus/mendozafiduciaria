<div class="form-content">
    <div class="form_generar">
        <div class="row c12 grupo">
            <div class="c3">
                <span class="titulo-seccion">
                    General
                </span>
            </div>
            <div class="c8">
                    <div class="row">
                        <div class="c5">
                            <span >CREDITO ID</span>
                        </div>
                        <div class="c7">
                            <input type="text" class="" id="txtCreditoID-opc" value="<?=$credito['ID']?>"/>
                        </div>
                    </div>

                <div class="row">
                    <div class="c5">
                        <span >Microcredito</span>
                    </div>
                    <div class="c7">
                        <input type="checkbox" id="chkMicro" value="1" />
                    </div>
                </div>                
                <div class="row">
                    <div class="c5">
                        <span >Cantidad cuotas</span>
                    </div>
                    <div class="c7">
                        <input type="text" class="" id="txtCantidadCuotas" value="<?= max($credito['INTERES_CUOTAS'], $credito['CAPITAL_CUOTAS']) ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="c5">
                        <span >Cuotas de gracia</span>
                    </div>
                    <div class="c7">
                        <input type="text" class="" id="txtCantidadCuotasGracia" value="<?= abs($credito['INTERES_CUOTAS'] - $credito['CAPITAL_CUOTAS']) ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="c5">
                        <span >Primer vencimiento</span>
                    </div>
                    <div class="c7">
                        <input type="text" class="fecha" id="txtPrimerVencimiento" value="<?= $credito['INTERES_VTO'] ?>"/>
                    </div>
                </div>   
            </div>
        </div>
        <div class="row c12 div-intereses grupo">
            <div class="c3">
                <span class="titulo-seccion">
                    Intereses
                </span>
            </div>                
            <div class="c8 ">
                <div class="row">
                    <div class="c5">
                        <span >Periodicidad</span>
                    </div>
                    <div class="c7">
                        <input type="text" class="" id="txtPeriodicidad" value="<?= $credito['INTERES_CUOTAS'] ?>"/>
                    </div>
                </div>
                <div class="row">
                    <div class="c5">
                        <span >Sistema de Interes</span>
                    </div>
                    <div class="c7">
                        <div class="row">
                            <div class="c1">
                                <input type="radio"  id="chkInteresSimple" name="tipoInteres" value="0" checked="" onclick="change_interes();"/>
                            </div>
                            <div class="c3">
                                <span >Interes Simple</span>
                            </div>

                            <div class="c1">
                                <input type="radio"  id="chkInteresCompuesto" name="tipoInteres" value="1" checked="checked" onclick="change_interes();"/>
                            </div>
                            <div class="c3">
                                <span >Interes Compuesto</span>
                            </div>
                        </div>
                    </div>
                </div>        

                <div class=" row tasa_periodo">
                    <div class="c5">
                        <span >Periodicidad Tasa (en dias)</span>
                    </div>
                    <div class="c7">
                        <input type="text" class="" id="txtPeriodicidadTasa" value="30"/>
                    </div>
                </div>
                <div class="row intereses">
                    <div class=" row titulo-intereses">
                        <div class="c5">
                            <span >&nbsp;</span>
                        </div>
                        <div class="c3">
                            <span class="">Interes (%)</span>
                        </div>
                        <div class="c3">
                            <span class="">Dias</span>
                        </div>
                    </div>   
                    <div class=" row">
                        <div class="c5">
                            <span >Interes Compensatorio</span>
                        </div>
                        <div class="c3">
                            <input type="text" class="" id="txtInteresCompensatorio" value="<?= $credito['T_COMPENSATORIO'] ?>"/>
                        </div>
                        <div class="c3">
                            <input type="text" class="" id="txtPeriodicidadCalculoCompensatorio" value="<?= $credito['PLAZO_COMPENSATORIO'] ?>"/>
                        </div>
                    </div>   

                    <div class="row ">
                        <div class="c5">
                            <span >Interes Punitorio</span>
                        </div>
                        <div class="c3">
                            <input type="text" class="" id="txtInteresPunitorio" value="<?= $credito['T_PUNITORIO'] ?>"/>
                        </div>
                        <div class="c3">
                            <input type="text" class="" id="txtPeriodicidadCalculoMoratorio" value="<?= $credito['PLAZO_PUNITORIO'] ?>"/>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="c5">
                            <span >Interes Moratorio</span>
                        </div>
                        <div class="c3">
                            <span ><input type="text" class="" id="txtIntereeMoratorio" value="<?= $credito['T_MORATORIO'] ?>"/></span>
                        </div>
                        <div class="c3">
                            <span ><input type="text" class="" id="txtPeriodicidadCalculoPunitorio" value="<?= $credito['PLAZO_MORATORIO'] ?>"/></span>
                        </div>
                    </div>       
                </div>
            </div>
        </div>

        <div class="row c12 div-bonificacion grupo">

            <div class="c3">
                <span class="titulo-seccion">
                    Bonificaciones/Subsidios
                </span>
            </div>                   
            <div class="c8 row">
                <div class="row">
                    <div class="c5">
                        <span >Bonificaci??n/Subsidio</span>
                    </div>

                    <div class="c5">
                        <div class="row">
                            <div class="c1">
                                <input type="radio"  id="chkUnsub" name="chkSubidio" value="0" checked="<?= $credito['T_BONIFICACION'] > 0 ? '' : 'checked' ?>" onclick="change_subsidio();"/>
                            </div>
                            <div class="c3">
                                <span >No</span>
                            </div>

                            <div class="c1">
                                <input type="radio"  id="chkSub" name="chkSubidio" value="1" checked="<?= $credito['T_BONIFICACION'] > 0 ? 'checked' : '' ?>" onclick="change_subsidio();"/>
                            </div>
                            <div class="c3">
                                <span >Si</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class=" row tasa_subsidio">
                    <div class="c5 ">
                        <span >Porcentaje de subsidio (con respecto al monto total)</span>
                    </div>
                    <div class="c7">
                        <input type="text" class="" id="txtTasaSubsidio" value="<?= $credito['T_BONIFICACION'] ?>"/>
                    </div>
                </div>
                <div class=" row tasa_subsidio">
                    <div class="c5 ">
                        <span >Plazo de pago</span>
                    </div>
                    <div class="c7">
                        <input type="text" class="" id="txtPlazo" value="60" />
                    </div>            
                </div>
            </div>
        </div>
        <div class="row c12 grupo">

            <div class="c3">
                <span class="titulo-seccion">
                    Vinculaciones
                </span>
            </div>                   
            <div class="c8 row">
                <div class="row">
                    <div class="c5">
                        <span >Fideicomiso</span>
                    </div>

                    <div class="c7">
                        <select id="comboFideicomiso">
                            <option value="0">Seleccione Fideicomiso</option>
<?php foreach ($credito['FIDEICOMISOS'] as $fid) { ?>
                                <option value="<?= $fid['ID'] ?>"><?= $fid['NOMBRE'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="c5">
                        <span >Operatorias</span>
                    </div>

                    <div class="c7">
                        <select id="comboOperatorias">
                            <option value="0">Seleccione un fideicomiso</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="c5">
                        <span >Postulantes</span>
                    </div>

                    <div class="c7">
                        <select id="comboClientes" multiple>
<?php foreach ($credito['POSTULANTES'] as $cliente) { ?>
                                <option value="<?= $cliente['ID'] ?>"><?= $cliente['RAZON_SOCIAL']." -  ".$cliente['CUIT'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>


            </div>
        </div>

        <div class="row c12 div-desembolsos grupo">
            <div class="c3">
                <span class="titulo-seccion">
                    Desembolsos
                </span>
            </div>                  
            <div class="c8 row">
                <div class="row">
                    <div class="c5">
                        <span >Capital Teorico </span>
                    </div>
                    <div class="c7">
                        <input type="text" class="" id="txtMontoTotalDesembolsos" value="<?= $credito['MONTO_CREDITO'] ?>"/>

                    </div>
                </div>
                <div class=" row">
                    <div class="c5">
                        <span >Fecha </span><br/>
                        <input type="text" class="fecha" id="txtFechaDesembolso" value=""/>
                    </div>
                    <div class="c5">
                        <span >Monto</span><br/>
                        <input type="text" class="" id="txtMontoDesembolso" value=""/>
                    </div>
                    <div class="c1"><br/>
                        <button onclick="agregar_desembolso_generar();">Agregar</button>
                    </div>
                </div>

                <div class="c8 row">
                    <ul class="ul-desembolsos">
                        <li class="titulo">
                            <span class="fecha_desembolso">FECHA</span>
                            <span class="monto_desembolso">MONTO</span>
                            <span class="borrar_desembolso">ACCION</span>
                        </li>
                        <li class="data">
                            <span class="fecha_desembolso"></span>
                            <span class="monto_desembolso"></span>
                            <span class="borrar_desembolso"><button onclick="quitar_desembolso_generar(this);">Borrar</button></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    <div class="c12 row">
        <div class="button-a blue send_generar_credito" onclick="generar_cuotas();"><span>Generar</span></div>
    </div>


    <input type="hidden" class="fecha" id="hFechaActual" value="<?= time(); ?>"/>
    <input type="hidden" value="<?= $credito['ID'] ?>" id="hCreditoID"/>
    <input type="hidden" value="<?= $credito['MICRO'] ?>" id="hMicro"/>


