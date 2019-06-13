<div class="box-group" id="accordion">
    @php
        $grafica = [];
    @endphp
    @foreach($data['variedades'] as $var)
        @php
            $valores_x_var = [];
        @endphp
        <div class="panel box box-primary">
            <div class="box-header with-border">
                <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$var['variedad']->id_variedad}}" aria-expanded="false"
                       class="collapsed" style="color: {{$var['variedad']->color}}">
                        {{$var['variedad']->planta->siglas}} - {{$var['variedad']->nombre}}
                    </a>
                </h4>
            </div>
            <div id="collapse{{$var['variedad']->id_variedad}}" class="panel-collapse collapse" aria-expanded="false">
                <div class="box-body">
                    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d"
                           id="table_ciclo_{{$var['variedad']->id_variedad}}">
                        <thead>
                        <tr>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Módulo
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Inicio
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Poda/Siembra
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Área m<sup>2</sup>
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Días
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                1ra Flor
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                80%
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Tallos Cosechados
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Tallos/m<sup>2</sup>
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Final
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $area_total = 0;
                            $ciclo_total = 0;
                            $tallos_total = 0;

                            $anterior_semana = 0;

                            $area_parcial = 0;
                            $ciclo_parcial = 0;
                            $tallos_parcial = 0;

                            $cant_parcial = 0;

                            $pos_ciclo = 0;
                        @endphp
                        @foreach($var['ciclos'] as $c)
                            @php
                                foreach($semanas as $pos => $s){
                                    if($c->fecha_fin >= $s->fecha_inicial && $c->fecha_fin <= $s->fecha_final)
                                        $semana = [
                                            'semana' => $s,
                                            'color' => $colores_semana[$pos]
                                        ];
                                }

                                if($semana['semana']->codigo == $semanas[$anterior_semana]->codigo){
                                    $area_parcial += $c->area;
                                    $ciclo_parcial += difFechas($c->fecha_inicia, $c->fecha_inicio)->days;
                                    $tallos_parcial += $c->getTallosCosechados();
                                    $cant_parcial++;
                                }
                            @endphp


                            @if($semana['semana']->codigo != $semanas[$anterior_semana]->codigo)
                                <tr style="background-color: white">
                                    <th colspan="3" class="text-center" style="border-color: #9d9d9d">
                                        {{$semanas[$anterior_semana]->codigo}}
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{number_format($area_parcial, 2)}}
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{$cant_parcial > 0 ? round($ciclo_parcial / $cant_parcial, 2) : 0}}
                                    </th>
                                    <td colspan="2" style="border-color: #9d9d9d"></td>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{number_format($tallos_parcial, 2)}}
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{$area_parcial > 0 ? round($tallos_parcial / $area_parcial, 2) : 0}}
                                    </th>
                                    <td style="border-color: #9d9d9d"></td>
                                </tr>

                                @php
                                    array_push($valores_x_var, [
                                        'area' => $area_parcial,
                                        'ciclo' => $ciclo_parcial,
                                        'tallos' => $tallos_parcial,
                                        'semana' => $semanas[$anterior_semana]->codigo
                                    ]);

                                        $anterior_semana++;

                                        $area_parcial = $c->area;
                                        $ciclo_parcial = difFechas($c->fecha_inicia, $c->fecha_inicio)->days;
                                        $tallos_parcial = $c->getTallosCosechados();

                                        $cant_parcial = 1;
                                @endphp
                            @endif


                            <tr style="background-color: {{$semana['color']}}" title="Semana: {{$semana['semana']->codigo}}">
                                <td class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->modulo->nombre}}
                                </td>
                                <td class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->fecha_inicio}}
                                </td>
                                <td class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->modulo->getPodaSiembraByCiclo($c->id_ciclo)}}
                                </td>
                                <td class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{number_format($c->area, 2)}}
                                    @php
                                        $area_total += $c->area;
                                    @endphp
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{difFechas($c->fecha_inicia, $c->fecha_inicio)->days}}
                                    @php
                                        $ciclo_total += difFechas($c->fecha_inicia, $c->fecha_inicio)->days;
                                    @endphp
                                </td>
                                <td class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{difFechas($c->fecha_cosecha, $c->fecha_inicio)->days}}
                                </td>
                                <td class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->get80Porciento()}}
                                </td>
                                <td class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{number_format($c->getTallosCosechados(), 2)}}
                                    @php
                                        $tallos_total += $c->getTallosCosechados();
                                    @endphp
                                </td>
                                <td class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->area > 0 ? round($c->getTallosCosechados() / $c->area , 2) : 0}}
                                </td>
                                <td class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->fecha_fin}}
                                </td>
                            </tr>


                            @if($pos_ciclo == count($var['ciclos']) - 1)
                                <tr style="background-color: white">
                                    <th colspan="3" class="text-center" style="border-color: #9d9d9d">
                                        {{$semanas[$anterior_semana]->codigo}}
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{number_format($area_parcial, 2)}}
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{$cant_parcial > 0 ? round($ciclo_parcial / $cant_parcial, 2) : 0}}
                                    </th>
                                    <td colspan="2" style="border-color: #9d9d9d"></td>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{number_format($tallos_parcial, 2)}}
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{$area_parcial > 0 ? round($tallos_parcial / $area_parcial, 2) : 0}}
                                    </th>
                                    <td style="border-color: #9d9d9d"></td>
                                </tr>

                                @php
                                    array_push($valores_x_var, [
                                        'area' => $area_parcial,
                                        'ciclo' => $ciclo_parcial,
                                        'tallos' => $tallos_parcial,
                                        'semana' => $semanas[$anterior_semana]->codigo
                                    ]);
                                @endphp
                            @endif


                            @php
                                $pos_ciclo++;
                            @endphp
                        @endforeach
                        </tbody>
                        <tr style="background-color: #357ca5; color: white;">
                            <td colspan="3"></td>
                            <th class="text-center"
                                style="border-color: #9d9d9d">
                                {{number_format($area_total, 2)}}
                            </th>
                            <th class="text-center"
                                style="border-color: #9d9d9d">
                                {{count($var['ciclos']) > 0 ? round($ciclo_total / count($var['ciclos']), 2) : 0}}
                            </th>
                            <td colspan="2"></td>
                            <th class="text-center"
                                style="border-color: #9d9d9d">
                                {{number_format($tallos_total, 2)}}
                            </th>
                            <td class="text-center">
                                {{$area_total > 0 ? round($tallos_total / $area_total, 2) : 0}}
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @php
            array_push($grafica, [
                'variedad'=>$var['variedad'],
                'valores'=>$valores_x_var,
            ]);
        @endphp
    @endforeach
</div>