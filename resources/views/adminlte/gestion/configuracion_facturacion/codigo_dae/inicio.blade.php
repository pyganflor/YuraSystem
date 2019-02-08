@extends('layouts.adminlte.master')

@section('titulo')
    Código DAE
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        @include('adminlte.gestion.partials.breadcrumb')
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de códigos DAE
                </h3>
            </div>
            <div class="box-body" id="div_content_codigo_dae">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-group input-group" style="padding: 0px">
                                <input type="text" class="form-control" placeholder="Búsqueda" id="busqueda_codigo_dae"
                                       name="busqueda_codigo_dae">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" onclick="buscar_listado()"
                                            onmouseover="$('#title_btn_buscar').html('Buscar')"
                                            onmouseleave="$('#title_btn_buscar').html('')">
                                        <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                            id="title_btn_buscar"></em>
                                    </button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" onclick="add_codigo_dae()"
                                            onmouseover="$('#title_btn_add').html('Exportar paises')"
                                            onmouseleave="$('#title_btn_add').html('')">
                                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                        <em id="title_btn_add"></em>
                                    </button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn btn-success" onclick="subir_codigo_dae()"
                                            onmouseover="$('#title_btn_upload').html('Subir códigos DAE')"
                                            onmouseleave="$('#title_btn_upload').html('')">
                                        <i class="fa fa-upload" aria-hidden="true"></i>
                                        <em id="title_btn_upload"></em>
                                    </button>
                                </span>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_codigo_dae"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.configuracion_facturacion.codigo_dae.script')
@endsection
