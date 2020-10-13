@if(count($ciclos) > 0)
    <div id="div_content_fixed">
        <table class="table-striped table-bordered" width="100%" style="border: 1px solid #9d9d9d; border-radius: 18px 18px 0 0;"
               id="table_fenograma_ejecucion">
            <thead>
            <tr style="color: white">
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px; border-radius: 18px 0 0 0">
                    Cama
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px" width="95px">
                    Inicio
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; width: 30px; padding-left: 5px">
                    Semana siembra
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; width: 30px; padding-left: 5px">
                    Semana actual
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    Esq. Cosechados
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; padding-left: 5px">
                    Cosechado <sup>%</sup>
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    Ptas Iniciales
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px">
                    Ptas Muertas
                </th>
                <th class="fila_fija1" style="border-color: #9d9d9d; background-color: #00B388; padding-left: 5px; border-radius: 0 18px 0 0">
                    Conteo T/P
                </th>
            </tr>
            </thead>
            <tbody>
            @php
                $cosechados = 0;
                $ptas_iniciales = 0;
            @endphp
            @foreach($ciclos as $c)
                @php
                    $cosechados += $c->getEsquejesCosechados();
                    $ptas_iniciales += $c->getPlantasProductivas();
                @endphp
                <tr>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->cama->nombre}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->fecha_inicio}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->semana_ini()->codigo}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->semana_vida()}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->getEsquejesCosechados()}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->getPorcentajeCosechado()}}%
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->getPlantasProductivas()}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->plantas_muertas}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{$c->esq_x_planta}}
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tr>
                <th class="text-left th_yura_green" style="border-color: white" colspan="4">
                    Totales
                </th>
                <th class="text-center th_yura_green" style="border-color: white">
                    {{$cosechados}}
                </th>
                <th class="text-center th_yura_green" style="border-color: white">
                </th>
                <th class="text-center th_yura_green" style="border-color: white">
                    {{$ptas_iniciales}}
                </th>
                <th class="text-center th_yura_green" style="border-color: white">
                </th>
                <th class="text-center th_yura_green" style="border-color: white">
                </th>
            </tr>
        </table>
    </div>

    <script>
        estructura_tabla('table_fenograma_ejecucion', false, false);
        $('#table_fenograma_ejecucion_filter label').addClass('text-color_yura');
        $('#table_fenograma_ejecucion_filter label input').addClass('input-yura_default');
    </script>

    <style>
        #div_content_fixed {
            overflow-x: scroll;
            overflow-y: scroll;
            width: 100%;
            max-height: 450px;
        }

        #table_fenograma_ejecucion {
            border-spacing: 0 !important;
            border: 1px solid #9d9d9d !important;
        }

        #table_fenograma_ejecucion th, #table_fenograma_ejecucion td {
            border-spacing: 0;
        }

        #table_fenograma_ejecucion thead .fila_fija1 {
            background-color: #00B388 !important;
            border: 1px solid #9d9d9d !important;
            z-index: 9;
            position: sticky;
            top: 0;
        }

        #table_fenograma_ejecucion thead .fila_fija2 {
            background-color: #0b3248 !important;
            border: 1px solid #9d9d9d !important;
            z-index: 9;
            position: sticky;
            top: 0;
        }
    </style>
@endif