@if($comprobante != '')
ok
@else
    <div class="alert alert-warning text-center">
        Este pedido aún no tiene comprobante generado.
    </div>
@endif