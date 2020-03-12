<div style="overflow-x: scroll; overflow-y: scroll; max-height: 450px">
    <table class="table-striped table-bordered" style="width: 100%; border: 2px solid #9d9d9d; font-size: 0.9em">
        <tr class="tr_fija_top_0">
            <th class="text-center th_fijo_left_0" style="border-color: #9d9d9d; background-color: #357CA5; color: white; z-index: 9">
                <div style="width: 70px">
                    Módulo
                </div>
            </th>
            <th class="text-center th_fijo_left_1" style="border-color: #9d9d9d; background-color: #357CA5; color: white; z-index: 9">
                <div style="width: 70px">
                    Semana Inicio
                </div>
            </th>
            <th class="text-center th_fijo_left_2" style="border-color: #9d9d9d; background-color: #357CA5; color: white; z-index: 9">
                <div style="width: 70px">
                    Días Fen.
                </div>
            </th>
            @php
                $array_prom = [];
                $prom_ini_curva = [
                    'valor' => 0,
                    'cantidad' => 0,
                ];
            @endphp
            @for($i = 1; $i <= $num_semanas; $i++)
                <th class="text-center {{$i < $min_semanas ? 'hide' : ''}}" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <div style="width: 50px">
                        {{$i}}º
                    </div>
                </th>
                @php
                    array_push($array_prom, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                @endphp
            @endfor
            <th class="text-center th_fijo_right_0" style="border-color: #9d9d9d; background-color: #357CA5; color: white; z-index: 9">
                <div style="width: 70px">
                    Ingresar
                </div>
            </th>
        </tr>
        @foreach($ciclos as $pos => $item)
            @php
                $modulo = $item['ciclo']->modulo;
                $semana = $item['ciclo']->semana();
                $cant_mon = 1;
                if($item['ini_curva'] > 0){
                    $prom_ini_curva['valor'] += $item['ini_curva'];
                    $prom_ini_curva['cantidad']++;
                }
            @endphp
            <tr class="{{count($item['monitoreos']) >= $min_semanas ? '' : 'hide'}}">
                <th class="text-center th_fijo_left_0" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        {{$modulo->nombre}}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" style="margin-left: 210px; margin-top: 0">
                        <li>
                            <a href="javascript:void(0)" onclick="$('.input_ciclo_{{$item['ciclo']->id_ciclo}}').attr('readonly', false)">
                                Habilitar
                            </a>
                        </li>
                    </ul>
                </th>
                <th class="text-center th_fijo_left_1" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$semana->codigo}}
                </th>
                <th class="text-center th_fijo_left_2" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{difFechas($item['ciclo']->fecha_inicio, date('Y-m-d'))->days}}
                </th>
                @php
                    $mon_actual = $item['mon_actual'] != '' ? $item['mon_actual'] : '';
                    $ant = 0;
                @endphp
                @foreach($item['monitoreos'] as $pos_mon => $mon)
                    @php    // algoritmo para calcular el crecimiento semanal
                        $val = $mon->altura;
                        $crec_sem = round($val - $ant, 2);
                        $crec_dia = round($crec_sem / 7, 2);
                        $ant = $val;

                        $title = '';
                        if ($crec_sem > 0){
                            $title = '<em>Crec. Sem.: '.$crec_sem.'</em><br>';
                            $title .= '<em>Crec. Día: '.$crec_dia.'</em>';
                            if ($mon_actual != '')
                                if ($mon_actual->num_sem == $cant_mon)
                                    $title .= '*';
                        }
                    @endphp
                    <th class="text-center celda_hovered {{$cant_mon < $min_semanas ? 'hide' : ''}}"
                        style="border-color: #9d9d9d; background-color: #e9ecef" id="td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}"
                        onmouseover="mouse_over_celda('td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}', 1)"
                        onmouseleave="mouse_over_celda('{{$item['ciclo']->id_ciclo}}', 0)">
                        <input type="number" style="width: 100%; border: {{$item['ini_curva'] == $cant_mon ? '3px solid blue' : ''}}"
                               id="monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}" data-toggle="tooltip" data-placement="top"
                               data-html="true" title="{{$title}}" value="{{$mon->altura}}" readonly ondblclick="$(this).attr('readonly', false)"
                               min="0" class="text-center input_sem_{{$cant_mon}} input_ciclo_{{$item['ciclo']->id_ciclo}}"
                               onchange="guardar_monitoreo('{{$item['ciclo']->id_ciclo}}', '{{$cant_mon}}')">
                    </th>
                    @php
                        if ($mon->altura > 0){
                            $array_prom[$cant_mon - 1]['valor'] += $mon->altura;
                            $array_prom[$cant_mon - 1]['positivos'] ++;
                        }
                        $cant_mon++;
                    @endphp
                @endforeach
                @for($i = $cant_mon; $i <= $num_semanas; $i++)
                    <th class="text-center celda_hovered {{$i < $min_semanas ? 'hide' : ''}}" style="border-color: #9d9d9d"
                        id="td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}"
                        onmouseover="mouse_over_celda('td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}', 1)"
                        onmouseleave="mouse_over_celda('{{$item['ciclo']->id_ciclo}}', 0)">
                        <input type="number" style="width: 100%; border: {{$item['ini_curva'] == $cant_mon ? '3px solid blue' : ''}}"
                               id="monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}" readonly
                               ondblclick="$(this).attr('readonly', false)" class="text-center" min="0"
                               onchange="guardar_monitoreo('{{$item['ciclo']->id_ciclo}}', '{{$cant_mon}}')">
                    </th>
                    @php
                        $cant_mon++;
                    @endphp
                @endfor
                <th class="text-center th_fijo_right_0" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <input type="number" style="width: 100%; background-color: #e9ecef" id="ingresar_{{$item['ciclo']->id_ciclo}}"
                           class="text-center">
                    <input type="hidden" class="ids_ciclo" value="{{$item['ciclo']->id_ciclo}}">
                </th>
            </tr>
        @endforeach
        @php
            $array_crec_sem = [];
            $array_crec_dia = [];
        @endphp
        <tr class="tr_fijo_bottom_2">
            <th class="text-center th_fijo_left_0" style="border-color: #9d9d9d; background-color: #357CA5; color: white; z-index: 9"
                colspan="3">
                Promedios <sup title="Altura">cm</sup>
            </th>
            @php
                $ant = 0;
            @endphp
            @foreach($array_prom as $pos_sem => $item)
                @php
                    $sem_prom_ini_curva = '';
                    if($prom_ini_curva['cantidad'] > 0)
                        $sem_prom_ini_curva = round($prom_ini_curva['valor'] / $prom_ini_curva['cantidad']);
                @endphp
                <th class="text-center {{$pos_sem + 1 < $min_semanas ? 'hide' : ''}}"
                    style="border-color: #9d9d9d; background-color: {{$sem_prom_ini_curva == $pos_sem + 1 ? '#fbff00' : '#e9ecef'}}">
                    {{$item['positivos'] > 0 ? round($item['valor'] / $item['positivos'], 2) : 0}}
                    <input type="hidden" id="prom_sem_{{$pos_sem + 1}}"
                           value="{{$item['positivos'] > 0 ? round($item['valor'] / $item['positivos'], 2) : 0}}">
                </th>
                @php    // algoritmo para calcular el crecimiento semanal
                    $val = $item['positivos'] > 0 ? round($item['valor'] / $item['positivos'], 2) : 0;
                    array_push($array_crec_sem, round($val - $ant, 2));
                    $ant = $val;
                @endphp
            @endforeach
            <th class="text-center th_fijo_right_0" style="border-color: #9d9d9d; background-color: #e9ecef; color: white; z-index: 9">
            </th>
        </tr>
        <tr class="tr_fijo_bottom_1">
            <th class="text-center th_fijo_left_0" style="border-color: #9d9d9d; background-color: #357CA5; color: white; z-index: 9"
                colspan="3">
                Crecimiento <sup>semanal</sup>
            </th>
            @foreach($array_crec_sem as $pos_sem => $item)
                <th class="text-center {{$pos_sem + 1 < $min_semanas ? 'hide' : ''}}" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$item > 0 ? $item : 0}}
                    <input type="hidden" id="crec_sem_{{$pos_sem + 1}}" value="{{$item > 0 ? $item : 0}}">
                </th>
                @php
                    array_push($array_crec_dia, round($item > 0 ? $item / 7 : 0, 2))
                @endphp
            @endforeach
            <th class="text-center th_fijo_right_0" style="border-color: #9d9d9d; background-color: #e9ecef; color: white; z-index: 9">
            </th>
        </tr>
        <tr class="tr_fijo_bottom_0">
            <th class="text-center th_fijo_left_0" style="border-color: #9d9d9d; background-color: #357CA5; color: white; z-index: 9"
                colspan="3">
                Crecimiento <sup>diario</sup>
            </th>
            @foreach($array_crec_dia as $pos_sem => $item)
                <th class="text-center {{$pos_sem + 1 < $min_semanas ? 'hide' : ''}}" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$item}}
                    <input type="hidden" id="crec_sem_dia_{{$pos_sem + 1}}"
                           value="{{$item}}">
                </th>
            @endforeach
            <th class="text-center th_fijo_right_0 th_fijo_bottom_0"
                style="border-color: #9d9d9d; background-color: #e9ecef; color: white; z-index: 9" rowspan="3">
                <button type="button" class="btn btn-xs btn-block btn-success" onclick="store_nuevos_ingresos()">
                    <i class="fa fa-fw fa-check"></i>
                </button>
            </th>
        </tr>
    </table>
</div>
<div class="text-right" style="margin-top: 10px">
    <legend style="font-size: 1em; margin-bottom: 0">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseLeyenda">
            <strong style="color: black">Leyenda <i class="fa fa-fw fa-caret-down"></i></strong>
        </a>
    </legend>
    <div class="panel-collapse collapse" id="collapseLeyenda">
        <ul style="margin-top: 5px" class="list-unstyled">
            <li>Por encima que la media <i class=" fa fa-fw fa-circle" style="color: #30b32d"></i></li>
            <li>Por debajo de la media <i class="fa fa-fw fa-circle" style="color: #f03e3e"></i></li>
            <li>Semana de inicio de curva en módulos con primera flor <i class="fa fa-fw fa-circle-o" style="color: blue"></i></li>
            <li>Semana PROMEDIO de inicio de curva <i class="fa fa-fw fa-circle" style="color: #fbff00"></i></li>
        </ul>
    </div>
</div>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    function guardar_monitoreo(ciclo, cant_mon) {
        datos = {
            _token: '{{csrf_token()}}',
            ciclo: ciclo,
            cant_mon: cant_mon,
            valor: $('#monitoreo_' + ciclo + '_' + cant_mon).val(),
        };
        if (datos['valor'] != '') {
            $('#td_monitoreo_' + ciclo + '_' + cant_mon).LoadingOverlay('show');
            $.post('{{url('monitoreo_ciclos/guardar_monitoreo')}}', datos, function (retorno) {
                if (!retorno.success) {
                    alert(retorno.mensaje);
                } else {
                    colorear_celdas();
                }
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta_errores(retorno.responseText);
            }).always(function () {
                $('#td_monitoreo_' + ciclo + '_' + cant_mon).LoadingOverlay('hide');
                $('#monitoreo_' + ciclo + '_' + cant_mon).attr('readonly', true);
            });
        }
    }

    function colorear_celdas() {
        num_semanas = $('#filtro_num_semanas').val();
        for (i = 1; i <= num_semanas; i++) {
            inputs = $('.input_sem_' + i);
            for (y = 0; y < inputs.length; y++) {
                if (inputs[y].value > 0) {
                    if (parseFloat(inputs[y].value) >= parseFloat($('#prom_sem_' + i).val())) {
                        $('#' + inputs[y].id).css('background-color', '#30b32d');   // verde
                    } else {
                        $('#' + inputs[y].id).css('background-color', '#f03e3e');   // rojo
                    }
                    $('#' + inputs[y].id).css('color', 'white');
                }
            }
        }
    }

    function store_nuevos_ingresos() {
        ids_ciclo = $('.ids_ciclo');
        data = [];
        for (i = 0; i < ids_ciclo.length; i++) {
            id_ciclo = ids_ciclo[i].value;
            valor = $('#ingresar_' + id_ciclo).val();
            if (valor > 0) {
                data.push({
                    ciclo: id_ciclo,
                    valor: valor
                });
            }
        }
        datos = {
            _token: '{{csrf_token()}}',
            data: data
        };
        post_jquery('{{url('monitoreo_ciclos/store_nuevos_ingresos')}}', datos, function () {
            listar_ciclos();
        }, 'div_listado_ciclos');
    }

    $(window).ready(function () {
        colorear_celdas();
    })
</script>

<style>
    .th_fijo_right_0 {
        position: sticky;
        right: 0;
    }

    .th_fijo_left_0 {
        position: sticky;
        left: 0;
    }

    .th_fijo_left_1 {
        position: sticky;
        left: 71px;
    }

    .th_fijo_left_2 {
        position: sticky;
        left: 142px;
    }

    .tr_fijo_bottom_0 th {
        position: sticky;
        bottom: 0;
        z-index: 5;
    }

    .tr_fijo_bottom_1 th {
        position: sticky;
        bottom: 22px;
        z-index: 5;
    }

    .tr_fijo_bottom_2 th {
        position: sticky;
        bottom: 39px;
        z-index: 5;
    }

    .tr_fija_top_0 th {
        position: sticky;
        top: 0;
    }
</style>