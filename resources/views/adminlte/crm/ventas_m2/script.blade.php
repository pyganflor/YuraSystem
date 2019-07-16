<script>
    function exportar_excel() {
        convertCanvasToImage('chart_mensual');
        convertCanvasToImage('chart_anual');

        $('#btn_exportar_excel').LoadingOverlay('show');
        $.ajax({
            type: "POST",
            dataType: "html",
            contentType: "application/x-www-form-urlencoded",
            url: '{{url('ventas_m2/exportar_excel')}}',
            data: {
                _token: '{{csrf_token()}}',
                src_imagen_chart_mensual: $('#src_imagen_chart_mensual').val(),
                src_imagen_chart_anual: $('#src_imagen_chart_anual').val(),
            },
            success: function (data) {
                var opResult = JSON.parse(data);
                var $a = $("<a>");
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "DASHBOARD-Postcosecha.xlsx");
                $a[0].click();
                $a.remove();
                $('#btn_exportar_excel').LoadingOverlay('hide');
            }
        });
    }

    function convertCanvasToImage(id_canvas) {
        //var image = document.getElementById('imagen_' + id_canvas);
        var canvas = document.getElementById(id_canvas);
        var src = canvas.toDataURL("image/png");

        $('#src_imagen_' + id_canvas).val(src);
    }

</script>