<script>
    estructura_tabla('table_cuarto_frio', false);

    function editar_dia(pos_inv, pos_dia) {
        $('#span_editar_' + pos_inv + '_' + pos_dia).hide();
        $('#input_editar_' + pos_inv + '_' + pos_dia).show();
        $('#input_editar_' + pos_inv + '_' + pos_dia).focus();

        $('#input_accion_' + pos_inv + '_' + pos_dia).val('E');

        $('#btn_save_' + pos_inv).show();
    }

    function add_dia(pos_inv, pos_dia) {
        $('#span_editar_' + pos_inv + '_' + pos_dia).hide();
        $('#input_add_' + pos_inv + '_' + pos_dia).show();
        $('#input_add_' + pos_inv + '_' + pos_dia).focus();

        $('#input_accion_' + pos_inv + '_' + pos_dia).val('A');

        $('#btn_save_' + pos_inv).show();
    }

    function editar_inventario(pos_inv) {
        datos = {}
    }
</script>