@extends('layouts.adminlte.master')

@section('titulo')
    Notificaciones
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Notificaciones del sistema
            <small>módulo de administrador</small>
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
                <h3 class="box-title">Administración de las notificaciones del sistema</h3>
                <button class="btn btn-xs pull-right btn-primary" title="Añadir notificacion" onclick="add_notificacion()">
                    <i class="fa fa-fw fa-plus"></i>
                </button>
            </div>
            <div class="box-body">
                @include('adminlte.gestion.notificaciones.partials.listado')
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.notificaciones.script')
@endsection