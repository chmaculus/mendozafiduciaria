<?php function view_error_controller($file, $clase){?>
<div style="width:100%;padding: 20px;margin: 20px;
     background: #d1d1d1;">
    <?php if (DEVELOPER_MODE){ ?>
        <span>[ERR-800]No se encuentra el archivo '<?=$file?>'</span>
    <? }
    else{ ?>
        <span>La ruta de acceso no es válida</span>
    <?php } ?>
</div>

<?php


} ?>
<?php function view_error_class($file, $clase){?>
<div style="width:100%;padding: 20px;margin: 20px;
     background: #d1d1d1;">
    <?php if (DEVELOPER_MODE){ ?>
        <span>[ERR-100]No se encuentra la clase '<?=$clase?>' en el archivo '<?=$file?>'</span>
    <? }
    else{ ?>
        <span>Error interno. Ruta no válida.</span>
    <?php } ?>
    
</div>

<?php }



function view_error_controoller($file, $err){
    if (is_bool($err)){
        if (DEVELOPER_MODE){
            $ar_err = error_get_last();
            echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error en archivo: ".$file."<br/>";
            echo "[ERR-200]".$ar_err['message'];
            echo "<br/>En linea ".$ar_err['line'];
        }
        else{
            echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error Interno. Ruta no válida<br/>";

        }
    }
}

function view_error_warning($file, $err){

    if (DEVELOPER_MODE){
        $ar_err = error_get_last();
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error en archivo: ".$file."<br/>";
        echo "[ERR-300]".$ar_err['message'];
        echo "<br/>En linea ".$ar_err['line'];
    }
    else{
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error Interno. Ruta no válida<br/>";

    }
}
function view_error_func($file, $class){

    if (DEVELOPER_MODE){
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>[ERR-400]Metodo no encontrado en la clase '".$class."' en en archivo '".$file."'<br/>";
    }
    else{
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error Interno. Ruta no válida<br/>";

    }
}

function view_error_model($controller, $model){
    if (DEVELOPER_MODE){
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>[ERR-500]Error en controlador '".$controller."', el modelo '".$model."' no es accesible o no existe<br/>";
    }
    else{
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error Interno. Ruta no válida<br/>";

    }
}
function view_error_model_class($file, $model){
    if (DEVELOPER_MODE){
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>[ERR-600]Error en archivo '".$file."', la clase '".$model."' no está declarada<br/>";
    }
    else{
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error Interno. Ruta no válida<br/>";

    }
}

function view_error($str_error){
    if (DEVELOPER_MODE){
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>[ERR-700]".$str_error."<br/>";
    }
    else{
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error Interno. Ruta no válida<br/>";
    }
}

function view_error_view($file, $model){
    if (DEVELOPER_MODE){
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>[ERR-900]El archivo '".$file."', no existe<br/>";
    }
    else{
        echo "<div style='border: 1px solid black; font-size:24px;;background: #ddf;padding: 50px;'>Error Interno. Ruta no válida<br/>";

    }
}
?>