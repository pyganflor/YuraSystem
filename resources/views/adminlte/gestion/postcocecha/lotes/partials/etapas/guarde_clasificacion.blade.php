<li class="time-label">
    <span class="{{$lote->etapa == 'C' ? 'bg-green' : 'bg-gray-active'}}">
        {{$lote->guarde_clasificacion != '' ? convertDateToText($lote->guarde_clasificacion) : '----/--/--'}}
    </span>
</li>
<li>
    <i class="fa fa-cube"></i>

    <div class="timeline-item" style="border: 1px solid #9d9d9d">

        <h3 class="timeline-header"><strong>Guarde</strong> clasificación</h3>

        <div class="timeline-body sombra_estandar">
            @if($lote->etapa == 'C')
                <form id="form-etapa_C">
                    <small>Para pasar este lote a la siguiente etapa llene los datos siguientes</small>
                    <div class="form-group input-group">
                        <span class="input-group-addon" style="background-color: #e9ecef">Fecha</span>
                        <input type="date" id="fecha_C" name="fecha_C" class="form-control" required>
                        <span class="input-group-addon" style="background-color: #e9ecef">Días</span>
                        <input type="number" id="dias_C" name="dias_C" class="form-control" required
                               onkeypress="return isNumber(event)" min="{{$lote->variedad->minimo_apertura}}"
                               max="{{$lote->variedad->maximo_apertura}}" value="{{$lote->variedad->estandar_apertura}}">
                        <span class="input-group-btn" title="Guardar">
                            <button type="button" class="btn btn-success" onclick="store_etapa('C')">
                                <i class="fa fa-fw fa-save"></i>
                            </button>
                        </span>
                    </div>
                </form>
            @else
                @if($lote->guarde_clasificacion != '')
                    Estuvo en esta etapa durante <strong>{{$lote->getEstanciaByEtapa('C')}}</strong> días hasta el
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