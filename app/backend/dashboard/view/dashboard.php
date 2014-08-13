<?php 
        $arr_permisos_menu =array();
        if (isset($_SESSION["USER_PERMISOS_MENU"]))
            $arr_permisos_menu = $_SESSION["USER_PERMISOS_MENU"];
        
        $is_admin = $_SESSION["USER_ROL"]==1?true:false;
          
?>
<!--Buttons-->

<div class="grid-1 last">
    <div class="title-grid">Accesos Directos</div>
    <div class="content-gird">
        <div class="buttons-content">
            <ul class="buttons">

                
<?php foreach($_SESSION["USER_MENU"] as $rs_menu): ?>
<?php if (in_array( $rs_menu["ID"],$arr_permisos_menu) || $is_admin || PERMISOS_MENU_ALL): ?>
<?php foreach($rs_menu["HIJOS"] as $rs_hijo): ?>
    <?php if (in_array( $rs_hijo["ID"],$arr_permisos_menu) || $is_admin || PERMISOS_MENU_ALL): ?>
        <?php $modname = explode("/",$rs_hijo["URL"]); ?>
        <?php $modname = end( $modname ); ?>
        <?php $modname = trim( $modname ); ?>
        <li><a class="button-a blue item-dash" href="<?php echo $rs_hijo["URL"] ?>"><span><?php echo $rs_hijo["NOMBRE"] ?></span></a></li>
    <?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
<?php endforeach; ?>
                
            </ul>
         <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
</div>

<!--Buttons end-->