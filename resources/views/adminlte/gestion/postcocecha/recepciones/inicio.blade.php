@extends('layouts.adminlte.master')

@section('titulo')
    {{explode('|',getConfiguracionEmpresa()->postcocecha)[0]}}  {{--Recepción--}}
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{explode('|',getConfiguracionEmpresa()->postcocecha)[0]}}
            <small class="text-color_yura">módulo de postcosecha</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')" class="text-color_yura">
                    <i class="fa fa-home text-color_yura"></i>
                    Inicio</a></li>
            <li class="text-color_yura">
                {{$submenu->menu->grupo_menu->nombre}}
            </li>
            <li class="text-color_yura">
                {{$submenu->menu->nombre}}
            </li>

            <li class="active">
                <a href="javascript:void(0)" onclick="cargar_url('{{$submenu->url}}')" class="text-color_yura">
                    <i class="fa fa-fw fa-refresh text-color_yura"></i> {{$submenu->nombre}}
                </a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <legend>
            Listado de ingresos
        </legend>
        <div id="div_content_recepciones">
            <table width="100%" style="margin-bottom: 5px">
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group input-group">
                                        <span class="input-group-addon span-input-group-yura">
                                            <i class="fa fa-fw fa-calendar icon-input-group-yura"></i>
                                        </span>
                                    <input type="date" id="fecha_ingreso_search" name="fecha_ingreso_search" required
                                           class="form-control input-yura_default text-right" onchange="buscar_listado(); buscar_cosecha()"
                                           style="left: 15px; z-index: 1">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input type="text" readonly id="datos_cosecha" name="datos_cosecha"
                                       class="form-control text-center input-yura_disabled" placeholder="Cosecha">
                            </div>
                            <div class="col-md-2">
                                <div class="form-group input-group">
                                        <span class="input-group-addon span-input-group-yura">
                                            <i class="fa fa-fw fa-leaf icon-input-group-yura"></i>
                                        </span>
                                    <select name="datos_cosecha_x_variedad" id="datos_cosecha_x_variedad"
                                            class="form-control input-yura_default"
                                            style="display: none; left: 23px; z-index: 1"></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <input type="text" readonly id="rendimiento_cosecha" name="rendimiento_cosecha"
                                       class="form-control text-center input-yura_disabled" placeholder="Rendimiento">
                            </div>
                            <div class="col-md-2" id="html_ver_rendimiento">
                            </div>
                            <div class="col-md-2">
                                <div class="btn-group pull-right" style="padding: 0px">
                                        <span class="input-group-btn">
                                    <button class="btn btn-primary btn-yura_primary" onclick="add_recepcion()"
                                            onmouseover="$('#title_btn_add').html('Añadir')"
                                            onmouseleave="$('#title_btn_add').html('')">
                                        <i class="fa fa-fw fa-plus"></i> <em
                                                id="title_btn_add"></em>
                                    </button>
                                </span>
                                    <span class="input-group-btn">
                                    <button class="btn btn-success btn-yura_dark" onclick="exportar_recepciones()"
                                            onmouseover="$('#title_btn_exportar').html('Exportar')"
                                            onmouseleave="$('#title_btn_exportar').html('')">
                                        <i class="fa fa-fw fa-file-excel-o"></i> <em
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
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.gestion.postcocecha.recepciones.script')
@endsection
