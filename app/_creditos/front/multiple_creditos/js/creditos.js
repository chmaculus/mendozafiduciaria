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
/*
function tooltip_evento(){
    
    $(".tb_cli").jqxTooltip({ content: '<b>Clientes</b><br/>Ir a clientes', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_ents").jqxTooltip({ content: '<b>Entidades</b><br/>Ir a entidades', position: 'bottom', name: 'movieTooltip', theme: theme });
   
    $(".tb_todas").jqxTooltip({ content: '<b>Todas las Carpetas</b>', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_miscar").jqxTooltip({ content: '<b>Mis Carpetas</b><br/>Son las Carpetas en las que tengo pendiente alguna acción', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_cart").jqxTooltip({ content: '<b>En Cartera</b><br/>Son las Carpetas que tengo a mi cargo', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_pend").jqxTooltip({ content: '<b>Pendientes</b><br/>Son las Carpetas que me las enviaron pero aun no las acepto', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_autor").jqxTooltip({ content: '<b>Por Autorizar</b><br/>Son las Carpetas que me las enviaron para autorizarlas', position: 'bottom', name: 'movieTooltip', theme: theme });
   

}

*/
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
                case "ver":
                    get_credito(0);
                    break;
                case "editar":
                    get_credito(1);
                    break;
                case "eventos":
                    get_eventos();
                    break;
                case "estructura":
                    get_estructura();
                    break;
                case "eliminar":
                    get_eliminar();
                    break;
                case "listado":
                    get_listado();
                    break;
                case "cobros":
                    get_cobros();
                    break;
            }
    });

    init_grid();
   
    //$(".tb_todas").addClass("menu_sel");
    
    $('.tb_todas').on('click', function(e){
        e.preventDefault();
        init_grid();
    });
    
    $('.tb_miscar').on('click', function(e){
        e.preventDefault();
        init_grid(_USUARIO_SESION_ACTUAL);
    });
    
    $('.tb_pend').on('click', function(e){
        e.preventDefault();
        init_grid(_USUARIO_SESION_ACTUAL,'pendiente');
    });
});



function init_grid(id_usuario,tipo){
    id_usuario = id_usuario || '';
    tipo = tipo || '';
        
    //var iduser = _USER_ROL==1?'': (id_usuario?id_usuario:_USUARIO_SESION_ACTUAL);
    var iduser = _USER_ROL==1?'': (id_usuario?id_usuario:'');
    
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'ID_CREDITO' },
            { name: 'TOMADORES', type: 'string' },
            { name: 'OPERATORIA', type: 'string' },
            { name: 'FIDEICOMISO', type: 'string' },
            { name: 'CARPETA', type: 'string' }
        ],
        url: 'general/extends/extra/creditos.php',
        data:{
            accion  :   "getCreditos",
            iduser  :   iduser,
            tipo    :   tipo
        },
        async:false,
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };
    
    var dataAdapterope = new $.jqx.dataAdapter(sourceope,
        {
            loadComplete: function (data) { 
                _creditos_lista = data;
            },            
            formatData: function (data) {

                data.name_startsWith = $("#searchField").val();
                return data;
            }
        }
    );
			
    $("#jqxgrid").jqxGrid(
    {
        width: '98%',
        groupable:true,
        //source: source,
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function (data) {
    
       //     $("#jqxgrid").jqxGrid('hidecolumn', 'ID');
            //$("#jqxgrid").jqxGrid('autoresizecolumns');
        },
        selectionmode: "multiplerows",
        columnsresize: true,
        showtoolbar: true,
        localization: getLocalization(),
        rendertoolbar: function (toolbar) {
            var me = this;
            var container = $("<div style='margin: 5px;'></div>");
            var span = $("<span style='float: left; margin-top: 5px; margin-right: 4px;'>Buscar: </span>");
            var input = $("<input class='jqx-input jqx-widget-content jqx-rc-all' id='searchField' type='text' style='height: 23px; float: left; width: 223px;' />");
            toolbar.append(container);
            container.append(span);
            container.append(input);
            if (theme != "") {
                input.addClass('jqx-widget-content-' + theme);
                input.addClass('jqx-rc-all-' + theme);
            }

            input.on('keydown', function (event) {
                if (input.val().length >= 2) {
                    if (me.timer) clearTimeout(me.timer);
                    me.timer = setTimeout(function () {
                        dataAdapterope.dataBind();
                    }, 300);
                }
            });
        },
        columns: [
            { text: 'ID', datafield: 'ID_CREDITO', width: '6%', groupable:false, filterable:false , pinned: true},
            { text: 'TOMADORES', datafield: 'TOMADORES', width: '20%', hidden : false, filterable : true },
            { text: 'OPERATORIA', datafield: 'OPERATORIA', width: '20%', hidden : false, filterable : true },
            { text: 'FIDEICOMISO', datafield: 'FIDEICOMISO', width: '20%', hidden : false, filterable : true },
            { text: 'CARPETA', datafield: 'CARPETA', width: '20%', hidden : false, filterable : true }
        ]
    });
    
    
}

function get_eventos(){
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes'); 
    
    if (selectedrowindexes.length==0){
        $.unblockUI();
        return;
    }
    
    if(selectedrowindexes.length==1){
        var credito_selected = _creditos_lista[selectedrowindexes[0] ];
        _credito_selected = credito_selected;

        load_app("creditos/front/cuotas","#wpopup",[credito_selected.ID_CREDITO], 
        function(){
            $('#jqxgrid').hide();
            $.unblockUI();
        },
        function(){

        },
        function(){

        });
    }
    else{
        var id_creditos = [];
        for(var i = 0 ; i < selectedrowindexes.length ; i++){
            id_creditos.push(_creditos_lista [selectedrowindexes[i]] );
        }
        get_multiple_eventos(id_creditos);
    }
}


function get_eliminar(){
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes'); 
    if (selectedrowindexes.length!==1){
        $.unblockUI();
        return;
    }
    else{
        var credito_selected = _creditos_lista[selectedrowindexes[0] ];
        
        $.unblockUI();
        jConfirm("Esta seguro de querer eliminar el credito:" + credito_selected.ID_CREDITO + "?","Eliminación de Crédito", function(ret){
           if (ret) {
                $.ajax({
                    url : _creditos.URL + "/x_eliminar_credito",
                    data : {
                        credito_id : credito_selected.ID_CREDITO
                    },
                    type : "post",
                    success : function(rtn){
                        
                        jAlert("El credito "+credito_selected.ID_CREDITO+" ha sido eliminado","Acción Completa", function(){
                            get_listado();
                        });
                        return;
                    }
                });
           }
        });
    }
}
function get_estructura(){
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes'); 
    if (selectedrowindexes.length==0){
        $.unblockUI();
        return;
    }

    if(selectedrowindexes.length==1){
        var credito_selected = _creditos_lista[selectedrowindexes[0] ];
        _credito_selected = credito_selected;

        load_app("creditos/front/estructura","#wpopup",[credito_selected.ID_CREDITO], 
        function(){
            $('#jqxgrid').hide();
            $.unblockUI();
        },
        function(){

        },
        function(){

        });
    }
    else{
        /*var id_creditos = [];
        for(var i = 0 ; i < selectedrowindexes.length ; i++){
            id_creditos.push(_creditos_lista [selectedrowindexes[i]] );
        }
        get_multiple_eventos(id_creditos);*/
    }
}

function get_cobros(){
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes'); 

    if(selectedrowindexes.length==1){
        var credito_selected = _creditos_lista[selectedrowindexes[0] ];
        _credito_selected = credito_selected;

        load_app("creditos/front/cobros","#wpopup",[credito_selected.ID_CREDITO], 
        function(){
            $('#jqxgrid').hide();
            $.unblockUI();
        },
        function(){

        },
        function(){

        });
    }
    else{
        var credito_selected = _creditos_lista[selectedrowindexes[0] ];
        _credito_selected = credito_selected;

        load_app("creditos/front/cobros","#wpopup",[0], 
        function(){
            $('#jqxgrid').hide();
            $.unblockUI();
        },
        function(){

        },
        function(){

        });
    }
}

function get_multiple_eventos(creditos){

    
    var creditos_id = [];
    for(var i = 0 ; i < creditos.length ; i++){
        creditos_id.push(parseInt(creditos[i]['ID_CREDITO']) );
    }

    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindex'); 

    var credito_selected = _creditos_lista[selectedrowindexes ];
    _credito_selected = credito_selected;
    
    load_app("creditos/front/cuotas_m","#wpopup",[creditos_id], 
    function(){
        $('#jqxgrid').hide();
        $.unblockUI();
    },
    function(){

    },
    function(){

    });
}

function get_inicio(){
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindex'); 

    var credito_selected = _creditos_lista[selectedrowindexes ];
    _credito_selected = credito_selected;
    
    load_app("creditos/front/formalta","#wpopup",[credito_selected.ID_CREDITO], 
    function(){
        $('#jqxgrid').hide();
        $.unblockUI();
    },
    function(){

    },
    function(){

    }); 
}



function get_credito(edit){
    $.unblockUI();
    var urlbase = $("#ver-option").data("loc");
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes'); 
    
    if (selectedrowindexes.length === 1){
        var credito_selected = _creditos_lista[selectedrowindexes[0] ];
        var url = urlbase+"/init/"+credito_selected.ID_CREDITO+"/"+edit;
        location.href=url;
        return false;
        
    }
     
}

function get_formalta(){
    location.reload();
}


function get_informes(){
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes'); 
    if (selectedrowindexes.length==0){
        $.unblockUI();
        return;
    }
    
    if (selectedrowindexes.length == 1){
        var credito_selected = _creditos_lista[selectedrowindexes[0] ];
        _credito_selected = credito_selected;
        $.ajax({
            url : _creditos.URL + "/x_get_informes",
            data : {
                credito_id : credito_selected.ID_CREDITO
            },
            type : "post",
            async : false,
            success : function (rtn){
                $.unblockUI();
                $("#jqxgrid").hide();
                $("#wpopup").html(rtn);


                $("#txtFechaInformes").datepicker({
                    changeMonth: true,
                    changeYear: true
                });

                var fecha = new Date(_creditos.FECHA * 1000);
                
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
    else{
        var id_creditos = [];
        for(var i = 0 ; i < selectedrowindexes.length ; i++){
            id_creditos.push(_creditos_lista [selectedrowindexes[i]] );
        }
        get_multiple_informes(id_creditos);
    }
}

function actualizar_informe(){
    var fecha = $.datepicker.formatDate('@',  $("#txtFechaInformes").datepicker( "getDate" ))/1000;
    select_option_informe(_opt_informe_selected , fecha);
}

function select_option_informe(informe_index, fecha){
    console.log(informe_index);
    switch(informe_index){
        case 0:
            _creditos.get_desembolso(_credito_selected.ID_CREDITO, fecha);
            break;
        case 1:
            var chequera = $("#chkIntereses:checked").length;
            _creditos.get_estado_cuotas(_credito_selected.ID_CREDITO, fecha, chequera);
            break;
        case 2:
            _creditos.get_pagos(_credito_selected.ID_CREDITO, fecha);
            break;
        case 3:
            _creditos.get_gastos(_credito_selected.ID_CREDITO, fecha);
            break;
        case 4:
            _creditos.get_tasas(_credito_selected.ID_CREDITO, fecha);
            break;
    }
}


function get_multiple_informes(creditos_id){
    console.dir(creditos_id);
}

_creditos.get_desembolso = function(id){

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
    $.ajax({
        url : _creditos.URL + "/x_get_desembolsos",
        data : {
            credito_id : id
        },
        type : "post",
        async : false,
        success : function (rtn){
            $.unblockUI();
            $(".vtabinfo").hide().eq(0).show().html(rtn);
        }
    });
};

_creditos.get_estado_cuotas = function(id, fecha, chequera){

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
    $.ajax({
        url : _creditos.URL + "/x_obtener_cuotas",
        data : {
            credito_id : id,
            chequera: chequera,
            fecha : fecha
        },
        type : "post",
        async : false,
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
                        var view = _creditos.get_evolucion_cuota(_credito_selected.ID_CREDITO, cuota_id,fecha);
                        
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

_creditos.get_evolucion_cuota = function(credito_id, id, fecha){
    rtn = "";
    $.ajax({
        url : _creditos.URL + "/x_get_evolucion_cuota",
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

_creditos.get_pagos = function(id, fecha){

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
    $.ajax({
        url : _creditos.URL + "/x_get_cobranzas",
        data : {
            credito_id : id,
            fecha : fecha
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


_creditos.get_detalle_pago = function(credito_id, evento_id, $contenedor){

    //$.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
    $.ajax({
        url : _creditos.URL + "/x_obtener_pago",
        data : {
            credito_id : credito_id,
            evento_id : evento_id
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
            _creditos.get_detalle_pago(_credito_selected.ID_CREDITO, id_evento, $parent.find(".detalle-pago-lista")  );
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
            _creditos.get_detalle_pago(_credito_selected.ID_CREDITO, id_evento, $parent.find(".detalle-pago-lista")  );
            $(elem).text("( - )");
        }
        
        
        
    }
}


_creditos.get_gastos = function(credito_id, fecha){

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
    $.ajax({
        url : _creditos.URL + "/x_obtener_gastos",
        data : {
            credito_id : credito_id,
            fecha : fecha
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

_creditos.get_tasas = function(credito_id, fecha){

    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    
    $.ajax({
        url : _creditos.URL + "/x_obtener_tasas",
        data : {
            credito_id : credito_id,
            fecha : fecha
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