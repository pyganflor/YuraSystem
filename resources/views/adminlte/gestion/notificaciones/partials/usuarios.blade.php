<table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white;">
            Nombre
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">
            Username
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #357ca5; color: white">

        </th>
    </tr>
    @foreach($usuarios as $u)
        <tr>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$u->nombre_completo}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$u->username}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="checkbox" onchange="save_notificacion_usuario('{{$u->id_usuario}}', '{{$notificacion->id_notificacion}}')"
                       id="check_user_{{$u->id_usuario}}" {{$notificacion->getNotificacionUsuario($u->id_usuario) != '' ? 'checked': ''}}>
            </td>
        </tr>
    @endforeach
</table>