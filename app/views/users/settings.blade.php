@extends('layout')

@section('content')

<div class="panel panel-primary">

	<div class="panel-heading">Settings</div>

	<form class="form-horizontal unload-warning" method="post" action="/settings">

	<div class="panel-body">

	<div class="form-group">
		<label class="col-sm-4 control-label">Hide my online status</label>
		<div class="col-sm-8">
		{{ Helpers::radioGroup('hide_online', ['1' => 'Yes', '0' => 'No'], $me->hide_online) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Always notify me of replies</label>
		<div class="col-sm-8">
		{{ Helpers::radioGroup('notify', ['1' => 'Yes', '0' => 'No'], $me->notify) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Always attach my signature</label>
		<div class="col-sm-8">
		{{ Helpers::radioGroup('attach_sig', ['1' => 'Yes', '0' => 'No'], $me->attach_sig) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Notify on new Private Message</label>
		<div class="col-sm-8">
		{{ Helpers::radioGroup('notify_pm', ['1' => 'Yes', '0' => 'No'], $me->notify_pm) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Allow email from other members</label>
		<div class="col-sm-8">
		{{ Helpers::radioGroup('allow_email', ['1' => 'Yes', '0' => 'No'], $me->allow_email) }}
		</div>
	</div>

	{{-- 
	<div class="form-group">
		<label class="col-sm-4 control-label">Display attachments as</label>
		<div class="col-sm-8">
		{{ Helpers::radioGroup('attach_disp', ['1' => 'Larger Images', '0' => 'Thumbnails'], $me->attach_disp) }}
		</div>
	</div>
	--}}

	<div class="form-group">
		<label class="col-sm-4 control-label">Enable Smileys</label>
		<div class="col-sm-8">
		{{ Helpers::radioGroup('enable_smileys', ['1' => 'Yes', '0' => 'No'], $me->enable_smileys) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Timezone</label>
		<div class="col-sm-3">
		<select name="timezone" class="form-control">
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
		<label class="col-sm-4 control-label">Language</label>
		<div class="col-sm-3">
		{{ Form::select('lang', $languages, $me->lang, ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Style</label>
		<div class="col-sm-3">
		<select name="style" class="form-control">
		@foreach ( $themes as $theme )
			<option value="{{ $theme->id }}"{{ $theme->id == $me->style ? ' selected' : '' }}>{{{ $theme->name }}}</option>
		@endforeach
		</select>
		</div>
	</div>

	</div>

	<div class="panel-footer">

	<div class="form-group">
		<div class="col-sm-4 col-sm-offset-4">
		{{ Form::submit('Save Settings', ['class' => 'btn btn-primary btn-once', 'data-loading-text' => 'Saving...']) }}
		{{ Form::reset('Reset', ['class' => 'btn btn-default']) }}
		</div>
	</div>

	</div>

	</form>

</div>

@stop
