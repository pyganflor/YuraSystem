<div style="overflow-x: scroll">
    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d;" id="table_ver_cosechas">
        <thead>
        <tr style="background-color: #357ca5; color: white">
            <th class="text-center" style="border-color: white;">
                Fecha Cosecha
            </th>
            <th class="text-center" style="border-color: white;">
                Tallos Cosechados módulo: {{$modulo->nombre}}
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($cosechas as $cosecha)
            <tr>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{$cosecha->fecha_ingreso}}
                </td>
                <td class="text-center" style="border-color: #9d9d9d">
                    {{number_format($cosecha->getTotalTallosByModulo($modulo->id_modulo))}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    estructura_tabla('table_ver_cosechas', false, false);
</script>