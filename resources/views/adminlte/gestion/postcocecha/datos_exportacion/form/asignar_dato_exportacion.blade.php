<table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d" id="table_content_dato_exportacion">
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
                @php  $check = ''; foreach ($asginacion as $a) if($a->id_cliente == $item->id_cliente) $check = 'checked'; @endphp
                <input type="checkbox" {{$check}} id="cliente_{{$item->id_cliente}}" name="cliente"  value="{{$item->id_cliente}}"
                onclick="asignar_dato_exportacion('{{$id_dato_exportacion}}','{{$item->id_cliente}}',this)">
            </td>
        </tr>
    @endforeach
</table>
