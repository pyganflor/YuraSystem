@foreach($list_esp_emp as $pos_esp_emp => $esp_emp)
    <legend style="font-size: 1em; margin-bottom: 0">
        <strong>
            Distribución EMP-{{$pos_esp_emp + 1}}
            <button type="button" class="btn btn-xs btn-primary"
                    onclick="imprimir_distribucion('{{$det_ped->id_detalle_pedido}}')">
                <i class="fa fa-fw fa-print"></i> Imprimir
            </button>
            {{--<button type="button" class="btn btn-xs btn-danger pull-right"
                    onclick="quitar_distribuciones('{{$det_ped->id_pedido}}','{{csrf_token()}}')">
                <i class="fa fa-fw fa-times"></i> Quitar Distribuciones
            </button>--}}
            Marcación/Coloración
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
                        style="border-color: #9d9d9d; background-color: {{$col->color->fondo}}; color: {{$col->color->texto}}">
                        {{$col->color->nombre}}
                    </th>
                @endforeach
                <th class="text-center" style="border-color: #9d9d9d; width: 65px; background-color: #357ca5; color: white">Ramos</th>
                <th class="text-center" style="border-color: #9d9d9d; width: 65px; background-color: #357ca5; color: white">Piezas</th>
                <th class="text-center" style="border-color: #9d9d9d; width: 85px; background-color: #357ca5; color: white">Nº Caja</th>
                <th class="text-center" style="border-color: #9d9d9d; width: 150px">
                    Marcación
                </th>
            </tr>
            @php
                $anterior = '';
            @endphp
            @foreach($esp_emp['marcaciones'] as $pos_marc => $marc)
                @foreach($marc['distribuciones'] as $pos_distr => $distr)
                    @if(count($distr['coloraciones'][0])>1)
                        @foreach($distr['coloraciones'] as $pos_col => $col)
                            @foreach($col as $z=> $colum)
                                <tr>
                                    @if($pos_distr==0 && $z == 0)
                                        <th class="text-center" style="border-color: #9d9d9d"
                                            rowspan="{{count($marc['distribuciones'])*count($marc['distribuciones'][0]['coloraciones'][0])}}">
                                            {{$marc['marc']->nombre}}
                                        </th>
                                    @endif
                                    @foreach($colum as $data)
                                        <th class="text-center" style="border-color: #9d9d9d;width:100px;background:{{$data->fondo}};color:{{$data->texto}}">
                                            <div class="input-group" style="text-align: center">
                                                <span class="input-group-addon" style="background-color: #e9ecef">{{$data->p}}</span>
                                                {{$data->cantidad}}
                                            </div>
                                        </th>
                                    @endforeach
                                    @if($z==0 )
                                        <th class="text-center" style="border-color: #9d9d9d; width: 85px" rowspan="{{count($esp_emp['marcaciones'])}}" style="width:200px">
                                            {{isset($distr['distr']->ramos) ? $distr['distr']->ramos : ''}}
                                            {{isset($col[0]->variedad) ? $col[0]->variedad : ''}}
                                            {{isset($col[0]->ramo) ? $col[0]->ramo : ''}}
                                        </th>
                                        <th class="text-center" style="border-color: #9d9d9d; width: 65px" rowspan="{{count($esp_emp['marcaciones'])}}">
                                            {{$distr['distr']->piezas}}
                                        </th>
                                        <th class="text-center" style="border-color: #9d9d9d; width: 65px" rowspan="{{count($esp_emp['marcaciones'])}}">
                                            <input type="number" min="1" value="{{$distr['distr']->pos_pieza}}"
                                                   style="border:none;background:transparent;width: 100%;text-align:center" class="distribucion_{{$distr['distr']->id_distribucion}}">
                                            <input type="hidden" class="marcacion_{{$marc['marc']->id_marcacion}}"
                                                   value="{{$distr['distr']->id_distribucion}}">
                                        </th>
                                    @endif
                                    @if($pos_distr==0 && $z == 0)
                                        <th class="text-center" style="border-color: #9d9d9d" rowspan="{{count($marc['distribuciones'])*count($marc['distribuciones'][0]['coloraciones'][0])}}">
                                            {{$marc['marc']->nombre}}
                                        </th>
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                    @else
                        <tr>
                            @if($pos_distr == 0)
                                <th class="text-center" style="border-color: #9d9d9d" rowspan="{{count($marc['distribuciones'])}}">
                                    {{$marc['marc']->nombre}}
                                </th>
                            @endif
                            @foreach($distr['coloraciones'] as $pos_col => $col)
                                @foreach($col as $coloracion)
                                    @foreach($coloracion as $colum)
                                        <th style="background:{{$colum->fondo}};color:{{$colum->texto}};text-align: center;width: 100px;">
                                            <div class="input-group" style="">
                                                <span class="input-group-addon" style="background-color: #e9ecef">
                                                    {{$colum->p}}
                                                </span>
                                                {{$colum->cantidad}}
                                            </div>
                                        </th>
                                    @endforeach
                                @endforeach
                            @endforeach
                            <th class="text-center" style="border-color: #9d9d9d; width: 85px">
                                {{$distr['distr']->ramos}}  {{$coloracion[0]->variedad}} {{$coloracion[0]->ramo}}
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d; width: 65px">
                                {{$distr['distr']->piezas}}
                            </th>
                            <th class="text-center" style="border-color: #9d9d9d; width: 65px">
                                <input type="number" min="1" value="{{$distr['distr']->pos_pieza}}"
                                       style="border:none;background:transparent;width: 100%;text-align:center" class="distribucion_{{$distr['distr']->id_distribucion}}">
                                <input type="hidden" class="marcacion_{{$marc['marc']->id_marcacion}}"
                                       value="{{$distr['distr']->id_distribucion}}">
                            </th>
                            @if($pos_distr == 0)
                                <th class="text-center" style="border-color: #9d9d9d" rowspan="{{count($marc['distribuciones'])}}">
                                    {{$marc['marc']->nombre}}
                                </th>
                            @endif
                        </tr>
                    @endif
                @endforeach
                @php
                    $anterior = $marc['marc']->id_marcacion;
                @endphp
            @endforeach
        </table>
    </div>
@endforeach
<input type="hidden" value="{{$idsMaracaciones}}" id="id_marcaciones">
<input type="hidden" value="{{$idDistribuciones}}" id="id_distribuciones">

<div class="form-row text-center" style="margin-top: 20px">
    <buttom type="buttom" class="btn btn-primary" onclick="actualizar_distribucion()">
        <i class="fa fa-floppy-o"></i> Actualizar distribución
    </buttom>
</div>
