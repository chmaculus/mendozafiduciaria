<?php

$encod = 0;
require_once('core/helper/util.php');


//print_array($_GET);

if ($encod == 1) {
    t_include("config_enc.php");
} else {
    include("config.php");
}


$_config = array();
get_conf($_config);

if (isset($_config['access'])) {
    foreach ($_config['access'] as $access) {
        include("core/model/" . $access);
    }
    $cnn = $_db;
}

$file = $_GET['url'] . "x/" . $_GET['file'];

include($file);





/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

