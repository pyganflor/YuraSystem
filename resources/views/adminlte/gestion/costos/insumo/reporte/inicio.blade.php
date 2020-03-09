@extends('layouts.adminlte.master')

@section('titulo')
    Reporte - Insumos
@endsection

@section('script_inicio')
    <script>
        function listar_reporte(area = false, actividad = false) {
            datos = {
                area: area,
                actividad: actividad,
                desde: $('#desde').val(),
                hasta: $('#hasta').val(),
                criterio: $('#criterio').val(),
            };
            if (area != false) {
                $('.btn_actividad').removeClass('bg-blue');
                $('.btn_area').removeClass('bg-blue');
                $('#btn_area_' + area).addClass('bg-blue');
            }
            if (actividad != false) {
                $('.btn_area').removeClass('bg-blue');
                $('.btn_actividad').removeClass('bg-blue');
                $('#btn_actividad_' + actividad).addClass('bg-blue');
            }
            get_jquery('{{url('reporte_insumos/listar_reporte')}}', datos, function (retorno) {
                $('#div_content_fixed').html(retorno);
            }, 'div_content_fixed');
        }
    </script>
@endsection

@section('css_inicio')
    <style>

    </style>
@endsection

@section('contenido')
    <section class="content-header">
        <h1>
            Insumos
            <small>Reporte</small>
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
                    <div class="input-group-addon" style="background-color: #e9ecef">
                        Desde
                    </div>
                    <input type="number" id="desde" onkeypress="return isNumber(event)" class="form-control text-center"
                           value="{{$semana_desde->codigo}}">
                    <div class="input-group-addon" style="background-color: #e9ecef">
                        Hasta
                    </div>
                    <input type="number" id="hasta" onkeypress="return isNumber(event)" class="form-control text-center"
                           value="{{$semana_actual->codigo}}">
                    <div class="input-group-addon" style="background-color: #e9ecef">
                        Criterio
                    </div>
                    <select name="criterio" id="criterio" class="form-control">
                        <option value="V">Valores</option>
                        <option value="C">Cantidades</option>
                    </select>

                    <div class="input-group-btn">
                        <button type="button" class="btn btn-primary" title="OK" onclick="listar_reporte()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-2 div_content_fixed">
                        @include('adminlte.gestion.costos.mano_obra.reporte.partials.areas_actividades')
                    </div>
                    <div class="col-md-10 div_content_fixed">
                        <div id="div_content_fixed" style="overflow-x: scroll; overflow-y: scroll; max-height: 450px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    <script>
        listar_reporte();
    </script>
@endsection