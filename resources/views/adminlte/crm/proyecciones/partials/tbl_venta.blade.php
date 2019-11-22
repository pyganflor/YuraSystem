<table class="table table-bordered">
    <tbody >
    <tr>
        <th style="width: 10px;border:1px solid" class="bg-gray-light">Variedad/Semana</th>
        @foreach($data[0]['data'] as $semana => $d)
            <th class="text-center bg-gray-light" scope="col" colspan="2" style="border:1px solid">{{$semana}}</th>
        @endforeach
        <th style="width: 10px;border:1px solid"  class="bg-gray-light">Variedad/Semana</th>
    </tr>
    @foreach($data as $d)
        <tr>
            <td class="bg-gray-light text-center" style="border:1px solid">{{$d['variedad']}}</td>
            @foreach($d['data'] as $proy)
                <td class="text-center" style="border-bottom:1px solid" >
                    <span data-toggle="tooltip" title="Cajas">{{number_format($proy['cajas'],2,".","")}}</span>
                </td>
                <td class="text-center" style="border-right:1px solid;border-bottom:1px solid">
                    <span data-toggle="tooltip" title="Dinero" >${{number_format($proy['dinero'],2,".","")}}</span>
                </td>
            @endforeach
            <td class="bg-gray-light text-center" style="border:1px solid">{{$d['variedad']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
