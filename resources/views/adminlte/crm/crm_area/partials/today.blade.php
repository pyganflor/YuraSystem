<div class="list-group">
    <a href="javascript:void(0)" class="list-group-item disabled text-center">
        Semana {{$semana_actual->codigo}}
    </a>
    <a href="javascript:void(0)" class="list-group-item bg-teal-active">
        Área <sup>ha</sup>
        <span class="badge pull-right">
            {{number_format(round($semanal['area'] / 10000, 2), 2)}}
        </span>
    </a>
    <a href="javascript:void(0)" class="list-group-item bg-aqua">
        Ciclo
        <span class="badge pull-right">
            {{number_format($semanal['ciclo'], 2)}}
        </span>
    </a>
    <a href="javascript:void(0)" class="list-group-item bg-orange">
        Tallos/m<sup>2</sup>
        <span class="badge pull-right">
            {{number_format($semanal['tallos'], 2)}}
        </span>
    </a>
    <a href="javascript:void(0)" class="list-group-item bg-green-gradient text-white">
        Ramos/m<sup>2</sup>
        <span class="badge pull-right">
            {{number_format($semanal['ramos'], 2)}}
        </span>
    </a>
    <a href="javascript:void(0)" class="list-group-item bg-red">
        Ramos/m<sup>2</sup>/año
        <span class="badge pull-right">
            {{number_format($semanal['ramos_anno'], 2)}}
        </span>
    </a>
</div>