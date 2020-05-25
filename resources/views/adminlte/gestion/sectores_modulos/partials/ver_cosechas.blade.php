<div style="overflow-x: scroll">
    <table class="table-striped table-bordered" width="100%" style="border: 1px solid #9d9d9d; border-radius: 18px 18px 0 0" id="table_ver_cosechas">
        <thead>
        <tr style="background-color: #00b388; color: white">
            <th class="text-center th_yura_default" style="border-radius: 18px 0 0 0">
                Fecha Cosecha
            </th>
            <th class="text-center th_yura_default" style="border-radius: 0 18px 0 0">
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
    $('#table_ver_cosechas_filter label').addClass('text-color_yura');
    $('#table_ver_cosechas_filter label input').addClass('input-yura_white');
</script>