<div style="overflow-x: scroll; overflow-y: scroll; max-height: 550px">
    <table class="table-striped table-bordered" style="width: 100%; border: 2px solid #9d9d9d; font-size: 0.9em">
        <tr class="tr_fija_top_0">
            <th class="text-center th_fijo_left_0" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                <div style="width: 70px">
                    Módulo
                </div>
            </th>
            <th class="text-center th_fijo_left_1" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                <div style="width: 70px">
                    Semana Inicio
                </div>
            </th>
            <th class="text-center th_fijo_left_2" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                <div style="width: 70px">
                    Días Fen.
                </div>
            </th>
            @php
                $array_prom = [];
            @endphp
            @for($i = 1; $i <= $num_semanas; $i++)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
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
        </tr>
        @foreach($ciclos as $pos => $item)
            @php
                $modulo = $item['ciclo']->modulo;
                $semana = $item['ciclo']->semana();
                $cant_mon = 1;
            @endphp
            <tr>
                <th class="text-center th_fijo_left_0" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$modulo->nombre}}
                </th>
                <th class="text-center th_fijo_left_1" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$semana->codigo}}
                </th>
                <th class="text-center th_fijo_left_2" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{difFechas($item['ciclo']->fecha_inicio, date('Y-m-d'))->days}}
                </th>
                @foreach($item['monitoreos'] as $pos_mon => $mon)
                    <th class="text-center celda_hovered" style="border-color: #9d9d9d; background-color: #e9ecef"
                        id="td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}"
                        onmouseover="mouse_over_celda('td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}', 1)"
                        onmouseleave="mouse_over_celda('{{$item['ciclo']->id_ciclo}}', 0)">
                        <input type="number" style="width: 100%" id="monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}"
                               value="{{$mon->altura}}" readonly ondblclick="$(this).attr('readonly', false)"
                               class="text-center">
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
                    <th class="text-center celda_hovered" style="border-color: #9d9d9d"
                        id="td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}"
                        onmouseover="mouse_over_celda('td_monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}', 1)"
                        onmouseleave="mouse_over_celda('{{$item['ciclo']->id_ciclo}}', 0)">
                        <input type="number" style="width: 100%;" id="monitoreo_{{$item['ciclo']->id_ciclo}}_{{$cant_mon}}" readonly
                               ondblclick="$(this).attr('readonly', false)" class="text-center"
                               onchange="guardar_monitoreo('{{$item['ciclo']->id_ciclo}}', '{{$cant_mon}}')">
                    </th>
                    @php
                        $cant_mon++;
                    @endphp
                @endfor
            </tr>
        @endforeach
        <tr>
            <th class="text-center th_fijo_left_0" style="border-color: #9d9d9d; background-color: #357CA5; color: white" colspan="3">
                Promedios <sup title="Altura">cm</sup>
            </th>
            @foreach($array_prom as $item)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$item['positivos'] > 0 ? round($item['valor'] / $item['positivos'], 2) : 0}}
                </th>
            @endforeach
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
        $('#td_monitoreo_' + ciclo + '_' + cant_mon).LoadingOverlay('show');
        $.post('{{url('monitoreo_ciclos/guardar_monitoreo')}}', datos, function (retorno) {
            if (!retorno.success) {
                alert(retorno.mensaje);
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#td_monitoreo_' + ciclo + '_' + cant_mon).LoadingOverlay('hide');
            $('#monitoreo_' + ciclo + '_' + cant_mon).attr('readonly', true)
        });
    }
</script>

<style>
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
</style>