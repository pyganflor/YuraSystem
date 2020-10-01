<div style="overflow-x: scroll">
    <table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d; border-radius: 18px 18px 0 0"
           id="table_ciclos">
        <thead>
        <tr>
            <th class="text-center th_yura_green" style="border-color: white; border-radius: 18px 0 0 0">
                Cama
            </th>
            <th class="text-center th_yura_green" style="border-color: white;">
                Inicio
            </th>
            <th class="text-center th_yura_green" style="border-color: white;">
                No. Semana
            </th>
            <th class="text-center th_yura_green" style="border-color: white;">
                Días
            </th>
            <th class="text-center th_yura_green" style="border-color: white;">
                Esquejes cosechados
            </th>
            <th class="text-center th_yura_green" style="border-color: white;">
                Esquejes x planta
            </th>
            <th class="text-center th_yura_green" style="border-color: white;">
                Plantas iniciales
            </th>
            <th class="text-center th_yura_green" style="border-color: white;">
                Plantas no productivas
            </th>
            <th class="text-center th_yura_green" style="border-color: white;">
                Fecha fin
            </th>
            <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 18px 0 0">
                Opciones
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($ciclos as $c)
            <tr>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$c->cama->nombre}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="date" id="fecha_inicio_{{$c->id_ciclo_cama}}" value="{{$c->fecha_inicio}}" style="width: 100%"
                           class="text-center">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{round($c->getDiasVida() / 7) + 1}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$c->getDiasVida()}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$c->getEsquejesCosechados()}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" id="esq_x_planta_{{$c->id_ciclo_cama}}" value="{{$c->esq_x_planta}}" style="width: 100%"
                           class="text-center">
                </td>
                <td class="text-center mouse-hand" style="border-color: #9d9d9d" onclick="edit_ciclo_contenedores('{{$c->id_ciclo_cama}}')"
                    onmouseover="$('#icon_platnas_iniciales_{{$c->id_ciclo_cama}}').removeClass('hidden')"
                    onmouseleave="$('#icon_platnas_iniciales_{{$c->id_ciclo_cama}}').addClass('hidden')">
                    {{$c->getPlantasProductivas()}}
                    <i class="fa fa-fw fa-pencil pull-right hidden" id="icon_platnas_iniciales_{{$c->id_ciclo_cama}}"></i>
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="number" id="plantas_muertas_{{$c->id_ciclo_cama}}" value="{{$c->plantas_muertas}}" style="width: 100%"
                           class="text-center">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <input type="date" id="fecha_fin_{{$c->id_ciclo_cama}}" value="{{$c->fecha_fin}}" style="width: 100%" class="text-center">
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-yura_danger" onclick="terminar_ciclo('{{$c->id_ciclo_cama}}')">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-yura_default" onclick="update_ciclo('{{$c->id_ciclo_cama}}')">
                            <i class="fa fa-fw fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-xs btn-yura_danger" onclick="eliminar_ciclo('{{$c->id_ciclo_cama}}')">
                            <i class="fa fa-fw fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <input type="hidden" id="id_ciclo_{{$c->id_ciclo_cama}}" value="{{$c->id_ciclo_cama}}">
        @endforeach
        </tbody>
    </table>
</div>