var _version_id = 0;
var _desembolso_selected = {};

var _cuotas_filtradas = [];

$(document).ready(function() {

    $(".fecha").datepicker({
        changeMonth: true,
        changeYear: true
    });
    $(".fecha").datepicker("option", "dateFormat", 'dd-mm-yy');
    var fecha = new Date(_cuotas_m.FECHA * 1000);
    $("#txtFecha").datepicker("setDate", fecha).hide();
    $("#txtFecha2").text($("#txtFecha").val()).show();
  //  $("#txtFecha").attr("disabled","true");
    $("#txtFecha2").on({
        "click" : function(){
            jConfirm("¿Esta seguro que desea cambiar la fecha de ingreso de eventos?","Cambio de fechas", function(i){
                if(i){
                    $("#txtFecha2").hide();
                    $("#txtFecha").show();

                }
            });
        }
    });
    
    $("a#inline").fancybox().hide();

    change_interes();
    change_subsidio();
    console.dir(_cuotas_m.ID_CREDITO);
    _events_lista();

    cambiar_accion();

    $(".versiones_content").hide();
    _renew_versiones();

});

function _renew_versiones(){

    $('#divVersiones').jqxTree({source: _cuotas_m.VERSIONES, height: '300px', width: '300px'});
    $('#divVersiones').on('select', function(event)
    {
        var args = event.args;
        var item = $('#divVersiones').jqxTree('getItem', args.element);
        _version_id = item.value;
    });
}

function calcular_pago() {
    var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    var creadito_id = _cuotas_m.ID_CREDITO;
    var monto = $("#txtMonto").val();

    $.ajax({
        url: _cuotas_m.URL + "/x_set_pago",
        data: {
            fecha: fecha,
            credito_id: creadito_id,
            version_id: _version_id || 0,
            monto: monto
        },
        type: "post",
        success: function(result) {
            $(".div-result").html(result);
            _events_lista();
        }
    });
}



function agregar_variacion() {
    var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    var opcion = parseInt($("#txtEvento option:selected").val())



    switch (opcion) {
        case 0:

            _cuotas_m.mostrar_estado(_cuotas_m.ID_CREDITO, fecha);
            break;
        case 1:
            var monto = $("#txtMonto").val();
            _cuotas_m.agregar_desembolso(_cuotas_m.ID_CREDITO, monto, fecha);
            break;
        case 2:
            var gasto = $("#txtMonto").val();
            var descripcion = $("#txtDescripcion").val();
            _cuotas_m.agregar_gasto(_cuotas_m.ID_CREDITO, gasto, fecha, descripcion);
            break;
        case 3:
            
            var tasa = $("#txtMonto").val();
            var subsidio = $("#txtSubsidio").val();
            var moratorio = $("#txtMoratorio").val();
            var punitorio = $("#txtPunitorio").val();
            
            _cuotas_m.agregar_cambiotasa(_cuotas_m.ID_CREDITO, tasa, subsidio, moratorio, punitorio, fecha);
            
            break;
        case 4:
            modificar_vencimiento();
            break;
        case 5:
            generar_cuota(_cuotas_m.ID_CREDITO, fecha);
            break;
        case 6:
            enviar_cuotas(_cuotas_m.ID_CREDITO, fecha);
            break;
        case 8:
            generar_chequeras(_cuotas_m.ID_CREDITO, fecha);
            break;
    }

    $("#txtMonto").val("");
    $("txtFecha").focus();
    $("#txtEvento option").eq(0).attr("selected", "selected");
}

_cuotas_m.mostrar_estado = function(id_credito, fecha) {
    $.ajax({
        url: _cuotas_m.URL + "/x_get_detalle_cuotas",
        type: "post",
        data: {
            credito_id: id_credito,
            monto: 0,
            version_id: _version_id || 0,
            fecha: fecha
        },
        dataType : "json",
        success: function(result) {
            $(".div-result").html(result.html);
            $(".saldo_cuotas").hide();
            
            if (parseInt(result.rtn)==5){
                jConfirm("Hay cuotas a las que se le han vencido los plazos de pago y el subsidio ha sido anulado, Desea reimputar pagos ahora?", function(e) {
                    if (e) {
                //        _cuotas_m.agregar_desembolso(id_credito, monto, fecha, 1);
                    }
                });
            }
            

            $('#tabs_cuotas').jqxTabs({width: 800});
            $('#tabs_cuotas').jqxTabs({scrollPosition: 'both'});
            $('#tabs_cuotas').on('selected', function(event)
            {
                var selectedTab = event.args.item;
                if (selectedTab > 0) {
                    $clone = $(".contenido li").eq(selectedTab - 1).html();
                    $(".jqx-tabs-content-element.jqx-rc-b").eq(selectedTab).html("<div class='content-cuotas-esp'>"+$clone+"</div>");
                }
            });
            // _events_lista();*/
        }
    });
}

_cuotas_m.agregar_desembolso = function(id_credito, monto, fecha, reset) {
    reset = reset || 0;
    $.ajax({
        url: _cuotas_m.URL + "/x_agregar_desembolso",
        type: "post",
        data: {
            credito_id: id_credito,
            monto: monto,
            fecha: fecha,
            version_id: _version_id || 0,
            desembolso : _desembolso_selected || 0,
            reset: reset
        },
        success: function(result) {
            _desembolso_selected = {};
            if (result == "-1") {
                jConfirm("Los desembolsos teoricos deben ser eliminados para agregar este evento, ¿quiere eliminarlos ahora y agregar este evento?", "MENDOZA FIDUICIARIA", function(e) {
                    if (e) {
                        _cuotas_m.agregar_desembolso(id_credito, monto, fecha, 1);
                    }
                });
            }
            else {
                $(".div-result").html(result);
                _events_lista();
            }
        }
    });
}

_cuotas_m.agregar_gasto = function(id_credito, monto, fecha, descripcion) {
    $.ajax({
        url: _cuotas_m.URL + "/x_agregar_gasto",
        type: "post",
        data: {
            creditos: id_credito,
            monto: monto,
            version_id: _version_id || 0,
            descripcion: descripcion || "",
            fecha: fecha
        },
        success: function(result) {
            $(".div-result").html(result);
            _events_lista();
        }
    });
};

_cuotas_m.agregar_cambiotasa = function(id_credito, tasa, subsidio, moratorio, punitorio, fecha) {
    $.ajax({
        url: _cuotas_m.URL + "/x_agregar_cambiotasa",
        type: "post",
        data: {
            credito_id: id_credito,
            tasa: tasa,
            subsidio : subsidio,
            moratorio : moratorio,
            punitorio : punitorio,
            version_id: _version_id || 0,
            fecha: fecha
        },
        success: function(result) {
            $(".div-result").html(result);
            _events_lista();
        }
    });
};
/*
_cuotas_m.agregar_cambiotasa = function(id_credito, tasa, fecha) {
    $.ajax({
        url: _cuotas_m.URL + "/x_agregar_cambiotasa",
        type: "post",
        data: {
            credito_id: id_credito,
            tasa: tasa,
            version_id: _version_id || 0,
            fecha: fecha
        },
        success: function(result) {
            $(".div-result").html(result);
            _events_lista();
        }
    });
}
*/

function eliminar_variacion(id_variacion) {
    $.ajax({
        url: _cuotas_m.URL + "/x_eliminar_variacion",
        data: {
            id_variacion: id_variacion,
            version_id: _version_id || 0,
            credito_id: _cuotas_m.ID_CREDITO
        },
        type: "post",
        success: function(result) {
            $(".div-result").html(result);
            _events_lista();
        }
    });
}


function _events_lista() {
    $("ul.ul-cuotas li").off().on({
        "mouseenter": function() {
            $(this).addClass("over");
        },
        "mouseleave": function() {
            $(this).removeClass("over");
        },
        "click": function() {
            var $content = $(this).find(".content-segmento");
            if ($content.hasClass("open")) {
                $content.slideUp();
                $content.removeClass("open");
            }
            else {
                $content.slideDown();
                $content.addClass("open");
            }
        }
    });
    $("ul.ul-cuotas li.subcuentas").off().on({
        "mouseenter": function() {
            $(this).addClass("over");
        },
        "mouseleave": function() {
            $(this).removeClass("over");
        },
        "click": function() {

        }
    });

    $(".content-segmento").hide();
}

function change_interes() {
    var interes = $("input[name=tipoInteres]:checked").val();

    if (parseInt(interes) == 1) {
        $("#txtPeriodicidadTasa").val("30");
        $(".tasa_periodo").show();
    }
    else {
        $("#txtPeriodicidadTasa").val("0");
        $(".tasa_periodo").hide();
    }

}

function change_subsidio() {
    var interes = $("input[name=chkSubidio]:checked").val();

    if (parseInt(interes) == 1) {
        var int_compensatorio = $("#txtInteresCompensatorio").val();
        $("#txtTasaSubsidio").val(int_compensatorio);
        $(".tasa_subsidio").show();
    }
    else {
        $("#txtTasaSubsidio").val("0");
        $(".tasa_subsidio").hide();
    }

}

function generar_cuota(id_credito, fecha) {
    $.ajax({
        url: _cuotas_m.URL + "/x_generar_cuota",
        type: "post",
        data: {
            credito_id: id_credito,
            version_id: _version_id || 0,
            fecha: fecha
        },
        success: function(result) {
            $(".div-result").html(result);
            _events_lista();
        }
    });
}

function mostrar_credito() {
    filtrar_cuotas();
/*    $.ajax({
        url: _cuotas_m.URL + "/x_actualizar_lista",
        type: "post",
        data: {
            credito_id: _cuotas_m.ID_CREDITO,
            version_id: _version_id || 0
        },
        success: function(result) {
            $(".div-result").html(result);
            _events_lista();
        }
    });*/
}

function mostrar_variacion(id_variacion) {
    $.ajax({
        url: _cuotas_m.URL + "/x_obtener_pago",
        type: "post",
        data: {
            version_id: _version_id || 0,
            id_variacion: id_variacion,
            credito_id: _cuotas_m.ID_CREDITO
        },
        success: function(result) {
            $(".div-result").html(result);
            _events_lista();
        }
    });
}


function modificar_fecha_inicio() {

}
function modificar_fecha_vencimiento() {

}

function modificar_opciones_cuota(id_credito, cuotas_restantes, e) {
    stopBubble(e);
    $.ajax({
        url: _cuotas_m.URL + "/x_abrir_opciones_cuota",
        data: {
            version_id: _version_id || 0,
            credito_id: id_credito,
            cuotas_restantes: cuotas_restantes
        },
        type: "post",
        success: function(html) {
            $("#div_opciones_cuotas").html(html);
            var fecha_inicio = $("#txtFechaInicioEdit").val();
            var fecha_vencimiento = $("#txtFechaVencimientoEdit").val();

            $("a#inline").trigger("click");
            $(".fecha_opciones").datepicker({
                changeMonth: true,
                changeYear: true
            });
            $(".fecha_opciones").datepicker("option", "dateFormat", 'dd-mm-yy');
            var desde = new Date(fecha_inicio * 1000);
            var hasta = new Date(fecha_vencimiento * 1000);
            $("#txtFechaInicioEdit").datepicker("setDate", desde);
            $("#txtFechaVencimientoEdit").datepicker("setDate", hasta);
        }
    });
}

function guardar_opciones_cuota(id_credito, cuotas_restantes) {
    var fecha_vencimiento = $.datepicker.formatDate('@', $("#txtFechaVencimientoEdit").datepicker("getDate")) / 1000;
    var fecha_inicio = $.datepicker.formatDate('@', $("#txtFechaInicioEdit").datepicker("getDate")) / 1000;

    $.ajax({
        url: _cuotas_m.URL + "/x_guardar_opciones_cuota",
        data: {
            version_id: _version_id || 0,
            credito_id: id_credito,
            cuotas_restantes: cuotas_restantes,
            fecha_inicio: fecha_inicio,
            fecha_vencimiento: fecha_vencimiento
        },
        type: "post",
        success: function(rtn) {
            console.log(rtn);
            $.fancybox.close();
            // location.reload();
        }
    });
}


function cancelar_opciones_cuota() {
    $.fancybox.close();
}


function enviar_cuotas(id_credito, fecha) {
    $.ajax({
        url: _cuotas_m.URL + "/x_enviar_cuota",
        data: {
            version_id: _version_id || 0,
            fecha: fecha,
            credito_id: id_credito
        },
        type: "post",
        success: function(data) {
            console.log(data);
        }
    });
}

function cambiar_accion() {
    var selected = parseInt($("#txtEvento option:selected").val());
    _desembolso_selected = {};
    $("#div-monto").show();
    $("#eventos-pendientes").hide().html("");
    $("#spMonto").text("Monto");
    $("#div-descripcion").hide();
    $("#div-fecha").hide();
    $(".field_tasas").hide();
    switch (selected) {
        case 8:
        case 5:
        case 6:
        case 0:
            $("#div-monto").hide();
            break;

        case 1:
            leer_desembolsos();
            break;
        case 2:
            $("#div-descripcion").show();
            break;
        case 3:
            $("#spMonto").text("Tasa %");
            get_tasas_fecha();
            $(".field_tasas").show();
            break;
        case 4:
            $("#div-monto").hide();
            $("#div-fecha").show();
            break;
    }
}

function recalcular_cuotas(){
    $.ajax({
        url : _cuotas_m.URL + "/x_segmentar",
        data : {
            version_id: _version_id || 0,
            credito_id: _cuotas_m.ID_CREDITO
        },
        type : "post",
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
            console.log(result);
        }
    });
}

function leer_desembolsos(){
    $.ajax({
        url : _cuotas_m.URL + "/x_leer_desembolsos_pendientes",
        data : {
            credito_id: _cuotas_m.ID_CREDITO
        },
        type : "post",
        dataType : "json",
        success : function(result){
            var desembolsos = result.desembolsos;
            $("#eventos-pendientes").fadeIn().html(result.view);
            $("#eventos-pendientes .lista-desmbolsos-solicitados").on({
                "click" : function(){
                    var index = $(this).index()-1;
                    console.dir(desembolsos[index]);
                    $("#txtMonto").val(desembolsos[index]['DES_MONTO']);
                    _desembolso_selected = desembolsos[index];
                    volver_desembolsos_solicitados();
                }
            });
        }
    });
}

function eliminar_cuotas(){
    $.ajax({
        url : _cuotas_m.URL + "/x_eliminar_version",
        data : {
            version_id : _version_id || 0,
            credito_id : _cuotas_m.ID_CREDITO || 0
        },
        type : "post",
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
            console.log(result);            
        }
    });
}

function eliminar_gasto(id){
    $.ajax({
        url : _cuotas_m.URL + "/x_eliminar_gasto",
        data : {
            version_id : _version_id || 0,
            credito_id : _cuotas_m.ID_CREDITO || 0,
            gasto : id
        },
        type : "post",
        success : function(result){
            $(".div-result").html(result);
            _events_lista();
            console.log(result);            
        }
    });
}

function volver_desembolsos_solicitados(){
    $("#eventos-pendientes").fadeOut();
}

function filtrar_cuotas(){
    var desde = $.datepicker.formatDate('@', $("#txtFechaDesde").datepicker("getDate")) / 1000;
    var hasta = $.datepicker.formatDate('@', $("#txtFechaHasta").datepicker("getDate")) / 1000;
    $.ajax({
        url : _cuotas_m.URL + "/x_filtrar_cuotas",
        data :{
            creditos : _cuotas_m.ID_CREDITO,
            desde : desde || 0,
            hasta : hasta || 0
        },
        type : "post",
        dataType : "json",
        success : function(result){
            console.dir(result.cuotas);
            _cuotas_filtradas = result.cuotas;
      
            $(".div-result").html(result.view);
        }
    });
}

function modificar_vencimiento(){
    var vencimiento = $.datepicker.formatDate('@', $("#txtFechaCambioVencimiento").datepicker("getDate")) / 1000;
    
    $.ajax({
        url : _cuotas_m.URL + "/x_modificar_vencimiento",
        data :{
            creditos : _cuotas_m.ID_CREDITO,
            cuotas : _cuotas_filtradas ,
            fecha_vencimiento : vencimiento
        },
        type : "post",
        success : function(result){
      //      console.dir(rtn);
            $(".div-result").html(result);
        }
    });
}

function generar_chequeras(creditos){
    
    var desde = $.datepicker.formatDate('@', $("#txtFechaDesde").datepicker("getDate")) / 1000;
    var hasta = $.datepicker.formatDate('@', $("#txtFechaHasta").datepicker("getDate")) / 1000;
    var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    
    $.ajax({
        url : _cuotas_m.URL + "/x_generar_chequera",
        data : {
            creditos : creditos,
            cuotas : _cuotas_filtradas ,
            fecha : fecha || 0,
            fecha_desde  : desde || 0,
            fecha_hasta : hasta || 0
        },
        type : "post",
        success : function(rtn){
            $( '#frmPrint' ).attr( 'src', function ( i, val ) { return val; });            
            $(".content-result").show();
            document.getElementById('frmPrint').contentDocument.location.reload(true);
            $(".content-cuotas").hide();
        }
        
        
    });
}

function get_tasas_fecha(){
    var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    $.ajax({
        url : _cuotas_m.URL + "/x_get_tasas_fecha",
        data : {
            fecha : fecha,
            cuotas : _cuotas_filtradas,
            credito_id :  _cuotas_m.ID_CREDITO,
            version_id : _version_id || 0
        },
        type : "post",
        dataType : "json",
        success : function(rtn){
            $("#txtMonto").val(rtn.COMPENSATORIO);
            $("#txtSubsidio").val(rtn.SUBSIDIO);
            $("#txtMoratorio").val(rtn.MORATORIO);
            $("#txtPunitorio").val(rtn.PUNITORIO);
        }
    });
}


function imprimir_frame(){
    window.frames["frmPrint"].focus();
    window.frames["frmPrint"].print();
}

function salir_chequera(){
        $(".content-result").hide();
        $(".content-cuotas").show();
}