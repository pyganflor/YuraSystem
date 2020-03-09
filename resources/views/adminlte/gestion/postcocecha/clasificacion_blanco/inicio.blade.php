@extends('layouts.adminlte.master')

@section('titulo')
    {{explode('|',getConfiguracionEmpresa()->postcocecha)[3]}}  {{--Apertura--}}
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{explode('|',getConfiguracionEmpresa()->postcocecha)[3]}}
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

                    <button class="btn btn-default btn-sm" onclick="rendimiento_mesas()" onmouseover="$('#title_btn_mesas').html('Mesas')"
                            onmouseleave="$('#title_btn_mesas').html('')">
                        <i class="fa fa-fw fa-cubes"></i> <em id="title_btn_mesas"></em>
                    </button>
                </h3>
                <div class="form-group pull-right" style="margin: 0">
                    <label class="psull-right label_blanco"></label>

                    <input type="date" id="fecha_blanco" name="fecha_blanco" onchange="listar_clasificacion_blanco($('#variedad_search').val())"
                           value="{{isset($blanco) ? $blanco->fecha_ingreso : date('Y-m-d')}}" class="text-center"
                           required style="margin-right: 10px">
                    <label for="variedad_search" style="margin-right: 10px">Variedad</label>
                    <select name="variedad_search" id="variedad_search" onchange="listar_clasificacion_blanco($(this).val())">
                        <option value="">Seleccione...</option>
                        @foreach($variedades as $item)
                            <option value="{{$item->id_variedad}}">
                                {{$item->planta->nombre}} - {{$item->nombre}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="box-body" id="div_content_blanco">
                <div id="div_listado_blanco">
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.gestion.postcocecha.clasificacion_blanco.script')
@endsection