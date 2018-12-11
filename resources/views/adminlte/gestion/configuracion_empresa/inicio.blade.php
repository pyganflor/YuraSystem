@extends('layouts.adminlte.master')

@section('titulo')
    Configuración Empresa
@endsection

@section('contenido')

    @include('adminlte.gestion.partials.breadcrumb')

    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Datos principales
                </h3>
            </div>
            <div class="box-body" id="div_content_permisos">
                <div class="row">
                    <div class="col-md-12" id="div_content_form_config_empresa">
                        @include('adminlte.gestion.configuracion_empresa.forms.add_clasificacion')
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection

@section('script_final')
    @include('adminlte.gestion.configuracion_empresa.script')
@endsection