<style type="text/css">
    div#line1 span#a {display:inline;}
    div#line1:hover span#a {display:none;}
    div#line1 span#b {display:none;}
    div#line1:hover span#b {display:inline;}
</style>
<?php if($carpetas_pendientes){ ?>
<?php 
    $para_aux1 = 0;
    if ($_SESSION["USER_ROL"]=='23'){
        $para_aux1 = 1;
    }
?>
<div class="notif_titulo" style="width:500px;">Nuevas notificaciones:</div>
<?php       foreach ($carpetas_pendientes as $c){ ?>
<?php 
        $clase = "link_aceptar_carpeta";
        if ($c["TESTADO"]==4)
            $clase = "link_aceptar_carpeta rechazado";
        if ($c["TESTADO"]==8)
            $clase = "link_aceptar_carpeta rechazado";
        
        if ($c["TNOTIF"]==1)
            //$clase="es_notif";
        //log_this('aaaaaaa.log',print_r($carpetas_pendientes,1));
?>
        <div>
            <?php if ($c["TAUTOR_REQ"]>0):?>
                <div class="<?php echo $clase ?>">Requerimiento <?php echo $c["TETAPA"]?> requiere su Autorización. Carpeta: <?php echo $c["ID"]?>.Te lo solicita <?php echo $c["ENVIA"] ?>.</div>
                <div data-etapa="<?php echo $c["TETAPA"]?>" data-iid="<?php echo $c["TID"]?>" data-autor_req="1" class="link_aceptar tb_si"></div>
                <div data-iid="<?php echo $c["TID"]?>" data-idope="<?php echo $c["ID"]?>" data-autor_req="1" class="link_aceptar tb_no"></div>
            <?php elseif ($c["TAUTOR"]>0):?>
                <div class="<?php echo $clase ?>">Carpeta <?php echo $c["ID"]?> requiere su Autorización</div>
                <div data-etapa="<?php echo $c["TETAPA"]?>" data-iid="<?php echo $c["TID"]?>" data-autor="1" data-para_aux1="<?php echo $para_aux1 ?>" class="link_aceptar tb_si"></div>
                <div data-iid="<?php echo $c["TID"]?>" data-idope="<?php echo $c["ID"]?>" data-autor="1" class="link_aceptar tb_no"></div>
            <?php else: ?>
                <?php if ($c["TNOTIF"]==0): ?>
                
                <!--La condicion ==22 es solo para mostrar aviso de pasadas 24hs con la carpeta-->
                <?php if ($c["TESTADO"]!=22): ?>
                    <?php if ($c["TESTADO"]!=4): ?>
                        <?php if ($c["TESTADO"]!=8): ?>
                            <div class="<?php echo $clase ?>" >Carpeta: <?php echo $c["ID"]?> (<?php echo $c["OPERATORIA"] ?>) de: <?php echo $c["CLIENTE"] ?>. <span>Te lo envia: <?php echo $c['ENVIA']?></span> </div>
                        <?php else: ?>
                            <div data-iid="<?php echo $c["TID"]?>" class="<?php echo $clase ?>" >La Carpeta: <?php echo $c["ID"]?> no fue autorizada, ahora vuelve a estar en tu Cartera1.<span></span> </div>
                            <div data-etapa="<?php echo $c["TETAPA"]?>" data-iid="<?php echo $c["TID"]?>" data-noauto="1" class="link_aceptar tb_si"></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div data-iid="<?php echo $c["TID"]?>" class="<?php echo $clase ?>" >La Carpeta: <?php echo $c["ID"]?> fue rechazada, ahora vuelve a estar en tu Cartera2.<span></span> </div>
                        <div data-etapa="<?php echo $c["TETAPA"]?>" data-iid="<?php echo $c["TID"]?>" data-rech="1" class="link_aceptar tb_si"></div>
                    <?php endif; ?>

                    <?php if ($c["TESTADO"]!=4 && $c["TESTADO"]!=8): ?>
                    <div data-etapa="<?php echo $c["TETAPA"]?>" data-iid="<?php echo $c["ID"]?>" class="link_aceptar tb_si"></div>
                    <div data-iid="<?php echo $c["ID"]?>" class="link_aceptar tb_no"></div>
                    <?php endif; ?>
                   
                    <?php elseif($c["TESTADO"]==22): ?>
  
                            <div class="<?php echo $clase ?>">Carpeta: <?php echo $c["ID"]?> (<?php echo $c["OPERATORIA"] ?>)  <span>La carpeta asignada supero el tiempo permitido en la etapa. </span> </div>
                            <div data-etapa="<?php echo $c["TETAPA"]?>" data-iid="<?php echo $c["ID"]?>" class="link_aceptar tb_leida"></div>
                    

                     <?php endif; ?>
                    <!--finaliza el aviso de las 24hs-->
                <?php elseif($c["TNOTA"]>0): //Nota ?>
                    <?php if ( $c["TESTADO"]!=4 ):?>
                    <div id="line1" class="<?php echo $clase ?>" >Te enviaron una nota.<span id="a"> Nota ID: <span id="idNoti"><?php echo $c["TNOTA"]?></span></span>
                    <span id="b"> Asunto: <span id="idNoti"><?php echo $c["ASUNTO"]?></span></span></div>
                            <div data-iid="<?php echo $c["TNOTA"]?>" data-tid="<?php echo $c["TID"]?>" data-actual="<?php echo $_SESSION["USERADM"] ?>" data-notifnota="1" class="link_aceptar tb_si"></div>
                            <div data-iid_nr="<?php echo $c["TNOTA"]?>" data-iid="<?php echo $c["ID"]?>" class="link_aceptar tb_no"></div>
                    <?php else:?>
                            <div id="line1" class="<?php echo $clase ?>" >Rechazaron la nota que enviaste.<span id="a"> Nota ID: <?php echo $c["TNOTA"]?></span> 
                            <span id="b"> Asunto: <?php echo $c["ASUNTO"]?></span></div>
                            <div data-iid="<?php echo $c["TID"]?>" data-tid="<?php echo $c["TID"]?>" data-actual="<?php echo $_SESSION["USERADM"] ?>" data-notif="1" class="link_aceptar tb_si"></div>
                             <?php endif; ?>
                    
                    
                <?php elseif($c["TOBSERVACION"]=='NOTIFICACION' && $c["TDESCRIPCION"]=='PETICION DE CONFIRMACION DE COPIA DE CONTRATO EN LEGALES'): //Notificacion de envio de copia de contrato a legales ?>
                    <div class="<?php echo $clase ?>" ><?php echo $c["TDESCRIPCION"] ?>.<span> Carpeta ID:<?php echo $c["ID"]?></span> </div>
                    <div data-iid="<?php echo $c["TID"]?>" data-tid="<?php echo $c["TID"]?>" data-notif="2" class="link_aceptar tb_si"></div>
                    <div data-iid="<?php echo $c["ID"]?>"  data-tid="<?php echo $c["TID"]?>" data-notif="2" class="link_aceptar tb_no"></div>
                <?php elseif($c["TOBSERVACION"]=='NOTIFICACION'): //Notificacion de envio de analisis a coord. de operaciones ?>
                    <div class="<?php echo $clase ?>" ><?php echo $c["TDESCRIPCION"] ?>.<span> Carpeta ID:<?php echo $c["ID"]?></span> </div>
                    <div data-iid="<?php echo $c["TID"]?>" data-tid="<?php echo $c["TID"]?>" data-notif="1" class="link_aceptar tb_si"></div>
                <?php elseif($c["TESTADO"]=='10'): //Notificacion de envio de analisis a coord. de operaciones ?>
                    <div class="<?php echo $clase ?>" >Respondieron al Requerimiento que solicitaste.<span> Carpeta <?php echo $c["ID"]?></span> </div>
                    <div data-etapa="<?php echo $c["TETAPA"]?>" data-iid="<?php echo $c["TID"]?>" data-notif="1" class="link_aceptar tb_si"></div>
                <?php elseif($c["TOBSERVACION"]=='AUTORIZACION DE REQUERIMIENTO' && $c["TETAPA_ORIGEN"]=='1' && $c["TESTADO"]=='9'): // ?>
                    <div class="<?php echo $clase ?>" >Autorizaron tu requerimiento.<span> Requerimiento ID:<?php echo $c["TETAPA"]?></span> </div>
                    <div data-iid="<?php echo $c["TID"]?>" data-tid="<?php echo $c["TID"]?>" data-notif="1" class="link_aceptar tb_si"></div>
                <?php elseif($c["TOBSERVACION"]=='RECHAZO DE REQUERIMIENTO' && $c["TETAPA_ORIGEN"]=='0' && $c["TESTADO"]=='9'): // ?>
                    <div class="link_aceptar_carpeta rechazado" >No autorizaron tu requerimiento.<span> Requerimiento ID:<?php echo $c["TETAPA"]?></span> </div>
                    <div data-iid="<?php echo $c["TID"]?>" data-tid="<?php echo $c["TID"]?>" data-notif="1" class="link_aceptar tb_si"></div>
                <?php else: //Respuesta a Requerimiento ?>
                    <div class="<?php echo $clase ?>" >Respondieron al Requerimiento que solicitaste.<span> Carpeta <?php echo $c["ID"]?></span> </div>
                    <div data-etapa="<?php echo $c["TETAPA"]?>" data-iid="<?php echo $c["TID"]?>" data-notif="1" class="link_aceptar tb_si"></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
<?php       } ?>
<?php } ?>