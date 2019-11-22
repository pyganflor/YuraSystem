<div class="nav-tabs-custom">
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><i class="fa fa-pagelines"></i> Tallos</a></li>
        <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="true"><i class="fa fa-cube" ></i> Cajas</a></li>
        <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false"><i class="fa fa-table" ></i> Tabla</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
            <canvas id="chart1" style="margin-top: 5px"></canvas>
        </div>
        <div class="tab-pane" id="tab_2">
            <canvas id="chart2" style="margin-top: 5px"></canvas>
        </div>
        <div class="tab-pane" id="tab_3" style="width:100%;overflow-x:auto">
            @if($tabla ==='cosecha')
                @include('adminlte.crm.proyecciones.partials.tbl_cosecha')
            @elseif($tabla ==='venta')
                @include('adminlte.crm.proyecciones.partials.tbl_venta')
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
