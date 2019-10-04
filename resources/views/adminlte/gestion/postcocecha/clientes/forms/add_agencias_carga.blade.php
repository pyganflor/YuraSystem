<div id="div_select_agencia_carga">
    <div class="row">
        <div class="col-md-12">
            <h4 class="box-title">
                Agencias de carga
            </h4>
            <form id="form_add_user_agencia_carga">
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
                    <thead>
                    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        <th class="text-center" style="border-color: #9d9d9d">Seleccione una opción</th>
                        <th class="text-center" style="border-color: #9d9d9d;width: 10%;">
                            <button type="button" class="btn btn-xs btn-default" title="Añadir campo" onclick="cargar_opcion('campos_agencia_carga','','clientes/ver_agencias_carga','add')">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-default" title="Crear agencia de carga" onclick="create_agencia_carga('','{{csrf_token()}}','{{$dataCliente->id_cliente}}')">
                                <i class="fa fa-cubes" aria-hidden="true"></i>
                            </button>
                        </th>
                    </tr>
                    </thead>
                    <tbody id="campos_agencia_carga">
                    @if(isset($dataClienteAgenciasCarga) && count($dataClienteAgenciasCarga) > 0)
                        @foreach($dataClienteAgenciasCarga as $key => $clienteAgenciasCarga)
                            <tr id="tr_select_agencias_carga_{{$key+1}}">
                                <td>
                                    <div class="">
                                        <select id="select_agencia_carga_{{$key+1}}" {!! $clienteAgenciasCarga->estado == 0 ? 'disabled' : '' !!} name="select_agencia_carga_{{$key+1}}" class="form-control" required>
                                            <option disabled selected>Seleccione1</option>
                                            @foreach($dataAgenciaCarga as $agenciaCarga)
                                                @php
                                                $selected = '';
                                                    if($agenciaCarga->id_agencia_carga === $clienteAgenciasCarga->id_agencia_carga){
                                                        $selected ='selected=selected';
                                                    }
                                                @endphp
                                                <option {{$selected}} value="{{$agenciaCarga->id_agencia_carga}}">{{$agenciaCarga->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="id_select_agencia_carga_{{$key+1}}" value="{{$clienteAgenciasCarga->id_cliente_agencia_carga}}">
                                    </div>
                                    <div class="row contacto_agencia_carga_{{$key+1}}">
                                        <div class="col-md-12"><div class="row">
                                                <div class="col-md-4">
                                                    <label> Contacto</label>
                                                    <input type="text"  class="form-control contacto_cliente_agencia_carga"
                                                           value="{{isset($clienteAgenciasCarga->contacto_cliente_agencia_carga[0]) ? $clienteAgenciasCarga->contacto_cliente_agencia_carga[0]->contacto : ""}}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label> Correo</label>
                                                    <input type="email" class="form-control correo_cliente_agencia_carga"
                                                           value="{{isset($clienteAgenciasCarga->contacto_cliente_agencia_carga[0]) ? $clienteAgenciasCarga->contacto_cliente_agencia_carga[0]->correo : ""}}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label> Dirección</label>
                                                    <input type="text" class="form-control direccion_cliente_agencia_carga"
                                                           value="{{isset($clienteAgenciasCarga->contacto_cliente_agencia_carga[0]) ? $clienteAgenciasCarga->contacto_cliente_agencia_carga[0]->direccion : ""}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label> Contacto</label>
                                                    <input type="text" class="form-control contacto_cliente_agencia_carga"
                                                           value="{{isset($clienteAgenciasCarga->contacto_cliente_agencia_carga[1]) ? $clienteAgenciasCarga->contacto_cliente_agencia_carga[1]->contacto : ""}}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label> Correo</label>
                                                    <input type="email" class="form-control correo_cliente_agencia_carga"
                                                           value="{{isset($clienteAgenciasCarga->contacto_cliente_agencia_carga[1]) ? $clienteAgenciasCarga->contacto_cliente_agencia_carga[1]->correo : ""}}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label> Dirección</label>
                                                    <input type="text" class="form-control direccion_cliente_agencia_carga"
                                                           value="{{isset($clienteAgenciasCarga->contacto_cliente_agencia_carga[1]) ? $clienteAgenciasCarga->contacto_cliente_agencia_carga[1]->direccion : ""}}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label> Contacto</label>
                                                    <input type="text"  class="form-control contacto_cliente_agencia_carga"
                                                           value="{{isset($clienteAgenciasCarga->contacto_cliente_agencia_carga[2]) ? $clienteAgenciasCarga->contacto_cliente_agencia_carga[2]->contacto : ""}}" >
                                                </div>
                                                <div class="col-md-4">
                                                    <label> Correo</label>
                                                    <input type="email"  class="form-control correo_cliente_agencia_carga"
                                                           value="{{isset($clienteAgenciasCarga->contacto_cliente_agencia_carga[2]) ? $clienteAgenciasCarga->contacto_cliente_agencia_carga[2]->correo : ""}}" >
                                                </div>
                                                <div class="col-md-4">
                                                    <label> Dirección</label>
                                                    <input type="text"
                                                           class="form-control direccion_cliente_agencia_carga"
                                                           value="{{isset($clienteAgenciasCarga->contacto_cliente_agencia_carga[2]) ? $clienteAgenciasCarga->contacto_cliente_agencia_carga[2]->direccion : ""}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center" style="vertical-align: middle">
                                    <button type="button" class="btn btn-xs btn-danger" title="{!! $clienteAgenciasCarga->estado == 1 ? 'Desactivar' : 'Activar' !!} campo"
                                            onclick="deleteClienteAgenciaCarga('{{$clienteAgenciasCarga->id_cliente_agencia_carga}}','{!! $clienteAgenciasCarga->estado !!}','{{$dataCliente->id_cliente}}')">
                                        <i class="fa fa-trash " aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="tr_select_agencias_carga_1">
                            <td>
                                <div class="">
                                    <select id="select_agencia_carga_1" name="select_agencia_carga_1" class="form-control" required>
                                        <option disabled selected>Seleccione</option>
                                        @foreach($dataAgenciaCarga as $agenciaCarga)
                                            <option value="{{$agenciaCarga->id_agencia_carga}}">{{$agenciaCarga->nombre}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="id_select_agencia_carga_1" value="">
                                </div>
                                <div class="row contacto_agencia_carga_1">
                                    <div class="col-md-12"><div class="row">
                                            <div class="col-md-4">
                                                <label> Contacto</label>
                                                <input type="text"  class="form-control contacto_cliente_agencia_carga" value="" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label> Correo</label>
                                                <input type="email" class="form-control correo_cliente_agencia_carga" value="" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label> Dirección</label>
                                                <input type="text" class="form-control direccion_cliente_agencia_carga" value="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label> Contacto</label>
                                                <input type="text" class="form-control contacto_cliente_agencia_carga" value="" >
                                            </div>
                                            <div class="col-md-4">
                                                <label> Correo</label>
                                                <input type="email" class="form-control correo_cliente_agencia_carga" value="" >
                                            </div>
                                            <div class="col-md-4">
                                                <label> Dirección</label>
                                                <input type="text" class="form-control direccion_cliente_agencia_carga" value="">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label> Contacto</label>
                                                <input type="text"  class="form-control contacto_cliente_agencia_carga" value="" >
                                            </div>
                                            <div class="col-md-4">
                                                <label> Correo</label>
                                                <input type="email"  class="form-control correo_cliente_agencia_carga" value="" >
                                            </div>
                                            <div class="col-md-4">
                                                <label> Dirección</label>
                                                <input type="text"
                                                       class="form-control direccion_cliente_agencia_carga" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td></td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </form>
            <div class="text-center">
                <button class="btn btn-success" type="button" id="btn_guardar_modal_add_cliente" onclick="store_agencias('{{$dataCliente->id_cliente}}')"><span class="bootstrap-dialog-button-icon fa fa-fw fa-save"></span>Guardar</button>
            </div>
        </div>
    </div>
</div>
