@extends('layouts.adminlte.master')

@section('titulo')
   Emisión comprobantes electrónicos
@endsection

@section('contenido')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        @include('adminlte.gestion.partials.breadcrumb')
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Administración de emisión comprobantes electrónicos
                </h3>
            </div>
            <div class="box-body">
                <div class="">
                    <div class="col-md-4">
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Empleados para facturar</span>
                            <select id="empleados_facturar" name="empleados_facturar" onchange="listar_punto_emision()" class="form-control">
                                <option disabled selected> Seleccione </option>
                                @for($i=1;$i<=20;$i++)
                                    <option {{ count($exist_punto_acceso) == $i ? "selected" : "" }} value="{{$i}}"> {{$i}} </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="list-group">
                            <div class="list-group-item list-group-item-action active text-center">
                                Asignación de punto de accesos
                            </div>
                            <div id="punto_emision">
                                <form id="form_comprobante_emision" name="form_comprobante_emision">
                                    @for($i=0;$i<count($exist_punto_acceso);$i++)
                                        <div class="col-md-4" style="padding: 10px 5px">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="background-color: #e9ecef">Punto de emisión</span>
                                                <input type="text" class="form-control" name="punto_emision" id="punto_emision{{$i}}"
                                                       value="{{$exist_punto_acceso[$i]->punto_acceso}}" required disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-8" style="padding: 10px 0">
                                            <div class="input-group" style="margin-bottom: 10px;">
                                                <span class="input-group-addon" style="background-color: #e9ecef">Usuario</span>
                                                <select class="form-control" id="id_usuario_{{$i+1}}" name="id_usuario_{{$i+1}}" required>
                                                    <option disabled selected>Seleccione</option>
                                                    @foreach($usuario as $u)
                                                       @php
                                                            $selected= "";
                                                            if($u->id_usuario == $exist_punto_acceso[$i]->id_usuario)
                                                                $selected="selected";
                                                        @endphp
                                                        <option {{$selected}}  value="{{$u->id_usuario}}">{{$u->nombre_completo}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endfor
                                </form>
                            </div>
                        </div >
                        <div class="text-center" style="padding: 0px 5px;">
                            <button type="button" class="btn btn-success" onclick="store_punto_acceso()">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script_final')
    @include('adminlte.gestion.configuracion_facturacion.emision_comprobantes.script')
@endsection
