@extends('layouts.adminlte.master')

@section('titulo')
    Cuarto Frío
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Cuarto Frío
            <small>módulo de postcosecha</small>
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
                <h3 class="box-title">
                    Listado
                </h3>
            </div>
            <div class="box-body" id="div_content_cuarto_frio">
                @include('adminlte.gestion.postcocecha.cuarto_frio.partials.listado')
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.cuarto_frio.script')
@endsection