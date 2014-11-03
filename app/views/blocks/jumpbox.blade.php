<div class="row">
<form method="get" action="viewforum.php">

<div class="col-sm-8 col-md-{{ $j_size or 4 }} col-sm-push-4 col-md-push-{{ $j_size ? 12-$j_size : 8 }}">
<div class="well well-sm">

<div class="input-group input-group-sm">
	<span class="input-group-addon">
		Jump to
	</span>
	<select name="f" class="form-control" onchange="location = '/forums/' + this.options[this.selectedIndex].value;">
		<option value="">Forum Index</option>
@foreach ( $jump_categories as $jc )
		<option value="" style="background:#f7f7f7; color:#000" disabled>{{ $jc->name }}</option>
	@foreach ( $jc->forums as $jf )
		<option value="{{ $jf->id }}"{{ $selected == $jf->id ? ' selected' : '' }}>&nbsp;&nbsp;&nbsp;&nbsp;{{ $jf->name }}</option>
		@foreach ( $jf->children as $jsf )
		<option value="{{ $jsf->id }}"{{ $selected == $jsf->id ? ' selected' : '' }}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $jsf->name }}</option>
		@endforeach
	@endforeach
@endforeach
	</select>
	<div class="input-group-btn">
		{{ Form::submit('Go', ['class' => 'btn btn-primary']) }}
	</div>
</div>

</div>
</div>
</form>
</div>
