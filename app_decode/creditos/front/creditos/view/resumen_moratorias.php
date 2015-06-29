<ul class="toolbar">
    <li class="tb_lis" data-top='listado2' data-loc="creditos/front/creditos"><div>Listado</div></li>
</ul>

<div id="cmor">
    <table>
        <tr>
            <th>CREDITO</th>
            <th>POSTULANTE</th>
            <th>MONTO</th>
            <th>F.VENCIMIENTO</th>
            <th>MORATORIA</th>
            <th>IVA</th>
            <th>PAGADO</th>
        </tr>
    <?php
    if ($creditos_moratorias) { 
        foreach ($creditos_moratorias as $item) {
            $total_moratoria = 0;
            $total_pagado = 0;
            $total_iva = 0;
            foreach ($item['MORATORIAS'] as $v) {
                $total_moratoria += $v['MONTO'];
                $total_pagado += $v['IVA'];
                $total_iva += $v['PAGO'];
            }
    ?>
        <tr class="cr-<?=$item['ID_CREDITO']?>">
            <td><?=$item['ID_CREDITO']?></td>
            <td><?=$item['POSTULANTES']?></td>
            <td><?=$item['MONTO']?></td>
            <td><?=date('d/m/Y', strtotime($item['INTERES_VTO']))?></td>
            <td><?=number_format($total_moratoria,2,",",".");?></td>
            <td><?=number_format($total_iva,2,",",".");?></td>
            <td><?=number_format($total_pagado,2,",",".");?></td>
        </tr>
    <?php 
            foreach ($item['MORATORIAS'] as $v) {
    ?>
        <tr class="cri cri<?=$item['ID_CREDITO']?>">
            <td colspan="3"></td>
            <td><?=date('d/m/Y', $v['FECHA'])?></td>
            <td><?=number_format($v['MONTO'],2,",",".");?></td>
            <td><?=number_format($v['IVA'],2,",",".");?></td>
            <td><?=number_format($v['PAGO'],2,",",".");?></td>
        </tr>
    <?php
            }
        }
    }
    ?>
    </table>
</div>
<div id="wpopup"></div>