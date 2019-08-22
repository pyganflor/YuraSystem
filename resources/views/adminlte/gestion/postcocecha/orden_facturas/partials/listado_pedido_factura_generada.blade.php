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
                    <td style="border-color: #9d9d9d;cursor: pointer" class="text-center">
                        <div class="item_factura">
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
                Fecha
            </th>
        </tr>
        </thead>
        @foreach($comprobantes as $comprobante)
            <tr onmouseover="$(this).css('background-color','#add8e6')" onmouseleave="$(this).css('background-color','')">
                <td style="border-color: #9d9d9d;width:100px;cursor:pointer;" class="text-center orden_factura"
                    title="Haga clic para eliminar" onclick="delete_item_factura(this)"></td>
                <td style="border-color: #9d9d9d;" class="text-center">{{$comprobante->secuencial}}</td>
                <td style="border-color: #9d9d9d" class="text-center">{{$comprobante->envio->pedido->cliente->detalle()->nombre}}</td>
                <td style="border-color: #9d9d9d" class="text-center">{{\Carbon\Carbon::parse($comprobante->fecha_emision)->format('d/m/Y')}}</td>
            </tr>
        @endforeach
        <tr>
            <td class="text-center" colspan="4">
                <buttom class="btn btn-primary" title="Guardar orden de facturas">
                    <i class="fa fa-floppy-o"></i> Guardar
                </buttom>
            </td>
        </tr>
    </table>
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
        }
    });

    function delete_item_factura(td) {
        $(td).empty()
    }
</script>
