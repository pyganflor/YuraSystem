<div class="box box-primary">
    <div class="box-header with-border">
        <h4 class="box-title">
            Gráficas

            <select name="filtro_predeterminado" id="filtro_predeterminado" onchange="filtrar_predeterminado()">
                <option value="1">1 Mes</option>
                <option value="2">3 Meses</option>
                <option value="3">6 Meses</option>
                <option value="4">1 Año</option>
            </select>
        </h4>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" title="Cosecha por variedades"
                    onclick="select_option_cosecha('cosecha_x_variedad')">
                <i class="fa fa-fw fa-leaf"></i>
            </button>
            <button type="button" class="btn btn-box-tool" title="Filtrar" onclick="select_option_cosecha('filtro')">
                <i class="fa fa-fw fa-filter"></i>
            </button>
            <button type="button" class="btn btn-box-tool" title="Exportar a Excel">
                <i class="fa fa-fw fa-file-excel-o"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-8" id="div_chart_cosecha">
                {{--@include('adminlte.crm.postcocecha.partials.secciones.chart_cosecha')--}}
            </div>
            <div class="col-md-4">
                <div id="div_filtro_cosecha" class="div_option_cosecha" style="display: none">
                    <legend style="font-size: 1.1em; margin-bottom: 10px" class="text-center">Filtro</legend>
                    @include('adminlte.crm.postcocecha.partials.secciones.filtro_cosecha')
                    <legend style="margin-bottom: 0"></legend>
                </div>
                <div id="div_cosecha_x_variedad_cosecha" class="div_option_cosecha">
                    @include('adminlte.crm.postcocecha.partials.secciones.cosecha_x_variedad')
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    filtrar_predeterminado();
</script>