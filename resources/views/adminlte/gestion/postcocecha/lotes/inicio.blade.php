@extends('layouts.adminlte.master')

@section('titulo')
    Lotes
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Lotes
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
                    Listado de lotes
                </h3>
                <div class="form-group pull-right" style="margin: 0">
                    <select name="en_tiempo_search" id="en_tiempo_search" class="pull-right" onchange="buscar_listado()">
                        <option value="">Vencimiento</option>
                        <option value="1">En tiempo</option>
                        <option value="0">Fuera de tiempo</option>
                    </select>
                    <select name="clasificacion_ramo_search" id="clasificacion_ramo_search" onchange="buscar_listado()"
                            style="margin-right: 5px">
                        <option value="">Calibre del ramo</option>
                        @foreach(getCalibresRamo() as $calibre)
                            <option value="{{$calibre->id_clasificacion_ramo}}">{{$calibre->nombre}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="box-body" id="div_content_lotes">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-2">
                                    <select name="etapa_search" id="etapa_search" onchange="buscar_listado()" class="form-control">
                                        <option value="">Etapa</option>
                                        <option value="C">Guarde (clasificación)</option>
                                        <option value="A">Apertura</option>
                                        <option value="G">Guarde (apertura)</option>
                                        <option value="E">Empaquetado</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group input-group" style="width: 100%">
                                        <select name="variedad_search" id="variedad_search" class="form-control" onchange="buscar_listado()">
                                            <option value="">Variedad</option>
                                            @foreach($variedades as $item)
                                                <option value="{{$item->id_variedad}}">{{$item->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group input-group" style="width: 100%">
                                        <select name="unitaria_search" id="unitaria_search" class="form-control" onchange="buscar_listado()">
                                            <option value="">Calibre</option>
                                            @foreach($unitarias as $item)
                                                <option value="{{$item->id_clasificacion_unitaria}}">
                                                    {{explode('|',$item->nombre)[0]}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group input-group" style="padding: 0px">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Desde</span>
                                        <input type="date" id="fecha_desde_search" name="fecha_desde_search" class="form-control"
                                               onchange="buscar_listado()">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Hasta</span>
                                        <input type="date" id="fecha_hasta_search" name="fecha_hasta_search" class="form-control"
                                               onchange="buscar_listado()">
                                        <span class="input-group-btn">
                                </span>
                                        <span class="input-group-btn">
                                    <button class="btn btn-success" onclick="exportar_lotes()"
                                            onmouseover="$('#title_btn_exportar').html('Exportar')"
                                            onmouseleave="$('#title_btn_exportar').html('')">
                                        <i class="fa fa-fw fa-file-excel-o" style="color: #0c0c0c"></i> <em
                                                id="title_btn_exportar"></em>
                                    </button>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_lotes"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.lotes.script')
@endsection