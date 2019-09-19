@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Bienvenido
            <small>a <a href="{{url('')}}">{{explode('//',url(''))[1]}}</a></small>
        </h1>
        <ol class="breadcrumb">
            <li class="active">
                <a href="javascript:void(0)" onclick="location.reload()">
                    <i class="fa fa-fw fa-refresh"></i> Inicio
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        @if(count(getUsuario(Session::get('id_usuario'))->rol()->getSubmenusByTipo('C')) > 0)
            <div class="box box-primary" style="background-color: #18ef152b">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="info-box mouse-hand sombra_pequeña" style="background-color: #fffb1f"
                                 onclick="location.href='{{url('ventas_m2')}}'">
                                <span class="info-box-icon"><i class="fa fa-fw fa-diamond"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text text-center" style="font-size: 1.2em">Ventas/
                                        <small>m<sup>2</sup></small>
                                        /año
                                    </strong>
                                    <span class="info-box-number text-center">
                                        [{{$venta_mensual}} / ({{$area_anual}} * 10000)] * 3

                                        <br>
                                        @if($area_anual > 0)
                                            {{number_format(round(($venta_mensual / round($area_anual * 10000, 2)), 2), 2) * 3}}
                                        @else
                                            0
                                        @endif
                                        <small>$/m<sup>2</sup>/año (4 meses)</small>
                                    </span>
                                    <span class="info-box-number text-center">
                                        @if($area_anual > 0)
                                            {{number_format(round(($venta_anual / round($area_anual * 10000, 2)), 2), 2)}}
                                        @else
                                            0
                                        @endif
                                        <small>$/m<sup>2</sup>/año (1 año)</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-green-gradient mouse-hand sombra_pequeña"
                                 onclick="location.href='{{url('crm_postcosecha')}}'">
                                <span class="info-box-icon"><i class="fa fa-fw fa-leaf"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Postcosecha</strong>
                                    <span class="info-box-number">{{$calibre}}
                                        <small>t/r calibre</small></span>
                                    <strong class="info-box-number" title="Tallos">{{number_format($tallos)}}
                                        <small>tallos</small>
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-orange mouse-hand sombra_pequeña"
                                 onclick="location.href='{{url('crm_ventas')}}'">
                                <span class="info-box-icon"><i class="fa fa-fw fa-usd"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Ventas</strong>
                                    <span class="info-box-number">{{number_format($precio_x_ramo, 2)}}
                                        <small>precio</small></span>
                                    <span class="info-box-number" title="Valor">
                                        <small>$</small>
                                        {{number_format($valor, 2)}}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-teal mouse-hand sombra_pequeña"
                                 onclick="location.href='{{url('crm_rendimiento')}}'">
                                <span class="info-box-icon"><i class="ion ion-ios-people-outline"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Rend/Desecho</strong>
                                    <span class="info-box-number">
                                        {{number_format(round(($rendimiento_desecho['blanco']['rendimiento'] + $rendimiento_desecho['verde']['rendimiento_ramos'])/2,2) , 2)}}
                                        <small>r/hr rend</small>
                                    </span>
                                    <span class="info-box-number" title="Desecho">
                                        {{number_format(round(($rendimiento_desecho['blanco']['desecho'] + $rendimiento_desecho['verde']['desecho'])/2,2) , 2)}}
                                        <small>% desecho</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-fuchsia mouse-hand sombra_pequeña"
                                 onclick="location.href='{{url('crm_area')}}'">
                                <span class="info-box-icon"><i class="fa fa-fw fa-cube"></i></span>
                                <div class="info-box-content">
                                    <strong class="info-box-text" style="font-size: 1.2em">Área</strong>
                                    <span class="info-box-number">
                                        {{number_format(round($area['area'] / 10000, 2), 2)}}
                                        <small> <sup>ha</sup></small>
                                    </span>
                                    <span class="info-box-number" title="Ramos/m2">
                                        {{number_format($area['ramos_anno'], 2)}}
                                        <small>r/m<sup>2</sup>/año</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    <script>
        notificar('Bienvenid@ {{explode(' ',getUsuario(Session::get('id_usuario'))->nombre_completo)[0]}}',
            '{{url('')}}', function () {
            }, null, false);
    </script>
@endsection
