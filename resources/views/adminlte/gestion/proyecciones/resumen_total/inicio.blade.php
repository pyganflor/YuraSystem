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
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </div>
                    <select name="filtro_predeterminado_planta" id="filtro_predeterminado_planta" class="form-control planta" style="width:200px"
                            onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades', '<option value=T selected>Todos los tipos</option>')">
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}" {{$p->siglas == 'GYP' ? 'selected' : ''}}>{{$p->nombre}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-addon bg-gray" id="div_cargar_variedades">
                        <i class="fa fa-fw fa-leaf"></i> Tipo
                    </div>
                    <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control variedad"
                            style="width:200px">
                        @foreach(getPlantas()[0]->variedades as $variedad)
                            <option {{$variedad->id_variedad == "2" ? "selected" : "" }}
                                    value="{{$variedad->id_variedad}}" >{{$variedad->nombre}}</option>
                        @endforeach
                    </select>




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
                <div class="box-body" id="listado_proyecciones_resumen_total" style="width:100%;overflow-x: auto"></div>
            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.proyecciones.resumen_total.script')
@endsection
