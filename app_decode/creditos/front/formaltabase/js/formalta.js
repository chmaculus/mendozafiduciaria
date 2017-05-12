var _version_id = 0;
var _desembolsos = [];


$(document).ready(function() {
    
    
});

_formaltabase.start = function(){

    var fecha = $("#txtPrimerVencimiento").val();
    $(".fecha").datepicker({
        changeMonth: true,
        changeYear: true
    });
    $(".fecha").datepicker("option", "dateFormat", 'dd-mm-yy');
    
    var parsedDate = $.datepicker.parseDate('yy-mm-dd', fecha);
    $('#txtPrimerVencimiento').datepicker('setDate', parsedDate);
    

    $("#comboFideicomiso").chosen();
    
    $("#comboClientes").chosen();
    $("#comboOperatorias").chosen();
    
    if ($("#comboFideicomiso").val() > 0) {
        get_operatorios_from_fideicomiso($("#comboFideicomiso").val());
    }
    
    $("#comboFideicomiso").chosen().change(function() {
        get_operatorios_from_fideicomiso($(this).val());
        //$('#' + $(this).val()).show();
    });

    change_interes();
    change_subsidio();
    

    cambiar_accion();
    init_form_generar();

    
    
    $(".grupo").on({
        "mouseenter" : function(){
            $(this).addClass("over");   
        },
        "mouseleave" : function(){
            $(this).removeClass("over");
        }
    });
    
    for(var i = 0 ; i < _formaltabase.DESEMBOLSOS.length ; i++){
        var d = new Date(_formaltabase.DESEMBOLSOS[i].FECHA * 1000);

        var curr_date = d.getDate();
        var curr_month = d.getMonth() + 1; //Months are zero based
        var curr_year = d.getFullYear();
        var fecha_str = curr_date + "-" + curr_month + "-" + curr_year;
        _agregar_desembolso_generar(fecha_str, _formaltabase.DESEMBOLSOS[i].MONTO);
    }
    
    $('#comboOperatorias').on('change', function() {
        var fecha = 0;
        var tmpf;
        if ($('.div-desembolsos .ul-desembolsos .data .fecha_desembolso').length > 0) {
            $('.div-desembolsos .ul-desembolsos .data .fecha_desembolso').each(function(k, v) {
                tmpf = Date.parse($(v).text().replace('-', '/').replace('-', '/')) / 1000;
                console.log(tmpf)
                if (tmpf && (fecha == 0 || tmpf < fecha)) {
                    fecha = tmpf;
                }
            });
        }
        $.ajax({
            url : _formaltabase.URL + "/x_get_data_operatoria/",
            type: "post",
            data: {
                id: $("#comboOperatorias").val(),
                fecha: fecha
            },
            dataType: "json",
            success: function (data) {
                if (data && data !== 'false') {
                    $("#txtInteresCompensatorio").val(data.COMPENSATORIO);
                    $("#txtInteresPunitorio").val(data.PUNITORIO);
                    $("#txtInteresMoratorio").val(data.MORATORIO);
                    $("#txtTasaSubsidio").val(data.SUBSIDIO);
                    $("#txtIVA").val(data.IVA);
                    $("#chkActComp").attr('checked', (data.ACT_COMP == '1' ? true : false));
                }
            }
        });
    })
}

function change_interes() {
    var interes = $("input[name=tipoInteres]:checked").val();

    if (parseInt(interes) == 1) {
        $("#txtPeriodicidadTasa").val("30");
        $(".tasa_periodo input").attr("disable","false").removeClass("disabled");
    }
    else {
        $("#txtPeriodicidadTasa").val("0");
        $(".tasa_periodo input").attr("disable","true").addClass("disabled");
    }

}

function change_subsidio() {
    var interes = $("input[name=chkSubidio]:checked").val();

    if (parseInt(interes) == 1) {
        var int_compensatorio = $("#txtInteresCompensatorio").val();
        $("#txtTasaSubsidio").val(int_compensatorio);
        $(".tasa_subsidio input").attr("disable","false").removeClass("disabled");
    }
    else {
        $("#txtTasaSubsidio").val("0");
        $(".tasa_subsidio input").attr("disable","true").addClass("disabled");
    }

}



function agregar_desembolso_generar(){
    $("#txtMontoDesembolso").removeClass("error");
    $("#txtFechaDesembolso").removeClass("error");
    
    var monto = $("#txtMontoDesembolso").val();
    var fecha = $("#txtFechaDesembolso").val();
    
    berror = false;
    if (!(fecha.length > 6) ){
        berror = true;
        $("#txtFechaDesembolso").addClass("error");
    }
    
    if (!(monto > 0) ){
        berror = true;
        $("#txtMontoDesembolso").addClass("error");
    }    
    
    if(!berror){
        _agregar_desembolso_generar(fecha, monto);
        $("#txtFechaDesembolso, #txtMontoDesembolso").val("");
    }
    
}
var _$li_desembolsos = null;
function _agregar_desembolso_generar(fecha, monto){
    var $copy_li = _$li_desembolsos.clone();
    $copy_li.find(".fecha_desembolso").text(fecha);
    $copy_li.find(".monto_desembolso").text("$"+monto);
    $(".ul-desembolsos").append($copy_li);
    _desembolsos.push(
    {   fecha : fecha, 
        monto : monto});
    
    var dif = _suma_total_desembolsos();
}


function quitar_desembolso_generar(elem){
    var index = $(elem).closest("li").index();
    $(".ul-desembolsos li").eq(index).remove();
    _desembolsos.splice(index-1,1);
}




function init_form_generar(){
    _$li_desembolsos = $(".ul-desembolsos li.data").clone();
    $(".ul-desembolsos li.data").remove();
}

function _suma_total_desembolsos(){
    var total = 0;
    for(var i = 0 ; i < _desembolsos.length ; i++){
        total += parseFloat(_desembolsos[i].monto);
    }
    
    var total_desembolsos = parseFloat($("#txtMontoTotalDesembolsos").val());
    console.log(total+  " - "+total_desembolsos);
    return (total_desembolsos - total);
}





function cambiar_accion() {
    var selected = parseInt($("#txtEvento option:selected").val());
    $("#div-monto").show();
    $("#spMonto").text("Monto");
    switch (selected) {
        case 5:
        case 6:
        case 0:
            $("#div-monto").hide();
            break;

        case 1:
            break;
        case 2:
            break;
        case 3:
            $("#spMonto").text("Tasa %");
            break;
        case 4:
            break;
    }
}

function generar_cuotas(){
    _generar_cuotas(0);
}

function _generar_cuotas(simulacion){
    $("#txtMonto").removeClass("error");
    $("#txtCantidadCuotasGracia").removeClass("error");
    $("#txtCreditoID-opc").removeClass("error");
    
    var fecha_actual =  $("#hFechaActual").val();
    var fecha_inicio =  $.datepicker.formatDate('@',  $("#txtPrimerVencimiento").datepicker( "getDate" ))/1000;
    var cuotas = $("#txtCantidadCuotas").val();
    var cuotas_gracia = $("#txtCantidadCuotasGracia").val();
    var monto = $("#txtMonto").val();
    var int_compensatorio = $("#txtInteresCompensatorio").val();
    var int_subsidio = $("#txtTasaSubsidio").val();
    var int_punitorio = $("#txtInteresPunitorio").val();
    var int_moratorio= $("#txtInteresMoratorio").val();
    var gastos= $("#txtGastos").val();
    var gastosMin= $("#txtGastosMin").val();
    
    var plazo_compensatorio = $("#txtPeriodicidadCalculoCompensatorio").val();
    var plazo_moratorio = $("#txtPeriodicidadCalculoMoratorio").val();
    var plazo_punitorio = $("#txtPeriodicidadCalculoPunitorio").val();
    var tasa_iva = $("#txtIVA").val();
    
    var periodicidad = $("#txtPeriodicidad").val();
    var periodicidad_tasa = $("#txtPeriodicidadTasa").val();
    var plazo_pago = $("#txtPlazo").val();
    var credito_id = $("#hCreditoID").val();
    var micro = $("#hMicro").val();
    
    var total_credito = $("#txtMontoTotalDesembolsos").val();
    
    var clientes = $("#comboClientes").val();
    var fideicomiso = $("#comboFideicomiso").val();
    var operatoria = $("#comboOperatorias").val();

    if (parseInt(cuotas_gracia) >= parseInt(cuotas)){
        jAlert("No pueden existir mas cuotas de gracia que cuotas de credito","ERROR DE GENERACION");
        $("#txtCantidadCuotasGracia").addClass("error");
        return;
    }
    
    if (parseInt(credito_id)===0){
        credito_id = $("#txtCreditoID-opc").val();
        micro = $("#chkMicro:checked").length;
        
        if (!(credito_id > 0)){
            jAlert("Debe ingresar un numero entero en el numero de credito","ERROR DE GENERACION");
            $("#txtCreditoID-opc").addClass("error");
        }
    }
    else{
        credito_id = $("#txtCreditoID-opc").val();
        if (credito_id > 0){
            
        }
        else{
            credito_id = $("#hCreditoID").val();
        }
        
    }
    
    //interes porcentaje 
    var interes_subsidiado = int_subsidio;
    
    if (_suma_total_desembolsos()!==0){
            jAlert("El total de desembolsos no coincide con el monto","Error en generacion de Credito", function(){
                $("#txtMonto").addClass("error");
            });
            return;
    }
    
    
    $.ajax({
        url : _formaltabase.URL + "/x_generar_cuotas",
        data : {
            fecha : fecha_actual,
            micro : micro,
            fecha_inicio: fecha_inicio,
            cuotas : cuotas,
            cuotas_gracia: cuotas_gracia,
            monto : monto,
            credito_id : credito_id,
            int_compensatorio : int_compensatorio,
            plazo_pago : plazo_pago,
            int_subsidio : interes_subsidiado,
            int_punitorio : int_punitorio,
            int_moratorio : int_moratorio,
            int_gastos : gastos,
            int_gastos_min : gastosMin,
            tiva : tasa_iva,
            periodicidad : periodicidad,
            periodicidad_tasa : periodicidad_tasa,
            plazo_compensatorio : plazo_compensatorio || 0,
            plazo_moratorio : plazo_moratorio || 0,
            plazo_punitorio : plazo_punitorio || 0,
            total_credito : total_credito || 0,
            desembolsos : _desembolsos,
            clientes : clientes || 0,
            operatoria : operatoria || 0,
            fideicomiso : fideicomiso || 0,
            credito_caduca: $("#credito_caduca").val(),
            fecha_caduca: $("#fecha_caduca").val(),
            prorroga: $("#prorroga").val(),
            act_comp: $("#chkActComp:checked").length,
            simulacion: simulacion
        },
        type : "post",
        success : function(data){
            if (simulacion) {
                $.ajax({
                    url: "creditos/front/credito/x_obtener_cuotas",
                    data : {
                        chequera: true,
                        simulacion: true,
                        planchado: 0
                    },
                    type : "post",
                    success : function(data){
                        $("#simulacion").html(data);
                        $(".datos .opcion.ampliar").on({
                            "mouseenter" : function(){

                            },
                            "mouseleave" : function(){

                            },
                            "click" : function(){
                                console.log("enter");
                                $this_op = $(this);
                                if ($this_op.hasClass("selected")){
                                    $this_op.siblings(".especificaciones").fadeOut();
                                    $this_op.removeClass("selected");
                                    $(this).text("( + )");
                                }
                                else{
                                    if ($(".datos .opcion.ampliar.selected").length > 0 && false){
                                        $this_op.siblings(".especificaciones").fadeIn();
                                        $this_op.addClass("selected");
                                    }
                                    else{
                                        $this_op.addClass("selected");
                                        $this_op.siblings(".especificaciones").fadeIn();
                                        $(this).text("( - )");
                                    }

                                }
                            }
                        });
                    }
                });
            } else {
                $(".div-result").html(data);
                if ($("#credito_caduca").val()) {
                    jAlert("El credito ha sido caducado y emitido uno nuevo correctamente","Error en generacion de Credito", function(){
                        location.href="creditos/front/credito/init/" + $("#txtCreditoID-opc").val() + "/1";
                    });
                } else {
                    jConfirm("Desea generar otro credito?","Proceso Terminado con éxito", function(e){
                        if (e){
                            _formaltabase.restart();
                        }
                        else{
                            location.href="creditos/front/creditos";
                        }
                    });
                }
            }
        }
    });
}

function get_operatorios_from_fideicomiso(id){
    
    if (parseInt(id)===0){
        $("#comboOperatorias").html('<option value="0">Seleccione un fideicomiso</option>');
        $("#comboOperatorias").trigger("chosen:updated");
        return;
    }
    $.ajax({
        url : _formaltabase.URL + "/x_get_operatorias",
        data : {
            id_fideicomiso : id
        },
        type : "post",
        dataType : "json",
        success : function(rtn){
            $("#comboOperatorias").html("");
            $("#comboOperatorias").append('<option value="0">Seleccione un fideicomiso</option>');
            var ope = $("#credito_operatoria").val();
            for(var i = 0 ; i < rtn.length ; i++){
                if (ope == rtn[i]['ID']) {
                    $("#comboOperatorias").append("<option value='"+rtn[i]['ID']+"' selected='selected'>"+rtn[i]['NOMBRE']+"</option>");
                } else {
                    $("#comboOperatorias").append("<option value='"+rtn[i]['ID']+"'>"+rtn[i]['NOMBRE']+"</option>");
                }
            }
            
            $("#comboOperatorias").trigger("chosen:updated");
            console.log(rtn);
        }
    });
}


function simular_cuotas() {
    _generar_cuotas(1);
}