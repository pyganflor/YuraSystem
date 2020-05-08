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
            <table width="100%" style="margin-bottom: 0">
                <tr>
                    <td>
                        <div class="form-row">
                            <div class="col-md-4 col-sm-12 col-xs-12 mt-2 mt-md-0">
                                <div class="form-group input-group">
                                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                        <i class="fa fa-fw fa-calendar"></i>
                                    </span>
                                    <input type="date" id="fecha_ingreso_search" name="fecha_ingreso_search" required
                                           class="form-control input-yura_default text-center" onchange="buscar_listado(); buscar_cosecha()"
                                           style="width: 100% !important;">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12 col-xs-12 mt-2 mt-md-0">
                                <div class="form-group input-group">
                                    <input type="text" readonly id="datos_cosecha" name="datos_cosecha" style="width: 100% !important;"
                                           class="form-control text-center input-yura_disabled" placeholder="Cosecha">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12 col-xs-12 mt-2 mt-md-0">
                                <div class="form-group input-group" style="display: none" id="div_cosecha_x_variedad">
                                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                                        <i class="fa fa-fw fa-leaf"></i>
                                    </span>
                                    <select name="datos_cosecha_x_variedad" id="datos_cosecha_x_variedad"
                                            class="form-control input-yura_default" style="width: 100%;"></select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12 col-xs-12 mt-2 mt-md-0">
                                <div class="btn-group" style="padding: 0px; margin-bottom: 25px">
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
                <tr>
                    <td>
                        <div class="form-row">
                            <div class="col-md-2 col-sm-12 col-xs-12 mt-2 mt-md-0">
                                <div class="form-group input-group">
                                    <input type="text" readonly id="rendimiento_cosecha" name="rendimiento_cosecha" style="width: 100%"
                                           class="form-control text-center input-yura_disabled" placeholder="Rendimiento">
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-12 col-xs-12 mt-2 mt-md-0 text-right" id="html_ver_rendimiento">
                            </div>
                            <div class="col-md-8 col-sm-12 col-xs-12 mt-2 mt-md-0 text-right" id="html_filtro_tabla">
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
