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
                    <form id="vicular_producto_venture_yura" name="vicular_producto_venture_yura">
                        <div class="col-lg-4">
                            <div class="">
                                <label>Empresa</label>
                                <select class="form-control" id="id_configuracion_empresa_productos" name="id_configuracion_empresa_productos" onchange="listar_productos()">
                                    @foreach ($empresas as $empresa)
                                        <option value="{{$empresa->id_configuracion_empresa}}" >
                                            {{$empresa->nombre}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="">
                                <label>Presentaciones YuraSystem</label>
                                <select class="form-control" id="presentacion_yura_system" name="presentacion_yura_system" required>
                                    <option selected disabled>Seleccione</option>
                                    @foreach ($presentaciones_yura_system as $pys)
                                        <option {{(in_array(Illuminate\Support\Str::Slug($pys->variedad->planta->id_planta."|".$pys->variedad->id_variedad."|".$pys->clasificacion_ramo->id_clasificacion_ramo."|".$pys->clasificacion_ramo->unidad_medida->id_unidad_medida."|".$pys->tallos_x_ramos."|".$pys->longitud_ramo."|".$pys->unidad_medida->id_unidad_medida."|".substr($pys->variedad->planta->nombre, 0,3).". ".$pys->variedad->nombre. " ".  $pys->clasificacion_ramo->nombre.$pys->clasificacion_ramo->unidad_medida->siglas . " ,". $pys->tallos_x_ramos ." Tallos ". $pys->longitud_ramo.$pys->unidad_medida->siglas),$productos_vinculados)) ? "style=font-weight:bold" : ""}}
                                                value="{{$pys->variedad->planta->id_planta."|".$pys->variedad->id_variedad."|".$pys->clasificacion_ramo->id_clasificacion_ramo."|".$pys->clasificacion_ramo->unidad_medida->id_unidad_medida."|".$pys->tallos_x_ramos."|".$pys->longitud_ramo."|".$pys->unidad_medida->id_unidad_medida."|".substr($pys->variedad->planta->nombre, 0,3).". ".$pys->variedad->nombre. " ".  $pys->clasificacion_ramo->nombre.$pys->clasificacion_ramo->unidad_medida->siglas . " ,". $pys->tallos_x_ramos ." Tallos ". $pys->longitud_ramo.$pys->unidad_medida->siglas}}" >
                                            {{substr($pys->variedad->planta->nombre, 0,3).". ".$pys->variedad->nombre. " ".  $pys->clasificacion_ramo->nombre.$pys->clasificacion_ramo->unidad_medida->siglas . ", ". $pys->tallos_x_ramos ." Tallos ". $pys->longitud_ramo.$pys->unidad_medida->siglas}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label>Presentaciones Venture</label>
                            <div class="input-group">
                                <select class="form-control" id="presentacion_venture" name="presentacion_venture" required>
                                    <option selected disabled>Seleccione</option>
                                    @foreach($presentacion_venture as $x => $pv)
                                        <option class="option_dinamico" value="{{$x}}">{{$pv}}</option>
                                    @endforeach
                                </select>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" type="button" onclick="vincular_productos_venture()">
                                        <i class="fa fa-floppy-o"></i> Vincular
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="div_listado_codigo_prodcutos" style="margin-top: 20px"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.configuracion_facturacion.productos_venture.script')
@endsection
