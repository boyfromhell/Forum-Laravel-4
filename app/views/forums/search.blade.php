@extends('layout')

@section('content')

<form class="form-horizontal" method="post" action="{{ $query->url }}">
<div class="panel panel-primary">

	<div class="panel-heading">Search</div>

	<div class="panel-body">

	<div class="form-group">
		<label class="control-label col-sm-3">Keywords</label>
		<div class="col-sm-6 col-md-5">
			{{ Form::text('keywords', $query->keywords, ['class' => 'form-control', 'autofocus']) }}
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6 col-md-3 col-sm-offset-3">
		{{ Form::select('match', ['Match ANY keyword', 'Match ALL keywords'], $query->match, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6 col-md-3 col-sm-offset-3">
		{{ Form::select('where', ['Search topic titles', 'Search message text', 'Search titles and text'], $query->where, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-sm-3">Author</label>
		<div class="col-sm-6 col-md-3">
		{{ Form::text('author', $query->author, ['class' => 'form-control', 'maxlength' => 32]) }}
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6 col-md-3 col-sm-offset-3">
		{{ Form::select('starter', ['Find all posts by user', 'Find topics started by user'], $query->starter, ['class' => 'form-control']) }}
		</div>
	</div>

	<hr>

	<div class="form-group">
		<label class="control-label col-sm-3">Find Posts from</label>
		<div class="col-sm-6 col-md-3">
		{{ Form::select('since', [
				'0' => 'Any date',
				'1' => 'Your last visit',
				'86400' => 'Yesterday',
				'604800' => 'A week ago',
				'1209600' => '2 weeks ago',
				'2628000' => 'A month ago',
				'7884000' => '3 months ago',
				'15768000' => '6 months ago',
				'31536000' => 'A year ago'
			], $query->since, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-sm-3">Display Results as</label>
		<div class="col-sm-6 col-md-3">
		{{ Form::select('show', ['Posts', 'Topics'], $query->show, ['class' => 'form-control']) }}
		</div>
	</div>

	<hr>

	<div class="form-group">
		<label class="control-label col-sm-3">Forum</label>
		<div class="col-sm-6 col-md-4">
		{{--<select class="left" multiple tabindex="1" name="forums[]" size="8" style="width:300px">
			<option value="0"{if count($query->forum_array) == 0} selected{/if}>All Forums</option>
			{foreach $categories as $category}
			<option value="0" style="background:#333; color:#999" disabled>{$category['name']}</option>
			{$category['forum_html']}
			{/foreach}
		</select>--}}
		</div>
	</div>

	</div>

	<div class="panel-footer">

	<div class="form-group">
		<div class="col-sm-6 col-sm-offset-3">
		{{ Form::submit('Search', ['class' => 'btn btn-primary']) }}
		{{ Form::reset('Reset', ['class' => 'btn btn-default']) }}
		</div>
	</div>
</div>

@stop
