<input type="hidden" id="id_transportista" value="{{$id_transportista}}">
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-6">
                        <i class="fa fa-truck" aria-hidden="true"></i> Camiones
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" class="btn btn-success btn-xs" title="Agregar conductor" onclick="add_camion()">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="table_empaque_conductores">
                <table width="100%" class="table table-responsive table-bordered" style="margin:0;font-size: 0.8em; border-color: #9d9d9d"
                       id="table_content_empaques_conductores">
                    <thead>
                    <tr style="background-color: #dd4b39; color: white">
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:70%">
                            MODELO
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:70%">
                            PLACAS
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                            OPCIONES
                        </th>
                    </tr>
                    </thead>
                    @if(sizeof($camiones)>0)
                        @foreach($camiones as $item)
                            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                                style="{{$item->estado == 1 ? '' : 'color:red'}}">
                                <td style="border-color: #9d9d9d" class="text-center">
                                    {{$item->modelo}}
                                </td>
                                <td style="border-color: #9d9d9d" class="text-center">
                                    {{$item->placa}}
                                </td>
                                <td style="border-color: #9d9d9d" class="text-center">
                                    <button type="button" {{$item->estado == 1 ? '' : 'disabled'}} class="btn btn-default btn-xs" title="editar" onclick="add_camion('{{$item->id_camion}}')">
                                        <i class="fa fa-pencil" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-{{$item->estado == 1 ? 'danger' : 'success'}} btn-xs" title="editar" onclick="update_estado_camion('{{$item->id_camion}}','{{$item->estado}}')">
                                        <i class="fa fa-{{$item->estado == 1 ? 'trash' : 'undo'}}" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3">
                                <div class="alert alert-info text-center">No se han encontrado coincidencias de camiones</div>
                            </td>
                        </tr>
                    @endif
                </table>
                <div id="pagination_listado_empaques_conductores"> </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-6">
                    <i class="fa fa-male" aria-hidden="true"></i> Conductores
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-success btn-xs" title="Agregar conductor" onclick="add_conductor()">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="table_empaque_camiones">
            <table width="100%" class="table table-responsive table-bordered" style="margin: 0;font-size: 0.8em; border-color: #9d9d9d"
                   id="table_content_empaque_camiones">
                <thead>
                <tr style="background-color: #dd4b39; color: white">
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:70%">
                        NOMBRE CHOFER
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:70%">
                        IDENTIFICACIÓN
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        OPCIONES
                    </th>
                </tr>
                </thead>
                @if(sizeof($conductores)>0)
                    @foreach($conductores as $item)
                        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')" style="{{$item->estado == 1 ? '' : 'color:red'}}">
                            <td style="border-color: #9d9d9d" class="text-center">
                                {{$item->nombre}}
                            </td>
                            <td style="border-color: #9d9d9d" class="text-center">
                                {{$item->ruc}}
                            </td>
                            <td style="border-color: #9d9d9d" class="text-center">
                                <button type="button" {{$item->estado == 1 ? '' : 'disabled'}} class="btn btn-default btn-xs" title="Editar" onclick="add_conductor('{{$item->id_conductor}}')">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-{{$item->estado == 1 ? 'danger' : 'success'}} btn-xs" title="Deshabilitar" onclick="update_estado_conductor('{{$item->id_conductor}}','{{$item->estado}}')">
                                    <i class="fa fa-{{$item->estado == 1 ? 'trash' : 'undo'}}" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3">
                            <div class="alert alert-info text-center">No se han encontrado coincidencias de conductores</div>
                        </td>
                    </tr>
                @endif
            </table>
            <div id="pagination_listado_empaques_camiones"></div>
        </div>
    </div>
</div>
</div>
