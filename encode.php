<?php
echo "encoding...";
function t_enc($source, $target){
    $x = file_get_contents($source);
    $data = mcrypt_ecb (MCRYPT_CAST_128, 'k38dos92jdoa2', $x, MCRYPT_ENCRYPT);
    $data = base64_encode($data);
    
    file_put_contents($target, $data);
}

$dir = "app_decode/";

$source = "app_decode";
$target = "app";


function recurse_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    
    $enc = false;
    $segments_dst = explode("/",$dst);
    $last_segment = $segments_dst[count($segments_dst )-1];
    if ($last_segment  =='controller' || $last_segment =='model'){
        echo $dst." - encoded<br/>";
        $enc = true;
    }
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                if ($enc){
                    t_enc($src . '/' . $file,$dst . '/' . $file );
                }
                else{
                    copy($src . '/' . $file,$dst . '/' . $file); 
                }
            } 
        } 
    } 
    closedir($dir); 
} 

function rrmdir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
       } 
     } 
     reset($objects); 
     rmdir($dir); 
   } 
 }
 
if (file_exists($dir)){
    
    rrmdir($target);
    mkdir($target);
    recurse_copy($source, $target);
    /*
    $files1 = scandir($dir);
    foreach($files1 as $file){
        if (is_dir($dir.$file)){
            
            $modules = scandir($dir.$file);
            foreach($modules as $module){
                if (is_dir($dir.$file."/".$module) && $module=='controller'){
                    $controllers = scandir($dir.$file."/".$module);
                    foreach($controllers as $controller){
                        if (!is_dir($dir.$file."/".$module."/".$controller ) ){
                            $source = "app_decode/".$file."/".$module."/".$controller;
                            $target = "app/".$file."/".$module."/".$controller;
                            if (file_exists($source))
                                t_enc($source, $target);    
                            
                        }
                    }
                }
                if (is_dir($dir.$file."/".$module) && $module=='model'){
                    $models = scandir($dir.$file."/".$module);
                    foreach($models as $model){
                        if (!is_dir($dir.$file."/".$module."/".$model ) ){
                            $source = "app_decode/".$file."/".$module."/".$model;
                            $target = "app/".$file."/".$module."/".$model;
                            if (file_exists($source))
                                t_enc($source, $target);    
                            
                        }
                    }
                }
            }

        }
    }*/
    t_enc("332256458.php", "332256458_enc.php");    
    t_enc("config.php", "config_enc.php");    
    echo "ENCODED OK!";
}
else{
    echo "asd";
}




?>
