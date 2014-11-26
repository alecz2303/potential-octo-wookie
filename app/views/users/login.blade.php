@extends('layouts.default')
@section('content')
	<div class="row">
		<div class="large-4 columns">
			<div class="small-12 columns title-div welcome"><p><span class="tit1">KERBEROS</span><span class="tit2">POS</span></p></div>
		</div>
		<div class="large-8 columns mio">
			{{ Confide::makeLoginForm()->render() }}
		</div>
	</div>
@stop
