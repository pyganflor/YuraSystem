<div id="table_especificaciones">
<table width="100%" class="table table-responsive table-bordered" style="border-color: #9d9d9d;margin-top:20px">
    <thead>
    <tr style="background-color: #dd4b39; color: white">
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
        <th style="width: 130px;border-color: #9d9d9d" class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
            OPCIONES
        </th>
    </tr>
    </thead>
    <tbody id="div_nueva_especificacion">
    <tr id="tr_nueva_especificacion_1">
        <td style="padding: 5px 0px;border-color: #9d9d9d">
            <select id="id_variedad_1" style="width: 100%;height: 25.8px;" name="id_variedad">
                {{--<option selected disabled>Seleccione</option>--}}
                @foreach($variedades as $v)
                    <option value="{{$v->id_variedad}}">{{$v->nombre}}</option>
                @endforeach
            </select>
        </td>
        <td style="padding: 5px 0px;border-color: #9d9d9d">
            <select id="id_clasificacion_ramo_1" style="width: 100%;height: 25.8px;" name="id_clasificacion_ramo_1">
                {{--<option selected disabled>Seleccione</option>--}}
                @foreach($clasificacion_ramo as $c)
                    <option value="{{$c->id_clasificacion_ramo}}">{{$c->nombre}}</option>
                @endforeach
            </select>
        </td>
        <td style="padding: 5px 0px;border-color: #9d9d9d">
            <select id="id_empaque_1" style="width: 100%;height: 25.8px;" name="id_empaque_1">
                {{--<option selected disabled>Seleccione</option>--}}
                @foreach($empaque as $e)
                    <option value="{{$e->id_empaque}}">{{explode("|",$e->nombre)[0]}}</option>
                @endforeach
            </select>
        </td>
        <td style="padding: 5px 0px;border-color: #9d9d9d">
            <input type="text" placeholder="Cantidad" id="ramo_x_caja_1" style="width: 100%" value="1" name="ramo_x_caja_1" required>
        </td>
        <td style="padding: 5px 0px;border-color: #9d9d9d">
            <select id="id_presentacion_1" style="width: 100%;height: 25.8px;" name="id_presentacion_1">
                {{--<option selected disabled>Seleccione</option>--}}
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
</table>
<table width="100%" class="table table-responsive table-bordered" style="border-color: #9d9d9d" id="table_content_especificaciones">
    <thead>
    <tr style="background-color: #dd4b39; color: white">
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
        <th style="width: 80px;border-color: #9d9d9d" class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
            OPCIONES
        </th>
    </tr>
    </thead>
    @if(sizeof($listado)>0)
        @php  $anterior = ''; @endphp
        @foreach($listado as $x => $item)
            @foreach($item->especificacionesEmpaque as $y => $esp_emp)
                @foreach($esp_emp->detalles as $z => $det_esp_emp)
                    <tr style="border-top: {{$item->id_especificacion != $anterior ? '2px solid #9d9d9d' : ''}}">
                        <td style="border-color: #9d9d9d; padding: 0px; vertical-align: middle; width: 100px; "
                            class="text-center">
                            {{$det_esp_emp->variedad->nombre}}
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            {{$det_esp_emp->clasificacion_ramo->nombre}}
                        </td>
                        @if($z == 0)
                            <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center"
                                rowspan="{{count($esp_emp->detalles)}}">
                                {{explode('|',$esp_emp->empaque->nombre)[0]}}
                            </td>
                        @endif
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            {{$det_esp_emp->cantidad}}
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            {{$det_esp_emp->empaque_p->nombre}}
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            {{isset($det_esp_emp->tallos_x_ramos) ? $det_esp_emp->tallos_x_ramos : "-"}}
                        </td>
                        <td style="border-color: #9d9d9d;padding: 0px;vertical-align: middle;" class="text-center">
                            {{isset($det_esp_emp->longitud_ramo) ? $det_esp_emp->longitud_ramo." ".$det_esp_emp->unidad_medida->siglas : "-"}}
                        </td>
                        @if($item->id_especificacion != $anterior)
                            <td style="border-color: #9d9d9d;padding: 0px 0px; vertical-align: middle;" class="text-center"
                                id="td_precio_{{$x+1}}" rowspan="{{getCantDetEspEmp($item->id_especificacion)}}">
                                @if($item->tipo == "N" && $item->estado == 1)
                                    <button type="button" class="btn btn-default btn-xs" title="Ver asignaciones" onclick="asignar_especificacicon('{{$item->id_especificacion}}')">
                                        <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                    </button>
                                @endif
                                <a href="javascript:void(0)" class="btn btn-{{$item->estado == 1 ? 'danger':'success'}} btn-xs" title="{{$item->estado == 1 ? 'Deshabilitar':'Habilitar'}}"
                                   onclick="update_especificacion('{{$item->id_especificacion}}','{{$item->estado}}','{{csrf_token()}}')">
                                    <i class="fa fa-fw fa-{{$item->estado == 1 ? 'trash':'undo'}}" style="color: white" ></i>
                                </a>
                            </td>
                        @endif
                    </tr>
                    @php  $anterior = $item->id_especificacion;  @endphp
                @endforeach
            @endforeach
        @endforeach
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
    {{--@if(sizeof($listado)>0)
        @foreach($listado as $key => $item)
                    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                        class="{{$item->estado == 1 ? '':'error'}}" id="row_especificaciones_{{$item->id_especificacion}}">
                        @php $esp = getDetalleEspecificacion($item->id_especificacion);@endphp
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
                                @php $b = 1; @endphp
                                @foreach($esp as $f => $e)
                                    @for($a=1;$a<=getCantDetEspEmp($e["id_especificacion_empaque"]); $a++)

                                        @if(($f+1) == $b)
                                        <li style="list-style: none;{{count($esp) != 1 ? "border-bottom: 1px solid silver" : ""}}">
                                            {{$e["caja"]}}
                                        </li>
                                        @endif
                                          @php $b++ @endphp
                                    @endfor
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
                            @if($item->tipo == "N" && $item->estado == 1)
                            <button type="button" class="btn btn-default btn-xs" title="Ver asignaciones" onclick="asignar_especificacicon('{{$item->id_especificacion}}')">
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
    @endif--}}
</table>
<div id="pagination_listado_especificaciones">
    {!! str_replace('/?','?',$listado->render()) !!}
</div>
</div>

