@extends('layout')

@section('content')

@if ( $me->id )
<a href="/community/submit_score" class="button">Submit Score</a>

<div class="break"></div>
@endif

<div class="welcome wide no-margin">
	<div class="table-header">
		<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<th class="icon">&nbsp;</th>
			<th style="width:8%">Score</th>
			<th style="width:13%">Character</th>
			<th style="width:13%">Player</th>
			<th style="width:8%">Variant</th>
			<th>Ending</th>
			<th style="width:10px">&nbsp;</th>
		</tr>
		</table>
	</div>
</div>

@foreach ( $categories as $name => $scores )
<div class="welcome wide{{ $name == 'Defeats' ? ' no-margin' : '' }}">

	<div class="header">{{{ $name }}}</div>

	<div class="body">
	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">

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

	</table>
	</div>

</div>
@endforeach

@if ( $me->id )
<a href="/community/submit_score" class="button">Submit Score</a>

<div class="break"></div>
@endif

@stop

