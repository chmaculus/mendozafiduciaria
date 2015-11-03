var mydata;
var id_edit;
var working = false;
var workincli = false;
var workingf = false;
var workinggar = false;
var cont_legales = 0;

var _array_chk = {};
var semmilla;

var checkboxHeight = "25";
var radioHeight = "25";
var selectWidth = "190";

var myfancy = 0;

function tooltip_evento(){
    
    $(".tb_cli").jqxTooltip({ content: '<b>Clientes</b><br/>Ir a clientes', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_ents").jqxTooltip({ content: '<b>Entidades</b><br/>Ir a entidades', position: 'bottom', name: 'movieTooltip', theme: theme });
   
    $(".tb_todas").jqxTooltip({ content: '<b>Todas las Carpetas</b>', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_miscar").jqxTooltip({ content: '<b>Mis Carpetas</b><br/>Son las Carpetas en las que tengo pendiente alguna acción', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_cart").jqxTooltip({ content: '<b>En Cartera</b><br/>Son las Carpetas que tengo a mi cargo', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_pend").jqxTooltip({ content: '<b>Pendientes</b><br/>Son las Carpetas que me las enviaron pero aun no las acepto', position: 'bottom', name: 'movieTooltip', theme: theme });
    $(".tb_autor").jqxTooltip({ content: '<b>Por Autorizar</b><br/>Son las Carpetas que me las enviaron para autorizarlas', position: 'bottom', name: 'movieTooltip', theme: theme });
   
   /*
    $(".tb_min").each(function( index ) {
        var tip = $(this).data('tip');
        $(this).jqxTooltip({ content: tip, position: 'mouse', name: 'movieTooltip', theme: theme });
    });
    */
}

$(document).ready(function(){
               
    tooltip_evento();
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
            
            if (top == 'desis'){
                
                mydata = '';
                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                    
                if ( mydata==null ){
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                
                var estado = 20;
                var msg_des = 'Esta seguro que desea desistir esta carpeta?.';
                if (mydata.ETAPA_ACTUAL=='Desistida'){
                    estado = 99;
                    msg_des = 'Esta seguro que desea reactivar esta carpeta?.';
                }
                jConfirm(msg_des, $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                        $.ajax({
                            url : _carpetas.URL + "/x_actualizar_operacion_desistir",
                            data : {
                                idope:mydata.IDOPE,
                                estado:estado // desistidox_actualizar_operacion_desistir
                            },
                            dataType : "json",
                            type : "post",
                            success : function(data){
                                jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                    $.fancybox.close();
                                    $("#jqxgrid").show();
                                    $("#jqxgrid").jqxGrid('updatebounddata');
                                    $("#wpopup").html('');
                                });

                            }
                        });
                    }
                });
                
                
                $.unblockUI();
            }else if(top=='exp'){
                
                if (_permiso_exportar==0){

                    jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                
                // exportar
                $.ajax({
                    url : _carpetas.URL + "/x_getexportar",
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
            }else if (top == 'lis_su_edihist'){
                
                mydata = '';
                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                    
                if ( mydata==null ){
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                if ( mydata.CARGAH == 0 ){
                    jAlert('Esta carpeta no es de Carga Historica.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                
                }
                var urlh = "backend/carpeta/carpetash/init/2/"+mydata.IDOPE; // add
                $(location).attr('href',urlh);
                $.unblockUI();
                
            }else if (top == 'lis_su_edihist1'){
                
                mydata = '';
                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                    
                if ( mydata==null ){
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                if ( mydata.CARGAH == 0 ){
                    jAlert('Esta carpeta no es de Carga Historica.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                
                }
                var urlh = "backend/carpeta/carpetash1/init/2/"+mydata.IDOPE; // add
                $(location).attr('href',urlh);
                $.unblockUI();
                
            }else if (top == 'lis_su_hist'){
                var urlh = "backend/carpeta/carpetash/init/1"; // add
                $(location).attr('href',urlh);
                $.unblockUI();
            }else if (top == 'lis_su_hist1'){
                var urlh = "backend/carpeta/carpetash1/init/1"; // add
                $(location).attr('href',urlh);
                $.unblockUI();
            }else if (top == 'lis_su_2'){
                if (_proceso_operatoria==2){
                    
                    jAlert('Esta funcionalidad no existe para Proceso Acotado.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                
                //obtener el activo y ultimo traza, y de esa info obtener el campo DESTINO
                $.ajax({

                    url : _carpetas.URL + "/x_get_id_accion_pendiente",
                    data : {
                        idope:_array_obj.ID
                    },
                    dataType : "json",
                    type : "post",
                    success : function(data){
                        
                        if (data>0){
                            notifMain(data);
                        }else{
                            //jalert
                            jAlert('Esta carpeta no tiene acciones pendientes.', $.ucwords(_etiqueta_modulo),function(){
                                $.unblockUI();
                            });
                            return false;
                        }
                    }
                });
                
            
            }else if (top == 'lis_su_1'){
                
                if (_proceso_operatoria==2){
                    
                    jAlert('Esta funcionalidad no existe para Proceso Acotado.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                

                $.ajax({
                        url : _carpetas.URL + "/x_getetapas_menor",
                        data : {
                            etapa:_array_obj.ID_ETAPA_ACTUAL,
                            idope: _array_obj.ID
                        },
                        dataType : "json",
                        type : "post",
                        success : function(data1){
                            var clase_asignar;
                            var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div>';
                            if(data1){
                                $.each(data1, function (index, value){
                                    clase_asignar = 'link_asignar_atras';
                                    if (value.IID!=_USUARIO_SESION_ACTUAL){
                                        cadhtml +=  '<div class="' + clase_asignar + ' x_area" data-etapa="'+value.ETAPA+'" data-iid="'+value.ID+'"><span>' + value.NOMBRE;
                                        cadhtml += '</span></div>';
                                    }
                                });
                            }

                            $.fancybox({
                                "content": cadhtml,
                                'padding'   :  35,
                                'autoScale' :true,
                                'height' : 900,
                                'scrolling' : 'no',
                                'beforeClose': function() {
                                    if (myfancy==1)
                                        regresar_a_listado();
                                }
                            });


                            $(".x_area").click(function(e1){
                                e1.preventDefault();
                                var tmpfancy = myfancy;
                                myfancy=0;

                                var iid = $(this).data('iid');
                                var apuesto_in = $(this).data('puesto_in');
                                var etapa_in = $(this).data('etapa');
                                apuesto_in = isNaN(apuesto_in)?'':apuesto_in;

                                $.ajax({
                                    url : _carpetas.URL + "/x_getetapas_menor2",
                                    data : {
                                        etapa: etapa_in,
                                        idope: _array_obj.ID
                                    },
                                    error: function (xhr, ajaxOptions, thrownError){
                                      alert(xhr.status);
                                      alert(thrownError);
                                    },
                                    dataType : "json",
                                    type : "post",
                                    success : function(datar){

                                        var clase_asignar;
                                        var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div> <div class="regresar_ar">Regresar</div>';
                                        if(datar){
                                            $.each(datar, function (index, value){

                                                clase_asignar = 'link_asignar_atras';
                                                //if (value.IID!=_USUARIO_SESION_ACTUAL){
                                                if (value.IID!=_USUARIO_SESION_ACTUAL || ( _array_obj.ID_ETAPA_ACTUAL==1 && _USER_ROL==10 && value.ETAPA=='3' )  ){

                                                    cadhtml +=  '<div class="' + clase_asignar + '" data-etapa="'+value.ETAPA_OLD+'" data-iid="'+value.IID+'"><span>' + value.NOMBRE + ' ' + value.APELLIDO+ ' ('+ value.AREA+ ' - ' + value.PUESTO+')';
                                                    cadhtml += '</span></div>';

                                                }

                                            });
                                        }

                                        $.fancybox({
                                            "content": cadhtml,
                                            'padding'   :  35,
                                            'autoScale' :true,
                                            'height' : 900,
                                            'scrolling' : 'no',
                                            'beforeClose': function() { 
                                                if (myfancy==1)
                                                    regresar_a_listado();
                                            }
                                        });
                                        if (tmpfancy==1)
                                            myfancy=1;
//                                        alert("Poner Regresar");

                                        $(".regresar_ar").click(function(e){
//                                            alert("Deberia regresar");
                                            e.preventDefault();
                                            $.fancybox.close();
                                            //$(".asignar").trigger('click');
                                            $("#barra_editar li.su_1").trigger('click');
                                        });

                                        $(".link_asignar_atras").click(function(e){

                                            e.preventDefault();
                                            var iid = $(this).data('iid');
                                            var new_etapa_data = $(this).data('etapa');

                                            var observacion;
                                            var estado;
                                            var descripcion;

                                            jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                                if(r==true){
                                                    var id_usuario = iid;
                                                    //nueva etapa

                                                    if (_array_obj.ID_PROCESO=='2'){
                                                       
                                                    }else{
                                                       
                                                    }
                                                    observacion='PARA ATRAS';
                                                    descripcion='SETEO A ETAPA Y USUARIO ANTERIOR'
                                                    estado='2'
                                                    
                                                    $.ajax({

                                                        url : _carpetas.URL + "/x_actualizar_operacion_atras",
                                                        data : {
                                                            OPERACION:_array_obj.ID,
                                                            ID_ETAPA_ACTUAL:new_etapa_data,
                                                            /*USUARIO:id_usuario,*/
                                                            CARTERADE:id_usuario,
                                                            OBSERVACION:observacion,
                                                            DESCRIPCION:descripcion,
                                                            ESTADO:estado,
                                                            ID_ETAPA_ORIGEN:0
                                                        },
                                                        dataType : "json",
                                                        type : "post",
                                                        success : function(data){
                                                            $.fancybox.close();
                                                            $("#jqxgrid").show();
                                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                                            $("#wpopup").html('');
                                                        }
                                                    });

                                                }
                                            });
                                        });

                                    }
                                });
                            });
                            return false;

                        }
                    });
                
                $.unblockUI();
                return false;
                
            }else if(top =='add'){
                
                actualizarBarraHerramientas();
                setMenuCarpeta('1','0');
                
                _monto_solicitado = 0;
                _proceso_operatoria = 0;
                if (_permiso_alta==0){

                    jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                
               _array_chk = {};
                               
                $.ajax({
                    url : _carpetas.URL + "/x_getform_addentidad",
                    type : "post",
                    success : function(data){
                        
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        init_chk(); // iniciar funciones de forms.js
                        var $items = $('#vtab>ul>li');
                        $items.click(function() {
                            var index = $items.index($(this));
                            var etapa_div = $('#vtab>div').eq(index).data('etapa');
                            if (etapa_div <= 1 ){
                                $items.removeClass('selected');
                                $(this).addClass('selected');
                                $('#vtab>div').hide().eq(index).show();
                            }
                            else{
                                if (_proceso_operatoria=='2'){
                                    if (index!='2'){
                                        jAlert('Esta etapa aun no está disponible.', $.ucwords(_etiqueta_modulo),function(){
                                        });
                                    }else{
                                        $items.removeClass('selected');
                                        $(this).addClass('selected');
                                        $('#vtab>div').hide().eq(index).show();
                                    }
                                    
                                    
                                }else{
                                    jAlert('Esta etapa aun no está disponible.', $.ucwords(_etiqueta_modulo),function(){
                                    });
                                }
                                
                                
                                
                            }
                        }).eq(0).click();
                        /*
                        $items.click(function() {
                            $items.removeClass('selected');
                            $(this).addClass('selected');
                            var index = $items.index($(this));
                            $('#vtab>div').hide().eq(index).show();
                        }).eq(0).click();
                        */
                       
                        $( "#tabs" ).tabs();
                        activar_acordeon('.grid-1');
                        
                        $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
                        $("input[type=file]").each(function(){
                            if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");}
                        });
                        
                        $("#cuit").jqxMaskedInput({ mask: '##-########-#', width: 150, height: 22, theme: theme });
                        $("#montom").numeric({ negative: false });
                        $("#montosol").numeric({ negative: false });
                        
                        
                        $("#nombre").focus();
                        //$(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                        $(".chzn-select").not('#clientes').chosen({ disable_search_threshold: 5 }); 
                        $(".chzn-container-multi .chzn-choices").css('height','auto');
                        $("#btnBorrar").hide();
                        
                        event_add_file();
                        
                        $('#clientes').chosen({ search_contains: true });
                        
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
                        
                        $('.agregarc').on('click', function(event) {
                            
                            $.ajax({
                                    url : _carpetas.URL + "/x_getform_addentidad_cli",
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
                                        
                                        $("#provinciacli").chosen({ disable_search_threshold: 5 }); 
                                        $("#condicioniva").chosen({ disable_search_threshold: 5 });
                                        $("#condicioniibb").chosen({ disable_search_threshold: 5 }); 
                                        $("#tipobeneficiario").chosen({ disable_search_threshold: 5 });
                                           
                                        $('#btnClear').on('click', function(event) {
                                            $("#customForm").validationEngine('hideAll');
                                            event.preventDefault();
                                            $("#frmagregar :text").val("");
                                            $("#frmagregar :text").first().focus();
                                            $("#idh").val("");
                                            $("#provinciahcli").val("");
                                            $("#localidadhcli").val("");
                                            $("#obs").val("");
                                            $("#provinciacli").val(0).trigger("chosen:updated");
                                            loadChild_cli(0)
                                            $("#condicioniva").val(0).trigger("chosen:updated");
                                            $("#condicioniibb").val(0).trigger("chosen:updated");
                                            $("#tipobeneficiario").val(0).trigger("chosen:updated");
                                            if($("#label_action").html()=='Editar'){
                                              $("#label_action").html("Agregar");
                                            }
                                            $("#frmagregar :text").removeClass('error');
                                            $("#nom").select();

                                        });

                                        $('#raz').keyup(function(){
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


                                        $('#sendcli').on('click', function(event) {
                                            
                                            /*
                                            if ( !$("#customForm").validationEngine('validate') )
                                                return false;
                                            */
                                            event.preventDefault();
                                            var id = $("#idh").val();
                                            var prov = $("#provinciahcli").val();
                                            var loca = $("#localidadhcli").val();
                                            var condicioniibb = $("#condicioniibb").val();
                                            var condicioniva = $("#condicioniva").val();
                                            var falta = $("#falta").val();
                                            var raz = $("#raz").val();
                                            var dir = $("#dir").val();
                                            var tel = $("#tel").val();
                                            var con = $("#con").val();
                                            var cuit = $("#cuit").val();
                                            var insiibb = $("#insiibb").val();
                                            var insinv = $("#insinv").val();
                                            var ema = $("#ema").val();
                                            var obs = $("#obs").val();
                                            var cbu = $("#cbu").val();

                                            $.ajax({
                                                url : _carpetas.URL + "/x_verificarcuit",
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
                                                            var email       = $(this).find('#ema').val();
                                                            arr_contactos.push({con:contacto,tel:telefono,ema:email});
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
                                                            CONTACTO:con,
                                                            CUIT:cuit,
                                                            CORREO:ema,
                                                            OBSERVACION:obs,
                                                            INSCRIPCION_IIBB:insiibb,
                                                            CBU:cbu,
                                                            contactos:arr_contactos
                                                        }

                                                        $.ajax({
                                                            url : _carpetas.URL + "/x_sendobjcli",
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

                                        loadChild_cli(0);


                                        $('#provinciacli').bind('change', function(event) {
                                            event.preventDefault();
                                            $(this).validationEngine('validate');
                                            if ($('#provincia').val()=='')
                                                loadChild_cli(0)

                                            $('#provinciah').val($('#provincia').val());

                                            var selected = $(this).find('option').eq(this.selectedIndex);

                                            var connection = selected.data('connection');
                                            selected.closest('#rubro li').nextAll().remove();
                                            if(connection){
                                                loadChild_cli(connection);
                                            }

                                        });

                                    }
                                });  
                            
                        });
                        
                        
                        $('.send').on('click', function(event){
                            event.preventDefault();
                            var id = $("#idh").val();
                            
                            var montosol = $("#montosol").val();
                            var destino = $("#destino").val();
                            var provincia = $("#provincia").val();
                            var localidadh = $("#localidadh").val();
                            var fideicomiso = $("#fideicomiso").val();
                            var operatoriah = $("#operatoriah").val();
                            
                            //checklist
                            var items = $("#listbox").jqxListBox('getItems');
                            var checkedItems = [];
                            var allItems = [];
                            if (items){
                                $.each(items, function (index, value) {
                                    //allItems.push(value.value);
                                    if (value.checked)
                                        checkedItems.push(value.value);
                                    allItems.push(value.value);
                                });
                            }
                            
                            var postulantes = $("#clientes").val() || '';
                            
                            var obs_checlist = $("#obs_checlist").val() || '';
                            var obs_cinicial = $("#obs_cinicial").val() || '';
                            var obs_patrimoniales = $("#obs_patrimoniales").val() || '';
                            var obs_legales = $("#obs_legales").val() || '';
                            var obs_tecnico = $("#obs_tecnico").val() || '';
                            var obs_elevacion = $("#obs_elevacion").val() || '';
                            var obs_contrato = $("#obs_contrato").val() || '';
                            var arr_obs = [];
                            arr_obs.push({
                                obs_checlist:obs_checlist,
                                obs_cinicial:obs_cinicial,
                                obs_patrimoniales:obs_patrimoniales,
                                obs_legales:obs_legales,
                                obs_tecnico:obs_tecnico,
                                obs_elevacion:obs_elevacion,
                                obs_contrato:obs_contrato
                            });
                            
                            var chk_checklist = $("#chk_checklist").is(":checked")?1:0;
                            var chk_cinicial = $("#chk_cinicial").is(":checked")?1:0;
                            var chk_legales = $("#chk_legales").is(":checked")?1:0;
                            var chk_patrimoniales = $("#chk_patrimoniales").is(":checked")?1:0;
                            var chk_tecnicos = $("#chk_tecnicos").is(":checked")?1:0;
                            var chk_elevacion = $("#chk_elevacion").is(":checked")?1:0;
                            var chk_rcontrato = $("#chk_rcontrato").is(":checked")?1:0;
                            var chk_fcontrato = $("#chk_fcontrato").is(":checked")?1:0;
                            var chk_altacredito = $("#chk_altacredito").is(":checked")?1:0;
                            
                            var arr_chk = [];
                            arr_chk.push({
                                chk_checklist:chk_checklist,
                                chk_cinicial:chk_cinicial,
                                chk_legales:chk_legales,
                                chk_patrimoniales:chk_patrimoniales,
                                chk_tecnicos:chk_tecnicos,
                                chk_elevacion:chk_elevacion,
                                chk_rcontrato:chk_rcontrato,
                                chk_fcontrato:chk_fcontrato,
                                chk_altacredito:chk_altacredito
                            });
                            
                            
                            //adjuntos
                            var _array_uploads = [];
                            $( ".lista_adjuntos li" ).each(function( index ) {
                                var nombre = $(this).data('nom');
                                var nombre_tmp = $(this).data('tmp');
                                var etapa = $(this).data('eta');
                                _array_uploads.push({nombre:nombre,nombre_tmp:nombre_tmp,etapa:etapa});
                            });   
                            
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                            
                            if (montosol>_monto_solicitado){
                                jAlert('Este monto supera el permitido por la Operatoria.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#montosol").focus().select();
                                });
                                return false;
                            }

                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                MONTO_SOLICITADO:montosol,
                                DESTINO:destino,
                                ID_PROVINCIA:provincia,
                                ID_DEPARTAMENTO:localidadh,
                                ID_FIDEICOMISO:fideicomiso,
                                ID_OPERATORIA:operatoriah,
                                ID_ESTADO:99,
                                CARTERADE:_USUARIO_SESION_ACTUAL,
                                CHECK_ALL: allItems,
                                CHECK_SEL: checkedItems,
                                POSTULANTES: postulantes,
                                arr_obs:arr_obs,
                                arr_chk:arr_chk,
                                ID_ETAPA_ACTUAL:1,
                                adjuntos:_array_uploads,
                                ID_PROCESO:_proceso_operatoria
                            }
                            
                            //alert(_proceso_operatoria);
                            if (_proceso_operatoria==1 || 1){ // validar checks y conformiddad para poder guardar
                                var conformidad = $("#chk_checklist").attr("checked");
                                if (conformidad != 'checked'){
                                    jAlert('Se debe dar conformidad a esta Etapa para guardarla.', $.ucwords(_etiqueta_modulo),function(){
                                    });
                                    return false;
                                }
                            }
                  
                            $.ajax({
                                url : _carpetas.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){

                                    if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                            var sw_asignar_add = 1
                                            _array_obj = data.obj_rtn
                                            var opt_puesto=0;
                                            var opt_area=0;
                                            
                                            if (_array_obj.ID_ETAPA_ACTUAL==1){
                                                if (_array_obj.ID_PROCESO=='2'){
                                                    opt_area = [4];
                                                    opt_puesto = 7;
                                                }else{
                                                    if (_USER_ROL=='10'){
                                                        sw_asignar_add = 2;
                                                    }
                                                    opt_area = [4];
                                                    opt_puesto = 6;
                                                }
                                            }
                                            
                                            
                                            $('.asignar').show();
                                            $('.asignar').on('click', function(event){
                                                event.preventDefault();
                                                $.ajax({
                                                    url : _carpetas.URL + "/x_getenviar_a1",
                                                    data : {
                                                        puesto_in:opt_puesto,// parametro opcional
                                                        area:opt_area
                                                    },
                                                    dataType : "json",
                                                    type : "post",
                                                    success : function(data1){
                                                        var clase_asignar;
                                                        var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div>';
                                                        if(data1){
                                                            
                                                            //_USUARIO_SESION_ACTUAL
                                                            
                                                            $.each(data1, function (index, value){
                                                                clase_asignar = 'link_asignar';
                                                                if (value.IID!=_USUARIO_SESION_ACTUAL){
                                                                    cadhtml +=  '<div class="' + clase_asignar + ' x_area" data-etapa="'+value.ETAPA+'" data-iid="'+value.ID+'" data-puesto_in="'+value.puesto_in+'"><span>' + value.DENOMINACION;
                                                                    cadhtml += '</span></div>';

                                                                }
                                                            });
                                                        }

                                                        $.fancybox({
                                                            "content": cadhtml,
                                                            'padding'   :  35,
                                                            'autoScale' :true,
                                                            'height' : 900,
                                                            'scrolling' : 'no',
                                                            'beforeClose': function() {
                                                                if (myfancy==1)
                                                                    regresar_a_listado();
                                                            }
                                                        });
                                                        

                                                        $(".x_area").click(function(e1){
                                                            e1.preventDefault();
                                                            var tmpfancy = myfancy;
                                                            myfancy=0;

                                                            var iid = $(this).data('iid');
                                                            var apuesto_in = $(this).data('puesto_in');
                                                            apuesto_in = isNaN(apuesto_in)?'':apuesto_in;


                                                            $.ajax({
                                                                url : _carpetas.URL + "/x_getenviar_a2",
                                                                data : {
                                                                    id_area:iid,
                                                                    puesto_in:apuesto_in
                                                                },
                                                                error: function (xhr, ajaxOptions, thrownError) {
                                                                  alert(xhr.status);
                                                                  alert(thrownError);
                                                                },
                                                                dataType : "json",
                                                                type : "post",
                                                                success : function(datar){

                                                                    var clase_asignar;
                                                                    var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div> <div class="regresar_ar">Regresar</div>';
                                                                    if(datar){
                                                                                                                                                
                                                                        $.each(datar, function (index, value){

                                                                            clase_asignar = 'link_asignar';
                                                                            //if (parseFloat(_array_obj.obj_operatoria.COORDOPE)>0 && _USER_ROL==9 && _array_obj.ID_ETAPA_ACTUAL==1){
                                                                            if (parseFloat(_array_obj.obj_operatoria.COORDOPE)>0 && _array_obj.obj_operatoria.COORDOPE==value.IID && _USER_ROL==9 && _array_obj.ID_ETAPA_ACTUAL==1){
                                                                                    cadhtml +=  '<div class="' + clase_asignar + '" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'"><span>' + value.NOMBRE + ' ' + value.APELLIDO+ ' ('+ value.AREA+ ' - ' + value.PUESTO+')';
                                                                                    cadhtml += '</span></div>';
                                                                                return false;
                                                                            }else{
                                                                                if (parseFloat(_array_obj.obj_operatoria.COORDOPE)<=0){
                                                                                    if (value.IID!=_USUARIO_SESION_ACTUAL || ( _array_obj.ID_ETAPA_ACTUAL==1 && _USER_ROL==10 && value.ETAPA=='3' )  ){
                                                                                        cadhtml +=  '<div class="' + clase_asignar + '" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'"><span>' + value.NOMBRE + ' ' + value.APELLIDO+ ' ('+ value.AREA+ ' - ' + value.PUESTO+')';
                                                                                        cadhtml += '</span></div>';
                                                                                    }
                                                                                }
                                                                            }
                                                                        });
                                                                    }

                                                                    $.fancybox({
                                                                        "content": cadhtml,
                                                                        'padding'   :  35,
                                                                        'autoScale' :true,
                                                                        'height' : 900,
                                                                        'scrolling' : 'no',
                                                                        'beforeClose': function() { 
                                                                            if (myfancy==1)
                                                                                regresar_a_listado();
                                                                        }
                                                                    });
                                                                    if (tmpfancy==1)
                                                                        myfancy=1;


                                                                    $(".regresar_ar").click(function(e){
                                                                        e.preventDefault();
                                                                        $.fancybox.close();
                                                                        $(".asignar").trigger('click');
                                                                    });

                                                                    $(".link_asignar").click(function(e){

                                                                        e.preventDefault();
                                                                        var iid = $(this).data('iid');
                                                                        var new_etapa_data = $(this).data('etapa');

                                                                        var observacion;
                                                                        var estado;
                                                                        var descripcion;
                                                                        
                                                                        jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                                                            if(r==true){
                                                                                var id_usuario = iid;
                                                                                //nueva etapa
                                                                                var new_etapa = 1;
                                                                                if (_array_obj.ID_ETAPA_ACTUAL==1){
                                                                                    
                                                                                    if (_array_obj.ID_PROCESO=='2'){
                                                                                        new_etapa = 9;
                                                                                        observacion='ENVIADO';
                                                                                        descripcion='DE MESA DE ENTRADA A COMITE, ESPERANDO ACEPTACION'
                                                                                        estado='2'
                                                                                    }else{
                                                                                        new_etapa = 2;
                                                                                        observacion='ENVIADO';
                                                                                        descripcion='DE MESA DE ENTRADA A CONTROL INICIAL, ESPERANDO ACEPTACION'
                                                                                        estado='2'
                                                                                    }
                                                                                    
                                                                                }
                                                                                $.ajax({

                                                                                    url : _carpetas.URL + "/x_actualizar_operacion",
                                                                                    data : {
                                                                                        OPERACION:_array_obj.ID,
                                                                                        ID_ETAPA_ACTUAL:new_etapa,
                                                                                        USUARIO:id_usuario,
                                                                                        OBSERVACION:observacion,
                                                                                        DESCRIPCION:descripcion,
                                                                                        ESTADO:estado,
                                                                                        ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL
                                                                                    },
                                                                                    dataType : "json",
                                                                                    type : "post",
                                                                                    success : function(data){
                                                                                        $.fancybox.close();
                                                                                        $("#jqxgrid").show();
                                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                                        $("#wpopup").html('');
                                                                                    }
                                                                                });

                                                                            }
                                                                        });
                                                                    });

                                                                }
                                                            });
                                                        });
                                                        return false;

                                                    }
                                                });
                                            });
                                            
                                            myfancy=1
                                            //$(".asignar").trigger('click');
                                            //checklist
                                            //var items = $("#listbox").jqxListBox('getCheckedItems');
                                            var items = $("#listbox").jqxListBox('getItems');
                                            var checkedItems = [];
                                            var allItems = [];
                                            if(items){
                                                $.each(items, function (index, value) {
                                                    //allItems.push(value.value);
                                                    if (value.checked)
                                                        checkedItems.push(value.value);
                                                    allItems.push(value.value);
                                                });
                                            }

                                            var num_items_all = allItems.length;
                                            var num_items_select = checkedItems.length;
                                            var conformidad = $("#chk_checklist").attr("checked");
                                            
                                            
                                            if ( (num_items_select==num_items_all) && (conformidad=='checked') && (sw_asignar_add != 2) ){
                                                $(".asignar").trigger('click');
                                            }else{
                                                if (sw_asignar_add==2){
                                                    jAlert('La Carpeta ahora se encuentra en la Etapa de Control Inicial.', $.ucwords(_etiqueta_modulo),function(){
                                                        $("#jqxgrid").show();
                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                        $("#wpopup").html('');
                                                        switchBarra();
                                                    });
                                                }else{
                                                    $("#jqxgrid").show();
                                                    $("#jqxgrid").jqxGrid('updatebounddata');
                                                    $("#wpopup").html('');
                                                    switchBarra();
                                                }
                                                
                                            }
                                            
                                            /*
                                            $('#btnClear').trigger('click');
                                            $("#jqxgrid").show();
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                            $("#wpopup").html('');
                                            */
                                            
                                        });
                                        
                                    }else{
                                        
                                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                        });
                                        
                                    }
                                    
                                }
                            });

                        });
                        
                        event_d_tab_left();

                        $("#periodicidad").jqxNumberInput({ width: '100px', height: '25px', inputMode: 'simple', spinButtons: true,  decimal:0, decimalDigits:0,min: 0, max: 60 });
                        $("#desembolsos").jqxNumberInput({ width: '100px', height: '25px', inputMode: 'simple', spinButtons: true,  decimal:0, decimalDigits:0,min: 0, max: 60 });
                        $("#devoluciones").jqxNumberInput({ width: '100px', height: '25px', inputMode: 'simple', spinButtons: true,  decimal:0, decimalDigits:0,min: 0, max: 60 });
                        
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
                        
                        loadChild_fid(0);
                        $('#fideicomiso').bind('change', function(event) {
                            event.preventDefault();
                            $(this).validationEngine('validate');
                            if ($('#fideicomiso').val()=='')
                                loadChild_fid(0)

                            $('#fideicomisoh').val($('#fideicomiso').val());

                            var selected = $(this).find('option').eq(this.selectedIndex);

                            var connection = selected.data('connection');
                            selected.closest('#fideicomiso li').nextAll().remove();
                            if(connection){
                                loadChild_fid(connection);
                            }

                        });
                        
                        /*
                        $("#montosol").focusout(function(event) {
                            //validar monto sol (no debe ser mayor al monto maximo del fideicom)
                            var monto = $(this).val();
                            if (monto>_monto_solicitado){
                                event.preventDefault();
                                jAlert('Este monto supera el permitido por la Operatoria.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#montosol").select();
                                });
                                return false;
                            }
                            
                        })
                        */
                       
                        ev_hide_it();
                        
                        //desbloquear primera etapa cuando se agrega una nueva carpeta
                        //$("#vtab .ocultame_etapa:first").hide();
                        
                        agregar_garantias();
                        agregar_altacredito();
                        agregar_solicituddesembolso();
                        agregar_garantias_1('add');
                        //event_grid_traza();
                        refresGridevent();
                        
                    }
                });
                
            }else if(top=='des'){
                mydata = '';
                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                    
                if ( mydata==null ){
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                var idope = mydata.IDOPE;
                
                
                //preguntar si carpeta esta en mi cartera, sino lanzar mensaje de error
                $.ajax({
                    url : _carpetas.URL + "/x_getobj",
                    type : "post",
                    data : {
                        id:idope
                    },
                    dataType:'json',
                    async:false,
                    success : function(data){
                        if(data.CARTERADE){
                            
                            if (_USUARIO_SESION_ACTUAL==data.CARTERADE){
                                jConfirm('Esta seguro de recuperar esta carpeta??.', 'Carpetas',function(r){
                                    if(r==true){
                                        $.ajax({
                                            url : _carpetas.URL + "/x_recuperar_carpeta",
                                            type : "post",
                                            data : {
                                                idope:idope
                                            },
                                            async:false,
                                            success : function(data){
                                                jAlert('Carpeta Recuperada.', 'Carpetas',function(){
                                                    actualizaNotif();
                                                    regresar_a_listado();
                                                    $.unblockUI();
                                                    $.fancybox.close();
                                                    
                                                });
                                            }
                                        });
                                    }else{
                                        $.unblockUI();
                                    }
                                });
                            }else{
                                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                                    $.unblockUI();
                                    switchBarra();
                                });
                                
                            }
                            
                        }
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
                if(mydata.CARGAH=='2'){
                    var urlh = "backend/carpeta/carpetash1/init/3/"+mydata.IDOPE; // add
                    $(location).attr('href',urlh);
                    $.unblockUI();
                }else{
                // edit entidades
                $.ajax({
                    url : _carpetas.URL + "/x_getform_addentidad",
                    data : {
                        obj:mydata.IDOPE
                    },
                    async:false,
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        
                        
                        actualizarBarraHerramientas();
                        //boton guardar
                        if (_array_obj.CARTERADE==_USUARIO_SESION_ACTUAL && _array_obj.ENVIADOA==0){
                            $(".tb_save").show();
                        }else{
                            $(".tb_save").hide();
                        }
                        
                        if (_array_obj.CARTERADE==0 && _USER_ROL==9)
                            setMenuCarpeta('1','1');
                        
                        event_add_file();
                        activar_acordeon('.grid-1');
                        init_chk(); // iniciar funciones de forms.js
                        
                        
                        var $items = $('#vtab>ul>li');
                        $items.click(function() {
                            var index = $items.index($(this));
                            var etapa_div = $('#vtab>div').eq(index).data('etapa');
                            
                            if (_array_obj.ID_PROCESO==1 && (  (etapa_div <= _array_obj.ID_ETAPA_ACTUAL) || (etapa_div =='7' && jQuery.inArray(_array_obj.ID_ETAPA_ACTUAL, ['3','4','5','6','7'])!=-1) ) ){
                                $items.removeClass('selected');
                                $(this).addClass('selected');
                                $('#vtab>div').hide().eq(index).show();
                            }else if( _array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL=='9' && jQuery.inArray(etapa_div, [1,9])!=-1 ){
                                $items.removeClass('selected');
                                $(this).addClass('selected');
                                $('#vtab>div').hide().eq(index).show();
                            }else if( _array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL=='10' && jQuery.inArray(etapa_div, [1,9,10])!=-1 ){
                                $items.removeClass('selected');
                                $(this).addClass('selected');
                                $('#vtab>div').hide().eq(index).show();
                            }else if( _array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL=='13' && jQuery.inArray(etapa_div, [1,9,10,13])!=-1 ){
                                $items.removeClass('selected');
                                $(this).addClass('selected');
                                $('#vtab>div').hide().eq(index).show();
                            }else if( _array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL=='11' && jQuery.inArray(etapa_div, [1,9,11,10,13])!=-1 ){
                                $items.removeClass('selected');
                                $(this).addClass('selected');
                                $('#vtab>div').hide().eq(index).show();
                            }else if( _array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL=='12' && jQuery.inArray(etapa_div, [1,9,10,13,11,12])!=-1 ){
                                $items.removeClass('selected');
                                $(this).addClass('selected');
                                $('#vtab>div').hide().eq(index).show();
                            }else{
                                if (_array_obj.ID_PROCESO=='2'){
                                    if (_array_obj.ID_ETAPA_ACTUAL!=14){
                                        if (index<='1'){
                                            console.log('aquiiii 111111 ++' + index);
                                            $items.removeClass('selected');
                                            $(this).addClass('selected');
                                            $('#vtab>div').hide().eq(index).show();
                                        }else{
                                            console.log('aquiiii 22222222 ++' + index);
                                            if (_array_obj.ID_ETAPA_ACTUAL!=14){
                                                jAlert('Esta etapa aun no está disponible.', $.ucwords(_etiqueta_modulo),function(){
                                                });
                                            }
                                        }
                                    }else{
                                        $items.removeClass('selected');
                                        $(this).addClass('selected');
                                        $('#vtab>div').hide().eq(index).show();
                                    }
                                    
                                }else{
                                    jAlert('Esta etapa aun no está disponible.', $.ucwords(_etiqueta_modulo),function(){
                                    });
                                }
                                
                            }
                            
                        })//;.eq(0).click();
                        //.eq(_array_obj.ID_ETAPA_ACTUAL-1).click();
                        
                        
                        if (_array_obj.ID_PROCESO=='1'){
                            //$items.eq(_array_obj.ID_ETAPA_ACTUAL-1).click();
                        }else{
                            $items.eq(0).click();
                        }
                        
                        //alert(_array_obj.ID_ETAPA_ACTUAL);
                        
                        
                        $('#vtab>ul>li').each(function(index){
                            var eta = $( this ).attr("eta");
                            var eta_act = parseInt(_array_obj.ID_ETAPA_ACTUAL);
                            if (eta==eta_act){
                                $(this).click();
                                return false;
                            }
                            if (jQuery.inArray(eta_act, [4,5,6])!=-1){
                                $('#vtab>ul>li').eq(3).click();
                            }
                            
                        });
                                                
                        $('#tabs').tabs({
                            select: function(event, ui) {
                                etapa = 99;
                                switch(true) {
                                    case (ui.index==0):
                                        etapa = 4;
                                        break;
                                    case (ui.index==1):
                                        etapa = 5;
                                        break;
                                    case (ui.index==2):
                                        etapa = 6;
                                }
                                
                                $('.ver_todos').html('Ver Todos');
                                $(".grid_adjuntos ul li").hide();;
                                $(".grid_adjuntos ul li").next().hide();
                                
                                $(".grid_adjuntos ul li."+".eta-"+etapa).show();
                                $(".grid_adjuntos ul li."+".eta-"+etapa).next().show();
                                
                            }
                        });
                        
                        var solapa = $('#vtab>ul>li.etapa_ready').last().next();
                        solapa.css('opacity','0.5');
                        
                        if (_array_obj.ID_PROCESO=='1'){
                            if( jQuery.inArray( _array_obj.ID_ETAPA_ACTUAL, ['3','4','5','6'])!=-1 ){
                                solapa.next().css('opacity','0.5');
                            }
                        }
                        
                        if (_array_obj.ID_PROCESO=='2'){
                            if( jQuery.inArray( _array_obj.ID_ETAPA_ACTUAL, ['9'])!=-1 ){
                                solapa.next().css('opacity','0.5');
                            }else if( jQuery.inArray( _array_obj.ID_ETAPA_ACTUAL, ['11'])!=-1 ){
                                solapa.next().css('opacity','0.5');
                            }
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL>2)
                            $('#vtab>ul>li.garantia').css('opacity','0.5');
                        
                        //comportamientos de conformidad y no corresponde para analisis
                        $('#chk_legales').on('click', chk_1);
                        $('#chk_legales').prev().on('click', chk_1);
                        $('#chk_legales_nc').on('click', chk_2);
                        $('#chk_legales_nc').prev().on('click', chk_2);
                        
                        $('#chk_patrimoniales').on('click', chk_3);
                        $('#chk_patrimoniales').prev().on('click', chk_3);
                        $('#chk_patrimoniales_nc').on('click', chk_4);
                        $('#chk_patrimoniales_nc').prev().on('click', chk_4);
                        
                        $('#chk_tecnicos').on('click', chk_5);
                        $('#chk_tecnicos').prev().on('click', chk_5);
                        $('#chk_tecnicos_nc').on('click', chk_6);
                        $('#chk_tecnicos_nc').prev().on('click', chk_6);
                        
                        if (jQuery.inArray(_USER_ROL, ['12','13','14','15','16','17','18'])!=-1){
                            $(".chk_opera").hide();
                        }
                        
                        init_datepicker('#facta','-3','+5','0',0);
                        init_datepicker('#cffirma','-3','+5','0',0);
                        
                        init_datepicker('#fentregam','-3','+5','0',0);
                        init_datepicker('#fdevm','-3','+5','0',0);
                        
                        var comite_facta_h = $("#comite_facta_h").val();
                        $("#facta").val(comite_facta_h);
                        
                        var contratof_h = $("#contratof_h").val();
                        $("#cffirma").val(contratof_h);
                        
                        var minuta_fentregam_h = $("#minuta_fentregam_h").val();
                        $("#fentregam").val(minuta_fentregam_h);
                        
                        var minuta_fdevm_h = $("#minuta_fdevm_h").val();
                        $("#fdevm").val(minuta_fdevm_h);
                        
                        var escribanoh = $("#escribanoh").val();
                        $("#escribano").val(escribanoh).trigger("chosen:updated");
                        
                        
                        $("#nacta").numeric({ negative: false });
                        $("#macta").numeric({ negative: false });
                        $("#montosol").numeric({ negative: false });
                        $("#cmaprob").numeric({ negative: false });
                        
                        //cargar el monto del comite
                        if ( _array_obj.ID_ETAPA_ACTUAL==11 && _USER_ROL==10 ){
                            if (parseInt($("#cmaprob").val())==0)
                                $("#cmaprob").val($("#macta").val());
                        }
                        
                        $(".chzn-select").not('#clientes').not('#escribano').chosen({ disable_search_threshold: 5 }); 
                        $('#clientes').chosen({ search_contains: true });
                        $(".chzn-container-multi .chzn-choices").css('height','auto');
                        $("#escribano").chosen({width: "250px"}); 
                        
                        var idph = $("#provinciah").val();
                        var idlh = $("#localidadh").val();
                        
                        //$("#estadoreq").val(2).attr('disabled', true).trigger("chosen:updated");
                        if (_USER_ROL!=1 && 0){
                            $("#provincia").val(idph).attr('disabled', true).trigger("chosen:updated");
                            loadChild(idph);
                            $("#subrubro").val(idlh).attr('disabled', true).trigger("chosen:updated");
                        }else{
                            $("#provincia").val(idph).trigger("chosen:updated");
                            loadChild(idph);
                            $("#subrubro").val(idlh).trigger("chosen:updated");
                            $("#montosol").removeAttr('readonly');
                            $("#destino").removeAttr('readonly');
                        }
                        //jjjjjjjjjjjjjjjjjjjjjjjjjjj
                        
                        var idfh = $("#fideicomisoh").val();
                        var idoh = $("#operatoriah").val();
                        
                        if (_USER_ROL!=1 && 0){
                            $("#fideicomiso").val(idfh).attr('disabled', true).trigger("chosen:updated");
                        }
                        else{
                            $("#fideicomiso").val(idfh).attr('disabled', true).trigger("chosen:updated");
                        }
                        
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
                        
                        $('#fideicomiso').bind('change', function(event) {
                            event.preventDefault();
                            $(this).validationEngine('validate');
                            if ($('#fideicomiso').val()=='')
                                loadChild_fid(0)

                            $('#fideicomisoh').val($('#fideicomiso').val());

                            var selected = $(this).find('option').eq(this.selectedIndex);

                            var connection = selected.data('connection');
                            selected.closest('#fideicomiso li').nextAll().remove();
                            if(connection){
                                loadChild_fid(connection);
                            }

                        });
                        
                        //$("#fideicomiso").val(idfh).trigger("chosen:updated");
                        
                        loadChild_fid(idfh);
                        if (_USER_ROL!=1 && 0){
                            $("#operatoria").val(idoh).attr('disabled', true).trigger("chosen:updated");
                            $("#clientes").attr('disabled', true);
                        }else{
                            $("#operatoria").val(idoh).attr('disabled', true).trigger("chosen:updated");
                        }
                        
                        $("#operatoria").change();
                        
                        var items = $("#listbox").jqxListBox('getItems');
                        
                        $(".chkcom:last").css("color","red");
                        
                        if(items){
                            $.each(_array_checklist, function (index, value) {
                                $.each(items, function (index1, value1) {
                                    if(value1.value==value){
                                        $("#listbox").jqxListBox('checkIndex',index1);    
                                        return false;
                                    }
                                });
                            });
                        }
                        
                        //rellenar listbox deudas
                        
                        var cad = $("#val_clientesh").val();
                        if (cad.length>0){
                            var stringParts = cad.split(",");
                            $.each(stringParts, function(key, value) {
                                $('#clientes').find("option[value='"+value+"']").attr('selected', 'selected');
                            });   
                            $('#clientes').trigger('chosen:updated');
                        }
                        
                        agregarCondicionesPrevias(_array_obj);
                        agregarCondicionesPrevias_dese();
                        
                        //llenar condiciones previas al contrato
                        var itemscom = $("#listbox_cond").jqxListBox('getItems');
                        $.each(_array_checklist_comite, function (index, value) {
                            $.each(itemscom, function (index1, value1) {
                                if(value1.value==value){
                                    $("#listbox_cond").jqxListBox('checkIndex',index1);    
                                    return false;
                                }
                            });
                        });
                        
                        //llenar condiciones previas al desembolso
                        var itemscomdese = $("#listbox_cond_dese").jqxListBox('getItems');
                        $.each(_array_checklist_desembolso, function (index, value) {
                            $.each(itemscomdese, function (index1, value1) {
                                if(value1.value==value){
                                    $("#listbox_cond_dese").jqxListBox('checkIndex',index1);    
                                    return false;
                                }
                            });
                        });
                        
                        agregar_garantias();
                        agregar_altacredito();
                        agregar_solicituddesembolso();
                        agregar_garantias_1('edi',_array_obj);
                        
                        
                        $("#btnBorrar").show();
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
                        
                        $("#chk_rcontrato").attr('disabled', true);
                        
                        if ( _array_obj.ID_ETAPA_ACTUAL==10 && jQuery.inArray( _USER_ROL, ['12','13','14'])!=-1 ){
                            $("#chk_rcontrato").removeAttr('disabled');
                        }
                        
                        $('.send').on('click', function(event) {
                            
                            event.preventDefault();
                            
                            var id = $("#idh").val();
                            
                            var montosol = $("#montosol").val();
                            var destino = $("#destino").val();
                            var provincia = $("#provincia").val();
                            var localidadh = $("#localidadh").val();
                            var fideicomiso = $("#fideicomiso").val();
                            var operatoriah = $("#operatoriah").val();
                            
                            //checklist
                            //var items = $("#listbox").jqxListBox('getCheckedItems');
                            var items = $("#listbox").jqxListBox('getItems');
                            var checkedItems = [];
                            var allItems = [];
                            if(items){
                                $.each(items, function (index, value) {
                                    //allItems.push(value.value);
                                    if (value.checked)
                                        checkedItems.push(value.value);
                                    allItems.push(value.value);
                                });
                            }
                            
                            var num_items_all = allItems.length;
                            var num_items_select = checkedItems.length;
                            
                            /* deudas*/
                            
                            //checklist
                            //var items = $("#listbox").jqxListBox('getCheckedItems');
                            var items_deudas = $("#listbox_deudas").jqxListBox('getItems');
                            var checkedItems_deudas = [];
                            var allItems_deudas = [];
                            if(items_deudas){
                                $.each(items_deudas, function (index, value) {
                                    //allItems.push(value.value);
                                    if (value.checked)
                                        checkedItems_deudas.push(value.value);
                                    allItems_deudas.push(value.value);
                                });
                            }
                            
                            var num_items_all = allItems.length;
                            var num_items_select = checkedItems.length;
                            
                            /* deudas */
                            
                            var postulantes = $("#clientes").val();
                            
                            var obs_checlist = $("#obs_checlist").length>0?$("#obs_checlist").val():'-1' ;
                            var obs_cinicial = $("#obs_cinicial").length>0?$("#obs_cinicial").val():'-1' ;
                            var obs_patrimoniales = $("#obs_patrimoniales").length>0?$("#obs_patrimoniales").val():'-1' ;
                            var obs_legales = $("#obs_legales").length>0?$("#obs_legales").val():'-1' ;
                            var obs_tecnico = $("#obs_tecnico").length>0?$("#obs_tecnico").val():'-1' ;
                            var obs_elevacion = $("#obs_elevacion").length>0?$("#obs_elevacion").val():'-1' ;
                            var obs_comite = $("#obs_comite").length>0?$("#obs_comite").val():'-1' ;
                            var obs_contrato = $("#obs_contrato").length>0?$("#obs_contrato").val():'-1' ;
                            
                            var arr_obs = [];
                            arr_obs.push({
                                obs_checlist:obs_checlist,
                                obs_cinicial:obs_cinicial,
                                obs_patrimoniales:obs_patrimoniales,
                                obs_legales:obs_legales,
                                obs_tecnico:obs_tecnico,
                                obs_elevacion:obs_elevacion,
                                obs_comite:obs_comite,
                                obs_contrato:obs_contrato
                            });
                                                        
                            var chk_checklist =  $("#chk_checklist").length>0? ($("#chk_checklist").is(":checked")?1:0): '-1';
                            var chk_cinicial =  $("#chk_cinicial").length>0? ($("#chk_cinicial").is(":checked")?1:0): '-1';
                            var chk_legales =  $("#chk_legales").length>0? ($("#chk_legales").is(":checked")?1:0): '-1';
                            var chk_legales_nc = $("#chk_legales_nc").length>0? ($("#chk_legales_nc").is(":checked")?1:0): '-1';
                            var chk_patrimoniales =  $("#chk_patrimoniales").length>0? ($("#chk_patrimoniales").is(":checked")?1:0): '-1';
                            var chk_patrimoniales_nc = $("#chk_patrimoniales_nc").length>0? ($("#chk_patrimoniales_nc").is(":checked")?1:0): '-1';
                            var chk_tecnicos =  $("#chk_tecnicos").length>0? ($("#chk_tecnicos").is(":checked")?1:0): '-1';
                            var chk_tecnicos_nc = $("#chk_tecnicos_nc").length>0? ($("#chk_tecnicos_nc").is(":checked")?1:0): '-1';
                            var chk_elevacion =  $("#chk_elevacion").length>0? ($("#chk_elevacion").is(":checked")?1:0): '-1';
                            var chk_comite =  $("#chk_comite").length>0? ($("#chk_comite").is(":checked")?1:0): '-1';
                            var chk_rcontrato =  $("#chk_rcontrato").length>0? ($("#chk_rcontrato").is(":checked")?1:0): '-1';
                            var chk_fcontrato =  $("#chk_fcontrato").length>0? ($("#chk_fcontrato").is(":checked")?1:0): '-1';
                            var chk_altacredito =  $("#chk_altacredito").length>0? ($("#chk_altacredito").is(":checked")?1:0): '-1';
                            
                            if (chk_legales_nc==1){
                                chk_legales = 2;
                            }
                            if (chk_patrimoniales_nc==1){
                                chk_patrimoniales = 2;
                            }
                            if (chk_tecnicos_nc==1){
                                chk_tecnicos = 2;
                            }
                            
                            
                            var arr_chk = [];
                            arr_chk.push({
                                chk_checklist:chk_checklist,
                                chk_cinicial:chk_cinicial,
                                chk_legales:chk_legales,
                                chk_patrimoniales:chk_patrimoniales,
                                chk_tecnicos:chk_tecnicos,
                                chk_elevacion:chk_elevacion,
                                chk_comite:chk_comite,
                                chk_rcontrato:chk_rcontrato,
                                chk_fcontrato:chk_fcontrato,
                                chk_altacredito:chk_altacredito
                            });
                            
                            var nacta = $("#nacta").length>0?$("#nacta").val():'-1' ;
                            var facta = $("#facta").length>0?$("#facta").val():'-1' ;
                            var macta = $("#macta").length>0?$("#macta").val():'-1' ;
                            
                            var fcon = $("#cffirma").length>0?$("#cffirma").val():'-1' ;
                            var mcon = $("#cmaprob").length>0?$("#cmaprob").val():'-1' ;
                            
                            
                            var fentregam = $("#fentregam").length>0?$("#fentregam").val():'-1' ;
                            var fdevm = $("#fdevm").length>0?$("#fdevm").val():'-1' ;
                            var escribanom = $("#escribano").length>0?$("#escribano").val():'-1' ;
                            
                            
                            var arr_infoadd = [];
                            arr_infoadd.push({
                                etapa:9, //comite
                                nombre:'comite_nacta',
                                valor:nacta
                            });
                            arr_infoadd.push({
                                etapa:9, //comite
                                nombre:'comite_facta',
                                valor:facta
                            });
                            arr_infoadd.push({
                                etapa:9, //comite
                                nombre:'comite_macta',
                                valor:macta
                            });
                            
                            arr_infoadd.push({
                                etapa:11, //contrato
                                nombre:'contrato_fcon',
                                valor:fcon
                            });
                            arr_infoadd.push({
                                etapa:11, //contrato
                                nombre:'contrato_mcon',
                                valor:mcon
                            });
                            
                            arr_infoadd.push({
                                etapa:10, //conf contrato
                                nombre:'minuta_fentregam',
                                valor:fentregam
                            });
                            arr_infoadd.push({
                                etapa:10, //conf contrato
                                nombre:'minuta_fdevm',
                                valor:fdevm
                            });
                            arr_infoadd.push({
                                etapa:10, //conf contrato
                                nombre:'minuta_escribano',
                                valor:escribanom
                            });
                            
                            //adjuntos
                            var _array_uploads = [];
                            $( ".lista_adjuntos li" ).each(function( index ) {
                                var nombre = $(this).data('nom');
                                var nombre_tmp = $(this).data('tmp');
                                var etapa = $(this).data('eta');
                                _array_uploads.push({nombre:nombre,nombre_tmp:nombre_tmp,etapa:etapa});
                            });
                            
                            
                            //checklist comite
                            var items = $("#listbox_cond").jqxListBox('getItems');
                            var arr_itemscom = [];
                            if (items){
                                $.each(items, function (index, value){
                                    arr_itemscom.push({'label':value.label,'seleccionado':value.checked==true?1:0,'tipo':'1'});
                                });
                            }
                            var items_dese = {};
                            //checklist contrato
                            items_dese = $("#listbox_cond_dese").jqxListBox('getItems');
                            var arr_itemscom_dese = [];
                            var cont_indet = 0;

                            var tmpcad;
                            if (typeof(items_dese) !== "undefined") {
                                $.each(items_dese, function (index, value){
                                    if (value.checkBoxElement){
                                        tmpcad = value.checkBoxElement.innerHTML;
                                        if (tmpcad.indexOf("jqx-checkbox-check-indeterminate")>0 || tmpcad.indexOf("jqx-checkbox-check-checked")>0){
                                            cont_indet++;
                                        }
                                    }
                                    arr_itemscom_dese.push({'label':value.label,'seleccionado':value.checked==true?1:0,'tipo':'2'});
                                });
                            }
                            
                            var arr_condiciones_merge = $.merge( arr_itemscom, arr_itemscom_dese );
                            var tipo_seleccion = $('input[name=opt_comite]:checked').attr('value');
                                        
                            if ( _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==11 ){
                                //validar los check de desembolsos
                                if (items_dese){
                                    if (items_dese.length == cont_indet){
                                        //seguir
                                    }else{
                                        jAlert('Se deben evaluar todas las Condiciones de desembolso.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                        });
                                        return false;
                                    }
                                }
                            }
                            
                            //validar monto de comite
                            //falta comparar con el monto de las garantias aprobadas
                            if ( _array_obj.ID_ETAPA_ACTUAL==9 && _USER_ROL==11 ){
                                var montosol_t = $("#montosol").val();
                                var macta_t = $("#macta").val();
                                
                                
                                if ( macta_t*1 > montosol_t*1){
                                    jAlert('El monto aprobado no puede ser mayor al monto solicitado.', $.ucwords(_etiqueta_modulo),function(){
                                        $("#macta").focus().select();
                                    });
                                    return false;
                                }
                            }
                            
                            //validar monto de firma de contrato
                            if ( _array_obj.ID_ETAPA_ACTUAL==11 && _USER_ROL==10 ){
                                var monto_c = $("#macta").val();
                                var monto_fc = $("#cmaprob").val();
                                
                                if (monto_fc != monto_c){
                                    jAlert('El monto no puede ser diferente al monto aprobado en comité.', $.ucwords(_etiqueta_modulo),function(){
                                        $("#cmaprob").focus().select();
                                    });
                                    return false;
                                }
                            }
                            
                            
                            
                            /*
                             *yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy
                            if ( _array_obj.ID_ETAPA_ACTUAL==9 && _USER_ROL==11 ){
                                //validar garantias antes de comite
                                //todas las garantias deben tener estado: desaprobada o aprobada (4,5)
                                
                                
                                
                                if (items_dese.length == cont_indet){
                                    //seguir
                                }else{
                                    jAlert('Se deben evaluar todas las Condiciones de desembolso.', $.ucwords(_etiqueta_modulo),function(){
                                        $.unblockUI();
                                    });
                                    return false;
                                }
                            }
                            */
                           
                                        
                            var new_state = 2;
                            if (tipo_seleccion==3)
                                new_state = 3;
                            else if(tipo_seleccion==2)
                                new_state = 5;
                            
                            //if ( _array_obj.ID_ETAPA_ACTUAL==9 && tipo_seleccion==2 )
                                //_array_obj.ID_ETAPA_ACTUAL = 10
                            
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                            
                            
                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                MONTO_SOLICITADO:montosol,
                                DESTINO:destino,
                                ID_PROVINCIA:provincia,
                                ID_DEPARTAMENTO:localidadh,
                                ID_FIDEICOMISO:fideicomiso,
                                ID_OPERATORIA:operatoriah,
                                ID_ESTADO:new_state,
                                CARTERADE:_array_obj.CARTERADE,
                                CHECK_ALL: allItems,
                                CHECK_SEL: checkedItems,
                                POSTULANTES:postulantes,
                                arr_obs:arr_obs,
                                arr_chk:arr_chk,
                                ID_ETAPA_ACTUAL:_array_obj.ID_ETAPA_ACTUAL,
                                adjuntos:_array_uploads,
                                arr_infoadd:arr_infoadd,
                                arr_itemscom:arr_condiciones_merge,
                                ID_PROCESO:_proceso_operatoria,
                                checkedItems_deudas:checkedItems_deudas
                            }
                          
                            $.ajax({
                                url : _carpetas.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
             
                                    if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            /*
                                            $('#btnClear').trigger('click');
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                            $("#jqxgrid").show();
                                            $("#wpopup").html('');
                                            */
                                           $.unblockUI();
                                            myfancy=1
                                            console.log(_array_obj.ID_ETAPA_ACTUAL + '---' + _USER_ROL);
                                            console.log( num_items_select + '--' + num_items_all);
                                            
                                            var sw_asignar = 0;
                                            
                                            //revisar si las condiciones y checks estn ok para la etapa actual, si es asi, enviar
                                            if (_array_obj.ID_ETAPA_ACTUAL==1){
                                                    var conformidad_cl = $("#chk_checklist").attr("checked");
                                                    if ( (num_items_select==num_items_all) && conformidad_cl=='checked'){
                                                        //$(".asignar").trigger('click');
                                                        sw_asignar = 1;
                                                    }
                                            }
                                            
                                            if (_array_obj.ID_ETAPA_ACTUAL==2){
                                                var conformidad_ci = $("#chk_cinicial").attr("checked");
                                                if ( conformidad_ci=='checked' ){
                                                        sw_asignar = 1;
                                                }
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==4 && (jQuery.inArray( _USER_ROL, ['12','13','14'])!=-1 ) ){
                                                sw_asignar = 1;
                                            }
                                            if ( _array_obj.ID_ETAPA_ACTUAL==5 && (jQuery.inArray( _USER_ROL, ['15','16'])!=-1 ) ){
                                                sw_asignar = 1;
                                            }
                                            if ( _array_obj.ID_ETAPA_ACTUAL==6 && (jQuery.inArray( _USER_ROL, ['17','18'])!=-1 ) ){
                                                sw_asignar = 1;
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==8 && (jQuery.inArray( _USER_ROL, ['10'])!=-1 ) ){
                                                var conformidad_el = $("#chk_elevacion").attr("checked");
                                                if ( conformidad_el=='checked' ){
                                                    sw_asignar = 1;
                                                }
                                            }
                                            
                                            
                                            if ( _array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL==10 && (jQuery.inArray( _USER_ROL, ['10'])!=-1 ) ){
                                                    //????????????????????
                                                    //controlar las cond previas (contrato y desembolso)
                                                        sw_asignar = 1;
                                            }
                                            
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==10 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){
                                                
                                                    //controlar las cond previas (contrato y desembolso)
                                                    /*
                                                    var cont1=0,cont2=0;
                                                    var items1 = $("#listbox_cond").jqxListBox('getItems');
                                                    var t1 = items1.length;
                                                    $.each(items1, function (index, value){
                                                        
                                                        if (value.checked==true){
                                                            cont1++;
                                                        }
                                                    });
                                                    
                                                    var items2 = $("#listbox_cond_dese").jqxListBox('getItems');
                                                    var t2 = items2.length;
                                                    $.each(items2, function (index, value){
                                                        if (value.checked==true){
                                                            cont2++;
                                                        }
                                                    });
                                                    
                                                    if ( (t1==cont1) && (t2==cont2)){
                                                        sw_asignar = 1;
                                                    }
                                                    */
                                                   sw_asignar = 1;
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==10 && (jQuery.inArray( _USER_ROL, ['12','13','14'])!=-1 ) ){
                                                    var cont1=0,cont2=0;
                                                    var items1 = $("#listbox_cond").jqxListBox('getItems');
                                                    var t1 = items1.length;
                                                    $.each(items1, function (index, value){
                                                        
                                                        if (value.checked==true){
                                                            cont1++;
                                                        }
                                                    });
                                                    
                                                    var items2 = $("#listbox_cond_dese").jqxListBox('getItems');
                                                    var t2 = items2.length;
                                                    $.each(items2, function (index, value){
                                                        if (value.checked==true){
                                                            cont2++;
                                                        }
                                                    });
                                                    
                                                    var conformidad_cr = $("#chk_rcontrato").attr("checked");
                                                    
                                                    if ( (t1==cont1) && (t2==cont2) && (conformidad_cr=='checked') ){
                                                        sw_asignar = 1;
                                                    }
                                                    else if((t1==cont1) && (t2==cont2)){
                                                        sw_asignar = 1;
                                                    }
                                                    /*
                                                    if ( conformidad_cr=='checked' ){
                                                        sw_asignar = 1;
                                                    }*/
                                                    
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==11 && (jQuery.inArray( _USER_ROL, ['10'])!=-1 ) ){
                                                var conformidad_fc = $("#chk_fcontrato").attr("checked");
                                                if( conformidad_fc == 'checked' ){
                                                    
                                                    //preguntar por las garantias (deben estar en estado Constituidas)
                                                    $.ajax({
                                                        url : _carpetas.URL + "/x_get_gar_const",
                                                        data : {
                                                            idope:_array_obj.ID
                                                        },
                                                        async:false,
                                                        type : "post",
                                                        success : function(datac){
                                                            if (datac>0){
                                                                jAlert('Se dió conformidad a la firma del contrato. Se procede a la alta del credito.', $.ucwords(_etiqueta_modulo),function(){
                                                                    $.unblockUI();
                                                                    $("#jqxgrid").show();
                                                                    $("#jqxgrid").jqxGrid('updatebounddata');
                                                                    $("#wpopup").html('');
                                                                    switchBarra();
                                                                });
                                                            }else{
                                                                //aun falta
                                                                jAlert('Aun no existen garantias Constituidas.', $.ucwords(_etiqueta_modulo) );
                                                            }
                                                        }
                                                    });
                                                    return false;
                                                }
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==12 && (jQuery.inArray( _USER_ROL, ['10'])!=-1 ) ){
                                                /*var conformidad_ac = $("#chk_altacredito").attr("checked");
                                                if ( conformidad_ac=='checked' ){
                                                    sw_asignar = 1;
                                                }*/
                                                // preguntar si ya tiene guardada la solicitud de alta de credito
                                                if ( $("#agregar_altacredito").data('credito')   ){
                                                    sw_asignar = 1;
                                                }else{
                                                    jAlert('Para pasar de etapa debe cargar una Solicitud de Alta de Crédito.', $.ucwords(_etiqueta_modulo),function(){
                                                        
                                                    });
                                                }
                                            }
                                            
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==12 && _array_obj.ID_PROCESO==1 && (jQuery.inArray( _USER_ROL, ['20'])!=-1 ) ){
                                                
                                                var idcredito = $("#agregar_altacredito").data("credito");
                                                if (idcredito){
                                                    //preguntar si se genero el credito
                                                        $.ajax({
                                                            url : _carpetas.URL + "/x_get_tienecuotas",
                                                            data : {
                                                                idcredito:idcredito
                                                            },
                                                            async:false,
                                                            dataType : "json",
                                                            type : "post",
                                                            success : function(datac){
                                                                if (datac>0){
                                                                    //existe
                                                                    var conformidad_acf = $("#chk_altacredito").attr("checked");
                                                                    if ( conformidad_acf=='checked' ){
                                                                        sw_asignar = 1;
                                                                    }
                                                                }else{
                                                                    //no existe
                                                                    jAlert('Este Crédito aún no tiene generada las cuotas. Para pasar de etapa, genere las cuotas por favor.', $.ucwords(_etiqueta_modulo) );
                                                                }
                                                            }
                                                        });
                                                    
                                                }
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==12 && _array_obj.ID_PROCESO==2 && (jQuery.inArray( _USER_ROL, ['20'])!=-1 ) ){
                                                
                                                var idcredito = $("#agregar_altacredito").data("credito");
                                                if (idcredito){
                                                    //preguntar si se genero el credito
                                                        $.ajax({
                                                            url : _carpetas.URL + "/x_get_tienecuotas",
                                                            data : {
                                                                idcredito:idcredito
                                                            },
                                                            async:false,
                                                            dataType : "json",
                                                            type : "post",
                                                            success : function(datac){
                                                                if (datac>0){
                                                                    //existe
                                                                    var conformidad_acf = $("#chk_altacredito").attr("checked");
                                                                    if ( conformidad_acf=='checked' ){
                                                                        //sw_asignar = 1;
                                                                        jAlert('Se dió conformidad al Alta de Credito. Se pasa a la etapa de Desembolso.', $.ucwords(_etiqueta_modulo),function(){
                                                                            $.unblockUI();
                                                                            $("#jqxgrid").show();
                                                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                                                            $("#wpopup").html('');
                                                                            switchBarra();
                                                                        });
                                                                        
                                                                    }
                                                                }else{
                                                                    //no existe
                                                                    jAlert('Este Crédito aún no tiene generada las cuotas. Para pasar de etapa, genere las cuotas por favor.', $.ucwords(_etiqueta_modulo) );
                                                                }
                                                            }
                                                        });
                                                    
                                                }
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==13 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){
                                                    sw_asignar = 1;
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==13 && (jQuery.inArray( _USER_ROL, ['20'])!=-1 ) ){
                                                    sw_asignar = 1;
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==13 && (jQuery.inArray( _USER_ROL, ['22'])!=-1 ) ){
                                                    sw_asignar = 1;
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==13 && (jQuery.inArray( _USER_ROL, ['24'])!=-1 ) ){
                                                    sw_asignar = 1;
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==13 && (jQuery.inArray( _USER_ROL, ['12','13','14','15','16','17','18'])!=-1 ) ){
                                                    sw_asignar = 1;
                                            }
                                            
                                            if ( _array_obj.ID_ETAPA_ACTUAL==14 && (jQuery.inArray( _USER_ROL, ['20'])!=-1 ) ){
                                                    sw_asignar = 1;
                                            }

                                            if (_array_obj.ID_ETAPA_ACTUAL==3 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){

                                                var c1 = $("#chk_legales").is(":checked");
                                                var c2 = $("#chk_patrimoniales").is(":checked");
                                                var c3 = $("#chk_tecnicos").is(":checked");
                                                var d1 = $("#chk_legales_nc").is(":checked");
                                                var d2 = $("#chk_patrimoniales_nc").is(":checked");
                                                var d3 = $("#chk_tecnicos_nc").is(":checked");
                                                
                                                if ( (c1 || d1) && (c2 || d2) && (c3 || d3)){
                                                                                                        
                                                    $.ajax({
                                                        url : _carpetas.URL + "/x_get_num_garantias",
                                                        data : {
                                                            idope:_array_obj.ID
                                                        },
                                                        type : "post",
                                                        async: false,
                                                        success : function(dat_num_gar){

                                                            if (dat_num_gar<1){
                                                                jAlert('Para pasar de etapa debe cargar por lo menos una garantia.', $.ucwords(_etiqueta_modulo),function(){

                                                                });
                                                                return false;
                                                            }else{
                                                                sw_asignar = 1;

                                                                if ( _array_obj.ID_ETAPA_ACTUAL==9 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){
                                                                    if( $("#opt_comite2").is(':checked')){
                                                                        jAlert('Se aceptó la carpeta. Se procede a la confección del contrato.', $.ucwords(_etiqueta_modulo),function(){
                                                                            $.unblockUI();
                                                                            $("#jqxgrid").show();
                                                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                                                            $("#wpopup").html('');
                                                                            switchBarra();
                                                                        });
                                                                        return false;
                                                                    }
                                                                }

                                                                if (_array_obj.ID_ETAPA_ACTUAL==3 ){
                                                                    sw_asignar = 1;
                                                                }

                                                                if (sw_asignar == 1){
                                                                    $(".asignar").trigger('click');
                                                                }else{
                                                                    $("#jqxgrid").show();
                                                                    $("#jqxgrid").jqxGrid('updatebounddata');
                                                                    $("#wpopup").html('');
                                                                    switchBarra();
                                                                }

                                                            }

                                                        }
                                                    });   
                                                    
                                                }else{
                                                    sw_asignar = 1;

                                                    if ( _array_obj.ID_ETAPA_ACTUAL==9 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){
                                                        if( $("#opt_comite2").is(':checked')){
                                                            jAlert('Se aceptó la carpeta. Se procede a la confección del contrato.', $.ucwords(_etiqueta_modulo),function(){
                                                                $.unblockUI();
                                                                $("#jqxgrid").show();
                                                                $("#jqxgrid").jqxGrid('updatebounddata');
                                                                $("#wpopup").html('');
                                                                switchBarra();
                                                            });
                                                            return false;
                                                        }
                                                    }

                                                    if (_array_obj.ID_ETAPA_ACTUAL==3 ){
                                                        sw_asignar = 1;
                                                    }

                                                    if (sw_asignar == 1){
                                                        $(".asignar").trigger('click');
                                                    }else{
                                                        $("#jqxgrid").show();
                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                        $("#wpopup").html('');
                                                        switchBarra();
                                                    }
                                                                                                   
                                                    
                                                    
                                                    
                                                }
                                            }else{
                                                
                                                if (_array_obj.ID_ETAPA_ACTUAL==9 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){
                                                    $.ajax({
                                                            url : _carpetas.URL + "/x_get_gar_comite",
                                                            data : {
                                                                idope:_array_obj.ID
                                                            },
                                                            type : "post",
                                                            async: false,
                                                            success : function(dat_sw_gar){
                                                                if (dat_sw_gar <= -1 ){
                                                                    jAlert('Para pasar de etapa, todas las garantías deben estar aprobadas o desaprobadas.', $.ucwords(_etiqueta_modulo),function(){
                                                                    });
                                                                    return false;
                                                                }else if(dat_sw_gar>0){
                                                                    var maprobado = $("#macta").val();
                                                                    console.log ( 'comparar: ' + maprobado + ' - ' + dat_sw_gar );
                                                                    if (maprobado*1>dat_sw_gar*1){
                                                                        jAlert('Para pasar de etapa, la suma de las garantías aprobadas debe ser igual o mayor al monto aprobado por el Comité.', $.ucwords(_etiqueta_modulo),function(){
                                                                        });
                                                                        return false;
                                                                    }else{
                                                                        if ( _array_obj.ID_ETAPA_ACTUAL==9 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){
                                                                        if( $("#opt_comite2").is(':checked')){
                                                                                jAlert('Se aceptó la carpeta. Se procede a la confección del contrato.', $.ucwords(_etiqueta_modulo),function(){
                                                                                    $.unblockUI();
                                                                                    $("#jqxgrid").show();
                                                                                    $("#jqxgrid").jqxGrid('updatebounddata');
                                                                                    $("#wpopup").html('');
                                                                                    switchBarra();
                                                                                });
                                                                                return false;
                                                                            }
                                                                        }

                                                                        sw_asignar = 1;

                                                                        if (_array_obj.ID_ETAPA_ACTUAL==3 ){
                                                                            sw_asignar = 1;
                                                                        }

                                                                        if (sw_asignar == 1){
                                                                            $(".asignar").trigger('click');
                                                                        }else{
                                                                            $("#jqxgrid").show();
                                                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                                                            $("#wpopup").html('');
                                                                            switchBarra();
                                                                        }
                                                                        
                                                                        
                                                                    }
                                                                }else{
                                                                    
                                                                    if ( _array_obj.ID_ETAPA_ACTUAL==9 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){
                                                                        if( $("#opt_comite2").is(':checked')){
                                                                            jAlert('Se aceptó la carpeta. Se procede a la confección del contrato.', $.ucwords(_etiqueta_modulo),function(){
                                                                                $.unblockUI();
                                                                                $("#jqxgrid").show();
                                                                                $("#jqxgrid").jqxGrid('updatebounddata');
                                                                                $("#wpopup").html('');
                                                                                switchBarra();
                                                                            });
                                                                            return false;
                                                                        }
                                                                    }
                                                                    
                                                                    sw_asignar = 1;

                                                                    if (_array_obj.ID_ETAPA_ACTUAL==3 ){
                                                                        sw_asignar = 1;
                                                                    }

                                                                    if (sw_asignar == 1){
                                                                        $(".asignar").trigger('click');
                                                                    }else{
                                                                        $("#jqxgrid").show();
                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                        $("#wpopup").html('');
                                                                        switchBarra();
                                                                    }

                                                                }

                                                            }
                                                        });
                                                }else{
                                                    
                                                    if ( _array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL==9 && (jQuery.inArray( _USER_ROL, ['10'])!=-1 ) ){
                                                        if( $("#opt_comite2").is(':checked')){
                                                            jAlert('Se aceptó la carpeta. Se procede a la confección del contrato.', $.ucwords(_etiqueta_modulo),function(){
                                                                $.unblockUI();
                                                                $("#jqxgrid").show();
                                                                $("#jqxgrid").jqxGrid('updatebounddata');
                                                                $("#wpopup").html('');
                                                                switchBarra();
                                                            });
                                                            return false;
                                                        }
                                                    }
                                                    
                                                    
                                                    if ( _array_obj.ID_ETAPA_ACTUAL==9 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){
                                                        if( $("#opt_comite2").is(':checked')){
                                                            jAlert('Se aceptó la carpeta. Se procede a la confección del contrato.', $.ucwords(_etiqueta_modulo),function(){
                                                                $.unblockUI();
                                                                $("#jqxgrid").show();
                                                                $("#jqxgrid").jqxGrid('updatebounddata');
                                                                $("#wpopup").html('');
                                                                switchBarra();
                                                            });
                                                            return false;
                                                        }
                                                    }
                                                    
                                                    if (  _array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL==11 && (jQuery.inArray( _USER_ROL, ['11'])!=-1 ) ){
                                                        sw_asignar = 1;
                                                    }
                                                    
                                                    if (_array_obj.ID_ETAPA_ACTUAL==3 ){
                                                        sw_asignar = 1;
                                                    }

                                                    if (sw_asignar == 1){
                                                        $(".asignar").trigger('click');
                                                    }else{
                                                        $("#jqxgrid").show();
                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                        $("#wpopup").html('');
                                                        switchBarra();
                                                    }
                                                    
                                                }
                                            }
                                            
                                        });
                                        
                                    }else{
                                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                        });
                                    }
                                }
                            });
                            
                        });
                        
                        
                        
                        
                        ev_hide_it();
                        
                        //solo hacer si el usuario es el permitido
                        if (_array_obj.CARTERADE == _USUARIO_SESION_ACTUAL ){
                            
                            evento_lista_req();
                        
                            /*
                            $(".grid_reqs .lista_reqs li.ya_enviado").off().click(function(e){
                                e.preventDefault();
                                //stopBubble(e);
                                jAlert('Este requerimiento ya fue enviado al Solicitante.', $.ucwords(_etiqueta_modulo),function(){
                                    $.unblockUI();
                                });
                            });
                            */
                        }
                        
                        
                        var arr_get_enviar_a = [];
                        arr_get_enviar_a = [ {area:4,puesto:6} ]; // operaciones , jefe de operaciones
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==2){
                            arr_get_enviar_a = [ {area:4,puesto:7} ]; // operaciones, coordinador de operaciones
                        }else if ( (_array_obj.ID_ETAPA_ACTUAL==3 || _array_obj.ID_ETAPA_ACTUAL==4 || _array_obj.ID_ETAPA_ACTUAL==5 || _array_obj.ID_ETAPA_ACTUAL==6) && jQuery.inArray(_USER_AREA, ['5','6','7'])!=-1 ){
                            arr_get_enviar_a = [ {area:4,puesto:7} ]; // operaciones , coordinador de operaciones
                        }else if (_array_obj.ID_ETAPA_ACTUAL==3){
                            arr_get_enviar_a = [ 3,4,5,6,7 ]; //areas de operaciones ( legales, tecnico y patrimonial )
                        }else if ( _array_obj.ID_ETAPA_ACTUAL==8 ){
                            arr_get_enviar_a = [ 4 ]; // operaciones , coordinador de operaciones
                        }
                        
                        var opt_puesto=0;
                        var opt_area=0;
                        
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==1){
                            if (_array_obj.ID_PROCESO=='2'){
                                opt_area = [4];
                                opt_puesto = 7;
                            }else{
                                opt_area = [4];
                                opt_puesto = 6;
                            }
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==2){
                            opt_area = [4];
                            opt_puesto = 7;
                        }
                        
                        //solo mostrar operaciones si los 3 sectores son suficientes (conformidad o no corresponde)
                        
                        var tmp_permi  = [5,6,7];
                        if (_array_obj.ID_ETAPA_ACTUAL==3){
                            opt_area = tmp_permi;
                            opt_puesto = 6;
                        }
                        
                        if ( jQuery.inArray(_array_obj.ID_ETAPA_ACTUAL, ['4','5','6'])!=-1 ){
                            //cordinador de operaciones
                            opt_area = [4];
                            opt_puesto = 7;
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==8){
                            opt_area = [4];
                            opt_puesto = 7;
                        }
                        if (_array_obj.ID_ETAPA_ACTUAL==9){
                            /*
                            opt_area = [4];
                            opt_puesto = 6;
                            */
                        }
                        
                        // 12, 13 14 legales
                        // 10 jefe de operaciones
                        if (_array_obj.ID_ETAPA_ACTUAL==10 && jQuery.inArray(_USER_ROL, ['12','13','14'])!=-1){
                            
                            var conformidad_cr = $("#chk_rcontrato").attr("checked");
                            if (conformidad_cr=='checked'){ // conformidad
                                opt_area = [4];
                                opt_puesto = 6;
                            }else{
                                opt_area = [4];
                                opt_puesto = 7;
                            }    
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==10 && _USER_ROL==11){
                            opt_area = [5];
                        }
                        
                        //proceso 2
                        if ( _array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL==10 && jQuery.inArray(_USER_ROL, ['12','13','14'])!=-1 ){
                            opt_area = [4];
                            opt_puesto = 7;
                        }
                        if (_array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL==10 && _USER_ROL==10 ){
                            opt_area = [5];
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==11 && jQuery.inArray(_USER_ROL, ['12','13','14'])!=-1){
                            opt_area = [4];
                            opt_puesto = 6;
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==11 && _USER_ROL==10){
                            opt_area = [4];
                            opt_puesto = 7;
                            //zzzzzzzzzzzzzzzzz
                        }
                        
                        if (_array_obj.ID_PROCESO==1 && _array_obj.ID_ETAPA_ACTUAL==11 && _USER_ROL==11){
                            opt_area = [4];
                            opt_puesto = 6;
                        }
                        
                        if (_array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL==11 && _USER_ROL==11){
                            opt_area = [4];
                            opt_puesto = 6;
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==12 && _USER_ROL==10){// enviar a Jefe de adminsitracion de credito (para Alta de Credito)
                            opt_area = [10];
                            opt_puesto = 21;
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==12 && _USER_ROL==20){// enviar a Jefe de adminsitracion de credito (para Alta de Credito)
                            opt_area = [4];
                            opt_puesto = 7;
                        }
                        
                        /* Iteracion Desembolsos */
                        
                        /* Iteracion Desembolsos* Proceso 2 */
                        // primera etapa del desembolso: 
                        if (_array_obj.ID_ETAPA_ACTUAL==13 && _array_obj.ID_PROCESO==2 && _USER_ROL==11){
                            opt_area = [9];
                            opt_puesto = 18;
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==13 && _array_obj.ID_PROCESO==2 && _USER_ROL==22){// 
                            opt_area = [4];
                            opt_puesto = 7;
                        }
                        
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==13 && _array_obj.ID_PROCESO==1 && _USER_ROL==11){// primera etapa del desembolso: enviar a legales(auditoria) o enviar a jefe de administracion de creditos para desembolso
                            //opt_area = [5,6,7,10];
                            opt_area = [5,6,7,9];
                            //opt_puesto = 21;
                            opt_puesto = 19;
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==13 && _array_obj.ID_PROCESO==1 && _USER_ROL==20){// segunda etapa del desembolso: enviar a jefe de contabilidad para desembolso
                            /*opt_area = [9,4];
                            opt_puesto = [18,7];*/
                            opt_area = [9];
                            opt_puesto = 18;
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==13 && _array_obj.ID_PROCESO==1 && _USER_ROL==22){// tercera etapa del desembolso: enviar a Adminsitrativo contable con autorizacion de Gerente de Administracion y Finanzas
                            opt_area = [9];
                            opt_puesto = 19;
                        }
                        
                        //modificacion
                        if (_array_obj.ID_ETAPA_ACTUAL==13 && _array_obj.ID_PROCESO==1 && _USER_ROL==24){// cuarta etapa del desembolso: enviar a Jefe de adminsitracion de creditos
                            opt_area = [9,4];
                            opt_puesto = [19,7];
                        }
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==13 && _array_obj.ID_PROCESO==1 && jQuery.inArray(_USER_ROL, ['12','13','14','15','16','17','18'])!=-1){
                            opt_area = [4];
                            opt_puesto = 7;
                        }
                        
                        
                        if (_array_obj.ID_ETAPA_ACTUAL==14 && _array_obj.ID_PROCESO==2 && jQuery.inArray(_USER_ROL, ['20'])!=-1){
                            opt_area = [4];
                            opt_puesto = 7;
                        }
                        
                        

                        //$('.asignar').show();
                        $('.asignar').on('click', function(event){
                            event.preventDefault();

                            if (_array_obj.ID_ETAPA_ACTUAL==3 && _USER_ROL==11){
                                var c1 = $("#chk_legales").is(":checked");
                                var c2 = $("#chk_patrimoniales").is(":checked");
                                var c3 = $("#chk_tecnicos").is(":checked");
                                var d1 = $("#chk_legales_nc").is(":checked");
                                var d2 = $("#chk_patrimoniales_nc").is(":checked");
                                var d3 = $("#chk_tecnicos_nc").is(":checked");
                                if ( (c1 || d1) && (c2 || d2) && (c3 || d3)){
                                    opt_area = [4,5,6,7];
                                }
                            }
                            
                            
                            //console.log('cont_legales: ' + cont_legales);
                            
                            if (cont_legales==1){
                                if (_array_obj.ID_ETAPA_ACTUAL==11 && jQuery.inArray(_USER_ROL, ['10'])!=-1){
                                    opt_area = [5];
                                    opt_puesto = [8,9,10];
                                }
                                
                            }
                            
                            
                            
                            if (_array_obj.ID_ETAPA_ACTUAL==10 && jQuery.inArray(_USER_ROL, ['12','13','14'])!=-1){
                            
                                var conformidad_cr = $("#chk_rcontrato").is(':checked');
                                if (conformidad_cr){ // conformidad
                                    opt_area = [4];
                                    opt_puesto = 6;
                                }else{
                                    opt_area = [4];
                                    opt_puesto = 7;
                                }    
                            }
                            

                            $.ajax({
                                url : _carpetas.URL + "/x_getenviar_a1",
                                data : {
                                    puesto_in:opt_puesto,// parametro opcional
                                    area:opt_area
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data1){

                                    var clase_asignar;
                                    var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div>';
                                    if(data1){
                                        $.each(data1, function (index, value){
                                            clase_asignar = 'link_asignar';
                                            if (value.IID!=_USUARIO_SESION_ACTUAL){
                                                //si estamos en operaciones, solo poner puesto, si es etapa = 3
                                                
                                                if (value.ETAPA==3 || value.ETAPA==12 || value.ETAPA==13){
                                                    if (index==1){
                                                        cadhtml +=  '<div data-xxx="11111" class="' + clase_asignar + ' x_area" data-etapa="'+value.ETAPA+'" data-iid="'+value.ID+'" data-puesto_in="'+value.puesto_in+'"><span>' + value.DENOMINACION+'</span></div>';
                                                    }else{
                                                        if (value.puesto_in1){
                                                            cadhtml +=  '<div data-xxx="2222222" class="' + clase_asignar + ' x_area" data-etapa="'+value.ETAPA+'" data-iid="'+value.ID+'" data-puesto_in="'+value.puesto_in1+'"><span>' + value.DENOMINACION +'</span></div>';
                                                        }else{
                                                            cadhtml +=  '<div data-xxx="3333333" class="' + clase_asignar + ' x_area" data-etapa="'+value.ETAPA+'" data-iid="'+value.ID+'" data-puesto_in="'+value.puesto_in+'"><span>' + value.DENOMINACION +'</span></div>';
                                                        }
                                                    }
                                                    //cadhtml +=  '<div class="' + clase_asignar + ' x_area" data-etapa="'+value.ETAPA+'" data-iid="'+value.ID+'" data-puesto_in="'+value.puesto_in+'"><span>' + value.DENOMINACION;
                                                    //if (value.puesto_in1)
                                                        //cadhtml +=  '<div class="' + clase_asignar + ' x_area" data-etapa="'+value.ETAPA+'" data-iid="'+value.ID+'" data-puesto_in="'+value.puesto_in1+'"><span>' + value.DENOMINACION;
                                                }
                                                else{
                                                    cadhtml +=  '<div data-xxx="4444444" class="' + clase_asignar + ' x_area" data-etapa="'+value.ETAPA+'" data-iid="'+value.ID+'"><span>' + value.DENOMINACION  +'</span></div>';
                                                }
                                                
                                                //cadhtml += '</span></div>';
                                                
                                            }
                                        });
                                    }
                                                                         
                                    $.fancybox({
                                        "content": cadhtml,
                                        'padding'   :  35,
                                        'autoScale' :true,
                                        'height' : 900,
                                        'scrolling' : 'no',
                                        'beforeClose': function() { 
                                            if (myfancy==1)
                                                regresar_a_listado();
                                        }
                                    });
                                    
               
                                    $(".x_area").click(function(e1){
                                        e1.preventDefault();
                                        var tmpfancy = myfancy;
                                        myfancy=0;
                                        
                                        var iid = $(this).data('iid');
                                        var apuesto_in = $(this).data('puesto_in');
                                        apuesto_in = isNaN(apuesto_in)?'':apuesto_in;
                                        
                                        
                                        $.ajax({
                                            url : _carpetas.URL + "/x_getenviar_a2",
                                            data : {
                                                id_area:iid,
                                                puesto_in:apuesto_in
                                            },
                                            error: function (xhr, ajaxOptions, thrownError) {
                                              alert(xhr.status);
                                              alert(thrownError);
                                            },
                                            dataType : "json",
                                            type : "post",
                                            success : function(datar){
                                                //console.dir(datar);
                                                var clase_asignar;
                                                var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div> <div class="regresar_ar">Regresar</div>';
                                                if(datar){
                                                    
                                                    console.log("xxxxx:::::");
                                                    console.dir(_array_obj);
                                                    //_array_obj.obj_operatoria.COORDOPE
                                                    
                                                    

                                                    if (parseFloat(_array_obj.obj_operatoria.COORDOPE)<=0){
                                                        if (( _array_obj.ID_ETAPA_ACTUAL==1 && _USER_ROL==10 )  ){
                                                            cadhtml +=  '<div class="' + clase_asignar + '" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'"><span>' + value.NOMBRE + ' ' + value.APELLIDO+ ' ('+ value.AREA+ ' - ' + value.PUESTO+')';
                                                            cadhtml += '</span></div>';
                                                        }
                                                    }
                                                    
                                                                                                        
                                                    $.each(datar, function (index, value){
                                                        console.log( 'ACTUAL: '+ _array_obj.ID_ETAPA_ACTUAL );
                                                        console.log( 'rol: '+_USER_ROL);
                                                        console.log( 'ETAPA: '+value.ETAPA );

                                                        clase_asignar = 'link_asignar';
                                                        
                                                        if (value.ETAPA==3 && _array_obj.ID_ETAPA_ACTUAL!=2 && _array_obj.ID_ETAPA_ACTUAL!=1 && _array_obj.ID_ETAPA_ACTUAL!=6 && _array_obj.ID_ETAPA_ACTUAL!=5 && _array_obj.ID_ETAPA_ACTUAL!=4){
                                                            clase_asignar = 'link_asignar_tipob';
                                                        }else if(value.ETAPA==12 && _array_obj.ID_ETAPA_ACTUAL==12 ){
                                                            clase_asignar = 'link_asignar_tipob';
                                                        }else if((value.ETAPA==12 || value.ETAPA==4) && _array_obj.ID_ETAPA_ACTUAL==13 ){
                                                            clase_asignar = 'link_asignar_tipob';
                                                        }else if((value.ETAPA==13) && _array_obj.ID_ETAPA_ACTUAL==13 ){
                                                            clase_asignar = 'link_asignar_tipob';
                                                        }else if((value.ETAPA==5 || value.ETAPA==6 || value.ETAPA==4) && _array_obj.ID_ETAPA_ACTUAL==13 ){
                                                            clase_asignar = 'link_asignar_tipob';
                                                        }
                                                        
                                                        var info_div='';
                                                        if (parseFloat(_array_obj.obj_operatoria.COORDOPE)>0 && _array_obj.obj_operatoria.COORDOPE==value.IID && _USER_ROL==9 && _array_obj.ID_ETAPA_ACTUAL==1){
                                                            console.log('rrr11111');
                                                            info_div =  '<div class="myButton acomite" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Acta de Comité</div>';
                                                            cadhtml +=  '<div class="' + clase_asignar + '" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'"><span>' + value.NOMBRE + ' ' + value.APELLIDO+ ' ('+ value.AREA+ ' - ' + value.PUESTO+')';
                                                            if(clase_asignar=='link_asignar_tipob')
                                                                cadhtml += '</span><div class="info_div">'+info_div+'</div>'+ '</div>';
                                                            else
                                                                cadhtml += '</span></div>';
                                                            return false;
                                                        }else{
                                                            console.log('rrr22222');
                                                            if (1 || parseFloat(_array_obj.obj_operatoria.COORDOPE)<=0){
                                                                //si es igual a Jefe de Operaciones (PUESTO = 6)
                                                                //if (value.IID!=_USUARIO_SESION_ACTUAL && (value.PUESTOID=='6' || value.PUESTOID=='7')){
                                                                if (value.IID!=_USUARIO_SESION_ACTUAL || ( _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==24 && value.ETAPA=='13' ) || ( _array_obj.ID_ETAPA_ACTUAL==1 && _USER_ROL==10 && value.ETAPA=='3' )  ){
                                                                    cadhtml +=  '<div class="' + clase_asignar + '" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'"><span>' + value.NOMBRE + ' ' + value.APELLIDO+ ' ('+ value.AREA+ ' - ' + value.PUESTO+')';
                                                                    //console.log('etapa actual: '+_array_obj.ID_ETAPA_ACTUAL);
                                                                    //console.log('rol: '+_USER_ROL);
                                                                    if( _array_obj.ID_ETAPA_ACTUAL==8 && _USER_ROL==10) //_USER_ROL 11 es rol de Coordinador de Op
                                                                        info_div =  '<div class="myButton acomite" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Acta de Comité</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==3 && _USER_ROL==11) //
                                                                        info_div =  '<div class="myButton acomite" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Elevación a Comité</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==9 && _USER_ROL==11) //
                                                                        info_div =  '<div class="myButton acontrato" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Confeccion de Contrato</div>';
                                                                    else if( _array_obj.ID_PROCESO == 1 && _array_obj.ID_ETAPA_ACTUAL==10 && jQuery.inArray(_USER_ROL, ['12','13','14'])!=-1){
                                                                        var conformidad_cr = $("#chk_rcontrato").is(':checked');

                                                                        if (conformidad_cr){ // conformidad
                                                                            info_div =  '<div class="myButton afirmacontrato" data-proceso="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Firma de Contrato</div>';
                                                                        }else{
                                                                            info_div =  '<div class="myButton afirmacontrato" data-proceso="1" data-regresar="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Firma de Contrato222</div>';
                                                                        }

                                                                    }else if( _array_obj.ID_PROCESO == 2 && _array_obj.ID_ETAPA_ACTUAL==10 && jQuery.inArray(_USER_ROL, ['12','13','14'])!=-1) //
                                                                        info_div =  '<div class="myButton afirmacontrato" data-proceso="2" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Solicitud de Desembolso (Acotado)</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==11 && _USER_ROL==10) //
                                                                        info_div =  '<div class="myButton adelegacionfirmacontrato" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Delegación de Firma de Contrato</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==11 && _USER_ROL==11 && _array_obj.ID_PROCESO == 1) //
                                                                        info_div =  '<div class="myButton adelegacionfirmacontrato" data-deleg_respuesta="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Respuesta a Delegación de Firma de Contrato</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==11 && _USER_ROL==11 && _array_obj.ID_PROCESO == 2) //
                                                                        info_div =  '<div class="myButton adelegacionfirmacontrato" data-aalta_credito_proceso2="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Alta de Credito (Acotado)</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==12 && _USER_ROL==10) //
                                                                        info_div =  '<div class="myButton aaltacredito" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Alta de Crédito</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==12 && _USER_ROL==20) //
                                                                        info_div =  '<div class="myButton aaltacredito" data-paradesembolso="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Desembolsos</div>';
                                                                    else if(_array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==11 && value.ETAPA==13) //
                                                                        //_array_obj.ID_ETAPA
                                                                        info_div =  '<div class="myButton aaltacredito" data-paraemisiondesembolso="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Emisión de Desembolso (Acotado)</div>';
                                                                    else if(_array_obj.ID_PROCESO==1 && _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==11 && value.ETAPA==13) //
                                                                        info_div =  '<div class="myButton aaltacredito" data-paraemisiondesembolso3="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Desembolso</div>';
                                                                    else if(_array_obj.ID_PROCESO==1 && _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==24 && value.ETAPA==13) //
                                                                        info_div =  '<div class="myButton aaltacredito" data-paraemisiondesembolso4="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Desembolso (Autopase)</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==11 && value.ETAPA==12)//
                                                                        info_div =  '<div class="myButton aaltacredito" data-paraemisiondesembolso2="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Emisión de Desembolso</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==11 && (value.ETAPA==4 || value.ETAPA==5 || value.ETAPA==6 ) ) //
                                                                        info_div =  '<div class="myButton aaltacredito" data-paraauditoria="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Auditoría</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==13 && (_USER_ROL>=12 && _USER_ROL<=18 ) && (value.ETAPA==3 || value.ETAPA==5 || value.ETAPA==6) ) //
                                                                        info_div =  '<div class="myButton aaltacredito" data-paradevolucion_a_cordina="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Cordinador en Desembolsos</div>';
                                                                    else if(_array_obj.ID_PROCESO==2 && _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==22 ){ //
                                                                        info_div =  '<div class="myButton aaltacredito" data-paradevolucion_a_cordina2="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para Cordinador en Desembolsos (Acotado)</div>';
                                                                    }
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==20 && value.ETAPA==13) //
                                                                        info_div =  '<div class="myButton aaltacredito" data-paraejecutardesembolso="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para ejecutar Desembolso</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==22 && value.ETAPA==13) //
                                                                        info_div =  '<div class="myButton adesembolso" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para realizar Desembolso</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==24 && value.ETAPA==3) //
                                                                        info_div =  '<div class="myButton adesembolso" data-paraotraiteracion="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para desembolso, auditoría o archivo</div>';
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==14 && _USER_ROL==20 && _array_obj.ID_PROCESO==2 ) //
                                                                        info_div =  '<div class="myButton adesembolso" data-paraotraiteracion2="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para desembolso, auditoría o archivo</div>';
                                                                    /*
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==20 && value.ETAPA==3) //
                                                                        info_div =  '<div class="myButton adesembolso" data-paraotraiteracion="1" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'">Para desembolso, auditoría o archivo</div>';
                                                                      */

                                                                    if(clase_asignar=='link_asignar_tipob')
                                                                        cadhtml += '</span><div class="info_div">'+info_div+'</div>'+ '</div>';
                                                                    else
                                                                        cadhtml += '</span></div>';

                                                                }
                                                            }
                                                            
                                                        }
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        
                                                    });
                                                }
                                                
                                                $.fancybox({
                                                    "content": cadhtml,
                                                    'padding'   :  35,
                                                    'autoScale' :true,
                                                    'height' : 900,
                                                    'scrolling' : 'no',
                                                    'beforeClose': function() { 
                                                        if (myfancy==1)
                                                            regresar_a_listado();
                                                    }
                                                });
                                                if (tmpfancy==1)
                                                    myfancy=1;
                                                

                                                $(".regresar_ar").click(function(e){
                                                    e.preventDefault();
                                                    $.fancybox.close();
                                                    $(".asignar").trigger('click');
                                                });
                                                
                                                $('.link_asignar_tipob').on({
                                                    click: function() {
                                                            event.preventDefault();
                                                            var xxx = $(this).find('span');
                                                            xxx.hide();
                                                            $(this).find('.info_div').show();
                                                    }
                                                });
                                                
                                                
                                                $('.adesembolso').on({
                                                    click: function() {
                                                        e.preventDefault();
                                                        
                                                        var iid = $(this).data('iid');
                                                        //var pararegistrodesembolso  = $(this).data('pararegistrodesembolso');
                                                        var paraotraiteracion       = $(this).data('paraotraiteracion');
                                                        var paraotraiteracion2       = $(this).data('paraotraiteracion2');
                                                        
                                                        var observacion;
                                                        var estado;
                                                        var descripcion;
                                                        
                                                        var new_etapa = 0;
                                                        new_etapa = 13;
                                                        
                                                        if ( (paraotraiteracion2 && paraotraiteracion2==1) ){
                                                            descripcion='DE JEFE DE CREDITOS A COORDINADOR PARA OTRO DESEMBOLSO, AUDITORIA O ARCHIVO, ESPERANDO ACEPTACION'
                                                            autor = '0';
                                                            new_etapa = 13;
                                                        }else if ( (paraotraiteracion && paraotraiteracion==1) ){
                                                            descripcion='DE ADMINISTRATIVO CONTABLE A COORDINADOR PARA OTRO DESEMBOLSO, AUDITORIA O ARCHIVO, ESPERANDO ACEPTACION'
                                                            autor = '0';
                                                        }/*
                                                        else if ( (pararegistrodesembolso && pararegistrodesembolso==1) ){
                                                            descripcion='DE ADMINISTRATIVO CONTABLE A JEFE DE CREDITOS PARA REGISTRO DESEMBOLSO, ESPERANDO ACEPTACION'
                                                            autor = '0';
                                                        }*/else{
                                                            descripcion='DE JEFE DE CONTABILIDAD A ADMINISTRATIVO CONTABLE PARA EMITIR DESEMBOLSO, ESPERANDO AUTORIZACION Y ACEPTACION'
                                                            //autor = '41'; // gerente de operaciones
                                                            autor = _gfinanzas_id;
                                                        }

                                                        jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                                            if(r==true){
                                                                var id_usuario = iid;
                                                                //nueva etapa
                                                                
                                                                observacion='ENVIADO';
                                                                estado='2';
                                                                
                                                                console.log('a desembolso');
                                                                $.ajax({
                                                                    url : _carpetas.URL + "/x_actualizar_operacion",
                                                                    data : {
                                                                        OPERACION:mydata.IDOPE,
                                                                        ID_ETAPA_ACTUAL:new_etapa,
                                                                        USUARIO:id_usuario,
                                                                        OBSERVACION:observacion,
                                                                        DESCRIPCION:descripcion,
                                                                        ESTADO:estado,
                                                                        AUTOR:autor,
                                                                        ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL
                                                                    },
                                                                    dataType : "json",
                                                                    type : "post",
                                                                    success : function(data){
                                                                        $.fancybox.close();
                                                                        $("#jqxgrid").show();
                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                        $("#wpopup").html('');
                                                                    }
                                                                });

                                                            }
                                                        });
                                                    }
                                                });
                                                
                                                
                                                $(".link_asignar").click(function(e){
                                                    
                                                    e.preventDefault();
                                                    var iid = $(this).data('iid');
                                                    var new_etapa_data = $(this).data('etapa');

                                                    var observacion;
                                                    var estado;
                                                    var descripcion;
                                                    
                                                    //console.log('link_asignar: ' + cont_legales);
                                                    if (cont_legales==1){
                                                        
                                                        jConfirm('Esta seguro que desea enviar la Peticion de Confirmacion?.', $.ucwords(_etiqueta_modulo),function(r){
                                
                                                            if(r==true){

                                                                //zzzzzzzzzzzzzzzzzzzzzzzzzz
                                                                //opt_area = [4];
                                                                //opt_puesto = [8,9,10];
                                                                cont_legales = 1;

                                                                //$(".asignar").trigger('click');
                                                                //return false;
                                                                new_etapa = _array_obj.ID_ETAPA_ACTUAL;
                                                                //var id_usuario = $("#legales_et4").val();
                                                                var id_usuario = iid;
                                                                
                                                                observacion='NOTIFICACION';
                                                                descripcion='PETICION DE CONFIRMACION DE COPIA DE CONTRATO EN LEGALES';
                                                                estado='2';
                                                                notif='1';

                                                                $.ajax({
                                                                    url : _carpetas.URL + "/x_actualizar_operacion_notif",
                                                                    data : {
                                                                        OPERACION:mydata.IDOPE,
                                                                        ID_ETAPA_ACTUAL:new_etapa,
                                                                        USUARIO:id_usuario,
                                                                        OBSERVACION:observacion,
                                                                        DESCRIPCION:descripcion,
                                                                        ESTADO:estado,
                                                                        ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL,
                                                                        NOTIF:notif,
                                                                        usuario_notif:id_usuario
                                                                    },
                                                                    dataType : "json",
                                                                    type : "post",
                                                                    success : function(data){
                                                                        $.fancybox.close();
                                                                        /*
                                                                        $("#jqxgrid").show();
                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                        $("#wpopup").html('');
                                                                        */
                                                                        $("#copia_contrato").fadeOut(600);
                                                                        $("#text_fc").fadeOut(600);
                                                                        
                                                                        cont_legales=0;
                                                                    }
                                                                });
                                                            }

                                                        });
                                                        
                                                        
                                                    }else{
                                                        
                                                        jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                                            if(r==true){
                                                                var autor = 0;
                                                                var autor1 = 0;
                                                                var id_usuario = iid;
                                                                var notif = 0;
                                                                var usuario_notif = 0;
                                                                //nueva etapa
                                                                var new_etapa = 1;
                                                                if (_array_obj.ID_ETAPA_ACTUAL==1){
                                                                    new_etapa = 2;
                                                                    observacion='ENVIADO';
                                                                    descripcion='DE MESA DE ENTRADA A CONTROL INICIAL, ESPERANDO ACEPTACION'
                                                                    estado='2'
                                                                }
                                                                else if(_array_obj.ID_ETAPA_ACTUAL==2){
                                                                    new_etapa = 3;
                                                                    observacion='ENVIADO';
                                                                    descripcion='DE CONTROL INICIAL A COORDINADOR DE OPERACIONES, ESPERANDO ACEPTACION'
                                                                    estado='2'
                                                                }
                                                                else if(_array_obj.ID_ETAPA_ACTUAL==3 || _array_obj.ID_ETAPA_ACTUAL==13){
                                                                    new_etapa = new_etapa_data;
                                                                    observacion='ENVIADO';
                                                                    if (new_etapa=='4')
                                                                        descripcion='DE COORDINADOR DE OPERACIONES A LEGALES, ESPERANDO ACEPTACION'
                                                                    else if(new_etapa=='5')
                                                                        descripcion='DE COORDINADOR DE OPERACIONES A PATRIMONIALES, ESPERANDO ACEPTACION'
                                                                    else if(new_etapa=='6')
                                                                        descripcion='DE COORDINADOR DE OPERACIONES A TECNICOS, ESPERANDO ACEPTACION'
                                                                    estado='2'
                                                                }
                                                                else if(_array_obj.ID_ETAPA_ACTUAL==4 || _array_obj.ID_ETAPA_ACTUAL==5 || _array_obj.ID_ETAPA_ACTUAL==6 || _array_obj.ID_ETAPA_ACTUAL==13){
                                                                    new_etapa = 3;
                                                                    observacion='ENVIADO';

                                                                    if (_array_obj.ID_ETAPA_ACTUAL=='4')
                                                                        descripcion='DE LEGALES ACOORDINADOR DE OPERACIONES, ESPERANDO ACEPTACION'
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL=='5')
                                                                        descripcion='DE PATRIMONIALES A COORDINADOR DE OPERACIONES, ESPERANDO ACEPTACION'
                                                                    else if(_array_obj.ID_ETAPA_ACTUAL=='6')
                                                                        descripcion='DE TECNICOS A COORDINADOR DE OPERACIONES, ESPERANDO ACEPTACION'
                                                                    estado='2';
                                                                    notif='1';
                                                                    //usuario_notif= '24';
                                                                    usuario_notif = $("#jefeope_h").val();

                                                                }
                                                                else if(_array_obj.ID_ETAPA_ACTUAL==8){
                                                                    /*
                                                                    new_etapa = 9;
                                                                    observacion='ENVIADO';
                                                                    descripcion='DE JEFE DE OPERACIONES A COMITE(COORD. OPERACIONES), ESPERANDO ACEPTACION'
                                                                    estado='2'*/
                                                                }else if(_array_obj.ID_ETAPA_ACTUAL==10 && jQuery.inArray(_USER_ROL, ['12','13','14'])!=-1){//legales contrato
                                                                    new_etapa = 10;
                                                                    observacion='ENVIADO';
                                                                    descripcion='DE LEGALES(CONFECCION CONTRATO) A COORDINADOR DE OPERACIONES, ESPERANDO ACEPTACION'
                                                                    estado='2';
                                                                    //autor = '37'; // gerente de operaciones
                                                                    autor = _goperaciones_id;
                                                                    //autor1 = '24'; // gerente de operaciones
                                                                    autor = $("#jefeope_h").val();
                                                                }else if(_array_obj.ID_ETAPA_ACTUAL==10){
                                                                    new_etapa = 10;
                                                                    observacion='ENVIADO';
                                                                    descripcion='DE COORDINADOR DE OPERACIONES A LEGALES(CONFECCION CONTRATO), ESPERANDO ACEPTACION'
                                                                    estado='2'
                                                                    //autor = '24'; // jefe de operaciones
                                                                    autor = $("#jefeope_h").val();
                                                                }

                                                                $.ajax({

                                                                    url : _carpetas.URL + "/x_actualizar_operacion",
                                                                    data : {
                                                                        OPERACION:mydata.IDOPE,
                                                                        ID_ETAPA_ACTUAL:new_etapa,
                                                                        USUARIO:id_usuario,
                                                                        OBSERVACION:observacion,
                                                                        DESCRIPCION:descripcion,
                                                                        ESTADO:estado,
                                                                        ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL,
                                                                        AUTOR:autor,
                                                                        AUTOR1:autor1,
                                                                        NOTIF:notif,
                                                                        usuario_notif:usuario_notif
                                                                    },
                                                                    dataType : "json",
                                                                    type : "post",
                                                                    success : function(data){
                                                                        $.fancybox.close();
                                                                        $("#jqxgrid").show();
                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                        $("#wpopup").html('');
                                                                    }
                                                                });

                                                            }
                                                        });
                                                        
                                                        
                                                        
                                                    }
                                                    
                                                    
                                                });
                                                
                                                
                                                $('.adelegacionfirmacontrato').on({
                                                    click: function() {
                                                        e.preventDefault();
                                                        
                                                        var iid = $(this).data('iid');
                                                        //var new_etapa_data = $(this).data('etapa');
                                                        var deleg_respuesta = $(this).data('deleg_respuesta');
                                                        var aalta_credito_proceso2 = $(this).data('aalta_credito_proceso2');
                                                        
                                                        var new_etapa = 0;
                                                        new_etapa = 11;
                                                        
                                                        var observacion;
                                                        var estado;
                                                        var descripcion;
                                                        
                                                        if ( (aalta_credito_proceso2 && aalta_credito_proceso2==1) ){
                                                            descripcion='DE COORDINADOR A JEFE DE OPERACIONES PARA ALTA DE CREDITO, ESPERANDO ACEPTACION'
                                                            new_etapa = 12;
                                                        }else if ( (deleg_respuesta && deleg_respuesta==1) ){
                                                            descripcion='DE COORDINADOR A JEFE DE OPERACIONES EN RESPUESTA A DELEGACION DE FIRMA DE CONTRATO, ESPERANDO ACEPTACION'
                                                        }else{
                                                            descripcion='DE JEFE DE OPERACIONES A COORDINADOR PARA FIRMA DE CONTRATO, ESPERANDO ACEPTACION'
                                                        }
                                                        
                                                        jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                                            if(r==true){
                                                                var id_usuario = iid;
                                                                //nueva etapa
                                                                observacion='ENVIADO';
                                                                estado='2';
                                                                console.log('a DELEGACION firma contrato');
                                                                $.ajax({
                                                                    url : _carpetas.URL + "/x_actualizar_operacion",
                                                                    data : {
                                                                        OPERACION:mydata.IDOPE,
                                                                        ID_ETAPA_ACTUAL:new_etapa,
                                                                        USUARIO:id_usuario,
                                                                        OBSERVACION:observacion,
                                                                        DESCRIPCION:descripcion,
                                                                        ESTADO:estado,
                                                                        ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL
                                                                    },
                                                                    dataType : "json",
                                                                    type : "post",
                                                                    success : function(data){
                                                                        $.fancybox.close();
                                                                        $("#jqxgrid").show();
                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                        $("#wpopup").html('');
                                                                    }
                                                                });

                                                            }
                                                        });
                                                            
                                                        
                                                        
                                                    }
                                                });
                                                
                                                
                                                $('.aaltacredito').on({
                                                    click: function() {
                                                        e.preventDefault();
                                                        
                                                        var iid = $(this).data('iid');
                                                        //var new_etapa_data = $(this).data('etapa');
                                                        var paradesembolso = $(this).data('paradesembolso');
                                                        var paraemisiondesembolso = $(this).data('paraemisiondesembolso');
                                                        var paraemisiondesembolso2 = $(this).data('paraemisiondesembolso2');
                                                        var paraemisiondesembolso3 = $(this).data('paraemisiondesembolso3');
                                                        var paraemisiondesembolso4 = $(this).data('paraemisiondesembolso4');
                                                        var paraauditoria = $(this).data('paraauditoria');
                                                        var paraejecutardesembolso = $(this).data('paraejecutardesembolso');
                                                        var paradevolucion_a_cordina = $(this).data('paradevolucion_a_cordina');
                                                        var paradevolucion_a_cordina2 = $(this).data('paradevolucion_a_cordina2');
                                                        
                                                        
                                                        
                                                        
                                                        
                                                        var observacion;
                                                        var estado;
                                                        var descripcion;
                                                        var new_etapa = 0;
                                                        var autor = 0;
                                                        var autor1 = 0;
                                                        var autor2 = 0;
                                                        if ( (paradevolucion_a_cordina2 && paradevolucion_a_cordina2==1) ){
                                                            descripcion='DE JEFE CONTABILIDAD A CORDINADOR PARA FIRMA DE CONTRATO Y DESEMBOLSO, ESPERANDO ACEPTACION'
                                                            new_etapa = 11;
                                                            //autor = '41';
                                                            autor = _gfinanzas_id;
                                                            
                                                        }else if ( (paradevolucion_a_cordina && paradevolucion_a_cordina==1) ){
                                                            descripcion='DE ANALISIS A CORDINADOR PARA DESEMBOLSO, ESPERANDO ACEPTACION'
                                                            new_etapa = 13;
                                                        }else if ( (paraejecutardesembolso && paraejecutardesembolso==1) ){
                                                            descripcion='DE JEFE DE CREDITOS A JEFE DE CONTABILIDAD PARA EJECUCION DE DESEMBOLSO, ESPERANDO ACEPTACION'
                                                            new_etapa = 13;
                                                        }else if ( (paraauditoria && paraauditoria==1) ){
                                                            descripcion='DE COORDINADOR DE OPERACIONES A LEGALES PARA AUDITORIA, ESPERANDO ACEPTACION'
                                                            new_etapa = 13;
                                                        }else if ( (paraemisiondesembolso2 && paraemisiondesembolso2==1) ){
                                                            descripcion='DE COORDINADOR DE OPERACIONES A JEFE DE CONTABILIDAD PARA EMISION DESEMBOLSO, ESPERANDO ACEPTACION'
                                                            new_etapa = 13;
                                                            //autor='24';
                                                            autor = $("#jefeope_h").val();
                                                            //autor1 = '37';
                                                            autor1 = _goperaciones_id;
                                                        }else if ( (paraemisiondesembolso && paraemisiondesembolso==1) ){
                                                            descripcion='DE COORDINADOR DE OPERACIONES A JEFE DE CREDITOS PARA EMISION DESEMBOLSO, ESPERANDO ACEPTACION'
                                                            new_etapa = 13;
                                                            //autor='24';
                                                            autor = $("#jefeope_h").val();
                                                            //autor1 = '37';
                                                            autor1 = _goperaciones_id;
                                                        }else if ( (paraemisiondesembolso3 && paraemisiondesembolso3==1) ){
                                                            descripcion='DE COORDINADOR DE OPERACIONES A ADMINISTRATIVO CONTABLE PARA EMISION DESEMBOLSO, ESPERANDO ACEPTACION'
                                                            new_etapa = 13;
                                                            //autor='24';
                                                            autor = $("#jefeope_h").val();
                                                            //autor1 = '37';
                                                            autor1 = _goperaciones_id;
                                                            autor2 = 38; // jefe finanzas
                                                        }else if ( (paraemisiondesembolso4 && paraemisiondesembolso4==1) ){
                                                            descripcion='SE LO QUEDA ADMINISTRATIVO CONTABLE PARA EMISION DESEMBOLSO, ESPERANDO APROBACIONES'
                                                            new_etapa = 13;
                                                            autor = 40;/// jefe de contabilidad
                                                            autor1 = 41; // gerente de administracion
                                                        }else if ( (paradesembolso && paradesembolso==1) ){
                                                            descripcion='DE JEFE DE CREDITOS A COORDINADOR DE OPERACIONES PARA DESEMBOLSOS, ESPERANDO ACEPTACION'
                                                            new_etapa = 13;
                                                        }else{
                                                            descripcion='DE JEFE DE OPERACIONES A JEFE DE CREDITOS PARA ALTA DE CREDITO, ESPERANDO ACEPTACION'
                                                            new_etapa = 12;
                                                        }
                                                        jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                                            if(r==true){
                                                                var id_usuario = iid;
                                                                //nueva etapa
                                                                observacion='ENVIADO';
                                                                estado='2';
                                                                console.log('a alta de credito');
                                                                $.ajax({
                                                                    url : _carpetas.URL + "/x_actualizar_operacion",
                                                                    data : {
                                                                        OPERACION:mydata.IDOPE,
                                                                        ID_ETAPA_ACTUAL:new_etapa,
                                                                        USUARIO:id_usuario,
                                                                        OBSERVACION:observacion,
                                                                        DESCRIPCION:descripcion,
                                                                        ESTADO:estado,
                                                                        ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL,
                                                                        AUTOR:autor,
                                                                        AUTOR1:autor1,
                                                                        AUTOR2:autor2
                                                                    },
                                                                    dataType : "json",
                                                                    type : "post",
                                                                    success : function(data){
                                                                        $.fancybox.close();
                                                                        $("#jqxgrid").show();
                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                        $("#wpopup").html('');
                                                                    }
                                                                });

                                                            }
                                                        });
                                                    }
                                                });
                                                
                                                $('.afirmacontrato').on({
                                                    click: function() {
                                                        e.preventDefault();
                                                        
                                                        var iid = $(this).data('iid');
                                                        //var new_etapa_data = $(this).data('etapa');
                                                        var idproceso = $(this).data('proceso');
                                                        var regresar = $(this).data('regresar');
                                                        

                                                        var observacion;
                                                        var estado;
                                                        var descripcion;
                                                        var new_etapa = 0;
                                                        new_etapa = 11;
                                                        
                                                        var autor = _goperaciones_id;
                                                        descripcion='DE LEGALES A JEFE DE OPERACIONES PARA FIRMA DE CONTRATO, ESPERANDO AUTORIZACION Y ACEPTACION';
                                                        if ( (regresar && regresar==1) ){
                                                            descripcion='DE LEGALES A COORDINADOR PARA REVISION DE CONFECCION DE CONTRATO, ESPERANDO ACEPTACION';
                                                            new_etapa = 10;
                                                            autor= 0 ;
                                                        }
                                                        if (idproceso==2){
                                                            descripcion='DE LEGALES A JEFE DE OPERACIONES PARA SOLICITUD DE DESEMBOLSO (ACOTADO), ESPERANDO AUTORIZACION Y ACEPTACION';
                                                            new_etapa = 13;
                                                        }
                                                        
                                                        

                                                        jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                                            if(r==true){
                                                                var id_usuario = iid;
                                                                //nueva etapa
                                                                
                                                                observacion='ENVIADO';
                                                                //descripcion='DE LEGALES A JEFE DE OPERACIONES PARA SOLICITUD DE DESEMBOLSO (ACOTADO), ESPERANDO AUTORIZACION Y ACEPTACION'
                                                                estado='2';
                                                                //autor = '37'; // gerente de operaciones
                                                                
                                                                console.log('a firma contrato');
                                                                $.ajax({
                                                                    url : _carpetas.URL + "/x_actualizar_operacion",
                                                                    data : {
                                                                        OPERACION:mydata.IDOPE,
                                                                        ID_ETAPA_ACTUAL:new_etapa,
                                                                        USUARIO:id_usuario,
                                                                        OBSERVACION:observacion,
                                                                        DESCRIPCION:descripcion,
                                                                        ESTADO:estado,
                                                                        AUTOR:autor,
                                                                        ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL
                                                                    },
                                                                    dataType : "json",
                                                                    type : "post",
                                                                    success : function(data){
                                                                        $.fancybox.close();
                                                                        $("#jqxgrid").show();
                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                        $("#wpopup").html('');
                                                                    }
                                                                });

                                                            }
                                                        });
                                                    }
                                                });
                                                
                                                $('.acontrato').on({
                                                    click: function() {
                                                        e.preventDefault();
                                                        var iid = $(this).data('iid');
                                                        //var new_etapa_data = $(this).data('etapa');

                                                        var observacion;
                                                        var estado;
                                                        var descripcion;
                                                        
                                                        jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                                            if(r==true){
                                                                var id_usuario = iid;
                                                                //nueva etapa
                                                                var new_etapa = 0;
                                                                new_etapa = 10;
                                                                observacion='ENVIADO';
                                                                descripcion='DE COMITE(COORD. OPERACIONES) A JEFE DE OPERACIONES, ESPERANDO ACEPTACION'
                                                                estado='2'
                                                                console.log('a contrato');
                                                                $.ajax({
                                                                    url : _carpetas.URL + "/x_actualizar_operacion",
                                                                    data : {
                                                                        OPERACION:mydata.IDOPE,
                                                                        ID_ETAPA_ACTUAL:new_etapa,
                                                                        USUARIO:id_usuario,
                                                                        OBSERVACION:observacion,
                                                                        DESCRIPCION:descripcion,
                                                                        ESTADO:estado,
                                                                        ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL
                                                                    },
                                                                    dataType : "json",
                                                                    type : "post",
                                                                    success : function(data){
                                                                        $.fancybox.close();
                                                                        $("#jqxgrid").show();
                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                        $("#wpopup").html('');
                                                                    }
                                                                });

                                                            }
                                                        });
                                                    }
                                                });
                                                
                                                $('.acomite').on({
                                                    click: function() {
                                                        e.preventDefault();
                                                        
                                                        var iid = $(this).data('iid');
                                                        //var new_etapa_data = $(this).data('etapa');

                                                        var observacion;
                                                        var estado;
                                                        var descripcion;
                                                        
                                                        jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                                            if(r==true){
                                                                var id_usuario = iid;
                                                                var autor = 0;
                                                                //nueva etapa
                                                                var new_etapa = 0;
                                                                if (_array_obj.ID_ETAPA_ACTUAL==8){
                                                                    new_etapa = 9;
                                                                    observacion='ENVIADO';
                                                                    descripcion='DE JEFE DE OPERACIONES A COMITE(COORD. OPERACIONES), ESPERANDO ACEPTACION';
                                                                    estado='2';
                                                                    //autor = '37'; // gerente de operaciones
                                                                    autor = _goperaciones_id;
                                                                }
                                                                else{
                                                                    new_etapa = 8;
                                                                    observacion='ENVIADO';
                                                                    descripcion='DE COORDINADOR OP. A JEFE OP. PARA ELEVACION A COMITE, ESPERANDO ACEPTACION';
                                                                    estado='2'
                                                                }

                                                                //console.log(_array_obj.ID_ETAPA_ACTUAL);
                                                                //console.log( _array_obj );
                                                                //console.log( new_etapa );
                                                                console.log('a comite');
                                                                $.ajax({
                                                                    url : _carpetas.URL + "/x_actualizar_operacion",
                                                                    data : {
                                                                        OPERACION:mydata.IDOPE,
                                                                        ID_ETAPA_ACTUAL:new_etapa,
                                                                        USUARIO:id_usuario,
                                                                        OBSERVACION:observacion,
                                                                        DESCRIPCION:descripcion,
                                                                        ESTADO:estado,
                                                                        ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL,
                                                                        AUTOR:autor
                                                                    },
                                                                    dataType : "json",
                                                                    type : "post",
                                                                    success : function(data){
                                                                        $.fancybox.close();
                                                                        $("#jqxgrid").show();
                                                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                                                        $("#wpopup").html('');
                                                                    }
                                                                });

                                                            }
                                                            
                                                        });


                                                    }
                                                });
                                                    

                                            }
                                        });
                                        
                                        
                                        
                                    });
                                    
                                    
                                    return false;
                                    
                                }
                            });
                        });
                        
                        
                        //if (num_items_select==num_items_all)
                        var items = $("#listbox").jqxListBox('getItems');
                        var checkedItems = [];
                        var allItems = [];
                        if(items){
                            $.each(items, function (index, value) {
                                //allItems.push(value.value);
                                if (value.checked)
                                    checkedItems.push(value.value);
                                allItems.push(value.value);
                            });
                        }

                        var num_items_all = allItems.length;
                        var num_items_select = checkedItems.length;
                        var conformidad = $("#chk_checklist").attr("checked");
                        
                        /*
                        if ( (num_items_all==num_items_select) && (conformidad=='checked') ){
                            //mostrar boton enviar
                            $(".asignar").show();
                            
                        }else{
                            //no mostrar boton enviar
                            $(".asignar").hide();
                        }
                        */
                            
                        event_d_tab_left();
                        
                        //$("#vtab li:first").trigger('click');
                        
                        

                        //  ! (EL USUARIO ES MESA DE ENTRADA y en carterade = 0)
                        if ( (_array_obj.CARTERADE != _USUARIO_SESION_ACTUAL)  && !(_USER_ROL==9 && _array_obj.CARTERADE==0   ) ){
                            jAlert('Usted no tiene En Cartera esta Carpeta.', $.ucwords(_etiqueta_modulo));
                        }  
                        
                        
                        agregar_requerimiento();
                                                
                        event_grid_traza( _array_obj.ID );
                              
                        $('.lista_adjuntos li').on('click', function(event){
                            event.preventDefault();
                            var desc = $(this).data('descripcion');
                            jAlert( desc, 'Adjuntos' );
                        });
                        
                        $('.lista_adjuntos a.delete_file').on('click', function(event){
                            event.preventDefault();
                            //var desc = $(this).data('descripcion');
                            var $_li = $(this).prev().prev();
                            //var iid = $_li.data('iid');
                            var idope = $_li.data('identidad');
                            var ruta = $_li.data('ruta');
                            var usuario = $_li.data('usuario');
                            var $this = $(this);
                            
                            if (usuario==_USUARIO_SESION_ACTUAL){
                                jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                                    if(r==true){
                                        //borrar archivo en la bd y fisicamente
                                        $.ajax({
                                            url : _carpetas.URL + "/x_delupload_ope",
                                            data : {
                                                idope:idope,
                                                ruta:ruta
                                            },
                                            dataType : "json",
                                            type : "post",
                                            success : function(data){
                                                $this.prev().prev().remove();
                                                $this.prev().remove();
                                                $this.remove();
                                            }
                                        });                                        
                                    }
                                });
                            }else{
                                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                                });
                            }
                        });
                        
                        
                        //si rol == a analistas
                        if( !(_USER_ROL==10 || _USER_ROL==11)){
                            $("#obs_legales").attr('disabled', true);
                            $("#chk_legales").attr('disabled', true);
                            $("#chk_legales_nc").attr('disabled', true);
                            
                            $("#obs_patrimoniales").attr('disabled', true);
                            $("#chk_patrimoniales").attr('disabled', true);
                            $("#chk_patrimoniales_nc").attr('disabled', true);
                            
                            $("#obs_tecnico").attr('disabled', true);
                            $("#chk_tecnicos").attr('disabled', true);
                            $("#chk_tecnicos_nc").attr('disabled', true);
                        }
                        
                        if( (_USER_ROL==12 || _USER_ROL==13 || _USER_ROL==14) ){
                            $("#obs_legales").removeAttr('disabled', true);
                        }
                        
                        if( (_USER_ROL==15 || _USER_ROL==16 ) ){
                            $("#obs_patrimoniales").removeAttr('disabled', true);
                        }
                        
                        if( (_USER_ROL==17 || _USER_ROL==18 ) ){
                            $("#obs_tecnico").removeAttr('disabled', true);
                        }
                        
                        $("#chk_altacredito").attr('disabled', true);
                        if(_array_obj.ID_ETAPA_ACTUAL=='12' && _USER_ROL==10){
                            $("#chk_altacredito").attr('disabled', true);
                        }else if (_array_obj.ID_ETAPA_ACTUAL=='12' && _USER_ROL==20){
                            $("#chk_altacredito").removeAttr('disabled');
                        }
                        
                        var eee = $("#chk_checklist").is(':checked');
                        //si etapa actual es 1 y usuario es mesa y eee=1
                        if (eee && _array_obj.ID_ETAPA_ACTUAL==1 && _USER_ROL==9){
                            //mostrar boton
                            $(".asignar1").show();
                        }else if( !eee && _array_obj.ID_ETAPA_ACTUAL==1 && _USER_ROL==9){
                            $(".asignar1").hide();
                        }
                        
                        //solo si es quien autoriza
                        evento_lista_req('no_save');
                        //evento_lista_req();
                        
                        if( _array_obj.ID_PROCESO == 1 && _array_obj.ID_ETAPA_ACTUAL==10 && jQuery.inArray(_USER_ROL, ['12','13','14'])!=-1){
                            $("#listbox_cond").on('checkChange', function (event) {
                                var allItems = $("#listbox_cond").jqxListBox('getItems');
                                var checkItems = $("#listbox_cond").jqxListBox('getCheckedItems');

                                var allItems_d = $("#listbox_cond_dese").jqxListBox('getItems');
                                var checkItems_d = $("#listbox_cond_dese").jqxListBox('getCheckedItems');

                                if ( (checkItems.length==allItems.length) && (checkItems_d.length==allItems_d.length) ){
                                        $("#div_chk_checklist1").fadeIn(600);
                                        $("#div_chk_checklist1 input").attr("checked","checked");
                                        $("#div_chk_checklist1 span.checkbox").css("background-position","0px -50px");
                                }else{
                                    $("#div_chk_checklist1").fadeOut(400);
                                    //quitar el check
                                    $("#div_chk_checklist1 input").removeAttr("checked");
                                    $("#div_chk_checklist1 span.checkbox").css("background-position","0px 0px");
                                }

                            });

                            $("#listbox_cond_dese").on('checkChange', function (event) {
                                var allItems = $("#listbox_cond").jqxListBox('getItems');
                                var checkItems = $("#listbox_cond").jqxListBox('getCheckedItems');

                                var allItems_d = $("#listbox_cond_dese").jqxListBox('getItems');
                                var checkItems_d = $("#listbox_cond_dese").jqxListBox('getCheckedItems');

                                if ( (checkItems.length==allItems.length) && (checkItems_d.length==allItems_d.length) ){
                                        $("#div_chk_checklist1").fadeIn(600);
                                        $("#div_chk_checklist1 input").attr("checked","checked");
                                        $("#div_chk_checklist1 span.checkbox").css("background-position","0px -50px");
                                }else{
                                    $("#div_chk_checklist1").fadeOut(400);
                                    //quitar el check
                                    $("#div_chk_checklist1 input").removeAttr("checked");
                                    $("#div_chk_checklist1 span.checkbox").css("background-position","0px 0px");
                                }

                            });
                            
                            
                        }
                        
                        

                        $('#copia_contrato').on('click', function(e){
                           
                           
                           jConfirm('Esta seguro que desea enviar la Peticion de Confirmacion?.', $.ucwords(_etiqueta_modulo),function(r){
                                
                                if(r==true){
                                    cont_legales = 1;
                                    $(".asignar").trigger('click');
                                }
                            });
                            
                        });
                        
                        refresGridevent();
                        
                        $("#facta").blur(function(e){
                            //validar
                            e.preventDefault();
                            
                            var d = new Date();
                            var month = d.getMonth()+1;
                            var day = d.getDate();

                            var output = 
                                (day<10 ? '0' : '') + day + '/' +
                                (month<10 ? '0' : '') + month + '/' +
                                d.getFullYear();
                            
                            var dato = $(this).val();
                            var valf = validarFechafunc( dato );
                            if (valf==1){
                                //continuar
                            }else{
                                if ( !(valf=='Fecha no válida' && dato=='')){
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#fdevm").val(output);
                                        $("#fdevm").focus();
                                    });
                                    return false;
                                }
                            }
                        });
                        
                        $("#fentregam").blur(function(e){
                            //validar
                            e.preventDefault();
                            
                            var d = new Date();
                            var month = d.getMonth()+1;
                            var day = d.getDate();

                            var output = 
                                (day<10 ? '0' : '') + day + '/' +
                                (month<10 ? '0' : '') + month + '/' +
                                d.getFullYear();
                            
                            var dato = $(this).val();
                            var valf = validarFechafunc( dato );
                            if (valf==1){
                                //continuar
                            }else{
                                if ( !(valf=='Fecha no válida' && dato=='')){
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#fdevm").val(output);
                                        $("#fdevm").focus();
                                    });
                                    return false;
                                }
                            }
                        });
                        
                        $("#fdevm").blur(function(e){
                            //validar
                            e.preventDefault();
                            
                            var d = new Date();
                            var month = d.getMonth()+1;
                            var day = d.getDate();

                            var output = 
                                (day<10 ? '0' : '') + day + '/' +
                                (month<10 ? '0' : '') + month + '/' +
                                d.getFullYear();
                            
                            var dato = $(this).val();
                            var valf = validarFechafunc( dato );
                            if (valf==1){
                                //continuar
                            }else{
                                if ( !(valf=='Fecha no válida' && dato=='')){
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#fdevm").val(output);
                                        $("#fdevm").focus();
                                    });
                                    return false;
                                }
                            }
                        });
                        
                        $("#cffirma").blur(function(e){
                            //validar
                            e.preventDefault();
                            
                            var d = new Date();
                            var month = d.getMonth()+1;
                            var day = d.getDate();

                            var output = 
                                (day<10 ? '0' : '') + day + '/' +
                                (month<10 ? '0' : '') + month + '/' +
                                d.getFullYear();
                            
                            var dato = $(this).val();
                            var valf = validarFechafunc( dato );
                            if (valf==1){
                                //continuar
                            }else{
                                if ( !(valf=='Fecha no válida' && dato=='')){
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#cffirma").val(output);
                                        $("#cffirma").focus();
                                    });
                                    return false;
                                }
                            }
                        });
                        
                        
                        var sourcechk_deudas =
                        {
                            datatype: "json",
                            url: 'general/extends/extra/operatorias.php',
                            data:{
                                accion: "getDeudas",
                            },
                            async:false,
                            datafields: [
                                { name: 'ID' },
                                { name: 'NOMBRE' }
                            ],
                            id: 'ID'
                        };
                        var dataAdapterchk_deudas = new $.jqx.dataAdapter(sourcechk_deudas);
                        $("#listbox_deudas").jqxListBox({ source: dataAdapterchk_deudas, checkboxes: true, displayMember: "NOMBRE", valueMember: "ID", width: 460, height: 80 });
                        
                        $("#listbox_deudas").on('checkChange', function (event) {
                            
                            var allItems = $("#listbox_deudas").jqxListBox('getItems');
                            var checkItems = $("#listbox_deudas").jqxListBox('getCheckedItems');
                            
                            
                            if (checkItems.length==allItems.length){
                                $(".div_chk_deudas").fadeIn(600);
                                $("#chk_cinicial").attr("checked","checked");
                                $(".div_chk_deudas span.checkbox").css("background-position","0px -50px");
                            }else{
                                $(".div_chk_deudas").fadeOut(400);
                                //quitar el check
                                $("#chk_cinicial").removeAttr("checked");
                                $(".div_chk_deudas span.checkbox").css("background-position","0px 0px");
                            }
                            
                        });
                        
                        if (_array_obj.DEUDA_FYTC==1){
                            $("#listbox_deudas").jqxListBox('checkIndex',0);
                        }
                        if (_array_obj.DEUDA_MF==1){
                            $("#listbox_deudas").jqxListBox('checkIndex',1);
                        }
                        
                        initNotas( _array_obj.ID );
                        
                        if (ver!=-1){
                            $(".tb_save").html('').hide();
                        }
                        
                    }
                });
                $.unblockUI();
                
        }
                
                
                
                
                
                
                
            }else if (top=='del'){
                
                if (_permiso_baja==0){
                    jAlert('Usted no tiene Permisos para ejecutar esta acción', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                
                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                var rowscount = $("#jqxgrid").jqxGrid('getdatainformation').rowscount;

                if ( mydata==null ){
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                
                jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                        $.ajax({
                            url : _carpetas.URL + "/x_delobj",
                            data : {
                                id:mydata.IDOPE
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

            }else if (top=='cli'){
                
                url = "backend/archivo/clientes/clientes";
                jConfirm('Esta seguro de ir a Clientes?. Los datos sin guardar, se perderán.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                        $(location).attr('href',url);
                    }else{
                        $.unblockUI();
                    }
                });
                
            }else if (top=='ents'){
                
                url = "backend/entidades";
                jConfirm('Esta seguro de ir a Entidades?. Los datos sin guardar, se perderán.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                        $(location).attr('href',url);
                    }else{
                        $.unblockUI();
                    }
                });
                
            }else if(top=='fil'){
                $.unblockUI();
                $('#jqxlistbox').slideToggle('slow', function() {});
            }else if(top=='lis'){
                $.unblockUI();
                $('#btnClear').trigger('click');
                $("#jqxgrid").show();
                $("#jqxgrid").jqxGrid('updatebounddata');
                $("#wpopup").html('');
                
            }else if(top=='lis_editar'){
                $.unblockUI();
                $('#btnClear').trigger('click');
                $("#jqxgrid").show();
                $("#jqxgrid").jqxGrid('updatebounddata');
                $("#wpopup").html('');
                
                switchBarra_back();
                
            }else if(top=='exp'){
                // exportar
                $.ajax({
                    url : _carpetas.URL + "/x_getexportar",
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
                
                
            }else if (top=='lis_guardar'){
                $("#send").trigger("click");
                $.unblockUI();
                
            }else if (top=='lis_asignar'){
                $(".asignar").trigger("click");
                //$(".asignar").trigger("click");
                $.unblockUI();
            }else if (top=='lis_libre'){
                $.unblockUI();
                process_asignar();
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


function loadChild_cli(val){
    if(workincli==false){
        workincli = true;
        $.ajax({
              url : _carpetas.URL + "/x_getlocalidad",
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
                $('#div_subrubrocli').html('<select class="chzn-select medium-select2 select" id="subrubrocli">'+ options +'</select>');
                $('#subrubrocli').on('change', function(event) {
                    event.preventDefault();
                    $('#localidadhcli').val($('#subrubrocli').val());
                });
                var selects = $('#div_subrubrocli').find('select');
                selects.chosen();
                workincli = false;
              }
          });
    }
    
}


function loadChild(val){
    if(working==false){
        working = true;
        $.ajax({
              url : _carpetas.URL + "/x_getlocalidad",
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
                $('#div_subrubro').html('<select class="chzn-select medium-select2 select" id="subrubro">'+ options +'</select>');
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

var _monto_solicitado = 0;
var _proceso_operatoria = 0;

function loadChild_fid(val){
    if(workingf==false){
        workingf = true;
        $.ajax({
              url : _carpetas.URL + "/x_getoperatoria",
              async:false,
              data : {
                    idf : val
              },
              dataType: "json",
              type : "post",
              success : function(r){
                  
                var connection, options = '';
                $.each(r.items,function(k,v){
                    connection = '';
                    var n=v.split("--");
                    v = n[0];
                    m = n[1];
                    p = n[2];
                    
                    if(v)   connection = 'data-connection="'+v+'" data-monto="'+m+'" data-proceso="'+p+'"';
                    options+= '<option value="'+v+'" '+connection+'>'+k+'</option>';
                });
                if(r.defaultText){
                    options = '<option>'+r.defaultText+'</option>'+options;
                }
                $('#div_operatoria').html('<select class="chzn-select medium-select2 select" id="operatoria">'+ options +'</select>');
                
                $('#operatoria').on('change', function(event) {
                    event.preventDefault();
                    var ope_value = $('#operatoria').val();
                    $('#operatoriah').val(ope_value);
                    
                    //var monto_o = $(this).data('monto');
                    
                    var monto_o = $("#operatoria option:selected").data('monto');
                    var proceso_o = $("#operatoria option:selected").data('proceso');
                    
                    $("#listbox").jqxListBox('clear');
                    
                    if (ope_value>0 && proceso_o!='2'){
                        
                        $(".alert").fadeOut(500);
                        $("#listbox").show();
                        $("#div_chk_checklist").fadeOut(400);
                        //quitar el check
                        $("#chk_checklist").removeAttr("checked");
                        $("#div_chk_checklist span.checkbox").css("background-position","0px 0px");
                        var sourcechk =
                        {
                            datatype: "json",
                            url: 'general/extends/extra/operatorias.php',
                            data:{
                                accion: "getOperatoriasChecklist",
                                id_operatoria:ope_value
                            },
                            async:false,
                            datafields: [
                                { name: 'ID' },
                                { name: 'NOMBRE' }
                            ],
                            id: 'ID'
                        };
                        var dataAdapterchk = new $.jqx.dataAdapter(sourcechk);
                        $("#listbox").jqxListBox({ source: dataAdapterchk, checkboxes: true, displayMember: "NOMBRE", valueMember: "ID", width: 760, height: 300 });
                        
                        $("#listbox").on('checkChange', function (event) {
                            var allItems = $("#listbox").jqxListBox('getItems');
                            var checkItems = $("#listbox").jqxListBox('getCheckedItems');
                            
                            if (checkItems.length==allItems.length){
                                $("#div_chk_checklist").fadeIn(600);
                                $("#chk_checklist").attr("checked","checked");
                                $("#div_chk_checklist span.checkbox").css("background-position","0px -50px");
                            }else{
                                $("#div_chk_checklist").fadeOut(400);
                                //quitar el check
                                $("#chk_checklist").removeAttr("checked");
                                $("#div_chk_checklist span.checkbox").css("background-position","0px 0px");
                            }
                            
                        });
                        
                        
                        _monto_solicitado = monto_o;
                        _proceso_operatoria = proceso_o;
                        if ($("#montosol").val()<=0){
                            $("#montosol").val(monto_o);
                        }
                        
                        
                    }else{
                        if (proceso_o!='2'){
                            $(".alert").fadeIn(500);
                            $("#montosol").val('');
                        }else{
                            $(".alert").fadeOut(500);
                            $("#listbox").hide();
                            $("#div_chk_checklist").fadeIn(600);
                            $("#chk_checklist").attr("checked","checked");
                            $("#div_chk_checklist span.checkbox").css("background-position","0px -50px");
                            _monto_solicitado = monto_o;
                            _proceso_operatoria = proceso_o;
                            if ($("#montosol").val()<=0){
                                $("#montosol").val(monto_o);
                            }
                            
                            var solapa = $('#vtab>ul>li').eq(5);
                            solapa.css('opacity','0.5');
                            
                        }
                    }
                    
                });
                var selects = $('#div_operatoria').find('select');
                selects.chosen();
                workingf = false;
                
              }
          });
    }
    
}


function loadChild_gar(val){
    if(workinggar==false){
        workinggar = true;
        $.ajax({
              url : _carpetas.URL + "/x_get_garobjeto",
              async:false,
              data : {
                    idgartipo : val
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
                $('#div_objeto').html('<select class="chzn-select medium-select1 select" id="objeto">'+ options +'</select>');
                
                $('#objeto').on('change', function(event){
                    event.preventDefault();
                    var ope_value = $('#objeto').val();
                    $('#objetoh').val(ope_value);
                });
                var selects = $('#div_objeto').find('select');
                selects.chosen();
                workinggar = false;
                
              }
          });
    }
    
}




function post_upload(nombre,nombre_tmp, etapa){
    
    jAlert('Archivo cargado correctamente. ' + nombre, $.ucwords(_etiqueta_modulo),function(){
        //agregarlo a la lista
        $(".lista_adjuntos").append('<li class="eta-'+etapa+'" data-eta="'+etapa+'" data-nom="'+nombre+'" data-tmp="'+nombre_tmp+'">'+nombre+'</li>');
        $('.lista_adjuntos li').last().off().on('click', function(event){
            event.preventDefault();
            $this = $(this);
            var ruta = $(this).data('tmp');
            //x_borrar_file
            $.ajax({
                url : _carpetas.URL + "/x_borrar_file",
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
        $.fancybox.close();
        
        if ($('.grid_adjuntos').hasClass('inactive'))
            $(".grid_adjuntos span").trigger('click');
                
        
    });
    
}

function post_upload_gar(nombre,nombre_tmp, etapa){
    
    jAlert('Archivo cargado correctamente. ' + nombre, $.ucwords(_etiqueta_modulo),function(){
        $('#upload_file1').each (function(){
            this.reset();
        });
        $("#upload_file1 input[type=file]").each(function(){
            $(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");
        });
        $.fancybox.close();
        
        if ($('.grid_adjuntos').hasClass('inactive'))
            $(".grid_adjuntos span").trigger('click');
        
        
                
        
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
            { name: 'IDOPE', type: 'string' },
            { name: 'ETAPA_ACTUAL', type: 'string' },
            { name: 'MONTO_SOLICITADO', type: 'string' },
            { name: 'MONTO_APROBADO' , type: 'string' },
            { name: 'OPERATORIA' , type: 'string' },
            { name: 'FIDEICOMISO' , type: 'string' },
            { name: 'ENCARTERADE' , type: 'string' },
            { name: 'ENVIADOA' , type: 'string' },
            { name: 'ENVIADOA1' , type: 'string' },
            { name: 'BENEFICIARIO' , type: 'string' },
            { name: 'AUTOR' , type: 'string' },
            { name: 'AUTOR1' , type: 'string' },
            { name: 'e1_estado' , type: 'string' },
            { name: 'e2_estado' , type: 'string' },
            { name: 'e10_estado' , type: 'string' },
            { name: 'e4_estado' , type: 'string' },
            { name: 'e5_estado' , type: 'string' },
            { name: 'e6_estado' , type: 'string' },
            { name: 'e7_estado' , type: 'string' },
            { name: 'e8_estado' , type: 'string' },
            { name: 'e9_estado' , type: 'string' },
            { name: 'CARGAH' , type: 'string' }
        ],
        url: 'general/extends/extra/carpetas.php',
        data:{
            accion  :   "getCarpetas",
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
            //$("#jqxgrid").jqxGrid('autoresizecolumns');
        },
        columnsresize: true,
        showtoolbar: true,
        sortable: true,
        filterable: true,
        showfilterrow: true,
        localization: getLocalization(),
        rendertoolbar: function (toolbar){
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
            { text: 'ID', datafield: 'IDOPE', width: '6%', groupable:false, pinned: true, columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'POSTULANTE/TOMADOR', datafield: 'BENEFICIARIO', width: '40%', hidden : false, align: 'center', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'OPERATORIA', datafield: 'OPERATORIA', width: '20%', hidden : false, columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'FIDEICOMISO', datafield: 'FIDEICOMISO', width: '10%' , align: 'center', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'ETAPA', datafield: 'ETAPA_ACTUAL', width: '10%' , align: 'center', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'EN CARTERA DE', datafield: 'ENCARTERADE', width: '15%' , align: 'center' /*, pinned: true*/, columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'ENVIADO A', datafield: 'ENVIADOA', width: '20%' , align: 'center', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},
            { text: 'AUTORIZ.', datafield: 'AUTOR', width: '20%' , align: 'center', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            /*{ text: 'AUTORIZ. 1', datafield: 'AUTOR1', width: '20%' , align: 'center'},*/
            { text: 'M. SOLICITADO', datafield: 'MONTO_SOLICITADO', width: '10%', hidden : false, align: 'center', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'M. APROBADO', datafield: 'MONTO_APROBADO', width: '10%', hidden : false, align: 'center', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true},

            { text: 'Inscripción', datafield: 'e1_estado', width: 80, columntype: 'checkbox', align: 'center' , filterable : false},
            { text: 'C. Inicial', datafield: 'e2_estado', width: 70, columntype: 'checkbox', align: 'center' , filterable : false},
            { text: 'Legal', datafield: 'e4_estado', width: 60, columntype: 'checkbox', align: 'center' , filterable : false},
            { text: 'Patrimonial', datafield: 'e5_estado', width: 80, columntype: 'checkbox', align: 'center' , filterable : false},
            { text: 'Técnico', datafield: 'e6_estado', width: 60, columntype: 'checkbox', align: 'center' , filterable : false},
            { text: 'Garantías', datafield: 'e7_estado', width: 70, columntype: 'checkbox', align: 'center' , filterable : false},
            { text: 'Elevación', datafield: 'e8_estado', width: 70, columntype: 'checkbox', align: 'center' , filterable : false},
            { text: 'Comité', datafield: 'e9_estado', width: 60, columntype: 'checkbox', align: 'center' , filterable : false},
            { text: 'Contrato', datafield: 'e10_estado', width: 70, columntype: 'checkbox', align: 'center' , filterable : false},
            { text: 'CARGAH', datafield: 'CARGAH', width: 70,  align: 'center' , hidden : true }
            
        ]
    });
    
    
}

function event_add_file(){

    $('.add_file').on('click', function(event) {
        
        var etapa = $(this).parent().data('etapa');
        
        
        
        if (etapa==3){
            //etapa = 4;
            etapa = $("#tabs .content-gird div.tab_b").not('.ui-tabs-hide').first().data("etapab");
        }
        
        
        if ( etapa==4 && !(jQuery.inArray(_USER_ROL, ['12','13','14'])!=-1)){
            jAlert('Usted no puede subir adjuntos en legales.', $.ucwords(_etiqueta_modulo));
            return false;
        }
        
        if ( etapa==5 && !(jQuery.inArray(_USER_ROL, ['15','16'])!=-1)){
            jAlert('Usted no puede subir adjuntos en patrimoniales.', $.ucwords(_etiqueta_modulo));
            return false;
        }
        
        if ( etapa==6 && !(jQuery.inArray(_USER_ROL, ['17','18'])!=-1)){
            jAlert('Usted no puede subir adjuntos en técnicos.', $.ucwords(_etiqueta_modulo));
            return false;
        }
        
        
        if (typeof(_array_obj) !== "undefined") {
            
            if ( _array_obj.ID_ETAPA_ACTUAL && etapa!=_array_obj.ID_ETAPA_ACTUAL){
                jAlert('Usted no puede subir adjuntos en esta etapa.', $.ucwords(_etiqueta_modulo));
                return false;
            }
        }
        
        
        if (jQuery.inArray(_USER_ROL, ['12','13','14','15','16','17','18'])!=-1 && !( etapa==4 || etapa==5 || etapa==6 || etapa==10) ){
            jAlert('Usted no puede subir adjuntos en esta instancia.', $.ucwords(_etiqueta_modulo));
            return false;
        }
        
        
        
        $.ajax({
            url : _carpetas.URL + "/x_getform_adjunto",
            data : {
                etapa:etapa
            },
            type : "post",
            success : function(dataadj){
               $.fancybox(
                    dataadj,
                    {
                        'padding'   :  35,
                        'autoScale' :true,
                        'height' : 400,
                        'scrolling' : 'no'
                    }
                );
                    $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
                    $("input[type=file]").each(function(){
                    if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");}
                    });
                    
        
            $("#btnSubirfile").click(function(e){
                if ($("#req_etiqueta").val()==''){
                        e.preventDefault();
                        jAlert('Ingrese una etiqueta, por favor.', $.ucwords(_etiqueta_modulo),function(){
                            $("#req_etiqueta").select();
                        });
                }

            });
            $( "#req_etiqueta" ).keyup(function() {
                $("#req_etiquetah").val( $(this).val() );
            });
                    
                    

            }
        });
    });
    
}


function event_d_tab_left(){
    
    $("#vtab li").on('click', function(event){
        var indextab = $(this).index();

        var div_info = $(this).parent().parent().find(".vtabinfo").eq(indextab);
        var etapa = div_info.data("etapa");

        if (etapa){
            
            if (etapa==3)
                etapa = $("#tabs .content-gird div.tab_b").not('.ui-tabs-hide').first().data("etapab");
            
            $(".grid_adjuntos ul li").hide();;
            $(".grid_adjuntos ul li").next().hide();
            $(".grid_adjuntos ul li").next().next().hide();
            $(".grid_adjuntos ul li."+".eta-"+etapa).show();
            $(".grid_adjuntos ul li."+".eta-"+etapa).next().show();
            $(".grid_adjuntos ul li."+".eta-"+etapa).next().next().show();
            $(".ver_todos").html('Ver Todos');
            
        }
        
        
    });
    
    $(".ver_todos").on('click', function(event) {
        
        if ($(this).text()=='Ver Todos'){
            if ($('.grid_adjuntos').hasClass('inactive'))
            $(".grid_adjuntos span").trigger('click');
            $(".grid_adjuntos ul li").show();
            $(".grid_adjuntos ul li").next().show();
            $(".grid_adjuntos ul li").next().next().show();
            $(this).html('Ver solo los adjuntos de la etapa');
        }else if($(this).text()=='Ver solo los adjuntos de la etapa'){
            var etapa = $(".vtabinfo:visible").first().data("etapa");
            
            if (etapa==3){
                var ope_tab_sel = $("#tabs").tabs('option', 'selected');
                switch(true) {
                    case (ope_tab_sel==0):
                        etapa = 4;
                        break;
                    case (ope_tab_sel==1):
                        etapa = 5;
                        break;
                    case (ope_tab_sel==2):
                        etapa = 6;
                }
            }
            
            $(this).html('Ver Todos');
            $(".grid_adjuntos ul li").hide();;
            $(".grid_adjuntos ul li").next().hide();
            $(".grid_adjuntos ul li").next().next().hide();

            $(".grid_adjuntos ul li."+".eta-"+etapa).show();
            $(".grid_adjuntos ul li."+".eta-"+etapa).next().show();
            $(".grid_adjuntos ul li."+".eta-"+etapa).next().next().show();
                        
        }
    });
    
    
    /*
     $('#tabs').tabs({
        select: function(event, ui) { 
            switch(true) {
                case (ui.index==0):
                    etapa = 4;
                    break;
                case (ui.index==1):
                    etapa = 5;
                    break;
                case (ui.index==2):
                    etapa = 6;
            }

            $('.ver_todos').html('Ver Todos');
            $(".grid_adjuntos ul li").hide();;
            $(".grid_adjuntos ul li").next().hide();

            $(".grid_adjuntos ul li."+".eta-"+etapa).show();
            $(".grid_adjuntos ul li."+".eta-"+etapa).next().show();

        }
    });
     
     
     
     **/
    
    
}



function agregarCondicionesPrevias( _arr_objeto ){
    _arr_objeto || ( _arr_objeto = [] );
    var id_operacion = $("#codigo").val();
        
    var sourcechk =
    {
        datatype: "json",
        url: 'general/extends/extra/carpetas.php',
        data:{
            accion: "getCondicionesPrevias",
            id_operacion:id_operacion
        },
        async:false,
        datafields: [
            { name: 'ID' },
            { name: 'VALOR' }
        ],
        id: 'ID'
    };
    
    var dataAdapterchk = new $.jqx.dataAdapter(sourcechk);
    
    
    if(_array_obj.ID_ETAPA_ACTUAL == 10){
            if (jQuery.inArray(_USER_ROL, ['12','13','14','15','16','17','18'])!=-1){
            $("#listbox_cond").jqxListBox({ source: dataAdapterchk, displayMember: "VALOR", valueMember: "ID", width: 690, height: 80, checkboxes:true});
        }else{
            $("#listbox_cond").jqxListBox({ source: dataAdapterchk, displayMember: "VALOR", valueMember: "ID", width: 690, height: 80, checkboxes:true });
        }
    }else{
        $("#listbox_cond").jqxListBox({ source: dataAdapterchk, displayMember: "VALOR", valueMember: "ID", width: 690, height: 80 });
    }
    
    

    $("#add_cond").off().on('click', function () {
                
        if ( $(".form_condiciones input#condicion").val()==''){
            jAlert('Ingrese alguna condición.', $.ucwords(_etiqueta_modulo),function(){
                $(".form_condiciones input#condicion").first().select();
            });
            return false;
        }            
                
        if ($(".form_condiciones input#condicion").val()!=''){
            var condicion = $("#condicion").val();
            $("#listbox_cond").jqxListBox('addItem', condicion ); 
            $(".form_condiciones input").val('');
            $(".form_condiciones input").first().focus();
        }
        
        $("#listbox_cond").on('select', function (event) {
            var args = event.args.item;
            $("#condicion").val(args.label);
        });
       

    });
    
    $("#del_cond").off().on('click', function(){
        var condicion = $("#condicion").val();
        var items = $("#listbox_cond").jqxListBox('getItems'); 
        
        $.each(items, function (index, value){
            if (value.label==condicion){
                $("#listbox_cond").jqxListBox('removeAt', index );
                $(".form_condiciones input").val('');
                $(".form_condiciones input").first().focus();
                $("#listbox_cond").on('select', function (event) {
                    var args = event.args.item;
                    console.dir(args);
                    $("#condicion").val(args.label);
                });
                return false;
            }
        });
    });
    
    $("#listbox_cond").on('select', function (event) {
        var args = event.args.item;
        $("#condicion").val(args.label);
    });
    
    
}




function agregarCondicionesPrevias_dese(_arr_objeto){
    _arr_objeto || ( _arr_objeto = [] );
    
    var id_operacion = $("#codigo").val();
    var sourcechkdese =
    {
        datatype: "json",
        url: 'general/extends/extra/carpetas.php',
        data:{
            accion: "getCondicionesPrevias",
            id_operacion:id_operacion,
            tipo:2 // desembolsos
        },
        async:false,
        datafields: [
            { name: 'ID' },
            { name: 'VALOR' }
        ],
        id: 'ID'
    };
    
    $(".titulo_condprev").show();
    $(".dese_info").show();
    $("#listbox_cond_dese").show();
    

    var dataAdapterchk_dese = new $.jqx.dataAdapter( sourcechkdese );
    /*
    if( ( _array_obj.ID_PROCESO=='2' && _array_obj.ID_ETAPA_ACTUAL == 13 && _USER_ROL=='11') ){
        //console.log('exeeeeeeeeeeee');
        //$("#listbox_cond_dese").hide();
        
        //$(".titulo_condprev").hide();
        //$(".dese_info").hide();
    }else 
      */  
        
        
    if( (_array_obj.ID_ETAPA_ACTUAL == 13 || _array_obj.ID_ETAPA_ACTUAL == 10) ){
        if (jQuery.inArray(_USER_ROL, ['12','13','14','15','16','17','18'])!=-1){
            if (_array_obj.ID_ETAPA_ACTUAL== 10){
                $("#listbox_cond_dese").jqxListBox({ source: dataAdapterchk_dese, displayMember: "VALOR", valueMember: "ID", width: 690, height: 80, checkboxes: true });
            }
            else{
                $("#listbox_cond_dese").jqxListBox({ source: dataAdapterchk_dese, displayMember: "VALOR", valueMember: "ID", width: 690, height: 80 });
            }
        }else{
            if(_array_obj.ID_ETAPA_ACTUAL == 13){
                $("#listbox_cond_dese").jqxListBox({ source: dataAdapterchk_dese, checkboxes: true, displayMember: "VALOR", valueMember: "ID", width: 690, height: 80, hasThreeStates:true });
            }else{
                $("#listbox_cond_dese").jqxListBox({ source: dataAdapterchk_dese, checkboxes: true, displayMember: "VALOR", valueMember: "ID", width: 690, height: 80 });
            }
        }
    }else{
        $("#listbox_cond_dese").jqxListBox({ source: dataAdapterchk_dese, displayMember: "VALOR", valueMember: "ID", width: 690, height: 80 });
    }
        

    $("#add_cond_dese").off().on('click', function() {
        
        if ( $(".form_condiciones_dese input#condicion_dese").val()==''){
            jAlert('Ingrese alguna condición.', $.ucwords(_etiqueta_modulo),function(){
                $(".form_condiciones_dese input#condicion_dese").first().select();
            });
            return false;
        }
                
        if ($(".form_condiciones_dese input#condicion_dese").val()!=''){
            var condicion = $("#condicion_dese").val();
            $("#listbox_cond_dese").jqxListBox('addItem', condicion ); 
            $(".form_condiciones_dese input").val('');
            $(".form_condiciones_dese input").first().focus();
        }
        $("#listbox_cond_dese").on('select', function (event) {
            var args = event.args.item;
            $("#condicion_dese").val(args.label);
        });

    });
    
    $("#del_cond_dese").off().on('click', function(){
        var condicion = $("#condicion_dese").val();
        var items = $("#listbox_cond_dese").jqxListBox('getItems'); 
        
        $.each(items, function (index, value){
            if (value.label==condicion){
                $("#listbox_cond_dese").jqxListBox('removeAt', index );
                $(".form_condiciones_dese input").val('');
                $(".form_condiciones_dese input").first().focus();
                
                 $("#listbox_cond_dese").on('select', function (event) {
                    var args = event.args.item;
                    $("#condicion_dese").val(args.label);
                });
                
                return false;
            }
        });
        
        
        
    });
    
    
    $("#listbox_cond_dese").on('select', function (event) {
        var args = event.args.item;
        $("#condicion_dese").val(args.label);
    });
    
    
    
}



function agregar_requerimiento(){
    /*Requerimientos*/
    
    $(".agregar_req").on('click', function(event){
       $.ajax({
            url : _carpetas.URL + "/x_getform_agregar_requerimiento",
            data : {
                obj:mydata.IDOPE
            },
            type : "post",
            success : function(datareq){
                $.fancybox(
                    datareq,
                    {
                        'padding'   :  35,
                        'autoScale' :true,
                        'height' : 700,
                        'scrolling' : 'no'
                    }
                );
                init_datepicker('#femis','-3','+5','0',0);
                
                //init_datepicker('#fresp','-3','+0','0',1);
                $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
                $("input[type=file]").each(function(){
                    if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");}
                });
                
                $("#estadoreq").val(2).attr('disabled', true).trigger("chosen:updated");
                $("#req_respuesta").attr("readonly","readonly");
                
                
                addEventsRequerimientos();
                
                $("#femis").blur(function(e){
                    //validar
                    e.preventDefault();

                    var d = new Date();
                    var month = d.getMonth()+1;
                    var day = d.getDate();

                    var output = 
                        (day<10 ? '0' : '') + day + '/' +
                        (month<10 ? '0' : '') + month + '/' +
                        d.getFullYear();

                    var dato = $(this).val();
                    var valf = validarFechafunc( dato );
                    if (valf==1){
                        //continuar
                    }else{
                        if ( !(valf=='Fecha no válida' && dato=='')){
                            jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                $("#femis").val(output);
                                $("#femis").focus();
                            });
                            return false;
                        }
                    }
                });
                
                $("#fresp").blur(function(e){
                    //validar
                    e.preventDefault();

                    var d = new Date();
                    var month = d.getMonth()+1;
                    var day = d.getDate();

                    var output = 
                        (day<10 ? '0' : '') + day + '/' +
                        (month<10 ? '0' : '') + month + '/' +
                        d.getFullYear();

                    var dato = $(this).val();
                    var valf = validarFechafunc( dato );
                    if (valf==1){
                        //continuar
                    }else{
                        if ( !(valf=='Fecha no válida' && dato=='')){
                            jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                $("#fresp").val(output);
                                $("#fresp").focus();
                            });
                            return false;
                        }
                    }
                });
                
                
                
            }
        });
    });
    
    
}


function addEventsRequerimientos(idr){
    idr || ( idr = '0' );
    if(idr>0){
        $(".lista_reqs_adj a").click(function(e){
            e.preventDefault();

            var nom = $(this).prev().data('nom');
            var yo = $(this);
            var el = $(this).prev();

            $.ajax({
                url : _carpetas.URL + "/x_delupload_req",
                data : {
                    idnotareq:idr,
                    ruta:nom
                },
                dataType : "json",
                type : "post",
                success : function(data){
                    jAlert('Item Borrado.', $.ucwords(_etiqueta_modulo),function(){
                        yo.remove();
                        el.remove();
                    });
                }
            });
            return false;
        });
    }
       
    $("#btnSubirfile").click(function(e){
        
        if ($("#req_etiqueta").val()==''){
                e.preventDefault();
                jAlert('Ingrese una etiqueta, por favor.', $.ucwords(_etiqueta_modulo),function(){
                    $("#req_etiqueta").select();
                });
        }
        
    });
    
 /* En este sector agrega los requerimientos que se cargan desde 
 * la seccion carpetas. El inconveniente es que no entra al ajax,
 * pero guarda correctamente los datos.*/
    $(".send_req").on('click', function(){
//        e.preventDefault();
        // edit/new
        var idreqh = $("#idreqh").val();
        var req_asu = $("#req_asunto").val();
        var req_des = $("#req_descripcion").val();
        var req_res = $("#req_respuesta").val();
        //var req_etiqueta = $("#req_etiqueta").val();
        
        var req_femis = $("#femis").val();
        var req_fresp = $("#fresp").val();
        
        var id_ope_req = mydata.IDOPE;
        var estado = 1;
        //var autor_req=24;
        var autor_req = $("#jefeope_h").val();

        // si rol es jefe de op, estado = 2
        if (_USER_ROL==10){
            estado = 2;
            autor_req=0;
        }
        //adjuntos
        var _array_uploads_adj = [];
        $( ".lista_reqs_adj li" ).each(function(index){
            var nombre = $(this).data('nom');
            var nombre_tmp = $(this).data('tmp');
            _array_uploads_adj.push({nombre:nombre,nombre_tmp:nombre_tmp});
        });   
        obj_req = {
            ID_OPERACION:id_ope_req,
            ASUNTO:req_asu,
            DESCRIPCION:req_des,
            RESPUESTA:req_res,
            estado: estado,
            idreqh:idreqh,
            FCREA:req_femis,
            FREC:req_fresp,
            adjuntos:_array_uploads_adj,
            autor_req: autor_req
        }
        if ($("#femis").val()==''){
            jAlert('Ingrese una fecha.', $.ucwords(_etiqueta_modulo),function(){
                $("#femis").datepicker("show");
            });
            return false;
        }else{
            var valf = validarFechafunc( $("#femis").val() );
            if (valf==1){
                //continuar
            }else{
                jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                    $("#femis").focus();
                });
                return false;
            }
            
        }
        if ($("#req_asunto").val()==''){
            jAlert('Ingrese un asunto.', $.ucwords(_etiqueta_modulo),function(){
                $("#req_asunto").focus().select();
            });
            return false;
        }

        $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
        $.ajax({
            url : _carpetas.URL + "/x_sendreq",
            data : {
                obj:obj_req,
                valorSeteado:1,
            },
            dataType : "json",
            type : "post",
            success : function(resp){
                data = resp.result;
                if (resp.accion=='add'){
                    if(data){
                        var msg = (data.email == 1) ? 'Operacion Exitosa' : 'Guardado - No se pudo enviar correo';
                        jAlert(msg, $.ucwords(_etiqueta_modulo),function(){
                            $.unblockUI();
//agregar req con jquery
    //echo '<li class="li_cabezera">Asunto<span class="fem">'. "F. Emisión" .'</span><span class="fer">'. "F. Recepción" .'</span><span class="fet">'. "F. Tratamiento" .'</span></li>';
                            //echo '<li>xxxxxxx<span>('. "15/15/15" .')</span><span>('. "15/15/15" .')</span><span>('. "15/15/15" .')</span></li>';
                            var ftra = data.FTRA?data.FTRA:'          ' ;
                            var frec = data.FREC?data.FREC:'           ' ;
                            var fcrea = data.FCREA?data.FCREA:'          ' ;
                            var tmp1_h =  '<li class="li_cabezera">Id<span class="fet">'+ "F. Tratamiento" +'</span><span class="fer">'+ "F. Recepción" +'</span><span class="fem">'+ "F. Emisión" +'</span><span class="reqest">Estado</span><span class="reqiid">Asunto</span></li>';
//                            var tmp_li = '<li data-idr="'+data.ID+'"><span class="filr_iid">'+ '33' +'</span><span class="filr_asunto">'+ data.ASUNTO +'</span><span class="filr_estado">Pendiente Envío</span><span>'+ fcrea +'</span><span>'+ frec +'</span><span>'+ ftra +'</span></li>';
/*este va sin el id*/       var tmp_li = '<li data-idr="'+data.ID+'"></span><span class="filr_asunto">'+ data.ASUNTO +'</span><span class="filr_estado">Pendiente Envío</span><span>'+ fcrea +'</span><span>'+ frec +'</span><span>'+ ftra +'</span></li>';
                            if (_USER_ROL=='11')
                                var tmp_li = '<li data-idr="'+data.ID+'"><span class="filr_iid">'+ '33' +'</span><span class="filr_asunto">'+ data.ASUNTO +'</span><span class="filr_estado">Pendiente Autorizacion</span><span>'+ fcrea +'</span><span>'+ frec +'</span><span>'+ ftra +'</span></li>';
                            
                            if (data.ESTADO==2)
                                tmp_li = '<li class="ya_enviado" data-idr="'+data.ID+'"><span class="filr_iid">'+ '33' +'</span><span class="filr_asunto">'+ data.ASUNTO +'</span><span class="filr_estado">Emitido2</span><span>'+ fcrea +'</span><span>'+ frec +'</span><span>'+ ftra +'</span></li>';
                            
                            var lis = $(".grid_reqs .lista_reqs li");

                            if (lis.length==0){
                                $(".grid_reqs .lista_reqs").append( tmp1_h  );
                            }
                            $(".grid_reqs .lista_reqs").append( tmp_li  );
                            //eventos
                            evento_lista_req();
                            $.fancybox.close();
//                          $("#jqxgrid").show();
//                            $("#jqxgrid").jqxGrid('updatebounddata');
//                            $("#wpopup").html('');
                        });
                    }else{
                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                            $.unblockUI();
                        });
                    }

                }else{
                    jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                        //actualizar campos visibles (asunto estado y 3 fechas)
                        $(".grid_reqs .lista_reqs li").each(function( index ) {
                            var idr = $(this).data('idr');
                            //data[0].ESTADO

                            if (idr==data[0].ID){
                                if (data[0].ESTADO=='Enviado')
                                    $(this).addClass('ya_enviado');

                                var s1 =  $(this).find('span').eq(0);
                                var s2 =  $(this).find('span').eq(1);
                                var s3 =  $(this).find('span').eq(2);
                                //var s4 =  $(this).find('span').eq(3);
                                var s4 =  '<span>'+data[0].ESTADO+'</span>'

                                $(this).html( data[0].ASUNTO );
                                $(this).append( s1 );
                                $(this).append( s2 );
                                $(this).append( s3 );
                                $(this).append( s4 );
                                return false;
                            }
                        });
                        $.fancybox.close();
                        /*
                        if (_array_obj.CARTERADE == _USUARIO_SESION_ACTUAL ){
                            //eventos
                            $(".grid_reqs .lista_reqs li.ya_enviado").off().click(function(e){
                                e.preventDefault();
                                //stopBubble(e);
                                jAlert('Este requerimiento ya fue enviado al Solicitante.', $.ucwords(_etiqueta_modulo),function(){
                                    $.unblockUI();
                                });
                            });
                        }
                        */
                    });
                }
            }
            });
            /*Aqui lo agrega al requerimirnto, pero hay problemas con el tema mail*/
        $.fancybox.close();
    });

    $(".clear_req").on('click', function(e){
        e.preventDefault();
        $("#idreqh").val('');
        $("#req_descripcion").val('');
        $("#req_respuesta").val('');
        $("#femis").val('');
        $("#fresp").val('');
        $("#estadoreq").val(2).trigger("chosen:updated");
        $("#upload_file2 input[type=file]").each(function(){
            $(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");
        });
        $("#req_asunto").val('').select();
        

    });
    
    $( "#req_etiqueta" ).keyup(function() {
        $("#req_etiquetah").val( $(this).val() );
    });
    
    
    var estadoh_req = $("#estadoh").val();
    var id_reqh = $("#idreq_usoh").val();
    
    
    if (estadoh_req==3 && (_USER_ROL=='10' || _USER_ROL=='11') ){
        $(".btn_insuf").on('click', function(e){
            //cambiar estado del req a 5
            arr_up = {
                ESTADO:5
            }
            
            $.ajax({
                url : _carpetas.URL + "/x_update_req",
                data : {
                    idr:id_reqh,
                    arr_up:arr_up,
                    ftra:1
                },
                dataType : "json",
                type : "post",
                success : function(data){
                    jAlert('El requerimiento se marcó como insuficiente.', $.ucwords(_etiqueta_modulo),function(){
                        $.fancybox.close();
                        $("#jqxgrid").show();
                        $("#jqxgrid").jqxGrid('updatebounddata');
                        $("#wpopup").html('');
                        switchBarra();
                    });
                }
            });
        });
        
        $(".btn_suf").on('click', function(e){
            //cambiar estado del req a 4
            arr_up = {
                ESTADO:4
            }
            $.ajax({
                url : _carpetas.URL + "/x_update_req",
                data : {
                    idr:id_reqh,
                    arr_up:arr_up,
                    ftra:1
                },
                dataType : "json",
                type : "post",
                success : function(data){
                    jAlert('El requerimiento se marcó como suficiente.', $.ucwords(_etiqueta_modulo),function(){
                        $.fancybox.close();
                        $("#jqxgrid").show();
                        $("#jqxgrid").jqxGrid('updatebounddata');
                        $("#wpopup").html('');
                        switchBarra();
                    });
                }
            });
        });
    }
    
    
    $(".btn_env_user").on('click', function(e){
        var req_femis = $("#femis").val();
        
        if (req_femis==''){
            jAlert('Ingrese una fecha.', $.ucwords(_etiqueta_modulo),function(){
                $("#femis").datepicker("show");
            });
            return false;
        }
        
        //cambiar estado del req a 4
        arr_up = {
            ESTADO:1
        }
        
        

        console.log(arr_up);
        $.ajax({
            url : _carpetas.URL + "/x_update_req",
            data : {
                idr:id_reqh,
                arr_up:arr_up,
                fcrea:req_femis
            },
            dataType : "json",
            type : "post",
            success : function(data){
                jAlert('El requerimiento se marcó como Enviado al Usuario.', $.ucwords(_etiqueta_modulo),function(){
                    $.fancybox.close();
                    $(".lista_reqs li").not(".li_cabezera").each(function(){
                        var iidr = $(this).data('idr');
                        if (iidr==id_reqh){
                            $(this).find('.filr_estado').html('Enviado (Pendiente de Respuesta)');
                            $(this).find('.filr_estado').next().html( req_femis );
                            return false;
                        }
                    });
                    
                    /*
                    $("#jqxgrid").show();
                    $("#jqxgrid").jqxGrid('updatebounddata');
                    $("#wpopup").html('');
                    switchBarra();
                    */
                   
                });
            }
        });
    });
    
    
    var _sitio = $("#_dir_sitio").val();
                
    $( ".lista_reqs_adj li").click(function(){
        var n = $(this).data("nom");
        url = _sitio + n;
        window.open(url, '_blank');
        return false;
    });
    
}


function agregar_solicituddesembolso(){
    /*Solicitud de desembolso*/
    
    if (typeof(_array_obj) === "undefined") {
        return false;
    }
    
    var idope = _array_obj.ID;
    
    var sourcesoldese ={
        datatype: "json",
        datafields: [
            { name: 'ID' },
            { name: 'ID_OPERACION' },
            { name: 'PROYECTO', type: 'string' },
            { name: 'DES_NUMERO', type: 'string' },
            { name: 'DES_MONTO' , type: 'string' },
            { name: 'ESTADONR' , type: 'string' }
        ],
        url: 'general/extends/extra/carpetas.php',
        data:{
            accion: "getSolDesembolsos",
            idope: idope
        }
    };
    
    var dataAdapter_soldese = new $.jqx.dataAdapter(sourcesoldese,
        {
            formatData: function (data) {
                data.name_startsWith = $("#searchField").val();
                return data;
            }
        }
    );
			
    $("#jqxgrid_soldesem").jqxGrid(
    {
        width: '98%',
        source: dataAdapter_soldese,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid_soldesem").jqxGrid('hidecolumn', 'ID');
        },
        showstatusbar: true,
        renderstatusbar: function (statusbar) {
            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
            var responderButton = $("<div style='float: left; margin-left: 5px;width:217px;'><img width=16 style='position: relative; margin-top: 2px;' src='general/css/images/32x32/checked.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Ver</span></div>");
            var responderButton2 = $("<div style='float: left; margin-left: 5px;width:217px;'><img width=16 style='position: relative; margin-top: 2px;' src='general/css/images/32x32/checked.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Agregar</span></div>");
            container.append(responderButton2);
            container.append(responderButton);
            responderButton2.jqxButton({ theme: theme, width: 210, height: 20 });
            
            if ( _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==24 ){
                var responderButton1 = $("<div style='float: left; margin-left: 5px;width:217px;'><img width=16 style='position: relative; margin-top: 2px;' src='general/css/images/32x32/checked.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Marcar como Emitido</span></div>");
                container.append(responderButton1);
            }
            statusbar.append(container);
            responderButton.jqxButton({ theme: theme, width: 210, height: 20 });
            
            /*
            if ( _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==24 ){
                
            }
            */
            
            
            if ( _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==24 ){
                responderButton1.jqxButton({ theme: theme, width: 210, height: 20 });
                responderButton1.click(function (event) {
                    var selectedrowindex = $("#jqxgrid_soldesem").jqxGrid('getselectedrowindex');
                    var rowscount = $("#jqxgrid_soldesem").jqxGrid('getdatainformation').rowscount;

                    if ( selectedrowindex != '-1' ){
                        if (selectedrowindex<rowscount){

                                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                    var id = $("#jqxgrid_soldesem").jqxGrid('getrowid', selectedrowindex);
                                    var datarow = $("#jqxgrid_soldesem").jqxGrid('getrowdata', id);

                                    var idsol =  datarow.ID;
                                    id_ope_actual = datarow.ID_OPERACION;
                                    var obj = {
                                        "ESTADO":'1'
                                    }
                                    
                                    
                                    
                                    
                                    $.ajax({
                                        url : _carpetas.URL + "/x_get_traza_aux1",
                                        type : "post",
                                        data : {
                                            id_ope_actual:id_ope_actual
                                        },
                                        async:false,
                                        success : function(data){
                                            if (data>0){
                                                //cambiar estado
                                                jConfirm('Esta seguro de realizar esta acción?.', 'Carpetas',function(r){
                                                    if(r==true){
                                                        $.ajax({
                                                            url : _carpetas.URL + "/x_update_soldese",
                                                            type : "post",
                                                            data : {
                                                                idsol:idsol,
                                                                obj:obj,
                                                                id_ope_actual:id_ope_actual
                                                            },
                                                            async:false,
                                                            success : function(data){
                                                                jAlert('Operacion Exitosa.', 'Carpetas',function(){
                                                                    $.unblockUI();
                                                                    $.fancybox.close();
                                                                    $("#jqxgrid_soldesem").jqxGrid('updatebounddata');
                                                                });
                                                            }
                                                        });
                                                    }else{
                                                        $.unblockUI();
                                                    }
                                                });
                                            }else{
                                                jAlert('Aun no autorizaron esta accion.', $.ucwords(_etiqueta_modulo),function(){
                                                    $.unblockUI();
                                                });
                                            }
                                            
                                            
                                        }
                                    });

                                }

                        }else{
                            jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){

                            });
                            return false;
                        }


                    }else{
                        jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                        });
                        return false;
                    }
                });
            }
            
            responderButton.click(function (event) {
                
                var selectedrowindex = $("#jqxgrid_soldesem").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_soldesem").jqxGrid('getdatainformation').rowscount;
                
                if ( selectedrowindex != '-1' ){
                    if (selectedrowindex<rowscount){
                        
                            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                var id = $("#jqxgrid_soldesem").jqxGrid('getrowid', selectedrowindex);
                                var datarow = $("#jqxgrid_soldesem").jqxGrid('getrowdata', id);
                                
                                var idsol =  datarow.ID;
                                id_ope_actual = datarow.ID_OPERACION;
                                
                                $.ajax({
                                     url : _carpetas.URL + "/x_getform_solicituddesembolso",
                                     data : {
                                          opeid:_array_obj.ID,
                                          idsol:idsol
                                     },
                                     type : "post",
                                     success : function(datasolicituddesembolso){

                                         $.fancybox({
                                             "content": datasolicituddesembolso,
                                             'padding'   :  35,
                                             'autoScale' :true,
                                             'height' : 900,
                                             'scrolling' : 'yes',
                                             'afterShow': function(){
                                                 $(".fancybox-inner").css({'overflow-x':'hidden'});
                                             }
                                         });

                                     }
                                 });
                                
                            }
                        
                    }else{
                        jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){

                        });
                        return false;
                    }
                    
                }else{
                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                    });
                    return false;
                }
            });
            
            responderButton2.click(function (event) {
                
                //verfificar estados de desembolsos
                //checklist desemb
                
                var items_dese = $("#listbox_cond_dese").jqxListBox('getItems');
                var cont_indet = 0;
                var tmpcad;
                
                if (items_dese){
                    $.each(items_dese, function (index, value){
                        if (value.checkBoxElement){
                            tmpcad = value.checkBoxElement.innerHTML;
                            if (tmpcad.indexOf("jqx-checkbox-check-indeterminate")>0 || tmpcad.indexOf("jqx-checkbox-check-checked")>0){
                                cont_indet++;
                            }
                        }
                    });
                }
                
                var des_chk = $("#chk_copiacontrato").is(":checked");
                
                
                
                if ( _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==11 && _array_obj.ID_PROCESO==1){
                    //validar los check de desembolsos
                    if (items_dese.length == cont_indet && des_chk==true){
                        //seguir
                        mostrarSoldes(_array_obj.ID,_array_obj.ID_PROCESO);
                    }else{
                        jAlert('Se deben evaluar todas las Condiciones de desembolso.', $.ucwords(_etiqueta_modulo),function(){
                            $.unblockUI();
                        });
                        return false;
                    }
                }else if ( _array_obj.ID_ETAPA_ACTUAL==13 && _USER_ROL==11 && _array_obj.ID_PROCESO==2){
                     mostrarSoldes(_array_obj.ID,_array_obj.ID_PROCESO);
                }
                else{
                        jAlert('Usted no puede agregar Solicitudes de desembolso.', $.ucwords(_etiqueta_modulo),function(){
                                $.unblockUI();
                        });
                }
            });
            
        },
        columnsresize: true,
        localization: getLocalization(),
        columns: [
            { text: 'ID', datafield: 'ID', width: '0%', hidden : true, filterable : false , align: 'center'},
            { text: 'OPERACION', datafield: 'ID_OPERACION', width: '20%', hidden : false, filterable : false , align: 'center'},
            { text: 'PROYECTO', datafield: 'PROYECTO', width: '30%', hidden : false, filterable : false , align: 'center'},
            { text: 'NUMERO', datafield: 'DES_NUMERO', width: '15%', hidden : false, filterable : false , align: 'center'},
            { text: 'MONTO', datafield: 'DES_MONTO', width: '15%', hidden : false, filterable : false , align: 'center'},
            { text: 'ESTADO', datafield: 'ESTADONR', width: '20%', hidden : false, filterable : false , align: 'center'}
            
        ]
    });
}

function mostrarSoldes(iid,proceso){
    $.ajax({
        url : _carpetas.URL + "/x_getform_solicituddesembolso",
        data : {
             opeid:iid
        },
        type : "post",
        success : function(datasolicituddesembolso){

            $.fancybox({
                "content": datasolicituddesembolso,
                'padding'   :  35,
                'autoScale' :true,
                'height' : 900,
                'scrolling' : 'yes',
                'afterShow': function(){
                    $(".fancybox-inner").css({'overflow-x':'hidden'});
                }
            });


            $("#desem_monto" ).numeric({ negative: false });
            $("#desem_numero").numeric({ negative: false, decimal:false });


            $(".send_solicdesembolso").on('click', function(e){
                 //validar el monto
                var mf = $("#cmaprob").val(); // monto firmado
                var sd = $("#_suma_desembolsosh").val(); //suma desembolsos
                var gc = $("#_suma_garantiash").val(); //suma garantias constituidas
                var md = $("#desem_monto").val(); // monto dsembolso actual
                var m1 = sd*1+md*1;
                // (sd+monto) comparar contra monto firmado
                if ( (m1*1>mf*1) && proceso==1){
                    jAlert('La suma de los desembolsos no puede ser mayor al monto de la firma de contrato ('+mf+').', $.ucwords(_etiqueta_modulo),function(){
                        $("#desem_monto").focus().select();
                    });
                    return false;
                }
                
                //validaciones
                if ($("#desem_numero").val()==''){
                    jAlert('Ingrese Número.', $.ucwords(_etiqueta_modulo),function(){
                        $("#desem_numero").focus();
                    });
                    return false;
                }
                //validaciones
                if ($("#desem_monto").val()==''){
                    jAlert('Ingrese Cantidad.', $.ucwords(_etiqueta_modulo),function(){
                        $("#desem_monto").focus();
                    });
                    return false;
                }
                
                

                //gc
                if ((m1*1>gc*1) && proceso==1){
                    jAlert('La suma de los desembolsos no puede ser mayor al valor de la suma de las garantias constituidas ('+gc+').', $.ucwords(_etiqueta_modulo),function(){
                        $("#desem_monto").focus().select();
                    });
                    return false;
                }

                 //var id_creditoh = $("#id_creditoh").val();
                 var arr_frm_alta = {'ID_OPERACION':iid,'id':0};
                 $("#form_desem .save input").each(function( index ) {
                     var campo = $(this).data('campo');
                     var valor = $(this).val();
                     arr_frm_alta[campo] = valor;
                 });
                 arr_frm_alta['obs'] = $("#desem_obs").val();

                 $.ajax({
                      url : _carpetas.URL + "/x_guardar_soldesem",
                      data : {
                          obj:arr_frm_alta
                      },
                      dataType : "json",
                      type : "post",
                      success : function(dato){

                          if(dato.result>0){
                             jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                 //sacar el fancybox
                                 $.fancybox.close();
                                 $("#jqxgrid_soldesem").jqxGrid('updatebounddata');
                             });
                          }else{
                             jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                 $.unblockUI();
                             });
                          }

                      }
                  });
             });
        }
    });
    
}



function agregar_altacredito(){
    /*Garantias*/
    $("#agregar_altacredito").jqxButton( { width: '250', theme: theme } );
    

    $("#agregar_altacredito").on('click', function(event){
        var credito_id_ajax = 0;
        var idope = $(this).data('idope');
        
        //pedir credito con ajax
        $.ajax({
            url : _carpetas.URL + "/x_get_solicitud_de_credito",
            data : {
                idope:idope
            },
            dataType : "json",
            type : "post",
            success : function(dato_dol){
                //console.dir(dato_dol);
                credito_id_ajax = dato_dol.ID;
                //var credito = $(this).data('credito');
                
                var credito = credito_id_ajax;
                var id_credito = 0;
                if ( (credito && credito>0) ){
                    id_credito = credito;
                }
                //console.log( id_credito );
                $.ajax({
                     url : _carpetas.URL + "/x_getform_altacredito",
                     data : {
                         opeid:_array_obj.ID,
                         id_credito:id_credito
                     },
                     async : false,
                     type : "post",
                     success : function(dataaltacredito){

                         $.fancybox({
                             "content": dataaltacredito,
                             'padding'   :  35,
                             'autoScale' :true,
                             'height' : 900,
                             'scrolling' : 'yes',
                             'afterShow': function(){
                                 $(".fancybox-inner").css({'overflow-x':'hidden'});
                             }
                         });


                         $("#alta_nacta").val($("#nacta").val());
                         $("#alta_facta").val($("#facta").val());
                         $("#alta_cffirma").val($("#cffirma").val());
                         $("#alta_cmaprob").val($("#cmaprob").val());

                         $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                         
                         $("#alta_por_cmf").numeric({ negative: false });
                         $("#alta_propios").numeric({ negative: false });
                         $("#alta_por_propios").numeric({ negative: false });
                         $("#alta_otros").numeric({ negative: false });
                         $("#alta_por_otros").numeric({ negative: false });
                         
                         $("#alta_total").numeric({ negative: false });
                         $("#alta_por_total").numeric({ negative: false });
                         
                         $("#alta_desem1").numeric({ negative: false });
                         $("#alta_desem2").numeric({ negative: false });
                         $("#alta_desem3").numeric({ negative: false });
                         $("#alta_desem4").numeric({ negative: false });
                         $("#alta_desem5").numeric({ negative: false });
                         $("#alta_desem6").numeric({ negative: false });
                         
                         
                         //preguntar si es coord o finanzas
                         //alta_de_credito
                         if (_array_obj.ID_ETAPA_ACTUAL==12 && _USER_ROL==10){
                             $(".alta_de_credito").hide();
                             $(".send_altacredito").show();
                         }
                         if (_array_obj.ID_ETAPA_ACTUAL==12 && _USER_ROL==11){
                             $(".alta_de_credito").show();
                             $(".send_altacredito").hide();
                         }
                         
                         $("#alta_cap_vto").blur(function(e){
                            //validar
                            e.preventDefault();
                            
                            var d = new Date();
                            var month = d.getMonth()+1;
                            var day = d.getDate();

                            var output = 
                                (day<10 ? '0' : '') + day + '/' +
                                (month<10 ? '0' : '') + month + '/' +
                                d.getFullYear();
                            
                            var dato = $(this).val();
                            var valf = validarFechafunc( dato );
                            if (valf==1){
                                //continuar
                            }else{
                                if ( !(valf=='Fecha no válida' && dato=='')){
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#alta_cap_vto").val(output);
                                        $("#alta_cap_vto").focus();
                                    });
                                    return false;
                                }
                            }
                        });
                        $("#alta_int_vto").blur(function(e){
                            //validar
                            e.preventDefault();
                            
                            var d = new Date();
                            var month = d.getMonth()+1;
                            var day = d.getDate();

                            var output = 
                                (day<10 ? '0' : '') + day + '/' +
                                (month<10 ? '0' : '') + month + '/' +
                                d.getFullYear();
                            
                            var dato = $(this).val();
                            var valf = validarFechafunc( dato );
                            if (valf==1){
                                //continuar
                            }else{
                                if ( !(valf=='Fecha no válida' && dato=='')){
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#alta_int_vto").val(output);
                                        $("#alta_int_vto").focus();
                                    });
                                    return false;
                                }
                            }
                        });

                         $(".send_altacredito").on('click', function(e){
                            var idcredito = $("#id_creditoh").val();
                             
                            //validar domicilio y actividad
                            if ($("#alta_domproy").val()==''){
                                jAlert('Ingrese Domicilio del Proyecto.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#alta_domproy").focus();
                                });
                                return false;
                            }
                            if ($("#alta_actividadtitular").val()==''){
                                jAlert('Ingrese Actividad del Titular.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#alta_actividadtitular").focus();
                                });
                                return false;
                            }
                            //fechas
                            if ($("#alta_int_vto").val()==''){
                                jAlert('Ingrese Fecha.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#alta_int_vto").focus();
                                });
                                return false;
                            }
                            if ($("#alta_cap_vto").val()==''){
                                jAlert('Ingrese Fecha.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#alta_cap_vto").focus();
                                });
                                return false;
                            }
                             
                             
                            $.ajax({
                                url : _carpetas.URL + "/x_get_tienecuotas",
                                data : {
                                    idcredito:idcredito
                                },
                                dataType : "json",
                                type : "post",
                                success : function(datac){
                                    if (datac>0){
                                        jAlert('Este Crédito ya fue dado de alta1. Vea las cuotas en el informe correspondiente.', $.ucwords(_etiqueta_modulo),function(){
                                            
                                        });
                                    }else{
                                        
                                        //validar el monto de los desembolsos
                                        var d1 = $("#alta_desem1").val();
                                        var d2 = $("#alta_desem2").val();
                                        var d3 = $("#alta_desem3").val();
                                        var d4 = $("#alta_desem4").val();
                                        var d5 = $("#alta_desem5").val();
                                        var d6 = $("#alta_desem6").val();
                                        
                                        //var t = parseFloat(d1)+parseFloat(d2)+parseFloat(d3)+parseFloat(d4)+parseFloat(d5)+parseFloat(d6);
                                        var t = d1*1+d2*1+d3*1+d4*1+d5*1+d6*1;
                                        var mc = $("#cmaprob").val();
                                        
                                        if (t>mc){
                                            jAlert('La suma de los desembolsos estimados no puede ser mayor al monto de la firma de contrato ('+mc+').', $.ucwords(_etiqueta_modulo),function(){
                                                $("#alta_desem1").focus().select();
                                            });
                                            return false;
                                        }
                                        /*
                                        //fecha actual
                                        var d = new Date();
                                        var month = d.getMonth()+1;
                                        var day = d.getDate();

                                        var output = 
                                            (day<10 ? '0' : '') + day + '/' +
                                            (month<10 ? '0' : '') + month + '/' +
                                            d.getFullYear();
                                        
                                        //validacion de fechas
                                        var f1 = $("#alta_cap_vto").val();
                                        if (f1==''){
                                            jAlert('Ingrese una fecha.', $.ucwords(_etiqueta_modulo),function(){
                                                //$("#alta_cap_vto").datepicker("show");
                                                $("#alta_cap_vto").val(output);
                                            });
                                            return false;
                                        }else{
                                            var valf = validarFechafunc( f1 );
                                            console.log(valf);
                                            if (valf==1){
                                                //continuar
                                                f1 = formattedDate(f1);
                                            }else{
                                                jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                                    $("#alta_cap_vto").val(output);
                                                    $("#alta_cap_vto").focus();
                                                });
                                                return false;
                                            }
                                        }
                                        */
                                       
                                        
                                        var id_creditoh = $("#id_creditoh").val();
                                        var arr_frm_alta = {'ID_OPERACION':_array_obj.ID,'id':id_creditoh};
                                        $(".frm_altadecredito div.save input").each(function( index ) {
                                            var campo = $(this).data('campo');
                                            var valor = $(this).val();
                                            //validaciones
                                            //INTERES_VTO
                                            //CAPITAL_VTO
                                            arr_frm_alta[campo] = valor;
                                        });
                                        
                                        var arr_desem = [];
                                        var sw = 0;
                                        var obs_tmp ='';
                                        $(".frm_altadecredito div.savedes input").each(function( index ) {
                                            var monto = ""   
                                            if ($(this).hasClass('des_monto')){
                                                var monto = $(this).val();
                                                sw++;
                                            }else{
                                                var obs = $(this).val();
                                                obs_tmp = obs;
                                                sw++;
                                            }
                                            if (sw==2){
                                                if(monto && obs_tmp){
                                                   arr_desem.push({monto:monto,obs:obs_tmp});
                                                   sw=0;
                                                   obs_tmp='';
                                                }
                                           }
                                        });

                                        $.ajax({
                                             url : _carpetas.URL + "/x_guardar_altacredito",
                                             data : {
                                                 obj:arr_frm_alta,
                                                 arr_desem:arr_desem
                                             },
                                             dataType : "json",
                                             type : "post",
                                             success : function(dato){
                                                 console.dir(dato);
                                                 if(dato.result>0){
                                                    jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                                        if (dato.accion=='add'){
                                                            //actualizar el boton
                                                            //data-credito="12"
                                                            $("#agregar_altacredito").attr("data-credito",dato.result);
                                                        }

                                                        //sacar el fancybox
                                                        $.fancybox.close();
                                                    });
                                                 }else{
                                                    jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                                        $.unblockUI();
                                                    });
                                                 }
                                             }
                                         });
                                        
                                    }
                                }
                            });
                         });
                         
                         
                         $(".alta_de_credito").on('click', function(){
                            //preguntar si existe ya, el alta de credito 
                            //preguntar en la tabla fid_creditos_cuotas
                            var idcredito = $("#id_creditoh").val();
                            $.ajax({
                                url : _carpetas.URL + "/x_get_tienecuotas",
                                data : {
                                    idcredito:idcredito
                                },
                                dataType : "json",
                                type : "post",
                                success : function(datac){
                                    if (datac>0){
                                        jAlert('Este Crédito ya fue dado de alta2. Vea las cuotas en el informe correspondiente.', $.ucwords(_etiqueta_modulo),function(){
                                            
                                        });
                                    }else{
                                        var cred = $("#id_creditoh").val();
                                        $(".frm_altadecredito").hide();
                                        load_app("creditos/front/formalta","#div_altadecredito",[cred],
                                        function(){
                                            $('#jqxgrid').hide();
                                            $.unblockUI();
                                            console.log('aaaaaa' );
                                        },
                                        function(){
                                            $.fancybox.close();console.log('bbbbbbb');
                                        },
                                        function(){
                                            console.log('ccccccc');
                                        }); 
                                    }
                                    
                                    
                                }
                            });
                         });
                         
                         init_datepicker('#alta_int_vto','-3','+5','0',0);
                         init_datepicker('#alta_cap_vto','-3','+5','0',0);

                         $("#alta_int_vto").val($("#id_interes_vtoh").val());
                         $("#alta_cap_vto").val($("#id_capital_vtoh").val());

                         var interes_periodoh = $("#interes_periodoh").val();
                         //$("#alta_int_periodo").val(interes_periodoh).trigger("chosen:updated");

                         var interes_capitalh = $("#interes_capitalh").val();
                         //$("#alta_cap_periodo").val(interes_capitalh).trigger("chosen:updated");
                         
                         
                         $( "#alta_propios" ).keyup(function() {
                            var m0 = parseFloat( $("#alta_cmaprob").val() );
                            var m1 = parseFloat ($(this).val());
                            var m2 = parseFloat ($("#alta_otros").val());
                            m0 = isNaN(m0)?'0':m0;
                            m1 = isNaN(m1)?'0':m1;
                            m2 = isNaN(m2)?'0':m2;
                            $("#alta_total").val( 1*m0 + 1*m1 + 1*m2 );
                            
                            var p1 = (parseFloat($("#alta_cmaprob").val()) / parseFloat($("#alta_total").val()) ) * 100;
                            var rounded1 = Math.round( p1 * 100) / 100;
                            $("#alta_por_cmf").val(rounded1);
                            
                            var p2 = (parseFloat($("#alta_propios").val()) / parseFloat($("#alta_total").val()) ) * 100;
                            p2 = isNaN(p2)?'0':p2;
                            var rounded2 = Math.round( p2 * 100) / 100;
                            $("#alta_por_propios").val(rounded2);
                            
                            var rounded3 = Math.round( (100-1*p1-1*p2) * 100) / 100;
                            rounded3 = isNaN(rounded3)?'0':rounded3;
                            $("#alta_por_otros").val( rounded3 );
                            
                            
                         });
                         
                         $( "#alta_otros" ).keyup(function() {
                            var m0 = parseFloat( $("#alta_cmaprob").val() );
                            var m1 = parseFloat ( ($(this).val()));
                            var m2 = parseFloat ($("#alta_propios").val());
                            m0 = isNaN(m0)?'0':m0;
                            m1 = isNaN(m1)?'0':m1;
                            m2 = isNaN(m2)?'0':m2;
                            $("#alta_total").val( 1*m0 + 1*m1 + 1*m2 );
                            
                            var p1 = (parseFloat($("#alta_cmaprob").val()) / parseFloat($("#alta_total").val()) ) * 100;
                            var rounded1 = Math.round( p1 * 100) / 100;
                            $("#alta_por_cmf").val(rounded1);
                            
                            var p2 = (parseFloat($("#alta_propios").val()) / parseFloat($("#alta_total").val()) ) * 100;
                            p2 = isNaN(p2)?'0':p2;
                            var rounded2 = Math.round( p2 * 100) / 100;
                            $("#alta_por_propios").val(rounded2);
                            
                            var rounded3 = Math.round( (100-1*p1-1*p2) * 100) / 100;
                            rounded3 = isNaN(rounded3)?'0':rounded3;
                            $("#alta_por_otros").val( rounded3 );
                         });

                         /*    
                         var cod = $("#codigo").val();
                         $("#operacion").val(cod);
                         var cliente = $("#cliente option:selected").html();
                         $("#gar_cliente").val(cliente);
                         $(".chzn-select").chosen({ disable_search_threshold: 5 }); 

                         init_datepicker('#fini','-3','+0','0',1);
                         init_datepicker('#ffin','-3','+10','0',1);
                         init_datepicker('#soltas','-3','+0','0',1);
                         init_datepicker('#pretas','-3','+10','0',1);
                         */


                     }
                 });

                
                
                
                
            }
        });
        
        
         
    });
}

function agregar_garantias(){
    /*Garantias*/
    
    $("#agregar_garantia").jqxButton( { width: '150', theme: theme } );
    $("#editar_garantia").jqxButton(  { width: '150', theme: theme } );

    $("#agregar_garantia").on('click', function(event){
        
       
        
        
    });
}


function evento_lista_req(no_save){
    no_save || ( no_save = '0' );
    
    $(".grid_reqs .lista_reqs li").not(".li_cabezera").click(function(e){
        e.preventDefault();
        var idr = $(this).data("idr");
        var remitente = $(this).data("remitente");

        if (remitente==_USUARIO_SESION_ACTUAL){
            no_save = 0;
        }
            
        $.ajax({
            url : _carpetas.URL + "/x_getform_agregar_requerimiento",
            data : {
                idr:idr,
                no_save:no_save
            },
            type : "post",
            success : function(datareq){
                $.fancybox(
                    datareq,
                    {
                        'padding'   :  35,
                        'autoScale' :true,
                        'height' : 700,
                        'scrolling' : 'no'
                    }
                );

                init_datepicker('#femis','-3','+5','0',0);
                //init_datepicker('#fresp','-3','+0','0',1);
                $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
                $("input[type=file]").each(function(){
                    if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");}
                });
                
                var femish = $("#femish").val();
                $('#femis').val(femish);
                
                var fresph = $("#fresph").val();
                $('#fresp').val(fresph);
                $("#req_respuesta").attr("readonly","readonly");
                
                var estadoh_req = $("#estadoh").val();
                if (estadoh_req==3 && (_USER_ROL=='10' || _USER_ROL=='11') ){
                    //hacer esto siempre y cuando el estado sea = 3                
                    $(".btn_insuf").addClass("ver_fuerte");
                    $(".btn_suf").addClass("ver_fuerte");
                }
                $("#estadoreq").val(estadoh_req).attr('disabled', true).trigger("chosen:updated");
                addEventsRequerimientos(idr);
                
                $("#femis").blur(function(e){
                    //validar
                    e.preventDefault();

                    var d = new Date();
                    var month = d.getMonth()+1;
                    var day = d.getDate();

                    var output =
                        (day<10 ? '0' : '') + day + '/' +
                        (month<10 ? '0' : '') + month + '/' +
                        d.getFullYear();

                    var dato = $(this).val();
                    var valf = validarFechafunc( dato );
                    if (valf==1){
                        //continuar
                    }else{
                        if ( !(valf=='Fecha no válida' && dato=='')){
                            jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                $("#femis").val(output);
                                $("#femis").focus();
                            });
                            return false;
                        }
                    }
                });
                
                $("#fresp").blur(function(e){
                    //validar
                    e.preventDefault();

                    var d = new Date();
                    var month = d.getMonth()+1;
                    var day = d.getDate();

                    var output = 
                        (day<10 ? '0' : '') + day + '/' +
                        (month<10 ? '0' : '') + month + '/' +
                        d.getFullYear();

                    var dato = $(this).val();
                    var valf = validarFechafunc( dato );
                    if (valf==1){
                        //continuar
                    }else{
                        if ( !(valf=='Fecha no válida' && dato=='')){
                            jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                $("#fresp").val(output);
                                $("#fresp").focus();
                            });
                            return false;
                        }
                    }
                });
                
            }
        });

        /*
        $.ajax({
            url : _carpetas.URL + "/x_get_req",
            data : {
                idr:idr
            },
            dataType : "json",
            type : "post",
            success : function(data){
                if (data.ESTADO==3){
                    $(".enviada_al_usuario").hide();
                    $(".acep_rech").show();
                }
                $("#req_descripcion").val(data.DESCRIPCION);
                $("#idreqh").val(data.ID);
                $("#req_asunto").val(data.ASUNTO).select().focus();
                $(".enviada_al_usuario span.checkbox").css("background-position","0px 0px");
                $("#enviada_al_usuario").removeAttr('checked');
                if (data.ESTADO==2){
                    $(".enviada_al_usuario span.checkbox").css("background-position","0px -50px");
                    $("#enviada_al_usuario").attr('checked', true);
                }

            }
        });
        */
    });
    
    
    
}


function event_grid_traza(idope){
        
    /*
     
     **/    
        
    var sourcetraza ={
        datatype: "json",
        datafields: [
            { name: 'ID' },
            { name: 'DESCRIPCION' , type: 'string' },
            { name: 'ETAPA' , type: 'string' },
            { name: 'USUARIO' , type: 'string' },
            { name: 'FECHA' , type: 'string' },
            { name: 'AUTOR_NOMBRE' , type: 'string' },
            { name: 'AUTOR1_NOMBRE' , type: 'string' }
        ],
        url: 'general/extends/extra/carpetas.php',
        data:{
            accion  :   "getTrazabilidad",
            idope  :   idope
        }
    };
    
    var dataAdaptertraza = new $.jqx.dataAdapter(sourcetraza,
        {
            
        }
    );
        
			
    $("#jqxgrid_traza").jqxGrid({
        width: '900px',
        source: dataAdaptertraza,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid_traza").jqxGrid('hidecolumn', 'ID');
        },
        columnsresize: true,
        localization: getLocalization(),
        columns: [
            { text: 'ETAPA', datafield: 'ETAPA', width: '15%', hidden : false },
            { text: 'USUARIO', datafield: 'USUARIO', width: '15%', hidden : false },
            { text: 'FECHA', datafield: 'FECHA', width: '20%', hidden : false },
            { text: 'DESCRIPCION', datafield: 'DESCRIPCION', width: '60%', hidden : false },
            { text: 'AUTORIZACION 1', datafield: 'AUTOR_NOMBRE', width: '20%', hidden : false },
            { text: 'AUTORIZACION 2', datafield: 'AUTOR1_NOMBRE', width: '20%', hidden : false }
            
        ]
    });
    
    
    
}


function agregar_garantias_1(acc, _array_obj){
    
    acc || ( acc = ' edi' );
    
    var idope = '9999';
    if(acc=='edi'){
        idope = _array_obj.ID;
    }else if(acc=='add'){
        var _array_obj = [];
        _array_obj.ID = '9999';
    }
    
    /*
    var idope = '9999';
    if (typeof(_array_obj) !== "undefined"){
        idope = _array_obj.ID;
    }else{
        var _array_obj = [];
        _array_obj.ID = '9999';
    }
    */
    
    //var idope = _array_obj.ID;
    
    //console.log ( idope );
    
    var source_gar ={
        datatype: "json",
        datafields: [
            { name: 'ID' },
            { name: 'ID_OPERACION' },
            { name: 'TIPOG', type: 'string' },
            { name: 'VALOR_GARANTIA', type: 'string' },
            { name: 'ESTADOGARNUM', type: 'string' },
            { name: 'ESTADOG', type: 'string' }
        ],
        url: 'general/extends/extra/carpetas.php',
        data:{
            accion: "getGarantias",
            idope: idope
        }
    };
    
    var dataAdapter_soldese = new $.jqx.dataAdapter(source_gar,
        {
            formatData: function (data) {
                data.name_startsWith = $("#searchField").val();
                return data;
            }
        }
    );
        
        
        
			
    $("#jqxgrid_garantias").jqxGrid(
    {
        width: '98%',
        source: dataAdapter_soldese,
        theme: 'energyblue',
        ready: function () {
            
            refrescarGarantias();
            
            
        },
        showstatusbar: true,
        renderstatusbar: function (statusbar) {
            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
            var responderButton = $("<div style='float: left; margin-left: 5px;'><img width=16 style='position: relative; margin-top: 2px;' src='general/css/images/32x32/checked.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Editar</span></div>");
            var responderButton1 = $("<div style='float: left; margin-left: 5px;'><img width=16 style='position: relative; margin-top: 2px;' src='general/css/images/32x32/checked.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Cambiar Estado</span></div>");
            var responderButton2 = $("<div style='float: left; margin-left: 5px;'><img width=16 style='position: relative; margin-top: 2px;' src='general/css/images/32x32/checked.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Agregar</span></div>");
            var adjuntoButton = $("<div style='float: left; margin-left: 5px;'><img width=16 style='position: relative; margin-top: 2px;' src='general/css/images/32x32/checked.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Adjunto</span></div>");
            container.append(responderButton2);
            container.append(responderButton);
            container.append(responderButton1);
            container.append(adjuntoButton);
            
            statusbar.append(container);
            responderButton.jqxButton ( { theme: theme, width: 150, height: 20 });
            responderButton1.jqxButton( { theme: theme, width: 150, height: 20 });
            responderButton2.jqxButton( { theme: theme, width: 150, height: 20 });
            adjuntoButton.jqxButton   ( { theme: theme, width: 150, height: 20 });
            
            adjuntoButton.click(function (event) {
                var selectedrowindex = $("#jqxgrid_garantias").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_garantias").jqxGrid('getdatainformation').rowscount;
                
                if ( selectedrowindex != '-1' ){
                    if (selectedrowindex<rowscount){
                                var id = $("#jqxgrid_garantias").jqxGrid('getrowid', selectedrowindex);
                                var datarow = $("#jqxgrid_garantias").jqxGrid('getrowdata', id);
                                var id_garantia =  datarow.ID;

                            $.ajax({
                                url : _carpetas.URL + "/x_getform_adjunto_gar",
                                data : {
                                    etapa:999,
                                    id_garantia:id_garantia
                                },
                                type : "post",
                                success : function(dataadj){
                                   $.fancybox(
                                        dataadj,
                                        {
                                            'padding'   :  35,
                                            'autoScale' :true,
                                            'height' : 400,
                                            'scrolling' : 'no'
                                        }
                                    );
                                    $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
                                    $("input[type=file]").each(function(){
                                    if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");}
                                    });

                                    $("#btnSubirfile").click(function(e){
                                        if ($("#req_etiqueta").val()==''){
                                                e.preventDefault();
                                                jAlert('Ingrese una etiqueta, por favor.', $.ucwords(_etiqueta_modulo),function(){
                                                    $("#req_etiqueta").select();
                                                });
                                        }

                                    });
                                    $( "#req_etiqueta" ).keyup(function() {
                                        $("#req_etiquetah").val( $(this).val() );
                                    });



                                }
                            });
                        
                        
                    }else{
                        jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){

                        });
                        return false;
                    }
                }else{
                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                    });
                    return false;
                }
                
               
               
            });
            
            
            responderButton1.click(function (event) {
                var selectedrowindex = $("#jqxgrid_garantias").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_garantias").jqxGrid('getdatainformation').rowscount;
                                
                if ( selectedrowindex != '-1' ){
                    if (selectedrowindex<rowscount){
                        
                            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                var id = $("#jqxgrid_garantias").jqxGrid('getrowid', selectedrowindex);
                                var datarow = $("#jqxgrid_garantias").jqxGrid('getrowdata', id);
                                
                                var id_garantia =  datarow.ID;
                                id_ope_actual = datarow.ID_OPERACION;
                                
                                //cambiar estado
                                $.ajax({
                                    url : _carpetas.URL + "/x_getform_actualizarestadogarantia",
                                    type : "post",
                                    data : {
                                        opeid:id_ope_actual
                                    },
                                    async:false,
                                    success : function(datafrm_estadogar){
                                        $.fancybox({
                                            "content": datafrm_estadogar,
                                            'padding'   :  35,
                                            'autoScale' :true,
                                            'height' : 700,
                                            'scrolling' : 'no',
                                            'afterShow': function(){
                                                //$(".fancybox-inner").css({'overflow-x':'hidden'});
                                            }
                                        });
                                        
                                        $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                                        
                                        //
                                        $('.send_cambiarestado_gar').on('click', function(event) {
                                            //cambiar estado, llamar a ajax y leer el estado y actualizar el id_garantia
                                            var nuevo_estado = $("#change_tipo_garantia").val();
                                            var obj_edit = {
                                                'ID_ESTADO':nuevo_estado
                                            }
                                            $.ajax({
                                                url : _carpetas.URL + "/x_actualizar_garantia",
                                                data : {
                                                    idgar:id_garantia,
                                                    obj:obj_edit
                                                },
                                                dataType : "json",
                                                type : "post",
                                                success : function(data){
                                                    $.fancybox.close();
                                                    
                                                    $("#jqxgrid_garantias").jqxGrid('updatebounddata');
                                                    //$('#jqxgrid_garantias').jqxGrid('render');
                                                    //refrescarGarantias();
                                                    //$('#jqxgrid_garantias').jqxGrid('refresh');
                                                    //$('#jqxgrid_garantias').jqxGrid('refreshdata');
                                                    
                                                    $('#jqxgrid_garantias').trigger('ready');
                                                    


                                                    
                                                }
                                            });

                                        });
                                        
                                        

                                    }
                                });
                                    
                            }
                        
                    }else{
                        jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){

                        });
                        return false;
                    }
                    
                    
                }else{
                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                    });
                    return false;
                }
            });
            
            responderButton.click(function (event) {
                
                var selectedrowindex = $("#jqxgrid_garantias").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_garantias").jqxGrid('getdatainformation').rowscount;
                
                if ( selectedrowindex != '-1' ){
                    if (selectedrowindex<rowscount){
                        
                            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                var id = $("#jqxgrid_garantias").jqxGrid('getrowid', selectedrowindex);
                                var datarow = $("#jqxgrid_garantias").jqxGrid('getrowdata', id);
                                
                                var idgar =  datarow.ID;
                                id_ope_actual = datarow.ID_OPERACION;
                                
                                $.ajax({
                                     //url : _carpetas.URL + "/x_getform_solicituddesembolso",
                                     url : _carpetas.URL + "/x_getform_garantias",
                                     data : {
                                          opeid:_array_obj.ID,
                                          idgar:idgar
                                     },
                                     type : "post",
                                     success : function(datasolicituddesembolso){

                                         $.fancybox({
                                             "content": datasolicituddesembolso,
                                             'padding'   :  35,
                                             'autoScale' :true,
                                             'height' : 900,
                                             'scrolling' : 'yes',
                                             'afterShow': function(){
                                                 $(".fancybox-inner").css({'overflow-x':'hidden'});
                                             }
                                         });
                                         
                                         $(".chzn-select").chosen({ disable_search_threshold: 5 }); 
                                         //ocultar boton guardar
                                         
                                         //$(".frm_garantia .elempie").html('');
                                         //valores
                                         var id_tipoh = $("#id_tipoh").val();
                                         $("#tipo_garantia").val(id_tipoh).trigger("chosen:updated");
                                         
                                         var id_objetoh = $("#id_objetoh").val();
                                         loadChild_gar(id_tipoh);
                                        $("#objeto").val(id_objetoh).trigger("chosen:updated");
                                        $("#objeto").change();
                                         
                                         
                                        init_datepicker('#fini','-3','+5','0',0);
                                        init_datepicker('#ffin','-3','+5','0',0);
                                        init_datepicker('#soltas','-3','+5','0',0);
                                        init_datepicker('#pretas','-3','+5','0',0);
                                        $("#valortas").numeric({ negative: false });
                                        $("#aforo").numeric({ negative: false, decimal:false });
                                        
                                        $( "#aforo" ).keyup(function() {
                                            if ($(this).val()==0)
                                                $("#valorgar").val( 0 );
                                            else
                                                $("#valorgar").val( $("#valortas").val() * 100 / $(this).val() );
                                        });
                                        
                                        
                                        $('#tipo_garantia').bind('change', function(event){
                                            event.preventDefault();
                                            $(this).validationEngine('validate');

                                            if ($('#tipo_garantia').val()=='')
                                                loadChild_gar(0)

                                            $('#tipo_garantiah').val($('#tipo_garantia').val());

                                            var selected = $(this).find('option').eq(this.selectedIndex);

                                            var connection = selected.data('connection');
                                            selected.closest('#rubro li').nextAll().remove();
                                            if(connection){
                                                loadChild_gar(connection);
                                            }
                                        });
                                         
                                         var id_estadoh = $("#id_estadoh").val();
                                         $("#estado_garantia").val(id_estadoh).attr('disabled', true).trigger("chosen:updated");
                                         
                                         var id_tasadorh = $("#id_tasadorh").val();
                                         $("#tasador").val(id_tasadorh).trigger("chosen:updated");
                                         
                                         var id_tipodato1h = $("#id_tipodato1h").val();
                                         $("#tipodato1").val(id_tipodato1h).trigger("chosen:updated");
                                         
                                         var id_tipodato2h = $("#id_tipodato2h").val();
                                         $("#tipodato2").val(id_tipodato2h).trigger("chosen:updated");
                                         
                                         var id_tipodato3h = $("#id_tipodato3h").val();
                                         $("#tipodato3").val(id_tipodato3h).trigger("chosen:updated");
                                         
                                         var fechadesdeh = $("#fechadesdeh").val();
                                         $("#fini").val(fechadesdeh);
                                         
                                         var fechahastah = $("#fechahastah").val();
                                         $("#ffin").val(fechahastah);
                                         
                                         var tasaf1h = $("#tasaf1h").val();
                                         $("#soltas").val(tasaf1h);
                                         
                                         var tasaf2h = $("#tasaf2h").val();
                                         $("#pretas").val(tasaf2h);
                                         
                                         
                                         $('.lista_adjuntos li').off().on('click', function(event) {
                                            event.preventDefault();
                                            var $this = $(this);
                                            var idfid = $this.data('identidad');
                                            var ruta = $this.data('ruta');
                                            var descripcion = $this.data('descripcion');

                                            jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                                                if(r==true){
                                                    //borrar archivo en la bd y fisicamente
                                                    $.ajax({
                                                        url : _carpetas.URL + "/x_delupload_gar",
                                                        data : {
                                                            idgar:idgar,
                                                            descripcion:descripcion
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
                        
                        
                        

                                         
                                         $(".send_guardargarantia").on('click', function(e){
                                            
                                            var fini = $("#fini").val();
                                            var ffin = $("#ffin").val();
                                             
                                            if (fini==''){
                                                jAlert('Ingrese una fecha.', $.ucwords(_etiqueta_modulo),function(){
                                                    $("#fini").datepicker("show");
                                                });
                                                return false;
                                            }else{
                                                var valf = validarFechafunc( $("#fini").val() );
                                                if (valf==1){
                                                    //continuar
                                                    fini = formattedDate(fini);
                                                }else{
                                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                                        $("#fini").focus();
                                                    });
                                                    return false;
                                                }
                                            }
                                            if (ffin==''){
                                                jAlert('Ingrese una fecha.', $.ucwords(_etiqueta_modulo),function(){
                                                    $("#ffin").datepicker("show");
                                                });
                                                return false;
                                            }else{
                                                var valf = validarFechafunc( $("#ffin").val() );
                                                if (valf==1){
                                                    //continuar
                                                    ffin = formattedDate(ffin);
                                                }else{
                                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                                        $("#ffin").focus();
                                                    });
                                                    return false;
                                                }
                                            }
                                             
                                             
                                            var objins = {
                                                'id':idgar,
                                                'ID_OPERACION':_array_obj.ID,
                                                'ID_TIPO':$("#tipo_garantiah").val(),
                                                'ID_OBJETO':$("#objetoh").val(),
                                                'FECHA_DESDE':fini,
                                                'FECHA_HASTA':ffin,
                                                'ID_TASADOR':$("#tasador").val(),
                                                'TASA_F1':$("#soltas").val(),
                                                'TASA_F2':$("#pretas").val(),
                                                'TAS_VALOR':$("#valortas").val(),
                                                'VALOR_GARANTIA':$("#valorgar").val(),
                                                'TAS_AFORO':$("#aforo").val(),
                                                'TIPO_DATO_1':$("#tipodato1").val(),
                                                'TIPO_DATO_2':$("#tipodato2").val(),
                                                'TIPO_DATO_3':$("#tipodato3").val(),
                                                'DATO_1':$("#dato1").val(),
                                                'DATO_2':$("#dato2").val(),
                                                'DATO_3':$("#dato3").val(),
                                                'ID_ESTADO':$("#estado_garantia").val(),
                                                'GARANTE':$("#garante").val(),
                                                'GARANTE_DOM':$("#gar-dom").val(),
                                                'GARANTE_CUIT':$("#gar-cuit").val(),
                                                'GARANTE_TEL':$("#gar-tel").val()
                                            }
                                            
                                            
                                            
                                            

                                            $.ajax({
                                                 url : _carpetas.URL + "/x_guardar_garantia",
                                                 data : {
                                                     obj:objins
                                                 },
                                                 dataType : "json",
                                                 type : "post",
                                                 success : function(dato){

                                                     if(dato.result>0){
                                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                                            //sacar el fancybox
                                                            $.fancybox.close();
                                                            $("#jqxgrid_garantias").jqxGrid('updatebounddata');
                                                        });
                                                     }else{
                                                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                                            $.unblockUI();
                                                        });
                                                     }
                                                 }
                                             });

                                        });
                                     }
                                 });
                            }
                        
                    }else{
                        jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){

                        });
                        return false;
                    }
                    
                }else{
                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                    });
                    return false;
                }
            });
            
            responderButton2.click(function (event) {
                
                $.ajax({
                    url : _carpetas.URL + "/x_getform_garantias",
                    data : {
                        opeid:_array_obj.ID
                    },
                    type : "post",
                    success : function(datagar){
                        $.fancybox({
                             "content": datagar,
                             'padding'   :  35,
                             'autoScale' :true,
                             'height' : 900,
                             'scrolling' : 'yes',
                             'afterShow': function(){
                                 $(".fancybox-inner").css({'overflow-x':'hidden'});
                             }
                         });

                        var cod = $("#codigo").val();
                        $("#operacion").val(cod);


                        $(".chzn-select").chosen({ disable_search_threshold: 5 }); 

                        init_datepicker('#fini','-3','+5','0',0);
                        init_datepicker('#ffin','-3','+5','0',0);
                        init_datepicker('#soltas','-3','+5','0',0);
                        init_datepicker('#pretas','-3','+5','0',0);
                        $("#valortas").numeric({ negative: false });
                        $("#aforo").numeric({ negative: false, decimal:false });
                        $( "#aforo" ).keyup(function() {
                            if ($(this).val()==0)
                                $("#valorgar").val( 0 );
                            else
                                $("#valorgar").val( $("#valortas").val() * 100 / $(this).val() );
                        });
                        $('#tipo_garantia').bind('change', function(event){
                            event.preventDefault();
                            $(this).validationEngine('validate');

                            if ($('#tipo_garantia').val()=='')
                                loadChild_gar(0)

                            $('#tipo_garantiah').val($('#tipo_garantia').val());

                            var selected = $(this).find('option').eq(this.selectedIndex);

                            var connection = selected.data('connection');
                            selected.closest('#rubro li').nextAll().remove();
                            if(connection){
                                loadChild_gar(connection);
                            }
                        });

                        //estado deshabilitado
                        $("#estado_garantia").val(1).attr('disabled', true).trigger("chosen:updated");
                        //
                        $(".send_guardargarantia").on('click', function(e){
                            
                            var fini = $("#fini").val();
                            var ffin = $("#ffin").val();
                            var soltas = $("#soltas").val();
                            var pretas = $("#pretas").val();
                            var tipo_gar = $("#tipo_garantiah").val();
                            var tipo_obj = $("#objetoh").val();
                            
                            if (tipo_gar==0){
                                jAlert('Elija un tipo de garantia.', $.ucwords(_etiqueta_modulo),function(){
                                });
                                return false;
                            }
                            if (tipo_obj==0){
                                jAlert('Elija un objeto.', $.ucwords(_etiqueta_modulo),function(){
                                });
                                return false;
                            }
                            if (fini==''){
                                jAlert('Ingrese una fecha.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#fini").datepicker("show");
                                });
                                return false;
                            }else{
                                var valf = validarFechafunc( $("#fini").val() );
                                if (valf==1){
                                    //continuar
                                    fini = formattedDate(fini);
                                }else{
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#fini").focus();
                                    });
                                    return false;
                                }
                            }
                            if (ffin==''){
                                jAlert('Ingrese una fecha.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#ffin").datepicker("show");
                                });
                                return false;
                            }else{
                                var valf = validarFechafunc( $("#ffin").val() );
                                if (valf==1){
                                    //continuar
                                    ffin = formattedDate(ffin);
                                }else{
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#ffin").focus();
                                    });
                                    return false;
                                }
                            }
                            
                            if (soltas==''){
                                jAlert('Ingrese una fecha.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#soltas").datepicker("show");
                                });
                                return false;
                            }else{
                                var valf = validarFechafunc( $("#soltas").val() );
                                if (valf==1){
                                    //continuar
                                    soltas = formattedDate(soltas);
                                }else{
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#soltas").focus();
                                    });
                                    return false;
                                }
                            }
                            
                            if (pretas==''){
                                jAlert('Ingrese una fecha.', $.ucwords(_etiqueta_modulo),function(){
                                    $("#pretas").datepicker("show");
                                });
                                return false;
                            }else{
                                var valf = validarFechafunc( $("#pretas").val() );
                                if (valf==1){
                                    //continuar
                                    pretas = formattedDate(pretas);
                                }else{
                                    jAlert(valf, $.ucwords(_etiqueta_modulo),function(){
                                        $("#pretas").focus();
                                    });
                                    return false;
                                }
                            }
                            
                            

                            var objins = {
                                'id':'0',
                                'ID_OPERACION':_array_obj.ID,
                                'ID_TIPO':tipo_gar,
                                'ID_OBJETO':tipo_obj,
                                'FECHA_DESDE':$("#fini").val(),
                                'FECHA_HASTA':$("#ffin").val(),
                                'ID_TASADOR':$("#tasador").val(),
                                'TASA_F1':soltas,
                                'TASA_F2':pretas,
                                'TAS_VALOR':$("#valortas").val(),
                                'VALOR_GARANTIA':$("#valorgar").val(),
                                'TAS_AFORO':$("#aforo").val(),
                                'TIPO_DATO_1':$("#tipodato1").val(),
                                'TIPO_DATO_2':$("#tipodato2").val(),
                                'TIPO_DATO_3':$("#tipodato3").val(),
                                'DATO_1':$("#dato1").val(),
                                'DATO_2':$("#dato2").val(),
                                'DATO_3':$("#dato3").val(),
                                'ID_ESTADO':$("#estado_garantia").val()
                            }

                            $.ajax({
                                 url : _carpetas.URL + "/x_guardar_garantia",
                                 data : {
                                     obj:objins
                                 },
                                 dataType : "json",
                                 type : "post",
                                 success : function(dato){

                                     if(dato.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            //sacar el fancybox
                                            $.fancybox.close();
                                            $("#jqxgrid_garantias").jqxGrid('updatebounddata');
                                        });
                                     }else{
                                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                        });
                                     }

                                 }
                             });
                             
                             
                        });
                        
                        
                    }
                });
                    
                
            });
            
        },
        columnsresize: true,
        localization: getLocalization(),
        columns: [
            { text: 'ID', datafield: 'ID', width: '10%', filterable : false , align: 'center'},
            { text: 'TIPO', datafield: 'TIPOG', width: '30%', hidden : false, filterable : false , align: 'center'},
            { text: 'VALOR GARANTIA', datafield: 'VALOR_GARANTIA', width: '30%', hidden : false, filterable : false , align: 'center'},
            { text: 'ESTADO', datafield: 'ESTADOG', width: '30%', hidden : false, filterable : false , align: 'center'},
            { text: 'ESTADO', datafield: 'ESTADOGARNUM', width: '0%', hidden : true, filterable : false , align: 'center'}
        ]
    });
    
    $("#jqxgrid_garantias").on("bindingcomplete", function (event) {
    
        refrescarGarantias();
    
    });
    
}


function actualizarBarraHerramientas(){
    
    $("#barra_normal").hide();
    $("#barra_editar").show();
 
}





function setMenuCarpeta(boton1, boton2){
    
    if (boton1==1)
        $("#barra_editar li").eq(1).show();
    else
        $("#barra_editar li").eq(1).hide();
    
    if (boton2==1)
        $("#barra_editar li").eq(2).show();
    else
        $("#barra_editar li").eq(2).hide();
 
}


function process_asignar(){
    //$('.asignar_nota').show();
    //$('.asignar_nota').on('click', function(event){
        event.preventDefault();
        
        var opt_puesto=0;
        var opt_area=0;
        
        opt_area = [4];
        opt_puesto = 6;
        
        $.ajax({
            url : "backend/carpeta/notas/x_getenviar_a1",
            data : {
                puesto_in:opt_puesto,// parametro opcional
                area:opt_area
            },
            dataType : "json",
            type : "post",
            success : function(data1){
                var clase_asignar;
                var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div>';
                if(data1){
                    $.each(data1, function (index, value){
                        clase_asignar = 'link_asignar';
                        if (value.IID!=_USUARIO_SESION_ACTUAL){
                            cadhtml +=  '<div class="' + clase_asignar + ' x_area" data-etapa="'+value.ETAPA+'" data-iid="'+value.ID+'" data-puesto_in="'+value.puesto_in+'"><span>' + value.DENOMINACION;
                            cadhtml += '</span></div>';

                        }
                    });
                }

                $.fancybox({
                    "content": cadhtml,
                    'padding'   :  35,
                    'autoScale' :true,
                    'height' : 900,
                    'scrolling' : 'no',
                    'beforeClose': function() { 
                        if (myfancy==1)
                            regresar_a_listado();
                    }
                });


                $(".x_area").click(function(e1){
                    e1.preventDefault();
                    var tmpfancy = myfancy;
                    myfancy=0;

                    var iid = $(this).data('iid');
                    var apuesto_in = $(this).data('puesto_in');
                    apuesto_in = isNaN(apuesto_in)?'':apuesto_in;


                    $.ajax({
                        url : "backend/carpeta/notas/x_getenviar_a2",
                        data : {
                            id_area:iid,
                            puesto_in:apuesto_in
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                          alert(xhr.status);
                          alert(thrownError);
                        },
                        dataType : "json",
                        type : "post",
                        success : function(datar){

                            var clase_asignar;
                            var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div> <div class="regresar_ar">Regresar</div>';
                            if(datar){
                                $.each(datar, function (index, value){

                                    clase_asignar = 'link_asignar';
                                    if (value.IID!=_USUARIO_SESION_ACTUAL){

                                        cadhtml +=  '<div class="' + clase_asignar + '" data-etapa="'+value.ETAPA+'" data-iid="'+value.IID+'"><span>' + value.NOMBRE + ' ' + value.APELLIDO+ ' ('+ value.AREA+ ' - ' + value.PUESTO+')';
                                        cadhtml += '</span></div>';

                                    }

                                });
                            }

                            $.fancybox({
                                "content": cadhtml,
                                'padding'   :  35,
                                'autoScale' :true,
                                'height' : 900,
                                'scrolling' : 'no',
                                'beforeClose': function() { 
                                    if (myfancy==1)
                                        regresar_a_listado();
                                }
                            });
                            if (tmpfancy==1)
                                myfancy=1;


                            $(".regresar_ar").click(function(e){
                                e.preventDefault();
                                $.fancybox.close();
                                $("#asignar").trigger('click');
                            });

                            $(".link_asignar").click(function(e){

                                e.preventDefault();
                                var iid = $(this).data('iid');
                                var new_etapa_data = $(this).data('etapa');
                                var id_usuario = iid;

                                var observacion;
                                var estado;
                                var descripcion;
                                
                                
                                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                                

                                jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                    if(r==true){
                                        observacion='PASE LIBRE';
                                        descripcion='ENVIO DE CARPETA A OTRO USUARIO (PASE LIBRE)'
                                        estado='1'
                                        
                                        $.ajax({

                                            url : _carpetas.URL + "/x_paselibre",
                                            data : {
                                                OPERACION:_array_obj.ID,
                                                ID_ETAPA_ACTUAL:_array_obj.ID_ETAPA_ACTUAL,
                                                USUARIO:id_usuario, //destino
                                                OBSERVACION:observacion,
                                                DESCRIPCION:descripcion,
                                                ESTADO:estado,
                                                ID_ETAPA_ORIGEN:_array_obj.ID_ETAPA_ACTUAL
                                            },
                                            dataType : "json",
                                            type : "post",
                                            success : function(data){
                                                $.fancybox.close();
                                                $("#jqxgrid").show();
                                                $("#jqxgrid").jqxGrid('updatebounddata');
                                                $("#wpopup").html('');
                                            }
                                        });

                                    }
                                });
                            });

                        }
                    });
                });
                return false;

            }
        });
    //});
}


/* comportamiento de checks en analsisis */
function chk_1(){
    if ($('#chk_legales').attr('checked')) {  
          //desmarcar el otro si ta marcado
          if ($('#chk_legales_nc').attr('checked')) {
            $('#chk_legales_nc').removeAttr('checked');
            $("#chk_legales_nc").prev().css("background-position","0px 0px");
          }
    }
}

function chk_2(){
    if ($('#chk_legales_nc').attr('checked')) {
          //desmarcar el otro
          if ($('#chk_legales').attr('checked')) {  
            $('#chk_legales').removeAttr('checked');
            $("#chk_legales").prev().css("background-position","0px 0px");
          }
    }
}

function chk_3(){
                            
    if ($('#chk_patrimoniales').attr('checked')) {
        if ($('#chk_patrimoniales_nc').attr('checked')) {
          //desmarcar el otro
          $('#chk_patrimoniales_nc').removeAttr('checked');
          $("#chk_patrimoniales_nc").prev().css("background-position","0px 0px");
        }
    }
    
}

function chk_4(){
    if ($('#chk_patrimoniales_nc').attr('checked')) {
        if ($('#chk_patrimoniales').attr('checked')) {
          //desmarcar el otro
          $('#chk_patrimoniales').removeAttr('checked');
          $("#chk_patrimoniales").prev().css("background-position","0px 0px");
        }
    }
    
}

function chk_5(){
    if ($('#chk_tecnicos').attr('checked')) {
        if ($('#chk_tecnicos_nc').attr('checked')) {
          //desmarcar el otro
          $('#chk_tecnicos_nc').removeAttr('checked');
          $("#chk_tecnicos_nc").prev().css("background-position","0px 0px");
        }
    }
}

function chk_6(){
    if ($('#chk_tecnicos_nc').attr('checked')) {
        if ($('#chk_tecnicos').attr('checked')) {  
          //desmarcar el otro
          $('#chk_tecnicos').removeAttr('checked');
          $("#chk_tecnicos").prev().css("background-position","0px 0px");
        }
    }
}

/* comporamiento de checks en analsisis */


function refrescarGarantias(){
    
    //$("#jqxgrid_garantias").jqxGrid('hidecolumn', 'ESTADOGARNUM');
            //actualizar suma
            var griddata = $('#jqxgrid_garantias').jqxGrid('getdatainformation');
            var _arr_aportes_tmp = [];
            for (var i = 0; i < griddata.rowscount; i++)
                _arr_aportes_tmp.push($('#jqxgrid_garantias').jqxGrid('getrenderedrowdata', i));
            
            var total_1=0; // evaluacion
            var total_6=0; // constituida
            var total_5=0; // aprobada
            if (griddata.rowscount==0){
                $("#suma_aporte").html('');
                $(".suma_aportes").hide();
            }else{
                if(_arr_aportes_tmp.length>0){
                    //colocar
                    $.each(_arr_aportes_tmp,function(k,v){
                        if(v.ESTADOGARNUM==1){
                            total_1 = total_1 + parseFloat(v.VALOR_GARANTIA);
                        }else if (v.ESTADOGARNUM==6){
                            total_6 = total_6 + parseFloat(v.VALOR_GARANTIA);
                        }else if (v.ESTADOGARNUM==5){
                            total_5 = total_5 + parseFloat(v.VALOR_GARANTIA);
                        }
                    });
                    $(".suma_aportes").show();
                    $("#suma_aporte_1").html(precise_round(total_1,2));
                    $("#suma_aporte_6").html(precise_round(total_6,2));
                    $("#suma_aporte_5").html(precise_round(total_5,2));
                }
            }
    
}




function initNotas( id_oper ){
    
    id_oper = id_oper || '';
    
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'ID', type: 'string' },
            { name: 'ASUNTO', type: 'string' },
            /*{ name: 'ESTADONR', type: 'string' },*/
            { name: 'DESTINATARIO', type: 'string' },
            { name: 'ID_OPERACION', type: 'string' },
            { name: 'DESTINATARIO_NOMBRE', type: 'string' },
            { name: 'PROPIETARIO_NOMBRE', type: 'string' },
            { name: 'PROPIETARIO', type: 'string' },
            { name: 'FCREA', type: 'string' },
            { name: 'REMITENTE', type: 'string' },
            { name: 'ENVIADOA', type: 'string' }
            
        ],
        url: 'general/extends/extra/carpetas.php',
        data:{
            accion: "getNotas",
            idoper  :   id_oper
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
    
			
    $("#jqxgrid_notas").jqxGrid({
        width: '900px',
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function () {
            //$("#jqxgrid").jqxGrid('hidecolumn', 'REMITENTE');
        },
        showstatusbar: true,
        renderstatusbar: function (statusbar) {
            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
            var responderButton = $("<div style='float: left; margin-left: 5px;width:217px;'><img width=16 style='position: relative; margin-top: 2px;' src='general/css/images/32x32/checked.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Ver</span></div>");
            container.append(responderButton);
            statusbar.append(container);
            responderButton.jqxButton({ theme: theme, width: 120, height: 20 });
            responderButton.click(function(event){
               
                //aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
                
               
                var selectedrowindex = $("#jqxgrid_notas").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_notas").jqxGrid('getdatainformation').rowscount;
                                
                if ( selectedrowindex != '-1' ){
                    if (selectedrowindex<rowscount){
                        
                            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                var id = $("#jqxgrid_notas").jqxGrid('getrowid', selectedrowindex);
                                var datarow = $("#jqxgrid_notas").jqxGrid('getrowdata', id);
                                
                                var idr =  datarow.ID;
                                id_ope_actual = datarow.ID_OPERACION;
                                
                                console.dir(datarow);
                                agregar_nota(idr);
                                
                            }
                        
                    }else{
                        jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){

                        });
                        return false;
                    }
                    
                    
                }else{
                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo),function(){
                    });
                    return false;
                }
            });
        },
        columnsresize: true,
        sortable: true,
        filterable: true,
        showfilterrow: true,
        localization: getLocalization(),
        columns: [
            { text: 'ID', datafield: 'ID', width: '6%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'REMITENTE', datafield: 'REMITENTE', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'ASUNTO', datafield: 'ASUNTO', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'EN CARTERA DE', datafield: 'PROPIETARIO_NOMBRE', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'ENVIADO A', datafield: 'ENVIADOA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'CARPETA VINCULADA', datafield: 'ID_OPERACION', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true }
        ]
    });
    
}



function agregar_nota(idobjeto, ver){
       idobjeto || ( idobjeto = [] );
       ver || ( ver = -1 );
       $.ajax({
            url : "backend/carpeta/notas/x_getform_agregar_requerimiento",
            data : {
                idr:idobjeto
            },
            type : "post",
            success : function(datareq){
                $.fancybox({
                    "content": datareq,
                    'padding'   :  35,
                    'autoScale' :true,
                    'height' : 900,
                    'scrolling' : 'yes',
                    'afterShow': function(){
                        $(".fancybox-inner").css({'overflow-x':'hidden'});
                    }
                });
                
                if (ver!=-1)
                    $(".elempie").html('');
                if (idobjeto>0){//edit
                   //var estado = $("#estadoh").val();
                   //$("#estadoreq").val(estado).attr('disabled', true).trigger("chosen:updated");
                   var destinatarioh = $("#destinatarioh").val();
                    $("#destinatario").val(destinatarioh).trigger("chosen:updated");
                }else{ //add
                    //$("#estadoreq").val(0).attr('disabled', true).trigger("chosen:updated");
                }
                addEventsRequerimientos(idobjeto);
                
                $(".chzn-select").chosen({ disable_search_threshold: 5 });
                $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
                $("input[type=file]").each(function(){
                    if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");}
                });
                
                
                $("#femis").val( $("#femish").val() );
                
                init_datepicker('#femis','-3','+5','0',0);
                
                $(".elempie").html("").hide();
                
                $(".grid_adj_ope").html("").hide();
                $("#fot_car").html("").hide();
                $(".elem_med_cond").eq(4).html("").hide();
                
                //$(".elem_med_cond").eq(4).css("border","1px solid red");
                
                //activar_acordeon('.grid-1');
                
            }
        });
}

var inter = function(){
             
    setInterval(
        function() {
            poll()
        }, 1000 * 10 * 1);
 }
    
//var poll = function(){
 function poll(){
    $.ajax({
       url: _carpetas.URL + "/x_consultar_fechas",
//       url : _carpetas.URL + "/x_getenviar_a1",
        type : "post",
        data : {
        iduser:_USUARIO_SESION_ACTUAL
        },
        timeout: 10000,
        dataType: 'html',
         
        success: function(respuesta){
//            alert(respuesta);
console.log(respuesta);
        }
    });
};
 
inter();