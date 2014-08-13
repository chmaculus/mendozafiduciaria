$(document).ready(function(){
    
    $("#customForm").validationEngine();
    $('#nom').keyup(function() {
        $(this).validationEngine('validate');
    });
    
    cargarResults();
    $('#btnClear').on('click', function(event) {
        $("#customForm").validationEngine('hideAll');
        event.preventDefault();
        $("#frmagregar :text").val("");
        $("#frmagregar :text").first().focus();
        $("#idh").val("");
        
        if($("#label_action").html()=='Editar'){
          $("#label_action").html("Agregar");
        }
        $("#frmagregar :text").removeClass('error');
                
    });
    
    $('#send').on('click', function(event) {
        var id = $("#idh").val();
        var nom = $("#nom").val();
        var val = $("#val").val();
       
       if ( !$("#customForm").validationEngine('validate') )
           return false;
       
        iid = id ? id:0;
        if (id==0){
            if (_permiso_alta==0){
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
        obj = {
            id:id,
            condicion:nom,
            valor:val
        }
                
        $.ajax({
            url : _condicioniibb.URL + "/x_sendobj",
            data : {
                obj:obj
            },
            dataType : "json",
            type : "post",
            success : function(data){
                $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
               
                if(data.result>0){
                    setTimeout(function() {
                        $.unblockUI({
                            onUnblock: function(){ 
                                jAlert('Operacion Exitosa.', $.ucwords(_etiqueta_modulo),function(){
                                    $('#btnClear').trigger('click');   
                                    RefreshTable('#datatable', _condicioniibb.URL +"/x_get_datatable");
                                });
                            } 
                        }); 
                    }, 500);
                }
                else{
                  jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                      $.unblockUI();
                  });
                }
            }
        });
       
    });
       
});

function addEventsTable(){

    $('.btnEditar').on('click', function(e) {
        e.preventDefault();
        if (_permiso_modificacion==0){
            jAlert('Usted no tiene Permisos para ejecutar esta acción.', $.ucwords(_etiqueta_modulo),function(){
                $.unblockUI();
                switchBarra();
            });
            return false;
        }
        var id = $(this).data('id');
        
        editar(id);       
    });
    
    
    $('.btnBorrar').on('click', function(e) {
        e.preventDefault();
        if (_permiso_baja==0){
            jAlert('Usted no tiene Permisos para ejecutar esta acción', $.ucwords(_etiqueta_modulo),function(){
                $.unblockUI();
                switchBarra();
            });
            return false;
        }
        var id = $(this).data('id');
        
        jConfirm('Esta seguro de borrar este item??.', $.ucwords(_etiqueta_modulo),function(r){
          if(r==true){
              $.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Procesando</h4>' });
              //eliminar
              $.ajax({
                  url : _condicioniibb.URL + "/x_delobj",
                  data : {
                      id : id
                  },
                  type : "post",
                  success : function(data){
        
                        if(data==1){
                            setTimeout(function() {
                                $.unblockUI({
                                    onUnblock: function(){ 
                                        $('#btnClear').trigger('click');   
                                        RefreshTable('#datatable', _condicioniibb.URL +"/x_get_datatable");
                                    } 
                                }); 
                            }, 100);
                        }
                        else{
                          jAlert('Operacion Erronea. Intente Otra vez.', $.ucwords(_etiqueta_modulo),function(){
                              $.unblockUI();
                          });
                        }
                  }
              });
              
          }
          
        });
    });
    
    
}

function cargarResults(){
    //$.blockUI({ message: '<h4><img src="general/images/block-loader.gif" /> Refresh</h4>' });
        
    var settings = {
        "sDom": '<"clear">lfrtip<"clear spacer">',
        'bSort': true,
        "oLanguage": {
            "sUrl": 'general/plugin/datatables/' + "dataTables.es.txt"
        },
        "bProcessing": true,
        "aoColumns": [
          null,
          null,
          null,
          {"bSortable": false }
        ],
        "aaSorting": [[ 0, "desc" ]],
        "iDisplayLength": 10,
        "fnDrawCallback": function( oSettings ) {
            addEventsTable();
        },
        "sAjaxSource": _condicioniibb.URL + "/x_get_datatable",
        "fnServerData": function ( sSource, aoData, fnCallback ) {
                $.ajax( {
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": function (code){
                        fnCallback(code);
                        $.unblockUI();
                    }
                } );
        }
    };
        
    $('#datatable').dataTable( settings );
        
}

function editar(id){
        
    $("#label_action").html("Editar");  
    $.ajax({
        url : _condicioniibb.URL + "/x_getobj",
        async: false,
        data : {
            id : id
        },
        dataType: "json",
        type : "post",
        success : function(obj){
            fillFormEdit(obj);
        }
    });
        
}

function fillFormEdit(obj){
    $("#idh").val(obj.ID);
    $("#nom").val(obj.CONDICION);
    $("#val").val(obj.VALOR);
}
