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
                <div id="div_listado">
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

            $.post('{{url('db_jobs/send_queue_job')}}', datos, function (retorno) {
                true;
            }, 'json');
        }
    </script>
@endsection