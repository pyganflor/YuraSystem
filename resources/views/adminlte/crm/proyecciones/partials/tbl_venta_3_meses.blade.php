<table class="table table-bordered">
    <tbody >
        <tr>
            <td class="bg-gray-light text-center" style="border:1px solid">Mes</td>
            @foreach($data as $mes=> $valor)
                <td class="bg-gray-light text-center" style="border-bottom:1px solid" >
                    <span data-toggle="tooltip" title="Mes" style="border:1px solid">{{$mes}}</span></span>
                </td>
            @endforeach
            <td class="bg-gray-light text-center" style="border:1px solid">Mes</td>
        </tr>
        <tr>
            <td class="bg-gray-light text-center" style="border:1px solid">Valor</td>
            @foreach($data as $mes=> $valor)
                <td class="text-center" style="border-bottom:1px solid" >
                    <span data-toggle="tooltip" title="Cajas">{{$valor}}</span></span>
                </td>
            @endforeach
            <td class="bg-gray-light text-center" style="border:1px solid">Valor</td>
        </tr>
    </tbody>
</table>


