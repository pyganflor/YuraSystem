@extends('layouts.adminlte.master')

@section('titulo')
    Transportista
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de Transportistas
                </h3>
            </div>
            <div class="box-body" id="div_content_transportista">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="estado">Estado</label><br />
                                    <select class="form-control" id="estado" name="estado">
                                        <option value=""> Seleccione </option>
                                        <option value="1"> Activo </option>
                                        <option value="0"> No activo </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label style="visibility: hidden;"> .</label><br />
                                    <input type="text" class="form-control" placeholder="Búsqueda" id="busqueda_transportista"
                                           name="busqueda_transportista">
                                    <span class="">
                                        <button class="btn btn-default" onclick="buscar_listado_transportista()"
                                                onmouseover="$('#title_btn_buscar_transportista').html('Buscar')"
                                                onmouseleave="$('#title_btn_buscar_transportista').html('')">
                                            <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar_transportista"></em>
                                        </button>
                                    </span>
                                    <span class="">
                                    <button class="btn btn-primary" onclick="add_transportista()"
                                            onmouseover="$('#title_btn_add_transportista').html('Añadir')"
                                            onmouseleave="$('#title_btn_add_transportista').html('')">
                                        <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i>
                                        <em id="title_btn_add_transportista"></em>
                                    </button>
                                </span>
                                    <span class="">
                                        <button class="btn btn-success" onclick="exportar_trasportistas()"
                                                onmouseover="$('#title_btn_exportar_transportista').html('Exportar a excel')"
                                                onmouseleave="$('#title_btn_exportar_transportista').html('')">
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                            <em id="title_btn_exportar_transportista"></em>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_transportista" style="margin-top: 20px"></div>
            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.postcocecha.transportistas.script')
@endsection
