<div style="overflow-x: scroll; width: 100%">
    <table class="table-bordered" style="border: 2px solid #9d9d9d; width: 100%">
        @foreach($grupos_menu as $g)
            <tr onclick="$('.tr_menu_from_{{$g->id_grupo_menu}}').toggleClass('hide')">
                <th class="text-left mouse-hand" style="background-color: #00A6C7; border-color: #9d9d9d; color: white" colspan="3">
                    <span style="margin-left: 5px">{{$g->nombre}}</span>
                </th>
            </tr>
            @foreach($g->menus->where('estado', 'A')->sortBy('nombre') as $m)
                <tr class="tr_menu_from_{{$g->id_grupo_menu}} hide" onclick="$('.tr_submenu_from_{{$m->id_menu}}').toggleClass('hide')">
                    <th class="text-left mouse-hand" style="background-color: #e9ecef; border-color: #9d9d9d" colspan="3">
                        <span style="margin-left: 15px">{{$m->nombre}}</span>
                    </th>
                </tr>
                @foreach($m->submenus_activos as $s)
                    @if(isActive_action($s->id_submenu))
                        <tr class="tr_submenu_from_{{$m->id_menu}} hide" id="tr_submenu_{{$s->id_submenu}}"
                            onmouseover="$(this).css('background-color', '#77dbf9')" onmouseleave="$(this).css('background-color', '')">
                            <td class="text-left" style="border-color: #9d9d9d; width: 40%">
                                <label for="check_{{$s->id_submenu}}" class="mouse-hand" style="margin-left: 25px">{!! $s->nombre !!}</label>
                                <span class="pull-right text-green" id="icon_selected_{{$s->id_submenu}}">
                            </span>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                <div style="overflow-x: scroll; max-width: 300px; margin: 0 auto">
                                    <table class="table-bordered" style="width: 100%">
                                        <tr>
                                            @foreach($iconos as $i)
                                                <td class="text-center mouse-hand" style="border-color: #9d9d9d; width: 45px"
                                                    onmouseover="$(this).css('background-color', 'white')"
                                                    onmouseleave="$(this).css('background-color', '')"
                                                    onclick="select_icono('{{$i->id_icono}}', '{{$i->nombre}}', '{{$s->id_submenu}}')">
                                                    <i class="fa fa-fw fa-{{$i->nombre}}" style="margin-right: 5px; margin-left: 5px"></i>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </table>
                                </div>
                                <input type="hidden" id="id_icon_{{$s->id_submenu}}">
                            </td>
                            <th class="text-center mouse-hand" style="border-color: #9d9d9d; width: 20px">
                                <input type="checkbox" id="check_{{$s->id_submenu}}" onchange="seleccionar_submenu('{{$s->id_submenu}}')"
                                        {{in_array($s->id_submenu, $ids_submenu_ad) ? 'checked' : ''}}>
                            </th>
                        </tr>
                    @endif
                @endforeach
            @endforeach
        @endforeach
    </table>
</div>