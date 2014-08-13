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
                        eval(panel[_panel][atributo]);
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
        items : "inp_form"
    }
    
    //Agrega campos segun un selector con un nombre generico
    this.setFormList = function(sel, val){
        
        var tmp = [];
        $(sel).each(function(i){
            $(sel).eq(i).attr("id","inp_"+i);
            var obj = {
                nombre :"inp_"+i,
                tipo : val.tipo,
                max_length : val.max_length,                
                rules : {},
                active: true
            }
            tmp.push(obj);
            
        })

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
            //console.log(val.nombre);
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
        this._seleccionar_input($prev.find("input[type=text], input[type=password]").attr("id"));
    }

    this.next = function(){
        if ($(this._op.content_id+" ."+this._op.onfocus_class).length == 0) {$("."+this._op.items).first().addClass(this._op.onfocus_class);return;}
        if($(this._op.content_id+" ."+this._op.onfocus_class).nextAll("."+this._op.items).first().length == 0) return;
        var $next = $(this._op.content_id+" ."+this._op.onfocus_class).nextAll("."+this._op.items).first();
        $(this._op.content_id+" ."+this._op.onfocus_class).removeClass(this._op.onfocus_class);
        $next.addClass(this._op.onfocus_class);
        this._seleccionar_input($next.find("input[type=text], input[type=password]").attr("id"));
    }    
    
    this.first = function(){
        var $first = $(this._op.content_id+" "+this._op.content_element).first();
        
        $(this._op.content_id+" ."+this._op.onfocus_class).removeClass(this._op.onfocus_class);
        $first.addClass(this._op.onfocus_class);
        
        this._seleccionar_input($first.find("input[type=text], input[type=password]").attr("id"));
    }
    
    this.is_last = function(){
        var rtn = false ;

        if ( $("."+this._op.onfocus_class).find("input[type=text], input[type=password]").first().attr("id") ==   _inputs_val[_inputs_val.length-1].Nombre)
            rtn = true;
        return rtn;            
    }
    
    this.current = function(){
        this._seleccionar_input($("."+this._op.onfocus_class).find("input[type=text], input[type=password]").first().attr("id"));
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
/*Listados*/

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
                eval(iLista._op.click);
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
    
    this.get_current = function(){
        var $class = this._op.onfocus_class;
        var $cont = "#"+this._op.content_id;
        return $($cont + " ."+$class).length == 1 ? $($cont + " ."+$class) : 0;
    }
    
    this.remove = function(nro){
        var $li = this._op.items;
        var $cont = "#"+this._op.content_id;
        $($cont+" "+$li).eq(nro).removeClass(this._op.classinclude);
    }
    
    this.first = function(){
        
    }
    
}


function validar(id){
    
}

function isDefined(variable) {
    return (typeof(window[variable]) == "undefined")?  false: true;
}