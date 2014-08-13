$(document).ready(function(){
    
    $("#etiqueta_modulo").html('Dashboard');
    
    var dat_notif = $(".notif").data("notificaciones");
    if (dat_notif && dat_notif>0){
        $(".notif").trigger("click");
    }
    
});