@extends('layouts.default')

@section('title')
{{{ $title }}} :: @parent
@stop

@section('content')
        <div class="row">
        <div class="large-12 columns">
                <div class="panel" align="center">
                        <h1>Reporte de Categorias</h1>
                        <h4>{{$date_range}}</h4>
                </div>
        </div>
        </div>
        <hr>
        <div align="center">
                <canvas id="myChart" width="800" height="400"></canvas>
        </div>

        <?php
        foreach ($category as $key => $value){
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
                foreach ($category as $key => $value){
                        $subtotal += $value->subtotal;
                        $total += $value->total;
                        $tax += $value->tax;
                        $ganancia += $value->ganancia;
                }
        ?><?=implode("','", $fecha_array)?><?=implode(",", $total_array)?>
        <ul class="pricing-table">
                <li class="title">Resumen Categorias</li>
                <li class="bullet-item">Sub Total: {{number_format($subtotal,2)}}</li>
                <li class="bullet-item">Total: {{number_format($total,2)}}</li>
                <li class="bullet-item">Impuesto: {{number_format($tax,2)}}</li>
                <li class="bullet-item">Ganancia: {{number_format($ganancia,2)}}</li>
        </ul>
@stop

@section('scripts')

        <script src="{{asset('chart/Chart.js')}}"></script>
        <script>

        var data = [
                {
        value: 300,
        color:"#F7464A",
        highlight: "#FF5A5E",
        label: "Red"
    },
    {
        value: 50,
        color: "#46BFBD",
        highlight: "#5AD3D1",
        label: "Green"
    },
    {
        value: 100,
        color: "#FDB45C",
        highlight: "#FFC870",
        label: "Yellow"
    }
        ];
		var ctx = document.getElementById("myChart").getContext("2d");
		var myLineChart = new Chart(ctx).Pie(data, {
            //Boolean - Whether we should show a stroke on each segment
            segmentShowStroke : true,
            //String - The colour of each segment stroke
            segmentStrokeColor : "#fff",
            //Number - The width of each segment stroke
            segmentStrokeWidth : 2,

            //Number - The percentage of the chart that we cut out of the middle
            percentageInnerCutout : 10, // This is 0 for Pie charts

            //Number - Amount of animation steps
            animationSteps : 100,

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
