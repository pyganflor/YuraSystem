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
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#ProyeccionUpdateSemanal" role="tab" aria-controls="home"
                               aria-selected="true">
                                ProyeccionUpdateSemanal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#ResumenCosechaSemanal" role="tab" aria-controls="profile"
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
                                <input type="number" id="comando1_desde" onkeypress="return isNumber(event)" class="form-control text-center">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="ResumenCosechaSemanal" role="tabpanel" aria-labelledby="profile-tab">
                            ResumenCosechaSemanal
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @dump(getCurrentDateDB())
@endsection

@section('script_final')
    <script>
        setInterval(function () {
            $.get('{{url('db_jobs/actualizar')}}', {}, function (retorno) {
                $('#div_listado').html(retorno);
            });
        }, 2000)
    </script>
@endsection