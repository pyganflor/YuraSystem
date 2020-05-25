@extends('layouts.adminlte.master')

@section('titulo')
    Sectores y módulos
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Sectores y módulos
            <small class="text-color_yura">módulo de administrador</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li  class="text-color_yura">
                {{$submenu->menu->grupo_menu->nombre}}
            </li>
            <li class="text-color_yura">
                {{$submenu->menu->nombre}}
            </li>

            <li class="active">
                <a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('{{$submenu->url}}')">
                    <i class="fa fa-fw fa-refresh"></i> {{$submenu->nombre}}
                </a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Administración de los sectores y módulos</h3>
                @if(count($nuevos_ciclos) > 0)
                    <button class="btn btn-xs btn-yura_default pull-right" onclick="nuevos_ciclos()">
                        <i class="fa fa-fw fa-exclamation text-red"></i> Nuevos ciclos
                    </button>
                @endif
            </div>
            <div class="box-body" id="div_content_sectores_modulos">
                <div class="box-group" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <div class="panel box box-success">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse"  data-parent="#accordion" href="#collapse1" aria-expanded="true" class="text-color_yura">
                                    Gestión de <strong>ciclos</strong>
                                </a>
                            </h4>
                            <div class="input-group pull-right">
                                <select name="variedad_ciclos" id="variedad_ciclos" onchange="listar_ciclos()" class="border-radius_18">
                                    @foreach(getVariedades() as $var)
                                        <option value="{{$var->id_variedad}}">{{$var->nombre}}</option>
                                    @endforeach
                                </select>
                                <select name="tipo_ciclos" id="tipo_ciclos" onchange="listar_ciclos()" class="border-radius_18">
                                    <option value="1">Activos</option>
                                    <option value="0">Inactivos</option>
                                </select>
                            </div>
                        </div>
                        <div id="collapse1" class="panel-collapse collapse in" aria-expanded="true">
                            <div class="box-body" id="div_ciclos"></div>
                        </div>
                    </div>
                    <div class="panel box box-success">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" class="collapsed text-color_yura" aria-expanded="false">
                                    Ingresar nuevos <strong>sectores</strong> y/o <strong>módulos</strong>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse2" class="panel-collapse collapse" aria-expanded="false">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6" id="div_content_sectores" style="overflow-x: scroll">
                                        @include('adminlte.gestion.sectores_modulos.partials.listado_sector')
                                    </div>
                                    <div class="col-md-3" id="div_content_modulos">
                                        <table width="100%" class="table-responsive table-bordered"
                                               style="font-size: 0.8em; border-color: #9d9d9d"
                                               id="table_content_modulos">
                                            <thead>
                                            <tr>
                                                <th class="text-center th_yura_default" style="border-color: #9d9d9d">MÓDULO</th>
                                                <th class="text-center th_yura_default" style="border-color: #9d9d9d">
                                                    <button type="button" class="btn btn-xs btn-yura_default" title="Añadir Módulo"
                                                            onclick="add_modulo()">
                                                        <i class="fa fa-fw fa-plus"></i>
                                                    </button>
                                                </th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="col-md-3" id="div_content_lotes">
                                        <table width="100%" class="table-responsive table-bordered"
                                               style="font-size: 0.8em; border-color: #9d9d9d"
                                               id="table_content_lotes">
                                            <thead>
                                            <tr>
                                                <th class="text-center th_yura_default" style="border-color: #9d9d9d">LOTE</th>
                                                <th class="text-center th_yura_default" style="border-color: #9d9d9d">
                                                    <button type="button" class="btn btn-xs btn-yura_default" title="Añadir Lote"
                                                            onclick="add_lote()">
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
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.sectores_modulos.script')
@endsection