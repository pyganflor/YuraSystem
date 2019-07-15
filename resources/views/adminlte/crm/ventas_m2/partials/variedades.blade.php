@foreach($variedades as $var)
    <div class="small-box bg-green">
        <div class="inner">
            <h3>
                {{number_format(round(($var['venta'] / $var['area_cerrada']) * $var['ciclo_anno'], 2), 2)}}
                <sup>s/m<sup>2</sup>/año</sup>
            </h3>

            <p>anual</p>
        </div>
        <div class="icon">
            <i class="fa fa-fw fa-usd"></i>
        </div>
        <span class="small-box-footer">{{$var['variedad']->nombre}}</span>
    </div>
@endforeach