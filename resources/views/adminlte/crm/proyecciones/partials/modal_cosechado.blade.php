<div class="nav-tabs-custom">
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><i class="fa {{$iconFirst}}"></i> {{$first}}</a></li>
        <li class="{{!$second ? "hide": ""}}" ><a href="#tab_2" data-toggle="tab" aria-expanded="true"><i class="fa {{$iconSecond}}" ></i> {{$second}}</a></li>
        <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false"><i class="fa fa-table" ></i> Tabla</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
            <canvas id="chart1" style="margin-top: 5px"></canvas>
        </div>
        <div class="tab-pane {{!$second ? "hide": ""}}" id="tab_2">
            <canvas id="chart2" style="margin-top: 5px"></canvas>
        </div>
        <div class="tab-pane" id="tab_3" style="width:100%;overflow-x:auto">
            @if($tabla ==='cosecha')
                @include('adminlte.crm.proyecciones.partials.tbl_cosecha')
            @elseif($tabla ==='venta')
                @include('adminlte.crm.proyecciones.partials.tbl_venta')
            @elseif($tabla ==='venta a 3 meses')
                @include('adminlte.crm.proyecciones.partials.tbl_venta_3_meses')
            @else
            @endif
    </div>
</div>
</div>

<script>
$(function ( ) {
$('[data-toggle="tooltip"]').tooltip();
})
</script>
