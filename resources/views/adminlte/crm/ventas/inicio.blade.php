@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard - Ventas
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
            <small>Ventas</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li><a href="javascript:void(0)"><i class="fa fa-line-chart"></i> Dashboard</a></li>
            <li class="active">
                <a href="javascript:void(0)" onclick="location.reload()">
                    <i class="fa fa-fw fa-refresh"></i> Ventas
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div id="div_indicadores">
            @include('adminlte.crm.ventas.partials.indicadores')
        </div>

        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <strong>Gráficas</strong>

                    <select name="filtro_predeterminado_rango" id="filtro_predeterminado_rango" style="height: 30px;"
                            onchange="filtrar_predeterminado(1)">
                        <option value="1">1 Mes</option>
                        <option value="2">3 Meses</option>
                        <option value="3">6 Meses</option>
                        <option value="4">1 Año</option>
                    </select>

                    <select name="filtro_predeterminado_criterio" id="filtro_predeterminado_criterio" style="height: 30px;"
                            onchange="filtrar_predeterminado(1)">
                        @foreach(getClientes() as $c)
                            <option value="{{$c->id_cliente}}">{{$c->detalle()->nombre}}</option>
                        @endforeach
                        <option value="A" selected>Acumulado</option>
                    </select>

                    <select class="select2" multiple="multiple" id="filtro_predeterminado_annos" name="filtro_predeterminado_annos"
                            data-placeholder="Años naturales" style="width: 175px; height: 35px">
                        @foreach($annos as $a)
                            <option value="{{$a->anno}}">{{$a->anno}}</option>
                        @endforeach
                    </select>

                    <button type="button" class="btn btn-sm btn-default" onclick="filtrar_predeterminado(0)">
                        <i class="fa fa-fw fa-search"></i>
                    </button>
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-9" id="div_graficas">
                    </div>
                    <div class="col-md-3" id="div_today">
                        @include('adminlte.crm.ventas.partials.today')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.ventas.script')
@endsection