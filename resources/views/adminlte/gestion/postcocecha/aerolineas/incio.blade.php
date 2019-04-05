@extends('layouts.adminlte.master')

@section('titulo')
    Aerolinea
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de aerolíneas
                </h3>
            </div>
            <div class="box-body" id="div_content_aerolinea">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-group input-group" style="padding: 0px">
                                <input type="text" class="form-control" placeholder="Búsqueda" id="busqueda_aerolinea"
                                       name="busqueda_aerolinea">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" onclick="buscar_listado()"
                                            onmouseover="$('#title_btn_buscar').html('Buscar')"
                                            onmouseleave="$('#title_btn_buscar').html('')"  >
                                        <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em id="title_btn_buscar"></em>
                                    </button>
                                 </span>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" onclick="create_aerolinea()"
                                            onmouseover="$('#title_btn_add').html('Añadir')"
                                            onmouseleave="$('#title_btn_add').html('')">
                                          <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em id="title_btn_add"></em>
                                     </button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn btn-success" onclick="exportar_aerolinea()"
                                            onmouseover="$('#title_btn_exportar').html('Exportar')"
                                            onmouseleave="$('#title_btn_exportar').html('')">
                                        <i class="fa fa-fw fa-file-excel-o" style="color: #0c0c0c" ></i> <em id="title_btn_exportar"></em>
                                    </button>
                                </span>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_aerolinea"></div>
            </div>
        </div>
    </section>

@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.aerolineas.script')
@endsection
