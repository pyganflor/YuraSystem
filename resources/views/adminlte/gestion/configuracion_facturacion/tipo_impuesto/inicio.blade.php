@extends('layouts.adminlte.master')

@section('titulo')
    Tipos de impuestos
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
                    Administración de tipos de impuestos
                </h3>
            </div>
            <div class="box-body" id="div_content_tipo_impuesto">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="anno">Estado</label><br />
                                    <select class="form-control" id="estado" name="estado">
                                        <option value=""> Seleccione </option>
                                        <option value="1"> Activo </option>
                                        <option value="0"> Inactivo </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tipo_impuesto">Tipo Impuesto</label><br />
                                    <select class="form-control" id="codigo_impuesto" name="codigo_impuesto">
                                        <option value=""> Seleccione </option>
                                       @foreach($impuestos as $impuesto)
                                            <option value="{{$impuesto->codigo}}"> {{$impuesto->nombre}} </option>
                                       @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="margin-top: 5px;">
                                    <label for="tipo_impuesto"></label><br />
                                    <input type="text" class="form-control" placeholder="Búsqueda" id="busqueda_tipo_impuesto"
                                           name="busqueda_tipo_impuesto">
                                </div>
                                <div class="form-group" style="margin-top: 5px;">
                                    <br />
                                    <span class="">
                                        <button class="btn btn-default" onclick="buscar_listado()"
                                                onmouseover="$('#title_btn_buscar').html('Buscar')"
                                                onmouseleave="$('#title_btn_buscar').html('')">
                                            <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar"></em>
                                        </button>
                                    </span>
                                    <span class="">
                                        <button class="btn btn-primary" onclick="add_tipo_impuesto()"
                                                onmouseover="$('#title_btn_add').html('Añadir')"
                                                onmouseleave="$('#title_btn_add').html('')">
                                            <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i>
                                            <em id="title_btn_add"></em>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_tipo_impuesto"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.configuracion_facturacion.tipo_impuesto.script')
@endsection
