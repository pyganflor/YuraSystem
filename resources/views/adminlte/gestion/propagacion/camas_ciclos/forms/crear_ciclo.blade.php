<div class="row">
    <div class="col-md-4">
        <table class="table-bordered" style="width: 100%; border-radius: 18px 0 0 18px">
            <tr>
                <th class="text-center th_yura_green" style="border-color: white; border-radius: 18px 0 0 0">
                    Cama
                </th>
                <td class="text-center" style="border-color: #9d9d9d;">
                    {{$cama->nombre}}
                </td>
            </tr>
            <tr>
                <th class="text-center th_yura_green" style="border-color: white;">
                    Variedad
                </th>
                <td class="text-center" style="border-color: #9d9d9d;">
                    {{$variedad->nombre}}
                </td>
            </tr>
            <tr>
                <th class="text-center th_yura_green" style="border-color: white;">
                    Fecha inicio
                </th>
                <td class="text-center" style="border-color: #9d9d9d;">
                    {{$fecha_inicio}}
                </td>
            </tr>
            <tr>
                <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 0 0 18px">
                    Esquejes x planta
                </th>
                <td class="text-center" style="border-color: #9d9d9d;">
                    {{$esq_planta}}
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-8 text-center">
        <table class="table-bordered" style="width: 100%; border-radius: 18px 18px 0 0">
            <tr>
                <th class="text-center th_yura_green" style="border-color: white; border-radius: 18px 0 0 0">
                    Nombre
                </th>
                <th class="text-center th_yura_green" style="border-color: white; width: 100px;">
                    Cantidad x Unidad
                </th>
                <th class="text-center th_yura_green" style="border-color: white; width: 100px;">
                    Cantidad contenedores
                </th>
                <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 18px 0 0">
                    Total
                </th>
            </tr>
            @foreach($contenedores as $c)
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->nombre}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->cantidad}}
                        <input type="hidden" id="cantidad_x_unidad_{{$c->id_contenedor_propag}}" value="{{$c->cantidad}}">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        <input type="number" id="cantidad_{{$c->id_contenedor_propag}}" style="width: 100%" class="text-center"
                               onchange="calcular_totales_ciclo()" min="0">
                        <input type="hidden" id="id_contenedor_{{$c->id_contenedor_propag}}" class="ids_contenedor"
                               value="{{$c->id_contenedor_propag}}">
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d" id="td_total_{{$c->id_contenedor_propag}}">
                    </td>
                </tr>
            @endforeach
            <tr>
                <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 0 0 18px" colspan="2">
                    Total
                </th>
                <th class="text-center th_yura_green" style="border-color: white;" id="th_total_contenedores">
                </th>
                <th class="text-center th_yura_green" style="border-color: white; border-radius: 0 0 18px 0" id="th_total_plantas">
                </th>
            </tr>
        </table>
        <button type="button" class="btn btn-yura_primary text-white" style="margin-top: 5px" onclick="store_ciclo()">
            <i class="fa fa-fw fa-save"></i> Crear ciclo
        </button>
    </div>
    <input type="hidden" id="id_cama" value="{{$cama->id_cama}}">
    <input type="hidden" id="id_variedad" value="{{$variedad->id_variedad}}">
    <input type="hidden" id="fecha_inicio" value="{{$fecha_inicio}}">
    <input type="hidden" id="fecha_fin" value="{{$fecha_fin}}">
    <input type="hidden" id="esq_planta" value="{{$esq_planta}}">
</div>