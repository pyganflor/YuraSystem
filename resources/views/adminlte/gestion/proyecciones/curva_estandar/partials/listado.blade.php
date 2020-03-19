<table class="table-striped table-bordered" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d">
            Módulo
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            Ini. Curva
        </th>
    </tr>
    @foreach($ciclos as $c)
        @php
            $modulo = $c->modulo;
        @endphp
        <th class="text-center" style="border-color: #9d9d9d">
            {{$modulo->nombre}}
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            {{$modulo->poda_siembra}}
        </th>
    @endforeach
</table>