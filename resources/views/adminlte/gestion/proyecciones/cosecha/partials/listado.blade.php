<div style="overflow-x: scroll">
    <table class="table table-striped table-bordered table-hover" style="border: 2px solid #9d9d9d">
        <thead>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Módulos
            </th>
            @foreach($semanas as $sem)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$sem->codigo}}
                </th>
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Módulos
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($modulos as $mod)
            <tr>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$mod['modulo']->nombre}}
                </th>
                @foreach($mod['valores'] as $pos_val => $val)
                    @php
                        $fondo = '';
                        if($val['data']['tipo'] == 'P')
                            if(substr($val['data']['info'], 2) > 1)
                                $fondo = '#ffb100'; // poda de 2 o más
                            else
                                $fondo = '#efff00'; // poda de 1
                        else if($val['data']['tipo'] == 'S')
                            $fondo = '#08ffe8'; // siembra
                        else if($val['data']['tipo'] == 'Y')
                            $fondo = '#9100ff7d';   // proyeccion
                        else if($val['data']['tipo'] == 'T')
                            $fondo = '#03de00'; // semana de cosecha

                    @endphp
                    <td class="text-center {{in_array($val['data']['tipo'], ['F', 'P', 'S', 'Y']) ? 'mouse-hand' : ''}}"
                        onmouseover="$(this).css('border', '3px solid black')"
                        onmouseleave="$(this).css('border', '1px solid #9d9d9d')"
                        style="border-color: #9d9d9d; background-color: {{$fondo}}"
                        onclick="select_celda('{{$val['data']['tipo']}}', '{{$mod['modulo']->id_modulo}}', '{{$semanas[$pos_val]->id_semana}}', '{{$val['data']['modelo']}}')">
                        @if($val['data']['tipo'] == 'T')
                            <strong style="font-size: 0.8em">{{$val['data']['proyectados']}}</strong>
                            <br>
                            <strong style="font-size: 0.8em">{{$val['data']['cosechado']}}</strong>
                        @else
                            {{$val['data']['info']}}
                        @endif
                    </td>
                @endforeach
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$mod['modulo']->nombre}}
                </th>
            </tr>
        @endforeach
        </tbody>
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

