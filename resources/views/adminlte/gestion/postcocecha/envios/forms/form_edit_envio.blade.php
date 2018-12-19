@for($i=0;$i<$cantForms;$i++)
    <form id="form_envio_{{$i+1}}" name="form_envio_{{$i+1}}">
          <div class="well sombra_estandar">
              <input type="hidden" value="{{$dataDetallesPedidos[$i]->cantidad}}" id="cantidad_detalle_form_{{$i+1}}">
              <input type="hidden" id="id_especificacion_{{$i+1}}" value="{{$dataDetallesPedidos[$i]->id_especificacion}}">
              <input type="hidden" id="id_cliente" value="{{$dataDetallesPedidos[$i]->id_cliente}}">
              <input type="hidden" id="id_pedido" value="{{$dataDetallesPedidos[$i]->id_pedido}}">
              <legend style="font-size: 1.3em">
                  Detalle N# {{$i+1}} <span id="numero_detalle_{{$i+1}}">({{$dataDetallesPedidos[$i]->cantidad ." ".$dataDetallesPedidos[$i]->nombre}}) </span>
                  <div>
                      <label style="float: left;margin-bottom: 0;margin-top: 4px;margin-right: 5px;">Fecha envío: </label>
                      <input type="date" id="fecha_envio_{{$i+1}}" class="form-control"
                             value="{{\Carbon\Carbon::parse($fechaEnvio[count($fechaEnvio) == 1 ? 0 : $i]->fecha_envio)->format('Y-m-d')}}"
                             style="width: 25%;margin-bottom: 5px">
                  </div>
                  <a href="javascript:void(0)" class="btn btn-xs btn-warning pull-right" title="Reiniciar envío" onclick="delete_input('{{$i+1}}')">
                      <i class="fa fa-eraser" aria-hidden="true"></i>
                  </a>
                  <a href="javascript:void(0)" class="btn btn-xs btn-primary pull-right" title="Añadir envío" onclick="add_form_envio('{{$i+1}}','{{$dataDetallesPedidos[$i]->cantidad}}')">
                      <i class="fa fa-fw fa-plus"></i>
                  </a>
              </legend>
              <div id="div_inputs_envios_{{$i+1}}">
                   @php $rows = 1; @endphp
                   @foreach($dataDetalleEnvio as $detalleEnvio)
                      @php $cantidad = 0; @endphp
                      @if($i+1 == $detalleEnvio->envio)
                          <div class="row" id="rows">
                              <input type="hidden"  id="id_detalle_envio_{{$i+1}}_{{$rows}}" value="{{$detalleEnvio->id_detalle_envio}}">
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
                                      <span class="input-group-addon" style="background-color: #e9ecef">Cantidad </span>
                                      <select class="form-control" id="cantidad_{{$i+1}}_{{$rows}}" name="cantidad_{{$i+1}}_{{$rows}}"required>
                                          <option selected disabled> Seleccione </option>
                                          @php  $cantidad += $detalleEnvio->cantidad; @endphp
                                          @for($x=0; $x<$cantidad; $x++)
                                              <option @if($detalleEnvio->cantidad === $x+1) {{"selected='selected'"}} @endif value="{{$x+1}}"> {{$x+1}} </option>
                                          @endfor
                                      </select>
                                  </div>
                              </div>
                              <div class="form-group col-md-4">
                                  <div class="input-group">
                                      <span class="input-group-addon" style="background-color: #e9ecef">Envío</span>
                                      <select class="form-control" id="envio_{{$i+1}}_{{$rows}}" name="envio_{{$i+1}}" onchange="change_agencia_transporte(this)" required>
                                          <option id="seleccione" value="{{$i+1}}"> Mismo envío </option>
                                      </select>
                                  </div>
                              </div>
                          </div>
                      @endif
                      @php $i+1 != $detalleEnvio->envio ?  $rows=1 : $rows++;  @endphp
                   @endforeach
              </div>
              <span id="msg_{{$i+1}}" class="error" style="margin-left:4%;"></span>
          </div>
      </form>
    @foreach($dataDetalleEnvio as $detalleEnvio1)
        <script>add_form_envio('{{$i+1}}','{{$dataDetallesPedidos[$i]->cantidad}}','{{$detalleEnvio1->form}}')</script>
    @endforeach
@endfor


