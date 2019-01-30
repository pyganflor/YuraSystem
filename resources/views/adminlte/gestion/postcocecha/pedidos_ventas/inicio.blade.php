@extends('layouts.adminlte.master')

@section('titulo')
    Pedidos
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        @include('adminlte.gestion.partials.breadcrumb')
    </section>

    <!-- Main content -->
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
                            <div class="pull-right" style="padding: 0px">
                                <div class="form-inline">
                                    <div class="form-group">
                                        <label for="anno">Estado</label><br/>
                                        <select class="form-control" id="estado" name="estado">
                                            <option value=""> Seleccione</option>
                                            <option value="1"> Activo </option>
                                            <option value="0"> Cancelado </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="anno">Cliente</label><br/>
                                        <select class="form-control" id="id_cliente" name="id_cliente">
                                            <option value=""> Seleccione</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{{$cliente->id_cliente}}"> {{$cliente->nombre}} </option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                        <label> Desde</label><br/>
                                        <input type="date" class="form-control" id="desde" name="desde">
                                    </div>
                                    <div class="form-group">
                                        <label> Hasta</label><br/>
                                        <input type="date" class="form-control" id="hasta" name="hasta">
                                    </div>
                                    <div class="form-group">
                                        <label style="visibility: hidden;"> .</label><br/>
                                        <!--<input type="text" class="form-control" placeholder="Búsqueda" id="busqueda_pedidos"
                                             size="35"  name="busqueda_pedidos">-->
                                        <span class="">
                                    <button class="btn btn-default" onclick="buscar_listado_pedidos()"
                                            onmouseover="$('#title_btn_buscar_pedido').html('Buscar')"
                                            onmouseleave="$('#title_btn_buscar_pedido').html('')">
                                        <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar_pedido"></em>
                                    </button>
                                </span>
<<<<<<< HEAD
                                    <span class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fa fa-bars" aria-hidden="true"></i> Acciones de pedidos
                                        <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li onclick="add_pedido('','','pedidos')" class="btn btn-default text-left" style="cursor:pointer;padding:5px 3px;width:100%;">
                                                 <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i>
                                                <em id="title_btn_add_pedido"> Pedido</em>
                                            </li>
                                            <li onclick="add_pedido('', $fijo = true,'pedidos')" class="btn btn-default text-left" style="cursor:pointer;padding:5px 3px;width:100%;">
                                                <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i>
                                                <em id="title_btn_add_pedido_fijo"> Pedido fijo</em>
                                            </li>
                                            <li onclick="add_orden_semanal()" class="btn btn-default text-left" style="cursor:pointer;padding:5px 3px;width:100%;">
                                                <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i>
                                                <em id="title_btn_add_orden_semanal"> Orden semanal</em>
                                            </li>
                                        </ul>
                                    </span>
=======
                                        <span class="">
                                    <button class="btn btn-primary" onclick="add_pedido('','','pedidos')"
                                            onmouseover="$('#title_btn_add_pedido').html('Pedido')"
                                            onmouseleave="$('#title_btn_add_pedido').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em
                                                id="title_btn_add_pedido"></em>
                                    </button>
                                </span>
                                        <span class="">
                                    <button class="btn btn-success" onclick="add_pedido('', $fijo = true,'pedidos')"
                                            onmouseover="$('#title_btn_add_pedido_fijo').html('Pedido fijo')"
                                            onmouseleave="$('#title_btn_add_pedido_fijo').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em
                                                id="title_btn_add_pedido_fijo"></em>
                                    </button>
                                </span>
                                        <span class="">
                                    <button class="btn btn-info" onclick="add_orden_semanal()"
                                            onmouseover="$('#title_btn_add_orden_semanal').html('Orden semanal')"
                                            onmouseleave="$('#title_btn_add_orden_semanal').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em
                                                id="title_btn_add_orden_semanal"></em>
                                    </button>
                                </span>
                                        <span class="">
                                    <button class="btn btn-default" onclick="add_pedido_personalizado()"
                                            onmouseover="$('#title_btn_add_pedido_personalizado').html('Pedido personalizado')"
                                            onmouseleave="$('#title_btn_add_pedido_personalizado').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em
                                                id="title_btn_add_pedido_personalizado"></em>
                                    </button>
                                </span>
>>>>>>> ff80cd3d9f52ca72b85e0c5333153e1c38f10c08
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_pedidos"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.pedidos_ventas.script')
@endsection
