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
                                    <span class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fa fa-bars" aria-hidden="true"></i> Acciones de pedidos
                                        <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li onclick="add_pedido('{{$idCliente}}')" class="btn btn-default text-left" style="cursor:pointer;padding:5px 3px;width:100%;">
                                                 <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i>
                                                <em id="title_btn_add_pedido"> Pedido</em>
                                            </li>
                                            <li onclick="add_pedido('{{$idCliente}}',$fijo = true)" class="btn btn-default text-left" style="cursor:pointer;padding:5px 3px;width:100%;">
                                                <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i>
                                                <em id="title_btn_add_pedido_fijo"> Pedido fijo</em>
                                            </li>
                                            <li onclick="add_orden_semanal('{{$idCliente}}')" class="btn btn-default text-left" style="cursor:pointer;padding:5px 3px;width:100%;">
                                                <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i>
                                                <em id="title_btn_add_orden_semanal"> Orden semanal</em>
                                            </li>
                                        </ul>
                                    </span>
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

@section('script_final')
    @include('adminlte.gestion.postcocecha.pedidos.script')
@endsection

