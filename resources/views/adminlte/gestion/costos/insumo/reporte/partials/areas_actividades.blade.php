<div style="overflow-y: scroll; max-height: 450px">
    <div class="box-group" id="accordion">
        @foreach($areas as $a)
            <button type="button" class="box-title btn btn-xs btn-block btn-default bg-gray mouse-hand collapsed btn_area" data-toggle="collapse"
                    data-parent="#accordion" href="#collapse{{$a->id_area}}" aria-expanded="false" style="font-size: 0.9em; margin-bottom: 5px"
                    onclick="listar_reporte('{{$a->id_area}}', false)" id="btn_area_{{$a->id_area}}">
                {{$a->nombre}}
            </button>
            <div id="collapse{{$a->id_area}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                @foreach($a->actividades as $act)
                    <button type="button" class="btn btn-xs btn-block btn_actividad" style="margin-bottom: 5px"
                            id="btn_actividad_{{$act->id_actividad}}" onclick="listar_reporte('{{$a->id_area}}', '{{$act->id_actividad}}')">
                        {{$act->nombre}}
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>
</div>