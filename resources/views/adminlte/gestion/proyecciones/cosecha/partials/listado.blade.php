<div class="progress progress-xs hide" id="div_barra_progreso">
    <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0"
         aria-valuemax="100" style="width: 0%" id="barra_progreso">
    </div>
</div>

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
                            <a href="javascript:void(0)" onclick="actualizar_proyecciones()">
                                Actualizar Semanas
                            </a>
                        </li>
                        <li>
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
                        </li>
                    </ul>
                </div>
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
                    <td class="text-center celda_hovered {{in_array($val->tipo, ['F', 'P', 'S', 'T', 'Y']) ? 'mouse-hand' : ''}}"
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
    });

    function restaurar_proyeccion(mod) {
        if (mod != null) {
            datos = {
                _token: '{{csrf_token()}}',
                modulo: mod
            };
            $('#tr_modulo_' + mod).LoadingOverlay('show');
            $.post('{{url('proy_cosecha/restaurar_proyeccion')}}', datos, function (retorno) {
                setTimeout(function () {
                    listar_proyecciones();
                }, 500);
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta_errores(retorno.responseText);
            }).always(function () {
                $('#tr_modulo_' + mod).LoadingOverlay('hide');
            });
        } else {
            var all = $('.checkbox_modulo');
            var selected = [];
            for (i = 0; i < all.length; i++) {
                if ($('#' + all[i].id).prop('checked') == true) {
                    selected.push(all[i].id.substr(16));
                }
            }

            factor = (Math.round((100 / selected.length) * 100) / 100);
            total_progress = 0;
            $('#div_barra_progreso').removeClass('hide');

            for (i = 0; i < selected.length; i++) {
                datos = {
                    _token: '{{csrf_token()}}',
                    modulo: selected[i]
                };
                mod = datos['modulo'];

                $('#tr_modulo_' + mod).LoadingOverlay('show');

                $.post('{{url('proy_cosecha/restaurar_proyeccion')}}', datos, function (retorno) {
                    mod = retorno.modulo;
                    total_progress += factor;
                    $('#barra_progreso').css('width', total_progress + '%');
                    $('#celda_modulo_' + mod).LoadingOverlay('hide');

                    if (mod == selected[selected.length - 1]) {
                        setTimeout(function () {
                            $('#div_barra_progreso').hide();
                            listar_proyecciones();
                        }, 500);
                    }
                }, 'json').fail(function (retorno) {
                    console.log(retorno);
                    alerta_errores(retorno.responseText);
                }).always(function () {
                    $('#tr_modulo_' + mod).LoadingOverlay('hide');
                });
            }
        }
    }

    function actualizar_proyecciones(mod) {
        if (mod != null) {
            datos = {
                _token: '{{csrf_token()}}',
                modulo: mod,
                variedad: $('#filtro_predeterminado_variedad').val(),
                desde: $('#filtro_predeterminado_desde').val(),
                hasta: $('#filtro_predeterminado_hasta').val(),
            };
            $('#tr_modulo_' + mod).LoadingOverlay('show');
            $.post('{{url('proy_cosecha/actualizar_proyecciones')}}', datos, function (retorno) {
                setTimeout(function () {
                    listar_proyecciones();
                }, 500);
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta_errores(retorno.responseText);
            }).always(function () {
                $('#tr_modulo_' + mod).LoadingOverlay('hide');
            });
        } else {
            var all = $('.checkbox_modulo');
            var selected = [];
            for (i = 0; i < all.length; i++) {
                if ($('#' + all[i].id).prop('checked') == true) {
                    selected.push(all[i].id.substr(16));
                }
            }

            factor = (Math.round((100 / selected.length) * 100) / 100);
            total_progress = 0;
            $('#div_barra_progreso').removeClass('hide');

            for (i = 0; i < selected.length; i++) {
                datos = {
                    _token: '{{csrf_token()}}',
                    modulo: selected[i],
                    variedad: $('#filtro_predeterminado_variedad').val(),
                    desde: $('#filtro_predeterminado_desde').val(),
                    hasta: $('#filtro_predeterminado_hasta').val(),
                };
                mod = datos['modulo'];

                $('#tr_modulo_' + mod).LoadingOverlay('show');

                $.post('{{url('proy_cosecha/actualizar_proyecciones')}}', datos, function (retorno) {
                    mod = retorno.modulo;
                    total_progress += factor;
                    $('#barra_progreso').css('width', total_progress + '%');
                    $('#celda_modulo_' + mod).LoadingOverlay('hide');

                    if (mod == selected[selected.length - 1]) {
                        setTimeout(function () {
                            $('#div_barra_progreso').hide();
                            listar_proyecciones();
                        }, 500);
                    }
                }, 'json').fail(function (retorno) {
                    console.log(retorno);
                    alerta_errores(retorno.responseText);
                }).always(function () {
                    $('#tr_modulo_' + mod).LoadingOverlay('hide');
                });
            }
        }
    }

    function select_all_modulos(input) {
        if (input.prop('checked') == true) {  // select all
            $('.checkbox_modulo').prop('checked', true);
        } else {    // deseleccionar todos
            $('.checkbox_modulo').prop('checked', false);
        }
    }
</script>