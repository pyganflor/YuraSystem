<div style="overflow-y: scroll; max-height: 450px">
    <div class="box-group" id="accordion">
        @foreach($areas as $a)
            <button type="button" class="box-title btn btn-xs btn-block btn-default bg-gray mouse-hand collapsed" data-toggle="collapse"
                    data-parent="#accordion" href="#collapse{{$a->id_area}}" aria-expanded="false" style="font-size: 0.9em; margin-bottom: 5px">
                {{$a->nombre}}
            </button>
            <div id="collapse{{$a->id_area}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                @foreach($a->actividades as $act)
                    <button type="button" class="btn btn-xs btn-block" style="margin-bottom: 5px">
                        {{$act->nombre}}
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>
</div>