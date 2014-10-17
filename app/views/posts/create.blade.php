@extends('layout')

@section('content')

@if ( $mode == 'newtopic' )
<h1><a href="{{ $forum->url }}">{{{ $forum->name }}}</a></h1>
@else
<h1><a href="{{ $topic->url }}">{{{ $topic->title }}}</a></h1>
@endif

<a href="/forum/">{{{ Config::get('app.forum_name') }}}</a>

@foreach ( $forum->parents as $parent )
	&nbsp;&gt;&nbsp;&nbsp;<a href="{{ $parent->url }}">{{{ $parent->name }}}</a>
@endforeach

@if ( $mode != 'newtopic' )
&nbsp;&gt;&nbsp;&nbsp;<a href="{{ $forum->url }}">{{{ $forum->name }}}</a>
@endif

<br><br>

<form class="form-horizontal unload-warning" method="post" action="" enctype="multipart/form-data"{{ count($attachments) > 0 ? ' data-changed="1"' : '' }}>
<div class="welcome wide">

	<div class="header">{{{ $_PAGE['title'] }}}</div>
	
	<div class="body">

		{{ Form::hidden('hash', $hash) }}

		<label class="left">Subject</label>
		{{ Form::text('subject', $subject, ['class' => 'left', 'tabindex' => 1, 'maxlength' => 70]) }}
		<div class="break"></div>
		
		@if ( $me->is_moderator )
		<label class="left">Topic Type</label>
		{{ Form::select('type', ['Normal', 'Sticky', 'Announcement'], $topic->type, ['class' => 'left', 'tabindex' => 1]) }}
		<div class="break"></div>
		@endif

		<div style="margin:15px 0">
		<label class="left">
			{{ BBCode::show_smiley_controls() }}
		</label>
		<div class="float_left">
			{{ BBCode::show_bbcode_controls() }}
			<div class="break"></div>
			{{ Form::textarea('content', $content, ['id' => 'bbtext', 'tabindex' => 1]) }}
		</div>
		
		<div class="break"></div>
		</div>

		<label class="left">Options</label>
		<div class="float_left">

		<label>{{ Form::checkbox('subscribe', 1, $check_sub, ['tabindex' => 1]) }} Notify me of replies</label><br>
		<label>{{ Form::checkbox('show_smileys', 1, $show_smileys, ['tabindex' => 1]) }} Enable smileys</label><br>
		<label>{{ Form::checkbox('attach_sig', 1, $attach_sig, ['tabindex' => 1]) }} Attach my signature to this post</label>
		</div>
		
		<div class="break"></div>

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
		
		<div class="attachment-box">
		<label class="left">Attachments</label>
		<div class="float_left" style="padding:7px 0">
		Select up to <b>{{ $max_file_uploads }}</b> files, <b>{{ $upload_max_filesize }} MB</b> each. Total limit is <b>{{ $post_max_size }} MB</b><br>
		<input tabindex="1" type="file" id="files" name="files[]" multiple />
		<output id="list"></output>
		<div class="break"></div>
		<input tabindex="1" name="attach" id="attach" type="submit" value="Attach Files" style="display:none">
		</div>
		
		<div class="break"></div>
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
		
		<div class="form-box" style="margin:0 0 15px">
		<label class="left">Poll</label>
		<div class="float_left" style="padding:7px 0">
		<a href="" onClick="return false">Add a poll</a>
		</div>
		
		<div class="break"></div>
		</div>
		
		<center>
	
		<input class="btn btn-primary" tabindex="1" name="addpost" type="submit" accesskey="S" value="Submit">
		<input class="btn btn-default preview" tabindex="1" name="preview" type="submit" accesskey="P" value="Preview">

		<div class="break"></div>
		
		</center>
		
	</div>
</div>

<div id="preview"></div>

@if ( count($attachments) > 0 )
<div class="welcome wide">

	<div class="header">Attachments</div>
	
	<div class="body">
		<?php $prev_type = 0; ?>
		@foreach ( $attachments as $attachment )
			@if ( $attachment->filetype == 1 )
				@if ( $prev_type == 0 )<div style="padding:0 5px">@endif
				<div><a href="{{ $attachment->url }}">{{{ $attachment->origfilename }}}</a> ({{ $attachment->size }}) -
				<a href="" class="delete-attachment" data-id="{{ $attachment->id }}">Remove</a></div>
				<div class="break"></div>
			@else
				@if ( $prev_type == 1 )</div><br>@endif
				<div class="photo" style="height:192px">
				<a class="thumb" href="{{ $attachment->url }}">
				<img src="{{ $cdn }}{{ $attachment->thumbnail }}"></a>
				<a href="" class="delete-attachment" data-id="{{ $attachment->id }}">Remove</a></div>
			@endif
			<?php $prev_type = $attachment->filetype; ?>
		@endforeach
		
		<div class="break"></div>
	</div>
</div>
@endif
</form>

@if ( $mode == 'reply' || $mode == 'quote' )
<div class="welcome wide">

	<div class="header">Topic Review (Newest First)</div>
	
	<div class="body">
	
	<iframe src="/topic-review/{{ $topic->id }}" width="100%" height="250" frameborder="no" scrolling="auto"></iframe>
	
	</div>
</div>
@endif

@stop

