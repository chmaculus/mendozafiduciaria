var mydata;
var id_edit;
var working = false;
var _array_entidades = {};
var _array_chk = {};
var semmilla;
var id_ope_actual;
var myfancy = 0;

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
    
    
    $('.tb_todas').on('click', function(e){
        e.preventDefault();
        initGrid();
    });

    $('.tb_miscar').on('click', function(e){
        e.preventDefault();
        initGrid(_USUARIO_SESION_ACTUAL);
    });
        
        
    $(".toolbar li:not(.sub)").click(function(e){
        e.preventDefault();
        var top = $(this).data('top');
        var obj = [];
        
        
        /*
        $('.tb_miscar').on('click', function(e){
            e.preventDefault();
            init_grid(_USUARIO_SESION_ACTUAL);
        });
        */
        
        if(top =='vin'){
            var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
            mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
            
            

            if(mydata){
                if (mydata.PROPIETARIO!=_USUARIO_SESION_ACTUAL){
                    jAlert('Usted no es propietario de esta Nota.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                }else if(mydata.ID_OPERACION>0){
                    jAlert('Esta Nota ya tiene una carpeta vinculada.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                }else{
                    //procesooo
                    process_vincular(mydata.ID,mydata.FOJAS);
                }
            }else{
                jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                    $.unblockUI();
                });
                return false;
            }
            
        }else if(top =='add'){
           if (_permiso_alta==0){
               
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
           agregar_nota(  );
       }else if (top=='asi'){
            var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
            mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
            
                        
            if(mydata){
                if (mydata.PROPIETARIO!=_USUARIO_SESION_ACTUAL){
                    jAlert('Usted no es propietario de esta Nota.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                }else if(mydata.ID_OPERACION>0){
                    jAlert('Esta Nota ya tiene una carpeta vinculada.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                }else if(mydata.ENVIADOAID>0){
                    jAlert('Esta Nota ya fue enviada.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                }else{
                    process_asignar();
                }
            }else{
                jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                    $.unblockUI();
                });
                return false;
            }
       }else if (top=='edi'){
           
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
            
            var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
            mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
            
            if(mydata){
                if (mydata.PROPIETARIO!=_USUARIO_SESION_ACTUAL){
                    jAlert('Usted no es propietario de esta Nota.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                }else{
                    agregar_nota( mydata.ID, ver );
                }
                
            }else{
                jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                    $.unblockUI();
                });
                return false;
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
            
            if(mydata){
                jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                if(r==true){
                    var rowscount = $("#jqxgrid").jqxGrid('getdatainformation').rowscount;
                    $.ajax({
                        url : _notas.URL + "/x_delobj",
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
            
            }else{
                jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                    $.unblockUI();
                });
                return false;
            }
        }

    });    
    
    initGrid();
    
});


function initGrid(id_usuario){
    
    id_usuario = id_usuario || '';
    
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
            { name: 'ENVIADOA', type: 'string' },
            { name: 'FOJAS', type: 'string' },
            { name: 'ENVIADOAID', type: 'string' }

        ],
        url: 'general/extends/extra/carpetas.php',
        data:{
            accion: "getNotas",
            iduser  :   id_usuario
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
    
			
    $("#jqxgrid").jqxGrid({
        width: '98%',
        groupable:true,
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid").jqxGrid('hidecolumn', 'FOJAS');
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
                    if (me.timer) clearTimeout(me.timer);
                    me.timer = setTimeout(function () {
                        dataAdapterope.dataBind();
                    }, 300);
            });
        },
        columns: [
            
            { text: 'ID', datafield: 'ID', width: '6%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'REMITENTE', datafield: 'REMITENTE', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'ASUNTO', datafield: 'ASUNTO', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'EN CARTERA DE', datafield: 'PROPIETARIO_NOMBRE', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'ENVIADO A', datafield: 'ENVIADOA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'CARPETA VINCULADA', datafield: 'ID_OPERACION', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'FOJAS', datafield: 'FOJAS', width: '0%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with' }
        ]
    });
    
    
}



function addEventsRequerimientos(idnr){
    idnr || ( idnr = '0' );
    
    $("#btnSubirfile").click(function(e){
        
        if ($("#req_etiqueta").val()==''){
                e.preventDefault();
                jAlert('Ingrese una etiqueta, por favor.', $.ucwords(_etiqueta_modulo),function(){
                    $("#req_etiqueta").select();
                });
        }
        
    });

    if (idnr>0){
        $(".lista_reqs_adj a").click(function(e){
            e.preventDefault();

            var nom = $(this).prev().data('nom');
            var yo = $(this);
            var el = $(this).prev();

            $.ajax({
                url : _notas.URL + "/x_delupload_nota",
                data : {
                    idnotareq:idnr,
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

    $(".send_nota").on('click', function(e){
        e.preventDefault();



        // edit/new
        var idreqh = $("#idreqh").val();
        var req_asu = $("#req_asunto").val();
        var req_des = $("#req_descripcion").val();
        var req_remitente = $("#req_remitente").val();
        var destinatario = $("#destinatario").val();
        
        //var req_femis = $("#femis").val();
        
        //var estado = 0;
               
        /*
        // si rol es jefe de op, estado = 2
        if (_USER_ROL==10){
            estado = 2;
            autor_req=0;
        }
        
        // si rol es mesa entrada, estado = 3
        if (_USER_ROL==9){
            estado = 3;
            autor_req=0;
        }
        */
        var propietario=0;
        if (idreqh==''){
            propietario = _USUARIO_SESION_ACTUAL;
        }
        
        //adjuntos
        var _array_uploads_adj = [];
        $( ".lista_reqs_adj li" ).each(function(index){
            var nombre = $(this).data('nom');
            var nombre_tmp = $(this).data('tmp');
            _array_uploads_adj.push({nombre:nombre,nombre_tmp:nombre_tmp});
        });   
        
        obj_req = {
            idreqh:idreqh,
            ID_OPERACION:0,
            ASUNTO:req_asu,
            DESCRIPCION:req_des,
            //estado: estado,
            adjuntos:_array_uploads_adj,
            REMITENTE:req_remitente,
            PROPIETARIO:propietario
        }
        
        //console.dir( obj_req );
        
        $.ajax({
            url : _notas.URL + "/x_sendnota",
            data : {
                obj:obj_req
            },
            dataType : "json",
            type : "post",
            success : function(resp){
                
                data = resp.result;
                if (resp.accion=='add'){
                    if(data){
                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                            
                            $.fancybox.close();
                            $("#jqxgrid").show();
                            $("#jqxgrid").jqxGrid('updatebounddata');
                            $("#wpopup").html('');
                            process_asignar( resp.result.ID );
                
                        });
                    }else{
                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                            $.unblockUI();
                        });
                    }

                }else{
                    jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                        //evento_lista_req();
                        $.fancybox.close();
                        $("#jqxgrid").show();
                        $("#jqxgrid").jqxGrid('updatebounddata');
                        $("#wpopup").html('');

                    });
                }
            }
        });
    });

    $( "#req_etiqueta" ).keyup(function() {
        $("#req_etiquetah").val( $(this).val() );
    });
    
    
}

function agregar_nota(idobjeto, ver){
       idobjeto || ( idobjeto = [] );
       ver || ( ver = -1 );
       $.ajax({
            url : _notas.URL + "/x_getform_agregar_requerimiento",
            data : {
                idr:idobjeto
            },
            async:false,
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
                
                activar_acordeon('.grid-1');
                event_grid_traza( idobjeto );
                
            }
        });
}


function process_vincular(iid_nr, carpeta){
    
    carpeta || ( carpeta = '0' );
    
        $.ajax({
            url : _notas.URL + "/x_getvincular",
            data : {
                idusu:_USUARIO_SESION_ACTUAL
            },
            dataType : "json",
            type : "post",
            success : function(data1){
                var clase_asignar;
                var cadhtml = '<div class="asignar_titulo">Vincular a Carpeta Nº:</div>';
                if(data1){

                    $.each(data1, function (index, value){
                        clase_asignar = 'link_asignar link_vincular';
                        if (value.IID!=_USUARIO_SESION_ACTUAL){
                            if(carpeta>0){
                                if (carpeta==value.ID){
                                    cadhtml +=  '<div class="' + clase_asignar + ' x_area"  data-iid_nr="'+iid_nr+'" data-iid="'+value.ID+'"><span> Carpeta Nº ' + value.ID;
                                    cadhtml += '</span></div>';
                                }
                            }else{
                                cadhtml +=  '<div class="' + clase_asignar + ' x_area"  data-iid_nr="'+iid_nr+'" data-iid="'+value.ID+'"><span> Carpeta Nº ' + value.ID;
                                cadhtml += '</span></div>';
                            }
                            
                        }
                    });

                }

                $.fancybox({
                    "content": cadhtml,
                    'padding'   :  35,
                    'autoScale' :true,
                    'height' : 900,
                    'scrolling' : 'yes',
                    'beforeClose': function() {
                        /*
                        if (myfancy==1)
                            regresar_a_listado();
                            */
                    }
                });
                
                
                $(".link_vincular").click(function(e){

                    e.preventDefault();
                    var idcarpeta = $(this).data('iid');
                    var idnr = $(this).data('iid_nr');
                    
                    //asignar id_operacion a nr
                    jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                        if(r==true){
                            $.ajax({
                                url : _notas.URL + "/x_vincular_nr",
                                data : {
                                    idnr:idnr,
                                    idcarpeta:idcarpeta
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                
                                    jAlert('La nota fue adjuntada a la carpeta Nº .' + idcarpeta, $.ucwords(_etiqueta_modulo),function(){
                                        $.fancybox.close();
                                        $("#jqxgrid").show();
                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                        $("#wpopup").html('');
                                    });
                                
                                }

                            });

                        }
                    });
                });

                return false;

            }
        });
}

function process_asignar(iidnota){
    
    iidnota || ( iidnota = '-1' );
    //$('.asignar_nota').show();
    //$('.asignar_nota').on('click', function(event){
        //event.preventDefault();
        
        var opt_puesto=0;
        var opt_area=0;
        
        opt_area = [4];
        opt_puesto = 6;
        
        $.ajax({
            url : _notas.URL + "/x_getenviar_a1",
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
                        url : _notas.URL + "/x_getenviar_a2",
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

                                var observacion;
                                var estado;
                                var descripcion;
                                
                                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                                
                                var id_send;
                                if(mydata!=null){
                                    id_send = mydata.ID;
                                }else if(iidnota>0){
                                    id_send = iidnota
                                }

                                jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo),function(r){
                                    if(r==true){
                                        observacion='ENVIAR NOTA';
                                        descripcion='ENVIO DE NOTA A DESTINATARIO'
                                        estado='1'
                                        
                                        $.ajax({

                                            url : _notas.URL + "/x_guardar_traza_nota",
                                            data : {
                                                id_req_nota:id_send,
                                                destinatario:iid,
                                                observacion:observacion,
                                                descripcion:descripcion
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


function event_grid_traza( idnota ){
    
    if (idnota>0){
        
        var sourcetraza ={
            datatype: "json",
            datafields: [
                { name: 'ID' },
                { name: 'DESCRIPCION' , type: 'string' },
                { name: 'USUARIO' , type: 'string' },
                { name: 'FECHA' , type: 'string' }
            ],
            url: 'general/extends/extra/carpetas.php',
            data:{
                accion  :   "getTrazabilidadNota",
                idnota  :   idnota
            }
        };

        var dataAdaptertraza = new $.jqx.dataAdapter(sourcetraza,
            {}
        );


        $("#jqxgrid_traza").jqxGrid({
            width: '98%',
            source: dataAdaptertraza,
            theme: 'energyblue',
            ready: function () {
                $("#jqxgrid_traza").jqxGrid('hidecolumn', 'ID');
            },
            columnsresize: true,
            localization: getLocalization(),
            columns: [
                { text: 'USUARIO', datafield: 'USUARIO', width: '25%', hidden : false },
                { text: 'FECHA', datafield: 'FECHA', width: '25%', hidden : false },
                { text: 'DESCRIPCION', datafield: 'DESCRIPCION', width: '90%', hidden : false }
            ]
        });
        
    }
    
}