
_creditosopciones.start= function(){
};

function guardarOpciones(){
    var opciones = {};
    var banco = $("#comboBancos").val();
    if (banco > 0){
        opciones['banco'] = banco;
    }
    $.ajax({
        url : _creditosopciones.URL + "/x_guardar_opciones",
        data: {
            opciones : opciones ,
            creditos : _creditosopciones.CREDITOS
        },
        type : "post",
        success : function(){
            jAlert("Opciones Guardadas","Las opciones ingresadas han sido guardadas correctamnete",function(){
                _creditosopciones.finish();
            });
        }
    })
}


