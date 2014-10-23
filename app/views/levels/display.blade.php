@extends('layout')

@section('content')

<div class="row">
<div class="col-sm-6">
<div class="panel panel-primary">

	<div class="panel-heading">Badges</div>

	<table class="table">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th>Name</th>
		<th class="date" width="25%">Posts Required</th>
		<th width="15%">&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	@foreach( $levels as $level )
	<tr>
		<td class="icon"><img src="/images/titles/{{ $level->image }}"></td>
		<td>{{{ $level->name }}}</td>
		<td class="posts">{{ number_format($level->min_posts) }}</td>
		<td class="text-center">
		@if ( $me->id && $level->id == $me->post_level->id )
		<span class="glyphicon glyphicon-star gold-icon"></span> You
		@else
		&nbsp;
		@endif
		</td>
	</tr>
	@endforeach
	</tbody>
	</table>

</div>
</div>

<div class="col-sm-6">
<div class="panel panel-primary">

	<div class="panel-heading">Special</div>

	<table class="table">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th>Name</th>
		<th>Member</th>
	</tr>
	</thead>
	<tbody>
	@foreach ( $special as $level )
	<tr>
		<td class="icon">
		@if ( $level->image )
		<img src="/images/titles/{{ $level->image }}">
		@else
		&nbsp;
		@endif
		</td>
		<td>{{{ $level->name }}}</td>
		<td>
		@foreach( $level->users as $user )
		<a href="{{ $user->url }}">{{{ $user->name }}}</a>
		@endforeach
		</td>
	</tr>
	@endforeach
	</tbody>
	</table>

</div>
</div>
</div>

@stop
