<div class="content-opciones">
    <div class="opt-content">
        <table>
            <tr>
                <td class="label">Bancos:</td>
                <td>
                    <select id="comboBancos" data-val="<?=$banco?>">
                        <option value="0">No Modificar</option>
                        <option value="1">Banco Nacion</option>
                        <option value="2">Banco Superville</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label">Convenio:</td>
                <td>
                    <input type="text" id="txtConvenio" value="<?=$convenio?>" />
                    
                </td>
            </tr>
        </table>
    </div>
    <div class="button-content">
        <button onclick="guardarOpciones();">Guardar</button>
    </div>

</div>