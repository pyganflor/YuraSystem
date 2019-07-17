@extends('layouts.adminlte.master')

@section('titulo')
    Prodcutos venture
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    @include('adminlte.gestion.partials.breadcrumb')

    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de productos del venture
                </h3>
            </div>
            <div class="box-body" >
                <div class="row">
                    <div class="">
                        <div class="col-lg-6">
                            <div class="">
                                <label>Presentaciones YuraSystem</label>
                                <select class="form-control" id="presentacion" name="presentacion" required>
                                    <option selected disabled>Seleccione</option>
                                    @foreach ($presentaciones_yuraSystem as $pys)
                                        <option >{{substr($pys->variedad->planta->nombre, 0,3).". ".$pys->variedad->nombre. " ".  $pys->clasificacion_ramo->nombre.$pys->clasificacion_ramo->unidad_medida->siglas . " ". $pys->tallos_x_ramo ." ". $pys->longitud_ramo.$pys->unidad_medida->siglas}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label>Presentaciones Venture</label>
                            <div class="input-group">
                                <select class="form-control" id="presentacion_venture" name="presentacion_venture" required>
                                    <option selected disabled>Seleccione</option>
                                    @foreach($presentacion_venture as $x => $pv)
                                        <option value="{{$x}}">{{$pv}}</option>
                                    @endforeach
                                </select>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button">
                                        <i class="fa fa-floppy-o"></i> Vincular
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="div_listado_codigo_prodcutos" style="margin-top: 20px">
                    <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d" id="table_content_agencias_carga">
                        <thead>
                            <tr style="background-color: #dd4b39; color: white">
                                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                                    PRODUCTO
                                </th>
                                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                    style="border-color: #9d9d9d">
                                    CÓDIGO VENTURE
                                </th>
                                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                    style="border-color: #9d9d9d">
                                    OPCIONES
                                </th>
                            </tr>
                        </thead>
                        <tbody id="body_productos_viculados"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.configuracion_facturacion.productos_venture.script')
@endsection
