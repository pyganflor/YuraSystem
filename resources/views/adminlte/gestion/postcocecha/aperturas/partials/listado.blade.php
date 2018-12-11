<div id="table_aperturas">
    @if(sizeof($listado)>0)
    <div class="row">
        <div class="col-md-8">
            <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_aperturas">
            <thead>
            <tr>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Variedad
                </th>
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
                    style="border-color: #9d9d9d">
                    Ramos
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    Disponibles
                </th>
            </tr>
            </thead>
            @php
                $current_fecha = substr($listado[0]->fecha_inicio,0,10);

                $total_disponibles = 0;
                $total_ramos = 0;
                $i=0;
            @endphp
            @foreach($listado as $apertura)
                <tr onmouseover="$(this).css('background-color','#ADD8E6')" onmouseleave="$(this).css('background-color','')">
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{getStockById($apertura->id_stock_apertura)->variedad->nombre}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{getSemanaByDate($apertura->fecha_inicio)->codigo}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->nombre)[0]}}
                        {{getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->unidad_medida->siglas}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{difFechas(date('Y-m-d'),substr($apertura->fecha_inicio,0,10))->days}}
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        @if($calibre == '')
                            {{round(getStockById($apertura->id_stock_apertura)->cantidad_disponible /
                            explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->nombre)[1],2)}}
                        @else
                            @php
                                $f = round(($calibre->nombre / explode('|',getStockById($apertura->id_stock_apertura)->clasificacion_unitaria->nombre)[0]),2);
                                echo round(getStockById($apertura->id_stock_apertura)->cantidad_disponible / $f,2);
                            @endphp
                        @endif
                    </td>
                    <td class="text-center" style="border-color: #9d9d9d">
                        {{getStockById($apertura->id_stock_apertura)->getDisponibles()}}
                    </td>
                </tr>
                @php
                    $total_disponibles += getStockById($apertura->id_stock_apertura)->getDisponibles();
                    $total_ramos += round(getStockById($apertura->id_stock_apertura)->cantidad_disponible / $f,2);
                @endphp
                @if(count($listado) > ($i+1) && substr($listado[$i+1]->fecha_inicio,0,10) != substr($apertura->fecha_inicio,0,10) ||
                    count($listado) == ($i+1))
                    <tr style="background-color: #e9ecef">
                        <td></td>
                        <td></td>
                        <td></td>
                        <th class="text-center" style=" border-color: #9d9d9d">Total</th>
                        <th class="text-center" style=" border-color: #9d9d9d">{{$total_ramos}}</th>
                        <th class="text-center" style=" border-color: #9d9d9d">{{$total_disponibles}}</th>
                    </tr>
                    @php
                        $total_ramos = 0;
                        $total_disponibles = 0;
                    @endphp
                @endif
                @php
                    $i++;
                @endphp
            @endforeach
        </table>
        </div>
        <div class="col-md-4">
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