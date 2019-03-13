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
        @php $esp = getDetalleEspecificacion($id_especificacion);@endphp
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
    </tr>
</table>






<div class="text-right">
    <button type="button" onclick="add_input()" id="btn_add_input" title="Agregar campos" class="btn btn-success btn-xs">
        <i class="fa fa-plus" aria-hidden="true"></i>
    </button>
    <button type="button" onclick="delete_input('{{count($cliente_pedido_especificacion)}}')" title="Eliminar campos" class="btn btn-danger btn-xs">
        <i class="fa fa-minus" aria-hidden="true"></i>
    </button>
</div>
<form id="form_add_precio_especificicacion_cliente">
    @foreach($cliente_pedido_especificacion as $key => $cpe)
        <div class="row" id="row_{{$key+1}}">
            <input type="hidden" id="id_cliente_pedido_especificacion_{{$key+1}}" value="{{$cpe->id_cliente_pedido_especificacion}}">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="nombre_marca">Cliente</label>
                    <select id="id_cliente_{{$key+1}}" name="id_cliente" class="form-control" disabled required>
                        <option disabled selected> Seleccione </option>
                        @foreach($clientes as $c)
                            <option {{$cpe->id_cliente == $c->id_cliente ? "selected" : ""}} value="{{$c->id_cliente}}"> {{$c->nombre}} </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="Precio">Precio $</label>
                    <input type="text" id="precio_{{$key+1}}" name="precio" class="form-control" min="1"
                           onkeypress="return barra_string(this,event)" value="{{$cpe->precio}}" required>
                </div>
            </div>
        </div>
    @endforeach
</form>
