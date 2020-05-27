<div class="row">
    <div class="col-md-3">
        <div class="div_indicadores border-radius_16" style="background-color: #30BBBB; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px; color: white">Producción <sup>-4 semanas</sup></legend>
            <p style="color: white">Área <sup>ha</sup>
                <strong class="pull-right">{{number_format(round($area_mensual / 10000, 2), 2)}}</strong>
            </p>
            <legend style="margin-bottom: 5px; color: white"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" style="color: white" onclick="desglose_indicador('area')">
                    <strong>Ver más <i class="fa fa-fw fa-arrow-circle-right"></i></strong>
                </a>
            </p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="div_indicadores border-radius_16" style="background-color: #30BBBB; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px; color: white">Producción <sup>-4 semanas</sup></legend>
            <p style="color: white">Ciclo
                <strong class="pull-right">
                    {{number_format($ciclo_mensual, 2)}}
                    @if($ciclo_mensual > 0)
                        <small style="font-size: 0.5em; color: white; font-weight: bold;">({{round(365 / $ciclo_mensual,2)}})</small>
                    @endif
                </strong>
            </p>
            <legend style="margin-bottom: 5px; color: white"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" style="color: white" onclick="desglose_indicador('ciclo')">
                    <strong>Ver más <i class="fa fa-fw fa-arrow-circle-right"></i></strong>
                </a>
            </p>
        </div>
    </div>
    <div class="col-md-2">
        <div class="div_indicadores_md_2 border-radius_16" style="background-color: #FFFFFF; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px;"><sup>-4 semanas</sup></legend>
            <p>Tallos/m<sup>2</sup>
                <strong class="pull-right">{{number_format($tallos_m2_mensual, 2)}}</strong>
            </p>
            <legend style="margin-bottom: 5px;"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" onclick="desglose_indicador('tallos')">
                    <strong>Ver más <i class="fa fa-fw fa-arrow-circle-right"></i></strong>
                </a>
            </p>
        </div>
    </div>
    <div class="col-md-2">
        <div class="div_indicadores_md_2 border-radius_16" style="background-color: #FFFFFF; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px;"><sup>-4 semanas</sup></legend>
            <p>Ramos/m<sup>2</sup>
                <strong class="pull-right">{{number_format($ramos_m2_mensual, 2)}}</strong>
            </p>
            <legend style="margin-bottom: 5px;"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" onclick="desglose_indicador('ramos')">
                    <strong>Ver más <i class="fa fa-fw fa-arrow-circle-right"></i></strong>
                </a>
            </p>
        </div>
    </div>
    <div class="col-md-2">
        <div class="small-box bg-red">
            <div class="inner">
                <h3 class="info-box-number">
                    {{number_format($ramos_m2_anno_mensual, 2)}}
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