<div style="overflow-x: scroll">
    <table class="table-bordered table-striped table-hover" width="100%" style="border: 2px solid #9d9d9d; font-size: 1em;">
        <thead>
        <tr style="background-color: #e9ecef">
            <th class="text-center" style="border-color: #9d9d9d; width: 250px">
                <b style="padding: 20px">Área</b>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; width: 250px">
                <b style="padding: 10px">Días</b>
            </th>
            @foreach($list_tallos as $item)
                <th class="text-center" style="border-color: #9d9d9d; width: 250px">
                    <b style="padding: 10px">{{$item->semana}}</b>
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        {{-- CONTENIDO --}}
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" rowspan="2">
                <b style="padding: 10px" data-toggle="tooltip" data-placement="top" data-html="true"
                   title="Rendimiento: {{$rend_verde}} <sup>t / hr / per</sup> <br> Personal: {{$pers_verde}}">Verde</b>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                7
            </th>
            @foreach($list_tallos as $item)
                @php
                    $mano_obra = $rend_verde > 0 ? ($item->cant / $rend_verde) / (7 * $hr_diarias_verde) : 0;

                    if($mano_obra <= $pers_verde){  // hay suficientes trabajadores
                        if($mano_obra < ($pers_verde - 5)){   // sobran más de 5 trabajadores
                            $fondo = '#00ffc4';
                            $texto = 'black';
                        } else {
                            $fondo = '#00ff00';
                            $texto = 'black';
                        }
                    } else {   // faltan trabajadores
                        if($mano_obra > ($pers_verde + 5)){   // faltan más de 5 trabajadores
                            $fondo = 'red';
                            $texto = 'white';
                        } else {    // faltan hasta 5 trabajadores
                            $fondo = 'orange';
                            $texto = 'white';
                        }
                    }
                @endphp
                <td class="text-center celda_hovered" id="celda_verde_7_{{$item->semana}}"
                    style="border-color: #9d9d9d; background-color: {{$fondo}}; color: {{$texto}}"
                    onmouseover="mouse_over_celda('celda_verde_7_{{$item->semana}}', 1)" onmouseleave="mouse_over_celda('', 0)">
                    {{number_format($mano_obra, 2)}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                6
            </th>
            @foreach($list_tallos as $item)
                @php
                    $mano_obra = $rend_verde > 0 ? ($item->cant / $rend_verde) / (6 * $hr_diarias_verde) : 0;

                    if($mano_obra <= $pers_verde){  // hay suficientes trabajadores
                        if($mano_obra < ($pers_verde - 5)){   // sobran más de 5 trabajadores
                            $fondo = '#00ffc4';
                            $texto = 'black';
                        } else {
                            $fondo = '#00ff00';
                            $texto = 'black';
                        }
                    } else {   // faltan trabajadores
                        if($mano_obra > ($pers_verde + 5)){   // faltan más de 5 trabajadores
                            $fondo = 'red';
                            $texto = 'white';
                        } else {    // faltan hasta 5 trabajadores
                            $fondo = 'orange';
                            $texto = 'white';
                        }
                    }
                @endphp
                <td class="text-center celda_hovered" id="celda_verde_6_{{$item->semana}}"
                    style="border-color: #9d9d9d; background-color: {{$fondo}}; color: {{$texto}}"
                    onmouseover="mouse_over_celda('celda_verde_6_{{$item->semana}}', 1)" onmouseleave="mouse_over_celda('', 0)">
                    {{number_format($mano_obra, 2)}}
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="number" id="horas_diarias_verde" min="1" max="24" style="width: 100%; height: 22px" class="text-center mouse-hand"
                       title="Horas diarias" ondblclick="$(this).prop('readonly', false)" onchange="update_horas_diarias_verde()"
                       value="{{$hr_diarias_verde}}" readonly>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                5
            </th>
            @foreach($list_tallos as $item)
                @php
                    $mano_obra = $rend_verde > 0 ? ($item->cant / $rend_verde) / (5 * $hr_diarias_verde) : 0;

                    if($mano_obra <= $pers_verde){  // hay suficientes trabajadores
                        if($mano_obra < ($pers_verde - 5)){   // sobran más de 5 trabajadores
                            $fondo = '#00ffc4';
                            $texto = 'black';
                        } else {
                            $fondo = '#00ff00';
                            $texto = 'black';
                        }
                    } else {   // faltan trabajadores
                        if($mano_obra > ($pers_verde + 5)){   // faltan más de 5 trabajadores
                            $fondo = 'red';
                            $texto = 'white';
                        } else {    // faltan hasta 5 trabajadores
                            $fondo = 'orange';
                            $texto = 'white';
                        }
                    }
                @endphp
                <td class="text-center celda_hovered" id="celda_verde_5_{{$item->semana}}"
                    style="border-color: #9d9d9d; background-color: {{$fondo}}; color: {{$texto}}"
                    onmouseover="mouse_over_celda('celda_verde_5_{{$item->semana}}', 1)" onmouseleave="mouse_over_celda('', 0)">
                    {{number_format($mano_obra, 2)}}
                </td>
            @endforeach
        </tr>
        {{-- TOTALES --}}
        <tr style="background-color: #f3ff91">
            <th class="text-center" style="border-color: #9d9d9d" colspan="2">
                <b style="padding: 5px">Tallos</b>
            </th>
            @foreach($list_tallos as $item)
                <th class="text-center" style="border-color: #9d9d9d">
                    <b style="padding: 5px">{{number_format($item->cant, 2)}}</b>
                </th>
            @endforeach
        </tr>
        <tr style="background-color: white">
            <th class="text-center" style="border-color: #9d9d9d" colspan="2">
                <b style="padding: 5px">Horas Totales</b>
            </th>
            @foreach($list_tallos as $item)
                <th class="text-center" style="border-color: #9d9d9d">
                    <b style="padding: 5px">{{$rend_verde > 0 ? number_format($item->cant / $rend_verde, 2) : 0}}</b>
                </th>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>

<div class="text-right" style="margin-top: 10px">
    <legend style="font-size: 1em; margin-bottom: 0">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseLeyenda">
            <strong style="color: black">Leyenda <i class="fa fa-fw fa-caret-down"></i></strong>
        </a>
    </legend>
    <ul style="margin-top: 5px" class="list-unstyled panel-collapse collapse" id="collapseLeyenda">
        <li><strong>Sobran más de 5</strong> trabajadores <i class="fa fa-fw fa-circle" style="color: #00ffc4"></i></li>
        <li><strong>Suficientes</strong> trabajadores <i class="fa fa-fw fa-circle" style="color: #00ff00"></i></li>
        <li>Se necesitan <strong>hasta 5</strong> trabajadores más <i class="fa fa-fw fa-circle" style="color: orange"></i></li>
        <li>Se necesitan <strong>más de 5</strong> trabajadores <i class="fa fa-fw fa-circle" style="color: red"></i></li>
    </ul>
</div>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    function update_horas_diarias_verde() {
        datos = {
            _token: '{{csrf_token()}}',
            valor: $('#horas_diarias_verde').val() >= 0 ? $('#horas_diarias_verde').val() : 8
        };
        $.post('{{url('proy_mano_obra/update_horas_diarias_verde')}}', datos, function () {
            $('#horas_diarias_verde').prop('readonly', true);
        }, 'json');
    }
</script>