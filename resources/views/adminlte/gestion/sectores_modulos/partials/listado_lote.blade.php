<table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
       id="table_content_lotes">
    <thead>
    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
        <th class="text-center th_yura_default" style="border-color: #9d9d9d" colspan="2">LOTE</th>
        <th class="text-center th_yura_default" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-yura_default" title="Añadir Lote" onclick="add_lote()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    </thead>
    @if(sizeof($lotes)>0)
        @foreach($lotes as $item)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="row_lote {{$item->estado == 1 ? '' : 'error'}}" id="row_lote_{{$item->id_lote}}">
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$item->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$item->area != '' ? $item->area : 0}} ha
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-yura_default" type="button" title="Editar"
                                onclick="edit_lote('{{$item->id_lote}}')">
                            <i class="fa fa-fw fa-pencil"></i>
                        </button>
                        <button class="btn btn-xs btn-yura_danger" type="button" title="{{$item->estado == 1 ? 'Desactivar' : 'Activar'}}"
                                onclick="cambiar_estado_lote('{{$item->id_lote}}','{{$item->estado}}')">
                            <i class="fa fa-fw fa-{{$item->estado == 1 ? 'trash' : 'unlock'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
            class="row_lote">
            <td style="border-color: #9d9d9d" class="text-center" colspan="3">
                No hay lotes registrados en este módulo
            </td>
        </tr>
    @endif
</table>
