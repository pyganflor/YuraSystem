@extends('layouts.adminlte.master')

@section('titulo')
    {{explode('|',getConfiguracionEmpresa()->postcocecha)[0]}}  {{--Recepción--}}
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{explode('|',getConfiguracionEmpresa()->postcocecha)[0]}}
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
                    Listado de ingresos
                </h3>
            </div>
            <div class="box-body" id="div_content_recepciones">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-2">
                                    <select name="anno_search" id="anno_search" class="form-control">
                                        <option value="">Año</option>
                                        @foreach($annos as $item)
                                            <option value="{{$item->anno}}">{{$item->anno}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" id="fecha_ingreso_search" name="fecha_ingreso_search"
                                           class="form-control">
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group input-group" style="padding: 0px">
                                        <input type="text" class="form-control" placeholder="Búsqueda"
                                               id="busqueda_recepciones"
                                               name="busqueda_recepciones">
                                        <span class="input-group-btn">
                                    <button class="btn btn-default" onclick="buscar_listado()"
                                            onmouseover="$('#title_btn_buscar').html('Buscar')"
                                            onmouseleave="$('#title_btn_buscar').html('')">
                                        <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar"></em>
                                    </button>
                                </span>
                                        <span class="input-group-btn">
                                    <button class="btn btn-primary" onclick="add_recepcion()"
                                            onmouseover="$('#title_btn_add').html('Añadir')"
                                            onmouseleave="$('#title_btn_add').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em
                                                id="title_btn_add"></em>
                                    </button>
                                </span>
                                        <span class="input-group-btn">
                                    <button class="btn btn-success" onclick="exportar_recepciones()"
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
                <div id="div_listado_recepciones"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.recepciones.script')
@endsection