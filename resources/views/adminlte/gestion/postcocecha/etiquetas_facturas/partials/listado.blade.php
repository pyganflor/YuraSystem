<div id="table_etiqueta"  style="margin-top: 20px;">
    @if(sizeof($pedidos)>0)
        <table width="100%" class="table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d" id="table_content_etiqueta" >
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
                    N° Comprobante
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
                    Cliente
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
                    Agencia de carga
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;vertical-align: middle">
                    N° Cajas
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:130px">
                   <label for="exportar_todos"> Exportar</label>
                    <input type="checkbox" id="exportar_todos" name="exportar_todos" checked onclick="select_exportar(this)">
                </th>
            </tr>
            </thead>
            <tbody id="tbody_etiquetas_facturas">
                @foreach($pedidos as $x => $pedido)
                    {{--@if($comprobante->envio->pedido->tipo_especificacion === "N")--}}
                        <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                            <td style="border-color: #9d9d9d" class="text-center">{{isset($pedido->secuencial) ? $pedido->secuencial : 'No posee'}}</td>
                            <td style="border-color: #9d9d9d" class="text-center">{{$pedido->cli_nombre}}</td>
                            <td style="border-color: #9d9d9d" class="text-center">{{$pedido->detalles[0]->agencia_carga->nombre}}</td>
                            <td style="border-color: #9d9d9d" class="text-center">
                                @php
                                    $full_equivalente_real = 0;
                                    $full = false;
                                    $half = false;
                                    $cuarto = false;
                                    $sexto = false;
                                    $octavo = false;
                                    foreach($pedido->detalles as $det_ped){
                                        foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                                            $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$det_ped->cantidad;
                                            $caja = explode("|",$esp_emp->empaque->nombre)[1];
                                                 if($caja == '1')
                                                    $full = true;
                                                if($caja == '0.5')
                                                    $half = true;
                                                if($caja == '0.25')
                                                    $cuarto = true;
                                                if($caja == '0.17')
                                                    $sexto = true;
                                                if($caja == '0.125')
                                                    $octavo = true;
                                        }
                                    }
                                @endphp
                                {{number_format($full_equivalente_real,2,".","")}}
                            </td>
                            <td style="border-color: #9d9d9d;width:60px" class="text-center">
                                <button type="button" class="btn btn-primary btn-xs" title="Generar etiqueta por factura"
                                        onclick="form_etiqueta_factura('{{$pedido->id_pedido}}')">
                                    <i class="fa fa-file-excel-o"></i>
                                </button>
                            </td>
                        </tr>
                    {{--@endif--}}
                @endforeach
            </tbody>
        </table>
        {{--<div class="text-center" style="margin-top: 20px;">
            <button type="button" class="btn btn-primary" onclick="generar_excel()">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i>  Gerenerar excel
            </button>
        </div>--}}
    @else
        <div class="alert alert-info text-center" style="margin-top: 15px">No se han encontrado coincidencias</div>
    @endif
</div>

