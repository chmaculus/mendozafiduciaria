var mydata;

$(document).ready(function(){
    mydata = '';
    
    $(".toolbar li").hover(
        function () {
            $(this).removeClass('li_sel').addClass('li_sel');
        },
        function () {
            $(this).removeClass('li_sel');
        }
    );
        
    $(".toolbar li").click(function(e) {
            e.preventDefault();
            var top = $(this).data('top');
            var obj = [];
            $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
            
            if (top=='ent'){
                // tipo entidad
                $.ajax({
                    url : _entidades.URL + "/x_getform_entidad",
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
                        $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                        $(".chzn-container-multi .chzn-choices").css('height','auto');
                            
                        var source_ent ={
                                datatype: "json",
                                datafields: [{ name: 'ID' },{ name: 'NOMBRE' }],
                                url: _entidades.URL + '/x_get_tipos_entidades',
                                updaterow: function (rowid, rowdata, commit) {
                                    process_data(_entidades.URL + "/x_update_tipos_entidades",rowdata);
                                },
                                deleterow: function (rowid, commit) {
                                    process_data(_entidades.URL + "/x_delete_tipos_entidades",mydata);
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
                                    { text: 'NOMBRE', datafield: 'NOMBRE', width: '70%' }
                            ]
                        });                        
                        add_event('jqxgrid_ent',_entidades.URL + "/x_add_tipos_entidades",'NOMBRE');
                        delete_event( 'jqxgrid_ent', $.ucwords(_etiqueta_modulo) );
                        edit_event('jqxgrid_ent','NOMBRE');
                            
                    }
                });                
                
            }else if(top =='add'){
                if (_permiso_alta==0){

                    jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }

                // add entidades
                $.ajax({
                    url : _entidades.URL + "/x_getform_addentidad",
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
                        $("#nombre").focus();
                        $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                        $(".chzn-container-multi .chzn-choices").css('height','auto');
                        $("#btnBorrar").hide();
                        
                        $("#cuit").jqxMaskedInput({ mask: '##-########-#', width: 150, height: 22, theme: theme });
                        $("#telefono").jqxMaskedInput({ mask: '(###)###-####', width: 150, height: 22, theme: theme });
                        
                        $('#btnClear').on('click', function(event) {
                            $("#customForm").validationEngine('hideAll');
                            event.preventDefault();
                            $("#frmagregar :text").val("");
                            $("#frmagregar :text").first().focus();
                            $("#idh").val("");
                            $("#provincia").val(0).trigger("chosen:updated");

                            if($("#label_action").html()=='Editar'){
                              $("#label_action").html("Agregar");
                            }
                            $("#frmagregar :text").removeClass('error');
                            $("#nom").select();
                            
//                            $('#tipo_entidades').val('').trigger('chosen:updated');

                        });
                                                
                        $('#send').on('click', function(event) {
                            
                            event.preventDefault();
                            var id = $("#idh").val();
                            var nombre = $("#nombre").val();
                            var descripcion = $("#descripcion").val();
                            var cuit = $("#cuit").val();
                            var telefono = $("#telefono").val();
                            var organismo = $("#organismo").val();
                            var tipo_entidades = $("#tipo_entidades").val();
                            var domicilio       = $("#domicilio").val();
                            var situacion_iva   = $("#situacion_iva").val();
                            var situacion_iibb  = $("#situacion_iibb").val();
                            var mail           = $("#mail").val();
                            var representante   = $("#representante").val();
                            var limite   = $("#limite").val();
                            
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;

                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                nombre:nombre,
                                descripcion:descripcion,
                                cuit:cuit,
                                telefono:telefono,
                                organismo:organismo,
                                domicilio:domicilio,
                                situacion_iva:situacion_iva,
                                situacion_iibb:situacion_iibb,
                                mail:mail,
                                representante:representante,
                                limite:limite
                            }

                            $.ajax({
                                url : _entidades.URL + "/x_sendobj",
                                data : {
                                    obj:obj,
                                    tipo_entidades:tipo_entidades
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    
                                    if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $('#btnClear').trigger('click');
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
                    url : _entidades.URL + "/x_getform_addentidad",
                    data : {
                        obj:mydata.id
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
                        $("#nombre").focus();
                        $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                        $(".chzn-container-multi .chzn-choices").css('height','auto');
                        $("#btnBorrar").show();
                        
                        $("#cuit").jqxMaskedInput({ mask: '##-########-#', width: 150, height: 22, theme: theme });
                        $("#telefono").jqxMaskedInput({ mask: '(###)###-####', width: 150, height: 22, theme: theme });
                        
                        
                        var cad = $("#val_entidadesh").val();
                        $("#label_action").html('Editar');
                        
                        if (cad.length>0){
                            var stringParts = cad.split(",");
                                
                            $.each(stringParts, function(key, value) {
                                $('#tipo_entidades').find("option[value='"+value+"']").attr('selected', 'selected');
                            });   
                            $('#tipo_entidades').trigger('chosen:updated');

                        }
                        
                        $('#btnBorrar').on('click', function(event) {
                            event.preventDefault();
                            
                            var iddel = $("#idh").val();
                                                     
                            jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                                if(r==true){
                                    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
                                    //eliminar
                                    $.ajax({
                                        url : _entidades.URL + "/x_delobj",
                                        data : {
                                            id : iddel
                                        },
                                        type : "post",
                                        success : function(data){

                                              if(data==1){
                                                  $.fancybox.close();
                                                  $("#jqxgrid").jqxGrid('updatebounddata');
                                                  $.unblockUI();
                                              }
                                              else{
                                                jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                                    $.unblockUI();
                                                });
                                              }
                                        }
                                    });

                                }

                            });
                            

                        });
                        
                        $('#btnClear').on('click', function(event) {
                            $("#customForm").validationEngine('hideAll');
                            event.preventDefault();
                            $("#frmagregar :text").val("");
                            $("#frmagregar :text").first().focus();
                            $("#idh").val("");
                            $("#provincia").val(0).trigger("chosen:updated");

                            if($("#label_action").html()=='Editar'){
                              $("#label_action").html("Agregar");
                            }
                            $("#frmagregar :text").removeClass('error');
                            $("#nom").select();
                            
                            $('#tipo_entidades').val('').trigger('chosen:updated');

                        });
                        
                        
                        $('#send').on('click', function(event) {
                            
                            event.preventDefault();
                            var id = $("#idh").val();
                            var nombre = $("#nombre").val();
                            var descripcion = $("#descripcion").val();
                            //var cuit = $("#cuit").val().replace('-','').replace('-','');
                            //var telefono = $("#telefono").val().replace('-','').replace('(','').replace(')','');
                            var cuit = $("#cuit").val();
                            var telefono = $("#telefono").val();
                            var organismo = $("#organismo").val();
                            var tipo_entidades = $("#tipo_entidades").val();
                            var domicilio       = $("#domicilio").val();
                            var situacion_iva   = $("#situacion_iva").val();
                            var situacion_iibb  = $("#situacion_iibb").val();
                            var mail           = $("#mail").val();
                            var representante   = $("#representante").val();
                            
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;

                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                nombre:nombre,
                                descripcion:descripcion,
                                cuit:cuit,
                                telefono:telefono,
                                organismo:organismo,
                                domicilio:domicilio,
                                situacion_iva:situacion_iva,
                                situacion_iibb:situacion_iibb,
                                mail:mail,
                                representante:representante
                            }

                            $.ajax({
                                url : _entidades.URL + "/x_sendobj",
                                data : {
                                    obj:obj,
                                    tipo_entidades:tipo_entidades
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    
                                    if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $('#btnClear').trigger('click');
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                            $.fancybox.close();
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
                
                console.dir(mydata);
                return false;
                
              
                $.ajax({
                    url : _entidades.URL + "/x_delobj_detalle",
                    data : {
                        id:mydata.id,
                        idt: mydata.idt
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
                
            }else if(top=='fil'){
                $.unblockUI();
                $('#jqxlistbox').slideToggle('slow', function() {
                });
                                
            }else if(top=='exp'){
                // exportar
                $.ajax({
                    url : _entidades.URL + "/x_getexportar",
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
        
    });
            
            /*
    var source ={
            datatype: "json",
            datafields: [{ name: 'entidad_tipo' },{ name: 'entidad' },{name: 'ID'},{name: 'idt'},{name: 'CUIT'},{name: 'DESCRIPCION'},{name: 'TELEFONO'},{name: 'ORGANISMO'},{name: 'FEC'},{name: 'DOMICILIO'},{name: 'MAIL'},{name: 'SITUACION_IVA'},,{name: 'SITUACION_IIBB'},{name: 'REPRESENTANTE'}],
            url: _entidades.URL + '/x_get_info_grid',
            deleterow: function (rowid, commit) {
                commit(true);
            }
    };
    */
    
    var source ={
        datatype: "json",
        datafields: [
            { name: 'id' },
            { name: 'idt' , type: 'string'},
            { name: 'entidad' , type: 'string'},
            { name: 'entidad_tipo' , type: 'string'},
            { name: 'DESCRIPCION' , type: 'string'},
            { name: 'CUIT' , type: 'string'},
            { name: 'TELEFONO' , type: 'string'},
            { name: 'ORGANISMO' , type: 'string'},
            { name: 'FEC' , type: 'string'},
            { name: 'DOMICILIO' , type: 'string'},
            { name: 'MAIL' , type: 'string'},
            { name: 'SITUACION_IVA' , type: 'string'},
            { name: 'SITUACION_IIBB' , type: 'string'},
            { name: 'REPRESENTANTE' , type: 'string'}
        ],
        //url: _clientes.URL + '/x_get_info_grid',
        url: 'general/extends/extra/clientes.php',
        data:{
            accion: "getEntidades"
        },
        async:false,
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };
    
    
    var dataAdapter = new $.jqx.dataAdapter(source,
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
        //source: source,
        source: dataAdapter,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid").jqxGrid('hidecolumn', 'ID');
            $("#jqxgrid").jqxGrid('hidecolumn', 'idt');
        },
        columnsresize: true,
        showtoolbar: true,
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
                //$("#word").addClass('jqx-widget-content-' + theme);
                //$("#word").addClass('jqx-rc-all-' + theme);
            }

            input.on('keydown', function (event) {
                if (input.val().length >= 2) {
                    if (me.timer) clearTimeout(me.timer);
                    me.timer = setTimeout(function () {
                        dataAdapter.dataBind();
                    }, 300);
                }
            });
        },
        sortable: true,
        filterable: true,
        showfilterrow: true,
        localization: getLocalization(),
        columns: [
                { text: 'ENTIDAD', datafield: 'entidad', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
                { text: 'TIPO ENTIDAD', datafield: 'entidad_tipo', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
                { text: 'ID ENTIDAD', datafield: 'ID', width: '0%' },
                { text: 'ID TIPO', datafield: 'idt', width: '0%' },
                { text: 'DESCRIPCION', groupable: false, datafield: 'DESCRIPCION', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
                { text: 'CUIT', groupable: false, datafield: 'CUIT', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
                { text: 'TELEFONO', groupable: false, datafield: 'TELEFONO', width: '20%' , columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
                { text: 'ORGANISMO', groupable: false, datafield: 'ORGANISMO', width: '20%' , hidden : true },
                { text: 'FECHA ALTA', groupable: false, datafield: 'FEC', width: '20%' , hidden : true },
                { text: 'DOMICILIO', groupable: false, datafield: 'DOMICILIO', width: '20%' , hidden : true },
                { text: 'MAIL', groupable: false, datafield: 'MAIL', width: '20%' , hidden : true },
                { text: 'SITUACION_IVA', groupable: false, datafield: 'SITUACION_IVA', width: '20%' , hidden : true },
                { text: 'SITUACION_IIBB', groupable: false, datafield: 'SITUACION_IIBB', width: '20%' , hidden : true },
                { text: 'REPRESENTANTE', groupable: false, datafield: 'REPRESENTANTE', width: '20%' , hidden : true },
        ]
    });
    
    
    //_campos
    var tmp_campos = [];
    var check;
    $.each(_campos, function(key, value) {
        
        check = true;
        if (key>2)
            check = false;
        
        if (value=='FEC'){
            tmp_campos.push({ label: 'FECHA ALTA', value: value, checked: check })
        }else{
            tmp_campos.push({ label: value, value: value, checked: check })
        }
        
    });
    
    var listSource = tmp_campos;
    $("#jqxlistbox").hide();
    $("#jqxlistbox").jqxListBox({ source: listSource, width: 300, height: 130, theme: theme, checkboxes: true });
    $("#jqxlistbox").on('checkChange', function (event) {
        
        if (event.args.checked) {
            $("#jqxgrid").jqxGrid('showcolumn', event.args.value);
        }
        else {
            $("#jqxgrid").jqxGrid('hidecolumn', event.args.value);
        }
    });
    
});


