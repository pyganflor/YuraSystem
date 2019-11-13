@extends('layouts.adminlte.master')

@section('titulo')
    Proyecciones de Mano de Obra
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
            <small>Mano de Obra</small>
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
                    <i class="fa fa-fw fa-refresh"></i> {!! $submenu->nombre !!}
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="input-group">
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-calendar"></i> Área
                    </div>
                    <select name="area_trabajo" id="area_trabajo" required class="form-control">
                        <option value="C">Cosecha</option>
                        <option value="V">Clasificación Verde</option>
                        {{--<option value="B">Clasificación Blanco</option>--}}
                    </select>
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-calendar"></i> Desde
                    </div>
                    <input type="number" class="form-control" id="filtro_desde" name="filtro_desde" required
                           value="{{getSemanaByDate(date('Y-m-d'))->codigo}}">
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-calendar"></i> Hasta
                    </div>
                    <input type="number" class="form-control" id="filtro_hasta" name="filtro_hasta" required
                           value="{{$semana_hasta->codigo}}">

                    <div class="input-group-btn">
                        <button type="button" class="btn btn-primary" onclick="listar_proyecciones()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body" id="div_listado_proyecciones"></div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.proyecciones.mano_obra.script')
@endsection