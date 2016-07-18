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
    var numOperatoria = $("#numOperatoria").val();
    var id = $("#idh").val();
    var numero = $("#numero").val();
    var fecha = $("#fecha").val();
    fecha = formattedDate_ui(fecha);
    var cai = $("#cai").val();
    var fechavto = $("#fechavto").val();
    fechavto = formattedDate_ui(fechavto);
    var proveedor_list = $("#proveedor-jquery").val();
    var ltros = $("#ltros").val();
    var fpago = $("#fpago-select").val();
    var cuitform = $("#cuitform").val();
    var azucar = $("#azucar").val();
    var precio = $("#precio").val();
    var neto = $("#neto").val();
    var iva = $("#iva").val();
    var porcentaje_iva = $("#porcentaje_iva").val();
    var total = $("#total").val();
    var observacion_fact = $("#observacion_fact").val();
    var formula = $("#formula").val();
    var numVinedo = $("#numVinedo").val();
    var numRut = $("#numRut").val();
    iid = id ? id : 0;

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
    if (cai !== '') {
        if (fechavto < fecha) {
            jAlert('La fecha de Vencimiento del CAI no puede estar vacia o ser anterior a la fecha de la factura.', $.ucwords(_etiqueta_modulo), function () {
                $("#fechavto").focus();
            });
            return false;
        }
    }
    if (proveedor_list == '') {
        jAlert('Elija un proveedor.', $.ucwords(_etiqueta_modulo), function () {
            $("#proveedor-jquery").focus();
        });
        return false;
    }
    if (ltros == '') {
        jAlert('Ingrese el valor de los Ltros.', $.ucwords(_etiqueta_modulo), function () {
            $("#ltros").focus();
        });
        return false;
    }

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
        CUIT: cuitform,
        PRECIO: precio,
        ID_ESTADO: 1,
        USU_CARGA: _USUARIO_SESION_ACTUAL,
        NETO: neto,
        IVA: iva,
        TOTAL: total,
        FORMA_PAGO: fpago,
        OBSERVACIONES: observacion_fact,
        update_cius: 0,
        PORC_IVA: porcentaje_iva,
        FORMULA: formula,
        TIPO: 2
    }

//validar numero de factura
//numero
    $.ajax({
        url: _agencia.URL + "/x_verificarnumfactura",
        data: {
            numero: numero,
            cuit: cuitform,
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
                    url: _agencia.URL + "/x_sendobj",
                    data: {obj: objsave},
                    dataType: "json", type: "post", async: "false", });
                /*Verificar si tiene cuotas sino generar*/
                $.ajax({
                    url: _agencia.URL + "/x_verificarCuotas",
                    data: {numFactura: numero, cant_cu: fpago, neto: neto, iva: iva, fecha: fecha},
                    dataType: "json", type: "post", async: "false", });
                jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {
                    var urlh = "backend/carpeta/agencia/init/12/2";
                    $(location).attr('href', urlh);
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
}

function limpiar_form_fact() {
    $(".nuevafact_form input").not('.button-a').val('');
    $(".nuevafact_form textarea").val('');
    $("#bodega").val(0).trigger("chosen:updated");
    $('#jqxgridcius').jqxGrid('clear');
}

function show_btns(sw) {
    sw || (sw = '0');
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
    $(".env_form #retencionesD").val(cliente.RETENCION);
    if (cliente.MAYORISTA == 0) {
        $(".env_form #tipo_m").val('Minorista');
        $("#ver_limite_m").hide();
    } else {
        $(".env_form #tipo_m").val('Mayorista');
        $("#ver_limite_m").show();
        $(".env_form #limite_m_d").val(cliente.LIMITE_M);
    }
    $(".env_form #observacion").val(cliente.OBSERVACION);
    $(".env_form #condicioniva").val(cliente.ID_CONDICION_IVA).trigger("chosen:updated");
    $(".env_form #condicioniibb").val(cliente.ID_CONDICION_IIBB).trigger("chosen:updated");
    $(".env_form #provincia").val(cliente.ID_PROVINCIA).trigger("chosen:updated");
    loadChild(cliente.ID_PROVINCIA);
    $(".env_form #subrubro").val(cliente.ID_DEPARTAMENTO).trigger("chosen:updated");
}

$(document).ready(function () {
    $("#opeFideicomiso").chosen({width: "250px"});
    $("#opeCoordinador").chosen({width: "250px"});
    $("#opeJefe").chosen({width: "250px"});
    $("#opeProveedores").chosen({width: "400px"});
    $("#opeBodega").chosen({width: "400px"});
    $("#juridica").hide();
    $('#send').show();
    $('.tb_atras_ope').on('click', function (e) {
        var urlh = "backend/carpeta/agencia/init/12/7";
        $(location).attr('href', urlh);
    });
    $('.tb_regresar_ope').on('click', function (e) {
        var urlh = "backend/carpeta/agencia/init/12";
        $(location).attr('href', urlh);
    });
    init_datepicker('#fechaBuscarVen', '-3', '+5', '0', 0);
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
    $('.consultar').on('click', function (e) {
        e.preventDefault();
        $('.env_form').show();
        $('.tb_regresar_ope').hide();
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
            url: _agencia.URL + "/x_getobjcliente",
            data: {
                cuit: cuit
            },
            dataType: "json",
            type: "post",
            success: function (data) {
                if (data.ID > 0) {
                    $.unblockUI();
                    llenar_form(data);
                    show_btns(1);
                    $("#send").hide();
                } else {
                    jAlert('Este CUIT no está registrado. guarde un nuevo cliente o intente otra busqueda (con Escape - Esc ).', $.ucwords(_etiqueta_modulo), function () {
                        $("#cuit_busqueda").val('');
                        $("#nombre").focus();
                        $("#cuit").val(cuit);
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
//        var trae_operatoria = 0;
//        $.ajax({
//            url: _agencia.URL + "/x_getNumOpe",
//            data: {
//                id_cliente: $("#id_buscar").val()
//            },
//            dataType: "json",
//            type: "post",
//            async: false,
//            success: function (data_op) {
//                trae_operatoria = 1;
//                $("#numOperatoria").val(data_op.ID_OPERATORIA);
//                $.ajax({
//                    url: _agencia.URL + "/x_getAlgunosProveedores",
//                    datatype: 'html',
//                    type: 'post',
//                    async: false,
//                    data: {id: data_op.ID_OPERATORIA},
//                    success: function (data) {
//                        $('#indent_prueba').html(data);
//                        $("#proveedor-jquery").chosen({width: "220px"});
//                    }
//                })
//                $.ajax({
//                    url: _agencia.URL + "/x_getChecklistHumanaFact",
//                    datatype: 'html',
//                    type: 'post',
//                    async: false,
//                    data: {id: data_op.ID_OPERATORIA},
//                    success: function (data) {
//                        $('#check_datos').html(data);
//                    }
//                })
        $.ajax({
            url: _agencia.URL + "/x_getFormasPago",
            datatype: 'html',
            type: 'post',
            async: false,
//                    data: {id: data_op.ID_OPERATORIA},
            success: function (data) {
                $('#fpago').html(data);
                $("#fpago-select").chosen({width: "220px"});
            }
        })
        $('.nuevafact_form').show();
//            }
//        });
//        if (trae_operatoria == 0) {
//            jAlert('El proveedor no pertenece a una operatoria. Debe ser asignado previamente.', $.ucwords(_etiqueta_modulo), function () {
//                var urlh = "backend/carpeta/agencia/init/12/7";
//                $(location).attr('href', urlh);
//            });
//        }

        $("#nombre2").val($("#nombre").val());
        $("#retencion").val($("#retencionesD").val());
        $("#cuitform").val(cc);
        show_btns(2);
    });
    refresGridevent();

    $('#send-estado').on('click', function (e) {
        var estCuo1, estCuo2, estCuo3, estCuo4, estCuo5, estCuo6 = 0;
        var ordenPago1, ordenPago2, ordenPago3, ordenPago4, ordenPago5, ordenPago6 = '';
        var numFactura = $("#numFactura").val();
        var estFactura = $("#estFact").val();
        if ($("#cant-cuotas-f").val() == '1') {
            estCuo1 = $("#estadoCuota1").val();
            ordenPago1 = $("#ordenPago1").val();
            $.ajax({
                url: _agencia.URL + "/x_sendPago1",
                data: {
                    numFactura: numFactura,
                    estFactura: estFactura,
                    estCuo1: estCuo1,
                    ordenPago1: ordenPago1
                },
                dataType: "json", type: "post",
                success: function () {
                    jAlert('Se actualizaron los datos correctamente.', $.ucwords(_etiqueta_modulo), function () {
                        $.unblockUI();
                        var urlh = "backend/carpeta/agencia/init/12/2";
                        $(location).attr('href', urlh);
                    });
                }});
        }

        if ($("#cant-cuotas-f").val() == '2') {
            estCuo1 = $("#estadoCuota1").val();
            ordenPago1 = $("#ordenPago1").val();
            estCuo2 = $("#estadoCuota2").val();
            ordenPago2 = $("#ordenPago2").val();
            $.ajax({
                url: _agencia.URL + "/x_sendPago2",
                data: {
                    numFactura: numFactura,
                    estFactura: estFactura,
                    estCuo1: estCuo1,
                    ordenPago1: ordenPago1,
                    estCuo2: estCuo2,
                    ordenPago2: ordenPago2
                },
                dataType: "json", type: "post",
                success: function () {
                    jAlert('Se actualizaron los datos correctamente.', $.ucwords(_etiqueta_modulo), function () {
                        $.unblockUI();
                        var urlh = "backend/carpeta/agencia/init/12/2";
                        $(location).attr('href', urlh);
                    });
                }});
        }
        if ($("#cant-cuotas-f").val() == '3') {
            estCuo1 = $("#estadoCuota1").val();
            ordenPago1 = $("#ordenPago1").val();
            estCuo2 = $("#estadoCuota2").val();
            ordenPago2 = $("#ordenPago2").val();
            estCuo3 = $("#estadoCuota3").val();
            ordenPago3 = $("#ordenPago3").val();
            $.ajax({
                url: _agencia.URL + "/x_sendPago3",
                data: {
                    numFactura: numFactura,
                    estFactura: estFactura,
                    estCuo1: estCuo1,
                    ordenPago1: ordenPago1,
                    estCuo2: estCuo2,
                    ordenPago2: ordenPago2,
                    estCuo3: estCuo3,
                    ordenPago3: ordenPago3
                },
                dataType: "json", type: "post",
                success: function () {
                    jAlert('Se actualizaron los datos correctamente.', $.ucwords(_etiqueta_modulo), function () {
                        $.unblockUI();
                        var urlh = "backend/carpeta/agencia/init/12/2";
                        $(location).attr('href', urlh);
                    });
                }});
        }
        if ($("#cant-cuotas-f").val() == '4') {
            estCuo1 = $("#estadoCuota1").val();
            ordenPago1 = $("#ordenPago1").val();
            estCuo2 = $("#estadoCuota2").val();
            ordenPago2 = $("#ordenPago2").val();
            estCuo3 = $("#estadoCuota3").val();
            ordenPago3 = $("#ordenPago3").val();
            estCuo4 = $("#estadoCuota4").val();
            ordenPago4 = $("#ordenPago4").val();
            $.ajax({
                url: _agencia.URL + "/x_sendPago3",
                data: {
                    numFactura: numFactura,
                    estFactura: estFactura,
                    estCuo1: estCuo1,
                    ordenPago1: ordenPago1,
                    estCuo2: estCuo2,
                    ordenPago2: ordenPago2,
                    estCuo3: estCuo3,
                    ordenPago3: ordenPago3,
                    estCuo4: estCuo4,
                    ordenPago4: ordenPago4
                },
                dataType: "json", type: "post",
                success: function () {
                    jAlert('Se actualizaron los datos correctamente.', $.ucwords(_etiqueta_modulo), function () {
                        $.unblockUI();
                        var urlh = "backend/carpeta/agencia/init/12/2";
                        $(location).attr('href', urlh);
                    });
                }});
        }
        if ($("#cant-cuotas-f").val() == '5') {
            estCuo1 = $("#estadoCuota1").val();
            ordenPago1 = $("#ordenPago1").val();
            estCuo2 = $("#estadoCuota2").val();
            ordenPago2 = $("#ordenPago2").val();
            estCuo3 = $("#estadoCuota3").val();
            ordenPago3 = $("#ordenPago3").val();
            estCuo4 = $("#estadoCuota4").val();
            ordenPago4 = $("#ordenPago4").val();
            estCuo5 = $("#estadoCuota5").val();
            ordenPago5 = $("#ordenPago5").val();
            $.ajax({
                url: _agencia.URL + "/x_sendPago3",
                data: {
                    numFactura: numFactura,
                    estFactura: estFactura,
                    estCuo1: estCuo1,
                    ordenPago1: ordenPago1,
                    estCuo2: estCuo2,
                    ordenPago2: ordenPago2,
                    estCuo3: estCuo3,
                    ordenPago3: ordenPago3,
                    estCuo4: estCuo4,
                    ordenPago4: ordenPago4,
                    estCuo5: estCuo5,
                    ordenPago5: ordenPago5
                },
                dataType: "json", type: "post",
                success: function () {
                    jAlert('Se actualizaron los datos correctamente.', $.ucwords(_etiqueta_modulo), function () {
                        $.unblockUI();
                        var urlh = "backend/carpeta/agencia/init/12/2";
                        $(location).attr('href', urlh);
                    });
                }});
        }
        if ($("#cant-cuotas-f").val() == '6') {
            estCuo1 = $("#estadoCuota1").val();
            ordenPago1 = $("#ordenPago1").val();
            estCuo2 = $("#estadoCuota2").val();
            ordenPago2 = $("#ordenPago2").val();
            estCuo3 = $("#estadoCuota3").val();
            ordenPago3 = $("#ordenPago3").val();
            estCuo4 = $("#estadoCuota4").val();
            ordenPago4 = $("#ordenPago4").val();
            estCuo5 = $("#estadoCuota5").val();
            ordenPago5 = $("#ordenPago5").val();
            estCuo6 = $("#estadoCuota6").val();
            ordenPago6 = $("#ordenPago6").val();
            $.ajax({
                url: _agencia.URL + "/x_sendPago3",
                data: {
                    numFactura: numFactura,
                    estFactura: estFactura,
                    estCuo1: estCuo1,
                    ordenPago1: ordenPago1,
                    estCuo2: estCuo2,
                    ordenPago2: ordenPago2,
                    estCuo3: estCuo3,
                    ordenPago3: ordenPago3,
                    estCuo4: estCuo4,
                    ordenPago4: ordenPago4,
                    estCuo5: estCuo5,
                    ordenPago5: ordenPago5,
                    estCuo6: estCuo6,
                    ordenPago6: ordenPago6
                },
                dataType: "json", type: "post",
                success: function () {
                    jAlert('Se actualizaron los datos correctamente.', $.ucwords(_etiqueta_modulo), function () {
                        $.unblockUI();
                        var urlh = "backend/carpeta/agencia/init/12/2";
                        $(location).attr('href', urlh);
                    });
                }
            });
        }
    });

    $('#send').on('click', function (e) {
        e.preventDefault();
        var opeNombre = $("#opeNombre").val();
        var opeDescripcion = $("#opeDescripcion").val();
        var opeFideicomiso = $("#opeFideicomiso").val();
        var opeCoordinador = $("#opeCoordinador").val();
        var opeJefe = $("#opeJefe").val();
        var listrosMax = $("#listrosMax").val();
        var maxPesos = $("#maxPesos").val();
        var maxHectareas = $("#hectMax").val();
        var opeProveedores = $("#opeProveedores").val();
        var opeBodega = $("#opeBodega").val();
        var opePrecio1 = $("#opeP1").val();
        var opePrecio2 = $("#opeP2").val();
        var opePrecio3 = $("#opeP3").val();
        var opePrecio4 = $("#opeP4").val();
        var opePrecio5 = $("#opeP5").val();
        var opePrecio6 = $("#opeP6").val();
        var formaPago = $("#fpago-select").val();
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
        var data_checklists_persona = [];
        $('.op input:checked').each(function () {
            data_checklists_persona.push($(this).val());
        })

        if (opeNombre == '') {
            jAlert('Ingrese el nombre de la operatoria.', $.ucwords(_etiqueta_modulo), function () {
                $("#opeNombre").focus();
            });
            return false;
        }
        if (opeFideicomiso == '') {
            jAlert('Ingrese el Fideicomiso.', $.ucwords(_etiqueta_modulo), function () {
                $("#opeFideicomiso").focus();
            });
            return false;
        }
        if (data_checklists_persona == '') {
            jAlert('Seleccione requerimientos para los tipo de personas a presentar.', $.ucwords(_etiqueta_modulo), function () {
                $("#humana").focus();
            });
            return false;
        }
        $.ajax({
            url: _agencia.URL + "/x_getIdOperatoria",
            dataType: "json",
            type: "post",
            success: function (data) {
                nuevoID = data;
                $.ajax({
                    url: _agencia.URL + "/x_sendOperatoria",
                    data: {
                        nuevoID: nuevoID,
                        opeNombre: opeNombre,
                        opeDescripcion: opeDescripcion,
                        opeFideicomiso: opeFideicomiso,
                        opeCoordinador: opeCoordinador,
                        opeJefe: opeJefe,
                        listrosMax: listrosMax,
                        maxPesos: maxPesos,
                        checklistsPersona: data_checklists_persona,
                        opePrecio1: opePrecio1,
                        opePrecio2: opePrecio2,
                        opePrecio3: opePrecio3,
                        opePrecio4: opePrecio4,
                        opePrecio5: opePrecio5,
                        opePrecio6: opePrecio6,
                        formaPago: formaPago,
                        maxHectareas: maxHectareas
                    },
                    dataType: "json", type: "post"});
                $.ajax({
                    url: _agencia.URL + "/x_sendProveedores",
                    data: {data_proveedores: data_proveedores, nuevoID: nuevoID},
                    dataType: "json", type: "post", });
                $.ajax({
                    url: _agencia.URL + "/x_sendBodegas",
                    data: {
                        data_bodegas: data_bodegas,
                        nuevoID: nuevoID
                    },
                    dataType: "json",
                    type: "post",
                });
                jAlert('Se guardo operatoria correctamente.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    var urlh = "backend/carpeta/agencia/init/12/7";
                    $(location).attr('href', urlh);
                });
            }
        });
    });
    $('#sincronizar_grillas').on('click', function () {
        var datosBuscar = [];
        var rows = $('#jqxgrid_listado').jqxGrid('getrows');
        for (var i = 0; i < rows.length; i++) {
            datosBuscar.push({
                ID: rows[i].ID,
                NUMERO: rows[i].NUMERO
            });
        }
        $.ajax({
            url: _agencia.URL + "/x_sincronizarAgencia",
            data: {
                datosBuscar: datosBuscar
            },
//            dataType: "json",
            type: "post",
            async: false,
            success: function () {
                jAlert('Se actualizaron los registros.', $.ucwords(_etiqueta_modulo), function () {
                    var urlh = "backend/carpeta/agencia/init/12/2";
                    $(location).attr('href', urlh);
                });
            }
        });
    });
    $('#buscar-ven').on('click', function () {

        var fechaBuscar = $("#fechaBuscarVen").val();
        if (fechaBuscar == '') {
        }
        $("#jqxgrid_listado").hide();
        $("#jqxgrid_listado").show();
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
                {name: 'ID_BODEGA', type: 'int'},
                {name: 'LITROS', type: 'string'},
                {name: 'OBSERVACIONES', type: 'string'},
                {name: 'USU_CARGA', type: 'string'},
                {name: 'USU_CHEQUEO', type: 'string'},
                {name: 'ESTADO', type: 'string'},
                {name: 'PRECIO', type: 'number'},
                {name: 'NETO', type: 'number'},
                {name: 'IVA', type: 'number'},
                {name: 'TOTAL', type: 'number'},
                {name: 'CREATEDON', type: 'string'},
                {name: 'ORDEN_PAGO', type: 'string'},
                {name: 'CANT_CUOTAS', type: 'string'},
                {name: 'VALORPAGAR', type: 'float'},
                {name: 'NUMCUOTA', type: 'int'},
                {name: 'FECHA_VEN', type: 'string'},
                {name: 'CHECK_ESTADO', type: 'string'},
                {name: 'ID_CONTABLE', type: 'int'},
                {name: 'FORMULA', type: 'string'},
                {name: 'IID', type: 'string'}
            ],
            url: 'general/extends/extra/carpetas.php',
            data: {
                accion: "getFacturasAgencia",
                idtipo: 2,
                idpro: _provincia,
                fechaBuscar: fechaBuscar
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
        var cellbeginedit = function (row, datafield, columntype, value) {
            var fila = row;
            if (row == fila)
                return false;
        }
        var cellsrenderer = function (row, column, value, defaultHtml) {
            var fila = row;
            if (column == 'CHECK_ESTADO' && value == 'Confirmada' && row == fila) {
                var element = $(defaultHtml);
                element.css({'background-color': '#32CD32', 'width': '100%', 'height': '100%', 'margin': '0px'});
                return element[0].outerHTML;
            }
            return defaultHtml;
        }

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
                {text: 'ID_CONTABLE', datafield: 'ID_CONTABLE', hidden: true, width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'TITULARIDAD', datafield: 'CHECK_ESTADO', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'ID', datafield: 'ID', width: '6%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'CLIENTE', datafield: 'CLIENTE', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'CUIT', datafield: 'CUIT', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'CONDICION IVA', datafield: 'CONDIVA', width: '18%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'CONDICION IIBB', datafield: 'CONDIIBB', width: '18%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'CBU', datafield: 'CBU', width: '18%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'FACTURA', datafield: 'NUMERO', width: '15%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'FECHA FACTURA', datafield: 'FECHA', width: '12%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, selectable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'BODEGA', datafield: 'BODEGA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'ID BODEGA', datafield: 'ID_BODEGA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, hidden: true, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'LITROS', datafield: 'LITROS', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'OBSERVACIONES', datafield: 'OBSERVACIONES', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'CARGA', datafield: 'USU_CARGA', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'CHEQUEO', datafield: 'USU_CHEQUEO', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'ESTADO', datafield: 'ESTADO', width: '15%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false/*,cellsrenderer: cellsrenderer*/, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'PRECIO', datafield: 'PRECIO', width: '8%', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'NETO', datafield: 'NETO', width: '14%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'IVA', datafield: 'IVA', width: '14%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'CUOTA A PAGAR', datafield: 'VALORPAGAR', width: '15%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'CUOTAS', datafield: 'CANT_CUOTAS', width: '7%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'VENCIMIENTO', datafield: 'FECHA_VEN', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'TOTAL', datafield: 'TOTAL', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'FECHA DE IMPORTACIÓN', datafield: 'CREATEDON', width: '18%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
                {text: 'ORDEN PAGO', datafield: 'ORDEN_PAGO', width: '16%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
//            {text: 'FORMULA', datafield: 'FORMULA', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2'},
                {text: 'IID', datafield: 'IID', width: '0%'}
            ]
        });

    });
    $('#send_edit').on('click', function (e) {
        e.preventDefault();
        var url_con_id = document.location.href;
        var ultimo_id = url_con_id.split("/");
        var el_id = ultimo_id[ultimo_id.length - 1];
        var opeNombre = $("#opeNombre").val();
        var opeDescripcion = $("#opeDescripcion").val();
        var opeFideicomiso = $("#opeFideicomiso").val();
        var opeCoordinador = $("#opeCoordinador").val();
        var opeJefe = $("#opeJefe").val();
        var listrosMax = $("#listrosMax").val();
        var maxPesos = $("#maxPesos").val();
        var maxHectareas = $("#hectMax").val();
        var opeProveedores = $("#opeProveedores").val();
        var opeBodega = $("#opeBodega").val();
        var opePrecio1 = $("#opeP1").val();
        var opePrecio2 = $("#opeP2").val();
        var opePrecio3 = $("#opeP3").val();
        var opePrecio4 = $("#opeP4").val();
        var opePrecio5 = $("#opeP5").val();
        var opePrecio6 = $("#opeP6").val();
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
        var data_checklists_persona = [];
        $('.op input:checked').each(function () {
            data_checklists_persona.push($(this).val());
        })
        if (opeNombre == '') {
            jAlert('Ingrese Nombre Operatoria.', $.ucwords(_etiqueta_modulo), function () {
                $("#opeNombre").focus();
            });
            return false;
        }
        if (opeDescripcion == '') {
            jAlert('Ingrese Descripcion.', $.ucwords(_etiqueta_modulo), function () {
                $("#opeDescripcion").focus();
            });
            return false;
        }
        if (opeFideicomiso == '') {
            jAlert('Ingrese el Fideicomiso.', $.ucwords(_etiqueta_modulo), function () {
                $("#opeFideicomiso").focus();
            });
            return false;
        }
        if (listrosMax == '') {
            jAlert('Ingrese el limite de litros de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {
                $("#listrosMax").focus();
            });
            return false;
        }
        if (maxHectareas == '') {
            jAlert('Ingrese el maximo de hectareas permitido.', $.ucwords(_etiqueta_modulo), function () {
                $("#maxHectareas").focus();
            });
            return false;
        }
        $.ajax({
            url: _agencia.URL + "/x_updateOperatoria",
            data: {
                nuevoID: el_id,
                opeNombre: opeNombre,
                opeDescripcion: opeDescripcion,
                opeFideicomiso: opeFideicomiso,
                opeCoordinador: opeCoordinador,
                opeJefe: opeJefe,
                listrosMax: listrosMax,
                maxPesos: maxPesos,
                checklistsPersona: data_checklists_persona,
                opePrecio1: opePrecio1,
                opePrecio2: opePrecio2,
                opePrecio3: opePrecio3,
                opePrecio4: opePrecio4,
                opePrecio5: opePrecio5,
                opePrecio6: opePrecio6,
                maxHectareas: maxHectareas
            },
            dataType: "json",
            type: "post"});
        $.ajax({
            url: _agencia.URL + "/x_updateProveedores",
            data: {
                data_proveedores: data_proveedores,
                data_bodegas: data_bodegas,
                nuevoID: el_id
            },
            dataType: "json",
            type: "post"});
        jAlert('Se guardo operatoria correctamente.', $.ucwords(_etiqueta_modulo), function () {
            $.unblockUI();
            var urlh = "backend/carpeta/agencia/init/12/7";
            $(location).attr('href', urlh);
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
            url: _agencia.URL + "/x_getDatoProveedor",
            data: {
                ids_proveedores: ids_proveedores,
                firstColumnData: firstColumnData
            },
            dataType: "json",
            type: "post",
            async: false,
            success: function (datos) {
                console.log("Q UE DEVUELVE");
//                alert(datos[0]['ACCION']);
                console.log(datos);
                accion_proveedores_new = datos[0]['ACCION'];
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
                console.log("Viene a este llamado");
                console.log(datarow);
                console.log(accion_proveedores_new);
                /* AGREGAR AQUI VALIDACION DE SI EL USUARIO YA SE ENCUENTRA EN UNA OPERATORIA, SINO ASIGNAR OTRO
                 * 
                 * SELECT * FROM fid_operatoria_proveedores p JOIN fid_operatoria_vino o ON (o.ID_OPERATORIA=p.ID_OPERATORIA)
                 * WHERE p.id_proveedor=1 ORDER BY p.ID_OPERATORIA DESC
                 * */
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
            {text: 'RAZON SOCIAL', datafield: 'RAZON_SOCIAL', width: '49%', cellsalign: 'left', filtercondition: 'starts_with', editable: false},
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
            url: _agencia.URL + "/x_getDatoBodega",
            data: {
                ids_bodegas: ids_bodegas,
                firstColumnData: firstColumnData
            },
            dataType: "json",
            type: "post",
            async: false,
            success: function (datos) {
                console.log("VIENE ACA BODEGA");
                console.log(datos);

                accion_bodegas_new = datos[0]['ACCION'];
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
                console.log("DONDE DEBERIA BORRAR");
                console.log(datarow);
                console.log(accion_bodegas_new);
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
    $(".toolbar li:not(.sub)").click(function (e) {
        e.preventDefault();
        var top = $(this).data('top');
        var obj = [];
        if (top == 'search') {
            $('.consultar').trigger('click');
        } else if (top == 'nueva_f') {
            $('#nuevafactura').trigger('click');
        } else if (top == 'lis_editar') {
            var urlh = "backend/carpeta/agencia/init/12/1";

            $(location).attr('href', urlh);
            show_btns(7);
        } else if (top == 'lis_guardar_enviar') {
//            $('#send').trigger('click');
            var nombre = $('#nombre').val();
            var cuit = $('#cuit').val();
            var cbu = $('#cbu').val();
            var condicioniva = $('#condicioniva').val();
            var condicioniibb = $('#condicioniibb').val();
            var insciibb = $('#insciibb').val();
            var direccion = $('#direccion').val();
            var provincia = $('#provincia').val();
            var subrubro = $('#subrubro').val();
            var telefono = $('#telefono').val();
            var correo = $('#correo').val();
            var observacion = $('#observacion').val();
//        if (opeDescripcion == '') {jAlert('Ingrese Descripcion.', $.ucwords(_etiqueta_modulo), function () {$("#opeDescripcion").focus();});
//            return false;}
//        if (opeCoordinador == '') {jAlert('Seleccione Coordinador de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#opeCoordinador").focus();});
//            return false;}
//        if (opeJefe == '') {jAlert('Seleccione Jefe de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#opeJefe").focus();});
//            return false;}
//        if (listrosMax == '') {jAlert('Ingrese el limite de litros de la Operatoria.', $.ucwords(_etiqueta_modulo), function () {$("#listrosMax").focus();});
//            return false;}
//        if (maxHectareas == '') {jAlert('Seleccione el maximo de hectareas permitido.', $.ucwords(_etiqueta_modulo), function () {$("#maxHectareas").focus();});
//            return false;}
//        if (opeProveedores == '') {jAlert('Seleccione proveedor/es.', $.ucwords(_etiqueta_modulo), function () {$("#maxHectareas").focus();});
//            return false;}
//        if (opeBodega == '') {jAlert('Seleccione bodega/s.', $.ucwords(_etiqueta_modulo), function () {$("#maxHectareas").focus();});
//            return false;}
            $.ajax({
                url: _agencia.URL + "/x_sendCliente",
                dataType: "json",
                type: "post",
                data: {
                    nombre: nombre,
                    cuit: cuit,
                    cbu: cbu,
                    condicioniva: condicioniva,
                    condicioniibb: condicioniibb,
                    insciibb: insciibb,
                    direccion: direccion,
                    provincia: provincia,
                    subrubro: subrubro,
                    telefono: telefono,
                    correo: correo,
                    observacion: observacion
                },
                success: function (data) {
                    jAlert('Operacion Exitosa.\n Recuerde agregar Cliente desde la operatoria para la carga de factura.', $.ucwords(_etiqueta_modulo), function () {
//                        show_btns(1);limpiar_form_fact();$('#send').hide();
                        var urlh = "backend/carpeta/agencia/init/" + _provincia + "/7";
                        $(location).attr('href', urlh);
                    });

                }
            });
        } else if (top == 'lis_guardar_fact') {
            guardar_factura();
        } else if (top == 'lis_mendoza') {
            var urlh = "backend/carpeta/agencia/init/12";
            $(location).attr('href', urlh);
        } else if (top == 'lis_sanjuan') {
            var urlh = "backend/carpeta/agencia/init/17";
            $(location).attr('href', urlh);
        } else if (top == 'lis_addnf') {
            if (_permiso_alta == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            var urlh = "backend/carpeta/agencia/init/" + _provincia + "/1";
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
            var urlh = "backend/carpeta/agencia/init/" + _provincia + "/8";
            $(location).attr('href', urlh);
        } else if (top == 'lis_lis') {
            if (_permiso_ver == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            var urlh = "backend/carpeta/agencia/init/" + _provincia + "/2";
            $(location).attr('href', urlh);
        } else if (top == 'lis_ope') {
            if (_permiso_ver == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            var urlh = "backend/carpeta/agencia/init/" + _provincia + "/7";
            $(location).attr('href', urlh);
        } else if (top == 'inicio') {
            var urlh = "backend/carpeta/agencia";
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
        } else if (top == 'estado_cu') {
            if (_permiso_modificacion == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            editar_estado_cu();

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
        } else if (top == 'detalle_cu') {
            var rowindexes = $('#jqxgrid_listado').jqxGrid('getselectedrowindexes');
            mydata = $('#jqxgrid_listado').jqxGrid('getrowdata', rowindexes);
            if (rowindexes.length == 1) {
                var armado_detalle = '';
                $.ajax({
                    url: _agencia.URL + "/x_getDetalleCu",
                    data: {num_fat: mydata.NUMERO},
                    dataType: "json",
                    type: "post",
                    async: false,
                    success: function (data) {
                        if (data.length > 0) {
                            armado_detalle = '<h2 style="font-size:15px">Estado Cuotas Factura N°: ' + data[0].NUM_FACTURA + '</h2>';
                            armado_detalle += '<ul>';
                            for (var i = 0; i < data.length; i++) {
                                armado_detalle += '<li style="list-style:none;margin-top:5px;">Cuota N° ' + data[i].NUM_CUOTA
                                        + ' =>  Vencimiento: ' + data[i].FECHA_VEN + ' </li>'
                                        + '<li style="margin-left:15px;"> Estado:' + data[i].ESTADO_CUOTA + '</li>'
                                        + '<li style="margin-left:15px;"> Orden Pago:' + data[i].ORDEN_PAGO + ' </li>';
                            }
                            armado_detalle += '</ul>';
                            $.fancybox(
                                    $("#op_cuota").html(armado_detalle),
                                    {'padding': 80, 'autoScale': true, 'scrolling': 'auto'}
                            );
                        } else {
                            jAlert('No posee cuotas.', $.ucwords(_etiqueta_modulo));
                        }
                    }
                });
            } else if (rowindexes.length >= 2) {
                jAlert('Se seleccionaron mas de un comprobante, debe seleccionar uno para ver el detalle.', $.ucwords(_etiqueta_modulo));
            } else {
                jAlert('No se selecciono comprobante, debe seleccionar un comprobante para ver el detalle.', $.ucwords(_etiqueta_modulo));
            }

        } else if (top == 'export') {
//imprimir_listado_seleccionado();
        } else if (top == 'desmarcar_cu') {
            $('#jqxgrid_listado').jqxGrid('clearselection');
        } else if (top == 'lis_importar') {
            if (_permiso_alta == 0) {
                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            var urlh = "backend/carpeta/agencia/init/3";
            $(location).attr('href', urlh);
        } else if (top == 'impor_procesar') {
//            importar_procesar();
            _imp_procesar();
        } else if (top == 'impor_revision') {
            var urlh = "backend/carpeta/agencia/init/4";
            $(location).attr('href', urlh);
        } else if (top == 'addOpe') {
            $('.nuevaOpe_form').show();
        } else if (top == 'testing') {
            $.ajax({
                url: _agencia.URL + "/x_testing",
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
//    agregarCIUS();
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
    $("#porcentaje_iva").keyup(function () {
        factor = $('#porcentaje_iva').val();
        var iva = factor * $("#neto").val() / 100;
        $("#iva").val(dec(iva, 2));
        var total = 1 * $("#neto").val() + 1 * $("#iva").val();
        $("#total").val(dec(total, 2));
    });
    $("#precio").keyup(function () {
        cambiarPrecio();
    });
//    $("#precio,#retencion").keyup(function () {
//        cambiarPrecio();
//    });

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
    if (_opcion == 10) {
        editar_formulario_estado_cu();
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
                    url: _agencia.URL + "/x_borrar_file",
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
    $.blockUI({message: '<h4><img src="general/images/block-loader.gif" /> Cargando</h4>'});
    $("#send").hide();
    $('.tb_regresar_ope').show();
    $('.tb_atras').hide();
    var num_fact_buscar = 0;
    $.ajax({
        url: _agencia.URL + "/x_getobj",
        data: {id_objeto: _id_objeto},
        dataType: "json",
        type: "post",
        success: function (rtn) {
            var arr_check = rtn.CHECK_TITULARIDAD;
            data = rtn.factura;
            $("#idh").val(data.ID);
            $("#numOperatoria").val(data.ID_OPERATORIA);
            $("#cuitform").val(data.CUIT);
            $("#nombre2").val(data.RAZ);
            var forma_pago = 0;
//            $("#fecha").val(formattedDate(data.FECHA));
//            $("#fecha").val(data.FECHA);
            if (data.NUMERO) {
                var fecha_string = data.FECHA;
                $("#fecha").val(fecha_string.substr(0, 10));
                var fecha_vto_string = '';
                if (data.FECHAVTO != null) {
                    fecha_vto_string = data.FECHAVTO;
                    $("#fechavto").val(fecha_vto_string.substr(0, 10));
                } else {
                    $("#fechavto").val('');
                }

                $("#fecha").datepicker('disable');
//                $("#fechavto").datepicker('disable');
                $("#numero").val(data.NUMERO).attr("readonly", "readonly");
                num_fact_buscar = data.NUMERO;
                $("#cai").val(data.CAI).attr("readonly", "readonly");
                //            $("#fechavto").val(formattedDate(data.FECHAVTO));$("#fechavto").val(data.FECHAVTO);

//                $("#ltros").val(data.LITROS);//.attr("readonly", "readonly");
                $("#precio").val(data.PRECIO);//.attr("readonly", "readonly");
                $("#observacion_fact").val(data.OBSERVACIONES);
                $("#neto").val(data.NETO).attr("readonly", "readonly");
                $("#iva").val(data.IVA).attr("readonly", "readonly");
                $("#total").val(data.TOTAL).attr("readonly", "readonly");
                $("#porcentaje_iva").val(data.PORC_IVA).attr("readonly", "readonly");

                forma_pago = data.FORMA_PAGO;

                $.ajax({
                    url: _agencia.URL + "/x_getFormasPago",
                    datatype: 'html',
                    type: 'post',
                    async: false,
                    data: {id: data.NUMERO},
                    success: function (data) {

                        $('#fpago').html(data);
                        $("#fpago-select").chosen({width: "220px"});
                        $("#fpago-select").val(forma_pago).trigger("chosen:updated");
                        $("#fpago-select").trigger('change');
                    }
                })
//                $.ajax({
//                    url: _agencia.URL + "/x_getFormasPago",
//                    datatype: 'html',
//                    type: 'post',
//                    async: false,
//                    data: {id: data.ID_OPERATORIA},
//                    success: function (data) {
//                        $('#fpago').html(data);
//                        $("#fpago-select").chosen({width: "220px"});
//                    }
//                })
//                $("#fpago-select").val(data.FORMA_PAGO).attr('disabled', true).trigger("chosen:updated");
//                $("#fpago-select").val(data.FORMA_PAGO).attr('enable', true).trigger("chosen:updated");
//                var data_checklists_persona = [];
//                var listado_checklist = data.CHECKLIST_PERSONA;
//                data_checklists_persona = listado_checklist.split(',');
//                $('.op input').each(function () {
//                    if ($.inArray($(this).val(), data_checklists_persona) >= 0) {
//                        $(this).prop('checked', true);
//                    }
//                });
//                if (arr_check == 1) {
//                    $("#cambio_titularidad").attr('checked', true);
//                    $.ajax({
//                        url: _agencia.URL + "/x_getTitularidad",
//                        datatype: 'html',
//                        type: 'post',
//                        async: false,
//                        data: {num_factura: num_fact_buscar},
//                        success: function (datos) {
//                            $("#cambio_titularidad").hide();
//                            $("#cambio_titularidad_true").show();
//                            $("#cambio_titularidad_true").attr('checked', true);
//                            $("#comentario-titularidad").text(datos);
//                        }
//                    })
////                $.ajax({
////                    url: _agencia.URL + "/x_getChecklistHumanaFactTitu",datatype: 'html',type: 'post',async: false,
////                    data: {id: data.ID_OPERATORIA, num_factura: num_fact_buscar},
////                    success: function (data) {$('#check_datos').html(data);$("#cambio_titularidad").hide();$("#cambio_titularidad_true").show();$("#cambio_titularidad_true").attr('checked', true);$("#comentario-titularidad").text(datos);}})
//                } else {
////                $.ajax({
////                    url: _agencia.URL + "/x_getChecklistHumanaFact",datatype: 'html',type: 'post',async: false,
////                    data: {id: data.ID_OPERATORIA},
////                    success: function (data) {$('#check_datos').html(data);
//                    $("#cambio_titularidad").attr('checked', false);
////                    }})
//                }

                var sourceope_titularidad = {
                    datatype: "json",
                    type: "post",
                    datafields: [
                        {name: 'ID_FACTURA', type: 'int'}, {name: 'NOMBRE', type: 'string'},
                        {name: 'FECHA', type: 'datetime'}, {name: 'CHECK_ESTADO', type: 'string'}
                    ],
                    url: _agencia.URL + "/x_getTitularidad",
                    data: {num_factura: $("#numero").val()},
                    async: false
                };
                var dataAdapterope_titularidad = new $.jqx.dataAdapter(sourceope_titularidad,
                        {formatData: function (data) {
                                data.name_startsWith = $("#searchField").val();
                                return data;
                            }}
                );
                $("#jqxgridtitularidad").jqxGrid({
                    width: '50%',
                    height: '200px',
                    source: dataAdapterope_titularidad,
                    theme: 'energyblue',
                    selectionmode: 'singlerows',
                    localization: getLocalization(),
                    columns: [
                        {text: 'ID_FACTURA', datafield: 'ID_FACTURA', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, hidden: true},
                        {text: 'USUARIO', datafield: 'NOMBRE', width: '25%', cellsalign: 'left', filtercondition: 'starts_with', editable: false},
                        {text: 'FECHA', datafield: 'FECHA', cellsalign: 'left', width: '50%', filtercondition: 'starts_with', editable: true},
                        {text: 'ESTADO', datafield: 'CHECK_ESTADO', cellsalign: 'left', width: '25%', filtercondition: 'starts_with', editable: true},
                    ]
                });
                $.unblockUI();
            } else {
                $("#fecha").val();
                $("#fechavto").val();
                $("#numero").val();
                $("#cai").val();
                $("#bodega").chosen({width: "220px"});
                $("#bodega").val(data.ID_BODEGA).attr('disabled', true).trigger("chosen:updated");
                $("#ltros").val(data.LITROS).attr("readonly", "readonly");
                $("#precio").val(data.PRECIO).attr("readonly", "readonly");
                $("#numVinedo").val(data.VINEDO);
                $("#numRut").val(data.RUT);
                $("#observacion_fact").val(data.OBSERVACIONES);
                $("#neto").val(data.NETO).attr("readonly", "readonly");
                $("#iva").val(data.IVA).attr("readonly", "readonly");
                $("#total").val(data.TOTAL).attr("readonly", "readonly");
                $("#porcentaje_iva").val(data.PORC_IVA).attr("readonly", "readonly");
                $.ajax({
                    url: _agencia.URL + "/x_getAlgunasBodegas",
                    datatype: 'html',
                    type: 'post',
                    async: false,
                    data: {id: data.ID_OPERATORIA},
                    success: function (data) {
                        $('#indent_prueba').html(data);
                        $("#proveedor-jquery").chosen({width: "220px"});
                    }
                })
                $("#proveedor-jquery").val(data.ID_BODEGA).attr('enable', true).trigger("chosen:updated");
                $.ajax({
                    url: _agencia.URL + "/x_getChecklistHumanaFact",
                    datatype: 'html',
                    type: 'post',
                    async: false,
                    data: {id: data.ID_OPERATORIA},
                    success: function (data) {
                        $('#check_datos').html(data);
                    }
                })
//                var cant_pagos = 0;
                $.ajax({
                    url: _agencia.URL + "/x_getFormasPago",
                    datatype: 'html',
                    type: 'post',
                    async: false,
                    data: {id: data.ID_OPERATORIA},
                    success: function (data) {
                        $('#fpago').html(data);
                        $("#fpago-select").chosen({width: "220px"});
                    }
                })
                $("#fpago-select").val(data.FORMA_PAGO).attr('enable', true).trigger("chosen:updated");
//                var data_checklists_persona = [];
//                var listado_checklist = data.CHECKLIST_PERSONA;
//                data_checklists_persona = listado_checklist.split(',');
//                $('.op input').each(function () {
//                    if ($.inArray($(this).val(), data_checklists_persona) >= 0) {
//                        $(this).prop('checked', true);
//                    }
//                });
                if (arr_check == 1) {
                    $("#cambio_titularidad").attr('checked', true);
                    $.ajax({
                        url: _agencia.URL + "/x_getTitularidad",
                        datatype: 'html',
                        type: 'post',
                        async: false,
                        data: {num_factura: num_fact_buscar},
                        success: function (datos) {
                            console.log("VER Q TRAE");
                            console.log(datos);
                            $("#cambio_titularidad").hide();
                            $("#cambio_titularidad_true").show();
                            $("#cambio_titularidad_true").attr('checked', true);
                            $("#comentario-titularidad").text(datos);
                        }
                    })
                } else {
                    $("#cambio_titularidad").attr('checked', false);
                }

                var sourceope_titularidad = {
                    datatype: "json",
                    type: "post",
                    datafields: [
                        {name: 'ID_FACTURA', type: 'int'}, {name: 'NOMBRE', type: 'string'},
                        {name: 'FECHA', type: 'datetime'}, {name: 'CHECK_ESTADO', type: 'string'}
                    ],
                    url: _agencia.URL + "/x_getTitularidad",
                    data: {num_factura: $("#numero").val()},
                    async: false
                };
                var dataAdapterope_titularidad = new $.jqx.dataAdapter(sourceope_titularidad,
                        {formatData: function (data) {
                                data.name_startsWith = $("#searchField").val();
                                return data;
                            }}
                );
                $("#jqxgridtitularidad").jqxGrid({
                    width: '50%',
                    height: '200px',
                    source: dataAdapterope_titularidad,
                    theme: 'energyblue',
                    selectionmode: 'singlerows',
                    localization: getLocalization(),
                    columns: [
                        {text: 'ID_FACTURA', datafield: 'ID_FACTURA', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, hidden: true},
                        {text: 'USUARIO', datafield: 'NOMBRE', width: '25%', cellsalign: 'left', filtercondition: 'starts_with', editable: false},
                        {text: 'FECHA', datafield: 'FECHA', cellsalign: 'left', width: '50%', filtercondition: 'starts_with', editable: true},
                        {text: 'ESTADO', datafield: 'CHECK_ESTADO', cellsalign: 'left', width: '25%', filtercondition: 'starts_with', editable: true},
                    ]
                });
                $.unblockUI();
            }
        }
    });

    $("#precio").keyup(function () {
        cambiarPrecio();
    });
//    $("#precio").keyup(function () {
//        cambiarPrecio();
//    });
}


function editar_formulario_estado_cu() {
    var url_con_id = document.location.href;
    var ultimo_id = url_con_id.split("/");
    var el_id = ultimo_id[ultimo_id.length - 1];
    var pagoshtml = '';
    $.ajax({
        url: _agencia.URL + "/x_getfactura",
        data: {id_objeto: el_id},
        dataType: "json",
        type: "post",
        success: function (rtn) {
            if (rtn.length > 0) {
                $("#numFactura").val(rtn[0].NUMERO);
                $("#cant-cuotas-f").val(rtn[0].FORMA_PAGO);
                $("#estFact").chosen({width: "220px"});
                $("#estFact").val(rtn[0].ID_ESTADO).trigger("chosen:updated");
                for (var i = 0; i < rtn.length; i++) {
                    pagoshtml += '<div class="elem elem_med_cond"><label class="der">N° Cuota:' + rtn[i].NUM_CUOTA + '</label>'
                            + '<div class="indent formtext">'
                            + '<input type="text" class="tip-right" title="numcuota" id="cuota' + rtn[i].NUM_CUOTA + '" value="' + rtn[i].VALOR_CUOTA + '">'
                            + '</div>'
                            + '</div>';

                    pagoshtml += '<div class="elem elem_med"><label class="der">ESTADO:</label><div class="indent" id="">'
                            + '<select class="chzn-select medium-select select" id="estadoCuota' + rtn[i].NUM_CUOTA + '">'
                            + '<option value="0">No enviada</option><option value="1">Pendiente</option><option value="2">Pagado</option>'
                            + '</select></div></div>';

                    pagoshtml += '<div class="elem elem_med_cond">'
                            + ' <label class="der">Orden de pago:</label><div class="indent formtext">'
                            + '<input type="text" title="OrdenPago" id="ordenPago' + rtn[i].NUM_CUOTA + '" value="' + rtn[i].ORDEN_PAGO + '"></div></div>'
                            + '<div style="margin-top:50px;" class="clear"></div>';
                }
                $("#estado-cuota").html(pagoshtml);
                if (rtn[0].FORMA_PAGO == 1) {
                    $("#estadoCuota1").chosen({width: "220px"});
                    $("#estadoCuota1").val(rtn[0].ESTADO_CUOTA).trigger("chosen:updated");
                }
                if (rtn[0].FORMA_PAGO == 2) {
                    $("#estadoCuota1").chosen({width: "220px"});
                    $("#estadoCuota1").val(rtn[0].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota2").chosen({width: "220px"});
                    $("#estadoCuota2").val(rtn[1].ESTADO_CUOTA).trigger("chosen:updated");
                }
                if (rtn[0].FORMA_PAGO == 3) {
                    $("#estadoCuota1").chosen({width: "220px"});
                    $("#estadoCuota1").val(rtn[0].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota2").chosen({width: "220px"});
                    $("#estadoCuota2").val(rtn[1].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota3").chosen({width: "220px"});
                    $("#estadoCuota3").val(rtn[2].ESTADO_CUOTA).trigger("chosen:updated");
                }
                if (rtn[0].FORMA_PAGO == 4) {
                    $("#estadoCuota1").chosen({width: "220px"});
                    $("#estadoCuota1").val(rtn[0].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota2").chosen({width: "220px"});
                    $("#estadoCuota2").val(rtn[1].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota3").chosen({width: "220px"});
                    $("#estadoCuota3").val(rtn[2].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota4").chosen({width: "220px"});
                    $("#estadoCuota4").val(rtn[3].ESTADO_CUOTA).trigger("chosen:updated");
                }
                if (rtn[0].FORMA_PAGO == 5) {
                    $("#estadoCuota1").chosen({width: "220px"});
                    $("#estadoCuota1").val(rtn[0].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota2").chosen({width: "220px"});
                    $("#estadoCuota2").val(rtn[1].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota3").chosen({width: "220px"});
                    $("#estadoCuota3").val(rtn[2].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota4").chosen({width: "220px"});
                    $("#estadoCuota4").val(rtn[3].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota5").chosen({width: "220px"});
                    $("#estadoCuota5").val(rtn[4].ESTADO_CUOTA).trigger("chosen:updated");
                }
                if (rtn[0].FORMA_PAGO == 6) {
                    $("#estadoCuota1").chosen({width: "220px"});
                    $("#estadoCuota1").val(rtn[0].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota2").chosen({width: "220px"});
                    $("#estadoCuota2").val(rtn[1].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota3").chosen({width: "220px"});
                    $("#estadoCuota3").val(rtn[2].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota4").chosen({width: "220px"});
                    $("#estadoCuota4").val(rtn[3].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota5").chosen({width: "220px"});
                    $("#estadoCuota5").val(rtn[4].ESTADO_CUOTA).trigger("chosen:updated");
                    $("#estadoCuota6").chosen({width: "220px"});
                    $("#estadoCuota6").val(rtn[5].ESTADO_CUOTA).trigger("chosen:updated");
                }
            } else {
                jAlert('No se ha generado ninguna forma de pago en la cuota con el ID ' + el_id + '.', $.ucwords(_etiqueta_modulo), function () {
                    var urlh = "backend/carpeta/agencia/init/12/2";
                    $(location).attr('href', urlh);
                });
            }
        }
    });
}

function editar_formulario_operatoria() {
    $.blockUI({message: '<h4><img src="general/images/block-loader.gif" /> Cargando</h4>'});
    var accion_proveedores = '';
    var accion_bodegas = '';
    var url_con_id = document.location.href;
    var ultimo_id = url_con_id.split("/");
    var el_id = ultimo_id[ultimo_id.length - 1];
    $('#send').hide();
    $('#send_edit').show();
    $('#fecha_ven_edit').show();
    $.ajax({
        url: _agencia.URL + "/x_getoperatoria",
        data: {id_objeto: el_id},
        dataType: "json",
        type: "post",
        success: function (rtn) {
            $("#opeNombre").val(rtn[0].NOMBRE_OPE);
            $("#fechavto").datepicker('enable');
//          $("#fechavto").val(formattedDate(data.FECHAVTO));
//          $("#fechavto").val(data.FECHAVTO);
            $("#fechavto").val(rtn[0].FECHA_VEN);
            $("#opeDescripcion").val(rtn[0].DESCRIPCION_OPE);
            $("#opeFideicomiso").val(rtn[0].ID_FIDEICOMISO).trigger("chosen:updated");
            $("#listrosMax").val(rtn[0].LTRS_MAX);
            $("#maxPesos").val(rtn[0].MAX_PESOS);
            $("#hectMax").val(rtn[0].HECT_MAX);
            $("#opeCoordinador").val(rtn[0].ID_COORDINADOR_OPE).attr('eneable', true).trigger("chosen:updated");
            $("#opeJefe").val(rtn[0].ID_JEFE_OPE).attr('eneable', true).trigger("chosen:updated");
            $("#opeP1").val(rtn[0].PRECIO_1);
            $("#opeP2").val(rtn[0].PRECIO_2);
            $("#opeP3").val(rtn[0].PRECIO_3);
            $("#opeP4").val(rtn[0].PRECIO_4);
            $("#opeP5").val(rtn[0].PRECIO_5);
            $("#opeP6").val(rtn[0].PRECIO_6);
            $("#opePCuota").val(rtn[0].PRECIO_CUOTA);
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
            var data_checklists_persona = [];
            var listado_checklist = rtn[0].CHECKLIST_PERSONA;
            data_checklists_persona = listado_checklist.split(',');
            $('.op input').each(function () {
                if ($.inArray($(this).val(), data_checklists_persona) >= 0) {
                    $(this).prop('checked', true);
                }
            });
            $.ajax({
                url: _agencia.URL + "/x_getOperatoriaProveedores",
                data: {id_operatoria: el_id},
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
                        url: _agencia.URL + "/x_getProveedoresEdit",
                        data: {id_operatoria: el_id},
                        async: false, addrow: function (rowid, rowdata, position, commit) {
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
                            {formatData: function (data) {
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
                            url: _agencia.URL + "/x_getDatoProveedor",
                            data: {ids_proveedores: ids_proveedores, firstColumnData: firstColumnData},
                            dataType: "json",
                            type: "post",
                            async: false,
                            success: function (datos) {
                                accion_proveedores = datos[0]['ACCION'];
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
                                    console.log("PASO X ACA ELIMINAR");
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
                url: _agencia.URL + "/x_getOperatoriaBodegas",
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
                        url: _agencia.URL + "/x_getBodegasEdit",
                        data: {
                            id_operatoria: el_id
                        },
                        async: false, addrow: function (rowid, rowdata, position, commit) {
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
                            url: _agencia.URL + "/x_getDatoBodega",
                            data: {
                                ids_bodegas: ids_bodegas,
                                firstColumnData: firstColumnData
                            },
                            dataType: "json",
                            type: "post",
                            async: false,
                            success: function (datos) {
                                accion_bodegas = datos[0]['ACCION'];
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
                                }
                            });
                        },
                        columns: [
                            {text: 'ID', datafield: 'ID', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, hidden: true},
                            {text: 'NOMBRE', datafield: 'NOMBRE', cellsalign: 'left', filtercondition: 'starts_with', editable: false},
                            {text: 'LIMITE LTRS', datafield: 'LIMLTRS', cellsalign: 'left', width: '30%', filtercondition: 'starts_with', editable: true}
                        ]
                    });
                    $.unblockUI();
                }
            });
        }
    });
}


//function agregarCIUS(_arr_cius) {
//
//    _arr_cius || (_arr_cius = []);
//    var source = {
//        datatype: "json",
//        datafields: [
//            {name: 'NUM'},
//            {name: 'KGRS', type: 'number'},
//            {name: 'AZUCAR'},
//            {name: 'CHEQUEO', type: 'bool'},
//            {name: 'INSC'},
//            {name: 'ID'}
//        ],
//        url: _agencia.URL + '/x_get_info_bancos',
//        deleterow: function (rowid, commit) {
//            commit(true);
//        }
//    };
//    $("#jqxgridcius").jqxGrid({
//        width: '98%',
//        height: '200px',
//        source: source,
//        theme: 'energyblue',
//        editable: true,
//        ready: function () {
//            $("#jqxgridcius").jqxGrid('hidecolumn', 'ID');
//            if (_arr_cius.length > 0) {
//                //colocar
//                $.each(_arr_cius, function (k, v) {
//                    var data = {
//                        'NUM': v.ciu_num,
//                        'KGRS': v.ciu_kgrs,
//                        'AZUCAR': v.ciu_azucar,
//                        'CHEQUEO': v.ciu_chequeo,
//                        'INSC': v.ciu_insc,
//                        'ID': 'DDDDDDD',
//                        'uid': 1
//                    }
//                    var commit = $("#jqxgridcius").jqxGrid('addrow', null, data);
//                    $('#jqxgridcius').jqxGrid('selectrow', data.uid);
//                    var selectedrowindex = $("#jqxgridcius").jqxGrid('getselectedrowindex');
//                });
//            }
//        },
//        columnsresize: true,
//        localization: getLocalization(),
//        showstatusbar: true,
//        renderstatusbar: function (statusbar) {
//            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;'></div>");
//            var deleteButton = $("<div style='float: left; margin-left: 5px;'><img style='position: relative; margin-top: 2px;' src='general/css/images/delete.png'/><span style='margin-left: 4px; position: relative; top: -3px;'>Borrar</span></div>");
//            container.append(deleteButton);
//            statusbar.append(container);
//            deleteButton.jqxButton({theme: theme, width: 65, height: 20});
//            deleteButton.click(function (event) {
//                var selectedrowindex = $("#jqxgridcius").jqxGrid('getselectedrowindex');
//                var rowscount = $("#jqxgridcius").jqxGrid('getdatainformation').rowscount;
//                if (selectedrowindex < rowscount) {
//
//                    jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo), function (r) {
//                        if (r == true) {
//
//                            if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
//                                var id = $("#jqxgridcius").jqxGrid('getrowid', selectedrowindex);
//                                $("#jqxgridcius").jqxGrid('deleterow', id);
//                            }
//
//                            //actualizar suma
//                            var griddata = $('#jqxgridcius').jqxGrid('getdatainformation');
//                            var _arr_aportes_tmp = [];
//                            for (var i = 0; i < griddata.rowscount; i++)
//                                _arr_aportes_tmp.push($('#jqxgridcius').jqxGrid('getrenderedrowdata', i));
//                            var total = 0;
//                            var total1 = 0;
//                            if (griddata.rowscount == 0) {
//                                $("#suma_aporte").html('');
//                                $(".suma_aportes").hide();
//                            } else {
//                                if (_arr_aportes_tmp.length > 0) {
//                                    //colocar
//                                    $.each(_arr_aportes_tmp, function (k, v) {
//                                        total = total + parseFloat(v.KGRS);
//                                        total1 = total1 + parseFloat(v.AZUCAR * v.KGRS);
//                                    });
//                                    total1 = total1 / total;
//                                    $(".suma_aportes").show();
//                                    $("#suma_aporte").html(dec(precise_round(total, 2), 2));
//                                    $("#suma_aporte1").html(dec(precise_round(total1, 2), 2));
//                                }
//                            }
//                        }
//                    });
//                } else {
//                    jAlert('Seleccione un item.', $.ucwords(_etiqueta_modulo), function () {
//                    });
//                    return false;
//                }
//            });
//        },
//        columns: [
//            {text: 'NUM CIU', datafield: 'NUM', width: '20%', editable: false},
//            {text: 'KILOGRAMOS', datafield: 'KGRS', width: '30%', editable: false, cellsformat: 'c2'},
//            {text: 'AZUCAR', datafield: 'AZUCAR', width: '30%', editable: false},
//            {text: 'INSCR', datafield: 'INSC', width: '30%', editable: false},
//            {text: 'VERIFICACION', datafield: 'CHEQUEO', width: '20%', columntype: 'checkbox', editable: true},
//            {text: 'ID', datafield: 'ID', width: '0%', editable: false}
//        ]
//    });
////    $("#add_cius").off().on('click', function () {
////
////        if ($("#frm_cargacius input#ciu_iva").val() == '' || $("#frm_cargacius input#ciu_total").val() == ''
////                || $("#frm_cargacius input#ciu_azucar").val() == '') {
////            jAlert('Todos los campos son obligatorios.', $.ucwords(_etiqueta_modulo), function () {
////                $("#frm_cargacius input").first().select();
////            });
////            return false;
////        }
////
////        var ciu_num = $("#ciu_num").val();
////        var ciu_kgrs = $("#ciu_kgrs").val();
////        var ciu_azucar = $("#ciu_azucar").val();
////        var ciu_insc = $("#ciu_insc").val();
////        if (!isnumeroCiu(ciu_num)) {
////            jAlert('El formato del Número de Ciu no es correcto (Ejem: A9854124).', $.ucwords(_etiqueta_modulo), function () {
////                $("#frm_cargacius input").first().select();
////            });
////            return false;
////        }
////
////        if (!isnumeroCiuIns(ciu_insc)) {
////            jAlert('El formato del Número de Inscripcion no es correcto(Ejem: A-9854124).', $.ucwords(_etiqueta_modulo), function () {
////                $("#frm_cargacius #ciu_insc").first().next().next().select();
////            });
////            return false;
////        }
////
//////recorrer el grid, si ya eciste el ciu, alertar y no agregar
////        var griddata = $('#jqxgridcius').jqxGrid('getdatainformation');
////        var _arr_cius = [];
////        for (var i = 0; i < griddata.rowscount; i++)
////            _arr_cius.push($('#jqxgridcius').jqxGrid('getrenderedrowdata', i));
////        sw1 = 0;
////        if (_arr_cius) {
////            $.each(_arr_cius, function (index, value) {
////                if (value.NUM == ciu_num) {
////                    jAlert('Este numero de CIU ya esta agregado.', $.ucwords(_etiqueta_modulo), function () {
////                        $("#ciu_num").select();
////                    });
////                    sw1 = 1;
////                    return false;
////                }
////            });
////        }
////
////        if (sw1 == 1) {
////            return false;
////        }
////
//////validar ciu a traves de todas las bd
////        $.ajax({
////            url: _agencia.URL + "/x_verificarciu",
////            data: {
////                nciu: ciu_num
////            },
////            dataType: "json",
////            type: "post",
////            success: function (data) {
////                console.dir(data);
////                if (data <= 0) {
////                    var data = {
////                        'NUM': ciu_num,
////                        'KGRS': ciu_kgrs,
////                        'AZUCAR': ciu_azucar,
////                        'CHEQUEO': 0,
////                        'INSC': ciu_insc,
////                        'ID': 'DDDDDDD',
////                        'uid': 1
////                    }
////
////                    var commit = $("#jqxgridcius").jqxGrid('addrow', null, data);
////                    $('#jqxgridcius').jqxGrid('selectrow', data.uid);
////                    var selectedrowindex = $("#jqxgridcius").jqxGrid('getselectedrowindex');
////                    //$('#jqxgridbancos').jqxGrid( { editable: true} );
////                    //var editable = $("#jqxgridbancos").jqxGrid('begincelledit', selectedrowindex, "BANCO");
////
////
////                    //actualizar suma
////                    var griddata = $('#jqxgridcius').jqxGrid('getdatainformation');
////                    var _arr_aportes_tmp = [];
////                    for (var i = 0; i < griddata.rowscount; i++)
////                        _arr_aportes_tmp.push($('#jqxgridcius').jqxGrid('getrenderedrowdata', i));
////                    var total = 0;
////                    var total1 = 0;
////                    if (griddata.rowscount == 0) {
////                        $("#suma_aporte").html('');
////                        $(".suma_aportes").hide();
////                    } else {
////                        if (_arr_aportes_tmp.length > 0) {
////                            //colocar
////                            $.each(_arr_aportes_tmp, function (k, v) {
////                                total = total + parseFloat(v.KGRS);
////                                total1 = total1 + parseFloat(v.AZUCAR * v.KGRS);
////                            });
////                            total1 = total1 / total;
////                            $(".suma_aportes").show();
////                            $("#suma_aporte").html(dec(precise_round(total, 2), 2));
////                            $("#suma_aporte1").html(dec(precise_round(total1, 2), 2));
////                        }
////                    }
////
////                    $("#frm_cargacius input").not('#add_cius').val('');
////                    $("#frm_cargacius input").first().focus();
////                } else {
////                    jAlert('Este numero de CIU ya existe. Vefique los datos por favor.', $.ucwords(_etiqueta_modulo), function () {
////                        $.unblockUI();
////                    });
////                }
////
////            }
////        });
////        return false;
////    });
//}


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
                url: _agencia.URL + "/x_delupload_nota",
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
            url: _agencia.URL + "/x_sendnota",
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
        url: _agencia.URL + "/x_getform_agregar_requerimiento",
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
        url: _agencia.URL + "/x_getvincular",
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
                            url: _agencia.URL + "/x_vincular_nr",
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
        url: _agencia.URL + "/x_getenviar_a1",
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
                    url: _agencia.URL + "/x_getenviar_a2",
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
                                        url: _agencia.URL + "/x_guardar_traza_nota",
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
            url: _agencia.URL + "/x_getlocalidad",
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
            accion: "getFacturasAgencia",
            idtipo: 2,
            estado: '12' // en revision
        },
        async: false,
        deleterow: function (rowid, commit) {
            process_data(_agencia.URL + "/x_delete_facturas_cu", mydata);
//            console.dir(mydata);
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
//            {name: 'BODEGA', type: 'string'},
//            {name: 'ID_BODEGA', type: 'int'},
//            {name: 'LITROS', type: 'string'},
            {name: 'OBSERVACIONES', type: 'string'},
            {name: 'USU_CARGA', type: 'string'},
            {name: 'USU_CHEQUEO', type: 'string'},
            {name: 'ESTADO', type: 'string'},
            {name: 'PRECIO', type: 'number'},
            {name: 'NETO', type: 'number'},
            {name: 'IVA', type: 'number'},
            {name: 'TOTAL', type: 'number'},
            {name: 'CREATEDON', type: 'string'},
            {name: 'ORDEN_PAGO', type: 'string'},
            {name: 'CANT_CUOTAS', type: 'string'},
            {name: 'VALORPAGAR', type: 'float'},
            {name: 'NUMCUOTA', type: 'int'},
            {name: 'FECHA_VEN', type: 'string'},
//            {name: 'CHECK_ESTADO', type: 'string'},
//            {name: 'ID_CONTABLE', type: 'int'},
            {name: 'FORMULA', type: 'string'},
            {name: 'IID', type: 'string'}
        ],
        url: 'general/extends/extra/carpetas.php',
        data: {
            accion: "getFacturasAgencia",
            idtipo: 2,
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
    var cellbeginedit = function (row, datafield, columntype, value) {
        var fila = row;
        if (row == fila)
            return false;
    }
    var cellsrenderer = function (row, column, value, defaultHtml) {
        var fila = row;
        if (column == 'CHECK_ESTADO' && value == 'Confirmada' && row == fila) {
            var element = $(defaultHtml);
            element.css({'background-color': '#32CD32', 'width': '100%', 'height': '100%', 'margin': '0px'});
            return element[0].outerHTML;
        }
        return defaultHtml;
    }

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
            {text: 'ID', datafield: 'ID', width: '6%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'CLIENTE', datafield: 'CLIENTE', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'CUIT', datafield: 'CUIT', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'CONDICION IVA', datafield: 'CONDIVA', width: '18%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'CONDICION IIBB', datafield: 'CONDIIBB', width: '18%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'CBU', datafield: 'CBU', width: '18%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'FACTURA', datafield: 'NUMERO', width: '15%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'FECHA FACTURA', datafield: 'FECHA', width: '12%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, selectable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'OBSERVACIONES', datafield: 'OBSERVACIONES', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'CARGA', datafield: 'USU_CARGA', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'CHEQUEO', datafield: 'USU_CHEQUEO', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'ESTADO', datafield: 'ESTADO', width: '15%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false/*,cellsrenderer: cellsrenderer*/, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'PRECIO', datafield: 'PRECIO', width: '8%', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'NETO', datafield: 'NETO', width: '14%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'IVA', datafield: 'IVA', width: '14%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'CUOTA A PAGAR', datafield: 'VALORPAGAR', width: '15%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'CUOTAS', datafield: 'CANT_CUOTAS', width: '7%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'VENCIMIENTO', datafield: 'FECHA_VEN', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'TOTAL', datafield: 'TOTAL', width: '20%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: false, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'FECHA DE IMPORTACIÓN', datafield: 'CREATEDON', width: '18%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'ORDEN PAGO', datafield: 'ORDEN_PAGO', width: '16%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true, cellsformat: 'c2', cellbeginedit: cellbeginedit, cellsrenderer: cellsrenderer},
            {text: 'IID', datafield: 'IID', width: '0%'}
        ]
    });
    var sourceope_ope = {
        datatype: "json",
        datafields: [
            {name: 'ID_OPERATORIA', type: 'int'},
            {name: 'NOMBRE_OPE', type: 'string'},
            {name: 'DESCRIPCION_OPE', type: 'string'},
            {name: 'FECHA_CRE', type: 'datetime'},
            {name: 'FECHA_VEN', type: 'datetime'},
            {name: 'NOMBRE_COOR', type: 'string'},
            {name: 'NOMBRE_JEFE', type: 'string'}
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
            {text: 'OPERATORIA', datafield: 'ID_OPERATORIA', width: '9%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'FECHA INICIO', datafield: 'FECHA_CRE', width: '10%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'FECHA LIMITE', datafield: 'FECHA_VEN', width: '10%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'NOMBRE', datafield: 'NOMBRE_OPE', width: '22%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'DESCRIPCION', datafield: 'DESCRIPCION_OPE', width: '20%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'COORDINADOR', datafield: 'NOMBRE_COOR', width: '15%', columntype: 'textbox', filtercondition: 'starts_with', filterable: false},
            {text: 'JEFE', datafield: 'NOMBRE_JEFE', width: '15%', columntype: 'textbox', filtercondition: 'starts_with', filterable: true}
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
    var urlh = "backend/carpeta/agencia/init/12/3/" + mydata.ID;
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
    var urlh = "backend/carpeta/agencia/init/12/9/" + mydata.ID_OPERATORIA;
    $(location).attr('href', urlh);
}

function editar_estado_cu(name_grid) {
    name_grid || (name_grid = 'jqxgrid_listado');
    mydata = '';
    var selectedrowindex = $("#" + name_grid).jqxGrid('getselectedrowindex');
    var selectedrowindexes = $("#" + name_grid).jqxGrid('getselectedrowindexes');
    mydata = $('#' + name_grid).jqxGrid('getrowdata', selectedrowindex);
    console.log("LALALALALA");
    console.log(mydata);
//    if (mydata == null) {
//        jAlert('Seleccione una factura.', $.ucwords(_etiqueta_modulo), function () {
//            $.unblockUI();
//        });
//        return false;
//    }
//    if (selectedrowindexes.length > 1) {
//        jAlert('Elija solo una Factura para editar.', $.ucwords(_etiqueta_modulo), function () {
//            $.unblockUI();
//        });
//        return false;
//    }
    var urlh = "backend/carpeta/agencia/init/12/10/" + mydata.ID;
    $(location).attr('href', urlh);
//    var urlh = "backend/carpeta/agencia/init/" + _provincia + "/10";
//    $(location).attr('href', urlh);
}

function limpiar_form_nf() {
    $(".nuevafact_form textarea").val("").removeAttr("readonly");
    $(".nuevafact_form input").not("#add_cius").val("");
    $(".nuevafact_form input").not("#cuitform,#nombre2,#add_cius,#total,#dto_bodega,#neto,#iva,#precio").removeAttr("readonly");
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
        var id_referencia_devolver = "";
        var id_ref_devolver = 0;
        $.each(rowindexes, function (index, value) {
            var reg = $('#jqxgrid_listado').jqxGrid('getrowdata', value);
            if (reg.ESTADO == 'Pago Solicitado') {
                swa = 1;
            } else if (reg.ESTADO == 'Pagada o Pago Rechazado') {
                swa = 3;
            } else if (reg.ESTADO == 'Anulada') {
                swa = 10;
            }
            if (reg.CHECK_ESTADO == 'S/Confirmar') {
                swa = 2;
            }
            if (reg.NUMCUOTA == "") {
                id_ref_devolver = reg.ID;
                swa = 5;
            }
            $.ajax({
                url: _agencia.URL + "/x_verificar_enviadas",
                data: {
                    provincia: _agencia._provincia,
                    obj: reg
                },
                dataType: "json",
                type: "post",
                async: false,
                success: function (data) {
                    if (data.length > 0) {
                        id_referencia_devolver = data[0].IDFACTURAINT;
                        swa = 6;
                    }
                }
            });

            _arr_sel.push(reg);
        });
        if (swa == '1') {
            jAlert('La seleccion contiene comprobantes ya procesados.', $.ucwords(_etiqueta_modulo), function () {
            });
            return false;
        } else if (swa == '3') {
            jAlert('Comprbantes seleccionados con pagos registrados.', $.ucwords(_etiqueta_modulo), function () {
            });
            return false;
        } else if (swa == '5') {
            jAlert('En la factura ID ' + id_ref_devolver + ' no hay cuotas para enviar.', $.ucwords(_etiqueta_modulo), function () {
            });
            return false;
        } else if (swa == '6') {
            jAlert('La cuota de la factura con el ID ' + id_referencia_devolver + ' ya se encuentra en el destino.', $.ucwords(_etiqueta_modulo), function () {
            });
            return false;
        } else if (swa == '10') {
            jAlert('La factura se encuentra anulada.', $.ucwords(_etiqueta_modulo), function () {
            });
            return false;
        }
        jConfirm('Esta seguro de generar este lote de pago??.', $.ucwords(_etiqueta_modulo), function (r) {
            if (r == true) {
                $.blockUI({message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>'});
                $.ajax({
                    url: _agencia.URL + "/x_guardarlote",
                    data: {
                        provincia: _agencia._provincia,
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
                        url: _agencia.URL + "/x_borrar_file",
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

function _imp_procesar() {
    jConfirm('Esta seguro de procesar estos archivos??.', $.ucwords(_etiqueta_modulo), function (r) {
        if (r == true) {
// llamar ajax
            $.blockUI({message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>'});
            $.ajax({
                url: _agencia.URL + "/x_importar_xls",
                data: {fid_sanjuan: _fid_sanjuan, ope_sanjuan: _ope_sanjuan},
                dataType: "json",
                type: "post",
                success: function (dat) {
                    console.dir(dat);
                    if (dat == -2) {
                        jAlert('No existen archivos para la importación.', $.ucwords(_etiqueta_modulo), function () {});
                    } else if (dat == -1) {
                        $.ajax({url: _agencia.URL + "/x_actualizarLista", data: {},
                            //dataType: "json",
                            type: "post",
                            success: function (data) {
                                console.dir(data);
                                $('.lista_arch').html(data);
                                evento_lista_arch();
                            }
                        });
                    } else if (dat == 1) {
                        jAlert('Los datos fueron importados con exito.', $.ucwords(_etiqueta_modulo), function () {
                            //actualizar el listado
                            /*$.ajax({
                             url: _agencia.URL + "/x_actualizarLista",
                             data: {
                             },
                             //dataType: "json",
                             type: "post",
                             success: function (data) {
                             console.dir(data);
                             $('.lista_arch').html(data);
                             evento_lista_arch();
                             }
                             });*/
                            window.location.href = "backend/carpeta/agencia/init/12/2";
                        });
                    }

                }
            });
        }
    });
}

function cambiarPrecio() {
    var precioAsignado = $('#fpago-select').find(':selected').attr('data-precio');
//    $('#precio').val(precioAsignado);
    if ($('#precio').val() == 0) {
        $("#neto").val(0);
    } else {
        var neto = $('#precio').val();
        $("#neto").val(dec(neto, 2));
        var iva = $('#porcentaje_iva').val() * $("#neto").val() / 100;
        $("#iva").val(dec(iva, 2));
        var total = 1 * $("#neto").val() + 1 * $("#iva").val();
//        if($('#retencion').val()!=0){total = total - ($('#retencion').val() * total / 100);}
        $("#total").val(dec(total, 2));
    }
}