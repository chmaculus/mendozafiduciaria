//ESTA CLASE NOSE Q HACE; NO SE MODIFICA
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

// local
var _fid_sanjuan = 41;
var _ope_sanjuan = 32;

var _fid_mendoza = 33;
var _ope_mendoza = 42;


var nolocal = 1;
if (nolocal == 1) {
    var _fid_sanjuan = 9;
    var _ope_sanjuan = 17;

    var _fid_mendoza = 10;
    var _ope_mendoza = 18;
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
    var kgrs = $("#kgrs").val();
    var cuitform = $("#cuitform").val();
    var azucar = $("#azucar").val();
    var precio = $("#precio").val();
    var neto = $("#neto").val();
    var iva = $("#iva").val();
    var total = $("#total").val();
    var observacion_fact = $("#observacion_fact").val();
    var formula = $("#formula").val();



    //bancos
    var griddata = $('#jqxgridcius').jqxGrid('getdatainformation');
    var _arr_cius = [];
    for (var i = 0; i < griddata.rowscount; i++)
        _arr_cius.push($('#jqxgridcius').jqxGrid('getrenderedrowdata', i));

    var sum_kgrs = 0;
    var sum_azuc = 0;

    //validacion
    if (_arr_cius) {
        $.each(_arr_cius, function (index, value) {
            //sum_kgrs
            sum_kgrs += parseFloat(value.KGRS);
            sum_azuc += parseFloat(value.AZUCAR * value.KGRS);
        });
        sum_azuc = sum_azuc / sum_kgrs;
    }

    iid = id ? id : 0;


    if (_opcion == 3) {
        objsave = {
            id: iid,
            update_cius: 1,
            arr_cius: _arr_cius,
            CUIT: cuitform,
        }
    } else {
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
         if (cai==''){
         jAlert('Ingrese CAI.', $.ucwords(_etiqueta_modulo), function(){
         $("#cai").focus();
         });
         return false;
         }
         if (fechavto==''){
         jAlert('Ingrese fecha Vencimiento.', $.ucwords(_etiqueta_modulo), function(){
         $("#fechavto").focus();
         });
         return false;
         }
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

        if (kgrs == '') {
            jAlert('Ingrese el valor de los Kgrs.', $.ucwords(_etiqueta_modulo), function () {
                $("#kgrs").focus();
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

        console.log('kgrs:: ' + kgrs + ' sum kgrs::' + sum_kgrs);
        if (kgrs != sum_kgrs) {
            jAlert('Las sumas kgrs no coinciden.', $.ucwords(_etiqueta_modulo), function () {
            });
            return false;
        }

        console.log('azucar:: ' + azucar + ' sum azucar::' + sum_azuc);
        if (sum_azuc < azucar) {
            jAlert('Las sumas azucar no coinciden.', $.ucwords(_etiqueta_modulo), function () {

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

        objsave = {
            id: iid,
            NUMERO: numero,
            FECHA: fecha,
            CAI: cai,
            ID_PROVINCIA: _provincia,
            FECHAVTO: fechavto,
            ID_BODEGA: bodega,
            CUIT: cuitform,
            KGRS: kgrs,
            AZUCAR: azucar,
            PRECIO: precio,
            ID_ESTADO: 1,
            USU_CARGA: _USUARIO_SESION_ACTUAL,
            NETO: neto,
            IVA: iva,
            TOTAL: total,
            OBSERVACIONES: observacion_fact,
            arr_cius: _arr_cius,
            update_cius: 0,
            ID_OPERATORIA: tmp_ope,
            ID_FIDEICOMISO: tmp_fid,
            FORMULA: formula
        }

        console.log('aaaaaaaa::::');
        console.dir(objsave);

    }





    //validar numero de factura
    //numero
    $.ajax({
        url: _comprauva.URL + "/x_verificarnumfactura",
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
                    url: _comprauva.URL + "/x_sendobj",
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
    //alert('sw::' + sw );

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
        $(".tb_search").hide();
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


    //alert(_permiso_exportar+'-'+_permiso_ver+'-'+_permiso_modificacion+'-'+_permiso_baja+'-'+_permiso_alta);


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
            $("#dto_bodega").val(local);
        }
    });


    $('.consultar').on('click', function (e) {
        e.preventDefault();

        $('.env_form').show();
        $('.nuevafact_form').hide();
        $("#provincia").chosen();
        $("#condicioniva").chosen({width: "220px"});
        $("#condicioniibb").chosen({width: "220px"});
        $("#bodega").chosen({width: "220px"});
        $("#formula").chosen({width: "220px"});

        var cuit = $("#cuit_busqueda").val();

        /* buscar por cuit */
        $.ajax({
            url: _comprauva.URL + "/x_getobjcliente",
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

        /*
         $.ajax({
         url: _comprauva.URL + "/x_getobjcliente",
         data: {
         cuit: cc
         },
         dataType: "json",
         type: "post",
         success: function(data) {
         
         }
         });
         */

        var cc = $("#cuit_busqueda").val();
        limpiar_form_nf();
        $('.env_form').hide();
        $('.nuevafact_form').show();
        $("#nombre2").val($("#nombre").val());
        $("#cuitform").val(cc);
        $("#porcentaje_iva").val('10.5');
        show_btns(2);

    });

    refresGridevent();

    $('#send').on('click', function (e) {
        e.preventDefault();

        var id = $("#idh").val();

        var prov = $("#provinciah").val();
        var loca = $("#localidadh").val();
        var condicioniibb = $("#condicioniibb").val();
        var condicioniva = $("#condicioniva").val();
        var nombre = $("#nombre").val();
        var dir = $("#direccion").val();
        var tel = $("#telefono").val();
        var cuit = $("#cuit").val();
        var insciibb = $("#insciibb").val();
        var insinv = $("#insinv").val();
        var correo = $("#correo").val();
        var obs = $("#observacion").val();
        var cbu = $("#cbu").val();

        iid = id ? id : 0;
        objsave = {
            id: iid,
            ID_PROVINCIA: prov,
            ID_DEPARTAMENTO: loca,
            ID_CONDICION_IIBB: condicioniibb,
            ID_CONDICION_IVA: condicioniva,
            ID_INV: insinv,
            DIRECCION: dir,
            RAZON_SOCIAL: nombre,
            TELEFONO: tel,
            CUIT: cuit,
            CORREO: correo,
            OBSERVACION: obs,
            INSCRIPCION_IIBB: insciibb,
            CBU: cbu
        }

        //validar campos
        if (nombre == '') {
            jAlert('Ingrese Razón Social.', $.ucwords(_etiqueta_modulo), function () {
                $("#nombre").focus();
            });
            return false;
        }

        if (cuit == '') {
            jAlert('Ingrese CUIT.', $.ucwords(_etiqueta_modulo), function () {
                $("#cuit").focus();
            });
            return false;
        }

        if (cbu == '') {
            jAlert('Ingrese CBU.', $.ucwords(_etiqueta_modulo), function () {
                $("#cbu").focus();
            });
            return false;
        }

        if (condicioniva == '') {
            jAlert('Elija condicion iva.', $.ucwords(_etiqueta_modulo), function () {
                $("#condicioniva").focus();
            });
            return false;
        }

        if (condicioniibb == '') {
            jAlert('Elija condicion iibb.', $.ucwords(_etiqueta_modulo), function () {
                $("#condicioniibb").focus();
            });
            return false;
        }

        if (insciibb == '') {
            jAlert('Elija Inscripcion IIBB.', $.ucwords(_etiqueta_modulo), function () {
                $("#insciibb").focus();
            });
            return false;
        }

        if (dir == '') {
            jAlert('Ingrese domicilio.', $.ucwords(_etiqueta_modulo), function () {
                $("#direccion").focus();
            });
            return false;
        }

        if (prov == '') {
            jAlert('Elija provincia.', $.ucwords(_etiqueta_modulo), function () {
                $("#provincia").focus();
            });
            return false;
        }

        if (loca == '') {
            jAlert('Elija Localidad.', $.ucwords(_etiqueta_modulo), function () {
                $("#subrubro").focus();
            });
            return false;
        }

        if (tel == '') {
            jAlert('Ingrese un numero de teléfono.', $.ucwords(_etiqueta_modulo), function () {
                $("#telefono").focus();
            });
            return false;
        }

        if (correo == '') {
            jAlert('Ingrese email.', $.ucwords(_etiqueta_modulo), function () {
                $("#correo").focus();
            });
            return false;
        }





        $.ajax({
            url: _comprauva.URL + "/x_sendobjcli",
            data: {
                obj: objsave
            },
            dataType: "json",
            type: "post",
            success: function (data) {
                alert("Mensaje");
                alert(data.valor);
                condicioniva_g = data.valor;
                console.dir(data);
                if (data.result > 0) {
                    $('#nuevafactura').off().on('click', function (e) {
                        e.preventDefault();
                        limpiar_form_nf();
                        $('.env_form').hide();
                        $('.nuevafact_form').show();
                        $("#cuitform").val(cuit);
                        $("#nombre2").val($("#nombre").val());
                        show_btns(2);
                    });

                    jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo), function () {
                        show_btns(1);
                        limpiar_form_fact();
                        $('#send').hide();
                    });

                } else {
                    jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo), function () {
                        $.unblockUI();
                    });
                }
            }
        });
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
            show_btns();
        } else if (top == 'lis_guardar_enviar') {
            $('#send').trigger('click');
        } else if (top == 'lis_guardar_fact') {
            guardar_factura();
        } else if (top == 'lis_mendoza') {
            var urlh = "backend/carpeta/comprauva/init/12";
            $(location).attr('href', urlh);
        } else if (top == 'lis_sanjuan') {
            var urlh = "backend/carpeta/comprauva/init/17";
            $(location).attr('href', urlh);
        } else if (top == 'lis_addnf') {

            if (_permiso_alta == 0) {

                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }

            var urlh = "backend/carpeta/comprauva/init/" + _provincia + "/1";
            $(location).attr('href', urlh);
        } else if (top == 'lis_lis') {
            if (_permiso_ver == 0) {

                jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                    switchBarra();
                });
                return false;
            }
            var urlh = "backend/carpeta/comprauva/init/" + _provincia + "/2";
            $(location).attr('href', urlh);
        } else if (top == 'inicio') {
            var urlh = "backend/carpeta/comprauva";
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

            var urlh = "backend/carpeta/comprauva/init/3";
            $(location).attr('href', urlh);
        } else if (top == 'impor_procesar') {
            importar_procesar();
        } else if (top == 'impor_revision') {
            var urlh = "backend/carpeta/comprauva/init/4";
            $(location).attr('href', urlh);
        } else if (top == 'testing') {

            $.ajax({
                url: _comprauva.URL + "/x_testing",
                data: {
                },
                dataType: "json",
                type: "post",
                success: function (data) {

                    alert(data);

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
    $("#ciu_azucar").numeric({negative: false});
    $("#ciu_kgrs").numeric({negative: false});


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
            var neto = $("#kgrs").val() * $(this).val();
            $("#neto").val(dec(neto, 2));

            factor = $('#porcentaje_iva').val();
            var iva = factor * $("#neto").val() / 100;
            $("#iva").val(dec(iva, 2));


            var total = 1 * $("#neto").val() + 1 * $("#iva").val();
            $("#total").val(dec(total, 2));

        }
    });

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
            url: _comprauva.URL + "/x_verificarcbu",
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



    evento_lista_arch();

});


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
                    url: _comprauva.URL + "/x_borrar_file",
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
        url: _comprauva.URL + "/x_getobj",
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


            //$("#bodega").attr('disabled', true).trigger("chosen:updated");

            $("#kgrs").val(data.KGRS);
            $("#azucar").val(data.AZUCAR);
            $("#precio").val(data.PRECIO);
            $("#observacion_fact").val(data.OBSERVACIONES);

            //$("#kgrs").val( data.KGRS ).attr("readonly","readonly");
            //$("#azucar").val( data.AZUCAR ).attr("readonly","readonly");
            //$("#precio").val( data.PRECIO ).attr("readonly","readonly");
            $("#neto").val(data.NETO).attr("readonly", "readonly");
            $("#iva").val(data.IVA).attr("readonly", "readonly");
            $("#total").val(data.TOTAL).attr("readonly", "readonly");
            //$("#observacion_fact").val( data.OBSERVACIONES ).attr("readonly","readonly");

            //$(".cabezera_frm_ciu").hide();

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
        url: _comprauva.URL + '/x_get_info_bancos',
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
//            alert("ciu_num 1");
            jAlert('El formato del Número de Ciu no es correcto (Ejem: A9854124).', $.ucwords(_etiqueta_modulo), function () {
                $("#frm_cargacius input").first().select();
            });
            return false;
        }
        /*Esto valida lo contrario a la anterior, si uno no le pone guion (-) es ejecutada, la comento para que se ingresen
         * sin guion el dato */
//        if (!isnumeroCiuIns(ciu_insc)) {
//            jAlert('El formato del Número de Inscripcion no es correcto(Ejem: A-9854124).', $.ucwords(_etiqueta_modulo), function() {
//                $("#frm_cargacius #ciu_insc").first().next().next().select();
//            });
//            return false;
//        }



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
            url: _comprauva.URL + "/x_verificarciu",
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
                url: _comprauva.URL + "/x_delupload_nota",
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
            url: _comprauva.URL + "/x_sendnota",
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
        url: _comprauva.URL + "/x_getform_agregar_requerimiento",
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
        url: _comprauva.URL + "/x_getvincular",
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
                            url: _comprauva.URL + "/x_vincular_nr",
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
        url: _comprauva.URL + "/x_getenviar_a1",
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
                    url: _comprauva.URL + "/x_getenviar_a2",
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
                                        url: _comprauva.URL + "/x_guardar_traza_nota",
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
            url: _comprauva.URL + "/x_getlocalidad",
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
            estado: '12' // en revision
        },
        async: false,
        deleterow: function (rowid, commit) {
            process_data(_comprauva.URL + "/x_delete_facturas_cu", mydata);
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

    $("#jqxgridgetFacturasCuva").jqxGrid({
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

    var urlh = "backend/carpeta/comprauva/init/12/3/" + mydata.ID;
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
                    url: _comprauva.URL + "/x_guardarlote",
                    data: {
                        provincia: _comprauva._provincia,
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
                        url: _comprauva.URL + "/x_borrar_file",
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
                url: _comprauva.URL + "/x_importar_xls",
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
                                url: _comprauva.URL + "/x_actualizarLista",
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
                                url: _comprauva.URL + "/x_actualizarLista",
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

