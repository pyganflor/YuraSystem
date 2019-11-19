@extends('layouts.adminlte.master')

@section('titulo')
    DB - Jobs
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <section class="content-header">
        <h1>
            DB
            <small>Jobs</small>
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
                    <i class="fa fa-fw fa-refresh"></i> {!! $submenu->nombre !!}
                </a>
            </li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div id="div_listado">
                </div>
                <div>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#ProyeccionUpdateSemanal" role="tab"
                               aria-controls="home"
                               aria-selected="true">
                                ProyeccionUpdateSemanal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#ResumenCosechaSemanal" role="tab"
                               aria-controls="profile"
                               aria-selected="false">
                                ResumenCosechaSemanal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#VentaSemanalReal" role="tab"
                               aria-controls="profile"
                               aria-selected="false">
                                VentaSemanalReal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#UpdateIndicador" role="tab"
                               aria-controls="profile"
                               aria-selected="false">
                                UpdateIndicador
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade" id="ProyeccionUpdateSemanal" role="tabpanel" aria-labelledby="home-tab">
                            <div class="input-group">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Desde
                                </div>
                                <input type="number" id="comando1_desde" onkeypress="return isNumber(event)" class="form-control text-center"
                                       value="{{$semana_actual->codigo}}">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Hasta
                                </div>
                                <input type="number" id="comando1_hasta" onkeypress="return isNumber(event)" class="form-control text-center"
                                       value="{{$semana_actual->codigo}}">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Variedad
                                </div>
                                <select name="comando1_variedad" id="comando1_variedad" class="form-control">
                                    <option value="0">Todas</option>
                                    @foreach($variedades as $var)
                                        <option value="{{$var->id_variedad}}">{{$var->siglas}}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Módulos
                                </div>
                                <select name="comando1_modulo" id="comando1_modulo" class="form-control">
                                    <option value="0">Todos</option>
                                    @foreach($modulos as $mod)
                                        <option value="{{$mod->id_modulo}}">{{$mod->nombre}}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    <input type="checkbox" id="comando1_restriccion"> <label for="comando1_restriccion">Restricción</label>
                                </div>

                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary" title="OK" onclick="send_queue_job(1)">
                                        <i class="fa fa-fw fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="ResumenCosechaSemanal" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="input-group">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Desde
                                </div>
                                <input type="number" id="comando2_desde" onkeypress="return isNumber(event)" class="form-control text-center"
                                       value="{{$semana_actual->codigo}}">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Hasta
                                </div>
                                <input type="number" id="comando2_hasta" onkeypress="return isNumber(event)" class="form-control text-center"
                                       value="{{$semana_actual->codigo}}">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Variedad
                                </div>
                                <select name="comando2_variedad" id="comando2_variedad" class="form-control">
                                    <option value="0">Todas</option>
                                    @foreach($variedades as $var)
                                        <option value="{{$var->id_variedad}}">{{$var->siglas}}</option>
                                    @endforeach
                                </select>

                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary" title="OK" onclick="send_queue_job(2)">
                                        <i class="fa fa-fw fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="VentaSemanalReal" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="input-group">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Desde
                                </div>
                                <input type="number" id="comando3_desde" onkeypress="return isNumber(event)" class="form-control text-center"
                                       value="{{$semana_actual->codigo}}">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Hasta
                                </div>
                                <input type="number" id="comando3_hasta" onkeypress="return isNumber(event)" class="form-control text-center"
                                       value="{{$semana_actual->codigo}}">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Cliente
                                </div>
                                <select name="comando3_cliente" id="comando3_cliente" class="form-control">
                                    <option value="0">Todos</option>
                                    @foreach($clientes as $cli)
                                        <option value="{{$cli->id_cliente}}">{{$cli->detalle()->nombre}}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Variedad
                                </div>
                                <select name="comando3_variedad" id="comando3_variedad" class="form-control">
                                    <option value="0">Todas</option>
                                    @foreach($variedades as $var)
                                        <option value="{{$var->id_variedad}}">{{$var->siglas}}</option>
                                    @endforeach
                                </select>

                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary" title="OK" onclick="send_queue_job(3)">
                                        <i class="fa fa-fw fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="UpdateIndicador" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="input-group">
                                <div class="input-group-addon" style="background-color: #e9ecef">
                                    Indicador
                                </div>
                                <select name="comando4_indicador" id="comando4_indicador" class="form-control">
                                    <option value="0">Todos</option>
                                    @foreach($indicadores as $ind)
                                        <option value="{{$ind->nombre}}" title="{{$ind->nombre}}">{{$ind->descripcion}}</option>
                                    @endforeach
                                </select>

                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-primary" title="OK" onclick="send_queue_job(4)">
                                        <i class="fa fa-fw fa-check"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    <script>
        setInterval(function () {
            $.get('{{url('db_jobs/actualizar')}}', {}, function (retorno) {
                $('#div_listado').html(retorno);
            });
        }, 1000)

        function send_queue_job(comando) {
            if (comando == 1) {
                datos = {
                    _token: '{{csrf_token()}}',
                    desde: $('#comando1_desde').val(),
                    hasta: $('#comando1_hasta').val(),
                    variedad: $('#comando1_variedad').val(),
                    modulo: $('#comando1_modulo').val(),
                    restriccion: $('#comando1_restriccion').prop('checked'),
                    comando: comando
                };
            }
            if (comando == 2) {
                datos = {
                    _token: '{{csrf_token()}}',
                    desde: $('#comando2_desde').val(),
                    hasta: $('#comando2_hasta').val(),
                    variedad: $('#comando2_variedad').val(),
                    comando: comando
                };
            }
            if (comando == 3) {
                datos = {
                    _token: '{{csrf_token()}}',
                    desde: $('#comando3_desde').val(),
                    hasta: $('#comando3_hasta').val(),
                    cliente: $('#comando3_cliente').val(),
                    variedad: $('#comando3_variedad').val(),
                    comando: comando
                };
            }
            if (comando == 4) {
                datos = {
                    _token: '{{csrf_token()}}',
                    indicador: $('#comando4_indicador').val(),
                    comando: comando
                };
            }

            $.post('{{url('db_jobs/send_queue_job')}}', datos, function (retorno) {
                true;
            }, 'json');
        }
    </script>
@endsection