@extends('layouts.adminlte.master')

@section('titulo')
    Enraizamiento
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Enraizamiento
            <small>módulo de propagación</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')" class="text-color_yura"><i class="fa fa-home"></i> Inicio</a></li>
            <li class="text-color_yura">
                {{$submenu->menu->grupo_menu->nombre}}
            </li>
            <li class="text-color_yura">
                {{$submenu->menu->nombre}}
            </li>

            <li class="active">
                <a href="javascript:void(0)" onclick="cargar_url('{{$submenu->url}}')" class="text-color_yura">
                    <i class="fa fa-fw fa-refresh"></i> {{$submenu->nombre}}
                </a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8 col-sm-12">
                <div class="form-group input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        <i class="fa fa-fw fa-calendar"></i>
                    </span>
                    <input type="date" id="fecha_search" name="fecha_search" value="{{date('Y-m-d')}}"
                           class="form-control input-yura_default text-center" onchange="listar_siembras();" style="width: 100% !important;"
                           max="{{date('Y-m-d')}}">
                </div>

                <div id="listado_siembras"></div>
            </div>
            <div class="col-md-4" id="div_form_add_siembras">
                @include('adminlte.gestion.propagacion.enraizamiento.form.add_siembra')
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.propagacion.enraizamiento.script')
@endsection