<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Rendimiento de
            <span class="badge">{{$clasificacion_verde->personal}}</span>
            personas:
            <span class="badge">{{$clasificacion_verde->getRendimiento()}}</span>
            tallos por persona/hora
        </h3>
    </div>
    <div class="box-body">
        <label for="check_mostrar_ocultar_detalles_rendimiento" class="pull-right" style="margin-left: 5px">Mostrar detalles de
            rendimiento</label>
        <input type="checkbox" class="pull-right" onchange="ocultar_mostrar_detalles_rendimiento()"
               id="check_mostrar_ocultar_detalles_rendimiento">
        <table class="table-responsive table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d">
            <tr style="background-color: #e9ecef">
                <th class="text-center" style="border-color: #9d9d9d">
                    Fecha y Hora
                </th>
                <th class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                    Variedad
                </th>
                <th class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                    Calibre
                </th>
                <th class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                    Cantidad
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    Total
                </th>
                <th class="text-center elemento_especifico"
                    style="border-color: #9d9d9d; background-color: #357CA5; color: white; display: none;">
                    x Persona
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white;">
                    Rendimiento
                </th>
            </tr>
            @foreach($listado as $item)
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$item->fecha}}
                    </td>
                    <td class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                        <ul class="list-unstyled">
                            @foreach($clasificacion_verde->getDetallesByFecha($item->fecha) as $li)
                                <li style="border-bottom: 1px solid #9d9d9d">
                                    {{$li->variedad->siglas}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                        <ul class="list-unstyled">
                            @foreach($clasificacion_verde->getDetallesByFecha($item->fecha) as $li)
                                <li style="border-bottom: 1px solid #9d9d9d">
                                    {{explode('|',$li->clasificacion_unitaria->nombre)[0]}}{{$li->clasificacion_unitaria->unidad_medida->siglas}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                        <ul class="list-unstyled">
                            @foreach($clasificacion_verde->getDetallesByFecha($item->fecha) as $li)
                                <li style="border-bottom: 1px solid #9d9d9d">
                                    {{$li->cantidad_ramos * $li->tallos_x_ramos}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$item->cantidad}}
                    </td>
                    <td class="text-center elemento_especifico" style="border-color: #9d9d9d; display: none;">
                        <ul class="list-unstyled">
                            @foreach($clasificacion_verde->getDetallesByFecha($item->fecha) as $li)
                                <li style="border-bottom: 1px solid #9d9d9d">
                                    {{round(($li->cantidad_ramos * $li->tallos_x_ramos) / $clasificacion_verde->personal,2)}}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{round($item->cantidad/$clasificacion_verde->personal,2)}}
                    </td>
                </tr>
            @endforeach
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Total
                </th>
                <th class="text-center" style="border-color: #9d9d9d" id="th_total_tallos">
                    {{$clasificacion_verde->total_tallos()}}
                </th>
            </tr>
        </table>
    </div>
</div>

<script>
    function ocultar_mostrar_detalles_rendimiento() {
        if ($('#check_mostrar_ocultar_detalles_rendimiento').prop('checked')) {
            $('.elemento_especifico').show();
            $('#th_total_tallos').prop('colspan', 4);
        } else {
            $('.elemento_especifico').hide();
            $('#th_total_tallos').prop('colspan', 1);
        }
    }
</script>