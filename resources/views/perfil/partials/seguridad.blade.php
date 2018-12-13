<form id="form_edit_password">
    <div class="row">
        <div class="col-md-6">
            <label for="password_current">Contraseña actual</label>
            <div class="form-group input-group has-feedback" style="width: 100%">
                    <span class="input-group-btn">
                        <button type="button" class="btn text-black" title="Mostrar/ocultar" id="btn_mostrar_ocultar_current"
                                onclick="mostrar_ocultar_passw('password_current')">
                            <i class="fa fa-fw fa-eye"></i>
                        </button>
                    </span>
                <input type="password" name="password_current" id="password_current" class="form-control" placeholder="Contraseña" required
                       maxlength="250" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="password">Nueva contraseña</label>
            <div class="form-group input-group has-feedback" style="width: 100%">
                    <span class="input-group-btn">
                        <button type="button" class="btn text-black" title="Mostrar/ocultar" id="btn_mostrar_ocultar_reg"
                                onclick="mostrar_ocultar_passw('password')">
                            <i class="fa fa-fw fa-eye"></i>
                        </button>
                    </span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required
                       maxlength="250" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
        </div>
        <div class="col-md-6">
            <label for="password_b">Repite la contraseña</label>
            <div class="form-group has-feedback">
                <input type="password" name="password_b" id="password_b" class="form-control" placeholder="Repite la contraseña"
                       required maxlength="250">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
        </div>
    </div>
    <input type="hidden" name="h_clave1" id="h_clave1" value="">
    <input type="hidden" name="h_clave" id="h_clave" value="">
    <div class="row">
        <div class="col-md-3 col-md-offset-9">
            <button type="button" class="btn btn-success btn-block" onclick="update_password()">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        </div>
    </div>
</form>