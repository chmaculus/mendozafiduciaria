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
        function () {
            $(this).removeClass('li_sel').addClass('li_sel');
        },
        function () {
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
                _array_entidades = {};
                _array_chk = {};

                // add entidades
                $.ajax({
                    url : _operatorias.URL + "/x_getform_addentidad",
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        $('#tabs').tabs({
                            select: function(event, ui) { 
                                if(ui.index==1){
                                    $("#upload_file1").show();
                                }else{
                                    $("#upload_file1").hide();
                                }
                            }
                        });
                        $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
                        $("input[type=file]").each(function(){
                        if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");}
                        });
                       
                        
                        $("#cuit").jqxMaskedInput({ mask: '##-########-#', width: 150, height: 22, theme: theme });
                        $("#montom").numeric({ negative: false });
                        $("#tope").numeric({ negative: false });
                        $("#tasa_ic").numeric({ negative: false });
                        $("#tasa_im").numeric({ negative: false });
                        $("#tasa_ip").numeric({ negative: false });
                        $("#tasa_is").numeric({ negative: false });
                        
                        
                        $("#nombre").focus();
                        $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                        $("#btnBorrar").hide();
                                              
                        init_datepicker('#fini','-3','+0','0',1);
                        init_datepicker('#ffin','-3','+10','0',1);
                                                
                        $('.contenedor_entidades input').on('click', function(event) {
                            
                            $(".lista_ents").html('');
                            var valor = $(this).attr("id");
                            loadChild_ent(valor);
                            
                            // si tiene, cargar el array
                            var xxx = $(".contenedor_entidades p input:checked").val();
                            if (_array_entidades[xxx]){
                                
                                $.each(_array_entidades[xxx], function(key, value) {
                                    
                                    var valor = $('#entidades_sel [value='+value+']').text();
                                    $(".lista_ents").append('<li data-identidad="'+value+'">'+valor+'</li>');
                                    
                                    $('.lista_ents li').off().on('click', function(event) {
                                        event.preventDefault();
                                        var input_sel = $(".contenedor_entidades p input:checked").val();
                                        var pos = $(this).index();

                                        if (_array_entidades[input_sel]){
                                            _array_entidades[input_sel].splice(pos,1);
                                        }
                                        $(this).remove();

                                    });
                                    
                                });   
                            }
                        });
                        
                        $('.lista_opciones.add').on('click', function(event) {
                            event.preventDefault();
                            var id = $("#entidades_sel").val() ;
                            var nom = $("#entidades_sel option:selected").html() ;
                            
                            //validar vacio
                            if(id=='' || id == 'Elegir Entidad'){
                                jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                                    
                                });
                                return false;
                            }
                            
                            var sw = 0;
                            //validar repetido
                            $( ".lista_ents li" ).each(function( index ) {
                                if (id ==$(this).data('identidad')){
                                    sw = 1;
                                    return false;
                                }
                                
                            });
                            if( sw==1){
                                jAlert('Item repetido.', $.ucwords(_etiqueta_modulo),function(){
                                });
                                return false;
                            }
                            
                            $(".lista_ents").append('<li data-identidad="'+id+'">'+nom+'</li>');
                                                        
                            //crear array
                            var obj=[];
                            $( ".lista_ents li" ).each(function( index ) {
                                var ide = $(this).data('identidad');
                                obj.push(ide);
                            });
                            var xxx = $(".contenedor_entidades p input:checked").val();
                            _array_entidades[xxx] = obj;
                            
                            $('.lista_ents li').off().on('click', function(event) {
                                event.preventDefault();
                                var input_sel = $(".contenedor_entidades p input:checked").val();
                                var pos = $(this).index();
                                
                                if (_array_entidades[input_sel]){
                                    _array_entidades[input_sel].splice(pos,1);
                                }
                                $(this).remove();
                                                                
                            });
                                                        
                        });
                        
                        $('#btnClear').on('click', function(event) {
                            $("#customForm").validationEngine('hideAll');
                            event.preventDefault();
                            $("#frmagregar :text").val("");
                            $("#frmagregar :text").first().focus();
                            $("#idh").val("");
                            $("#tipoope").val(0).trigger("chosen:updated");
                            $(".uploader").find(".filename").val('Selecciona un Archivo...');
                            if($("#label_action").html()=='Editar'){
                              $("#label_action").html("Agregar");
                            }
                            $("#frmagregar :text").removeClass('error');
                            $("#nom").select();
                            
                        });
                                                
                        $('#send').on('click', function(event) {
                            event.preventDefault();
                            
                            var id = $("#idh").val();
                            var nombre = $("#nombre").val();
                            var descripcion = $("#descripcion").val();
                            var tope = $("#tope").val();
                            
                            var tasa_ic = $("#tasa_ic").val();
                            var tasa_im = $("#tasa_im").val();
                            var tasa_ip = $("#tasa_ip").val();
                            var tipoope = $("#tipoope").val();
                            var tasa_is = $("#tasa_is").val();
                            var desembolsos = $("#desembolsos").val();
                            var devoluciones = $("#devoluciones").val();
                            var periodicidad = $("#periodicidad").val();
                            var id_proceso = $("#id_proceso").val();
                            var jefeope = $("#jefeope").val();
                            var cordope = $("#cordope").val();
                            var ivapope = $("#ivaope").val();
                            var bancoope = $("#bancoope").val();
                            

                            
                            //checklist
                            var items = $("#listbox").jqxListBox('getCheckedItems');
                            var checkedItems = [];
                            $.each(items, function (index, value) {
                                checkedItems.push(value.value);
                            });
                            
                            //adjuntos
                            var _array_uploads = [];
                            $( ".lista_ents li" ).each(function( index ) {
                                var nombre = $(this).data('nom');
                                var nombre_tmp = $(this).data('tmp');
                                _array_uploads.push({nombre:nombre,nombre_tmp:nombre_tmp});
                            });
                            
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                                                                                 
                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                NOMBRE:nombre,
                                DESCRIPCION:descripcion,
                                TOPE_PESOS:tope,
                                TASA_INTERES_COMPENSATORIA:tasa_ic,
                                TASA_INTERES_MORATORIA:tasa_im,
                                TASA_INTERES_POR_PUNITORIOS:tasa_ip,
                                TASA_SUBSIDIADA:tasa_is,
                                DESEMBOLSOS:desembolsos,
                                DEVOLUCIONES:devoluciones,
                                JEFEOP:jefeope,
                                COORDOPE:cordope,
                                PERIODICIDAD:periodicidad,
                                ID_TIPO_OPERATORIA:tipoope,
                                checklist:checkedItems,
                                adjuntos:_array_uploads,
                                ID_PROCESO:id_proceso,
                                IVA:ivaope,
                                BANCO:bancoope
                            }
                          
                            $.ajax({
                                url : _operatorias.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    
                                    if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $('#btnClear').trigger('click');
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                            _array_entidades = {};
                                            $("#jqxgrid").show();
                                            $("#wpopup").html('');
                                            
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
                        
                        var sourcechk =
                        {
                            datatype: "json",
                            datafields: [
                                { name: 'ID' },
                                { name: 'NOMBRE' }
                            ],
                            id: 'ID',
                            url: _operatorias.URL + '/x_get_checklist'
                        };

                        var dataAdapterchk = new $.jqx.dataAdapter(sourcechk);
                        //$('#listbox').jqxListBox('invalidate');
                        $("#listbox").jqxListBox({ source: dataAdapterchk, checkboxes: true, displayMember: "NOMBRE", valueMember: "ID", width: 880, height: 450 });

                        $("#periodicidad").jqxNumberInput({ width: '100px', height: '25px', inputMode: 'simple', spinButtons: true,  decimal:0, decimalDigits:0,min: 0, max: 60 });
                        $("#desembolsos").jqxNumberInput({ width: '100px', height: '25px', inputMode: 'simple', spinButtons: true,  decimal:0, decimalDigits:0,min: 0, max: 60 });
                        $("#devoluciones").jqxNumberInput({ width: '100px', height: '25px', inputMode: 'simple', spinButtons: true,  decimal:0, decimalDigits:0,min: 0, max: 60 });
                        
                        refresListevent();
                        
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
                
                _array_entidades = {};
                _array_chk = {};
                
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
                    url : _operatorias.URL + "/x_getform_addentidad",
                    data : {
                        obj:mydata.ID
                    },
                    async:false,
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        $('#tabs').tabs({
                            select: function(event, ui) { 
                                if(ui.index==1){
                                    $("#upload_file1").show();
                                }else{
                                    $("#upload_file1").hide();
                                }
                            }
                        });

                        
                        
                        $("#nombre").focus();
                        $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                        $("#btnBorrar").show();
                                                
                        var cad = $("#val_operatoriash").val();
                        $("#label_action").html('Editar');
                        
                        var opeh1 = $("#operatoriah1").val();
                        $("#tipoope").val(opeh1).trigger("chosen:updated");
                        
                        var jefeopeh = $("#jefeopeh").val();
                        $("#jefeope").val(jefeopeh).trigger("chosen:updated");
                        
                        var cordopeh = $("#cordopeh").val();
                        $("#cordope").val(cordopeh).trigger("chosen:updated");
                        
                        
                        var id_procesoh = $("#id_procesoh").val()
                        $("#id_proceso").val(id_procesoh).trigger("chosen:updated");
                        $("#tope").numeric({ negative: false });
                        $("#tasa_ic").numeric({ negative: false });
                        $("#tasa_im").numeric({ negative: false });
                        $("#tasa_ip").numeric({ negative: false });
                        $("#tasa_is").numeric({ negative: false });
                        
                        var desh = $("#desh").val();
                        var devh = $("#devh").val();
                        var perh = $("#perh").val();
                        
                        $("#periodicidad").jqxNumberInput({ width: '100px', height: '25px', inputMode: 'simple', spinButtons: true,  decimal:0, decimalDigits:0,min: 0, max: 60 });
                        $("#desembolsos").jqxNumberInput({ width: '100px', height: '25px', inputMode: 'simple', spinButtons: true,  decimal:0, decimalDigits:0,min: 0, max: 60 });
                        $("#devoluciones").jqxNumberInput({ width: '100px', height: '25px', inputMode: 'simple', spinButtons: true,  decimal:0, decimalDigits:0,min: 0, max: 60 });
                        
                        $("#periodicidad").val(perh);
                        $("#devoluciones").val(devh);
                        $("#desembolsos").val(desh);
                        
                        $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
                        $("input[type=file]").each(function(){
                        if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");}
                        });
                        

                        var a_ent = $("#a_ent").val();
                        _array_entidades = {};
                                              
                        var sourcechk =
                        {
                            datatype: "json",
                            datafields: [
                                { name: 'ID' },
                                { name: 'NOMBRE' }
                            ],
                            id: 'ID',
                            async:false,
                            url: _operatorias.URL + '/x_get_checklist'
                        };
                        var dataAdapterchk = new $.jqx.dataAdapter(sourcechk);
                        $("#listbox").jqxListBox({ source: dataAdapterchk, checkboxes: true, displayMember: "NOMBRE", valueMember: "ID", width: 880, height: 450});

                        /* cargar valores de checklist */
                        _array_chk = _array_checklist;
                        var allItems = $("#listbox").jqxListBox('getItems'); 
                        var arr_index = [];
                        $.each(_array_chk, function (indice,valor) {
                            $.each(allItems, function (index,value) {
                                if (valor==value.value){
                                  arr_index.push(index);
                                  return false;
                                }
                            });
                        });
                        $.each( arr_index, function(key, value) {
                            $("#listbox").jqxListBox('checkIndex', value );
                        });
                        /* cargar valores de checklist fin */
                        
                        $(".contenedor_entidades p input").first().trigger('click');
                        
                        $('.contenedor_entidades input').on('click', function(event) {
                            
                            $(".lista_ents").html('');
                            var valor = $(this).attr("id");
                            loadChild_ent(valor);
                            
                            // si tiene, cargar el array
                            var xxx = $(".contenedor_entidades p input:checked").val();
                            if (_array_entidades[xxx]){
                                
                                $.each(_array_entidades[xxx], function(key, value) {
                                    
                                    var valor = $('#entidades_sel [value='+value+']').text();
                                    $(".lista_ents").append('<li data-identidad="'+value+'">'+valor+'</li>');
                                    
                                    $('.lista_ents li').off().on('click', function(event) {
                                        event.preventDefault();
                                        var input_sel = $(".contenedor_entidades p input:checked").val();
                                        var pos = $(this).index();

                                        if (_array_entidades[input_sel]){
                                            _array_entidades[input_sel].splice(pos,1);
                                        }
                                        $(this).remove();

                                    });
                                    
                                });   
                            }
                        });
                        
                        $(".contenedor_entidades p input").first().trigger('click');
                        $('.lista_opciones.add').on('click', function(event) {
                            event.preventDefault();
                            var id = $("#entidades_sel").val() ;
                            var nom = $("#entidades_sel option:selected").html() ;
                            
                            //validar vacio
                            if(id=='' || id == 'Elegir Entidad'){
                                jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                                    
                                });
                                return false;
                            }
                            
                            var sw = 0;
                            //validar repetido
                            $( ".lista_ents li" ).each(function( index ) {
                                if (id ==$(this).data('identidad')){
                                    sw = 1;
                                    return false;
                                }
                                
                            });
                            if( sw==1){
                                jAlert('Item repetido.', $.ucwords(_etiqueta_modulo),function(){
                                });
                                return false;
                            }
                            
                            $(".lista_ents").append('<li data-identidad="'+id+'">'+nom+'</li>');
                            
                            //crear array
                            var obj=[];
                            $( ".lista_ents li" ).each(function( index ) {
                                var ide = $(this).data('identidad');
                                obj.push(ide);
                            });
                            var xxx = $(".contenedor_entidades p input:checked").val();
                            _array_entidades[xxx] = obj;
                            
                            
                            $('.lista_ents li').off().on('click', function(event) {
                                event.preventDefault();
                                var input_sel = $(".contenedor_entidades p input:checked").val();
                                var pos = $(this).index();
                                
                                if (_array_entidades[input_sel]){
                                    _array_entidades[input_sel].splice(pos,1);
                                }
                                $(this).remove();
                                                                
                            });
                        });
                            
                        $('.lista_ents li').off().on('click', function(event) {
                            event.preventDefault();
                            var $this = $(this);
                            var idope = $this.data('identidad');
                            var ruta = $this.data('ruta');

                            jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                                if(r==true){
                                    //borrar archivo en la bd y fisicamente
                                    $.ajax({
                                        url : _operatorias.URL + "/x_delupload",
                                        data : {
                                            idope:idope,
                                            ruta:ruta
                                        },
                                        dataType : "json",
                                        type : "post",
                                        success : function(data){
                                            $this.next().remove();
                                            $this.remove();
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

                        });
                        
                        
                        $('#send').on('click', function(event) {
                            
                            event.preventDefault();
                            var id = $("#idh").val();
                            var nombre = $("#nombre").val();
                            var descripcion = $("#descripcion").val();
                            var tope = $("#tope").val();
                            
                            var tasa_ic = $("#tasa_ic").val();
                            var tasa_im = $("#tasa_im").val();
                            var tasa_ip = $("#tasa_ip").val();
                            var tipoope = $("#tipoope").val();
                            var tasa_is = $("#tasa_is").val();
                            var id_proceso = $("#id_proceso").val();
                            var desembolsos = $("#desembolsos").val();
                            var devoluciones = $("#devoluciones").val();
                            var periodicidad = $("#periodicidad").val();
                            var jefeope = $("#jefeope").val();
                            var cordope = $("#cordope").val();
                            var bancoope = $("#bancoope").val();
                            
                            var items = $("#listbox").jqxListBox('getCheckedItems');
                            var checkedItems = [];
                            $.each(items, function (index, value) {
                                checkedItems.push(value.value);
                            });
                            
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                            
                            //adjuntos
                            var _array_uploads = [];
                            $( ".lista_ents li" ).each(function( index ) {
                                var nombre = $(this).data('nom');
                                var nombre_tmp = $(this).data('tmp');
                                if (nombre && nombre_tmp)
                                    _array_uploads.push({nombre:nombre,nombre_tmp:nombre_tmp});
                            });
                            
                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                NOMBRE:nombre,
                                DESCRIPCION:descripcion,
                                TOPE_PESOS:tope,
                                TASA_INTERES_COMPENSATORIA:tasa_ic,
                                TASA_INTERES_MORATORIA:tasa_im,
                                TASA_INTERES_POR_PUNITORIOS:tasa_ip,
                                TASA_SUBSIDIADA:tasa_is,
                                DESEMBOLSOS:desembolsos,
                                DEVOLUCIONES:devoluciones,
                                PERIODICIDAD:periodicidad,
                                ID_TIPO_OPERATORIA:tipoope,
                                checklist:checkedItems,
                                adjuntos:_array_uploads,
                                ID_PROCESO:id_proceso,
                                JEFEOP:jefeope,
                                COORDOPE:cordope,
                                IVA:ivaope,
                                BANCO:bancoope
                            }
                                                        
                            $.ajax({
                                url : _operatorias.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    
                                    if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $('#btnClear').trigger('click');
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                            _array_entidades = {};
                                            $(".contenedor_entidades p input").first().trigger('click');
                                            
                                            $("#jqxgrid").show();
                                            $("#wpopup").html('');
                                                                                        
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
                        refresListevent();

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
                            url : _operatorias.URL + "/x_delobj",
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
                    url : _operatorias.URL + "/x_getform_entidad",
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
                                datafields: [{ name: 'ID' },{ name: 'TIPO' }],
                                url: _operatorias.URL + '/x_get_tipos_operatoria',
                                updaterow: function (rowid, rowdata, commit) {
                                    process_data(_operatorias.URL + "/x_update_tipos_operatoria",rowdata);
                                },
                                deleterow: function (rowid, commit) {
                                    process_data(_operatorias.URL + "/x_delete_tipos_operatoria",mydata);
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
                                    { text: 'TIPO', datafield: 'TIPO', width: '70%' }
                            ]
                        });
                        
                        add_event('jqxgrid_ent',_operatorias.URL + "/x_add_tipos_operatoria",'TIPO');
                        
                        var arr_confirma = [];
                        arr_confirma['url'] = _operatorias.URL+ "/x_get_dependencia_operatoria";
                        arr_confirma['tabla'] = 'fid_operatorias';
                        arr_confirma['campo'] = 'ID_TIPO_OPERATORIA';

                        delete_event( 'jqxgrid_ent', $.ucwords(_etiqueta_modulo) , arr_confirma );
                        edit_event('jqxgrid_ent','TIPO');
                            
                    }
                });                
                
            }else if (top=='chk'){
                                
                // tipo entidad
                $.ajax({
                    url : _operatorias.URL + "/x_getform_checklist",
                    data : {
                        obj:obj
                    },
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        /*
                        $.fancybox(
                            data,
                            {
                                'padding'   :  20,
                                'autoScale' :true,
                                'scrolling' : 'no'
                            }
                        );
                        */
                        
                        $.fancybox({
                            "content": data,
                            'padding'   :  35,
                            'autoScale' :true,
                            'height' : 900,
                            'scrolling' : 'no',
                            'beforeClose': function() {
                                $(".refresgrid").trigger('click');
                            }
                        });
                            
                        $("#nom").focus();
                        $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                        $(".chzn-container-multi .chzn-choices").css('height','auto');
                        
                        var employeesSource =
                        {
                            datatype: "json",
                            datafields: [
                                { name: 'ID' },{ name: 'TIPO' }
                            ],
                            id: 'ID',
                            url: _operatorias.URL + '/x_get_tipos_operatoria',
                            async: false
                        };
                        
                        var employeesAdapter = new $.jqx.dataAdapter(employeesSource, {
                            autoBind: true,
                            beforeLoadComplete: function (records) {
                                var data = new Array();
                                for (var i = 0; i < records.length; i++) {
                                    var employee = records[i];
                                    data.push(employee);
                                }
                                return data;
                            }
                        });
                        
                        var dropdownListSource = [];
                        var dropdownListIid = [];
                        for (var i = 0; i < employeesAdapter.records.length; i++) {
                            dropdownListSource[i] = employeesAdapter.records[i]['TIPO'];
                            dropdownListIid[i] = employeesAdapter.records[i]['ID'];
                        }                  
                        
                        var source_ent ={
                                datatype: "json",
                                datafields: [
                                    { name: 'ID' },{ name: 'NOMBRE' }
                                ],
                                url: _operatorias.URL + '/x_get_checklist',
                                updaterow: function (rowid, rowdata, commit) {
                                    var arr_ed = [];
                                    arr_ed.push({ID:rowdata.ID,ID_OPERATORIA:id_edit,NOMBRE:rowdata.NOMBRE});
                                    process_data(_operatorias.URL + "/x_update_checklist",arr_ed);
                                },
                                deleterow: function (rowid, commit) {
                                    process_data(_operatorias.URL + "/x_delete_checklist",mydata);
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
                            columnsresize: true,
                            ready: function () {
                                $("#jqxgrid_ent").jqxGrid('hidecolumn', 'ID');
                           },
                            columns: [
                                { text: 'ID', datafield: 'ID', width: '0%' },
                                { text: 'NOMBRE', datafield: 'NOMBRE', width: '100%' }
                            ]
                        });
                        
                        add_event('jqxgrid_ent',_operatorias.URL + "/x_add_checklist",'NOMBRE');
                        //delete_event('jqxgrid_ent', $.ucwords(_etiqueta_modulo) );
                        
                        var arr_confirma = [];
                        arr_confirma['url'] = _operatorias.URL+ "/x_get_dependencia_checklist";
                        arr_confirma['tabla'] = 'fid_operatoria_checklist';
                        arr_confirma['campo'] = 'ID_CHECKLIST';
                        
                        delete_event('jqxgrid_ent', $.ucwords(_etiqueta_modulo) , arr_confirma );
                        
                        edit_event('jqxgrid_ent','NOMBRE');
                    
                    }
                });                
                
            }else if(top=='fil'){
                $.unblockUI();
                $('#jqxlistbox').slideToggle('slow', function() {});
            }else if(top=='lis'){
                $.unblockUI();
                $('#btnClear').trigger('click');
                $("#jqxgrid").jqxGrid('updatebounddata');
                _array_entidades = {};
                $("#jqxgrid").show();
                $("#wpopup").html('');
            }else if(top=='exp'){
                
                // exportar
                $.ajax({
                    url : _operatorias.URL + "/x_getexportar",
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
                                    $("#jqxgrid").jqxGrid('exportdata', 'xls', 'ope_'+fGetNumUnico(), true, null, null, url_e);
                                    break;
                                case 'csv':
                                    $("#jqxgrid").jqxGrid('exportdata', 'csv', 'ope_'+fGetNumUnico(), true, null, null, url_e);
                                    break;
                                case 'htm':
                                    $("#jqxgrid").jqxGrid('exportdata', 'html','ope_'+fGetNumUnico(), true, null, null, url_e);
                                    break;
                                case 'xml':
                                    $("#jqxgrid").jqxGrid('exportdata', 'xml', 'ope_'+fGetNumUnico(), true, null, null, url_e);
                            }
                        });
                            
                    }
                });
                
                
            }
    });
            
    var source ={
            datatype: "json",
            datafields: [{ name: 'NOMBRE' },{ name: 'DESCRIPCION' },{name: 'OTTIPO'},{name: 'TOPE_PESOS'},{name: 'ID'}],
            url: _operatorias.URL + '/x_get_info_grid',
            deleterow: function (rowid, commit) {
                commit(true);
            }
    };
    
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'ID' },
            { name: 'NOMBRE', type: 'string' },
            { name: 'FIDEI', type: 'string' },
            { name: 'TIPO', type: 'string' },
            { name: 'DESCRIPCION', type: 'string' },
            { name: 'TOPE_PESOS' , type: 'string'},
            { name: 'TASA_INTERES_COMPENSATORIA', type: 'string' },
            { name: 'TASA_INTERES_MORATORIA', type: 'string' },
            { name: 'TASA_INTERES_POR_PUNITORIOS', type: 'string' },
            { name: 'TASA_SUBSIDIADA', type: 'string' },
            { name: 'DESEMBOLSOS', type: 'string' },
            { name: 'DEVOLUCIONES', type: 'string' },
            { name: 'PERIODICIDAD', type: 'string' }
        ],
        //url: _clientes.URL + '/x_get_info_grid',
        url: 'general/extends/extra/operatorias.php',
        data:{
            accion: "getOperatorias"
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
        //source: source,
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid").jqxGrid('hidecolumn', 'ID');
        },
        sortable: true,
        filterable: true,
        showfilterrow: true,
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
            { text: 'FIDEICOMISO', datafield: 'FIDEI', width: '45%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'NOMBRE', datafield: 'NOMBRE', width: '45%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'TIPO', datafield: 'TIPO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'TOPE', datafield: 'TOPE_PESOS', width: '15%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'DESCRIPCION', datafield: 'DESCRIPCION', width: '20%', groupable:false, columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'ID', datafield: 'ID', width: '0%' },
            { text: 'TASA_INTERES_COMPENSATORIA', datafield: 'TASA_INTERES_COMPENSATORIA', width: '20%', hidden : true, filterable : false },
            { text: 'TASA_INTERES_MORATORIA', datafield: 'TASA_INTERES_MORATORIA', width: '20%', hidden : true, filterable : false },
            { text: 'TASA_INTERES_POR_PUNITORIOS', datafield: 'TASA_INTERES_POR_PUNITORIOS', width: '20%', hidden : true, filterable : false },
            { text: 'TASA_SUBSIDIADA', datafield: 'TASA_SUBSIDIADA', width: '20%', hidden : true, filterable : false },
            { text: 'DESEMBOLSOS', datafield: 'DESEMBOLSOS', width: '20%', hidden : true, filterable : false },
            { text: 'DEVOLUCIONES', datafield: 'DEVOLUCIONES', width: '20%', hidden : true, filterable : false },
            { text: 'PERIODICIDAD', datafield: 'PERIODICIDAD', width: '20%', hidden : true, filterable : false }
        ]
    });
    
    //Agregar Campos
    var tmp_campos = [];
    var check;        
    $.each(_campos, function(key, value) {
        check = true;
        if (key>3)
            check = false;
        
        if (value=='ID_INV'){
            tmp_campos.push({ label: 'INSCRIPCION INV', value: value, checked: check })
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


function loadChild(val){
    if(working==false){
        working = true;
        $.ajax({
              url : _operatorias.URL + "/x_getlocalidad",
              async:false,
              data : {
                    idp : val
              },
              dataType: "json",
              type : "post",
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
                $('#div_subrubro').html('<select class="chzn-select medium-select select" id="subrubro">'+ options +'</select>');
                $('#subrubro').on('change', function(event) {
                    event.preventDefault();
                    $('#localidadh').val($('#subrubro').val());
                });
                var selects = $('#div_subrubro').find('select');
                selects.chosen();
                working = false;
              }
          });
    }
    
}

function loadChild_ent(val){

    if(working==false){
        $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
        working = true;
        $.ajax({
              url : _operatorias.URL + "/x_getentidad_select",
              async:false,
              data : {
                    idt : val
              },
              dataType: "json",
              type : "post",
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
                
                $('.contenedor_entidades2').html('<select class="chzn-select medium-select1 select" id="entidades_sel">'+ options +'</select>');
                $('#entidades_sel').on('change', function(event) {
                    event.preventDefault();
                    //$('#localidadh').val($('#subrubro').val());
                });
                var selects = $('.contenedor_entidades2').find('select');
                selects.chosen();
                working = false;
                $.unblockUI();
              }
          });
    }
    
}

function post_upload(nombre,nombre_tmp){
    
    jAlert('Archivo cargado correctamente. ' + nombre, $.ucwords(_etiqueta_modulo),function(){
        //agregarlo a la lista
        $(".lista_ents").append('<li data-nom="'+nombre+'" data-tmp="'+nombre_tmp+'">'+nombre+'</li>');
        $('.lista_ents li').last().off().on('click', function(event){
            event.preventDefault();
            $this = $(this);
            var ruta = $(this).data('tmp');
            //x_borrar_file
            $.ajax({
                url : _operatorias.URL + "/x_borrar_file",
                data : {
                    ruta:ruta
                },
                dataType : "json",
                type : "post",
                success : function(){
                    $this.remove();
                }
            });
        });
        $('#upload_file1').each (function(){
            this.reset();
        });
        $("#upload_file1 input[type=file]").each(function(){
            $(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");
        });
    });
    
}

function error_post_upload(nombre){
    jAlert('El archivo ' + nombre + ' ya existe en el servidor.', $.ucwords(_etiqueta_modulo),function(){
         //agregarlo a la lista
    });
}
