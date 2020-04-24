<div style="overflow-x: scroll; overflow-y: scroll; max-height: 450px">
    <table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d; font-size: 0.9em">
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
                        $pos_sem++;
                    @endphp
                    <td class="text-center" style="border: {{$pos_sem == $c['ini_curva'] ? '2px solid blue' : '1px solid #9d9d9d'}}">
                        {{$temp->acumulado}}
                    </td>
                @endforeach
                @for($i = $pos_sem + 1; $i <= $max_semana; $i++)
                    <td class="text-center" style="border-color: #9d9d9d">
                    </td>
                @endfor
            </tr>
        @endforeach
    </table>
</div>

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
            <li>Semana de inicio de curva en módulos CON primera flor <i class="fa fa-fw fa-circle-o" style="color: blue"></i></li>{{--
            <li>Semana PROMEDIO de inicio de curva <sup>real</sup> <i class="fa fa-fw fa-circle" style="color: #fbff00"></i></li>
            <li>Semana PROMEDIO de inicio de curva <sup>proyectado</sup> <i class="fa fa-fw fa-circle" style="color: orange"></i></li>--}}
        </ul>
    </div>
</div>

<style>
    .tr_fijo_top_0 th {
        position: sticky;
        top: 0;
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
