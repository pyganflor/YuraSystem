<div class="row" style="margin-bottom: 10px">
    <div class="col-md-4">
        <div class="div_indicadores border-radius_16" style="background-color: #30BBBB; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px; color: white">Cosechado <sup>+4 semanas</sup></legend>
            <p style="color: white">Cajas <strong class="pull-right">{{number_format($indicador[0]->valor,2,",",".")}}</strong></p>
            <p style="color: white">Tallos <strong class="pull-right">{{number_format($indicador[1]->valor,2,",",".")}}</strong></p>
            <legend style="margin-bottom: 5px; color: white"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" style="color: white" onclick="modal_indicador('cosecha')">
                    <strong>Ver más <i class="fa fa-fw fa-arrow-circle-right"></i></strong>
                </a>
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="div_indicadores border-radius_16" style="background-color: #FFFFFF; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px; color: #04C0EF">Proyectado <sup>+4 semanas</sup></legend>
            <p>Cajas <strong class="pull-right">{{number_format($indicador[2]->valor,2,",",".")}}</strong></p>
            <p>Dinero <strong class="pull-right">${{number_format($indicador[3]->valor,2,",",".")}}</strong></p>
            <legend style="margin-bottom: 5px"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" style="color: #04C0EF" onclick="modal_indicador('venta')">
                    <strong>Ver más <i class="fa fa-fw fa-arrow-circle-right"></i></strong>
                </a>
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="div_indicadores border-radius_16" style="background-color: #FFFFFF; margin-bottom: 5px">
            <legend class="text-center" style="font-size: 1.1em; margin-bottom: 5px; color: #EF6E11">$ Proyectado <sup>+3 meses</sup></legend>
            <ul class="info-box-number list-unstyled">
                @php
                    $indicardor4 = explode("|",$indicador[4]->valor);
                @endphp
                <li style="font-size: 15px"><b>{{explode(":",$indicardor4[0])[0]}}:
                        <strong class="pull-right">${{number_format((explode(":",$indicardor4[0])[1]),2,",",".")}}</strong></b></li>
                <li style="font-size: 15px"><b>{{explode(":",$indicardor4[1])[0]}}:
                        <strong class="pull-right">${{number_format((explode(":",$indicardor4[1])[1]),2,",",".")}}</strong></b></li>
                <li style="font-size: 15px"><b>{{explode(":",$indicardor4[2])[0]}}:
                        <strong class="pull-right">${{number_format((explode(":",$indicardor4[2])[1]),2,",",".")}}</strong></b></li>
            </ul>
            <legend style="margin-bottom: 0px"></legend>
            <p class="text-center" style="margin-bottom: 0px">
                <a href="javascript:void(0)" class="text-center" style="color: #EF6E11" onclick="modal_indicador('venta')">
                    <strong>Ver más <i class="fa fa-fw fa-arrow-circle-right"></i></strong>
                </a>
            </p>
        </div>
    </div>
</div>