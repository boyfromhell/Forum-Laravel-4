<form class="form2 unload-warning wide" method="post" action="/reply-to-topic/{{ $topic->id }}">

<div id="quick-reply" class="welcome">

	<div class="header">Quick Reply</div>
	
	<div class="body">

		<div class="quickedit">
		{{ BBCode::show_bbcode_controls() }}
		<div class="break"></div>

		{{ Form::textarea('content', '', ['id' => 'bbtext', 'tabindex' => 1]) }}

		{{ Form::hidden('subscribe', $check_sub ? 1 : 0) }}
		{{ Form::hidden('attach_sig', $me->attach_sig) }}
		{{ Form::hidden('show_smileys', 1) }}
		</div>

		<center>
	
		<input class="primary" tabindex="1" name="addpost" type="submit" accesskey="S" value="Post Reply">
		<input class="preview" tabindex="1" name="preview" type="submit" accesskey="P" value="Advanced">

		<div class="break"></div>
		
		</center>

	</div>
</div>

</form>

