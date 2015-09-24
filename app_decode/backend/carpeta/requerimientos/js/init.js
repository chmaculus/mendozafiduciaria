var mydata;
var id_edit;
var working = false;
var _array_entidades = {};
var _array_chk = {};
var semmilla;
var id_ope_actual;

$(document).ready(function () {
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

    var sourceope = {
        datatype: "json",
        datafields: [
            {name: 'ID', type: 'string'},
            {name: 'ASUNTO', type: 'string'},
            {name: 'ESTADONR', type: 'string'},
            {name: 'ESTADO', type: 'string'},
            {name: 'DESTINATARIO', type: 'string'},
            {name: 'BENEF', type: 'string'},
            {name: 'ID_OPERACION', type: 'string'},
            {name: 'REMITENTE_NOMBRE', type: 'string'},
            {name: 'FCREA', type: 'string'},
            {name: 'REMITENTE', type: 'string'}

        ],
        url: 'general/extends/extra/carpetas.php',
        data: {
            accion: "getRequerimientos"
        },
        async: false,
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
                groupable: true,
                source: dataAdapterope,
                theme: 'energyblue',
                ready: function () {
                    $("#jqxgrid").jqxGrid('hidecolumn', 'REMITENTE');
                    $("#jqxgrid").jqxGrid('hidecolumn', 'ESTADO');
                },
                columnsresize: true,
                showtoolbar: true,
                sortable: true,
                filterable: true,
                showfilterrow: true,
                localization: getLocalization(),
                showstatusbar: true,
                renderstatusbar: function (statusbar) {
                    var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
                    var responderButton = $("<div style='float: left; margin-left: 5px;width:217px;'><img width=16 style='position: relative; margin-top: 2px;' src='general/css/images/32x32/checked.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Ver Requerimiento</span></div>");
                    container.append(responderButton);
                    statusbar.append(container);
                    responderButton.jqxButton({theme: theme, width: 210, height: 20});
                    responderButton.click(function (event) {
                        if (_permiso_modificacion == 0) {
                            jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                                $.unblockUI();
                                switchBarra();
                            });
                            return false;
                        }

                        var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                        var rowscount = $("#jqxgrid").jqxGrid('getdatainformation').rowscount;

                        if (selectedrowindex != '-1') {
                            if (selectedrowindex < rowscount) {

                                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                    var id = $("#jqxgrid").jqxGrid('getrowid', selectedrowindex);
                                    var datarow = $("#jqxgrid").jqxGrid('getrowdata', id);

                                    if (datarow.ESTADO == '3') {
                                        jAlert('Este Requerimiento ya fue marcado como Respondido.', $.ucwords(_etiqueta_modulo), function () {
                                        });
                                        return false;
                                    }

                                    var idr = datarow.ID;
                                    id_ope_actual = datarow.ID_OPERACION;

                                    $.ajax({
                                        url: "backend/carpeta/carpetas/x_getform_agregar_requerimiento",
                                        data: {
                                            idr: idr
                                        },
                                        type: "post",
                                        success: function (datareq) {
                                            $.fancybox(
                                                    datareq,
                                                    {
                                                        'padding': 35,
                                                        'autoScale': true,
                                                        'height': 900,
                                                        'scrolling': 'no'
                                                    }
                                            );

                                            //init_datepicker('#femis','-3','+0','0',1);

                                            init_datepicker('#fresp', '-3', '+0', '0', 1);
                                            $(".chzn-select").chosen({disable_search_threshold: 5});
                                            $("input[type=file]").change(function () {
                                                $(this).parents(".uploader").find(".filename").val($(this).val());
                                            });
                                            $("input[type=file]").each(function () {
                                                if ($(this).val() == "") {
                                                    $(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");
                                                }
                                            });

                                            var estadoh_req = $("#estadoh").val();
                                            $("#estadoreq").val(estadoh_req).attr('disabled', true).trigger("chosen:updated");

                                            var femish = $("#femish").val();
                                            $('#femis').val(femish);
                                            var fresph = $("#ahora").val();
                                            $('#fresp').val(fresph);

                                            $("#femis").attr("readonly", "readonly");
                                            $("#req_asunto").attr("readonly", "readonly");
                                            $("#req_descripcion").attr("readonly", "readonly");
                                            $("#idreqh").val(idr);

                                            var _sitio = $("#_dir_sitio").val();

                                            $(".lista_reqs_adj li").click(function () {
                                                var n = $(this).data("nom");
                                                url = _sitio + n;
                                                window.open(url, '_blank');
                                                return false;
                                            });

                                            $(".lista_reqs_adj a").click(function (e) {
                                                e.preventDefault();
                                                return false;

                                                var nom = $(this).prev().data('nom');
                                                var yo = $(this);
                                                var el = $(this).prev();


                                                $.ajax({
                                                    url: "backend/carpeta/carpetas/x_delupload_req",
                                                    data: {
                                                        idnotareq: idr,
                                                        ruta: nom
                                                    },
                                                    dataType: "json",
                                                    type: "post",
                                                    success: function (data) {
                                                        jAlert('Item Borrado.', $.ucwords(_etiqueta_modulo), function () {
                                                            yo.remove();
                                                            el.remove();
                                                        });
                                                    }
                                                });
                                                return false;
                                            });



                                            addEventsRequerimientos();

                                        }
                                    });
                                }

                            } else {
                                jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo), function () {

                                });
                                return false;
                            }


                        } else {
                            jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo), function () {
                            });
                            return false;
                        }
                    });
                },
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
                        //if (input.val().length >= 1) {
                        if (me.timer)
                            clearTimeout(me.timer);
                        me.timer = setTimeout(function () {
                            dataAdapterope.dataBind();
                        }, 300);
                        //}
                    });
                },
                columns: [
                    {text: 'ID', datafield: 'ID', width: '6%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
                    {text: 'REMITENTE', datafield: 'REMITENTE_NOMBRE', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
                    {text: 'ASUNTO', datafield: 'ASUNTO', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
                    {text: 'ESTADO', datafield: 'ESTADONR', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
                    {text: 'ESTADO', datafield: 'ESTADO', width: '0%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
                    {text: 'CARPETA', datafield: 'ID_OPERACION', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
                    {text: 'POSTULANTE', datafield: 'BENEF', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
                    {text: 'REMITENTE', datafield: 'REMITENTE', width: '0%'}
                ]
            });

});

function cargarMotivoReq() {
    $.ajax({
        url: 'backend/carpeta/requerimientos/x_cargar_motivo_rechazo_req',
        type: "post",
        data: {
            contMotivoReq: contMotivoReq,
            idNotReq: idNotificacionReq
        },
        async: false,
        success: function (data) {
//lo siguiente no lo saque de la copia del ajax, capas q no sirve
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


function addEventsRequerimientos() {

    $("#btnSubirfile").click(function (e) {

        if ($("#req_etiqueta").val() == '') {
            e.preventDefault();
            jAlert('Ingrese una etiqueta, por favor.', $.ucwords(_etiqueta_modulo), function () {
                $("#req_etiqueta").select();
            });
        }
    });

    $(".send_req_rechaz").on('click', function () {
        var idreqh = $("#idreqh").val();
        var req_asu = $("#req_asunto").val();
        var req_des = $("#req_descripcion").val();
        var req_res = $("#req_respuesta").val();

        var req_femis = $("#femis").val();
        var req_fresp = $("#fresp").val();
        var id_ope_req = id_ope_actual;
        //Se asigna el estado en 5 que es rechazado
        var estado = 5;
        var autor_req = 24;
        var idNotificacion = idreqh;
        jConfirm('Esta seguro de rechazar este Requerimiento??.', 'Carpetas', function (r) {
            if (r == true) {
                var metodo = "<div style='width:350px; height:200px;'>\n\
            <h2 style='font-size: 18px;margin-left: 3px;font-weight: bold; width:300px; '>Ingrese el motivo de rechazo</h2>\n\
            <input id='inputmotivo' style='width:300px; height:50px; margin-top:25px'/>\n\
            <input id='traermotivo' type='button' style='width:300px; padding:20px; margin-top:25px' value='Guardar'/> </div>";
                $.fancybox({
                    "content": metodo,
                    'padding': 40,
                    'autoScale': true,
                    'scrolling': 'no',
                    close: [27], // escape key
//                'beforeClose': function() {
//                    location.reload();
//                }
                });
                $("#traermotivo").on('click', function () {
                    var idnr = idreqh;
                    var contMotivo = $('#inputmotivo').val();
                    $.ajax({
                        url: 'backend/carpeta/carpetas/x_cancelar_requerimientos',
                        type: "post",
                        data: {
                            idnr: idnr,
                            motivotext: contMotivo,
                        },
                        async: false,
                        success: function (data) {
                            cargarMotivo(contMotivo, idNotificacion);
                            jAlert('Requerimiento Rechazada.', 'Carpetas', function () {
                                actualizaNotif();
                                regresar_a_listado();
//                                        refrescarDomNotif(meobj);
                            });
                        }
                    });
                    /*****************************************/
                    /*ACA ponemos lo de actualizar los datos*/
                    /*****************************************/
                    // si rol es jefe de op, estado = 2
                    if (_USER_ROL == 10) {
                        estado = 2;
                        autor_req = 0;
                    }
                    // si rol es mesa entrada, estado = 3
                    if (_USER_ROL == 9) {
                        estado = 3;
                        autor_req = 0;
                    }
                    //adjuntos
                    var _array_uploads_adj = [];
                    $(".lista_reqs_adj li").each(function (index) {
                        var nombre = $(this).data('nom');
                        var nombre_tmp = $(this).data('tmp');
                        _array_uploads_adj.push({nombre: nombre, nombre_tmp: nombre_tmp});
                    });
                    obj_req = {
                        ID_OPERACION: id_ope_req,
                        ASUNTO: req_asu,
                        DESCRIPCION: req_des,
                        RESPUESTA: req_res,
                        estado: estado,
                        idreqh: idreqh,
                        FCREA: req_femis,
                        FREC: req_fresp,
                        adjuntos: _array_uploads_adj,
                        autor_req: autor_req
                    }
                    $.ajax({
                        url: "backend/carpeta/carpetas/x_sendreq_rechaz",
                        data: {
                            obj: obj_req,
                            notif_ope: 1,
                            valorSeteado: 1,
                        },
                        dataType: "json",
                        type: "post",
                        success: function (resp) {
                            data = resp.result;
                            if (resp.accion == 'add') {
                                if (data) {
                                    jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {
                                        var ftra = data.FTRA ? data.FTRA : '          ';
                                        var frec = data.FREC ? data.FREC : '           ';
                                        var fcrea = data.FCREA ? data.FCREA : '          ';
                                        var tmp1_h = '<li class="li_cabezera">Id<span class="fet">' + "F. Tratamiento" + '</span><span class="fer">' + "F. Recepción" + '</span><span class="fem">' + "F. Emisión" + '</span><span class="reqest">Estado</span><span class="reqiid">Asunto</span></li>';
                                        var tmp_li = '<li data-idr="' + data.ID + '"><span class="filr_iid">' + '33' + '</span><span class="filr_asunto">' + data.ASUNTO + '</span><span class="filr_estado">Emitido</span><span>' + fcrea + '</span><span>' + frec + '</span><span>' + ftra + '</span></li>';

                                        if (data.ESTADO == 2)
                                            tmp_li = '<li class="ya_enviado" data-idr="' + data.ID + '"><span class="filr_iid">' + '33' + '</span><span class="filr_asunto">' + data.ASUNTO + '</span><span class="filr_estado">Emitido</span><span>' + fcrea + '</span><span>' + frec + '</span><span>' + ftra + '</span></li>';

                                        var lis = $(".grid_reqs .lista_reqs li");

                                        if (lis.length == 0) {
                                            $(".grid_reqs .lista_reqs").append(tmp1_h);
                                        }
                                        $(".grid_reqs .lista_reqs").append(tmp_li);
                                        //eventos
                                        //evento_lista_req();
                                        $.fancybox.close();
                                        $("#jqxgrid").show();
                                        $("#jqxgrid").jqxGrid('updatebounddata');
                                        $("#wpopup").html('');
                                    });

                                } else {
                                    jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo), function () {
                                        $.unblockUI();
                                    });
                                }

                            }
                            $.fancybox.close();
                        }
                    });
                })
            }
        });
    });

    $(".send_req_acept").on('click', function () {
        // edit/new
        var idreqh = $("#idreqh").val();
        var req_asu = $("#req_asunto").val();
        var req_des = $("#req_descripcion").val();
        var req_res = $("#req_respuesta").val();
        //var req_etiqueta = $("#req_etiqueta").val();

        var req_femis = $("#femis").val();
        var req_fresp = $("#fresp").val();

        var id_ope_req = id_ope_actual;
        var estado = 4;
        var autor_req = 24;

        // si rol es jefe de op, estado = 2
        if (_USER_ROL == 10) {
            estado = 2;
            autor_req = 0;
        }

        // si rol es mesa entrada, estado = 3
        if (_USER_ROL == 9) {
            estado = 3;
            autor_req = 0;
        }

        //adjuntos
        var _array_uploads_adj = [];
        $(".lista_reqs_adj li").each(function (index) {
            var nombre = $(this).data('nom');
            var nombre_tmp = $(this).data('tmp');
            _array_uploads_adj.push({nombre: nombre, nombre_tmp: nombre_tmp});
        });
        obj_req = {
            ID_OPERACION: id_ope_req,
            ASUNTO: req_asu,
            DESCRIPCION: req_des,
            RESPUESTA: req_res,
            estado: estado,
            idreqh: idreqh,
            FCREA: req_femis,
            FREC: req_fresp,
            adjuntos: _array_uploads_adj,
            autor_req: autor_req
        }
        $.ajax({
            url: "backend/carpeta/carpetas/x_sendreq_estado",
            data: {
                obj: obj_req,
                notif_ope: 1,
                valorSeteado: 1,
            },
            dataType: "json",
            type: "post",
            success: function (resp) {
                data = resp.result;
                if (resp.accion == 'add') {
                    if (data) {
                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {

                            var ftra = data.FTRA ? data.FTRA : '          ';
                            var frec = data.FREC ? data.FREC : '           ';
                            var fcrea = data.FCREA ? data.FCREA : '          ';
                            var tmp1_h = '<li class="li_cabezera">Id<span class="fet">' + "F. Tratamiento" + '</span><span class="fer">' + "F. Recepción" + '</span><span class="fem">' + "F. Emisión" + '</span><span class="reqest">Estado</span><span class="reqiid">Asunto</span></li>';
                            var tmp_li = '<li data-idr="' + data.ID + '"><span class="filr_iid">' + '33' + '</span><span class="filr_asunto">' + data.ASUNTO + '</span><span class="filr_estado">Emitido</span><span>' + fcrea + '</span><span>' + frec + '</span><span>' + ftra + '</span></li>';

                            if (data.ESTADO == 2)
                                tmp_li = '<li class="ya_enviado" data-idr="' + data.ID + '"><span class="filr_iid">' + '33' + '</span><span class="filr_asunto">' + data.ASUNTO + '</span><span class="filr_estado">Emitido</span><span>' + fcrea + '</span><span>' + frec + '</span><span>' + ftra + '</span></li>';

                            var lis = $(".grid_reqs .lista_reqs li");

                            if (lis.length == 0) {
                                $(".grid_reqs .lista_reqs").append(tmp1_h);
                            }
                            $(".grid_reqs .lista_reqs").append(tmp_li);
                            //eventos
                            //evento_lista_req();
                            $.fancybox.close();
                            $("#jqxgrid").show();
                            $("#jqxgrid").jqxGrid('updatebounddata');
                            $("#wpopup").html('');

                        });
                    } else {
                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo), function () {
                            $.unblockUI();
                        });
                    }

                } else {
                    jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {
                        $.fancybox.close();
                        $("#jqxgrid").show();
                        $("#jqxgrid").jqxGrid('updatebounddata');
                        $("#wpopup").html('');
                    });
                }
            }
        });
//        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {
//            $.fancybox.close();
//            $("#jqxgrid").show();
//            $("#jqxgrid").jqxGrid('updatebounddata');
//            $("#wpopup").html('');
////                            process_asignar( resp.result.ID );
//
//        });
    });

    $(".send_req").on('click', function () {
//        e.preventDefault();
        // edit/new
        var idreqh = $("#idreqh").val();
        var req_asu = $("#req_asunto").val();
        var req_des = $("#req_descripcion").val();
        var req_res = $("#req_respuesta").val();
        //var req_etiqueta = $("#req_etiqueta").val();

        var req_femis = $("#femis").val();
        var req_fresp = $("#fresp").val();

        var id_ope_req = id_ope_actual;
        var estado = 0;
        var autor_req = 24;

        // si rol es jefe de op, estado = 2
        if (_USER_ROL == 10) {
            estado = 2;
            autor_req = 0;
        }

        // si rol es mesa entrada, estado = 3
        if (_USER_ROL == 9) {
            estado = 3;
            autor_req = 0;
        }

        //adjuntos
        var _array_uploads_adj = [];
        $(".lista_reqs_adj li").each(function (index) {
            var nombre = $(this).data('nom');
            var nombre_tmp = $(this).data('tmp');
            _array_uploads_adj.push({nombre: nombre, nombre_tmp: nombre_tmp});
        });

        obj_req = {
            ID_OPERACION: id_ope_req,
            ASUNTO: req_asu,
            DESCRIPCION: req_des,
            RESPUESTA: req_res,
            estado: estado,
            idreqh: idreqh,
            FCREA: req_femis,
            FREC: req_fresp,
            adjuntos: _array_uploads_adj,
            autor_req: autor_req
        }

        $.ajax({
            url: "backend/carpeta/carpetas/x_sendreq",
            data: {
                obj: obj_req,
                notif_ope: 1,
                valorSeteado: 1,
            },
            dataType: "json",
            type: "post",
            success: function (resp) {
                data = resp.result;
                if (resp.accion == 'add') {
                    if (data) {
                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {
                            //agregar req con jquery
                            //echo '<li class="li_cabezera">Asunto<span class="fem">'. "F. Emisión" .'</span><span class="fer">'. "F. Recepción" .'</span><span class="fet">'. "F. Tratamiento" .'</span></li>';
                            //echo '<li>xxxxxxx<span>('. "15/15/15" .')</span><span>('. "15/15/15" .')</span><span>('. "15/15/15" .')</span></li>';

                            var ftra = data.FTRA ? data.FTRA : '          ';
                            var frec = data.FREC ? data.FREC : '           ';
                            var fcrea = data.FCREA ? data.FCREA : '          ';
                            var tmp1_h = '<li class="li_cabezera">Id<span class="fet">' + "F. Tratamiento" + '</span><span class="fer">' + "F. Recepción" + '</span><span class="fem">' + "F. Emisión" + '</span><span class="reqest">Estado</span><span class="reqiid">Asunto</span></li>';
                            var tmp_li = '<li data-idr="' + data.ID + '"><span class="filr_iid">' + '33' + '</span><span class="filr_asunto">' + data.ASUNTO + '</span><span class="filr_estado">Emitido</span><span>' + fcrea + '</span><span>' + frec + '</span><span>' + ftra + '</span></li>';

                            if (data.ESTADO == 2)
                                tmp_li = '<li class="ya_enviado" data-idr="' + data.ID + '"><span class="filr_iid">' + '33' + '</span><span class="filr_asunto">' + data.ASUNTO + '</span><span class="filr_estado">Emitido</span><span>' + fcrea + '</span><span>' + frec + '</span><span>' + ftra + '</span></li>';

                            var lis = $(".grid_reqs .lista_reqs li");

                            if (lis.length == 0) {
                                $(".grid_reqs .lista_reqs").append(tmp1_h);
                            }
                            $(".grid_reqs .lista_reqs").append(tmp_li);
                            //eventos
                            //evento_lista_req();
                            $.fancybox.close();
                            $("#jqxgrid").show();
                            $("#jqxgrid").jqxGrid('updatebounddata');
                            $("#wpopup").html('');

                        });
                    } else {
                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo), function () {
                            $.unblockUI();
                        });
                    }

                } else {
                    jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {
                        /*
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
                         $(".clear_req").trigger('click');
                         */
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

    $(".clear_req").on('click', function (e) {
        e.preventDefault();
        $("#idreqh").val('');
        $("#req_descripcion").val('');
        $("#req_respuesta").val('');
        $("#femis").val('');
        $("#fresp").val('');
        $("#estadoreq").val(0).trigger("chosen:updated");
        $("#upload_file2 input[type=file]").each(function () {
            $(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");
        });
        $("#req_asunto").val('').select();


    });

    $("#req_etiqueta").keyup(function () {
        $("#req_etiquetah").val($(this).val());
    });


}