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
            @endphp
            <tr>
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
                @foreach($item['monitoreos'] as $pos_mon => $mon)
                    <th class="text-center celda_hovered {{$cant_mon < $min_semanas ? 'hide' : ''}}"
                        style="border-color: #9d9d9d; background-color: #e9ecef" id="td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}"
                        onmouseover="mouse_over_celda('td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}', 1)"
                        onmouseleave="mouse_over_celda('{{$item['ciclo']->id_ciclo}}', 0)">
                        <input type="number" style="width: 100%" id="monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}"
                               value="{{$mon->altura}}" readonly ondblclick="$(this).attr('readonly', false)" min="0"
                               class="text-center input_sem_{{$cant_mon}} input_ciclo_{{$item['ciclo']->id_ciclo}}"
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
                        <input type="number" style="width: 100%;" id="monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}" readonly
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
        <tr class="tr_fijo_bottom_0">
            <th class="text-center th_fijo_left_0" style="border-color: #9d9d9d; background-color: #357CA5; color: white; z-index: 9"
                colspan="3">
                Promedios <sup title="Altura">cm</sup>
            </th>
            @foreach($array_prom as $pos_sem => $item)
                <th class="text-center {{$pos_sem + 1 < $min_semanas ? 'hide' : ''}}" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$item['positivos'] > 0 ? round($item['valor'] / $item['positivos'], 2) : 0}}
                    <input type="hidden" id="prom_sem_{{$pos_sem + 1}}"
                           value="{{$item['positivos'] > 0 ? round($item['valor'] / $item['positivos'], 2) : 0}}">
                </th>
            @endforeach
            <th class="text-center th_fijo_right_0" style="border-color: #9d9d9d; background-color: #357CA5; color: white; z-index: 9">
                <button type="button" class="btn btn-xs btn-block btn-success" onclick="store_nuevos_ingresos()">
                    <i class="fa fa-fw fa-check"></i>
                </button>
            </th>
        </tr>
    </table>
</div>

<script>
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
                        $('#' + inputs[y].id).css('background-color', '#30b32d');
                    } else {
                        $('#' + inputs[y].id).css('background-color', '#f03e3e');
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

    .tr_fija_top_0 th {
        position: sticky;
        top: 0;
    }
</style>