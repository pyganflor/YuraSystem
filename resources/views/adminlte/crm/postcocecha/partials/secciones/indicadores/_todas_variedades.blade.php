@if(count($labels) > 0)
    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-teal-active">
                <div class="inner">
                    @foreach($arreglo_variedades as $item)
                        <h3 class="info-box-number" title="Cajas Equivalentes">
                            {{number_format($item['cajas'], 2)}}
                            <sup style="font-size: 0.4em" title="Variedad">
                                <em>{{$item['variedad']->siglas}}</em>
                            </sup>
                        </h3>
                    @endforeach
                </div>
                <div class="icon">
                    <i class="fa fa-fw fa-gift"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_cajas('{{$desde}}', '{{$hasta}}')">
                    Cosecha cajas <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-aqua">
                <div class="inner">
                    @foreach($arreglo_variedades as $item)
                        <h3 class="info-box-number" title="Tallos">
                            {{number_format($item['tallos'])}}
                            <sup style="font-size: 0.4em" title="Variedad">
                                <em>{{$item['variedad']->siglas}}</em>
                            </sup>
                        </h3>
                    @endforeach
                </div>
                <div class="icon">
                    <i class="ion ion-leaf"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_tallos('{{$desde}}', '{{$hasta}}')">
                    Cosecha tallos <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-2">
            <div class="small-box bg-red">
                <div class="inner">
                    @foreach($arreglo_variedades as $item)
                        <h3 class="info-box-number">
                            {{$item['desecho']}}<sup style="font-size: 0.4em">% <em>{{$item['variedad']->siglas}}</em></sup>
                        </h3>
                    @endforeach
                </div>
                <div class="icon">
                    <i class="ion ion-trash-a"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_desechos('{{$desde}}', '{{$hasta}}')">
                    Desechos <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-2">
            <div class="small-box bg-green-gradient">
                <div class="inner">
                    @foreach($arreglo_variedades as $item)
                        <h3 class="info-box-number">
                            {{number_format($item['rendimiento'], 2)}}<sup style="font-size: 0.4em"><em>{{$item['variedad']->siglas}}</em></sup>
                        </h3>
                    @endforeach
                </div>
                <div class="icon">
                    <i class="ion ion-ios-people-outline"></i>
                </div>
                <a href="javascript:void(0)" class="small-box-footer" onclick="show_data_rendimientos('{{$desde}}', '{{$hasta}}')">
                    Rendimiento <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md-2">
            <div class="small-box bg-orange">
                <div class="inner">
                    @foreach($arreglo_variedades as $item)
                        <h3 class="info-box-number">
                            {{$item['calibre']}}<sup style="font-size: 0.4em"><em>{{$item['variedad']->siglas}}</em></sup>
                        </h3>
                    @endforeach
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
        No se ha trabajado aún el día de hoy
    </div>
@endif