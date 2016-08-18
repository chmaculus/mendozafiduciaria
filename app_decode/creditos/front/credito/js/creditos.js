var mydata;
var id_edit;
var working = false;
var workingf = false;

var _array_chk = {};
var semmilla;

var checkboxHeight = "25";
var radioHeight = "25";
var selectWidth = "190";

var myfancy = 0;


var _creditos_lista = [];
var _credito_selected = 0;
var _opt_informe_selected = 0;


$(document).ready(function(){
    
   // tooltip_evento();
    semmilla = fGetNumUnico();
    mydata = '';
    
    $(".toolbar li").hover(
        function () {
            $(this).removeClass('li_sel').addClass('li_sel');
        },
        function () {
            $(this).removeClass('li_sel');
        }
    );
        
    $(".toolbar li:not(.sub)").click(function(e){
            e.preventDefault();
            var top = $(this).data('top');
            var obj = [];
            bloq();
            
            switch(top){

                case "informe":
                    get_informes();
                    break;
                case "eventos":
                    get_eventos();
                    break;
                case "estructura":
                    get_estructura();
                    break;
                case "opciones":
                    get_opciones_credito();
                    break;                    
                case "listado":
                    get_listado();
                    break;

            }
    });

    $(".right.less").on({
        "click" : function(){
            if ($(this).hasClass("selected")){
                $(this).removeClass("selected");
                $(".right.less").html("+");                
                $('#div-creidito-info .content-gird .form').fadeOut();
            }
            else{
                $(this).addClass("selected");
                $(".right.less").html("-");                
                $('#div-creidito-info .content-gird .form').fadeIn();
            }
            
            
        }
    });    

});

function get_opciones_credito(){
    
    
        
        
        $("#wpopup").show();
        load_app("creditos/front/creditosopciones","#wpopup",[_credito.ID], 
        function(){
            $(".right.less.selected").click();           
            $.unblockUI();
        },
        function(){
        },
        function(){
        }); 
        

}

function get_eventos(){

        load_app("creditos/front/cuotas","#wpopup",[_credito.ID, _credito._permiso_modificacion], 
        function(){
            $(".right.less.selected").click();           
            $.unblockUI();
        },
        function(){

        },
        function(){

        });

}



function get_estructura(){
    
        load_app("creditos/front/estructura","#wpopup",[_credito.ID], 
        function(){
            
            $(".right.less.selected").click();                       
            $.unblockUI();
        },
        function(){

        },
        function(){

        });
}



function get_listado(){
    var loc = $(".tb_lis").data("loc");
    location.href=  loc;
}


function get_informes(){

    $.ajax({
        url : _credito.URL + "/x_get_informes",
        data : {
            credito_id : _credito.ID
        },
        type : "post",
        async : false,
        success : function (rtn){
            $.unblockUI();
            $(".right.less.selected").click();                       
            
            $("#wpopup").html(rtn);


            $("#txtFechaInformes").datepicker({
                changeMonth: true,
                changeYear: true
            });

            var fecha = new Date(_credito.FECHA * 1000);
            
            $("#txtFechaInformes").datepicker("option", "dateFormat", 'dd-mm-yy');
            $("#txtFechaInformes").datepicker("setDate", fecha);

            $("#vtab li").on({
                'click' : function(event){

                    if ($(this).hasClass("selected")){
                       $("#vtab li").removeClass("selected");
                    }
                    else{
                        $("#vtab li").removeClass("selected");
                        $(this).addClass("selected");

                        var index = $(this).index();
                        var fecha = $.datepicker.formatDate('@',  $("#txtFechaInformes").datepicker( "getDate" ))/1000;
                        _opt_informe_selected = index;
                        select_option_informe(index, fecha);

                    };
                },
                "mouseenter" : function(){
                    $(this).addClass("over");
                },
                "mouseout" : function(){
                    $(this).removeClass("over");
                }
            });
            $("#vtab li").eq(0).trigger("click");
        }
    });
   
}

function actualizar_informe(){
    var fecha = $.datepicker.formatDate('@',  $("#txtFechaInformes").datepicker( "getDate" ))/1000;
    select_option_informe(_opt_informe_selected , fecha);
}

function select_option_informe(informe_index, fecha){
    console.log(informe_index);
    var chequera = $("#chkIntereses:checked").length;
    switch(informe_index){
        case 0:
            _credito.get_desembolso(_credito.ID, fecha, chequera);
            break;
        case 1:
            
            var planchado = $("#chkPlanchado:checked").length;
            _credito.get_estado_cuotas(_credito.ID, fecha, chequera, planchado);
            break;
        case 2:
            _credito.get_pagos(_credito.ID, fecha, chequera);
            break;
        case 3:
            _credito.get_gastos(_credito.ID, fecha, chequera);
            break;
        case 4:
            _credito.get_tasas(_credito.ID, fecha, chequera);
            break;
        case 5:
            _credito.get_reporte(_credito.ID);
            break;
    }
}


function get_multiple_informes(creditos_id){
    console.dir(creditos_id);
}

_credito.get_desembolso = function(id, fecha, chequera){

    bloq();
    
    $.ajax({
        url : _credito.URL + "/x_get_desembolsos",
        data : {
            credito_id : id,
            fecha : fecha,
            chequera : chequera
        },
        type : "post",
        async : false,
        success : function (rtn){
            $.unblockUI();
            $(".vtabinfo").hide().eq(0).show().html(rtn);
        }
    });
};

_credito.get_estado_cuotas = function(id, fecha, chequera, planchado){

    bloq();
    
    $.ajax({
        url : _credito.URL + "/x_obtener_cuotas",
        data : {
            credito_id : id,
            chequera: chequera,
            planchado: planchado || 0,
            fecha : fecha
        },
        type : "post",
        async : true,
        success : function (rtn){
            $.unblockUI();
            
            $(".vtabinfo").hide().eq(1).show().html(rtn);
            $(".especificaciones").hide();
            
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
            $(".datos .opcion.evolucion").on({
                "mouseenter" : function(){
                    
                },
                "mouseleave" : function(){
                    
                },
                "click" : function(){
                
                    $this_op = $(this);
                    if ($this_op.hasClass("selected")){
                        $this_op.siblings(".evolucion").fadeOut();
                        $this_op.removeClass("selected");
                        $(this).text("( Evolucion + )");
                    }
                    else{
                        var cuota_id = $(this).closest(".datos").data("id");
                        var view = _credito.get_evolucion_cuota(_credito.ID, cuota_id,fecha);
                        
                        $this_op.siblings(".evolucion").html(view);

                        if ($(".datos .opcion.evolucion.selected").length > 0 && false){
                            $this_op.siblings(".evolucion").fadeIn();
                            $this_op.addClass("selected");
                        }
                        else{
                            $this_op.addClass("selected");
                            $this_op.siblings(".evolucion").fadeIn();
                            $(this).text("( Evolucion - )");
                        }
                        
                    }
                }
            });            
        }
    });
};

_credito.get_evolucion_cuota = function(credito_id, id, fecha){
    rtn = "";
    $.ajax({
        url : _credito.URL + "/x_get_evolucion_cuota",
        data : {
            cuota_id : id,
            credito_id : credito_id,
            fecha : fecha
        },
        type : "post",
        async : false,
        success : function(data){
            rtn = data;
        }
    });
    return rtn;
};

_credito.get_pagos = function(id, fecha, chequera){

    bloq();

    $.ajax({
        url : _credito.URL + "/x_get_cobranzas",
        data : {
            credito_id : id,
            fecha : fecha,
            chequera: chequera || 0
        },
        type : "post",
        async : false,
        success : function (rtn){
            $.unblockUI();
            $(".vtabinfo").hide().eq(2).show().html(rtn);
            $(".detalle-pago").show();
        }
    });
};


_credito.get_detalle_pago = function(credito_id, evento_id, $contenedor, chequera){

    //$.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
    $.ajax({
        url : _credito.URL + "/x_obtener_pago",
        data : {
            credito_id : credito_id,
            evento_id : evento_id,
            chequera: chequera || 0
        },
        type : "post",
        async : false,
        success : function (rtn){
            //$.unblockUI();
            $contenedor.fadeIn().html(rtn).addClass("selected");

            
        }
    });
};


function ver_detalle(id_evento,elem){
   
    var $parent = $(elem).closest(".datos.pago");
    
    //var $elem = $(".detalle-pago").hide().eq(index);
    if ($(".detalle-pago-lista.selected").length > 0 && false){
        $(".detalle-pago-lista.selected").removeClass("selected").fadeOut(function(){
            _credito.get_detalle_pago(_credito.ID, id_evento, $parent.find(".detalle-pago-lista")  );
        });
        $(elem).text("( + )");
    }
    else{
        if ($parent.find(".detalle-pago-lista").hasClass("selected")){
            $parent.find(".detalle-pago-lista").removeClass("selected");
            $parent.find(".detalle-pago-lista").fadeOut();
            $(elem).text("( + )");
        }
        else{
            _credito.get_detalle_pago(_credito.ID, id_evento, $parent.find(".detalle-pago-lista")  );
            $(elem).text("( - )");
        }
    }
}

function eliminar_pago(id_evento){
    
    jConfirm("¿Esta seguro de eliminar pagos?", "Eliminar pagos de créditos", function (i) {
        if (i) {
            jConfirm("¿Desea eliminar los pagos siguientes?", "Eliminar pagos de créditos", function (j) {
                bloq();
                var url_d;
                if(j) {
                    url_d =  "/x_eliminar_cobranza_s";
                } else {
                    url_d =  "/x_eliminar_cobranza";
                }
                
                $.ajax({
                    url : _credito.URL + url_d,
                    data : {
                        credito_id : _credito.ID,
                        id_evento : id_evento
                    },
                    type : "post",
                    fail: function() {
                        $.unblockUI();
                        jAlert('Hubo un problema, vuelva a intentar', "Eliminar archivo");
                    },
                    success : function (rtn) {
                        if (rtn == "1") {
                            actualizar_informe();
                            $.unblockUI();
                        } else{
                            $.unblockUI();
                        }
                    }
                }); 
            });
        }
    });
}


_credito.get_gastos = function(credito_id, fecha, chequera){

    bloq();
    
    $.ajax({
        url : _credito.URL + "/x_obtener_gastos",
        data : {
            credito_id : credito_id,
            fecha : fecha,
            chequera: chequera || 0
        },
        type : "post",
        async : false,
        success : function (rtn){
            $.unblockUI();
            $(".vtabinfo").hide().eq(3).show().html(rtn);
            $(".detalle-pago").show();
        }
    });
};

_credito.get_tasas = function(credito_id, fecha, chequera){

    bloq();
    
    $.ajax({
        url : _credito.URL + "/x_obtener_tasas",
        data : {
            credito_id : credito_id,
            fecha : fecha,
            chequera: chequera || 0
        },
        type : "post",
        async : false,
        success : function (rtn){
            $.unblockUI();
            $(".vtabinfo").hide().eq(3).show().html(rtn);
            $(".detalle-pago").show();
        }
    });
};

_credito.get_reporte = function(credito_id, fecha) {
    bloq();
     $.ajax({
        url : _credito.URL + "/x_reporte_credito",
        data : {
            credito_id : credito_id,
            fecha : fecha
        },
        type : "post",
        async : false,
        success : function (rtn){
            $(".vtabinfo").hide().eq(3).show().html(rtn);
            $(".detalle-pago").show();
        }
    });
}

_credito.get_reporte2 = function(credito_id, fecha) {
    bloq();
    
    $.ajax({
        url : _credito.URL + "/x_reporte_credito",
        data : {
            credito_id : credito_id
        },
        type : "post",
        async : false,
        success : function (rtn){
            $(".vtabinfo").hide().eq(3).show().html(rtn);
            $(".detalle-pago").show();
            
             var sourceope = {
                datatype: "json",
                datafields: [
                    { name: 'CONCEPTO', type: 'string' },
                    { name: 'FECHA', type: 'date' },
                    { name: 'VENCIDA', type: 'string' },
                    { name: 'INT_COMPENSATORIO', type: 'number' },
                    { name: 'INT_COMPENSATORIO_IVA', type: 'number' },
                    { name: 'CUOTA', type: 'number' },
                    { name: 'PAGO_MONTO', type: 'number' },
                    { name: 'PAGO_FECHA', type: 'date' }
                ],
                url: _credito.URL + "/x_obtener_reporte",
                type: 'post',
                data:{
                    credito_id : credito_id,
                    fecha : fecha
                },
                async:false,
                deleterow: function (rowid, commit) {
                    commit(true);
                }
            };

            var dataAdapterope = new $.jqx.dataAdapter(sourceope, {
                    loadComplete: function (data) { 
                        _creditos_lista = data;
                    },            
                    formatData: function (data) {
                        data.name_startsWith = $("#searchField").val();
                        return data;
                    }
                });

            $("#jqxgrid-reporte").jqxGrid({
                width: '98%',
                groupable:false,
                //source: source,
                source: dataAdapterope,
                theme: 'energyblue',
                ready: function () {},
                selectionmode: "multiplerows",
                columnsresize: true,
                showtoolbar: false,
                localization: getLocalization(),
                sortable: true,
                filterable: true,
                showfilterrow: false,
                columns: [
                    { text: 'CONCEPTO', datafield: 'CONCEPTO', width: '10%', groupable:false, filterable: false },
                    { text: 'FECHA', datafield: 'FECHA', cellsformat: 'dd/MM/yyyy', width: '10%', hidden : false, filterable : false },
                    { text: 'VENCIDA', datafield: 'VENCIDA', width: '10%', groupable:false, filterable: false },
                    { text: 'INT_COMPENSATORIO', datafield: 'INT_COMPENSATORIO', width: '10%', groupable:false, filterable: false },
                    { text: 'INT_COMPENSATORIO_IVA', datafield: 'INT_COMPENSATORIO_IVA', width: '10%', groupable:false, filterable: false },
                    { text: 'CUOTA', datafield: 'CUOTA', width: '10%', groupable:false, filterable: false },
                    { text: 'PAGO MONTO', datafield: 'PAGO_MONTO', width: '10%', groupable:false, filterable: false },
                    { text: 'PAGO FECHA', datafield: 'PAGO_FECHA', cellsformat: 'dd/MM/yyyy', width: '10%', groupable:false, filterable: false }
                ]
            });
        }
    });
};

function expreport() {
    
    $.ajax({
        url : _credito.URL + "s/x_getexportar",
        type : "post",
        success : function(data){
            $.unblockUI();
            $.fancybox(
                data,
                {
                    'padding'   :  20,
                    'autoScale' :true,
                    'scrolling' : 'no'
                }
            );

            $(".div_exportar .toolbar li").hover(
                function () {
                    $(this).removeClass('li_sel').addClass('li_sel');
                },
                function () {
                    $(this).removeClass('li_sel');
                }
            );

            var url_e = $(".div_exportar ul").data('url_e');
            $('.div_exportar .toolbar li').on('click', function(event){
                event.preventDefault();
                var tipo = $(this).data('acc');
                switch(tipo) {
                    case 'exc':
                        $("#jqxgrid-reporte").jqxGrid('exportdata', 'xls', 'ent_'+fGetNumUnico(), true, null, false, url_e);
                        break;
                    case 'csv':
                        $("#jqxgrid-reporte").jqxGrid('exportdata', 'csv', 'ent_'+fGetNumUnico(), true, null, false, url_e);
                        break;
                    case 'htm':
                        $("#jqxgrid-reporte").jqxGrid('exportdata', 'html','ent_'+fGetNumUnico(), true, null, false, url_e);
                        break;
                    case 'xml':
                        $("#jqxgrid-reporte").jqxGrid('exportdata', 'xml', 'ent_'+fGetNumUnico(), true, null, false, url_e);
                }
            });

        }
    });
}

function exportReporte() {
    $("#reporteCredito table").table2excel({
        exclude: ".noExl",
        name: "Reporte de credito"
      });
}

function bloq() {
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
}

function ver_print(id_evento){
    window.open(_credito.URL + "/x_prints_credito/" + _credito.ID + '/' + id_evento, "credito_cuota", "width=300,height=300");
}