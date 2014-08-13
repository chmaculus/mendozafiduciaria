
function tabmanager(){
    var _panel_anterior = 0;
    var randomnumber;
    var panel = [];
    var _panel;
    var _no_tab = false;
    var _workspace; //workspace para los eventos
    
    
    var panel_default = {};
    
    this.add = function(elem, def){    

        
        //genrar nombre de workspace
        var randomnumber=Math.floor(Math.random()*1000001);
        _workspace = "wks_"+randomnumber;

        if (typeof elem.shortcut!='undefined'){
            
        }
        else{
            elem.shortcut = {};
        }

        elem.active = true;
        panel.push(elem);
        
        if (def==true){
            panel_default = elem;
        }
        else{
            //el primer panel que se agrega es el default
            if (panel.length == 1){
                panel_default = panel[0];
            }                
        }        
        _panel = 0; 
    }
    
    this.remove = function(str){
        for(var i = 0 ; i < panel.length ; i++){
            if (str == panel[i]["pan"]){
                _panel = i;
                break;
            }
        }
        this.panel.splice(_panel,1);
        _panel = 0;        
    }
    
    this.set_tab = function(str, init){
        _panel_anterior = _panel;
        if (typeof str == 'undefined' || str=='default'){
            _panel = 1000;            
        }
        else{
            for(var i = 0 ; i < panel.length ; i++){
                if (typeof(str)== "object"){
                    if (str.pan == panel[i]["pan"]){
                        _panel = i;
                        
                    }
                }
                else{
                    if (str == panel[i]["pan"]){
                        _panel = i;
                    }
                }  
            }            
        }
        this.get_tab(init);   
    }
    
    this.get_prev_panel = function(){
        return panel[_panel_anterior];
    }
    
    this.get_tab = function(init){
        $(document).unbind("keyup");
        $(document).bind("keyup."+_workspace,keyup_bind);   
        this.bind_shortcut();
        this.bind_exit();
        
        if (typeof init !=='undefined'){
            
        }
        
        this.bind_init(init);
    }
    
    this.set_inactive = function(str){
        for(var i = 0 ; i < this.panel.length ; i++){
            if (str == this.panel[i]["pan"]){
                this.panel[i]["active"] = false;
                _panel = i;
            }
        }
    }
    //go : true, hace activo tab
    this.set_active = function(str, go){
        for(var i = 0 ; i < this.panel.length ; i++){
  
            if (typeof(str)== "object"){
                if (str.pan == panel[i]["pan"]){
                    _panel = i;
                }
            }
            else{
                if (str == panel[i]["pan"]){
                    _panel = i;
                }
            }            
        }
        
        if (typeof go == 'undefined' || go == false){
            
        }
        else{
            set_tab(str);
        }        
    }

    function keyup_bind (ev){
            ev.preventDefault();  
            // cambia con tabulador pero si esta activado
            if(ev.keyCode == 9 && !_no_tab ){
                _panel++;
                if (_panel >= panel.length) _panel = 0;    
                
                $(document).unbind("keyup");
                $(document).bind("keyup."+_workspace,keyup_bind);
            }

             
             //se evalua si se trae el panel default   
            var panel_tmp = _panel == 1000 ? panel_default : panel[_panel] ;
             
            for (atributo in panel_tmp) {
                
                if (atributo!="pan" && atributo!="shortcut" &&  atributo!="init" &&  atributo!="exit"){
                    var code = 0;
                    switch(atributo){
                        case "pan":code = 0;break;
                        case "divide":code = 111;break;
                        case "up":code = 38;break;
                        case "down":code = 40;break;
                        case "left":code = 37;break;
                        case "right":code = 39;break;
                        case "intro":code = 13;break;
                        case "space":code = 32;break;
                        case "del":code = 46;break;
                        case "insert":code = 45;break;
                        case "ctrl":code = 17;break;
                        case "end":code = 35;break;
                        case "esc":code = 27;break;
                        case "shift":code = 16;break;
                        case "alt":code = 18;break;
                        case "tab":code = 9;break;
                        case "repag":code = 33;break;
                        case "avpag":code = 34;break;
                        default:break;
                    }
                    if(ev.keyCode==code){
                        if (typeof panel[_panel][atributo] == "function"){
                            panel[_panel][atributo]();
                        }
                        else{
                            eval(panel[_panel][atributo]);
                        }
                        
                    }                   
                } 
            }    
    }
    
    this.bind_shortcut = function(){   
        if (typeof panel[_panel]['shortcut']=="undefined") return;
        
        for (sc in panel[_panel]['shortcut']){ //carga shurcut 
            var combinacion = sc;
            shortcut.remove(combinacion);
            var str =             'shortcut.add("'+combinacion+'",function() {                '+
                panel[_panel]["shortcut"][combinacion]+';'+
            '});';
            eval(str);

        }
    }        
    
    this.unbind_shortcut = function(){  
        if (typeof _panel == 'undefined') return;
        
        if (typeof panel[_panel]['shortcut']=='undefined'){
            
        }
        else{
            for (sc in panel[_panel]['shortcut']){ //carga shurcut 
                var combinacion = sc;
                shortcut.remove(combinacion);
                var str =             'shortcut.add("'+combinacion+'",function(){});';
                eval(str);
            }
        }
    }        
    
    this.bind_init = function(init){
        if (typeof init != 'undefined' ){
             eval(init);
        }
         else
            eval(panel[_panel]['init']);
    }

    this.bind_exit = function(){
        eval(panel[_panel_anterior]['exit']);
    }
    
    this.not_tab = function(){
        _no_tab = true;
    }
    
    this.reset = function(){
        $(document).unbind("keyup");
        this.unbind_shortcut();
    }
    
    this.get_active_tab = function(){
        return panel[_panel]['pan'];
    }
    this.get_active_tab_object = function(){
        return panel[_panel];
    }
}
///////////////////////////////////////////////





function set_formtab(object){
    var ft = new formtab(); 
   
   
    $.extend(ft._op, object )
    return ft;
}

function formtab(){
    var _inputs_val = [];
    var _selected_input = 0;


    
    this._op = {
        onfocus_class : "over",
        content_id : "#form_content",
        content_element : "li",
        items : "inp_form",
        params : {
            length : "length",
            type : "type"
        }
    }
    
    var _op_ = this._op;
    
    //Agrega campos segun un selector con un nombre generico
    this.setFormList = function(sel, val){
        

        
        var tmp = [];
        $(sel).each(function(i){
            $(sel).eq(i).attr("id","inp_"+i);
            //se evalua si tiene parametros dinamicos
            var length =  (typeof _op_.params.length=='undefined') ? val.length : $(sel).eq(i).data(_op_.params.length);
            var tipo=  (typeof _op_.params.type=='undefined') ? val.tipo :  $(sel).eq(i).data(_op_.params.type); ;
        
            //se crea el objeto
            var obj = {
                Nombre :"inp_"+i,
                nombre :"inp_"+i,
                tipo : tipo,
                max_length : length,                
                rules : {},
                active: true
            }
            tmp.push(obj);
            
        })
        console.dir(tmp);

        for(var i  = 0 ; i < tmp.length ; i++){
            this.add(tmp[i]);
        }
    }
    
  
    
    this.add = function(val, def){     
        var main_object  = this;
        val.get$ = function(){
            return $("#"+val.nombre);
        }
        
        val.put = function(data){
            $("#"+val.nombre).val(data);
        }        
        val.get = function(data){
            return $("#"+val.nombre).val();
        }        
        val.validate = function(){
            return main_object.validate(val.nombre);
        }
        val.active = true;
        
        if (typeof def=='undefined') {
            val.def = false;
        }
        else{
            val.def = def;
        }
        
        
        
        _inputs_val.push(val);
        $("#"+val.nombre).closest(this._op.content_element).addClass(this._op.items);
        
        if (val.def!=false)
            $("#"+val.nombre).val(def);

    }
    
    this.seleccionar = function(elem){
        if (typeof elem=='object'){
            elem = elem.nombre;
        }

        if ($("#"+elem).length==1 && $("#"+elem).closest("."+this._op.items)){
            $(this._op.content_id+" ."+this._op.onfocus_class).removeClass(this._op.onfocus_class);
            $("#"+elem).closest("."+this._op.items).addClass(this._op.onfocus_class);
            this._seleccionar_input(elem);
        }
        else{

        }
    }
    
    this._seleccionar_input = function(elem){
        var i = 0;
        var find = false;

        for(i =  0 ; i < _inputs_val.length ; i++){
            if (_inputs_val[i]['nombre'] == elem){  
                find = true;
                break;
            }
        }
        
        if (find){
            _selected_input = i;
            set_next(elem,_inputs_val[i]['max_length'],_inputs_val[i]['tipo'],2);   

        }
    }
    
    this._focus_input = function(){
        
    }
    
    this.prev = function(){
        if ($(this._op.content_id+" ."+this._op.onfocus_class).length == 0) {$("."+this._op.items).first().addClass(this._op.onfocus_class);return;}
        if($(this._op.content_id+" ."+this._op.onfocus_class).prevAll("."+this._op.items).length == 0) return;
        var $prev = $(this._op.content_id+" ."+this._op.onfocus_class).prevAll("."+this._op.items).first();
        $(this._op.content_id+" ."+this._op.onfocus_class).removeClass(this._op.onfocus_class);
        $prev.addClass(this._op.onfocus_class);
        this._seleccionar_input($prev.find("input[type=text], input[type=password], textarea").attr("id"));
    }

    this.next = function(){
        if ($(this._op.content_id+" ."+this._op.onfocus_class).length == 0) {$("."+this._op.items).first().addClass(this._op.onfocus_class);return;}
        if($(this._op.content_id+" ."+this._op.onfocus_class).nextAll("."+this._op.items).first().length == 0) return;
        var $next = $(this._op.content_id+" ."+this._op.onfocus_class).nextAll("."+this._op.items).first();
        $(this._op.content_id+" ."+this._op.onfocus_class).removeClass(this._op.onfocus_class);
        $next.addClass(this._op.onfocus_class);
        this._seleccionar_input($next.find("input[type=text], input[type=password], textarea").attr("id"));
    }    
    
    this.first = function(){
        var $first = $(this._op.content_id+" "+this._op.content_element).first();
        
        $(this._op.content_id+" ."+this._op.onfocus_class).removeClass(this._op.onfocus_class);
        $first.addClass(this._op.onfocus_class);
        
        this._seleccionar_input($first.find("input[type=text], input[type=password], textarea").attr("id"));
    }
    
    this.is_last = function(){
        var rtn = false ;

        if ( $("."+this._op.onfocus_class).find("input[type=text], input[type=password], textarea").first().attr("id") ==   _inputs_val[_inputs_val.length-1].Nombre)
            rtn = true;
        return rtn;            
    }
    
    this.is_first = function(){
        var rtn = false ;
        if ( $("."+this._op.onfocus_class).find("input[type=text], input[type=password], textarea").first().attr("id") ==   _inputs_val[0].Nombre)
            rtn = true;
        return rtn;            
    }
    
    this.current = function(){
        this._seleccionar_input($("."+this._op.onfocus_class).find("input[type=text], input[type=password], textarea").first().attr("id"));
    }
    
    this.remove = function(elem){
        var i = 0;
        var find = false;
        for(i =  0 ; i < _inputs_val.length ; i++){
            if (_inputs_val[i]['nombre'] == elem.nombre){  
                find = true;
                break;
            }
        }
        if (find){
            $("#"+_inputs_val[i]['nombre']).closest(this._op.content_element).removeClass(this._op.items)
            _inputs_val.splice(i, 1)
        }
    }
    this.disabled = function(elem){
        var i = 0;
        var find = false;
        for(i =  0 ; i < _inputs_val.length ; i++){
            if (_inputs_val[i]['nombre'] == elem.nombre){  
                find = true;
                break;
            }
        }
        if (find){
            $("#"+_inputs_val[i]['nombre']).closest(this._op.content_element).removeClass(this._op.items)
            _inputs_val[i].active = false;
        }
    }
    this.enabled = function(elem){
        var i = 0;
        var find = false;
        for(i =  0 ; i < _inputs_val.length ; i++){
            if (_inputs_val[i]['nombre'] == elem.nombre){  
                find = true;
                break;
            }
        }
        if (find){
            $("#"+_inputs_val[i]['nombre']).closest(this._op.content_element).addClass(this._op.items)
            _inputs_val[i].active = true;
        }
    }
    
    this.get_current = function(){
        return _inputs_val[_selected_input];
    }
    
    //obtiene objeto
    this.get_object = function(obj){
        var find = false;
        
        for(var i = 0 ; i < _inputs_val.length ; i++){
            if (_inputs_val[i].nombre == obj.nombre){
                find = _inputs_val[i];
                break;
            }
        }
        return find;
    }
    
    this.set = function(atribute, value){
        var ev = "_inputs_val[_selected_input]."+atribute+" = "+value;
        eval(ev)
    }
    this.get = function(atribute){
        var ev = "return _inputs_val[_selected_input]."+atribute;
        eval(ev)
    }
    
    this.validate = function(elem){

        var obj;
        if ( _inputs_val.length == 0) return false;
        
        if (typeof elem!='undefined'  ){
            for(i =  0 ; i < _inputs_val.length ; i++){
                if (_inputs_val[i]['nombre'] == elem){  
                    find = true;
                    obj = _inputs_val[i];
                    
                    break;
                }
            }            
        }
        else{
            obj = _inputs_val[_selected_input];
        }

        var value = $("#"+obj.nombre).val();

        var error= [];
        for (atributo in obj['rules']) {
            var atributo_value = obj['rules'][atributo]

            
            var valid;
            switch(atributo){

                case "required":
                    if (atributo_value === true){
                        if (value.length > 0){

                        }
                        else{
                            error.push(atributo);
                        }
                    }
                    break;
                case "range":
                    if (value <= atributo_value[1] && value >= atributo_value[0] ){

                    }
                    else{
                        error.push(atributo);
                    }
                    break;
                case "inside":
                    valid = false;
                    for(var x = 0 ; x < atributo_value.length ; x++){
                        if (value == atributo_value[x]){
                            
                            valid = true;
                            break;
                        }
                    }
                    if (valid){
                        
                    }
                    else{
                        error.push(atributo);
                    }
                    break;
                case "date":
                    if (value.length==0){valid = true; break;}
                    valid = false;
                    var strSeparator = value.substring(2,3)
                    //create a lookup for months not equal to Feb.
                    var arrayDate = value.split(strSeparator);

                    var arrayLookup = {'01' : 31,'03' : 31,
                      '04' : 30,'05' : 31,
                      '06' : 30,'07' : 31,
                      '08' : 31,'09' : 30,
                      '10' : 31,'11' : 30,'12' : 31
                    }

                    var intDay = parseInt(arrayDate[0],10);
                    var intMonth = parseInt(arrayDate[1],10);
                    var intYear = parseInt(arrayDate[2],10);
                    //check if month value and day value agree

                    if (arrayLookup[arrayDate[1]] != null) {
                      if (intDay <= arrayLookup[arrayDate[1]] && intDay != 0
                        && intYear > 1975 && intYear < 2050)
                        valid =  true;     //found in lookup table, good date
                    }
                    else{
                        if (intMonth == 2) {
                          var intYear = parseInt(arrayDate[2]);

                          if (intDay > 0 && intDay < 29) {
                            valid =  true;
                          }
                          else if (intDay == 29) {
                            if ((intYear % 4 == 0) && (intYear % 100 != 0) ||
                                (intYear % 400 == 0)) {
                              // year div by 4 and ((not div by 100) or div by 400) ->ok
                              valid = true;
                            }
                          }
                        }
                    }
                    if (valid==true){
                        
                    }
                    else{
                         error.push(atributo);
                    }
                break;

            }
        }
        for(var i = 0 ; i < error.length ; i++){

        }
        var rtn = true;
        if (error.length > 0)
        rtn = false;

        return rtn;
    }
}



//////////////////////////////////////////////////////////////////////////////////



function set_lista(data){
    var iLista = new lista();
    $.extend(iLista._op, data)

    $("#"+iLista._op.content_id+" "+ iLista._op.items).addClass(iLista._op.classinclude);
    
    
    $("#"+iLista._op.content_id+" "+ iLista._op.items).off().on({
        "mouseover" : function(){
            $("#"+iLista._op.content_id+" "+ iLista._op.items).removeClass(iLista._op.onfocus_class);
            $(this).addClass(iLista._op.onfocus_class);
        },
        "mouseout" : function(){
            $(this).removeClass(iLista._op.onfocus_class);
        },      
        "click" : function(){
            if (typeof iLista._op.click== 'function'){
                iLista._op.click($(this) );
            }
            else{
                eval(iLista._op.click);
            }

        }      

    });
    return iLista;
}

function lista(){
    
    this._op = {
        onfocus_class : "over",
        content_id : "lista",
        items : "inp_form",
        callbefore : "",
        callafter : "",
        classinclude : "inc",
        click : ""
    }
    



    this.next = function(){        
        var $li = this._op.items;
        var $cont = "#"+this._op.content_id;
        var $class = this._op.onfocus_class;
        
        //si no existe arranca desde el primero

        
        if($($cont + " " + $li + "." + $class).length==0) {
            $($cont + " " + $li).first().addClass($class);

            return $($cont + " ."+$class);
        }

        //si no hay siguiente no hace nada
        if ($($cont + " ." + $class).nextAll("."+this._op.classinclude).length==0) return $($cont + " ."+$class);

         
        var $next = $($cont + " ." + $class).nextAll("."+this._op.classinclude).first();
        $($cont + " ." + $class).removeClass($class);
        $next.addClass($class);
       
       // eval(this._op.items.callafter);
        return $next;
        
    }
    
    this.reset = function(){
        var $li = this._op.items;
        var $cont = "#"+this._op.content_id;        
        $($cont + " " + $li).remove();
    }
    
    this.prev = function(){
        var $li = this._op.items;
        var $cont = "#"+this._op.content_id;
        var $class = this._op.onfocus_class;

        //si no existe arranca desde el primero
        if($($cont + " "+$li+"."+$class).length==0) {
            $($cont + " "+$li).first().addClass($class);
            return $($cont + " ."+$class);
        }
        
        //si no hay anterior no hace nada
        if ($($cont + " ."+$class).prevAll("."+this._op.classinclude).length==0) return $($cont + " ."+$class);
        
        
        var $prev = $($cont + " ." + $class).prevAll("."+this._op.classinclude).first();
        $($cont + " ." + $class).removeClass($class);
        $prev.addClass($class);
        return $prev;        
    }    
    
    this.set_current = function(index){
        $("#"+this._op.content_id+" "+this._op.items).removeClass(this._op.onfocus_class);
        $("#"+this._op.content_id+" "+this._op.items).eq(index).addClass(this._op.onfocus_class);
    }
    
    this.get_current = function(){
        var $class = this._op.onfocus_class;
        var $cont = "#"+this._op.content_id;
        return $($cont + " ."+$class).length == 1 ? $($cont + " ."+$class) : 0;
    }
    
    this.unselect = function(){
        $("#"+this._op.content_id+" "+this._op.items).removeClass(this._op.onfocus_class);
    }    

    
    this.remove = function(nro){
        var $li = this._op.items;
        var $cont = "#"+this._op.content_id;
        $($cont+" "+$li).eq(nro).removeClass(this._op.classinclude);
    }
    
    this.remove_current = function(){
        var $class = this._op.onfocus_class;
        var $cont = "#"+this._op.content_id;
        return $($cont + " ."+$class).remove();
    }
    
    this.first = function(){
        
    }
    
}


function validar(id){
    
}

function isDefined(variable) {
    return (typeof(window[variable]) == "undefined")?  false: true;
}

function set_next(nombre_componente, max_length, tipo, forma, init){
    
    if (init == true) _b_init_teclado_numerico = true;

    if (forma==1){        

        $("#"+nombre_componente).focus();
    } 
    else if (forma==2){
        $("#"+nombre_componente).focus();
        //$("#"+nombre_componente).select();
    }
    else if (forma==0){    
        $("#"+nombre_componente).focus();
    }

    
    $("#"+nombre_componente).attr("maxlength",max_length)
    
    $("div").removeClass("active");
    
    $("#"+nombre_componente).parent().addClass("active");   
    $("#"+nombre_componente).focus();
    $("#"+nombre_componente).off("keydown.us").on("keydown.us",function(event){
        var k = event.keyCode;
        console.log(k);
        switch(tipo){
            
            case 0://ALFANUMERICO   
                if(!((k == 32) || (k == 13) || ((k >= 65) && (k <= 90)) || ((k >= 48) && (k <= 57)) || ((k >= 96) && (k <= 122)) || k == 188 || special_char(k))){	             
                    return false
                }
                break;
            case 6://NUMERICO   
            case 1://NUMERICO   `
                if(!((k == 13) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 105))) || special_char(k))){		
                    return false
                }
                break;                
            case 2://ALFABETICO     
                if(!((k == 32) || (k == 13) || ((k >= 65) && (k <= 90)) || ((k >= 96) && (k <= 122))  ||  special_char(k))){	           
                    return false
                }                
                break;
            case 30://VERIFICAR COMPATIBILIDAD CON ANALET(Q=3 -> Q=30) facturador-normal->392
                if(!((k == 13) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 122))) || special_char(k))){
                    return false
                }
                break;
            case 31://VERIFICAR COMPATIBILIDAD CON ANALET(Q=3 -> Q=30) facturador-normal->392
                if(!((k == 13) || (k == 107) ||(k == 32) || ((k >= 65) && (k <= 90)) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 105))) || special_char(k))){
                    return false
                }
                break;
                
            case 29://RECUENTO, SOLO NUMEROS Y COMAS
                if(!((k == 13) || (k == 110) || (k == 190) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 105))) || special_char(k))){
                    return false
                }
                break;
            case 32://Hora, SOLO NUMEROS Y :
                if(!((k == 13) || (k == 190) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 105))) || special_char(k))){
                    return false
                }
                break;
            case 4://SOLO NUMEROS
                if(!((k == 13) || (k == 27) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 105))) || special_char(k))){
                    return false
                }
                break;
            case 15://NUMERICO CON GUION MEDIO
                if(!((k == 13) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 122))) || (k <= 12) || special_char(k))){		
                    return false
                }
                break;
                default:
        }
        
        return true;
        
    });
}

//  backspace: 8,  delete: 46,  flechas : 37 39, escape: 27
function special_char(k){
    var rtn = false;
    if (k == 8 || k == 46|| k == 37|| k == 39 || k == 27)
        rtn = true;

    return rtn;
}



//eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('t 2l(){s d=0;s e;s f=[];s g;s h=A;s j;s k={};5.1h=t(a,b){s c=1N.2m(1N.2n()*2o);j="2p"+c;r(G a.O!=\'N\'){}F{a.O={}}a.U=B;f.Y(a);r(b==B){k=a}F{r(f.w==1){k=f[0]}}g=0}5.1a=t(a){H(s i=0;i<f.w;i++){r(a==f[i]["Q"]){g=i;u}}5.1i.1O(g,1);g=0}5.1P=t(a,b){d=g;r(G a==\'N\'||a==\'1x\'){g=1Q}F{H(s i=0;i<f.w;i++){r(G(a)=="1y"){r(a.Q==f[i]["Q"]){g=i}}F{r(a==f[i]["Q"]){g=i}}}}5.1R(b)}5.2q=t(){v f[d]}5.1R=t(a){$(1j).1z("1k");$(1j).1S("1k."+j,1A);5.1T();5.1U();r(G a!==\'N\'){}5.1V(a)}5.2r=t(a){H(s i=0;i<5.1i.w;i++){r(a==5.1i[i]["Q"]){5.1i[i]["U"]=A;g=i}}}5.2s=t(a,b){H(s i=0;i<5.1i.w;i++){r(G(a)=="1y"){r(a.Q==f[i]["Q"]){g=i}}F{r(a==f[i]["Q"]){g=i}}}r(G b==\'N\'||b==A){}F{1P(a)}}t 1A(a){a.2t();r(a.1B==9&&!h){g++;r(g>=f.w)g=0;$(1j).1z("1k");$(1j).1S("1k."+j,1A)}s b=g==1Q?k:f[g];H(I 1p b){r(I!="Q"&&I!="O"&&I!="1W"&&I!="1X"){s c=0;1C(I){z"Q":c=0;u;z"2u":c=2v;u;z"2w":c=38;u;z"2x":c=2y;u;z"2z":c=37;u;z"2A":c=39;u;z"2B":c=13;u;z"2C":c=32;u;z"2D":c=1Y;u;z"2E":c=2F;u;z"2G":c=17;u;z"2H":c=35;u;z"2I":c=27;u;z"2J":c=16;u;z"2K":c=18;u;z"2L":c=9;u;z"2M":c=33;u;z"2N":c=34;u;1x:u}r(a.1B==c){r(G f[g][I]=="t"){f[g][I]()}F{S(f[g][I])}}}}}5.1T=t(){r(G f[g][\'O\']=="N")v;H(1q 1p f[g][\'O\']){s a=1q;O.1a(a);s b=\'O.1h("\'+a+\'",t() {                \'+f[g]["O"][a]+\';\'+\'});\';S(b)}}5.1Z=t(){r(G g==\'N\')v;r(G f[g][\'O\']==\'N\'){}F{H(1q 1p f[g][\'O\']){s a=1q;O.1a(a);s b=\'O.1h("\'+a+\'",t(){});\';S(b)}}}5.1V=t(a){r(G a!=\'N\'){S(a)}F S(f[g][\'1W\'])}5.1U=t(){S(f[d][\'1X\'])}5.2O=t(){h=B}5.2P=t(){$(1j).1z("1k");5.1Z()}5.2Q=t(){v f[g][\'Q\']}5.2R=t(){v f[g]}}t 2S(a){s b=20 21();$.22(b.7,a)v b}t 21(){s o=[];s p=0;5.7={y:"23",C:"#2T",1b:"2U",D:"24",1l:{w:"w",J:"J"}}s q=5.7;5.2V=t(d,e){s f=[];$(d).2W(t(i){$(d).1m(i).V("Z","1D"+i);s a=(G q.1l.w==\'N\')?e.w:$(d).1m(i).25(q.1l.w);s b=(G q.1l.J==\'N\')?e.1E:$(d).1m(i).25(q.1l.J);s c={1F:"1D"+i,E:"1D"+i,1E:b,26:a,1G:{},U:B}f.Y(c)})28.2X(f);H(s i=0;i<f.w;i++){5.1h(f[i])}}5.1h=t(b,c){s d=5;b.1H$=t(){v $("#"+b.E)}b.2Y=t(a){$("#"+b.E).1r(a)}b.1H=t(a){v $("#"+b.E).1r()}b.1I=t(){v d.1I(b.E)}b.U=B;r(G c==\'N\'){b.1J=A}F{b.1J=c}o.Y(b);$("#"+b.E).1c(5.7.1b).K(5.7.D);r(b.1J!=A)$("#"+b.E).1r(c)}5.2Z=t(a){r(G a==\'1y\'){a=a.E}r($("#"+a).w==1&&$("#"+a).1c("."+5.7.D)){$(5.7.C+" ."+5.7.y).M(5.7.y);$("#"+a).1c("."+5.7.D).K(5.7.y);5.1d(a)}F{}}5.1d=t(a){s i=0;s b=A;H(i=0;i<o.w;i++){r(o[i][\'E\']==a){b=B;u}}r(b){p=i;2a(a,o[i][\'26\'],o[i][\'1E\'],2)}}5.36=t(){}5.2b=t(){r($(5.7.C+" ."+5.7.y).w==0){$("."+5.7.D).L().K(5.7.y);v}r($(5.7.C+" ."+5.7.y).1s("."+5.7.D).w==0)v;s a=$(5.7.C+" ."+5.7.y).1s("."+5.7.D).L();$(5.7.C+" ."+5.7.y).M(5.7.y);a.K(5.7.y);5.1d(a.14("P[J=1e], P[J=1f], 1g").V("Z"))}5.2c=t(){r($(5.7.C+" ."+5.7.y).w==0){$("."+5.7.D).L().K(5.7.y);v}r($(5.7.C+" ."+5.7.y).1t("."+5.7.D).L().w==0)v;s a=$(5.7.C+" ."+5.7.y).1t("."+5.7.D).L();$(5.7.C+" ."+5.7.y).M(5.7.y);a.K(5.7.y);5.1d(a.14("P[J=1e], P[J=1f], 1g").V("Z"))}5.L=t(){s a=$(5.7.C+" "+5.7.1b).L();$(5.7.C+" ."+5.7.y).M(5.7.y);a.K(5.7.y);5.1d(a.14("P[J=1e], P[J=1f], 1g").V("Z"))}5.3a=t(){s a=A;r($("."+5.7.y).14("P[J=1e], P[J=1f], 1g").L().V("Z")==o[o.w-1].1F)a=B;v a}5.3b=t(){s a=A;r($("."+5.7.y).14("P[J=1e], P[J=1f], 1g").L().V("Z")==o[0].1F)a=B;v a}5.3c=t(){5.1d($("."+5.7.y).14("P[J=1e], P[J=1f], 1g").L().V("Z"))}5.1a=t(a){s i=0;s b=A;H(i=0;i<o.w;i++){r(o[i][\'E\']==a.E){b=B;u}}r(b){$("#"+o[i][\'E\']).1c(5.7.1b).M(5.7.D)o.1O(i,1)}}5.3d=t(a){s i=0;s b=A;H(i=0;i<o.w;i++){r(o[i][\'E\']==a.E){b=B;u}}r(b){$("#"+o[i][\'E\']).1c(5.7.1b).M(5.7.D)o[i].U=A}}5.3e=t(a){s i=0;s b=A;H(i=0;i<o.w;i++){r(o[i][\'E\']==a.E){b=B;u}}r(b){$("#"+o[i][\'E\']).1c(5.7.1b).K(5.7.D)o[i].U=B}}5.2d=t(){v o[p]}5.3f=t(a){s b=A;H(s i=0;i<o.w;i++){r(o[i].E==a.E){b=o[i];u}}v b}5.3g=t(a,b){s c="2e[2f]."+a+" = "+b;S(c)}5.1H=t(a){s b="v 2e[2f]."+a;S(b)}5.1I=t(a){s b;r(o.w==0)v A;r(G a!=\'N\'){H(i=0;i<o.w;i++){r(o[i][\'E\']==a){14=B;b=o[i];u}}}F{b=o[p]}s c=$("#"+b.E).1r();s d=[];H(I 1p b[\'1G\']){s e=b[\'1G\'][I]s f;1C(I){z"3h":r(e===B){r(c.w>0){}F{d.Y(I)}}u;z"3i":r(c<=e[1]&&c>=e[0]){}F{d.Y(I)}u;z"3j":f=A;H(s x=0;x<e.w;x++){r(c==e[x]){f=B;u}}r(f){}F{d.Y(I)}u;z"3k":r(c.w==0){f=B;u}f=A;s g=c.3l(2,3)s h=c.3m(g);s j={\'3n\':31,\'3o\':31,\'3p\':30,\'3q\':31,\'3r\':30,\'3s\':31,\'3t\':31,\'3u\':30,\'10\':31,\'11\':30,\'12\':31}s k=1u(h[0],10);s l=1u(h[1],10);s m=1u(h[2],10);r(j[h[1]]!=3v){r(k<=j[h[1]]&&k!=0&&m>3w&&m<3x)f=B}F{r(l==2){s m=1u(h[2]);r(k>0&&k<29){f=B}F r(k==29){r((m%4==0)&&(m%3y!=0)||(m%3z==0)){f=B}}}}r(f==B){}F{d.Y(I)}u}}H(s i=0;i<d.w;i++){}s n=B;r(d.w>0)n=A;v n}}t 3A(a){s b=20 1K();$.22(b.7,a)$("#"+b.7.C+" "+b.7.D).K(b.7.19);$("#"+b.7.C+" "+b.7.D).2g().2h({"3B":t(){$("#"+b.7.C+" "+b.7.D).M(b.7.y);$(5).K(b.7.y)},"3C":t(){$(5).M(b.7.y)},"1n":t(){r(G b.7.1n==\'t\'){b.7.1n($(5))}F{S(b.7.1n)}}});v b}t 1K(){5.7={y:"23",C:"1K",D:"24",3D:"",3E:"",19:"3F",1n:""}5.2c=t(){s a=5.7.D;s b="#"+5.7.C;s c=5.7.y;r($(b+" "+a+"."+c).w==0){$(b+" "+a).L().K(c);v $(b+" ."+c)}r($(b+" ."+c).1t("."+5.7.19).w==0)v $(b+" ."+c);s d=$(b+" ."+c).1t("."+5.7.19).L();$(b+" ."+c).M(c);d.K(c);v d}5.2b=t(){s a=5.7.D;s b="#"+5.7.C;s c=5.7.y;r($(b+" "+a+"."+c).w==0){$(b+" "+a).L().K(c);v $(b+" ."+c)}r($(b+" ."+c).1s("."+5.7.19).w==0)v $(b+" ."+c);s d=$(b+" ."+c).1s("."+5.7.19).L();$(b+" ."+c).M(c);d.K(c);v d}5.3G=t(a){$("#"+5.7.C+" "+5.7.D).M(5.7.y);$("#"+5.7.C+" "+5.7.D).1m(a).K(5.7.y)}5.2d=t(){s a=5.7.y;s b="#"+5.7.C;v $(b+" ."+a).w==1?$(b+" ."+a):0}5.3H=t(){$("#"+5.7.C+" "+5.7.D).M(5.7.y)}5.1a=t(a){s b=5.7.D;s c="#"+5.7.C;$(c+" "+b).1m(a).M(5.7.19)}5.3I=t(){s a=5.7.y;s b="#"+5.7.C;v $(b+" ."+a).1a()}5.L=t(){}}t 3J(a){}t 3K(a){v(G(3L[a])=="N")?A:B}t 2a(b,c,d,e,f){r(f==B)3M=B;r(e==1){$("#"+b).1v()}F r(e==2){$("#"+b).1v()}F r(e==0){$("#"+b).1v()}$("#"+b).V("3N",c)$("3O").M("U");$("#"+b).3P().K("U");$("#"+b).1v();$("#"+b).2g("2i.2j").2h("2i.2j",t(a){s k=a.1B;28.3Q(k);1C(d){z 0:r(!((k==32)||(k==13)||((k>=1L)&&(k<=1M))||((k>=W)&&(k<=X))||((k>=T)&&(k<=1w))||k==3R||R(k))){v A}u;z 6:z 1:r(!((k==13)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 2:r(!((k==32)||(k==13)||((k>=1L)&&(k<=1M))||((k>=T)&&(k<=1w))||R(k))){v A}u;z 30:r(!((k==13)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1w)))||R(k))){v A}u;z 31:r(!((k==13)||(k==3S)||(k==32)||((k>=1L)&&(k<=1M))||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 29:r(!((k==13)||(k==3T)||(k==2k)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 32:r(!((k==13)||(k==2k)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 4:r(!((k==13)||(k==27)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 15:r(!((k==13)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1w)))||(k<=12)||R(k))){v A}u;1x:}v B})}t R(k){s a=A;r(k==8||k==1Y||k==37||k==39||k==27)a=B;v a}',62,242,'|||||this||_op||||||||||||||||||||if|var|function|break|return|length||onfocus_class|case|false|true|content_id|items|nombre|else|typeof|for|atributo|type|addClass|first|removeClass|undefined|shortcut|input|pan|special_char|eval|96|active|attr|48|57|push|id|||||find|||||classinclude|remove|content_element|closest|_seleccionar_input|text|password|textarea|add|panel|document|keyup|params|eq|click|105|in|sc|val|prevAll|nextAll|parseInt|focus|122|default|object|unbind|keyup_bind|keyCode|switch|inp_|tipo|Nombre|rules|get|validate|def|lista|65|90|Math|splice|set_tab|1000|get_tab|bind|bind_shortcut|bind_exit|bind_init|init|exit|46|unbind_shortcut|new|formtab|extend|over|inp_form|data|max_length||console||set_next|prev|next|get_current|_inputs_val|_selected_input|off|on|keydown|us|190|tabmanager|floor|random|1000001|wks_|get_prev_panel|set_inactive|set_active|preventDefault|divide|111|up|down|40|left|right|intro|space|del|insert|45|ctrl|end|esc|shift|alt|tab|repag|avpag|not_tab|reset|get_active_tab|get_active_tab_object|set_formtab|form_content|li|setFormList|each|dir|put|seleccionar|||||||_focus_input||||is_last|is_first|current|disabled|enabled|get_object|set|required|range|inside|date|substring|split|01|03|04|05|06|07|08|09|null|1975|2050|100|400|set_lista|mouseover|mouseout|callbefore|callafter|inc|set_current|unselect|remove_current|validar|isDefined|window|_b_init_teclado_numerico|maxlength|div|parent|log|188|107|110'.split('|'),0,{}))

//eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('t 2l(){s d=0;s e;s f=[];s g;s h=A;s j;s k={};5.1h=t(a,b){s c=1N.2m(1N.2n()*2o);j="2p"+c;r(G a.O!=\'N\'){}F{a.O={}}a.U=B;f.Y(a);r(b==B){k=a}F{r(f.w==1){k=f[0]}}g=0};5.1a=t(a){H(s i=0;i<f.w;i++){r(a==f[i]["Q"]){g=i;u}}5.1i.1O(g,1);g=0};5.1P=t(a,b){d=g;r(G a==\'N\'||a==\'1x\'){g=1Q}F{H(s i=0;i<f.w;i++){r(G(a)=="1y"){r(a.Q==f[i]["Q"]){g=i}}F{r(a==f[i]["Q"]){g=i}}}}5.1R(b)};5.2q=t(){v f[d]};5.1R=t(a){$(1j).1z("1k");$(1j).1S("1k."+j,1A);5.1T();5.1U();r(G a!==\'N\'){}5.1V(a)};5.2r=t(a){H(s i=0;i<5.1i.w;i++){r(a==5.1i[i]["Q"]){5.1i[i]["U"]=A;g=i}}};5.2s=t(a,b){H(s i=0;i<5.1i.w;i++){r(G(a)=="1y"){r(a.Q==f[i]["Q"]){g=i}}F{r(a==f[i]["Q"]){g=i}}}r(G b==\'N\'||b==A){}F{1P(a)}};t 1A(a){a.2t();r(a.1B==9&&!h){g++;r(g>=f.w)g=0;$(1j).1z("1k");$(1j).1S("1k."+j,1A)}s b=g==1Q?k:f[g];H(I 1p b){r(I!="Q"&&I!="O"&&I!="1W"&&I!="1X"){s c=0;1C(I){z"Q":c=0;u;z"2u":c=2v;u;z"2w":c=38;u;z"2x":c=2y;u;z"2z":c=37;u;z"2A":c=39;u;z"2B":c=13;u;z"2C":c=32;u;z"2D":c=1Y;u;z"2E":c=2F;u;z"2G":c=17;u;z"2H":c=35;u;z"2I":c=27;u;z"2J":c=16;u;z"2K":c=18;u;z"2L":c=9;u;z"2M":c=33;u;z"2N":c=34;u;1x:u}r(a.1B==c){r(G f[g][I]=="t"){f[g][I]()}F{S(f[g][I])}}}}};5.1T=t(){r(G f[g][\'O\']=="N")v;H(1q 1p f[g][\'O\']){s a=1q;O.1a(a);s b=\'O.1h("\'+a+\'",t() {                \'+f[g]["O"][a]+\';\'+\'});\';S(b)}};5.1Z=t(){r(G g==\'N\')v;r(G f[g][\'O\']==\'N\'){}F{H(1q 1p f[g][\'O\']){s a=1q;O.1a(a);s b=\'O.1h("\'+a+\'",t(){});\';S(b)}}};5.1V=t(a){r(G a!=\'N\'){S(a)}F S(f[g][\'1W\'])};5.1U=t(){S(f[d][\'1X\'])};5.2O=t(){h=B};5.2P=t(){$(1j).1z("1k");5.1Z()};5.2Q=t(){v f[g][\'Q\']};5.2R=t(){v f[g]}}t 2S(a){s b=20 21();$.22(b.7,a);v b}t 21(){s o=[];s p=0;5.7={y:"23",C:"#2T",1b:"2U",D:"24",1l:{w:"w",J:"J"}};s q=5.7;5.2V=t(d,e){s f=[];$(d).2W(t(i){$(d).1m(i).V("Z","1D"+i);s a=(G q.1l.w==\'N\')?e.w:$(d).1m(i).25(q.1l.w);s b=(G q.1l.J==\'N\')?e.1E:$(d).1m(i).25(q.1l.J);s c={1F:"1D"+i,E:"1D"+i,1E:b,26:a,1G:{},U:B};f.Y(c)});28.2X(f);H(s i=0;i<f.w;i++){5.1h(f[i])}};5.1h=t(b,c){s d=5;b.1H$=t(){v $("#"+b.E)};b.2Y=t(a){$("#"+b.E).1r(a)};b.1H=t(a){v $("#"+b.E).1r()};b.1I=t(){v d.1I(b.E)};b.U=B;r(G c==\'N\'){b.1J=A}F{b.1J=c}o.Y(b);$("#"+b.E).1c(5.7.1b).K(5.7.D);r(b.1J!=A)$("#"+b.E).1r(c)};5.2Z=t(a){r(G a==\'1y\'){a=a.E}r($("#"+a).w==1&&$("#"+a).1c("."+5.7.D)){$(5.7.C+" ."+5.7.y).M(5.7.y);$("#"+a).1c("."+5.7.D).K(5.7.y);5.1d(a)}F{}};5.1d=t(a){s i=0;s b=A;H(i=0;i<o.w;i++){r(o[i][\'E\']==a){b=B;u}}r(b){p=i;2a(a,o[i][\'26\'],o[i][\'1E\'],2)}};5.36=t(){};5.2b=t(){r($(5.7.C+" ."+5.7.y).w==0){$("."+5.7.D).L().K(5.7.y);v}r($(5.7.C+" ."+5.7.y).1s("."+5.7.D).w==0)v;s a=$(5.7.C+" ."+5.7.y).1s("."+5.7.D).L();$(5.7.C+" ."+5.7.y).M(5.7.y);a.K(5.7.y);5.1d(a.14("P[J=1e], P[J=1f], 1g").V("Z"))};5.2c=t(){r($(5.7.C+" ."+5.7.y).w==0){$("."+5.7.D).L().K(5.7.y);v}r($(5.7.C+" ."+5.7.y).1t("."+5.7.D).L().w==0)v;s a=$(5.7.C+" ."+5.7.y).1t("."+5.7.D).L();$(5.7.C+" ."+5.7.y).M(5.7.y);a.K(5.7.y);5.1d(a.14("P[J=1e], P[J=1f], 1g").V("Z"))};5.L=t(){s a=$(5.7.C+" "+5.7.1b).L();$(5.7.C+" ."+5.7.y).M(5.7.y);a.K(5.7.y);5.1d(a.14("P[J=1e], P[J=1f], 1g").V("Z"))};5.3a=t(){s a=A;r($("."+5.7.y).14("P[J=1e], P[J=1f], 1g").L().V("Z")==o[o.w-1].1F)a=B;v a};5.3b=t(){s a=A;r($("."+5.7.y).14("P[J=1e], P[J=1f], 1g").L().V("Z")==o[0].1F)a=B;v a};5.3c=t(){5.1d($("."+5.7.y).14("P[J=1e], P[J=1f], 1g").L().V("Z"))};5.1a=t(a){s i=0;s b=A;H(i=0;i<o.w;i++){r(o[i][\'E\']==a.E){b=B;u}}r(b){$("#"+o[i][\'E\']).1c(5.7.1b).M(5.7.D);o.1O(i,1)}};5.3d=t(a){s i=0;s b=A;H(i=0;i<o.w;i++){r(o[i][\'E\']==a.E){b=B;u}}r(b){$("#"+o[i][\'E\']).1c(5.7.1b).M(5.7.D);o[i].U=A}};5.3e=t(a){s i=0;s b=A;H(i=0;i<o.w;i++){r(o[i][\'E\']==a.E){b=B;u}}r(b){$("#"+o[i][\'E\']).1c(5.7.1b).K(5.7.D);o[i].U=B}};5.2d=t(){v o[p]};5.3f=t(a){s b=A;H(s i=0;i<o.w;i++){r(o[i].E==a.E){b=o[i];u}}v b};5.3g=t(a,b){s c="2e[2f]."+a+" = "+b;S(c)};5.1H=t(a){s b="v 2e[2f]."+a;S(b)};5.1I=t(a){s b;r(o.w==0)v A;r(G a!=\'N\'){H(i=0;i<o.w;i++){r(o[i][\'E\']==a){14=B;b=o[i];u}}}F{b=o[p]}s c=$("#"+b.E).1r();s d=[];H(I 1p b[\'1G\']){s e=b[\'1G\'][I];s f;1C(I){z"3h":r(e===B){r(c.w>0){}F{d.Y(I)}}u;z"3i":r(c<=e[1]&&c>=e[0]){}F{d.Y(I)}u;z"3j":f=A;H(s x=0;x<e.w;x++){r(c==e[x]){f=B;u}}r(f){}F{d.Y(I)}u;z"3k":r(c.w==0){f=B;u}f=A;s g=c.3l(2,3);s h=c.3m(g);s j={\'3n\':31,\'3o\':31,\'3p\':30,\'3q\':31,\'3r\':30,\'3s\':31,\'3t\':31,\'3u\':30,\'10\':31,\'11\':30,\'12\':31};s k=1u(h[0],10);s l=1u(h[1],10);s m=1u(h[2],10);r(j[h[1]]!=3v){r(k<=j[h[1]]&&k!=0&&m>3w&&m<3x)f=B}F{r(l==2){s m=1u(h[2]);r(k>0&&k<29){f=B}F r(k==29){r((m%4==0)&&(m%3y!=0)||(m%3z==0)){f=B}}}}r(f==B){}F{d.Y(I)}u}}H(s i=0;i<d.w;i++){}s n=B;r(d.w>0)n=A;v n}}t 3A(a){s b=20 1K();$.22(b.7,a);$("#"+b.7.C+" "+b.7.D).K(b.7.19);$("#"+b.7.C+" "+b.7.D).2g().2h({"3B":t(){$("#"+b.7.C+" "+b.7.D).M(b.7.y);$(5).K(b.7.y)},"3C":t(){$(5).M(b.7.y)},"1n":t(){r(G b.7.1n==\'t\'){b.7.1n($(5))}F{S(b.7.1n)}}});v b}t 1K(){5.7={y:"23",C:"1K",D:"24",3D:"",3E:"",19:"3F",1n:""};5.2c=t(){s a=5.7.D;s b="#"+5.7.C;s c=5.7.y;r($(b+" "+a+"."+c).w==0){$(b+" "+a).L().K(c);v $(b+" ."+c)}r($(b+" ."+c).1t("."+5.7.19).w==0)v $(b+" ."+c);s d=$(b+" ."+c).1t("."+5.7.19).L();$(b+" ."+c).M(c);d.K(c);v d};5.2b=t(){s a=5.7.D;s b="#"+5.7.C;s c=5.7.y;r($(b+" "+a+"."+c).w==0){$(b+" "+a).L().K(c);v $(b+" ."+c)}r($(b+" ."+c).1s("."+5.7.19).w==0)v $(b+" ."+c);s d=$(b+" ."+c).1s("."+5.7.19).L();$(b+" ."+c).M(c);d.K(c);v d};5.3G=t(a){$("#"+5.7.C+" "+5.7.D).M(5.7.y);$("#"+5.7.C+" "+5.7.D).1m(a).K(5.7.y)};5.2d=t(){s a=5.7.y;s b="#"+5.7.C;v $(b+" ."+a).w==1?$(b+" ."+a):0};5.3H=t(){$("#"+5.7.C+" "+5.7.D).M(5.7.y)};5.1a=t(a){s b=5.7.D;s c="#"+5.7.C;$(c+" "+b).1m(a).M(5.7.19)};5.3I=t(){s a=5.7.y;s b="#"+5.7.C;v $(b+" ."+a).1a()};5.L=t(){}}t 3J(a){}t 3K(a){v(G(3L[a])=="N")?A:B}t 2a(b,c,d,e,f){r(f==B)3M=B;r(e==1){$("#"+b).1v()}F r(e==2){$("#"+b).1v()}F r(e==0){$("#"+b).1v()}$("#"+b).V("3N",c);$("3O").M("U");$("#"+b).3P().K("U");$("#"+b).1v();$("#"+b).2g("2i.2j").2h("2i.2j",t(a){s k=a.1B;28.3Q(k);1C(d){z 0:r(!((k==32)||(k==13)||((k>=1L)&&(k<=1M))||((k>=W)&&(k<=X))||((k>=T)&&(k<=1w))||k==3R||R(k))){v A}u;z 6:z 1:r(!((k==13)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 2:r(!((k==32)||(k==13)||((k>=1L)&&(k<=1M))||((k>=T)&&(k<=1w))||R(k))){v A}u;z 30:r(!((k==13)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1w)))||R(k))){v A}u;z 31:r(!((k==13)||(k==3S)||(k==32)||((k>=1L)&&(k<=1M))||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 29:r(!((k==13)||(k==3T)||(k==2k)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 32:r(!((k==13)||(k==2k)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 4:r(!((k==13)||(k==27)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1o)))||R(k))){v A}u;z 15:r(!((k==13)||((k>=W)&&(k<=X)||((k>=T)&&(k<=1w)))||(k<=12)||R(k))){v A}u;1x:}v B})}t R(k){s a=A;r(k==8||k==1Y||k==37||k==39||k==27)a=B;v a}',62,242,'|||||this||_op||||||||||||||||||||if|var|function|break|return|length||onfocus_class|case|false|true|content_id|items|nombre|else|typeof|for|atributo|type|addClass|first|removeClass|undefined|shortcut|input|pan|special_char|eval|96|active|attr|48|57|push|id|||||find|||||classinclude|remove|content_element|closest|_seleccionar_input|text|password|textarea|add|panel|document|keyup|params|eq|click|105|in|sc|val|prevAll|nextAll|parseInt|focus|122|default|object|unbind|keyup_bind|keyCode|switch|inp_|tipo|Nombre|rules|get|validate|def|lista|65|90|Math|splice|set_tab|1000|get_tab|bind|bind_shortcut|bind_exit|bind_init|init|exit|46|unbind_shortcut|new|formtab|extend|over|inp_form|data|max_length||console||set_next|prev|next|get_current|_inputs_val|_selected_input|off|on|keydown|us|190|tabmanager|floor|random|1000001|wks_|get_prev_panel|set_inactive|set_active|preventDefault|divide|111|up|down|40|left|right|intro|space|del|insert|45|ctrl|end|esc|shift|alt|tab|repag|avpag|not_tab|reset|get_active_tab|get_active_tab_object|set_formtab|form_content|li|setFormList|each|dir|put|seleccionar|||||||_focus_input||||is_last|is_first|current|disabled|enabled|get_object|set|required|range|inside|date|substring|split|01|03|04|05|06|07|08|09|null|1975|2050|100|400|set_lista|mouseover|mouseout|callbefore|callafter|inc|set_current|unselect|remove_current|validar|isDefined|window|_b_init_teclado_numerico|maxlength|div|parent|log|188|107|110'.split('|'),0,{}))