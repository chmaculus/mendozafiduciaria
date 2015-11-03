var nroit = 0;
var items = [];

$(document).ready(function () {
    $(".chzn-select").chosen({width: "255px"});
    $("#cambio").numeric({negative: false});
    $("#netod").numeric({negative: false});
    $("#ivai").numeric({negative: false});

    click_quitar();

    $('#send').click(function () {
        guardar_presupuesto();
    });
    actualizar_total();
});

function add_p() {
    /*if ( !$("#customForm").validationEngine('validate') ) {
     return false;
     }*/
    $("#items table").append('<tr class="it' + nroit + '"><td class="nom">' + $("#nombre").val() + '</td><td class="mon">' + $("#moneda").val() + '</td><td class="cam">' + $("#cambio").val() + '</td><td class="iva">' + $("#ivai").val() + '</td><td class="net">' + $("#netod").val() + '</td><td class="ivat">' + $("#netod").val() * $("#ivai").val() / 100 + '</td><td class="tt">' + ($("#netod").val() * $("#cambio").val() * (parseFloat($("#ivai").val()) + 100) / 100) + '</td><td class="dd">Quitar</td></tr>')
    $("#nombre").val('');
    $("#moneda").val('');
    $("#cambio").val('');
    $("#ivai").val('');
    $("#netod").val('');
    ++nroit;
    actualizar_total();
    click_quitar();
}

function actualizar_total() {
    var total = 0;
    var totaln = 0;
    var totali = 0;
    var fila;
    items = [];
    var it;
    $("#items").hide();
    if ($("#items .subtot").length) {
        $("#items .subtot").remove();
    }
    $("#items table tr").not('.tit').not('.subtot').each(function () {
        $("#items").show();
        it = [$(this).children('.nom').text(), $(this).children('.mon').text(), $(this).children('.cam').text(), $(this).children('.net').text(), $(this).children('.iva').text()];
        items.push(it);
        total += parseFloat($(this).children('.tt').text());
        totaln += parseFloat($(this).children('.net').text());
        totali += parseFloat($(this).children('.ivat').text());
    });

    if (total) {
        $("#items table").append('<tr class="subtot"><td></td><td></td><td></td><td></td><td class="net">' + totaln + '</td><td class="ivat">' + totali + '</td><td class="tt">' + total + '</td><td></td></tr>')
    }
}


function click_quitar() {
    $('.dd').click(function () {
        var eliminar = $(this).parent();
        jConfirm('Esta seguro de quitar el item?.', $.ucwords(_etiqueta_modulo), function (r) {
            if (r) {
                eliminar.remove();
                actualizar_total();
            }
        });


    });
}

function guardar_presupuesto() {
    if (items.length) {
        $.ajax({
            url: _presupuestos.URL + "/x_save",
            data: {
                id: $("#presupuesto_id").val(),
                items: items
            },
            dataType: "html",
            type: "post",
            success: function (rt) {
                if (rt == "1") {
                    if($("#presupuesto_id").val()) {
                        var titulo = "El presupuesto ha sido modificado correctamente";
                    } else {
                        var titulo = "El presupuesto ha sido guardado correctamente";
                    }
                    
                    jAlert(titulo, $.ucwords(_etiqueta_modulo), function () {
                        $("#wpopup").hide();
                        init_presupuesto();
                    });
                    
                } else {
                    jAlert('Hubo un inconveniente, vuelva a intentar', $.ucwords(_etiqueta_modulo), function () {});
                }
            }
        });
    } else {
        jAlert('No hay items cargados', $.ucwords(_etiqueta_modulo), function () {});
    }
}

function limpiar_presupuesto() {
    $("#items table tr").not('.tit').not('.subtot').each(function () {
        $(this).remove();
    });
    $("#presupuesto_id").val('');
    actualizar_total();
}