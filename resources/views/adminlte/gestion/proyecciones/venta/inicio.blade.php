@extends('layouts.adminlte.master')
@section('titulo')
    Proyecciones de venta por cliente
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
                </div>
                <div class="input-group">
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-users"></i> clientes
                    </div>
                    <select class="form-control" id="id_cliente" name="id_cliente" required>
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{!! $cliente->detalle()->id_cliente !!}">{!!$cliente->detalle()->nombre !!}</option>
                        @endforeach
                    </select>
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-filter"></i> Criterio
                    </div>
                    <select name="filtro_predeterminado_criterio" id="filtro_predeterminado_criterio" class="form-control">
                        <option value="D">Dinero</option>
                        <option value="CF">Cajas fisicas</option>
                        <option value="CE">Cajas equivalentes</option>
                    </select>
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-thumbs-o-up"></i> Top
                    </div>
                    <input type="number" class="form-control top" id="filtro_predeterminado_top" name="filtro_predeterminado_top" value="10" >
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-success" title="Guardar proyección" onclick="store_proyeccion_venta()">
                            <i class="fa fa-fw fa-floppy-o"></i>
                        </button>
                        <button type="button" class="btn btn-primary" onclick="listar_proyecciones_venta_semanal()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body" id="listado_proyecciones_venta_semanal"></div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.proyecciones.venta.script')
@endsection
