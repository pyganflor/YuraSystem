@extends('layouts.adminlte.master')

@section('titulo')
    Camas y ciclos
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Camas y ciclos
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
                <li class="active"><a href="#tab_camas" data-toggle="tab" aria-expanded="true">Camas</a></li>
                <li class=""><a href="#tab_ciclos" data-toggle="tab" aria-expanded="false">Ciclos</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_camas">
                    <div class="row">
                        <div class="col-md-8 col-sm-12" id="listado_camas">
                        </div>
                        <div class="col-md-4" id="div_form_add_cama">
                            @include('adminlte.gestion.propagacion.camas_ciclos.forms.add_cama')
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="tab_ciclos">
                    ciclos
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.propagacion.camas_ciclos.script')
@endsection