<div style="overflow-x: scroll">
    <table class="table-striped table-bordered table-hover" style="border: 2px solid #9d9d9d" width="100%">
        <thead>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                        <span class="fa fa-caret-down"></span></button>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="javascript:void(0)" class="hide">
                                Crear masiva
                            </a>
                        </li>
                       {{--<li class="divider"></li>
                        <li class="list-group-item-danger">
                            <a href="javascript:void(0)">
                                Actualizar Semanas
                            </a>
                        </li>
                        <li class="list-group-item-danger">
                            <a href="javascript:void(0)" onclick="restaurar_proyeccion()">
                                Restaurar Proyecciones
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="javascript:void(0)"
                               onclick="$('.checkbox_modulo').prop('checked', true); $('#checkbox_modulo_all').prop('checked', true)">
                                <strong>
                                    Seleccionar Todos
                                </strong>
                            </a>
                        </li>--}}
                    </ul>
                </div>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; width: 250px">
                Clientes
            </th>
            @foreach($semanas as $pos_sem => $sem)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; width: 250px">
                    <span data-toggle="tooltip" data-placement="top" data-html="true"
                          title="<em>T.Ramo: {{$sem->tallos_ramo_poda}}</em><br>
                          <em>T.Pta: {{$sem->tallos_planta_poda}}</em><br>
                          <em>%Desecho: {{$sem->desecho}}</em>">
                        {{$sem->codigo}}
                    </span>

                    <input type="hidden" id="semana_{{$pos_sem}}" value="{{$sem->codigo}}">
                </th>
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef; width: 250px">
                Módulos
            </th>
        </tr>
        </thead>
        <tbody>
        @php
            $tallos_proyectados = [];
            $tallos_cosechados = [];
        @endphp
        @foreach($modulos as $mod)
            <tr id="tr_modulo_{{$mod['modulo']->id_modulo}}">
                <th class="text-center" style="border-color: #9d9d9d">
                    <input type="checkbox" id="checkbox_modulo_{{$mod['modulo']->id_modulo}}" class="checkbox_modulo">
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" id="celda_modulo_{{$mod['modulo']->id_modulo}}">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            {{$mod['modulo']->nombre}}
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:void(0)" onclick="actualizar_proyecciones('{{$mod['modulo']->id_modulo}}')">
                                    Actualizar
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="actualizar_manual('{{$mod['modulo']->id_modulo}}')">
                                    Actualizar manualmente
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="javascript:void(0)" onclick="restaurar_proyeccion('{{$mod['modulo']->id_modulo}}')">
                                    Restaurar Proyección
                                </a>
                            </li>
                        </ul>
                    </div>
                </th>
                @foreach($mod['valores'] as $pos_val => $val)
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
                        $tallos_proyectados[$pos_val] = 0;
                        $tallos_cosechados[$pos_val] = 0;
                    @endphp
                    <td class="text-center celda_hovered celda_modulo_{{$mod['modulo']->id_modulo}} {{in_array($val->tipo, ['F', 'P', 'S', 'T', 'Y']) ? 'mouse-hand' : ''}}"
                        style="border-color: #9d9d9d; background-color: {{$fondo}}" id="celda_{{$mod['modulo']->id_modulo}}_{{$pos_val}}"
                        onclick="select_celda('{{$val->tipo}}', '{{$mod['modulo']->id_modulo}}', '{{$val->semana}}', '{{$val->id_variedad}}', '{{$val->tabla}}', '{{$val->modelo}}')"
                        onmouseover="mouse_over_celda('celda_{{$mod['modulo']->id_modulo}}_{{$pos_val}}', 1)"
                        onmouseleave="mouse_over_celda('celda_{{$mod['modulo']->id_modulo}}_{{$pos_val}}', 0)">
                        <span data-toggle="tooltip" data-placement="top" data-html="true"
                              title="{{$title}}">
                            @if($val->tipo == 'T')
                                <strong style="font-size: 0.8em; margin-bottom: 0">
                                    {{$val->proyectados != '' ? number_format($val->proyectados, 2) : 0}}
                                </strong>
                            @else
                                <p style="margin-top: 0; margin-bottom: 0">
                                    {{$val->info}}
                                </p>
                            @endif
                            @if($val->cosechados > 0)
                                <p style="margin-top: 0; margin-bottom: 0">
                                    <strong style="font-size: 0.8em">{{number_format($val->cosechados)}}</strong>
                                </p>
                            @endif
                        </span>
                    </td>
                @endforeach

                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            {{$mod['modulo']->nombre}}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a href="javascript:void(0)" onclick="actualizar_proyecciones('{{$mod['modulo']->id_modulo}}')">
                                    Actualizar
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" onclick="actualizar_manual('{{$mod['modulo']->id_modulo}}')">
                                    Actualizar manualmente
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="javascript:void(0)" onclick="restaurar_proyeccion('{{$mod['modulo']->id_modulo}}')">
                                    Restaurar Proyección
                                </a>
                            </li>
                        </ul>
                    </div>
                </th>
            </tr>
        @endforeach
        </tbody>

        {{-- CALCULAR TOTALES --}}
        @foreach($modulos as $mod)
            @foreach($mod['valores'] as $pos_val => $val)
                @php
                    $tallos_proyectados[$pos_val] += $val->proyectados;
                    $tallos_cosechados[$pos_val] += $val->cosechados;
                @endphp
            @endforeach
        @endforeach

        {{-- TOTALES --}}
        <tr style="background-color: #fdff8b">
            <th class="text-center" style="border-color: #9d9d9d">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Proyectados
                <br>
                <small><em>Tallos/cajas</em></small>
            </th>
            @foreach($tallos_proyectados as $pos_val => $val)
                <th class="text-center" style="border-color: #9d9d9d">
                    @if($val > 0)
                        @php
                            $calibre = getCalibreByRangoVariedad($semanas[$pos_val]->fecha_inicial, $semanas[$pos_val]->fecha_final, $variedad);
                            if($calibre <= 0){
                                if($semanas[$pos_val]->tallos_ramo_poda > 0){
                                    $calibre = $semanas[$pos_val]->tallos_ramo_poda;
                                }
                            }
                        @endphp
                        <span data-toggle="tooltip" data-placement="top" data-html="true"
                              title="{{$semanas[$pos_val]->codigo}} <br> <small>Calib:<em>{{$calibre}}</em></small>">
                            {{number_format($val, 2)}}
                            <br>
                            <strong>
                                @if($calibre > 0)
                                    {{number_format(round(($val / $calibre) / $ramos_x_caja, 2), 2)}}
                                @endif
                            </strong>
                        </span>
                    @endif
                </th>
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d">
                Proyectados
                <br>
                <small><em>Tallos/cajas</em></small>
            </th>
        </tr>
        <tr style="background-color: #c4c4ff">
            <th class="text-center" style="border-color: #9d9d9d">
            </th>
            <th class="text-center" style="border-color: #9d9d9d">
                Cosechados
                <br>
                <small><em>Tallos/cajas</em></small>
            </th>
            @foreach($tallos_cosechados as $pos_val => $val)
                <th class="text-center" style="border-color: #9d9d9d">
                    @if($val > 0)
                        @php
                            $cajas = getCajasByRangoVariedad($semanas[$pos_val]->fecha_inicial, $semanas[$pos_val]->fecha_final, $variedad);
                        @endphp
                        <span data-toggle="tooltip" data-placement="top" data-html="true"
                              title="{{$semanas[$pos_val]->codigo}}">
                            {{number_format($val, 2)}}
                            <br>
                            <strong>
                                @if($cajas > 0)
                                    {{number_format($cajas, 2)}}
                                @endif
                            </strong>
                        </span>
                    @endif
                </th>
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d">
                Cosechados
                <br>
                <small><em>Tallos/cajas</em></small>
            </th>
        </tr>

        <tr>
            <th class="text-center" style="border-color: #9d9d9d">
                <input type="checkbox" id="checkbox_modulo_all" onclick="select_all_modulos($(this))">
            </th>
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
    </table>
</div>
