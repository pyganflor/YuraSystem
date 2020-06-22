<div id="table_factura" style="width: 100%;overflow-x: auto;">
    @if(sizeof($listado)>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d"
               id="table_content_factura">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    N°#
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    Secuencial
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    Clave SRI
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    DAE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    CÓDIGO DAE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    EXPORTADOR
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    GUÍA MADRE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    GUÍA HIJA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    FECHA GUÍA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    FACTURA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    FECHA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    MANIFIESTO
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    AEROLÍNEA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    AGENCIA CARGA
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    CLIENTE
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    RAMOS VAR
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    CAJAS FULL
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    TALLOS
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    PIEZAS
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    NETO DOLARES
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                    PESO GUÍA Kg.
                </th>
            </tr>
            </thead>
            @php
                $tr = 0; $tll=0; $fer = 0; $tp=0; $mt=0;
            @endphp
            @foreach($listado as $x => $item)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')"
                    class="{{$item->estado == 1 ? '':'error'}}" id="row_marcas_{{$item->id_marca}}">
                    <td style="border-color: #9d9d9d" class="text-center">{{$x+1}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->secuencial}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->clave_acceso}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->dae}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->codigo_dae}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{getConfiguracionEmpresa($item->id_configuracion_empresa)->razon_social}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->guia_madre}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->guia_hija}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{\Carbon\Carbon::parse($item->fecha_pedido)->addDay(1)->format('d/m/Y')}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->numero_comprobante}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->fecha_pedido}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->manifiesto}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{isset(getPedido($item->id_pedido)->envios[0]->detalles[0]->id_aerolinea)
                            ? getAerolinea(getPedido($item->id_pedido)->envios[0]->detalles[0]->id_aerolinea)->nombre
                            : ""
                        }}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">{{getAgenciaCarga(getPedido($item->id_pedido)->detalles[0]->id_agencia_carga)->nombre}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->nombre}}</td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        @php
                            $total_ramos = 0;
                            $full_equivalente_real = 0;
                            $total_tallos = 0;
                                foreach (getPedido($item->id_pedido)->detalles as $det_ped)
                                    foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                                        foreach ($esp_emp->detalles as $n => $det_esp_emp){
                                            $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                                            $total_ramos += number_format(($det_ped->cantidad*$esp_emp->cantidad*(isset($ramos_modificado) ? $ramos_modificado->cantidad :$det_esp_emp->cantidad)),2,".","");
                                            $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$det_ped->cantidad;
                                            $total_tallos += number_format(($det_ped->cantidad*$esp_emp->cantidad*(isset($ramos_modificado) ? $ramos_modificado->cantidad :$det_esp_emp->cantidad)*$det_esp_emp->tallos_x_ramos),2,".","");
                                        }
                            $tll+= $total_tallos;
                            $tr+=$total_ramos;
                            $fer+=$full_equivalente_real;
                        @endphp
                        {{$total_ramos}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$full_equivalente_real}} </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        {{$total_tallos}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        @php
                            $total_piezas = 0;
                            foreach (getPedido($item->id_pedido)->detalles as $det_ped)
                                $total_piezas += $det_ped->cantidad;

                            $tp += $total_piezas;
                        @endphp
                        {{number_format($total_piezas,2,".","")}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">
                        @php $mt += $item->monto_total @endphp
                        ${{$item->monto_total}}
                    </td>
                    <td style="border-color: #9d9d9d" class="text-center">{{$item->peso}}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="14" ></td>
                <td style="border-color: #9d9d9d" class="text-center">Total: </td>
                <td style="border-color: #9d9d9d" class="text-center">{{$tr}}</td>
                <td style="border-color: #9d9d9d" class="text-center">{{$fer}}</td>
                <td style="border-color: #9d9d9d" class="text-center">{{$tll}}</td>
                <td style="border-color: #9d9d9d" class="text-center">{{$tp}}</td>
                <td style="border-color: #9d9d9d" class="text-center">${{$mt}}</td>
                <td style="border-color: #9d9d9d"></td>
            </tr>
        </table>
    @else
        <div class="alert alert-info text-center">No se han encontrado coincidencias</div>
    @endif
</div>
