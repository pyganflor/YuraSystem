<table class="table table-striped table-responsive table-bordered" width="100%"
       style="border: 2px solid #9d9d9d; font-size: 0.8em;">
    <tr>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            Disponibles
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            Pedidos
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            Saldo
        </th>
    </tr>
</table>