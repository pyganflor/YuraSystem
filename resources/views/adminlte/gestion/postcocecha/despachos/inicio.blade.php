@extends('layouts.adminlte.master')

@section('titulo')
    Despachos
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Despachos
            <small>módulo de postcosecha</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li>
                {{$submenu->menu->grupo_menu->nombre}}
            </li>
            <li>
                {{$submenu->menu->nombre}}
            </li>

            <li class="active">
                <a href="javascript:void(0)" onclick="cargar_url('{{$submenu->url}}')">
                    <i class="fa fa-fw fa-refresh"></i> {{$submenu->nombre}}
                </a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Empaquetado
                </h3>
                <div class="form-group pull-right" style="margin: 0">
                    <label for="fecha_pedidos_search" style="margin-right: 10px">Fecha de pedidos</label>
                    <input type="date" name="fecha_pedidos_search" id="fecha_pedidos_search"
                           value="{{\Carbon\Carbon::now()->toDateString()}}" onchange="listar_resumen_pedidos($(this).val())">
                    <select id="id_configuracion_empresa_despacho" name="id_configuracion_empresa_despacho"
                            style="height: 26px;" onchange="desbloquea_pedido()">
                        <option value="">Seleccionar empresa para ordenar pedidos</option>
                        @foreach($empresas as $emp)
                            <option value="{{$emp->id_configuracion_empresa}}">{{$emp->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="box-body" id="div_content_blanco">
                <div id="div_listado_blanco"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.despachos.script')
@endsection
