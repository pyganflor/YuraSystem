 @if(count($listado)>0)
  <legend class="text-center">
    Pedidos
  </legend>
  <div class="accordion well" id="accordionExample">
  @foreach($listado as $fecha)
  <div class="card">
    <div class="card-header text-center" id="headingOne">
      <h5 class="mb-0">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne_{{$fecha->fecha_pedido}}" aria-expanded="false" aria-controls="collapseOne_{{$fecha->fecha_pedido}}">
          {{getDias()[transformDiaPhp(date('w', strtotime($fecha->fecha_pedido)))]}} 
  		{{convertDateToText($fecha->fecha_pedido)}}
        </button>
      </h5>
    </div>

    <div id="collapseOne_{{$fecha->fecha_pedido}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
      <div class="card-body text-center">
        
      </div>
    </div>
  </div>
  @endforeach
</div>
@else
<p class="text-center">
	No se han encontrado pedidos en el rango de tiempo especificado
</p>
@endif