@extends('layouts.adminlte.master')

@section('titulo')
    {{explode('|',getConfiguracionEmpresa()->postcocecha)[2]}}  {{--Apertura--}}
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{explode('|',getConfiguracionEmpresa()->postcocecha)[2]}}
            <small>módulo de postcosecha</small>

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
                    Stock
                </h3>
                <div class="form-group pull-right" style="margin-right: 10px; margin-bottom: 0">
                    <label for="variedad_search" style="margin-left: 10px">Variedad</label>
                    <select name="variedad_search" id="variedad_search" onchange="buscar_listado()">
                        <option value="">Variedad</option>
                        @foreach($variedades as $item)
                            <option value="{{$item->id_variedad}}">
                                {{$item->planta->nombre}} - {{$item->nombre}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group pull-right" id="div_form_group_coches" style="display: none; margin-bottom: 0">
                    <label for="check_coches">Tallos por coches</label>
                    <select name="tallos_x_coche" id="tallos_x_coche" onchange="calcular_tallos_x_coche()">
                        <option value="">Cantidad de tallos</option>
                        @foreach(getUnitarias() as $unitaria)
                            <option value="{{$unitaria->tallos_x_ramo * $unitaria->ramos_x_balde * getConfiguracionEmpresa()->baldes_x_coche}}"
                                    style="background-color: {{explode('|',$unitaria->color)[0]}}; color: {{explode('|',$unitaria->color)[1]}}">
                                {{$unitaria->tallos_x_ramo * $unitaria->ramos_x_balde * getConfiguracionEmpresa()->baldes_x_coche}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group pull-right" style="margin-right: 10px; margin-bottom: 0">
                    <label for="clasificacion_ramo_search" style="margin-right: 5px">Calibre del ramo</label>
                    <select name="clasificacion_ramo_search" id="clasificacion_ramo_search" onchange="calcularConvercion($(this).val())">
                        @foreach(getCalibresRamo() as $calibre)
                            @if($calibre->unidad_medida->tipo == 'P')
                                <option value="{{$calibre->nombre}}" {{$calibre->nombre == getCalibreRamoEstandar()->nombre ? 'selected' : ''}}>
                                    {{$calibre->nombre}}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group pull-right" style="margin-right: 10px; margin-bottom: 0">
                    <label for="check_filtro" style="margin-right: 5px" class="mouse-hand">Filtro</label>
                    <input type="checkbox" class="pull-right mouse-hand" id="check_filtro" onchange="show_hide_filtro()">
                </div>
                <div class="form-group pull-right" style="margin-right: 10px; margin-bottom: 0">
                    <label for="check_dont_verify" style="margin-right: 5px" class="mouse-hand">Sacar siempre</label>
                    <input type="checkbox" class="pull-right mouse-hand" id="check_dont_verify">
                </div>
            </div>
            <div class="box-body" id="div_content_aperturas">
                <table width="100%" id="table_filtro" style="display: none">
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group input-group" style="width: 100%">
                                        <select name="unitaria_search" id="unitaria_search" class="form-control" onchange="buscar_listado()">
                                            <option value="">Calibre</option>
                                            @foreach($unitarias as $item)
                                                <option value="{{$item->id_clasificacion_unitaria}}">
                                                    {{explode('|',$item->nombre)[0]}} {{$item->unidad_medida->siglas}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group input-group" style="padding: 0px">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Desde</span>
                                        <input type="date" id="fecha_desde_search" name="fecha_desde_search" class="form-control"
                                               onchange="buscar_listado()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group input-group" style="padding: 0px">
                                        <span class="input-group-addon" style="background-color: #e9ecef">Hasta</span>
                                        <input type="date" id="fecha_hasta_search" name="fecha_hasta_search" class="form-control"
                                               onchange="buscar_listado()">
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

                <div style="margin-bottom: 10px">
                    <span class="pull-right badge" id="html_current_sacar" title="Ramos seleccionados" style="margin-right: 5px"></span>
                    <a href="javascript:void(0)" class="badge btn-success pull-right" id="btn_sacar" title="Sacar de apertura"
                       style="display: none; margin-right: 5px" onclick="sacar_aperturas()">
                        <i class="fa fa-fw fa-share-square-o"></i> Sacar
                    </a>
                </div>

                <div id="div_listado_aperturas"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.aperturas.script')
@endsection