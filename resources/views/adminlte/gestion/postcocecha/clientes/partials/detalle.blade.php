<div class="row">
    <div class="col-md-8">
        <table width="100%" class="table table-responsive table-bordered sombra_estandar"
               style="font-size: 0.8em; border-color: #9d9d9d" id="table_content_detalles">
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d;width: 40%;">
                    Nombre completo
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$dataCliente->nombre}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d;width: 40%;">
                    Identificación
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$dataCliente->ruc}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d;width: 40%;">
                    País
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$pais}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d;width: 40%;">
                    Provincia
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$dataCliente->provincia}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d;width: 40%;">
                    Dirección
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$dataCliente->direccion}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d;width: 40%;">
                    Teléfono
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$dataCliente->telefono}}
                </td>
            </tr>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d;width: 40%;">
                    Correo
                </th>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$dataCliente->correo}}

                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-4">
        <div class="list-group">
            <a href="javascript:void(0)" class="list-group-item list-group-item-action active">
                Opciones
            </a>
            <a href="javascript:void(0)" class="list-group-item list-group-item-action" title="Editar detalles de usuario"
               onclick="cargar_opcion('div_content_opciones','{{$dataCliente->id_cliente}}','clientes/add','add')">
                Editar usuario
            </a>
            <a href="javascript:void(0)" class="list-group-item list-group-item-action" title="Añadir nueva información"
               onclick="add_info('{{$dataCliente->id_detalle_cliente}}','{{$dataCliente->id_cliente}}')">
                Añadir información personalizada
            </a>
            @if(count(getDocumentos('detalle_cliente', $dataCliente->id_detalle_cliente )) > 0)
                <a href="javascript:void(0)" class="list-group-item list-group-item-action" title="Ver información personalizada"
                   onclick="ver_documentos('detalle_cliente','{{$dataCliente->id_detalle_cliente}}','{{$dataCliente->id_cliente}}')">
                    Ver información personalizada
                </a>
            @endif
            <a href="javascript:void(0)" class="list-group-item list-group-item-action" title="Añadir nueva información"
               onclick="cargar_opcion('campos_contactos','{{$dataCliente->id_detalle_cliente}}','clientes/ver_contactos_clientes')">
                Añadir contactos
            </a>
            <a href="javascript:void(0)" class="list-group-item list-group-item-action" title="Consignatario"
               onclick="cargar_opcion('div_consignatario','{{$dataCliente->id_cliente}}','clientes/agregar_consignatario')">
                Consignatarios
            </a>
            <a href="javascript:void(0)" class="list-group-item list-group-item-action" title="Añadir nueva información"
               onclick="cargar_opcion('campos_agencia_carga','','clientes/ver_agencias_carga')">
                Agencia de carga
            </a>
            {{--<a href="javascript:void(0)" class="list-group-item list-group-item-action" title="Administrar especificaciones"
               onclick="admin_especificaciones('{{$dataCliente->id_cliente}}')">
                Especificaciones
            </a>
            <a href="javascript:void(0)" class="list-group-item list-group-item-action" title="Administrar pedidos"
               onclick="cargar_opcion('div_pedidos','{{$dataCliente->id_cliente}}','clientes/listar_pedidos')">
                Pedidos
            </a>--}}
        </div>
    </div>
</div>

@if(count(getDocumentos('detalle_cliente', $dataCliente->id_detalle_cliente )) > 0)
    <div id="content_documentos"></div>
@endif

<div id="div_content_opciones" style="margin-top: 10px"></div>

<div id="include_agencia_carga" class="hide">
    @include('adminlte.gestion.postcocecha.clientes.forms.add_agencias_carga')
</div>

<div id="include_contactos_cliente" class="hide">
    @include('adminlte.gestion.postcocecha.clientes.forms.add_contactos')
</div>

<div id="include_contactos_cliente" class="hide">
    @include('adminlte.gestion.postcocecha.clientes.forms.add_contactos')
</div>
