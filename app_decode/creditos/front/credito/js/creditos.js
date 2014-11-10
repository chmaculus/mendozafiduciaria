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
            $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
            
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
    }
}


function get_multiple_informes(creditos_id){
    console.dir(creditos_id);
}

_credito.get_desembolso = function(id, fecha, chequera){

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
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

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
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

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });

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


_credito.get_gastos = function(credito_id, fecha, chequera){

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
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

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
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