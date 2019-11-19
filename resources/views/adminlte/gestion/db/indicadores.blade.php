@extends('layouts.adminlte.master')

@section('titulo')
    DB - Indicadores
@endsection

@section('script_inicio')
    <script>
    </script>
@endsection

@section('contenido')
    <section class="content-header">
        <h1>
            DB
            <small>Indicadores</small>
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
                <div id="div_listado" style="overflow-x: scroll">
                    <table class="table-striped table-bordered table-hover" width="100%" style="border: 2px solid #9d9d9d"
                           id="db_tbl_indicadores">
                        <thead>
                        <tr style="background-color: #e9ecef">
                            <th class="text-center" style="border-color: #9d9d9d" width="10%">
                                Nombre
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d">
                                Descripcion
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d" width="10%">
                                Valor
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d" width="10%">
                                Estado
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d" width="10%">
                                <button type="button" class="btn btn-xs btn-primary" title="Añadir" onclick="add_indicador()"
                                        id="btn_add_indicador">
                                    <i class="fa fa-fw fa-plus"></i>
                                </button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($indicadores as $item)
                            <tr>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    <input type="text" class="text-center" id="nombre_{{$item->id_indicador}}" style="width: 100%"
                                           value="{{$item->nombre}}" max="4">
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    <input type="text" class="text-center" id="descripcion_{{$item->id_indicador}}" style="width: 100%"
                                           value="{{$item->descripcion}}" max="250">
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    <input type="number" class="text-center" id="valor_{{$item->id_indicador}}" style="width: 100%"
                                           value="{{$item->valor}}">
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    <input type="checkbox" id="estado_{{$item->id_indicador}}" {{$item->estado == 1 ? 'checked' : ''}}>
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    <button type="button" class="btn btn-xs btn-success" title="Editar"
                                            onclick="update_indicador('{{$item->id_indicador}}')">
                                        <i class="fa fa-fw fa-save"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script_final')
    <script>
        function add_indicador() {
            $('#btn_add_indicador').hide();
            $('#db_tbl_indicadores').append('<tr>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="text" class="text-center" id="new_nombre" style="width: 100%" placeholder="P1" max="4">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="text" class="text-center" id="new_descripcion" style="width: 100%" max="250">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="number" class="text-center" id="new_valor" style="width: 100%" value="0">' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="checkbox" id="new_estado" style="width: 100%" checked>' +
                '</td>' +
                '<td class="text-center" style="border-color: #9d9d9d">' +
                '<button type="button" class="btn btn-xs btn-success" title="Guardar" onclick="store_inidcador()">' +
                '<i class="fa fa-fw fa-save"></i>' +
                '</button>' +
                '</td>' +
                '</tr>');
            $('#new_nombre').focus();
        }

        function store_inidcador() {
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#new_nombre').val(),
                descripcion: $('#new_descripcion').val(),
                valor: $('#new_valor').val(),
                estado: $('#new_estado').prop('checked'),
            };
            post_jquery('{{url('db_indicadores/store_indicador')}}', datos, function (retorno) {
                location.reload();
            });
        }

        function update_indicador(id) {
            datos = {
                _token: '{{csrf_token()}}',
                id: id,
                nombre: $('#nombre_' + id).val(),
                descripcion: $('#descripcion_' + id).val(),
                valor: $('#valor_' + id).val(),
                estado: $('#estado_' + id).prop('checked'),
            };
            post_jquery('{{url('db_indicadores/update_indicador')}}', datos, function (retorno) {
                location.reload();
            });
        }
    </script>
@endsection