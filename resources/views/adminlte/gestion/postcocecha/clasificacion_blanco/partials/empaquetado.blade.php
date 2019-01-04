<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Ingrese las cantidades de ramos empaquetados por cada variedad
        </h3>
    </div>
    <div class="box-body">
        <small>
            Estos datos corresponden a los pedidos del
            día <strong>{{getDias()[transformDiaPhp(date('w',strtotime($fecha)))]}} {{convertDateToText($fecha)}}</strong>
        </small>
        <table class="table table-responsive table-bordered" style="border: 1px solid #9d9d9d; margin-bottom: 0; font-size: 0.8em"
               onmouseover="$(this).css('border-color','#ADD8E6')"
               onmouseleave="$(this).css('border-color','#9d9d9d')">
            @foreach($stock_frio as $frio)
                <tr>
                    <th style="border-color: #9d9d9d; background-color: #e9ecef" width="50%">
                        {{getVariedad($frio->id_variedad)->nombre}}
                    </th>
                    <td class="text-center" style="border-color: #9d9d9d" width="25%"
                        title="Ramos sacados de aperturas para el empaquetado de los pedidos de esta fecha">
                        {{round($frio->cantidad_ingresada,2)}}
                    </td>
                    <td style="border-color: #9d9d9d" width="25%">
                        <input type="number" id="empaquetar_{{$frio->id_variedad}}" class="text-center">
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>