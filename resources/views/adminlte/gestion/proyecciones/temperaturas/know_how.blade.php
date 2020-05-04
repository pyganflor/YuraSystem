@extends('layouts.adminlte.master')

@section('titulo')
    Temperaturas
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Temperaturas
            <small>monitoreo</small>
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
                        Sector
                    </div>
                    <select name="filtro_sector" id="filtro_sector" class="form-control">
                        <option value="T">Todos</option>
                        @foreach($sectores as $s)
                            <option value="{{$s->id_sector}}">{{$s->nombre}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </div>
                    <select name="filtro_predeterminado_planta" id="filtro_predeterminado_planta" class="form-control"
                            onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades', '<option value=T selected>Todos los tipos</option>')">
                        <option value="">Todas las variedades</option>
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}" {{$p->siglas == 'GYP' ? 'selected' : ''}}>{{$p->nombre}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-addon bg-gray" id="div_cargar_variedades">
                        <i class="fa fa-fw fa-leaf"></i> Tipo
                    </div>
                    <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control">
                        <option value="T" selected>Todos los tipos</option>
                    </select>
                    <div class="input-group-addon bg-gray">
                        P/S
                    </div>
                    <select name="filtro_poda_siembra" id="filtro_poda_siembra" class="form-control">
                        <option value="P">Poda</option>
                        <option value="S">Siembra</option>
                    </select>
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-primary" onclick="listar_ciclos()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div id="div_listado_ciclos">
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.proyecciones.temperaturas.kh_script')
@endsection
