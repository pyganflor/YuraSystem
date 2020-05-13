<div style="overflow-x: scroll; overflow-y: scroll; max-height: 450px">
    <table class="table-bordered table-striped" style="width: 100%; border: 1px solid #9d9d9d; font-size: 0.9em">
        <tr class="tr_fijo_top_0">
            <th class="text-center th_fijo_left_0" style="background-color: #357CA5; color: white; border-color: #9d9d9d; z-index: 9">
                <div style="padding: 5px">
                    Módulo
                </div>
            </th>
            <th class="text-center th_fijo_left_1" style="background-color: #357CA5; color: white; border-color: #9d9d9d; z-index: 9">
                <div style="padding: 5px">
                    Semana Inicio
                </div>
            </th>
            <th class="text-center th_fijo_left_2" style="background-color: #357CA5; color: white; border-color: #9d9d9d; z-index: 9">
                <div style="padding: 5px">
                    Semana Fen.
                </div>
            </th>
            @php
                $array_prom = [];
                $array_acum_semanal = [];
                $array_acum_diario = [];
            @endphp
            @for($i = 1; $i <= $max_semana; $i++)
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    <div style="width: 50px">
                        {{$i}}º
                    </div>
                </th>
                @php
                    array_push($array_prom, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                    array_push($array_acum_semanal, 0);
                    array_push($array_acum_diario, 0);
                @endphp
            @endfor
        </tr>
        @foreach($ciclos as $por_c => $c)
            @php
                $modulo = $c['ciclo']->modulo;
                $semana = $c['ciclo']->semana();
                $pos_sem = 0;
            @endphp
            <input type="hidden" class="ids_ciclo" value="{{$c['ciclo']->id_ciclo}}">
            <input type="hidden" class="ini_curva" value="{{$c['ini_curva']}}">
            <tr class="{{$modulo->id_sector == $sector || $sector == 'T' ? '' : 'hide'}}">
                <td class="text-center th_fijo_left_0" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{$modulo->nombre}}
                </td>
                <td class="text-center th_fijo_left_1" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{$semana->codigo}}
                </td>
                <td class="text-center th_fijo_left_2" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{intval(difFechas($c['ciclo']->fecha_inicio, date('Y-m-d'))->days / 7) + 1}}
                </td>
                @foreach($c['temperaturas'] as $pos_temp => $temp)
                    @php
                        $array_prom[$pos_sem]['valor'] += $temp->acumulado;
                        $array_prom[$pos_sem]['positivos'] ++;
                        $pos_sem++;
                    @endphp
                    <td class="text-center" style="border: {{$pos_sem == $c['ini_curva'] ? '2px solid blue' : '1px solid #9d9d9d'}}"
                        id="td_acumulado_{{$c['ciclo']->id_ciclo}}_{{$pos_sem}}">
                        {{number_format($temp->acumulado, 2)}}
                        <input type="hidden" id="acumulado_{{$c['ciclo']->id_ciclo}}_{{$pos_sem}}" value="{{$temp->acumulado}}">
                    </td>
                @endforeach
                @for($i = $pos_sem + 1; $i <= $max_semana; $i++)
                    <td class="text-center" style="border: {{$i == $c['ini_curva'] ? '2px solid blue' : '1px solid #9d9d9d'}}">
                    </td>
                @endfor
            </tr>
        @endforeach
        <tr class="tr_fijo_bottom_2">
            <th class="text-center th_fijo_left_0" style="background-color: #357CA5; border-color: #9d9d9d; color: white; z-index: 9"
                colspan="3">
                Promedios
            </th>
            @php
                $acum_anterior = 0;
            @endphp
            @foreach($array_prom as $pos_prom => $item)
                @php
                    $prom_semanal = $item['positivos'] > 0 ? round($item['valor'] / $item['positivos'], 2) : 0;
                    $array_acum_semanal[$pos_prom] = $prom_semanal - $acum_anterior;
                    $acum_anterior = $prom_semanal;
                @endphp
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" id="th_prom_{{$pos_prom + 1}}">
                    {{number_format($prom_semanal, 2)}}
                    <input type="hidden" id="prom_{{$pos_prom + 1}}" value="{{$prom_semanal}}">
                </th>
            @endforeach
        </tr>
        <tr class="tr_fijo_bottom_1">
            <th class="text-center th_fijo_left_0" style="background-color: #357CA5; border-color: #9d9d9d; color: white; z-index: 9"
                colspan="3">
                Acumulado <sup>semanal</sup>
            </th>
            @foreach($array_acum_semanal as $pos_sem => $item)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{number_format($item, 2)}}
                </th>
                @php
                    $array_acum_diario[$pos_sem] = round($item / 7, 2);
                @endphp
            @endforeach
        </tr>
        <tr class="tr_fijo_bottom_0">
            <th class="text-center th_fijo_left_0" style="background-color: #357CA5; border-color: #9d9d9d; color: white; z-index: 9"
                colspan="3">
                Acumulado <sup>diario</sup>
            </th>
            @foreach($array_acum_diario as $pos_sem => $item)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{number_format($item, 2)}}
                </th>
            @endforeach
        </tr>
    </table>
</div>
<input type="hidden" id="max_semana" value="{{$max_semana}}">

<div class="text-right" style="margin-top: 10px">
    <legend style="font-size: 1em; margin-bottom: 0">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseLeyenda" aria-expanded="true">
            <strong style="color: black">Leyenda <i class="fa fa-fw fa-caret-down"></i></strong>
        </a>
    </legend>
    <div class="panel-collapse collapse" id="collapseLeyenda" aria-expanded="true" style="">
        <ul style="margin-top: 5px" class="list-unstyled">{{--
            <li>Por encima de la media <i class=" fa fa-fw fa-circle" style="color: #30b32d"></i></li>
            <li>Por debajo de la media <i class="fa fa-fw fa-circle" style="color: #f03e3e"></i></li>
            <li>Semana de inicio de curva en módulos SIN primera flor <i class="fa fa-fw fa-circle-o" style="color: orange"></i></li>--}}
            <li>Semana de inicio de curva en módulos CON primera flor <i class="fa fa-fw fa-circle-o" style="color: blue"></i></li>
            <li>Semana PROMEDIO de inicio de curva <sup>real</sup> <i class="fa fa-fw fa-circle" style="color: #fbff00"></i></li>
            {{--<li>Semana PROMEDIO de inicio de curva <sup>proyectado</sup> <i class="fa fa-fw fa-circle" style="color: orange"></i></li>--}}
        </ul>
    </div>
</div>

<style>
    .tr_fijo_top_0 th {
        position: sticky;
        top: 0;
    }

    .tr_fijo_bottom_0 th {
        position: sticky;
        bottom: 0;
    }

    .tr_fijo_bottom_1 th {
        position: sticky;
        bottom: 18px;
    }

    .tr_fijo_bottom_2 th {
        position: sticky;
        bottom: 35px;
    }

    .th_fijo_left_0 {
        position: sticky;
        left: 0;
    }

    .th_fijo_left_1 {
        position: sticky;
        left: 53px;
    }

    .th_fijo_left_2 {
        position: sticky;
        left: 109px;
    }
</style>
<script>
    function calcular_iniCurva_promedio() {
        list = $('.ini_curva');
        valor = 0;
        positivos = 0;
        for (i = 0; i < list.length; i++) {
            if (list[i].value > 0) {
                valor += parseFloat(list[i].value);
                positivos++;
            }
        }
        promedio = positivos > 0 ? Math.round(valor / positivos) : 0;
        $('#th_prom_' + promedio).css('background-color', '#fbff00')
    }

    function colorear_celdas() {
        num_semanas = $('#max_semana').val();
        for (i = 1; i <= num_semanas; i++) {
            prom = $('#prom_' + i).val();
            ids_ciclo = $('.ids_ciclo');
            for (c = 0; c < ids_ciclo.length; c++) {
                id_ciclo = ids_ciclo[c].value;
                acumulado = $('#acumulado_' + id_ciclo + '_' + i).val();
                if (acumulado > 0) {
                    if (acumulado >= prom) {
                        $('#td_acumulado_' + id_ciclo + '_' + i).css('background-color', '#30b32d');
                    } else {
                        $('#td_acumulado_' + id_ciclo + '_' + i).css('background-color', '#f03e3e');
                    }
                    $('#td_acumulado_' + id_ciclo + '_' + i).css('color', 'white');
                }
            }
        }
    }

    $(window).ready(function () {
        calcular_iniCurva_promedio();
        colorear_celdas();
    })
</script>
