<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            Recepciones
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" title="Exportar a Excel">
                <i class="fa fa-fw fa-file-excel-o"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-8" id="div_chart_recepciones">
                @include('adminlte.crm.dashboard.partials.chart_recepciones')
            </div>
            <div class="col-md-4">
                <legend style="font-size: 1.1em; margin-bottom: 10px" class="text-center">Filtro</legend>
                @include('adminlte.crm.dashboard.partials.filtro_recepciones')
                <legend style="margin-bottom: 0"></legend>
            </div>
        </div>
    </div>
</div>