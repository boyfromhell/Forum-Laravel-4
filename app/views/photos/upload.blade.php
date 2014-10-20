@extends('layout')

@section('header')

<h1><a href="{{ $album->url }}">{{{ $album->name }}}</a></h1>

@if ( $album->id != 1 )
<ol class="breadcrumb">
	<li><a href="/">{{{ Config::get('app.forum_name') }}}</a></li>
@foreach ( $album->parents as $parent )
	<li><a href="{{ $parent->url }}">{{{ $parent->name }}}</a></li>
@endforeach
</ol>
@endif

@stop

@section('buttons')
<a href="{{ $album->url }}" class="btn btn-primary">Back to Album</a>
@stop

@section('content')

<form class="form-horizontal" method="post" action="/upload-photos/{{ $album->id }}" enctype="multipart/form-data">
<div class="panel panel-primary">

	<div class="panel-heading">Upload Photos</div>
	
	<div class="panel-body">

		Uploading to album <b><a href="{{ $album->url }}">{{{ $album->name }}}</a></b><br>
		Select up to <b>{{ $max_file_uploads }}</b> files, <b>{{ $upload_max_filesize }} MB</b> each. Total limit is <b>{{ $post_max_size }} MB</b><br><br>
		
		When the dialog opens, <b>hold down SHIFT to select multiple images</b><br><br>

		{{ Form::file('photos[]', ['id' => 'files', 'accept' => 'image/*', 'multiple']) }}
		<output id="list"></output>

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
			} else {
				$('#list').html('');
			}
		}

		document.getElementById('files').addEventListener('change', handleFileSelect, false);
		</script>

	</div>

	<div class="panel-footer text-center">

		{{ Form::submit('Upload', ['class' => 'btn btn-primary btn-once', 'data-loading-text' => 'Uploading...']) }}
		{{ Form::reset('Reset', ['class' => 'btn btn-default', 'onClick' => "$('#list').html('')"]) }}

	</div>
	
</div>
</form>

@stop
