@foreach($variedades as $var)
    <div class="small-box" style="background-color: {{$var['variedad']->color}}">
        <div class="inner">
            <h3 class="info-box-number" style="color: white">
                @if($var['area_anual'] > 0)
                    {{number_format(round(($var['venta_mensual'] / round($var['area_anual'] * 10000, 2)), 2), 2) * 3}}
                @else
                    0
                @endif
                <small style="color: white; font-size: 0.5em">
                    (4 meses)
                </small>
            </h3>
            <h3 class="info-box-number" style="color: white">
                @if($var['area_anual'] > 0)
                    {{number_format(round(($var['venta_anual'] / round($var['area_anual'] * 10000, 2)), 2), 2)}}
                @else
                    0
                @endif
                <small style="color: white; font-size: 0.5em">
                    (1 año)
                </small>
            </h3>
        </div>
        <div class="icon">
            <i class="fa fa-fw fa-usd"></i>
        </div>
        <span class="small-box-footer">
            <strong>{{$var['variedad']->nombre}}</strong>
            <small class="pull-left" style="color: white; margin-left: 5px">$/m<sup>2</sup>/año</small>
        </span>
    </div>
@endforeach