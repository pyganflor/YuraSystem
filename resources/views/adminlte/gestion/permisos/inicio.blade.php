@extends('layouts.adminlte.master')

@section('titulo')
    Permisos
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Permisos
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
                <h3 class="box-title">
                    Administración de los permisos
                    <small id="texto_seleccionar_rol">seleccione un rol</small>
                </h3>
            </div>
            <div class="box-body" id="div_content_permisos">
                <div class="row">
                    <div class="col-md-3" id="div_content_roles">
                        @include('adminlte.gestion.permisos.partials.listado_rol')
                        <input type="hidden" id="rol_selected">
                    </div>
                    <div class="col-md-6" id="div_content_submenus">
                        <table width="100%" class="table table-responsive table-bordered"
                               style="font-size: 0.8em; border-color: #9d9d9d"
                               id="table_content_submenus">
                            <thead>
                            <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                                <th class="text-center" style="border-color: #9d9d9d">SUBMENÚ</th>
                            </tr>
                            </thead>
                            <tr>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    No se ha seleccionado ningún rol
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-3" id="div_content_usuarios">
                        <table width="100%" class="table table-responsive table-bordered"
                               style="font-size: 0.8em; border-color: #9d9d9d"
                               id="table_content_usuarios">
                            <thead>
                            <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                                <th class="text-center" style="border-color: #9d9d9d">USUARIOS</th>
                            </tr>
                            </thead>
                            <tr>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    No se ha seleccionado ningún rol
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.permisos.script')
@endsection