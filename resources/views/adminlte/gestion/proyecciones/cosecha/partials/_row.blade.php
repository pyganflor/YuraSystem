<th class="text-center" style="border-color: #9d9d9d">
    <input type="checkbox" id="checkbox_modulo_{{$modulo->id_modulo}}" class="checkbox_modulo">
</th>

<th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" id="celda_modulo_{{$modulo->id_modulo}}">
    <div class="btn-group">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
            {{$modulo->nombre}}
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="javascript:void(0)" onclick="actualizar_proyecciones('{{$modulo->id_modulo}}')">
                    Actualizar
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" onclick="actualizar_manual('{{$modulo->id_modulo}}')">
                    Actualizar manualmente
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="javascript:void(0)" onclick="restaurar_proyeccion('{{$modulo->id_modulo}}')">
                    Restaurar Proyección
                </a>
            </li>
        </ul>
    </div>
</th>

@foreach($proyecciones as $pos_val => $val)
    @php
        $semana = $val->semana_model();
        $fondo = '';
        $title = '<em>Mod: '.$modulo->nombre.'</em><br>'.
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
            if($val->poda_siembra == 0)
                $fondo = '#08ffe8'; // siembra
            else if($val->poda_siembra == 1)
                $fondo = '#efff00'; // poda de 1
            else if($val->poda_siembra > 1)
                $fondo = '#ffb100'; // poda de 2 o más
            if($val->info != 'C'){  // no está cerrada la proyeccion
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
        /*$tallos_proyectados[$pos_val] = 0;
        $tallos_cosechados[$pos_val] = 0;
        $ptas_iniciales[$pos_val] = 0;
        $total_area[$pos_val] = 0;*/
    @endphp
    <td class="text-center celda_hovered celda_semana_{{$semana->id_semana}} celda_modulo_{{$modulo->id_modulo}} {{in_array($val->tipo, ['F', 'P', 'S', 'T', 'Y']) ? 'mouse-hand' : ''}}"
        style="border-color: #9d9d9d; background-color: {{$fondo}}" id="celda_{{$modulo->id_modulo}}_{{$pos_val}}"
        onclick="select_celda('{{$val->tipo}}', '{{$modulo->id_modulo}}', '{{$val->semana}}', '{{$val->id_variedad}}', '{{$val->tabla}}', '{{$val->modelo}}')"
        onmouseover="mouse_over_celda('celda_{{$modulo->id_modulo}}_{{$pos_val}}', 1)"
        onmouseleave="mouse_over_celda('celda_{{$modulo->id_modulo}}_{{$pos_val}}', 0)">
        <span data-toggle="tooltip" data-placement="top" data-html="true" title="{{$title}}">
            @if($val->tipo == 'T')
                <strong style="font-size: 0.8em; margin-bottom: 0">
                    {{$val->proyectados != '' ? number_format($val->proyectados, 2) : 0}}
                </strong>
            @elseif($val->tipo == 'Y')
                <p style="margin-top: 0; margin-bottom: 0">
                    {{$val->info}}-{{$val->poda_siembra}}
                </p>
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
            {{$modulo->nombre}}
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li>
                <a href="javascript:void(0)" onclick="actualizar_proyecciones('{{$modulo->id_modulo}}')">
                    Actualizar
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" onclick="actualizar_manual('{{$modulo->id_modulo}}')">
                    Actualizar manualmente
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="javascript:void(0)" onclick="restaurar_proyeccion('{{$modulo->id_modulo}}')">
                    Restaurar Proyección
                </a>
            </li>
        </ul>
    </div>
</th>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>