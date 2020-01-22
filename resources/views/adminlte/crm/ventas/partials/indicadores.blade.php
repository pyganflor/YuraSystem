<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-teal-active">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($precioXTallo, 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-usd"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('valor')">
                Valor $ <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($precioPromedioRamo, 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-gift"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('cajas')">
                Cajas <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($cajasEquivalentes, 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-usd"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('precios')">
                Precio x Ramo <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-green-gradient">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($dinero, 2)}}
                    <sup style="font-size: 0.4em"></sup>
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-usd"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('tallos')">
                Precio x Tallo <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
