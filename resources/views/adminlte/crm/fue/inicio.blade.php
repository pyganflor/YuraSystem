@extends('layouts.adminlte.master')

@section('titulo')
    Fue
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
        @include('adminlte.gestion.partials.breadcrumb')
        <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de Datos de Exportación
                </h3>
            </div>
            <div class="box-body" id="div_content_fue">
                <table width="100%">
                    <tr>
                        <td style="width: 30%"></td>
                        <td>
                            <div class="form-group input-group" style="padding: 0px">
                                <span class="input-group-addon" style="background-color: #e9ecef">
                                    <i class="fa fa-calendar" aria-hidden="true"></i> Seleccione la fecha
                                </span>
                                <input type="date" class="form-control" id="busqueda_facturas"
                                       name="busqueda_facturas" value="{{now()->toDateString()}}" onchange="buscar_facturas()">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" onclick="reporte_fue()">
                                        <i class="fa fa-file-excel-o"></i> Reporte
                                    </button>
                                </span>
                            </div>
                        </td>
                        <td style="width: 30%"></td>
                    </tr>
                </table>
                <div id="div_listado_facturas"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.crm.fue.script')
@endsection
