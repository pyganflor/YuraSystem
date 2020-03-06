<div style="overflow-x: scroll; overflow-y: scroll">
    <table class="table-striped table-bordered" style="width: 100%; border: 2px solid #9d9d9d">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                <div style="width: 70px">
                    Módulo
                </div>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                <div style="width: 70px">
                    Semana Inicio
                </div>
            </th>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">
                <div style="width: 70px">
                    Días Fen.
                </div>
            </th>
            @for($i = 1; $i <= $num_semanas; $i++)
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    <div style="width: 50px">
                        {{$i}}º
                    </div>
                </th>
            @endfor
        </tr>
        @foreach($ciclos as $pos => $item)
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Módulo
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Semana Inicio
                </th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                    Días Fen.
                </th>
                @for($i = 1; $i <= $num_semanas; $i++)
                    <th class="text-center" style="border-color: #9d9d9d">
                        <div style="width: 50px">
                            {{$i}}º
                        </div>
                    </th>
                @endfor
            </tr>
        @endforeach
    </table>
</div>