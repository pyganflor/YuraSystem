@extends('layouts.adminlte.master')

@section('titulo')
    {{explode('|',getConfiguracionEmpresa()->postcocecha)[1]}}  {{--Recepción--}}
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{explode('|',getConfiguracionEmpresa()->postcocecha)[1]}}
            <small>módulo de postcocecha</small>

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
                    Listado de clasificaciones
                </h3>
                <div class="btn-group pull-right">
                    <button class="btn btn-primary btn-sm" onclick="add_verde('')"
                            onmouseover="$('#title_btn_add').html('Añadir')"
                            onmouseleave="$('#title_btn_add').html('')">
                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em
                                id="title_btn_add"></em>
                    </button>
                    <button class="btn btn-success btn-sm" onclick="exportar_clasificaciones()"
                            onmouseover="$('#title_btn_exportar').html('Exportar')"
                            onmouseleave="$('#title_btn_exportar').html('')">
                        <i class="fa fa-fw fa-file-excel-o" style="color: #0c0c0c"></i> <em
                                id="title_btn_exportar"></em>
                    </button>
                </div>
            </div>
            <div class="box-body" id="div_content_clasificaciones">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-2">
                                    <select name="anno_search" id="anno_search" class="form-control" onchange="buscar_listado()">
                                        <option value="">Año</option>
                                        @foreach($annos as $item)
                                            <option value="{{$item->anno}}">{{$item->anno}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group input-group" style="width: 100%">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Semana inicio</span>
                                        <input type="text" id="semana_desde_search" name="semana_desde_search" class="form-control"
                                               onkeypress="return isNumber(event)" maxlength="4" minlength="4" onchange="buscar_listado()">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Semana final</span>
                                        <input type="text" id="semana_hasta_search" name="semana_hasta_search" class="form-control"
                                               onkeypress="return isNumber(event)" maxlength="4" minlength="4" onchange="buscar_listado()">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group input-group">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Desde</span>
                                        <input type="date" id="fecha_desde_search" name="fecha_desde_search" class="form-control"
                                               onchange="buscar_listado()">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Hasta</span>
                                        <input type="date" id="fecha_hasta_search" name="fecha_hasta_search" class="form-control"
                                               onchange="buscar_listado()">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="row">
                    <div class="col-md-12">
                        <label for="check_mandar_apertura_auto" class="pull-right" style="margin-left: 5px">Mandar automáticamente a
                            aperturas</label>
                        <input type="checkbox" id="check_mandar_apertura_auto" class="pull-right" checked>
                    </div>
                </div>
                <div id="div_listado_clasificaciones"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.clasificacion_verde.script')
@endsection