@extends('layouts.default')

@section('title')
{{{ $title }}} :: @parent
@stop

@section('content')
        <div class="row">
        <div class="large-12 columns">
                <div class="panel" align="center">
                        <h1>Reporte de Descuentos</h1>
                        <h4>{{$date_range}}</h4>
                </div>
        </div>
        </div>
        <hr>
        <div align="center">
                <canvas id="myChart" width="800" height="400"></canvas>
        </div>

        <?php
        foreach ($discount as $key => $value){
                $count_array[] = round($value->disc_count,2);
                $discount_array[] = $value->discount_percent.' %';
        }
        ?>

        <hr>
        <?php
                $subtotal = 0;
                $total = 0;
                $tax = 0;
                $ganancia = 0;
                foreach ($discount as $key => $value){
                        $subtotal += $value->subtotal;
                        $total += $value->total;
                        $tax += $value->tax;
                        $ganancia += $value->ganancia;
                }
        ?>
        <ul class="pricing-table">
                <li class="title">Resumen Descuentos</li>
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
            labels: ['<?=implode("','", $discount_array)?>'],
            datasets: [
                {
                    label: "Descuentos",
                    fillColor: "rgba(240,66,112,0.2)",
                    strokeColor: "rgba(240,66,112,1)",
                    pointColor: "rgba(240,66,112,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(240,66,112,1)",
                    data: [<?=implode(",", $count_array)?>]
                }
            ]
        };
		var ctx = document.getElementById("myChart").getContext("2d");
		var myLineChart = new Chart(ctx).Bar(data, {
                        //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                        scaleBeginAtZero : true,

                        //Boolean - Whether grid lines are shown across the chart
                        scaleShowGridLines : true,

                        //String - Colour of the grid lines
                        scaleGridLineColor : "rgba(0,0,0,.05)",

                        //Number - Width of the grid lines
                        scaleGridLineWidth : 1,

                        //Boolean - If there is a stroke on each bar
                        barShowStroke : true,

                        //Number - Pixel width of the bar stroke
                        barStrokeWidth : 2,

                        //Number - Spacing between each of the X value sets
                        barValueSpacing : 5,

                        //Number - Spacing between data sets within X values
                        barDatasetSpacing : 1,

                        //String - A legend template
                        legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
                    });


	</script>
@stop
