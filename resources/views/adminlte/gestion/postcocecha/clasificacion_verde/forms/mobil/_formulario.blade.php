@if($verde != '' && $cosecha != '')
    <table class="table-striped table-bordered" style="border: 2px solid #9d9d9d" width="100%" id="table_formulario">
        <thead>
        <tr>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d; width: 120px">
                <select name="variedad_form" id="variedad_form" style="width: 100%">
                    @foreach($cosecha->getVariedades() as $v)
                        <option value="{{$v->id_variedad}}">{{getVariedad($v->id_variedad)->siglas}}</option>
                    @endforeach
                </select>
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Calibre
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Ramos
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Tallos x Ramo
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d">
                Total
            </th>
            <th class="text-center" style="background-color: #e9ecef; border-color: #9d9d9d; width: 20px">
                <a href="javascript:void(0)" onclick="$('#div_form_verde').toggleClass('hide')"
                   class="pull-right text-black btn btn-xs btn-default">
                    <i class="fa fa-fw fa-ellipsis-v"></i>
                </a>
            </th>
        </tr>
        </thead>
        <tbody id="body_tabla_formulario"></tbody>
        <tfoter>
            <tr>
                <th colspan="6" class="text-center" style="border-color: #9d9d9d">
                    <button type="button" class="btn btn-xs btn-block btn-primary">
                        <i class="fa fa-fw fa-save"></i> Guardar
                    </button>
                </th>
            </tr>
        </tfoter>
    </table>

    <script>
        construir_tabla();
    </script>
@else
    <div class="well text-center">
        No se ha encontrado ningún trabajo realizado en la fecha seleccionada
        <a href="javascript:void(0)" onclick="$('#div_form_verde').toggleClass('hide')"
           class="pull-right text-black btn btn-xs btn-default">
            <i class="fa fa-fw fa-ellipsis-v"></i>
        </a>
    </div>
@endif

<script>
    @if($verde != '')
    $('#personal').val('{{$verde->personal}}');
    $('#hora_inicio').val('{{$verde->hora_inicio}}');
    @else
    $('#personal').val('');
    $('#hora_inicio').val('');

    @endif
</script>