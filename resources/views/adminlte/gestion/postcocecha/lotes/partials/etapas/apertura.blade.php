<li class="time-label">
    <span class="{{$lote->etapa == 'A' ? 'bg-green' : 'bg-gray-active'}}">
        {{$lote->apertura != '' ? convertDateToText($lote->apertura) : '----/--/--'}}
    </span>
</li>
<li>
    <i class="fa fa-cube"></i>

    <div class="timeline-item" style="border: 1px solid #9d9d9d">

        <h3 class="timeline-header"><strong>Apertura</strong> clasificación</h3>

        <div class="timeline-body sombra_estandar">
            @if($lote->etapa == 'A')
                <form id="form-etapa_A">
                    <small>Para pasar este lote a la siguiente etapa llene los datos siguientes</small>
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">Fecha</span>
                        <input type="date" id="fecha_A" name="fecha_A" class="form-control" required>
                        <span class="input-group-addon" style="background-color: #e9ecef">Días</span>
                        <input type="number" id="dias_A" name="dias_A" class="form-control" required
                               onkeypress="return isNumber(event)" min="{{$lote->variedad->minimo_apertura}}"
                               max="{{$lote->variedad->maximo_apertura}}" value="{{$lote->variedad->estandar_apertura}}">
                        <span class="input-group-btn" title="Guardar">
                            <button type="button" class="btn btn-success" onclick="store_etapa('A')">
                                <i class="fa fa-fw fa-save"></i>
                            </button>
                        </span>
                    </div>
                </form>
            @else
                @if($lote->apertura != '')
                    Estuvo en esta etapa durante <strong>{{$lote->getEstanciaByEtapa('A')}}</strong> días hasta el
                    <strong>{{convertDateToText($lote->apertura)}}</strong>
                @else
                    <div class="text-center">
                        <p>Nada que hacer en esta etapa</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</li>