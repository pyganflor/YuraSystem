@if($tallos > 0)
    <table style="width: 100%; font-size: 1.2em" class="table-bordered" id="table_rendimiento_mesa">
        @for($i = 1; $i<=18; $i++)
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; width: 33%">
                    <div class="bg-teal disabled color-palette" style="height: 28px">
                        <span style="margin-left: 10px">{{$i}}</span>
                        <span class="badge">{{getRendimientoVerdeByFechaMesa($fecha_verde, $i)}}</span>
                    </div>
                </th>
                <th class="text-center" style="border-color: #9d9d9d; width: 33%">
                    <div class="bg-navy disabled color-palette" style="height: 28px">
                        <span style="margin-left: 10px">{{$i+18}}</span>
                        <span class="badge">{{getRendimientoVerdeByFechaMesa($fecha_verde, $i+18)}}</span>
                    </div>
                </th>
                <th class="text-center" style="border-color: #9d9d9d">
                    <div class="bg-orange disabled color-palette" style="height: 28px">
                        <span style="margin-left: 10px">{{$i+36}}</span>
                        <span class="badge">{{getRendimientoVerdeByFechaMesa($fecha_verde, $i+36)}}</span>
                    </div>
                </th>
            </tr>
        @endfor
    </table>
@else
    <div class="alert alert-info text-center">
        No se han encontrado clasificaciones para la fecha indicada
    </div>
@endif