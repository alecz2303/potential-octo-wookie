@extends('layouts.default')

@section('title')
{{{ $title }}} :: @parent
@stop

@section('content')
        <div class="row">
        <div class="large-12 columns">
                <div class="panel" align="center">
                        <h1>Reporte de Ventas</h1>
                        <h4>{{$date_range}}</h4>
                </div>
        </div>
        </div>
        <hr>
        <div align="center">
                <canvas id="myChart" width="800" height="400"></canvas>
        </div>

        <?php
        foreach ($sales as $key => $value){
                $total_array[] = round($value->total,2);
                $fecha_array[] = $value->created_at;
        }
        $datetime = new DateTime('tomorrow');
        $total_array[] = 0;
        $fecha_array[] = $datetime->format('Y-m-d');
        ?>

        <hr>
        <?php
                $subtotal = 0;
                $total = 0;
                $tax = 0;
                $ganancia = 0;
                foreach ($sales as $key => $value){
                        $subtotal += $value->subtotal;
                        $total += $value->total;
                        $tax += $value->tax;
                        $ganancia += $value->ganancia;
                }
        ?>
        <ul class="pricing-table">
                <li class="title">Resumen Ventas</li>
                <li class="bullet-item">Sub Total: {{number_format($subtotal,2)}}</li>
                <li class="bullet-item">Total: {{number_format($total,2)}}</li>
                <li class="bullet-item">Impuesto: {{number_format($tax,2)}}</li>
                <li class="bullet-item">Ganancia: {{number_format($ganancia,2)}}</li>
        </ul>
@stop

@section('scripts')

        <script src="{{asset('chart/Chart.js')}}"></script>
        <script>

        var data = {
            labels: ['<?=implode("','", $fecha_array)?>'],
            datasets: [
                {
                    label: "Ventas",
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(151,187,205,1)",
                    data: [<?=implode(",", $total_array)?>]
                }
            ]
        };
		var ctx = document.getElementById("myChart").getContext("2d");
		var myLineChart = new Chart(ctx).Line(data, {
            ///Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines : true,

            //String - Colour of the grid lines
            scaleGridLineColor : "rgba(0,0,0,.05)",

            //Number - Width of the grid lines
            scaleGridLineWidth : 1,

            //Boolean - Whether the line is curved between points
            bezierCurve : true,

            //Number - Tension of the bezier curve between points
            bezierCurveTension : 0.4,

            //Boolean - Whether to show a dot for each point
            pointDot : true,

            //Number - Radius of each point dot in pixels
            pointDotRadius : 4,

            //Number - Pixel width of point dot stroke
            pointDotStrokeWidth : 1,

            //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
            pointHitDetectionRadius : 20,

            //Boolean - Whether to show a stroke for datasets
            datasetStroke : true,

            //Number - Pixel width of dataset stroke
            datasetStrokeWidth : 2,

            //Boolean - Whether to fill the dataset with a colour
            datasetFill : true,

            //String - A legend template
            legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"

        });


	</script>
@stop
