@extends('layouts.default')
@section('content')
<div class="row">
<div class="large-12 columns">
	<div class="panel" align="center">
		<h1>Abonos a Cuenta</h1>
		<h2>
			{{$customer_name->full_name}}
		</h2>
	</div>
</div>
</div>
<hr>
<table id="sales" class="cell-border display compact responsive" width="100%">
	<thead>
		<tr>
			<th >Venta</th>
			<th >Fecha</th>
			<th >Total</th>
			<th >Deuda</th>
			<th >Acciones</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
            <tr>
                <th colspan="3" style="text-align:right">Total:</th>
                <th><span id="totalMilesPlanned"></span></th>
                <th>

                </th>
            </tr>
        </tfoot>
</table>
<hr>

@stop

@section('scripts')
<script src="{{asset('js/jquery.number.min.js')}}"></script>
	<script type="text/javascript">
		var table;
		$(document).ready(function() {
			// Setup - add a text input to each footer cell
			table = $('#sales').DataTable({
				searching: false,
				"ajax": {
					"url": "{{ URL::to('pos/payments/data') }}",
					"data": function ( d ) {
		                d.customer_id = {{$customer_id}};
		            }
				},
			});
			setTimeout("finishTable()",1500);
    });
	</script>
<script type="text/javascript">
var debugScript = false;

function computeTableColumnTotal(tableId, colNumber)
{
  // find the table with id attribute tableId
  // return the total of the numerical elements in column colNumber
  // skip the top row (headers) and bottom row (where the total will go)

  var result = 0;

  try
  {
    var tableElem = window.document.getElementById(tableId);
    var tableBody = tableElem.getElementsByTagName("tbody").item(0);
    var i;
    var howManyRows = tableBody.rows.length;
    for (i=0; i<(howManyRows); i++) // skip first and last row (hence i=1, and howManyRows-1)
    {


    var thisTrElem = tableBody.rows[i];
    var thisTdElem = thisTrElem.cells[colNumber];
    var thisTextNode = thisTdElem.childNodes.item(0)  ;
    if (debugScript)
      {
        window.alert("text is " + thisTextNode.data);
      } // end if

       // try to convert text to numeric
       var thisNumber = thisTextNode.data;
       var thisNumber = thisNumber.replace(/[\$,]/g, '');
       thisNumber = parseFloat(thisNumber);
       // if you didn't get back the value NaN (i.e. not a number), add into result
       if (!isNaN(thisNumber))
         result += thisNumber;
	 } // end for

  } // end try
  catch (ex)
  {
     window.alert("Exception in function computeTableColumnTotal()\n" + ex);
     result = 0;
  }
  finally
  {
     return result;
  }

}

function intVal ( i ) {
  alert('hola')
          return typeof i === 'string' ?
              i.replace(/[\$,]/g, '')*1 :
              typeof i === 'number' ?
                  i : 0;
          return i;
}

function finishTable()
{
   if (debugScript)
     window.alert("Beginning of function finishTable");

   var tableElemName = "sales";

   var totalMilesPlanned = computeTableColumnTotal("sales",3);

   try
  {
    var totalMilesPlannedElem = window.document.getElementById("totalMilesPlanned");
    totalMilesPlannedElem.innerHTML = '$ '+$.number(totalMilesPlanned,2);

   }
   catch (ex)
   {
     window.alert("Exception in function finishTable()\n" + ex);
   }

   return;
}
</script>
@stop
