<div style="overflow-x: scroll; overflow-y: scroll; max-height: 450px">
    <table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d">
        <tr>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                <div style="padding: 5px">
                    Módulo
                </div>
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                <div style="padding: 5px">
                    Semana Inicio
                </div>
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                <div style="padding: 5px">
                    Semana Fen.
                </div>
            </th>
            @for($i = 1; $i <= $max_semana; $i++)
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    <div style="width: 50px">
                        {{$i}}º
                    </div>
                </th>
            @endfor
        </tr>
        @foreach($ciclos as $por_c => $c)
            @php
                $modulo = $c['ciclo']->modulo;
                $semana = $c['ciclo']->semana();
                $pos_sem = 0;
            @endphp
            <tr class="{{$modulo->id_sector == $sector || $sector == 'T' ? '' : 'hide'}}">
                <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{$modulo->nombre}}
                </td>
                <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{$semana->codigo}}
                </td>
                <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{intval(difFechas($c['ciclo']->fecha_inicio, date('Y-m-d'))->days / 7) + 1}}
                </td>
                @foreach($c['temperaturas'] as $pos_temp => $temp)
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$temp->acumulado}}
                    </td>
                    @php
                        $pos_sem++;
                    @endphp
                @endforeach
                @for($i = $pos_sem + 1; $i <= $max_semana; $i++)
                    <td class="text-center" style="border-color: #9d9d9d">
                    </td>
                @endfor
            </tr>
        @endforeach
    </table>
</div>
