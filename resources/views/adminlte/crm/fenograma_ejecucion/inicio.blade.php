@extends('layouts.adminlte.master')

@section('titulo')
    Reporte - Fenograma de Ejecución
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Reporte
            <small>Fenograma de Ejecución</small>
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
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </div>
                    <select name="filtro_predeterminado_planta" id="filtro_predeterminado_planta" class="form-control"
                            onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades', '<option value=T selected>Todos los tipos</option>')">
                        <option value="">Todas las variedades</option>
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-addon bg-gray" id="div_cargar_variedades">
                        <i class="fa fa-fw fa-leaf"></i> Tipo
                    </div>
                    <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control"
                            onchange="filtrar_ciclos()">
                        <option value="T" selected>Todos los tipos</option>
                    </select>
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-calendar"></i> Fecha
                    </div>
                    <input type="date" class="form-control" id="filtro_predeterminado_fecha" name="filtro_predeterminado_fecha" required
                           value="{{date('Y-m-d')}}" onchange="filtrar_ciclos()">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-primary" onclick="filtrar_ciclos()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body" id="div_listado_ciclos"></div>
        </div>
    </section>
@endsection

@section('script_final')

    @include('adminlte.crm.fenograma_ejecucion.script')
@endsection