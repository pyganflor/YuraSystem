@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard - Ventas/m2
@endsection

@section('script_inicio')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Ventas/m<sup>2</sup></small>
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
        <div class="row">
            <div class="col-md-4">
                @include('adminlte.crm.ventas_m2.partials.variedades')
            </div>
            <div class="col-md-8">
                @include('adminlte.crm.ventas_m2.partials.graficas')
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.crm.ventas_m2.script')
@endsection