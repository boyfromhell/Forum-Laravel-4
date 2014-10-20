<form class="form unload-warning" method="post" id="quickedit" name="quickform" action="/edit-post/{{ $post->id }}" data-id="{{ $post->id }}">

	<div class="quickedit">
	{{ BBCode::show_bbcode_controls() }}
	{{ Form::textarea('content', $post->text, ['id' => 'bbtext', 'class' => 'form-control']) }}

	{{ Form::hidden('subject', $post->subject) }}
	{{ Form::hidden('subscribe', ( $check_sub ? 1 : 0 )) }}
	{{ Form::hidden('show_smileys', $post->smileys) }}
	{{ Form::hidden('attach_sig', $post->signature) }}
	</div>
	
	<div class="text-center">
	{{ Form::submit('Save', ['name' => 'addpost', 'class' => 'btn btn-primary btn-once', 'accesskey' => 'S', 'btn-loading-text' => 'Saving...']) }}
	{{ Form::submit('Advanced', ['name' => 'preview', 'class' => 'btn btn-default', 'accesskey' => 'P']) }}
	{{ Form::submit('Cancel', ['name' => 'cancel', 'class' => 'btn btn-default cancel', 'accesskey' => 'C']) }}
	</div>

</form>

