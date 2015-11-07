var mydata;
var working = false;

var working_or = false;

$(document).ready(function () {
    mydata = '';

    $(".toolbar li").hover(
            function () {
                $(this).removeClass('li_sel').addClass('li_sel');
            },
            function () {
                $(this).removeClass('li_sel');
            }
    );

    $(".toolbar li").click(function (e) {
        e.preventDefault();
        var top = $(this).data('top');

        switch (top) {
            case 'add':
                form_presupuesto(0);
                break;
            case 'edi':
                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                $("#wpopup").hide();
                if (mydata && mydata.ID_PRES !== 'undefined') {
                    form_presupuesto(mydata.ID_PRES);
                } else {
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo), function () {});
                }
                break;
            case 'lis':
                $("#wpopup").hide();
                init_presupuesto();
                break;
            case 'exp':
                exp_presupuesto();
                break;
            case 'del':
                del_presupuesto();
                break;

        }
    });

    init_presupuesto();
});

function init_presupuesto() {

    var source = {
        datatype: "json",
        datafields: [{name: 'ID_PRES', type: 'string'}, {name: 'NETO', type: 'money'}, {name: 'IVA', type: 'string'}, {name: 'TOTAL', type: 'money'}],
        url: _presupuestos.URL + '/x_get_info_grid',
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };
    $("#jqxgrid").jqxGrid({
        width: '98%',
        groupable: true,
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
            {text: '# PRESUPUESTO', datafield: 'ID_PRES', width: '10%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
            {text: 'NETO', datafield: 'NETO', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
            {text: 'IVA', datafield: 'IVA', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true},
            {text: 'TOTAL', datafield: 'TOTAL', width: '30%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable: true}
        ]
    });
    $("#jqxgrid").show();
}

function add_presupuesto() {
    $.blockUI({message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>'});

    if (_permiso_alta == 0) {

        jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
            $.unblockUI();
            switchBarra();
        });
        return false;
    }

    $.ajax({
        url: _presupuestos.URL + "/x_getform",
        type: "post",
        success: function (data) {
            $.unblockUI();
            $("#jqxgrid").hide();
            $("#wpopup").html(data);
        }
    });
}

function form_presupuesto(id_pres) {
    $.blockUI({message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>'});

    var ver = $(this).data('ver');
    ver || (ver = '-1');

    if (ver != -1) {
        if (_permiso_ver == 0 && ver) {
            jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                $.unblockUI();
                switchBarra();
            });
            return false;
        }
    } else {
        if (_permiso_modificacion == 0) {
            jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo), function () {
                $.unblockUI();
                switchBarra();
            });
            return false;
        }
    }

    $.ajax({
        url: _presupuestos.URL + "/x_getform",
        type: "post",
        data: {
            id: id_pres
        },
        success: function (data) {
            $.unblockUI();
            $("#jqxgrid").hide();
            $("#wpopup").html(data).show();
        }
    });
}

function del_presupuesto() {
    if (_permiso_baja == 0) {
        jAlert('Usted no tiene Permisos para ejecutar esta acción', $.ucwords(_etiqueta_modulo), function () {
            $.unblockUI();
            switchBarra();
        });
        return false;
    }

    jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo), function (r) {
        if (r == true) {
            var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
            mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
            var rowscount = $("#jqxgrid").jqxGrid('getdatainformation').rowscount;

            if (mydata == null) {
                jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo), function () {
                    $.unblockUI();
                });
                return false;
            }

            $.ajax({
                url: _presupuestos.URL + "/x_delobj",
                data: {
                    id: mydata.ID_PRES
                },
                dataType: "json",
                type: "post",
                success: function (data) {
                    if (data > 0) {
                        if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                            var id = $("#jqxgrid").jqxGrid('getrowid', selectedrowindex);
                            var commit = $("#jqxgrid").jqxGrid('deleterow', id);
                        }
                    }
                    else {
                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo), function () {
                            $.unblockUI();
                        });
                    }
                }
            });
        }
        $.unblockUI();
    });
}

function exp_presupuesto() {
    $.ajax({
        url: _presupuestos.URL + "/x_getexportar",
        type: "post",
        success: function (data) {
            $.unblockUI();
            $.fancybox(
                    data,
                    {
                        'padding': 20,
                        'autoScale': true,
                        'scrolling': 'no'
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
            $('.div_exportar .toolbar li').on('click', function (event) {
                event.preventDefault();
                var tipo = $(this).data('acc');
                switch (tipo) {
                    case 'exc':
                        $("#jqxgrid").jqxGrid('exportdata', 'xls', 'ope_' + fGetNumUnico(), true, null, null, url_e);
                        break;
                    case 'csv':
                        $("#jqxgrid").jqxGrid('exportdata', 'csv', 'ope_' + fGetNumUnico(), true, null, null, url_e);
                        break;
                    case 'htm':
                        $("#jqxgrid").jqxGrid('exportdata', 'html', 'ope_' + fGetNumUnico(), true, null, null, url_e);
                        break;
                    case 'xml':
                        $("#jqxgrid").jqxGrid('exportdata', 'xml', 'ope_' + fGetNumUnico(), true, null, null, url_e);
                }
            });

        }
    });
}