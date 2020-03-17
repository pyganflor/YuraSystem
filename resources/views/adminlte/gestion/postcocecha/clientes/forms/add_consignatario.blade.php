<div id="div_contactos_cliente">
    <div class="row">
        <div class="col-md-12">
            <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
                <thead>
                <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
                    <th class="text-center" style="border-color: #9d9d9d">
                        <div class="box-title" style="font-size:15px">Agregar consignatrio</div>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d;width: 10%;">
                        <button type="button" class="btn btn-xs btn-default" title="Añadir campo" onclick="aumentar_consignatario()">
                            <i class="fa fa-fw fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-danger" title="Elminar campo" onclick="elimnar_consignatario()">
                            <i class="fa fa-fw fa-trash"></i>
                        </button>
                    </th>
                </tr>
                </thead>
            </table>
            <form id="form_add_user_contactos" style="margin-top: 20px">
                <div class="row" id="row_add_user_contactos">
                    @if($cliente_consignatario->count()>0)
                        @foreach($cliente_consignatario as $cc)
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <div class="input-group consignatario">
                                    <select id="consignatario" name="consignatario" class="form-control">
                                        {{--<option value="" selected disabled>Seleccione</option>--}}
                                        @foreach($consignatatios as $consignatario)
                                            <option {{$cc->id_consignatario == $consignatario->id_consignatario ? 'selected' : '' }} value="{{$consignatario->id_consignatario}}">{{$consignatario->nombre}}</option>
                                        @endforeach
                                    </select>
                                    <span class="input-group-addon" title="Seleccione este consignatario para que parezca por default en la facturas del cliente">
                                        <input type="radio" name="consignatario_default" {{$cc->default ? 'checked' : ''}}>
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group consignatario">
                                        <select id="consignatario" name="consignatario" class="form-control">
                                            {{--<option value="" selected disabled>Seleccione</option>--}}
                                            @foreach($consignatatios as $consignatario)
                                                <option value="{{$consignatario->id_consignatario}}">{{$consignatario->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-addon" title="Seleccione este consignatario para que parezca por default en la facturas del cliente">
                                            <input type="radio" name="consignatario_default">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                </div>
            </form>
            <div class="text-center">
                <button class="btn btn-success" type="button" id="btn_guardar_modal_add_cliente"
                        onclick="store_cliente_consignatario('{{csrf_token()}}','{{$id_cliente}}')">
                    <span class="bootstrap-dialog-button-icon fa fa-fw fa-save"></span>Guardar</button>
            </div>
        </div>
    </div>
</div>
