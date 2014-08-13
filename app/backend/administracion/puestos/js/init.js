var mydata;
var id_edit;
var working = false;
var _array_entidades = {};
var _array_chk = {};
var semmilla;

$(document).ready(function(){
    semmilla = fGetNumUnico();
    mydata = '';
    
    $(".toolbar li").hover(
        function(){
            $(this).removeClass('li_sel').addClass('li_sel');
        },
        function(){
            $(this).removeClass('li_sel');
        }
    );
        
    $(".toolbar li").click(function(e){
            e.preventDefault();
            var top = $(this).data('top');
            var obj = [];
            $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
            
            if(top =='add'){
                if (_permiso_alta==0){

                    jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                // add
                $.ajax({
                    url : _puestos.URL + "/x_getform_addentidad",
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        
                        $(".chzn-select").chosen(); 
                        $("#btnBorrar").hide();
                                               
                        $('#send').on('click', function(event) {
                            event.preventDefault();
                            
                            var id_area = $("#id_area").val();
                            var denominacion = $("#denominacion").val();
                            
                            var_ins = {
                                "DENOMINACION":denominacion,
                                "ID_AREA":id_area
                            }
                            var id = $("#idh").val();

                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                            
                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                var_ins:var_ins
                            }

                            $.ajax({
                                url : _puestos.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    
                                    if(data.result==-1){
                                        $("#username").css('border','1px solid red');
                                        $('#username').keyup(function() {
                                            $("#username").css('border','1px solid #e3e3e3');
                                        });
                                        jAlert('Este Nombre de Usuario no está disponible.', $.ucwords(_etiqueta_modulo),function(){
                                            $("#username").select();
                                        });
                                    }else if(data.result==-2){
                                        $("#email").css('border','1px solid red');
                                        $('#email').keyup(function() {
                                            $("#email").css('border','1px solid #e3e3e3');
                                        });
                                        jAlert('Este Email ya existe en la Base de Datos.', $.ucwords(_etiqueta_modulo),function(){
                                            $("#email").select();
                                        });
                                    }else if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $('#btnClear').trigger('click');
                                            $("#jqxgrid").show();
                                            $("#wpopup").html('');
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                        });
                                    }else{
                                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                        });
                                    }
                                }
                            });
                        });
                        
                                                
                        $('#btnClear').on('click', function(e) {
                            $("#customForm").validationEngine('hideAll');
                            e.preventDefault();
                            
                            limpiar_inputs();
                            
                        });
                        
                       agregarEventoval();
                        
                        
                    }
                });
                
            }else if(top=='edi'){
                var ver = $(this).data('ver');
                ver || ( ver = '-1' );
                
                if (ver!=-1){
                    if (_permiso_ver==0 && ver ){
                        jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                            $.unblockUI();
                            switchBarra();
                        });
                        return false;
                    }
                }else{
                    if (_permiso_modificacion==0){
                        jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                            $.unblockUI();
                            switchBarra();
                        });
                        return false;
                    }
                }
                mydata = '';
                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                               
                if ( mydata==null ){
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                
                // edit entidades
                $.ajax({
                    url : _puestos.URL + "/x_getform_addentidad",
                    data : {
                        obj:mydata.ID
                    },
                    async:false,
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        
                        $(".chzn-select").chosen();
                        $("#btnBorrar").hide();
                        
                        $("#id_area").val(_array_obj.ID_AREA).trigger("chosen:updated");
                        
                        $('#btnClear').on('click', function(e) {
                            $("#customForm").validationEngine('hideAll');
                            e.preventDefault();
                            limpiar_inputs();
                        });
                        
                        $('#send').on('click', function(event){
                            event.preventDefault();
                            var id_area = $("#id_area").val();
                            var denominacion = $("#denominacion").val();
                            
                            var_ins = {
                                "DENOMINACION":denominacion,
                                "ID_AREA":id_area
                            }
                            
                            var id = $("#idh").val();

                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                            
                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                var_ins:var_ins
                            }
                            
                            $.ajax({
                                url : _puestos.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    if(data.result==-1){
                                        jAlert('Este Permiso ya existe para este Rol. No se puede agregar.', $.ucwords(_etiqueta_modulo),function(){
                                        });
                                    }else if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $('#btnClear').trigger('click');
                                            $(".contenedor_entidades p input").first().trigger('click');
                                            $("#jqxgrid").show();
                                            $("#wpopup").html('');
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                        });
                                    }
                                    else{
                                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                        });
                                    }
                                }
                            });
                        });
                        
                        agregarEventoval();
                        
                        if (ver!=-1){
                            $(".elempie").html('').hide();
                        }
                        
                    }
                });
                
                $.unblockUI();
                
            }else if (top=='del'){
                if (_permiso_baja==0){
                    jAlert('Usted no tiene Permisos para ejecutar esta acción', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                
                jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                
                        var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                        mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                        var rowscount = $("#jqxgrid").jqxGrid('getdatainformation').rowscount;

                        if ( mydata==null ){
                            jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                                $.unblockUI();
                            });
                            return false;
                        }

                        $.ajax({
                            url : _puestos.URL + "/x_delobj",
                            data : {
                                id:mydata.ID
                            },
                            dataType : "json",
                            type : "post",
                            success : function(data){

                                if(data>0){

                                    if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                        var id = $("#jqxgrid").jqxGrid('getrowid', selectedrowindex);
                                        var commit = $("#jqxgrid").jqxGrid('deleterow', id);
                                    }

                                }
                                else{
                                    jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                        $.unblockUI();
                                    });
                                }

                            }
                        });
                    }
                    $.unblockUI();
                    });

            }else if (top=='are'){
                                
                // tipo entidad
                $.ajax({
                    url : _puestos.URL + "/x_getform_areas",
                    data : {
                        obj:obj
                    },
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
                            
                        $("#nom").focus();
                            
                        var source_ent ={
                                datatype: "json",
                                datafields: [{ name: 'ID' },{ name: 'DENOMINACION' }],
                                url: _puestos.URL + '/x_get_areas',
                                updaterow: function (rowid, rowdata, commit) {
                                    process_data(_puestos.URL + "/x_update_areasu",rowdata);
                                },
                                deleterow: function (rowid, commit) {
                                    process_data(_puestos.URL + "/x_delete_areasu",mydata);
                                    commit(true);
                                }
                        };

                        $("#jqxgrid_areas").jqxGrid(
                        {
                            width: '98%',
                            source: source_ent,
                            theme: 'energyblue',
                            editable: false,
                            localization: getLocalization(),
                            columns: [
                                    { text: 'ID', datafield: 'ID', width: '30%' , editable: false},
                                    { text: 'TIPO', datafield: 'DENOMINACION', width: '70%' }
                            ]
                        });
                        
                        add_event('jqxgrid_areas',_puestos.URL + "/x_add_areas",'DENOMINACION');
                        
                        var arr_confirma = [];
                        arr_confirma['url'] = _puestos.URL+ "/x_get_dependencia";
                        arr_confirma['tabla'] = 'fid_xpuestos';
                        arr_confirma['campo'] = 'ID_AREA';
                        delete_event( 'jqxgrid_areas', $.ucwords(_etiqueta_modulo) , arr_confirma );
                        
                        edit_event('jqxgrid_areas','DENOMINACION');
                            
                    }
                });                
                
            }else if(top=='lis'){
                $.unblockUI();
                $('#btnClear').trigger('click');
                $("#jqxgrid").show();
                $("#wpopup").html('');
                $("#jqxgrid").jqxGrid('updatebounddata');
            }
    });
            
       
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'DENOMINACION', type: 'string' },
            { name: 'AREA', type: 'string' },
            { name: 'ID' }
        ],
        url: 'general/extends/extra/puestos.php',
        data:{
            accion: "getUsuarios"
        },
        async:false,
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };
    
    var dataAdapterope = new $.jqx.dataAdapter(sourceope,
        {
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
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid").jqxGrid('hidecolumn', 'ID');
        },
        columnsresize: true,
        showtoolbar: true,
        sortable: true,
        filterable: true,
        showfilterrow: true,
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
            { text: 'ID', datafield: 'ID', width: '0%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'AREA', datafield: 'AREA', width: '30%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'DENOMINACION', datafield: 'DENOMINACION', width: '70%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true}
        ]
    });
    
   
    
});


function agregarEventoval(){
    $('#id_rol,').change(function() {
        $(this).validationEngine('validate');
    });

    $('#username,#email').keyup(function() {
        $(this).validationEngine('validate');
    });
}

function limpiar_inputs(){
    
    $("#frmagregar input").val("");
    $("#idh").val("");
    $("#id_area").val(0).trigger("chosen:updated");

    if($("#label_action").html()=='Editar'){
      $("#label_action").html("Agregar");
    }
    $("#frmagregar :text").removeClass('error');
    $("#email").select();
    
}