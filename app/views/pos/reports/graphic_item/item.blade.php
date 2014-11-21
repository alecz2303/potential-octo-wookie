@extends('layouts.default')

@section('title')
{{{ $title }}} :: @parent
@stop

@section('content')
        <div class="row">
        <div class="large-12 columns">
                <div class="panel" align="center">
                        <h1>Reporte de Artículos</h1>
                        <h4>{{$date_range}}</h4>
                </div>
        </div>
        </div>
        <hr>
        <div align="center">
                <canvas id="myChart" width="800" height="400"></canvas>
                <div id="lineLegend"></div>
        </div>

        <?php
        foreach ($item as $key => $value){
                $total_array[] = round($value->total,2);
                $item_array[] = $value->name;
                $qty_array[] = $value->quantity_purchased;
        }
        ?>

        <hr>
        <?php
                $subtotal = 0;
                $total = 0;
                $tax = 0;
                $ganancia = 0;
                foreach ($item as $key => $value){
                        $subtotal += $value->subtotal;
                        $total += $value->total;
                        $tax += $value->tax;
                        $ganancia += $value->ganancia;
                }
        ?>
        <ul class="pricing-table">
                <li class="title">Resumen Artículos</li>
                <li class="bullet-item">Sub Total: {{number_format($subtotal,2)}}</li>
                <li class="bullet-item">Total: {{number_format($total,2)}}</li>
                <li class="bullet-item">Impuesto: {{number_format($tax,2)}}</li>
                <li class="bullet-item">Ganancia: {{number_format($ganancia,2)}}</li>
        </ul>
@stop

@section('scripts')

        <script src="{{asset('chart/Chart.js')}}"></script>
        <script src="{{asset('chart/legend.js')}}"></script>
        <script>

        var data = {
            labels: ['<?=implode("','", $item_array)?>'],
            datasets: [
                {
                    label: "Ventas",
                    fillColor: "rgba(20,110,255,0.2)",
                    strokeColor: "rgba(20,110,255,1)",
                    pointColor: "rgba(20,110,255,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(20,110,255,1)",
                    data: [<?=implode(",", $total_array)?>]
                },
                {
                    label: "Cantidad Artículos Vendidos",
                    fillColor: "rgba(255,94,51,0.2)",
                    strokeColor: "rgba(255,94,51,1)",
                    pointColor: "rgba(255,94,51,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(255,94,51,1)",
                    data: [<?=implode(",", $qty_array)?>]
                }
            ]
        };
		var ctx = document.getElementById("myChart").getContext("2d");
                legend(document.getElementById("lineLegend"), data);

		var myLineChart = new Chart(ctx).Line(data, {
                        // Boolean - Whether to show labels on the scale
                        scaleShowLabels: true,

                        // Boolean - whether or not the chart should be responsive and resize when the browser does.
                        responsive: true,

                        // Interpolated JS string - can access value
                        scaleLabel: "<%=value%>",

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
