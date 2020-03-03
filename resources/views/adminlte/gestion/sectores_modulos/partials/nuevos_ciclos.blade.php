<div style="overflow-x: scroll">
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
                Curva
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Conteo
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Inicio Cosecha
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Desecho
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Opciones
            </th>
        </tr>
        @foreach($nuevos_ciclos as $pos => $item)
            <tr>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->modulo->nombre}}
                    <input type="hidden" id="id_modulo_{{$pos}}" value="{{$item->id_modulo}}">
                    <input type="hidden" id="id_semana_{{$pos}}" value="{{$item->id_semana}}">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->variedad->siglas}}
                    <input type="hidden" id="id_variedad_{{$pos}}" value="{{$item->id_variedad}}">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="date" class="text-center" id="fecha_inicio_{{$pos}}" value="{{date('Y-m-d')}}" style="width: 100%" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="text" class="text-center" id="poda_siembra_{{$pos}}" value="{{$item->tipo}}" style="width: 100%" readonly>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center" id="area_{{$pos}}" value="{{$item->modulo->area}}" style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center" id="plantas_iniciales_{{$pos}}" value="{{$item->plantas_iniciales}}"
                           style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="text" class="text-center" id="curva_{{$pos}}" value="{{$item->curva}}" style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center" id="conteo_{{$pos}}" value="{{$item->tallos_planta}}" style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center" id="semana_poda_siembra_{{$pos}}" value="{{$item->semana_poda_siembra}}"
                           style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center" id="desecho_{{$pos}}" value="{{$item->desecho}}" style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">

                </td>
            </tr>
        @endforeach
    </table>
</div>