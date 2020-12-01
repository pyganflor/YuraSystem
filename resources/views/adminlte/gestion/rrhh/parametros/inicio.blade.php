@extends('layouts.adminlte.master')

@section('titulo')
    Parámetros
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Parámetros
            <small>módulo de rrhh</small>
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

    <section class="content">
        <div class="form-group input-group">
            <span class="input-group-addon span-input-group-yura-fixed bg-yura_dark">Tipo</span>
            <select name="tipo_parametro" id="tipo_parametro" class="form-control input-yura_default" onchange="listar_parametro()">
                <option value="">Seleccione</option>
                <option value="banco">Banco</option>
                <option value="cargos">Cargos</option>
                <option value="profesion">Profesión</option>
                <option value="tipo_rol">Tipo Rol</option>
            </select>
        </div>

        <div id="div_contenido_parametro">
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.rrhh.parametros.script')
@endsection