<ul class="toolbar">
    <li class="tb_lis" data-top='listado2' data-loc="creditos/front/creditos"><div>Listado</div></li>
</ul>

<div id="cmor" style="overflow:scroll">
    <table>
        <tr>
            <th>DEUDOR</th>
            <th>CREDITO</th>
            <th>DIRECCION</th>
            <th>PROVINCIA</th>
            <th>LOCALIDAD</th>
            <th>FECHA DEL CONT. Y DESEMB.</th>
            <th>MONTO DEL CREDITO</th>
            <th>SITUACION</th>
            <th>SALDO CAPITAL</th>
            <th>COBRANZAS</th>
            <th>CANTIDAD DE CUOTAS EN MORAS</th>
            <th>MONTO VENCIDO</th>
            <th>MONTO MORA</th>
            <th>PORCENTAJE DE MORA</th>
            <th>ESTADO</th>
        </tr>
        <?php
        if ($creditos_moratorias) {
            foreach ($creditos_moratorias as $item) {
                if (!$item['DESEMBOLSO'] && !$item['CUOTAS']) {
                    continue;
                }
                
                $_moratoria = 0;
                $total_moratoria = 0;
                $total_punitorio = 0;
                $total_iva = 0;
                $cuotas_mora = 0;
                $capital_pagado = 0;
                $total_credito = $item['MONTO_CREDITO'];
                
                if ($item['CUOTAS']) {
                    $cant_cuotas = count($item['CUOTAS']);
                } else {
                    $cant_cuotas = $item['DESEMBOLSO'][0]['CUOTAS_RESTANTES'];
                }
                $cobranzas = 0;

                if ($item['PAGOS']) {

                    foreach ($item['PAGOS'] as $v) {
                        $cobranzas += $v['MONTO'];
                        switch ($v['ID_TIPO']) {
                            case PAGO_IVA_MORATORIO:
                                $total_iva += $v['MONTO'];
                                $total_moratoria += $v['MONTO'];
                                break;
                            case PAGO_MORATORIO:
                                $_moratoria += $v['MONTO'];
                                $total_moratoria += $v['MONTO'];
                                ++$cuotas_mora;
                                break;
                            case PAGO_IVA_PUNITORIO:
                            case PAGO_PUNITORIO:
                                $total_punitorio += $v['MONTO'];
                                break;
                            case 7:
                                $capital_pagado += $v['MONTO'];
                                break;
                        }
                    }
                }
                

                if ($item['CUOTAS']) {

                    foreach ($item['CUOTAS'] as $v) {
                        $total_credito += $v['INT_COMPENSATORIO'] + $v['INT_COMPENSATORIO_IVA'];
                    }
                    
                }
                ?>
                <tr class="cr-<?= $item['ID'] ?>">
                    <td><?= $item['RAZON_SOCIAL'] ?></td>
                    <td><?= $item['ID'] ?></td>
                    <td><?= $item['DIRECCION'] ?></td>
                    <td><?= $item['PROVINCIA'] ?></td>
                    <td><?= $item['LOCALIDAD'] ?></td>
                    <td><?= $item['DESEMBOLSO'] ? date('d/m/Y', $item['DESEMBOLSO'][0]['FECHA']) : '' ?></td>
                    <td><?= $item['MONTO_CREDITO'] ?></td>
                    <td><?= $_moratoria ? 'Mora' : 'Al Día' ?></td>
                    <td><?= number_format($item['MONTO_CREDITO'] - $capital_pagado, 2, ",", ".") ?></td>
                    <td><?= number_format($cobranzas, 2, ",", ".") ?></td>
                    <td><?= $cuotas_mora ?></td>
                    <td><?= number_format($total_credito + $total_moratoria + $total_punitorio, 2, ",", ".") ?></td>
                    <td><?= number_format($total_moratoria, 2, ",", ".") ?></td>
                    <td><?= $cant_cuotas ? round($cuotas_mora * 100 / $cant_cuotas) . '%' : 'ND' ?></td>
                    <td>Estado</td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>
<div id="wpopup"></div>