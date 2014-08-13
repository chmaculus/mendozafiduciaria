<!DOCTYPE html>
<html>
    <head>
        <base href="<?=URL_SITIO ?>" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <!--<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Cuprum" />-->
        <title><?=$titulo?></title>
        <?php
        echo $css->render();
        //echo $script_ini;
        echo $js->render();
        echo $plug->render();
        ?>
    </head>
    <body>
        
            <?php echo $main?>
            
    </body>
    
    
</html>