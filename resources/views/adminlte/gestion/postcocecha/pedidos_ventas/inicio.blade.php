@extends('layouts.adminlte.master')

@section('titulo')
    Pedidos
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    @include('adminlte.gestion.partials.breadcrumb')

    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de pedidos
                </h3>
                <div class="form-group pull-right" style="margin: 0">
                    {{--<label for="fecha_pedidos_search" style="margin-right: 10px">Fecha de pedidos</label>--}}
                    <select id="id_cliente" name="id_cliente" style="height: 26px;width:250px">
                        <option value="">Clientes</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->id_cliente}}"> {{$cliente->nombre}} </option>
                        @endforeach
                    </select>
                    <input type="date" name="fecha_pedidos_search" id="fecha_pedidos_search"
                               value="{{\Carbon\Carbon::now()->toDateString()}}">
                    <select id="id_configuracion_pedido" name="id_configuracion_empresa_pedido"
                                style="height: 26px">
                            <option value="" disabled selected>Ver pedidos de:</option>
                            @foreach(getConfiguracionEmpresa(null,true) as $empresa)
                                <option value="{{$empresa->id_configuracion_empresa}}">{{$empresa->nombre}}</option>
                            @endforeach
                        </select>
                    <button class="btn btn-xs btn-primary"
                        style="padding: 3px 7px;position: relative;bottom: 2px;right: 3px;"
                        onclick="listar_resumen_pedidos(document.getElementById('fecha_pedidos_search').value,
                                                        true,document.getElementById('id_configuracion_pedido').value,
                                                        document.getElementById('id_cliente').value)">
                        <i class="fa fa-search"></i>
                    </button>
                    <span class="dropdown">
                            <button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown"
                                    style="padding-top: 3px;padding-bottom: 3px;position: relative;bottom: 2px;">
                                <i class="fa fa-plus" aria-hidden="true"></i> Añadir pedidos
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li onclick="add_pedido('','','pedidos')" class="btn btn-default text-left"
                                    style="cursor:pointer;padding:5px 3px;width:100%;">
                                    <em id="title_btn_add_pedido"> Pedido</em>
                                </li>
                                <li onclick="add_pedido('', $fijo = true,'pedidos')" class="btn btn-default text-left"
                                    style="cursor:pointer;padding:5px 3px;width:100%;">
                                    <em id="title_btn_add_pedido_fijo"> Pedido fijo</em>
                                </li>
                                <li onclick="add_orden_semanal()" class="btn btn-default text-left"
                                    style="cursor:pointer;padding:5px 3px;width:100%;">
                                    <em id="title_btn_add_orden_semanal"> Flor tinturada</em>
                                </li>
                                {{--<li onclick="add_pedido_personalizado()" class="btn btn-default text-left"
                                    style="cursor:pointer;padding:5px 3px;width:100%;">
                                    <em id="title_btn_add_pedido_personalizado"> Pedido personalizado</em>
                                </li>--}}
                            </ul>
                        </span>
                </div>
            </div>
            <div class="box-body" id="div_content_pedidos">
                <div id="div_listado_blanco"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.pedidos_ventas.script')
@endsection
