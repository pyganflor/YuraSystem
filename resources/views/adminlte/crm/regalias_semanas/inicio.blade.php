@extends('layouts.adminlte.master')

@section('titulo')
    Reporte - Regalías Semanas
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
            <small>Regalías Semanas</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" onclick="cargar_url('')"><i class="fa fa-home"></i> Inicio</a></li>
            <li><a href="javascript:void(0)"><i class="fa fa-line-chart"></i> Dashboard</a></li>
            <li class="active">
                <a href="javascript:void(0)" onclick="location.reload()">
                    <i class="fa fa-fw fa-refresh"></i> Regalías Semanas
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="input-group">
                    <span class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-calendar-minus-o"></i> Desde
                    </span>
                    <input type="text" onkeypress="return isNumber(event)" id="desde" maxlength="4" minlength="4"
                           value="{{getSemanaByDate(date('Y-m-d'))->codigo}}" class="form-control" required>
                    <span class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-calendar-plus-o"></i> Hasta
                    </span>
                    <input type="text" onkeypress="return isNumber(event)" id="hasta" maxlength="4" minlength="4"
                           value="{{getSemanaByDate(date('Y-m-d'))->codigo}}" class="form-control" required>
                    <span class="input-group-addon bg-gray">
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </span>
                    <select class="form-control" id="variedad">
                        <option value="T">Todas</option>
                        @foreach(getVariedades() as $item)
                            <option value="{{$item->id_variedad}}">{{$item->nombre}}</option>
                        @endforeach
                    </select>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" title="Buscar" onclick="buscar_listado()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
            <div class="box-body" id="div_listado">

            </div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.regalias_semanas.script')
@endsection