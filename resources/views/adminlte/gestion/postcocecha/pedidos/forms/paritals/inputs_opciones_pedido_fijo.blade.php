@if($opcion == 1 || $opcion == 2)
    <div style="overflow-x: scroll">
        <table width="100%" class="table table-responsive table-bordered"
               style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_pedidos">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    @if($opcion == 1)  DIAS SEMANA @else MES @endif
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FECHA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                    style="border-color: #9d9d9d">
                    FECHA
                </th>
                @if($opcion != 2 && $opcion != 3)
                    <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                        style="border-color: #9d9d9d">
                        INTÉRVALO
                    </th>
                @endif
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    @if($opcion == 1)
                        <div class="form-group">
                            <input type="hidden" id="opcion_pedido_fijo" value="{{$opcion}}">
                            <label for="Especificaciones">Seleccione un día de la semana</label><br/>
                            <select class="form-control" id="dia_semana" name="dia_semana" required onchange="habilitar_campos()">
                                <option selected disabled> Seleccione</option>
                                @foreach(getDias() as $key => $dias)
                                    <option value="@php if($key+1 == 7)  $key = -1; @endphp {{$key+1}}">{{$dias}}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($opcion == 2)
                        <div class="form-group">
                            <input type="hidden" id="opcion_pedido_fijo" value="{{$opcion}}">
                            <label for="Especificaciones">Seleccione un día del mes</label><br/>
                            <select class="form-control" id="dia_mes" name="dia_mes" required onchange="habilitar_campos()">
                                <option selected disabled> Seleccione</option>
                                @for($i=1; $i<32; $i++)
                                    <option value="{{$i}}">{{$i}} </option>
                                @endfor
                            </select>
                        </div>
                    @endif
                </td>
                <td>
                    <label for="Especificaciones">Desde</label><br/>
                    <input disabled type="date" id="fecha_desde_pedido_fijo" onchange="verificar_intervalo_fecha()"
                           name="fecha_desde_pedido_fijo" class="form-control" required>
                </td>
                <td>
                    <label for="Especificaciones">Hasta</label><br/>
                    <input disabled type="date" id="fecha_hasta_pedido_fijo" onchange="verificar_intervalo_fecha()"
                           name="fecha_hasta_pedido_fijo" class="form-control" required>
                </td>
                @if($opcion != 2 && $opcion != 3)
                    <td>
                        <label for="Especificaciones">Intérvalo de entrega</label><br/>
                        <select disabled class="form-control" id="intervalo" name="intervalo" required>
                            <option value=""> Seleccione</option>
                        </select>
                    </td>
                @endif
            </tr>
            </tbody>
        </table>
    </div>
@else
    <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
           id="table_content_fechas_pedidos_personalizados">
        <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                FECHAS
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                style="border-color: #9d9d9d">
                <button type="button" onclick="add_fechas_pedido_fijo_personalizado()" title="Agregar fecha" class="btn btn-success btn-xs"
                        id="btn_add_fechas_pedido_fijo_personalizado"><i class="fa fa-plus" aria-hidden="true"></i></button>
                <button type="button" onclick="delete_fechas_pedido_fijo_personalizado()" title="Eliminar fecha"
                        id="btn_delete_fechas_pedido_fijo_personalizado" class="btn btn-danger btn-xs hide"><i class="fa fa-trash "
                                                                                                               aria-hidden="true"></i></button>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="2" id="td_fechas_pedido_fijo_personalizado">
                <input type="hidden" id="opcion_pedido_fijo" value="{{$opcion}}">
                <div class="col-md-4" id="div_1">
                    <div class="form-group">
                        <label for="fecha_1">Fecha 1</label>
                        <br/>
                        <input type="date" id="fecha_desde_pedido_fijo_1" name="fecha_desde_pedido_fijo_1" class="form-control" required>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
@endif
