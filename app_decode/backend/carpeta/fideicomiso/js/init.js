var mydata;
var working = false;

var working_or = false;

var _array_entidades = {};
var _array_operatorias = [];

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
            
            if(top =='add'){
                if (_permiso_alta==0){

                    jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                _array_entidades = {};
                _array_operatorias = [];
                // add entidades
                $.ajax({
                    url : _fideicomiso.URL + "/x_getform_addentidad",
                    type : "post",
                    success : function(data){
                        $(".suma_aportes").hide();
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        $('#tabs').tabs({
                            select: function(event, ui) {
                                if(ui.index==4){
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
                        
                        //$("#cuit").jqxMaskedInput({ mask: '##-########-#', width: 150, height: 22, theme: theme });
                        $("#montom").numeric({ negative: false });
                                                
//                        $('#select-fid-contable').show();
//                        $('#lbl-fidcontables').show();
//                        $('#fidcontables').show();
                        
                        $("#aporte_aporte").numeric({ negative: false });
                        //$("#aporte_origen").chosen({width: "255px"});
                        //$("#aporte_nombre").chosen({width: "255px"});
                        //$("#operatorias").chosen({width: "255px"});
                        
                        
                        $("#nombre").focus();
                        //$(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                        $(".chzn-select").not("#operatorias").not("#entidades_sel").chosen({width: "255px"});
                        $("#entidades_sel").chosen({width: "630px"});
                        $("#operatorias").chosen({width: "630px"});
                        $("#btnBorrar").hide();
                                              
                        init_datepicker('#fini','-3','+0','0');
                        init_datepicker('#ffin','-3','+10','0',1);
                        init_datepicker('#aporte_fecha','-3','+5','0',0);
                                                
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
                        
                        //addd operatorias
                        $('.lista_opciones.addope').on('click', function(event) {
                            event.preventDefault();
                            
                            var id = $("#operatorias").val() ;
                            var nom = $("#operatorias option:selected").html() ;
                            
                            //validar vacio
                            if(id=='' || id == 'Elegir Entidad'){
                                jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                                    
                                });
                                return false;
                            }
                            
                            var sw = 0;
                            //validar repetido
                            $( ".lista_ope li" ).each(function( index ) {
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
                            
                            $(".lista_ope").append('<li data-identidad="'+id+'">'+nom+'</li>');
                                                        
                            _array_operatorias.push(id);
                            
                            $('.lista_ope li').off().on('click', function(event){
                                event.preventDefault();
                                var pos = $(this).index();
                                _array_operatorias.splice(pos,1);
                                $(this).remove();
                            });
                        });
                        //$("#telefono").jqxMaskedInput({ mask: '(###)###-####', width: 150, height: 22, theme: theme });
                        loadChild(0);

                        $('#provincia').bind('change', function(event){
                            event.preventDefault();
                            $(this).validationEngine('validate');
                            if ($('#provincia').val()=='')
                                loadChild(0)

                            $('#provinciah').val($('#provincia').val());

                            var selected = $(this).find('option').eq(this.selectedIndex);

                            var connection = selected.data('connection');
                            selected.closest('#rubro li').nextAll().remove();
                            if(connection){
                                loadChild(connection);
                            }
                        });
                        
                        
                        //loadChildOrigen(0);

                        $('#aporte_origen').bind('change', function(event){
                            
                            event.preventDefault();
                            //$(this).validationEngine('validate');
                            if ($('#aporte_origen').val()=='')
                                loadChildOrigen(0)

                            $('#origenh').val($('#aporte_origen').val());

                            var selected = $(this).find('option').eq(this.selectedIndex);

                            var connection = selected.data('connection');
                            selected.closest('#aporte_origen li').nextAll().remove();
                            if(connection){
                                loadChildOrigen(connection);
                            }
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
                                                
                        $('#send').on('click', function(event){
                            event.preventDefault();
          
                            var id = $("#idh").val();
                            var nombre = $("#nombre").val();
                            var fini = $("#fini").val();
                            fini = formattedDate(fini);
                            
                            var ffin = $("#ffin").val();
                            ffin = formattedDate(ffin);
                            
                            var descripcion = $("#descripcion").val();
                            var cuit = $("#cuit").val();
                            //if (cuit=='__-________-_') cuit='';
                            var montom = $("#montom").val();
                            var prov = $("#provinciah").val();
                            var loca = $("#localidadh").val();
                            
                            var fidcontables = $('#fidcontables').val();
                            
                            //adjuntos
                            var _array_uploads = [];
                            $( ".lista_uploads li" ).each(function( index ) {
                                var nombre = $(this).data('nom');
                                var nombre_tmp = $(this).data('tmp');
                                _array_uploads.push({nombre:nombre,nombre_tmp:nombre_tmp});
                            });             
                            
                            //bancos
                            var griddata = $('#jqxgridbancos').jqxGrid('getdatainformation');
                            var _arr_bancos = [];
                            for (var i = 0; i < griddata.rowscount; i++)
                                _arr_bancos.push($('#jqxgridbancos').jqxGrid('getrenderedrowdata', i));
                            
                            //aportes
                            var griddata = $('#jqxgridaportes').jqxGrid('getdatainformation');
                            var _arr_aportes = [];
                            for (var i = 0; i < griddata.rowscount; i++)
                                _arr_aportes.push($('#jqxgridaportes').jqxGrid('getrenderedrowdata', i));
                            
                                                                     
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;

                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                nombre:nombre,
                                fecha_inicio:fini,
                                fecha_fin:ffin,
                                descripcion:descripcion,
                                cuit:cuit,
                                montomax:montom,
                                id_provincia:prov,
                                id_departamento:loca,
                                entidades:_array_entidades,
                                adjuntos:_array_uploads,
                                operatorias: _array_operatorias,
                                bancos:_arr_bancos,
                                aportes:_arr_aportes,
                                ID_CONTABLE:fidcontables
                            }       
                            $.ajax({
                                url : _fideicomiso.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            location.reload();
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
                        
                        agregarBancos();
                        agregarAportes();
                        
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
                _array_operatorias = [];
                
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
                    url : _fideicomiso.URL + "/x_getform_addentidad",
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
                                if(ui.index==4){
                                    $("#upload_file1").show();
                                }else{
                                    $("#upload_file1").hide();
                                }
                            }
                        });
                        
//                        $('#select-fid-contable').show();
//                        $('#lbl-fidcontables').show();
//                        $('#fidcontables').show();
                        
                        $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
                        $("input[type=file]").each(function(){
                        if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");}
                        });
                        
                        $("#nombre").focus();
                        //$(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                        //$(".chzn-select").chosen(); 
                        $(".chzn-select").not("#operatorias").not("#entidades_sel").chosen({width: "255px"});
                        $("#entidades_sel").chosen({width: "630px"});
                        $("#operatorias").chosen({width: "630px"});
                        
                        //$(".chzn-select").chosen({width: "255px"});
                        
                        $(".chzn-container-multi .chzn-choices").css('height','auto');
                        $("#btnBorrar").show();
                        
                        
                        //console.log('hasta acaaaaa');
                        //return false;
                        
                        
                        //$("#cuit").jqxMaskedInput({ mask: '##-########-#', width: 150, height: 22, theme: theme });
                        //$("#telefono").jqxMaskedInput({ mask: '(###)###-####', width: 150, height: 22, theme: theme });
                        $("#aporte_aporte").numeric({ negative: false });
                        
                        $("#fini").val( $("#finih").val().replace(/\//g,'') );
                                                                    
                        var cad = $("#val_fideicomisoh").val();
                        $("#label_action").html('Editar');
                        
                        var provh1 = $("#provinciah1").val();
                        $("#provincia").val(provh1).trigger("chosen:updated");
                        
                        
                        loadChild(provh1);
                        
                        var fcont = $("#fcontable").val();
                        $("#fidcontables").val(fcont).trigger("chosen:updated");
                        
                        loadChild(fcont);
                        
                        var loch1 = $("#localidadh1").val();
                        $("#subrubro").val(loch1).trigger("chosen:updated");
                        $(".suma_aportes").show();
                        
                        var finih = $("#finih").val();
                        var ffinh = $("#ffinh").val();
                        
                        var a_ent = $("#a_ent").val();
                        _array_entidades = javascript_array;
                                               
                       init_datepicker('#fini','-3','+0','0');
                       init_datepicker('#ffin','-3','+10','0',1);
                        
                        $("#fini").val(finih);
                        $("#ffin").val(ffinh);

                        $('#provincia').bind('change', function(event){
                            event.preventDefault();
                            $(this).validationEngine('validate');
                            if ($('#provincia').val()=='')
                                loadChild(0)

                            $('#provinciah').val($('#provincia').val());

                            var selected = $(this).find('option').eq(this.selectedIndex);

                            var connection = selected.data('connection');
                            selected.closest('#rubro li').nextAll().remove();
                            if(connection){
                                loadChild(connection);
                            }
                        });
                        
                        init_datepicker('#aporte_fecha','-3','+5','0',0);                        
                        $('#aporte_origen').bind('change', function(event){
                            
                            event.preventDefault();
                            //$(this).validationEngine('validate');
                            if ($('#aporte_origen').val()=='')
                                loadChildOrigen(0)

                            $('#origenh').val($('#aporte_origen').val());

                            var selected = $(this).find('option').eq(this.selectedIndex);

                            var connection = selected.data('connection');
                            selected.closest('#aporte_origen li').nextAll().remove();
                            if(connection){
                                loadChildOrigen(connection);
                            }
                        });
                        
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
                        
                        
                        
                        //cargar _array_operatorias
                        $("#tabs-2 .lista_ope li").each(function(){
                            var iid = $(this).data('identidad')
                            _array_operatorias.push(iid);
                        });
                        
                        
                        $('.lista_opciones.addope').on('click', function(event) {
                            event.preventDefault();
                            
                            var id = $("#operatorias").val() ;
                            var nom = $("#operatorias option:selected").html() ;
                            
                            //validar vacio
                            if(id=='' || id == 'Elegir Entidad'){
                                jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                                    
                                });
                                return false;
                            }
                            
                            var sw = 0;
                            //validar repetido
                            $( ".lista_ope li" ).each(function( index ) {
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
                            
                            $(".lista_ope").append('<li data-identidad="'+id+'">'+nom+'</li>');
                                                        
                            _array_operatorias.push(id);
                            
                            $('.lista_ope li').off().on('click', function(event){
                                event.preventDefault();
                                var pos = $(this).index();
                                _array_operatorias.splice(pos,1);
                                $(this).remove();
                            });
                        });
                        
                        $('.lista_ope li').off().on('click', function(event){
                            event.preventDefault();
                            var pos = $(this).index();
                            _array_operatorias.splice(pos,1);
                            $(this).remove();
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
                            var fini = $("#fini").val();
                            fini = formattedDate(fini);
                            var ffin = $("#ffin").val();
                            ffin = formattedDate(ffin);
                            var descripcion = $("#descripcion").val();
                            var cuit = $("#cuit").val();
                            //if (cuit=='__-________-_') cuit='';
                            var montom = $("#montom").val();
                            var prov = $("#provinciah").val();
                            var loca = $("#localidadh").val();
                            
                            var fidcontables = $('#fidcontables').val();
                                 
                            //adjuntos
                            var _array_uploads = [];
                            $( ".lista_uploads li" ).each(function( index ) {
                                var nombre = $(this).data('nom');
                                var nombre_tmp = $(this).data('tmp');
                                _array_uploads.push({nombre:nombre,nombre_tmp:nombre_tmp});
                            });   
                            
                            //bancos
                            var griddata = $('#jqxgridbancos').jqxGrid('getdatainformation');
                            var _arr_bancos = [];
                            for (var i = 0; i < griddata.rowscount; i++)
                                _arr_bancos.push($('#jqxgridbancos').jqxGrid('getrenderedrowdata', i));

                            //aportes
                            //$("#jqxgridaportes").jqxGrid('updatebounddata');
                            var griddata = $('#jqxgridaportes').jqxGrid('getdatainformation');
                            var _arr_aportes = [];
                            for (var i = 0; i < griddata.rowscount; i++){
                                console.dir($('#jqxgridaportes').jqxGrid('getrenderedrowdata', i));
                                _arr_aportes.push( $('#jqxgridaportes').jqxGrid('getrenderedrowdata', i) );
                                
                            }
                            
                            console.dir( griddata );
                            console.dir( _arr_aportes );
                            
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;

                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                nombre:nombre,
                                fecha_inicio:fini,
                                fecha_fin:ffin,
                                descripcion:descripcion,
                                cuit:cuit,
                                montomax:montom,
                                id_provincia:prov,
                                id_departamento:loca,
                                entidades:_array_entidades,
                                operatorias: _array_operatorias,
                                bancos:_arr_bancos,
                                aportes:_arr_aportes,
                                adjuntos:_array_uploads,
                                ID_CONTABLE:fidcontables
                            }
                           
                            $.ajax({
                                url : _fideicomiso.URL + "/x_sendobj",
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
                        
                        $('.lista_uploads li').off().on('click', function(event) {
                            event.preventDefault();
                            var $this = $(this);
                            var idfid = $this.data('identidad');
                            var ruta = $this.data('ruta');

                            jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                                if(r==true){
                                    //borrar archivo en la bd y fisicamente
                                    $.ajax({
                                        url : _fideicomiso.URL + "/x_delupload",
                                        data : {
                                            idfid:idfid,
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
                        
                    agregarBancos(_array_bancos_e);
                    agregarAportes(_array_aportes_e);
                    
                    $("#aporte_origen").chosen({width: "255px"});
                    $("#aporte_nombre").chosen({width: "255px"});
                    $("#operatorias").chosen({width: "255px"});
                    
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
                            url : _fideicomiso.URL + "/x_delobj",
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
                url = "backend/carpeta/operatorias";
                jConfirm('Esta seguro de ir a Operatorias?. Los datos sin guardar, se perderán.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                        $(location).attr('href',url);
                    }else{
                        $.unblockUI();
                    }
                });
            }else if(top=='lis'){
                $.unblockUI();
                $('#btnClear').trigger('click');
                $("#jqxgrid").jqxGrid('updatebounddata');
                _array_entidades = {};
//                $('#select-fid-contable').hide();
//                $('#lbl-fidcontables').hide();
//                $('#fidcontables').hide();
                $("#jqxgrid").show();
                $("#wpopup").html('');
            }else if(top=='exp'){
                // exportar
                $.ajax({
                    url : _fideicomiso.URL + "/x_getexportar",
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
                                    $("#jqxgrid").jqxGrid('exportdata', 'xls', 'fid_'+fGetNumUnico(), true, null, false, url_e);
                                    break;
                                case 'csv':
                                    $("#jqxgrid").jqxGrid('exportdata', 'csv', 'fid_'+fGetNumUnico(), true, null, false, url_e);
                                    break;
                                case 'htm':
                                    $("#jqxgrid").jqxGrid('exportdata', 'html', 'fid_'+fGetNumUnico(), true, null, false, url_e);
                                    break;
                                case 'xml':
                                    $("#jqxgrid").jqxGrid('exportdata', 'xml', 'fid_'+fGetNumUnico(), true, null, false, url_e);
                            }
                        });
                            
                    }
                });
            }
    });
            
    var source ={
            datatype: "json",
            datafields: [{ name: 'NOMBRE' },{ name: 'DESCRIPCION' },{name: 'CUIT', type: 'string'},{name: 'MONTOMAX', type: 'string'},{name: 'FECHA_INICIO'},{name: 'FECHA_FIN'},{name: 'PROVINCIA'},{name: 'DEPARTAMENTO'},{name: 'ID'}],
            url: _fideicomiso.URL + '/x_get_info_grid',
            deleterow: function (rowid, commit) {
                commit(true);
            }
    };
			
    $("#jqxgrid").jqxGrid(
    {
        width: '98%',
        groupable:true,
        source: source,
        theme: 'energyblue',
        sortable: true,
        ready: function () {
            $("#jqxgrid").jqxGrid('hidecolumn', 'ID');
        },
        columnsresize: true,
        filterable: true,
        showfilterrow: true,
        localization: getLocalization(),
        columns: [
            { text: 'NOMBRE', datafield: 'NOMBRE', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'DESCRIPCION', datafield: 'DESCRIPCION', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'CUIT', datafield: 'CUIT', width: '20%',columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'MONTO', datafield: 'MONTOMAX', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'FECHA INICIO', datafield: 'FECHA_INICIO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'FECHA FIN', datafield: 'FECHA_FIN', width: '20%' , filterable : false },
            { text: 'PROVINCIA', datafield: 'PROVINCIA', width: '20%' , filterable : false },
            { text: 'ID', datafield: 'ID', width: '0%' }
        ]
    });
    
});



function loadChildOrigen(val){
    		
    if(working_or==false){
        working_or = true;
        $.ajax({
              url : _fideicomiso.URL + "/x_getnombreorigen",
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
                $('#div_nombreorigen').html('<select class="chzn-select medium-select select" id="aporte_nombre">'+ options +'</select>');
                $('#aporte_nombre').on('change', function(event) {
                    event.preventDefault();
                    $('#nombreh').val($('#aporte_nombre').val());
                });
                var selects = $('#div_nombreorigen').find('select');
                selects.chosen();
                working_or = false;
              }
          });
    }
    
}


function loadChild(val){
    		
    if(working==false){
        working = true;
        $.ajax({
              url : _fideicomiso.URL + "/x_getlocalidad",
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
              url : _fideicomiso.URL + "/x_getentidad_select",
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

function agregarAportes(_arr_aportes){
    
    //console.dir( _arr_aportes );
    
    _arr_aportes || ( _arr_aportes = [] );
    
    var source ={
            datatype: "json",
            datafields: [
                { name: 'ORIGEN' },
                { name: 'ORIGEN_DAT' },
                { name: 'NOMBRE_NUM' },
                { name: 'NOMBRE' },
                { name: 'NOMBREA' },
                { name: 'NOMBRE_DAT' },
                { name: 'APORTE', type: 'number'},
                { name: 'FECHA'},
                { name: 'ID'},
                { name: 'OBS1'}
            ],
            url: _fideicomiso.URL + '/x_get_info_bancos',
            deleterow: function (rowid, commit) {
                commit(true);
            }
    };
			
    $("#jqxgridaportes").jqxGrid(
    {
        width: '98%',
        source: source,
        theme: 'energyblue',
        ready: function () {
            var total = 0;
            $("#jqxgridaportes").jqxGrid('hidecolumn', 'ID');
            $("#jqxgridaportes").jqxGrid('hidecolumn', 'NOMBRE_NUM');
            $("#jqxgridaportes").jqxGrid('hidecolumn', 'ORIGEN');
            $("#jqxgridaportes").jqxGrid('hidecolumn', 'OBS1');
            $("#jqxgridaportes").jqxGrid('hidecolumn', 'NOMBREA');
            $("#jqxgridaportes").jqxGrid('hidecolumn', 'NOMBRE');
            
            if(_arr_aportes.length>0){
                //colocar
                 $.each(_arr_aportes,function(k,v){
                        var data = {
                            'ORIGEN_DAT':v.ORIGEN_DAT,
                            'NOMBRE_DAT':v.NOMBRE_DAT,
                            'ORIGEN':v.ORIGEN,
                            'NOMBRE':v.NOMBRE_NUM,
                            'NOMBREA':v.NOMBREA,
                            'APORTE':v.APORTE,
                            'FECHA':v.FECHA,
                            'OBSERVACION':v.OBS1,
                            'ID':'DDDDDDD',
                            'uid':1
                        }
                        total = total + parseFloat(v.APORTE);
                        
                        var commit = $("#jqxgridaportes").jqxGrid('addrow', null, data);
                        $('#jqxgridaportes').jqxGrid('selectrow', data.uid );
                        var selectedrowindex = $("#jqxgridaportes").jqxGrid('getselectedrowindex');

                    });
                    
                    $("#suma_aporte").html(precise_round(total,2));
            }
        
        },
        columnsresize: true,
        localization: getLocalization(),
        showstatusbar: true,
        renderstatusbar: function (statusbar) {
            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
            var deleteButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: 2px;' src='general/css/images/delete.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Borrar</span></div>");
            container.append(deleteButton);
            statusbar.append(container);
            deleteButton.jqxButton({ theme: theme, width: 65, height: 20 });
            deleteButton.click(function (event) {
                var selectedrowindex = $("#jqxgridaportes").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgridaportes").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex<rowscount){
                
                    jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                        if(r==true){

                            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                var id = $("#jqxgridaportes").jqxGrid('getrowid', selectedrowindex);
                                $("#jqxgridaportes").jqxGrid('deleterow', id);
                            }
                            //actualizar suma
                            var griddata = $('#jqxgridaportes').jqxGrid('getdatainformation');
                            var _arr_aportes_tmp = [];
                            for (var i = 0; i < griddata.rowscount; i++)
                                _arr_aportes_tmp.push($('#jqxgridaportes').jqxGrid('getrenderedrowdata', i));
                            var total=0;
                            if (griddata.rowscount==0){
                                $("#suma_aporte").html('');
                                $(".suma_aportes").hide();
                            }else{
                                if(_arr_aportes_tmp.length>0){
                                    //colocar
                                    $.each(_arr_aportes_tmp,function(k,v){
                                        total = total + parseFloat(v.APORTE);
                                    });
                                    $(".suma_aportes").show();
                                    $("#suma_aporte").html(precise_round(total,2));
                                }
                            }
                            
                        }
                    });
                }else{
                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){

                    });
                    return false;
                }
            });
        },
        columns: [
                { text: 'ORIGEN1', datafield: 'ORIGEN', width: '0%' },
                { text: 'ORIGEN2', datafield: 'ORIGEN_DAT', width: '30%' },
                { text: 'NOMBRE3', datafield: 'NOMBRE_DAT', width: '30%' },
                { text: 'NOMBREA4', datafield: 'NOMBREA', width: '0%' },
                { text: 'FECHA5', datafield: 'FECHA', width: '20%' },
                { text: 'ID6', datafield: 'ID', width: '0%' },
                { text: 'APORTE7', datafield: 'APORTE', width: '20%' , cellsalign: 'right'},
                { text: 'NOMBRE8', datafield: 'NOMBRE_NUM', width: '0%' },
                { text: 'NOMBRE9', datafield: 'NOMBRE', width: '0%' },
                { text: 'OBSERVACION11', datafield: 'OBS1', width: '0%' }
        ]
    });
        
    $("#add_aporte").off().on('click', function () {
        
        if ( $(".form_aportes input#origen").val()=='' || $(".form_aportes input#aporte_nombre").val()==''
          || $(".form_aportes input#aporte_aporte").val()=='' || $(".form_aportes input#aporte_obs").val()==''
           ){
                jAlert('Todos los campos son obligatorios.', $.ucwords(_etiqueta_modulo),function(){
                    $(".form_aportes input#aporte_origen").first().select();
                });
                return false;
            }
               

        //if ($(".form_aportes input").val()!=''){

            var aporte_origen_dat = $("#aporte_origen option:selected").html();
            var aporte_nombre_dat = $("#aporte_nombre option:selected").html();
            var aporte_origen = $("#aporte_origen").val();
            var aporte_nombre = $("#aporte_nombre").val();
            var aporte_aporte = $("#aporte_aporte").val();
            var aporte_obs = $("#aporte_obs").val();
            //var cur_fec = getDatejs();
            var cur_fec = $("#aporte_fecha").val();
            
            var data = {
                'ORIGEN':aporte_origen,
                'NOMBRE':aporte_nombre,
                'NOMBREA':aporte_nombre,
                'ORIGEN_DAT':aporte_origen_dat,
                'NOMBRE_DAT':aporte_nombre_dat,
                'APORTE':aporte_aporte,
                'FECHA':cur_fec,
                'ID':'DDDDDDD',
                'OBSERVACION':aporte_obs,
                'uid':1
            }

            var commit = $("#jqxgridaportes").jqxGrid('addrow', null, data);
            $('#jqxgridaportes').jqxGrid('selectrow', data.uid);
            var selectedrowindex = $("#jqxgridaportes").jqxGrid('getselectedrowindex');
            
            //actualizar suma
            var griddata = $('#jqxgridaportes').jqxGrid('getdatainformation');
            var _arr_aportes_tmp = [];
            for (var i = 0; i < griddata.rowscount; i++)
                _arr_aportes_tmp.push($('#jqxgridaportes').jqxGrid('getrenderedrowdata', i));
            var total=0;
            if (griddata.rowscount==0){
                $("#suma_aporte").html('');
                $(".suma_aportes").hide();
            }else{
                if(_arr_aportes_tmp.length>0){
                    //colocar
                    $.each(_arr_aportes_tmp,function(k,v){
                        total = total + parseFloat(v.APORTE);
                    });
                    $(".suma_aportes").show();
                    $("#suma_aporte").html(precise_round(total,2));
                }
            }
            
            $(".form_aportes input").val('');
            $(".form_aportes input").first().focus();
        //}

    });
    
    //$("#aporte_origen").chosen({width: "255px"});
    //$("#aporte_nombre").chosen({width: "255px"});
    
}

function agregarBancos(_arr_bancos){
    
    _arr_bancos || ( _arr_bancos = [] );
    
    var source ={
            datatype: "json",
            datafields: [{ name: 'BANCO' },{ name: 'TITULAR' },{name: 'CUIT'},{name: 'NROCUENTA'},{name: 'CBU'},{name: 'ID'}],
            url: _fideicomiso.URL + '/x_get_info_bancos',
            deleterow: function (rowid, commit) {
                commit(true);
            }
    };
			
    $("#jqxgridbancos").jqxGrid(
    {
        width: '98%',
        source: source,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgridbancos").jqxGrid('hidecolumn', 'ID');
            if(_arr_bancos.length>0){
                //colocar
                 $.each(_arr_bancos,function(k,v){
                        var data = {
                            'BANCO':v.BANCO,
                            'TITULAR':v.TITULAR,
                            'CUIT':v.CUIT,
                            'NROCUENTA':v.NROCUENTA,
                            'ID':'DDDDDDD',
                            'CBU':v.CBU,
                            'uid':1
                        }
                        var commit = $("#jqxgridbancos").jqxGrid('addrow', null, data);
                        $('#jqxgridbancos').jqxGrid('selectrow', data.uid);
                        var selectedrowindex = $("#jqxgridbancos").jqxGrid('getselectedrowindex');

                    });
                
            }
            
        },
        columnsresize: true,
        localization: getLocalization(),
        showstatusbar: true,
        renderstatusbar: function (statusbar) {
            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
            var deleteButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: 2px;' src='general/css/images/delete.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Borrar</span></div>");
            container.append(deleteButton);
            statusbar.append(container);
            deleteButton.jqxButton({ theme: theme, width: 65, height: 20 });
            deleteButton.click(function (event) {
                var selectedrowindex = $("#jqxgridbancos").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgridbancos").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex<rowscount){
                
                    jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                        if(r==true){

                            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                var id = $("#jqxgridbancos").jqxGrid('getrowid', selectedrowindex);
                                $("#jqxgridbancos").jqxGrid('deleterow', id);
                            }
                        }
                    });
                }else{
                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){

                    });
                    return false;
                }
            });
        },
        columns: [
                { text: 'BANCO', datafield: 'BANCO', width: '20%' },
                { text: 'FIRMANTES', datafield: 'TITULAR', width: '20%' },
                { text: 'CUIT', datafield: 'CUIT', width: '20%' },
                { text: 'NROCUENTA', datafield: 'NROCUENTA', width: '20%' },
                { text: 'ID', datafield: 'ID', width: '0%' },
                { text: 'CBU', datafield: 'CBU', width: '20%' }
        ]
    });
        
    $("#add_banco").off().on('click', function () {
        if ( $(".form_cuentbanco input#banco_banco").val()=='' || $(".form_cuentbanco input#banco_cuit").val()==''
          || $(".form_cuentbanco input#banco_cbu").val()=='' || $(".form_cuentbanco input#banco_titular").val()==''
          || $(".form_cuentbanco input#banco_nrocuenta").val()==''
           ){
                jAlert('Todos los campos son obligatorios.', $.ucwords(_etiqueta_modulo),function(){
                    $(".form_cuentbanco input#banco_banco").first().select();
                });
                return false;
            }
            
                
        if ($(".form_cuentbanco input").val()!=''){
        
            var banco_banco = $("#banco_banco").val();
            var banco_titular = $("#banco_titular").val();
            var banco_cuit = $("#banco_cuit").val();
            var banco_nrocuenta = $("#banco_nrocuenta").val();
            var banco_cbu = $("#banco_cbu").val();

            var data = {
                'BANCO':banco_banco,
                'TITULAR':banco_titular,
                'CUIT':banco_cuit,
                'NROCUENTA':banco_nrocuenta,
                'ID':'DDDDDDD',
                'CBU':banco_cbu,
                'uid':1
            }       

            var commit = $("#jqxgridbancos").jqxGrid('addrow', null, data);
            $('#jqxgridbancos').jqxGrid('selectrow', data.uid);
            var selectedrowindex = $("#jqxgridbancos").jqxGrid('getselectedrowindex');
            //$('#jqxgridbancos').jqxGrid( { editable: true} );
            //var editable = $("#jqxgridbancos").jqxGrid('begincelledit', selectedrowindex, "BANCO");
            $(".form_cuentbanco input").val('');
            $(".form_cuentbanco input").first().focus();
        }

    });
}

function post_upload(nombre,nombre_tmp){
    
    jAlert('Archivo cargado correctamente. ' + nombre, $.ucwords(_etiqueta_modulo),function(){
        //agregarlo a la lista
        $(".lista_uploads").append('<li data-nom="'+nombre+'" data-tmp="'+nombre_tmp+'">'+nombre+'</li>');
        $('.lista_uploads li').last().off().on('click', function(event){
            event.preventDefault();
            $this = $(this);
            var ruta = $(this).data('tmp');
            //x_borrar_file
            $.ajax({
                url : _fideicomiso.URL + "/x_borrar_file",
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
