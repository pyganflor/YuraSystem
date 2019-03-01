@extends('layouts.adminlte.master')

@section('titulo')
    {{explode('|',getConfiguracionEmpresa()->postcocecha)[3]}}  {{--Apertura--}}
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{explode('|',getConfiguracionEmpresa()->postcocecha)[3]}}
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
                    Empaquetado
                </h3>
                <div class="form-group pull-right" style="margin: 0">
                    <label for="variedad_search" style="margin-right: 10px">Variedad</label>
                    <select name="variedad_search" id="variedad_search" onchange="listar_clasificacion_blanco($(this).val())">
                        <option value="">Seleccione...</option>
                        @foreach($variedades as $item)
                            <option value="{{$item->id_variedad}}">
                                {{$item->planta->nombre}} - {{$item->nombre}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="box-body" id="div_content_blanco">
                <div id="div_listado_blanco"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.clasificacion_blanco.script')
@endsection