@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard - Postcosecha
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
            <small>Postcosecha</small>
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

    <section class="content">
        <div id="div_indicadores">
            @if($cant_verde > 0)
                <div class="row">
                    <div class="col-md-4">
                        <div class="small-box bg-teal-active">
                            <div class="inner">
                                <h3 class="info-box-number">
                                    {{number_format($indicadores['cajas'], 2)}}
                                </h3>
                                <input type="hidden" id="indicador_cajas" name="indicador_cajas" value="{{$indicadores['cajas']}}">
                            </div>
                            <div class="icon">
                                <i class="fa fa-fw fa-gift"></i>
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_cajas('{{$desde}}', '{{$hasta}}')">
                                Cosecha cajas <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3 class="info-box-number">
                                    {{number_format($indicadores['tallos'])}}
                                </h3>
                                <input type="hidden" id="indicador_tallos" name="indicador_tallos" value="{{$indicadores['tallos']}}">
                            </div>
                            <div class="icon">
                                <i class="ion ion-leaf"></i>
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_tallos('{{$desde}}', '{{$hasta}}')">
                                Tallos clasificados <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h3 class="info-box-number">
                                    {{$indicadores['calibre']}}
                                    <sup style="font-size: 0.4em">t/r</sup>
                                </h3>
                                <input type="hidden" id="indicador_calibre" name="indicador_calibre" value="{{$indicadores['calibre']}}">
                            </div>
                            <div class="icon">
                                <i class="fa fa-tint"></i>
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_calibres('{{$desde}}', '{{$hasta}}')">
                                Calibre <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center">
                    No se han encontrado resultados en los últimos 7 días
                </div>
            @endif
        </div>

        <div id="div_cosecha"></div>

        <input type="hidden" id="src_imagen_chart_cajas" name="src_imagen_chart_cajas">
        <input type="hidden" id="src_imagen_chart_tallos" name="src_imagen_chart_tallos">
        <input type="hidden" id="src_imagen_chart_calibres" name="src_imagen_chart_calibres">
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.postcocecha.script')
@endsection