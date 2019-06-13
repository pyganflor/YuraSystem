<div class="box-group" id="accordion">
    @foreach($data['variedades'] as $var)
        <div class="panel box box-primary">
            <div class="box-header with-border">
                <h4 class="box-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{$var['variedad']->id_variedad}}" aria-expanded="false"
                       class="collapsed" style="color: {{$var['variedad']->color}}">
                        {{$var['variedad']->planta->siglas}} - {{$var['variedad']->nombre}}
                    </a>
                </h4>
            </div>
            <div id="collapse{{$var['variedad']->id_variedad}}" class="panel-collapse collapse" aria-expanded="false">
                <div class="box-body">
                    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d"
                           id="table_ciclo_{{$var['variedad']->id_variedad}}">
                        <thead>
                        <tr>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Módulo
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Inicio
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Poda/Siembra
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Área
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Días
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                1ra Flor
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                80%
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Tallos Cosechados
                            </th>
                            <th class="text-center table-{{getUsuario(Session::get('id_usuario'))->configuracion->skin}}"
                                style="border-color: #9d9d9d">
                                Final
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($var['ciclos'] as $c)
                            <tr>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->modulo->nombre}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->fecha_inicio}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->modulo->getPodaSiembraByCiclo($c->id_ciclo)}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{number_format($c->area, 2)}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{difFechas($c->fecha_inicia, $c->fecha_inicio)->days}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{difFechas($c->fecha_cosecha, $c->fecha_inicio)->days}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->get80Porciento()}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{number_format($c->getTallosCosechados(), 2)}}
                                </th>
                                <th class="text-center"
                                    style="border-color: #9d9d9d">
                                    {{$c->fecha_fin}}
                                </th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{dd($data)}}