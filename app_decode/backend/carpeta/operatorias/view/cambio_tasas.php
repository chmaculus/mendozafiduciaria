<div id="cambio_tasa" class="elem">
    <h4>CAMBIOS DE TASAS</h4>
    <table width="100%">
        <tr>
            <td valign="top">
                <table width="95%">
                    <tr>
                        <th>Fecha</th>
                        <th>Créditos</th>
                        <th>Int. Comp.</th>
                        <th>Int. Subs.</th>
                        <th>Int. Mor.</th>
                        <th>Int. Pun.</th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php if ($lst_cambiotasas) { ?>
                        <?php foreach ($lst_cambiotasas as $ct) { ?>
                            <tr class="ct-<?php echo $ct['ID'] ?>">
                                <td><?php echo date('d/m/Y', $ct['FECHA']) ?></td>
                                <td><?php echo $ct['TC'] ?></td>
                                <td><?php echo ($ct['COMPENSATORIO'] >= 0) ? $ct['COMPENSATORIO'] : '-' ?></td>
                                <td><?php echo ($ct['SUBSIDIO'] >= 0) ? $ct['SUBSIDIO'] : '-' ?></td>
                                <td><?php echo ($ct['MORATORIO'] >= 0) ? $ct['MORATORIO'] : '-' ?></td>
                                <td><?php echo ($ct['PUNITORIO'] >= 0) ? $ct['PUNITORIO'] : '-' ?></td>
                                <td>
                                    <i class="fa fa-remove" title="Eliminar cambio de tasa masivo" onclick="del_ct(<?php echo $ct['ID'] ?>)"></i>
                                    <i class="fa fa-repeat" title="Sincronizar cambios de tasas" onclick="sinc_ct(<?php echo $ct['ID'] ?>)"></i>
                                    <!-- <i class="fa fa-calculator" title="Regenerar cambio de tasa y recalcular cuotas y pagos" onclick="reimp_ct(<?php echo $ct['ID'] ?>)"></i> -->
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="7" align="center">No hay cambios de tasas</td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
            <td>
                <?php if ($imputacion_tasas) { ?>
                    <label>Impactar cambio de tasas en créditos:</label><br /><br />
                    <input type="checkbox" id="imp_comp" /> Int.Comp.<br />
                    <input type="checkbox" id="imp_subs" /> Int.Subs.<br />
                    <input type="checkbox" id="imp_mora" /> Int.Mor.<br />
                    <input type="checkbox" id="imp_pun" /> Int.Pun.<br /><br />
                    <label>Fecha cambio de tasas créditos:</label><br />
                    <input type="text" class="fecha" title="Ingrese fecha de cambio de tasas" id="fec_imp_tasas" value="<?= date('d-m-Y') ?>" /><br /><br />
                    <input type="button" onclick="impactar_cambiotasas()" value="Impactar valores" />
                <?php } ?>
            </td>
        </tr>
    </table>
    <div id="pct"></div>
</div>