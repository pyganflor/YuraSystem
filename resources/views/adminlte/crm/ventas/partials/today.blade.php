<p class="text-center">
    <strong>
        Pedidos del día
    </strong>
</p>
<div class="row">
    <div class="col-sm-6 col-xs-6">
        <div class="description-block border-right">
                {{--<span class="description-percentage text-orange">
                    <i class="fa fa-caret-left"></i>
                    0 %
                </span>--}}
            <h5 class="description-header">
                {{$today['cajas']}}
            </h5>
            <span class="description-text">Cajas</span>
        </div>
    </div>
    <div class="col-sm-6 col-xs-6">
        <div class="description-block border-right">
                {{--<span class="description-percentage text-orange">
                    <i class="fa fa-caret-left"></i>
                    0 %
                </span>--}}
            <h5 class="description-header">
                ${{$today['valor']}}
            </h5>
            <span class="description-text">Valor</span>
        </div>
    </div>
</div>