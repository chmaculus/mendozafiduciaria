var mydata;
var id_edit;
var working = false;
var _array_entidades = {};
var _array_chk = {};
var semmilla;

$(document).ready(function(){
    semmilla = fGetNumUnico();
    mydata = '';
    
    $(".toolbar li").hover(
        function(){
            $(this).removeClass('li_sel').addClass('li_sel');
        },
        function(){
            $(this).removeClass('li_sel');
        }
    );
        
    $(".toolbar li").click(function(e){
            e.preventDefault();
            var top = $(this).data('top');
            var obj = [];
            $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
            
            if(top =='add_new'){
                
                if (_permiso_alta==0){

                    jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                // add
                $.ajax({
                    url : _permisos.URL + "/x_getform_addentidad_new",
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        
                        $(".chzn-select").chosen(); 
                        $("#btnBorrar").hide();
                        
                        $(".CSSTableGenerator input").click(function(e){
                            stopBubble(e);
                        });
                        
                        $(".CSSTableGenerator table tr td").first().click(function(e){
                           stopBubble(e);
                           
                           if ($(this).parent().parent().find('td input').first().is(":checked")){
                               $(this).parent().parent().find('td input').removeAttr("checked");
                           }else{
                               $(this).parent().parent().find('td input').attr("checked","checked");
                           }
                           
                        });
                        
                        $(".CSSTableGenerator table tr td").click(function(e){
                            var ind_tmp = $(this).index();
                            if (ind_tmp==0){
                                //seleccionar todos
                                
                                if ( $(this).parent().find('input').first().is(":checked")){
                                    $(this).parent().find('input').removeAttr("checked");
                                }else{
                                    $(this).parent().find('input').attr("checked","checked");
                                }
                                return false;
                            }
                            if ($(this).find('input').is(":checked")){
                                $(this).find('input').removeAttr("checked");
                            }else{
                                $(this).find('input').attr("checked","checked");
                            }
                        });
                        
                        
                        $(".CSSTableGenerator table tr").first().find('td').click(function(e){
                            var indextr = $(this).index();
                           $(".CSSTableGenerator table tr:gt(0)").each(function( index1 ) {
                               if ($(this).find('td').eq(indextr).find('input').first().is(":checked")){
                                   $(this).find('td').eq(indextr).find('input').removeAttr("checked");
                               }else{
                                   $(this).find('td').eq(indextr).find('input').attr("checked","checked");
                               }
                           });
                        });
                        
                            
                        $('#send').on('click', function(event) {
                            event.preventDefault();
                            
                            var id_rol = $("#id_rol").val();
                            //var id_permiso = $("#id_permiso").val();
                            
                            if (id_rol<=0){
                                jAlert('Elija un Rol.', $.ucwords(_etiqueta_modulo),function(){
                                    
                                });
                                return false;
                            }
                            
                            
                            //recorrer matriz
                            var obj_fin =[];
                            $(".CSSTableGenerator table tr:gt(0)").each(function(index){
                                //console.log( index + ": " + $( this ).text() );
                                
                                var id_permiso = $(this).data("idp");
                                var_ins = {
                                    "ID_ROL":id_rol,
                                    "ID_PERMISO":id_permiso,
                                    "MOSTRAR":"0",
                                    "ALTA":"0",
                                    "BAJA":"0",
                                    "MODIFICACION":"0",
                                    "VER":"0",
                                    "EXPORTAR":"0",
                                    "OTROS":"0"
                                }
                                    
                                $(this).find('td input').each(function( index1 ) {
                                    if ($(this).is(":checked")){
                                        //console.log( 'permiso: ' + id_permiso + ' - ' + index1 + ' index1: si');
                                        if (index1==0){
                                            var_ins['MOSTRAR'] = 1;
                                        }else if (index1==1){
                                            var_ins['ALTA'] = 1;
                                        }else if (index1==2){
                                            var_ins['BAJA'] = 1;
                                        }else if (index1==3){
                                            var_ins['MODIFICACION'] = 1;
                                        }else if (index1==4){
                                            var_ins['VER'] = 1;
                                        }else if (index1==5){
                                            var_ins['EXPORTAR'] = 1;
                                        }else if (index1==6){
                                            var_ins['OTROS'] = 1;
                                        }
                                    }else{
                                        //console.log( 'permiso: ' + id_permiso + ' - ' + index1 + ' index1: no');
                                        if (index1==0){
                                            var_ins['MOSTRAR'] = 0;
                                        }else if (index1==1){
                                            var_ins['ALTA'] = 0;
                                        }else if (index1==2){
                                            var_ins['BAJA'] = 0;
                                        }else if (index1==3){
                                            var_ins['MODIFICACION'] = 0;
                                        }else if (index1==4){
                                            var_ins['VER'] = 0;
                                        }else if (index1==4){
                                            var_ins['EXPORTAR'] = 0;
                                        }else if (index1==5){
                                            var_ins['OTROS'] = 0;
                                        }
                                    }
                                });
                                obj_fin.push(var_ins);
                                
                            });
                            
                            var id = $("#idh").val();

                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                            
                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                //var_ins:var_ins,
                                obj_fin:obj_fin
                            }

                            $.ajax({
                                url : _permisos.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    
                                    if(data.result==-1){
                                        jAlert('Este Permiso ya existe para este Rol. No se puede agregar.', $.ucwords(_etiqueta_modulo),function(){
                                            
                                        });
                                    }else if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $('#btnClear').trigger('click');
                                            $("#jqxgrid").show();
                                            $("#wpopup").html('');
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                        });
                                    }else{
                                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                        });
                                    }
                                    
                                }
                            });

                        });
                        
                        $('#btnClear').on('click', function(event) {
                            $("#customForm").validationEngine('hideAll');
                            event.preventDefault();
                            $("#frmagregar :text").val("");
                            $("#frmagregar :text").first().focus();
                            $("#idh").val("");
                            $("#id_rol").val(0).trigger("chosen:updated");
                            $("#id_permiso").val(0).trigger("chosen:updated");
                            
                            if($("#label_action").html()=='Editar'){
                              $("#label_action").html("Agregar");
                            }
                            $("#frmagregar :text").removeClass('error');
                            $("#nom").select();
                            
                        });
                        
                        $('#id_rol,#id_permiso').change(function() {
                            $(this).validationEngine('validate');
                        });
                    }
                });
                
            
            }else if(top =='add'){
                if (_permiso_alta==0){

                    jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                // add
                $.ajax({
                    url : _permisos.URL + "/x_getform_addentidad",
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        
                        $(".chzn-select").chosen(); 
                        $("#btnBorrar").hide();
                                              
                        
                                                
                        $('#send').on('click', function(event) {
                            event.preventDefault();
                            
                            var id_rol = $("#id_rol").val();
                            var id_permiso = $("#id_permiso").val();
                            var_ins = {
                                "ID_ROL":id_rol,
                                "ID_PERMISO":id_permiso,
                                "MOSTRAR":"0",
                                "ALTA":"0",
                                "BAJA":"0",
                                "MODIFICACION":"0",
                                "VER":"0",
                                "EXPORTAR":"0",
                                "OTROS":"0"
                            }
                            //checklist
                            var items = $("#listbox").jqxListBox('getCheckedItems');
                            var checkedItems = [];
                            $.each(items, function (index, value) {
                                if (value.title.length>0)
                                    checkedItems.push(value.title);
                            });
                            $.each(checkedItems, function (index, value) {
                                $.each(var_ins, function (index1, value1) {
                                    if(value==index1){
                                        var_ins[index1] = '1';
                                        return false;
                                    }
                                });
                            });
                            
                            var id = $("#idh").val();

                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                            
                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                var_ins:var_ins
                            }

                            $.ajax({
                                url : _permisos.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    
                                    if(data.result==-1){
                                        jAlert('Este Permiso ya existe para este Rol. No se puede agregar.', $.ucwords(_etiqueta_modulo),function(){
                                            
                                        });
                                    }else if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $('#btnClear').trigger('click');
                                            $("#jqxgrid").show();
                                            $("#wpopup").html('');
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                        });
                                    }else{
                                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                        });
                                    }
                                    
                                }
                            });

                        });
                        
                        $('#btnClear').on('click', function(event) {
                            $("#customForm").validationEngine('hideAll');
                            event.preventDefault();
                            $("#frmagregar :text").val("");
                            $("#frmagregar :text").first().focus();
                            $("#idh").val("");
                            $("#id_rol").val(0).trigger("chosen:updated");
                            $("#id_permiso").val(0).trigger("chosen:updated");
                            
                            if($("#label_action").html()=='Editar'){
                              $("#label_action").html("Agregar");
                            }
                            $("#frmagregar :text").removeClass('error');
                            $("#nom").select();
                            
                        });
                        
                        var source_acc = [
                            { html: "<div><div>Mostrar</div></div>", title:"MOSTRAR", group: "Acciones" },
                            { html: "<div><div>Alta</div></div>", title:"ALTA", group: "Acciones" },
                            { html: "<div><div>Baja</div></div>", title:"BAJA", group: "Acciones" },
                            { html: "<div><div>Modificacion</div></div>", title:"MODIFICACION", group: "Acciones" },
                            { html: "<div><div>Exportar</div></div>", title:"EXPORTAR", group: "Acciones" },
                            { html: "<div><div>Otros</div></div>", title:"OTROS", group: "Acciones" }
                        ];
                        $("#listbox").jqxListBox({ source: source_acc, checkboxes: true, displayMember: "NOMBRE", valueMember: "ID", width: 300, height: 170 });
                        $("#listbox").jqxListBox('checkAll');
                        
                        $("#jqxButton").jqxToggleButton({ width: '200', toggled: true, theme: theme });
                        $("#jqxButton").on('click', function () {
                            var toggled = $("#jqxButton").jqxToggleButton('toggled');
                            if (toggled){
                                $("#jqxButton")[0].value = 'Quitar selección';
                                $("#listbox").jqxListBox('checkAll');
                            }else{
                                $("#jqxButton")[0].value = 'Seleccionar Todos';
                                $("#listbox").jqxListBox('uncheckAll');
                            }
                        });
                        $('#id_rol,#id_permiso').change(function() {
                            $(this).validationEngine('validate');
                        });
                    }
                });
                
            }else if(top=='edi'){
                var ver = $(this).data('ver');
                ver || ( ver = '-1' );
                
                if (ver!=-1){
                    if (_permiso_ver==0 && ver ){
                        jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                            $.unblockUI();
                            switchBarra();
                        });
                        return false;
                    }
                }else{
                    if (_permiso_modificacion==0){
                        jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                            $.unblockUI();
                            switchBarra();
                        });
                        return false;
                    }
                }
                
                mydata = '';
                var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                               
                if ( mydata==null ){
                    jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                    });
                    return false;
                }
                
                                
                // edit entidades
                $.ajax({
                    url : _permisos.URL + "/x_getform_addentidad_new",
                    data : {
                        obj:mydata.ID_ROL
                    },
                    async:false,
                    type : "post",
                    success : function(data){
                      
                        $.unblockUI();
                        $("#jqxgrid").hide();
                        $("#wpopup").html(data);
                        
                        $(".chzn-select").chosen(); 
                        $("#btnBorrar").hide();
                                                                        
                        $("#label_action").html('Editar');
                        var _arr_obj_n = jQuery.parseJSON($("#_arr_obj_n").val());
                        
                        $("#id_rol").val(_arr_obj_n[0].ID_ROL).trigger("chosen:updated");
                        //$("#id_permiso").val(_array_obj.ID_PERMISO).trigger("chosen:updated");
         
                        //eventos
                        
                        $(".CSSTableGenerator input").click(function(e){
                            stopBubble(e);
                        });
                        
                        $(".CSSTableGenerator table tr td").first().click(function(e){
                           stopBubble(e);
                           
                           if ($(this).parent().parent().find('td input').first().is(":checked")){
                               $(this).parent().parent().find('td input').removeAttr("checked");
                           }else{
                               $(this).parent().parent().find('td input').attr("checked","checked");
                           }
                           
                        });
                        
                        $(".CSSTableGenerator table tr td").click(function(e){
                            var ind_tmp = $(this).index();
                            if (ind_tmp==0){
                                //seleccionar todos
                                
                                if ( $(this).parent().find('input').first().is(":checked")){
                                    $(this).parent().find('input').removeAttr("checked");
                                }else{
                                    $(this).parent().find('input').attr("checked","checked");
                                }
                                return false;
                            }
                            if ($(this).find('input').is(":checked")){
                                $(this).find('input').removeAttr("checked");
                            }else{
                                $(this).find('input').attr("checked","checked");
                            }
                        });
                        
                        $(".CSSTableGenerator table tr").first().find('td').click(function(e){
                            var indextr = $(this).index();
                           $(".CSSTableGenerator table tr:gt(0)").each(function( index1 ) {
                               if ($(this).find('td').eq(indextr).find('input').first().is(":checked")){
                                   $(this).find('td').eq(indextr).find('input').removeAttr("checked");
                               }else{
                                   $(this).find('td').eq(indextr).find('input').attr("checked","checked");
                               }
                           });
                        });
         
                        //cargar valoresss
                        
                        
                        $.each(_arr_obj_n, function (index, value){
                            
                            
                            $(".CSSTableGenerator table tr:gt(0)").eq(index).find('input').each(function(index1){
                                //$(this).eq(0).attr("checked","checked");
                                
                                if (index1=='0'){
                                    if (value.MOSTRAR==1){
                                        $(this).attr("checked","checked");
                                    }
                                }
                                if (index1=='1'){
                                    if (value.ALTA==1){
                                        $(this).attr("checked","checked");
                                    }
                                }
                                if (index1=='2'){
                                    if (value.BAJA==1){
                                        $(this).attr("checked","checked");
                                    }
                                }
                                if (index1=='3'){
                                    if (value.MODIFICACION==1){
                                        $(this).attr("checked","checked");
                                    }
                                }
                                if (index1=='4'){
                                    if (value.VER==1){
                                        $(this).attr("checked","checked");
                                    }
                                }
                                if (index1=='5'){
                                    if (value.EXPORTAR==1){
                                        $(this).attr("checked","checked");
                                    }
                                }
                                if (index1=='6'){
                                    if (value.OTROS==1){
                                        $(this).attr("checked","checked");
                                    }
                                }
                               });
                        });
                        
                        $("#jqxButton").jqxToggleButton({ width: '200', toggled: true, theme: theme });
                        $("#jqxButton").on('click', function () {
                            var toggled = $("#jqxButton").jqxToggleButton('toggled');
                            if (toggled){
                                $("#jqxButton")[0].value = 'Quitar selección';
                                $("#listbox").jqxListBox('checkAll');
                            }else{
                                $("#jqxButton")[0].value = 'Seleccionar Todos';
                                $("#listbox").jqxListBox('uncheckAll');
                            }
                        });
                       
                        $('#btnClear').on('click', function(event) {
                            $("#customForm").validationEngine('hideAll');
                            event.preventDefault();
                            $("#frmagregar :text").val("");
                            $("#frmagregar :text").first().focus();
                            $("#idh").val("");
                            $("#provincia").val(0).trigger("chosen:updated");

                            if($("#label_action").html()=='Editar'){
                              $("#label_action").html("Agregar");
                            }
                            $("#frmagregar :text").removeClass('error');
                            $("#nom").select();
                        });
                        
                        $('#send').on('click', function(event) {
                            
                            event.preventDefault();
                            
                            var id_rol = $("#id_rol").val();
                            var id_permiso = $("#id_permiso").val();
                            
                            
                            if (id_rol<=0){
                                jAlert('Elija un Rol.', $.ucwords(_etiqueta_modulo),function(){
                                    
                                });
                                return false;
                            }
                            
                            //recorrer matriz
                            var obj_fin =[];
                            $(".CSSTableGenerator table tr:gt(0)").each(function(index){
                                //console.log( index + ": " + $( this ).text() );
                                
                                var id_permiso = $(this).data("idp");
                                var_ins = {
                                    "ID_ROL":id_rol,
                                    "ID_PERMISO":id_permiso,
                                    "MOSTRAR":"0",
                                    "ALTA":"0",
                                    "BAJA":"0",
                                    "MODIFICACION":"0",
                                    "VER":"0",
                                    "EXPORTAR":"0",
                                    "OTROS":"0"
                                }
                                    
                                $(this).find('td input').each(function( index1 ) {
                                    if ($(this).is(":checked")){
                                        //console.log( 'permiso: ' + id_permiso + ' - ' + index1 + ' index1: si');
                                        if (index1==0){
                                            var_ins['MOSTRAR'] = 1;
                                        }else if (index1==1){
                                            var_ins['ALTA'] = 1;
                                        }else if (index1==2){
                                            var_ins['BAJA'] = 1;
                                        }else if (index1==3){
                                            var_ins['MODIFICACION'] = 1;
                                        }else if (index1==4){
                                            var_ins['VER'] = 1;
                                        }else if (index1==5){
                                            var_ins['EXPORTAR'] = 1;
                                        }else if (index1==6){
                                            var_ins['OTROS'] = 1;
                                        }
                                    }else{
                                        //console.log( 'permiso: ' + id_permiso + ' - ' + index1 + ' index1: no');
                                        if (index1==0){
                                            var_ins['MOSTRAR'] = 0;
                                        }else if (index1==1){
                                            var_ins['ALTA'] = 0;
                                        }else if (index1==2){
                                            var_ins['BAJA'] = 0;
                                        }else if (index1==3){
                                            var_ins['MODIFICACION'] = 0;
                                        }else if (index1==4){
                                            var_ins['VER'] = 0;
                                        }else if (index1==5){
                                            var_ins['EXPORTAR'] = 0;
                                        }else if (index1==6){
                                            var_ins['OTROS'] = 0;
                                        }
                                    }
                                });
                                obj_fin.push(var_ins);
                                
                            });
                            
                            var id = $("#idh").val();
                            
                            if ( !$("#customForm").validationEngine('validate') )
                                return false;
                           
                            
                            iid = id ? id:0;
                            obj = {
                                id:iid,
                                obj_fin:obj_fin
                            }
                            
                            $.ajax({
                                url : _permisos.URL + "/x_sendobj",
                                data : {
                                    obj:obj
                                },
                                dataType : "json",
                                type : "post",
                                success : function(data){
                                    if(data.result==-1){
                                        jAlert('Este Permiso ya existe para este Rol. No se puede agregar.', $.ucwords(_etiqueta_modulo),function(){
                                        });
                                    }else if(data.result>0){
                                        jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                            $('#btnClear').trigger('click');
                                            $(".contenedor_entidades p input").first().trigger('click');
                                            $("#jqxgrid").show();
                                            $("#wpopup").html('');
                                            $("#jqxgrid").jqxGrid('updatebounddata');
                                        });
                                    }
                                    else{
                                        jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                            $.unblockUI();
                                        });
                                    }
                                }
                            });
                        });
                        
                        $('#id_rol,#id_permiso').change(function(){
                            $(this).validationEngine('validate');
                        });
                        
                        
                        if (ver!=-1){
                            $(".elempie").html('').hide();
                        }
                        
                    }
                });  
                
                $.unblockUI();
                
            }else if (top=='del'){
                if (_permiso_baja==0){
                    jAlert('Usted no tiene Permisos para ejecutar esta acción', $.ucwords(_etiqueta_modulo),function(){
                        $.unblockUI();
                        switchBarra();
                    });
                    return false;
                }
                
                jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                
                        var selectedrowindex = $("#jqxgrid").jqxGrid('getselectedrowindex');
                        mydata = $('#jqxgrid').jqxGrid('getrowdata', selectedrowindex);
                        var rowscount = $("#jqxgrid").jqxGrid('getdatainformation').rowscount;

                        if ( mydata==null ){
                            jAlert('Seleccione Item.', $.ucwords(_etiqueta_modulo),function(){
                                $.unblockUI();
                            });
                            return false;
                        }
                        
                        
                        $.ajax({
                            url : _permisos.URL + "/x_delobj",
                            data : {
                                id:mydata.ID_ROL
                            },
                            dataType : "json",
                            type : "post",
                            success : function(data){

                                if(data>0){

                                    if (selectedrowindex >= 0 && selectedrowindex < rowscount) {
                                        var id = $("#jqxgrid").jqxGrid('getrowid', selectedrowindex);
                                        var commit = $("#jqxgrid").jqxGrid('deleterow', id);
                                    }

                                }
                                else{
                                    jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                        $.unblockUI();
                                    });
                                }

                            }
                        });
                    }
                    $.unblockUI();
                    });

            }else if (top=='ope'){
                                
                // tipo entidad
                $.ajax({
                    url : _permisos.URL + "/x_getform_roles",
                    data : {
                        obj:obj
                    },
                    type : "post",
                    success : function(data){
                        $.unblockUI();
                        $.fancybox(
                            data,
                            {
                                'padding'   :  20,
                                'autoScale' :true,
                                'scrolling' : 'no'
                            }
                        );
                            
                        $("#nom").focus();
                            
                        var source_ent ={
                                datatype: "json",
                                datafields: [{ name: 'ID' },{ name: 'DENOMINACION' }],
                                url: _permisos.URL + '/x_get_rolesp',
                                updaterow: function (rowid, rowdata, commit) {
                                    process_data(_permisos.URL + "/x_update_rolesp",rowdata);
                                },
                                deleterow: function (rowid, commit) {
                                    process_data(_permisos.URL + "/x_delete_rolesp",mydata);
                                    commit(true);
                                }
                        };

                        $("#jqxgrid_ent").jqxGrid(
                        {
                            width: '98%',
                            source: source_ent,
                            theme: 'energyblue',
                            editable: false,
                            //editmode: 'dblclick',
                            localization: getLocalization(),
                            columns: [
                                    { text: 'ID', datafield: 'ID', width: '30%' , editable: false},
                                    { text: 'TIPO', datafield: 'DENOMINACION', width: '70%' }
                            ]
                        });
                        
                        add_event('jqxgrid_ent',_permisos.URL + "/x_add_rolesp",'DENOMINACION');
                        
                        var arr_confirma = [];
                        arr_confirma['url'] = _permisos.URL+ "/x_get_dependencia";
                        arr_confirma['tabla'] = 'fid_roles_permisos';
                        arr_confirma['campo'] = 'ID_ROL';
                        delete_event( 'jqxgrid_ent', $.ucwords(_etiqueta_modulo) , arr_confirma );
                        
                        edit_event('jqxgrid_ent','DENOMINACION');
                            
                    }
                });                
                
            }else if(top=='lis'){
                $.unblockUI();
                $('#btnClear').trigger('click');
                $("#jqxgrid").show();
                $("#wpopup").html('');
                $("#jqxgrid").jqxGrid('updatebounddata');
            }else if (top=='usu'){
                url = "backend/administracion/usuarios";
                jConfirm('Esta seguro de ir a Usuarios?. Los datos sin guardar, se perderán.', $.ucwords(_etiqueta_modulo),function(r){
                    if(r==true){
                        $(location).attr('href',url);
                    }else{
                        $.unblockUI();
                    }
                });
            }
    });
            
    var source ={
            datatype: "json",
            datafields: [
                { name: 'ROLNAME' },
                { name: 'MODULONAME' },
                { name: 'PERMISONAME'},
                { name: 'MOSTRAR'},
                { name: 'ALTA'},
                { name: 'BAJA'},
                { name: 'MODIFICACION'},
                { name: 'EXPORTAR'},
                { name: 'OTROS'},
                { name: 'ID'}
            ],
            url: _permisos.URL + '/x_get_info_grid',
            deleterow: function (rowid, commit) {
                commit(true);
            }
    };
    
    var sourceope ={
        datatype: "json",
        datafields: [
            { name: 'ID_ROL', type: 'string' },
            { name: 'DENOMINACION', type: 'string' },
        ],
        //url: _clientes.URL + '/x_get_info_grid',
        url: 'general/extends/extra/roles_permisos.php',
        data:{
            accion: "getRolesPermisos_new"
        },
        async:false,
        deleterow: function (rowid, commit) {
            commit(true);
        }
    };
    
    var dataAdapterope = new $.jqx.dataAdapter(sourceope,
        {
            formatData: function (data) {
                data.name_startsWith = $("#searchField").val();
                return data;
            }
        }
    );
    
    
			
    $("#jqxgrid").jqxGrid(
    {
        width: '98%',
        groupable:true,
        //source: source,
        source: dataAdapterope,
        theme: 'energyblue',
        ready: function () {
            $("#jqxgrid").jqxGrid('hidecolumn', 'ID_ROL');
        },
        columnsresize: true,
        sortable: true,
        filterable: true,
        showfilterrow: true,
        showtoolbar: true,
        localization: getLocalization(),
        rendertoolbar: function (toolbar) {
            var me = this;
            var container = $("<div style='margin: 5px;'></div>");
            var span = $("<span style='float: left; margin-top: 5px; margin-right: 4px;'>Buscar: </span>");
            var input = $("<input class='jqx-input jqx-widget-content jqx-rc-all' id='searchField' type='text' style='height: 23px; float: left; width: 223px;' />");
            toolbar.append(container);
            container.append(span);
            container.append(input);
            if (theme != "") {
                input.addClass('jqx-widget-content-' + theme);
                input.addClass('jqx-rc-all-' + theme);
            }

            input.on('keydown', function (event) {
                if (input.val().length >= 2) {
                    if (me.timer) clearTimeout(me.timer);
                    me.timer = setTimeout(function () {
                        dataAdapterope.dataBind();
                    }, 300);
                }
            });
        },
        columns: [
            { text: 'ROL', datafield: 'DENOMINACION', width: '50%', columntype: 'textbox', filtertype: 'checkedlist', filtercondition: 'starts_with', filterable : true },
            { text: 'ID', datafield: 'ID_ROL', width: '0%' }
        ]
    });
   
    
});

//Es el mismo ajax que cuando llama al editar 
//nada mas que esta puesto dentra de una funcion para que refresque 
function actualiza_ss(valu) {
    $.ajax({
        url : _permisos.URL + "/x_getform_addentidad_new",
        data : {
            obj:valu
        },
        async:false,
        type : "post",
        success : function(data){
//console.log("QQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQQ");
//console.log(data);

            $.unblockUI();
            $("#jqxgrid").hide();
            $("#wpopup").html(data);

            $(".chzn-select").chosen(); 
            $("#btnBorrar").hide();

            $("#label_action").html('Editar');
            var _arr_obj_n = jQuery.parseJSON($("#_arr_obj_n").val());

//console.log("WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW");
//console.log($("#_arr_obj_n").val());

            $("#id_rol").val(_arr_obj_n[0].ID_ROL).trigger("chosen:updated");

            //$("#id_permiso").val(_array_obj.ID_PERMISO).trigger("chosen:updated");

            //eventos

            $(".CSSTableGenerator input").click(function(e){
                stopBubble(e);
            });

            $(".CSSTableGenerator table tr td").first().click(function(e){
               stopBubble(e);

               if ($(this).parent().parent().find('td input').first().is(":checked")){
                   $(this).parent().parent().find('td input').removeAttr("checked");
               }else{
                   $(this).parent().parent().find('td input').attr("checked","checked");
               }

            });

            $(".CSSTableGenerator table tr td").click(function(e){
                var ind_tmp = $(this).index();
                if (ind_tmp==0){
                    //seleccionar todos

                    if ( $(this).parent().find('input').first().is(":checked")){
                        $(this).parent().find('input').removeAttr("checked");
                    }else{
                        $(this).parent().find('input').attr("checked","checked");
                    }
                    return false;
                }
                if ($(this).find('input').is(":checked")){
                    $(this).find('input').removeAttr("checked");
                }else{
                    $(this).find('input').attr("checked","checked");
                }
            });

            $(".CSSTableGenerator table tr").first().find('td').click(function(e){
                var indextr = $(this).index();
               $(".CSSTableGenerator table tr:gt(0)").each(function( index1 ) {
                   if ($(this).find('td').eq(indextr).find('input').first().is(":checked")){
                       $(this).find('td').eq(indextr).find('input').removeAttr("checked");
                   }else{
                       $(this).find('td').eq(indextr).find('input').attr("checked","checked");
                   }
               });
            });

            //cargar valoresss


            $.each(_arr_obj_n, function (index, value){


                $(".CSSTableGenerator table tr:gt(0)").eq(index).find('input').each(function(index1){
                    //$(this).eq(0).attr("checked","checked");

                    if (index1=='0'){
                        if (value.MOSTRAR==1){
                            $(this).attr("checked","checked");
                        }
                    }
                    if (index1=='1'){
                        if (value.ALTA==1){
                            $(this).attr("checked","checked");
                        }
                    }
                    if (index1=='2'){
                        if (value.BAJA==1){
                            $(this).attr("checked","checked");
                        }
                    }
                    if (index1=='3'){
                        if (value.MODIFICACION==1){
                            $(this).attr("checked","checked");
                        }
                    }
                    if (index1=='4'){
                        if (value.VER==1){
                            $(this).attr("checked","checked");
                        }
                    }
                    if (index1=='5'){
                        if (value.EXPORTAR==1){
                            $(this).attr("checked","checked");
                        }
                    }
                    if (index1=='6'){
                        if (value.OTROS==1){
                            $(this).attr("checked","checked");
                        }
                    }
                   });
            });

            $("#jqxButton").jqxToggleButton({ width: '200', toggled: true, theme: theme });
            $("#jqxButton").on('click', function () {
                var toggled = $("#jqxButton").jqxToggleButton('toggled');
                if (toggled){
                    $("#jqxButton")[0].value = 'Quitar selección';
                    $("#listbox").jqxListBox('checkAll');
                }else{
                    $("#jqxButton")[0].value = 'Seleccionar Todos';
                    $("#listbox").jqxListBox('uncheckAll');
                }
            });

            $('#btnClear').on('click', function(event) {
                $("#customForm").validationEngine('hideAll');
                event.preventDefault();
                $("#frmagregar :text").val("");
                $("#frmagregar :text").first().focus();
                $("#idh").val("");
                $("#provincia").val(0).trigger("chosen:updated");

                if($("#label_action").html()=='Editar'){
                  $("#label_action").html("Agregar");
                }
                $("#frmagregar :text").removeClass('error');
                $("#nom").select();
            });

            $('#send').on('click', function(event) {

                event.preventDefault();

                var id_rol = $("#id_rol").val();
                var id_permiso = $("#id_permiso").val();


                if (id_rol<=0){
                    jAlert('Elija un Rol.', $.ucwords(_etiqueta_modulo),function(){

                    });
                    return false;
                }

                //recorrer matriz
                var obj_fin =[];
                $(".CSSTableGenerator table tr:gt(0)").each(function(index){
                    //console.log( index + ": " + $( this ).text() );

                    var id_permiso = $(this).data("idp");
                    var_ins = {
                        "ID_ROL":id_rol,
                        "ID_PERMISO":id_permiso,
                        "MOSTRAR":"0",
                        "ALTA":"0",
                        "BAJA":"0",
                        "MODIFICACION":"0",
                        "VER":"0",
                        "EXPORTAR":"0",
                        "OTROS":"0"
                    }

                    $(this).find('td input').each(function( index1 ) {
                        if ($(this).is(":checked")){
                            //console.log( 'permiso: ' + id_permiso + ' - ' + index1 + ' index1: si');
                            if (index1==0){
                                var_ins['MOSTRAR'] = 1;
                            }else if (index1==1){
                                var_ins['ALTA'] = 1;
                            }else if (index1==2){
                                var_ins['BAJA'] = 1;
                            }else if (index1==3){
                                var_ins['MODIFICACION'] = 1;
                            }else if (index1==4){
                                var_ins['VER'] = 1;
                            }else if (index1==5){
                                var_ins['EXPORTAR'] = 1;
                            }else if (index1==6){
                                var_ins['OTROS'] = 1;
                            }
                        }else{
                            //console.log( 'permiso: ' + id_permiso + ' - ' + index1 + ' index1: no');
                            if (index1==0){
                                var_ins['MOSTRAR'] = 0;
                            }else if (index1==1){
                                var_ins['ALTA'] = 0;
                            }else if (index1==2){
                                var_ins['BAJA'] = 0;
                            }else if (index1==3){
                                var_ins['MODIFICACION'] = 0;
                            }else if (index1==4){
                                var_ins['VER'] = 0;
                            }else if (index1==5){
                                var_ins['EXPORTAR'] = 0;
                            }else if (index1==6){
                                var_ins['OTROS'] = 0;
                            }
                        }
                    });
                    obj_fin.push(var_ins);

                });

                var id = $("#idh").val();

                if ( !$("#customForm").validationEngine('validate') )
                    return false;


                iid = id ? id:0;
                obj = {
                    id:iid,
                    obj_fin:obj_fin
                }

                $.ajax({
                    url : _permisos.URL + "/x_sendobj",
                    data : {
                        obj:obj
                    },
                    dataType : "json",
                    type : "post",
                    success : function(data){
                        if(data.result==-1){
                            jAlert('Este Permiso ya existe para este Rol. No se puede agregar.', $.ucwords(_etiqueta_modulo),function(){
                            });
                        }else if(data.result>0){
                            jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                $('#btnClear').trigger('click');
                                $(".contenedor_entidades p input").first().trigger('click');
                                $("#jqxgrid").show();
                                $("#wpopup").html('');
                                $("#jqxgrid").jqxGrid('updatebounddata');
                            });
                        }
                        else{
                            jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                                $.unblockUI();
                            });
                        }
                    }
                });
            });

            $('#id_rol,#id_permiso').change(function(){
                $(this).validationEngine('validate');
            });


            if (ver!=-1){
                $(".elempie").html('').hide();
            }

        }
    });  
                
}