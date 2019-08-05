<div style="overflow-x: scroll">
    <table class="table table-striped table-bordered table-hover">
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
        </tr>
        </thead>
        <tbody>
        @foreach($modulos as $mod)
            <tr>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$mod['modulo']->nombre}}
                </td>
                @foreach($mod['valores'] as $val)
                    @php
                        $fondo = '';
                        if($val['data']['tipo'] == 'P')
                            $fondo = '#e4ff08';
                        else if($val['data']['tipo'] == 'S')
                            $fondo = '#08ffe8';

                    @endphp
                    <td class="text-center" style="border-color: #9d9d9d; background-color: {{$fondo}}">
                        {{$val['tiempo']}}
                        <br>
                        {{$val['data']['tipo']}}
                        <br>
                        {{$val['data']['info']}}
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>