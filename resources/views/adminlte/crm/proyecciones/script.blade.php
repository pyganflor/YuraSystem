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
            var ctx = document.getElementById('chart').getContext('2d');
            labels=[];
            $.each(retorno[0].data,function(i){ labels.push(i);  });
            colores=['#00ff00','#ff8000','#8080ff','#ff0000','#00c0ef','#00a65a','#ffac58','#1B14FF',];
            datasets = [];

            $.each(retorno,function(i,j){
                data=[];
                $.each(j.data,function (k,l) {
                    data.push(l);
                });
                datasets.push({
                    label : j.label,
                    borderColor : colores[i],
                    borderWidth : 2,
                    fill : false,
                    data :data
                });
            });


            console.log(datasets);
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: datasets,
                    labels: labels
                },
            });
        });
    }
</script>
