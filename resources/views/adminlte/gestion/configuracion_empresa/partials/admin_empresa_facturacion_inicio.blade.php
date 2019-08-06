<section class="">
    <div class="box box-info">
        <div class="box-header with-border">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <select id="id_configuracion_empresa_facturacion" name="id_configuracion_emppresa_facturacion"
                                onchange="selected_configuracion_empresa()" class="form-control">
                            @foreach($config_empresa as $ce)
                                <option value="{{$ce->id_configuracion_empresa}}">{{$ce->razon_social}}</option>
                            @endforeach
                        </select>
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="button" onclick="nueva_empresa()"
                                    title="Crear nueva empresa para facturación">
                                <i class="fa fa-plus-circle"></i>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body" id="div_content_permisos">
            <div class="row">
                <div class="col-md-12" id="div_content_form_config_empresa_facturacion">
                </div>
            </div>
        </div>
    </div>
</section>
