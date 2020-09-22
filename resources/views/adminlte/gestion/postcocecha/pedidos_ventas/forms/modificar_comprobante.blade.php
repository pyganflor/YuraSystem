@if($comprobante != '')
    <div class="input-group">
        <span class="input-group-addon" style="background-color: #e9ecef">
            Fecha Emisión
        </span>
        <input type="text" readonly value="{{$comprobante->fecha_emision}}" class="form-control">
        <span class="input-group-addon" style="background-color: #e9ecef">
            Secuencial
        </span>
        <input type="text" id="secuencial_comprobante" maxlength="15" value="{{$comprobante->secuencial}}" class="form-control"
               onkeyup="return isNumber(event)">
        <span class="input-group-btn">
            <button type="button" class="btn btn-primary" onclick="update_comprobante('{{$comprobante->id_comprobante}}')">
                <i class="fa fa-fw fa-check"></i>
            </button>
        </span>
    </div>
@else
    <div class="alert alert-warning text-center">
        Este pedido aún no tiene comprobante generado.
    </div>
@endif

<script>
    function update_comprobante(comprobante) {
        datos = {
            _token: '{{csrf_token()}}',
            id_comprobante: comprobante,
            secuencial: $('#secuencial_comprobante').val()
        };
        post_jquery('{{url('pedidos/update_comprobante')}}', datos, function () {
            cerrar_modals();
            listar_resumen_pedidos(document.getElementById('fecha_pedidos_search').value,
                true, document.getElementById('id_configuracion_pedido').value,
                document.getElementById('id_cliente').value);
        });
    }
</script>