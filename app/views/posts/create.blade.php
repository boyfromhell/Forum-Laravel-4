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
			{{ Form::text('subject', $subject, ['class' => 'form-control', 'maxlength' => 70, 'autofocus']) }}
		</div>
	</div>

	@if ( $me->is_moderator )
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
			{{ Form::textarea('content', $content, ['id' => 'bbtext', 'class' => 'form-control']) }}
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
		<div class="attachment-box">
		<label class="col-sm-3 control-label">Attachments</label>
		<div class="col-sm-8">
		Select up to <b>{{ $max_file_uploads }}</b> files, <b>{{ $upload_max_filesize }} MB</b> each. Total limit is <b>{{ $post_max_size }} MB</b><br>
			<input type="file" id="files" name="files[]" multiple />
			<output id="list"></output>
			<input name="attach" id="attach" type="submit" value="Attach Files" style="display:none">
		</div>
		</div>
	</div>

	<script type="text/javascript">
		function handleFileSelect(evt) {
			var total = 0, count = 0;
			var files = evt.target.files; // FileList object

			// files is a FileList of File objects. List some properties.
			var output = [];
			for (var i = 0, f; f = files[i]; i++) {
				var content = '<li><strong>' + escape(f.name) + '</strong> (' + (f.type || 'n/a') + ') - ' + format_size(f.size);
				if( f.size > {{ $max_bytes }} ) {
					content += ' <span style="color:#a00">(TOO LARGE)</span>';
				}
				content += '</li>';
				output.push(content);
				total += f.size;
				count++;
			}
			if( count > 0 ) {
				var html = '<ul>' + output.join('') + '</ul>' + count + ' files, ' + format_size(total);
				if( count > {{ $max_file_uploads }} ) {
					html = '<br><span style="color:#a00"><b>You have selected too many files</b></span>';
				}
				if( total > {{ $max_total }} ) {
					html = '<br><span style="color:#a00"><b>The total size for these files is too large</b></span>';
				}
				$('#list').html(html);
				$('#attach').show();
			} else {
				$('#list').html('');
				$('#attach').hide();
			}
		}

		document.getElementById('files').addEventListener('change', handleFileSelect, false);
	</script>
	
	<div class="form-group">
		<label class="col-sm-3 control-label">Poll</label>
		<div class="col-sm-7" style="padding:7px 0">
		<a href="" onClick="return false">Add a poll</a>
		</div>
	</div>

	</div>

	<div class="panel-footer">

	<div class="form-group">
		<div class="col-sm-7 col-sm-offset-3">
			{{ Form::submit('Submit', ['name' => 'addpost', 'class' => 'btn btn-primary', 'accesskey' => 'S']) }}
			{{ Form::submit('Preview', ['name' => 'preview', 'class' => 'btn btn-default preview', 'accesskey' => 'P']) }}
		</div>
	</div>

	</div>
</div>

{{-- <div id="preview"></div> --}}

@if ( count($attachments) > 0 )
<div class="panel panel-default">

	<div class="panel-heading">Attachments</div>
	
	<div class="panel-body">
		<?php $prev_type = 0; ?>
		@foreach ( $attachments as $attachment )
			@if ( $attachment->filetype == 1 )
				@if ( $prev_type == 0 )<div style="padding:0 5px">@endif
				<div><a href="{{ $attachment->url }}">{{{ $attachment->origfilename }}}</a> ({{ $attachment->size }}) -
				<a href="" class="delete-attachment" data-id="{{ $attachment->id }}">Remove</a></div>
				<div class="clearfix"></div>
			@else
				@if ( $prev_type == 1 )</div><br>@endif
				<div class="photo" style="height:192px">
				<a class="thumb" href="{{ $attachment->url }}">
				<img src="{{ $cdn }}{{ $attachment->thumbnail }}"></a>
				<a href="" class="delete-attachment" data-id="{{ $attachment->id }}">Remove</a></div>
			@endif
			<?php $prev_type = $attachment->filetype; ?>
		@endforeach
		
		<div class="clearfix"></div>
	</div>
</div>
@endif
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

