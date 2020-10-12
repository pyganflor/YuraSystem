@extends('layouts.adminlte.master')

@section('titulo')
    Propagación - Fenograma de Ejecución
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Propagación
            <small class="text-color_yura">Fenograma de Ejecución</small>
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
        <div class="form-row">
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </span>
                    <select name="filtro_predeterminado_planta" id="filtro_predeterminado_planta" class="form-control input-yura_default"
                            onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades', '<option value=T selected>Todos los tipos</option>')">
                        <option value="">Todas las variedades</option>
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        <i class="fa fa-fw fa-leaf"></i> Tipo
                    </span>
                    <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control input-yura_default"
                            onchange="filtrar_ciclos()">
                        <option value="T" selected>Todos los tipos</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        <i class="fa fa-fw fa-calendar"></i> Fecha
                    </span>
                    <input type="date" class="form-control input-yura_default" id="filtro_predeterminado_fecha"
                           name="filtro_predeterminado_fecha" required value="{{date('Y-m-d')}}" onchange="filtrar_ciclos()">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-yura_primary" onclick="filtrar_ciclos()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="div_listado_ciclos"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')

    @include('adminlte.crm.propagacion.fenograma.script')
@endsection