<form id="form_add_usuario">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="id_usuario">Usuario</label>
                <select name="id_usuario" id="id_usuario" class="form-control" required>
                    <option value="">Seleccione el usuario</option>
                    @foreach($usuarios as $item)
                        <option value="{{$item->id_usuario}}">{{$item->nombre_completo}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</form>