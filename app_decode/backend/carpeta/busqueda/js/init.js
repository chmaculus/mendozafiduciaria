var tm_busc = new tabmanager();
var _panel_busc = {
    pan: "#lista1busc",
    esc: "cancelar_busqueda();"
}

    tm_busc.add(_panel_busc);
    tm_busc.reset();
    tm_busc.not_tab();
    tm_busc.set_tab(_panel_busc);
    

function cancelar_busqueda(){
    _busqueda.cancel();
}
var _items;

$(document).ready(function(){
    _items = _busqueda.ITEMS;
    
    if (_items.length==0){
        _items.finish({
            "COD" : "0", 
            "NOM" : "", 
            "DOM" : "",
            "CUIT" : 0
        });
        return;
    }
    
    $(".main_window_fix.busqueda ul.content li").on({
        "mouseover" : function(){
            $(this).addClass("over");
        },
        "mouseout" : function(){
            $(this).removeClass("over");
        },
        "click" : function(){
            var index = $(this).index();

            _busqueda.finish(_items[index]);
        }
    })
    
    $('#txtSearchBusqueda').quicksearch('.main_window_fix.busqueda ul.content li',	
                {'show': function () {
                        $(this).removeClass("none");
                        $(this).addClass("ShowElem");
                },
                'hide': function () {
                        $(this).removeClass("ShowElem over");
                        $(this).addClass("none");
                }});        
    $('#txtSearchBusqueda').focus();
        
})

