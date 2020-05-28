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
            <small class="text-color_yura">Área</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li class="text-color_yura">
                {{$submenu->menu->grupo_menu->nombre}}
            </li>
            <li class="text-color_yura">
                {{$submenu->menu->nombre}}
            </li>

            <li class="active">
                <a href="javascript:void(0)" class="text-color_yura" onclick="cargar_url('{{$submenu->url}}')">
                    <i class="fa fa-fw fa-refresh"></i> {{$submenu->nombre}}
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div id="div_indicadores">
            @include('adminlte.crm.crm_area.partials.indicadores')
        </div>

        <h4 class="box-title">
            <strong>Gráficas</strong>
        </h4>
        <div style="background-color: white; padding: 10px">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group input-group">
                        <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                            <i class="fa fa-fw fa-calendar-check-o"></i> Rango
                        </span>
                        <select name="filtro_predeterminado_rango" id="filtro_predeterminado_rango" class="form-control input-yura_default"
                                onchange="filtrar_predeterminado()">
                            <option value="2">3 Meses</option>
                            <option value="3">6 Meses</option>
                            <option value="4">1 Año</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group input-group">
                        <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                            <i class="fa fa-fw fa-leaf"></i> Variedad
                        </span>
                        <select name="filtro_predeterminado_planta" id="filtro_predeterminado_planta" class="form-control input-yura_default"
                                onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades', '<option value=T selected>Todos los tipos</option>')">
                            <option value="">Todas las variedades</option>
                            @foreach(getPlantas() as $p)
                                <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group input-group">
                        <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                            <i class="fa fa-fw fa-leaf"></i> Tipo
                        </span>
                        <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control input-yura_default"
                                onchange="filtrar_predeterminado()">
                            <option value="T" selected>Todos los tipos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group input-group">
                        <span class="input-group-btn bg-yura_dark span-input-group-yura-fixed">
                            <button type="button" class="btn btn-sm btn-yura_dark dropdown-toggle bg-gray" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                <i class="fa fa-calendar-minus-o"></i> Años <span class="caret"></span>
                            </button>
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
                        </span>
                        <input type="text" class="form-control input-yura_default" placeholder="Años" id="filtro_predeterminado_annos"
                               name="filtro_predeterminado_annos" readonly>
                        <span class="input-group-btn">
                            <button type="button" id="btn_filtrar" class="btn btn-yura_primary" onclick="filtrar_predeterminado()"
                                    title="Buscar">
                                <i class="fa fa-fw fa-search"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-9" id="div_graficas"></div>
                <div class="col-md-3" id="div_today">
                    @include('adminlte.crm.crm_area.partials.today')
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