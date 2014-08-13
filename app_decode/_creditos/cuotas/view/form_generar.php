<div class="form_cuotas">
    <div class="full_field">
        <span >Credito</span>
        <input type="text" class="" id="txtCreditoID"/>
    </div>

    <div class="full_field">
        <div class="mid_field">
            <span >Fecha Actual</span>
            <input type="text" class="fecha" id="txtFechaActual" value="29-12-2012"/>
        </div>
    </div>

    <div class="full_field">
        <div class="mid_field">
            <span >Cantidad cuotas</span>
            <input type="text" class="" id="txtCantidadCuotas" value="2"/>
        </div>
        <div class="mid_field">
            <span >Cuotas de gracia</span>
            <input type="text" class="" id="txtCantidadCuotasGracia" value="0"/>
        </div>
    </div>

    

    <div class="full_field">
        <div class="mid_field">
            <span >Primer vencimiento</span>
            <input type="text" class="fecha" id="txtInicio" value="25-12-2013"/>
        </div>        
        <div class="mid_field">
            <span >Periodicidad</span>
            <input type="text" class="" id="txtPeriodicidad" value="6"/>
        </div>
            
    </div>
    
    <div class="full_field">
        <div class="mid_field">
            <span >Interes Simple</span>
            <input type="radio"  id="chkInteresSimple" name="tipoInteres" value="0" checked="true" onclick="change_interes();"/>
        </div>        
        <div class="mid_field">
            <span >Interes Compuesto</span>
            <input type="radio"  id="chkInteresCompuesto" name="tipoInteres" value="1" onclick="change_interes();"/>
        </div>        
        <div class="mid_field tasa_periodo">
            <span >Periodicidad Tasa (en dias)</span>
            <input type="text" class="" id="txtPeriodicidadTasa" value="30"/>
        </div>
    </div>
    
    <div class="full_field">
        <div class="mid_field">
            <span >Interes Compensatorio</span>
            <input type="text" class="" id="txtInteresCompensatorio" value="10"/>
        </div>
        <div class="mid_field">
            <span >Interes Punitorio</span>
            <input type="text" class="" id="txtInteresPunitorio" value="10"/>
        </div>
        <div class="mid_field">
            <span >Interes Moratorio</span>
            <span ><input type="text" class="" id="txtIntereeMoratorio" value="20"/></span>
        </div>
    </div>    
    
    <div class="full_field">
        <div class="mid_field">
            <span >Sin Subsidio</span>
            <input type="radio"  id="chkUnsub" name="chkSubidio" value="0" checked="true" onclick="change_subsidio();"/>
        </div>        
        <div class="mid_field">
            <span >Tasa Subsidiada</span>
            <input type="radio"  id="chkSub" name="chkSubidio" value="1" onclick="change_subsidio();"/>
        </div>        
        <div class="mid_field tasa_subsidio">
            <span >Porcentaje de subsidio (con respecto al monto total)</span>
            <input type="text" class="" id="txtTasaSubsidio" value="100"/>
        </div>
        <div class="mid_field tasa_subsidio">
            <span >Plazo de pago</span>
            <input type="text" class="" id="txtPlazo" value="60"/>
        </div>
    </div>



    <div class="full_field">
        <div class="mid_field">
            <span >Capital Teorico </span>
            <input type="text" class="" id="txtMonto" value="1000"/>
        </div>
    </div>





    <div class="full_field">
        <button onclick="generar_cuotas();">Generar</button>
    </div>


</div>
