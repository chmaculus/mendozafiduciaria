<style>
    @media all {
        .page-break	{ display: none; }
    }

    .codbar{font-size:0;position:relative;}
    .codbar div{-webkit-print-color-adjust:exact}
    
    @page {
        size: A4;
        margin-left: 1.15cm;
        margin-right: 1.15cm;
        margin-bottom: 0cm;
        margin-top: 0cm;        
    }

    @media print {
        .page {

            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }

    }         
    @media screen, print {
        .page-break	{ display: block; page-break-before: always; }
        table tr{
            width:100%;
            clear: both;
            font-family: sans-serif;
        }


        table{
            width:200mm;
            // border:1px solid #aaa;
            margin: 10px 0px;
            padding:20px 10px;
            font-size: 3mm;
        }

        table thead{
            font-weight: bold;
            background: #eee;


        }

        table thead tr td{
            padding:6px  0px;        
        }

        table tbody tr td{
            height:30px;
        }

        table tbody tr td{
            padding:5px 0px;
            border-bottom:  1px dashed #aaa;
        }

        td.fecha{
            width:100px;
        }

        td.descripcion{
            width:150px;
            font-style: italic;
        }
    </style>

    <?php
    $cuotasCollection = $credito->getCuotas();
    $eventosCollection = $credito->getEventos();

    foreach ($cuotasCollection as $cuota) {

        $fevento = new FiltroEventos($eventosCollection);
        $eventosCuota = $fevento->getEventosTipo(false, $cuota->getRango()->getStart() + 1, $cuota->getRango()->getEnd());

        $eventosArray = $eventosCuota->getEventosArray();
        ?>


        <table>
            <thead>
                <tr>
                    <td colspan="7">Cuota <?= ($cuotasCollection->size() - $cuota->getCuotasRestantes() + 1); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($eventosArray as $evento) {

                    switch ($evento->getTipo()) {
                        case 4000:
                            ?>             
                            <tr>
                                <td class="fecha"><?= date("d-m-Y", $evento->getFecha()); ?></td>        
                                <td class="descripcion">Vencimiento</td>        
                                <td>&nbsp;</td>        
                                <td>&nbsp;</td>        
                                <td>&nbsp;</td>        
                                <td>&nbsp;</td>        
                                <td>&nbsp;</td>        
                            </tr>
                            <?php
                            break;
                        case 1:
                            ?>
                            <tr>
                                <td class="fecha"><?= date("d-m-Y", $evento->getFecha()); ?></td>        
                                <td class="descripcion">Desembolso</td>        
                                <td>$<?= $evento->getTotal(); ?></td>        
                                <td>&nbsp;</td>        
                                <td>&nbsp;</td>        
                                <td>&nbsp;</td>        
                                <td>&nbsp;</td>        
                            </tr>        
                            <?php
                            break;
                        case 2:
                            ?>
                            <tr>
                                <td class="fecha"><?= date("d-m-Y", $evento->getFecha()); ?></td>        
                                <td class="descripcion">Cambio de tasa</td>        
                                <td>Int.Compensatorio<br/><?= $evento->getCompensatorio(); ?>%</td>        
                                <td>Int.Punitorio<br/><?= $evento->getPunitorio(); ?>%</td>        
                                <td>Int.Moratorio<br/><?= $evento->getMoratorio(); ?>%</td>        
                                <td>Int.Bonificacion<br/><?= $evento->getBonificacion(); ?>%</td>        
                                <td></td>        
                            </tr>      
                            <?php
                            break;
                        case 3:

                            $recuperosCollection = $evento->getRecuperoItems();
                            $capital = $compensatorio = $moratorio = $punitorio = $impuestos = 0;
                            $total = 0;
                            foreach ($recuperosCollection as $item) {
                                $monto = round($item->getMonto(), 2);
                                $total += $monto;
                                switch ($item->getTipo()) {
                                    case PAGO_CAPITAL: $capital += $monto;
                                        break;
                                    case PAGO_COMPENSATORIO: $compensatorio += $monto;
                                        break;
                                    case PAGO_IVA_COMPENSATORIO: $impuestos += $monto;
                                        break;
                                    case PAGO_MORATORIO: $moratorio += $monto;
                                        break;
                                    case PAGO_IVA_MORATORIO: $impuestos += $monto;
                                        break;
                                    case PAGO_PUNITORIO: $punitorio += $monto;
                                        break;
                                    case PAGO_IVA_PUNITORIO: $impuestos += $monto;
                                        break;
                                }
                            }
                            ?>
                            <tr>
                                <td class="fecha"><?= date("d-m-Y", $evento->getFecha()); ?></td>        
                                <td class="descripcion">Recupero</td>        
                                <td>Capital<br/>$<?= $capital ?></td>        
                                <td>Int.Compensatorio<br/>$<?= $compensatorio ?></td>        
                                <td>Int.Moratorio y Pun<br/>$<?= ($moratorio + $punitorio) ?></td>        
                                <td>Inpuestos<br/>$<?= $impuestos ?></td>        
                                <td>Total<br/>$<?= $total ?></td>        
                            </tr>        
                            <?php
                            break;
                    }
                }
                ?>





            </tbody>
        </table>
    <?php } ?>