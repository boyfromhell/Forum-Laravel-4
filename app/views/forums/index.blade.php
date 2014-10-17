@extends('layout')

@section('content')

@if ( $announcement->id )
<div class="panel panel-info">
	<div class="panel-heading">
		{{{ $announcement->title }}}
	</div>
	<div id="announcement_{{ $announcement->id }}" class="panel-body">
		{{ BBCode::parse($announcement->text) }}
	</div>
</div>
@endif

@if ( $quote->id )
<div id="quotebot">

	<div style="float:left; width:95%;">{{{ $quote->text }}}

	@if ( $quote->author )
	<br><i>&mdash; 
	@if ( $quote->user->id )<a href="{{ $quote->user->url }}">@endif
	{{{ $quote->author }}}
	@if ( $quote->user->id )</a>@endif
	</i>
	@endif
	
	</div>
	<div style="text-align:right"><a class="light" href="{{ $quote->url }}">#{{ $quote->id }}</a></div>
	<div class="break"></div>

</div><br>
@endif

<div style="float:left; margin-bottom:6px;">
	<h1><a href="/forum/">{{{ Config::get('app.forum_name') }}}</a></h1>
</div>

@if ( $me->id )
<div class="actions">
	View new <a href="/forum/search?show=new">posts</a> - <a href="/forum/search?show=newtopics">topics</a><br>
	<a href="/forum/?mark=forums">Mark all forums read</a>
</div>
@endif

<div class="break"></div>

@include ('forums.categories')

<div class="welcome wide no-margin">

	<div class="header">
		@if ( $me->id )
			<a href="/forum/stats">Forum Statistics</a>
		@else
			Forum Statistics
		@endif
	</div>

	<div class="body">
	
	<table class="table2" cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
	<td style="width:55px" class="icon">
		<img src="{{ $skin }}icons/stats.png" alt="Statistics" title="Statistics">
	</td>
	<td>
	
	Topics: <b>{{ $stats['total_topics'] }}</b>&nbsp;&nbsp;
	Posts: <b>{{ $stats['total_posts'] }}</b>&nbsp;&nbsp;
	Members: <b>{{ $stats['total_users'] }}</b><br>
	Welcome to our newest member, <a href="{{ $newest_user->url }}">{{{ $newest_user->name }}}</a><br><br>

	<div id="online">
	{{ $online_stats }}
	</div>
	</td>
	</tr>
	</table>
	
	</div>

</div>

@stop
