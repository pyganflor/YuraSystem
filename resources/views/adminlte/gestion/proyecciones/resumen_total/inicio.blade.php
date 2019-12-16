@extends('layouts.adminlte.master')
@section('titulo')
    Resumen total de proyecciones
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="input-group">
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-calendar"></i> Desde
                    </div>
                    <input type="number" class="form-control desde" id="filtro_predeterminado_desde" name="filtro_predeterminado_desde"
                           style="" required value="{{getSemanaByDate(date('Y-m-d'))->codigo}}">
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-calendar"></i> Hasta
                    </div>
                    <input type="number" class="form-control hasta" id="filtro_predeterminado_hasta" name="filtro_predeterminado_hasta" required
                           value="{{$hasta->codigo}}" style="">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-primary" onclick="listar_proyecciones_resumen_total()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="box-body" id="listado_proyecciones_resumen_total"></div>
            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.proyecciones.resumen_total.script')
@endsection
