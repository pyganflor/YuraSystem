@extends('layouts.adminlte.master')
@section('titulo')
    Ventas
@endsection
@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <div class="col-md-6 col-xs-12">
                    <h3 class="box-title">
                        Ordenar facturas
                    </h3>
                </div>
                <div class="col-md-6 col-xs-12 text-right form-inline">
                    <label>Ver facturas de: </label>
                    <select id="id_configuracion_empresa_orden_factura" name="id_configuracion_empresa_orden_factura" class=form-control onchange="listado_pedido_factura_generada()">
                        @foreach($empresas as $empresa)
                            <option value="{{$empresa->id_configuracion_empresa}}"> {{$empresa->razon_social}} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="box-body" id="div_content_orden_factura">


            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.postcocecha.orden_facturas.script')
@endsection
