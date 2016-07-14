var _total = 0;

$_clone_archivo_li = null;


$(document).ready(function(){
    $_clone_archivo_li  = $(".lista_archivos ul.datos li").eq(0).clone();
    $(".lista_archivos ul.datos li").remove();
    $("a#inline").fancybox().hide();
    abrir_archivos_lista();
});

function uploadDone() {
   var ret = $("#hidden_upload").contents().find("body").html();
   console.log(ret);
 
 
    /* If we got JSON, try to inspect it and display the result */
    if (ret.length) {
      /* Convert from JSON to Javascript object */
      var json = eval("("+ret+")");
      /* Process data in json ... */
     abrir_archivos_lista();
      
      

    }
    abrir_archivos_lista();
}

function abrir_archivos_lista(){
    $.ajax({
        url : _cobros.URL + "/x_get_archivos_bancos",
        type : "post", 
        async : false,
        dataType : "json",
        success : function(rtn){
            $(".lista_archivos ul.datos").html("");
            for(var i = 0 ; i < rtn.length ; i++){
                _agregar_item_html(rtn[i]);
            
            }

            
        }
    });
}

function _agregar_item_html(item){
    var $li = $_clone_archivo_li.clone();
    
    var fecha = new Date(item.FECHA_REC * 1000);    
    
    var fecha_txt = ("0" + fecha.getDate()).slice(-2) + "-" + ("0" + (fecha.getUTCMonth() + 1)).slice(-2) + "-"+fecha.getFullYear();
    
    $li.find(".archivo-fecha").text(fecha_txt );
    $li.find(".archivo-id").text(item.ID );
    $li.find(".archivo-nombre").text(item.ARCHIVO );
    $li.data("id",item.ID);
    if (item.ID_CREDITO) {
        $li.find(".archivo-nombre").addClass('nimp');
    }
    $(".lista_archivos ul.datos").append($li);
    
    $(".lista_archivos ul.datos li").off().on({
        "mouseenter" : function(){
            $(this).addClass("over");
        },
        "mouseleave" : function(){
            $(this).removeClass("over");    
        }
    });
    
    
    
}

function mostrar_archivo(){
    var $over = $(".lista_archivos ul li.over");
    $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
    if ($over.length==1){
        var id = $over.data("id");
        var rtn = _cobros.get_data_file(id);
        $("#div-mostrar-archivo").html(rtn);

        var cantidad_total = $(".datos .opciones_chk input").length;
        $(".datos .opciones_chk input").off().on({
            "click" : function(){
                var cantidad_seleccionados = $(".datos .opciones_chk input:checked").length;

                console.log(cantidad_total+"==="+cantidad_seleccionados);
                if (cantidad_total===cantidad_seleccionados){
                    $("#chkTodos").attr("checked","checked");
                }
                else{
                    $("#chkTodos").removeAttr("checked");
                }
            }
        });        

        
        $.unblockUI();
        
        $(".lista-extract ul li").off().on({
            "mouseenter" : function(){
                $(this).addClass("over");
            },
            "mouseleave" : function(){
                $(this).removeClass("over");    
            }
        });
        
        $("a#inline").trigger("click");
    }
}

_cobros.get_data_file = function(id){
    var rtn = false;
    $.ajax({
        url : _cobros.URL + "/x_get_cobro_file",
        data : {
            id : id
        },
        type : "post",
        async : false,
        success : function(data){
            rtn  =data;
            
            
        }
    });
    return rtn;
};

function agregar_cobros_seleccionados(){
    
    var pagos = [];
    $(".lista-extract ul li.no_ingresado").each(function(){
        console.log("length: " + $(this).find(".opciones_chk input:checked").length );
       if ( $(this).find(".opciones_chk input:checked").length > 0 ){
           pagos.push({
               "ID" : $(this).data("id"),
               "ID_CREDITO" : $(this).data("idcredito"),
               "FECHA" : $(this).data("fecha"),
               "CREDITO_VENCIMIENTO" : $(this).data("cvencimiento"),
               "IMPORTE" : $(this).data("importe")
           });
       }
    });
    console.dir(pagos);
    _cobros.agregar_coboros_credito(pagos);
}

_cobros.agregar_coboros_credito = function(cobros){
    
    $.ajax({
        url : _cobros.URL.replace('front/cobros', 'front/cuotas') + "/x_add_cobros",
        data : {
            cobros : cobros
        },
        type : "post",
        success : function(rtn){
            console.log(rtn);
            $.fancybox.close();
        }
    });
};


function invertir_seleccion(){
    $("#chkTodos").attr("checked","checked");
    $(".datos .opciones_chk input[type=checkbox]").each(function(i){
        var checked = $(this).attr("checked") ? 1 : 0;
        
        
        if (checked){
            $("#chkTodos").removeAttr("checked");
            $(this).removeAttr("checked");
        }
        else{
            $(this).attr("checked","true");
        }
    });
}

function seleccionar_todos(){
    if ($("#chkTodos:checked").length){
        $(".datos .opciones_chk input[type=checkbox]").attr("checked", "checked");
    }
    else{
        $(".datos .opciones_chk input[type=checkbox]").removeAttr("checked");
    }
}

function borrar_archivo() {
    var $over = $(".lista_archivos ul li.over");
    jConfirm("¿Esta seguro de eliminar el archivo?", "Eliminar archivo de cobro", function (i) {
        if (i) {
            
            $.blockUI({message: '<h4><img src="general/images/block-loader.gif" />Procesando</h4>'});
            if ($over.length == 1) {
                var id = $over.data("id");
                $.ajax({
                    url: _cobros.URL + "/x_del_cobros",
                    data: {
                        id: id
                    },
                    type: "post",
                    success: function (rtn) {
                        if (rtn == '-2') {
                            jAlert('No se puede eliminar el archivo, tiene imputaciones de pagos realizadas', "Eliminar archivo", function(){});
                        } else if (rtn != '1') {
                            jAlert('Hubo un problema, vuelva a intentar', "Eliminar archivo", function(){});
                        } 

                        $.unblockUI();
                        if (rtn == '1') {
                            abrir_archivos_lista();
                        }
                    }
                });

            }
        }
    });
}