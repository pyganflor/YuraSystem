@extends('layouts.adminlte.master')

@section('titulo')
    Semanas
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Semanas
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
                    Administración de las semanas de cultivo
                </h3>

                <div class="pull-right">
                    <select name="accion" id="accion" class="form-control" onchange="select_accion($(this).val())">
                        <option value="1">Filtrar</option>
                        <option value="2">Procesar</option>
                        <option value="3">Copiar semanas</option>
                    </select>
                </div>
            </div>
            <div class="box-body">
                <form id="form-accions">
                    <div id="div_content_form_accions"></div>
                </form>

                <br>

                <div id="div_content_semanas"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.semanas.script')
@endsection