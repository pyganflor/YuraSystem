<div class="nav-tabs-custom">
    <ul class="nav nav-pills nav-justified">
        <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><i class="fa fa-pagelines"></i> Tallos</a></li>
        <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="true"><i class="fa fa-cube" ></i> Cajas</a></li>
        <li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false"><i class="fa fa-usd" ></i> Tabla</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab_1">
            <canvas id="chart1" style="margin-top: 5px"></canvas>
        </div>
        <div class="tab-pane" id="tab_2">
            <canvas id="chart2" style="margin-top: 5px"></canvas>
        </div>
        <div class="tab-pane" id="tab_3">
            @dump($data)
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <th style="width: 10px" class="bg-gray-light">Variedad/Semana</th>
                    @foreach($data[0]['data'] as $semana => $d)
                        <th class="text-center">{{$semana}}</th>
                    @endforeach
                    <th style="width: 10px"  class="bg-gray-light">Variedad/Semana</th>
                </tr>
                @foreach($data as $d)
                    <tr>
                        <td class="bg-gray-light text-center">{{$d['variedad']}}</td>
                        @foreach($d['data'] as $proy)
                        <td class="text-center"><span data-toggle="tooltip" title="Cajas">{{$proy['cajas']}}</span> / <span data-toggle="tooltip" title="Tallos">{{$proy['tallos']}}</span></td>
                        @endforeach
                        <td class="bg-gray-light text-center">{{$d['variedad']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function ( ) {
        $('[data-toggle="tooltip"]').tooltip();
    })
</script>
