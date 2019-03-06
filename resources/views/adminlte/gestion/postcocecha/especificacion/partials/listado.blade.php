<div id="table_especificaciones"> {{--AQUI TODOS LAS ESPECIFICIACION CON LOS PRECIOS Y AL HACER CLIC VER TODOS LOS CLIENTES PARA ASIGNAR--}}
        <table width="100%" class="table table-responsive table-bordered" style="border-color: #9d9d9d"
               id="table_content_especificaciones">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                     NOMBRE ESPECIFACIÓN
                 </th>--}}
                {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                     TIPO
                 </th>--}}
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
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
                {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    DESCRIPCIÓN
                </th>--}}
                {{--<th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    ESTADO
                </th>--}}
                <th style="width: 80px;" class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            <tbody id="div_nueva_especificacion">
            <tr id="tr_nueva_especificacion_1">
                <td style="padding: 5px 0px;border-color: #9d9d9d">
                    <select id="id_variedad_1" style="width: 100%;height: 25.8px;" name="id_variedad">
                        <option selected disabled>Seleccione</option>
                        @foreach($variedades as $v)
                            <option value="{{$v->id_variedad}}">{{$v->nombre}}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding: 5px 0px;border-color: #9d9d9d">
                    <select id="id_clasificacion_ramo_1" style="width: 100%;height: 25.8px;" name="id_clasificacion_ramo_1">
                        <option selected disabled>Seleccione</option>
                        @foreach($clasificacion_ramo as $c)
                            <option value="{{$c->id_clasificacion_ramo}}">{{$c->nombre}}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding: 5px 0px;border-color: #9d9d9d">
                    <select id="id_empaque_1" style="width: 100%;height: 25.8px;" name="id_empaque_1">
                        <option selected disabled>Seleccione</option>
                        @foreach($empaque as $e)
                            <option value="{{$e->id_empaque}}">{{explode("|",$e->nombre)[0]}}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding: 5px 0px;border-color: #9d9d9d">
                    <input type="text" placeholder="Cantidad" id="ramo_x_caja_1" style="width: 100%" name="ramo_x_caja_1">
                </td>
                <td style="padding: 5px 0px;border-color: #9d9d9d">
                    <select id="id_presentacion_1" style="width: 100%;height: 25.8px;" name="id_presentacion_1">
                        <option selected disabled>Seleccione</option>
                        @foreach($presentacion as $p)
                            <option value="{{$p->id_empaque}}">{{$p->nombre}}</option>
                        @endforeach
                    </select>
                </td>
                <td style="padding: 5px 0px;border-color: #9d9d9d">
                    <input type="text" placeholder="Cantidad" id="tallos_x_ramo_1" style="width: 100%" name="tallos_x_ramo_1">
                </td>
                <td style="padding: 5px 0px;border-color: #9d9d9d">
                    <input type="text" placeholder="Cantidad" id="longitud_1" style="width: 50%" name="longitud_1">
                    <select id="id_unidad_medida_1" name="id_unidad_medida_1" style="width: 48%;height: 25.8px;">
                        {{--<option value="">Seleccione</option>--}}
                        @foreach($unidad_medida as $u)
                            <option value="{{$u->id_unidad_medida}}">{{$u->siglas}}</option>
                        @endforeach
                    </select>
                </td>
                <td id="td_btn_add_store_1" style="padding: 5px 0px;border-color: #9d9d9d" class="text-center">
                    <div class='btn-group' role='group' aria-label='Basic example'>
                        <button type="button" class="btn btn-success btn-xs" id="btn_add_row_especificacion_1" title="Crear fila" onclick="add_row_especificacion()">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="btn btn-primary btn-xs" title="Guardar" onclick="store_nueva_especificacion()">
                            <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
            @if(sizeof($listado)>0)
                @foreach($listado as $key => $item)
                    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                        class="{{$item->estado == 1 ? '':'error'}}" id="row_especificaciones_{{$item->id_especificacion}}">
                       {{--<td style="border-color: #9d9d9d;vertical-align: middle;" class="text-center">
                            {{$item->nombre_especificacicon}}</td>--}}
                        @php $esp = getDetalleEspecificacion($item->id_especificacion);@endphp
                        {{--<td style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center">  {{$item->tipo == "N" ? "Normal": "Otros"}} </td>--}}
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
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
                        {{--<td style="border-color: #9d9d9d" class="text-center"> {{$item->descripcion}}</td>--}}
                        {{--<td style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center">  {{$item->estado == 0 ? "Descativado": "Activo"}} </td>--}}
                        <td style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center">
                            @if($item->tipo == "N" && $item->estado == 1)
                            <button type="button" class="btn btn-default btn-xs" title="Ver asignaciones" onclick="asignar_especificacicon('{{$item->id_especificacion}}',' {{$item->nombre_especificacicon}}')">
                                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                            </button>
                            @endif
                                <a href="javascript:void(0)" class="btn btn-{{$item->estado == 1 ? 'success':'warning'}} btn-xs" title="{{$item->estado == 1 ? 'Habilitada':'Deshabilitada'}}"
                                   onclick="update_especificacion('{{$item->id_especificacion}}','{{$item->estado}}','{{csrf_token()}}')">
                                    <i class="fa fa-fw fa-{{$item->estado == 1 ? 'check':'ban'}}" style="color: white" ></i>
                                </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
            @endif
        </table>
        <div id="pagination_listado_especificaciones">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
</div>

