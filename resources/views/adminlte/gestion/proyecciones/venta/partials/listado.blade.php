<div style="overflow: auto;">
    @if(count($proyeccionVentaSemanalReal)>0)
    <table class=" table-bordered table-hover" style="border: 2px solid #000000;font-size:0.8em" width="100%">
        <tr>
            <td class="text-center" style="background-color: #e9ecef;width:250px;border-right: 2px solid #000000;" >
                Clientes / Semanas
                <input type="hidden" id="ramos_x_caja_empresa" name="ramos_x_caja_empresa" value="{{$ramosxCajaEmpresa}}">
            </td>
            @foreach($semanas as $semana => $item)
                <td class="text-center" style="border:1px solid #9d9d9d; background-color: #e9ecef; width:350px;border-bottom: 2px solid #000000;border-right: 2px solid #000000;" colspan="3">
                    <input type="checkbox" id="semana_{{$semana}}" name="semana_{{$semana}}" value="{{$semana}}"
                           class="check_programacion_semana" style='margin: 0;position: relative;top: 2px;' onclick="selecciona_check(this)">
                    <label for="semana_{{$semana}}">{{$semana}}</label>
                </td>
            @endforeach
            <td class="text-center" style="background-color: #e9ecef;width:250px;border-right: 2px solid #000000;">
                Clientes / Semanas
            </td>
        </tr>
        <tr>
            <td class="text-center" style="background-color: #e9ecef;width:250px;border: 2px solid #000000;" >
               Saldo inicial
            </td>
            @php $x=0; $semanaPasada='' @endphp
            @foreach($semanas as $semana => $item)
                @php $saldoInicial = getObjSemana($semana)->firstSaldoInicialBusqueda($idVariedad,$semana)->saldo_inicial;
                @endphp
                <td class="text-center" style="border:1px solid #9d9d9d; background-color: #e9ecef; width:350px;border-bottom: 2px solid #000000;border-right: 2px solid #000000;" colspan="3">
                    <b class="{{$saldoInicial < 0 ? "text-red" : "text-success"}} saldo_inicial_{{$semana}}"
                       data-toggle="tooltip" data-placement="top" title="Saldo inicial">
                        {{round($saldoInicial,2)}}
                    </b>
                    <b><i class="fa {{$saldoInicial < 0 ? "fa-arrow-down text-red" : "fa-arrow-up text-success"}}" aria-hidden="true"></i></b>
                </td>
                @php $x++; $semanaPasada=$semana @endphp
            @endforeach
            <td class="textsemanar" style="background-color: #e9ecef;width:250px;border: 2px solid #000000;">
                Saldo inicial
            </td>
        </tr>
        <tr >
            <td class="text-center" style="background-color: #e9ecef;width:250px;border: 2px solid #000000;">
                Cajas proyectadas
            </td>
            @foreach($semanas as $semana => $item)
                @php $cajasProyectadas = getObjSemana($semana)->getCajasProyectadas($idVariedad);@endphp
                <td class="text-center" style="border:1px solid #9d9d9d; background-color: #e9ecef; width:350px;border-bottom: 2px solid #000000;border-right: 2px solid #000000;" colspan="3">
                    <b class="cajas_proyectas_semana_{{$semana}}">{{round($cajasProyectadas,2)}}</b>
                </td>
            @endforeach
            <td class="text-center" style="background-color: #e9ecef;width:250px;border: 2px solid #000000;">
                Cajas proyectadas
            </td>
        </tr>
        @php
            $x=1;
            $idsClientes = [];
        @endphp
        @foreach($proyeccionVentaSemanalReal as $idCliente => $semana)
            @php
                $semanas= $semana['semanas'];
                $cliente = getCliente($idCliente);
                $total=0;
                $idsClientes[]=$idCliente;
            @endphp
            @if($x ==1)
                <tr style="background-color: #ffb100;">
                    <td class="text-center" style="width:250px;border: 2px solid #000000;">
                        Desecho
                    </td>
                    @foreach($semana['semanas'] as $codigoSemana => $dataSemana)
                        <td class="text-center desecho_semana_{{$codigoSemana}}"
                            style="border:1px solid #9d9d9d; width:350px;border-bottom: 2px solid #000000;border-right: 2px solid #000000;" colspan="3">
                            <input type="number" min="0" id="desecho_semana_{{$codigoSemana}}" name="desecho_semana_{{$codigoSemana}}"
                                   data-toggle="tooltip" data-placement="top" title="Cajas desechadas" value="{{getObjSemana($codigoSemana)->desecho($idVariedad)}}"
                                   onkeyup="calcular_proyeccion_cliente(null,'{{$codigoSemana}}')"
                                   onchange="calcular_proyeccion_cliente(null,'{{$codigoSemana}}')"
                                   style="border:none;background-color: transparent;text-align:center" class="input_semana_{{$codigoSemana}}">
                            <input type="hidden" class="desecho_inicial_{{$codigoSemana}}" value="{{getObjSemana($codigoSemana)->desecho($idVariedad)}}">
                        </td>
                    @endforeach
                    <td class="text-center" style="width:250px;border: 2px solid #000000;">
                        Desecho
                    </td>
                </tr>
            @endif
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
                        @php $cajasFisicasAnnoAnterior = getObjSemana($codigoSemana)->cajasFisicasAnnoAnterior($idVariedad,$idCliente) @endphp
                        <td class="text-center"  style="border-left:2px solid #000000;border-right:2px solid #000000;border-top:2px solid #000000;width: 250px;background: #08ffe836;" colspan="3">
                            <div style="width:100%" data-toggle="tooltip" data-placement="top"  title="Cajas físicas año anterior">
                                <b>{{isset($cajasFisicasAnnoAnterior->cajas_fisicas_anno_anterior) ? $cajasFisicasAnnoAnterior->cajas_fisicas_anno_anterior : 0}}</b>
                            </div>
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
                        @php
                            $objSemana = getObjSemana($codigoSemana);
                            $cajasFisicasAnnoAnterior = $objSemana->cajasFisicasAnnoAnterior($idVariedad,$idCliente);
                            if($dataSemana['cajas_fisicas'] == 0 && $semanaActual<$codigoSemana){
                                $cajasFisicas = isset($cajasFisicasAnnoAnterior->cajas_fisicas_anno_anterior) ? $cajasFisicasAnnoAnterior->cajas_fisicas_anno_anterior : 0;
                                $cajasEquivalentes = $cajasFisicas*$cliente->factor;
                                $ramosTotales = $cajasFisicas*$cliente->factor*$ramosxCajaEmpresa;
                                $precioPromedio = $cliente->precio_promedio($idVariedad);
                                $valor = $ramosTotales*(isset($precioPromedio) ? $precioPromedio->precio : 0);
                            }else{
                                $cajasFisicas =  $dataSemana['cajas_fisicas'];
                                $cajasEquivalentes =$dataSemana['cajas_equivalentes'];
                                $valor = $dataSemana['valor'];
                            }
                        @endphp
                        <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;" class="td_cajas_proyectadas semana_{{$codigoSemana}}">
                            <div style="width:100%;text-align:center;" data-toggle="tooltip" data-placement="top" title="Cajas físicas proyectadas"
                                 ondblclick="habilitar('cajas_proyectadas_{{$cliente->id_cliente}}_{{$codigoSemana}}')">
                                <input type="number" id="cajas_proyectadas_{{$cliente->id_cliente}}_{{$codigoSemana}}"  min="0"
                                       onkeyup="calcular_proyeccion_cliente('{{$cliente->id_cliente}}','{{$codigoSemana}}')"
                                       onchange="calcular_proyeccion_cliente('{{$cliente->id_cliente}}','{{$codigoSemana}}')"
                                       disabled name="cajas_proyectadas_{{$cliente->id_cliente}}_{{$codigoSemana}}"
                                       class="input_cajas_proyectadas input_cajas_proyectadas_{{$codigoSemana}}" style="border:none;text-align:center;width:50px"
                                       value="{{$cajasFisicas}}">
                                <input type="hidden" class="id_cliente" value="{{$cliente->id_cliente}}">
                                <input type="hidden" class="input_codigo_semana" value="{{$codigoSemana}}">
                                <input type="hidden" class="cajas_fisicas_inicial_{{$cliente->id_cliente}}_{{$codigoSemana}}" value="{{$cajasFisicas}}">
                            </div>
                        </td>
                        <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;">
                            <div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer" class="cajas_equivalentes" data-toggle="tooltip" data-placement="top" title="Cajas equivalentes proyectadas">
                                <b id="cajas_equivalentes_{{$cliente->id_cliente}}_{{$codigoSemana}}">{{round($cajasEquivalentes,2)}}</b>
                                <input type="hidden" class="cajas_equivalente_inicial_{{$cliente->id_cliente}}_{{$codigoSemana}}" value="{{round($cajasEquivalentes,2)}}">
                            </div>
                        </td>
                        <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;border-right: 2px solid #000000">
                            <div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer;" class="precio_proyectado" data-toggle="tooltip" data-placement="top" title="Valor proyectado">
                                <b  id="precio_proyectado_{{$cliente->id_cliente}}_{{$codigoSemana}}">${{round($valor,2)}}</b>
                                <input type="hidden" class="valor_inicial_{{$cliente->id_cliente}}_{{$codigoSemana}}" value="{{round($valor,2)}}">
                            </div>
                        </td>
                    @endforeach
                    <td class="text-center" style="border-bottom:2px solid #000000;border-left:2px solid #000000;border-right:2px solid #000000;width: 250px">Proyectado</td>
                </tr>
                @if($x == $cantProyeccionVentaSemanalReal)
                    @if($otros)
                        <tr style="background: #08ffe8">
                           <td class="text-center" style="border-bottom:2px solid #000000;border-left:2px solid #000000;border-right:2px solid #000000;width: 250px"><b>Otros</b></td>
                            @foreach($semana['semanas'] as $codigoSemana => $dataSemana)
                                @php $objSemana = getObjSemana($codigoSemana)->getTotalesProyeccionVentaSemanal($idsClientes,$idVariedad);@endphp
                                <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;">
                                    <div style="width:100%;text-align:center;" data-toggle="tooltip" data-placement="top" title="Cajas físicas proyectadas">
                                        <b>{{round($objSemana['cajasFisicas'],2)}}</b>
                                    </div>
                                </td>
                                <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;">
                                    <div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer" data-toggle="tooltip" data-placement="top" title="Cajas equivalentes proyectadas">
                                        <b class="otros_semana_{{$codigoSemana}}">{{round($objSemana['cajasEquivalentes'],2)}}</b>
                                    </div>
                                </td>
                                <td style="border: 1px solid #9d9d9d;border-bottom: 2px solid #000000;border-right: 2px solid #000000">
                                    <div style="padding: 3px 6px;width:100%;text-align:center;cursor:pointer;" data-toggle="tooltip" data-placement="top" title="Valor proyectado">
                                        <b> ${{round($objSemana['valor'],2)}}</b>
                                    </div>
                                </td>
                            @endforeach
                            <td class="text-center" style="border-bottom:2px solid #000000;border-left:2px solid #000000;border-right:2px solid #000000;width: 250px"><b>Otros</b></td>
                       </tr>
                    @endif
                    <tr style="background-color: rgb(3, 222, 0)">
                        <td class="text-center" style="width:250px;border-right: 2px solid #000000;">
                            <b>Totales</b>
                        </td>
                        @foreach($semanas as $semana => $dataSemana)
                            @php $objSemana = getObjSemana($semana)->getTotalesProyeccionVentaSemanal(false,$idVariedad,true,$semanaActual,false,$ramosxCajaEmpresa); @endphp
                            <td class="text-center"  style="border: 1px solid #9d9d9d" >
                                <b class="total_cajas_semana_{{$semana}}">{{round($objSemana['cajasFisicas'],2)}}</b>
                            </td>
                            <td class="text-center "  style="border: 1px solid #9d9d9d" >
                                <b class="total_cajas_equivalentes_semana_{{$semana}}">{{round($objSemana['cajasEquivalentes'],2)}}</b>
                            </td>
                            <td class="text-center"  style="border-right:2px solid #000000;" >
                               <b class="total_dinero_semana_{{$semana}}"> ${{round($objSemana['valor'],2)}}</b>
                            </td>
                        @endforeach
                        <td class="text-center" style="width:250px;border-right: 2px solid #000000;border-top: 2px solid #000000">
                            <b>Totales</b>
                        </td>
                    </tr>
                    <tr style="background-color: #e9ecef">
                        <td class="text-center" style="border:2px solid #000000;width: 250px"><b>Saldo final</b></td>
                        @php $x =0;$semanaPasada=''  @endphp
                        @foreach($semanas as $semana => $item)
                            @php $saldoFinal= getObjSemana($semana)->firstSaldoInicialBusqueda($idVariedad,$semana)->saldo_final;
                                /*$objSemanaPasada =getObjSemana($semanaPasada);
                                if($x==0){
                                    $firstSemanaResumenSemanaCosechaByVariedad = (int)$objSemanaActual->firstSemanaResumenSemanaCosechaByVariedad($idVariedad);
                                    if($firstSemanaResumenSemanaCosechaByVariedad > $semana){
                                        $saldoFinal = $objSemanaActual->getSaldo($idVariedad);
                                    }elseif($firstSemanaResumenSemanaCosechaByVariedad < $semana){
                                        $saldoFinal = $objSemanaActual->firstSaldoInicialBusqueda($idVariedad,$semana)->saldo_final;
                                        //$saldoFinal = $objSemanaActual->getLastSaldoFinal($idVariedad,$semana);
                                    }else{
                                        $saldoFinal = $objSemanaActual->firstSaldoInicialByVariedad($idVariedad)+round($objSemanaActual->getSaldo($idVariedad),2);
                                    }
                                }

                                $saldoInicial = round($objSemanaActual->getSaldo($idVariedad),2)+$saldoFinal;

                                if($x>0)
                                    $saldoFinal=$saldoInicial;*/
                            @endphp
                            <td style="border: 1px solid #9d9d9d;border: 2px solid #000000;" colspan="3">
                                <div style="width:100%;text-align:center;" data-tooltip data-placement="top" title="Saldo final">
                                    <b class="{{$saldoFinal < 0 ? "text-red" : "text-success"}} saldo_final_{{$semana}}">
                                      {{round($saldoFinal,2)}}
                                    </b>
                                    <b><i class="fa {{$saldoFinal < 0 ? "fa-arrow-down text-red" : "fa-arrow-up text-success"}}" aria-hidden="true"></i></b>
                               </div>
                            </td>
                            @php $x++; $semanaPasada=$semana @endphp
                        @endforeach
                        <td class="text-center" style="border:2px solid #000000;width: 250px"><b>Saldo final</b></td>
                    </tr>
                @endif
            @php $x++ @endphp
        @endforeach
        <tr>
            <td class="text-center" style="border: 2px solid #000000;background-color: #e9ecef;width:250px;">
                Clientes / Semanas
                <input type="hidden" id="ramos_x_caja_empresa" name="ramos_x_caja_empresa" value="{{$ramosxCajaEmpresa}}">
            </td>
            @foreach($semanas as $semana => $item)
                <td class="text-center" style="border: 2px solid #000000;background-color: #e9ecef; width:350px;" colspan="3">{{$semana}}</td>
            @endforeach
            <td class="text-center" style="border: 2px solid #000000;background-color: #e9ecef;width:250px;">
                Clientes / Semanas
            </td>
        </tr>
    </table>
    @else
        <div class="alert alert-info text-center" style="font-size:14px">No se encontraron registros</div>
    @endif
</div>
<div class="text-right" style="margin-top: 10px">
    <legend style="font-size: 1em; margin-bottom: 0">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseLeyenda">
            <strong style="color: black">Leyenda <i class="fa fa-fw fa-caret-down"></i></strong>
        </a>
    </legend>
    <ul style="margin-top: 5px" class="list-unstyled panel-collapse collapse" id="collapseLeyenda">
        <li>Totales <i class="fa fa-fw fa-circle" style="color: rgb(3, 222, 0)"></i></li>
        <li>Otros clientes <i class="fa fa-fw fa-circle" style="color:#08ffe8"></i></li>
        <li>Desecho <i class="fa fa-fw fa-circle" style="color:#ffb100"></i></li>
        <li>Cajas físicas proyectadas del año anterior <i class="fa fa-fw fa-circle" style="color:#08ffe836"></i></li>
        <li>Total clientes: {{$clientes}} <i class="fa fa-users" style="color: #9100ff7d"></i> </li>
    </ul>
</div>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
    function habilitar(id){
        if($("#"+id).prop('disabled')){
            $("#"+id).removeAttr('disabled');
        }else{
            $("#"+id).attr('disabled',true);
        }
    }
</script>

