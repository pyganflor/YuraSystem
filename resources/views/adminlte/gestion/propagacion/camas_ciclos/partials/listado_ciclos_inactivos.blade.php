<div style="overflow-x: scroll">
    <table class="table-bordered table-striped" style="width: 100%; border: 2px solid #9d9d9d; border-radius: 18px 18px 0 0"
           id="table_ciclos">
        <thead>
        <tr>
            <th class="text-center th_yura_green" style="border-radius: 18px 0 0 0; border-color: white">
                Cama
            </th>
            <th class="text-center th_yura_green" style="border-color: white">
                Variedad
            </th>
            <th class="text-center th_yura_green" style="border-color: white">
                Inicio
            </th>
            <th class="text-center th_yura_green" style="border-color: white; width: 80px">
                Esquejes x planta
            </th>
            <th class="text-center th_yura_green" style="border-color: white">
                Fina
            </th>
            <th class="text-center th_yura_green" style="border-radius: 0 18px 0 0; border-color: white; width: 80px">
                Opciones
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($camas as $c)
            <tr>
                <th class="text-center td_yura_default" style="border-color: #9d9d9d">
                    {{$c->nombre}}
                </th>
                <th class="text-center td_yura_default" style="border-color: #9d9d9d">
                    <select name="variedad_{{$c->id_cama}}" id="variedad_{{$c->id_cama}}" style="width: 100%">
                        @foreach($variedades as $v)
                            <option value="{{$v->id_variedad}}" {{$v->defecto == 1 ? 'selected' : ''}}>{{$v->nombre}}</option>
                        @endforeach
                    </select>
                </th>
                <th class="text-center td_yura_default" style="border-color: #9d9d9d">
                    <input type="date" id="fecha_inicio_{{$c->id_cama}}" value="{{date('Y-m-d')}}" style="width: 100%" class="text-center">
                </th>
                <th class="text-center td_yura_default" style="border-color: #9d9d9d">
                    <input type="number" id="esq_planta_{{$c->id_cama}}" value="2" style="width: 100%" class="text-center">
                </th>
                <th class="text-center td_yura_default" style="border-color: #9d9d9d">
                    <input type="date" id="fecha_fin_{{$c->id_cama}}" value="{{date('Y-m-d')}}" style="width: 100%" class="text-center">
                </th>
                <th class="text-center td_yura_default" style="border-color: #9d9d9d">
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-yura_primary" title="Crear ciclo" onclick="crear_ciclo('{{$c->id_cama}}')">
                            <i class="fa fa-fw fa-plus"></i>
                        </button>
                    </div>
                </th>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>