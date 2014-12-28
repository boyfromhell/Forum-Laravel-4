@extends('layout')

@section('buttons')

@if ( $me->id )
<a href="/honor-rolls/submit" class="btn btn-primary">Submit Score</a>
@endif

@stop

@section('content')

@foreach ( $categories as $name => $scores )

<div class="panel panel-primary">

	<div class="panel-heading">{{ $name }}</div>

	<table class="table table-hover">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th style="width:8%">Score</th>
		<th style="width:13%" class="hidden-xs">Character</th>
		<th>Player</th>
		<th style="width:8%">Variant</th>
		<th style="width:48%" class="hidden-xs">Ending</th>
	</tr>
	</thead>
	<tbody>
@foreach ( $scores as $count => $score )
	<tr>
		<td class="icon">{{ $count+1 }}</td>
		<td><b>
		@if ( $score->url )
			<a href="{{{ $score->url }}}">{{{ number_format($score->score) }}}</a>
		@else
			{{{ number_format($score->score) }}}
		@endif
		</b></td>
		<td class="hidden-xs">{{{ $score->character }}}</td>
		<td><a href="{{ $score->user->url }}" style="text-decoration:none">{{{ $score->user->name }}}</a></td>
		<td>{{{ $score->variant }}}</td>
		<td class="ending hidden-xs">{{{ $score->ending }}}</td>
	</tr>
@endforeach
	</tbody>
	</table>

</div>
@endforeach

@stop

