
function init_grid() {
    var sourceope = {
        datatype: "json",
        datafields: [
            {name: 'IDC', type: 'boolean'},
            {name: 'ID', type: 'integer'},
            {name: 'POSTULANTES_NOMBRES', type: 'string'},
            {name: 'CUIT', type: 'string'},
            {name: 'OPERATORIA', type: 'string'},
            {name: 'CAPITAL_CUOTA', type: 'float'},
            {name: 'INT_COMPENSATORIO', type: 'float'},
            {name: 'INT_COMPENSATORIO_IVA', type: 'float'}
        ],
        url: _cobranzas.URL + '/init_json/',
        data: {
            fecha: ''
        },
        async: false,
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };

    var dataAdapterope = new $.jqx.dataAdapter(sourceope, {
        loadComplete: function (data) {
            console.log(data.length);
            for (var i = 0; i < data.length; i++) {
                console.log(data[i].IDC);
            }
        },
        formatData: function (data) {

            data.name_startsWith = $("#searchField").val();
            return data;
        }
    });

    var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {

        if (value == 'PRORROGADO') {
            return '<div style="margin:4px;font-weight:bold;">' + value + '</div>';
        } else if (value == 'CADUCADO') {
            return '<div style="margin:4px;color:#ff0000;font-weight:bold;">' + value + '</div>';
        } else {
            return '<div style=margin:4px;>' + value + '</div>';
        }
    }

    $("#jqxgrid").jqxGrid({
        width: '98%',
        groupable: true,
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function (data) {
        },
        selectionmode: "multiplerows",
        columnsresize: true,
        showtoolbar: true,
        localization: getLocalization(),
        sortable: true,
        editable: true,
        filterable: true,
        showfilterrow: true,
        rendertoolbar: function (toolbar) {
            var me = this;
            var container = $("<div style='margin: 5px;'></div>");
            var span = $("<span style='float: left; margin-top: 5px; margin-right: 4px;'>Buscar: </span>");
            var input = $("<input class='jqx-input jqx-widget-content jqx-rc-all' id='searchField' type='text' style='height: 23px; float: left; width: 223px;' />");

            if (theme != "") {
                input.addClass('jqx-widget-content-' + theme);
                input.addClass('jqx-rc-all-' + theme);
            }

            input.on('keydown', function (event) {
                if (input.val().length >= 2) {
                    if (me.timer)
                        clearTimeout(me.timer);
                    me.timer = setTimeout(function () {
                        dataAdapterope.dataBind();
                    }, 300);
                }
            });
        },
        columns: [
            {text: '', datafield: 'IDC', width: '55px', groupable: false, filterable: false, columntype: 'checkbox', editable: true},
            {text: 'ID', datafield: 'ID', width: '55px', groupable: false, filterable: true, editable: true},
            {text: 'TOMADORES', datafield: 'POSTULANTES_NOMBRES', hidden: false, filterable: true},
            {text: 'CUIT', datafield: 'CUIT', width: '100px', hidden: false, filterable: true},
            {text: 'OPERATORIA', datafield: 'OPERATORIA', width: '170px', hidden: false, filterable: true},
            {text: 'CAPITAL CUOTA', datafield: 'CAPITAL_CUOTA', width: '170px', hidden: false, filterable: true},
            {text: 'INT. COMP.', datafield: 'INT_COMPENSATORIO', width: '70px', hidden: false, filterable: false},
            {text: 'I.C. IVA', datafield: 'INT_COMPENSATORIO_IVA', width: '70px', hidden: false, filterable: false}
        ]
    });

    //$("#jqxgrid").setTooltipsOnColumnHeader();
}

$(document).ready(function () {
    init_grid();

    $("#btnSelAll").click(function () {
        var rowscount = $("#jqxgrid").jqxGrid('getdatainformation').rowscount;
        $("#jqxgrid").jqxGrid('beginupdate');
        for (var i = 0; i < rowscount; i++) {
            $("#jqxgrid").jqxGrid('setcellvalue', i, 'IDC', true, false);
        }
        $("#jqxgrid").jqxGrid('endupdate');
    });
});