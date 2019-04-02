@extends('layouts.adminlte.master')

@section('titulo')
   Envios
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de Envios
                </h3>
            </div>
            <div class="box-body" id="div_content_pedidos">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="anno">Estado</label><br />
                                    <select class="form-control" id="estado" name="estado">
                                        <option value=""> Seleccione </option>
                                        <option value="1"> Enviado </option>
                                        <option value="0"> No enviado </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="Especificaciones">Cliente</label><br />
                                    <select class="form-control" id="id_cliente" name="id_cliente">
                                        <option value=""> Seleccione </option>
                                        @foreach($clientes as $cliente)
                                             <option value="{{$cliente->id_cliente}}"> {{$cliente->nombre}} </option>
                                         @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label> Fecha</label><br />
                                    <input type="date" class="form-control" id="fecha" name="fecha" value='{{\Carbon\Carbon::now()->toDateString()}}'>
                                </div>
                                <div class="form-group">
                                    <label style="visibility: hidden;"> .</label><br />
                                    <span class="">
                                        <button class="btn btn-default" onclick="buscar_listado_envios()"
                                                onmouseover="$('#title_btn_buscar_pedido').html('Buscar')"
                                                onmouseleave="$('#title_btn_buscar_pedido').html('')">
                                            <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar_pedido"></em>
                                        </button>
                                    </span>
                                    <span class="">
                                        <button class="btn btn-success" onclick="exportar_envios()"
                                                onmouseover="$('#title_btn_add_pedido_fijo').html('Exportar a excel')"
                                                onmouseleave="$('#title_btn_add_pedido_fijo').html('')">
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                            <em id="title_btn_add_pedido_fijo"></em>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_envios"></div>
            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.postcocecha.envios.script')
@endsection
