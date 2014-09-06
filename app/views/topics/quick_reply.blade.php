<form class="form2 unload-warning wide" method="post" action="/forum/post?mode=reply&amp;t={{ $topic->id }}">

<div id="quick-reply" class="welcome">

	<div class="header">Quick Reply</div>
	
	<div class="body">

		<div class="quickedit">
		{{ BBCode::show_bbcode_controls() }}
		<div class="break"></div>
		
		<textarea id="bbtext" name="content" tabindex="1"></textarea>

		<input type="hidden" name="subscribe" value="{{ $check_sub ? '1' : '0' }}">
		<input type="hidden" name="attach_sig" value="{{ $me->attach_sig }}">
		<input type="hidden" name="show_smileys" value="1">
		</div>

		<center>
	
		<input class="primary" tabindex="1" name="addpost" type="submit" accesskey="S" value="Post Reply">
		<input class="preview" tabindex="1" name="preview" type="submit" accesskey="P" value="Advanced">

		<div class="break"></div>
		
		</center>

	</div>
</div>

</form>

