<div class="grid-1" id="frmagregar">
    <div class="title-grid"><div id="label_action">Credito</div> <?php echo ($vars['CREDITO_ESTADO'] == ESTADO_CREDITO_CADUCADO) ? '<strong>(CADUCADO)</strong>' : ''; ?><div class="right less selected">-</div></div>
    <div class="content-gird">
        <div class="form">


            <div class="elem elem_med">
                <label>Codigo:</label>
                <div class="indent formtext">
                    <span ><?=$ID?></span>
                    
                </div>
            </div>
            <div class="clear"> </div>


            <div class="elem elem_med" >
                <label>Fideicomiso:</label>
                <div class="indent">
                    <span ><?=(isset($FIDEICOMISO['NOMBRE']))?$FIDEICOMISO['NOMBRE']:''?></span>
                </div>
            </div>


            <div class="elem elem_med">
                <label>Operatoria:</label>
                <div class="indent" id="div_operatoria">
                    <span ><?=(isset($OPERATORIAS['NOMBRE']))?$OPERATORIAS['NOMBRE']:''?>  </span>
                </div>
            </div>

            <div class="elem elem_med" >
                <label>Cantidad Cuotas </label>
                <div class="indent">
                    <span ><?=$CANTIDAD_CUOTAS?>  </span>
                </div>
            </div>
            
            <div class="elem elem_med" >
                <label>Cuotas Gracia</label>
                <div class="indent">
                    <span ><?=$CUOTAS_GRACIA?>  </span>
                </div>
            </div>

            <div class="elem elem_med" >
                <label>Periodicidad Cuota </label>
                <div class="indent">
                    <span ><?=$PERIODICIDAD?>  </span>
                </div>
            </div>
            
            <div class="elem elem_med" >
                <label>Periodicidad Tasa:</label>
                <div class="indent">
                    <span ><?=$PERIODICIDAD_TASA?>  </span>
                </div>
            </div>

            
            <div class="elem elem_med tri" >
                <label>T. Compensatoria:</label>
                <div class="indent">
                    <span ><?=number_format($T_COMPENSATORIO,3,",",".");?>%  </span>
                </div>
            </div>            
            <div class="elem elem_med tri" >
                <label>T. Comp. Dias:</label>
                <div class="indent">
                    <span ><?=$PLAZO_COMPENSATORIO?>  </span>
                </div>
            </div>            
            
            <div class="elem elem_med tri" >
                <label>T. Punitorio:</label>
                <div class="indent">
                    <span ><?=number_format($T_PUNITORIO,3,",",".");?>%  </span>
                </div>
            </div>            
            <div class="elem elem_med tri" >
                <label>T. Punitorio Dias:</label>
                <div class="indent">
                    <span ><?=$PLAZO_PUNITORIO?>  </span>
                </div>
            </div>            
            
            <div class="elem elem_med tri" >
                <label>T. Moratoria:</label>
                <div class="indent" >
                    <span ><?=number_format($T_MORATORIO,3)?>%  </span>
                </div>
            </div>            
            <div class="elem elem_med tri" >
                <label>T. Moratoria Dias:</label>
                <div class="indent">
                    <span ><?=$PLAZO_MORATORIO?>  </span>
                </div>
            </div>            
            
            <div class="elem elem_med tri" >
                <label>T. Bonificación:</label>
                <div class="indent" >
                    <span ><?=  number_format($T_BONIFICACION,3,",",".")?>%  </span>
                </div>
            </div>            
            <div class="elem elem_med tri" >
                <label>Plazo Pago</label>
                <div class="indent">
                    <span ><?=$PLAZO_PAGO?>  </span>
                </div>
            </div>        
            <div class="elem elem_med tri">
                <label class="der">Monto Solicitado:</label>
                <div class="indent formtext">
                    <span ><?=number_format($MONTO_CREDITO,2,",",".");?>  </span>
                </div>
            </div>            
            <div class="elem elem_med tri" >
                <label>Convenio</label>
                <div class="indent">
                    <span ><?=$CONVENIO?>  </span>
                </div>
            </div>            




            <div class="elem">
                <label>Postulante/s:</label>
                <div class="indent">
                    <span ><?=$post_str?>  </span>
                </div>
            </div>


            <div class="clear"> </div>
        </div>
    </div>
</div>