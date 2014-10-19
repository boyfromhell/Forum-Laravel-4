<form class="form unload-warning" method="post" id="quickedit" name="quickform" action="/edit-post/{{ $post->id }}" data-id="{{ $post->id }}">

	<div class="quickedit">
	{{ BBCode::show_bbcode_controls() }}
	{{ Form::textarea('content', $post->text, ['id' => 'bbtext', 'class' => 'form-control']) }}

	<input type="hidden" name="subject" value="{{{ $post->subject }}}">
	<input type="hidden" name="subscribe" value="{{ $check_sub ? 1 : 0 }}">
	<input type="hidden" name="show_smileys" value="{{ $post->smileys }}">
	<input type="hidden" name="attach_sig" value="{{ $post->signature }}">
	</div>
	
	<div class="text-center">
	{{ Form::submit('Save', ['name' => 'addpost', 'class' => 'btn btn-primary', 'accesskey' => 'S']) }}
	{{ Form::submit('Advanced', ['name' => 'preview', 'class' => 'btn btn-default', 'accesskey' => 'P']) }}
	{{ Form::submit('Cancel', ['name' => 'cancel', 'class' => 'btn btn-default cancel', 'accesskey' => 'C']) }}
	</div>

</form>

