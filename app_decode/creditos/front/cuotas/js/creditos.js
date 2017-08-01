var _version_id = 0;
var _desembolso_selected = {};
var _version_change = false;

_cuotas.start = function(){
    if (parseInt(_cuotas.MODIFICAR)===1){
       $(".editar").show();
    }
    else{
        $(".editar").hide();
    }

   if (parseInt(_cuotas.ESTADO)===3){
       $("#txtEvento option").eq(7).show();
       console.log(_cuotas.ESTADO+"lelga si");
   }
   else{
       console.log(_cuotas.ESTADO+"lelga no");
       $("#txtEvento option").eq(7).hide();
   }

    $(".fecha").datepicker({
        changeMonth: true,
        changeYear: true,
        onSelect : function(){
            if ($(this).attr("id")==='txtFecha'){
                var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
                _cuotas.mostrar_estado(_cuotas.ID_CREDITO, fecha);
            }
        }
    });
    var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    _cuotas.mostrar_estado(_cuotas.ID_CREDITO, fecha);
    
    $(".fecha").datepicker("option", "dateFormat", 'dd-mm-yy');
    var fecha = new Date(_cuotas.FECHA * 1000);
    $("#txtFecha").datepicker("setDate", fecha).hide();
    $("#txtFecha2").text($("#txtFecha").val()).show();
  //  $("#txtFecha").attr("disabled","true");
    $("#txtFecha2").on({
        "click" : function(){
                    $("#txtFecha2").hide();
                    $("#txtFecha").show();
/*            
            jConfirm("¿Esta seguro que desea cambiar la fecha de ingreso de eventos?","Cambio de fechas", function(i){
                if(i){
                }
            });*/
        }
    });
    
    $("a#inline").fancybox().hide();
    $("#txtMonto, #txtSubsidio, #txtMoratorio, #txtPunitorio").validationKeySpecial('mon');

    change_interes();
    change_subsidio();
    _events_lista();
    cambiar_accion();
    _renew_versiones();
    
    $(".titulo-versiones").on({
       "click" : function(){
           if ($(".wrap_version").hasClass("min")){
               $(".wrap_version").removeClass("min");
               $(".wrap_version").slideDown();
               
           }
           else{
               $(".wrap_version").addClass("min");
               $(".wrap_version").slideUp();
           }
       }
    });
    
    $(".wrap_version").hide().addClass("min");
};



function _renew_versiones(){

    $('#divVersiones').jqxTree({source: _cuotas.VERSIONES, height: '300px', width: '300px'});
    $('#divVersiones').jqxTree('expandAll');

    $('#divVersiones').on('select', function(event)
    {
        var args = event.args;
        var item = $('#divVersiones').jqxTree('getItem', args.element);
        console.log(item);
        console.dir(item);    
        _version_id = item.value;
        $("#spVersionTitulo").text("VER: "+item.value+" - DESC: "+item.label);
    });
    
    var item = $('#divVersiones').jqxTree('getSelectedItem');

    _version_id = item.value;
    $("#spVersionTitulo").text("VER: "+item.value+" - DESC: "+item.label);
}


function existDesembolsos(creadito_id, fecha){
    var ret = false;
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Verificando desembolsos</h4>' });
    $.ajax({
        url: _cuotas.URL + "/x_verificar_desembolsos",
        data: {
            fecha: fecha,
            credito_id: creadito_id,
            version_id: _version_id || 0
        },
        type: "post",
        async : false,
        success: function(result) {
            $.unblockUI();
            if (result == '-1') {
                ret = true;
                return;
            }         
        }
    });
    
    return ret;
}

_cuotas.agregar_pago = function(id_credito, fecha, monto, confirm){
    confirm = confirm || false;
    _version_change = false;
    if (!confirm){
        if (!existDesembolsos(id_credito, fecha)){
            jAlert("Debe agregar el 100% de los desembolsos para agregar este evento.", "MENDOZA FIDUICIARIA", function() {
                return;
            });      
            return;
        }
    }

    if (existEventosPosteriores() && !confirm){
        jConfirm("Hay eventos posteriores ¿Desea reimputar esos eventos? ","MENDOZA FIDUCIARIA", function(e){
            if (e){
                agregar_pago(id_credito, fecha, monto);
            } else {
                return;
            }
        });
    } else {
        agregar_pago(id_credito, fecha, monto);
    }
};

function agregar_pago(id_credito, fecha, monto) {
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    $.ajax({
        url: _cuotas.URL + "/x_set_pago",
        data: {
            fecha: fecha,
            credito_id: id_credito,
            version_id: 0,
            monto: monto
        },
        type: "post",
        success: function(result) {
            if (result == '-1') {
                jAlert("No se guardó el recupero. Se deben cargar el 100% de desembolsos", "MENDOZA FIDUICIARIA", function(e) {
                    return;
                });
            } else if (result == '-2') {
                jAlert("El monto ingresado es incorrecto", "MENDOZA FIDUICIARIA", function(e) {
                    return;
                });
            } else {
                $(".div-result").html(result);
                $.unblockUI();
                _events_lista();
            }
        }
    });
}

function agregar_variacion() {
    
    var validar_fecha = $.datepicker.formatDate('dd-mm-yy', $("#txtFecha").datepicker("getDate"));
    if (validar_fecha != $("#txtFecha").val()) {
        jAlert("La fecha ingresada es incorrecta", "ERROR");
        return;
    }
    var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    var opcion = parseInt($("#txtEvento option:selected").val());

    switch (opcion) {
        case 0:

            _cuotas.mostrar_estado(_cuotas.ID_CREDITO, fecha);
            break;
        case 1:
            var monto = $("#txtMonto").val();
            _cuotas.agregar_desembolso(_cuotas.ID_CREDITO, monto, 1, fecha);
            break;
        case 2:
            var gasto = $("#txtMonto").val();
            var descripcion = $("#txtDescripcion").val();
            _cuotas.agregar_gasto(_cuotas.ID_CREDITO, gasto, fecha, descripcion, true);
            break;
        case 3:
            var tasa = $("#txtMonto").val();
            var subsidio = $("#txtSubsidio").val();
            var moratorio = $("#txtMoratorio").val();
            var punitorio = $("#txtPunitorio").val();
            
            _cuotas.agregar_cambiotasa(_cuotas.ID_CREDITO, tasa, subsidio, moratorio, punitorio, fecha);
            $("#txtPunitorio, #txtMoratorio, #txtSubsidio").val("");
            break;
        case 4:
            //_cuotas.agregar_pago();
            var monto = $("#txtMonto").val();
            _cuotas.agregar_pago(_cuotas.ID_CREDITO,  fecha, monto);
            break;
        case 5:
            desimputar_pagos(_cuotas.ID_CREDITO, fecha);
            break;
        case 6:
            enviar_cuotas(_cuotas.ID_CREDITO, fecha);
            break;
            
        case 7:
            var monto = $("#txtMonto").val();
            _cuotas.agregar_desembolso(_cuotas.ID_CREDITO, monto, 0, fecha);
            break;
        case 8:
            generar_chequeras();
            break;            
        case 9:
            refinanciar();
            break;
        case 10:
            refinanciacion_caida();
            break;
        case 11:
            credito_caido();
            break;
        case 12:
            if (!ajuste(0)) {
                return;
            }
            break;
        case 13:
            if (!ajuste(1)) {
                return;
            }
            break;
    }

    $("#txtMonto").val("");
    $("txtFecha").focus();
    $("#txtEvento option").eq(0).attr("selected", "selected");
    cambiar_accion();
}

_cuotas.mostrar_estado = function(id_credito, fecha) {
    $.ajax({
        url: _cuotas.URL + "/x_get_detalle_cuotas",
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
              _events_lista();
            $(".saldo_cuotas").hide();
            
            if (parseInt(result.rtn)===5){
                jConfirm("Hay cuotas a las que se le han vencido los plazos de pago y el subsidio ha sido anulado, Desea generar una nueva version con reimputación de pagos ahora?", "ATENCION!",function(e) {
                  /*  if (e) {
                        _version_id = generar_nueva_version(id_credito, _version_id , result.fecha_vencimiento_subsidio);
                        var tasas = get_tasas_fecha(result.fecha_vencimiento_subsidio);
                        _version_change = true;
                        _cuotas.agregar_cambiotasa(id_credito, tasas.COMPENSATORIO, 0, tasas.MORATORIO, tasas.PUNITORIO, result.fecha_vencimiento_subsidio, true);
                        _renew_versiones();
                    }*/
                });
            }
        }
    });
};

_cuotas.agregar_desembolso = function(id_credito, monto, tipo, fecha, reset, confirm) {
    
    confirm = confirm || false;
    reset = reset || 0;
    
    if (existEventosPosteriores() && !confirm){
        jConfirm("¿Hay eventos posteriores, desea reimputar estos eventos? ","MENDOZA FIDUCIARIA", function(e){
            if (e){
                agregar_desembolso(id_credito, monto, tipo, fecha, reset);
            }
        });
        return;
    }
    
    agregar_desembolso(id_credito, monto, tipo, fecha, reset);
};

function agregar_desembolso(id_credito, monto, tipo, fecha, reset) {
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    $.ajax({
        url: _cuotas.URL + "/x_agregar_desembolso",
        type: "post",
        async : false,
        data: {
            credito_id: id_credito,
            tipo: tipo,
            monto: monto,
            fecha: fecha,
            version_id: _version_id || 0,
            desembolso : _desembolso_selected || 0,
            reset: reset
        },
        success: function(result) {
            _desembolso_selected = {};
            if (result === "-1") {
/*                jConfirm("Los desembolsos teoricos deben ser eliminados para agregar este evento, ¿quiere eliminarlos ahora y agregar este evento?", "MENDOZA FIDUICIARIA", function(e) {
                    if (e) {
                        _cuotas.agregar_desembolso(id_credito, monto, fecha, 1, true);
                    }
                });*/
                 _cuotas.agregar_desembolso(id_credito, monto, tipo, fecha, 1, true);
            } else if (result === "-2") {
                jAlert("El monto total de desembolsos realizados es superior al total del crédito", "MENDOZA FIDUICIARIA", function(e) {
                    return;
                });
            } else {
                $(".div-result").html(result);
                _events_lista();
                $.unblockUI();
            }
        }
    });
}

_cuotas.agregar_gasto = function(id_credito, monto, fecha, descripcion, confirm) {
    confirm = confirm || false;
    
    if (existEventosPosteriores() && confirm) {
        jConfirm("¿Hay eventos posteriores, desea reimputar estos eventos? ","MENDOZA FIDUCIARIA", function(e){
            if (e){
                _agregar_gasto(id_credito, monto, fecha, descripcion);
            }
        });
        return;
    }
    
    _agregar_gasto(id_credito, monto, fecha, descripcion);
};

function _agregar_gasto(id_credito, monto, fecha, descripcion) {
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });

    $.ajax({
        url: _cuotas.URL + "/x_agregar_gasto",
        type: "post",
        data: {
            credito_id: id_credito,
            monto: monto,
            version_id: _version_id || 0,
            descripcion : descripcion,
            fecha: fecha
        },
        success: function(result) {
            $.unblockUI();
            if (result=='-1'){
                jAlert("Debe agregar desembolsos reales para agregar este evento.", "MENDOZA FIDUICIARIA", function(e) {
                    return;
                });                
                return;
            }

          $(".div-result").html(result);
            _events_lista();

            jAlert("Se ha agregado el gasto correctamente","Eventos Cargados", function(){
                return;
            }
        );
        }
    });
};

_cuotas.agregar_cambiotasa = function(id_credito, tasa, subsidio, moratorio, punitorio, fecha, confirm) {
    confirm = confirm || false;

    if (existEventosPosteriores() && !confirm){
        jConfirm("Hay eventos posteriores. ¿Desea reimputar los pagos? ","MENDOZA FIDUCIARIA", function(e){
            if (e){
                agregar_cambiotasa(id_credito, tasa, subsidio, moratorio, punitorio, fecha, true);
            }
        });
        return;
    }
    
    agregar_cambiotasa(id_credito, tasa, subsidio, moratorio, punitorio, fecha);
}

function agregar_cambiotasa(id_credito, tasa, subsidio, moratorio, punitorio, fecha, reimputar) {
    reimputar = reimputar || false;
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
    $.ajax({
        url: _cuotas.URL + "/x_agregar_cambiotasa",
        type: "post",
        data: {
            credito_id: id_credito,
            tasa: tasa,
            subsidio : subsidio,
            moratorio : moratorio,
            punitorio : punitorio,
            version_id: 0,
            fecha: fecha,
            reimputar: reimputar
        },
        success: function(result) {
            $.unblockUI();
            if (result=='-1'){
                jAlert("Debe agregar desembolsos reales para agregar este evento.", "MENDOZA FIDUICIARIA", function(e) {
                    return;
                });                
                return;
            }
            $(".div-result").html(result);
            _events_lista();
        }
    });
};


function eliminar_variacion(id_variacion) {
    $.ajax({
        url: _cuotas.URL + "/x_eliminar_variacion",
        data: {
            id_variacion: id_variacion,
            version_id: _version_id || 0,
            credito_id: _cuotas.ID_CREDITO
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
        url: _cuotas.URL + "/x_generar_cuota",
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
    $.ajax({
        url: _cuotas.URL + "/x_actualizar_lista",
        type: "post",
        data: {
            credito_id: _cuotas.ID_CREDITO,
            version_id: _version_id || 0
        },
        success: function(result) {
            $(".div-result").html(result);
            _events_lista();
        }
    });
}

function mostrar_variacion(id_variacion) {
    $.ajax({
        url: _cuotas.URL + "/x_obtener_pago",
        type: "post",
        data: {
            version_id: _version_id || 0,
            id_variacion: id_variacion,
            credito_id: _cuotas.ID_CREDITO
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
    if (parseInt(_cuotas.MODIFICAR)===1){
    
    }
    else{
        return;
    }
    
    stopBubble(e);
    $.ajax({
        url: _cuotas.URL + "/x_abrir_opciones_cuota",
        data: {
            version_id: _version_id || 0,
            credito_id: id_credito,
            cuotas_restantes: cuotas_restantes
        },
        type: "post",
        success: function(result) {
            if (result=='-1'){
                jAlert("Debe agregar desembolsos reales para agregar este evento.", "MENDOZA FIDUICIARIA", function(e) {
                    return;
                });                
                return;
            }       
            
            $("#div_opciones_cuotas").html(result);
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
        url: _cuotas.URL + "/x_guardar_opciones_cuota",
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
        url: _cuotas.URL + "/x_enviar_cuota",
        data: {
            version_id: _version_id || 0,
            fecha: fecha,
            credito_id: id_credito
        },
        type: "post",
        success: function(result) {
            if (result=='-1'){
                jAlert("Debe agregar desembolsos reales para agregar este evento.", "MENDOZA FIDUICIARIA", function(e) {
                    return;
                });                
                return;
            }              
            console.log(result);
        }
    });
}

function cambiar_accion() {
    var selected = parseInt($("#txtEvento option:selected").val());
    _desembolso_selected = {};
    $("#eventos-pendientes").hide().html("");
    $("#spMonto").text("Monto");
    $("#div-monto,#div-descripcion,.field_tasas").hide();
    $("#txtMonto").val('');
    switch (selected) {
        case 1:
            $("#div-monto").show();
            leer_desembolsos();
            break;
        case 2:
            $("#div-monto").show();
            $("#div-descripcion").show();
            break;
        case 3:
            $("#div-monto").show();
            $("#spMonto").text("Tasa %");
            get_tasas_fecha();
            $(".field_tasas").show();
            break;
        case 4:
        case 12:
        case 13:
            $("#div-monto").show();
            break;
    }
}

function recalcular_cuotas(){
    $.ajax({
        url : _cuotas.URL + "/x_segmentar",
        data : {
            version_id: _version_id || 0,
            credito_id: _cuotas.ID_CREDITO
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
        url : _cuotas.URL + "/x_leer_desembolsos_pendientes",
        data : {
            credito_id: _cuotas.ID_CREDITO
        },
        type : "post",
        dataType : "json",
        success : function(result){
            var desembolsos = result.desembolsos;
            
            if (desembolsos.length > 0){
                $("#eventos-pendientes").fadeIn().html(result.view);
                $("#eventos-pendientes .lista-desmbolsos-solicitados").off("click").on({
                    "click" : function(){
                        var index = $(this).index()-1;
                        $("#txtMonto").val(desembolsos[index]['DES_MONTO']);
                        _desembolso_selected = desembolsos[index];
                        volver_desembolsos_solicitados();
                    }
                });
            }
            else{
                $("#txtMonto").focus().select();
            }

            console.log(result);
        }
    });
}

function eliminar_version(){

    
    $.ajax({
        url : _cuotas.URL + "/x_eliminar_version",
        data : {
            version_id : _version_id || 0,
            credito_id : _cuotas.ID_CREDITO || 0
        },
        type : "post",
        dataType : "json",
        success : function(result){
            console.dir(result);
            _cuotas.VERSIONES = result;
           // $(".div-result").html(result);
            
            _renew_versiones();
            
            _events_lista();
            


            
            console.dir(result);            
        }
    });
}

function eliminar_gasto(id){
    $.ajax({
        url : _cuotas.URL + "/x_eliminar_gasto",
        data : {
            version_id : _version_id || 0,
            credito_id : _cuotas.ID_CREDITO || 0,
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

function desimputar_pagos(id_credito, fecha, confirm){
    _version_change  = false;

    $.ajax({
        url: _cuotas.URL + "/x_recalcular_pago_imputaciones",
        data: {
            version_id: _version_id || 0,
            fecha: fecha,
            credito_id: id_credito
        },
        type: "post",
        success: function(result) {
            $.unblockUI();
            if (result=='-1'){
                jAlert("Debe agregar desembolsos reales para agregar este evento.", "MENDOZA FIDUICIARIA", function(e) {
                    return;
                });                
                return;
            }            
        }
    });
    
}


function get_tasas_fecha(fecha){
    var rtn_tasas = {};
    fecha = fecha || false;
    if (!fecha){
        fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    }
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
        
    $.ajax({
        url : _cuotas.URL + "/x_get_tasas_fecha",
        data : {
            fecha : fecha,
            credito_id :  _cuotas.ID_CREDITO,
            version_id : _version_id || 0
        },
        type : "post",
        async : false,
        dataType : "json",
        success : function(rtn){
            $("#txtMonto").val(rtn.COMPENSATORIO);
            $("#txtSubsidio").val(rtn.SUBSIDIO);
            $("#txtMoratorio").val(rtn.MORATORIO);
            $("#txtPunitorio").val(rtn.PUNITORIO);
            
            rtn_tasas = {
              COMPENSATORIO : rtn.COMPENSATORIO,
              SUBSIDIO : rtn.SUBSIDIO,
              MORATORIO : rtn.MORATORIO,
              PUNITORIO : rtn.PUNITORIO
            };
            
            $.unblockUI();
        }
    });
    return rtn_tasas;
}

function existEventosPosteriores(){
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Verificando otros eventos</h4>' });
    
    var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    var ret = false;
    $.ajax({
        url : _cuotas.URL + "/x_verificar_eventos_posteriores",
        data : {
            fecha : fecha,
            credito_id :  _cuotas.ID_CREDITO,
            version_id : _version_id || 0
        },
        type : "post",
        dataType : "json",
        async : false,
        success : function(rtn){
            if (parseInt(rtn)==1) {
                ret = true;
            }
            $.unblockUI();            
            
        }
    });
    return ret;
}


function generar_nueva_version(id_credito, id_version, fecha){
    var ret = id_version;
    $.ajax({
        url : _cuotas.URL + "/x_agregar_version",
        data : {
            fecha : fecha,
            credito_id :  id_credito,
            version_id : id_version || 0
        },
        type : "post",
        dataType : "json",
        async : false,
        success : function(rtn){
            _version_id = rtn.VERSION_ID;
            _cuotas.VERSIONES = rtn.VERSIONES;
            
            ret = rtn.VERSION_ID;
            
        }
    });
    return ret;
}

function eliminar_eventos_posteriores(id_credito, id_version, fecha){
    var ret = false;
    $.ajax({
        url : _cuotas.URL + "/x_eliminar_eventos_posteriores",
        data : {
            fecha : fecha,
            credito_id :  id_credito,
            version_id : id_version || 0
        },
        type : "post",
        dataType : "json",
        async : false,
        success : function(rtn){
            console.log(rtn);
            ret = true;
            
        }
    });
    return ret;
}

function make_active_version(id_credito){
    $.ajax({
        url : _cuotas.URL + "/x_make_active_version",
        data : {
            credito_id :  _cuotas.ID_CREDITO,
            version_id : _version_id || 0
        },
        type : "post",
        async : false,
        success : function(rtn){
            jAlert("La version "+_version_id+" es la version activa","Cambio de Version",function(){
                mostrar_credito();
            });
            
            ret = true;
            
        }
    });
    
}

function generar_chequeras(){

    var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    
    $.ajax({
        url : _cuotas.URL + "/x_generar_chequera",
        data : {
            credito : _cuotas.ID_CREDITO,
            fecha : fecha || 0
        },
        type : "post",
        success : function(rtn){

            $( '#frmPrint' ).attr( 'src', rtn);
            //$( '#frmPrint' ).attr( 'src', function ( i, val ) { return val; });

            $(".content-result").show();
            $(".content-cuotas").hide();
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

function imprimirEventos(){
    
    $.ajax({
        url : _cuotas.URL + "/x_generar_eventos",
        data : {
            credito : _cuotas.ID_CREDITO,
        },
        type : "post",
        success : function(rtn){

            $( '#frmPrint' ).attr( 'src', rtn);
            //$( '#frmPrint' ).attr( 'src', function ( i, val ) { return val; });

            $(".content-result").show();
            $(".content-cuotas").hide();
        }
        
        
    });
}

function refinanciar() {
    var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
    if (!existDesembolsos(_cuotas.ID_CREDITO, fecha)){
        jAlert("Debe agregar el 100% de los desembolsos para agregar este evento.", "MENDOZA FIDUICIARIA", function() {
            return;
        });      
        return;
    } else {
        $.ajax({
            url: _cuotas.URL + "/x_emitir_una_cuota",
            data: {
                fecha: fecha,
                credito_id: _cuotas.ID_CREDITO
            },
            type: "post",
            async : false,
            success: function(result) {
                if (result=='-1') {
                    ret = true;
                    return;
                } else {
                    $(".div-result").html(result);
                    $(window).scrollTop($(".div-result").offset().top);
                }
            }
        });
    }
}

function caducar() {
    //$("#frmagregar .content-gird").hide();
    
    var datos = [
        0,//caducar
        _cuotas.ID_CREDITO,
        $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000
    ];
    formaltabase(datos);
}

function caducarCuota() {
    var datos = [
        0,//caducar
        _cuotas.ID_CREDITO,
        $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000,
        1//1 cuota
    ];
    formaltabase(datos);
}

function refinanciacion_caida() {
    jConfirm("¿Esta seguro de cancelar este crédito y volver al anterior?","Caducidad", function(i){
        if(i){
            $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Refinanciaión caida</h4>' });
            $.ajax({
                url: _cuotas.URL + "/x_refinanciacion_caida",
                data: {
                    credito_id: _cuotas.ID_CREDITO
                },
                type: "post",
                async : true,
                success: function(result) {
                    if (result) {
                        location.href="creditos/front/creditos";
                    }
                }
            });
        }
    });
}

function prorroga() { 
    var datos = [
        0,//caducar
        _cuotas.ID_CREDITO,
        $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000,
        2//credito prorroga
    ];
    formaltabase(datos);
}

function caerCredito() {
    var datos = [
        0,//caducar
        _cuotas.ID_CREDITO,
        $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000,
        3//credito caido
    ];
    formaltabase(datos);
}

function formaltabase(datos) {
    $(".listado-credito").hide();
    load_app("creditos/front/formaltabase","#wpopup",datos, 
    function(){
        $(window).scrollTop($("#div-creidito-info").offset().top);
        $('#jqxgrid').hide();
        $.unblockUI();
    },
    function(){

    },
    function(){

    }); 
    
}


function credito_caido() {
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando crédito caido</h4>' });
    $.ajax({
        url: _cuotas.URL + "/x_credito_caido",
        data: {
            fecha: $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000,
            credito_id: _cuotas.ID_CREDITO
        },
        type: "post",
        success: function(result) {
            if (result=='-1') {
                ret = true;
                return;
            } else {
                $(".div-result").html(result);
                $(window).scrollTop($(".div-result").offset().top);
            }
        }
    });
}

function ajuste(tipo) {
    var monto = $("#txtMonto").val();
    if (monto != '' && monto > 0) {
        jConfirm("¿Está seguro de agregar el ajuste al crédito? ", "MENDOZA FIDUCIARIA", function (e) {
            $.blockUI({message: '<h4><img src="general/images/block-loader.gif" /> Procesando ajuste</h4>'});
            var fecha = $.datepicker.formatDate('@', $("#txtFecha").datepicker("getDate")) / 1000;
            $.ajax({
                url: _cuotas.URL + "/x_ajuste",
                data: {
                    fecha: fecha,
                    monto: monto,
                    tipo: tipo, //0 cobro, 1 pago
                    version_id: _version_id || 0,
                    id_credito: _cuotas.ID_CREDITO
                },
                type: "post",
                success: function (result) {
                    if (result == '1') {
                        jAlert('Hubo un inconveniente, intente nuevamente', 'MENDOZA FIDUCIARIA');
                    } else if (result == '2') {
                        jAlert('El crédito ya tiene un ajuste cargado', 'MENDOZA FIDUCIARIA');
                    } else {
                        $(".div-result").html(result);
                        $.unblockUI();
                        _events_lista();
                    }
                }
            });
        });
    } else {
        $("#txtMonto").focus();
        return false;
    }
    
    return true;
}