var mydata;
var working = false;
var working1 = false;
var _more;

function loadChild(val){
    		
    if(working==false){
        working = true;
        $.ajax({
              url : _clientes.URL + "/x_getlocalidad",
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
                    options = '<option value="">'+r.defaultText+'</option>'+options;
                }
                $('#div_subrubro').html('<select class="chzn-select medium-select select" id="subrubro">'+ options +'</select>');
                $('#localidadh, #loc').val($('#subrubro').val());
                $('#subrubro').on('change', function(event) {
                    event.preventDefault();
                    $('#localidadh, #loc').val($(this).val());
                });
                var selects = $('#div_subrubro').find('select');
                selects.chosen();
                working = false;
              }
          });
    }
    
}


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
                $.ajax({
                    url : _clientes.URL + "/x_getform_addentidad",
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $.fancybox(
                            data,
                            {
                                'padding'   :  35,
                                'autoScale' :true,
                                'height' : 900,
                                'scrolling' : 'yes'
                            }
                        );
                        
                        $("#cuit").numeric({ negative: false, decimal: false });
                            
                        _more = $(".group .elem_group").eq(0).clone();
                            
                        $(".fancybox-inner").css('overflow-x','hidden');
                        $(".fancybox-inner").css('overflow-y','auto');
                        $("#provincia").chosen({ disable_search_threshold: 5 }); 
                        $("#condicioniva").chosen({ disable_search_threshold: 5 });
                        $("#condicioniibb").chosen({ disable_search_threshold: 5 });
                        $("#tipobeneficiario").chosen({ disable_search_threshold: 5 });
                        init_datepicker('#falta','-3','+0','1',0);
                        /*
                        $("#cuit").jqxMaskedInput({ mask: '##-########-#', width: 150, height: 22, theme: theme });
                        $("#cuit").css('height','28px').css('width','255px');
                        $("#tel").jqxMaskedInput({ mask: '(###)###-####', width: 150, height: 22, theme: theme });
                        $("#tel").css('height','28px').css('width','255px');
                        $("#cbu").jqxMaskedInput({ mask: '########-##############', width: 150, height: 22, theme: theme });
                        $("#cbu").css('height','28px').css('width','255px');
                            */
                        $('#btnClear').on('click', function(event) {
                            $("#customForm").validationEngine('hideAll');
                            event.preventDefault();
                            $("#frmagregar :text").val("");
                            $("#frmagregar :text").first().focus();
                            $("#idh").val("");
                            $("#provinciah").val("");
                            $("#localidadh").val("");
                            $("#obs").val("");
                            $("#provincia").val(0).trigger("chosen:updated");
                            loadChild(0)
                            $("#condicioniva").val(0).trigger("chosen:updated");
                            $("#condicioniibb").val(0).trigger("chosen:updated");
                            $("#tipobeneficiario").val(0).trigger("chosen:updated");
                            if($("#label_action").html()=='Editar'){
                              $("#label_action").html("Agregar");
                            }
                            $("#frmagregar :text").removeClass('error');
                            $("#nom").select();

                        });
                        
                        $('.div_pidepass').on('click', function(event) {
                            
                            mostrar_pwd();
                            
                        });
                        
                        $('#raz').keyup(function() {
                            $(this).validationEngine('validate');
                        });
                        
                        $('.group_more').click(function(e) {
                           var $actual = _more.clone();
                           $(".group").append($actual);
                           $('.elem_cerrar').off().click(function(e) {
                                $(this).fadeOut(400, function() {
                                    $(this).parent().remove();
                                });
                           });
                        });
                        $('.elem_cerrar').off().click(function(e) {
                            $(this).fadeOut(400, function() {
                                $(this).parent().remove();
                            });
                        });
                        
                        
                        $('#send').on('click', function(event) {
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                            
                            event.preventDefault();
                            var id = $("#idh").val();
                            var prov = $("#provinciah").val();
                            var loca = $("#localidadh").val();
                            var condicioniibb = $("#condicioniibb").val();
                            var condicioniva = $("#condicioniva").val();
                            var falta = $("#falta").val();
                            var raz = $("#raz").val();
                            var dir = $("#dir").val();
                            var tel = $("#tel").val();
                            var tel2 = $("#tel2").val();
                            var tel3 = $("#tel3").val();
                            var con = $("#con").val();
                            var cuit = $("#cuit").val();
                            var insiibb = $("#insiibb").val();
                            var insinv = $("#insinv").val();
                            var ema = $("#ema").val();
                            var obs = $("#obs").val();
                            var cbu = $("#cbu").val();
                            
                            $.ajax({
                                url : _clientes.URL + "/x_verificarcuit",
                                data : {
                                    cuit:cuit
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
                                    
                                    console.dir(data.length);
                                    
                                    if(data.length>0){
                                        $.unblockUI();
                                        jAlert('Este cuit ('+cuit+') ya existe en la base de datos.', $.ucwords(_etiqueta_modulo),function(){
                                            $("#cuit").focus();
                                        });
                                    }
                                    else{
                                        /*contactos*/
                                        var arr_contactos = [];
                                        $( ".group .elem_group" ).each(function() {

                                            var contacto    = $(this).find('#con').val();
                                            var telefono    = $(this).find('#tel').val();
                                            var telefono2    = $(this).find('#tel2').val();
                                            var telefono3    = $(this).find('#tel3').val();
                                            var email       = $(this).find('#ema').val();
                                            arr_contactos.push({con:contacto,tel:telefono,tel2:telefono2,tel3:telefono3,ema:email});
                                        });

                                       //if ( !$("#customForm").validationEngine('validate') )
                                         //   return false;

                                        iid = id ? id:0;
                                        obj = {
                                            id:iid,
                                            ID_PROVINCIA:prov,
                                            ID_DEPARTAMENTO:loca,
                                            ID_CONDICION_IIBB:condicioniibb,
                                            ID_CONDICION_IVA:condicioniva,
                                            ID_INV:insinv,
                                            FECHA_ALTA:falta,
                                            DIRECCION:dir,
                                            RAZON_SOCIAL:raz,
                                            TELEFONO:tel,
                                            TEL_CEL:tel2,
                                            TEL_TRAB:tel3,
                                            CONTACTO:con,
                                            CUIT:cuit,
                                            CORREO:ema,
                                            OBSERVACION:obs,
                                            INSCRIPCION_IIBB:insiibb,
                                            CBU:cbu,
                                            contactos:arr_contactos
                                        }

                                        $.ajax({
                                            url : _clientes.URL + "/x_sendobj",
                                            data : {
                                                obj:obj
                                            },
                                            dataType : "json",
                                            type : "post",
                                            success : function(data){
                                                $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });

                                                if(data.result>0){
                                                    setTimeout(function() {
                                                        $.unblockUI({
                                                            onUnblock: function(){ 
                                                                jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                                                    $('#btnClear').trigger('click');   
                                                                    $("#jqxgrid").jqxGrid('updatebounddata');
                                                                    //actualizar contactos
                                                                    var $nuevo = _more.clone();
                                                                    $(".group").html('').append($nuevo);;
                                                                });
                                                            } 
                                                        }); 
                                                    }, 500);
                                                }
                                                else{
                                                  jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                                      $.unblockUI();
                                                  });
                                                }
                                            }
                                        });
                                        return false;
                                    }
                                }
                            });
                            
                            return false;
                            
                            
                            
                            
                            
                        });
                                                
                        loadChild(0);

                        $('#provincia').bind('change', function(event) {
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
                        
                                                
                        $('#send').on('click', function(event) {

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
                    url : _clientes.URL + "/x_getform_addentidad",
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
                                'scrolling' : 'yes'
                            }
                        );
                        _more = $(".group .elem_group").eq(0).clone();
                        _more.find('input').val('');
                        
                        $('.group_more').click(function(e) {
                           var $actual = _more.clone();
                           $(".group").append($actual);
                           $('.elem_cerrar').off().click(function(e) {
                                $(this).fadeOut(400, function() {
                                    $(this).parent().remove();
                                });
                           });
                        });
                        
                        $('.elem_cerrar').off().click(function(e) {
                            $(this).fadeOut(400, function() {
                                $(this).parent().remove();
                            });
                        });
                                                
                        $(".fancybox-inner").css('overflow-x','hidden');
                        $(".fancybox-inner").css('overflow-y','auto');
                        
                        init_datepicker('#falta','-3','+0','1',0);
                        var faltah = $("#faltah").val();
                        $("#falta").val(faltah);
                        
                        
                        $("#provincia").chosen({ disable_search_threshold: 5 }); 
                        $("#condicioniva").chosen({ disable_search_threshold: 5 });
                        $("#condicioniibb").chosen({ disable_search_threshold: 5 }); 
                        $("#tipobeneficiario").chosen({ disable_search_threshold: 5 });

                        $("#condicioniva").val($("#civae").val()).trigger("chosen:updated");
                        $("#condicioniibb").val($("#cibbe").val()).trigger("chosen:updated");
                        $("#provincia").val($("#prove").val()).trigger("chosen:updated");
                        loadChild($("#prove").val());
                        $("#subrubro").val($("#locae").val()).trigger("chosen:updated");
                        $("#tipobeneficiario").val($("#id_tipoe").val()).trigger("chosen:updated");
                        
                        
                        $("#provinciah").val($("#prove").val());
                        $("#localidadh,#loc").val($("#locae").val());
                        
                        $('#provincia').bind('change', function(event) {
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
                        
                        $('#btnBorrar').on('click', function(event) {
                            event.preventDefault();
                            var iddel = $("#idh").val();
                                                     
                            jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                                if(r==true){
                                    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
                                    //eliminar
                                    $.ajax({
                                        url : _clientes.URL + "/x_delobj",
                                        data : {
                                            id : iddel
                                        },
                                        type : "post",
                                        success : function(data){
                                                
                                                console.log('data::');
                                                console.dir(data);

                                              if(data==1){
                                                  $.fancybox.close();
                                                  $("#jqxgrid").jqxGrid('updatebounddata');
                                                  $.unblockUI();
                                              }if(data=='-2'){
                                                  jAlert('El cliente tiene dependencias (Carpetas, Creditos, Notas u Requerimientos). No puede ser borrado.', $.ucwords(_etiqueta_modulo),function(){
                                                    $.unblockUI();
                                                });
                                              }else{
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
                            $("#provinciah").val("");
                            $("#localidadh").val("");
                            $("#obs").val("");
                            $("#provincia").val(0).trigger("chosen:updated");
                            loadChild(0)
                            $("#condicioniva").val(0).trigger("chosen:updated");
                            $("#condicioniibb").val(0).trigger("chosen:updated");
                            $("#tipobeneficiario").val(0).trigger("chosen:updated");
                            if($("#label_action").html()=='Editar'){
                              $("#label_action").html("Agregar");
                            }
                            $("#frmagregar :text").removeClass('error');
                            $("#nom").select();

                        });
                       
                        $('#send').on('click', function(event) {
                                                      
                            e.preventDefault();
                           
                            var id = $("#idh").val();
                            var prov = $("#provinciah").val();
                            var loca = $("#localidadh").val();
                            var condicioniibb = $("#condicioniibb").val();
                            var condicioniva = $("#condicioniva").val();
                            var tipobeneficiario = $("#tipobeneficiario").val();
                            var falta = $("#falta").val();
                            var raz = $("#raz").val();
                            var dir = $("#dir").val();
                            var tel = $("#tel").val();
                            var tel2 = $("#tel2").val();
                            var tel3 = $("#tel3").val();
                            var con = $("#con").val();
                            var cuit = $("#cuit").val();
                            var insiibb = $("#insiibb").val();
                            var ema = $("#ema").val();
                            var obs = $("#obs").val();
                            var cbu = $("#cbu").val();
                            
                            /*contactos*/
                            var arr_contactos = [];
                            $( ".group .elem_group" ).each(function() {
                                var contacto    = $(this).find('#con').val();
                                var telefono    = $(this).find('#tel').val();
                                var telefono2    = $(this).find('#tel2').val();
                                var telefono3    = $(this).find('#tel3').val();
                                var email       = $(this).find('#ema').val();
                                arr_contactos.push({con:contacto,tel:telefono,tel2:telefono2,tel3:telefono3,ema:email});
                            });     

                           if ( !$("#customForm").validationEngine('validate') )
                                return false;

                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                ID_PROVINCIA:prov,
                                ID_DEPARTAMENTO:loca,
                                ID_CONDICION_IIBB:condicioniibb,
                                ID_CONDICION_IVA:condicioniva,
                                ID_TIPO:tipobeneficiario,
                                FECHA_ALTA:falta,
                                DIRECCION:dir,
                                RAZON_SOCIAL:raz,
                                TELEFONO:tel,
                                TEL_CEL:tel2,
                                TEL_TRAB:tel3,
                                CONTACTO:con,
                                CUIT:cuit,
                                CORREO:ema,
                                OBSERVACION:obs,
                                INSCRIPCION_IIBB:insiibb,
                                CBU:cbu,
                                contactos:arr_contactos
                            }                                                  
                                                  
                            $.ajax({
                                url : _clientes.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });

                                    if(data.result>0){
                                        setTimeout(function() {
                                            $.unblockUI({
                                                onUnblock: function(){ 
                                                    jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                        $.fancybox.close();
                                                    });
                                                } 
                                            }); 
                                        }, 500);
                                    }
                                    else{
                                      jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                          $.unblockUI();
                                      });
                                    }                                    
                                    return false;
                                }
                            });
                            return false;
                        });
                        
                        if(ver!=-1){
                            $(".btns").html('').hide();
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
                            url : _clientes.URL + "/x_delobj",
                            data : {
                                id:mydata.id
                            },
                            dataType : "json",
                            type : "post",
                            success : function(data){
                                if(data>0){
                                    if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                        var id = $("#jqxgrid").jqxGrid('getrowid', selectedrowindex);
                                        var commit = $("#jqxgrid").jqxGrid('deleterow', id);
                                    }
                                }if(data=='-2'){
                                    jAlert('El cliente tiene dependencias (Carpetas, Creditos, Notas u Requerimientos). No puede ser borrado.', $.ucwords(_etiqueta_modulo),function(){
                                        $.unblockUI();
                                    });
                                }else{
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
                $('#jqxlistbox').slideToggle('slow', function() {});
            }else if(top=='exp'){
                // exportar
                $.ajax({
                    url : _clientes.URL + "/x_getexportar",
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
                                    $("#jqxgrid").jqxGrid('exportdata', 'xls', 'cli_'+fGetNumUnico(), true, null, false, url_e);
                                    break;
                                case 'csv':
                                    $("#jqxgrid").jqxGrid('exportdata', 'csv', 'cli_'+fGetNumUnico(), true, null, false, url_e);
                                    break;
                                case 'htm':
                                    $("#jqxgrid").jqxGrid('exportdata', 'html','cli_'+fGetNumUnico(), true, null, false, url_e);
                                    break;
                                case 'xml':
                                    $("#jqxgrid").jqxGrid('exportdata', 'xml', 'cli_'+fGetNumUnico(), true, null, false, url_e);
                            }
                        });
                            
                    }
                });
            }
        
    });
    
            
    var source ={
        datatype: "json",
        datafields: [
            { name: 'id' },
            { name: 'razon_social' , type: 'string'},
            { name: 'DIRECCION' , type: 'string'},
            { name: 'PROVINCIA' , type: 'string'},
            { name: 'LOCALIDAD' , type: 'string'},
            { name: 'TELEFONO' , type: 'string'},
            { name: 'CONTACTO' , type: 'string'},
            { name: 'CUIT' , type: 'string'},
            { name: 'CBU' , type: 'string'},
            { name: 'CORREO' , type: 'string'},
            { name: 'ID_INV' , type: 'string'},
            { name: 'INSCRIPCION_IIBB' , type: 'string'}
        ],
        //url: _clientes.URL + '/x_get_info_grid',
        url: 'general/extends/extra/clientes.php',
        data:{
            accion: "todosClientes"
        },
        async:false,
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };
    
    var addfilter = function () {
            var filtergroup = new $.jqx.filter();
            var filter_or_operator = 1;
            var filtervalue = '';
            var filtercondition = 'contains';
            var filter1 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
            filtervalue = '';
            filtercondition = 'starts_with';
            var filter2 = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
            filtergroup.addfilter(filter_or_operator, filter1);
            filtergroup.addfilter(filter_or_operator, filter2);
            $("#jqxgrid").jqxGrid('addfilter', 'razon_social', filtergroup);
            $("#jqxgrid").jqxGrid('applyfilters');
        }
               
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
        source: dataAdapter,
        showtoolbar: true,
        filterable: true,
        showfilterrow: true,
        sortable: true,
        theme: 'energyblue',
        ready: function () {
            //addfilter();
            $("#jqxgrid").jqxGrid('hidecolumn', 'id');
        },
        columnsresize: true,
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
        localization: getLocalization(),
        columns: [
            { text: 'RAZON SOCIAL', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with',  datafield: 'razon_social', width: '20%' },
            { text: 'PROVINCIA', datafield: 'PROVINCIA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with' },
            { text: 'LOCALIDAD', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with',  datafield: 'LOCALIDAD', width: '20%' },
            { text: 'DIRECCION', datafield: 'DIRECCION', width: '20%'},
            { text: 'CUIT', datafield: 'CUIT', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with'},
            { text: 'CBU', datafield: 'CBU', width: '20%', hidden : true, filterable : false },
            { text: 'CONTACTO', datafield: 'CONTACTO', width: '20%', hidden : true, filterable : false},
            { text: 'CORREO', datafield: 'CORREO', width: '20%', hidden : true, filterable : false },
            { text: 'TELEFONO', datafield: 'TELEFONO', width: '20%', hidden : true, filterable : false },
            { text: 'INSCRIPCION INV', datafield: 'ID_INV', width: '20%', hidden : true, filterable : false },
            { text: 'INSCRIPCCION IIBB', datafield: 'INSCRIPCION_IIBB', width: '20%', hidden : true, filterable : false }
        ]
    });
            
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
    $("#jqxlistbox").jqxListBox({ source: listSource, width: 300, height: 130, checkboxes: true });
    $("#jqxlistbox").on('checkChange', function (event) {
        if (event.args.checked) {
            $("#jqxgrid").jqxGrid('showcolumn', event.args.value);
        }
        else {
            $("#jqxgrid").jqxGrid('hidecolumn', event.args.value);
        }
    });
        
});


function mostrar_pwd(){
    $("#obs_cob").show();
    $("#obs_cob input").select();
    $("#obs_cob input").keyup(function( event ) {
        if ( event.which == 27 ) {
            salir_pwd();
        }
    });
}

function salir_pwd(){
    $("#obs_cob").hide();
}

function ingresar_pwd(){
    
    var passw = $("#clave_desb").val();
    var nivel = $("#clave_desb").data('nivel');
    
    if (working1==false){
        working1 = true;
        $.ajax({
            url : _clientes.URL + "/x_getclavenivel",
            data : {
                  passw : passw,
                  nivel : nivel
            },
            dataType: "json",
            type : "post",
            success : function(r){
                console.dir(r);
                if (r==1){
                    salir_pwd();
                    $(".div_pidepass").hide();
                }else{
                    jAlert('Clave incorrecta.', $.ucwords(_etiqueta_modulo),function(){
                        $("#clave_desb").select();
                    });
                }
                
                working1 = false;
            }
        });
    }
    
    
    
}