@if (count($errors) > 0 && is_array($errors))
<div class="alert alert-danger alert-dismissible">
	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

	@foreach ( $errors as $error )
		{{ $error }}<br>
	@endforeach
</div>
@endif
@if (count($messages) > 0)
<div class="alert alert-success alert-dismissible">
	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

	@foreach ( $messages as $message )
		{{ $message }}<br>
	@endforeach
</div>
@endif
@if (count($notices) > 0)
<div class="alert alert-info alert-dismissible">
	<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>

	@foreach ( $notices as $notice )
		{{ $notice }}<br>
	@endforeach
</div>
@endif
