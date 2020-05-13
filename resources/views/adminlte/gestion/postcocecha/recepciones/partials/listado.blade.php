<div id="table_recepciones">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-bordered table-striped"
               style="font-size: 0.8em; border-color: #9d9d9d; border-radius: 18px 18px 0 0;"
               id="table_content_recepciones">
            <thead>
            <tr>
                <th class="text-left th_yura_default" style="border-radius: 18px 0 0 0;">
                    SEMANA
                </th>
                <th class="text-left th_yura_default">
                    FECHA
                </th>
                <th class="text-left th_yura_default">
                    TALLOS
                </th>
                <th class="text-left th_yura_default">
                    CANTIDADES
                </th>
                <th class="text-left th_yura_default" style="border-radius: 0 18px 0 0">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#e5f7f3 !important');"
                    onmouseleave="$(this).css('background-color','white');" class="{{$item->estado == 1?'':'error'}}"
                    id="row_recepciones_{{$item->id_recepcion}}" style="background-color: white; border-bottom: 1px solid black">
                    <td class="text-left" style="border-color: #9d9d9d">{{$item->semana}}</td>
                    <td class="text-left" style="border-color: #9d9d9d">{{substr($item->fecha_ingreso,0,16)}}</td>
                    <td class="text-left" title="Cantidad de tallos" style="border-color: #9d9d9d">
                        {{getRecepcion($item->id_recepcion)->cantidad_tallos()}}
                    </td>
                    <td class="text-left" style="border-color: #9d9d9d">
                        @foreach(getRecepcion($item->id_recepcion)->desgloses as $recepcion)
                            {{$recepcion->variedad->planta->nombre}} - {{$recepcion->variedad->siglas}}:
                            <strong>{{$recepcion->cantidad_mallas}}</strong> mallas
                            de <strong>{{$recepcion->tallos_x_malla}}</strong> tallos =
                            <strong>{{$recepcion->cantidad_mallas * $recepcion->tallos_x_malla}}</strong>
                            módulo <strong>{{$recepcion->modulo->nombre}}</strong>
                            <br>
                        @endforeach
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <a href="javascript:void(0)" class="btn btn-yura_primary btn-xs" title="Detalles"
                           onclick="ver_recepcion('{{$item->id_recepcion}}')"
                           id="btn_view_recepcion_{{$item->id_recepcion}}">
                            <i class="fa fa-fw fa-eye"></i>
                        </a>
                        {{--@if(getUsuario(Session::get('id_usuario'))->rol()->tipo == 'P')
                            <a href="javascript:void(0)"
                               class="btn {{$item->estado == 1 ? 'btn-success' : 'btn-danger'}} btn-xs"
                               title="{{$item->estado == 1 ? 'Desactivar' : 'Activar'}}"
                               onclick="eliminar_recepcion('{{$item->id_recepcion}}', '{{$item->estado}}')"
                               id="btn_recepciones_{{$item->id_recepcion}}">
                                <i class="fa fa-fw {{$item->estado == 1 ? 'fa-trash' : 'fa-unlock'}}"
                                   style="color: black"
                                   id="icon_recepciones_{{$item->id_recepcion}}"></i>
                            </a>
                        @endif--}}
                    </td>
                </tr>
                <script>
                    $('#popover_tallos_{{$item->id_recepcion}}').popover({
                        animation: true,
                        html: true,
                        content: '@foreach(getRecepcion($item->id_recepcion)->desgloses as $item)' +
                        '{{$item->variedad->siglas}}: {{$item->cantidad_mallas}} mallas de {{$item->tallos_x_malla}} tallos = ' +
                        '{{$item->cantidad_mallas * $item->tallos_x_malla}}' +
                        '<br>' +
                        '@endforeach'
                    });
                </script>
            @endforeach
        </table>
        <div id="pagination_listado_recepciones">
            {!! str_replace('/?','?',$listado->render()) !!}
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>