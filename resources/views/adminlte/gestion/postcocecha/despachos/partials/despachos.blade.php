<div id="table_despachos">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_despachos">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    N# Despacho
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    Fecha despacho
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    Responsable
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->n_despacho}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->fecha_despacho}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->resp_transporte}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <a target="_blank" class="btn btn-default btn-xs" href="{{url('despachos/descargar_despacho/'.$item->n_despacho.'')}}">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-xs" title="Cancelar despacho" onclick="update_estado_despacho('{{$item->id_despacho}}','{{$item->estado}}')">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </table>
        <div id="pagination_listado_despachos">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
