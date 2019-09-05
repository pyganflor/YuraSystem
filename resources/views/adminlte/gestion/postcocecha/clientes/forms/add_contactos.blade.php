<div id="div_contactos_cliente">
    <div class="row">
        <div class="col-md-12">
            <h4 class="box-title">
                Agregar contactos
            </h4>
            <form id="form_add_user_contactos">
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
                    <thead>
                    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                        <th class="text-center" style="border-color: #9d9d9d">Ingrese los datos del contacto</th>
                        <th class="text-center" style="border-color: #9d9d9d;width: 10%;">
                            <button type="button" class="btn btn-xs btn-default" title="Añadir campo" onclick="cargar_opcion('campos_contactos','','clientes/ver_contactos_clientes','add')">
                                <i class="fa fa-fw fa-plus"></i>
                            </button>
                        </th>
                    </tr>
                    </thead>
                        <tbody id="campos_contactos">
                            @if(isset($dataContacto) && count($dataContacto) > 0)
                                @foreach($dataContacto as $key => $contacto)
                                    @php
                                        $contacto->estado == 0 ? $disabled='disabled=disabled' :  $disabled='';
                                    @endphp
                                    <tr id="tr_select_agencias_carga_{{$key+1}}">
                                        <td>
                                            <div class="form-group col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="background-color: #e9ecef">Nombre</span>
                                                    <input type="text" id="nombre_contacto_{{$key+1}}" name="nombre_contacto_{{$key+1}}" required=""
                                                           value="{{$contacto->nombre}}" {{$disabled}} class="form-control" minlength="1" maxlength="100">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="background-color: #e9ecef">Correo</span>
                                                    <input type="email" id="correo_{{$key+1}}" name="correo_{{$key+1}}" required=""
                                                           value="{{$contacto->correo}}" {{$disabled}} class="form-control" minlength="1" maxlength="100">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="background-color: #e9ecef">teléfono</span>
                                                    <input type="text" id="telefono_{{$key+1}}" name="telefono_{{$key+1}}" required=""
                                                           value="{{$contacto->telefono}}" {{$disabled}} class="form-control" minlength="1" maxlength="15">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="input-group" >
                                                    <span class="input-group-addon" style="background-color: #e9ecef">Dirección</span>
                                                    <textarea id="direccion_{{$key+1}}" name="direccion_{{$key+1}}" required=""
                                                               class="form-control" cols="3" {{$disabled}} minlength="1" maxlength="500">{{$contacto->direccion}}</textarea>
                                                </div>
                                            </div>
                                            <input type="hidden" id="id_inputs_contacto_{{$key+1}}" value="{{$contacto->id_contacto}}">
                                        </td>
                                        <td style="vertical-align: middle;text-align: center;">
                                            <button type="button" class="btn btn-xs btn-warning" title="Actualizar estado del contacto" onclick="actualizarContacto('{{$contacto->id_contacto}}','{{$contacto->estado}}','{{$dataCliente->id_cliente}}')">
                                                <i class="fa fa-ban" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr id="tr_select_agencias_carga_1">
                                    <td>
                                        <div class="form-group col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="background-color: #e9ecef">Nombre</span>
                                                <input type="text" id="nombre_contacto_1" name="nombre_contacto_1" required="" class="form-control" minlength="1" maxlength="100">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="background-color: #e9ecef">Correo</span>
                                                <input type="email" id="correo_1" name="correo_1" required="" class="form-control" minlength="1" maxlength="100">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-addon" style="background-color: #e9ecef">teléfono</span>
                                                <input type="text" id="telefono_1" name="telefono_1" required="" class="form-control" minlength="1" maxlength="15">
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <div class="input-group" >
                                                <span class="input-group-addon" style="background-color: #e9ecef">Dirección</span>
                                                <textarea id="direccion_1" name="direccion_1" required="" class="form-control" cols="3" minlength="1" maxlength="500"></textarea>
                                            </div>
                                        </div>
                                        <input type="hidden" id="id_inputs_contacto_1" value="">
                                    </td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                </table>
            </form>
            <div class="text-center">
                <button class="btn btn-success" type="button" id="btn_guardar_modal_add_cliente" onclick="store_contactos('{{$dataCliente->id_cliente}}','{{$dataCliente->id_detalle_cliente}}')"><span class="bootstrap-dialog-button-icon fa fa-fw fa-save"></span>Guardar</button>
            </div>
        </div>
    </div>
</div>
