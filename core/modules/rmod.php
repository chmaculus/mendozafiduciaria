<?php

require '../../config.php';
include("../helper/util.php");



//inicia configuracion en $_config
$_config = array();
get_conf($_config);


//inicializa origenes de datos de modelos (mysql, soap)
foreach($_config['access'] as $access){
    include("../model/".$access);
}

$mysql = $global['_db'] ;

$modulo = $_GET['mod'];

$js_var = array();
$js_array = array();

//$data = new sqldata();


$DIR = "../../".MODULE_DIRECTORY.$modulo."/";

if (!isset($_GET['init'])){
    include($DIR."controller.php");
    die();
}

$params = $_POST;

echo "<style>".file_get_contents($DIR.$modulo.".css")."</style>"; ?>
<script type="text/javascript"><?php echo file_get_contents($DIR.$modulo.".js"); ?></script>
<?php
include($DIR."controller.php");
//include($DIR."controller.php");
include($DIR."view.php");

$js_init = str_replace(array("_","-"), "", $modulo);


?>
<script type="text/javascript">
     
    <?php foreach($js_var as $key=>$value){?>
        var <?=$key?> = "<?=$value?>";
    <?php } ?>
     
    <?php foreach($js_array as $key=>$value){?>
        var tmp = '<?=json_encode($value)?>';
        var <?=$key?> = $.parseJSON(tmp);
    <?php } ?>    
    
    var x = 'init_<?=$js_init?>()';
    var URL_MODULO = 'modulo.php?modulo=<?=$modulo?>';
    if(typeof (window.init_<?=$js_init?>) == 'function') {
        eval(x);        
    }    
    
</script>