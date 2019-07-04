<table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d" id="table_listado_notificaciones">
    <thead>
    <tr>
        <th class="text-center" style="background-color: #357ca5; color: white; border-color: #9d9d9d">
            NOMBRE
        </th>
        <th class="text-center" style="background-color: #357ca5; color: white; border-color: #9d9d9d">
            TIPO
        </th>
        <th class="text-center" style="background-color: #357ca5; color: white; border-color: #9d9d9d">
            ICONO
        </th>
        <th class="text-center" style="background-color: #357ca5; color: white; border-color: #9d9d9d">
            OPCIONES
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($listado as $item)
        <tr>
            <td class="text-center" style="border-color: #9d9d9d">
                <input type="text" class="text-center" id="nombre_not_{{$item->id_notificacion}}" value="{{$item->nombre}}" required
                       style="width: 100%">
                <p style="display: none">{{$item->nombre}}</p>
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <select id="tipo_not_{{$item->id_notificacion}}" style="width: 100%;">
                    <option value="S" {{$item->tipo == 'S' ? 'selected' : ''}}>Sistema</option>
                    <option value="M" {{$item->tipo == 'M' ? 'selected' : ''}}>Mensaje</option>
                </select>
                @if($item->tipo == 'S')
                    <p style="display: none">Sistema</p>
                @else
                    <p style="display: none">Mensaje</p>
                @endif
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <select id="icon_not_{{$item->id_notificacion}}" style="width: 100%;">
                    @foreach($iconos as $i)
                        <option value="{{$i->id_icono}}" {{$item->id_icono == $i->id_icono ? 'selected' : ''}}>{{$i->nombre}}</option>
                    @endforeach
                </select>
                <p style="display: none">{{$item->icono->nombre}}</p>
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                <div class="btn-group">
                    <button class="btn btn-xs btn-success" title="Guardar notificación"
                            onclick="update_notificacion('{{$item->id_notificacion}}')">
                        <i class="fa fa-fw fa-save"></i>
                    </button>
                    @if($item->estado == 1)
                        <button class="btn btn-xs btn-primary" title="Usuarios" onclick="admin_usuarios('{{$item->id_notificacion}}')">
                            <i class="fa fa-fw fa-users"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" title="Desactivar notificación"
                                onclick="cambiar_estado('{{$item->id_notificacion}}')">
                            <i class="fa fa-fw fa-lock"></i>
                        </button>
                    @else
                        <button class="btn btn-xs btn-warning" title="Activar notificación"
                                onclick="cambiar_estado('{{$item->id_notificacion}}')">
                            <i class="fa fa-fw fa-check"></i>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<select id="tipo_not" style="display: none">
    @foreach($iconos as $i)
        <option value="{{$i->id_icono}}">
            {{$i->nombre}}
        </option>
    @endforeach
</select>