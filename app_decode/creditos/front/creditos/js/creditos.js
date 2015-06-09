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
                case "listado":
                    get_listado();
                    break;
                case "cobros":
                    get_cobros();
                    break;
                case "eventos-multiples":
                    get_multiple_eventos();
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
    
    $('#content').on('click', function(e){
        $("#msgs").fadeOut(500);
    });
});

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
    id_usuario = id_usuario || '';
    tipo = tipo || '';
        
    //var iduser = _USER_ROL==1?'': (id_usuario?id_usuario:_USUARIO_SESION_ACTUAL);
    var iduser = _USER_ROL==1?'': (id_usuario?id_usuario:'');
    
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'ID_CREDITO', type: 'string' },
            { name: 'TOMADORES', type: 'string' },
            { name: 'CUIT', type: 'string' },
            { name: 'OPERATORIA', type: 'string' },
            { name: 'FIDEICOMISO', type: 'string' },
            { name: 'CARPETA', type: 'string' },
            { name: 'ESTADO', type: 'string' }
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
            { text: 'ID', datafield: 'ID_CREDITO', width: '6%', groupable:false, filterable:true },
            { text: 'TOMADORES', datafield: 'TOMADORES', width: '20%', hidden : false, filterable : true },
            { text: 'CUIT', datafield: 'CUIT', width: '10%', hidden : false, filterable : true },
            { text: 'OPERATORIA', datafield: 'OPERATORIA', width: '20%', hidden : false, filterable : true },
            { text: 'FIDEICOMISO', datafield: 'FIDEICOMISO', width: '20%', hidden : false, filterable : true },
            { text: 'CARPETA', datafield: 'CARPETA', width: '10%', hidden : false, filterable : true },
            { text: 'ESTADO', datafield: 'ESTADO', width: '10%', hidden : false, filterable : true }
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
    if (selectedrowindexes.length===0){
        $.unblockUI();
        return;
    }
    else{
        
        var credito_selected = [];
        //_creditos_lista[selectedrowindexes[0]
        //console.dir(_creditos_lista);
        var str_list_creditos = "";
        for(var i = 0 ; i < selectedrowindexes.length ; i++){
            credito_selected.push(parseInt(_creditos_lista[selectedrowindexes[i]].ID_CREDITO) );
            if (i > 0){
                str_list_creditos  += ", ";
            }
            str_list_creditos  +=  _creditos_lista[selectedrowindexes[i]].ID_CREDITO;
        }
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
    var urlbase = $("#liVer").data("loc");
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