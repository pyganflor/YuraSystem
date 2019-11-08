<div style="overflow-x: scroll">
    <table class="table-bordered table-striped table-hover" width="100%" style="border: 3px solid #9d9d9d; font-size: 1em">
        <thead>
        <tr style="background-color: #e9ecef">
            <th class="text-center" style="border-color: #9d9d9d; width: 250px">
                <b style="padding: 20px">Área</b>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; width: 250px">
                <b style="padding: 10px">Días</b>
            </th>
            @foreach($list_tallos as $item)
                <th class="text-center" style="border-color: #9d9d9d; width: 250px">
                    <b style="padding: 10px">{{$item->semana}}</b>
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        {{-- CONTENIDO --}}
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <b style="padding: 10px">Cosecha</b>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                7
            </th>
            @foreach($list_tallos as $item)
                <td class="text-center celda_hovered" id="celda_cosecha_7_{{$item->semana}}" style="border-color: #9d9d9d"
                    onmouseover="mouse_over_celda('celda_cosecha_7_{{$item->semana}}', 1)" onmouseleave="mouse_over_celda('', 0)">
                    -
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <b style="padding: 5px"><small title="Rendimiento">{{$rend_cosecha}} </small></b>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                6
            </th>
            @foreach($list_tallos as $item)
                <td class="text-center celda_hovered" id="celda_cosecha_6_{{$item->semana}}" style="border-color: #9d9d9d"
                    onmouseover="mouse_over_celda('celda_cosecha_6_{{$item->semana}}', 1)" onmouseleave="mouse_over_celda('', 0)">
                    -
                </td>
            @endforeach
        </tr>
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="number" id="horas_diarias_cosecha" min="1" max="24" style="width: 100%; height: 22px" class="text-center" readonly
                       title="Horas diarias" ondblclick="$(this).prop('readonly', false)" onchange="update_horas_diarias_cosecha()"
                       value="{{$hr_diarias_cosecha}}">
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                5
            </th>
            @foreach($list_tallos as $item)
                <td class="text-center celda_hovered" id="celda_cosecha_5_{{$item->semana}}" style="border-color: #9d9d9d"
                    onmouseover="mouse_over_celda('celda_cosecha_5_{{$item->semana}}', 1)" onmouseleave="mouse_over_celda('', 0)">
                    -
                </td>
            @endforeach
        </tr>
        {{-- TOTALES --}}
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" colspan="2">
                <b style="padding: 5px">Tallos</b>
            </th>
            @foreach($list_tallos as $item)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    {{number_format($item->cant, 2)}}
                </th>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>
<script>
    function update_horas_diarias_cosecha() {
        datos = {
            _token: '{{csrf_token()}}',
            valor: $('#horas_diarias_cosecha').val() >= 0 ? $('#horas_diarias_cosecha').val() : 8
        };
        $.post('{{url('proy_mano_obra/update_horas_diarias_cosecha')}}', datos, function () {
            $('#horas_diarias_cosecha').prop('readonly', true);
        }, 'json');
    }
</script>