<form id="form-add_especificacion" method="POST" action="{{url('clientes/store_especificacion')}}">
    @csrf
    <div class="row">
        <div class="col-md-5">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Nombre</span>
                <input type="text" id="nombre" name="nombre" class="form-control" maxlength="250" placeholder="Escriba el nombre">
            </div>
            @if(!isset($cliente))
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Clientes</span>
                <select id="cliente" name="cliente" class="form-control" onchange="asignar_cliente(this.id)">
                    @foreach($clientes as $c)
                        <option value="{{$c->id_cliente}}">{{$c->nombre}}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
        <div class="col-md-7">
            <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef">Descripción</span>
                <textarea id="descripcion" name="descripcion" class="form-control" maxlength="4000"
                          placeholder="Escriba la descripción"></textarea>
            </div>
        </div>
    </div>

    <legend style="font-size: 1.3em">
        Detalles
        <a href="javascript:void(0)" class="btn btn-xs btn-danger pull-right" title="Añadir detalle" onclick="del_detalle_especificacion()"
           id="btn_del_detalles" style="display: none">
            <i class="fa fa-fw fa-trash"></i>
        </a>
        <a href="javascript:void(0)" class="btn btn-xs btn-primary pull-right" title="Añadir detalle" onclick="add_detalle_especificacion()"
           id="btn_add_detalles">
            <i class="fa fa-fw fa-plus"></i>
        </a>
    </legend>
    <table width="100%" id="table_forms_especificaciones">
    </table>
    <input type="hidden" id="cant_forms_detalles" name="cant_forms_detalles" value="0">
    <input type="hidden" id="id_cliente" name="id_cliente" value="{{isset($cliente) ? $cliente->id_cliente : ""}}">
</form>

<div class="text-center">
    <button type="button" class="btn btn-success" id="btn_store_especificacion" onclick="store_especificacion({{isset($cliente) ? true : false}})">
        <i class="fa fa-fw fa-save"></i> Guardar
    </button>
</div>
<script>
    add_detalle_especificacion();

    function add_detalle_especificacion() {
        cant_detalles = $('#cant_forms_detalles').val();
        cant_detalles++;
        $('#cant_forms_detalles').val(cant_detalles);

        datos = {
            cant_detalles: cant_detalles,
        };

        get_jquery('{{url('clientes/cargar_form_especificacion_empaque')}}', datos, function (retorno) {
            $('#table_forms_especificaciones').append('<tr id="row_detalle_especificacion_' + cant_detalles + '">' +
                '   <td>' +
                '       <div id="div_detalle_especificacion_' + cant_detalles + '" class="well sombra_estandar"></div>' +
                '   </td>' +
                '</tr>');
            $('#div_detalle_especificacion_' + cant_detalles).html(retorno);
            add_desgloses_especificacion(cant_detalles);
            if (cant_detalles > 1)
                $('#btn_del_detalles').show();
        });
    }

    function del_detalle_especificacion() {
        cant_detalles = $('#cant_forms_detalles').val();
        if (cant_detalles > 1) {
            $('#row_detalle_especificacion_' + cant_detalles).remove();
            cant_detalles--;
            $('#cant_forms_detalles').val(cant_detalles);
        }

        if (cant_detalles <= 1)
            $('#btn_del_detalles').hide();
    }

    function add_desgloses_especificacion(pos) {
        pos_form_detalles = $('#pos_form_detalles_' + pos).val();

        cant_desgloses = $('#cant_forms_desgloses_' + pos_form_detalles).val();
        cant_desgloses++;
        $('#cant_forms_desgloses_' + pos_form_detalles).val(cant_desgloses);

        datos = {
            pos_form_detalles: pos_form_detalles,
            cant_desgloses: cant_desgloses,
        };

        get_jquery('{{url('clientes/cargar_form_detalle_especificacion_empaque')}}', datos, function (retorno) {
            $('#table_forms_especificaciones_desgloses_' + pos_form_detalles).append('<tr id="row_desglose_especificacion_' + pos_form_detalles + '_' + cant_desgloses + '">' +
                '   <td style="padding: 3px">' +
                '       <div id="div_desglose_especificacion_' + pos_form_detalles + '_' + cant_desgloses + '" class="well"' +
                '       style="border: 1px solid #9d9d9d"></div>' +
                '   </td>' +
                '</tr>');
            $('#div_desglose_especificacion_' + pos_form_detalles + '_' + cant_desgloses).html(retorno);
            if (cant_desgloses > 1)
                $('#btn_del_desgloses_' + pos_form_detalles).show();
        });
    }

    function del_desgloses_especificacion(pos) {
        pos_form_detalles = $('#pos_form_detalles_' + pos).val();

        cant_desgloses = $('#cant_forms_desgloses_' + pos_form_detalles).val();
        if (cant_desgloses > 1) {
            $('#row_desglose_especificacion_' + pos_form_detalles + '_' + cant_desgloses).remove();
            cant_desgloses--;
            $('#cant_forms_desgloses_' + pos_form_detalles).val(cant_desgloses);
        }
        if (cant_desgloses <= 1)
            $('#btn_del_desgloses_' + pos_form_detalles).hide();
    }

    function store_especificacion(cliente) {
        if ($('#form-add_especificacion').valid()) {
            $("#btn_store_especificacion").attr('disabled',true);
            formulario = $('#form-add_especificacion');
            var formData = new FormData(formulario[0]);
            //hacemos la petición ajax
            $.ajax({
                url: formulario.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                //necesario para subir archivos via ajax
                cache: false,
                contentType: false,
                processData: false,

                success: function (retorno2) {
                    if (retorno2.success) {
                        alerta_accion(retorno2.mensaje, function () {
                            cerrar_modals();
                           if(cliente) {
                               detalles_cliente($('#id_cliente').val());
                               admin_especificaciones($('#id_cliente').val());
                           }else{
                               buscar_listado_especificaciones();
                           }
                        });
                    } else {
                        alerta(retorno2.mensaje);
                    }
                    $.LoadingOverlay('hide');
                    $("#btn_store_especificacion").attr('disabled',false);
                },
                //si ha ocurrido un error
                error: function (retorno2) {
                    console.log(retorno2);
                    alerta(retorno2.responseText);
                    alert('Hubo un problema en la envío de la información');
                    $.LoadingOverlay('hide');
                    $("#btn_store_especificacion").attr('disabled',false);
                }
            });
        }
    }

    function asignar_cliente(id) {
        $("#id_cliente").val($("#"+id).val());
    }

    $(window).ready(function () {
        console.log($("#cliente_id").length == 1);
        if($("#cliente_id").length == 1)
        $("#id_cliente").val($("#cliente").val());
    });


    function activar_input(input){
        $("#"+input.id).attr('readonly',false);
    }
</script>
