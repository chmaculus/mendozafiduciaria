$(document).ready(function(){
    //$("#form-login").validationEngine();
    $(".cambiarpassword_div input").first().select();
   
    $("#btn_cambiar_pass").click("submit", function(e){
        e.preventDefault();
        
        var in1 = $(".cambiarpassword_div input").first();
        var in2 = $(".cambiarpassword_div input").eq(1);
        var val1 = $.trim(in1.val());
        var val2 = $.trim(in2.val());
        
        if (val1.length<5){
            jAlert('Los passwords deben tener 5 caracteres como mínimo.', 'Cambiar password',function(){
                in1.select();
            });
            return false;
        }
        if (val2.length<5){
            jAlert('Los passwords deben tener 5 caracteres como mínimo.', 'Cambiar password',function(){
                in2.select();
            });
            return false;
        }
        
        if (val1!=val2){
            jAlert('Los passwords no coinciden.', 'Cambiar password',function(){
                in2.select();
            });
            return false;
        }

        $.ajax({
                url : _cambiarpassword.URL + "/x_send_change",
                data : {
                    val1 : val1,
                    val2 : val2
                },
                dataType: "json",
                type : "post",
                success : function(obj){
                    if (obj==true || obj==1){
                        jAlert('Su clave fue cambiada. Inicie sesion por favor.', 'Mendoza Fiduciaria',function(){
                            var url = "backend/login/logout";
                            $(location).attr('href',url);
                        });
                    }
                    else
                        jAlert('Error al cambiar password. Verifique los datos por favor..', 'Foca Software',function(){
                        });

                }
        });
        
        
    });
   
   
});