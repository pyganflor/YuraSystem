@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard - Rendimiento y Desecho
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <small>Rendimiento y Desecho</small>
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

    <section class="content">
        <div id="div_indicadores">
            @include('adminlte.crm.rendimiento_desecho.partials.indicadores')
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <strong>Gráficas</strong>
                </h3>

                <div class="input-group">
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-calendar-check-o"></i> Rango
                    </div>
                    <select name="filtro_predeterminado_rango" id="filtro_predeterminado_rango"
                            onchange="filtrar_predeterminado()" class="form-control">
                        <option value="1">1 Mes</option>
                        <option value="2">3 Meses</option>
                        <option value="3">6 Meses</option>
                        <option value="4">1 Año</option>
                    </select>

                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-filter"></i> Criterio
                    </div>
                    <select name="filtro_predeterminado_criterio" id="filtro_predeterminado_criterio"
                            onchange="filtrar_predeterminado()" class="form-control">
                        <option value="R" selected>Rendimiento</option>
                        <option value="D">Desecho</option>
                    </select>

                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </div>
                    <select name="filtro_predeterminado_planta" id="filtro_predeterminado_planta" class="form-control"
                            onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades',
                    '<option value= selected>Todos los tipos</option>')">
                        <option value="">Todas las variedades</option>
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-addon bg-gray" id="div_cargar_variedades">
                        <i class="fa fa-fw fa-leaf"></i> Tipo
                    </div>
                    <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control"
                            onchange="filtrar_predeterminado()">
                        <option value="" selected>Todos los tipos</option>
                    </select>

                    <div class="input-group-btn bg-gray">
                        <button type="button" class="btn btn-default dropdown-toggle bg-gray" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fa fa-calendar-minus-o"></i> Años
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            @foreach($annos as $a)
                                <li>
                                    <a href="javascript:void(0)" onclick="select_anno('{{$a}}')"
                                       class="li_anno" id="li_anno_{{$a}}">
                                        {{$a}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <input type="text" class="form-control" placeholder="Años" id="filtro_predeterminado_annos"
                           name="filtro_predeterminado_annos" readonly>

                    <div class="input-group-btn">
                        <button type="button" id="btn_filtrar" class="btn btn-default" onclick="filtrar_predeterminado()" title="Buscar">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-9" id="div_graficas"></div>
                    <div class="col-md-3" id="div_today">
                        @include('adminlte.crm.rendimiento_desecho.partials.today')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.rendimiento_desecho.script')
@endsection