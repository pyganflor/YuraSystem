<table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d" id="table_productos_viculados">
    <thead>
    <tr style="background-color: #dd4b39; color: white">
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            PRESENTACIÓN YURASYSTEM
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
            PRESENTACIÓN VENTURE
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
            style="border-color: #9d9d9d">
            CÓDIGO VENTURE
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
            style="border-color: #9d9d9d">
            EMPRESA
        </th>
        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
            style="border-color: #9d9d9d">
            OPCIONES
        </th>
    </tr>
    </thead>
    <tbody id="body_productos_viculados">
        @foreach($productos_vinculados as $key => $item)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                <td style="border-color: #9d9d9d" class="text-center">
                    {{explode("|",$item->presentacion_yura)[7]}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{getPrductoVenture($item->codigo_venture)}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$item->codigo_venture}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$item->empresa->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-xs" title="Elimnar vinculación"
                                onclick="delete_vinculacion('{{$item->id_producto_yura_venture}}')" id="ver_agencia_carga">
                            <i class="fa fa-fw fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
