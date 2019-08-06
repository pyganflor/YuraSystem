<script>
    listar_productos_vinculados();
    function listar_productos_vinculados() {
        $.LoadingOverlay('show');
        $.get('{{url('producto_venture/listar_productos_vinculados')}}', datos, function (retorno) {
            $('#div_listado_codigo_prodcutos').html(retorno);
            estructura_tabla('table_productos_viculados');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function vincular_productos_venture() {
        if($("#vicular_producto_venture_yura").valid()){
            $.LoadingOverlay('show');
            datos = {
                presentacion_yura_sistem : $("#presentacion_yura_system").val(),
                presentacion_venture : $("#presentacion_venture").val(),
                _token : '{{csrf_token()}}'
            };
            post_jquery('{{url('producto_venture/vincular_yura_system_venture')}}', datos, function () {
                location.reload();
                cerrar_modals();
            });
            $.LoadingOverlay('hide');
        }
    }

    function delete_vinculacion(id_vinculacion) {
        modal_quest('modal_delete_vinculacion', "<div class='alert alert-warning text-center'> '¿Esta seguro que desea eliminar la vinculación ed este producto con el venture?</div>", 'Eliminiar vinculación', true, false, '40%', function () {
            $.LoadingOverlay('show');
            datos = {
                id_vinculacion: id_vinculacion,
                _token: '{{csrf_token()}}'
            };
            post_jquery('{{url('producto_venture/delete_vinculados')}}', datos, function () {
                listar_productos_vinculados();
                cerrar_modals();
            });
            $.LoadingOverlay('hide');
        });
    }
</script>
