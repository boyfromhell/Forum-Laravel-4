@extends('layout')

@section('header')
<h1><a href="{{ $forum->url }}">{{{ $forum->name }}}</a></h1>

<ol class="breadcrumb">
	<li><a href="/">{{{ Config::get('app.forum_name') }}}</a></li>
	<li><a href="/forum/">Forum</a></li>
@foreach ( $forum->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach
</ol>
@stop

@section('buttons')
<div class="pull-left">
	<a href="/new-topic/{{ $forum->id }}" class="btn btn-primary">New Topic</a>
</div>
<div class="pull-right">
	{{ $topics->links() }}
</div>
@stop

@section('content')

@if ( count($children) > 0 )
	@include ('forums.list', ['forums' => $children])
@endif

@if ( $me->id )
<div class="pull-right">
	<a href="{{ $forum->url }}?mark">Mark all topics read</a>
</div>
<div class="clearfix"></div>
@endif

<div class="panel panel-primary">

	<div class="panel-heading">Topics</div>

	<table class="table">
	<thead>
	<tr>
		<th class="icon">&nbsp;</th>
		<th class="icon">&nbsp;</th>
		<th style="min-width:50%">Topics</th>
		<th class="hidden-xs">Last Post</th>
		<th class="posts hidden-xs">Replies</th>
		<th class="posts hidden-xs">Views</th>
	</tr>
	</thead>
	<tbody>
	@foreach ( $topics as $topic )
		@include ('topics.row', ['topic_mode' => 'last_post'])
	@endforeach
	</tbody>
	</table>

</div>

@stop

@section('footer')
@include('blocks.jumpbox', ['selected' => $forum->id])
@stop
