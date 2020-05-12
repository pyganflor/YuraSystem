<div class="form-row">
    <div class="col-md-4">
        <div class="div_indicadores border-radius_16" style="background-color: #30BBBB;">
            <legend class="text-white" style="font-size: 1.1em">Cosechado <sup>+4 semanas</sup></legend>
            <p>Cajas: <strong class="pull-right">{{number_format($indicador[0]->valor,2,",",".")}}</strong></p>
            <p>Tallos: <strong class="pull-right">{{number_format($indicador[1]->valor,2,",",".")}}</strong></p>
            <legend></legend>
        </div>
    </div>
    <div class="col-md-4">c</div>
    <div class="col-md-4">c</div>
</div>


<div class="row">
    <div class="col-md-4" style="cursor:pointer" onclick="modal_indicador('cosecha')">
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
            <a href="javascript:void(0)" class="small-box-footer">
                Cosechado <sup>+4 semanas</sup> <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-md-4" style="cursor:pointer" onclick="modal_indicador('venta')">
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
    <div class="col-md-4" style="cursor:pointer" onclick="modal_indicador('venta a 3 meses')">
        <div class="small-box bg-orange">
            <div class="inner" style="padding: 3.5px 10px">
                <ul class="info-box-number list-unstyled">
                    @php
                        $indicardor4 = explode("|",$indicador[4]->valor);

                    @endphp
                    <li style="font-size: 15px"><b>{{explode(":",$indicardor4[0])[0]}}:
                            ${{number_format((explode(":",$indicardor4[0])[1]),2,",",".")}}</b></li>
                    <li style="font-size: 15px"><b>{{explode(":",$indicardor4[1])[0]}}:
                            ${{number_format((explode(":",$indicardor4[1])[1]),2,",",".")}}</b></li>
                    <li style="font-size: 15px"><b>{{explode(":",$indicardor4[2])[0]}}:
                            ${{number_format((explode(":",$indicardor4[2])[1]),2,",",".")}}</b></li>
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
    .icon {
        font-size: 65px !important;
    }

    .icon:hover {
        font-size: 70px !important;
    }
</style>
