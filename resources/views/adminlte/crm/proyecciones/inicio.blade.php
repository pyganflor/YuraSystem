@extends('layouts.adminlte.master')

@section('titulo')
    Dashboard - Proyecciones
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div id="div_indicadores">
            @include('adminlte.crm.proyecciones.partials.indicadores')
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
                        <i class="fa fa-fw fa-leaf"></i> Variedad
                    </div>
                    <select name="filtro_predeterminado_planta" id="filtro_predeterminado_planta" class="form-control"
                            onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades', '<option value=T selected>Todos los tipos</option>')">
                        <option value="">Todas las variedades</option>
                        @foreach(getPlantas() as $p)
                            <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
                        @endforeach
                    </select>
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
                    <div class="col-md-3">
                        <div class="box box-solid box-info">
                            <div class="box-header with-border ">
                                <i class="fa fa-pie-chart"></i>
                                <h3 class="box-title">Proyección semana: </h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li><a href="#">Tallos cosechados <span class="pull-right badge bg-blue">{{number_format($indicador[5]->valor,2,",",".")}}</span></a></li>
                                        <li><a href="#">Cajas cosechadas <span class="pull-right badge bg-aqua">{{number_format($indicador[7]->valor,2,",",".")}}</span></a></li>
                                        <li><a href="#">Cajas vendidas <span class="pull-right badge bg-lime-active">{{number_format($indicador[6]->valor,2,",",".")}}</span></a></li>
                                        <li><a href="#">Dinero generado <span class="pull-right badge bg-green">${{number_format($indicador[8]->valor,2,",",".")}}</span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    {{-- JS de Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

    @include('adminlte.crm.proyecciones.script')
@endsection
