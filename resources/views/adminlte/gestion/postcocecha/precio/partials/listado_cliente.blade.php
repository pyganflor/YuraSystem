
    <div class="">
        <div id="table_cliente">
            @if(sizeof($clientes)>0)
                <table width="100%" class="table table-responsive table-bordered" style=" border-color: #9d9d9d"
                       id="table_content_clientes">
                    <thead>
                        <tr style="background-color: #dd4b39; color: white">
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:70%">
                                CLIENTE
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                                OPCIONES
                            </th>
                        </tr>
                    </thead>
                    @foreach($clientes as $cliente)
                        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                            <td style="border-color: #9d9d9d" class="text-center">
                                {{$cliente->nombre}}
                            </td>
                            <td style="border-color: #9d9d9d" class="text-center">
                                <button type="button" class="btn btn-default btn-xs" title="Asignar precios a las especificaciones del cliente" onclick="precio_cliente_especificacion('{{$cliente->id_cliente}}')">
                                    <i class="fa fa-usd" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
            @endif
        </div>
        <div id="pagination_listado_clientes">
            {!! str_replace('/?','?',$clientes->render()) !!}
        </div>
    </div>






