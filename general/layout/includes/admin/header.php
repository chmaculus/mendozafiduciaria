<header>
    <div class="centered-head">
    <!-- Logo -->
        <h1><a href="backend/dashboard" class="logo">Admin Panel</a></h1>
    <!-- Logo end -->

    <!-- Navigation -->
    <nav>             
      <ul>
          <li id="login">
              <span id="login-trigger">
               <span id="login-triggers">
                                      <span id="user-panel-check"></span>
                                      <span id="user-panel-title">Mi Cuenta</span>
                </span>
              </span>
              <div id="login-content">
                <ul>
                    <li><a id="header_settings" href="#"><img src="general/css/img/setting.png" alt=""> <span>Settings</span></a></li>
                    <li><a href="#"><img src="general/css/img/help.png" alt=""><span>Help</span></a></li>
                    <li><a href="backend/login/logout"><img src="general/css/img/logout.png" alt=""><span>Log Out</span></a></li>
                </ul>
             </div>                     
          </li> 
      </ul>
    </nav>  

    <div class="account-name"><p><span class="welcome">Bienvenido,</span> <strong><?php echo ucwords( (isset($_SESSION["USER_NOMBRE"])?$_SESSION["USER_NOMBRE"]:'Desconocido') ) ?></strong><div class="notif"></div></p><div class="account-separator"></div></div>
    <!-- Navigation end-->
    <div id="notif_div" class="fancybox"></div>
    </div>
</header>