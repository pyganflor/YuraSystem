@extends('layouts.adminlte.master')

@section('titulo')
    Cajas y presentaciones
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <div class="col-md-6">
                    <h3 class="box-title">
                        Cajas y presentaciones
                    </h3>
                </div>
                <div class="col-md-6 text-right">
                    <button class="btn btn-success" onclick="add_empaque()"
                            onmouseover="$('#title_btn_agregar_empaque').html('Agregar empaque')"
                            onmouseleave="$('#title_btn_agregar_empaque').html('')">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        <em id="title_btn_agregar_empaque"></em>
                    </button>
                    <button class="btn btn-primary" onclick="exportar_detalle_empaque()"
                            onmouseover="$('#title_btn_exportar_excel').html('Exportar excel de detalles de empaques')"
                            onmouseleave="$('#title_btn_exportar_excel').html('')">
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                        <em id="title_btn_exportar_excel"></em>
                    </button>
                    <button class="btn btn-success"  onclick="form_add_detalle_empaque()"
                            onmouseover="$('#title_btn_importar_excel').html('Importar Excel de detalles de empaques')"
                            onmouseleave="$('#title_btn_importar_excel').html('')">
                        <i class="fa fa-upload" aria-hidden="true"></i>
                        <em id="title_btn_importar_excel"></em>
                    </button>
                </div>

            </div>
            <div class="box-body" id="div_content_caja_presentacion">
                <div id="div_listado_empaque"></div>
            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.caja_presentacion.script')
@endsection
