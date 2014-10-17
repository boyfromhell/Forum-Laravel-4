@extends('layout')

@section('content')

@if ( $me->id )
<a href="/community/submit_score" class="btn btn-primary">Submit Score</a>
@endif

@foreach ( $categories as $name => $scores )

<div class="panel panel-primary">

	<div class="panel-heading">{{ $name }}</div>

	<table class="table">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th style="width:8%">Score</th>
		<th style="width:13%">Character</th>
		<th style="width:13%">Player</th>
		<th style="width:8%">Variant</th>
		<th>Ending</th>
	</tr>
	</thead>
	<tbody>
@foreach ( $scores as $count => $score )
	<tr>
		<td class="icon">{{ $count+1 }}</td>
		<td style="width:8%"><b>
		@if ( $score->url )
			<a href="{{{ $score->url }}}">{{{ $score->score }}}</a>
		@else
			{{{ $score->score }}}
		@endif
		</b></td>
		<td style="width:13%">{{{ $score->character }}}</td>
		<td style="width:13%"><a href="{{ $score->user->url }}" style="text-decoration:none">{{{ $score->user->name }}}</a></td>
		<td style="width:8%">{{{ $score->variant }}}</td>
		<td class="ending">{{{ $score->ending }}}</td>
	</tr>
@endforeach
	</tbody>
	</table>

</div>
@endforeach

@if ( $me->id )
<a href="/community/submit_score" class="btn btn-primary">Submit Score</a>
@endif

@stop

