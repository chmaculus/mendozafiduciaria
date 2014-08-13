        <!--Form-->
        <a name="anchorTarget" id="anchorTarget"></a>
        
        <div class="grid-1" id="frmagregar">
           <div class="title-grid"><div id="label_action">Agregar</div> <?php echo $etiqueta_mod ?></div>
           <div class="content-gird">
           <div class="form">
                <form target="enviar_archivo" method="post" enctype="multipart/form-data" id="customForm" >
                    <input type="hidden" id="idh" value="" />
                    <input type="hidden" id="val_ok" value="0" />
                     
                    <?php if(is_array($lst_provincias)): ?>
                    <div class="elem">
                           <label>Provincia:</label>
                           <div class="indent">
                           <select class="chzn-select" id="provincia" data-prompt-position="centerRight">
                               <option value="">Elegir Provincia</option>
                               <?php foreach($lst_provincias as $rs_prov): ?>
                               <option value="<?php echo $rs_prov["ID"] ?>"><?php echo $rs_prov["PROVINCIA"] ?></option>
                               <?php endforeach; ?>
                           </select>   
                           </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="elem">
                            <label>Localidad:</label>
                            <div class="indent formtext">
                                <input type="text" class="validate[required] medium tip-right" title="Ingrese Localidad" id="nom" data-prompt-position="centerRight"> 
                            </div>
                    </div>
                     
                     <div class="elem">
                            <div class="indent">
                              <input id="send" name="send" type="submit" class="button-a gray" value="Enviar" /> &nbsp;&nbsp;
                              <button class="button-a dark-blue" id="btnClear">Limpiar</button>  
                            </div>
                     </div>
               
                 </form>
                 <iframe name="enviar_archivo" id="enviar_archivo"></iframe>
    		 <div class="clear"> </div>
             </div>
           </div>
        </div>
        <!--Form end-->
       
        
        <div id="container">

            <div id="dynamic">
                <table cellpadding="0" cellspacing="0" border="0" class="display" id="datatable">
                    <thead>
                        <tr>
                            <th align="center" width="20%">Id</th>
                            <th width="30%">Provincia</th>
                            <th width="30%">Departamento</th>
                            <th width="20%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="dataTables_empty">Cargando Datos del servidor.</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>Provincia</th>
                            <th>Departamento</th>
                            <th>Acciones</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>