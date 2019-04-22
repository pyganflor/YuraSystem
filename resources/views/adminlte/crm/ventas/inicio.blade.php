@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard - Ventas
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
            <small>Ventas</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li><a href="javascript:void(0)"><i class="fa fa-line-chart"></i> Dashboard</a></li>
            <li class="active">
                <a href="javascript:void(0)" onclick="location.reload()">
                    <i class="fa fa-fw fa-refresh"></i> Ventas
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div id="div_indicadores">
            @include('adminlte.crm.ventas.partials.indicadores')
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Gráficas
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-9" id="div_graficas">
                        @include('adminlte.crm.ventas.partials.graficas')
                    </div>
                    <div class="col-md-3" id="div_today">
                        @include('adminlte.crm.ventas.partials.today')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.ventas.script')
@endsection