@if(count($modulo->ciclos) > 0)
    <div style="overflow-x: scroll">
        <table class="table-striped table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d;" id="table_ver_ciclos">
            <thead>
            <tr>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    Inicio
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    Variedad
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    Área m<sup>2</sup>
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    Poda/Siembra
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    Dias
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    1ra Flor
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    80%
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    Tallos Cosechados
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    Final
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                    Opciones
                </th>
            </tr>
            </thead>

            <tbody>
            @foreach($modulo->ciclos->where('estado',1)->sortByDesc('fecha_inicio') as $pos_ciclo => $ciclo)
                <input type="hidden" id="activo_ciclo_modal_{{$ciclo->id_ciclo}}" value="{{$ciclo->activo}}">
                <tr class="{{$ciclo->activo == 1 ? 'bg-green-gradient text-black' : ''}} {{$ciclo->estado == 0 ? 'error' : ''}}"
                    title="{{$ciclo->activo == 1 ? 'Activo' : ''}}">
                    <th class="text-center" style="border-color: #9d9d9d">
                        <span class="elemento_view_{{$ciclo->id_ciclo}}">{{$ciclo->fecha_inicio}}</span>
                        <input type="date" id="fecha_inicio_ciclo_modal_{{$ciclo->id_ciclo}}" value="{{$ciclo->fecha_inicio}}"
                               class="elemento_input_{{$ciclo->id_ciclo}} text-center {{$ciclo->activo == 1 ? 'bg-green-gradient' : ''}}"
                               style="width: 100%; display: none"
                               required>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        <span class="elemento_view_{{$ciclo->id_ciclo}}">{{$ciclo->variedad->siglas}}</span>
                        <select id="variedad_ciclo_modal_{{$ciclo->id_ciclo}}" class="elemento_input_{{$ciclo->id_ciclo}}
                        {{$ciclo->activo == 1 ? 'bg-green-gradient text-black' : ''}}" style="width: 100%; display: none">
                            @foreach(getVariedades() as $item)
                                <option value="{{$item->id_variedad}}" {{$item->id_variedad == $ciclo->id_variedad ? 'selected' : ''}}>
                                    {{$item->siglas}}
                                </option>
                            @endforeach
                        </select>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        <span class="elemento_view_{{$ciclo->id_ciclo}}">{{$ciclo->area}}m<sup>2</sup></span>
                        <input type="number" id="area_ciclo_modal_{{$ciclo->id_ciclo}}" value="{{$ciclo->area}}"
                               class="elemento_input_{{$ciclo->id_ciclo}} text-center {{$ciclo->activo == 1 ? 'bg-green-gradient' : ''}}"
                               style="width: 100%; display: none" required>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        <span class="elemento_view_{{$ciclo->id_ciclo}}">{{$modulo->getPodaSiembraByCiclo($ciclo->id_ciclo)}}</span>
                        <select class="elemento_input_{{$ciclo->id_ciclo}} text-center {{$ciclo->activo == 1 ? 'bg-green-gradient text-black' : ''}}"
                                id="poda_siembra_ciclo_modal_{{$ciclo->id_ciclo}}" style="width: 100%; display: none">
                            <option value="P" {{$ciclo->poda_siembra == 'P' ? 'selected' : ''}}>Poda</option>
                            <option value="S" {{$ciclo->poda_siembra == 'S' ? 'selected' : ''}}>Siembra</option>
                        </select>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        @if($ciclo->fecha_fin != '')
                            {{difFechas($ciclo->fecha_fin, $ciclo->fecha_inicio)->days}}
                        @else
                            {{difFechas(date('Y-m-d'), $ciclo->fecha_inicio)->days}}
                        @endif
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        @if($ciclo->fecha_cosecha != '')
                            <span class="elemento_view_{{$ciclo->id_ciclo}}">
                                {{difFechas($ciclo->fecha_cosecha, $ciclo->fecha_inicio)->days}}
                            </span>
                        @else
                            <span class="elemento_view_{{$ciclo->id_ciclo}}">
                                {{difFechas(date('Y-m-d'), $ciclo->fecha_inicio)->days}}
                            </span>
                        @endif
                        <input type="text" id="fecha_cosecha_ciclo_modal_{{$ciclo->id_ciclo}}"
                               value="{{$ciclo->fecha_cosecha != '' ? difFechas($ciclo->fecha_cosecha, $ciclo->fecha_inicio)->days : ''}}"
                               class="elemento_input_{{$ciclo->id_ciclo}} text-center {{$ciclo->activo == 1 ? 'bg-green-gradient' : ''}}"
                               style="width: 100%; display: none" required onkeypress="return isNumber(event)" maxlength="3">
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{$ciclo->get80Porciento()}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{number_format($ciclo->getTallosCosechados())}}
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        <span class="elemento_view_{{$ciclo->id_ciclo}}">
                            {{$ciclo->fecha_fin}}
                        </span>
                        <input type="date" id="fecha_fin_ciclo_modal_{{$ciclo->id_ciclo}}" value="{{$ciclo->fecha_fin}}"
                               class="elemento_input_{{$ciclo->id_ciclo}} text-center {{$ciclo->activo == 1 ? 'bg-green-gradient' : ''}}"
                               style="width: 100%; display: none" required>
                    </th>
                    <th class="text-center" style="border-color: #9d9d9d">
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-primary" title="Ver cosechas"
                                    onclick="ver_cosechas('{{$ciclo->id_ciclo}}')">
                                <i class="fa fa-fw fa-leaf"></i>
                            </button>
                            @if($ciclo->activo == 1)
                                <button type="button" class="btn btn-xs btn-warning" title="Terminar ciclo"
                                        onclick="terminar_ciclo('{{$modulo->id_modulo}}', true)">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            @elseif($modulo->cicloActual() == '')
                                <button type="button" class="btn btn-xs btn-warning" title="Abrir ciclo"
                                        onclick="abrir_ciclo('{{$modulo->id_modulo}}', '{{$ciclo->id_ciclo}}', true)">
                                    <i class="fa fa-fw fa-check"></i>
                                </button>
                            @endif
                            <button type="button" class="btn btn-xs btn-success elemento_view_{{$ciclo->id_ciclo}}" title="Editar ciclo"
                                    onclick="editar_ciclo('{{$ciclo->id_ciclo}}')">
                                <i class="fa fa-fw fa-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-success elemento_input_{{$ciclo->id_ciclo}}" title="Guardar ciclo"
                                    onclick="guardar_ciclo('{{$ciclo->id_ciclo}}', '{{$modulo->id_modulo}}')" style="display: none">
                                <i class="fa fa-fw fa-save"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-danger" title="Eliminar ciclo"
                                    onclick="eliminar_ciclo('{{$ciclo->id_ciclo}}')">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </div>
                    </th>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <input type="hidden" id="id_modulo" value="{{$modulo->id_modulo}}">

    <script>
        estructura_tabla('table_ver_ciclos', false, true);
    </script>
@else
    <div class="alert alert-info text-center">
        No se han encontrado resultados
    </div>
@endif