<table class="table-striped table-bordered table-hover" width="100%" style="border: 2px solid #9d9d9d"
       id="db_tbl_indicadores">
    <thead>
    <tr style="background-color: #e9ecef">
        <th class="text-center" style="border-color: #9d9d9d" width="10%">
            Nombre
        </th>
        <th class="text-center" style="border-color: #9d9d9d">
            Descripción
        </th>
        <th class="text-center" style="border-color: #9d9d9d" width="10%">
            Valor
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($indicadores as $indicador)
        <tr>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$indicador->nombre}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$indicador->descripcion}}
            </td>
            <td class="text-center" style="border-color: #9d9d9d">
                {{$indicador->valor}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
