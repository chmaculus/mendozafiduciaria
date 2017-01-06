var list_object = [];
var _this_app = {};
var __list_object = [];

var class_generic = function() {
        this._fdata = "";
        this._finit = "";
        this._fcancel = "";
        this._ffinish = "";
};

class_generic.prototype.setData = function(param) {
        this._fdata = param;
};

class_generic.prototype.onInit = function(func) {
        this._finit = func;
};

class_generic.prototype.onFinish = function(func) {
        this._ffinish = func;
};

class_generic.prototype.onCancel = function(func) {
        this._fcancel = func;
};

class_generic.prototype.cancel = function(data) {
        if (typeof this._fcancel === 'function') {
                this._fcancel(data);
                list_object.pop();
                __list_object.pop();
                var tmp = "_" + list_object[list_object.length - 1].mod_name;
                eval(tmp + " = __list_object[__list_object.length-1];");
                eval("_this_app = " + tmp + ";");
        }
};

class_generic.prototype.finish = function(data, data2, data3) {
        if (typeof this._ffinish === 'function') {
                this._ffinish(data, data2, data3);

                list_object.pop();
                __list_object.pop();
                var tmp = "_" + list_object[list_object.length - 1].mod_name;
                eval(tmp + " = __list_object[__list_object.length-1];");
                eval("_this_app = " + tmp + ";");
        }
};


class_generic.prototype.init = function(data) {
        if (typeof this._finit === 'function') {
                this._finit(data);
        }
};
class_generic.prototype.start = function(data) {
};

class_generic.prototype.restart = function() {
        for (var i = list_object.length - 1; i >= 0; i--) {
                if (this.params.mod === list_object[i]['mod']) {

                        load_app(list_object[i].mod, list_object[i].container, list_object[i].params, list_object[i].finit, list_object[i].ffinish, list_object[i].fcancel, list_object[i].js_extra, 1);
                        i = -1;
                        break;
                }
        }
};

class_generic.prototype.copy = function() {
        function F() {
        }
        F.prototype = this;
        return new F();
};

var generic = new class_generic;

//load local app: 
//mod : carpeta app, 
//containter : selector jquery del contenedor
//params : variables POST que se pasaran al inicio del modulo.
//callback : funcion de retorno,
//cancel : funcion de cancel
function load_app(mod, container, params, finit, ffinish, fcancel, js_extra, restart) {
        var obj = false;
        $.ajax({
                url: _url_sitio + mod + "/x_init_mod",
                data: {
                        params: params,
                        modulo: mod
                },
                type: "post",
                async: true,
                success: function(rtn) {


                        obj = start_object(mod, container, params, finit, ffinish, fcancel, js_extra, restart);
                        $(container).html(rtn);

                        obj.start();
                }
        });
        return obj;
}



function start_object(mod, container, params, finit, ffinish, fcancel, js_extra, restart) {

        var segmento_mod = mod.split("/");
        var mod_name = segmento_mod[segmento_mod.length - 1];

        if (typeof finit === 'undefined') {
                finit = "_" + mod_name + ".init(param)";
        }
        if (typeof ffinish === 'undefined') {
                ffinish = "_" + mod_name + ".finish(param)";
        }
        if (typeof fcancel === 'undefined') {
                fcancel = "_" + mod_name + ".cancel(param)";
        }

        //
        var generic = new class_generic;
        eval('_' + mod_name + " = generic.copy();");

        generic.params = {
                mod: mod,
                mod_name: mod_name,
                container: container,
                params: params,
                finit: finit,
                ffinish: ffinish,
                fcancel: fcancel,
                js_extra: js_extra
        };


        generic.onInit(finit);
        generic.onFinish(ffinish);
        generic.onCancel(fcancel);

        generic._mod = container;
        generic.container = container;
        if (typeof js_extra !== 'undefined')
                generic.extras = js_extra;

        eval('_' + mod_name + '.URL = mod;');
        eval('_' + mod_name + '.container = container;');
        if (restart !== 1) {
                list_object.push(generic.params);

                eval('__list_object.push(_' + mod_name + ');');
        }

        eval('_this_app = _' + mod_name + ".copy();");
        eval("_" + mod_name + ".init();");



        var generic = new class_generic;

        return _this_app;
}

function stopBubble(e) {
        if (e) {
                e.stopPropagation();
        }
        else {
                window.event.cancelBubble = true;
        }
}



function obtener_lista(bind, obj, domelement, parent, click) {

        var $domelement = $(domelement).html("");
        var $element_list = $domelement.first().parent().clone();

        $domelement.first().parent().nextAll().remove();

        var $parent;
        if (typeof parent === 'undefined' || parent === false) {
                $parent = $(domelement).parent().first().parent();
        } else {
                $parent = $(parent);
        }

        $parent.find($element_list).first().remove();
        $(domelement).remove();

        $parent.html("");
        for (var i = 0; i < obj.length; i++) {

                for (var sc in bind) {
                        $element_list.find(sc).html(obj[i][bind[sc]]);
                }
                $parent.append($element_list.clone());
        }

        if (typeof click === 'undefined')
                click = "";

        var list = set_lista({content_id: $parent.parent().attr("id"), items: "li", click: click});
        return list;
}




function set_next(nombre_componente, max_length, tipo, forma, init) {

        if (init === true)
                _b_init_teclado_numerico = true;


        $("#" + nombre_componente).focus();
        $("#" + nombre_componente).attr("maxlength", max_length)
        $("div").removeClass("active");

        $("#" + nombre_componente).parent().addClass("active");
        $("#" + nombre_componente).focus();
        $("#" + nombre_componente).off("keydown.us").on("keydown.us", function(event) {
                var k = event.keyCode;
                switch (tipo) {

                        case 0://ALFANUMERICO   
                                if (!((k == 32) || (k == 13) || ((k >= 65) && (k <= 90)) || ((k >= 48) && (k <= 57)) || ((k >= 96) && (k <= 122)) || k == 188 || special_char(k))) {
                                        return false
                                }
                                break;
                        case 6://NUMERICO   
                        case 1://NUMERICO   `
                                if (!((k == 13) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 105))) || special_char(k))) {
                                        return false
                                }
                                break;
                        case 2://ALFABETICO     
                                if (!((k == 32) || (k == 13) || ((k >= 65) && (k <= 90)) || ((k >= 96) && (k <= 122)) || special_char(k))) {
                                        return false
                                }
                                break;
                        case 30://VERIFICAR COMPATIBILIDAD CON ANALET(Q=3 -> Q=30) facturador-normal->392
                                if (!((k == 13) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 122))) || special_char(k))) {
                                        return false
                                }
                                break;
                        case 31://VERIFICAR COMPATIBILIDAD CON ANALET(Q=3 -> Q=30) facturador-normal->392
                                if (!((k == 13) || (k == 107) || (k == 32) || ((k >= 65) && (k <= 90)) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 105))) || special_char(k))) {
                                        return false
                                }
                                break;

                        case 29://RECUENTO, SOLO NUMEROS Y COMAS
                                if (!((k == 13) || (k == 110) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 105))) || special_char(k))) {
                                        return false
                                }
                                break;
                        case 4://SOLO NUMEROS
                                if (!((k == 13) || (k == 27) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 105))) || special_char(k))) {
                                        return false
                                }
                                break;
                        case 15://NUMERICO CON GUION MEDIO
                                if (!((k == 13) || ((k >= 48) && (k <= 57) || ((k >= 96) && (k <= 122))) || (k <= 12) || special_char(k))) {
                                        return false
                                }
                                break;
                        default:
                }

                return true;

        });
}

//  backspace: 8,  delete: 46,  flechas : 37 39, escape: 27
function special_char(k) {
        var rtn = false;
        if (k == 8 || k == 46 || k == 37 || k == 39 || k == 27)
                rtn = true;

        return rtn;
}

function isInt(f) {
        return Math.round(f) == f && f.length > 0;
}
