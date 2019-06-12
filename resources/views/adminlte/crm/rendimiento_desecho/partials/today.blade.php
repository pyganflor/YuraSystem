<p class="text-center">
    <strong>
        Rendimiento y Desecho del día
    </strong>
</p>
<table class="table-bordered table-striped table-responsive" width="100%" style="border: 2px solid #9d9d9d;">
    <tr>
        <th class="text-center" style="background-color: #357ca5; color: white">
            Área
        </th>
        <th class="text-center" style="background-color: #357ca5; color: white">
            Rendimiento
        </th>
        <th class="text-center" style="background-color: #357ca5; color: white">
            Desecho
        </th>
    </tr>
    <tr class="bg-teal">
        <th class="text-center" style="border-color: black; color: black">
            Cosecha
        </th>
        <th class="text-center mouse-hand" style="border-color: black"
            onclick="ver_rendimiento_cosecha('{{isset($today['cosecha']['cosecha']) ? $today['cosecha']['cosecha']->id_cosecha : ''}}')">
            <span class="badge btn-link">{{$today['cosecha']['rendimiento']}}</span>
        </th>
        <th class="text-center" style="border-color: black; color: black">
            -
        </th>
    </tr>
    <tr class="bg-green">
        <th class="text-center" style="border-color: white">
            Verde
        </th>
        <th class="text-center mouse-hand" style="border-color: white"
            onclick="ver_rendimiento_verde('{{isset($today['verde']['verde']) ? $today['verde']['verde']->id_clasificacion_verde : ''}}')">
            <span class="badge btn-link">{{$today['verde']['rendimiento']}}</span>
        </th>
        <th class="text-center" style="border-color: white">
            <span class="badge">{{$today['verde']['desecho']}}</span>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="border-color: #9d9d9d">
            Blanco
        </th>
        <th class="text-center mouse-hand" style="border-color: #9d9d9d"
            onclick="ver_rendimiento_blanco('{{isset($today['blanco']['blanco']) ? $today['blanco']['blanco']->id_clasificacion_blanco : ''}}')">
            <span class="badge btn-link">{{$today['blanco']['rendimiento']}}</span>
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            <span class="badge {{$today['blanco']['desecho'] >= 0 ? 'bg-green' : 'bg-red'}}">{{round($today['blanco']['desecho'], 2)}}</span>
        </th>
    </tr>
</table>