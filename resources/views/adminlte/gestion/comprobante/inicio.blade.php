@extends('layouts.adminlte.master')

@section('titulo')
    Comprobantes
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de comprobantes electrónicos
                </h3>
            </div>
            <div class="box-body" id="div_content_comprobante">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="anno">Año</label><br />
                                    <select class="form-control" id="anno" name="anno">
                                        <option value=""> Seleccione </option>
                                        @foreach($annos as $anno)
                                            <option value="{{$anno->anno}}"> {{$anno->anno}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="anno">Cliente</label><br />
                                    <select class="form-control" id="id_cliente" name="id_cliente" style="width: 250px;">
                                        <option value=""> Seleccione </option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{$cliente->id_cliente}}"> {{$cliente->nombre}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="anno">Tipo comprobante</label><br />
                                    <select class="form-control" id="codigo_comprobante" name="codigo_comprobante">
                                        <option value=""> Seleccione </option>
                                        @foreach($tiposCompbantes as $tipoCompbante)
                                            <option value="{{$tipoCompbante->codigo}}">{{$tipoCompbante->nombre}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="anno">Estado</label><br />
                                    <select class="form-control" id="estado" name="estado">
                                        <option value=""> Seleccione </option>
                                        <option value="0"> No firmados </option>
                                        <option value="1"> Generados </option>
                                        <option value="3"> Devueltos </option>
                                        <option value="4"> Rechazados </option>
                                        <option value="5"> Enviado al SRI </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label> Fecha</label><br />
                                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{\Carbon\Carbon::now()->toDateString()}}">
                                </div>
                                <div class="form-group">
                                    <label style="visibility: hidden;"> .</label><br />
                                    <span class="">
                                        <button class="btn btn-default" onclick="buscar_listado_comprobante()"
                                                onmouseover="$('#title_btn_buscar_pedido').html('Buscar')"
                                                onmouseleave="$('#title_btn_buscar_pedido').html('')">
                                            <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i> <em
                                                id="title_btn_buscar_pedido"></em>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <div id="div_listado_comprobante"></div>
            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.comprobante.script')
@endsection
