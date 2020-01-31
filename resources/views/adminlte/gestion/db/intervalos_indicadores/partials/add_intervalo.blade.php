<div class="text-right" style="margin-bottom: 10px">
    {{--@dump(getIntervalosIndicador('D14'))--}}
    <button class="btn btn-primary btn-xs" title="Agregar intervalo de rango" onclick="add_row('rango')">
        <i class="fa fa-plus"></i>
    </button>
    {{--<button class="btn btn-success btn-xs" title="Agregar intervalo de condición" onclick="add_row('condicion')">
        <i class="fa fa-plus"></i>
    </button>--}}
</div>
<form id="form_add_intervalo" class="form_rows_intervalos">
    <input type="hidden" id="id_indicador" value="{{$indicador}}">
    <div id="alert_intervalo" class="alert alert-info text-center {{(isset($intervalos_indicadores) && $intervalos_indicadores->count() > 0) ? "hide": ""}}">
        Ingrese al menos un intervalo
    </div>
    @foreach($intervalos_indicadores as $x => $intervalo_indicador)
        @if($intervalo_indicador->tipo ==="C")
            <div class="row" id="row_input_{{$x+1}}">
                <input type="hidden" class="tipo" value="C">
                <div class="col-md-4">
                    <div class="">
                        <label for="condicional_{{$x+1}}">Condicional</label>
                        <select id="condicional_{{$x+1}}" name="condicional_{{$x+1}}" class="form-control condicional text-center" required>
                            <option value="=<" {{$intervalo_indicador->condicion == '=<' ? 'selected' : ''}}> Mayor o igual que</option>
                            <option value="=>" {{$intervalo_indicador->condicion == '=>' ? 'selected' : ''}}> Menor o igual que</option>
                            <option value="=" {{$intervalo_indicador->condicion == '='  ? 'selected' : ''}}> Igual que</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="">
                        <label for="cantidad_{{$x+1}}">Cantidad</label>
                        <input type="number" id="cantidad_{{$x+1}}" name="cantidad_{{$x+1}}" class="form-control cantidad text-center"
                               required value="{{$intervalo_indicador->hasta}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="">
                        <label for="id_color_{{$x+1}}"> Color {{--<span style="width: 79px;height: 10px;float: right;margin-top: 6px;margin-left: 10px;" id="color_{{$x+1}}"></span>--}} </label>
                        <input type="color" class="form-control color" id="id_color_{{$x+1}}" name="id_color_{{$x+1}}" value="{{$intervalo_indicador->color}}">
                    </div>
                </div>
                <div class="text-center">
                    <div class="">
                        <label> Acción </label>
                        <button type="button" class="btn btn-danger" title="Eliminar rango" id="{{$x+1}}" onclick="delete_row(this.id)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <hr style="margin: 3px 0 3px 0;"/>
            </div>
        @else
            <div class="row" id="row_input_{{$x+1}}">
                <input type="hidden" class="tipo" value="I">
                <div class="col-md-4">
                    <div class="">
                        <label for="desde_{{$x+1}}">Desde</label>
                        <input type="number" id="desde_{{$x+1}}" name="desde_{{$x+1}}" class="form-control desde text-center" required
                                autocomplete="off" value='{{$intervalo_indicador->desde}}'>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="">
                        <label for="hasta">Hasta</label>
                        <input type="number" id="hasta_{{$x+1}}" name="hasta_{{$x+1}}" class="form-control hasta text-center"
                               required autocomplete="off" value="{{$intervalo_indicador->hasta}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="">
                        <label for="id_color_{{$x+1}}"> Color</label>
                        <input type="color" class="form-control color" id="id_color_{{$x+1}}" name="id_color_{{$x+1}}"
                               value="{{$intervalo_indicador->color}}">
                    </div>
                </div>
                <div class="text-center">
                    <div class="">
                        <label> Acción </label>
                        <button type="button" class="btn btn-danger" title="Eliminar rango" id="{{$x+1}}" onclick="delete_row(this.id)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <hr style="margin: 3px 0 3px 0;"/>
            </div>
        @endif
    @endforeach
</form>
