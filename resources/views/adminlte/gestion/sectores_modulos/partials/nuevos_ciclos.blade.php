<div style="overflow-x: scroll">
    <table class="table-bordered table-striped" style="width: 100%; border: 1px solid #9d9d9d">
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
            <tr id="tr_nuevo_ciclo_{{$pos}}">
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->modulo->nombre}}
                    <input type="hidden" id="id_modulo_{{$pos}}" value="{{$item->id_modulo}}">
                    <input type="hidden" id="id_semana_{{$pos}}" value="{{$item->id_semana}}">
                    <input type="hidden" id="id_proyeccion_modulo_{{$pos}}" value="{{$item->id_proyeccion_modulo}}">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$item->variedad->siglas}}
                    <input type="hidden" id="id_variedad_{{$pos}}" value="{{$item->id_variedad}}">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="date" class="text-center input-yura_white" id="fecha_inicio_{{$pos}}" value="{{date('Y-m-d')}}" style="width: 100%" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="text" class="text-center input-yura_white" id="poda_siembra_{{$pos}}" value="{{$item->tipo}}" style="width: 100%" readonly>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center input-yura_white" id="area_{{$pos}}" value="{{$item->modulo->area}}" style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center input-yura_white" id="plantas_iniciales_{{$pos}}" value="{{$item->plantas_iniciales}}"
                           style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="text" class="text-center input-yura_white" id="curva_{{$pos}}" value="{{$item->curva}}" style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center input-yura_white" id="conteo_{{$pos}}" value="{{$item->tallos_planta}}" style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center input-yura_white" id="semana_poda_siembra_{{$pos}}" value="{{$item->semana_poda_siembra}}"
                           style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" class="text-center input-yura_white" id="desecho_{{$pos}}" value="{{$item->desecho}}" style="width: 100%">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <button class="btn btn-xs btn-yura_primary" onclick="store_nuevo_ciclo('{{$pos}}')" id="btn_nuevo_ciclo_{{$pos}}"
                            title="Crear ciclo">
                        <i class="fa fa-fw fa-check"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </table>
</div>

<script>
    function store_nuevo_ciclo(pos) {
        datos = {
            _token: '{{csrf_token()}}',
            id_modulo: $('#id_modulo_' + pos).val(),
            id_variedad: $('#id_variedad_' + pos).val(),
            id_proyeccion_modulo: $('#id_proyeccion_modulo_' + pos).val(),
            id_semana: $('#id_semana_' + pos).val(),
            fecha_inicio: $('#fecha_inicio_' + pos).val(),
            poda_siembra: $('#poda_siembra_' + pos).val(),
            area: $('#area_' + pos).val(),
            plantas_iniciales: $('#plantas_iniciales_' + pos).val(),
            curva: $('#curva_' + pos).val(),
            conteo: $('#conteo_' + pos).val(),
            semana_poda_siembra: $('#semana_poda_siembra_' + pos).val(),
            desecho: $('#desecho_' + pos).val(),
        };
        post_jquery('{{url('sectores_modulos/store_nuevo_ciclo')}}', datos, function () {
            $('#tr_nuevo_ciclo_' + pos).remove();
        });
    }
</script>