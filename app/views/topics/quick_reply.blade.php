<form class="form unload-warning" method="post" action="/reply-to-topic/{{ $topic->id }}">

<div id="quick-reply" class="panel panel-info">

	<div class="panel-heading">Quick Reply</div>
	
	<div class="panel-body">

		<div class="quickedit">
		{{ BBCode::show_bbcode_controls() }}
		<div class="break"></div>

		{{ Form::textarea('content', '', ['id' => 'bbtext', 'class' => 'form-control', 'tabindex' => 1]) }}

		{{ Form::hidden('subscribe', $check_sub ? 1 : 0) }}
		{{ Form::hidden('attach_sig', $me->attach_sig) }}
		{{ Form::hidden('show_smileys', 1) }}
		</div>

		<div class="text-center">
			<input class="btn btn-primary" tabindex="1" name="addpost" type="submit" accesskey="S" value="Post Reply">
			<input class="btn btn-default" tabindex="1" name="preview" type="submit" accesskey="P" value="Advanced">
		</div>

	</div>
</div>

</form>

