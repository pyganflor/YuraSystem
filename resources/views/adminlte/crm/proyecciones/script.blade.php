<script>

    function modal_indicador(param){
        datos={
            param : param
        };
        get_jquery('{{url('crm_proyeccion/desglose_indicador')}}', datos, function (retorno) {
            modal_view('modal_view_desglose_indicador', retorno, '<i class="fa fa-fw fa-bar-chart"></i> Desglose '+param+'', true, false,
                '{{isPC() ? '65%' : ''}}');
            tallos_4_semanas();
        });
    }

    function tallos_4_semanas(){
        get_jquery('{{url('crm_proyeccion/desglose_tallos_4_semanas')}}', datos, function (retorno) {
            console.log(retorno);
            var ctx = document.getElementById('chart').getContext('2d');

            labels=[];
            $.each(retorno[0].data,function(i,j){
                labels.push(j);
            });

            console.log(labels);
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'First dataset',
                        data: [0, 20, 40, 50],
                        borderColor: 'black',
                        borderWidth: 2,
                        fill: false,
                    }, {
                        label: 'Second dataset',
                        data: [5, 10,15, 25],
                        borderColor: 'black',
                        borderWidth: 2,
                        fill: false,
                    }],
                    labels: ['January', 'February', 'March', 'April']
                },
            });
        });
    }
</script>
