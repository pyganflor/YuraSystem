<table>
    @foreach($listado as $key => $item)
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
            class="{{$item->estado == 1  ? '':'error'}}" id="row_agencia_{{$item->id_agencia_carga}}">
            <td style="border-color: #9d9d9d" class="text-center">
                {{$item->nombre}}
            </td>
            <td style="border-color: #9d9d9d" class="text-center">
                {{$item->identificacion}}
            </td>
            <td style="border-color: #9d9d9d" class="text-center">
                {{$item->codigo}}
            </td>
            <td style="border-color: #9d9d9d" class="text-center">
                {{$item->correo}}
            </td>
            <td style="border-color: #9d9d9d" class="text-center">
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-xs" title="Editar"
                            onclick="create_agencia_carga('{{$item->id_agencia_carga}}','{{csrf_token()}}')" id="ver_agencia_carga">
                        <i class="fa fa-fw fa-pencil" style="color: black"></i>
                    </button>
                    <button type="button" class="btn {{$item->estado == 1 ? 'btn-success' : 'btn-danger'}} btn-xs" title="Desactivar"
                            onclick="actualizar_agencia_carga('{{$item->id_agencia_carga}}','{{$item->estado}}')"
                            id="boton_agencia_carga_{{$item->id_agencia_carga}}">
                        <i class="fa fa-fw {{$item->estado == 1 ? 'fa-trash' : 'fa-unlock'}}" style="color: black"
                           id="icon_agencia_carga_{{$item->id_agencia_carga}}"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
</table>
