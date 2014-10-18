@extends('minimal')

@section('content')

<body class="minimal">

<div class="panel panel-primary">

	<div class="panel-heading">Emoticons</div>
	
	<div class="panel-body">

@foreach ( $smileys as $i => $smiley )
	<div class="emote" onClick="addtext(' {{ $smiley->code }} ', '', 1);" title=" {{ $smiley->code }} "><img src="/images/smileys/{{ $smiley->file }}" alt=" {{ $smiley->code }} "></div>

	@if ( $i % 8 == 7 )
		<div class="clearfix"></div>
	@endif
@endforeach

	</div>
</div>

<div class="text-center">
	<small><a href="" onClick="window.close(); return false;">close</a></small>
</div>

</body>

@stop
