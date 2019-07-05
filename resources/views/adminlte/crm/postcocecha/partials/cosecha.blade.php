<div class="box box-primary">
    <div class="box-header with-border">
        <h4 class="box-title">
            <strong>Gráficas</strong>
        </h4>

        <div class="input-group">
            <div class="input-group-addon bg-gray">
                <i class="fa fa-calendar-check-o"></i> Rango
            </div>
            <select name="filtro_predeterminado" id="filtro_predeterminado" onchange="filtrar_predeterminado(1)" class="form-control">
                <option value="1">1 Mes</option>
                <option value="2">3 Meses</option>
                <option value="3">6 Meses</option>
                <option value="4">1 Año</option>
            </select>

            <div class="input-group-addon bg-gray">
                <i class="fa fa-fw fa-leaf"></i> Variedad
            </div>
            <select name="filtro_predeterminado_planta" id="filtro_predeterminado_planta" class="form-control"
                    onchange="select_planta($(this).val(), 'filtro_predeterminado_variedad', 'div_cargar_variedades',
                    '<option value=A selected>Acumulado</option><option value=T>Todos los tipos</option>')">
                <option value="">Todas las variedades</option>
                @foreach(getPlantas() as $p)
                    <option value="{{$p->id_planta}}">{{$p->nombre}}</option>
                @endforeach
            </select>
            <div class="input-group-addon bg-gray" id="div_cargar_variedades">
                <i class="fa fa-fw fa-leaf"></i> Tipo
            </div>
            <select name="filtro_predeterminado_variedad" id="filtro_predeterminado_variedad" class="form-control"
                    onchange="filtrar_predeterminado(1)">
                <option value="A" selected>Acumulado</option>
                <option value="T">Todos los tipos</option>
            </select>

            <div class="input-group-btn bg-gray">
                <button type="button" class="btn btn-default dropdown-toggle bg-gray" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    <i class="fa fa-calendar-minus-o"></i> Años
                    <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    @foreach($annos as $a)
                        <li>
                            <a href="javascript:void(0)" onclick="select_anno('{{$a->anno}}')"
                               class="li_anno" id="li_anno_{{$a->anno}}">
                                {{$a->anno}}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <input type="text" class="form-control" placeholder="Años" id="filtro_predeterminado_annos"
                   name="filtro_predeterminado_annos" readonly>

            <div class="input-group-btn">
                <button type="button" id="btn_filtrar" class="btn btn-default" onclick="filtrar_predeterminado(0)" title="Buscar">
                    <i class="fa fa-fw fa-search"></i>
                </button>
            </div>
        </div>

        <div class="box-tools pull-right">
            {{--<button type="button" class="btn btn-box-tool" title="Cosecha por variedades"
                    onclick="select_option_cosecha('cosecha_x_variedad')">
                <i class="fa fa-fw fa-leaf"></i>
            </button>--}}
            {{--<button type="button" class="btn btn-box-tool" title="Filtrar" onclick="select_option_cosecha('filtro')">
                <i class="fa fa-fw fa-filter"></i>
            </button>--}}
            <button type="button" class="btn btn-box-tool" title="Exportar a Excel" onclick="exportar_excel()">
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