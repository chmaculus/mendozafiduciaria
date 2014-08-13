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
                // add
                if (_permiso_alta==0){

                    jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                $.ajax({
                    url : _usuarios.URL + "/x_getform_addentidad",
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        
                        $(".chzn-select").chosen(); 
                        $("#btnBorrar").hide();
                                               
                        $('#send').on('click', function(event) {
                            event.preventDefault();
                            
                            var id_rol = $("#id_rol").val();
                            var nombre = $("#nombre").val();
                            var username = $("#username").val();
                            var email = $("#email").val();
                            var apellido = $("#apellido").val();
                            var clave = $("#clave").val();
                            var area = $("#areah").val();
                            var puesto = $("#puestoh").val();
                            
                            
                            var_ins = {
                                "USERNAME":username,
                                "NOMBRE":nombre,
                                "APELLIDO":apellido,
                                "ID_ROL":id_rol,
                                "EMAIL":email,
                                "CLAVE":clave,
                                "ID_AREA":area,
                                "ID_PUESTO":puesto
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
                                url : _usuarios.URL + "/x_sendobj",
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
                        
                        
                        $('#id_area').bind('change', function(event){
                            event.preventDefault();
                            $(this).validationEngine('validate');
                            if ($('#id_area').val()=='')
                                loadChild(0)

                            $('#areah').val($('#id_area').val());

                            var selected = $(this).find('option').eq(this.selectedIndex);
                            var connection = selected.data('connection');
                            
                            selected.closest('#rubro li').nextAll().remove();
                            if(connection){
                                loadChild(connection);
                            }
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
                    url : _usuarios.URL + "/x_getform_addentidad",
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
                        
                        $("#id_rol").val(_array_obj.ID_ROL).trigger("chosen:updated");
                        $("#id_area").val(_array_obj.ID_AREA).trigger("chosen:updated");
                        loadChild( _array_obj.ID_AREA );
                        $("#puesto").val(_array_obj.ID_PUESTO).trigger("chosen:updated");
                        
                        $('#btnClear').on('click', function(e) {
                            $("#customForm").validationEngine('hideAll');
                            e.preventDefault();
                            limpiar_inputs();
                        });
                        
                        $('#send').on('click', function(event){
                            event.preventDefault();
                            var id_rol = $("#id_rol").val();
                            var nombre = $("#nombre").val();
                            var username = $("#username").val();
                            var email = $("#email").val();
                            var apellido = $("#apellido").val();
                            var clave = $("#clave").val();
                            var area = $("#areah").val();
                            var puesto = $("#puestoh").val();
                            
                            var_ins = {
                                "USERNAME":username,
                                "NOMBRE":nombre,
                                "APELLIDO":apellido,
                                "ID_ROL":id_rol,
                                "EMAIL":email,
                                "ID_AREA":area,
                                "ID_PUESTO":puesto
                            }
                            
                            var _arr_adicionales = [];
                            _arr_adicionales = {
                                'c_hist':  $("#pie_chist").is(":checked")?'1':'0',
                                'e_hist':  $("#pie_ehist").is(":checked")?'1':'0',
                                'v_de':    $("#pie_vestde").is(":checked")?'1':'0',
                                'h_atras': $("#pie_hatras").is(":checked")?'1':'0',
                                'c_hist1': $("#pie_chist1").is(":checked")?'1':'0',
                                'e_hist1': $("#pie_ehist1").is(":checked")?'1':'0'
                            }
                            
                            
                            if(clave.length>0)
                                var_ins.CLAVE=clave
                                
                            
                            var id = $("#idh").val();

                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                            
                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                var_ins:var_ins,
                                _arr_adicionales:_arr_adicionales
                            }
                            
                            $.ajax({
                                url : _usuarios.URL + "/x_sendobj",
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
                        
                        $('#id_area').bind('change', function(event){
                            event.preventDefault();
                            $(this).validationEngine('validate');
                            if ($('#id_area').val()=='')
                                loadChild(0)

                            $('#areah').val($('#id_area').val());

                            var selected = $(this).find('option').eq(this.selectedIndex);
                            var connection = selected.data('connection');
                            
                            selected.closest('#rubro li').nextAll().remove();
                            if(connection){
                                loadChild(connection);
                            }
                        });
                        
                        if (ver!=-1){
                            $('.elempie').html('').hide();
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
                            url : _usuarios.URL + "/x_delobj",
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

            }else if (top=='ope'){
                                
                // tipo entidad
                $.ajax({
                    url : _usuarios.URL + "/x_getform_roles",
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
                                url: _usuarios.URL + '/x_get_rolesp',
                                updaterow: function (rowid, rowdata, commit) {
                                    process_data(_usuarios.URL + "/x_update_rolesp",rowdata);
                                },
                                deleterow: function (rowid, commit) {
                                    process_data(_usuarios.URL + "/x_delete_rolesp",mydata);
                                    commit(true);
                                }
                        };

                        $("#jqxgrid_ent").jqxGrid(
                        {
                            width: '98%',
                            source: source_ent,
                            theme: 'energyblue',
                            editable: false,
                            //editmode: 'dblclick',
                            localization: getLocalization(),
                            columns: [
                                    { text: 'ID', datafield: 'ID', width: '30%' , editable: false},
                                    { text: 'TIPO', datafield: 'DENOMINACION', width: '70%' }
                            ]
                        });
                        
                        add_event('jqxgrid_ent',_usuarios.URL + "/x_add_rolesp",'DENOMINACION');
                        
                        var arr_confirma = [];
                        arr_confirma['url'] = _usuarios.URL+ "/x_get_dependencia";
                        arr_confirma['tabla'] = 'fid_roles_usuarios';
                        arr_confirma['campo'] = 'ID_ROL';
                        delete_event( 'jqxgrid_ent', $.ucwords(_etiqueta_modulo) , arr_confirma );
                        
                        edit_event('jqxgrid_ent','DENOMINACION');
                            
                    }
                });                
                
            }else if (top=='are'){
                url = "backend/administracion/puestos";
                jConfirm('Esta seguro de ir a Areas y Puestos?. Los datos sin guardar, se perderán.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                        $(location).attr('href',url);
                    }else{
                        $.unblockUI();
                    }
                });
            }else if(top=='lis'){
                $.unblockUI();
                $('#btnClear').trigger('click');
                $("#jqxgrid").show();
                $("#wpopup").html('');
                $("#jqxgrid").jqxGrid('updatebounddata');
            }else if (top=='per'){
                url = "backend/administracion/permisos";
                jConfirm('Esta seguro de ir a Permisos?. Los datos sin guardar, se perderán.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                        $(location).attr('href',url);
                    }else{
                        $.unblockUI();
                    }
                });
            }
    });
      
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'DENOMINACION', type: 'string' },
            { name: 'USERNAME', type: 'string' },
            { name: 'AREA', type: 'string' },
            { name: 'PUESTO', type: 'string' },
            { name: 'NOMBRE', type: 'string' },
            { name: 'APELLIDO' , type: 'string'},
            { name: 'EMAIL', type: 'string' },
            { name: 'ID' }
        ],
        url: 'general/extends/extra/roles_permisos.php',
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
        sortable: true,
        filterable: true,
        showfilterrow: true,
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
            { text: 'AREA', datafield: 'AREA', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'PUESTO', datafield: 'PUESTO', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'ROL', datafield: 'DENOMINACION', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'NOMBRE', datafield: 'NOMBRE', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'APELLIDO', datafield: 'APELLIDO', width: '20%', groupable:false , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'USERNAME', datafield: 'USERNAME', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'EMAIL', datafield: 'EMAIL', width: '20%', groupable:false , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'ID', datafield: 'ID', width: '0%' }
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

function checkUSERNAME(field, rules, i, options){
    patron=/^[a-z\d_]{4,15}$/i;
    var usuario = field.val();
    if(usuario.match(patron)){
        return true;
    }else{
        return '* Nombre de usuario no válido';
    }
}

function limpiar_inputs(){
    
    $("#frmagregar input").val("");
    $("#idh").val("");
    $("#id_rol").val(0).trigger("chosen:updated");

    if($("#label_action").html()=='Editar'){
      $("#label_action").html("Agregar");
    }
    $("#frmagregar :text").removeClass('error');
    $("#email").select();
    
}


function loadChild(val){
    
    if(working==false){
        working = true;
        $.ajax({
              url : _usuarios.URL + "/x_getpuestos",
              async:false,
              data : {
                    idp : val
              },
              dataType: "json",
              type : "post",
              error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
              },
              success : function(r){
                var connection, options = '';
                $.each(r.items,function(k,v){
                    connection = '';
                    if(v)   connection = 'data-connection="'+v+'"';
                    options+= '<option value="'+v+'" '+connection+'>'+k+'</option>';
                });
                if(r.defaultText){
                    options = '<option>'+r.defaultText+'</option>'+options;
                }
                $('#div_puesto').html('<select class="chzn-select medium-select select" id="puesto">'+ options +'</select>');
                $('#puesto').on('change', function(event) {
                    event.preventDefault();
                    $('#puestoh').val($('#puesto').val());
                });
                var selects = $('#div_puesto').find('select');
                selects.chosen();
                working = false;
              }
          });
    }
    
}