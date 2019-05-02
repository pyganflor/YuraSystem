@extends('layouts.adminlte.master')

@section('titulo')
    Importar Data
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Importar Data
            <small>módulo de administrador</small>
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

    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-body">
                <form id="form-importar_postcosecha" action="{{url('importar_data/postcosecha')}}" method="POST">
                    {!! csrf_field() !!}
                    <div class="input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Post-Cosecha
                        </span>
                        <input type="file" id="file_postcosecha" name="file_postcosecha" required class="form-control input-group-addon"
                               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            <input type="checkbox" id="cajas_postcosecha" name="cajas_postcosecha">
                            <label for="cajas_postcosecha" class="mouse-hand">Cajas</label>
                        </span>
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            <input type="checkbox" id="activo_postcosecha" name="activo_postcosecha">
                            <label for="activo_postcosecha" class="mouse-hand">Activo</label>
                        </span>
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Hora Inicio
                        </span>
                        <input type="time" id="hora_inicio_postcosecha" name="hora_inicio_postcosecha" required
                               class="form-control input-group-addon">
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Personal
                        </span>
                        <input type="number" id="personal_postcosecha" name="personal_postcosecha" required
                               class="form-control input-group-addon" min="1" onkeypress="return isNumber(event)">
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Módulo
                        </span>
                        <select name="id_modulo_postcosecha" id="id_modulo_postcosecha" class="form-control" required style="width: 65px">
                            @foreach(getModulos() as $m)
                                <option value="{{$m->id_modulo}}">{{$m->nombre}}</option>
                            @endforeach
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary" onclick="importar_postcosecha()">
                                <i class="fa fa-fw fa-check"></i>
                            </button>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <div class="box box-info">
            <div class="box-body">
                <form id="form-importar_venta" action="{{url('importar_data/venta')}}" method="POST">
                    {!! csrf_field() !!}
                    <div class="input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Ventas
                        </span>
                        <input type="file" id="file_ventas" name="file_ventas" required class="form-control input-group-addon"
                               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Año
                        </span>
                        <input type="number" id="anno_ventas" name="anno_ventas" class="form-control" min="2000" max="{{date('Y')}}" required>
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Campo
                        </span>
                        <select name="campo_ventas" id="campo_ventas" class="form-control" required>
                            <option value="V">Valor</option>
                            <option value="F">Cajas Físicas</option>
                            <option value="Q">Cajas EQ</option>
                            <option value="P">Precio x Ramo</option>
                        </select>
                        <span class="input-group-addon" style="background-color: #e9ecef">
                            Variedad
                        </span>
                        <select name="variedad_ventas" id="variedad_ventas" class="form-control" required>
                            @foreach(getVariedades() as $v)
                                <option value="{{$v->id_variedad}}">{{$v->nombre}}</option>
                            @endforeach
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-primary" onclick="importar_venta()">
                                <i class="fa fa-fw fa-check"></i>
                            </button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.importar_data.script')
@endsection