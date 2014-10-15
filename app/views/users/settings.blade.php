@extends('layout')

@section('content')

<div class="welcome no-margin">

	<div class="header">Settings</div>

	<div class="body">
	
	<form class="form2 unload-warning form3" method="post" action="/users/settings">
	<div>

		<label class="left">Hide my online status</label>
		<label><input type="radio" tabindex="1" name="online" value="1"{{ $me->online ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
		<label><input type="radio" tabindex="1" name="online" value="0"{{ !$me->online ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>

		<div class="break"></div>
		
		<label class="left">Always notify me of replies</label>
		<label><input type="radio" tabindex="1" name="notify" value="1"{{ $me->notify ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
		<label><input type="radio" tabindex="1" name="notify" value="0"{{ !$me->notify ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>

		<div class="break"></div>
		
		<label class="left">Always attach my signature</label>
		<label><input type="radio" tabindex="1" name="attach_sig" value="1"{{ $me->attach_sig ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
		<label><input type="radio" tabindex="1" name="attach_sig" value="0"{{ !$me->attach_sig ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>

		<div class="break"></div>	
		
		<label class="left">Notify on new Private Message</label>
		<label><input type="radio" tabindex="1" name="notify_pm" value="1"{{ $me->notify_pm ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
		<label><input type="radio" tabindex="1" name="notify_pm" value="0"{{ !$me->notify_pm ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>

		<div class="break"></div>
		
		<label class="left">Allow email from other members</label>
		<label><input type="radio" tabindex="1" name="allow_email" value="1"{{ $me->allow_email ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
		<label><input type="radio" tabindex="1" name="allow_email" value="0"{{ !$me->allow_email ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>

		<div class="break"></div>
		
		{{-- <label class="left">Display attachments as</label>
		
		<label><input type="radio" tabindex="1" name="attach_disp" value="1"{if $me->attach_disp} checked{/if}>&nbsp;Larger Images&nbsp;&nbsp;</label>
		<label><input type="radio" tabindex="1" name="attach_disp" value="0"{if !$me->attach_disp} checked{/if}>&nbsp;Thumbnails&nbsp;&nbsp;</label>

		<div class="break"></div> --}}
		
		<label class="left">Enable Smileys</label>
		<label><input type="radio" tabindex="1" name="enable_smileys" value="1"{{ $me->enable_smileys ? ' checked' : '' }}>&nbsp;Yes&nbsp;&nbsp;</label>
		<label><input type="radio" tabindex="1" name="enable_smileys" value="0"{{ !$me->enable_smileys ? ' checked' : '' }}>&nbsp;No&nbsp;&nbsp;</label>

		<div class="break"></div>

		<label class="left">Timezone</label>
		<select name="timezone" tabindex="1">
		@foreach ( $tzs as $tz )
			<option value="{{ $tz }}"{{ $me->timezone == $tz ? ' selected' : '' }}>GMT 
			@if( $tz < 0 )- @elseif( $tz > 0 )+ @endif
			@if( $tz != 0 ){{ abs($tz) }} Hours @endif
			</option>
		@endforeach
		</select>

		<div class="break"></div>
		
		<label class="left">Style</label>
		<select name="style" tabindex="1">
		@foreach ( $themes as $theme )
			<option value="{{ $theme->id }}"{{ $theme->id == $me->style ? ' selected' : '' }}>{{{ $theme->name }}}</option>
		@endforeach
		</select>
		
		<div class="break"></div>
		
		<center>
		<input class="primary" type="submit" tabindex="1" name="update" value="Save Settings">
		<input type="reset" value="Reset" tabindex="1">
		<div class="break"></div>
		
		</center>
	
	</div>
	</form>
	
	</div>

</div>

@stop
