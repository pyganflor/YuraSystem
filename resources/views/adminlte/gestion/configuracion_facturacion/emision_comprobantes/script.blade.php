<script>

    function listar_punto_emision() {
        $.LoadingOverlay('show');
        datos = {
            cant_punto_emision: $('#empleados_facturar').val(),
        };
        $.get('{{url('emision_comprobantes/add_punto_emision')}}',datos, function (retorno) {
            $("#punto_emision").html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function store_punto_acceso(){

        if ($('#form_comprobante_emision').valid()) {
            $.LoadingOverlay('show');
            arrPuntosEmision = [];
            $.each($('input[name=punto_emision]'), function (i, j) {
                arrPuntosEmision.push([
                    j.value,
                    $("#id_usuario_"+(i+1)).val(),
                ]);
            });
            console.log(arrPuntosEmision);
            datos = {
                _token          : '{{csrf_token()}}',
                arrPuntosEmision: arrPuntosEmision
            };
            post_jquery('{{url('emision_comprobantes/store_punto_emision')}}', datos, function (retorno) {
                modal_view('modal_view_punto_de_acceso', retorno, '<i class="fa fa-fw fa-table"></i>Estado puntos de accesos', true, false, '{{isPC() ? '50%' : ''}}');
            });
            $.LoadingOverlay('hide');
        }
    }
</script>
