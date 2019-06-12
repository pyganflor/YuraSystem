@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard - Área
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
            <small>Área</small>
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
            @include('adminlte.crm.crm_area.partials.indicadores')
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <h4 class="box-title">
                    <strong>Gráficas</strong>
                </h4>

                <div class="input-group">
                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-calendar-check-o"></i> Rango
                    </div>
                    <select name="filtro_predeterminado_rango" id="filtro_predeterminado_rango" class="form-control"
                            onchange="filtrar_predeterminado()">
                        <option value="2">3 Meses</option>
                        <option value="3">6 Meses</option>
                        <option value="4">1 Año</option>
                    </select>

                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-filter"></i> Módulo
                    </div>
                    <select name="filtro_predeterminado_criterio" id="filtro_predeterminado_criterio" class="form-control"
                            onchange="filtrar_predeterminado()">
                        <option value="A" selected>Acumulado</option>
                        @foreach(getModulos()->sortBy('nombre') as $m)
                            <option value="{{$m->id_modulo}}">{{$m->nombre}}</option>
                        @endforeach
                    </select>

                    <div class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </div>
                    <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control"
                            onchange="filtrar_predeterminado()">
                        <option value="">Todas las variedades</option>
                        @foreach(getVariedades() as $v)
                            <option value="{{$v->id_variedad}}">{{$v->nombre}}</option>
                        @endforeach
                    </select>

                    <div class="input-group-btn bg-gray">
                        <button type="button" class="btn btn-default dropdown-toggle bg-gray" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            <i class="fa fa-calendar-minus-o"></i> Años
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            @foreach($annos as $a)
                                <li>
                                    <a href="javascript:void(0)" onclick="select_anno('{{$a->anno}}')"
                                       class="li_anno" id="li_anno_{{$a->anno}}">
                                        {{$a->anno}}
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
                        @include('adminlte.crm.crm_area.partials.today')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.crm_area.script')
@endsection