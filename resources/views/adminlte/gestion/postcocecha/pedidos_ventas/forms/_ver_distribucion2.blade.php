@php
    ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
@endphp
@foreach($list_esp_emp as $pos_esp_emp => $esp_emp)
    <legend style="font-size: 1em; margin-bottom: 0">
        <strong>
            Distribución EMP-{{$pos_esp_emp + 1}}
            <button type="button" class="btn btn-xs btn-primary"
                    onclick="imprimir_distribucion('{{$det_ped->id_detalle_pedido}}')">
                <i class="fa fa-fw fa-print"></i> Imprimir
            </button>
            <button type="button" class="btn btn-xs btn-danger pull-right"
                    onclick="quitar_distribuciones('{{$det_ped->id_pedido}}','{{csrf_token()}}')">
                <i class="fa fa-fw fa-times"></i> Quitar Distribuciones
            </button>
        </strong>
    </legend>
    <div style="overflow-x: scroll">
        <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d">
            <tr>
                <th class="text-center" style="border-color: #9d9d9d; width: 150px">
                    Marcación/Coloración
                </th>
                @foreach($esp_emp['coloraciones'] as $pos_col => $col)
                    <th class="text-center"
                        style="border-color: #9d9d9d; width: 100px; background-color: {{$col->color->fondo}}; color: {{$col->color->texto}}">
                        {{$col->color->nombre}}
                    </th>
                @endforeach
                <th class="text-center" style="border-color: #9d9d9d; width: 65px; background-color: #357ca5; color: white">Ramos</th>
                <th class="text-center" style="border-color: #9d9d9d; width: 65px; background-color: #357ca5; color: white">Piezas</th>
                <th class="text-center" style="border-color: #9d9d9d; width: 65px; background-color: #357ca5; color: white">Nº Caja</th>
                <th class="text-center" style="border-color: #9d9d9d; width: 150px">
                    Marcación
                </th>
            </tr>
            @php
                $anterior = '';
            @endphp
            @foreach($esp_emp['marcaciones'] as $pos_marc => $marc)
                @foreach($marc['distribuciones'] as $pos_distr => $distr)
                    <tr style="border-top: {{$anterior != $marc['marc']->id_marcacion ? '2px solid black' : ''}}">
                        @if($pos_distr == 0)
                            <th class="text-center" style="border-color: #9d9d9d" rowspan="{{count($marc['distribuciones'])}}">
                                {{$marc['marc']->nombre}}
                            </th>
                        @endif
                        @foreach($distr['coloraciones'] as $pos_col => $col)
                            <th class="text-center"
                                style="border-color: #9d9d9d; width: 100px; background-color: {{$col['col']->color->fondo}}; color: {{$col['col']->color->texto}}">
                                <ul class="list-unstyled">
                                    @foreach($col['detalles'] as $pos_det_esp => $det_esp)
                                        @php
                                            $distr_col = $det_esp['distr_col'];
                                        @endphp
                                        <li>
                                            <div class="input-group" style="width: 100px">
                                            <span class="input-group-addon" style="background-color: #e9ecef">
                                                P-{{$pos_det_esp + 1}}
                                            </span>
                                                <input type="text" readonly class="text-center"
                                                       value="{{$distr_col != '' ? $distr_col->cantidad : ''}}"
                                                       style="background-color: {{$col['col']->color->fondo}}; color: {{$col['col']->color->texto}}; width: 100%">
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </th>
                        @endforeach
                        <th class="text-center" style="border-color: #9d9d9d; width: 65px">
                            {{$distr['distr']->ramos}}
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; width: 65px">
                            {{$distr['distr']->piezas}}
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; width: 65px">
                            {{$distr['distr']->pos_pieza}}
                        </th>
                        @if($pos_distr == 0)
                            <th class="text-center" style="border-color: #9d9d9d" rowspan="{{count($marc['distribuciones'])}}">
                                {{$marc['marc']->nombre}}
                            </th>
                        @endif
                    </tr>
                    @php
                        $anterior = $marc['marc']->id_marcacion;
                    @endphp
                @endforeach
            @endforeach
        </table>
    </div>
@endforeach
