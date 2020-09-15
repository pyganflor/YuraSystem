@if(count($matriz) > 0)
    <table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d; font-size: 0.9em" id="table_costos">
        <thead>
        <tr id="tr_fijo_top_0">
            <th class="text-left" style="border-color: #9d9d9d; background-color: #e9ecef" colspan="{{count($semanas)*5 + 4}}">
                <span style="margin-left: 5px; position: sticky; left: 7px !important;">Costos {{$actividad != '' ? '"'.$actividad->nombre.'"' : ''}}</span>
            </th>
        </tr>
        <tr id="tr_fijo_top_1">
            <th class="text-left th_fijo_left_0" style="border-color: #9d9d9d; background-color: #e9ecef; z-index: 5 !important; width: 125px">
                <span style="margin-left: 5px; margin-right: 5px">SEMANAS</span>
            </th>
            @foreach($semanas as $sem)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <span style="margin-left: 5px; margin-right: 5px">{{$sem->codigo_semana}}</span>
                </th>
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <span style="margin-left: 5px; margin-right: 5px">Total</span>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <span style="margin-left: 5px; margin-right: 5px">%</span>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            </th>
        </tr>
        <tr id="tr_fijo_top_2" onclick="recalcular_acumulados()">
            <th class="text-left th_fijo_left_0" style="border-color: #9d9d9d; background-color: #e9ecef">
                <span style="margin-left: 5px; margin-right: 5px">Totales</span>
            </th>
            @php
                $total = 0;
            @endphp
            @foreach($totales as $item)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <span style="margin-left: 5px; margin-right: 5px">{{number_format($item->cant, 2)}}</span>
                </th>
                @php
                    $total += round($item->cant, 2);
                @endphp
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <span style="margin-left: 5px; margin-right: 5px">{{number_format($total, 2)}}</span>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <span style="margin-left: 5px; margin-right: 5px">100%</span>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <span style="margin-left: 5px; margin-right: 5px">Acum.</span>
            </th>
        </tr>
        </thead>
        <tfoot>
        </tfoot>
        <tbody>
        @php
            $acumulado = 0;
        @endphp
        @foreach($matriz as $pos_act => $act)
            <tr onmouseover="$(this).css('background-color', '#77dbf9')" onmouseleave="$(this).css('background-color', '')">
                @php
                    $total_prod = 0;
                @endphp
                @foreach($act as $pos_item => $item)
                    @if($pos_item == 0)
                        <td class="text-left th_fijo_left_0" style="border-color: #9d9d9d; background-color: #e9ecef">
                            <div style="width: 200px; margin-left: 5px">
                                {{$item->actividad_producto->producto->nombre}}
                            </div>
                        </td>
                    @endif
                    <td class="text-center" style="border-color: #9d9d9d">
                        <span>{{number_format($item->valor, 2)}}</span>
                    </td>
                    @php
                        $total_prod += round($item->valor, 2);
                    @endphp
                @endforeach
                <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <span>{{number_format($total_prod, 2)}}</span>
                </td>
                <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <span>{{round(($total_prod / $total) * 100, 2)}}</span>
                    <input type="hidden" value="{{round(($total_prod / $total) * 100, 2)}}" class="porcentaje_parcial">
                </td>
                @php
                    $acumulado += ($total_prod / $total) * 100;
                @endphp
                <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <span class="acumulado_parcial">{{round($acumulado, 2)}}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <script>
        estructura_tabla('table_costos', false, false);

        $('#table_costos_wrapper .row:first').hide();

        function recalcular_acumulados() {
            list_porc_parcial = $('.porcentaje_parcial');
            list_acum_parcial = $('.acumulado_parcial');
            acum = 0;
            for (i = 0; i < list_porc_parcial.length; i++) {
                porc_parcial = list_porc_parcial[i];
                acum += parseFloat(porc_parcial.value);
                list_acum_parcial[i].innerHTML = acum > 100 ? 100 : Math.round(acum * 100) / 100;
            }
        }
    </script>

    <style>
        .th_fijo_left_0 {
            position: sticky;
            left: 0;
            z-index: 1;
        }

        #tr_fijo_top_0 .th_fijo_left_0 {
            position: sticky;
            top: 20px;
        }

        #tr_fijo_top_0 th {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        #tr_fijo_top_1 th {
            position: sticky;
            top: 20px;
        }

        #tr_fijo_top_2 th {
            position: sticky;
            top: 40px;
        }
    </style>
@else
    <div class="well text-center">No hay registros que mostrar</div>
@endif