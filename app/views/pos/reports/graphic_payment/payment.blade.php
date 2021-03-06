@extends('layouts.default')

@section('title')
{{{ $title }}} :: @parent
@stop

@section('content')
        <div class="row">
        <div class="large-12 columns">
                <div class="panel" align="center">
                        <h1>Reporte de Pagos</h1>
                        <h4>{{$date_range}}</h4>
                </div>
        </div>
        </div>
        <hr>
        <div align="center" class="chart">
                <div class="labeled-chart-container">
        		<div class="canvas-holder">
                        <div id="legendDiv"></div>
        			<canvas id="myChart" width="250" height="250">
        			</canvas>
        		</div>
        	</div>
        </div>


        <hr>
@stop

@section('scripts')

        <script src="{{asset('chart/Chart.js')}}"></script>
        <script src="{{asset('chart/legend.js')}}"></script>
        <script>

        var data = [
                <?php foreach($payment as $key => $value): ?>
                {
                    value: <?= round($value->payment_amount,2); ?>,
                    color:"#" +(Math.random()*0xFFFFFF<<0).toString(16),
                    highlight: "#" +(Math.random()*0xFFFFFF<<0).toString(16),
                    label: "<?php echo $value->payment_type; ?>"
                },
                <?php endforeach; ?>
        ];


var ctx = document.getElementById("myChart").getContext("2d");
var myDoughnutChart = new Chart(ctx).Doughnut(data, {
	//Boolean - Whether we should show a stroke on each segment
	segmentShowStroke : true,
	//String - The colour of each segment stroke
	segmentStrokeColor : "#FFF",
	//Number - The width of each segment stroke
	segmentStrokeWidth : 2,

	//Number - The percentage of the chart that we cut out of the middle
	percentageInnerCutout : 50, // This is 0 for Pie charts

	//Number - Amount of animation steps
	animationSteps : 200,

	//String - Animation easing effect
	animationEasing : "easeOutBounce",

	//Boolean - Whether we animate the rotation of the Doughnut
	animateRotate : true,

	//Boolean - Whether we animate scaling the Doughnut from the centre
	animateScale : false,

	responsive: false,

	//String - A legend template
	legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
});

//and append it to your page somewhere
document.getElementById("legendDiv").innerHTML = myDoughnutChart.generateLegend();

	</script>
@stop
