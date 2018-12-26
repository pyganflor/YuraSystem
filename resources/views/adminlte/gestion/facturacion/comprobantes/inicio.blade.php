@extends('layouts.adminlte.master')

@section('titulo')
    Comprobantes
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
                    Administración de comprobantes de facturación
                </h3>
            </div>
            <div class="box-body" id="div_content_comprobantes">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="anno">Estado</label><br />
                                    <select class="form-control" id="estado" name="estado">
                                        <option value=""> Seleccione </option>
                                        <option value="1"> Enviado </option>
                                        <option value="0"> No enviado </option>
                                    </select>
                                    <input type="text" class="form-control" placeholder="Búsqueda" id="busqueda_comprobantes"
                                           name="busqueda_comprobantes">
                                    <span class="input-group-btn">
                                    <button class="btn btn-default" onclick="buscar_listado()"
                                            onmouseover="$('#title_btn_buscar').html('Buscar')"
                                            onmouseleave="$('#title_btn_buscar').html('')">
                                        <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                            id="title_btn_buscar"></em>
                                    </button>
                                </span>
                                    <span class="input-group-btn">
                                    <button class="btn btn-primary" onclick="add_comprobante()"
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
                <div id="div_listado_comprobantes"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.facturacion.comprobantes.script')
@endsection
