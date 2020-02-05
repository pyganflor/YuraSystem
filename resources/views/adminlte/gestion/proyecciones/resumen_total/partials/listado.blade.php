@dd($dataVentas)
<table class="table-bordered table-striped table-hover" width="100%" style="border: 2px solid #9d9d9d; font-size: 1em;">
    <thead>
    <tr style="background-color: #e9ecef">
        <th class="text-center" style="width: 250px;border:1px solid;">
            <b >Semanas</b>
        </th>
        {{--data semana--}}
        @foreach($semanas as $semana)
        <th class="text-center" style="width: 250px;border:1px solid">
            {{$semana}}
        </th>
        @endforeach
        {{--data semana--}}
        <th class="text-center" style="width: 250px;border:1px solid;">
            <b >Semanas</b>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th class="text-center" style="border:1px solid;background-color: #e9ecef">
            <b>Cajas proyectadas (Cosecha)</b>
        </th>
        {{--data cosechado--}}
        @foreach($dataCosecha as $dC)
        <td class="text-center celda_hovered" style="border:1px solid;padding: 2px 4px" >
            {{number_format($dC->cajas_proyectadas,2,".",",")}}
        </td>
        @endforeach
        {{--data cosechado--}}
        <th class="text-center" style="border:1px solid; background-color: #e9ecef">
            <b>Cajas proyectadas (Cosecha</b>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="border:1px solid;background-color: #e9ecef">
            <b>Tallos proyectados (Cosecha)</b>
        </th>
        {{--data cosechado--}}
        @foreach($dataCosecha as $dC)
            <td class="text-center " style="border:1px solid;padding: 2px 4px" >
               {{number_format($dC['tallos_proyectados'],2,".",",")}}
            </td>
        @endforeach
        <th class="text-center" style="border:1px solid; background-color: #e9ecef">
            <b>Tallos proyectados (Cosecha)</b>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="border:1px solid; background-color: #e9ecef">
            <b>Dinero proyectado (Venta)</b>
        </th>
        {{--data vendido--}}
        @foreach($dataVentas as $dV)
            <td class="text-center" style="border:1px solid;padding: 2px 4px"  >
                ${{number_format($dV['valor'],2,".",",")}}
            </td>
        @endforeach
        {{--data vendido--}}
        <th class="text-center" style="border:1px solid; background-color: #e9ecef">
            <b>Dinero proyectado (Venta)</b>
        </th>
    </tr>
    <tr>
        <th class="text-center" style="border:1px solid; background-color: #e9ecef">
            <b>Cajas proyectadas (Venta)</b>
        </th>
        {{--data vendido--}}
        @foreach($dataVentas as $dV)
            <td class="text-center" style="border:1px solid;padding: 2px 4px" >
               {{number_format($dV['cajasEquivalentes'],2,".",",")}}
            </td>
        @endforeach
        {{--data vendido--}}
        <th class="text-center" style="border:1px solid; background-color: #e9ecef">
            <b>Cajas proyectadas (Venta)</b>
        </th>
    </tr>
    </tbody>
</table>
