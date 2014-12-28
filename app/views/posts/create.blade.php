@extends('layout')

@section('content')

@if ( $mode == 'newtopic' )
<h1><a href="{{ $forum->url }}">{{{ $forum->name }}}</a></h1>
@else
<h1><a href="{{ $topic->url }}">{{{ $topic->title }}}</a></h1>
@endif

<ol class="breadcrumb">
	<li><a href="/">{{{ Config::get('app.forum_name') }}}</a></li>
	<li><a href="/forum">Forum</a></li>
@foreach ( $forum->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach

@if ( $mode != 'newtopic' )
	<li><a href="{{ $forum->url }}">{{{ $forum->name }}}</a></li>
@endif
</ol>

<form class="form-horizontal unload-warning" method="post" action="" enctype="multipart/form-data"{{ count($attachments) > 0 ? ' data-changed="1"' : '' }}>
<div class="panel panel-primary">

	<div class="panel-heading">{{{ $_PAGE['title'] }}}</div>

	<div class="panel-body">
	{{ Form::hidden('hash', $hash) }}

	<div class="form-group">
		<label class="col-sm-3 control-label">Subject</label>
		<div class="col-sm-7">
			{{ Form::text('subject', $subject, ['class' => 'form-control', 'maxlength' => 70, ( $mode == 'newtopic' ? 'autofocus' : '' ), 'tabindex' => 1]) }}
		</div>
	</div>

	@if ( $me->is_mod )
	<div class="form-group">
		<label class="col-sm-3 control-label">Topic Type</label>
		<div class="col-sm-3">
			{{ Helpers::radioGroup('type', ['Normal', 'Sticky', 'Announcement'], $topic->type) }}
		</div>
	</div>
	@endif

	<div class="form-group">
		<label class="col-sm-3 control-label hidden-xs">
			{{ BBCode::show_smiley_controls() }}
		</label>
		<div class="col-sm-7">
			{{ BBCode::show_bbcode_controls() }}
			{{ Form::textarea('content', $content, ['id' => 'bbtext', 'class' => 'form-control', ('mode' == 'newtopic' ? '' : 'autofocus'), 'tabindex' => 1]) }}
		</div>
	</div>

	<div class="form-group form-group-sm">
		<label class="col-sm-3 control-label">Notify me of replies</label>
		<div class="col-sm-9">
			{{ Helpers::radioGroup('subscribe', ['1' => 'Yes', '0' => 'No'], $check_sub, 'btn-group-sm') }}
		</div>
	</div>

	<div class="form-group form-group-sm">
		<label class="col-sm-3 control-label">Enable smileys</label>
		<div class="col-sm-9">
			{{ Helpers::radioGroup('show_smileys', ['1' => 'Yes', '0' => 'No'], $show_smileys, 'btn-group-sm') }}
		</div>
	</div>

	<div class="form-group form-group-sm">
		<label class="col-sm-3 control-label">Attach my signature</label>
		<div class="col-sm-9">
			{{ Helpers::radioGroup('attach_sig', ['1' => 'Yes', '0' => 'No'], $attach_sig, 'btn-group-sm') }}
		</div>
	</div>

	{{--
		Post Icon
		{for $i from 1 to 12}
			if( $i == 7 ) { echo "<td width='10%'>&nbsp;</td>"; }
			echo "<td width='10%' nowrap><label><input type='radio' name='smiley' value='$i'";
			if( $i == $smiley ) { echo " checked"; }
			echo ">";
			if( $i != 0 ) { display_smiley($i); }
			else { echo " None"; }
			echo "</label></small></td>\n";
			if( $i%7 == 6 ) { echo "</tr><tr>"; }
		{/for}
	--}}

	<div class="form-group">
		<label class="col-sm-3 control-label">Poll</label>
		<div class="col-sm-9">
			<p class="form-control-static"><a href="" onClick="return false">Add a poll</a></p>
		</div>
	</div>

	@include ('blocks.attachments')

	</div>

	<div class="panel-footer">

	<div class="form-group">
		<div class="col-sm-7 col-sm-offset-3">
			{{ Form::submit('Submit Post', ['name' => 'addpost', 'class' => 'btn btn-primary btn-once', 'accesskey' => 'S', 'data-loading-text' => 'Submitting...']) }}
			{{ Form::submit('Preview', ['name' => 'preview', 'class' => 'btn btn-default preview', 'accesskey' => 'P']) }}
		</div>
	</div>

	</div>
</div>

{{-- <div id="preview"></div> --}}

@include ('blocks.edit_attachments')
</form>

@if ( $mode == 'reply' || $mode == 'quote' )
<div class="panel panel-info">

	<div class="panel-heading">Topic Review (Newest First)</div>
	
	<div class="panel-body">
	
	<iframe src="/topic-review/{{ $topic->id }}" width="100%" height="250" frameborder="no" scrolling="auto"></iframe>
	
	</div>
</div>
@endif

@stop

