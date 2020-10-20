@if(count($modulos) > 0)
    <div style="overflow-x: scroll">
        <table class="table-striped table-bordered" width="100%" style="border: 1px solid #9d9d9d; border-radius: 18px 18px 0 0"
               id="table_listado_ciclos">
            <thead>
            <tr>
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388; border-radius: 18px 0 0 0"
                    rowspan="2">
                    Módulo
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388" colspan="{{$tipo == 1 ? 13 : 8}}">
                    Ciclos
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388; border-radius: 0 18px 0 0"
                    rowspan="2">
                    Opciones
                </th>
            </tr>
            <tr>
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                    Inicio
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                    Poda/Siembra
                </th>
                @if($tipo == 1)
                    <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                        Dias
                    </th>
                    <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                        1ra Flor
                    </th>
                    <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                        80%
                    </th>
                    <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                        Tallos Cosechados
                    </th>
                @endif
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                    Cosecha
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                    Final
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                    Área m<sup>2</sup>
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                    Ptas Iniciales
                </th>
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                    Ptas muertas
                </th>
                @if($tipo == 1)
                    <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                        Ptas actuales
                    </th>
                @endif
                <th class="text-center" style="border-color: white; color: white; background-color: #00b388">
                    Conteo T/P
                </th>
            </tr>
            </thead>
            <tbody>
            @php
                $total_area = 0;
                $total_iniciales = 0;
                $total_muertas = 0;
                $total_actuales = 0;
            @endphp
            @foreach($modulos as $pos_mdl => $modulo)
                @php
                    $cicloActual = $modulo->cicloActual();
                    $getLastCiclo = $modulo->getLastCiclo();
                @endphp
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$modulo->nombre}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <span class="hidden">{{$tipo == 1 ? $cicloActual->fecha_inicio : date('Y-m-d')}}</span>
                        <input type="date" id="ciclo_fecha_inicio_{{$modulo->id_modulo}}" name="ciclo_fecha_inicio_{{$modulo->id_modulo}}"
                               required style="width: 100%" value="{{$tipo == 1 ? $cicloActual->fecha_inicio : date('Y-m-d')}}"
                               class="text-center input-yura_white">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        @if($tipo == 1)
                            {{$modulo->getPodaSiembraActual()}}
                        @endif
                        <select name="ciclo_poda_siembra_{{$modulo->id_modulo}}" id="ciclo_poda_siembra_{{$modulo->id_modulo}}"
                                class="input-yura_white">
                            <option value="P" {{$tipo == 1 && $cicloActual->poda_siembra == 'P' ? 'selected' : ''}}>Poda</option>
                            <option value="S" {{($tipo == 1 && $cicloActual->poda_siembra == 'S') ? 'selected' : ''}}>
                                Siembra
                            </option>
                        </select>
                    </td>
                    @if($tipo == 1)
                        <td class="text-center" style="border-color: #9d9d9d">
                            {{difFechas($cicloActual->fecha_fin != '' ? $cicloActual->fecha_fin : date('Y-m-d'), $cicloActual->fecha_inicio)->days}}
                        </td>
                        <th class="text-center" style="border-color: #9d9d9d">
                            @if($cicloActual != '')
                                @if($cicloActual->fecha_cosecha != '')
                                    {{difFechas($cicloActual->fecha_cosecha, $cicloActual->fecha_inicio)->days}}
                                @else
                                    0
                                @endif
                            @endif
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{$cicloActual != '' ? $cicloActual->get80Porciento() : ''}}
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            {{$cicloActual != '' ? number_format($cicloActual->getTallosCosechados()) : ''}}
                        </th>
                    @endif
                    <td class="text-center" style="border-color: #9d9d9d">
                        <span class="hidden">{{$tipo == 1 ? $cicloActual->fecha_cosecha : ''}}</span>
                        <input type="text" id="ciclo_fecha_cosecha_{{$modulo->id_modulo}}" name="ciclo_fecha_cosecha_{{$modulo->id_modulo}}"
                               style="width: 100%" onkeypress="return isNumber(event)" maxlength="3"
                               value="{{$tipo == 1 && $cicloActual->fecha_cosecha != '' ? difFechas($cicloActual->fecha_cosecha, $cicloActual->fecha_inicio)->days : ''}}"
                               class="text-center input-yura_white" required>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <span class="hidden">{{$tipo == 1 ? $cicloActual->fecha_fin : ''}}</span>
                        <input type="date" id="ciclo_fecha_fin_{{$modulo->id_modulo}}" name="ciclo_fecha_fin_{{$modulo->id_modulo}}"
                               style="width: 100%" value="{{$tipo == 1 ? $cicloActual->fecha_fin : ''}}"
                               class="text-center input-yura_white" required>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        @php
                            $total_area += $tipo == 1 ? $cicloActual->area : $modulo->area;
                        @endphp
                        <span class="hidden">{{number_format($tipo == 1 ? $cicloActual->area : $modulo->area, 2)}}</span>
                        <input type="number" id="ciclo_area_{{$modulo->id_modulo}}" name="ciclo_area_{{$modulo->id_modulo}}"
                               class="text-center input-yura_white" value="{{$tipo == 1 ? $cicloActual->area : $modulo->area}}"
                               style="width: 100%" required>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        @php
                            $total_iniciales += $cicloActual != '' ? $cicloActual->plantas_iniciales : 0;
                        @endphp
                        <span class="hidden">{{$cicloActual != '' ? $cicloActual->plantas_iniciales : 0}}</span>
                        <input type="number" id="ciclo_plantas_iniciales_{{$modulo->id_modulo}}"
                               name="ciclo_plantas_iniciales_{{$modulo->id_modulo}}"
                               style="width: 100%" onkeypress="return isNumber(event)"
                               value="{{$cicloActual != '' ? $cicloActual->plantas_iniciales : ($getLastCiclo != '' ? $getLastCiclo->plantas_iniciales : 0)}}"
                               class="text-center input-yura_white" required>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        @php
                            $total_muertas += $cicloActual != '' ? $cicloActual->plantas_muertas : 0;
                        @endphp
                        <span class="hidden">{{$cicloActual != '' ? $cicloActual->plantas_muertas : 0}}</span>
                        <input type="number" id="ciclo_plantas_muertas_{{$modulo->id_modulo}}"
                               name="ciclo_plantas_muertas_{{$modulo->id_modulo}}"
                               style="width: 100%" onkeypress="return isNumber(event)"
                               value="{{$cicloActual != '' ? $cicloActual->plantas_muertas : 0}}"
                               class="text-center input-yura_white" required>
                    </td>

                    @if($tipo == 1)
                        <td class="text-center" style="border-color: #9d9d9d">
                            @php
                                $total_actuales += $cicloActual->plantas_actuales();
                            @endphp
                            {{$cicloActual->plantas_actuales()}}
                        </td>
                    @endif

                    <td class="text-center" style="border-color: #9d9d9d">
                        <span class="hidden">{{$cicloActual != '' ? $cicloActual->conteo : ''}}</span>
                        <input type="number" id="ciclo_conteo_{{$modulo->id_modulo}}" name="ciclo_conteo_{{$modulo->id_modulo}}"
                               style="width: 100%" value="{{$cicloActual != '' ? $cicloActual->conteo : ''}}"
                               class="text-center input-yura_white" required>
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d" colspan="6">
                        <div class="btn-group">
                            @if($tipo == 1)
                                <button type="button" class="btn btn-xs btn-yura_danger" title="Terminar Ciclo"
                                        onclick="terminar_ciclo('{{$modulo->id_modulo}}')">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                                {{--<button type="button" class="btn btn-xs btn-yura_primary" title="Editar Ciclo"
                                        onclick="update_ciclo('{{$cicloActual->id_ciclo}}', '{{$modulo->id_modulo}}')">
                                    <i class="fa fa-fw fa-pencil"></i>
                                </button>--}}
                            @else
                                <button type="button" class="btn btn-xs btn-yura_primary" title="Crear Ciclo"
                                        onclick="store_ciclo('{{$modulo->id_modulo}}')">
                                    <i class="fa fa-fw fa-save"></i>
                                </button>
                            @endif
                            @if(count($modulo->ciclos->where('estado',1)) > 0)
                                <button type="button" class="btn btn-xs btn-yura_default" title="Ver Ciclos"
                                        onclick="ver_ciclos('{{$modulo->id_modulo}}')">
                                    <i class="fa fa-fw fa-eye"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tr>
                <th class="text-center" style="border-color: #9d9d9d" colspan="{{$tipo == 1 ? 9 : 6}}">
                    Total
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{number_format($total_area, 2)}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$total_iniciales > 0 ? number_format($total_iniciales, 2) : ''}}
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    {{$total_muertas > 0 ? number_format($total_muertas, 2) : ''}}
                </th>
                @if($tipo == 1)
                    <th class="text-center" style="border-color: #9d9d9d">
                        {{number_format($total_actuales, 2)}}
                    </th>
                @endif
                <th class="text-center" style="border-color: #9d9d9d">
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                </th>
            </tr>
        </table>
    </div>
@else
    <div class="alert alert-info text-center">
        No hay resultados que mostrar
    </div>
@endif