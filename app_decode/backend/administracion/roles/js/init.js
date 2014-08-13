var theme = 'energyblue';
var mydata;
var id_edit;
var working = false;
var _array_entidades = {};
var _array_chk = {};

$(document).ready(function(){
    mydata = '';
    var data_arbol = _menu_arbol;
    var data_etapas = _etapas;
    
    var tmpx = {id:99,parentid:0,text:'Etapas del Sistema',value:10};
    data_etapas.unshift(tmpx);
    //data_etapas.push(tmpx);
    
    $(".toolbar li").hover(
        function(){
            $(this).removeClass('li_sel').addClass('li_sel');
        },
        function(){
            $(this).removeClass('li_sel');
        }
    );

    $(".taux li").click(function(e){
            e.preventDefault();
            var top = $(this).data('top');
            var obj = [];
            $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
            
            if(top=='usu'){
                url = "backend/administracion/usuarios";
                jConfirm('Esta seguro de ir a Usuarios?. Los datos sin guardar, se perderán.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                        $(location).attr('href',url);
                    }else{
                        $.unblockUI();
                    }
                });
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
    
    var obj = [];
    
    $.ajax({
        url : _roles.URL + "/x_getform_roles",
        data : {
            obj:obj
        },
        type : "post",
        success : function(data){
            $.unblockUI();

            var source_ent ={
                    datatype: "json",
                    datafields: [{ name: 'ID' },{ name: 'DENOMINACION' }],
                    url: _roles.URL + '/x_get_rolesp',
                    updaterow: function (rowid, rowdata, commit) {
                        process_data(_roles.URL + "/x_update_rolesp",rowdata);
                    },
                    deleterow: function (rowid, commit) {
                        process_data(_roles.URL + "/x_delete_rolesp",mydata);
                        commit(true);
                        $.unblockUI();
                    }
            };

            $("#jqxgrid_ent").jqxGrid(
            {
                width: '98%',
                source: source_ent,
                theme: 'energyblue',
                editable: false,
                ready: function () {
                    $("#taux2").show();
                },
                localization: getLocalization(),
                columns: [
                        { text: 'ID', datafield: 'ID', width: '30%' , editable: false},
                        { text: 'TIPO', datafield: 'DENOMINACION', width: '70%' }
                ]
            });

            add_event('jqxgrid_ent',_roles.URL + "/x_add_rolesp",'DENOMINACION');

            var arr_confirma = [];
            arr_confirma['url'] = _roles.URL+ "/x_get_dependencia";
            arr_confirma['tabla'] = 'fid_roles_roles';
            arr_confirma['campo'] = 'ID_ROL';
            delete_event( 'jqxgrid_ent', $.ucwords(_etiqueta_modulo) , arr_confirma );

            edit_event('jqxgrid_ent','DENOMINACION');

            $("#permisos_menu").jqxButton({ theme: theme });
            $("#permisos_etapas").jqxButton({ theme: theme });
            
            $("#permisos_etapas").on('click', function () {
                
                mydata1 = '';
                var selectedrowindex = $("#jqxgrid_ent").jqxGrid('getselectedrowindex');
                mydata1 = $('#jqxgrid_ent').jqxGrid('getrowdata', selectedrowindex);

                if ( mydata1==null ){
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                
                $.ajax({
                    url : _roles.URL + "/x_getform_etapas",
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
                            
                        $("#aux3 li").hover(
                            function(){
                                $(this).removeClass('li_sel').addClass('li_sel');
                            },
                            function(){
                                $(this).removeClass('li_sel');
                            }
                        );
                            
                        var source =
                        {
                            datatype: "json",
                            datafields: [
                                { name: 'id' },
                                { name: 'parentid' },
                                { name: 'text' },
                                { name: 'value' }
                            ],
                            id: 'id',
                            localdata: data_etapas
                            
                        };
                           
                        var dataAdapter = new $.jqx.dataAdapter(source);
                        dataAdapter.dataBind();
                        var records = dataAdapter.getRecordsHierarchy('id', 'parentid', 'items', [{ name: 'text', map: 'label'}]);
                        $('#jqxgrid_etapas').jqxTree({ source: records, height: '400px', theme: theme , hasThreeStates: true, checkboxes: true, width: '475px'});
                        $('#jqxgrid_etapas').jqxTree('expandAll');
                        
                        //cargar los q existen
                        $.ajax({
                            url : _roles.URL + "/x_get_roletapa",
                            data : {
                                id_rol:mydata1.ID
                            },
                            dataType : "json",
                            type : "post",
                            success : function(ret){
                               $.each(ret, function (index, value){
                                    $('#jqxgrid_etapas').jqxTree('checkItem', $("#jqxgrid_etapas").find('li#'+value.ID_ETAPA)[0]);
                               });

                            }
                        });
                       
                        $("#guardar_menu_btn").click(function(e){
                                e.preventDefault();
                                var items = $('#jqxgrid_etapas').jqxTree('getCheckedItems');
                                
                                var arr_index = [];
                                $.each(items, function (index, value) {
                                    arr_index.push({id:value.id,padre:value.parentId});
                                });
                                
                                                              
                                $.ajax({
                                    url : _roles.URL + "/x_guardar_roletapa",
                                    data : {
                                        items:arr_index,
                                        id_rol:mydata1.ID
                                    },
                                    dataType : "json",
                                    type : "post",
                                    success : function(data){
                                        if(data){
                                            jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                                $.fancybox.close();
                                            });
                                        }else{
                                            jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                                $.unblockUI();
                                                $.fancybox.close();
                                            });
                                        }

                                    }
                                });
                        });
                                               
                        $("#check_menu_btn").click(function(e){
                            $('#jqxgrid_etapas').jqxTree('checkAll');
                        });
                        
                        $("#uncheck_menu_btn").click(function(e){
                            $('#jqxgrid_etapas').jqxTree('uncheckAll');
                        });
                        
                        
                    }
                });  
                
            });
           
            $("#permisos_menu").on('click', function () {
                
                mydata1 = '';
                var selectedrowindex = $("#jqxgrid_ent").jqxGrid('getselectedrowindex');
                mydata1 = $('#jqxgrid_ent').jqxGrid('getrowdata', selectedrowindex);

                if ( mydata1==null ){
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                
                
                $.ajax({
                    url : _roles.URL + "/x_getform_menu",
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
                            
                        $("#aux3 li").hover(
                            function(){
                                $(this).removeClass('li_sel').addClass('li_sel');
                            },
                            function(){
                                $(this).removeClass('li_sel');
                            }
                        );
                            
                        $("#nom").focus();
                        
                        var source =
                        {
                            datatype: "json",
                            datafields: [
                                { name: 'id' },
                                { name: 'parentid' },
                                { name: 'text' },
                                { name: 'value' }
                            ],
                            id: 'id',
                            localdata: data_arbol
                        };
                           
                        var dataAdapter = new $.jqx.dataAdapter(source);
                        dataAdapter.dataBind();
                        var records = dataAdapter.getRecordsHierarchy('id', 'parentid', 'items', [{ name: 'text', map: 'label'}]);
                        $('#jqxgrid_menu').jqxTree({ source: records, height: '400px', theme: theme , hasThreeStates: true, checkboxes: true, width: '475px'});
                        $('#jqxgrid_menu').jqxTree('expandAll');
                        
                        //cargar los q existen
                        $.ajax({
                            url : _roles.URL + "/x_get_rolmenu",
                            data : {
                                id_rol:mydata1.ID
                            },
                            dataType : "json",
                            type : "post",
                            success : function(ret){
                               $.each(ret, function (index, value){
                                    $('#jqxgrid_menu').jqxTree('checkItem', $("#jqxgrid_menu").find('li#'+value.ID_MENU)[0]);
                               });

                            }
                        });
                        
                        $("#guardar_menu_btn").click(function(e){
                                e.preventDefault();
                                var items = $('#jqxgrid_menu').jqxTree('getCheckedItems');
                                
                                var arr_index = [];
                                $.each(items, function (index, value) {
                                    arr_index.push({id:value.id,padre:value.parentId});
                                });
                              
                                $.ajax({
                                    url : _roles.URL + "/x_guardar_rolmenu",
                                    data : {
                                        items:arr_index,
                                        id_rol:mydata1.ID
                                    },
                                    dataType : "json",
                                    type : "post",
                                    success : function(data){
                                        if(data){
                                            jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                                $.fancybox.close();
                                            });
                                        }else{
                                            jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                                $.unblockUI();
                                                $.fancybox.close();
                                            });
                                        }

                                    }
                                });
                        });
                        
                        $("#expandir_menu_btn").click(function(e){
                            $('#jqxgrid_menu').jqxTree('expandAll');
                        });
                        
                        $("#colapsar_menu_btn").click(function(e){
                            $('#jqxgrid_menu').jqxTree('collapseAll');
                        });
                        
                        $("#check_menu_btn").click(function(e){
                            $('#jqxgrid_menu').jqxTree('checkAll');
                        });
                        
                        $("#uncheck_menu_btn").click(function(e){
                            $('#jqxgrid_menu').jqxTree('uncheckAll');
                        });
                        
                        
                    }
                });  
                
            });
            

        }
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
