<?php

    if (isset($_POST)):
        $info = $_POST;
    
        $type = "application/force-download";
        header("Content-Type: $type");
        
        $filename = $info["filename"] . ".".$info["format"];
        
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Transfer-Encoding: binary");
    
        echo $info['content'];
        die();
        
    endif;

?>