@extends('layouts.adminlte.master')

@section('titulo')
    {{explode('|',getConfiguracionEmpresa()->postcocecha)[1]}}  {{--Recepción--}}
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{explode('|',getConfiguracionEmpresa()->postcocecha)[1]}}
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
                    Listado de clasificaciones
                </h3>
                <div class="btn-group pull-right">
                    <button class="btn btn-primary btn-sm" onclick="add_verde('')" onmouseover="$('#title_btn_add').html('Añadir')"
                            onmouseleave="$('#title_btn_add').html('')">
                        <i class="fa fa-fw fa-plus" style="color: #e9ecef"></i> <em id="title_btn_add"></em>
                    </button>
                    <button class="btn btn-default btn-sm" onclick="rendimiento_mesas()" onmouseover="$('#title_btn_mesas').html('Mesas')"
                            onmouseleave="$('#title_btn_mesas').html('')">
                        <i class="fa fa-fw fa-cubes"></i> <em id="title_btn_mesas"></em>
                    </button>
                    <button class="btn btn-success btn-sm" onclick="exportar_clasificaciones()"
                            onmouseover="$('#title_btn_exportar').html('Exportar')" onmouseleave="$('#title_btn_exportar').html('')">
                        <i class="fa fa-fw fa-file-excel-o" style="color: #e9ecef"></i> <em id="title_btn_exportar"></em>
                    </button>
                    <input type="checkbox" id="check_filtro_verde" style="display: none">
                </div>
                <div class="form-group pull-right" style="margin-bottom: 0; margin-right: 10px">
                    <label for="fecha_verde_search">Fecha</label>
                    <input type="date" id="fecha_verde_search" name="fecha_verde_search" onchange="buscar_listado()">
                </div>
            </div>
            <div class="box-body" id="div_content_clasificaciones">
                <div class="row">
                    <div class="col-md-12">
                        <label for="check_mandar_apertura_auto" class="pull-right" style="margin-left: 5px">Mandar automáticamente a
                            aperturas</label>
                        <input type="checkbox" id="check_mandar_apertura_auto" class="pull-right" checked>
                    </div>
                </div>
                <div id="div_listado_clasificaciones"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.gestion.postcocecha.clasificacion_verde.script')
@endsection