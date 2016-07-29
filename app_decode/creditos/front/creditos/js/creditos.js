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
    
    $("#ffid").chosen();
    
    if (_creditos._permiso_modificacion==="1"){
        console.log("lleg1");
        $("#liModificacion").show();
        $("#liVer").hide();
    }
    else{
        $("#liModificacion").hide();
        $("#liVer").show();
        
    }
    if (_creditos._permiso_alta==="1"){
        console.log("lleg2");
        $("#liAlta").show();
    }
    if (_creditos._permiso_baja==="1"){
        console.log("lleg3");
        $("#liBaja").show();
    }

    
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
            
            $('.tb_exportar,#cmor').hide();
            
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
                case "opciones":
                    get_opciones();
                    break;
                case "nuevo-credito":
                    get_nuevo_credito();
                    break;                    
                case "estructura":
                    get_estructura();
                    break;
                case "eliminar":
                    get_eliminar();
                    break;
                case "listado2":
                    volver_creditos();
                    break;
                case "listado":
                    $('.tb_exportar').show();
                    get_listado();
                    break;
                case "cobros":
                    get_cobros();
                    break;
                case "eventos-multiples":
                    get_multiple_eventos();
                    break;
                case "exportar":
                    $('.tb_exportar').show();
                    exportar();
                    break;
                case "moratorias":
                    get_moratorias();
                    return false;
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
    
    $('.tb_imp').on('click', function(e){
        e.preventDefault();
        $(".form-import").show();
        $.unblockUI();
    });
    
    $('#cmor table tr td').on('click', function(e){
        
        var clase = $(this).parent('tr').attr('class');
        clase = clase.split("-");
        clase = clase[1];
        $('tr.cri').hide();
        $('tr.cri' + clase).show();
        console.log('tr.cri-' + clase);
    });
    
});

$.fn.setTooltipsOnColumnHeader = function () {
    jQuery("#jqxgrid").find('.jqx-grid-column-header').children('div').each(function (index, element) {;
        $(this).attr('title', $(this).find('span').text());
    });
};
/*var  = function (grid, iColumn, text) {
    var thd = jQuery("thead:first", grid[0].grid.hDiv)[0];
    jQuery("tr.ui-jqgrid-labels th:eq(" + iColumn + ")", thd).attr("title", text);
};*/

function get_opciones(){
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes'); 
    
    if (selectedrowindexes.length===0){
        
        $.unblockUI();
        return;
    }
    else{
        var credito_selected = [];
       
        var str_list_creditos = "";
        for(var i = 0 ; i < selectedrowindexes.length ; i++){
            credito_selected.push(parseInt(_creditos_lista[selectedrowindexes[i]].ID_CREDITO) );
            if (i > 0){
                str_list_creditos  += ", ";
            }
            str_list_creditos  +=  _creditos_lista[selectedrowindexes[i]].ID_CREDITO;
        }
        $.unblockUI();
        
        console.dir(credito_selected);
        
        
        $("#wpopup").show();
        load_app("creditos/front/creditosopciones","#wpopup",[str_list_creditos], 
        function(){
            $('#jqxgrid').hide();
            $.unblockUI();
        },
        function(){
            $("#wpopup").show().html("");
            $('#jqxgrid').show();
        },
        function(){
            $("#wpopup").show().html("");
            $('#jqxgrid').show();
        }); 
        

    }
}

function get_listado(){
    location.reload();
}

function get_nuevo_credito(){
    $(".listado-credito").hide();
    load_app("creditos/front/formaltabase","#wpopup",[], 
    function(){
        $('#jqxgrid').hide();
        $.unblockUI();
    },
    function(){

    },
    function(){

    }); 
}


function init_grid(id_usuario,tipo){
    remJGrid2();
    id_usuario = id_usuario || '';
    tipo = tipo || '';
        
    //var iduser = _USER_ROL==1?'': (id_usuario?id_usuario:_USUARIO_SESION_ACTUAL);
    var iduser = _USER_ROL==1?'': (id_usuario?id_usuario:'');
    
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'ID_CREDITO', type: 'integer' },
            { name: 'TOMADORES', type: 'string' },
            { name: 'CUIT', type: 'string' },
            { name: 'OPERATORIA', type: 'string' },
            { name: 'FIDEICOMISO', type: 'string' },
            { name: 'CARPETA', type: 'integer' },
            { name: 'ESTADO', type: 'string' },
            { name: 'DESEMBOLSOS', type: 'float' },
            { name: 'PAGOS', type: 'float' }
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
                        
    var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {

        if (value == 'PRORROGADO') {
            return '<div style="margin:4px;font-weight:bold;">' + value + '</div>';
        } else if (value == 'CADUCADO') {
            return '<div style="margin:4px;color:#ff0000;font-weight:bold;">' + value + '</div>';
        }
        else {
            return '<div style=margin:4px;>' + value + '</div>';
        }
    }

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
        sortable: true,
        filterable: true,
        showfilterrow: true,
        rendertoolbar: function (toolbar) {
            var me = this;
            var container = $("<div style='margin: 5px;'></div>");
            var span = $("<span style='float: left; margin-top: 5px; margin-right: 4px;'>Buscar: </span>");
            var input = $("<input class='jqx-input jqx-widget-content jqx-rc-all' id='searchField' type='text' style='height: 23px; float: left; width: 223px;' />");
            //toolbar.append(container);
            //container.append(span);
            //container.append(input);
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
            { text: 'ID', datafield: 'ID_CREDITO', width: '55px', groupable:false, filterable:true },
            { text: 'TOMADORES', datafield: 'TOMADORES', hidden : false, filterable : true },
            { text: 'CUIT', datafield: 'CUIT', width: '100px', hidden : false, filterable : true },
            { text: 'OPERATORIA', datafield: 'OPERATORIA', width: '170px', hidden : false, filterable : true },
            { text: 'FIDEICOMISO', datafield: 'FIDEICOMISO', width: '170px', hidden : false, filterable : true },
            { text: 'CARPETA', datafield: 'CARPETA', width: '70px', hidden : false, filterable : true },
            { text: 'ESTADO', datafield: 'ESTADO', width: '10%', hidden : false, filterable : true, cellsrenderer: cellsrenderer, cellclassname: 'credEst' },
            { text: 'DESEMBOLSOS', datafield: 'DESEMBOLSOS', width:'70px', hidden : false, filterable : false },
            { text: 'PAGOS', datafield: 'PAGOS', width: '70px', hidden : false, filterable : false }
        ]
    });
    
    $("#jqxgrid").setTooltipsOnColumnHeader();
}

function reporte1() {
    remJGrid2();
    $("#wpopup").html('');
    
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'DEUDOR', type: 'string' },
            { name: 'CUIT', type: 'string' },
            { name: 'ID', type: 'integer' },
            { name: 'DIRECCION', type: 'string' },
            { name: 'PROVINCIA', type: 'string' },
            { name: 'LOCALIDAD', type: 'string' },
            { name: 'FECHA_DESEMB', type: 'date' },
            { name: 'MONTO_CREDITO', type: 'number' },
            { name: 'SITUACION', type: 'string' },
            { name: 'SALDO_CAPITAL', type: 'number' },
            { name: 'COBRANZAS', type: 'number' },
            { name: 'CANTIDAD_CUOTAS_MORAS', type: 'number' },
            { name: 'MONTO_VENCIDO', type: 'number' },
            { name: 'MONTO_MORA', type: 'number' },
            { name: 'PORCENTAJE_MORA', type: 'string' },
            { name: 'ESTADO', type: 'string' }
        ],
        url: 'creditos/front/creditos/resumen_moratorias/',
        data:{
            ffid: $("#ffid").val(),
            fdesde: $("#fdesde").val(),
            fhasta: $("#fhasta").val()
        },
        type:'post',
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
        groupable:false,
        //source: source,
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function (data) {},
        selectionmode: "multiplerows",
        columnsresize: true,
        showtoolbar: false,
        localization: getLocalization(),
        sortable: true,
        filterable: true,
        enabletooltips: true,
        showfilterrow: false,
        columns: [
            { text: 'DEUDOR', datafield: 'DEUDOR', width: '10%', groupable:false, filterable: false },
            { text: 'CREDITO', datafield: 'ID', width: '5%', hidden : false, filterable : false },
            { text: 'CUIT', datafield: 'CUIT', width: '10%', groupable:false, filterable: false },
            { text: 'DIRECCION', datafield: 'DIRECCION', width: '10%', hidden : false, filterable : false },
            { text: 'PROVINCIA', datafield: 'PROVINCIA', width: '10%', hidden : false, filterable : false },
            { text: 'LOCALIDAD', datafield: 'LOCALIDAD', width: '10%', hidden : false, filterable : false },
            { text: 'FECHA DEL CONT. Y DESEMB.', datafield: 'FECHA_DESEMB', cellsformat: 'dd/MM/yyyy', width: '10%', hidden : false, filterable : false },
            { text: 'MONTO DEL CREDITO', datafield: 'MONTO_CREDITO', width: '10%', hidden : false, filterable : false },
            { text: 'SITUACION', datafield: 'SITUACION', width: '5%', hidden : false, filterable : false },
            { text: 'SALDO CAPITAL', datafield: 'SALDO_CAPITAL', width: '8%', hidden : false, filterable : false },
            { text: 'COBRANZAS', datafield: 'COBRANZAS', width: '10%', hidden : false, filterable : false },
            { text: 'CANTIDAD DE CUOTAS EN MORAS', datafield: 'CANTIDAD_CUOTAS_MORAS', width: '5%', hidden : false, filterable : false },
            { text: 'MONTO VENCIDO', datafield: 'MONTO_VENCIDO', width: '10%', hidden : false, filterable : false },
            { text: 'MONTO MORA', datafield: 'MONTO_MORA', width: '10%', hidden : false, filterable : false },
            { text: 'PORCENTAJE DE MORA', datafield: 'PORCENTAJE_MORA', width: '10%', hidden : false, filterable : false },
            { text: 'ESTADO', datafield: 'ESTADO', width: '5%', hidden : false, filterable : false }
        ]
    });
    
    $("#jqxgrid").setTooltipsOnColumnHeader();
    
    $('#jqxgrid').show();
    $("#jqxgrid").before('<div id="jqxgrid2"></div>');
    reporte1b();
}


function reporte1b() {
    
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'TOTAL_CREDITOS', type: 'string' },
            { name: 'MONTOS_VENCIDOS', type: 'string' },
            { name: 'TOTAL_CREDITOS_MORA', type: 'string' },
            { name: 'CREDITOS_EFICIENCIA', type: 'string' }
        ],
        url: 'creditos/front/creditos/fn_resumen_moratorias/',
        data:{
            ffid: $("#ffid").val(),
            fdesde: $("#fdesde").val(),
            fhasta: $("#fhasta").val()
        },
        type:'post',
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
			
    $("#jqxgrid2").jqxGrid(
    {
        width: '98%',
        groupable:false,
        //source: source,
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function (data) {},
        selectionmode: "multiplerows",
        columnsresize: false,
        showtoolbar: false,
        localization: getLocalization(),
        sortable: true,
        filterable: false,
        showfilterrow: false,
        columns: [
            { text: 'Cantidad de créditos otorgados', datafield: 'TOTAL_CREDITOS', width: '25%', groupable:false, filterable: false },
            { text: 'Monto de Créditos Vencidos', datafield: 'MONTOS_VENCIDOS', width: '25%', groupable:false, filterable: false },
            { text: 'Cantidad de Créditos en Mora', datafield: 'TOTAL_CREDITOS_MORA', width: '25%', groupable:false, filterable: false },
            { text: 'Eficiencia por caso', datafield: 'CREDITOS_EFICIENCIA', width: '25%', groupable:false, filterable: false }
        ]
    });
    
    $("#jqxgrid2").setTooltipsOnColumnHeader();
    $("#jqxgrid2").show();
    
}



function reporte2() {
    remJGrid2();
    $("#wpopup").html('');
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'DEUDOR', type: 'string' },
            { name: 'CUIT', type: 'string' },
            { name: 'ID', type: 'integer' },
            { name: 'FIDEICOMISO', type: 'string' },
            { name: 'CUOTA_VENCE', type: 'number' },
            { name: 'FECHA_VENCE', type: 'date' },
            { name: 'MONTO_CUOTA', type: 'number' },
            { name: 'SALDO_CAPITAL_MORA', type: 'number' },
            { name: 'ESTADO', type: 'string' },
            { name: 'FECHA_PAGO', type: 'date' },
            { name: 'DIRECCION', type: 'string' },
            { name: 'PROVINCIA', type: 'string' },
            { name: 'LOCALIDAD', type: 'string' }
        ],
        url: 'creditos/front/creditos/resumen_moratorias2/',
        data:{
            ffid: $("#ffid").val(),
            fdesde: $("#fdesde").val(),
            fhasta: $("#fhasta").val()
        },
        type:'post',
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
        groupable:false,
        //source: source,
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function (data) {},
        selectionmode: "multiplerows",
        columnsresize: true,
        showtoolbar: false,
        localization: getLocalization(),
        sortable: true,
        filterable: true,
        showfilterrow: false,
        columns: [
            { text: 'DEUDOR', datafield: 'DEUDOR', width: '10%', groupable:false, filterable: false },
            { text: 'CUIT', datafield: 'CUIT', width: '10%', groupable:false, filterable: false },
            { text: 'CREDITO', datafield: 'ID', width: '5%', hidden : false, filterable : false },
            { text: 'FIDEICOMISO', datafield: 'FIDEICOMISO', width: '10%', hidden : false, filterable : false },
            { text: 'Nº CUOTA QUE VENCE EN EL MES', datafield: 'CUOTA_VENCE', width: '10%', hidden : false, filterable : false },
            { text: 'FECHA VTO DE LA CUOTA DEL MES', datafield: 'FECHA_VENCE', cellsformat: 'dd/MM/yyyy', width: '10%', hidden : false, filterable : false },
            { text: 'MONTO DE LA CUOTA', datafield: 'MONTO_CUOTA', width: '10%', hidden : false, filterable : false },
            { text: 'SALDO CAPITAL EN MORA', datafield: 'SALDO_CAPITAL_MORA', width: '10%', hidden : false, filterable : false },
            { text: 'ESTADO DEL CASO', datafield: 'ESTADO', width: '10%', hidden : false, filterable : false },
            { text: 'FECHA DE ULTIMPO PAGO', datafield: 'FECHA_PAGO', cellsformat: 'dd/MM/yyyy', width: '10%', hidden : false, filterable : false },,
            { text: 'DIRECCION', datafield: 'DIRECCION', width: '10%', hidden : false, filterable : false },
            { text: 'PROVINCIA', datafield: 'PROVINCIA', width: '10%', hidden : false, filterable : false },
            { text: 'LOCALIDAD', datafield: 'LOCALIDAD', width: '10%', hidden : false, filterable : false }
        ]
    });
    
    $("#jqxgrid").setTooltipsOnColumnHeader();
    $('#jqxgrid').show();
}

function reporte3() {
    remJGrid2();
    $("#wpopup").html('');
    $.ajax({
        url : _creditos.URL + "/resumen_moratorias3/",
        data : {
            ffid: $("#ffid").val(),
            fdesde: $("#fdesde").val(),
            fhasta: $("#fhasta").val()
        },
        type : "post",
        success : function(rtn){
            $("#jqxgrid").hide();
            $("#wpopup").html(rtn).show();
        }
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

function reporte4() {
    remJGrid2();
    $("#wpopup").html('');
    
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'DEUDOR', type: 'string' },
            { name: 'CUIT', type: 'string' },
            { name: 'ID', type: 'integer' },
            { name: 'DIRECCION', type: 'string' },
            { name: 'COD_POSTAL', type: 'string' },
            { name: 'PROVINCIA', type: 'string' },
            { name: 'LOCALIDAD', type: 'string' },
            { name: 'FIDEICOMISO', type: 'string' },
            { name: 'MONTO_CREDITO', type: 'number' },
            { name: 'SALDO_CAPITAL', type: 'number' },
            { name: 'SALDO_CREDITO', type: 'number' },
            { name: 'FECHA_1VENC_IMP', type: 'date' },
            { name: 'DIAS_MORAS', type: 'number' },
            { name: 'CUOTAS_IMPAGAS', type: 'number' }
        ],
        url: 'creditos/front/creditos/reporte4/',
        data:{
            ffid: $("#ffid").val(),
            fdesde: $("#fdesde").val(),
            fhasta: $("#fhasta").val()
        },
        type:'post',
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
        groupable:false,
        //source: source,
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function (data) {},
        selectionmode: "multiplerows",
        columnsresize: true,
        showtoolbar: false,
        localization: getLocalization(),
        sortable: true,
        filterable: true,
        showfilterrow: false,
        columns: [
            { text: 'DEUDOR', datafield: 'DEUDOR', width: '10%', groupable:false, filterable: false },
            { text: 'CUIT', datafield: 'CUIT', width: '10%', groupable:false, filterable: false },
            { text: 'CREDITO', datafield: 'ID', width: '5%', hidden : false, filterable : false },
            { text: 'DIRECCION', datafield: 'DIRECCION', width: '10%', hidden : false, filterable : false },
            { text: 'C.P.', datafield: 'COD_POSTAL', width: '5%', hidden : false, filterable : false },
            { text: 'PROVINCIA', datafield: 'PROVINCIA', width: '10%', hidden : false, filterable : false },
            { text: 'LOCALIDAD', datafield: 'LOCALIDAD', width: '10%', hidden : false, filterable : false },
            { text: 'FIDEICOMISO', datafield: 'FIDEICOMISO', width: '10%', hidden : false, filterable : false },
            { text: 'MONTO DEL CREDITO', datafield: 'MONTO_CREDITO', width: '10%', hidden : false, filterable : false },
            { text: 'SALDO CAPITAL', datafield: 'SALDO_CAPITAL', width: '8%', hidden : false, filterable : false },
            { text: 'SALDO CREDITO', datafield: 'SALDO_CREDITO', width: '8%', hidden : false, filterable : false },
            { text: 'FECHA VENC. 1ER CUOTA IMPAGA', datafield: 'FECHA_1VENC_IMP', cellsformat: 'dd/MM/yyyy', width: '10%', hidden : false, filterable : false },
            { text: 'DIAS MORAS', datafield: 'DIAS_MORAS', width: '10%', hidden : false, filterable : false },
            { text: 'CUOTAS IMPAGAS', datafield: 'CUOTAS_IMPAGAS', width: '10%', hidden : false, filterable : false }
        ]
    });
    
    $("#jqxgrid").setTooltipsOnColumnHeader();
    $('#jqxgrid').show();
}

function get_eliminar(){
    
    if (_creditos._permiso_baja==0) {
        jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
            $.unblockUI();
        });
        return false;
    }
    
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes'); 
    if (selectedrowindexes.length===0){
        jAlert("No ha seleccionado ningún crédito para eliminar","Eliminar Crédito", function(){
            $.unblockUI();
            return;
        });
    }
    else{
        
        var credito_selected = [];
        $('#contenttablejqxgrid div > .jqx-grid-cell-selected:first-child').each(function() {
            credito_selected.push($(this).text());
        });
        
        var str_list_creditos = credito_selected.join(", ");
        
        console.log(str_list_creditos);
        $.unblockUI();
        
        console.dir(credito_selected);
        
        jConfirm("Esta seguro de querer eliminar el credito:" + str_list_creditos+ "?","Eliminación de Crédito", function(ret){
           if (ret) {
                $.ajax({
                    url : _creditos.URL + "/x_eliminar_credito",
                    data : {
                        creditos : credito_selected
                    },
                    type : "post",
                    success : function(rtn){
                        console.dir(rtn);
                        
                        jAlert("El credito "+str_list_creditos+" ha sido eliminado","Acción Completa", function(){
                            get_listado();
                        });
                        return;
                    }
                });
           }
        });
    }
}


function get_cobros(){


        $(".listado-credito").hide();
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
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes'); 
    
    if (selectedrowindexes.length === 1){
        var estado = $('.jqx-grid-cell-selected.credEst').text();
        if (estado == 'CADUCADO') {
            jConfirm("Desea continuar?","Crédito Caducado", function(r){
                if (r) {
                    _get_credito(edit);
                }
            });
        } if (estado == 'PRORROGADO') {
            jConfirm("Desea continuar?","Crédito Prorrogado", function(r){
                if (r) {
                    _get_credito(edit);
                }
            });
        } else {
            _get_credito(edit);
        }
        
    } else {
        jAlert("Debe seleccionar solo un crédito","Editar Créditos", function(){
            $.unblockUI();
            return;
        });
    }
     
}

function _get_credito(edit) {
    var urlbase = $("#liVer").data("loc");
    var credito_selected = $('.jqx-grid-cell-selected :first').html();
    var url = urlbase+"/init/"+credito_selected+"/"+edit;
    location.href=url;
    return false;
}

function get_formalta(){
    location.reload();
}


function get_multiple_informes(creditos_id){
    console.dir(creditos_id);
}

$(document).ready(function(){
   $('.form-import form input.btnImp').on({
       "click" : function(){
           $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
       }
   });
});

function exportar() {
    $.ajax({
        url : _creditos.URL + "/x_getexportar",
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
                        $("#jqxgrid").jqxGrid('exportdata', 'xls', 'ent_'+fGetNumUnico(), true, null, false, url_e);
                        if ($("#jqxgrid2").length>0) {
                            setTimeout(function () {
                                $("#jqxgrid2").jqxGrid('exportdata', 'xls', 'ent_'+fGetNumUnico(), true, null, false, url_e);
                                $("#jqxgrid2").show();
                            }, 1500);
                        }
                        break;
                    case 'csv':
                        $("#jqxgrid").jqxGrid('exportdata', 'csv', 'ent_'+fGetNumUnico(), true, null, false, url_e);
                        break;
                    case 'htm':
                        $("#jqxgrid").jqxGrid('exportdata', 'html','ent_'+fGetNumUnico(), true, null, false, url_e);
                        break;
                    case 'xml':
                        $("#jqxgrid").jqxGrid('exportdata', 'xml', 'ent_'+fGetNumUnico(), true, null, false, url_e);
                }
            });

        }
    });
}


function get_moratorias() {
    $('#wpopup').hide();
    $('#liModificacion,#liOpcion,.tb_todas,.tb_del').hide();
    $('.tb_exportar,#mn-reportes').show();
    
    $("#fdesde,#fhasta").datepicker({
        changeMonth: true,
        changeYear: true
    });
    $("#fdesde,#fhasta").datepicker("option", "dateFormat", 'dd-mm-yy');

    $.unblockUI();
}

function volver_creditos() {
    location.href=$(".tb_lis").data("loc");
}

function remJGrid2() {
    if ($("#jqxgrid2").length > 0) {
        $("#jqxgrid2").remove();
    }
}

function exportReporteCobranza() {

    $("#reporteCobranza table").table2excel({
        exclude: ".noExl",
        name: "Reporte Cobranza"
      });
}

function verificar_imp_pagos() {
    $.ajax({
        async:false,
        url : "creditos/front/cuotas/x_verif_proc_imp_pago",
        type : "post",
        success : function(rtn){
            console.log(rtn);
        }
    });
}