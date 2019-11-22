<script>

    function modal_indicador(param){
        datos = {
            param : param
        };
        get_jquery('{{url('crm_proyeccion/desglose_indicador')}}', datos, function (retorno) {
            modal_view('modal_view_desglose_indicador', retorno, '<i class="fa fa-fw fa-bar-chart"></i> Desglose '+param+'', true, false,
                '{{isPC() ? '65%' : ''}}');
            switch (param) {
                case 'venta':
                    venta_4_semanas('crm_proyeccion/desglose_venta_4_semanas','chart1','cajas');
                    venta_4_semanas('crm_proyeccion/desglose_venta_4_semanas','chart2','dinero');
                    break;
                default:
                    cosecha_4_semanas('crm_proyeccion/desglose_cosecha_4_semanas','chart1','cajas');
                    cosecha_4_semanas('crm_proyeccion/desglose_cosecha_4_semanas','chart2','valor');
                    break;
            }
        });
    }

    function cosecha_4_semanas(url,id,opcion){
        datos={
            opcion : opcion
        };
        get_jquery(url, datos, function (retorno) {
            var ctx = document.getElementById(id).getContext('2d');
            labels=[];
            $.each(retorno[0].data,function(i){ labels.push(i); });
            colores=['#00ff00','#ff8000','#8080ff','#ff0000','#00c0ef','#00a65a','#ffac58','#1B14FF'];
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

            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: datasets,
                    labels: labels
                },
            });
        });
    }

    function venta_4_semanas(url,id,opcion) {
        datos={
            opcion : opcion
        };
        get_jquery(url, datos, function (retorno) {
            var ctx = document.getElementById(id).getContext('2d');
            labels=[];
            $.each(retorno[0].data,function(i){ labels.push(i); });
            colores=['#00ff00','#ff8000','#8080ff','#ff0000','#00c0ef','#00a65a','#ffac58','#1B14FF'];
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
