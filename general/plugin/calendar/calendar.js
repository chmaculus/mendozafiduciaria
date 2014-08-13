var __meses_calendario = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];


function calendario(month, year, day){
    if (typeof month=='undefined'){
        var fec = new Date();
        month = fec.getMonth()+1;
        year = parseInt(fec.getFullYear());
    }


    this.func = "";
    this.select_day = "";
    this.month = month;
    this.year = year;
    month = month - 1;


    var fecha = new Date(year,month,1);
    var primer_dia = fecha.getDay();
    var total_dias = 32 - new Date(year, month, 32).getDate();


    var num_dia = 1;
    var size_day = parseInt($("#calendar").width());
    size_day = size_day/7;

    $("#calendar").html("");
    for(var i = 0 ; i < 42 ; i++){
        var hdom;
        if (i >= primer_dia && i <= total_dias+primer_dia-1){
            
//            hdom = "<div onclick='seleccion_dia("+num_dia+", "+parseInt(month+1)+", "+year+")' class='dia'>"+num_dia+"</div>";
            hdom = "<div class='dia'>"+num_dia+"</div>";
            num_dia++;
        }
        else{
            hdom = "<div class='dia_vacio'>&nbsp</div>";
        }

        $("#calendar").append(hdom);
    }
    $(".dia, .dia_vacio").css("width",(size_day-2)+"px");


    this.getSelect = function(){
        return this.select_day;
    }
    this.get_day = function(){
        return this.select_day;
    }


    this.addClass = function(arr, clase){
        for(var i = 0 ; i < arr.length ; i++){
            $("#calendar .dia").eq(arr[i]-1).addClass(clase);
        }
    }

    this.removeClass = function(clase){
        $("#calendar .dia").removeClass(clase);
    }

    this.onClickDia = function(callback){
        //console.log(total_dias+primer_dia);
        $("#calendar .dia").off("click").click(function(){
            var arr_day =   $(this).index(".dia") + 1 ;
            $("#calendar .dia.over_day").removeClass("over_day");
            $(this).addClass("over_day");
            callback(arr_day);
            //callback(total_dias+primer_dia-1);
        });
    }

    this.getClassDay = function(clase){
        var arr = [];
        $("."+clase).each(function(){
            arr.push($(this).index() - primer_dia + 1);
        })
        return arr;
    }

    this.get_month = function(){
        return this.month;
    }
    this.get_year = function(){
        return this.year;
    }
    this.get_time_object = function(){
        return new Date(this.get_year(), this.get_month(), this.getClassDay())
    }
    
    this.get_month_name = function(){
        return __meses_calendario[this.month-1];
    }
    
    
    this.next_month = function(){
        var next_m;
        var next_y;
        if (this.month==12){
            next_m = 1;
            next_y = this.year + 1;
        }
        else{
            next_m = this.month + 1;
            next_y = this.year ;
        }

        return new calendario(next_m, next_y,1);

    }
    this.prev_month = function(){
        var prev_m;
        var prev_y;
        if (this.month==1){
            prev_m = 12;
            prev_y = this.year - 1;
        }
        else{
            prev_m = this.month - 1;
            prev_y = this.year ;
        }

        return new calendario(prev_m, prev_y,1);
    }
}

