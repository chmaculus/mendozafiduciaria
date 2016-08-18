<!DOCTYPE html>
<html>
<head>
    <base href="<?=URL_SITIO ?>" />
    <meta charset="utf-8">
    <title>Admin Panel</title>
    <meta name="description" content="">
    <?php
            echo $css->render();
            //echo $script_ini;
            echo $js->render();
            echo $plug->render();
    ?>    
    <!--<link rel="shortcut icon" href="images/favicon.ico" />-->
    <!--<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Cuprum" />-->
    <link rel="stylesheet" href="general/css/style.css" />
    <link rel="stylesheet" href="general/css/jquery-ui-1.8.16.custom.css" media="screen"  />
    <link rel="stylesheet" href="general/css/font-awesome.min.css" media="screen"  />
    <!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <script src="general/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="general/js/ddaccordion.js"></script>
    <script src="general/js/forms.js"></script>
    <script type="text/javascript" src="general/js/validation.js"></script>
    <script src="general/js/jquery.dataTables.min.js"></script>
    <script src="general/js/jquery.blockui.min.js"></script>
    <!--<script type="text/javascript" src="general/lib/functions.js"></script>-->
    
</head>
<body>
    <?php include ('includes/admin/header.php'); ?>
    <section id="middle">
  	<div id="container-main">
            <div id="container">
                <div id="content">
                    <?php include ('includes/admin/breadcrumb.php'); ?>
                    <?php echo $main ?>
                    <div class="clear"></div>
                </div>
            </div>
            <?php include ('includes/admin/sidebar.php'); ?>
        </div>
        <?php include ('includes/admin/footer.php'); ?>
    </section>
    <div class="clear"></div>
</body>
</html>