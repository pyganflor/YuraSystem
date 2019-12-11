@extends('layouts.adminlte.master')

@section('titulo')
    Costos - Gestión
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <section class="content-header">
        <h1>
            DB
            <small>Jobs</small>
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
            <div class="box-body">
                <div id="div_listado">
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    <script>

    </script>
@endsection