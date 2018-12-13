@extends('layouts.adminlte.master')

@section('titulo')
    Agencias de transporte
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de agencias de transporte
                </h3>
            </div>
            <div class="box-body" id="div_content_agencias_de_transporte">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-group input-group" style="padding: 0px">
                                <input type="text" class="form-control" placeholder="Búsqueda" id="busqueda_agencias_transporte"
                                       name="busqueda_agencias_transporte">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" onclick="buscar_listado()"
                                            onmouseover="$('#title_btn_buscar').html('Buscar')"
                                            onmouseleave="$('#title_btn_buscar').html('')"  >
                                        <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em id="title_btn_buscar"></em>
                                    </button>
                                 </span>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" onclick="create_agencia_transporte()"
                                            onmouseover="$('#title_btn_add').html('Añadir')"
                                            onmouseleave="$('#title_btn_add').html('')">
                                          <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i> <em id="title_btn_add"></em>
                                     </button>
                                </span>
                                <span class="input-group-btn">
                                    <button class="btn btn-success" onclick="exportar_agencia_transporte()"
                                            onmouseover="$('#title_btn_exportar').html('Exportar')"
                                            onmouseleave="$('#title_btn_exportar').html('')">
                                        <i class="fa fa-fw fa-file-excel-o" style="color: #0c0c0c" ></i> <em id="title_btn_exportar"></em>
                                    </button>
                                </span>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_agencia_transporte"></div>
            </div>
        </div>
    </section>

@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.agencia_transporte.script')
@endsection
