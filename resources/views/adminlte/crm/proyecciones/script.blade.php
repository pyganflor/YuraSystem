<script>
    const colores=['#00ff00','#ff8000','#8080ff','#ff0000','#00c0ef','#00a65a','#ffac58','#1B14FF'];

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
                case 'venta a 3 meses':
                    dinero_3_meses();
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
            datasets = [];

            $.each(retorno,function(i,j){
                data=[];
                $.each(j.data,function (k,l) {
                    data.push(l.toFixed(2));
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

    function dinero_3_meses(){

        get_jquery('{{url('crm_proyeccion/desglose_3_meses')}}', datos, function (retorno) {
            var ctx = document.getElementById('chart1').getContext('2d');
            labels=[];
            $.each(retorno,function(i){ labels.push(i); });
            datasets = [];

            x=0;
            $.each(retorno,function(k,l){
                if(x==0)
                    data = [l];
                if(x==1)
                    data = [0,l];
                if(x==2)
                    data = [0,0,l];

                datasets.push({
                    label : k,
                    borderColor : colores[x],
                    borderWidth : 2,
                    fill : false,
                    data :data
                });
                x++;
            });

            console.log(datasets,labels);
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    datasets: datasets,
                    labels: labels
                },
            });
        });
    }
</script>
