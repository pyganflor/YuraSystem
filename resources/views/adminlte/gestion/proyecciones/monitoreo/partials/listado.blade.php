<div style="overflow-x: scroll; overflow-y: scroll; max-height: 450px">
    <table class="table-striped table-bordered" style="width: 100%; border: 1px solid #9d9d9d; font-size: 0.9em">
        <tr class="tr_fija_top_0">
            <th class="text-center th_fijo_left_0 background-color_yura" style="border-color: #9d9d9d; color: white; z-index: 9">
                <div style="width: 70px">
                    Módulo
                </div>
            </th>
            <th class="text-center th_fijo_left_1 background-color_yura" style="border-color: #9d9d9d; color: white; z-index: 9">
                <div style="width: 70px">
                    Semana Inicio
                </div>
            </th>
            <th class="text-center th_fijo_left_2 background-color_yura" style="border-color: #9d9d9d; color: white; z-index: 9">
                <div style="width: 70px">
                    Semana Fen.
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
                <th class="text-center {{$i < $min_semanas ? 'hide' : ''}} th_yura_default" id="th_num_sem_{{$i}}">
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
            <th class="text-center th_fijo_right_0 background-color_yura" style="border-color: #9d9d9d; color: white; z-index: 9">
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
                $mon_actual = $item['mon_actual'] != '' ? $item['mon_actual'] : '';
            @endphp
            <input type="hidden" id="last_sem_{{$item['ciclo']->id_ciclo}}" value="{{$mon_actual != '' ? $mon_actual->num_sem : ''}}">
            <input type="hidden" id="ini_curva_{{$item['ciclo']->id_ciclo}}" value="{{$item['ini_curva']}}">
            <tr class="{{count($item['monitoreos']) >= $min_semanas && ($modulo->id_sector == $sector || $sector == 'T') ? '' : 'hide'}}">
                <th class="text-center th_fijo_left_0 th_yura_default" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <button type="button" class="btn btn-yura_default btn-xs dropdown-toggle" data-toggle="dropdown">
                        {{$modulo->nombre}}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left border-radius_18" style="margin-left: 210px; margin-top: 0; top: -1px;">
                        <li>
                            <a href="javascript:void(0)" class="btn-yura_default"
                               onclick="$('.input_ciclo_{{$item['ciclo']->id_ciclo}}').attr('readonly', false)">
                                Habilitar
                            </a>
                        </li>
                    </ul>
                </th>
                <th class="text-center th_fijo_left_1 th_yura_default" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$semana->codigo}}
                </th>
                <th class="text-center th_fijo_left_2 th_yura_default" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{intval(difFechas($item['ciclo']->fecha_inicio, date('Y-m-d'))->days / 7)}}
                </th>
                @php
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
                        }
                    @endphp
                    <th class="text-center celda_hovered {{$cant_mon < $min_semanas ? 'hide' : ''}}"
                        style="border-color: #9d9d9d; background-color: #e9ecef" id="td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}"
                        onmouseover="mouse_over_celda('td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}', 1)"
                        onmouseleave="mouse_over_celda('{{$item['ciclo']->id_ciclo}}', 0)">
                        <input type="number" style="width: 100%; border: {{$item['ini_curva'] == $cant_mon ? '3px solid blue' : ''}}"
                               id="monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}" data-toggle="tooltip" data-placement="top"
                               data-html="true" title="{{$title}}" value="{{$mon->altura}}" readonly ondblclick="$(this).attr('readonly', false)"
                               min="0" class="text-center input_sem_{{$cant_mon}} input_ciclo_{{$item['ciclo']->id_ciclo}} border-radius_18"
                               onchange="guardar_monitoreo('{{$item['ciclo']->id_ciclo}}', '{{$cant_mon}}')">
                        <input type="hidden" id="crec_sem_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}" value="{{$crec_sem}}">
                        <input type="hidden" id="crec_dia_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}" value="{{$crec_dia}}">
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
                               ondblclick="$(this).attr('readonly', false)" class="text-center border-radius_18" min="0"
                               onchange="guardar_monitoreo('{{$item['ciclo']->id_ciclo}}', '{{$cant_mon}}')">
                    </th>
                    @php
                        $cant_mon++;
                    @endphp
                @endfor
                <th class="text-center th_fijo_right_0" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <input type="number" style="width: 100%; background-color: #e9ecef" id="ingresar_{{$item['ciclo']->id_ciclo}}"
                           class="text-center border-radius_18">
                    <input type="hidden" class="ids_ciclo" value="{{$item['ciclo']->id_ciclo}}">
                </th>
            </tr>
        @endforeach
        @php
            $array_crec_sem = [];
            $array_crec_dia = [];
        @endphp
        <tr class="tr_fijo_bottom_2">
            <th class="text-center th_fijo_left_0 background-color_yura" style="border-color: #9d9d9d; color: white; z-index: 9"
                colspan="3">
                Promedios <sup title="Altura">cm</sup>
            </th>
            @php
                $sem_prom_ini_curva = '';
                if($prom_ini_curva['cantidad'] > 0)
                    $sem_prom_ini_curva = round($prom_ini_curva['valor'] / $prom_ini_curva['cantidad']);
                $ant = 0;
            @endphp
            @foreach($array_prom as $pos_sem => $item)
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
            <th class="text-center th_fijo_left_0 background-color_yura" style="border-color: #9d9d9d; color: white; z-index: 9"
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
            <th class="text-center th_fijo_left_0 background-color_yura" style="border-color: #9d9d9d; color: white; z-index: 9"
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
                <button type="button" class="btn btn-xs btn-block btn-yura_primary" onclick="store_nuevos_ingresos()">
                    <i class="fa fa-fw fa-check"></i>
                </button>
            </th>
        </tr>
        <input type="hidden" id="sem_prom_ini_curva" value="{{$sem_prom_ini_curva}}">
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
            <li>Por encima de la media <i class=" fa fa-fw fa-circle" style="color: #00B388"></i></li>
            <li>Por debajo de la media <i class="fa fa-fw fa-circle" style="color: #D01C62"></i></li>
            <li>Semana de inicio de curva en módulos SIN primera flor <i class="fa fa-fw fa-circle-o" style="color: orange"></i></li>
            <li>Semana de inicio de curva en módulos CON primera flor <i class="fa fa-fw fa-circle-o" style="color: blue"></i></li>
            <li>Semana PROMEDIO de inicio de curva <sup>real</sup> <i class="fa fa-fw fa-circle" style="color: #fbff00"></i></li>
            <li>Semana PROMEDIO de inicio de curva <sup>proyectado</sup> <i class="fa fa-fw fa-circle" style="color: orange"></i></li>
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
                        $('#' + inputs[y].id).css('background-color', '#00B388');   // verde
                    } else {
                        $('#' + inputs[y].id).css('background-color', '#D01C62');   // rojo
                    }
                    $('#' + inputs[y].id).css('color', 'white');
                }
            }
        }
    }

    function store_nuevos_ingresos() {
        ids_ciclo = $('.ids_ciclo');
        data = [];
        flag = false;
        for (i = 0; i < ids_ciclo.length; i++) {
            id_ciclo = ids_ciclo[i].value;
            valor = $('#ingresar_' + id_ciclo).val();
            if (valor > 0) {
                data.push({
                    ciclo: id_ciclo,
                    valor: valor
                });
                flag = true;
            }
        }
        if (flag) {
            datos = {
                _token: '{{csrf_token()}}',
                data: data
            };
            post_jquery('{{url('monitoreo_ciclos/store_nuevos_ingresos')}}', datos, function () {
                listar_ciclos();
            }, 'div_listado_ciclos');
        }
    }

    function proyectar_inicio_curvas() {    // algoritmo para proyectar el inicio de curva
        ciclos = $('.ids_ciclo');
        proy_sem_prom_ini_curva = {
            valor: 0,
            cantidad: 0,
        };
        for (i = 0; i < ciclos.length; i++) {
            id_ciclo = ciclos[i].value;
            last_sem = parseInt($('#last_sem_' + id_ciclo).val());
            if (last_sem >= $('#filtro_min_semanas').val() && last_sem <= 11) {  // se trate de un ciclo en el rango de semanas que interesan
                valor = $('#monitoreo_' + id_ciclo + '_' + last_sem).val();
                crec_sem = $('#crec_sem_' + id_ciclo + '_' + last_sem).val();
                crec_dia = $('#crec_dia_' + id_ciclo + '_' + last_sem).val();

                prom_sem = $('#prom_sem_' + last_sem).val();
                crec_sem_prom = $('#crec_sem_' + last_sem).val();
                crec_dia_prom = $('#crec_sem_dia_' + last_sem).val();

                dif_dia = crec_dia - crec_dia_prom;
                dif_sem = crec_sem - crec_sem_prom;
                dif_dia = Math.sign(dif_dia) == -1 ? dif_dia * -1 : dif_dia;
                dif_sem = Math.sign(dif_sem) == -1 ? dif_sem * -1 : dif_sem;

                resultado = dif_sem > 0 ? (dif_dia * 7) / dif_sem : 0;
                //resultado = Math.round(resultado);
                resultado = parseInt(resultado);
                direccion = Math.sign(valor - prom_sem);
                sem_prom_ini_curva = parseInt($('#sem_prom_ini_curva').val());

                if (direccion >= 0) {   // adelantar en el tiempo
                    nueva_curva = sem_prom_ini_curva - resultado;
                } else {    // atrasar en el tiempo
                    nueva_curva = sem_prom_ini_curva + resultado;
                }
                if ($('#ini_curva_' + id_ciclo).val() == nueva_curva) {
                    $('#monitoreo_' + id_ciclo + '_' + nueva_curva).css('border-top', '3px solid orange');
                    $('#monitoreo_' + id_ciclo + '_' + nueva_curva).css('border-left', '3px solid orange');
                    $('#monitoreo_' + id_ciclo + '_' + nueva_curva).css('border-bottom', '3px solid blue');
                    $('#monitoreo_' + id_ciclo + '_' + nueva_curva).css('border-right', '3px solid blue');
                } else {
                    $('#monitoreo_' + id_ciclo + '_' + nueva_curva).css('border', '3px solid orange');
                }
                proy_sem_prom_ini_curva['valor'] += nueva_curva;
                proy_sem_prom_ini_curva['cantidad']++;
            }
        }
        num_sem_proy = proy_sem_prom_ini_curva['cantidad'] > 0 ? Math.round(proy_sem_prom_ini_curva['valor'] / proy_sem_prom_ini_curva['cantidad']) : 0;
        num_sem_proy > 0 ? $('#th_num_sem_' + num_sem_proy).css('background-color', 'orange') : false;
    }

    $(window).ready(function () {
        proyectar_inicio_curvas();
        colorear_celdas();
    })
</script>

<style>
    .th_fijo_right_0 {
        position: sticky;
        right: 0;
        z-index: 5;
    }

    .th_fijo_left_0 {
        position: sticky;
        left: 0;
        z-index: 5;
    }

    .th_fijo_left_1 {
        position: sticky;
        left: 71px;
        z-index: 5;
    }

    .th_fijo_left_2 {
        position: sticky;
        left: 142px;
        z-index: 5;
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
        z-index: 5;
    }
</style>