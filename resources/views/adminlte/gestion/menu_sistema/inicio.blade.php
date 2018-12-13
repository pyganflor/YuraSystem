@extends('layouts.adminlte.master')

@section('titulo')
    Menú de sistema
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Menús del sistema
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
                <h3 class="box-title">Administración de los menú del sistema</h3>
            </div>
            <div class="box-body" id="div_content_menu_sistema">
                <div class="row">
                    <div class="col-md-3" id="div_content_grupos_menu">
                        @include('adminlte.gestion.menu_sistema.partials.listado_grupo_menu')
                    </div>
                    <div class="col-md-3" id="div_content_menus">
                        <table width="100%" class="table table-responsive table-bordered"
                               style="font-size: 0.8em; border-color: #9d9d9d"
                               id="table_content_menus">
                            <thead>
                            <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                                <th class="text-center" style="border-color: #9d9d9d">MENÚ</th>
                                <th class="text-center" style="border-color: #9d9d9d">
                                    <button type="button" class="btn btn-xs btn-default" title="Añadir Menú" onclick="add_menu()">
                                        <i class="fa fa-fw fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="col-md-6" id="div_content_submenus">
                        <table width="100%" class="table table-responsive table-bordered"
                               style="font-size: 0.8em; border-color: #9d9d9d"
                               id="table_content_submenus">
                            <thead>
                            <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                                <th class="text-center" style="border-color: #9d9d9d">SUBMENÚ</th>
                                <th class="text-center" style="border-color: #9d9d9d">
                                    <button type="button" class="btn btn-xs btn-default" title="Añadir Submenú" onclick="add_submenu()">
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
    @include('adminlte.gestion.menu_sistema.script')
@endsection