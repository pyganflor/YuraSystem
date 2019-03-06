<div class="col-md-8">
    <div id="table_empaque_c">
        @if(sizeof($especificacion)>0)
            <table width="100%" class="table table-responsive table-bordered" style="font-size: 1em; border-color: #9d9d9d"
                   id="table_content_empaque_c">
                <thead>
                <tr style="background-color: #dd4b39; color: white">
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;">
                        VARIEDAD
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        CALIBRE
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        CAJA
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        RAMO X CAJA
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        PRESENTACIÓN
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        TALLOS X RAMO
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        LONGITUD
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        OPCIONES
                    </th>
                </tr>
                </thead>
                @foreach($especificacion as $item)
                    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                        @php $esp = getDetalleEspecificacion($item->id_especificacion);@endphp
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;width: 100px;" class="text-center">
                            <ul style="padding: 0;margin:0">
                                @foreach($esp as $key => $e)
                                    <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                        {{$e["variedad"]}}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            <ul style="padding: 0;margin:0">
                                @foreach($esp as  $e)
                                    <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                        {{$e["calibre"]}}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            <ul style="padding: 0;margin:0">
                                @foreach($esp as $e)
                                    <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                        {{$e["caja"]}}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            <ul style="padding: 0;margin:0">
                                @foreach($esp as $e)
                                    <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                        {{$e["rxc"]}}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            <ul style="padding: 0;margin:0">
                                @foreach($esp as $e)
                                    <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                        {{$e["presentacion"]}}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            <ul style="padding: 0;margin:0">
                                @foreach($esp as $e)
                                    <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                        {{$e["txr"] == null ? "-" : $e["txr"] }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            <ul style="padding: 0;margin:0">
                                @foreach($esp as $e)
                                    <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                        {{$e["longitud"] == null ? "-" : $e["longitud"] }} {{($e["unidad_medida_longitud"] == null || $e["longitud"] == null) ? "" : $e["unidad_medida_longitud"]}}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center">
                            <button type="button" class="btn btn-default btn-xs" title="Asignar precios a esta especifiación" onclick="precio_especificacion('{{$item->id_especificacion}}')">
                                <i class="fa fa-usd" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </table>
        <div id="pagination_listado_empaques_c">
             {!! str_replace('/?','?',$especificacion->render()) !!}
            </div>
        @else
            <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
        @endif
    </div>
</div>
<div class="col-md-4"> {{--AQUI TODOS LOS CLIENTE Y AL HACER CLIC VER LAS ESPECIFICIACION CON LOS PRECIOS PARA PODER EDITAR PRECIOS Y ASIGNAR ESPECIFICACIONES--}}
    <div id="table_empaque_p">
        @if(sizeof($clientes)>0)
            <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
                   id="table_content_empaques_p">
                <thead>
                <tr style="background-color: #dd4b39; color: white">
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:70%">
                        CLIENTE
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                        OPCIONES
                    </th>
                </tr>
                </thead>
                @foreach($clientes as $cliente)
                    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                        <td style="border-color: #9d9d9d" class="text-center">
                            {{$cliente->nombre}}
                        </td>
                        <td style="border-color: #9d9d9d" class="text-center">

                        </td>
                    </tr>
                @endforeach
            </table>
       <div id="pagination_listado_empaques_p">
             {!! str_replace('/?','?',$clientes->render()) !!}
            </div>
        @else
            <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
        @endif
    </div>
</div>

