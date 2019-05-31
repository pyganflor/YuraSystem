@if(count($modulos) > 0)
    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d" id="table_listado_ciclos">
        <thead>
        <tr>
            <th class="text-center" style="border-color: white; color: white; background-color: #357ca5" rowspan="2">
                Módulo
            </th>
            <th class="text-center" style="border-color: white; color: white; background-color: #357ca5" colspan="6">
                Ciclos
            </th>
            <th class="text-center" style="border-color: white; color: white; background-color: #357ca5" rowspan="2">
                Opciones
            </th>
        </tr>
        <tr>
            <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                Inicio
            </th>
            <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                Poda/Siembra
            </th>
            <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                Dias
            </th>
            <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                Cosecha
            </th>
            <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                Final
            </th>
            <th class="text-center" style="border-color: white; color: white; background-color: #357ca5">
                Área m<sup>2</sup>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($modulos as $pos_mdl => $modulo)
            <tr>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$modulo->nombre}}
                </td>

                <td class="text-center" style="border-color: #9d9d9d">
                    <span class="hidden">{{$tipo == 1 ? $modulo->cicloActual()->fecha_inicio : date('Y-m-d')}}</span>
                    <input type="date" id="ciclo_fecha_inicio_{{$modulo->id_modulo}}" name="ciclo_fecha_inicio_{{$modulo->id_modulo}}" required
                           style="width: 100%" value="{{$tipo == 1 ? $modulo->cicloActual()->fecha_inicio : date('Y-m-d')}}" class="text-center">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    @if($tipo == 1)
                        {{$modulo->getPodaSiembraActual()}}
                    @endif
                    <select name="ciclo_poda_siembra_{{$modulo->id_modulo}}" id="ciclo_poda_siembra_{{$modulo->id_modulo}}">
                        <option value="P" {{$tipo == 1 && $modulo->cicloActual()->poda_siembra == 'P' ? 'selected' : ''}}>Poda</option>
                        <option value="S" {{($tipo == 1 && $modulo->cicloActual()->poda_siembra == 'S') ? 'selected' : ''}}>
                            Siembra
                        </option>
                    </select>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    @if($tipo == 1)
                        {{difFechas($modulo->cicloActual()->fecha_fin != '' ? $modulo->cicloActual()->fecha_fin : date('Y-m-d'), $modulo->cicloActual()->fecha_inicio)->days}}
                    @else
                        00
                    @endif
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <span class="hidden">{{$tipo == 1 ? $modulo->cicloActual()->fecha_cosecha : ''}}</span>
                    <input type="date" id="ciclo_fecha_cosecha_{{$modulo->id_modulo}}" name="ciclo_fecha_cosecha_{{$modulo->id_modulo}}"
                           style="width: 100%" value="{{$tipo == 1 ? $modulo->cicloActual()->fecha_cosecha : ''}}" class="text-center" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <span class="hidden">{{$tipo == 1 ? $modulo->cicloActual()->fecha_fin : ''}}</span>
                    <input type="date" id="ciclo_fecha_fin_{{$modulo->id_modulo}}" name="ciclo_fecha_fin_{{$modulo->id_modulo}}"
                           style="width: 100%" value="{{$tipo == 1 ? $modulo->cicloActual()->fecha_fin : ''}}" class="text-center" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <span class="hidden">{{$tipo == 1 ? $modulo->cicloActual()->area : $modulo->area}}</span>
                    <input type="number" id="ciclo_area_{{$modulo->id_modulo}}" name="ciclo_area_{{$modulo->id_modulo}}" class="text-center"
                           value="{{$tipo == 1 ? $modulo->cicloActual()->area : $modulo->area}}" style="width: 100%" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d" colspan="6">
                    <div class="btn-group">
                        @if($tipo == 1)
                            <button type="button" class="btn btn-xs btn-warning" title="Terminar Ciclo"
                                    onclick="terminar_ciclo('{{$modulo->id_modulo}}')">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-success" title="Editar Ciclo"
                                    onclick="update_ciclo('{{$modulo->cicloActual()->id_ciclo}}', '{{$modulo->id_modulo}}')">
                                <i class="fa fa-fw fa-pencil"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-xs btn-success" title="Crear Ciclo"
                                    onclick="store_ciclo('{{$modulo->id_modulo}}')">
                                <i class="fa fa-fw fa-save"></i>
                            </button>
                        @endif
                        @if(count($modulo->ciclos) > 0)
                            <button type="button" class="btn btn-xs btn-info" title="Ver Ciclos"
                                    onclick="ver_ciclos('{{$modulo->id_modulo}}')">
                                <i class="fa fa-fw fa-eye"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-info text-center">
        No hay resultados que mostrar
    </div>
@endif