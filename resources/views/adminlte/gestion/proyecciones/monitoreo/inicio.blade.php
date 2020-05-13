@extends('layouts.adminlte.master')

@section('titulo')
    Monitoreo de Ciclos
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Monitoreo
            <small class="text-color_yura">de ciclos</small>
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
            <div class="col-md-2">
                <div class="form-group input-group">
                    <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                        Sector
                    </span>
                    <select name="filtro_sector" id="filtro_sector" class="form-control input-yura_default">
                        <option value="T">Todos</option>
                        @foreach($sectores as $s)
                            <option value="{{$s->id_sector}}">{{$s->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group input-group">
                    <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                        Variedad
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
            <div class="col-md-2">
                <div class="form-group input-group">
                    <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark" id="div_cargar_variedades">
                        Tipo
                    </span>
                    <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control input-yura_default">
                        <option value="T" selected>Todos los tipos</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group input-group">
                    <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                        Tipo
                    </span>
                    <select name="filtro_poda_siembra" id="filtro_poda_siembra" class="form-control input-yura_default">
                        <option value="P">Poda</option>
                        <option value="S">Siembra</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">
                        Desde
                    </span>
                    <input type="number" id="filtro_min_semanas" onkeypress="return isNumber(event)" class="form-control input-yura_default"
                           required value="6" min="1">
                    <span class="input-group-addon span-input-group-yura-middle bg-yura_dark">
                        Semanas
                    </span>
                    <input type="number" id="filtro_num_semanas" onkeypress="return isNumber(event)" class="form-control input-yura_default"
                           required value="18" min="1">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-yura_primary" onclick="listar_ciclos()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div class="box-body" id="div_listado_ciclos"></div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.proyecciones.monitoreo.script')
@endsection
