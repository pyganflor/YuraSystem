<form id="form_despacho">
    <div class="box box-solid box-primary">
    <div class="box-header with-border">
        <div class="box-title col-md-4 " ><b>Despacho N#: {{str_pad((getSecuenciaDespacho()), 9, "0", STR_PAD_LEFT)}}</b>
        </div>
        <div class="box-title col-md-4 text-center" ><b>DESPACHO DE FINCA</b>

        </div>
        <div class="box-title col-md-4 text-right"><b>{{strtoupper($empresa->razon_social)}}</b>
            {{--<input type="date"  id="fecha_envio" name="fecha_envio" style="color:black"
                   value="{{now()->format('Y-m-d')}}">--}}
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
            <div class="col md-12 text-center" style="font-weight: bold">
                DATOS GENERALES
            </div>
        <table width="100%" class="table-responsive table-bordered" style=" border-color: white;margin-top:20px">
            <tr>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"><b>Transportisa</b></td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <select id="id_transportista" name="id_transportista" style="width: 100%;border: none;" required>
                        @foreach ($transportistas as $t)
                            <option value="{{$t->id_transportista}}">{{$t->nombre_empresa}}</option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Camión</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <select id="id_camion" style="width: 100%;border: none" onchange="busqueda_placa_camion()" required></select>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Placa</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" id="n_placa" style="width: 100%;border: none;" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Chofer</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <select id="id_chofer" style="width: 100%;border: none;" required></select>
                </td>
            </tr>
            <tr>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Fecha</b>
                </td>
                <td style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="date" id="fecha_despacho" name="fecha_despacho" value="{{now()->format('Y-m-d')}}"
                           readonly style="width: 100%;border: none;" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Sello de salida</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" id="sello_salida" name="sello_salida" style="width: 100%;border: none;" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Responsable</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle" >
                    <input type="text" id="responsable" name="responsable" onkeyup="duplicar_nombre(this)" style="width: 100%;border: none;" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Horario</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" id="horario" name="horario" style="width: 100%;border: none;" >
                </td>
            </tr>
            <tr>
                <td  class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Semana</b>
                </td>
                <td style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" id="semana" name="semana" value="{{getSemanaByDate(now()->toDateString())->codigo}}"
                        readonly style="width: 100%;border: none;" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Rango Tmp</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" id="rango_temp" name="rango_temp" style="width: 100%;border: none;" >
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Sellos entregados</b>
                </td>
                <td class="text-center" id="cant_sellos" style="border-color: #9d9d9d;vertical-align: middle"></td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Sello adicionale</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" id="sello_adicional" name="sello_adicional" style="width: 100%;border: none;" >
                </td>
            </tr>
            <tr>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Viaje N#</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input value="1" type="number" id="n_viaje" name="n_viaje" style="width: 100%;border: none;" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Hora de salida</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" id="horas_salida" name="horas_salida" style="width: 100%;border: none;">
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Temperatura</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" id="temperatura" name="temperatura" style="width: 100%;border: none;" >
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Kilometraje</b>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <input type="text" id="kilometraje" name="kilometraje" style="width: 100%;border: none;" >
                </td>
            </tr>
            <tr>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <b>Sellos</b>
                </td>
                @for ($i = 0; $i < 7; $i++)
                    <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                        <input type="text" class="sello" name="sello" style="width: 100%;border: none;" {{$i==0 ? "required" : ""}}>
                    </td>
                @endfor
            </tr>
        </table>
        <div class="col md-12 text-center" style="font-weight: bold;margin-top: 20px">
            DESPACHO
        </div>
        <table width="100%" class="table-responsive table-bordered" style=" border-color: white;margin-top:20px">
            <thead>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Cliente</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Agencia</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Cajas Full</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Piezas</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Guia</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Temp</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Persona que recibe</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Hora de llegada</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Hora de salida</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Sello inicial</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Sello final</th>
                <th class="text-center" style="border-color: #9d9d9d; background-color: #357CA5; color: white">Observaciones</th>
            </thead>
            <tbody>
            @php $total_caja_full = 0; $piezas_totales = 0; @endphp
                @foreach($pedidos as $pedido)
                    <tr id="tr_despachos">
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                           @foreach(getPedido($pedido->id_pedido)->cliente->detalles as $det_cliente)
                                {{$det_cliente->estado == 1 ? $det_cliente->nombre : "" }}
                            @endforeach
                            <input type="hidden" class="id_pedido" name="id_pedido" value="{{$pedido->id_pedido}}">
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                            {{getPedido($pedido->id_pedido)->detalles[0]->agencia_carga->nombre}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                            @php $caja_full = 0; @endphp
                            @foreach(getPedido($pedido->id_pedido)->detalles as $det_ped)
                                @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
                                    @php $caja_full += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1] @endphp
                                @endforeach
                            @endforeach
                            @php $total_caja_full +=  $caja_full@endphp
                            {{$caja_full}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                           @php $piezas = 0; @endphp
                            @foreach(getPedido($pedido->id_pedido)->detalles as $det_ped)
                                @foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp)
                                    @foreach($esp_emp->detalles as $x => $det_esp)
                                        @php  if($x == 0) $piezas += ($esp_emp->cantidad * $det_ped->cantidad); @endphp
                                    @endforeach
                                @endforeach
                            @endforeach
                            {{$piezas}}
                            @php $piezas_totales += $piezas; @endphp
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                           {{ getPedido($pedido->id_pedido)->envios[0]->detalles[0]->id_aerolinea ==  ""
                                ? "No se ha asignado aerolínea"
                                : getPedido($pedido->id_pedido)->envios[0]->detalles[0]->aerolinea->codigo}}
                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">

                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">

                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">

                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">

                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">

                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">

                        </td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">

                        </td>
                    </tr>
                @endforeach
                    <tr>
                        <td></td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">Total:</td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"> {{$total_caja_full}}</td>
                        <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle"> {{$piezas_totales}}</td>
                    </tr>
                    <tr>
                    </tr>
            </tbody>
        </table>
        <div class="col md-12 text-center" style="font-weight: bold;margin-top: 20px">
            RESPONSABLES
        </div>
        <table width="100%" class="table-responsive table-bordered" style=" border-color: white;margin-top:20px">
            <tr>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Oficina de despacho</label>
                    <input type="text" id="nombre_oficina_despacho" style="text-align: center" class="form-control input-sm" name="nombre_oficina_despacho"
                           value="{{isset($datos_responsables->resp_ofi_despacho) ? $datos_responsables->resp_ofi_despacho : ""}}" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Aux. Cuarto frio</label>
                    <input type="text" id="nombre_cuarto_frio" style="text-align: center" class="form-control input-sm" name="nombre_cuarto_frio"
                           value="{{isset($datos_responsables->aux_cuarto_fri) ? $datos_responsables->aux_cuarto_fri : ""}}" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Transportista</label>
                    <input type="text" id="nombre_transportista" style="text-align: center" class="form-control input-sm" name="nombre_transportista"
                           value="{{isset($datos_responsables->resp_transporte) ? $datos_responsables->resp_transporte : ""}}" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Guradia de turno</label>
                    <input type="text" id="nombre_guardia_turno" style="text-align: center" class="form-control input-sm" name="nombre_guardia_turno"
                           value="{{isset($datos_responsables->guardia_turno) ? $datos_responsables->guardia_turno : ""}}" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Asist Comercio Ext.</label>
                    <input type="text" id="nombre_asist_comercial" style="text-align: center" class="form-control input-sm" name="nombre_asist_comercial"
                           value="{{isset($datos_responsables->asist_comercial_ext) ? $datos_responsables->asist_comercial_ext : ""}}" required>
                </td>
            </tr>
            <tr>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="id_oficina_despacho" style="text-align: center" class="form-control input-sm" name="id_oficina_despacho"
                           value="{{isset($datos_responsables->id_resp_ofi_despacho) ? $datos_responsables->id_resp_ofi_despacho : ""}}"  required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="id_cuarto_frio" style="text-align: center" class="form-control input-sm" name="id_cuarto_frio"
                           value="{{isset($datos_responsables->id_aux_cuarto_fri) ? $datos_responsables->id_aux_cuarto_fri : ""}}" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="firma_id_transportista" style="text-align: center" class="form-control input-sm" name="firma_id_transportista"
                           value="{{isset($datos_responsables->id_resp_transporte) ? $datos_responsables->id_resp_transporte : ""}}"  required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="id_guardia_turno" style="text-align: center" class="form-control input-sm" name="id_guardia_turno"
                           value="{{isset($datos_responsables->id_guardia_turno) ? $datos_responsables->id_guardia_turno : ""}}" required>
                </td>
                <td class="text-center" style="border-color: #9d9d9d;vertical-align: middle">
                    <label>Identificación</label>
                    <input type="text" id="id_asist_comercial" style="text-align: center" class="form-control input-sm" name="id_asist_comercial"
                           value="{{isset($datos_responsables->id_asist_comrecial_ext) ? $datos_responsables->id_asist_comrecial_ext : ""}}" required>
                </td>
            </tr>
        </table>
    </div>
</div>
</form>
<script>  busqueda_camiones_conductores(); </script>
