@if($tallos > 0)
    @php
        $rend_verde = $verde != '' ? $verde->getRendimiento() : 0;
    @endphp
    <table style="width: 100%; font-size: 1.3em; color: black" class="table-bordered" id="table_rendimiento_mesa">
        @for($i = 1; $i<=18; $i++)
            <tr style="height: 35px">
                @php
                    $rend = getRendimientoVerdeByFechaMesa($fecha_verde, $i);
                    $color = $rend >= $rend_verde ? 'green' : 'orange';
                @endphp
                <th class="text-right bg-{{$color}}" style="border-color: #001F3F; width: 16%; vertical-align: middle">
                    <span style="margin-right: 5px; color: black">{{$i}}</span>
                </th>
                <th class="text-left bg-{{$color}}"
                    style="border-color: #001F3F; width: 16%; border-right-width: 10px;">
                    <span class="badge bg-navy-active" style="margin-left: 5px">{{round($rend, 2)}}</span>
                </th>

                @php
                    $rend = getRendimientoVerdeByFechaMesa($fecha_verde, $i+18);
                    $color = $rend >= $rend_verde ? 'green' : 'orange';
                @endphp
                <th class="text-right bg-{{$color}}" style="border-color: #001F3F; width: 16%">
                    <span style="margin-right: 5px; color: black">{{$i+18}}</span>
                </th>
                <th class="text-left bg-{{$color}}" style="border-color: #001F3F; width: 16%; border-right-width: 10px;">
                    <span class="badge bg-navy-active" style="margin-left: 5px">{{round($rend, 2)}}</span>
                </th>

                @php
                    $rend = getRendimientoVerdeByFechaMesa($fecha_verde, $i+36);
                    $color = $rend >= $rend_verde ? 'green' : 'orange';
                @endphp
                <th class="text-right bg-{{$color}}" style="border-color: #001F3F; width: 16%">
                    <span style="margin-right: 5px; color: black">{{$i+36}}</span>
                </th>
                <th class="text-left bg-{{$color}}" style="border-color: #001F3F; width: 16%">
                    <span class="badge bg-navy-active" style="margin-left: 5px">{{round($rend, 2)}}</span>
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
    setInterval(function () {
        datos = {
            fecha_verde: $('#fecha_verde_search').val().trim(),
        };
        $.get('{{url('clasificacion_verde/rendimiento_mesas')}}', datos, function (retorno) {
            $('#div_modal-modal-view_rendimiento_mesas').html(retorno);
        });
    }, 10000)
</script>