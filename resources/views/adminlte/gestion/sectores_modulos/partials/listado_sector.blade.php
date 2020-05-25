<table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
       id="table_content_sectores">
    <thead>
    <tr>
        <th class="text-center th_yura_default" style="border-color: #9d9d9d" colspan="2">SECTOR</th>
        <th class="text-center th_yura_default" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-yura_default" title="Añadir Sector" onclick="add_sector()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    </thead>
    @if(sizeof($sectores)>0)
        @foreach($sectores as $item)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="{{$item->estado == 1 ? '' : 'error'}}" id="row_sector_{{$item->id_sector}}">
                <td style="border-color: #9d9d9d" class="text-center mouse-hand" onclick="select_sector('{{$item->id_sector}}')">
                    <i class="fa fa-fw fa-check hidden icon_hidden_s" id="icon_sector_{{$item->id_sector}}"></i> {{$item->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center mouse-hand" onclick="select_sector('{{$item->id_sector}}')">
                    {{number_format($item->getAreaTotal(), 2)}} m<sup>2</sup>
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-yura_default" type="button" title="Editar"
                                onclick="edit_sector('{{$item->id_sector}}')">
                            <i class="fa fa-fw fa-pencil"></i>
                        </button>
                        <button class="btn btn-xs btn-yura_danger" type="button" title="{{$item->estado == 1 ? 'Desactivar' : 'Activar'}}"
                                onclick="cambiar_estado_sector('{{$item->id_sector}}','{{$item->estado}}')">
                            <i class="fa fa-fw fa-{{$item->estado == 1 ? 'trash' : 'unlock'}}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td style="border-color: #9d9d9d" class="text-center" colspan="3">
                No hay sectores registrados
            </td>
        </tr>
    @endif
</table>
