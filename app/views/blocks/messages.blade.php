@if (count($errors) > 0 && is_array($errors))
<div class="alert error fade">
	@foreach ( $errors as $error )
		{{ $error }}}<br>
	@endforeach
</div>
@endif
@if (count($messages) > 0)
<div class="alert message fade">
	@foreach ( $messages as $message )
		{{ $message }}<br>
	@endforeach
</div>
@endif
@if (count($notices) > 0)
<div class="alert notice">
	@foreach ( $notices as $notice )
		{{ $notice }}<br>
	@endforeach
</div>
@endif
