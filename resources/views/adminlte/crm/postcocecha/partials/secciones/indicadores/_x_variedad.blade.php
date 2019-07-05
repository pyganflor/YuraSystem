@if(count($labels) > 0)
    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-teal-active">
                <div class="inner">
                    <h3 class="info-box-number">
                        {{number_format($cajas, 2)}}
                    </h3>
                    <input type="hidden" id="indicador_cajas" value="{{$cajas}}">
                </div>
                <div class="icon">
                    <i class="fa fa-fw fa-gift"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_cajas('{{$desde}}', '{{$hasta}}')">
                    Cosecha cajas <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3 class="info-box-number">
                        {{number_format($tallos)}}
                    </h3>
                    <input type="hidden" id="indicador_tallos" value="{{$tallos}}">
                </div>
                <div class="icon">
                    <i class="ion ion-leaf"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_tallos('{{$desde}}', '{{$hasta}}')">
                    Cosecha tallos <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3 class="info-box-number">
                        {{$calibre}}
                        <sup style="font-size: 0.4em"></sup>
                    </h3>
                    <input type="hidden" id="indicador_calibre" value="{{$calibre}}">
                </div>
                <div class="icon">
                    <i class="fa fa-tint"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_calibres('{{$desde}}', '{{$hasta}}')">
                    Calibre <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info text-center">
        No se han encotnrado resultados que mostrar
    </div>
@endif