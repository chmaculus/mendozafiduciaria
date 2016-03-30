var mydata;
var id_edit;
var working = false;
var _array_entidades = {};
var _array_chk = {};
var semmilla;
var id_ope_actual;
var myfancy = 0;
var var_cliente = {};
var condicioniva_g = 0;
var id_a_editar = 0;
// local
/*
 var _fid_sanjuan = 41;
 var _ope_sanjuan = 32;
 
 var _fid_mendoza = 33;
 var _ope_mendoza = 42;
 */
var _fid_sanjuan = 88;
var _ope_sanjuan = 99;
var _fid_mendoza = 66;
var _ope_mendoza = 77;
var nolocal = 1;
if (nolocal == 1) {
    var _fid_sanjuan = 1;
    var _ope_sanjuan = 16;
    var _fid_mendoza = 1;
    var _ope_mendoza = 16;
}


function guardar_factura() {
//e.preventDefault();
    var id = $("#idh").val();
    var numero = $("#numero").val();
    var fecha = $("#fecha").val();
    fecha = formattedDate_ui(fecha);
    var cai = $("#cai").val();
    var fechavto = $("#fechavto").val();
    fechavto = formattedDate_ui(fechavto);
    var bodega = $("#bodega").val();
//    var kgrs = $("#kgrs").val();
    var ltros = $("#ltros").val();
    var cuitform = $("#cuitform").val();
    var azucar = $("#azucar").val();
    var precio = $("#precio").val();
    var neto = $("#neto").val();
    var iva = $("#iva").val();
    var porcentaje_iva = $("#porcentaje_iva").val();
    var total = $("#total").val();
    var observacion_fact = $("#observacion_fact").val();
    var formula = $("#formula").val();

    //bancos
//    var griddata = $('#jqxgridcius').jqxGrid('getdatainformation');
//    var _arr_cius = [];
//    for (var i = 0; i < griddata.rowscount; i++)
//        _arr_cius.push($('#jqxgridcius').jqxGrid('getrenderedrowdata', i));
//    var sum_kgrs = 0;
//    var sum_azuc = 0;
    //validacion
//    if (_arr_cius) {
//        $.each(_arr_cius, function (index, value) {
////sum_kgrs
//            sum_kgrs += parseFloat(value.KGRS);
//            sum_azuc += parseFloat(value.AZUCAR * value.KGRS);
//        });
//        sum_azuc = sum_azuc / sum_kgrs;
//    }

    iid = id ? id : 0;
//    if (_opcion == 3) {
//        objsave = {
//            id: iid,
//            update_cius: 1,
//            arr_cius: _arr_cius,
//            CUIT: cuitform,
//        }
//    } else {
//validar campos
    if (numero == '') {
        jAlert('Ingrese el número de factura.', $.ucwords(_etiqueta_modulo), function () {
            $("#numero").focus();
        });
        return false;
    }

    if (fecha == '') {
        jAlert('Ingrese fecha.', $.ucwords(_etiqueta_modulo), function () {
            $("#fecha").focus();
        });
        return false;
    }
    /*
     if (cai==''){jAlert('Ingrese CAI.', $.ucwords(_etiqueta_modulo), function(){$("#cai").focus();});return false;}
     if (fechavto==''){jAlert('Ingrese fecha Vencimiento.', $.ucwords(_etiqueta_modulo), function(){$("#fechavto").focus();});return false;}
     */
    if (cai !== '') {
        if (fechavto < fecha) {
            jAlert('La fecha de Vencimiento del CAI no puede ser anterior a la fecha de la factura.', $.ucwords(_etiqueta_modulo), function () {
                $("#fechavto").focus();
            });
            return false;
        }
    }
    if (bodega == '') {
        jAlert('Elija una bodega.', $.ucwords(_etiqueta_modulo), function () {
            $("#bodega").focus();
        });
        return false;
    }
    if (ltros == '') {
        jAlert('Ingrese el valor de los Ltros.', $.ucwords(_etiqueta_modulo), function () {
            $("#ltros").focus();
        });
        return false;
    }

    if (azucar == '') {
        jAlert('Ingrese el valor de Azúcar.', $.ucwords(_etiqueta_modulo), function () {
            $("#azucar").focus();
        });
        return false;
    }

    if (precio == '') {
        jAlert('Ingrese el precio.', $.ucwords(_etiqueta_modulo), function () {
            $("#precio").focus();
        });
        return false;
    }

//        console.log('kgrs:: ' + kgrs + ' sum kgrs::' + sum_kgrs);
//        if (kgrs != sum_kgrs) {
//            jAlert('Las sumas kgrs no coinciden.', $.ucwords(_etiqueta_modulo), function () {
//            });
//            return false;
//        }
//
//        console.log('azucar:: ' + azucar + ' sum azucar::' + sum_azuc);
//        if (sum_azuc < azucar) {
//            jAlert('Las sumas azucar no coinciden.', $.ucwords(_etiqueta_modulo), function () {
//
//            });
//            return false;
//        }

    var tmp_ope, tmp_fid;
    var local = 1;
    if (_provincia == '12') {
        tmp_ope = 42;
        tmp_fid = 33;
    } else if (_provincia == '17') {
        tmp_ope = 41;
        tmp_fid = 32;
    }

    var nolocal = 1;
    if (nolocal == 1) {

        if (_provincia == '12') {
            tmp_ope = _ope_mendoza;
            tmp_fid = _fid_mendoza;
        } else if (_provincia == '17') {
            tmp_ope = _ope_sanjuan;
            tmp_fid = _fid_sanjuan;
        }
    }

    objsave = {
        id: iid,
        NUMERO: numero,
        FECHA: fecha,
        CAI: cai,
        ID_PROVINCIA: _provincia,
        FECHAVTO: fechavto,
        ID_BODEGA: bodega,
        CUIT: cuitform,
//        KGRS: kgrs,
        LITROS: ltros,
        AZUCAR: azucar,
        PRECIO: precio,
        ID_ESTADO: 1,
        USU_CARGA: _USUARIO_SESION_ACTUAL,
        NETO: neto,
        IVA: iva,
        TOTAL: total,
        OBSERVACIONES: observacion_fact,
//            arr_cius: _arr_cius,
        update_cius: 0,
        ID_OPERATORIA: tmp_ope,
        ID_FIDEICOMISO: tmp_fid,
        PORC_IVA: porcentaje_iva,
        FORMULA: formula,
        TIPO: 1
    }
//    }
//validar numero de factura
//numero
    $.ajax({
        url: _compravino.URL + "/x_verificarnumfactura",
        data: {
            numero: numero
        },
        dataType: "json",
        type: "post",
        success: function (data) {
            // 1 existe // 0 no existe
            if (data > 0 && _opcion != 3) {//existe
                jAlert('Este numero de Factura ya esta ingresada. Verifique los datos por favor.', $.ucwords(_etiqueta_modulo), function () {
                });
            } else { // no existe
                $.ajax({
                    url: _compravino.URL + "/x_sendobj",
                    data: {
                        obj: objsave
                    },
                    dataType: "json",
                    type: "post",
                    success: function (data) {
                        console.dir(data);
                        if (data.result > 0) {
                            jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {
                                show_btns();
                                limpiar_form_fact();
                                $('#send').hide();
                                var_cliente = {};
                            });
                        } else {
                            jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo), function () {
                                $.unblockUI();
                            });
                        }
                    }
                });
            }
        }
    });
}

function limpiar_form() {

    $(".env_form input").not('.button-a').val('');
    $(".env_form textarea").val('');
    $("#provincia").val(0).trigger("chosen:updated");
    $("#condicioniva").val(0).trigger("chosen:updated");
    $("#condicioniibb").val(0).trigger("chosen:updated");
    loadChild(0);
}

function limpiar_form_fact() {
    $(".nuevafact_form input").not('.button-a').val('');
    $(".nuevafact_form textarea").val('');
    $("#bodega").val(0).trigger("chosen:updated");
    $('#jqxgridcius').jqxGrid('clear');
}

function show_btns(sw) {

    sw || (sw = '0');
    console.log(sw);
//    alert('sw::' + sw );

    if (sw == 1) {//busqueda
        $("#nuevafactura").hide();
        $(".tb_fil").show();
        $(".tb_save").hide();
        $(".tb_atras").show();
        $(".tb_save1").hide();
        $(".tb_search").hide();
    } else if (sw == 2) {//nueva factura
        $(".tb_save").hide();
        $(".tb_fil").hide();
        $(".tb_atras").show();
        $(".tb_save1").show();
        $(".tb_search").hide();
    } else if (sw == 3) {//cuando ingresamos cuit desconocido
        $("#nuevafactura").hide();
        $(".tb_fil").hide();
        $(".tb_save").show();
        $(".tb_atras").show();
        $(".tb_search").hide();
        $(".tb_save1").hide();
//    } else if (sw == 7) {//cuando ingesamos a operatoria commpra vino
//        $(".tb_atras").show();
    } else {//inicio 0
        $("#nuevafactura").hide();
        $(".tb_fil").hide();
        $(".tb_save").hide();
        $(".tb_search").show();
        $(".tb_atras").hide();
        $(".tb_save1").hide();
        $(".env_form").hide();
        $(".nuevafact_form").hide();
        //limpiar form
        limpiar_form();
    }
}


function llenar_form(cliente) {

    var_cliente = cliente;
    $("#id_buscar").val(cliente.ID);
    $(".env_form #nombre").val(cliente.RAZON_SOCIAL);
    $(".env_form #cuit").val(cliente.CUIT);
    $(".env_form #insciibb").val(cliente.INSCRIPCION_IIBB);
    $(".env_form #cbu").val(cliente.CBU);
    $(".env_form #direccion").val(cliente.DIRECCION);
    $(".env_form #correo").val(cliente.CORREO);
    $(".env_form #telefono").val(cliente.TELEFONO);
    $(".env_form #observacion").val(cliente.OBSERVACION);
    $(".env_form #condicioniva").val(cliente.ID_CONDICION_IVA).trigger("chosen:updated");
    $(".env_form #condicioniibb").val(cliente.ID_CONDICION_IIBB).trigger("chosen:updated");
    $(".env_form #provincia").val(cliente.ID_PROVINCIA).trigger("chosen:updated");
    loadChild(cliente.ID_PROVINCIA);
    $(".env_form #subrubro").val(cliente.ID_DEPARTAMENTO).trigger("chosen:updated");
}


$(document).ready(function () {
//$('#myDropDown').chosen({ disable_search_threshold: 10 });
    $("#opeCoordinador").chosen({width: "250px"});
    $("#opeJefe").chosen({width: "250px"});
    $("#opeProveedores").chosen({width: "400px"});
    $("#opeBodega").chosen({width: "400px"});
    $("#tipoPersona").chosen({width: "180px", disable_search_threshold: 10});
//    $("#listbox_juridica").hide();
    $("#juridica").hide();
    $('#send').show();
    $('.tb_atras_ope').on('click', function (e) {
        var urlh = "backend/carpeta/compravino/init/12/7";
        $(location).attr('href', urlh);
    });
    semmilla = fGetNumUnico();
    mydata = '';
    initGridListado();
    initGridListadoRevision();
    $(".toolbar li").hover(
            function () {
                $(this).removeClass('li_sel').addClass('li_sel');
            },
            function () {
                $(this).removeClass('li_sel');
            }
    );
    $('.tb_todas').on('click', function (e) {
        e.preventDefault();
        initGrid();
    });
    $('.tb_miscar').on('click', function (e) {
        e.preventDefault();
        initGrid(_USUARIO_SESION_ACTUAL);
    });
    $("#cuit_busqueda").keyup(function (event) {
        if (event.which == 13) {
            $('.consultar').trigger('click');
        }
    });
    loadChild(0);
    $('#provincia').bind('change', function (event) {
        event.preventDefault();
        $(this).validationEngine('validate');
        if ($('#provincia').val() == '')
            loadChild(0)
        $('#provinciah').val($('#provincia').val());
        var selected = $(this).find('option').eq(this.selectedIndex);
        var connection = selected.data('connection');
        selected.closest('#rubro li').nextAll().remove();
        if (connection) {
            loadChild(connection);
        }
    });
    $('#bodega').bind('change', function (event) {
        event.preventDefault();
        var selected = $(this).find('option').eq(this.selectedIndex);
        var local = selected.data('local');
        if ($('#bodega').val() != '') {
            $("#prov_bodega").val(local);
        }
    });
//    $('#bodega').bind('change', function (event) {
//        event.preventDefault();
//        var selected = $(this).find('option').eq(this.selectedIndex);
//        var local = selected.data('local');
//        if ($('#bodega').val() != '') {
//            $("#dto_bodega").val(local);
//        }
//    });
    $('.consultar').on('click', function (e) {
        e.preventDefault();
        $('.env_form').show();
        $('.nuevafact_form').hide();
        $("#provincia").chosen();
        $("#condicioniva").chosen({width: "220px"});
        $("#condicioniibb").chosen({width: "220px"});
        $("#bodega").chosen({width: "220px"});
        $("#formula").chosen({width: "220px"});
        $("#listbox").show();
        var cuit = $("#cuit_busqueda").val();
        /* buscar por cuit */
        $.ajax({
            url: _compravino.URL + "/x_getobjcliente",
            data: {
                cuit: cuit
            },
            dataType: "json",
            type: "post",
            success: function (data) {
                if (data.ID > 0) {
                    $.unblockUI();
                    llenar_form(data);
                    //$("#nuevafactura").show();
                    show_btns(1);
                    $("#send").hide();
                } else {
                    jAlert('Este CUIT no está registrado. guarde un nuevo cliente o intente otra busqueda (con Escape - Esc ).', $.ucwords(_etiqueta_modulo), function () {
                        $("#cuit_busqueda").val('');
                        $("#nombre").focus();
                        $("#cuit").val(cuit);
                        //$("#nuevafactura").hide();
                        show_btns(3);
                        $("#send").show();
                        $.unblockUI();
                    });
                }
            }
        });
    });
    $('#nuevafactura').off().on('click', function (e) {
        e.preventDefault();
        var cc = $("#cuit_busqueda").val();
        limpiar_form_nf();
        $("#porcentaje_iva").val('10.5');
        $('.env_form').hide();
        $('.nuevafact_form').show();
        $.ajax({
            url: _compravino.URL + "/x_getNumOpe",
            data: {
                id_cliente: $("#id_buscar").val()
            },
            dataType: "json",
            type: "post",
            async: false,
            success: function (data) {
                $("#numOperatoria").val(data.ID_OPERATORIA);
            }
        });
//ESTO SERIA PARA TRATAR DE LLENAR EL COMBO SOLAMENTE CON LAS BODEGAS QUE SE CARGARON A LA OPERATORIA
//        $.ajax({
//            url: _compravino.URL + "/x_getbodegas_vino",
//            data: {
//                id_operatoria:  $("#numOperatoria").val()
//            },
//            dataType: "json",
//            type: "post",
//            success: function (data) {
//            }
//        });

        /*AQUI TERMINARIA EL PROCESO DE CARGA DEL COMBO*/
        $("#nombre2").val($("#nombre").val());
        $("#cuitform").val(cc);
        show_btns(2);
    });
    refresGridevent();
    $('#send').on('click', function (e) {
        e.preventDefault();
//var selected = '';    
//alert($('#listbox_humana').val());
//        $('#listbox_humana').each(function(){
//            if (this.checked) {selected += $(this).val()+', ';}}); 
//        if ($('#listbox_humana').val() != '') alert('Has seleccionado: '+selected);  elsealert('Debes seleccionar al menos una opción.');
        var opeNombre = $("#opeNombre").val();
        var opeDescripcion = $("#opeDescripcion").val();
        var opeCoordinador = $("#opeCoordinador").val();
        var opeJefe = $("#opeJefe").val();
        var listrosMax = $("#listrosMax").val();
        var maxHectareas = $("#maxHectareas").val();
        var opeProveedores = $("#opeProveedores").val();
        var opeBodega = $("#opeBodega").val();
        var opePrecio1 = $("#opeP1").val();
        var opePrecio2 = $("#opeP2").val();
        var opePrecio3 = $("#opeP3").val();
        var opePrecio4 = $("#opeP4").val();
        var opePrecio5 = $("#opeP5").val();
        var opePrecio6 = $("#opeP6").val();
        var tipoPersona = $("#tipoPersona").val();
        var nuevoID = 0;
        var rows_proveedores = $('#jqxgrid_proveedores').jqxGrid('getrows');
        var rowscount_proveedores = rows_proveedores.length;
        var data_proveedores = [];
        for (var i = 0; i < rowscount_proveedores; i++) {
            data_proveedores[i] = $('#jqxgrid_proveedores').jqxGrid('getrowdata', i);
        }
        var rows_bodegas = $('#jqxgrid_bodegas').jqxGrid('getrows');
        var rowscount_bodegas = rows_bodegas.length;
        var data_bodegas = [];
        for (var i = 0; i < rowscount_bodegas; i++) {
            data_bodegas[i] = $('#jqxgrid_bodegas').jqxGrid('getrowdata', i);
        }
        var tipoPersona = $("#tipoPersona").val();
//if (formaPago == 'Cuotas') {
//            cantCuotas = $("#cantCuotas").val();
//        }


        var opeTitular = $("#opeTitular").val();
        var opeCuit = $("#opeCuit").val();
        var numVinedo = $("#numVinedo").val();
        var litrosOfrecidos = $("#litrosOfrecidos").val();
        var hectDeclaradas = $("#hectDeclaradas").val();
        var bgaDep = $("#bgaDep").val();
        var deptBodega = $("#deptBodega").val();
        var numINVBodega = $("#numINVBodega").val();
        var opetelefono = $("#opetelefono").val();
        var opeCorreo = $("#opeCorreo").val();
//validar campos
//        if (opeNombre == '') {
//            jAlert('Ingrese Nombre Operatoria.', $.ucwords(_etiqueta_modulo), function () {
//                $("#opeNombre").focus();
//            });
//            return false;
//        }
//        if (opeDescripcion == '') {
//            jAlert('Ingrese Descripcion.', $.ucwords(_etiqueta_modulo), function () {$("#opeDescripcion").focus();});
//            return false;
//        }
//        if (opeCoordinador == '') {
//            jAlert('Seleccione Coordinador de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#opeCoordinador").focus();});
//            return false;
//        }
//        if (opeJefe == '') {
//            jAlert('Seleccione Jefe de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#opeJefe").focus();});
//            return false;
//        }
//        if (listrosMax == '') {
//            jAlert('Ingrese el limite de litros de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#listrosMax").focus();});
//            return false;
//        }
//        if (maxHectareas == '') {
//            jAlert('Seleccione el maximo de hectareas permitido.', $.ucwords(_etiqueta_modulo), function () {$("#maxHectareas").focus();});
//            return false;
//        }
//        if (opeProveedores == '') {
//            jAlert('Seleccione proveedor/es.', $.ucwords(_etiqueta_modulo), function () {$("#maxHectareas").focus();});
//            return false;
//        }
//        if (opeBodega == '') {
//            jAlert('Seleccione bodega/s.', $.ucwords(_etiqueta_modulo), function () {$("#maxHectareas").focus();});
//            return false;
//        }

        $.ajax({
            url: _compravino.URL + "/x_getIdOperatoria",
            dataType: "json",
            type: "post",
            success: function (data) {
                nuevoID = data;
                $.ajax({
                    url: _compravino.URL + "/x_sendOperatoria",
                    data: {
                        nuevoID: nuevoID,
                        opeNombre: opeNombre,
                        opeDescripcion: opeDescripcion,
                        opeCoordinador: opeCoordinador,
                        opeJefe: opeJefe,
                        listrosMax: listrosMax,
                        tipoPersona: tipoPersona,
                        opePrecio1: opePrecio1,
                        opePrecio2: opePrecio2,
                        opePrecio3: opePrecio3,
                        opePrecio4: opePrecio4,
                        opePrecio5: opePrecio5,
                        opePrecio6: opePrecio6,
                        opeTitular: opeTitular,
                        opeCuit: opeCuit,
                        numVinedo: numVinedo,
                        litrosOfrecidos: litrosOfrecidos,
                        hectDeclaradas: hectDeclaradas,
                        bgaDep: bgaDep,
                        deptBodega: deptBodega,
                        numINVBodega: numINVBodega,
                        opetelefono: opetelefono,
                        opeCorreo: opeCorreo
                    },
                    dataType: "json",
                    type: "post",
                });
                $.ajax({
                    url: _compravino.URL + "/x_sendProveedores",
                    data: {
                        data_proveedores: data_proveedores,
                        nuevoID: nuevoID
                    },
                    dataType: "json",
                    type: "post",
                });
                $.ajax({
                    url: _compravino.URL + "/x_sendBodegas",
                    data: {
                        data_bodegas: data_bodegas,
                        nuevoID: nuevoID
                    },
                    dataType: "json",
                    type: "post",
                });
                if (tipoPersona == 'Humana') {

                    var array_humana = new Array();
                    $("#humana tr.op").each(function () {
                        var data_p = {
                            'numcheck': $(this).children("td.numCheck").text(),
                            'valor': $(this).children("td").children('select').val()
                        }
                        array_humana.push(data_p);
                    });
                    $.ajax({
                        url: _compravino.URL + "/x_sendHumana",
                        data: {
                            checks_humana: array_humana,
                            nuevoID: nuevoID
                        },
                        dataType: "json",
                        type: "post",
                    });
                } else if (tipoPersona == 'Juridica') {
                    var array_juridica = new Array();
                    $("#juridica tr.op").each(function () {
                        var data_p = {
                            'numcheck': $(this).children("td.numCheck").text(),
                            'valor': $(this).children("td").children('select').val()
                        }
                        array_juridica.push(data_p);
                    });
                    $.ajax({
                        url: _compravino.URL + "/x_sendJuridica",
                        data: {
                            array_juridica: array_juridica,
                            nuevoID: nuevoID
                        },
                        dataType: "json",
                        type: "post",
                    });
                }

                jAlert('Se guardo operatoria correctamente.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    var urlh = "backend/carpeta/compravino/init/12/7";
                    $(location).attr('href', urlh);
                });
            }
        });
    });
    $('#send_edit').on('click', function (e) {
        e.preventDefault();
        var url_con_id = document.location.href;
        var ultimo_id = url_con_id.split("/");
        var el_id = ultimo_id[ultimo_id.length - 1];
        var opeNombre = $("#opeNombre").val();
        var opeDescripcion = $("#opeDescripcion").val();
        var opeCoordinador = $("#opeCoordinador").val();
        var opeJefe = $("#opeJefe").val();
        var listrosMax = $("#listrosMax").val();
        var maxHectareas = $("#maxHectareas").val();
        var opeProveedores = $("#opeProveedores").val();
        var opeBodega = $("#opeBodega").val();
        var opePrecio1 = $("#opeP1").val();
        var opePrecio2 = $("#opeP2").val();
        var opePrecio3 = $("#opeP3").val();
        var opePrecio4 = $("#opeP4").val();
        var opePrecio5 = $("#opeP5").val();
        var opePrecio6 = $("#opeP6").val();
//        var nuevoID = 0;
        var rows_proveedores = $('#jqxgrid_proveedores').jqxGrid('getrows');
        var rowscount_proveedores = rows_proveedores.length;
        var data_proveedores = [];
        for (var i = 0; i < rowscount_proveedores; i++) {
            data_proveedores[i] = $('#jqxgrid_proveedores').jqxGrid('getrowdata', i);
        }
        var rows_bodegas = $('#jqxgrid_bodegas').jqxGrid('getrows');
        var rowscount_bodegas = rows_bodegas.length;
        var data_bodegas = [];
        for (var i = 0; i < rowscount_bodegas; i++) {
            data_bodegas[i] = $('#jqxgrid_bodegas').jqxGrid('getrowdata', i);
        }
//validar campos
//        if (opeNombre == '') {
//            jAlert('Ingrese Nombre Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#opeNombre").focus();});
//            return false;
//        }
//        if (opeDescripcion == '') {
//            jAlert('Ingrese Descripcion.', $.ucwords(_etiqueta_modulo), function () {$("#opeDescripcion").focus();});
//            return false;
//        }
//        if (opeCoordinador == '') {
//            jAlert('Seleccione Coordinador de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#opeCoordinador").focus();});
//            return false;
//        }
//        if (opeJefe == '') {
//            jAlert('Seleccione Jefe de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#opeJefe").focus();});
//            return false;
//        }
//        if (listrosMax == '') {
//            jAlert('Ingrese el limite de litros de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#listrosMax").focus();});
//            return false;
//        }
//        if (maxHectareas == '') {
//            jAlert('Seleccione el maximo de hectareas permitido.', $.ucwords(_etiqueta_modulo), function () {$("#maxHectareas").focus();});
//            return false;
//        }
//        if (opeProveedores == '') {
//            jAlert('Seleccione proveedor/es.', $.ucwords(_etiqueta_modulo), function () {$("#maxHectareas").focus();});
//            return false;
//        }
//        if (opeBodega == '') {
//            jAlert('Seleccione bodega/s.', $.ucwords(_etiqueta_modulo), function () {$("#maxHectareas").focus();});
//            return false;
//        }

        $.ajax({
            url: _compravino.URL + "/x_updateOperatoria",
            data: {
                nuevoID: el_id,
                opeNombre: opeNombre,
                opeDescripcion: opeDescripcion,
                opeCoordinador: opeCoordinador,
                opeJefe: opeJefe,
                listrosMax: listrosMax,
                opePrecio1: opePrecio1,
                opePrecio2: opePrecio2,
                opePrecio3: opePrecio3,
                opePrecio4: opePrecio4,
                opePrecio5: opePrecio5,
                opePrecio6: opePrecio6
            },
            dataType: "json",
            type: "post",
            success: function (data) {
            }
        });
        $.ajax({
            url: _compravino.URL + "/x_updateProveedores",
            data: {
                data_proveedores: data_proveedores,
                nuevoID: el_id
            },
            dataType: "json",
            type: "post",
            success: function (data) {
            }
        });
        $.ajax({
            url: _compravino.URL + "/x_updateBodegas",
            data: {
                data_bodegas: data_bodegas,
                nuevoID: el_id
            },
            dataType: "json",
            type: "post",
            success: function (data) {
            }
        });
    });
    var accion_proveedores_new = '';
    var id_proveedor = $('#opeProveedores').val();
    $("#info-proveedores").show();
    $("#jqxgrid_proveedores").show();
    var sourceope = {
        datatype: "json",
        datafields: [
            {name: 'ID', type: 'int'},
            {name: 'RAZON_SOCIAL', type: 'string'}
        ],
        url: 'general/extends/extra/carpetas.php',
        data: {
            accion: "getProveedoresGrilla",
            id_proveedor: id_proveedor,
        },
        async: false,
        addrow: function (rowid, rowdata, position, commit) {
            commit(true);
        },
        deleterow: function (rowid, commit) {
            commit(true);
        },
        updaterow: function (rowid, newdata, commit) {
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
    var generaterow_proveedores = function (i) {
        var row = {};
        var ids_proveedores = $("#opeProveedores").val();
        var firstColumnData = [];
        var rows = $('#jqxgrid_proveedores').jqxGrid('getrows');
        for (var i = 0; i < rows.length; i++) {
            firstColumnData.push(rows[i].ID);
        }
        $.ajax({
            url: _compravino.URL + "/x_getDatoProveedor",
            data: {
                ids_proveedores: ids_proveedores,
                firstColumnData: firstColumnData
            },
            dataType: "json",
            type: "post",
            async: false,
            success: function (datos) {
                accion_proveedores_new = datos[0]['ACCION'];
                console.log("1");
                for (var i = 0; i < datos.length; i++) {
                    row['ID'] = datos[i]['ID'];
                    row['RAZON_SOCIAL'] = datos[i]['RAZON_SOCIAL'];
                    row['LIMLTRS'] = '0';
                    row['MAXHECTAREAS'] = '0';
                }
            }
        });
        return row;
    }
    $("#jqxgrid_proveedores").jqxGrid({
        width: '90%',
        height: '200px',
        source: dataAdapterope,
        theme: 'energyblue',
        showstatusbar: true,
//            showaggregates: true,
        editable: true,
        selectionmode: 'singlerows',
        localization: getLocalization(),
        rendertoolbar: function (toolbar) {
            var me = this;
            var container = $("<div style='margin: 5px;'></div>");
            toolbar.append(container);
            $("#addrowbutton").jqxButton();
            $("#addmultiplerowsbutton").jqxButton();
            $("#deleterowbutton").jqxButton();
            $("#updaterowbutton").jqxButton();
            $("#updaterowbutton").on('click', function () {
                var datarow = generaterow_proveedores();
                var selectedrowindex = $("#jqxgrid_proveedores").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_proveedores").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                    var id = $("#jqxgrid_proveedores").jqxGrid('getrowid', selectedrowindex);
                    var commit = $("#jqxgrid_proveedores").jqxGrid('updaterow', id, datarow);
                    $("#jqxgrid_proveedores").jqxGrid('ensurerowvisible', selectedrowindex);
                }
            });
            $('#opeProveedores').on('change', function () {
                var datarow = generaterow_proveedores();
                if (accion_proveedores_new == 'AGREGAR') {
                    var commit = $("#jqxgrid_proveedores").jqxGrid('addrow', null, datarow);
                } else if (accion_proveedores_new == 'ELIMINAR') {
                    var posicion = 0;
                    var rows = $('#jqxgrid_proveedores').jqxGrid('getrows');
                    for (var j = 0; j < rows.length; j++) {
                        if (rows[j]['ID'] == datarow.ID) {
                            posicion = j;
                            break;
                        }
                    }
                    var id = $("#jqxgrid_proveedores").jqxGrid('getrowid', posicion);
                    var commit = $("#jqxgrid_proveedores").jqxGrid('deleterow', id);
                }
            });
            // delete row.
            $("#deleterowbutton").on('click', function () {
                var selectedrowindex = $("#jqxgrid_proveedores").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_proveedores").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                    var id = $("#jqxgrid_proveedores").jqxGrid('getrowid', selectedrowindex);
                    var commit = $("#jqxgrid_proveedores").jqxGrid('deleterow', id);
                    var firstColumnData = [];
                    var rows = $('#jqxgrid_proveedores').jqxGrid('getrows');
                    for (var i = 0; i < rows.length; i++) {
                        firstColumnData.push(rows[i].ID);
                    }
                    $("#opeProveedores").val(firstColumnData).attr('eneable', true).trigger("chosen:updated");
                }
            });
        },
        columns: [
            {text: 'ID', datafield: 'ID', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, hidden: true},
            {text: 'RAZON SOCIAL', datafield: 'RAZON_SOCIAL', width: '50%', cellsalign: 'left', filtercondition: 'starts_with', editable: false},
            {text: 'LIMITE LTRS', datafield: 'LIMLTRS', cellsalign: 'left', width: '25%', filtercondition: 'starts_with', editable: true},
            {text: 'MAX. HECTAREAS', datafield: 'MAXHECTAREAS', cellsalign: 'left', width: '25%', filtercondition: 'starts_with', editable: true},
        ]
    });
    var accion_bodegas_new = '';
    var id_bodega = $('#opeBodega').val();
    $("#info-bodegas").show();
    $("#jqxgrid_bodegas").show();
    var sourceope = {
        datatype: "json",
        datafields: [
            {name: 'ID', type: 'int'},
            {name: 'NOMBRE', type: 'string'}
        ],
        url: 'general/extends/extra/carpetas.php',
        data: {
            accion: "getBodegasGrilla",
            id_bodega: id_bodega,
        },
        async: false,
        addrow: function (rowid, rowdata, position, commit) {
            commit(true);
        },
        deleterow: function (rowid, commit) {
            commit(true);
        },
        updaterow: function (rowid, newdata, commit) {
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
    var generaterow_bodegas = function (i) {
        var row = {};
        var ids_bodegas = $("#opeBodega").val();
        var firstColumnData = [];
        var rows = $('#jqxgrid_bodegas').jqxGrid('getrows');
        for (var i = 0; i < rows.length; i++) {
            firstColumnData.push(rows[i].ID);
        }
        $.ajax({
            url: _compravino.URL + "/x_getDatoBodega",
            data: {
                ids_bodegas: ids_bodegas,
                firstColumnData: firstColumnData
            },
            dataType: "json",
            type: "post",
            async: false,
            success: function (datos) {
                accion_bodegas_new = datos[0]['ACCION'];
                console.log("1");
                for (var i = 0; i < datos.length; i++) {
                    row['ID'] = datos[i]['ID'];
                    row['NOMBRE'] = datos[i]['NOMBRE'];
                    row['LIMLTRS'] = '0';
                }
            }
        });
        return row;
    }
    $("#jqxgrid_bodegas").jqxGrid({
        width: '70%',
        height: '200px',
        source: dataAdapterope,
        theme: 'energyblue',
        showstatusbar: true,
        editable: true,
        selectionmode: 'singlerows',
        localization: getLocalization(),
        rendertoolbar: function (toolbar) {
            var me = this;
            var container = $("<div style='margin: 5px;'></div>");
            toolbar.append(container);
            $("#addrowbutton").jqxButton();
            $("#addmultiplerowsbutton").jqxButton();
            $("#deleterowbutton").jqxButton();
            $("#updaterowbutton").jqxButton();
            $("#updaterowbutton").on('click', function () {
                var datarow = generaterow_bodegas();
                var selectedrowindex = $("#jqxgrid_bodegas").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_bodegas").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                    var id = $("#jqxgrid_bodegas").jqxGrid('getrowid', selectedrowindex);
                    var commit = $("#jqxgrid_bodegas").jqxGrid('updaterow', id, datarow);
                    $("#jqxgrid_bodegas").jqxGrid('ensurerowvisible', selectedrowindex);
                }
            });
            $('#opeBodega').on('change', function () {
                var datarow = generaterow_bodegas();
                if (accion_bodegas_new == 'AGREGAR') {
                    var commit = $("#jqxgrid_bodegas").jqxGrid('addrow', null, datarow);
                } else if (accion_bodegas_new == 'ELIMINAR') {
                    var posicion = 0;
                    var rows = $('#jqxgrid_bodegas').jqxGrid('getrows');
                    for (var j = 0; j < rows.length; j++) {
                        if (rows[j]['ID'] == datarow.ID) {
                            posicion = j;
                            break;
                        }
                    }
                    var id = $("#jqxgrid_bodegas").jqxGrid('getrowid', posicion);
                    var commit = $("#jqxgrid_bodegas").jqxGrid('deleterow', id);
                }
            });
            // delete row.
            $("#deleterowbutton").on('click', function () {
                var selectedrowindex = $("#jqxgrid_bodegas").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_bodegas").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                    var id = $("#jqxgrid_bodegas").jqxGrid('getrowid', selectedrowindex);
                    var commit = $("#jqxgrid_bodegas").jqxGrid('deleterow', id);
                    var firstColumnData = [];
                    var rows = $('#jqxgrid_bodegas').jqxGrid('getrows');
                    for (var i = 0; i < rows.length; i++) {
                        firstColumnData.push(rows[i].ID);
                    }
                    $("#opeBodega").val(firstColumnData).attr('eneable', true).trigger("chosen:updated");
                }
            });
        },
        columns: [
            {text: 'ID', datafield: 'ID', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, hidden: true},
            {text: 'NOMBRE', datafield: 'NOMBRE', width: '60%', cellsalign: 'left', filtercondition: 'starts_with', editable: false},
            {text: 'LIMITE LTRS', datafield: 'LIMLTRS', cellsalign: 'left', width: '40%', filtercondition: 'starts_with', editable: true},
        ]
    });
// ESTO SERIA PARA GENERAR LOS CHECKLIST Y MOSTRARLOS
//    $.ajax({
//        datatype: "json",url: 'general/extends/extra/operatorias.php',
//        data: {accion: "getOperatoriasChecklistHumana",seleccion: $('#tipoPersona').val()},
//        async: false,success: function (data) {
//            var formularios = "";var cantidad_check = data.length;console.log("cantidad_check");console.log(cantidad_check);
////                    alert(data);
////                    for (var i = 0; i < cantidad_check; i++) {
////////                        console.log(datosBodegas);
////formularios = formularios + "<td></td>"
////+ "<td> <select id='' name=''><option value='SI'>SI</option><option value='NO'>NO</option><option value='NC'>N/C</option></select></td>";
////                        asignacion_x_bodega.push("litros_cargados_" + data[i].ID);
////                    }$('#campos_ver').html(formularios);}});
//    $.ajax({url: _compravino.URL + "/x_getChecklistHumana",data: {seleccion: $('#tipoPersona').val()},
//            type: "post",async: false,success: function (datos) {console.log("DATOS CHECK");console.log(datos);}
//        });
//    var sourcechk_humana =
//            {datatype: "json",url: 'general/extends/extra/operatorias.php',
//                data: {accion: "getOperatoriasChecklistHumana",seleccion: $('#tipoPersona').val()},
//                async: false,datafields: [{name: 'ID'},{name: 'DESCRIPCION'},],
//                id: 'ID'};
//    var dataAdapterchk_humana = new $.jqx.dataAdapter(sourcechk_humana);
////    $("#listbox_humana").jqxListBox({source: dataAdapterchk_humana, checkboxes: true, displayMember: "DESCRIPCION", valueMember: "ID", width: 760, height: 300});
////    $("#jqxgrid_humana").jqxGrid({source: dataAdapterchk_humana, checkboxes: true, displayMember: "DESCRIPCION", valueMember: "ID", width: 760, height: 300});
//    $("#jqxgrid_humana").jqxGrid({
//        width: '90%',height: '400px',source: dataAdapterchk_humana,autoheight: true,editable: true,selectionmode: 'singlecell',
//        columns: [
//            {text: 'ID', datafield: 'ID', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, hidden: true},
//            {text: 'DESCRIPCION', datafield: 'DESCRIPCION', width: '90%', cellsalign: 'left', filtercondition: 'starts_with', editable: false},
//        ]});
//        var sourcechk_juridica ={datatype: "json",url: 'general/extends/extra/operatorias.php',data: {
//                    accion: "getOperatoriasChecklistJuridica",seleccion: $('#tipoPersona').val()},
//                async: false,datafields: [{name: 'ID'},{name: 'DESCRIPCION'}],id: 'ID'};
//    var dataAdapterchk_juridica = new $.jqx.dataAdapter(sourcechk_juridica);
//    $("#listbox_juridica").jqxListBox({source: dataAdapterchk_juridica, checkboxes: true, displayMember: "DESCRIPCION", valueMember: "ID", width: 760, height: 300});
// AQUI TERMINARIA LA LISTA PARA GENERAR LOS CHECKLIST Y MOSTRARLOS

    $(".toolbar li:not(.sub)").click(function (e) {
        e.preventDefault();
        var top = $(this).data('top');
        var obj = [];
        if (top == 'search') {
            $('.consultar').trigger('click');
        } else if (top == 'nueva_f') {
            $('#nuevafactura').trigger('click');
        } else if (top == 'lis_editar') {
            show_btns();
        } else if (top == 'lis_guardar_enviar') {
            $('#send').trigger('click');
        } else if (top == 'lis_guardar_fact') {
            guardar_factura();
        } else if (top == 'lis_mendoza') {
            var urlh = "backend/carpeta/compravino/init/12";
            $(location).attr('href', urlh);
        } else if (top == 'lis_sanjuan') {
            var urlh = "backend/carpeta/compravino/init/17";
            $(location).attr('href', urlh);
        } else if (top == 'lis_addnf') {

            if (_permiso_alta == 0) {

                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }

            var urlh = "backend/carpeta/compravino/init/" + _provincia + "/1";
            $(location).attr('href', urlh);
        } else if (top == 'addOpe') {
            if (_permiso_alta == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
//            show_btns(7);
            var urlh = "backend/carpeta/compravino/init/" + _provincia + "/8";
            $(location).attr('href', urlh);
        } else if (top == 'lis_lis') {
            if (_permiso_ver == 0) {

                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            var urlh = "backend/carpeta/compravino/init/" + _provincia + "/2";
            $(location).attr('href', urlh);
        } else if (top == 'lis_ope') {
            if (_permiso_ver == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            var urlh = "backend/carpeta/compravino/init/" + _provincia + "/7";
            $(location).attr('href', urlh);
        } else if (top == 'inicio') {
            var urlh = "backend/carpeta/compravino";
            $(location).attr('href', urlh);
        } else if (top == 'edi') {
            if (_permiso_modificacion == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            editar_factura();
        } else if (top == 'edi_ope') {
            if (_permiso_modificacion == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            editar_operatoria();
        } else if (top == 'edi_rev') {
            if (_permiso_modificacion == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            editar_factura('jqxgrid_listado_revision');
        } else if (top == 'pago') {
            if (_permiso_exportar == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            lote_pago();
        } else if (top == 'export') {
//imprimir_listado_seleccionado();
        } else if (top == 'lis_importar') {
            if (_permiso_alta == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }

            var urlh = "backend/carpeta/compravino/init/3";
            $(location).attr('href', urlh);
        } else if (top == 'impor_procesar') {
            importar_procesar();
        } else if (top == 'impor_revision') {
            var urlh = "backend/carpeta/compravino/init/4";
            $(location).attr('href', urlh);
        } else if (top == 'addOpe') {
            $('.nuevaOpe_form').show();
        } else if (top == 'testing') {

            $.ajax({
                url: _compravino.URL + "/x_testing",
                data: {
                },
                dataType: "json",
                type: "post",
                success: function (data) {

//                    alert(data);

                }
            });
        }
    });
    initGrid();
    $("#cuit_busqueda").focus();
    $("#cuit_busqueda").numeric({negative: false});
    agregarCIUS();
    show_btns();
    $("#kgrs").numeric({negative: false});
    $("#azucar").numeric({negative: false});
    $("#precio").numeric({negative: false});
    $("#numero").numeric({negative: false});
    $("#numero").numeric({negative: false});
    $("#cuit").numeric({negative: false, decimal: false});
    $("#cbu").numeric({negative: false, decimal: false});
    init_datepicker('#fecha', '-3', '+5', '0', 0);
    init_datepicker('#fechavto', '-3', '+5', '0', 0);
    $("input[type=file]").change(function () {
        $(this).parents(".uploader").find(".filename").val($(this).val());
    });
    $("input[type=file]").each(function () {
        if ($(this).val() == "") {
            $(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");
        }
    });
    //$("#ciu_num").numeric({negative: false});
//    $("#ciu_azucar").numeric({negative: false});
//    $("#ciu_kgrs").numeric({negative: false});
    $("#precio").keyup(function () {
        if ($(this).val() == 0) {
            $("#neto").val(0);
        } else {
//var porc = var_cliente.VALOR;
//condicioniva_g
            var factor = 0;
            if (condicioniva_g >= 0) {
                factor = condicioniva_g;
            } else {
                factor = var_cliente.VALOR;
            }
            var neto = $("#ltros").val() * $(this).val();
            $("#neto").val(dec(neto, 2));
            var iva = factor * $("#neto").val() / 100;
            $("#iva").val(dec(iva, 2));
            var total = 1 * $("#neto").val() + 1 * $("#iva").val();
            $("#total").val(dec(total, 2));
        }
    });
//     $("#precio").keyup(function () {
//        if ($(this).val() == 0) {
//            $("#neto").val(0);
//        } else {
//            //var porc = var_cliente.VALOR;
//            //condicioniva_g
//            var factor = 0;
//            if (condicioniva_g >= 0) {
//                factor = condicioniva_g;
//            } else {
//                factor = var_cliente.VALOR;
//            }
//            var neto = $("#kgrs").val() * $(this).val();
//            $("#neto").val(dec(neto, 2));
//            factor = $('#porcentaje_iva').val();
//            var iva = factor * $("#neto").val() / 100;
//            $("#iva").val(dec(iva, 2));
//            var total = 1 * $("#neto").val() + 1 * $("#iva").val();
//            $("#total").val(dec(total, 2));
//        }
//    });

    $("#porcentaje_iva").keyup(function () {
        factor = $('#porcentaje_iva').val();
        var iva = factor * $("#neto").val() / 100;
        $("#iva").val(dec(iva, 2));

        var total = 1 * $("#neto").val() + 1 * $("#iva").val();
        $("#total").val(dec(total, 2));
    });
    
    $("#cbu").focusout(function () {
//verificar cbu
        var cbu = $(this).val();
        $.ajax({
            url: _compravino.URL + "/x_verificarcbu",
            data: {
                cbu: cbu
            },
            dataType: "json",
            type: "post",
            success: function (datos) {

                if (datos.length > 0) {
                    var cadcli = '';
                    $.each(datos, function (index, value) {
                        cadcli += value.RAZON_SOCIAL + '(' + value.CUIT + '), ';
                    });
                    cadcli = cadcli.substring(0, cadcli.length - 2);
                    jAlert('Este cbu esta asociado a mas clientes. ' + cadcli, $.ucwords(_etiqueta_modulo), function () {
                        $("#cbu").focus();
                    });
                }
            }
        });
    })

    if (_opcion == 3) {
//no buscar, entrar directamente al formulario con los datos cargados
        $('.env_form').hide();
        $('.nuevafact_form').show();
        $("#nombre2").val($("#nombre").val());
        $("#cuitform").val($("#cuit_busqueda").val());
        show_btns(2);
        editar_formulario();
    }
    if (_opcion == 9) {
        editar_formulario_operatoria();
    }

    evento_lista_arch();
});
function verPersona() {
    if ($("#tipoPersona").val() == 'Humana') {
        $("#humana").show();
        $("#juridica").hide();
    } else {
        $("#humana").hide();
        $("#juridica").show();
    }
}

function evento_lista_arch() {
    $('.lista_adjuntos li span').off().on('click', function (event) {
        event.preventDefault();
        var myobj = $(this)
        jConfirm('Esta seguro de borrar este archivo??.', $.ucwords(_etiqueta_modulo), function (r) {
            if (r == true) {
                $this = myobj.parent();
                var ruta = myobj.prev().attr('href');
                //x_borrar_file
                $.ajax({
                    url: _compravino.URL + "/x_borrar_file",
                    data: {
                        ruta: ruta
                    },
                    dataType: "json",
                    type: "post",
                    success: function () {
                        $this.remove();
                    }
                });
            }
        });
    });
}


function editar_formulario() {

//alert('editar:: ' + _id_objeto)
    $.ajax({
        url: _compravino.URL + "/x_getobj",
        data: {
            id_objeto: _id_objeto
        },
        dataType: "json",
        type: "post",
        success: function (rtn) {

            var arr_cius = rtn.cius;
            data = rtn.factura;
            console.dir(data);
            $("#idh").val(data.ID);
            $("#cuitform").val(data.CUIT);
            $("#cuitform").val(data.CUIT);
            $("#nombre2").val(data.RAZ);
            $("#fecha").val(formattedDate(data.FECHA));
            $("#fechavto").val(formattedDate(data.FECHAVTO));
            $("#fecha").datepicker('disable');
            $("#fechavto").datepicker('disable');
            $("#numero").val(data.NUMERO).attr("readonly", "readonly");
            $("#cai").val(data.CAI).attr("readonly", "readonly");
            $("#bodega").chosen({width: "220px"});
            $("#bodega").val(data.ID_BODEGA).attr('disabled', true).trigger("chosen:updated");
            $("#bodega").trigger('change');
            $("#formula").chosen({width: "220px"});
            $("#formula").val(data.FORMULA).attr('disabled', true).trigger("chosen:updated");
            $("#formula").trigger('change');
            $("#kgrs").val(data.KGRS);
            $("#azucar").val(data.AZUCAR);
            $("#precio").val(data.PRECIO);
            $("#observacion_fact").val(data.OBSERVACIONES);
            $("#neto").val(data.NETO).attr("readonly", "readonly");
            $("#iva").val(data.IVA).attr("readonly", "readonly");
            $("#total").val(data.TOTAL).attr("readonly", "readonly");
            if (arr_cius.length > 0) {
                //colocar
                $.each(arr_cius, function (k, v) {
                    var data = {
                        'NUM': v.NUMERO,
                        'KGRS': v.KGRS,
                        'AZUCAR': v.AZUCAR,
                        'CHEQUEO': v.VERIFICADO,
                        'INSC': v.INSC,
                        'ID': 'DDDDDDD',
                        'uid': 1
                    }
                    var commit = $("#jqxgridcius").jqxGrid('addrow', null, data);
                    $('#jqxgridcius').jqxGrid('selectrow', data.uid);
                    var selectedrowindex = $("#jqxgridcius").jqxGrid('getselectedrowindex');
                });
            }
        }
    });
}
function editar_formulario_operatoria() {
    var accion_proveedores = '';
    var accion_bodegas = '';
    var url_con_id = document.location.href;
    var ultimo_id = url_con_id.split("/");
    var el_id = ultimo_id[ultimo_id.length - 1];
    $('#send').hide();
    $('#send_edit').show();
    $('#fecha_ven_edit').show();
    $.ajax({
        url: _compravino.URL + "/x_getoperatoria",
        data: {
            id_objeto: el_id
        },
        dataType: "json",
        type: "post",
        success: function (rtn) {
            $("#opeNombre").val(rtn[0].NOMBRE_OPE);
            $("#fechavto").datepicker('enable');
            $("#fechavto").val(formattedDate(rtn[0].FECHA_VEN));
            $("#opeDescripcion").val(rtn[0].DESCRIPCION_OPE);
            $("#listrosMax").val(rtn[0].LTRS_MAX);
            $("#opeCoordinador").val(rtn[0].ID_COORDINADOR_OPE).attr('eneable', true).trigger("chosen:updated");
            $("#opeJefe").val(rtn[0].ID_JEFE_OPE).attr('eneable', true).trigger("chosen:updated");
            $("#opeP1").val(rtn[0].PRECIO_1);
            $("#opeP2").val(rtn[0].PRECIO_2);
            $("#opeP3").val(rtn[0].PRECIO_3);
            $("#opeP4").val(rtn[0].PRECIO_4);
            $("#opeP5").val(rtn[0].PRECIO_5);
            $("#opeP6").val(rtn[0].PRECIO_6);
            $("#opePCuota").val(rtn[0].PRECIO_CUOTA);
            $("#tipoPersona").val(rtn[0].PERSONA);
            $("#opeTitular").val(rtn[0].TITULAR);
            $("#opeCuit").val(rtn[0].CUIT);
            $("#numVinedo").val(rtn[0].NUM_VINEDO);
            $("#litrosOfrecidos").val(rtn[0].LITROS_OFRECIDOS);
            $("#hectDeclaradas").val(rtn[0].HECT_DECLARADAS);
            $("#bgaDep").val(rtn[0].BGA_DEP);
            $("#deptBodega").val(rtn[0].DEPT_BODEGA);
            $("#numINVBodega").val(rtn[0].NUM_INV_BODEGA);
            $("#opetelefono").val(rtn[0].TELEFONO);
            $("#opeCorreo").val(rtn[0].CORREO);
            $.ajax({
                url: _compravino.URL + "/x_getOperatoriaProveedores",
                data: {
                    id_operatoria: el_id
                },
                dataType: "json",
                type: "post",
                success: function (rtn_proveedores) {
                    var cadena_ids = [];
                    for (var i = 0; i < rtn_proveedores.length; i++) {
                        cadena_ids.push(rtn_proveedores[i]['ID_PROVEEDOR']);
                    }
                    $("#opeProveedores").val(cadena_ids).attr('eneable', true).trigger("chosen:updated");
                    $("#info-proveedores").show();
                    $("#jqxgrid_proveedores").show();
                    var sourceope_proveedores = {
                        datatype: "json",
                        type: "post",
                        datafields: [
                            {name: 'ID', type: 'int'},
                            {name: 'RAZON_SOCIAL', type: 'string'},
                            {name: 'LIMLTRS', type: 'number'},
                            {name: 'MAXHECTAREAS', type: 'number'}
                        ],
                        url: _compravino.URL + "/x_getProveedoresEdit",
                        data: {
                            id_operatoria: el_id
                        },
                        async: false,
                        addrow: function (rowid, rowdata, position, commit) {
                            commit(true);
                        },
                        deleterow: function (rowid, commit) {
                            commit(true);
                        },
                        updaterow: function (rowid, newdata, commit) {
                            commit(true);
                        }
                    };
                    var dataAdapterope_proveedores = new $.jqx.dataAdapter(sourceope_proveedores,
                            {
                                formatData: function (data) {
                                    data.name_startsWith = $("#searchField").val();
                                    return data;
                                }
                            }
                    );
                    var generaterow_proveedores = function (i) {
                        var row = {};
                        var ids_proveedores = $("#opeProveedores").val();
                        var firstColumnData = [];
                        var rows = $('#jqxgrid_proveedores').jqxGrid('getrows');
                        for (var i = 0; i < rows.length; i++) {
                            firstColumnData.push(rows[i].ID);
                        }
                        $.ajax({
                            url: _compravino.URL + "/x_getDatoProveedor",
                            data: {
                                ids_proveedores: ids_proveedores,
                                firstColumnData: firstColumnData
                            },
                            dataType: "json",
                            type: "post",
                            async: false,
                            success: function (datos) {
                                accion_proveedores = datos[0]['ACCION'];
                                console.log("1");
                                for (var i = 0; i < datos.length; i++) {
                                    row['ID'] = datos[i]['ID'];
                                    row['RAZON_SOCIAL'] = datos[i]['RAZON_SOCIAL'];
                                    row['LIMLTRS'] = '0';
                                    row['MAXHECTAREAS'] = '0';
                                }
                            }
                        });
                        return row;
                    }
                    $("#jqxgrid_proveedores").jqxGrid({
                        width: '70%',
                        height: '200px',
                        source: dataAdapterope_proveedores,
                        theme: 'energyblue',
                        editable: true,
                        selectionmode: 'singlerows',
                        localization: getLocalization(),
                        rendertoolbar: function (toolbar) {
                            var me = this;
                            var container = $("<div style='margin: 5px;'></div>");
                            toolbar.append(container);
//                            container.append('<input id="addrowbutton" type="button" value="Agregar Nuevos" />');
//                            container.append('<input style="margin-left: 5px;" id="deleterowbutton" type="button" value="Eliminar Seleccion" />');
                            $("#addrowbutton").jqxButton();
                            $("#addmultiplerowsbutton").jqxButton();
                            $("#deleterowbutton").jqxButton();
                            $("#updaterowbutton").jqxButton();
                            $("#updaterowbutton").on('click', function () {
                                var datarow = generaterow_proveedores();
                                var selectedrowindex = $("#jqxgrid_proveedores").jqxGrid('getselectedrowindex');
                                var rowscount = $("#jqxgrid_proveedores").jqxGrid('getdatainformation').rowscount;
                                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                    var id = $("#jqxgrid_proveedores").jqxGrid('getrowid', selectedrowindex);
                                    var commit = $("#jqxgrid_proveedores").jqxGrid('updaterow', id, datarow);
                                    $("#jqxgrid_proveedores").jqxGrid('ensurerowvisible', selectedrowindex);
                                }
                            });
                            $('#opeProveedores').on('change', function () {
                                var datarow = generaterow_proveedores();
                                if (accion_proveedores == 'AGREGAR') {
                                    var commit = $("#jqxgrid_proveedores").jqxGrid('addrow', null, datarow);
                                } else if (accion_proveedores == 'ELIMINAR') {
                                    var posicion = 0;
                                    var rows = $('#jqxgrid_proveedores').jqxGrid('getrows');
                                    for (var j = 0; j < rows.length; j++) {
                                        if (rows[j]['ID'] == datarow.ID) {
                                            posicion = j;
                                            break;
                                        }
                                    }
                                    var id = $("#jqxgrid_proveedores").jqxGrid('getrowid', posicion);
                                    var commit = $("#jqxgrid_proveedores").jqxGrid('deleterow', id);
                                }
                            });
                            // delete row.
                            $("#deleterowbutton").on('click', function () {
                                var selectedrowindex = $("#jqxgrid_proveedores").jqxGrid('getselectedrowindex');
                                var rowscount = $("#jqxgrid_proveedores").jqxGrid('getdatainformation').rowscount;
                                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                    var id = $("#jqxgrid_proveedores").jqxGrid('getrowid', selectedrowindex);
                                    var commit = $("#jqxgrid_proveedores").jqxGrid('deleterow', id);
                                    var firstColumnData = [];
                                    var rows = $('#jqxgrid_proveedores').jqxGrid('getrows');
                                    for (var i = 0; i < rows.length; i++) {
                                        firstColumnData.push(rows[i].ID);
                                    }
                                    $("#opeProveedores").val(firstColumnData).attr('eneable', true).trigger("chosen:updated");
                                }
                            });
                        },
                        columns: [
                            {text: 'ID', datafield: 'ID', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, hidden: true},
                            {text: 'RAZON SOCIAL', datafield: 'RAZON_SOCIAL', width: '50%', cellsalign: 'left', filtercondition: 'starts_with', editable: false},
                            {text: 'LIMITE LTRS', datafield: 'LIMLTRS', cellsalign: 'left', width: '25%', filtercondition: 'starts_with', editable: true},
                            {text: 'MAX. HECTAREAS', datafield: 'MAXHECTAREAS', cellsalign: 'left', width: '25%', filtercondition: 'starts_with', editable: true},
                        ]
                    });
                }
            });
            $.ajax({
                url: _compravino.URL + "/x_getOperatoriaBodegas",
                data: {
                    id_operatoria: el_id
                },
                dataType: "json",
                type: "post",
                success: function (rtn_bodegas) {
                    var cadena_ids = [];
                    for (var i = 0; i < rtn_bodegas.length; i++) {
                        cadena_ids.push(rtn_bodegas[i]['ID_BODEGA']);
                    }
                    $("#opeBodega").val(cadena_ids).attr('eneable', true).trigger("chosen:updated");
                    $("#info-bodegas").show();
                    $("#jqxgrid_bodegas").show();
                    var sourceope_bodegas = {
                        datatype: "json",
                        type: "post",
                        datafields: [
                            {name: 'ID', type: 'int'}, {name: 'NOMBRE', type: 'string'}, {name: 'LIMLTRS', type: 'number'}
                        ],
                        url: _compravino.URL + "/x_getBodegasEdit",
                        data: {
                            id_operatoria: el_id
                        },
                        async: false,
                        addrow: function (rowid, rowdata, position, commit) {
                            commit(true);
                        },
                        deleterow: function (rowid, commit) {
                            commit(true);
                        },
                        updaterow: function (rowid, newdata, commit) {
                            commit(true);
                        }
                    };
                    var dataAdapterope_bodegas = new $.jqx.dataAdapter(sourceope_bodegas,
                            {
                                formatData: function (data) {
                                    data.name_startsWith = $("#searchField").val();
                                    return data;
                                }
                            }
                    );
                    var generaterow_bodegas = function (i) {
                        var row = {};
                        var ids_bodegas = $("#opeBodega").val();
                        var firstColumnData = [];
                        var rows = $('#jqxgrid_bodegas').jqxGrid('getrows');
                        for (var i = 0; i < rows.length; i++) {
                            firstColumnData.push(rows[i].ID);
                        }
                        $.ajax({
                            url: _compravino.URL + "/x_getDatoBodega",
                            data: {
                                ids_bodegas: ids_bodegas,
                                firstColumnData: firstColumnData
                            },
                            dataType: "json",
                            type: "post",
                            async: false,
                            success: function (datos) {
                                accion_bodegas = datos[0]['ACCION'];
                                console.log("1");
                                for (var i = 0; i < datos.length; i++) {
                                    row['ID'] = datos[i]['ID'];
                                    row['NOMBRE'] = datos[i]['NOMBRE'];
                                    row['LIMLTRS'] = '0';
                                }
                            }
                        });
                        return row;
                    }
                    $("#jqxgrid_bodegas").jqxGrid({
                        width: '90%',
                        height: '200px',
                        source: dataAdapterope_bodegas,
                        theme: 'energyblue',
                        editable: true,
                        selectionmode: 'singlerows',
                        localization: getLocalization(),
                        rendertoolbar: function (toolbar) {
                            var me = this;
                            var container = $("<div style='margin: 5px;'></div>");
                            toolbar.append(container);
//                            container.append('<input id="addrowbutton" type="button" value="Agregar Nuevos" />');
//                            container.append('<input style="margin-left: 5px;" id="deleterowbutton" type="button" value="Eliminar Seleccion" />');
                            $("#addrowbutton").jqxButton();
                            $("#addmultiplerowsbutton").jqxButton();
                            $("#deleterowbutton").jqxButton();
                            $("#updaterowbutton").jqxButton();
                            $("#updaterowbutton").on('click', function () {
                                var datarow = generaterow_bodegas();
                                var selectedrowindex = $("#jqxgrid_bodegas").jqxGrid('getselectedrowindex');
                                var rowscount = $("#jqxgrid_bodegas").jqxGrid('getdatainformation').rowscount;
                                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                    var id = $("#jqxgrid_bodegas").jqxGrid('getrowid', selectedrowindex);
                                    var commit = $("#jqxgrid_bodegas").jqxGrid('updaterow', id, datarow);
                                    $("#jqxgrid_bodegas").jqxGrid('ensurerowvisible', selectedrowindex);
                                }
                            });
                            $('#opeBodega').on('change', function () {
                                var datarow = generaterow_bodegas();
                                if (accion_bodegas == 'AGREGAR') {
                                    var commit = $("#jqxgrid_bodegas").jqxGrid('addrow', null, datarow);
                                } else if (accion_bodegas == 'ELIMINAR') {
                                    var posicion = 0;
                                    var rows = $('#jqxgrid_bodegas').jqxGrid('getrows');
                                    for (var j = 0; j < rows.length; j++) {
                                        if (rows[j]['ID'] == datarow.ID) {
                                            posicion = j;
                                            break;
                                        }
                                    }
                                    var id = $("#jqxgrid_bodegas").jqxGrid('getrowid', posicion);
                                    var commit = $("#jqxgrid_bodegas").jqxGrid('deleterow', id);
                                }
                            });
                            // delete row.
                            $("#deleterowbutton").on('click', function () {
                                var selectedrowindex = $("#jqxgrid_bodegas").jqxGrid('getselectedrowindex');
                                var rowscount = $("#jqxgrid_bodegas").jqxGrid('getdatainformation').rowscount;
                                if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                    var id = $("#jqxgrid_bodegas").jqxGrid('getrowid', selectedrowindex);
                                    var commit = $("#jqxgrid_bodegas").jqxGrid('deleterow', id);
                                    var firstColumnData = [];
                                    var rows = $('#jqxgrid_bodegas').jqxGrid('getrows');
                                    for (var i = 0; i < rows.length; i++) {
                                        firstColumnData.push(rows[i].ID);
                                    }
                                    $("#opeProveedores").val(firstColumnData).attr('eneable', true).trigger("chosen:updated");
                                    console.log("DATOS COLUMA");
                                    console.log(firstColumnData);
                                }
                            });
                        },
                        columns: [
                            {text: 'ID', datafield: 'ID', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, hidden: true},
                            {text: 'NOMBRE', datafield: 'NOMBRE', cellsalign: 'left', filtercondition: 'starts_with', editable: false},
                            {text: 'LIMITE LTRS', datafield: 'LIMLTRS', cellsalign: 'left', width: '30%', filtercondition: 'starts_with', editable: true}
                        ]
                    });
                }
            });
        }
    });
}


function agregarCIUS(_arr_cius) {

    _arr_cius || (_arr_cius = []);
    var source = {
        datatype: "json",
        datafields: [
            {name: 'NUM'},
            {name: 'KGRS', type: 'number'},
            {name: 'AZUCAR'},
            {name: 'CHEQUEO', type: 'bool'},
            {name: 'INSC'},
            {name: 'ID'}
        ],
        url: _compravino.URL + '/x_get_info_bancos',
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };
    $("#jqxgridcius").jqxGrid({
        width: '98%',
        height: '200px',
        source: source,
        theme: 'energyblue',
        editable: true,
        ready: function () {
            $("#jqxgridcius").jqxGrid('hidecolumn', 'ID');
            if (_arr_cius.length > 0) {
                //colocar
                $.each(_arr_cius, function (k, v) {
                    var data = {
                        'NUM': v.ciu_num,
                        'KGRS': v.ciu_kgrs,
                        'AZUCAR': v.ciu_azucar,
                        'CHEQUEO': v.ciu_chequeo,
                        'INSC': v.ciu_insc,
                        'ID': 'DDDDDDD',
                        'uid': 1
                    }
                    var commit = $("#jqxgridcius").jqxGrid('addrow', null, data);
                    $('#jqxgridcius').jqxGrid('selectrow', data.uid);
                    var selectedrowindex = $("#jqxgridcius").jqxGrid('getselectedrowindex');
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
            deleteButton.jqxButton({theme: theme, width: 65, height: 20});
            deleteButton.click(function (event) {
                var selectedrowindex = $("#jqxgridcius").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgridcius").jqxGrid('getdatainformation').rowscount;
                if (selectedrowindex < rowscount) {

                    jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo), function (r) {
                        if (r == true) {

                            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                var id = $("#jqxgridcius").jqxGrid('getrowid', selectedrowindex);
                                $("#jqxgridcius").jqxGrid('deleterow', id);
                            }

                            //actualizar suma
                            var griddata = $('#jqxgridcius').jqxGrid('getdatainformation');
                            var _arr_aportes_tmp = [];
                            for (var i = 0; i < griddata.rowscount; i++)
                                _arr_aportes_tmp.push($('#jqxgridcius').jqxGrid('getrenderedrowdata', i));
                            var total = 0;
                            var total1 = 0;
                            if (griddata.rowscount == 0) {
                                $("#suma_aporte").html('');
                                $(".suma_aportes").hide();
                            } else {
                                if (_arr_aportes_tmp.length > 0) {
                                    //colocar
                                    $.each(_arr_aportes_tmp, function (k, v) {
                                        total = total + parseFloat(v.KGRS);
                                        total1 = total1 + parseFloat(v.AZUCAR * v.KGRS);
                                    });
                                    total1 = total1 / total;
                                    $(".suma_aportes").show();
                                    $("#suma_aporte").html(dec(precise_round(total, 2), 2));
                                    $("#suma_aporte1").html(dec(precise_round(total1, 2), 2));
                                }
                            }
                        }
                    });
                } else {
                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo), function () {
                    });
                    return false;
                }
            });
        },
        columns: [
            {text: 'NUM CIU', datafield: 'NUM', width: '20%', editable: false},
            {text: 'KILOGRAMOS', datafield: 'KGRS', width: '30%', editable: false, cellsformat: 'c2'},
            {text: 'AZUCAR', datafield: 'AZUCAR', width: '30%', editable: false},
            {text: 'INSCR', datafield: 'INSC', width: '30%', editable: false},
            {text: 'VERIFICACION', datafield: 'CHEQUEO', width: '20%', columntype: 'checkbox', editable: true},
            {text: 'ID', datafield: 'ID', width: '0%', editable: false}
        ]
    });
    $("#add_cius").off().on('click', function () {

        if ($("#frm_cargacius input#ciu_iva").val() == '' || $("#frm_cargacius input#ciu_total").val() == ''
                || $("#frm_cargacius input#ciu_azucar").val() == '') {
            jAlert('Todos los campos son obligatorios.', $.ucwords(_etiqueta_modulo), function () {
                $("#frm_cargacius input").first().select();
            });
            return false;
        }

        var ciu_num = $("#ciu_num").val();
        var ciu_kgrs = $("#ciu_kgrs").val();
        var ciu_azucar = $("#ciu_azucar").val();
        var ciu_insc = $("#ciu_insc").val();
        if (!isnumeroCiu(ciu_num)) {
            jAlert('El formato del Número de Ciu no es correcto (Ejem: A9854124).', $.ucwords(_etiqueta_modulo), function () {
                $("#frm_cargacius input").first().select();
            });
            return false;
        }

        if (!isnumeroCiuIns(ciu_insc)) {
            jAlert('El formato del Número de Inscripcion no es correcto(Ejem: A-9854124).', $.ucwords(_etiqueta_modulo), function () {
                $("#frm_cargacius #ciu_insc").first().next().next().select();
            });
            return false;
        }



//recorrer el grid, si ya eciste el ciu, alertar y no agregar
        var griddata = $('#jqxgridcius').jqxGrid('getdatainformation');
        var _arr_cius = [];
        for (var i = 0; i < griddata.rowscount; i++)
            _arr_cius.push($('#jqxgridcius').jqxGrid('getrenderedrowdata', i));
        sw1 = 0;
        if (_arr_cius) {
            $.each(_arr_cius, function (index, value) {
                if (value.NUM == ciu_num) {
                    jAlert('Este numero de CIU ya esta agregado.', $.ucwords(_etiqueta_modulo), function () {
                        $("#ciu_num").select();
                    });
                    sw1 = 1;
                    return false;
                }
            });
        }

        if (sw1 == 1) {
            return false;
        }

//validar ciu a traves de todas las bd
        $.ajax({
            url: _compravino.URL + "/x_verificarciu",
            data: {
                nciu: ciu_num
            },
            dataType: "json",
            type: "post",
            success: function (data) {

                console.dir(data);
                if (data <= 0) {
                    var data = {
                        'NUM': ciu_num,
                        'KGRS': ciu_kgrs,
                        'AZUCAR': ciu_azucar,
                        'CHEQUEO': 0,
                        'INSC': ciu_insc,
                        'ID': 'DDDDDDD',
                        'uid': 1
                    }

                    var commit = $("#jqxgridcius").jqxGrid('addrow', null, data);
                    $('#jqxgridcius').jqxGrid('selectrow', data.uid);
                    var selectedrowindex = $("#jqxgridcius").jqxGrid('getselectedrowindex');
                    //$('#jqxgridbancos').jqxGrid( { editable: true} );
                    //var editable = $("#jqxgridbancos").jqxGrid('begincelledit', selectedrowindex, "BANCO");


                    //actualizar suma
                    var griddata = $('#jqxgridcius').jqxGrid('getdatainformation');
                    var _arr_aportes_tmp = [];
                    for (var i = 0; i < griddata.rowscount; i++)
                        _arr_aportes_tmp.push($('#jqxgridcius').jqxGrid('getrenderedrowdata', i));
                    var total = 0;
                    var total1 = 0;
                    if (griddata.rowscount == 0) {
                        $("#suma_aporte").html('');
                        $(".suma_aportes").hide();
                    } else {
                        if (_arr_aportes_tmp.length > 0) {
                            //colocar
                            $.each(_arr_aportes_tmp, function (k, v) {
                                total = total + parseFloat(v.KGRS);
                                total1 = total1 + parseFloat(v.AZUCAR * v.KGRS);
                            });
                            total1 = total1 / total;
                            $(".suma_aportes").show();
                            $("#suma_aporte").html(dec(precise_round(total, 2), 2));
                            $("#suma_aporte1").html(dec(precise_round(total1, 2), 2));
                        }
                    }

                    $("#frm_cargacius input").not('#add_cius').val('');
                    $("#frm_cargacius input").first().focus();
                } else {
                    jAlert('Este numero de CIU ya existe. Vefique los datos por favor.', $.ucwords(_etiqueta_modulo), function () {
                        $.unblockUI();
                    });
                }

            }
        });
        return false;
    });
}


function initGrid(id_usuario) {

    id_usuario = id_usuario || '';
    var sourceope = {
        datatype: "json",
        datafields: [
            {name: 'ID', type: 'string'},
            {name: 'ASUNTO', type: 'string'},
            /*{ name: 'ESTADONR', type: 'string' },*/
            {name: 'DESTINATARIO', type: 'string'},
            {name: 'ID_OPERACION', type: 'string'},
            {name: 'DESTINATARIO_NOMBRE', type: 'string'},
            {name: 'PROPIETARIO_NOMBRE', type: 'string'},
            {name: 'PROPIETARIO', type: 'string'},
            {name: 'FCREA', type: 'string'},
            {name: 'REMITENTE', type: 'string'},
            {name: 'ENVIADOA', type: 'string'},
            {name: 'FOJAS', type: 'string'},
            {name: 'ENVIADOAID', type: 'string'}

        ],
        url: 'general/extends/extra/carpetas.php',
        data: {
            accion: "getNotas",
            iduser: id_usuario
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
    $("#jqxgrid").jqxGrid({
        width: '98%',
        groupable: true,
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
                if (me.timer)
                    clearTimeout(me.timer);
                me.timer = setTimeout(function () {
                    dataAdapterope.dataBind();
                }, 300);
            });
        },
        columns: [
            {text: 'ID', datafield: 'ID', width: '6%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
            {text: 'REMITENTE', datafield: 'REMITENTE', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
            {text: 'ASUNTO', datafield: 'ASUNTO', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
            {text: 'EN CARTERA DE', datafield: 'PROPIETARIO_NOMBRE', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
            {text: 'ENVIADO A', datafield: 'ENVIADOA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
            {text: 'CARPETA VINCULADA', datafield: 'ID_OPERACION', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
            {text: 'FOJAS', datafield: 'FOJAS', width: '0%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with'}
        ]
    });
}



function addEventsRequerimientos(idnr) {
    idnr || (idnr = '0');
    $("#btnSubirfile").click(function (e) {

        if ($("#req_etiqueta").val() == '') {
            e.preventDefault();
            jAlert('Ingrese una etiqueta, por favor.', $.ucwords(_etiqueta_modulo), function () {
                $("#req_etiqueta").select();
            });
        }

    });
    if (idnr > 0) {
        $(".lista_reqs_adj a").click(function (e) {
            e.preventDefault();
            var nom = $(this).prev().data('nom');
            var yo = $(this);
            var el = $(this).prev();
            $.ajax({
                url: _compravino.URL + "/x_delupload_nota",
                data: {
                    idnotareq: idnr,
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
    }

    $(".send_nota").on('click', function (e) {
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
        var propietario = 0;
        if (idreqh == '') {
            propietario = _USUARIO_SESION_ACTUAL;
        }

//adjuntos
        var _array_uploads_adj = [];
        $(".lista_reqs_adj li").each(function (index) {
            var nombre = $(this).data('nom');
            var nombre_tmp = $(this).data('tmp');
            _array_uploads_adj.push({nombre: nombre, nombre_tmp: nombre_tmp});
        });
        obj_req = {
            idreqh: idreqh,
            ID_OPERACION: 0,
            ASUNTO: req_asu,
            DESCRIPCION: req_des,
            //estado: estado,
            adjuntos: _array_uploads_adj,
            REMITENTE: req_remitente,
            PROPIETARIO: propietario
        }

//console.dir( obj_req );

        $.ajax({
            url: _compravino.URL + "/x_sendnota",
            data: {
                obj: obj_req
            },
            dataType: "json",
            type: "post",
            success: function (resp) {

                data = resp.result;
                if (resp.accion == 'add') {
                    if (data) {
                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {

                            $.fancybox.close();
                            $("#jqxgrid").show();
                            $("#jqxgrid").jqxGrid('updatebounddata');
                            $("#wpopup").html('');
                            process_asignar(resp.result.ID);
                        });
                    } else {
                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo), function () {
                            $.unblockUI();
                        });
                    }

                } else {
                    jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {
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
    $("#req_etiqueta").keyup(function () {
        $("#req_etiquetah").val($(this).val());
    });
}

function agregar_nota(idobjeto, ver) {
    idobjeto || (idobjeto = []);
    ver || (ver = -1);
    $.ajax({
        url: _compravino.URL + "/x_getform_agregar_requerimiento",
        data: {
            idr: idobjeto
        },
        async: false,
        type: "post",
        success: function (datareq) {
            $.fancybox({
                "content": datareq,
                'padding': 35,
                'autoScale': true,
                'height': 900,
                'scrolling': 'yes',
                'afterShow': function () {
                    $(".fancybox-inner").css({'overflow-x': 'hidden'});
                }
            });
            if (ver != -1)
                $(".elempie").html('');
            if (idobjeto > 0) {//edit
                //var estado = $("#estadoh").val();
                //$("#estadoreq").val(estado).attr('disabled', true).trigger("chosen:updated");
                var destinatarioh = $("#destinatarioh").val();
                $("#destinatario").val(destinatarioh).trigger("chosen:updated");
            } else { //add
                //$("#estadoreq").val(0).attr('disabled', true).trigger("chosen:updated");
            }
            addEventsRequerimientos(idobjeto);
            $(".chzn-select").chosen({disable_search_threshold: 5});
            $("input[type=file]").change(function () {
                $(this).parents(".uploader").find(".filename").val($(this).val());
            });
            $("input[type=file]").each(function () {
                if ($(this).val() == "") {
                    $(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");
                }
            });
            $("#femis").val($("#femish").val());
            init_datepicker('#femis', '-3', '+5', '0', 0);
            activar_acordeon('.grid-1');
            event_grid_traza(idobjeto);
        }
    });
}


function process_vincular(iid_nr, carpeta) {

    carpeta || (carpeta = '0');
    $.ajax({
        url: _compravino.URL + "/x_getvincular",
        data: {
            idusu: _USUARIO_SESION_ACTUAL
        },
        dataType: "json",
        type: "post",
        success: function (data1) {
            var clase_asignar;
            var cadhtml = '<div class="asignar_titulo">Vincular a Carpeta Nº:</div>';
            if (data1) {

                $.each(data1, function (index, value) {
                    clase_asignar = 'link_asignar link_vincular';
                    if (value.IID != _USUARIO_SESION_ACTUAL) {
                        if (carpeta > 0) {
                            if (carpeta == value.ID) {
                                cadhtml += '<div class="' + clase_asignar + ' x_area"  data-iid_nr="' + iid_nr + '" data-iid="' + value.ID + '"><span> Carpeta Nº ' + value.ID;
                                cadhtml += '</span></div>';
                            }
                        } else {
                            cadhtml += '<div class="' + clase_asignar + ' x_area"  data-iid_nr="' + iid_nr + '" data-iid="' + value.ID + '"><span> Carpeta Nº ' + value.ID;
                            cadhtml += '</span></div>';
                        }

                    }
                });
            }

            $.fancybox({
                "content": cadhtml,
                'padding': 35,
                'autoScale': true,
                'height': 900,
                'scrolling': 'no',
                'beforeClose': function () {
                    /*
                     if (myfancy==1)
                     regresar_a_listado();
                     */
                }
            });
            $(".link_vincular").click(function (e) {

                e.preventDefault();
                var idcarpeta = $(this).data('iid');
                var idnr = $(this).data('iid_nr');
                //asignar id_operacion a nr
                jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo), function (r) {
                    if (r == true) {
                        $.ajax({
                            url: _compravino.URL + "/x_vincular_nr",
                            data: {
                                idnr: idnr,
                                idcarpeta: idcarpeta
                            },
                            dataType: "json",
                            type: "post",
                            success: function (data) {

                                jAlert('La nota fue adjuntada a la carpeta Nº .' + idcarpeta, $.ucwords(_etiqueta_modulo), function () {
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

function process_asignar(iidnota) {

    iidnota || (iidnota = '-1');
    //$('.asignar_nota').show();
    //$('.asignar_nota').on('click', function(event){
    //event.preventDefault();

    var opt_puesto = 0;
    var opt_area = 0;
    opt_area = [4];
    opt_puesto = 6;
    $.ajax({
        url: _compravino.URL + "/x_getenviar_a1",
        data: {
            puesto_in: opt_puesto, // parametro opcional
            area: opt_area
        },
        dataType: "json",
        type: "post",
        success: function (data1) {
            var clase_asignar;
            var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div>';
            if (data1) {
                $.each(data1, function (index, value) {
                    clase_asignar = 'link_asignar';
                    if (value.IID != _USUARIO_SESION_ACTUAL) {
                        cadhtml += '<div class="' + clase_asignar + ' x_area" data-etapa="' + value.ETAPA + '" data-iid="' + value.ID + '" data-puesto_in="' + value.puesto_in + '"><span>' + value.DENOMINACION;
                        cadhtml += '</span></div>';
                    }
                });
            }

            $.fancybox({
                "content": cadhtml,
                'padding': 35,
                'autoScale': true,
                'height': 900,
                'scrolling': 'no',
                'beforeClose': function () {
                    if (myfancy == 1)
                        regresar_a_listado();
                }
            });
            $(".x_area").click(function (e1) {
                e1.preventDefault();
                var tmpfancy = myfancy;
                myfancy = 0;
                var iid = $(this).data('iid');
                var apuesto_in = $(this).data('puesto_in');
                apuesto_in = isNaN(apuesto_in) ? '' : apuesto_in;
                $.ajax({
                    url: _compravino.URL + "/x_getenviar_a2",
                    data: {
                        id_area: iid,
                        puesto_in: apuesto_in
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.status);
                        alert(thrownError);
                    },
                    dataType: "json",
                    type: "post",
                    success: function (datar) {

                        var clase_asignar;
                        var cadhtml = '<div class="asignar_titulo">Asignar Carpeta a:</div> <div class="regresar_ar">Regresar</div>';
                        if (datar) {
                            $.each(datar, function (index, value) {

                                clase_asignar = 'link_asignar';
                                if (value.IID != _USUARIO_SESION_ACTUAL) {

                                    cadhtml += '<div class="' + clase_asignar + '" data-etapa="' + value.ETAPA + '" data-iid="' + value.IID + '"><span>' + value.NOMBRE + ' ' + value.APELLIDO + ' (' + value.AREA + ' - ' + value.PUESTO + ')';
                                    cadhtml += '</span></div>';
                                }

                            });
                        }

                        $.fancybox({
                            "content": cadhtml,
                            'padding': 35,
                            'autoScale': true,
                            'height': 900,
                            'scrolling': 'no',
                            'beforeClose': function () {
                                if (myfancy == 1)
                                    regresar_a_listado();
                            }
                        });
                        if (tmpfancy == 1)
                            myfancy = 1;
                        $(".regresar_ar").click(function (e) {
                            e.preventDefault();
                            $.fancybox.close();
                            $("#asignar").trigger('click');
                        });
                        $(".link_asignar").click(function (e) {

                            e.preventDefault();
                            var iid = $(this).data('iid');
                            var new_etapa_data = $(this).data('etapa');
                            var observacion;
                            var estado;
                            var descripcion;
                            var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                            mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                            var id_send;
                            if (mydata != null) {
                                id_send = mydata.ID;
                            } else if (iidnota > 0) {
                                id_send = iidnota
                            }

                            jConfirm('Esta seguro de realizar esta Asignación?.', $.ucwords(_etiqueta_modulo), function (r) {
                                if (r == true) {
                                    observacion = 'ENVIAR NOTA';
                                    descripcion = 'ENVIO DE NOTA A DESTINATARIO'
                                    estado = '1'

                                    $.ajax({
                                        url: _compravino.URL + "/x_guardar_traza_nota",
                                        data: {
                                            id_req_nota: id_send,
                                            destinatario: iid,
                                            observacion: observacion,
                                            descripcion: descripcion
                                        },
                                        dataType: "json",
                                        type: "post",
                                        success: function (data) {
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


function event_grid_traza(idnota) {

    if (idnota > 0) {

        var sourcetraza = {
            datatype: "json",
            datafields: [
                {name: 'ID'},
                {name: 'DESCRIPCION', type: 'string'},
                {name: 'USUARIO', type: 'string'},
                {name: 'FECHA', type: 'string'}
            ],
            url: 'general/extends/extra/carpetas.php',
            data: {
                accion: "getTrazabilidadNota",
                idnota: idnota
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
                {text: 'USUARIO', datafield: 'USUARIO', width: '25%', hidden: false},
                {text: 'FECHA', datafield: 'FECHA', width: '25%', hidden: false},
                {text: 'DESCRIPCION', datafield: 'DESCRIPCION', width: '90%', hidden: false}
            ]
        });
    }

}



function loadChild(val) {
    if (working == false) {
        working = true;
        $.ajax({
            url: _compravino.URL + "/x_getlocalidad",
            async: false,
            data: {
                idp: val
            },
            dataType: "json",
            type: "post",
            success: function (r) {
                var connection, options = '';
                $.each(r.items, function (k, v) {
                    connection = '';
                    if (v)
                        connection = 'data-connection="' + v + '"';
                    options += '<option value="' + v + '" ' + connection + '>' + k + '</option>';
                });
                if (r.defaultText) {
                    options = '<option>' + r.defaultText + '</option>' + options;
                }

                $('#div_subrubro').html('<select class="chzn-select medium-select2 select" id="subrubro">' + options + '</select>');
                $('#subrubro').on('change', function (event) {
                    event.preventDefault();
                    $('#localidadh').val($('#subrubro').val());
                });
                var selects = $('#div_subrubro').find('select');
                selects.chosen({width: "220px"});
                working = false;
            }
        });
    }

}



function initGridListadoRevision(id_usuario) {

    var cellclass = function (row, columnfield, value) {
        return 'green'
    }

    id_usuario = id_usuario || '1';
    var sourceope = {
        datatype: "json",
        datafields: [
            {name: 'ID', type: 'string'},
            {name: 'CLIENTE', type: 'string'},
            {name: 'CUIT', type: 'string'},
            {name: 'CONDIVA', type: 'string'},
            {name: 'CONDIIBB', type: 'string'},
            {name: 'CBU', type: 'string'},
            {name: 'NUMERO', type: 'string'},
            {name: 'FECHA', type: 'string'},
            {name: 'BODEGA', type: 'string'},
            {name: 'DEPARTAMENTO', type: 'string'},
            {name: 'KGRS', type: 'string'},
            {name: 'OBSERVACIONES', type: 'string'},
            {name: 'USU_CARGA', type: 'string'},
            {name: 'USU_CHEQUEO', type: 'string'},
            {name: 'ESTADO', type: 'string'},
            {name: 'PRECIO', type: 'number'},
            {name: 'IMP_ERROR_TEXTO', type: 'number'},
            {name: 'NETO', type: 'number'},
            {name: 'IVA', type: 'number'},
            {name: 'TOTAL', type: 'number'},
            {name: 'CREATEDON', type: 'string'},
            {name: 'IID', type: 'string'}

        ],
        url: 'general/extends/extra/carpetas.php',
        data: {
            accion: "getFacturasCuva",
            idtipo: 1,
            estado: '12' // en revision
        },
        async: false,
        deleterow: function (rowid, commit) {
            process_data(_compravino.URL + "/x_delete_facturas_cu", mydata);
            console.dir(mydata);
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
    $("#jqxgrid_listado_revision").jqxGrid({
        width: '96%',
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid_listado_revision").jqxGrid('hidecolumn', 'IID');
        },
        //selectionmode:'multiplerows',
        columnsresize: true,
        showtoolbar: true,
        //sortable: true,
        //filterable: true,
        //showfilterrow: true,
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
                if (me.timer)
                    clearTimeout(me.timer);
                me.timer = setTimeout(function () {
                    dataAdapterope.dataBind();
                }, 300);
            });
        },
        showstatusbar: true,
        renderstatusbar: function (statusbar) {
            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
            var deleteButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: 2px;' src='general/css/images/delete.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Borrar</span></div>");
            container.append(deleteButton);
            statusbar.append(container);
            deleteButton.jqxButton({theme: theme, width: 65, height: 20});
            deleteButton.click(function (event) {
                var selectedrowindex = $("#jqxgrid_listado_revision").jqxGrid('getselectedrowindex');
                var rowscount = $("#jqxgrid_listado_revision").jqxGrid('getdatainformation').rowscount;
                mydata = $('#jqxgrid_listado_revision').jqxGrid('getrowdata', selectedrowindex);
                if (selectedrowindex > -1 && selectedrowindex < rowscount) {

                    jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo), function (r) {
                        if (r == true) {

                            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                var id = $("#jqxgrid_listado_revision").jqxGrid('getrowid', selectedrowindex);
                                $("#jqxgrid_listado_revision").jqxGrid('deleterow', id);
                            }

                        }
                    });
                } else {
                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo), function () {

                    });
                    return false;
                }
            });
        },
        columns: [
            {text: 'ID', datafield: 'ID', width: '6%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CLIENTE', datafield: 'CLIENTE', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'ERROR', datafield: 'IMP_ERROR_TEXTO', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellclassname: 'red'},
            {text: 'CUIT', datafield: 'CUIT', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CONDICION IVA', datafield: 'CONDIVA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CONDICION IIBB', datafield: 'CONDIIBB', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CBU', datafield: 'CBU', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'FACTURA', datafield: 'NUMERO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'FECHA FACTURA', datafield: 'FECHA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, selectable: false},
            {text: 'BODEGA', datafield: 'BODEGA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'DEPARTAMENTO', datafield: 'DEPARTAMENTO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'KGRS', datafield: 'KGRS', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'OBSERVACIONES', datafield: 'OBSERVACIONES', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CARGA', datafield: 'USU_CARGA', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CHEQUEO', datafield: 'USU_CHEQUEO', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'ESTADO', datafield: 'ESTADO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false/*,cellsrenderer: cellsrenderer*/},
            {text: 'PRECIO', datafield: 'PRECIO', width: '20%', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
            {text: 'NETO', datafield: 'NETO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
            {text: 'IVA', datafield: 'IVA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
            {text: 'TOTAL', datafield: 'TOTAL', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
            {text: 'FECHA IMPORTACIÓN', datafield: 'CREATEDON', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true, cellsformat: 'c2'},
            {text: 'IID', datafield: 'IID', width: '0%'}
        ]
    });
}


function initGridListado(id_usuario) {

    id_usuario = id_usuario || '1';
    var sourceope = {
        datatype: "json",
        datafields: [
            {name: 'ID', type: 'string'},
            {name: 'CLIENTE', type: 'string'},
            {name: 'CUIT', type: 'string'},
            {name: 'CONDIVA', type: 'string'},
            {name: 'CONDIIBB', type: 'string'},
            {name: 'CBU', type: 'string'},
            {name: 'NUMERO', type: 'string'},
            {name: 'FECHA', type: 'string'},
            {name: 'BODEGA', type: 'string'},
            {name: 'DEPARTAMENTO', type: 'string'},
            {name: 'KGRS', type: 'string'},
            {name: 'OBSERVACIONES', type: 'string'},
            {name: 'USU_CARGA', type: 'string'},
            {name: 'USU_CHEQUEO', type: 'string'},
            {name: 'ESTADO', type: 'string'},
            {name: 'PRECIO', type: 'number'},
            {name: 'NETO', type: 'number'},
            {name: 'IVA', type: 'number'},
            {name: 'TOTAL', type: 'number'},
            {name: 'CREATEDON', type: 'string'},
            {name: 'FORMULA', type: 'string'},
            {name: 'IID', type: 'string'}

        ],
        url: 'general/extends/extra/carpetas.php',
        data: {
            accion: "getFacturasCuva",
            idtipo: 1,
            idpro: _provincia
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
    $("#jqxgrid_listado").jqxGrid({
        width: '96%',
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid_listado").jqxGrid('hidecolumn', 'IID');
        },
        selectionmode: 'multiplerows',
        columnsresize: true,
        showtoolbar: true,
        //sortable: true,
        groupable: true,
        //filterable: true,
        //showfilterrow: true,
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
                if (me.timer)
                    clearTimeout(me.timer);
                me.timer = setTimeout(function () {
                    dataAdapterope.dataBind();
                }, 300);
            });
        },
        columns: [
            {text: 'ID', datafield: 'ID', width: '6%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CLIENTE', datafield: 'CLIENTE', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CUIT', datafield: 'CUIT', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CONDICION IVA', datafield: 'CONDIVA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CONDICION IIBB', datafield: 'CONDIIBB', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CBU', datafield: 'CBU', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'FACTURA', datafield: 'NUMERO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'FECHA FACTURA', datafield: 'FECHA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, selectable: false},
            {text: 'BODEGA', datafield: 'BODEGA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'DEPARTAMENTO', datafield: 'DEPARTAMENTO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'KGRS', datafield: 'KGRS', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'OBSERVACIONES', datafield: 'OBSERVACIONES', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CARGA', datafield: 'USU_CARGA', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'CHEQUEO', datafield: 'USU_CHEQUEO', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false},
            {text: 'ESTADO', datafield: 'ESTADO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false/*,cellsrenderer: cellsrenderer*/},
            {text: 'PRECIO', datafield: 'PRECIO', width: '20%', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
            {text: 'NETO', datafield: 'NETO', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
            {text: 'IVA', datafield: 'IVA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
            {text: 'TOTAL', datafield: 'TOTAL', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
            {text: 'FECHA DE IMPORTACIÓN', datafield: 'CREATEDON', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true, cellsformat: 'c2'},
            {text: 'FORMULA', datafield: 'FORMULA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
            {text: 'IID', datafield: 'IID', width: '0%'}
        ]
    });
    var sourceope_ope = {
        datatype: "json",
        datafields: [
            {name: 'ID_OPERATORIA', type: 'int'},
            {name: 'NOMBRE_OPE', type: 'string'},
            {name: 'DESCRIPCION_OPE', type: 'string'},
            {name: 'ID_COORDINADOR_OPE', type: 'int'},
            {name: 'ID_JEFE_OPE', type: 'int'},
            {name: 'TITULAR', type: 'string'},
            {name: 'CUIT', type: 'string'},
            {name: 'NUM_VINEDO', type: 'float'},
            {name: 'LITROS_OFRECIDOS', type: 'float'},
            {name: 'HECT_DECLARADAS', type: 'float'},
            {name: 'BGA_DEP', type: 'float'},
            {name: 'DEPT_BODEGA', type: 'float'},
            {name: 'NUM_INV_BODEGA', type: 'float'}
        ],
        url: 'general/extends/extra/carpetas.php',
        data: {
            accion: "getOperatoriaCompraUva"
        },
        async: false,
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };
    var dataAdapterope_ope = new $.jqx.dataAdapter(sourceope_ope,
            {
                formatData: function (data) {
                    data.name_startsWith = $("#searchField").val();
                    return data;
                }
            }
    );
    $("#jqxgrid_listado_operatoria").jqxGrid({
        width: '96%',
        source: dataAdapterope_ope,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid_listado").jqxGrid('hidecolumn', 'IID');
        },
        columnsresize: true,
        localization: getLocalization(),
        columns: [
            {text: 'OPERATORIA', datafield: 'ID_OPERATORIA', width: '10%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'TITULAR', datafield: 'TITULAR', width: '12%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'CUIT', datafield: 'CUIT', width: '12%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'N° VIÑEDO', datafield: 'NUM_VINEDO', width: '12%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'LITROS OFRECIDOS', datafield: 'LITROS_OFRECIDOS', width: '12%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'NUM. INV BODEGA', datafield: 'NUM_INV_BODEGA', width: '12%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'NOMBRE ', datafield: 'NOMBRE_OPE', width: '32%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'COORDINADOR', datafield: 'ID_COORDINADOR_OPE', width: '15%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'JEFE', datafield: 'ID_JEFE_OPE', width: '15%', columntype: 'textbox', filtercondition: 'starts_with', filterable: true}
        ]
    });
}


function editar_factura(name_grid) {

    name_grid || (name_grid = 'jqxgrid_listado');
    mydata = '';
    var selectedrowindex = $("#" + name_grid).jqxGrid('getselectedrowindex');
    var selectedrowindexes = $("#" + name_grid).jqxGrid('getselectedrowindexes');
    mydata = $('#' + name_grid).jqxGrid('getrowdata', selectedrowindex);
    if (mydata == null) {
        jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo), function () {
            $.unblockUI();
        });
        return false;
    }

    if (selectedrowindexes.length > 1) {
        jAlert('Elija solo una Factura para editar.', $.ucwords(_etiqueta_modulo), function () {
            $.unblockUI();
        });
        return false;
    }

    var urlh = "backend/carpeta/compravino/init/12/3/" + mydata.ID;
    $(location).attr('href', urlh);
}
function editar_operatoria(name_grid) {
    name_grid || (name_grid = 'jqxgrid_listado_operatoria');
    mydata = '';
    var selectedrowindex = $("#" + name_grid).jqxGrid('getselectedrowindex');
    var selectedrowindexes = $("#" + name_grid).jqxGrid('getselectedrowindexes');
    mydata = $('#' + name_grid).jqxGrid('getrowdata', selectedrowindex);
    if (mydata == null) {
        jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo), function () {
            $.unblockUI();
        });
        return false;
    }
    if (selectedrowindexes.length > 1) {
        jAlert('Elija solo una Operatoria para editar.', $.ucwords(_etiqueta_modulo), function () {
            $.unblockUI();
        });
        return false;
    }
//    id_a_editar = mydata.ID_OPERATORIA;
//    var urlh = "backend/carpeta/compravino/init/12/8/" + mydata.ID_OPERATORIA;
    var urlh = "backend/carpeta/compravino/init/12/9/" + mydata.ID_OPERATORIA;
    $(location).attr('href', urlh);
}

function limpiar_form_nf() {

    $(".nuevafact_form textarea").val("").removeAttr("readonly");
    $(".nuevafact_form input").not("#add_cius").val("");
    $(".nuevafact_form input").not("#cuitform,#nombre2,#add_cius,#total,#dto_bodega,#neto,#iva").removeAttr("readonly");
    $("#bodega").val(0).trigger("chosen:updated");
    $('#jqxgridcius').jqxGrid('clear');
    $(".cabezera_frm_ciu").show();
    $("#fecha").datepicker('enable');
    $("#fechavto").datepicker('enable');
}


function lote_pago() {

    var rowindexes = $('#jqxgrid_listado').jqxGrid('getselectedrowindexes');
    var _arr_sel = [];
    if (rowindexes.length > 0) {
        var swa = 0;
        $.each(rowindexes, function (index, value) {
            var reg = $('#jqxgrid_listado').jqxGrid('getrowdata', value);
            if (reg.ESTADO == 'Pago Solicitado') {
                swa = 1;
            }
            _arr_sel.push(reg);
        });
        if (swa == '1') {
            jAlert('La seleccion contiene comprobantes ya procesados.', $.ucwords(_etiqueta_modulo), function () {

            });
            return false;
        }

        jConfirm('Esta seguro de generar este lote de pago??.', $.ucwords(_etiqueta_modulo), function (r) {
            if (r == true) {
                $.blockUI({message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>'});
                $.ajax({
                    url: _compravino.URL + "/x_guardarlote",
                    data: {
                        provincia: _compravino._provincia,
                        obj: _arr_sel
                    },
                    dataType: "json",
                    type: "post",
                    success: function (data) {
                        console.dir(data);
                        if (data > 0) {
                            jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {

                                imprimir_listado_seleccionado();
                                show_btns();
                                limpiar_form_fact();
                                //$('#send').hide();
                                $("#jqxgrid_listado").show();
                                $("#jqxgrid_listado").jqxGrid('updatebounddata');
                                $("#wpopup").html('');
                            });
                        } else {
                            jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo), function () {
                                $.unblockUI();
                            });
                        }
                    }
                });
            }
        });
    } else {
        jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo), function () {
            $.unblockUI();
        });
        return false;
    }

}


function imprimir_listado_seleccionado() {
    var arr_id = $("#jqxgrid_listado").jqxGrid('getselectedrowindexes');
    var griddata = $('#jqxgrid_listado').jqxGrid('getdatainformation');
    for (var i = 0; i < griddata.rowscount; i++) {
        if (jQuery.inArray(i, arr_id) == -1) {
            $("#jqxgrid_listado").jqxGrid('deleterow', i);
        }
    }

//Uncaught TypeError: Cannot read property 'document' of undefined 

    var gridContent = $("#jqxgrid_listado").jqxGrid('exportdata', 'html');
    var newWindow = window.open('', '', 'width=800, height=500'),
            document = newWindow.document.open(),
            pageContent =
            '<!DOCTYPE html>\n' +
            '<html>\n' +
            '<head>\n' +
            '<meta charset="utf-8" />\n' +
            '<title>jQWidgets Grid</title>\n' +
            '</head>\n' +
            '<body>\n' + gridContent + '\n</body>\n</html>';
    document.write(pageContent);
    document.close();
    newWindow.print();
    $("#jqxgrid_listado").jqxGrid('updatebounddata');
}


function post_upload(nombre, nombre_tmp, etapa) {

    jAlert('Archivo cargado correctamente. ' + nombre, $.ucwords(_etiqueta_modulo), function () {
//agregarlo a la lista
        $(".lista_adjuntos").append('<li class="eta-' + etapa + '" data-eta="' + etapa + '" data-nom="' + nombre + '" data-tmp="' + nombre_tmp + '">' + nombre + '<span>&nbsp;<span></li>');
        $('.lista_adjuntos li span').off().on('click', function (event) {
            event.preventDefault();
            var myobj = $(this)
            jConfirm('Esta seguro de borrar este archivo??.', $.ucwords(_etiqueta_modulo), function (r) {
                if (r == true) {
                    $this = myobj.parent();
                    var ruta = $this.data('tmp');
                    //x_borrar_file
                    $.ajax({
                        url: _compravino.URL + "/x_borrar_file",
                        data: {
                            ruta: ruta
                        },
                        dataType: "json",
                        type: "post",
                        success: function () {
                            $this.remove();
                        }
                    });
                }
            });
        });
        $('#upload_file1').each(function () {
            this.reset();
        });
        $("#upload_file1 input[type=file]").each(function () {
            $(this).parents(".uploader").find(".filename").val("Seleccione Archivo...");
        });
        $.fancybox.close();
        if ($('.grid_adjuntos').hasClass('inactive'))
            $(".grid_adjuntos span").trigger('click');
    });
}

function importar_procesar() {

    jConfirm('Esta seguro de procesar estos archivos??.', $.ucwords(_etiqueta_modulo), function (r) {
        if (r == true) {
// llamar ajax
            $.blockUI({message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>'});
            $.ajax({
                url: _compravino.URL + "/x_importar_xls",
                data: {
                    fid_sanjuan: _fid_sanjuan,
                    ope_sanjuan: _ope_sanjuan
                },
                dataType: "json",
                type: "post",
                success: function (dat) {
                    console.dir(dat);
                    if (dat == -2) {
                        jAlert('No existen archivos para la importación.', $.ucwords(_etiqueta_modulo), function () {

                        });
                    } else if (dat == -1) {
                        jAlert('No existe el archivo de cius para la importación. El proceso se continuo', $.ucwords(_etiqueta_modulo), function () {
                            $.ajax({
                                url: _compravino.URL + "/x_actualizarLista",
                                data: {
                                },
                                //dataType: "json",
                                type: "post",
                                success: function (data) {
                                    console.dir(data);
                                    $('.lista_arch').html(data);
                                    evento_lista_arch();
                                }
                            });
                        });
                    } else {
                        jAlert('Los datos fueron importados con exito.', $.ucwords(_etiqueta_modulo), function () {
                            //actualizar el listado
                            $.ajax({
                                url: _compravino.URL + "/x_actualizarLista",
                                data: {
                                },
                                //dataType: "json",
                                type: "post",
                                success: function (data) {
                                    console.dir(data);
                                    $('.lista_arch').html(data);
                                    evento_lista_arch();
                                }
                            });
                        });
                    }
                }
            });
        }
    });
}