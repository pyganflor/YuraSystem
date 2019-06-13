<div class="box-group" id="accordion">
    @foreach($data as $d)
        <div class="panel box box-primary">
            <div class="box-header with-border">
                <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$d['variedad']->id_variedad}}" aria-expanded="false"
                       class="collapsed" style="color: {{$d['variedad']->color}}">
                        {{$d['variedad']->planta->siglas}} - {{$d['variedad']->nombre}}
                    </a>
                </h4>
            </div>
            <div id="collapse{{$d['variedad']->id_variedad}}" class="panel-collapse collapse" aria-expanded="false">
                <div class="box-body">
                    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d;">
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
                        @foreach($d['ciclos'] as $pos_c => $c)
                            @php
                                $area_x_sem = 0;
                                $ciclo_x_sem = 0;
                            @endphp
                            @foreach($c as $ciclo)
                                @php
                                    $tallos = $ciclo->getTallosCosechados();
                                @endphp
                                <tr style="background-color: {{$colores_semana[$pos_c]}}">
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$ciclo->modulo->nombre}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$ciclo->fecha_inicio}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$ciclo->modulo->getPodaSiembraByCiclo($ciclo->id_ciclo)}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{number_format($ciclo->area, 2)}}
                                        @php
                                            $area_x_sem += $ciclo->area;
                                        @endphp
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{difFechas($ciclo->fecha_fin, $ciclo->fecha_inicio)->days}}
                                        @php
                                            $ciclo_x_sem += difFechas($ciclo->fecha_fin, $ciclo->fecha_inicio)->days;
                                        @endphp
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{difFechas($ciclo->fecha_cosecha, $ciclo->fecha_inicio)->days}}
                                    </td>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{$ciclo->get80Porciento()}}
                                    </th>
                                    <th class="text-center" style="border-color: #9d9d9d">
                                        {{number_format($tallos, 2)}}
                                    </th>
                                    <th class="text-center"
                                        style="border-color: #9d9d9d">
                                        {{round($tallos / $ciclo->area)}}
                                    </th>
                                    <th class="text-center"
                                        style="border-color: #9d9d9d">
                                        {{$ciclo->fecha_fin}}
                                    </th>
                                </tr>
                            @endforeach
                            <tr style="background-color: white">
                                <th class="text-center" style="border-color: #9d9d9d" colspan="3">
                                    {{$semanas[$pos_c]->codigo}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{number_format($area_x_sem, 2)}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{round($ciclo_x_sem / count($d['ciclos']), 2)}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    1ra Flor
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    80%
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    Tallos Cosechados
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    Tallos/m<sup>2</sup>
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    Final
                                </th>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>