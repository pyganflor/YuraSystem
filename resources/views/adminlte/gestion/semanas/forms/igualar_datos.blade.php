<form id="form-igualar_datos">
    <div class="row">
        @if($curva == 'true')
            <div class="col-md-6">
                <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef;">
                    Curva
                </span>
                    <input type="text" id="curva" name="curva" class="form-control" maxlength="11" required
                           pattern="^\d{2}-\d{2}-\d{2}-\d{2}$"
                           placeholder="10-20-40-30">
                </div>
            </div>
        @else
            <input type="hidden" id="curva" name="curva" value="">
        @endif
        @if($desecho == 'true')
            <div class="col-md-6">
                <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef;">
                    Desecho %
                </span>
                    <input type="number" class="text-center form-control" name="desecho" id="desecho" required maxlength="2" min="0"
                           max="99">
                </div>
            </div>
        @else
            <input type="hidden" id="desecho" name="desecho" value="">
        @endif
    </div>
    <div class="row">
        @if($semana_poda == 'true')
            <div class="col-md-6">
                <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef;">
                    Semana Poda
                </span>
                    <input type="number" class="text-center form-control" name="semana_poda" id="semana_poda" required maxlength="2" min="1"
                           max="53">
                </div>
            </div>
        @else
            <input type="hidden" id="semana_poda" name="semana_poda" value="">
        @endif
        @if($semana_siembra == 'true')
            <div class="col-md-6">
                <div class="form-group input-group">
                <span class="input-group-addon" style="background-color: #e9ecef;">
                    Semana Siembra
                </span>
                    <input type="number" class="text-center form-control" name="semana_siembra" id="semana_siembra" required maxlength="2"
                           min="1"
                           max="53">
                </div>
            </div>
        @else
            <input type="hidden" id="semana_siembra" name="semana_siembra" value="">
        @endif
    </div>
</form>