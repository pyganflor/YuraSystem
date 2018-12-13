<table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
       id="table_content_variedades">
    <thead>
    <tr class="table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}">
        <th class="text-center" style="border-color: #9d9d9d">VARIEDAD</th>
        <th class="text-center" style="border-color: #9d9d9d">SIGLAS</th>
        <th class="text-center" style="border-color: #9d9d9d">UNIDAD DE MEDIDA </th>
        <th class="text-center" style="border-color: #9d9d9d">T x R (CANTIDAD)</th>
        <th class="text-center" style="border-color: #9d9d9d">Minimo apertura (Dias)</th>
        <th class="text-center" style="border-color: #9d9d9d">Maximo apertura (Dias)</th>
        <th class="text-center" style="border-color: #9d9d9d">
            <button type="button" class="btn btn-xs btn-default" title="Añadir Variedad" onclick="add_variedad()">
                <i class="fa fa-fw fa-plus"></i>
            </button>
        </th>
    </tr>
    </thead>
    @if(sizeof($variedades)>0)
        @foreach($variedades as $v)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                class="{{$v->estado == 1 ? '' : 'error'}}" id="row_variedad_{{$v->id_variedad}}">
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$v->nombre}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$v->siglas}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$v->unidad_de_medida}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$v->cantidad}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$v->minimo_apertura}}
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$v->maximo_apertura}}
                </td>

                <td style="border-color: #9d9d9d" class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-xs btn-default" type="button" title="Precio"
                                onclick="add_precio('{{$v->id_variedad}}')">
                            <i class="fa fa-usd"></i>
                        </button>
                        <button class="btn btn-xs btn-default" type="button" title="Editar"
                                onclick="edit_variedad('{{$v->id_variedad}}')">
                            <i class="fa fa-fw fa-pencil"></i>
                        </button>
                        <button class="btn btn-xs btn-danger" type="button" title="{{$v->estado == 1 ? 'Desactivar' : 'Activar'}}"
                                onclick="cambiar_estado_variedad('{{$v->id_variedad}}','{{$v->estado}}')">
                            <i class="fa fa-fw fa-{{$v->estado == 1 ? 'trash' : 'unlock'}}"></i>
                        </button>

                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
            <td style="border-color: #9d9d9d" class="text-center mouse-hand" colspan="3">
                No hay variedades registradas para esta planta
            </td>
        </tr>
    @endif
</table>
