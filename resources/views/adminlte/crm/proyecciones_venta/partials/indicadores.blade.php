<div class="row">
    <div class="col-md-4" style="cursor:pointer" onclick="desglose_indicador('tallos')">
        <div class="small-box bg-teal-active">
            <div class="inner">
                <ul class="info-box-number list-unstyled">
                    <li>Cajas: {{$indicador[1]->valor}}</li>
                    <li>Tallos: {{$indicador[0]->valor}}</li>
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
    <div class="col-md-4" style="cursor:pointer" onclick="desglose_indicador('tallos')">
        <div class="small-box bg-aqua">
            <div class="inner">
                <ul class="info-box-number list-unstyled">
                    <li>Cajas: {{$indicador[2]->valor}}</li>
                    <li>Dinero: {{$indicador[3]->valor}}</li>
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
    <div class="col-md-4" style="cursor:pointer" onclick="desglose_indicador()">
        <div class="small-box bg-orange">
            <div class="inner" style="padding: 3.5px 10px">
                <ul class="info-box-number list-unstyled">
                    <li style="font-size: 15px">Mes 1: 999.99</li>
                    <li style="font-size: 15px">Mes 2: 999.99</li>
                    <li style="font-size: 15px">Mes 3: 999.99</li>
                </ul>
            </div>
            <div class="icon">
                <i class="fa fa-calendar-check-o"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer">
                Meses <sup>+3 meses</sup> <i class="fa fa-arrow-circle-right"></i>
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
