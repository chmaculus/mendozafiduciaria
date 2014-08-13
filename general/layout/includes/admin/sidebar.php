<?php 
        $arr_permisos_menu =array();
        if (isset($_SESSION["USER_PERMISOS_MENU"]))
            $arr_permisos_menu = $_SESSION["USER_PERMISOS_MENU"];
        
        $is_admin = $_SESSION["USER_ROL"]==1?true:false;
          
?>
<?php if ( isset($_SESSION["USER_MENU"]) || $is_admin): ?>
<aside id="sideLeft">
    <?php if(0): ?>
    <span class="categories">Modulos</span>
    <ul class="menu">
        <?php foreach($_SESSION["USER_MENU"] as $rs_menu): ?>
        <?php if (in_array( $rs_menu["ID"],$arr_permisos_menu) || $is_admin): ?>
        
        <li>
           <a href="#"><span class="four-prong icono"><?php echo $rs_menu["NOMBRE"] ?></span></a>
           <ul class="acitem">
               <?php foreach($rs_menu["HIJOS"] as $rs_hijo): ?>
               <?php if (in_array( $rs_hijo["ID"],$arr_permisos_menu) || $is_admin): ?>
                    <?php $modname = explode("/",$rs_hijo["URL"]); ?>
                    <?php $modname = end( $modname ); ?>
                    <?php $modname = trim( $modname ); ?>
                    <li><a href="<?php echo $rs_hijo["URL"] ?>"><span class="dashboard icono <?php if ($this->get_controller_name()==$modname) echo 'activemenu';?>"><?php echo $rs_hijo["NOMBRE"] ?></span></a></li>
               <?php endif; ?>
               <?php endforeach; ?>
           </ul>
        </li>
        <?php endif; ?>
        
        <?php endforeach; ?>
     </ul>
    <?php endif; ?>
</aside>
<?php endif; ?>