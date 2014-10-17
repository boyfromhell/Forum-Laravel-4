@if (count($errors) > 0 && is_array($errors))
<div class="alert alert-danger alert-dismissible">
	@foreach ( $errors as $error )
		{{ $error }}<br>
	@endforeach
</div>
@endif
@if (count($messages) > 0)
<div class="alert alert-success alert-dismissible">
	@foreach ( $messages as $message )
		{{ $message }}<br>
	@endforeach
</div>
@endif
@if (count($notices) > 0)
<div class="alert alert-info alert-dismissible">
	@foreach ( $notices as $notice )
		{{ $notice }}<br>
	@endforeach
</div>
@endif
