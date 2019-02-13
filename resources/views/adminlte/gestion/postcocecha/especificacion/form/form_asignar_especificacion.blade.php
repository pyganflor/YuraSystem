<form>
    <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
           id="table_content_especificaciones">
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
                    @php
                        $check = '';
                        foreach ($asginacion as $a) {
                            if($a->id_cliente == $item->id_cliente)
                            $check = 'checked';
                        }
                    @endphp
                    <input type="checkbox" {{$check}} id="cliente_{{$item->id_cliente}}" name="cliente"
                           onclick="verificar_pedido_especificacion('{{$item->id_cliente}}','{{$id_especificacion}}',this.id)" value="{{$id_especificacion}}">
                </td>
            </tr>
        @endforeach
    </table>
    {{--<div id="pagination_listado_especificaciones">
        {!! str_replace('/?','?',$listado->render()) !!}
    </div>--}}
</form>
