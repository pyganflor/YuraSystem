<table class="table-bordered" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">{{$cliente->detalle()->nombre}}</th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">{{$mes}}</th>
        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">{{$anno}}</th>
    </tr>
</table>
<table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Fecha
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Empaque
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Flor
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Presentación
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Piezas
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Cajas Full
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Ramos
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Ramos x Caja
        </th>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Dinero
        </th>
    </tr>
    @foreach($pedidos as $p)
        <tr>
            <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                {{$p->fecha_pedido}}
            </td>
            <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Empaque
            </td>
            <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Flor
            </td>
            <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Presentación
            </td>
            <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Piezas
            </td>
            <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Cajas Full
            </td>
            <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Ramos
            </td>
            <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Ramos x Caja
            </td>
            <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Dinero
            </td>
        </tr>
    @endforeach
</table>