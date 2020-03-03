<table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Módulo
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Variedad
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Inicio
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Poda/Siembra
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Área
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Ptas Iniciales
        </th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            Opciones
        </th>
    </tr>
    @foreach($nuevos_ciclos as $pos => $item)
        <tr>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$item->modulo->nombre}}
                <input type="hidden" id="id_modulo_{{$pos}}" value="{{$item->id_modulo}}">
                <input type="hidden" id="id_semana_{{$pos}}" value="{{$item->id_semana}}">
            </td>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{$item->variedad->siglas}}
                <input type="hidden" id="id_variedad_{{$pos}}" value="{{$item->id_variedad}}">
            </td>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="date" id="fecha_inicio_{{$pos}}" value="{{date('Y-m-d')}}" style="width: 100%">
            </td>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="number" id="poda_siembra_{{$pos}}" value="{{$item->tipo}}" style="width: 100%" readonly>
            </td>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="number" id="area_{{$pos}}" value="{{$item->modulo->area}}" style="width: 100%">
            </td>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="number" id="plantas_iniciales_{{$pos}}" value="{{$item->modulo->area}}" style="width: 100%">
            </td>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="text" id="curva_{{$pos}}" value="{{$item->curva}}" style="width: 100%">
            </td>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="number" id="conteo_{{$pos}}" value="{{$item->id_semana}}" style="width: 100%">
            </td>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Opciones
            </td>
        </tr>
    @endforeach
</table>