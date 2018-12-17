@for($i=0;$i<$cantForms;$i++)
    <form id="form_envio_{{$i+1}}" name="form_envio_{{$i+1}}">
        <div class="well sombra_estandar">
            <input type="hidden" value="{{$dataDetallesPedidos[$i]->cantidad}}" id="cantidad_detalle_form_{{$i+1}}">
            <input type="hidden" id="id_especificacion_{{$i+1}}" value="{{$dataDetallesPedidos[$i]->id_especificacion}}">
            <input type="hidden" id="id_cliente" value="{{$dataDetallesPedidos[$i]->id_cliente}}">
            <legend style="font-size: 1.3em">
                Detalle N# {{$i+1}} <span id="numero_detalle_{{$i+1}}">({{$dataDetallesPedidos[$i]->cantidad ." ".$dataDetallesPedidos[$i]->nombre}}) </span>
                <div>
                    <label style="float: left;margin-bottom: 0;margin-top: 4px;margin-right: 5px;">Fecha envío:</label>
                    <input type="date" id="fecha_envio_{{$i+1}}" class="form-control" value="{{$dataDetalleEnvio[$i]->fecha_envio}}" style="width: 25%;margin-bottom: 5px">
                </div>
                <a href="javascript:void(0)" class="btn btn-xs btn-warning pull-right" title="Reiniciar envío" onclick="reset_form_envio('')" id="btn_reset_form_envio_">
                    <i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0)" class="btn btn-xs btn-primary pull-right" title="Añadir envío" onclick="add_form_envio('{{$i+1}}','{{$dataDetallesPedidos[$i]->cantidad}}')" id="btn_add_form_envio_">
                    <i class="fa fa-fw fa-plus"></i>
                </a>
                <a href="javascript:void(0)" class="btn btn-xs btn-danger pull-right hide" title="Eliminar envío" onclick="delete_form_envio('')" id="btn_delete_form_envio_">
                    <i class="fa fa-fw fa-trash"></i>
                </a>
            </legend>
                 @php $rows = 1; @endphp
                 @foreach($dataDetalleEnvio as $detalleEnvio)
                    @php $cantidad = 0; @endphp
                    @if($i+1 == $detalleEnvio->envio)
                        <div class="row" id="rows">
                             <div class=" col-md-1 text-center">
                                 <label for="envio"> Envío  N# {{$rows}}</label>
                             </div>
                            <div class="form-group col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Agencia de transporte</span>
                                    <select class="form-control" id="id_agencia_transporte_{{$i+1}}_{{$rows}}" name="id_agencia_transporte_" required>
                                     <!--  <option selected disabled> Seleccione </option>-->
                                     @foreach($agencia_transporte as $at)
                                            <option  @php if($at->id_agencia_transporte == $detalleEnvio->id_agencia_transporte)
                                                             echo "selected='selected'"
                                                      @endphp
                                                    value="{{$at->id_agencia_transporte}}"> {{$at->nombre}}
                                            </option>
                                     @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Cantidad</span>
                                    <select class="form-control" id="cantidad_{{$i+1}}_{{$rows}}" name="cantidad__" required>
                                        <option selected disabled> Seleccione </option>
                                        @php
                                            $cantidad += $detalleEnvio->cantidad;
                                        @endphp
                                        @for($j=0; $j<$cantidad; $j++)
                                            <option  @php  if($detalleEnvio->cantidad == $cantidad)
                                                             echo "selected='selected'"
                                                     @endphp
                                                value="{{$j+1}}"> {{$j+1}}   </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon" style="background-color: #e9ecef">Envío</span>
                                    <select class="form-control" id="envio_{{$i+1}}_{{$rows}}" name="envio_" onchange="change_agencia_transporte(this)" required>
                                        <option id="seleccione" value="form"> Mismo envío </option>
                                        @for($x=0;$x<$cant_detalles;$x++)
                                            {{$i+1}} {{$detalleEnvio->envio}} {{$x+1}}
                                            @if($x+1 != $detalleEnvio->envio)
                                                <option>Detalle # {{$i+1}} envío {{$rows}}</option>
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                    @php $i+1 != $detalleEnvio->envio ?  $rows=1 : $rows++;  @endphp
                 @endforeach
            <div id="div_inputs_envios_{{$i+1}}"></div>
            <span id="msg_{{$i+1}}" class="error" style="margin-left:4%;"></span>
        </div>
    </form>
@endfor
