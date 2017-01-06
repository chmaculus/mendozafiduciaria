var _meses = ["Enero", "Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre"];

var _ID_ADMIN;

var getLocalization = function () {

        var localizationobj = {};

        var localizationobj = {
            firstDay:1,
            days: {
                    names: ["domingo","lunes","martes","miércoles","jueves","viernes","sábado"],
                    namesAbbr: ["dom","lun","mar","mié","jue","vie","sáb"],
                    namesShort: ["do","lu","ma","mi","ju","vi","sá"]
            },
            months: {
                    names: ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre",""],
                    namesAbbr: ["ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic",""]
            },
            AM: ["a.m.","a.m.","A.M."],
            PM: ["p.m.","p.m.","P.M."],
            eras: [{"name":"d.C.","start":null,"offset":0}],
            twoDigitYearMax: 2100,
            patterns: {
                d: "dd/MM/yyyy",
                D: "dddd, dd' de 'MMMM' de 'yyyy",
                t: "hh:mm tt",
                T: "hh:mm:ss tt",
                f: "dddd, dd' de 'MMMM' de 'yyyy hh:mm tt",
                F: "dddd, dd' de 'MMMM' de 'yyyy hh:mm:ss tt",
                M: "dd MMMM",
                Y: "MMMM' de 'yyyy"
            },
            percentsymbol: "%",
            currencysymbol: "$",
            currencysymbolposition: "before",
            decimalseparator: '.',
            thousandsseparator: ',',
            pagergotopagestring: "Ir a:",
            pagershowrowsstring: "Mostrar filas:",
            pagerrangestring: " de ",
            pagerpreviousbuttonstring: "Previo",
            pagernextbuttonstring: "Proximo",
            filterselectstring: "Seleccione filtro",
            groupsheaderstring: "Arrastra una columna y sueltala aqui, para agruparla",
            filterselectallstring: "(Todos)",
            sortascendingstring: "Orden Ascendente",
            sortdescendingstring: "Orden Descendente",
            emptydatastring: "No hay datos para mostrar",
            sortremovestring: "Quitar Orden",
            groupbystring: "Agrupar por esta columna",
            groupremovestring: "Quitar de las agrupaciones",
            filterclearstring: "Limpiar",
            filterstring: "Filtrar",
            filtershowrowstring: "Mostrar columnas aqui:",
            filterorconditionstring: "O",
            filterandconditionstring: "Y",
            filterstringcomparisonoperators: ['vacio', 'no vacio', 'contiene', 'contiene(coincidir mayusc y minusc)',
                'no contiene', 'no contiene(coincidir mayusc y minusc)', 'inicia con', 'inicia con(coincidir mayusc y minusc)',
                'termina con', 'termina con(coincidir mayusc y minusc)', 'igual', 'igual(coincidir mayusc y minusc)', 'nulo', 'no nulo'],
            filternumericcomparisonoperators: ['equal', 'not equal', 'less than', 'less than or equal', 'greater than', 'greater than or equal', 'null', 'not null'],
            filterdatecomparisonoperators: ['equal', 'not equal', 'less than', 'less than or equal', 'greater than', 'greater than or equal', 'null', 'not null'],
            validationstring: "El valor introducido no es valido"
        };
        return localizationobj;
    }
    theme = 'energyblue';
    
    function activar_acordeon(namele, abierto){
        abierto || ( abierto = 0 );
        $(namele+' span').each(function() {
            var trigger = $(this), state = false, el = trigger.parent().next('.content-gird');
            trigger.click(function(){
                    state = !state;
                    el.slideToggle();
                    trigger.parent().parent().toggleClass('inactive');
            });
        });
        if (abierto==0)
            $(namele+' span').trigger('click');
        
    }

$(document).ready(function(){
    
    //x_get_carpetas_pendientes_cont". 
    if (_this_app.URL!='main'){
        setInterval(function(){
            actualizaNotif();
//            inter();
        },30000);
    }
        
    if ( _this_app.URL!='main' && _this_app.URL!='backend/cambiarpassword' )
        $("#jMenu").jMenu()
   
    /*
    
    jQuery.fn.initMenu = function() {  
        return this.each(function(){
            var theMenu = $(this).get(0);
            $('.acitem', this).hide();
            $('li.expand > .acitem', this).show();
            $('li.expand > .acitem', this).prev().addClass('active');
            $('li a', this).click(
                function(e) {
                    e.stopImmediatePropagation();
                    var theElement = $(this).next();
                    var parent = this.parentNode.parentNode;
                    if($(parent).hasClass('noaccordion')) {
                        if(theElement[0] === undefined) {
                            window.location.href = this.href;
                        }
                        $(theElement).slideToggle('normal', function() {
                            if ($(this).is(':visible')) {
                                $(this).prev().addClass('active');
                            }
                            else {
                                $(this).prev().removeClass('active');
                            }    
                        });
                        return false;
                    }
                    else {
                        if(theElement.hasClass('acitem') && theElement.is(':visible')) {
                            if($(parent).hasClass('collapsible')) {
                                $('.acitem:visible', parent).first().slideUp('normal', 
                                function() {
                                    $(this).prev().removeClass('active');
                                }
                            );
                            return false;  
                        }
                        return false;
                    }
                    if(theElement.hasClass('acitem') && !theElement.is(':visible')) {         
                        $('.acitem:visible', parent).first().slideUp('normal', function() {
                            $(this).prev().removeClass('active');
                        });
                        theElement.slideDown('normal', function() {
                            $(this).prev().addClass('active');
                        });
                        return false;
                    }
                }
            }
        );
    });
    };
    $( "#sideLeft .menu li .acitem" ).each(function( index ) {
        if($(this).find("span").hasClass('activemenu')){
            $(this).parent().addClass('expand');
        }
    });      
    $('.menu').initMenu();

    */
    
    
    $(document).ajaxStop($.unblockUI);
    
    
    $(".info-footer #left").click(function(e){
        e.preventDefault();
        url = "http://www.focasoftware.com/";
        window.open(url, '_blank');
        return false;
    });
    
    $('#login-trigger').click(function(){
        $(this).next('#login-content').slideToggle();
        $(this).toggleClass('active');					
					
    });


    $("#header_settings").click(function(e){
       e.preventDefault(); 
       
       cambiarpassword();
					
    });
                                        
    $("input[type=file]").change(function(){$(this).parents(".uploader").find(".filename").val($(this).val());});
    $("input[type=file]").each(function(){
        if($(this).val()==""){$(this).parents(".uploader").find(".filename").val("Selecciona un Archivo...");}
    });
    
    ev_hide_it();
    

    $('.notif').click(function(){
        notifMain();
    });
    if (_this_app.URL!='main'){
        actualizaNotif();
    }
    
});


function notifMain( iid ){
    iid || ( iid = '-1' );
    
    var url_n = 'backend/notificaciones';
    if (iid>0)
        url_n = 'backend/notificaciones/init/' + iid;
    $.ajax({
        url : url_n,
        type : "post",
        data : {},
        async:false,
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
 
            $('.link_aceptar.tb_leida').click(function(){
            var id_operacion_sem = $(this).data('iid');
            var etapa = $(this).data('etapa');
            var meobj = $(this);
//            var etapa = $(this).data('etapa');
//            var idope = $(this).data('idope');
                    var obst = 'AVISADO';
                    var dest = 'SE NOTIFICO SOBRE LA DEMORA AL USUARIO ';
//                    registrar ingreso en traza
                    var obj = {
                        ID_OPERACION:id_operacion_sem,
                        ESTADO:'3', //aceptado
                        //CARTERADE:carterade, //setear en php
                        DESTINO:'',
                        OBSERVACION: obst,
                        DESCRIPCION: dest,
                        ETAPA:etapa,
                        ETAPA_REAL:etapa
                    }
                    $.ajax({
                        url : 'backend/notificaciones/x_actualizar_traza_sem',
                        type : "post",
                        data : {
                            obj:obj
                        },
                        async:false,
                        success : function(){
                             actualizaNotif();
                            regresar_a_listado();
                            jAlert('Carpeta vista.', 'Carpetas',function(){
                                refrescarDomNotif(meobj);
                            });
                        }
                    });

            });

            $('.link_aceptar_carpeta').click(function(){


            });

            $('.link_aceptar.tb_si').click(function(){
                var id_operacion = $(this).data('iid');
                var rech = $(this).data('rech');
                var notif = $(this).data('notif');
                var autor = $(this).data('autor');
                var noauto = $(this).data('noauto');//no autorizada
                var autor_req = $(this).data('autor_req');
                var etapa = $(this).data('etapa');
                var notifnota = $(this).data('notifnota');
                var para_aux1 = $(this).data('para_aux1');
                var meobj = $(this);

                //insertar otro registro igual
                /*
                if ( (noauto && noauto==1) ){
                    var idt = id_operacion;
                    //cambiar estado de leido
                    $.ajax({
                        url : 'backend/notificaciones/x_cambiar_leido_traza',
                        type : "post",
                        data : {
                            idt:idt
                        },
                        async:false,
                        success : function(data){
                            actualizaNotif();
                            regresar_a_listado();
                            $.fancybox.close();
                        }
                    });
                }else 
                */
                var idt;
                if ( (notifnota && notifnota==1) ){
                    id_nota = id_operacion;
                    var tid = $(this).data('tid');

                    observacion='NOTA ACEPTADA';
                    descripcion='LA NOTA FUE ACEPTADA'
                    var uactual = $(this).data('actual');

                    $.ajax({
                        url : "backend/carpeta/notas/x_guardar_traza_nota",
                        data : {
                            id_req_nota:id_nota,
                            destinatario:0,
                            observacion:observacion,
                            descripcion:descripcion,
                            tid:tid, //traza id,
                            PROPIETARIO:uactual
                        },
                        dataType : "json",
                        type : "post",
                        success : function(data){
                            actualizaNotif();
                            regresar_a_listado();
                            //$.fancybox.close();
                            refrescarDomNotif(meobj);
                        }
                    });

                }else if ( (autor_req && autor_req==1) ){
                    idt = id_operacion;
                    //cambiar estado de leido
                    $.ajax({
                        url : 'backend/notificaciones/x_traza_autor',
                        type : "post",
                        data : {
                            idt:idt,
                            respuesta:1
                        },
                        async:false,
                        success : function(data){
                            actualizaNotif();
                            regresar_a_listado();
                            refrescarDomNotif(meobj);
                        }
                    });

                }else if ( (autor && autor==1) ){
                    var par_su2 = 0;
                    if (iid>0){
                        par_su2 = 1
                    }
                    idt = id_operacion;
                    //cambiar estado de leido
                    $.ajax({
                        url : 'backend/notificaciones/x_traza_autor',
                        type : "post",
                        data : {
                            idt:idt,
                            para_aux1:para_aux1,
                            par_su2:par_su2
                        },
                        async:false,
                        success : function(data){
                            actualizaNotif();
                            regresar_a_listado();
                            refrescarDomNotif(meobj);
                        }
                    });
                }else if ( (notif && notif===2) ){
                    idt = id_operacion;
                    //cambiar estado de leido
                    var obj_arr = {
                        'LEIDO':0,
                        'ESTADO': "3" //ESTADO=3, PARA CONFIRMADO, ESTADO=4 PARA RECHAZADO
                    }
                    $.ajax({
                        url : 'backend/notificaciones/x_cambiar_leido_traza',
                        type : "post",
                        data : {
                            idt:idt,
                            obj_arr:obj_arr
                        },
                        async:false,
                        success : function(){
                            actualizaNotif();
                            regresar_a_listado();
                            refrescarDomNotif(meobj);
                        }
                    });

                }else if ( (rech && rech==1) || (notif && notif==1) || (noauto && noauto==1) ){
                    idt = id_operacion;
                    //cambiar estado de leido
                    $.ajax({
                        url : 'backend/notificaciones/x_cambiar_leido_traza',
                        type : "post",
                        data : {
                            idt:idt
                        },
                        async:false,
                        success : function(){
                            actualizaNotif();
                            regresar_a_listado();
                            refrescarDomNotif(meobj);
                        }
                    });

                }else{
                    var obst = 'ACEPTADO';
                    var dest = 'SE ACEPTO LA CARPETA';
                    
                    if (iid>0){
                        obst = 'ACEPTADO CON SU';
                        dest = 'SE ACEPTO LA CARPETA CON SU';
                    }
    
                    //registrar ingreso en traza
                    var obj = {
                        ID_OPERACION:id_operacion,
                        ESTADO:'3', //aceptado
                        //CARTERADE:carterade, //setear en php
                        DESTINO:'',
                        OBSERVACION: obst,
                        DESCRIPCION: dest,
                        ETAPA:etapa,
                        ETAPA_REAL:etapa,
                        SEM:0
                    }
                    $.ajax({
                        url : 'backend/notificaciones/x_send_traza',
                        type : "post",
                        data : {
                            obj:obj
                        },
                        async:false,
                        success : function(){
                            actualizaNotif();
                            regresar_a_listado();
                            jAlert('Carpeta Aceptada.', 'Carpetas',function(){
                                refrescarDomNotif(meobj);
                            });
                        }
                    });
                }
            });


            $('.link_aceptar.tb_no').click(function(){
                var idope;
                var autor = $(this).data('autor');
                var autor_req = $(this).data('autor_req');
                var id_operacion = $(this).data('iid');
                var tid = $(this).data('tid');
                var notif2 = $(this).data('notif'); // para copia de contrato legales
                var idnr = $(this).data('iid_nr');
                
                var meobj = $(this);
                if ( (autor_req && autor_req==1) ){
                    idt = id_operacion;

                    //cambiar estado de leido
                    $.ajax({
                        url : 'backend/notificaciones/x_traza_autor',
                        type : "post",
                        data : {
                            idt:idt,
                            respuesta:0
                        },
                        async:false,
                        success : function(data){
                            actualizaNotif();
                            regresar_a_listado();
                            refrescarDomNotif(meobj);
                        }
                    });

                }else if ( (autor && autor===1) ){
                    idope = $(this).data('idope');
                    jConfirm('Esta seguro de rechazar esta autorización??.', 'Carpetas',function(r){
                        if(r==true){
                            $.ajax({
                                url : 'backend/carpeta/carpetas/x_cancelar_autorizacion',
                                type : "post",
                                data : {
                                    idope:idope
                                },
                                async:false,
                                success : function(data){
                                    jAlert('Autorización Rechazada.', 'Carpetas',function(){
                                        actualizaNotif();
                                        regresar_a_listado();
                                        refrescarDomNotif(meobj);
                                    });
                                }
                            });
                        }
                    });
                }else if ( (notif2 && notif2===2) ){
                    idt = id_operacion;
                    //cambiar estado de leido
                    
                    jConfirm('Esta seguro de rechazar esta confirmación??.', 'Carpetas',function(r){
                        if(r===true){
                            var obj_arr = {
                                'LEIDO':0,
                                'ESTADO': "4" //ESTADO=3, PARA CONFIRMADO, ESTADO=4 PARA RECHAZADO
                            }
                            $.ajax({
                                url : 'backend/notificaciones/x_cambiar_leido_traza',
                                type : "post",
                                data : {
                                    idt:tid,
                                    obj_arr:obj_arr
                                },
                                async:false,
                                success : function(){
                                    actualizaNotif();
                                    regresar_a_listado();
                                    refrescarDomNotif(meobj);
                                }
                            });
                        }
                    });
                } else if ( (idnr && idnr>0) ){
                var idNotificacion = $('#idNoti').text();
                    jConfirm('Esta seguro de rechazar esta Nota??.', 'Carpetas',function(r){
                        if(r==true){
           var metodo = "<div style='width:350px; height:200px;'>\n\
            <h2 style='font-size: 18px;margin-left: 3px;font-weight: bold; width:300px; '>Ingrese el motivo de rechazo</h2>\n\
            <input id='inputmotivo' style='width:300px; height:50px; margin-top:25px'/>\n\
            <input id='traermotivo' type='button' style='width:300px; padding:20px; margin-top:25px' value='Guardar'/> </div>";
                $.fancybox({
                "content": metodo,
                'padding'   :  40,
                'autoScale' :true,
                'scrolling' : 'no',
                close  : [27], // escape key
//                'beforeClose': function() {
//                    location.reload();
//                }
            });
               
            $("#traermotivo").on('click',function(){
                var contMotivo = $('#inputmotivo').val();
                $.ajax({
                                url : 'backend/carpeta/carpetas/x_cancelar_nota',
                                type : "post",
                                data : {
                                    idnr:idnr,
                                    motivotext: contMotivo,
                                },
                                async:false,
                                success : function(data){
                                    cargarMotivo(contMotivo,idNotificacion);
                                    jAlert('Nota Rechazada.', 'Carpetas',function(){
                                        actualizaNotif();
                                        regresar_a_listado();
                                        refrescarDomNotif(meobj);
                                    });
                                }
                            });
            })
                        }
                    });
                    
                }else{
                    idope = $(this).data('iid');
                    jConfirm('Esta seguro de rechazar esta carpeta??.', 'Carpetas',function(r){
                        if(r==true){
                            $.ajax({
                                url : 'backend/carpeta/carpetas/x_cancelar_solicitud',
                                type : "post",
                                data : {
                                    idope:idope
                                },
                                async:false,
                                success : function(data){
                                    jAlert('Carpeta Rechazada.', 'Carpetas',function(){
                                        actualizaNotif();
                                        regresar_a_listado();
                                        refrescarDomNotif(meobj);
                                    });
                                }
                            });
                        }
                    });
                }
            });



        }
    });
}


function actualizaNotif(p_auto) {
    p_auto || (p_auto = '0');
    $.ajax({
        url: 'backend/notificaciones/x_get_carpetas_pendientes_cont',
        type: "post",
        async: false,
        success: function (data) {
//         
//            $.ajax({
//                url: 'backend/carpeta/carpetas/x_consultar_fechas',
//                type: "post",
//                data: {
//                    iduser: 56
//                },
////                timeout: 10000,
////                dataType: 'html',
//                success: function () {
////                    alert("respuesta");
////                    console.log(respuesta);
//                }
//            });
//            

            if (data > 0) {
                $(".notif").html('(' + data + ')');
                $(".notif").attr("data-notificaciones", data);
                if (p_auto == 1) {
                    $.fancybox.close();
                    $(".notif").trigger('click');
                }
            }
            else if (data == -1) {
                $(".notif").html('');
            }
        }
    });
}

function cargarMotivo(contMotivo, idNotificacion){
    $.ajax({
        url : 'backend/notificaciones/x_cargar_motivo_rechazo',
        type : "post",
        data : {
        contMotivo: contMotivo,
        idNot: idNotificacion
                        },
        async:false,
        success : function(data){
//lo siguiente no lo saque de la copia del ajax, capas q no sirve
            if(data>0){
                $(".notif").html('('+data+')');
                $(".notif").attr("data-notificaciones",data);
                if (p_auto==1){
                    $.fancybox.close();
                    $(".notif").trigger('click');
                }
            }
            else if(data==-1){
                $(".notif").html('');
            }
        }
    });
}

function regresar_a_listado(){
    if($("#jqxgrid").length>0){
        $("#jqxgrid").show();
        $("#jqxgrid").jqxGrid('updatebounddata');
        $("#wpopup").html('');
        switchBarra();
    }else{
    }
    
}


function switchBarra(){
        $('#barra_editar').hide();
        $('#barra_normal').show();
}

function switchBarra_back(){
    //preguntar que barra ta visible
    if ($('#barra_editar').is (':visible')){
        $('#barra_editar').hide();
        $('#barra_normal').show();
    }else{
        $('#barra_editar').show();
        $('#barra_normal').hide();
    }
}

function dec(t){
	
    var t = parseFloat(t);
    var t = Math.round(t*100)/100;
    var t = t.toFixed(2);
    return t;
	
}

function addZeros(n) {
  return (n < 10)? '00' + n : (n < 100)? '0' + n : '' + n;
}

function RefreshTable(tableId, urlData){

  $.getJSON(urlData, null, function( json ){
    table = $(tableId).dataTable();
    oSettings = table.fnSettings();
    table.fnClearTable(this);
    for (var i=0; i<json.aaData.length; i++)
    {
      table.oApi._fnAddData(oSettings, json.aaData[i]);
    }
    oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
    table.fnDraw();
  });
}


function init_datepicker(div,range1,range2,shownow,haciadelante){
    range1  || ( range1 = '-5' );
    range2  || ( range2 = '+0' );
    shownow || ( shownow = '1' );
    // 0 no asigna valor inicial, 1 asigna la fecha actual, 2 asigna el primer dia del mes
    haciadelante || ( haciadelante = '0' );
        
    var d = new Date();
    month = d.getMonth()+1;
    y = d.getFullYear();
    day = d.getDate();
    d = (day<10 ? '0' : '') + day
    n = (month<10 ? '0' : '') + month
    f1 = '01-'+n + '-' +y;
    f2 = d +'-' + n + '-' +y;
    
    if (haciadelante==1)
        settings = {
            minDate: 0,
            changeMonth: true,
            changeYear: true,
            monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
            monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
            dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
            dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
            yearRange: range1+':'+range2, 
            beforeShow: function (input, inst) {
/*
                var offset = $(input).offset();
                var height = $(input).height();
                window.setTimeout(function () {
                    inst.dpDiv.css({ top: (offset.top + height - 25) + 'px', left: offset.left + 'px' })
                }, 1);
                */
            }
        }
    else
        settings = {
            changeMonth: true,
            changeYear: true,
            monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
            monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
            dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ],
            dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
            yearRange: range1+':'+range2, 
            beforeShow: function (input, inst) {
            /*
                var offset = $(input).offset();
                var height = $(input).height();
                window.setTimeout(function () {
                    inst.dpDiv.css({ top: (offset.top + height - 25) + 'px', left: offset.left + 'px' })
                }, 1);
                */
            }
        }
                
    $(div).datepicker(settings);
    $( div ).datepicker( "option", "dateFormat", 'dd/mm/yy' );
    //$( div ).attr('readonly','readonly'); 
    if (shownow==1)
        $( div ).val(f2);
    else if (shownow==2)
        $( div ).val(f1);
}




function add_event(id_grid,url,campo1){
    $("#addrowbutton").jqxButton({ theme: theme });
    $("#addrowbutton").on('click', function () {
        $.ajax({
            url : url,
            type : "post",
            dataType: 'json',
            success : function(data){
                var commit = $("#"+id_grid).jqxGrid('addrow', null, data);
                $('#'+id_grid).jqxGrid('selectrow', data.uid);
                var selectedrowindex = $("#"+id_grid).jqxGrid('getselectedrowindex');
                $('#'+id_grid).jqxGrid({ editable: true}); 
                var editable = $("#"+id_grid).jqxGrid('begincelledit', selectedrowindex, campo1);
            }
        });
    });
}

function delete_event(id_grid,etiqueta,arr_confirma){
    arr_confirma || ( arr_confirma = [] );
    
    $("#deleterowbutton").jqxButton({ theme: theme });
    $("#deleterowbutton").on('click', function () {
        var selectedrowindex = $("#"+id_grid).jqxGrid('getselectedrowindex');
        if (selectedrowindex==-1){
            jAlert('Seleccione un item.', etiqueta,function(){
            });
        }
        else{
            mydata = $('#'+id_grid).jqxGrid('getrowdata', selectedrowindex);
            var rowscount = $("#"+id_grid).jqxGrid('getdatainformation').rowscount;
            if (selectedrowindex >= 0 && selectedrowindex < rowscount){
                var id = $("#"+id_grid).jqxGrid('getrowid', selectedrowindex);
            }
            
            if(arr_confirma.url){
                $.ajax({
                    url : arr_confirma.url,
                    type : "post",
                    data : {
                        tabla:arr_confirma.tabla,
                        campo:arr_confirma.campo,
                        valor:mydata.ID
                    },
                    async:false,
                    type : "post",
                    success : function(data){
                        if (data>0){
                            jAlert( 'Este Elemento tiene dependencias.', etiqueta );
                            return false;
                        }else{
                            jConfirm('Esta seguro de borrar este item??.', etiqueta,function(r){
                                if(r==true){
                                    if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                        var commit = $("#"+id_grid).jqxGrid('deleterow', id);
                                    }
                                }
                            });
                        }
                    }
                });
            }else{
                jConfirm('Esta seguro de borrar este item??.', etiqueta,function(r){
                    if(r==true){
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var commit = $("#"+id_grid).jqxGrid('deleterow', id);
                        }
                    }
                });
            }
        }
    });
    
}

function edit_event(id_grid,campo1){
    $("#editrowbutton").jqxButton({ theme: theme });
    $("#editrowbutton").on('click', function () {
        var selectedrowindex = $("#"+id_grid).jqxGrid('getselectedrowindex');
        $('#'+id_grid).jqxGrid({ editable: true}); 
        var editable = $("#"+id_grid).jqxGrid('begincelledit', selectedrowindex, campo1);
    });
    $("#"+id_grid).on('cellbeginedit', function (event) 
    {
       $('#'+id_grid).jqxGrid({ editable: false}); 
       $('#'+id_grid).jqxGrid({ editable: true}); 
    });

    $("#"+id_grid).on('cellendedit', function (event) 
    {
       $('#'+id_grid).jqxGrid({ editable: false}); 
    });
    
}

function process_data(url, obj){
    $.ajax({
        url : url,
        data : {
            obj:obj
        },
        type : "post"
    });
}

function fGetNumUnico(){
	var Dia = new Date();
	var d = Dia.getDay();
	var n = Dia.getMonth();
	var a = Dia.getFullYear();
	var m = Dia.getMinutes();
	var h = Dia.getHours();
	var s = Dia.getSeconds();
	var Num = "" + a + n + d + h + m + s;
	return parseInt(Num);
}


function getDatejs(){
    var d = new Date();
    var month = d.getMonth()+1;
    var day = d.getDate();
    var output = 
        ((''+day).length<2 ? '0' : '') + day +'/' +
        ((''+month).length<2 ? '0' : '') + month + '/' +
        d.getFullYear() + '/';

        ;
    return output;
    
}

function precise_round(num,decimals){
    return Math.round(num*Math.pow(10,decimals))/Math.pow(10,decimals);
}


/* chk */
function push_chk(){
    element = this.nextSibling;
    if(element.checked == true && element.type == "checkbox") {
            this.style.backgroundPosition = "0 -" + checkboxHeight*3 + "px";
    } else if(element.checked == true && element.type == "radio") {
            this.style.backgroundPosition = "0 -" + radioHeight*3 + "px";
    } else if(element.checked != true && element.type == "checkbox") {
            this.style.backgroundPosition = "0 -" + checkboxHeight + "px";
    } else {
            this.style.backgroundPosition = "0 -" + radioHeight + "px";
    }
    
}

function check_chk(){
    element = this.nextSibling;
    //element.disabled
    if (element.disabled==true)
        return false;
    
    if(element.checked == true && element.type == "checkbox") {
            this.style.backgroundPosition = "0 0";
            element.checked = false;
    } else {
            if(element.type == "checkbox") {
                    this.style.backgroundPosition = "0 -" + checkboxHeight*2 + "px";
            } else {
                    this.style.backgroundPosition = "0 -" + radioHeight*2 + "px";
                    group = this.nextSibling.name;
                    inputs = document.getElementsByTagName("input");
                    for(a = 0; a < inputs.length; a++) {
                            if(inputs[a].name == group && inputs[a] != this.nextSibling) {
                                    inputs[a].previousSibling.style.backgroundPosition = "0 0";
                            }
                    }
            }
            element.checked = true;
    }
    
    
}

function change_chk(){
    
    inputs = document.getElementsByTagName("input");
    for(var b = 0; b < inputs.length; b++) {
            if(inputs[b].type == "checkbox" && inputs[b].checked == true && inputs[b].className == "styled") {
                    inputs[b].previousSibling.style.backgroundPosition = "0 -" + checkboxHeight*2 + "px";
            } else if(inputs[b].type == "checkbox" && inputs[b].className == "styled") {
                    inputs[b].previousSibling.style.backgroundPosition = "0 0";
            } else if(inputs[b].type == "radio" && inputs[b].checked == true && inputs[b].className == "styled") {
                    inputs[b].previousSibling.style.backgroundPosition = "0 -" + radioHeight*2 + "px";
            } else if(inputs[b].type == "radio" && inputs[b].className == "styled") {
                    inputs[b].previousSibling.style.backgroundPosition = "0 0";
            }
    }
}

function init_chk(){
    var inputs = document.getElementsByTagName("input"), span = Array(), textnode, option, active;
    for(a = 0; a < inputs.length; a++) {
            if((inputs[a].type == "checkbox" || inputs[a].type == "radio") && inputs[a].className == "styled") {
                    span[a] = document.createElement("span");
                    span[a].className = inputs[a].type;

                    if(inputs[a].checked == true) {
                            if(inputs[a].type == "checkbox") {
                                    position = "0 -" + (checkboxHeight*2) + "px";
                                    span[a].style.backgroundPosition = position;
                            } else {
                                    position = "0 -" + (radioHeight*2) + "px";
                                    span[a].style.backgroundPosition = position;
                            }
                    }
                    inputs[a].parentNode.insertBefore(span[a], inputs[a]);
                    inputs[a].onchange = change_chk;
                    if(!inputs[a].getAttribute("disabled")) {
                            //span[a].onmousedown = Custom.pushed;
                            span[a].onmouseup = check_chk;
                    } else {
                            span[a].className = span[a].className += " disabled";
                    }
            }
    }
    
}
/* chk */


function ev_hide_it(){
    $(".hideit").click(function() {
            $(this).fadeOut(500);
    });
}



function post_upload_req(nombre,nombre_tmp, etapa, etiqueta){
    
    jAlert('Archivo cargado correctamente. ' + nombre, $.ucwords(_etiqueta_modulo),function(){
        //agregarlo a la lista
        
        //var num_li = $(".lista_reqs_adj li").length
        
        $(".lista_reqs_adj").append('<li data-nom="'+nombre+'" data-tmp="'+nombre_tmp+'">'+etiqueta+'</li>');
        $("#req_etiqueta").val('').select(); 
        
        $('#upload_file2').each (function(){
            this.reset();
        });
        $("#upload_file2 input[type=file]").each(function(){
            $(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");
        });
    });
    
}

function error_post_upload(nombre){
    jAlert('El archivo ' + nombre + ' ya existe en el servidor.', $.ucwords(_etiqueta_modulo),function(){
         //agregarlo a la lista
         //$.fancybox.close();
    });
}

function refrescarDomNotif(c){
    c.parent().remove();
    var haydivs = $('.notif_titulo').next().find('div');
    if (haydivs.length<=0){
        $.fancybox.close();
    }
}

function validarFecha(field, rules, i, options) {
                            
    var value = field.val();
    value = value.replace(/\_/g,'');
    
    var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
    var fechaCompleta = value.match(datePat);
    if (fechaCompleta == null) {
            return "Fecha no válida";
    }

    dia = fechaCompleta[1];
    mes = fechaCompleta[3];
    anio = fechaCompleta[5];

    if (dia < 1 || dia > 31) {
            return "El valor del d&iacute;a debe estar comprendido entre 1 y 31.";
    }
    if (mes < 1 || mes > 12) { 
            return "El valor del mes debe estar comprendido entre 1 y 12.";
    }
    if ((mes==4 || mes==6 || mes==9 || mes==11) && dia==31) {
            return "El mes "+mes+" no tiene 31 días!";
    }
    if (mes == 2) { // bisiesto
            var bisiesto = (anio % 4 == 0 && (anio % 100 != 0 || anio % 400 == 0));
            if (dia > 29 || (dia==29 && !bisiesto)) {
                    return "Febrero del " + anio + " no contiene " + dia + " dias.";
            }
    }
}



function formattedDate(date) {
    if (date == null)
        return '';
    
    var arr = date.split("/");
    dia = arr[0];
    mes = arr[1];
    ano = arr[2];
    var d1 = [ano, mes, dia].join('/');
    var d = new Date(d1);
    month = '' + (d.getMonth() + 1),
    day = '' + d.getDate(),
    year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [day, month, year].join('/');
}



function formattedDate_ui(date) {
    if(date=='') return '';
    var arr = date.split("/");
    dia = arr[0];
    mes = arr[1];
    ano = arr[2];
    var d1 = [ano, mes, dia].join('/');
    var d = new Date(d1);
    month = '' + (d.getMonth() + 1),
    day = '' + d.getDate(),
    year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [year, month, day].join('');
}

function validarFechafunc( fecha ) {
                            
    var value = fecha;
    
    value = value.replace(/\_/g,'');
    
    var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
    var fechaCompleta = value.match(datePat);
    if (fechaCompleta == null) {
            return "Fecha no válida";
    }

    dia = fechaCompleta[1];
    mes = fechaCompleta[3];
    anio = fechaCompleta[5];

    if (dia < 1 || dia > 31) {
            return "El valor del d&iacute;a debe estar comprendido entre 1 y 31.";
    }
    if (mes < 1 || mes > 12) { 
            return "El valor del mes debe estar comprendido entre 1 y 12.";
    }
    if ((mes==4 || mes==6 || mes==9 || mes==11) && dia==31) {
            return "El mes "+mes+" no tiene 31 días!";
    }
    if (mes == 2) { // bisiesto
            var bisiesto = (anio % 4 == 0 && (anio % 100 != 0 || anio % 400 == 0));
            if (dia > 29 || (dia==29 && !bisiesto)) {
                    return "Febrero del " + anio + " no contiene " + dia + " dias.";
            }
    }
    return 1;
}

function refresGridevent(){
    console.log('aaaaa');
    $(".refresgrid").click(function(e){
        console.log('bbbbbb');
        var namegrid = $(this).data("gridname");
        $('#'+namegrid).jqxGrid('render');
    });
}


function refresListevent(){
    $(".refresgrid").click(function(e){
        var namegrid = $(this).data("gridname");
        $('#'+namegrid).jqxListBox('refresh');
    });
}


function cambiarpassword( iid ){
    
    iid || ( iid = '-1' );
    
    //$('#login-trigger').click();
    
    var url_n = 'backend/cambiarpassword';
    $.ajax({
        url : url_n,
        type : "post",
        data : {

        },
        async:false,
        type : "post",
        success : function(data){
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
                'padding'   :  20,
                'autoScale' :true,
                'scrolling' : 'no',
                'beforeClose': function() {
                    
                    location.reload();
                }
            });

        }
    });
}


function isnumeroCiu(cadena){
      if (cadena.match(/^[a-zA-Z][0-9]+$/))
        return true;
      else
        return false;
}

function isnumeroCiuIns(cadena){
      if (cadena.match(/^[a-zA-Z][-][0-9]+$/))
        return true;
      else
        return false;
}
