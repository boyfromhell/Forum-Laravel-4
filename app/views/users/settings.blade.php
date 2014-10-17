@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">Settings</div>

	<div class="panel-body">

	<form class="form-horizontal unload-warning" method="post" action="/users/settings">

	<div class="form-group">
		<label class="col-sm-4 control-label">Hide my online status</label>
		<div class="col-sm-5">
			<label><input type="radio" tabindex="1" name="online" value="1"{{ $me->online ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
			<label><input type="radio" tabindex="1" name="online" value="0"{{ !$me->online ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Always notify me of replies</label>
		<div class="col-sm-5">
			<label><input type="radio" tabindex="1" name="notify" value="1"{{ $me->notify ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
			<label><input type="radio" tabindex="1" name="notify" value="0"{{ !$me->notify ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Always attach my signature</label>
		<div class="col-sm-5">
			<label><input type="radio" tabindex="1" name="attach_sig" value="1"{{ $me->attach_sig ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
			<label><input type="radio" tabindex="1" name="attach_sig" value="0"{{ !$me->attach_sig ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Notify on new Private Message</label>
		<div class="col-sm-5">
			<label><input type="radio" tabindex="1" name="notify_pm" value="1"{{ $me->notify_pm ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
			<label><input type="radio" tabindex="1" name="notify_pm" value="0"{{ !$me->notify_pm ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Allow email from other members</label>
		<div class="col-sm-5">
			<label><input type="radio" tabindex="1" name="allow_email" value="1"{{ $me->allow_email ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
			<label><input type="radio" tabindex="1" name="allow_email" value="0"{{ !$me->allow_email ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>
		</div>
	</div>

	{{-- 
	<div class="form-group">
		<label class="col-sm-4 control-label">Display attachments as</label>
		<div class="col-sm-5">
			<label><input type="radio" tabindex="1" name="attach_disp" value="1"{if $me->attach_disp} checked{/if}>&nbsp;Larger Images&nbsp;&nbsp;</label>
			<label><input type="radio" tabindex="1" name="attach_disp" value="0"{if !$me->attach_disp} checked{/if}>&nbsp;Thumbnails&nbsp;&nbsp;</label>
		</div>
	</div>
	--}}

	<div class="form-group">
		<label class="col-sm-4 control-label">Enable Smileys</label>
		<div class="col-sm-5">
			<label><input type="radio" tabindex="1" name="enable_smileys" value="1"{{ $me->enable_smileys ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
			<label><input type="radio" tabindex="1" name="enable_smileys" value="0"{{ !$me->enable_smileys ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Timezone</label>
		<div class="col-sm-3">
		<select name="timezone" class="form-control" tabindex="1">
		@foreach ( $tzs as $tz )
			<option value="{{ $tz }}"{{ $me->timezone == $tz ? ' selected' : '' }}>GMT 
			@if( $tz < 0 )- @elseif( $tz > 0 )+ @endif
			@if( $tz != 0 ){{ abs($tz) }} Hours @endif
			</option>
		@endforeach
		</select>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Style</label>
		<div class="col-sm-3">
		<select name="style" class="form-control" tabindex="1">
		@foreach ( $themes as $theme )
			<option value="{{ $theme->id }}"{{ $theme->id == $me->style ? ' selected' : '' }}>{{{ $theme->name }}}</option>
		@endforeach
		</select>
		</div>
	</div>
		
	<div class="form-group">
		<div class="col-sm-4 col-sm-offset-4">
		<input class="btn btn-primary" type="submit" tabindex="1" name="update" value="Save Settings">
		<input type="reset" class="btn btn-default" value="Reset" tabindex="1">
		</div>
	</div>

	</form>
	
	</div>

</div>

@stop
