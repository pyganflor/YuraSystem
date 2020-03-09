@if($tallos > 0)
    @php
        $rend_verde = $verde != '' ? $verde->getRendimiento() : 0;
    @endphp
    <table style="width: 100%; font-size: 1.5em; color: black" class="table-bordered" id="table_rendimiento_mesa">
        @for($i = 1; $i<=18; $i++)
            <tr style="height: 35px">
                @php
                    $rend = 0;
                    $tallos = 0;
                    foreach($query as $item){
                        if ($item->mesa == $i)
                            $tallos += $item->cantidad_ramos * $item->tallos_x_ramos;
                    }

                    if ($getCantidadHorasTrabajoVerde > 0)
                        $rend =  $tallos / $getCantidadHorasTrabajoVerde;

                    $color = $rend >= $rend_verde ? 'green' : 'orange';
                @endphp
                <th class="text-right bg-{{$color}}" style="border-color: #001F3F; width: 16%; vertical-align: middle">
                    <span style="margin-right: 5px; color: black">{{$i}}</span>
                </th>
                <th class="text-left bg-{{$color}}"
                    style="border-color: #001F3F; width: 16%; border-right-width: 10px;">
                    <span class="badge bg-navy-active" style="margin-left: 5px; font-size: 0.8em">{{round($rend, 2)}}</span>
                </th>

                @php
                    $rend = 0;
                    $tallos = 0;
                    foreach($query as $item){
                        if ($item->mesa == $i+18)
                            $tallos += $item->cantidad_ramos * $item->tallos_x_ramos;
                    }

                    if ($getCantidadHorasTrabajoVerde > 0)
                        $rend =  $tallos / $getCantidadHorasTrabajoVerde;

                    $color = $rend >= $rend_verde ? 'green' : 'orange';
                @endphp
                <th class="text-right bg-{{$color}}" style="border-color: #001F3F; width: 16%">
                    <span style="margin-right: 5px; color: black">{{$i+18}}</span>
                </th>
                <th class="text-left bg-{{$color}}" style="border-color: #001F3F; width: 16%; border-right-width: 10px;">
                    <span class="badge bg-navy-active" style="margin-left: 5px; font-size: 0.8em">{{round($rend, 2)}}</span>
                </th>

                @php
                    $rend = 0;
                    $tallos = 0;
                    foreach($query as $item){
                        if ($item->mesa == $i+36)
                            $tallos += $item->cantidad_ramos * $item->tallos_x_ramos;
                    }

                    if ($getCantidadHorasTrabajoVerde > 0)
                        $rend =  $tallos / $getCantidadHorasTrabajoVerde;

                    $color = $rend >= $rend_verde ? 'green' : 'orange';
                @endphp
                <th class="text-right bg-{{$color}}" style="border-color: #001F3F; width: 16%">
                    <span style="margin-right: 5px; color: black">{{$i+36}}</span>
                </th>
                <th class="text-left bg-{{$color}}" style="border-color: #001F3F; width: 16%">
                    <span class="badge bg-navy-active" style="margin-left: 5px; font-size: 0.8em">{{round($rend, 2)}}</span>
                </th>
            </tr>
        @endfor
    </table>
@else
    <div class="alert alert-info text-center">
        No se han encontrado clasificaciones para la fecha indicada
    </div>
@endif

<script>
    setTimeout(function () {
        /*$('.modal-backdrop').remove();
        $('.modal').remove();
        rendimiento_mesas();*/

        datos = {
            fecha_verde: $('#fecha_verde_search').val().trim(),
        };
        get_jquery('{{url('clasificacion_verde/rendimiento_mesas')}}', datos, function (retorno) {
            $('#div_modal-modal-view_rendimiento_mesas').html(retorno);
        }, 'div_modal-modal-view_rendimiento_mesas');
    }, 60000)
</script>