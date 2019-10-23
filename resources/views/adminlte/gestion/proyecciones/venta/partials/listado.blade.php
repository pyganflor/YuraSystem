<div style="overflow: auto;">
    @if(count($proyeccionVentaSemanalReal)>0)
    <table class=" table-bordered table-hover" style="border: 2px solid #000000;font-size:0.8em" width="100%">
        <tr>
            <td class="text-center" style="background-color: #e9ecef;width:250px;border-right: 2px solid #000000;">
                Clientes / Semanas
                <input type="hidden" id="ramos_x_caja_empresa" name="ramos_x_caja_empresa" value="{{getConfiguracionEmpresa()->ramos_x_caja}}">
            </td>
            @foreach($semanas as $semana => $item)
                <td class="text-center" style="border:1px solid #9d9d9d; background-color: #e9ecef; width:350px;border-bottom: 2px solid #000000;border-right: 2px solid #000000;" colspan="3">{{$semana}}</td>
            @endforeach
            <td class="text-center" style="background-color: #e9ecef;width:250px;border-right: 2px solid #000000;">
                Clientes / Semanas
            </td>
        </tr>
        @foreach($proyeccionVentaSemanalReal as $idCliente => $semana)
            @php
                $semanas= $semana['semanas'];
                $cliente = getCliente($idCliente);
            @endphp
                <tr>
                    <td class="text-center" style="border-left:2px solid #000000;border-right:2px solid #000000;border-top:2px solid #000000;width: 250px">
                        <div class="btn-group" style="width:100%" data-toggle="tooltip" data-placement="top" title="{{$cliente->detalle()->nombre}}">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="width:100%">
                                <b>{{str_limit($cliente->detalle()->nombre,15)}}</b>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <form class="form-inline" >
                                        <div class="form-group">
                                            <label class="control-label" for="factor_cliente">Factor:</label>
                                            <input type="number" name="factor_cliente_{{$cliente->id_cliente}}"
                                                   value="{{$cliente->factor}}" id="factor_cliente_{{$cliente->id_cliente}}" style="width:80px;text-align:center">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success btn-xs" title="Guardar factor"  onclick="store_factor_cliente('{{$cliente->id_cliente}}','{{$idVariedad}}')" style="position: relative;border-radius: 0;bottom: 2px;padding-top: 2px;padding-bottom: 2px;">
                                                    <i class="fa fa-floppy-o"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </li>
                                <li>
                                    <form class="form-inline" >
                                        <div class="form-group">
                                            <label class="control-label" for="precio_variedad_{{$cliente->id_cliente}}">Precio:</label>
                                            <input type="number" id="precio_variedad_{{$cliente->id_cliente}}" name="precio_variedad_{{$cliente->id_cliente}}"
                                                   value="{{isset($cliente->precio_promedio($idVariedad)->precio) ? $cliente->precio_promedio($idVariedad)->precio : 0}}" style="width:80px;text-align:center">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success btn-xs" title="Guardar precio promedio"  onclick="store_precio_promedio('{{$cliente->id_cliente}}','{{$idVariedad}}')" style="position: relative;border-radius: 0;bottom: 2px;padding-top: 2px;padding-bottom: 2px;">
                                                    <i class="fa fa-floppy-o"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                    @foreach($semanas as $codigoSemana => $dataSemana)
                        <td class="text-center"  style="border-left:2px solid #000000;border-right:2px solid #000000;border-top:2px solid #000000;width: 250px" colspan="3">
                            <div style="width:100%" data-toggle="tooltip" data-placement="top"  title="Cajas físiscas año anterior"><b>{{$dataSemana['cajas_fisicas_anno_anterior']}}</b></div>
                        </td>
                    @endforeach
                    <td class="text-center" style="border-left:2px solid #000000;border-right:2px solid #000000;border-top:2px solid #000000;width: 250px">
                        <div class="btn-group" style="width:100%" data-toggle="tooltip" data-placement="top" title="{{$cliente->detalle()->nombre}}">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true" style="width:100%">
                                <b>{{str_limit($cliente->detalle()->nombre,15)}}</b>
                            </button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" style="border-bottom:2px solid #000000;border-left:2px solid #000000;border-right:2px solid #000000;width: 250px">Proyectado</td>
                    @foreach($semana['semanas'] as $codigoSemana => $dataSemana)
                        <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;">
                            <div style="width:100%;text-align:center;" data-toggle="tooltip" data-placement="top" title="Cajas físicas proyectadas">
                                <input type="number" id="cajas_proyectadas_{{$cliente->id_cliente}}_{{$codigoSemana}}"  min="0" onblur="store_proyeccion_venta('{{$cliente->id_cliente}}','{{$codigoSemana}}','{{$idVariedad}}')"
                                       onkeyup="calcular_proyeccion_cliente('{{$cliente->id_cliente}}','{{$codigoSemana}}')"
                                       name="cajas_proyectadas_{{$cliente->id_cliente}}_{{$codigoSemana}}" style="border:none;text-align:center;width:50px" value="{{$dataSemana['cajas_fisicas']}}">
                            </div>
                        </td>
                        <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;padding-left: 3px">
                            <div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer" data-toggle="tooltip" data-placement="top" title="Cajas equivalentes proyectadas">
                                <b id="cajas_equivalentes_{{$cliente->id_cliente}}_{{$codigoSemana}}">{{number_format($dataSemana['cajas_equivalentes'],2,".","")}}</b>
                            </div>
                        </td>
                        <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;padding-left: 3px;border-right: 2px solid #000000">
                            <div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Valor proyectado">
                                <b id="precio_proyectado_{{$cliente->id_cliente}}_{{$codigoSemana}}">${{number_format($dataSemana['valor'],2,".",",")}}</b>
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
