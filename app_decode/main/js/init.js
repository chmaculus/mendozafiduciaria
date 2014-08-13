var _usuario_log = {};

function get_login(){
    load_app("backend/login", "#login-content", [], function(){},  get_user, cancel_login);
    //$("#login-content").html('eeeeeeeeeeeeeee');
}

function get_user(data){
    $("#login-content").html("ssss");
    _usuario_log = data;
    $("#login-content").fadeOut(150);
    $(".main_menu").fadeIn(150);   
    $(".main_menu .item_menu").on({
        "mouseover" : function(){
            $(this).addClass("over",100);
        },
        "mouseout" : function(){
            $(this).removeClass("over",100);
        }
    })
}

function return_main(){

}

function cancel_login(){
    $("#login-content").html("");
}

$(document).ready(function(){
    if (_LOGIN==0)
        get_login();
    else{
        get_user(_USER);
    }
})

function abrir_recepcion(){
    $("#login-content").fadeIn(150);
    $(".main_menu").fadeOut(150);   
    load_app("recepcion", "#login-content", [], function(){},  function(){}, 
        function(){
            $("#login-content").html("");
            $(".main_menu").fadeIn();
        })
}

function abrir_planos(){
    $("#login-content").fadeIn(150);
    load_app("pdflista", "#login-content", [], function(){},  function(){}, 
        function(){
            $("#login-content").html("");
            $(".main_menu").fadeIn();
        })
}

function abrir_informes(){
    $("#login-content").fadeIn(150);
    load_app("informes", "#login-content", [], function(){},  function(){}, 
        function(){
            $("#login-content").html("");
            $(".main_menu").fadeIn();
        })
}


function abrir_agenda(){
    $("#login-content").fadeIn(150);
    load_app("agenda", "#login-content", [], function(){},  function(){}, 
        function(){
            $("#login-content").html("");
            $(".main_menu").fadeIn();
        })
}

function abrir_clientes(){
    $("#login-content").fadeIn(150);
    $(".main_menu").fadeOut(150);   
    load_app("clientes", "#login-content", [], function(){},  function(){}, 
        function(){
            $("#login-content").html("");
            $(".main_menu").fadeIn();
        })
}