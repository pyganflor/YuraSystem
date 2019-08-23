<div class="col-md-2">
    @if($comprobantes->count()>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;">
                    Facturas generadas
                </th>
            </tr>
            </thead>
            @foreach($comprobantes as $comp => $comprobante)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                    <td style="border-color: #9d9d9d;cursor: pointer" class="text-center">
                        <div class="item_factura item_factura_{{$comp+1}} " id="{{$comprobante->secuencial}}" style="wodth:100%">
                        <input type="hidden" id="n_factura" name="n_facutra" value="{{$comprobante->secuencial}}">
                        {{$comprobante->secuencial}}
                        </div>
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="alert alert-info text-center">No se han generado facturas</div>
    @endif
</div>
<div class="col-md-10" >
@if($comprobantes->count()>0)
    <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d" id="table_content_pedido_factura_generada">
        <thead>
        <tr style="background-color: #dd4b39; color: white">
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;">
                Orden
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;">
                Factura actual
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                Cliente
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                Agencia de carga
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                Cajas físicas
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                Cajas full
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                Opciones
            </th>
        </tr>
        </thead>
        @php $total_full_equivalente= 0; $total_full_fisicas=0; @endphp
        @foreach($comprobantes as $c => $comprobante)
            @php $full_equivalente_real =0;  $full_fisicas = 0; @endphp
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                <td style="border-color: #9d9d9d;width:100px;cursor:pointer;" class="text-center orden_factura" id="orden_factura_{{$c+1}}"
                    title="Haga clic para eliminar" onclick="delete_item_factura(this)"></td>
                <td style="border-color: #9d9d9d;" class="text-center">
                    {{$comprobante->secuencial}}
                    <input type="hidden" id='id_comprobante_{{$c+1}}' name='id_comprobante_{{$c+1}}' value="{{$comprobante->id_comprobante}}">
                </td>
                <td style="border-color: #9d9d9d" class="text-center">{{$comprobante->envio->pedido->cliente->detalle()->nombre}}</td>
                <td style="border-color: #9d9d9d" class="text-center">{{$comprobante->envio->pedido->detalles[0]->agencia_carga->nombre}}</td>
                <td style="border-color: #9d9d9d" class="text-center">
                    @php
                        foreach($comprobante->envio->pedido->detalles as $x => $det_ped){
                            foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                                $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$det_ped->cantidad;
                                $full_fisicas+=$det_ped->cantidad;
                            }
                        }
                    @endphp
                    {{$full_fisicas}}
                    @php $total_full_equivalente += $full_equivalente_real;
                         $total_full_fisicas+= $full_fisicas;
                    @endphp
                </td>
                <td style="border-color: #9d9d9d" class="text-center">
                    {{$full_equivalente_real}}
                </td>
                <td style="border-color: #9d9d9d;padding:0;vertical-align: middle;" class="text-center" >
                    <a target="_blank" href="{{url('comprobante/documento_pre_factura',[$comprobante->secuencial,true])}}" class="btn btn-info btn-xs" title="Ver factura Cliente">
                        <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3" ></td>
            <td class="text-center"> <b> Total: </b> </td>
            <td class="text-center"><b>{{$total_full_fisicas}} </b></td>
            <td class="text-center"> <b>{{$total_full_equivalente}} </b></td>
        </tr>
    </table>
    <div class="text-center">
        <buttom class="btn btn-primary" title="Guardar orden de facturas" onclick="update_orden_factura()">
            <i class="fa fa-floppy-o"></i> Guardar
        </buttom>
    </div>
@else
    <div class="alert alert-info text-center">No se han encontrado pedidos con facturas generadas</div>
@endif
</div>
<script>
    $('.item_factura').draggable({
        helper: 'clone',
        zIndex: 1000
    });
    $('.orden_factura').droppable({
        accept: '.item_factura',
        hoverClass: 'hovering',
        drop: function (ev, ui) {
            var element = $(ui.helper).clone();
            id_element = element[0].innerText.trim();
            console.log(id_element);
            $(this).empty();
            $(this).append(element);
            $(".item_factura").css({
                'position': 'relative',
                'z-index': '1000',
                'width': '100%',
                'right': 'auto',
                'height': '15px',
                'bottom': 'auto',
                'left': 0,
                'top': 0,
            });
            $( "div#"+id_element).draggable().draggable('disable');
        }
    });

    function delete_item_factura(td) {
        var id = $(td)[0].childNodes[0].childNodes[1].defaultValue;
        $("div#"+id).draggable().draggable('enable');
        $(td).empty()
    }
</script>
