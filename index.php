<?php

//ini_set('display_errors',0);

$encod = 0;

date_default_timezone_set("America/Argentina/Mendoza");
error_reporting(E_ALL | E_STRICT);
session_start();


include("core/helper/util.php");

if ($encod ){
    t_include("config_enc.php");
}
else{
    include_once('config.php');
}

## realizamos backup de la base de datos todos los días, en el primer uso de la aplicación
$zipfname = "backup\\" . MYSQL_DATABASE . "_" . date("Ymd").".zip";
if (!is_file($zipfname)) {
    if(!is_dir("backup")) {
        mkdir("backup");
    }
    $username = MYSQL_USER; 
    $password = MYSQL_PASS; 
    $hostname = MYSQL_SERVER; 
    $dbname   = MYSQL_DATABASE;

    // if mysqldump is on the system path you do not need to specify the full path
    // simply use "mysqldump --add-drop-table ..." in this case
    $dumpfname = "backup\\" . $dbname . ".sql";
    $command = "C:\\xampp\\mysql\\bin\\";
    if (!is_dir($command)) {
        $command = "D:\\xampp\\mysql\\bin\\";
    }
    
    $command .= "mysqldump --add-drop-table --host=$hostname --user=$username ";
    if ($password) 
            $command.= "--password=". $password ." "; 
    $command.= $dbname;
    $command.= " > " . $dumpfname;
    system($command);
    
    // zip the dump file

    $zip = new ZipArchive();
    if($zip->open($zipfname,ZIPARCHIVE::CREATE)) 
    {
       $zip->addFile($dumpfname,$dumpfname);
       $zip->close();
    }
}


if ($encod ){
    t_include("332256458_enc.php");
}
else{
    include("332256458.php");
}





?>