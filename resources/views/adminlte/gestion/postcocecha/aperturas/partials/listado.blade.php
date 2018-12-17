<div id="table_aperturas">
    @if(sizeof($listado)>0)
        <div class="row">
            <div class="col-md-7">
                <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
                       id="table_content_aperturas">
                    <thead>
                    <tr>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                            style="border-color: #9d9d9d">
                            Semana
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                            style="border-color: #9d9d9d">
                            Calibre
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                            style="border-color: #9d9d9d" width="7%">
                            Días Maduración
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                            style="border-color: #9d9d9d" title="Convertidos">
                            Ramos
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                            style="border-color: #9d9d9d">
                            Estandar
                        </th>
                        <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                            style="border-color: #9d9d9d">
                            Real
                        </th>
                    </tr>
                    </thead>
                    @php
                        $current_fecha = substr($listado[0]->fecha_inicio,0,10);

                        $total_disponibles = 0;
                        $total_ramos = 0;
                        $ids_apertura = '';
                        $i=0;
                    @endphp
                    @foreach($listado as $apertura)
                        <tr onmouseover="$(this).css('background-color','#ADD8E6')" onmouseleave="$(this).css('background-color','')"
                            style="color: {{getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->unidad_medida->tipo == 'L' ? 'blue' : ''}}">
                            <td class="text-center" style="border-color: #9d9d9d;">
                                <input type="hidden" class="ids_apertura" id="id_apertura_{{$apertura->id_stock_apertura}}"
                                       value="{{$apertura->id_stock_apertura}}">
                                <input type="hidden" id="cantidad_disponible_{{$apertura->id_stock_apertura}}"
                                       value="{{getStockById($apertura->id_stock_apertura)->cantidad_disponible}}">
                                <input type="hidden" id="ramos_convertidos_{{$apertura->id_stock_apertura}}"
                                       name="ramos_convertidos_{{$apertura->id_stock_apertura}}" class="ramos_convertidos"
                                       value="{{getStockById($apertura->id_stock_apertura)->calcularDisponibles()['estandar']}}">

                                {{getSemanaByDate($apertura->fecha_inicio)->codigo}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->nombre)[0]}}
                                {{getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->unidad_medida->siglas}}

                                <input type="hidden" id="calibre_unitario_{{$apertura->id_stock_apertura}}"
                                       value="{{explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->nombre)[0]}}">
                                <input type="hidden" id="tipo_calibre_unitatio_{{$apertura->id_stock_apertura}}"
                                       value="{{getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->unidad_medida->tipo}}">
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                {{difFechas(date('Y-m-d'),substr($apertura->fecha_inicio,0,10))->days}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d" title="Convertidos"
                                id="celda_ramos_convertidos_{{$apertura->id_stock_apertura}}">
                                {{getStockById($apertura->id_stock_apertura)->calcularDisponibles()['estandar']}}
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                <span class="badge" title="Estandar">
                                    {{getStockById($apertura->id_stock_apertura)->getDisponibles('estandar')}}
                                </span>
                            </td>
                            <td class="text-center" style="border-color: #9d9d9d">
                                <span class="badge" style="background-color: #357ca5" title="Real">
                                    {{getStockById($apertura->id_stock_apertura)->getDisponibles('real')}}
                                </span>
                            </td>
                        </tr>
                        @php
                            $total_disponibles += getStockById($apertura->id_stock_apertura)->getDisponibles('estandar');
                            $total_ramos += getStockById($apertura->id_stock_apertura)->calcularDisponibles()['estandar'];
                            $ids_apertura .= $apertura->id_stock_apertura.'|';
                        @endphp
                        @if(count($listado) > ($i+1) && substr($listado[$i+1]->fecha_inicio,0,10) != substr($apertura->fecha_inicio,0,10) ||
                            count($listado) == ($i+1))
                            <tr style="background-color: #e9ecef">
                                <td>
                                    <input type="hidden" id="fecha_{{substr($apertura->fecha_inicio,0,10)}}" class="fechas_aperturas"
                                           value="{{substr($apertura->fecha_inicio,0,10)}}">
                                    <input type="hidden" id="fecha_ids_aperturas_{{substr($apertura->fecha_inicio,0,10)}}"
                                           value="{{$ids_apertura}}">
                                    <input type="hidden" id="total_ramos_{{substr($apertura->fecha_inicio,0,10)}}"
                                           value="{{$total_ramos}}">
                                </td>
                                <td></td>
                                <th class="text-center" style=" border-color: #9d9d9d">Total</th>
                                <th class="text-center" style=" border-color: #9d9d9d"
                                    id="celda_total_ramos_{{substr($apertura->fecha_inicio,0,10)}}">
                                    {{$total_ramos}}
                                </th>
                                <th class="text-center" style=" border-color: #9d9d9d">{{$total_disponibles}}</th>
                                <td></td>
                            </tr>
                            @php
                                $total_ramos = 0;
                                $total_disponibles = 0;
                                $ids_apertura = '';
                            @endphp
                        @endif
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </table>
            </div>
            <div class="col-md-5">
                <div class="form-group text-center" style="margin-bottom: 15px" onchange="buscar_pedidos()">
                    <label for="fecha_pedidos">Fecha de pedidos</label>
                    <input type="date" id="fecha_pedidos">
                </div>
                <div id="div_listado_pedidos"></div>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>