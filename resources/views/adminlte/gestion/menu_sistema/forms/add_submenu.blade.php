<form id="form_add_submenu">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="id_grupo_menu">Grupo</label>
                <select name="id_grupo_menu" id="id_grupo_menu" required class="form-control" onchange="listar_menus_x_grupo($(this).val())">
                    <option value="">Seleccione</option>
                    @foreach($grupos as $g)
                        <option value="{{$g->id_grupo_menu}}">{{$g->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6" id="div_input_menus">
            <div class="form-group">
                <label for="id_menu">Menú</label>
                <select name="id_menu" id="id_menu" required class="form-control">
                    <option value="">Seleccione un grupo</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="50" autocomplete="off">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="url">Ruta</label>
                <input type="text" id="url" name="url" class="form-control" required maxlength="25" autocomplete="off"
                       placeholder="Formato: ruta_ejemplo" pattern="([a-z])\w+">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value="N">Normal</option>
                    <option value="C">CRM</option>
                    <option value="R">Reporte</option>
                </select>
            </div>
        </div>
    </div>
</form>

<script>
    $('#form_add_submenu').validate({
        messages: {
            url: {
                pattern: 'Formato incorrecto para la ruta'
            },
        }
    });
</script>