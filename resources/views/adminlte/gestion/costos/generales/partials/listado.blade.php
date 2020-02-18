<table class="table-striped table-bordered" style="width: 100%; border: 2px solid #9d9d9d">
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">

        </th>
        @foreach($semanas as $sem)
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                {{$sem->codigo_semana}}
            </th>
        @endforeach
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            Total
        </th>
    </tr>
    {{-- CAMPO --}}
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black; font-weight: bold" class="btn btn-xs btn-link"
                  onclick="$('.tr_campo').toggleClass('hide')">
                CAMPO
            </span>
        </th>
        @php
            $total_campo = 0;
        @endphp
        @foreach($semanas as $item)
            <th class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->campo, 2)}}
                </div>
            </th>
            @php
                $total_campo += $item->campo;
            @endphp
        @endforeach
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <div style="width: 110px">
                ${{number_format($total_campo, 2)}}
            </div>
        </th>
    </tr>
    <tr class="tr_campo hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                MP
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->campo_mp, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    <tr class="tr_campo hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                MO
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->campo_mo, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    <tr class="tr_campo hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                GIP
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->campo_gip, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    <tr class="tr_campo hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                GA
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->campo_ga, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    {{-- PROPAGACIÓN --}}
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black; font-weight: bold" class="btn btn-xs btn-link"
                  onclick="$('.tr_propagacion').toggleClass('hide')">
                PROPAGACIÓN
            </span>
        </th>
        @php
            $total_propagacion = 0;
        @endphp
        @foreach($semanas as $item)
            <th class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->propagacion, 2)}}
                </div>
            </th>
            @php
                $total_propagacion += $item->propagacion;
            @endphp
        @endforeach
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <div style="width: 110px">
                ${{number_format($total_propagacion, 2)}}
            </div>
        </th>
    </tr>
    <tr class="tr_propagacion hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                MP
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->propagacion_mp, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    <tr class="tr_propagacion hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                MO
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->propagacion_mo, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    <tr class="tr_propagacion hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                GIP
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->propagacion_gip, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    <tr class="tr_propagacion hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                GA
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->propagacion_ga, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    {{-- COSECHA --}}
    <tr>
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black; font-weight: bold" class="btn btn-xs btn-link"
                  onclick="$('.tr_cosecha').toggleClass('hide')">
                COSECHA
            </span>
        </th>
        @php
            $total_cosecha = 0;
        @endphp
        @foreach($semanas as $item)
            <th class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->cosecha, 2)}}
                </div>
            </th>
            @php
                $total_cosecha += $item->cosecha;
            @endphp
        @endforeach
        <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <div style="width: 110px">
                ${{number_format($total_cosecha, 2)}}
            </div>
        </th>
    </tr>
    <tr class="tr_cosecha hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                MP
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->cosecha_mp, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    <tr class="tr_cosecha hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                MO
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->cosecha_mo, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    <tr class="tr_cosecha hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                GIP
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->cosecha_gip, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
    <tr class="tr_cosecha hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                GA
            </span>
        </td>
        @foreach($semanas as $item)
            <td class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->cosecha_ga, 2)}}
                </div>
            </td>
        @endforeach
    </tr>
</table>