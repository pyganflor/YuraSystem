<form id="form_add_codigo_dae" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-7">
            <div class="form-group">
                <label for="nombre_comprobante">Archivo</label>
                <input type="file"  id="file" name="file" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="id_configuracion_empresa">Empresa</label>
                <select id="id_configuracion_empresa" name="id_configuracion_empresa" class="form-control">
                    @foreach($empresas as $emp)
                        <option value="{{$emp->id_configuracion_empresa}}">{{$emp->razon_social}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</form>
