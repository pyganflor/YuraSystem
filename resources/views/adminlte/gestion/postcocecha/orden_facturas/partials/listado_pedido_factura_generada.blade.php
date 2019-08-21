<div class="col-md-2">
    @if($comprobantes->count()>0)
        <table width="100%" class="table table-responsive table-bordered" style="font-size: 0.8em; border-color: #9d9d9d">
            <thead>
            <tr style="background-color: #dd4b39; color: white">
                <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;">
                    N° de factura
                </th>
            </tr>
            </thead>
            @foreach($comprobantes as $comprobante)
                <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                    <td style="border-color: #9d9d9d" class="text-center">
                        <input type="hidden" id="n_factura" name="n_facutra" value="{{$comprobante->secuencial}}">
                        {{$comprobante->secuencial}}
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
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d;width:70%">
                Factura
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                Cliente
            </th>
            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}" style="border-color: #9d9d9d">
                Fecha
            </th>
        </tr>
        </thead>
        @foreach($comprobantes as $comprobante)
                  <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                           <td style="border-color: #9d9d9d" class="text-center">
                               {{$comprobante->secuencial}}
                           </td>
                           <td style="border-color: #9d9d9d" class="text-center">
                               {{$comprobante->envio->pedido->cliente->detalle()->nombre}}
                           </td>
                           <td style="border-color: #9d9d9d" class="text-center">
                               {{$comprobante->fecha_emision}}git add -A
                           </td>
                       </tr>
               @endforeach
    </table>
@else
    <div class="alert alert-info text-center">No se han encontrado pedidos con facturas generadas</div>
@endif
</div>

