<div id="table_etiqueta"  style="margin-top: 20px;">
    @if(sizeof($facturas)>0)
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
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:60px;vertical-align: middle">
                   <label for="doble_todos">Doble</label>
                    <input type="checkbox" id="doble_todos" name="doble_todos" onclick="select_doble(this)">
                </th>
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:130px">
                   <label for="exportar_todos"> Exportar</label>
                    <input type="checkbox" id="exportar_todos" name="exportar_todos" checked onclick="select_exportar(this)">
                </th>
            </tr>
            </thead>
            <tbody id="tbody_etiquetas_facturas">

                @foreach($facturas as $x => $item)
                    @php $comprobante = getComprobante($item->id_comprobante) @endphp
                    <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                        <td style="border-color: #9d9d9d" class="text-center">{{isset($comprobante->numero_comprobante) ? $comprobante->numero_comprobante : "No facturado aún"}}</td>
                        <td style="border-color: #9d9d9d" class="text-center">{{$comprobante->envio->pedido->cliente->detalle()->nombre}}</td>
                        <td style="border-color: #9d9d9d" class="text-center">{{$comprobante->envio->pedido->detalles[0]->agencia_carga->nombre}}</td>
                        <td style="border-color: #9d9d9d" class="text-center">
                            @php
                                $full_equivalente_real = 0;
                                $full = false;
                                $half = false;
                                $cuarto = false;
                                $sexto = false;
                                $octavo = false;
                                foreach($comprobante->envio->pedido->detalles as $det_ped){
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
                            <input type="checkbox" name="doble_{{$x+1}}" class="doble" id="doble_{{$x+1}}">
                        </td>
                        <td style="border-color: #9d9d9d;vertical-align: middle;width:130px" class="text-center">
                            <table style="width: 100%">
                                <tr id="tr_exportables_{{$x+1}}">
                                    @if($full)
                                    <td>
                                        <label for="full_{{$x+1}}">Full</label>
                                        <input type="checkbox" checked name="full_{{$x+1}}" value="1" id="full_{{$x+1}}" class="exportar">
                                    </td>
                                    @endif
                                    @if($half)
                                    <td>
                                        <label for="half_{{$x+1}}">Half</label>
                                        <input type="checkbox" checked name="half_{{$x+1}}" value="0.5" id="half_{{$x+1}}" class="exportar">
                                    </td>
                                    @endif
                                    @if($cuarto)
                                    <td>
                                        <label for="cuarto_{{$x+1}}">Cuarto</label>
                                        <input type="checkbox" checked name="cuarto_{{$x+1}}" value="0.25" id="cuarto_{{$x+1}}" class="exportar">
                                    </td>
                                    @endif
                                    @if($sexto)
                                    <td>
                                        <label for="sexto_{{$x+1}}">Sexto</label>
                                        <input type="checkbox" checked name="sexto_{{$x+1}}" value="0.17" id="sexto_{{$x+1}}" class="exportar">
                                    </td>
                                    @endif
                                    @if($octavo)
                                        <td>
                                            <label for="octavo_{{$x+1}}">Octavo</label>
                                            <input type="checkbox" checked name="octavo_{{$x+1}}" value="0.125" id="octavo_{{$x+1}}" class="exportar">
                                        </td>
                                    @endif
                                    <td class="hide">
                                        <input type="hidden" name="selected_{{$x+1}}" value="{{$item->id_comprobante}}" class="id_comprobante" id="selected_{{$x+1}}">
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-center" style="margin-top: 20px;">
            <button type="button" class="btn btn-primary" onclick="generar_excel()">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i>  Gerenerar excel
            </button>
        </div>
    @else
        <div class="alert alert-info text-center" style="margin-top: 15px">No se han encontrado coincidencias</div>
    @endif
</div>

