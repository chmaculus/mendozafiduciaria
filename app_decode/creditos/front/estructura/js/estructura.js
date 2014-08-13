var _total = 0;
var _version_id = 0;

$(document).ready(function(){
    if (parseInt(_estructura.MODIFICAR)===1){
       $(".editar").show();
    }
    else{
        $(".editar").hide();
    }
    
    
    $(".cuotas_monto input").on({
        "focusin" : function(){
            $(this).select();
        },
        "change" : function(){
            calcular_total();
        }
    });
    var total = calcular_total();
    _total = total ;
    
    $("#spTotalReal").text(total );
    
    if (parseInt(_estructura.MODIFICAR)===1){
        $(".editar-estructura").show();
        $(".cuotas_fecha_modificar").on({
            "click" : function(){
                var index = $(this).parent("li").index();
                _estructura.CUOTAS[index-1]['FECHA_TEXT'] = $(this).text();
                var cantidad_cuotas = $("li.datos").length;
                var cuotas_restantes = cantidad_cuotas - (index - 1);
                modificar_opciones_cuota(_estructura.ID_CREDITO, cuotas_restantes);
            }
        });
    }
    else{
    }    
    

    
    $("a#inline").fancybox().hide();
});

function modificar_fecha(elem){
    
}

function calcular_total(){
    var total = 0;
    $(".cuotas_monto input").each(function(){
        console.log($(this).val());
        total += parseFloat($(this).val());
    });
    $("#spTotal").text(total);
    return total;
}

function guardar_datos_cuota(){
    var total = calcular_total();
    
    console.log(total + " - "+ _total);
    if (parseFloat(total) !== _total ) {
        jAlert("Los montos de las cuotas no coincide con el total correspondiente","Problema de actualizacion", function(){
            return;
        });
        return;
    }
    
    var cuotas = [];
    $("li.datos").each(function(){
        cuotas.push({id : $(this).data("id"), monto : $(this).find("input").val()});
    });
    
    _estructura.guardar_montos_cuotas(cuotas);
    _estructura.restart();

}

_estructura.guardar_montos_cuotas = function(cuotas){
    $.ajax({
        url : _estructura.URL + "/x_guardar_montos",
        data : {
            cuotas : cuotas,
            credito_id : _estructura.ID_CREDITO
        },
        type : "post",
        success : function(rtn){
            console.log(rtn);
            jAlert("Se ha actualizado los montos de las cuotas", function(){
                return;
            });
            return;

        }
    });
};

function guardar_datos_reset(){
    var cuotas = [];
    $("li.datos").each(function(){
        cuotas.push({id : $(this).data("id"), monto : 0});
    });    
    _estructura.guardar_montos_cuotas(cuotas);
    _estructura.restart();
}

function modificar_opciones_cuota(id_credito, cuotas_restantes) {

    $.ajax({
        url: _estructura.URL + "/x_abrir_opciones_cuota",
        data: {
            version_id: _version_id || 0,
            credito_id: id_credito,
            cuotas_restantes: cuotas_restantes
        },
        type: "post",
        success: function(html) {
            $("#div_opciones_cuotas").html(html);
            var fecha_inicio = $("#txtFechaInicioEdit").val();
            var fecha_vencimiento = $("#txtFechaVencimientoEdit").val();

            $("a#inline").trigger("click");
            $(".fecha_opciones").datepicker({
                changeMonth: true,
                changeYear: true
            });
            $(".fecha_opciones").datepicker("option", "dateFormat", 'dd-mm-yy');
            var desde = new Date(fecha_inicio * 1000);
            var hasta = new Date(fecha_vencimiento * 1000);
            $("#txtFechaInicioEdit").datepicker("setDate", desde);
            $("#txtFechaVencimientoEdit").datepicker("setDate", hasta);
        }
    });
}

function guardar_opciones_cuota(id_credito, cuotas_restantes) {
    $(".btns-opciones-cuota").hide();
    var fecha_vencimiento = $.datepicker.formatDate('@', $("#txtFechaVencimientoEdit").datepicker("getDate")) / 1000;
//    var fecha_inicio = $.datepicker.formatDate('@', $("#txtFechaInicioEdit").datepicker("getDate")) / 1000;
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    $.ajax({
        url: _estructura.URL + "/x_guardar_opciones_cuota",
        data: {
            version_id: _version_id || 0,
            credito_id: id_credito,
            cuotas_restantes: cuotas_restantes,
//            fecha_inicio: fecha_inicio,
            fecha_vencimiento: fecha_vencimiento
        },
        type: "post",
        async:false,
        success: function(rtn) {
            $.unblockUI();      
            console.log(rtn);
            $.fancybox.close();
            _estructura.restart();
        }
    });
}