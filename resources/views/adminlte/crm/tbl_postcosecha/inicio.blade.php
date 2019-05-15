@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard - Rendimiento y Desecho
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Tablas
            <small>Postcosecha</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li><a href="javascript:void(0)"><i class="fa fa-line-chart"></i> Tablas</a></li>
            <li class="active">
                <a href="javascript:void(0)" onclick="location.reload()">
                    <i class="fa fa-fw fa-refresh"></i> Postcosecha
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <h3 class="text-center">
            <i class="fa fa-fw fa-code"></i>
            EN DESARROLLO
            <i class="fa fa-fw fa-code"></i>
        </h3>
    </section>
@endsection

@section('script_final')

    @include('adminlte.crm.tbl_postcosecha.script')
@endsection