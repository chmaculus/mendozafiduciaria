


function invertir_seleccion() {
    console.log("invertir seleccion");
    $(".seleccionar").each(function() {
        var checked = $(this).find("input[type=checkbox]").attr("checked") ? 1 : 0;
        if (checked) {
            $(this).find("input[type=checkbox]").removeAttr("checked");
        }
        else {
            $(this).find("input[type=checkbox]").attr("checked", "checked");
        }
    });
}


function seleccionar_todo() {
    $(".seleccionar input[type=checkbox]").attr("checked", "checked");
}




function migrar() {
    var creditos = [];
    $(".seleccionar input[type=checkbox]:checked").each(function() {
        creditos.push($(this).val());
    });
    _migrar(creditos, 3);
}

function _migrar(creditos, cantidad) {
    var enviar = [];

    cantidad = creditos.length < cantidad ? creditos.length : cantidad;
    enviar = creditos.splice(0, cantidad);
    if (enviar.length === 0)
        return;


    $.ajax({
        url: _migracion.URL + "/x_migrar_creditos",
        data: {
            creditos: enviar
        },
        type: "post",
        async: false,
        success: function(rtn) {
            _migrar(creditos, cantidad);
        }

    });
}