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
            <th class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->campo_mp, 2)}}
                </div>
            </th>
        @endforeach
    </tr>
    <tr class="tr_campo hide">
        <td class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
            <span style="margin: auto 5px; color: black">
                MO
            </span>
        </td>
        @foreach($semanas as $item)
            <th class="text-center" style="border-color: #9d9d9d">
                <div style="width: 100px">
                    ${{number_format($item->campo_mo, 2)}}
                </div>
            </th>
        @endforeach
    </tr>
</table>