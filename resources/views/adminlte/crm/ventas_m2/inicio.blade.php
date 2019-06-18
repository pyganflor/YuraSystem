@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard - Ventas/m2
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Ventas/m<sup>2</sup></small>
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
                    <div class="input-group-addon">
                        Ventas/m<sup>2</sup>
                    </div>
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </div>
                    <select name="filtro_predeterminado_planta_m2" id="filtro_predeterminado_planta_m2" class="form-control"
                            onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad_m2', 'div_cargar_variedades_m2', '<option value=T selected>Todos los tipos</option>')">
                        <option value="">Todas las variedades</option>
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-addon bg-gray" id="div_cargar_variedades_m2">
                        <i class="fa fa-fw fa-leaf"></i> Tipo
                    </div>
                    <select name="filtro_predeterminado_variedad_m2" id="filtro_predeterminado_variedad_m2" class="form-control"
                            onchange="filtrar_m2()">
                        <option value="T" selected>Todos los tipos</option>
                    </select>
                </div>
            </div>
            <div class="box-body" id="div_chart_ventas_m2"></div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="input-group">
                    <div class="input-group-addon">
                        Ventas/m<sup>2</sup>/año
                    </div>
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </div>
                    <select name="filtro_predeterminado_planta_m2_anno" id="filtro_predeterminado_planta_m2_anno" class="form-control"
                            onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad_m2_anno', 'div_cargar_variedades_m2_anno', '<option value=T selected>Todos los tipos</option>')">
                        <option value="">Todas las variedades</option>
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-addon bg-gray" id="div_cargar_variedades_m2_anno">
                        <i class="fa fa-fw fa-leaf"></i> Tipo
                    </div>
                    <select name="filtro_predeterminado_variedad_m2_anno" id="filtro_predeterminado_variedad_m2_anno" class="form-control"
                            onchange="filtrar_m2_anno()">
                        <option value="T" selected>Todos los tipos</option>
                    </select>
                </div>
            </div>
            <div class="box-body" id="div_chart_ventas_m2_anno"></div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.ventas_m2.script')
@endsection