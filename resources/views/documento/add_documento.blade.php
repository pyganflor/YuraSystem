<form id="form-add_documento">
    <p class="text-center">Los campos con <span class="error">*</span> son obligatorios</p>
    <table id="table_forms_documento" width="100%" class="table-responsive">
        <tr style="border-top: 1px solid #9d9d9d" id="row_documento_1">
            <td>
                <div class="form-group input-group" style="margin-top: 10px">
                    <span class="input-group-addon" style="background-color: #e9ecef">Nombre del campo <span class="error">*</span></span>
                    <input type="text" class="form-control" id="nombre_campo_1" name="nombre_campo_1" required maxlength="250"
                           placeholder="Escriba el nombre del campo">
                </div>
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">Tipo de dato <span class="error">*</span></span>
                    <select name="tipo_dato_1" id="tipo_dato_1" class="form-control" required onchange="load_input(1)">
                        <option value="int">Número entero</option>
                        <option value="float">Número decimal</option>
                        <option value="char">Caracter</option>
                        <option value="varchar">Cadena de texto</option>
                        <option value="boolean">Binario</option>
                        <option value="date">Fecha</option>
                        <option value="datetime">Fecha y hora</option>
                    </select>
                    <span class="input-group-addon" style="background-color: #e9ecef">Valor <span class="error">*</span></span>
                    <span class="input-append" id="span_input_1">
                    </span>
                </div>
                <div class="form-group input-group">
                    <span class="input-group-addon" style="background-color: #e9ecef">Descripción </span>
                    <textarea name="descripcion_1" id="descripcion_1" cols="30" rows="3" maxlength="4000" class="form-control"
                              placeholder="Escriba alguna descripción sobre la información adicional"></textarea>
                </div>
            </td>
        </tr>
    </table>
    <legend style="font-size: 0.9em">
        <a href="javascript:void(0)" onclick="add_formulario()" id="btn_add_formulario">
            <i class="fa fa-fw fa-plus"></i> Añadir formulario
        </a>
        <a href="javascript:void(0)" onclick="del_formulario()" class="error" style="display: none" id="btn_del_formulario">
            <i class="fa fa-fw fa-times"></i> Quitar formulario
        </a>
    </legend>
    <input type="hidden" id="cant_doc" value="1">
</form>

<script>
    load_input(1);

    function load_input(number) {
        datos = {
            number: number,
            input: $('#tipo_dato_' + number).val()
        };
        get_jquery('{{url('documento/load_input')}}', datos, function (retorno) {
            $('#span_input_' + number).html(retorno);
        });
    }

    function add_formulario() {
        cant_doc = $('#cant_doc').val();
        cant_doc++;
        $('#cant_doc').val(cant_doc);
        $('#table_forms_documento').append('<tr style="border-top: 1px solid #9d9d9d" id="row_documento_' + cant_doc + '">' +
            '            <td>' +
            '                <div class="form-group input-group" style="margin-top: 10px">' +
            '                    <span class="input-group-addon" style="background-color: #e9ecef">Nombre del campo <span class="error">*</span></span>' +
            '                    <input type="text" class="form-control" id="nombre_campo_' + cant_doc + '" name="nombre_campo_' + cant_doc + '" required maxlength="250"' +
            '                           placeholder="Escriba el nombre del campo">' +
            '                </div>' +
            '                <div class="form-group input-group">' +
            '                    <span class="input-group-addon" style="background-color: #e9ecef">Tipo de dato <span class="error">*</span></span>' +
            '                    <select name="tipo_dato_' + cant_doc + '" id="tipo_dato_' + cant_doc + '" class="form-control" required onchange="load_input(' + cant_doc + ')">' +
            '                        <option value="int">Número entero</option>' +
            '                        <option value="float">Número decimal</option>' +
            '                        <option value="char">Caracter</option>' +
            '                        <option value="varchar">Cadena de texto</option>' +
            '                        <option value="boolean">Binario</option>' +
            '                        <option value="date">Fecha</option>' +
            '                        <option value="datetime">Fecha y hora</option>' +
            '                    </select>' +
            '                    <span class="input-group-addon" style="background-color: #e9ecef">Valor <span class="error">*</span></span>' +
            '                    <span class="input-append" id="span_input_' + cant_doc + '">' +
            '                    </span>' +
            '                </div>' +
            '                <div class="form-group input-group">' +
            '                    <span class="input-group-addon" style="background-color: #e9ecef">Descripción </span>' +
            '                    <textarea name="descripcion_' + cant_doc + '" id="descripcion_' + cant_doc + '" cols="30" rows="3" maxlength="4000" class="form-control"' +
            '                              placeholder="Escriba alguna descripción sobre la información adicional"></textarea>' +
            '                </div>' +
            '            </td>' +
            '        </tr>');
        load_input(cant_doc);
        $('#btn_del_formulario').show();
    }

    function del_formulario() {
        cant_doc = $('#cant_doc').val();
        $('#row_documento_' + cant_doc).remove();
        cant_doc--;
        $('#cant_doc').val(cant_doc);
        $('#row_documento_' + cant_doc)
        if (cant_doc <= 1)
            $('#btn_del_formulario').hide();
    }
</script>
