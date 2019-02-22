@if(count($labels) > 0)
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3 class="info-box-number" title="Cajas Equivalentes">
                        {{$cajas}}
                        <sup style="font-size: 0.4em" title="Ramos">
                            {{$ramos}} ramos
                        </sup>
                    </h3>
                </div>
                <div class="icon">
                    <i class="ion ion-leaf"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer">
                    Cosecha <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3 class="info-box-number">
                        {{$desecho}}
                        <sup style="font-size: 20px">%</sup>
                    </h3>
                </div>
                <div class="icon">
                    <i class="ion ion-trash-a"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer">
                    Desechos <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-green-gradient">
                <div class="inner">
                    <h3 class="info-box-number">
                        {{$rendimiento}}
                        <sup style="font-size: 0.4em">tallos/hora</sup>
                    </h3>
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people-outline"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer">
                    Rendimiento <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3 class="info-box-number">
                        {{$calibre}}
                        <sup style="font-size: 0.4em">gr</sup>
                    </h3>
                </div>
                <div class="icon">
                    <i class="fa fa-tint"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer">
                    Calibre <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info text-center">
        No se ha trabajado aún el día de hoy
    </div>
@endif