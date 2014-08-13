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
            console.dir(rtn);
        }
    });
}

function _agregar_item_html(item){
    var $li = $_clone_archivo_li.clone();
    
    var fecha = new Date(item.FECHA_REC * 1000);    
    
    var fecha_txt = fecha.getDate() + "-"+(fecha.getMonth() + 1) + "-"+fecha.getYear();
    
    $li.find(".archivo-fecha").text(fecha_txt );
    $li.find(".archivo-nombre").text(item.ARCHIVO );
    $li.data("id",item.ID);
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
    $(".lista-extract ul li").each(function(){
       if ( $(this).find(".opciones_chk input:checked").length ){
           pagos.push({
               "ID" : $(this).data("id"),
               "ID_CREDITO" : $(this).data("idcredito"),
               "FECHA" : $(this).data("fecha"),
               "IMPORTE" : $(this).data("importe")
           });
       }
    });
    _cobros.agregar_coboros_credito(pagos);
}

_cobros.agregar_coboros_credito = function(cobros){
    
    $.ajax({
        url : _cobros.URL + "/x_add_cobros",
        data : {
            cobros : cobros,
        },
        type : "post",
        success : function(rtn){
            console.log(rtn);
            $.fancybox.close();
        }
    });
};