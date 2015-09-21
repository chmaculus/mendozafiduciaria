<div class="content-form">
    <div id="customForm" >
        <form class="grid-1" id="frmagregar"  action='creditos/front/cobros/x_enviar_archivo' method="post" enctype='multipart/form-data' target='hidden_upload'>
            <div class="title-grid"><div id="label_action">Parametros</div>Generales</div>
            <div class="content-gird">
                <div class="field_fecha">
                    <span class="">Subir Archivo</span><br/>
                    <input type="file" class="fecha" name='txtArchivo' id="txtArchivo" />
                    <select id="comboEntidad" name="comboEntidad">
                        <option value="Nacion">Nacion</option>
                        <option value="Supervielle">Supervielle</option>
                        <option value="Rapipago">Rapipago</option>
                    </select>
                    <button type='submit'>Subir</button>
                </div>
                
                    
            </div>
        </form>
        <iframe id='hidden_upload' name='hidden_upload' src='' onLoad='uploadDone("hidden_upload");'  style='width: 200px;height:50px;border:0px solid #fff'></iframe>
    </div>
    

    
    
    <div class='lista_archivos'>
        <span class="titulo-sp">ARCHIVOS RECIBIDOS</span>
        <ul class="titulo">
            <li>
                <span class='archivo-nombre'>ARCHIVO</span>
                <span class='archivo-fecha'>FECHA</span>
                <span class='mostrar-archivo'>OPCIONES</span>
            </li>
        </ul>
        <ul class="datos">
            <li>
                <span class='archivo-nombre'></span>
                <span class='archivo-fecha'></span>
                <span class='mostrar-archivo'><button onclick="mostrar_archivo();">Mostrar</button> - <button onclick="borrar_archivo();">Eliminar</button></span>
            </li>
        </ul>
    </div>
    
</div>



    <a id="inline" href="#div-mostrar-archivo"></a>

    <div style="display:none"><div id="div-mostrar-archivo"></div></div>


