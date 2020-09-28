@extends('layouts.adminlte.master')

@section('titulo')
    Configuraciones
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Configuraciones
            <small>módulo de propagación</small>
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
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs-justified nav-justified">
                <li class="active"><a href="#tab_contenedores" data-toggle="tab" aria-expanded="true">Contenedores</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_contenedores">
                    <div class="row">
                        <div class="col-md-8 col-sm-12" id="listado_contenedores">
                        </div>
                        <div class="col-md-4" id="div_form_add_contenedor">
                            @include('adminlte.gestion.propagacion.configuraciones.forms.add_contenedor')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.propagacion.configuraciones.script')
@endsection