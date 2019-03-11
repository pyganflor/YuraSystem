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
            <li><a href="javascript:void(0)"><i class="fa fa-line-chart"></i> Dashboard</a></li>
            <li class="active">
                <a href="javascript:void(0)" onclick="location.reload()">
                    <i class="fa fa-fw fa-refresh"></i> Postcosecha
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div id="div_indicadores">
            @if($cant_verde > 0)
                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-teal-active">
                            <div class="inner">
                                <h3 class="info-box-number">
                                    {{$indicadores['cajas']}}
                                </h3>
                            </div>
                            <div class="icon">
                                <i class="fa fa-fw fa-gift"></i>
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer">
                                Cosecha cajas <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3 class="info-box-number">
                                    {{$indicadores['tallos']}}
                                </h3>
                            </div>
                            <div class="icon">
                                <i class="ion ion-leaf"></i>
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer">
                                Cosecha tallos <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3 class="info-box-number">
                                    {{$indicadores['desecho']}}
                                    <sup style="font-size: 20px">%</sup>
                                </h3>
                            </div>
                            <div class="icon">
                                <i class="ion ion-trash-a"></i>
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer">
                                Desechos <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-green-gradient">
                            <div class="inner">
                                <h3 class="info-box-number">
                                    {{$indicadores['rendimiento']}}
                                    <sup style="font-size: 0.4em">t/hr</sup>
                                </h3>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-people-outline"></i>
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer">
                                Rendimiento <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h3 class="info-box-number">
                                    {{$indicadores['calibre']}}
                                    <sup style="font-size: 0.4em">t/r</sup>
                                </h3>
                            </div>
                            <div class="icon">
                                <i class="fa fa-tint"></i>
                            </div>
                            <a href="javascript:void(0)" class="small-box-footer">
                                Calibre <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center">
                    No se ha trabajado aún el día de hoy
                </div>
            @endif
        </div>

        <div id="div_cosecha"></div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.postcocecha.script')
@endsection