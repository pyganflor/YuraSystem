<form id="form_add_cliente">
    <input type="hidden" id="id_marca" value=" {!! !empty($dataMarca->nombre) != '' ? $dataMarca->id_marca : '' !!}">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="nombre_marca">Nombre</label>
                <input type="text" id="marca" name="nmarca" class="form-control" required maxlength="250" autocomplete="off" value='{!! !empty($dataMarca->nombre) != '' ? $dataMarca->nombre : '' !!}'>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="identificacion">Descripción</label>
                <input type="text" id="descripcion" name="descripcion" class="form-control" required maxlength="25" autocomplete="off" value="{!! !empty($dataMarca->descripcion) != '' ? $dataMarca->descripcion : '' !!}">
            </div>
        </div>
    </div>
</form>
