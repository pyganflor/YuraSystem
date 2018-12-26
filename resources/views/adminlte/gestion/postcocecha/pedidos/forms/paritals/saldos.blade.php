<table class="table table-responsive table-bordered" width="100%"
       style="border: 2px solid #9d9d9d; font-size: 0.8em;">
    <tr>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            Variedades
        </th>
        @foreach(getVariedades() as $variedad)
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                {{$variedad->nombre}}
            </th>
        @endforeach
    </tr>
    <tr>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            Disponibles
        </th>
        @foreach(getVariedades() as $variedad)
            <td class="text-center" style="border-color: #9d9d9d">
                {{$variedad->nombre}}
            </td>
        @endforeach
    </tr>
    <tr>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            Pedidos
        </th>
        @foreach(getVariedades() as $variedad)
            <td class="text-center" style="border-color: #9d9d9d">
                {{$variedad->nombre}}
            </td>
        @endforeach
    </tr>
    <tr>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            Saldo
        </th>
        @foreach(getVariedades() as $variedad)
            <td class="text-center" style="border-color: #9d9d9d">
                {{$variedad->nombre}}
            </td>
        @endforeach
    </tr>
</table>