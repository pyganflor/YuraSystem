@foreach($accesos as $item)
    <li class="acceso_directo" title="{{$item->submenu->nombre}}">
        <a href="javascript:void(0)" onclick="cargar_url('{{$item->submenu->url}}')">
            @if($item->id_icono != '')
                <i class="fa fa-fw fa-{{$item->icono->nombre}} text-color_yura"></i>
            @else
                {{str_limit($item->submenu->nombre, 3)}}
            @endif
        </a>
    </li>
@endforeach