@if(count($listado)>0)
    <legend>
        <span class="pull-right badge" id="html_current_sacar" title="Ramos seleccionados" style="margin-right: 5px"></span>
        <a href="javascript:void(0)" class="badge btn-success pull-right" id="btn_sacar" title="Sacar de apertura"
           style="display: none; margin-right: 5px" onclick="sacar_aperturas()">
            <i class="fa fa-fw fa-share-square-o"></i> Sacar
        </a>
    </legend>
    <script>
        function sacar_aperturas() {
            listado = $('.checkbox_sacar');
            arreglo = [];

            tallos_x_coche = $('#tallos_x_coche').val();
            if (tallos_x_coche == '' || tallos_x_coche <= 0)
                tallos_x_coche = 1;

            for (i = 0; i < listado.length; i++) {
                if (listado[i].checked) {
                    id = listado[i].id.substr(15);
                    pos = $('#pos_pedido').val();

                    factor = $('#factor_calibre_unitario_' + id).val();
                    seleccionados = parseFloat($('#sacar_' + id).val()) * tallos_x_coche;
                    cantidad_seleccionada = (Math.round((seleccionados / factor) * 100) / 100);

                    data = {
                        id_stock_apertura: id,
                        dias_maduracion: $('#dias_maduracion_' + id).val(),
                        cantidad_ramos_estandar: cantidad_seleccionada,
                        fecha_pedido: $('#val_fecha_' + pos).val()
                    };
                    arreglo.push(data);
                }
            }

            datos = {
                _token: '{{csrf_token()}}',
                arreglo: arreglo
            };
            post_jquery('{{url('apertura/sacar')}}', datos, function () {
                buscar_listado();
                buscar_pedidos();
            });
        }
    </script>
@else
    <p class="text-center">
        No se han encontrado pedidos en el rango de tiempo especificado
    </p>
@endif