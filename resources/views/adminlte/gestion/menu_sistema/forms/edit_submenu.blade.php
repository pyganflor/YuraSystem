<form id="form_edit_submenu">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="id_grupo_menu">Grupo</label>
                <select name="id_grupo_menu" id="id_grupo_menu" required class="form-control"
                        onchange="listar_menus_x_grupo($(this).val())">
                    <option value="">Seleccione</option>
                    @foreach($grupos as $g)
                        <option value="{{$g->id_grupo_menu}}" {{$submenu->menu->id_grupo_menu == $g->id_grupo_menu ? 'selected' : ''}}>
                            {{$g->nombre}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6" id="div_input_menus">
            <div class="form-group">
                <label for="id_menu">Menú</label>
                <select name="id_menu" id="id_menu" required class="form-control">
                    <option value="{{$submenu->id_menu}}">{{$submenu->menu->nombre}}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required maxlength="50" autocomplete="off"
                       value="{{$submenu->nombre}}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="url">Ruta</label>
                <input type="text" id="url" name="url" class="form-control" required maxlength="25" autocomplete="off"
                       placeholder="Formato: ruta_ejemplo" pattern="([a-z])\w+" value="{{$submenu->url}}"
                       onfocus="$('#texto_alerta_url').removeClass('hidden');"
                        {{$submenu->url != explode('/',substr(Request::getRequestUri(),1))[0] ? '' : 'disabled'}}>
            </div>
            <p class="error {{$submenu->url != explode('/',substr(Request::getRequestUri(),1))[0] ? 'hidden' : ''}} text-justify"
               id="texto_alerta_url"><i class="fa fa-fw fa-exclamation-triangle"></i> Cambiar la ruta
                puede provocar problemas en el acceso a los datos</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="tipo">Tipo</label>
                <select name="tipo" id="tipo" class="form-control" required>
                    <option value="N" {{$submenu->tipo == 'N' ? 'selected' : ''}}>Normal</option>
                    <option value="C" {{$submenu->tipo == 'C' ? 'selected' : ''}}>CRM</option>
                    <option value="R" {{$submenu->tipo == 'R' ? 'selected' : ''}}>Reporte</option>
                </select>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_submenu" name="id_submenu" value="{{$submenu->id_submenu}}">
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