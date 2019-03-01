<div class="box box-info">
    <div class="box-header with-border">
        <h4>
            Disponibilidad
            <strong class="pull-right" style="font-size: 0.8em">
                <input type="number" onkeypress="return isNumber(event)" id="antes" name="antes" value="{{$antes}}" min="0" max="7"
                       onchange="buscar_saldos('{{$fecha}}',$('#antes').val(),$('#despues').val())" style="width: 50px;">
                días antes y
                <input type="number" onkeypress="return isNumber(event)" id="despues" name="despues" value="{{$despues}}"
                       onchange="buscar_saldos('{{$fecha}}',$('#antes').val(),$('#despues').val())" style="width: 50px;">
                después
            </strong>
        </h4>
    </div>
    <div class="box-body" style="overflow-x: scroll; overflow-y: scroll; max-width: 100%; height: 810px">
        <table class="table table-responsive table-bordered" width="100%"
               style="border: 2px solid #9d9d9d; font-size: 0.8em;">
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white"
                    width="15%" rowspan="2" colspan="2">
                    Fecha
                </th>
                @foreach(getVariedades() as $variedad)
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white"
                        width="{{75/count(getVariedades())}}%" colspan="2">
                        {{$variedad->nombre}}
                    </th>
                @endforeach
            </tr>
            <tr>
                @foreach(getVariedades() as $variedad)
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white">
                        Cosechado
                    </th>
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: white">
                        Acumulado
                    </th>
                @endforeach
            </tr>
            @foreach($fechas as $item)
                <tr>
                    <th class="text-center"
                        style="border-color: #9d9d9d; background-color: {{$item == $fecha ? '#b9ffb4' : '#e9ecef'}}; border-bottom: 3px solid #9d9d9d; text-align:center"
                        rowspan="4">
                        {{getDias(TP_ABREVIADO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime($item)))]}}
                        {{convertDateToText($item)}}
                    </th>
                </tr>
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #add8e6">
                        Disponibles
                    </th>
                    @foreach(getVariedades() as $variedad)
                        <td class="text-center" style="border-color: #9d9d9d" title="Cosechado">
                            {{$variedad->getDisponiblesToFecha($item)['cosechado']}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d" title="Acumulado">
                            @if($variedad->getPedidosToFecha($item) && $variedad->getDisponiblesToFecha($item)['acumulado'])
                                {{$variedad->getDisponiblesToFecha($item)['saldo']}}
                            @else
                                0
                            @endif
                        </td>
                    @endforeach
                </tr>
                <tr>
                    <th class="text-center" style="border-color: #9d9d9d; background-color: #add8e6">
                        Pedidos
                    </th>
                    @foreach(getVariedades() as $variedad)
                        <td class="text-center" style="border-color: #9d9d9d" title="Pedidos" colspan="2">
                            {{$variedad->getPedidosToFecha($item)}}
                        </td>
                    @endforeach
                </tr>
                <tr style="border-bottom: 3px solid #9d9d9d; background-color: #e9ecef">
                    <th class="text-center" style="border-color: #9d9d9d">
                        Saldo
                    </th>
                    @foreach(getVariedades() as $variedad)
                        <td class="text-center" style="border-color: #9d9d9d" title="Cosechado">
                            @php
                                $saldo = $variedad->getDisponiblesToFecha($item)['cosechado'] - $variedad->getPedidosToFecha($item);
                            @endphp
                            @if($saldo > 0)
                                <span class="badge" style="background-color: #b9ffb4; color: #0a0a0a; border: 1px solid #9d9d9d">
                                {{$saldo}}
                            </span>
                            @elseif($saldo < 0)
                                <span class="badge" style="background-color: #ce8483;">
                                {{$saldo}}
                            </span>
                            @else
                                <span class="badge">
                                {{$saldo}}
                            </span>
                            @endif
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d" title="Acumulado">
                            @if($variedad->getPedidosToFecha($item) && $variedad->getDisponiblesToFecha($item)['acumulado'])
                                @php
                                    $saldo = round($variedad->getDisponiblesToFecha($item)['saldo'] - $variedad->getPedidosToFecha($item),2);
                                @endphp
                                @if($saldo > 0)
                                    <span class="badge" style="background-color: #b9ffb4; color: #0a0a0a; border: 1px solid #9d9d9d">
                                        {{$saldo}}
                                    </span>
                                @elseif($saldo < 0)
                                    <span class="badge" style="background-color: #ce8483;">
                                        {{$saldo}}
                                    </span>
                                @else
                                    <span class="badge">
                                        {{$saldo}}
                                    </span>
                                @endif
                            @else
                                <span class="badge">0</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    </div>
</div>