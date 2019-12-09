@extends('layouts.adminlte.master')

@section('titulo')
    Semaforización
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    @include('adminlte.gestion.partials.breadcrumb')
    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Indicadores
                </h3>
            </div>
            <div class="box-body" id="div_content_intervalo_indicador">
                <table class="table-striped table-bordered table-hover" width="100%" style="border: 2px solid #9d9d9d"
                       id="db_tbl_indicadores">
                    <thead>
                    <tr style="background-color: #e9ecef">
                        <th class="text-center" style="border-color: #9d9d9d">
                            Descripción
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" width="10%">
                            Valor
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" width="10%">
                            Acciones
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($indicadores as $indicador)
                        <tr>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$indicador->descripcion}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{$indicador->valor}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                <button class="btn btn-primary btn-xs" title="Agregar semaforización" onclick="add_intervalo('{{$indicador->id_indicador}}')">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.db.intervalos_indicadores.script')
@endsection
