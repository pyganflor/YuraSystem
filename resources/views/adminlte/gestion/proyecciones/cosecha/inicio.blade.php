@extends('layouts.adminlte.master')

@section('titulo')
    Proyecciones de Cosecha
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Proyecciones
            <small class="text-color_yura">Cosecha</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li class="text-color_yura">
                {{$submenu->menu->grupo_menu->nombre}}
            </li>
            <li class="text-color_yura">
                {{$submenu->menu->nombre}}
            </li>

            <li class="active">
                <a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('{{$submenu->url}}')">
                    <i class="fa fa-fw fa-refresh"></i> {!! $submenu->nombre !!}
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group input-group">
                    <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                        <i class="fa fa-fw fa-calendar"></i> Desde
                    </span>
                    <input type="number" class="form-control input-yura_default" id="filtro_predeterminado_desde"
                           name="filtro_predeterminado_desde" required
                           value="{{getSemanaByDate(date('Y-m-d'))->codigo}}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group input-group">
                    <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                        <i class="fa fa-fw fa-calendar"></i> Hasta
                    </span>
                    <input type="number" class="form-control input-yura_default" id="filtro_predeterminado_hasta"
                           name="filtro_predeterminado_hasta" required
                           value="{{$semana_hasta->codigo}}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group input-group">
                    <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </span>
                    <select name="filtro_predeterminado_planta" id="filtro_predeterminado_planta" class="form-control input-yura_default"
                            onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades', '<option value=T selected>Todos los tipos</option>')">
                        <option value="">Todas las variedades</option>
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}" {{$p->siglas == 'GYP' ? 'selected' : ''}}>{{$p->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group input-group">
                    <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                        <i class="fa fa-fw fa-leaf"></i> Tipo
                    </span>
                    <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control input-yura_default">
                        <option value="T" selected>Todos los tipos</option>
                    </select>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-yura_primary" onclick="listar_proyecciones()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>

        <div class="input-group" style="display: none">
            <div class="input-group-addon bg-gray">
                <i class="fa fa-fw fa-calendar"></i> Opciones
            </div>
            <select class="form-control" id="filtro_predeterminado_opciones" name="filtro_predeterminado_opciones" required
                    onchange="listar_proyecciones()">
                <option value="I">Plantas Iniciales</option>
                <option value="A" selected>Plantas Actuales</option>
            </select>
            <div class="input-group-addon bg-gray">
                <i class="fa fa-fw fa-calendar"></i> Detalle
            </div>
            <select class="form-control" id="filtro_predeterminado_detalle" name="filtro_predeterminado_detalle" required
                    onchange="listar_proyecciones()">
                <option value="T" selected>Tallos</option>
                <option value="C">Cajas</option>
            </select>
        </div>

        <div id="div_listado_proyecciones"></div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.proyecciones.cosecha.script')
@endsection
