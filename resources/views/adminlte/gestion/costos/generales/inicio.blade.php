@extends('layouts.adminlte.master')

@section('titulo')
    Costos - Generales
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('css_inicio')
@endsection

@section('contenido')
    <section class="content-header">
        <h1>
            Generales
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

                    <div class="input-group-btn">
                        <button type="button" class="btn btn-primary" title="OK" onclick="listar_reporte()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div style="overflow-x: scroll" id="div_reporte"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    <script>
        listar_reporte();

        function listar_reporte() {
            datos = {
                desde: $('#desde').val(),
                hasta: $('#hasta').val(),
            };

            get_jquery('{{url('costos_generales/listar_reporte')}}', datos, function (retorno) {
                $('#div_reporte').html(retorno);
            }, 'div_reporte');
        }
    </script>
@endsection