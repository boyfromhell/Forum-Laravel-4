@extends('layout')

@section('content')

<h1><a href="{{ $forum->url }}">{{{ $forum->name }}}</a></h1>

<ol class="breadcrumb">
	<li><a href="/">{{{ Config::get('app.forum_name') }}}</a></li>
	<li><a href="/forum/">Forum</a></li>
@foreach ( $forum->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach
</ol>

<div class="pull-right">
	{{ $topics->links() }}
</div>
<div class="clearfix"></div>

@if ( count($children) > 0 )
	@include ('forums.list', ['forums' => $children])
@endif

@if ( $me->id )
<a href="/new-topic/{{ $forum->id }}" class="btn btn-primary">New Topic</a>

<div class="actions">
	<a href="{{ $forum->url }}?mark">Mark all topics read</a>
</div>
<div class="break"></div>
@endif

<div class="panel panel-primary">

	<div class="panel-heading">Topics</div>

	<table class="table">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th class="icon">&nbsp;</th>
		<th style="width:50%">Topics</th>
		<th>Last Post</th>
		<th class="posts">Replies</th>
		<th class="posts">Views</th>
	</tr>
	</thead>
	<tbody>
	@foreach ( $topics as $topic )
		@include ('topics.row', ['topic_mode' => 'last_post'])
	@endforeach
	</tbody>
	</table>

</div>

@if ( $me->id )
<a href="/new-topic/{{ $forum->id }}" class="btn btn-primary">New Topic</a>

<div class="pull-right">
	{{ $topics->links() }}
</div>
@endif

@stop
