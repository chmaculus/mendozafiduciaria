<?php 
        $arr_permisos_menu =array();
        if (isset($_SESSION["USER_PERMISOS_MENU"]))
            $arr_permisos_menu = $_SESSION["USER_PERMISOS_MENU"];
        
        
        //log_this('log/yyyyyy.log',print_r($arr_permisos_menu,1));
        
        
        $is_admin = $_SESSION["USER_ROL"]==1?true:false;
        
?>

<?php if ( (isset($_SESSION["USER_MENU"]) && count($arr_permisos_menu)>0)|| $is_admin || PERMISOS_MENU_ALL): ?>
<div class="jMenucont acaaa">
    <ul id="jMenu" class="jMenu">

            <?php foreach($_SESSION["USER_MENU"] as $rs_menu): ?>
            <?php if (in_array( $rs_menu["ID"],$arr_permisos_menu) || $is_admin || PERMISOS_MENU_ALL): ?>

            <li class="col_1"><a><?php echo $rs_menu["NOMBRE"] ?></a>
                <ul>
                    <?php foreach($rs_menu["HIJOS"] as $rs_hijo): ?>
                    <?php if (in_array( $rs_hijo["ID"],$arr_permisos_menu) || $is_admin || PERMISOS_MENU_ALL): ?>
                    <li><a href="<?php echo $rs_hijo["URL"] ?>"><?php echo $rs_hijo["NOMBRE"] ?></a></li>
                    <?php endif; ?>
                   <?php endforeach; ?>
                </ul>
            </li>

            <?php endif; ?>
            <?php endforeach; ?>
    </ul>
</div>
<?php else:?>
<div class="jMenucont">
    <ul id="jMenu" class="jMenu">
        <li class="col_1"><a>Acceso Restingido</a>
            <ul>
                <li><a href="#">No existen permisos al Menu</a></li>
            </ul>
        </li>
    </ul>
</div>
<?php endif; ?>

<div class="speedbar">
    <div class="speedbar-nav"> <a href="javascript:void(0)"><?php echo "Admin" ?></a> &rsaquo; <a href="javascript:void(0)" id="etiqueta_modulo"><?php if (isset($etiqueta_modulo)) echo $etiqueta_modulo ?></a></div> 
    <div class="search">
      <form>
            <input id="word" type="text">
      </form>   
    </div>
</div>