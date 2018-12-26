<div style="overflow-x: scroll; max-width: 100%">
    <table class="table table-responsive table-bordered" width="100%"
           style="border: 2px solid #9d9d9d; font-size: 0.8em;">
        <tr>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white"
                width="15%" rowspan="2" colspan="2">
                Fecha
            </th>
            @foreach(getVariedades() as $variedad)
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white"
                    width="{{75/count(getVariedades())}}%" colspan="2">
                    {{$variedad->nombre}}
                </th>
            @endforeach
        </tr>
        <tr>
            @foreach(getVariedades() as $variedad)
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white">
                    Cosechado
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white">
                    Acumulado
                </th>
            @endforeach
        </tr>
        @foreach($fechas as $item)
            <tr>
                <th class="text-center"
                    style="border-color: #9d9d9d; background-color: {{$item == $fecha ? '#b9ffb4' : '#e9ecef'}}; border-bottom: 3px solid #9d9d9d; text-align:center"
                    rowspan="4">
                    {{convertDateToText($item)}}
                </th>
            </tr>
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #add8e6">
                    Disponibles
                </th>
                @foreach(getVariedades() as $variedad)
                    <td class="text-center" style="border-color: #9d9d9d" title="Cosechado">
                        {{$variedad->getDisponiblesToFecha($item)['cosechado']}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d" title="Acumulado">
                        {{$variedad->getDisponiblesToFecha($item)['acumulado']}}
                    </td>
                @endforeach
            </tr>
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #add8e6">
                    Pedidos
                </th>
                @foreach(getVariedades() as $variedad)
                    <td class="text-center" style="border-color: #9d9d9d" title="Cosechado" colspan="2">
                        {{$variedad->getPedidosToFecha($item)}}
                    </td>
                @endforeach
            </tr>
            <tr style="border-bottom: 3px solid #9d9d9d; background-color: #e9ecef">
                <th class="text-center" style="border-color: #9d9d9d">
                    Saldo
                </th>
                @foreach(getVariedades() as $variedad)
                    <td class="text-center" style="border-color: #9d9d9d">
                        ----
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        ----
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>
</div>