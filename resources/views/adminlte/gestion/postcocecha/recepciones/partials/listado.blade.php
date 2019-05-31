<div id="table_recepciones">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered"
               style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_recepciones">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    SEMANA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FECHA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    TALLOS
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    CANTIDADES
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    OPCIONES
                </th>
            </tr>
            </thead>
            @foreach($listado as $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')"
                    onmouseleave="$(this).css('background-color','')" class="{{$item->estado == 1?'':'error'}}"
                    id="row_recepciones_{{$item->id_recepcion}}">
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->semana}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{substr($item->fecha_ingreso,0,16)}}</td>
                    <td style="border-color: #9d9d9d" class="text-center mouse-hand" data-toggle="popover"
                        title="Cantidad de tallos" id="popover_tallos_{{$item->id_recepcion}}">
                        <a href="javascript:void(0)">
                            {{getRecepcion($item->id_recepcion)->cantidad_tallos()}}
                        </a>
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        @foreach(getRecepcion($item->id_recepcion)->desgloses as $recepcion)
                            {{$recepcion->variedad->planta->nombre}} - {{$recepcion->variedad->siglas}}:
                            <strong>{{$recepcion->cantidad_mallas}}</strong> mallas
                            de <strong>{{$recepcion->tallos_x_malla}}</strong> tallos =
                            <strong>{{$recepcion->cantidad_mallas * $recepcion->tallos_x_malla}}</strong>
                            módulo <strong>{{$recepcion->modulo->nombre}}</strong>
                            <br>
                        @endforeach
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        <a href="javascript:void(0)" class="btn btn-default btn-xs" title="Detalles"
                           onclick="ver_recepcion('{{$item->id_recepcion}}')"
                           id="btn_view_recepcion_{{$item->id_recepcion}}">
                            <i class="fa fa-fw fa-eye" style="color: black"></i>
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