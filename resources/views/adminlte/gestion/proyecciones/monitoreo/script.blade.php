<script>
    select_planta($('#filtro_predeterminado_planta').val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades', '<option value="T" selected>Todos los tipos</option>');
    listar_ciclos();

    function listar_ciclos() {
        datos = {
            sector: $('#filtro_sector').val(),
            variedad: $('#filtro_predeterminado_variedad').val(),
            num_semanas: $('#filtro_num_semanas').val(),
            poda_siembra: $('#filtro_poda_siembra').val(),
            min_semanas: $('#filtro_min_semanas').val(),
        };
        if (datos['variedad'] != 'T') {
            get_jquery('{{url('monitoreo_ciclos/listar_ciclos')}}', datos, function (retorno) {
                $('#div_listado_ciclos').html(retorno);
            }, 'div_listado_ciclos');
        }
    }

    function mouse_over_celda(id, action) {
        $('.celda_hovered').css('border', '1px solid #9d9d9d');
        if (action == 1) {  // over
            $('#' + id).css('border', '2px solid black');
        }
    }

</script>
