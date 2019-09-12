<div style="overflow-x: scroll">
    <table class="table table-striped table-bordered table-hover" style="border: 2px solid #9d9d9d" width="100%">
        <thead>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; width: 250px">
                Módulos
            </th>
            @foreach($semanas as $sem)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; width: 250px">
                    <span data-toggle="tooltip" data-placement="top" data-html="true"
                          title="<em>T.Ramo: {{$sem->tallos_ramo_poda}}</em><br>
                          <em>T.Pta: {{$sem->tallos_planta_poda}}</em><br>
                          <em>%Desecho: {{$sem->desecho}}</em>">
                        {{$sem->codigo}}
                    </span>
                </th>
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; width: 250px">
                Módulos
            </th>
        </tr>
        </thead>
        <tbody>
        @php
            $cajas_proyectadas = [];
        @endphp
        @foreach($modulos as $mod)
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$mod['modulo']->nombre}}
                </th>
                @foreach($mod['valores'] as $pos_val => $val)
                    {{--@php
                        $fondo = '';
                        $title = '<em>Mod: '.$mod['modulo']->nombre.'</em><br>'.
                                 '<em>Sem: '.$semanas[$pos_val]->codigo.'</em><br>';
                        if($val['data']['tipo'] == 'P'){
                            if(substr($val['data']['info'], 2) > 1)
                                $fondo = '#ffb100'; // poda de 2 o más
                            else
                                $fondo = '#efff00'; // poda de 1
                            $title .= '<em>Ptas.Ini: '.number_format($val['data']['ciclo']->plantas_iniciales).'</em><br>';
                            $title .= '<em>Ptas.Act: '.number_format($val['data']['ciclo']->plantas_actuales()).'</em><br>';
                            $title .= '<em>T/Ptas: '.($val['data']['ciclo']->conteo).'</em><br>';
                            $title .= '<em>Sem.Cos: '.($val['data']['ciclo']->semana_poda_siembra).'</em><br>';
                            $title .= '<em>Curva: '.($val['data']['ciclo']->curva).'</em><br>';
                            $title .= '<em>Desecho: '.($val['data']['ciclo']->desecho).'%</em><br>';
                        } else if($val['data']['tipo'] == 'S'){
                            $fondo = '#08ffe8'; // siembra
                            $title .= '<em>Ptas.Ini: '.number_format($val['data']['ciclo']->plantas_iniciales).'</em><br>';
                            $title .= '<em>Ptas.Act: '.number_format($val['data']['ciclo']->plantas_actuales()).'</em><br>';
                            $title .= '<em>T/Ptas: '.($val['data']['ciclo']->conteo).'</em><br>';
                            $title .= '<em>Sem.Cos: '.($val['data']['ciclo']->semana_poda_siembra).'</em><br>';
                            $title .= '<em>Curva: '.($val['data']['ciclo']->curva).'</em><br>';
                            $title .= '<em>Desecho: '.($val['data']['ciclo']->desecho).'%</em><br>';
                        } else if($val['data']['tipo'] == 'Y'){
                            $fondo = '#9100ff7d';   // proyeccion
                            if($val['data']['proy']->tipo != 'C'){  // no está cerrada la proyeccion
                                $title .= '<em>Ptas.Ini: '.number_format($val['data']['proy']->plantas_iniciales).'</em><br>';
                                $title .= '<em>T/Ptas: '.($val['data']['proy']->tallos_planta).'</em><br>';
                                $title .= '<em>Sem.Cos: '.($val['data']['proy']->semana_poda_siembra).'</em><br>';
                                $title .= '<em>Curva: '.($val['data']['proy']->curva).'</em><br>';
                                $title .= '<em>Desecho: '.($val['data']['proy']->desecho).'%</em><br>';
                            } else {
                                $title .= '<em>Cierre de módulo</em>';
                            }
                        } else if($val['data']['tipo'] == 'T'){
                            $fondo = '#03de00'; // semana de cosecha
                            if($val['data']['tabla'] == 'C'){   // ciclo
                                $title .= '<em>Ptas.Ini: '.number_format($val['data']['ciclo']->plantas_iniciales).'</em><br>';
                                $title .= '<em>Ptas.Act: '.number_format($val['data']['ciclo']->plantas_actuales()).'</em><br>';
                                $title .= '<em>T/Ptas: '.($val['data']['ciclo']->conteo).'</em><br>';
                                $title .= '<em>Sem.Cos: '.($val['data']['ciclo']->semana_poda_siembra).'</em><br>';
                                $title .= '<em>Curva: '.($val['data']['ciclo']->curva).'</em><br>';
                                $title .= '<em>Desecho: '.($val['data']['ciclo']->desecho).'%</em><br>';
                            } else {    // proyeccion_modulo
                                $title .= '<em>Ptas.Ini: '.number_format($val['data']['proy']->plantas_iniciales).'</em><br>';
                                $title .= '<em>T/Ptas: '.($val['data']['proy']->tallos_planta).'</em><br>';
                                $title .= '<em>Sem.Cos: '.($val['data']['proy']->semana_poda_siembra).'</em><br>';
                                $title .= '<em>Curva: '.($val['data']['proy']->curva).'</em><br>';
                                $title .= '<em>Desecho: '.($val['data']['proy']->desecho).'%</em><br>';
                            }
                        }

                    //$cosechado = getTallosCosechadosByModSemVar($mod['modulo']->id_modulo, $semanas[$pos_val]->codigo, $variedad);
                    @endphp
                    <td class="text-center {{in_array($val['data']['tipo'], ['F', 'P', 'S', 'Y']) ? 'mouse-hand' : ''}}"
                        onmouseover="$(this).css('border', '3px solid black')" onmouseleave="$(this).css('border', '1px solid #9d9d9d')"
                        style="border-color: #9d9d9d; background-color: {{$fondo}}"
                        onclick="select_celda('{{$val['data']['tipo']}}', '{{$mod['modulo']->id_modulo}}', '{{$semanas[$pos_val]->id_semana}}', '{{$val['data']['modelo']}}')">
                        <span data-toggle="tooltip" data-placement="top" data-html="true"
                              title="{{$title}}">
                            @if($val['data']['tipo'] == 'T')
                                <strong style="font-size: 0.8em">{{number_format($val['data']['proyectados'], 2)}}</strong>
                            @else
                                {{$val['data']['info']}}
                            @endif
                            @if($val['data']['cosechado'] > 0)
                                <br>
                                <strong style="font-size: 0.8em">{{number_format($val['data']['cosechado'])}}</strong>
                            @endif
                        </span>
                    </td>--}}

                    @php
                        $fondo = '';
                        $title = '<em>Mod: '.$mod['modulo']->nombre.'</em><br>'.
                                 '<em>Sem: '.$val->semana.'</em><br>';
                        if($val->tipo == 'P'){
                            if(substr($val->info, 2) > 1)
                                $fondo = '#ffb100'; // poda de 2 o más
                            else
                                $fondo = '#efff00'; // poda de 1
                            $title .= '<em>Ptas.Ini: '.number_format($val->plantas_iniciales).'</em><br>';
                            $title .= '<em>Ptas.Act: '.number_format($val->plantas_actuales).'</em><br>';
                            $title .= '<em>T/Ptas: '.($val->tallos_planta).'</em><br>';
                            $title .= '<em>Sem.Cos: '.($val->semana_poda_siembra).'</em><br>';
                            $title .= '<em>Curva: '.($val->curva).'</em><br>';
                            $title .= '<em>Desecho: '.($val->desecho).'%</em><br>';
                        } else if($val->tipo == 'S'){
                            $fondo = '#08ffe8'; // siembra
                            $title .= '<em>Ptas.Ini: '.number_format($val->plantas_iniciales).'</em><br>';
                            $title .= '<em>Ptas.Act: '.number_format($val->plantas_actuales).'</em><br>';
                            $title .= '<em>T/Ptas: '.($val->tallos_planta).'</em><br>';
                            $title .= '<em>Sem.Cos: '.($val->semana_poda_siembra).'</em><br>';
                            $title .= '<em>Curva: '.($val->curva).'</em><br>';
                            $title .= '<em>Desecho: '.($val->desecho).'%</em><br>';
                        } else if($val->tipo == 'Y'){
                            $fondo = '#9100ff7d';   // proyeccion
                            if($val->tipo != 'C'){  // no está cerrada la proyeccion
                                $title .= '<em>Ptas.Ini: '.number_format($val->plantas_iniciales).'</em><br>';
                                $title .= '<em>T/Ptas: '.($val->tallos_planta).'</em><br>';
                                $title .= '<em>Sem.Cos: '.($val->semana_poda_siembra).'</em><br>';
                                $title .= '<em>Curva: '.($val->curva).'</em><br>';
                                $title .= '<em>Desecho: '.($val->desecho).'%</em><br>';
                            } else {
                                $title .= '<em>Cierre de módulo</em>';
                            }
                        } else if($val->tipo == 'T'){
                            $fondo = '#03de00'; // semana de cosecha
                            if($val->tabla == 'C'){   // ciclo
                                $title .= '<em>Ptas.Ini: '.number_format($val->plantas_iniciales).'</em><br>';
                                $title .= '<em>Ptas.Act: '.number_format($val->plantas_actuales).'</em><br>';
                                $title .= '<em>T/Ptas: '.($val->tallos_planta).'</em><br>';
                                $title .= '<em>Sem.Cos: '.($val->semana_poda_siembra).'</em><br>';
                                $title .= '<em>Curva: '.($val->curva).'</em><br>';
                                $title .= '<em>Desecho: '.($val->desecho).'%</em><br>';
                            } else {    // proyeccion_modulo
                                $title .= '<em>Ptas.Ini: '.number_format($val->plantas_iniciales).'</em><br>';
                                $title .= '<em>T/Ptas: '.($val->tallos_planta).'</em><br>';
                                $title .= '<em>Sem.Cos: '.($val->semana_poda_siembra).'</em><br>';
                                $title .= '<em>Curva: '.($val->curva).'</em><br>';
                                $title .= '<em>Desecho: '.($val->desecho).'%</em><br>';
                            }
                        }

                        /* =============== INICIALIZAR TOTALES ===================== */
                        $cajas_proyectadas[$pos_val] = 0;
                    @endphp
                    <td class="text-center {{in_array($val->tipo, ['F', 'P', 'S', 'T', 'Y']) ? 'mouse-hand' : ''}}"
                        style="border-color: #9d9d9d; background-color: {{$fondo}}"
                        onclick="select_celda('{{$val->tipo}}', '{{$mod['modulo']->id_modulo}}', '{{$val->semana}}', '{{$val->id_variedad}}')">
                        <span data-toggle="tooltip" data-placement="top" data-html="true"
                              title="{{$title}}">
                            @if($val->tipo == 'T')
                                <strong style="font-size: 0.8em">
                                    {{$val->proyectados != '' ? number_format($val->proyectados, 2) : 0}}
                                </strong>
                            @else
                                {{$val->info}}
                            @endif
                            @if($val->cosechados > 0)
                                <br>
                                <strong style="font-size: 0.8em">{{number_format($val->cosechados)}}</strong>
                            @endif
                        </span>
                    </td>
                @endforeach

                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$mod['modulo']->nombre}}
                </th>
            </tr>
        @endforeach
        </tbody>

        {{-- CALCULAR TOTALES --}}
        @foreach($modulos as $mod)
            @foreach($mod['valores'] as $pos_val => $val)
                @php
                    $cajas_proyectadas[$pos_val] = $cajas_proyectadas[$pos_val] + $val->proyectados;
                @endphp
            @endforeach
        @endforeach

        {{-- TOTALES --}}
        <tr style="background-color: #fdff8b">
            <th class="text-center" style="border-color: #9d9d9d">
                Proyectados
                <br>
                <small>Tallos/cajas</small>
            </th>
            @foreach($cajas_proyectadas as $pos_val => $val)
                <th class="text-center" style="border-color: #9d9d9d">
                    @if($val > 0)
                        <span data-toggle="tooltip" data-placement="top" title="{{$semanas[$pos_val]->codigo}}">
                            {{number_format($val, 2)}}
                            <br>
                            <strong>
                                @php
                                    $calibre = 1;
                                @endphp
                                {{number_format(round(($val / $calibre) / $ramos_x_caja, 2), 2)}}
                            </strong>
                        </span>
                    @endif
                </th>
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d">
                Proyectados
                <br>
                <small>Tallos/cajas</small>
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
    <ul style="margin-top: 5px" class="list-unstyled panel-collapse collapse" id="collapseLeyenda">
        <li>Segunda poda o posterior <i class="fa fa-fw fa-circle" style="color: #ffb100"></i></li>
        <li>Primera poda <i class="fa fa-fw fa-circle" style="color: #efff00"></i></li>
        <li>Siembra <i class="fa fa-fw fa-circle" style="color: #08ffe8"></i></li>
        <li>Proyección <i class="fa fa-fw fa-circle" style="color: #9100ff7d"></i></li>
        <li>Semana de cosecha <i class="fa fa-fw fa-circle" style="color: #03de00"></i></li>
    </ul>
</div>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>