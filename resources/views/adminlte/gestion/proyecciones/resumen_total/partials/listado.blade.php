<table class="table-bordered table-striped table-hover" width="100%" style="border: 2px solid #9d9d9d; font-size: 1em;">
    <thead>
    <tr style="background-color: #e9ecef">
        <th class="text-center" style="border-color: #9d9d9d; width: 250px">
            <b >Semanas</b>
        </th>
        {{--data semana--}}
        @foreach($semanas as $semana)
        <th class="text-center" style="border-color: #9d9d9d; width: 250px">
            {{$semana}}
        </th>
        @endforeach
        {{--data semana--}}
        <th class="text-center" style="border-color: #9d9d9d; width: 250px">
            <b >Semanas</b>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>

        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            <b>Proyección cosecha</b>
        </th>
        {{--data cosechado--}}
        <td class="text-center celda_hovered" id="">

        </td>
        {{--data cosechado--}}
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            <b>Proyección cosecha</b>
        </th>
    <tr>

        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            <b>Proyección venta</b>
        </th>
        {{--data vendido--}}
        <td class="text-center celda_hovered" id="" >

        </td>
        {{--data vendido--}}
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
            <b>Proyección venta</b>
        </th>
    </tr>

    </tbody>
</table>
