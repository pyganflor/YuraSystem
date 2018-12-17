@if(count($listado)>0)
    <legend class="text-center">
        Pedidos
    </legend>
    <div class="accordion" id="accordionExample">
        <ul class="list-group">
            @foreach($listado as $fecha)
                <li class="list-group-item" style="border: 1px solid #9d9d9d"
                    onmouseover="$(this).css('background-color','#e9ecef')" onmouseleave="$(this).css('background-color','')">
                    <a href="javascript:void(0)" data-toggle="collapse" data-target="#div_content_fecha_pedido_{{$fecha->fecha_pedido}}"
                       aria-expanded="false" aria-controls="div_content_fecha_pedido_{{$fecha->fecha_pedido}}">
                        {{getDias()[transformDiaPhp(date('w', strtotime($fecha->fecha_pedido)))]}}
                        {{convertDateToText($fecha->fecha_pedido)}}
                    </a>

                    <div id="div_content_fecha_pedido_{{$fecha->fecha_pedido}}" class="collapse"
                         aria-labelledby="div_header_fecha_pedido_{{$fecha->fecha_pedido}}" data-parent="#accordionExample">
                        <div class="text-center well">
                            @if(count(getResumenPedidosByFecha($fecha->fecha_pedido, $variedad->id_variedad)) > 0 ||
                                count(getResumenPedidosByFechaOfTallos($fecha->fecha_pedido, $variedad->id_variedad)))
                                @foreach(getResumenPedidosByFecha($fecha->fecha_pedido, $variedad->id_variedad) as $item)
                                    <input type="checkbox" id="check_pedido_{{$fecha->fecha_pedido}}_{{$item->id_clasificacion_ramo}}"
                                           class="check_pedidos"
                                           onchange="check_pedido('{{$fecha->fecha_pedido}}','{{$item->id_clasificacion_ramo}}')">
                                    <label for="check_pedido_{{$fecha->fecha_pedido}}_{{$item->id_clasificacion_ramo}}" class="mouse-hand">
                                        {{$item->cantidad}} Ramos
                                        de {{getCalibreRamoById($item->id_clasificacion_ramo)->nombre}}{{getCalibreRamoById($item->id_clasificacion_ramo)->unidad_medida->siglas}}
                                        de {{$variedad->nombre}}
                                    </label>
                                    <input type="hidden" id="val_fecha_{{$fecha->fecha_pedido}}_{{$item->id_clasificacion_ramo}}"
                                           value="{{$fecha->fecha_pedido}}">
                                    <input type="hidden" id="val_ramo_{{$fecha->fecha_pedido}}_{{$item->id_clasificacion_ramo}}"
                                           value="{{$item->id_clasificacion_ramo}}">
                                    <br>
                                @endforeach
                                @foreach(getResumenPedidosByFechaOfTallos($fecha->fecha_pedido, $variedad->id_variedad) as $item)
                                    <input type="checkbox" id="check_pedido_{{$fecha->fecha_pedido}}_{{$item->id_clasificacion_ramo}}"
                                           class="check_pedidos"
                                           onchange="check_pedido('{{$fecha->fecha_pedido}}','{{$item->id_clasificacion_ramo}}')">
                                    <label for="check_pedido_{{$fecha->fecha_pedido}}_{{$item->id_clasificacion_ramo}}" class="mouse-hand">
                                        {{$item->cantidad}} Ramos
                                        de {{getCalibreRamoById($item->id_clasificacion_ramo)->nombre}}{{getCalibreRamoById($item->id_clasificacion_ramo)->unidad_medida->siglas}}
                                        de {{$variedad->nombre}} de {{$item->tallos_x_ramos}} ramos c/u
                                    </label>
                                    <input type="hidden" id="val_fecha_{{$fecha->fecha_pedido}}_{{$item->id_clasificacion_ramo}}"
                                           value="{{$fecha->fecha_pedido}}">
                                    <input type="hidden" id="val_ramo_{{$fecha->fecha_pedido}}_{{$item->id_clasificacion_ramo}}"
                                           value="{{$item->id_clasificacion_ramo}}">
                                    <br>
                                @endforeach
                            @else
                                <small>
                                    No hay pedidos para {{$variedad->nombre}}
                                </small>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <p class="text-center">
        No se han encontrado pedidos en el rango de tiempo especificado
    </p>
@endif

<script>
    function check_pedido(fecha, ramo) {
        $('.check_pedidos').prop('checked', false);
        $('#check_pedido_' + fecha + '_' + ramo).prop('checked', true);
    }
</script>