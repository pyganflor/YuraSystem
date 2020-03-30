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
            @for($i = $min_dia; $i <= $max_dia; $i++)
                @php
                    $exist = false;
                @endphp
                @foreach($c['cosechas'] as $v)
                    @if(explode('º', $v->info)[0] == $i)
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{$v->cosechados}} - {{$v->info}}
                        </th>
                        @php
                            $exist = true;
                        @endphp
                    @endif
                @endforeach
                @if(!$exist)
                    <th class="text-center" style="border-color: #9d9d9d">
                    </th>
                @endif
            @endfor
        </tr>
    @endforeach
</table>