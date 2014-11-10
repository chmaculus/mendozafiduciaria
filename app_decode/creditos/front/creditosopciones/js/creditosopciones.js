
_creditosopciones.start= function(){
    var selected = $("#comboBancos").data("val");
    $("#comboBancos").val(selected);
};

function guardarOpciones(){
    var opciones = {};
    var banco = $("#comboBancos").val();
    var convenio = $("#txtConvenio").val();
    if (banco > 0){
        opciones['banco'] = banco;
    }
    if (convenio.length > 0){
        opciones['convenio'] = convenio;    
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


