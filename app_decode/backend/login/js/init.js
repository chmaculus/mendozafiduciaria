$(document).ready(function(){
    $("#form-login").validationEngine();
    $("#req").select();
    
    $("form").bind("submit", function(event) {
        event.preventDefault();
        event.stopPropagation();
        var user = $("#req").val();
        var pass = $("#pass").val();
         
        if ( $("#form-login").validationEngine('validate') ){
            //validado, validar en la base
            $.ajax({
                    url : _login.URL + "/x_send_login",
                    data : {
                        user : user,
                        pass : pass
                    },
                    dataType: "json",
                    type : "post",
                    success : function(obj){
                        if (obj.ID>0){
                            var url = "backend/dashboard/init/2";
                            $(location).attr('href',url);
                        }
                        else
                            jAlert('Error al iniciar sesión. Verifique los datos por favor..', 'Foca Software',function(){
                                });
                                                
                    }
            });
            
        }
    
    });
    
    $("#form #left").click(function(e){
        e.preventDefault();
        url = "http://www.focasoftware.com/";
        window.open(url, '_blank');
        return false;
    });
    
});