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
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$mod['modulo']->nombre}}
                </td>
                @foreach($mod['valores'] as $pos_val => $val)
                    @php
                        $fondo = '';
                        if($val['data']['tipo'] == 'P')
                            if(substr($val['data']['info'], 2) > 1)
                                $fondo = '#efff00';
                            else
                                $fondo = '#00ff00';
                        else if($val['data']['tipo'] == 'S')
                            $fondo = '#08ffe8';
                        else if($val['data']['tipo'] == 'Y')
                            $fondo = '#9100ff7d';

                    @endphp
                    <td class="text-center {{in_array($val['data']['tipo'], ['F', 'P', 'S', 'Y']) ? 'mouse-hand' : ''}}"
                        onmouseover="$(this).css('border', '3px solid black')"
                        onmouseleave="$(this).css('border', '1px solid #9d9d9d')"
                        style="border-color: #9d9d9d; background-color: {{$fondo}}"
                        onclick="select_celda('{{$val['data']['tipo']}}', '{{$mod['modulo']->id_modulo}}', '{{$semanas[$pos_val]->id_semana}}', null)">
                        {{$val['data']['info']}}
                    </td>
                @endforeach
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$mod['modulo']->nombre}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>