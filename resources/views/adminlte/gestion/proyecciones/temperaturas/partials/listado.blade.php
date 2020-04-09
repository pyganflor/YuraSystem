<table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Módulo
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Semana Inicio
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Semana Fen.
        </th>
    </tr>
    @foreach($ciclos as $por_c => $c)
        @php
            $modulo = $c->modulo;
            $semana = $c->semana();
        @endphp
        @if($modulo->sector == $sector || $sector == 'T')
            <tr>
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{$modulo->nombre}}
                </th>
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{$semana->codigo}}
                </th>
                <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                    {{intval(difFechas($c->fecha_inicio, date('Y-m-d'))->days / 7)}}
                </th>
            </tr>
        @endif
    @endforeach
</table>