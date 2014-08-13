_lista_html_item= {};


function init_busqueda(){
    _lista_html_item = set_lista({content_id :"main_busqueda", items : "li",  onfocus_class : "over", click : "select_item()"});

    if (_array_search.length==1){
        _select_item(0);
        return;
    }
    $('#txtSearch').quicksearch('#main_busqueda li',
        {'show': function () {
                $(this).removeClass("none");
                $(this).addClass("ShowElem");
        },
        'hide': function () {
                $(this).removeClass("ShowElem over");
                $(this).addClass("none");
        }});
}

function select_item(){
    var index = $("#main_busqueda .over").index();
    _select_item(index);
}

function _select_item(index){
    $("#main_busqueda").html("").hide();
    eval(_callback+"(_array_search[index])");
}

function get_item(id){
    alert(id);
}

function cancelar_busqueda(){
    $("#cont-busqueda").html("");
}