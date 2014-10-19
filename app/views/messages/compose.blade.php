@extends('layout')

@section('content')

<form class="form-horizontal unload-warning" method="post" action="{{ $action }}" enctype="multipart/form-data"{{ count($attachments) > 0 ? ' data-changed="1"' : '' }}>
<div class="panel panel-primary">

	<div class="panel-heading">{{ $thread->id ? 'Reply' : 'Compose Message' }}</div>
	
	<div class="panel-body">

	<input type="hidden" name="hash" value="{{ $hash }}">

	<div class="form-group">
		<label class="control-label col-sm-3">To</label>
		<div class="col-sm-7">
			{{ Form::textarea('recipients', $recipients, ['class' => 'form-control']) }}<br>
			<small>Separate multiple recipients with a comma or new line</small>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-sm-3">Subject</label>
		<div class="col-sm-7">
		@if ( $thread->id )
			<p class="form-control-static">Re: {{{ $thread->title }}}</p>
		@else
			{{ Form::text('subject', $subject, ['maxlength' => 70, 'class' => 'form-control']) }}
		@endif
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-3 control-label hidden-xs">
			{{ BBCode::show_smiley_controls() }}
		</label>
		<div class="col-sm-7">
			{{ BBCode::show_bbcode_controls() }}
			{{ Form::textarea('content', $content, ['id' => 'bbtext', 'class' => 'form-control']) }}
		</div>
	</div>

		<div class="attachment-box">
		<label class="left">Attachments</label>
		<div class="float_left" style="padding:7px 0">
		Select up to <b>{{ $max_file_uploads }}</b> files, <b>{{ $upload_max_filesize }} MB</b> each. Total limit is <b>{{ $post_max_size }} MB</b><br>
		<input type="file" id="files" name="files[]" multiple />
		<output id="list"></output>
		<div class="break"></div>
		<input name="attach" id="attach" type="submit" value="Attach Files" style="display:none">
		</div>
		
		<div class="break"></div>
		</div>
		
		<script>
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

	</div>

	<div class="panel-footer">

	<div class="form-group">
		<div class="col-sm-7 col-sm-offset-3">
			<input class="btn btn-primary" name="send" type="submit" accesskey="S" value="Send Message">
			<input class="btn btn-default preview" name="preview" type="submit" accesskey="P" value="Preview">
		</div>
	</div>

	</div>

</div>

<div id="preview"></div>

@if ( count($attachments) > 0 )
<div class="welcome wide">

	<div class="header">Attachments</div>
	
	<div class="body">
		{$prev_type = 0}
		{foreach $attachments as $attachment}
			{if $attachment->filetype == 1}
				{if $prev_type == 0}<div style="padding:0 5px">{/if}
				<div><a href="{$attachment->url}">{$attachment->origfilename}</a> ({$attachment->get_size()}) -
				<a href="" class="delete-attachment" data-id="{$attachment->id}">Remove</a></div>
				<div class="break"></div>
			{else}
				{if $prev_type == 1}</div><br>{/if}
				<div class="photo" style="height:192px">
				<a class="thumb" href="{$attachment->url}">
				<img src="{$cdn}{$attachment->get_path()}thumbs/{$attachment->thumb}"></a>
				<a href="" class="delete-attachment" data-id="{$attachment->id}">Remove</a></div>
			{/if}
			{$prev_type = $attachment->filetype}
		{/foreach}
		
		<div class="break"></div>
	</div>
</div>
@endif
</form>

@stop
