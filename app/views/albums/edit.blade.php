@extends('layout')

@section('buttons')
<a class="btn btn-primary" href="{{ $mode == 'new' ? $parent->url : $album->url }}">Return</a>
@stop

@section('content')

<script type="text/javascript">
function selectThumbnail( id ) {
	var previd = $('#id').val();
	if( previd != id ) {
		$('#thumbnail'+previd).removeClass('sel');
	}
	$('#thumbnail'+id).addClass('sel');
	$('#id').val(id);
}
</script>

<form class="form-horizontal unload-warning" method="post" action="">
<div class="panel panel-primary">

	<div class="panel-heading">{{{ $_PAGE['title'] }}}</div>

	<div class="panel-body">

		<div class="form-group">
			<label class="control-label col-sm-3">Name</label>
			<div class="col-sm-5">
				{{ Form::text('name', $album->name, ['class' => 'form-control', 'maxlength' => 40, 'required']) }}
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">Description</label>
			<div class="col-sm-7">
				{{ BBCode::show_bbcode_controls() }}
				{{ Form::textarea('description', $album->description, ['id' => 'bbtext', 'class' => 'form-control']) }}
			</div>
		</div>

		@if ( $mode == 'edit' && $me->is_admin )
		<div class="form-group">
			<label class="control-label col-sm-3">Owner</label>
			<div class="col-sm-5">
				{{ Form::text('owner', $album->user->name, ['class' => 'form-control', 'required']) }}
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">Folder</label>
			<div class="col-sm-5">
				{{ Form::text('folder', $album->folder, ['class' => 'form-control', 'maxlength' => 40, 'required']) }}
			</div>
		</div>
		@endif

		<div class="form-group">
			<label class="control-label col-sm-3">Parent Album</label>
			<div class="col-sm-5">
				{{-- Form::select('parent_id', []) --}}
				<p class="form-control-static">
					{{ Form::hidden('parent_id', $album->parent_id) }}
					{{{ $album->parent->name }}}
				</p>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">Who can view this album?</label>
			<div class="col-sm-9">
				{{ Helpers::radioGroup('permission_view', [0 => 'Everyone', 1 => 'Members Only', 2 => 'Admins Only'], $album->permission_view) }}
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3">Who can upload photos?</label>
			<div class="col-sm-9">
				{{ Helpers::radioGroup('permission_upload', [0 => 'Only Me', 1 => 'All Members', 2 => 'Admins Only'], $album->permission_upload) }}
			</div>
		</div>

		@if ($mode == 'edit' && count($photos) > 0)
		<div class="form-group">
			<label class="control-label col-sm-3">Thumbnail</label>
			<div class="col-sm-9">
			<div class="thumbnail-selector">
				@foreach ($photos as $photo)
				<div class="thumbnail">
					<div id="thumbnail{{ $photo->id }}" onClick="selectThumbnail({{ $photo->id }})"{{$photo->id == $album->cover ? ' class="sel"' : '' }}>
					<img src="{{ $cdn }}{{ $photo->thumbnail }}"></div></div>
				@endforeach
			</div>
			</div>
		</div>

		<input id="id" type="hidden" name="cover_id" value="{{ $album->cover }}">
		<div class="break"></div>
		@endif
	</div>

	<div class="panel-footer">

		<div class="form-group">
            <div class="col-sm-9 col-sm-offset-3">
			{{ Form::submit(( $mode == 'edit' ? 'Save Album' : 'Create Album'), ['name' => 'save', 'class' => 'btn btn-primary btn-once', 'data-loading-text' => 'Saving...']) }}
			@if ($me->administrator && $mode == 'edit')
			{{ Form::submit('Delete Album', ['name' => 'drop', 'id' => 'delete', 'class' => 'btn btn-danger btn-once', 'data-loading-text' => 'Deleting...', 'data-item' => 'album']) }}
			@endif
			{{ Form::reset('Reset', ['class' => 'btn btn-default']) }}
			{{-- if $mode == 'edit'} onClick="selectThumbnail({$album->cover})"{/--}}
			</div>
		</div>

	</div>

</div>
</form>

@stop
