<div class="box box-solid box-success">
    <div class="box-header with-border ">
        <i class="fa fa-pie-chart"></i>
        <h3 class="box-title">Semana: {{$semana_actual->codigo}}</h3>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <div class="box-footer no-padding">
            <ul class="nav nav-stacked">
                <li>
                    <a href="#"><b>Área <sup>ha</sup></b>
                        <span class="pull-right badge background-color_yura">{{number_format(round($semanal['area'] / 10000, 2), 2)}}</span>
                    </a>
                </li>
                <li><a href="#"><b>Ciclo</b> <span class="pull-right badge background-color_yura">{{number_format($semanal['ciclo'], 2)}}</span></a>
                </li>
                <li><a href="#"><b>Tallos/m<sup>2</sup></b>
                        <span class="pull-right badge background-color_yura">{{number_format($semanal['tallos'], 2)}}</span>
                    </a>
                </li>
                <li><a href="#"><b>Ramos/m<sup>2</sup></b>
                        <span class="pull-right badge background-color_yura">{{number_format($semanal['ramos'], 2)}}</span>
                    </a>
                </li>
                <li><a href="#"><b>Ramos/m<sup>2</sup>/año</b>
                        <span class="pull-right badge background-color_yura">{{number_format($semanal['ramos_anno'], 2)}}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- /.box-body -->
</div>