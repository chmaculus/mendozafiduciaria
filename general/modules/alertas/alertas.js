function init_alertas(){
    $(".stock-content li").on({
        "mouseover" : function(){
            $(this).addClass("over");
        },
        "mouseout" : function(){
            $(".stock-content li.over").removeClass("over");
        },
        "click" : function(){
          //  alert("click");
        }
    })
    $(".cheques-content li").on({
        "mouseover" : function(){
            $(this).addClass("over");
        },
        "mouseout" : function(){
            $(".cheques-content li.over").removeClass("over");
        },
        "click" : function(){
         //   alert("click");
        }
    })
    
    $(".content-tabs span").on({
        "mouseover" : function(){
            $(this).addClass("over");
        },
        "mouseout" : function(){
            $(".content-tabs span.over").removeClass("over");
        },
        "click" : function(){
            $(".content-tabs span.selected").removeClass("selected");
            $(this).addClass("selected");
        }
    })
    

    $(".content-tabs span").eq(0).click();
    
}

function selectcheque(){
    
    $(".cheques-content").show();
    $(".stock-content").hide();
}

function selectstock(){
    $(".cheques-content").hide();
    $(".stock-content").show();
}

function cerrar_mod(){
    $("#cont-busqueda").html("");
}

$(document).ready(function(){
    $(".alerta-content").on({
        "mouseover" : function(){
            $(this).addClass("over");
        },
        "mouseout" : function(){
            $(this).removeClass("over");
        },
        "click" : function(){
            load_mod("alertas","#cont-busqueda");
        }
    })
})