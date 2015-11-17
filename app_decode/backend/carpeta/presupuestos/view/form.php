<!--Form-->
<script type="text/javascript" src="app_decode/backend/carpeta/presupuestos/js/forms.js"></script>
<div class="content-form">
    <form method="post" enctype="multipart/form-data" id="customForm" onsubmit="return false;" >
        <input type="hidden" id="presupuesto_id" value="<?= (isset($presupuesto) && $presupuesto && $items) ? $id : ''; ?>" />
        <div class="grid-1" id="frmagregar">
            <div class="title-grid">Presupuesto</div>
            <div class="content-gird">
                <div class="form">
                    <div id="items" <?= (isset($presupuesto) && $presupuesto && $items) ? '' : 'style="display:none"' ?>>
                        <div class="elem">
                            <div class="indent formtext">
                                <table style="width:100%">
                                    <tr class="tit">
                                        <td>Nombre</td>
                                        <td>Divisa</td>
                                        <td>Cambio</td>
                                        <td>% IVA</td>
                                        <td>Neto</td>
                                        <td>IVA</td>
                                        <td>Total</td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    if (isset($presupuesto) && $presupuesto && $items) {
                                        $neto = 0;
                                        $iva = 0;
                                        $total = 0;

                                        foreach ($items as $k => $it) {
                                            $_neto = $it['NETO'] * $it['CAMBIO'];
                                            $_iva = $it['IVA'] * $_neto / 100;
                                            $neto += $_neto;
                                            $iva += $_iva;
                                            ?>
                                            <tr class="pp-<?= $k ?>">
                                                <td class="nom"><?= $it['DESCRIPCION'] ?></td>
                                                <td class="mon"><?= $it['DIVISA'] ?></td>
                                                <td class="cam"><?= $it['CAMBIO'] ?></td>
                                                <td class="iva"><?= $it['IVA'] ?></td>
                                                <td class="net"><?= $it['NETO'] ?></td>
                                                <td class="ivat"><?= $_iva ?></td>
                                                <td class="tt"><?= $_neto + $_iva ?></td>
                                                <td class="dd">Quitar</td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="clear"> </div>
                    <hr />
                    <div id="item">
                        <h3>Item (maquinaria)</h3>
                        <div class="elem">
                            <label>Descripción:</label>
                            <div class="indent formtext">
                                <input type="text" class="validate[required] medium" title="Ingrese Descripción" maxlength="64" id="nombre" value="" />
                            </div>
                        </div>

                        <div class="elem elem_med">
                            <label>Moneda:</label>
                            <div class="indent formtext">
                                <select class="validate[required] chzn-select medium-select select" id="moneda" data-prompt-position="topLeft">
                                    <option value="">Seleccione Moneda</option>
                                    <option value="$">$</option>
                                    <option value="u$s">u$s</option>
                                </select>
                            </div>
                        </div>

                        <div class="elem elem_med">
                            <label>Tipo de cambio:</label>
                            <div class="indent formtext">
                                <input type="text" maxlength="10" title="Ingrese cambio de divisas" id="cambio" class="validate[required]"> 
                            </div>
                        </div>

                        <div class="elem elem_med">
                            <label>Neto en Divisas:</label>
                            <div class="indent formtext">
                                <input maxlength="10" type="text" title="Ingrese neto en divisas" id="netod" value="" class="validate[required]" /> 
                            </div>
                        </div>

                        <div class="elem elem_med">
                            <label>IVA %:</label>
                            <div class="indent formtext">
                                <input type="text" class="tip-right" title="Ingrese IVA" id="ivai" value="" class="validate[required]" />
                            </div>
                        </div>
                        <?php /*
                          <div class="elem elem_med">
                          <label>Neto:</label>
                          <div class="indent formtext">
                          <input type="text" class="tip-right" id="neto" value="" readonly="readonly" />
                          </div>
                          </div>

                          <div class="elem elem_med">
                          <label>IVA:</label>
                          <div class="indent formtext">
                          <input type="text" class="tip-right" id="iva" value="" readonly="readonly" />
                          </div>
                          </div>

                          <div class="elem elem_med">
                          <label>Total:</label>
                          <div class="indent formtext">
                          <input type="text" class="tip-right" id="total" value="" readonly="readonly" />
                          </div>
                          </div> */ ?>

                        <div class="elem elem_med">
                            <br /><br />
                        </div>

                    </div>
                    <div id="action">
                        <input type="button" id="add_item" onclick="add_p()" class="button-a gray" value="Agregar Item" />
                    </div>


                </div>
            </div>
        </div>



        <!--Form end-->

        <!--Tabs end-->
        <div class="elem elempie">
            <div class="indent">
                <input id="send" name="send" type="submit" class="button-a gray" value="Enviar" /> &nbsp;&nbsp;
                <button class="button-a dark-blue" id="btnClear">Limpiar</button>  
            </div>
        </div>

    </form>


</div>