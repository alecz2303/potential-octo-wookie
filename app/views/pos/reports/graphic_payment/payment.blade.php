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
        <div align="center">
                <canvas id="myChart" width="800" height="400"></canvas>
                <div id="pieLegend"></div>
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

		// Colour variables
	var red = "#bf616a",
		blue = "#5B90BF",
		orange = "#d08770",
		yellow = "#ebcb8b",
		green = "#a3be8c",
		teal = "#96b5b4",
		pale_blue = "#8fa1b3",
		purple = "#b48ead",
		brown = "#ab7967";

	$id = function(id){
			return document.getElementById(id);
		},

(function(){

		var canvas = $id('myChart'),
			colours = {
				"Core": blue,
				"Line": orange,
				"Bar": teal,
				"Polar Area": purple,
				"Radar": brown,
				"Doughnut": green
			};
			helpers = Chart.helpers;
		var moduleData = [

			{
				value: 7.57,
				color: colours["Core"],
				highlight: "#" +(Math.random()*0xFFFFFF<<0).toString(16),
				label: "Core"
			},

			{
				value: 1.63,
				color: colours["Bar"],
				highlight: "#" +(Math.random()*0xFFFFFF<<0).toString(16),
				label: "Bar"
			},

			{
				value: 1.09,
				color: colours["Doughnut"],
				highlight: "#" +(Math.random()*0xFFFFFF<<0).toString(16),
				label: "Doughnut"
			},

			{
				value: 1.71,
				color: colours["Radar"],
				highlight: "#" +(Math.random()*0xFFFFFF<<0).toString(16),
				label: "Radar"
			},

			{
				value: 1.64,
				color: colours["Line"],
				highlight: "#" +(Math.random()*0xFFFFFF<<0).toString(16),
				label: "Line"
			},

			{
				value: 1.37,
				color: colours["Polar Area"],
				highlight: "#" +(Math.random()*0xFFFFFF<<0).toString(16),
				label: "Polar Area"
			}

		];
		//
		var moduleDoughnut = new Chart(canvas.getContext('2d')).Doughnut(moduleData, { tooltipTemplate : "<%if (label){%><%=label%>: <%}%><%= value %>kb", animation: false });
		//
		var legendHolder = document.createElement('div');
		legendHolder.innerHTML = moduleDoughnut.generateLegend();
		// Include a html legend template after the module doughnut itself
		helpers.each(legendHolder.firstChild.childNodes, function(legendNode, index){
			helpers.addEvent(legendNode, 'mouseover', function(){
				var activeSegment = moduleDoughnut.segments[index];
				activeSegment.save();
				activeSegment.fillColor = activeSegment.highlightColor;
				moduleDoughnut.showTooltip([activeSegment]);
				activeSegment.restore();
			});
		});
		helpers.addEvent(legendHolder.firstChild, 'mouseout', function(){
			moduleDoughnut.draw();
		});
		canvas.parentNode.parentNode.appendChild(legendHolder.firstChild);

	})();


var ctx = document.getElementById("myChart").getContext("2d");
		legend(document.getElementById("pieLegend"), data);
var myLineChart = new Chart(ctx).Doughnut(data, {
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
	animateScale : true,

	responsive: true,

	//String - A legend template
	legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
});

	</script>
@stop
