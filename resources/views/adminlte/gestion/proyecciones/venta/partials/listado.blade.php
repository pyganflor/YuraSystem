<div style="overflow: auto;">
    @if(count($proyeccionVentaSemanalReal)>0)
    <table class=" table-bordered table-hover" style="border: 2px solid #000000;font-size:0.8em" width="100%">
        <tr>
            <td class="text-center" style="background-color: #e9ecef;width:250px;border-right: 2px solid #000000;">
                Clientes / Semanas
                <input type="hidden" id="ramos_x_caja_empresa" name="ramos_x_caja_empresa" value="{{getConfiguracionEmpresa()->ramos_x_caja}}">
            </td>
            @foreach(array_values($proyeccionVentaSemanalReal)[0] as $semana => $item)
                <td class="text-center" style="border:1px solid #9d9d9d; background-color: #e9ecef; width:350px;border-bottom: 2px solid #000000;border-right: 2px solid #000000;" colspan="3">{{$semana}}</td>
            @endforeach
            <td class="text-center" style="background-color: #e9ecef;width:250px;border-right: 2px solid #000000;">
                Clientes / Semanas
            </td>
        </tr>
        @foreach($proyeccionVentaSemanalReal as $cliente => $semana)
            @php $proyeccion = array_values($semana)[0]['proyeccion'] @endphp
            <tr>
                <td class="text-center" style="border-left:2px solid #000000;border-right:2px solid #000000;border-top:2px solid #000000;width: 250px">
                    <div class="btn-group" style="width:100%" data-toggle="tooltip" data-placement="top" title="{{$cliente}}">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="width:100%">
                            <b>{{str_limit($cliente,15)}}</b>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <form class="form-inline" >
                                    <div class="form-group">
                                        <label class="control-label" for="factor_cliente">Factor:</label>
                                        <input type="number" name="factor_cliente_{{$proyeccion->cliente->id_cliente}}"
                                               value="{{$proyeccion->cliente->factor}}" id="factor_cliente_{{$proyeccion->id_cliente}}" style="width:80px;text-align:center">
                                        <div class="form-group">
                                            <button class="btn btn-success btn-xs" title="Guardar factor"  onclick="store_factor_cliente('{{$proyeccion->id_cliente}}','{{$idVariedad}}')" style="position: relative;border-radius: 0;bottom: 2px;padding-top: 2px;padding-bottom: 2px;">
                                                <i class="fa fa-floppy-o"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </li>
                            <li>
                                <form class="form-inline" >
                                    <div class="form-group">
                                        <label class="control-label" for="precio_variedad_{{$proyeccion->id_cliente}}">Precio:</label>
                                        <input type="number" id="precio_variedad_{{$proyeccion->id_cliente}}" name="precio_variedad_{{$proyeccion->id_cliente}}"
                                               value="{{isset($proyeccion->cliente->precio_promedio($idVariedad)->precio) ? $proyeccion->cliente->precio_promedio($idVariedad)->precio : 0}}" style="width:80px;text-align:center">
                                        <div class="form-group">
                                            <button class="btn btn-success btn-xs" title="Guardar precio promedio"  onclick="store_precio_promedio('{{$proyeccion->id_cliente}}','{{$idVariedad}}')" style="position: relative;border-radius: 0;bottom: 2px;padding-top: 2px;padding-bottom: 2px;">
                                                <i class="fa fa-floppy-o"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
                @foreach($semana as $s)
                    <td class="text-center" style="border-color: #9d9d9d; width:350px;border-right: 2px solid #000000;"  colspan="3"></td>
                @endforeach
                <td class="text-center" style="border-left:2px solid #000000;border-right:2px solid #000000;border-top:2px solid #000000;width: 250px"  >
                    <div style="width:100%" data-toggle="tooltip" data-placement="top" title="{{$cliente}}">
                        <b>{{str_limit($cliente,15)}}</b>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="text-center" style="border-bottom:2px solid #000000;border-left:2px solid #000000;border-right:2px solid #000000;width: 250px">Proyectado</td>
                @foreach($semana as $x => $s)
                    <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;">
                        <div style="width:100%;text-align:center;" data-toggle="tooltip" data-placement="top" title="Cajas físicas proyectadas">
                            <input type="number" id="cajas_proyectadas_{{$proyeccion->cliente->id_cliente}}_{{$x}}"  min="0" onblur="store_proyeccion_venta('{{$proyeccion->cliente->id_cliente}}','{{$x}}','{{$idVariedad}}')"
                                   onkeyup="calcular_proyeccion_cliente('{{$proyeccion->cliente->id_cliente}}','{{$x}}')"
                                   name="cajas_proyectadas_{{$proyeccion->cliente->id_cliente}}_{{$x}}" style="border:none;text-align:center;width:50px" value="{{$s['proyeccion']->cajas_fisicas}}">
                        </div>
                    </td>
                    <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;padding-left: 3px">
                        <div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer" data-toggle="tooltip" data-placement="top" title="Cajas equivalentes proyectadas">
                            <b id="cajas_equivalentes_{{$proyeccion->cliente->id_cliente}}_{{$x}}">{{number_format($s['proyeccion']->cajas_equivalentes,2,".","")}}</b>
                        </div>
                    </td>
                    <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;padding-left: 3px;border-right: 2px solid #000000">
                        <div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Valor proyectado">
                            <b id="precio_proyectado_{{$proyeccion->cliente->id_cliente}}_{{$x}}">${{number_format($s['proyeccion']->valor,2,".",",")}}</b>
                        </div>
                    </td>
                @endforeach
                <td class="text-center" style="border-bottom:2px solid #000000;border-left:2px solid #000000;border-right:2px solid #000000;width: 250px">Proyectado</td>
            </tr>
        @endforeach
        <tr>
            <td class="text-center" style="background-color: #e9ecef;width:250px;border-right: 2px solid #000000;">
                <b>Totales</b>
            </td>
        </tr>
    </table>
    @else
        <div class="alert alert-info text-center" style="font-size:14px">No se encontraron registros</div>
    @endif
</div>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>
