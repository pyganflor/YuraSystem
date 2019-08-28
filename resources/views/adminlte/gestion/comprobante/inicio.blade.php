@extends('layouts.adminlte.master')

@section('titulo')
    Comprobantes
@endsection

@section('contenido')
    @include('adminlte.gestion.partials.breadcrumb')
    <section class="content">
        <div class="box box-info">
                <div class="box-header with-border">
                    <div class="col-md-8">
                    <h3 class="box-title">
                        Administración de comprobantes electrónicos
                    </h3>
                    </div>
                    <div class="col-md-4 text-right">
                        <button class="btn btn-primary" onclick="subir_archivos_xml()"
                                onmouseover="$('#title_upload').html('Subir xml')"
                                onmouseleave="$('#title_upload').html('')">
                            <i class="fa fa-cloud-upload"></i>
                            <em  id="title_upload"></em>
                        </button>
                    </div>
            </div>

            <div class="box-body" id="div_content_comprobante">
                <table width="100%">
                    <tr>
                        <td>
                            <div class="form-inline">
                                <div class="form-group">
                                    <label for="id_configuracion_empresa">Empresa</label><br />
                                    <select class="form-control" id="id_configuracion_empresa_comproante" style="width:180px"
                                            name="id_configuracion_empresa_comproante">
                                        @foreach($empresas as $empresa)
                                             <option value="{{$empresa->id_configuracion_empresa}}"> {{$empresa->nombre}} </option>
                                         @endforeach
                                     </select>
                                 </div>
                                <div class="form-group">
                                    <label for="anno">Cliente</label><br />
                                    <select class="form-control" id="id_cliente" name="id_cliente" style="width: 200px;">
                                        <option value=""> Seleccione </option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{$cliente->id_cliente}}"> {{$cliente->nombre}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="anno">Tipo comprobante</label><br />
                                    <select class="form-control" id="codigo_comprobante" name="codigo_comprobante" style="width: 200px;">
                                        <option value=""> Seleccione </option>
                                        @foreach($tiposCompbantes as $tipoCompbante)
                                            <option {{$tipoCompbante->nombre === "FACTURA" ? "selected" : ""}} value="{{$tipoCompbante->codigo}}">{{ucwords($tipoCompbante->nombre)}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="anno">Estado</label><br />
                                    <select class="form-control" id="estado" name="estado">
                                        <option value=""> Seleccione </option>
                                        <option value="0"> No firmados </option>
                                        <option value="1" selected> Generados </option>
                                        <option value="3"> Devueltos </option>
                                        <option value="4"> Rechazados </option>
                                        <option value="5"> Aprobados por el SRI </option>
                                        <option value="6"> Anuladas </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label> Desde</label><br />
                                    <input type="date" class="form-control" id="desde" name="desde" style="width:150px" value="{{\Carbon\Carbon::now()->toDateString()}}">
                                </div>
                                <div class="form-group">
                                    <label> Hasta </label><br />
                                    <input type="date" class="form-control" id="hasta" name="hasta" style="width:150px" value="{{\Carbon\Carbon::now()->toDateString()}}">
                                </div>
                                <div class="form-group">
                                    <label style="visibility: hidden;"> .</label><br />
                                    <span class="">
                                        <button class="btn btn-default" onclick="buscar_listado_comprobante()" title="Buscar">
                                            <i class="fa fa-fw fa-search" style="color: #0c0c0c"></i>
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
