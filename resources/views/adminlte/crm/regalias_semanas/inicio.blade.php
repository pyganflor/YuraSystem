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
            Reporte
            <small class="text-color_yura">Regalías Semanas</small>
        </h1>

    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        <i class="fa fa-fw fa-calendar-minus-o"></i> Desde
                    </span>
                    <input type="number" onkeypress="return isNumber(event)" id="desde" maxlength="4" minlength="4"
                           value="{{$semana_desde->codigo}}" class="form-control input-yura_default" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        <i class="fa fa-fw fa-calendar-plus-o"></i> Hasta
                    </span>
                    <input type="number" onkeypress="return isNumber(event)" id="hasta" maxlength="4" minlength="4"
                           value="{{$semana_actual->codigo}}" class="form-control input-yura_default" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group input-group">
                    <span class="input-group-addon bg-yura_dark span-input-group-yura-fixed">
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </span>
                    <select class="form-control input-yura_default" id="variedad">
                        <option value="T">Todas</option>
                        @foreach(getVariedades() as $item)
                            <option value="{{$item->id_variedad}}">{{$item->nombre}}</option>
                        @endforeach
                    </select>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-yura_dark" title="Buscar" onclick="buscar_listado()">
                            <i class="fa fa-fw fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>
        </div>

        <div id="div_listado">

        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.regalias_semanas.script')
@endsection