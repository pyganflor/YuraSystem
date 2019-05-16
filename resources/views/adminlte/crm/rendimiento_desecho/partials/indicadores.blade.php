<div class="row">
    <div class="col-md-2">
        <div class="small-box bg-teal-active">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($semanal['cosecha']['rendimiento'], 2)}}
                    <small style="color: white">t/hr</small>
                </h3>
            </div>
            <div class="icon">
                <i class="ion ion-ios-rose"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('cosecha')">
                Cosecha <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-green">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($semanal['verde']['rendimiento'], 2)}}
                    <small style="color: white">t/hr</small>
                    <br>
                    {{number_format($semanal['verde']['desecho'], 2)}}
                    <small style="color: white">%</small>
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-leaf"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" onclick="desglose_indicador('verde')">
                Verde <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box" style="background-color: white; color: black">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($semanal['blanco']['rendimiento'], 2)}}
                    <small style="color: black">r/hr</small>
                    <br>
                    {{number_format($semanal['blanco']['desecho'], 2)}}
                    <small style="color: black">%</small>
                </h3>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-gift"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer text-black" onclick="desglose_indicador('blanco')">
                Blanco <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-2">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format(round(($semanal['blanco']['rendimiento'] + $semanal['verde']['rendimiento_ramos'])/2,2) , 2)}}
                    <small style="color: white">r/hr</small>
                </h3>
            </div>
            <div class="icon">
                <i class="ion ion-ios-people-outline"></i>
            </div>
            <span class="small-box-footer text-white">
                Rendimiento
            </span>
        </div>
    </div>
    <div class="col-md-2">
        <div class="small-box bg-red-gradient">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format(round(($semanal['blanco']['desecho'] + $semanal['verde']['desecho'])/2,2) , 2)}}
                    <small style="color: white">%</small>
                </h3>
            </div>
            <div class="icon">
                <i class="ion ion-ios-trash-outline"></i>
            </div>
            <span class="small-box-footer text-white">
                Desecho
            </span>
        </div>
    </div>
</div>