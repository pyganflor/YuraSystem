<div style="overflow-x: scroll">
    <table class="table-striped table-bordered table-responsive" width="100%" style="border: 2px solid #9d9d9d">
        <tr>
            @foreach($fechas as $f)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{$f}}
                </th>
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Promedio
            </th>
        </tr>
        <tr>
            @php
                $total = 0;
                $count = 0;
            @endphp
            @foreach($arreglo_dias as $pos => $item)
                <th class="text-center" style="border-color: #9d9d9d">
                    {{number_format($item, 2)}}
                </th>
                @php
                    $total += $item;

                     if ($option == 'cosecha')
                            $object = \yura\Modelos\Cosecha::All()->where('estado', 1)->where('fecha_ingreso', $fechas[$pos])->first();
                        if ($option == 'verde')
                            $object = \yura\Modelos\ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $fechas[$pos])->first();
                        if ($option == 'blanco')
                            $object = \yura\Modelos\ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $fechas[$pos])->first();

                    if($object != '')
                        $count ++;
                @endphp
            @endforeach
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                {{number_format(round($total / $count, 2), 2)}}*
            </th>
        </tr>
    </table>
</div>