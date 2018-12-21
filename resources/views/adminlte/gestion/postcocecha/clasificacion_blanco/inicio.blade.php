@extends('layouts.adminlte.master')

@section('titulo')
    {{explode('|',getConfiguracionEmpresa()->postcocecha)[3]}}  {{--Apertura--}}
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{explode('|',getConfiguracionEmpresa()->postcocecha)[3]}}
            <small>módulo de postcocecha</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li>
                {{$submenu->menu->grupo_menu->nombre}}
            </li>
            <li>
                {{$submenu->menu->nombre}}
            </li>

            <li class="active">
                <a href="javascript:void(0)" onclick="cargar_url('{{$submenu->url}}')">
                    <i class="fa fa-fw fa-refresh"></i> {{$submenu->nombre}}
                </a>
            </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Stock en frío
                </h3>
                <div class="form-group pull-right" style="margin: 0">
                    <label for="clasificacion_ramo_search" style="margin-right: 10px">Calibre del ramo</label>
                    <select name="clasificacion_ramo_search" id="clasificacion_ramo_search" onchange="calcularConvercion($(this).val())">
                        @foreach(getCalibresRamo() as $calibre)
                            @if($calibre->unidad_medida->tipo == 'P')
                                <option value="{{$calibre->nombre}}">{{$calibre->nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="box-body" id="div_content_aperturas">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group input-group" style="width: 100%">
                                        <select name="variedad_search" id="variedad_search" class="form-control" onchange="buscar_stock()">
                                            <option value="">Variedad</option>
                                            @foreach($variedades as $item)
                                                <option value="{{$item->id_variedad}}">
                                                    {{$item->planta->nombre}} - {{$item->nombre}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group input-group" style="width: 100%">
                                        <select name="unitaria_search" id="unitaria_search" class="form-control" onchange="buscar_stock()">
                                            <option value="">Calibre</option>
                                            @foreach($unitarias as $item)
                                                <option value="{{$item->id_clasificacion_unitaria}}">
                                                    {{explode('|',$item->nombre)[0]}} {{$item->unidad_medida->siglas}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group input-group" style="padding: 0px">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Fecha de pedidos</span>
                                        <input type="date" id="fecha_desde_search" name="fecha_desde_search" class="form-control"
                                               onchange="buscar_pedidos()">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_aperturas"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.clasificacion_blanco.script')
@endsection