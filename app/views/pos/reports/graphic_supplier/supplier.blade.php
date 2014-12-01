@extends('layouts.default')

@section('title')
{{{ $title }}} :: @parent
@stop

@section('content')
        <div class="row">
        <div class="large-12 columns">
                <div class="panel" align="center">
                        <h1>Reporte de Proveedores</h1>
                        <h4>{{$date_range}}</h4>
                </div>
        </div>
        </div>
        <hr>
        <div align="center">
                <canvas id="myChart" width="800" height="400"></canvas>
                <div id="pieLegend"></div>
        </div>


        <hr>
        <?php
                $subtotal = 0;
                $total = 0;
                $tax = 0;
                $ganancia = 0;
                foreach ($supplier as $key => $value){
                        $subtotal += $value->subtotal;
                        $total += $value->total;
                        $tax += $value->tax;
                        $ganancia += $value->ganancia;
                }
        ?>
        <ul class="pricing-table">
                <li class="title">Resumen Proveedores</li>
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

        var data = [
                <?php foreach($supplier as $key => $value): ?>
                {
                    value: <?= round($value->total,2); ?>,
                    color:"#" +(Math.random()*0xFFFFFF<<0).toString(16),
                    highlight: "#" +(Math.random()*0xFFFFFF<<0).toString(16),
                    label: "<?php echo $value->company_name; ?>"
                },
                <?php endforeach; ?>
        ];
		var ctx = document.getElementById("myChart").getContext("2d");
                legend(document.getElementById("pieLegend"), data);
		var myLineChart = new Chart(ctx).Pie(data, {
            //Boolean - Whether we should show a stroke on each segment
            segmentShowStroke : true,
            //String - The colour of each segment stroke
            segmentStrokeColor : "#FFF",
            //Number - The width of each segment stroke
            segmentStrokeWidth : 2,

            responsive: true,

            //Number - The percentage of the chart that we cut out of the middle
            percentageInnerCutout : 0, // This is 0 for Pie charts

            //Number - Amount of animation steps
            animationSteps : 200,

            //String - Animation easing effect
            animationEasing : "easeOutBounce",

            //Boolean - Whether we animate the rotation of the Doughnut
            animateRotate : true,

            //Boolean - Whether we animate scaling the Doughnut from the centre
            animateScale : false,

            //String - A legend template
            legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
        });


	</script>
@stop
