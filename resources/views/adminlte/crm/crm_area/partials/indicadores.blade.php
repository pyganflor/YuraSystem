<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-teal-active">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format(round($mensual['area'] / 10000, 2), 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-cube"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('area')">
                Área <sup>ha</sup> <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($mensual['ciclo'], 2)}}
                    <small>({{round(365 / $mensual['ciclo'],2)}})</small>
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
                    {{number_format($mensual['tallos'], 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-leaf"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('tallos')">
                Tallos/m<sup>2</sup> <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-2">
        <div class="small-box bg-green-gradient">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($mensual['ramos'], 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-leaf"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('ramos')">
                Ramos/m<sup>2</sup> <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-2">
        <div class="small-box bg-red">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($mensual['ramos_anno'], 2)}}
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-leaf"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('ramos_anno')">
                Ramos/m<sup>2</sup>/año <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>