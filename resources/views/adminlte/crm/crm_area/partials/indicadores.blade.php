<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-teal-active">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($semanal['area'], 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-cube"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('area')">
                Área m <sup>2</sup> <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($semanal['ciclo'], 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-refresh"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('ciclo')">
                Ciclo <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-2">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($semanal['area'], 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-usd"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('area')">
                Precio x Ramo <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-2">
        <div class="small-box bg-green-gradient">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($semanal['area'], 2)}}
                    <sup style="font-size: 0.4em"></sup>
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-usd"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('area')">
                Precio x Tallo <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-2">
        <div class="small-box bg-red">
            <div class="inner">
                <h3 class="info-box-number">
                    ?
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-question"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer">
                ? <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>