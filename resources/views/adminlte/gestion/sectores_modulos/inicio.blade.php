@extends('layouts.adminlte.master')

@section('titulo')
    Sectores y módulos
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Sectores y módulos
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
                <h3 class="box-title">Administración de los sectores y módulos</h3>
            </div>
            <div class="box-body" id="div_content_sectores_modulos">
                <div class="row">
                    <div class="col-md-6" id="div_content_sectores">
                        @include('adminlte.gestion.sectores_modulos.partials.listado_sector')
                    </div>
                    <div class="col-md-3" id="div_content_modulos">
                        <table width="100%" class="table table-responsive table-bordered"
                               style="font-size: 0.8em; border-color: #9d9d9d"
                               id="table_content_modulos">
                            <thead>
                            <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                                <th class="text-center" style="border-color: #9d9d9d">MÓDULO</th>
                                <th class="text-center" style="border-color: #9d9d9d">
                                    <button type="button" class="btn btn-xs btn-default" title="Añadir Módulo" onclick="add_modulo()">
                                        <i class="fa fa-fw fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="col-md-3" id="div_content_lotes">
                        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
                               id="table_content_lotes">
                            <thead>
                            <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                                <th class="text-center" style="border-color: #9d9d9d">LOTE</th>
                                <th class="text-center" style="border-color: #9d9d9d">
                                    <button type="button" class="btn btn-xs btn-default" title="Añadir Lote" onclick="add_lote()">
                                        <i class="fa fa-fw fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.sectores_modulos.script')
@endsection