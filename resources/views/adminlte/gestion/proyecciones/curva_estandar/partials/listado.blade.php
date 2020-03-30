<table class="table-striped table-bordered" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Módulo
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Semana P/S
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Curva
        </th>
        @for($i = $min_dia; $i <= $max_dia; $i++)
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$i}}
            </th>
        @endfor
    </tr>
    @foreach($ciclos as $c)
        @if(count(explode('-', $c['ciclo']->curva)) == count($c['cosechas']))
            @php
                $modulo = $c['ciclo']->modulo;
            @endphp
            <tr>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$modulo->nombre}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$c['ciclo']->semana_poda_siembra}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$c['ciclo']->curva}}
                </th>
                @foreach($c['cosechas'] as $v)
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{$v->cosechados}} - {{$v->info}}
                    </th>
                @endforeach
            </tr>
        @endif
    @endforeach
</table>