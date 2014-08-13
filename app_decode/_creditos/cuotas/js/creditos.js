$(document).ready(function(){
    
    $(".fecha").datepicker({
        changeMonth: true,
        changeYear: true        
    });
    $(".fecha" ).datepicker( "option", "dateFormat", 'dd-mm-yy' );    
    $("a#inline").fancybox().hide();    

   change_interes();
   change_subsidio();
   _events_lista();
   
   cambiar_accion();
   


})

function generar_cuotas(){
    var fecha_actual =  $.datepicker.formatDate('@',  $("#txtFechaActual").datepicker( "getDate" ))/1000;
    var fecha_inicio =  $.datepicker.formatDate('@',  $("#txtInicio").datepicker( "getDate" ))/1000;
    var cuotas = $("#txtCantidadCuotas").val();
    var cuotas_gracia = $("#txtCantidadCuotasGracia").val();
    var monto = $("#txtMonto").val();
    var int_compensatorio = $("#txtInteresCompensatorio").val();
    var int_subsidio = $("#txtTasaSubsidio").val();
    var int_punitorio = $("#txtInteresPunitorio").val();
    var int_moratorio= $("#txtIntereeMoratorio").val();
    
    var periodicidad = $("#txtPeriodicidad").val();
    var periodicidad_tasa = $("#txtPeriodicidadTasa").val();
    var plazo_pago = $("#txtPlazo").val();
    var credito_id = $("#txtCreditoID").val();
    
    //interes porcentaje 
    var interes_subsidiado = int_subsidio * 100 / int_compensatorio ;
    
    
    $.ajax({
        url : _cuotas.URL + "/x_generar_cuotas",
        data : {
            fecha : fecha_actual,
            fecha_inicio: fecha_inicio,
            cuotas : cuotas,
            cuotas_gracia: cuotas_gracia,
            monto : monto,
            credito_id : credito_id,
            int_compensatorio : int_compensatorio,
            plazo_pago : plazo_pago,
            //int_subsidio : int_subsidio,
            int_subsidio : interes_subsidiado,
            int_punitorio : int_punitorio,
            int_moratorio : int_moratorio,
            periodicidad : periodicidad,
            periodicidad_tasa : periodicidad_tasa
        },
        type : "post",
        success : function(data){
            $(".div-result").html(data);
            _events_lista();
        }
    });
}



function calcular_pago(){
    var fecha =  $.datepicker.formatDate('@',  $("#txtFecha").datepicker( "getDate" ))/1000;
    var creadito_id = _cuotas.ID_CREDITO;
    var monto = $("#txtMonto").val();
    
    $.ajax({
        url : _cuotas.URL + "/x_set_pago",
        data : {
            fecha : fecha,
            credito_id : creadito_id,
            monto : monto
        },
        type : "post",
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
        }
    });
}



function agregar_variacion(){
    var fecha =  $.datepicker.formatDate('@',  $("#txtFecha").datepicker( "getDate" ))/1000;
    var opcion = parseInt($("#txtEvento option:selected").val())
    

    
    switch(opcion){
        case 0:
            
            _cuotas.mostrar_estado(_cuotas.ID_CREDITO,  fecha);
            break;
        case 1:
            var monto = $("#txtMonto").val();
            _cuotas.agregar_desembolso(_cuotas.ID_CREDITO, monto, fecha);
            break;
        case 2:
            var gasto = $("#txtMonto").val();
            _cuotas.agregar_gasto(_cuotas.ID_CREDITO, gasto, fecha);
            break;
        case 3:
            var tasa = $("#txtMonto").val();
            _cuotas.agregar_cambiotasa(_cuotas.ID_CREDITO, tasa, fecha);
            break;
        case 4:
            calcular_pago();
            break;
        case 5:
            generar_cuota(_cuotas.ID_CREDITO,  fecha);
            break;
        case 6:
            enviar_cuotas(_cuotas.ID_CREDITO,  fecha);
            break;
    }
    
    $("#txtMonto").val("");
    $("txtFecha").focus();
    $("#txtEvento option").eq(0).attr("selected", "selected");
}

_cuotas.mostrar_estado = function(id_credito, fecha){
    $.ajax({
        url : _cuotas.URL + "/x_get_detalle_cuotas",
        type : "post",
        data : {
            credito_id : id_credito,
            monto : 0  ,
            fecha: fecha
        },
        success : function(result){
            $(".div-result").html(result);
            $(".saldo_cuotas").hide();
            
            $('#tabs_cuotas').jqxTabs({width: 800});
            $('#tabs_cuotas').jqxTabs({scrollPosition: 'both'});
            $('#tabs_cuotas').on('selected', function (event) 
            { 
                var selectedTab = event.args.item;
                if (selectedTab  > 0){
                    $clone = $(".contenido li").eq(selectedTab-1).html();
                    $(".jqx-tabs-content-element.jqx-rc-b").eq(selectedTab).html($clone)
                }
            });            
           // _events_lista();*/
        }
    });   
}

_cuotas.agregar_desembolso = function(id_credito, monto, fecha){
    $.ajax({
        url : _cuotas.URL + "/x_agregar_desembolso",
        type : "post",
        data : {
            credito_id : id_credito,
            monto : monto  ,
            fecha: fecha
        },
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
        }
    });    
}

_cuotas.agregar_gasto = function(id_credito, monto, fecha){
    $.ajax({
        url : _cuotas.URL + "/x_agregar_gasto",
        type : "post",
        data : {
            credito_id : id_credito,
            monto : monto  ,
            fecha: fecha
        },
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
        }
    });    
}

_cuotas.agregar_cambiotasa = function(id_credito, tasa, fecha){
    $.ajax({
        url : _cuotas.URL + "/x_agregar_cambiotasa",
        type : "post",
        data : {
            credito_id : id_credito,
            tasa : tasa  ,
            fecha: fecha
        },
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
        }
    });    
}


function eliminar_variacion(id_variacion){
    $.ajax({
        url : _cuotas.URL + "/x_eliminar_variacion",
        data : {
            id_variacion : id_variacion,
            credito_id : _cuotas.ID_CREDITO
        },
        type : "post",
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
        }
    });
}


function _events_lista(){
    $("ul.ul-cuotas li").off().on({
        "mouseenter"  : function(){
            $(this).addClass("over");
        },
        "mouseleave" : function(){
            $(this).removeClass("over");
        },
        "click" : function(){
            var $content = $(this).find(".content-segmento");
            if ($content.hasClass("open")){
                $content.slideUp();
                $content.removeClass("open");
            }
            else{
                $content.slideDown();
                $content.addClass("open");
            }
        }
    });
    $("ul.ul-cuotas li.subcuentas").off().on({
        "mouseenter"  : function(){
            $(this).addClass("over");
        },
        "mouseleave" : function(){
            $(this).removeClass("over");
        },
        "click" : function(){

        }
    });

    $(".content-segmento").hide();
}

function change_interes(){
    var interes = $("input[name=tipoInteres]:checked").val();
    
    if (parseInt(interes)==1){
        $("#txtPeriodicidadTasa").val("30");
        $(".tasa_periodo").show();
    }
    else{
        $("#txtPeriodicidadTasa").val("0");
        $(".tasa_periodo").hide();
    }
    
}

function change_subsidio(){
    var interes = $("input[name=chkSubidio]:checked").val();
    
    if (parseInt(interes)==1){
        var int_compensatorio = $("#txtInteresCompensatorio").val();
        $("#txtTasaSubsidio").val(int_compensatorio);
        $(".tasa_subsidio").show();
    }
    else{
        $("#txtTasaSubsidio").val("0");
        $(".tasa_subsidio").hide();
    }
    
}

function generar_cuota(id_credito, fecha){
    $.ajax({
        url : _cuotas.URL + "/x_generar_cuota",
        type : "post",
        data : {
            credito_id : id_credito,
            fecha: fecha
        },
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
        }
    });    
}

function mostrar_credito(){
    $.ajax({
        url : _cuotas.URL + "/x_actualizar_lista",
        type : "post",
        data : {
            credito_id : _cuotas.ID_CREDITO
        },
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
        }
    });
}

function mostrar_variacion(id_variacion){
    $.ajax({
        url : _cuotas.URL + "/x_obtener_pago",
        type : "post",
        data : {
            id_variacion : id_variacion,
            credito_id : _cuotas.ID_CREDITO
        },
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
        }
    });
}


function modificar_fecha_inicio(){
    
}
function modificar_fecha_vencimiento(){
    
}

function modificar_opciones_cuota(id_credito, cuotas_restantes,e){
    stopBubble(e);
    $.ajax({
        url : _cuotas.URL + "/x_abrir_opciones_cuota",
        data : {
            credito_id : id_credito,
            cuotas_restantes : cuotas_restantes
        },
        type : "post",
        success : function(html){
            $("#div_opciones_cuotas").html(html);
            var fecha_inicio = $("#txtFechaInicioEdit").val();
            var fecha_vencimiento = $("#txtFechaVencimientoEdit").val();
            
            $("a#inline").trigger("click");
            $(".fecha_opciones").datepicker({
                changeMonth: true,
                changeYear: true        
            });
            $(".fecha_opciones" ).datepicker( "option", "dateFormat", 'dd-mm-yy' );
            var desde = new Date(fecha_inicio * 1000) ;
            var hasta = new Date(fecha_vencimiento * 1000) ;
            $("#txtFechaInicioEdit").datepicker("setDate",desde );
            $("#txtFechaVencimientoEdit").datepicker("setDate",hasta );
        }
    });
}

function guardar_opciones_cuota(id_credito, cuotas_restantes){
    var fecha_vencimiento =  $.datepicker.formatDate('@',  $("#txtFechaVencimientoEdit").datepicker( "getDate" ))/1000;
    var fecha_inicio =  $.datepicker.formatDate('@',  $("#txtFechaInicioEdit").datepicker( "getDate" ))/1000;    
    
    $.ajax({
        url : _cuotas.URL + "/x_guardar_opciones_cuota",
        data : {
            credito_id : id_credito,
            cuotas_restantes : cuotas_restantes,
            fecha_inicio : fecha_inicio,
            fecha_vencimiento : fecha_vencimiento
        },
        type : "post",
        success : function(rtn){
            console.log(rtn);
            $.fancybox.close();         
            location.reload();
        }
    });
}


function cancelar_opciones_cuota(){
    $.fancybox.close();             
}


function enviar_cuotas(id_credito, fecha){
    $.ajax({
        url : _cuotas.URL + "/x_enviar_cuota",
        data : {
            fecha : fecha,
            credito_id : id_credito
        },
        type : "post",
        success : function(data){
            console.log(data);
        }
    });
}

function cambiar_accion(){
    var selected = parseInt($("#txtEvento option:selected").val());
    $("#div-monto").show();
    $("#spMonto").text("Monto");
    switch(selected){
        case 5:
        case 6:
        case 0:
            $("#div-monto").hide();
            break;
            
        case 1:break;
        case 2:break;
        case 3:
            $("#spMonto").text("Tasa %");
            break;
        case 4:break;
    }
}