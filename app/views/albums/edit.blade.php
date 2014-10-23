@extends('layout')

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

<a href="{if $mode == 'new'}{if $parent->url}{$parent->url}{else}/albums/{/if}{else}{$album->url}{/if}" class="button">Return</a>

<div class="break"></div>

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

		<label class="left">Parent Album</label>
		{* <select class="left" name="parent_id" tabindex="1">
		</select>
		if( $me->administrator ) { $top = -1; } else { $top = 1; }	
			showalbums( $top, $parent, 0 );
			*}
		<span class="left"><input type="hidden" name="parent_id" value="{$album->parent_id}">{htmlspecialchars($parent->name)}</span>
		
		<div class="break"></div>
		
		<label class="left">Who can view this album?</label>
		<select class="left" name="permission_view" tabindex="1">
			<option value="0"{if $album->permission_view == 0} selected{/if}>Everyone</option>
			<option value="1"{if $album->permission_view == 1} selected{/if}>Members Only</option>
			{if $me->administrator}<option value="2"{if $album->permission_view == 2} selected{/if}>Admin Only</option>{/if}
		</select>
		
		<div class="break"></div>
		
		<label class="left">Who can upload photos?</label>
		<select class="left" name="permission_upload" tabindex="1">
			<option value="0"{if $album->permission_upload == 0} selected{/if}>Only Me</option>
			<option value="1"{if $album->permission_upload == 1} selected{/if}>All Members</option>
			{if $me->administrator}<option value="2"{if $album->permission_upload == 2} selected{/if}>Admin Only</option>{/if}
		</select>
		
		<div class="break"></div>
		
		{if $mode == 'edit' && count($photos) > 0}
		<label class="left">Thumbnail</label>
		<div class="thumbnail-selector">
		{foreach $photos as $photo}
			<div class="thumbnail">
			<div id="thumbnail{$photo->id}" onClick="selectThumbnail({$photo->id})"{if $photo->id == $album->cover} class="sel"{/if}>
			<img src="{$cdn}{$photo->thumbnail}"></div></div>
		{/foreach}
		</div>
		
		<input id="id" type="hidden" name="cover_id" value="{$album->cover}">
		<div class="break"></div>
		{/if}
		
		<center>

		<input class="primary" tabindex="1" name="save" type="submit" value="{if $mode == 'edit'}Save Album{else}Create Album{/if}">
		{if $me->administrator && $mode == 'edit'}
		<input id="delete" name="drop" type="submit" value="Delete Album" data-item="album">
		{/if}
		<input type="reset" value="Reset"{if $mode == 'edit'} onClick="selectThumbnail({$album->cover})"{/if}>

		<div class="break"></div>
		
		</center>
	</div>

</div>
</form>

<a href="{if $mode == 'new'}{if $parent->url}{$parent->url}{else}/albums/{/if}{else}{$album->url}{/if}" class="button">Return</a>

<div class="break"></div>

@stop
