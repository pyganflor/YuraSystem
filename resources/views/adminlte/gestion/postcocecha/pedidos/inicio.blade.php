<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">
                Administración de pedidos
            </h3>
        </div>
        <div class="box-body" id="div_content_pedidos">
            <table width="100%">
                <tr>
                    <td>
                        <div class="form-inline">
                            <div class="form-group">
                                <label for="anno">Año</label><br/>
                                <select class="form-control" id="anno" name="anno">
                                    <option value=""> Seleccione</option>
                                    @foreach($annos as $anno)
                                        <option value="{{$anno->anno}}"> {{$anno->anno}} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="Especificaciones">Especificaciones</label><br/>
                                <select class="form-control" id="id_especificaciones" name="id_especificaciones">
                                    <option value=""> Seleccione</option>
                                    @foreach($especificaciones as $especificacion)
                                        <option value="{{$especificacion->id_cliente_pedido_especificacion}}"> {{$especificacion->nombre}} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label> Desde</label><br/>
                                <input type="date" class="form-control" id="desde" name="desde">
                            </div>
                            <div class="form-group">
                                <label> Hasta</label><br/>
                                <input type="date" class="form-control" id="hasta" name="hasta">
                            </div>
                            <div class="form-group">
                                <label style="visibility: hidden;"> .</label><br/>
                                <span class="">
                                    <button class="btn btn-default" onclick="buscar_listado_pedidos('{{$idCliente}}')"
                                            onmouseover="$('#title_btn_buscar_pedido').html('Buscar')"
                                            onmouseleave="$('#title_btn_buscar_pedido').html('')">
                                        <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar_pedido"></em>
                                    </button>
                                </span>
                                <span class="">
                                    <button class="btn btn-primary" onclick="add_pedido('{{$idCliente}}')"
                                            onmouseover="$('#title_btn_add_pedido').html('Pedido')"
                                            onmouseleave="$('#title_btn_add_pedido').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em
                                                id="title_btn_add_pedido"></em>
                                    </button>
                                </span>
                                <span class="">
                                    <button class="btn btn-success" onclick="add_pedido('{{$idCliente}}',$fijo = true)"
                                            onmouseover="$('#title_btn_add_pedido_fijo').html('Pedido fijo')"
                                            onmouseleave="$('#title_btn_add_pedido_fijo').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em
                                                id="title_btn_add_pedido_fijo"></em>
                                    </button>
                                </span>
                                <span class="">
                                    <button class="btn btn-success" onclick="add_orden_semanal('{{$idCliente}}')"
                                            onmouseover="$('#title_btn_add_orden_semanal').html('Orden semanal')"
                                            onmouseleave="$('#title_btn_add_orden_semanal').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em
                                                id="title_btn_add_orden_semanal"></em>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div id="div_listado_pedidos"></div>
        </div>
    </div>
</section>
<script>
    buscar_listado_pedidos('{{$idCliente}}');

    function buscar_listado_pedidos(id_cliente) {
        $.LoadingOverlay('show');
        datos = {
            busquedaAnno: $('#anno').val(),
            id_especificaciones: $("#id_especificaciones").val(),
            desde: $("#desde").val(),
            hasta: $("#hasta").val(),
            id_cliente: id_cliente
        };
        $.get('{{url('clientes/ver_pedidos')}}', datos, function (retorno) {
            $('#div_listado_pedidos').html(retorno);
            estructura_tabla('table_content_pedidos');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function update_status_detalle_pedido(id_detalle_pedido, estado, id_cliente) {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_detalle_pedido: id_detalle_pedido,
            estado: estado,
        };
        get_jquery('{{url('clientes/actualizar_estado_pedido_detalle')}}', datos, function () {
            cerrar_modals();
            detalles_cliente(id_cliente);
            setTimeout(function () {
                cargar_opcion('div_pedidos', id_cliente, 'clientes/listar_pedidos');
            }, 1000);

        });
        $.LoadingOverlay('hide');
    }


    $(document).on("click", "#pagination_listado_pedidos .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busquedaAnno=' + $('#anno').val() +
            '&id_especificaciones=' + $('#id_especificaciones').val() +
            '&desde=' + $('#desde').val() + '&' +
            '&hasta=' + $('#hasta').val() + '&' +
            '&id_cliente=' + {{$idCliente}} +'&');
        $('#div_listado_pedidos').html($('#table_pedidos').html());
        $.get(url, function (resul) {
            //console.log(resul);
            $('#div_listado_pedidos').html(resul);
            estructura_tabla('table_content_pedidos');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });
</script>

@section('script_final')
    @include('adminlte.gestion.postcocecha.pedidos.script')
@endsection

