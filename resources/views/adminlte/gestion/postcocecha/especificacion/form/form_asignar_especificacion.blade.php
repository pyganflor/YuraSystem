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
        </tr>
    </thead>
    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
    @php  $anterior = "";  @endphp
    @foreach($data_especificacion as $x => $item)
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
                </tr>
                @php  $anterior = $item->id_especificacion;  @endphp
            @endforeach
        @endforeach
    @endforeach
        {{--@php $esp = getDetalleEspecificacion($id_especificacion);@endphp
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
        </td>--}}
    </tr>
</table>
<table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d" id="table_content_especificaciones">
    <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                CLIENTE
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                ASIGNAR
            </th>
        </tr>
    </thead>
    @foreach($listado as $item)
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
            <td style="border-color: #9d9d9d" class="text-center">{{$item->nombre}}</td>
            <td style="border-color: #9d9d9d" class="text-center error_{{$item->id_cliente}}">
                @php  $check = ''; foreach ($asginacion as $a) if($a->id_cliente == $item->id_cliente)  $check = 'checked'; @endphp
                    <input type="checkbox" {{$check}} id="cliente_{{$item->id_cliente}}" name="cliente"
                           onclick="verificar_pedido_especificacion('{{$item->id_cliente}}','{{$id_especificacion}}',this.id)" value="{{$id_especificacion}}">
            </td>
        </tr>
    @endforeach
</table>
