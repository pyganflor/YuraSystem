<div class="row">
    <div class="col-md-4" style="cursor:pointer" onclick="modal_indicador('cosechado')">
        <div class="small-box bg-teal-active">
            <div class="inner">
                <ul class="info-box-number list-unstyled">
                    <li><b>Cajas:</b> {{number_format($indicador[0]->valor,2,",",".")}}</li>
                    <li><b>Tallos:</b> {{number_format($indicador[1]->valor,2,",",".")}}</li>
                </ul>
            </div>
            <div class="icon">
                <i class="fa fa-line-chart"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer" >
                Cosechado <sup>+4 semanas</sup> <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-4" style="cursor:pointer" onclick="modal_indicador('vendido')">
        <div class="small-box bg-aqua">
            <div class="inner">
                <ul class="info-box-number list-unstyled">
                    <li><b>Cajas:</b> {{number_format($indicador[2]->valor,2,",",".")}}</li>
                    <li><b>Dinero:</b> ${{number_format($indicador[3]->valor,2,",",".")}}</li>
                </ul>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-leaf"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer">
                Proyectado <sup>+4 semanas</sup> <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-4" style="cursor:pointer" onclick="modal_indicador('otros')">
        <div class="small-box bg-orange">
            <div class="inner" style="padding: 3.5px 10px">
                <ul class="info-box-number list-unstyled">
                    <li style="font-size: 15px"><b>Mes 1: 999.99</b></li>
                    <li style="font-size: 15px"><b>Mes 2: 999.99</b></li>
                    <li style="font-size: 15px"><b>Mes 3: 999.99</b></li>
                </ul>
            </div>
            <div class="icon">
                <i class="fa fa-calendar-check-o"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer">
                Dinero proyectado <sup>3 meses</sup> <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
<style>
    .icon{
        font-size: 65px!important;
    }
    .icon:hover{
        font-size: 70px!important;
    }
</style>
