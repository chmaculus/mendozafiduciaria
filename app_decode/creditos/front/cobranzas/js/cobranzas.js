var _creditos_lista = [];

function init_grid() {
    var sourceope = {
        datatype: "json",
        datafields: [
            {name: 'ID', type: 'integer'},
            {name: 'POSTULANTES_NOMBRES', type: 'string'},
            {name: 'CUIT', type: 'string'},
            {name: 'OPERATORIA', type: 'string'},
            {name: 'CAPITAL_CUOTA', type: 'float'},
            {name: 'INT_COMPENSATORIO', type: 'float'},
            {name: 'INT_COMPENSATORIO_IVA', type: 'float'}
        ],
        id: 'ID',
        url: _cobranzas.URL + '/init_json/',
        data: {
            fecha: _fecha_proceso
        },
        type: "post",
        async: false,
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };

    var dataAdapter = new $.jqx.dataAdapter(sourceope, {
        loadComplete: function (data) {
            _creditos_lista = data;
        },
        formatData: function (data) {
        }
    });

    var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {

        if (rowdata.IDC) {
            $(this).attr('disabled', true);
            return '<div style="margin-top:6px;font-weight:bold" class="jqx-grid-cell-left-align">' + value + '</div>';
        } else {
            return '<div style="margin-top:6px;" class="jqx-grid-cell-left-align">' + value + '</div>';
        }
    }

    $("#jqxgrid").jqxGrid({
        width: '100%',
        source: dataAdapter,
        theme: 'energyblue',
        ready: function () {
        },
        selectionmode: "checkbox",
        enablebrowserselection: true,
        columnsresize: true,
        localization: getLocalization(),
        sortable: true,
        filterable: true,
        showfilterrow: true,
        columns: [
            {text: 'ID', datafield: 'ID', width: '55px', groupable: false, filterable: true, editable: true},
            {text: 'TOMADORES', datafield: 'POSTULANTES_NOMBRES', hidden: false, filterable: true, cellsrenderer: cellsrenderer},
            {text: 'CUIT', datafield: 'CUIT', width: '100px', hidden: false, filterable: true},
            {text: 'OPERATORIA', datafield: 'OPERATORIA', width: '170px', hidden: false, filterable: true},
            {text: 'CAPITAL CUOTA', datafield: 'CAPITAL_CUOTA', width: '170px', hidden: false, filterable: true},
            {text: 'INT. COMP.', datafield: 'INT_COMPENSATORIO', width: '70px', hidden: false, filterable: false},
            {text: 'I.C. IVA', datafield: 'INT_COMPENSATORIO_IVA', width: '70px', hidden: false, filterable: false}
        ]
    });

    //$("#jqxgrid").setTooltipsOnColumnHeader();
}

function init_grid2() {
    var sourceope = {
        datatype: "json",
        datafields: [
            {name: 'ID', type: 'integer'},
            {name: 'POSTULANTES_NOMBRES', type: 'string'},
            {name: 'CUIT', type: 'string'},
            {name: 'OPERATORIA', type: 'string'},
            {name: 'CAPITAL_CUOTA', type: 'float'},
            {name: 'INT_COMPENSATORIO', type: 'float'},
            {name: 'INT_COMPENSATORIO_IVA', type: 'float'}
        ],
        id: 'ID',
        url: _cobranzas.URL + '/facturados_json/',
        data: {
            fecha: _fecha_proceso
        },
        async: false,
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };

    var dataAdapter = new $.jqx.dataAdapter(sourceope, {
        loadComplete: function (data) {
        },
        formatData: function (data) {
        }
    });

    var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {

        if (rowdata.IDC) {
            $(this).attr('disabled', true);
            return '<div style="margin-top:6px;font-weight:bold" class="jqx-grid-cell-left-align">' + value + '</div>';
        } else {
            return '<div style="margin-top:6px;" class="jqx-grid-cell-left-align">' + value + '</div>';
        }
    }

    $("#jqxgrid2").jqxGrid({
        width: '98%',
        source: dataAdapter,
        theme: 'energyblue',
        ready: function () {
        },
        selectionmode: "none",
        enablebrowserselection: true,
        columnsresize: true,
        localization: getLocalization(),
        sortable: true,
        filterable: true,
        showfilterrow: true,
        columns: [
            {text: 'ID', datafield: 'ID', width: '55px', groupable: false, filterable: true, editable: true},
            {text: 'TOMADORES', datafield: 'POSTULANTES_NOMBRES', hidden: false, filterable: true, cellsrenderer: cellsrenderer},
            {text: 'CUIT', datafield: 'CUIT', width: '100px', hidden: false, filterable: true},
            {text: 'OPERATORIA', datafield: 'OPERATORIA', width: '170px', hidden: false, filterable: true},
            {text: 'CAPITAL CUOTA', datafield: 'CAPITAL_CUOTA', width: '170px', hidden: false, filterable: true},
            {text: 'INT. COMP.', datafield: 'INT_COMPENSATORIO', width: '70px', hidden: false, filterable: false},
            {text: 'I.C. IVA', datafield: 'INT_COMPENSATORIO_IVA', width: '70px', hidden: false, filterable: false}
        ]
    });
}

function enviar_facturar() {
    var selectedrowindexes = $('#jqxgrid').jqxGrid('getselectedrowindexes');

    if (selectedrowindexes.length == 0) {
        jAlert('No ha seleccionado ninguna cuota', $.ucwords(_etiqueta_modulo));
        return;
    }

    var credito_selected = [];
    for (var i = 0; i < selectedrowindexes.length; i++) {
        credito_selected.push(parseInt(_creditos_lista[selectedrowindexes[i]].ID));
    }

    $.ajax({
        url: _cobranzas.URL + "/enviar_facturar/",
        data: {
            creditos: credito_selected,
            fecha: _fecha_proceso
        },
        type: "post",
        success: function (rtn) {

        }
    });

}

function buscar_creditos() {
    _fecha_proceso = $('.fecha').val();
    init_grid();
    window.setTimeout('init_grid2();', 500);
}


$(document).ready(function () {
    $('#jqxTabs').jqxTabs();
    init_grid();
    window.setTimeout('init_grid2();', 500);

    $(".fecha").val(_fecha_proceso);
    $(".fecha").datepicker({
        changeMonth: true,
        changeYear: true,
        changeDay: false,
        dateFormat: 'mm-yy',
    });

    $('#btnFacturar').on('click', function () {
        enviar_facturar();
    });

    $('#btnBuscar').on('click', function () {
        buscar_creditos();
    });

});