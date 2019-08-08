@extends('layouts.adminlte.master')

@section('titulo')
    Especificaciones
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->

        @include('adminlte.gestion.partials.breadcrumb')


    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de espeficicaciones
                </h3>
            </div>
            <div class="box-body" id="div_content_especificaciones">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="anno">Clientes</label><br />
                                    <select id="cliente_id" name="cliente_id" class="form-control">
                                        <option value="">Seleccione</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{$cliente->id_cliente}}">{{$cliente->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                               {{-- <div class="form-group">
                                    <label for="anno">Especificaciones</label><br />
                                    <select id="id_cliente" name="id_cliente" class="form-control">
                                        <option value="">Seleccione</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{$cliente->id_cliente}}">{{$cliente->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>--}}
                                <div class="form-group">
                                    <label for="anno">Tipo</label><br />
                                    <select id="tipo" name="tipo" class="form-control">
                                        <option value="">Seleccione</option>
                                        <option value="N">Normal</option>
                                        <option value="O">Otras</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="anno">Estado</label><br />
                                    <select id="estado" name="estado" class="form-control">
                                        <option value="">Seleccione</option>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="anno">Busqueda</label><br />
                                    <input type="text" class="form-control" style="width:250px" id="busqueda_especifiaciones"
                                           name="busqueda_especificaciones">
                                </div>
                                <div class="form-group">
                                    <label style="visibility: hidden;"> .</label><br/>
                                    <span >
                                        <button class="btn btn-default" onclick="buscar_listado_especificaciones()"
                                                onmouseover="$('#title_btn_buscar').html('Buscar')"
                                                onmouseleave="$('#title_btn_buscar').html('')">
                                            <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar"></em>
                                        </button>
                                    </span>
                                    <span >
                                        <button class="btn btn-primary"
                                                onmouseover="$('#title_btn_add').html('Añadir')"
                                                onmouseleave="$('#title_btn_add').html('')"
                                                onclick="add_especificacion()">
                                            <i class="fa fa-fw fa-plus" style="color: #0c0c0c"></i>
                                            <em id="title_btn_add"></em>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_especificaciones"></div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    @include('adminlte.gestion.postcocecha.especificacion.script')
@endsection
