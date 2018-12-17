<div class="row" id="rows">
    <div class="form-group col-md-1 text-center">
        <label for="envio"> Envío  N# {{$rows}}</label>
    </div>
    <div class="form-group col-md-4">
        <div class="input-group">
              <span class="input-group-addon" style="background-color: #e9ecef">Agencia de transporte</span>
            <select class="form-control" id="id_agencia_transporte_{{$form}}_{{$rows}}" name="id_agencia_transporte_{{$form}}_{{$rows}}" required>
              <!--  <option selected disabled> Seleccione </option>-->
                @foreach($agencia_transporte as $at)
                    <option value="{{$at->id_agencia_transporte}}"> {{$at->nombre}} </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group col-md-3">
        <div class="input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Cantidad</span>
            <select class="form-control" id="cantidad_{{$form}}_{{$rows}}" name="cantidad_{{$form}}_{{$rows}}" required>
                <option selected disabled> Seleccione </option>
                 {{--@if($rows == 1)
                    @for($j=0; $j<$cantidad; $j++)
                        <option value="{{$j+1}}"> {{$j+1}} </option>
                    @endfor
                 @endif--}}
            </select>
        </div>
    </div>
    <div class="form-group col-md-4">
        <div class="input-group">
            <span class="input-group-addon" style="background-color: #e9ecef">Envío</span>
            <select class="form-control" id="envio_{{$form}}_{{$rows}}" name="envio_{{$form}}" onchange="change_agencia_transporte(this)" required>
                <option id="seleccione" value="{{$form}}"> Mismo envío </option>
            </select>
        </div>
    </div>
</div>
