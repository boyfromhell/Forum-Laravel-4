@if ( count($post->attachments) > 0 )

<fieldset><legend>Attached Files</legend>

<?php $prev_type = 0; ?>

@foreach ( $post->attachments as $attachment )
	@if ( $attachment->filetype == 1 )
		@if ( $prev_type == 0 )
			<div style="padding:0 5px">
		@endif
		<a href="{{ $attachment->url }}">{{{ $attachment->origfilename }}}</a> ({{{ $attachment->size }}})
		<div class="clearfix"></div>
	@else
		@if ( $prev_type == 1 )
			</div><br>
		@endif
		<div class="photo{{ !$is_mobile ? '-large' : '' }}"> 
		<a class="thumb" href="{{ $attachment->url }}">
		<img src="{{ $cdn }}{{ $is_mobile ? $attachment->thumb : $attachment->scale }}"></a>
		</div>
	@endif
	<?php $prev_type = $attachment->filetype; ?>
@endforeach
</fieldset>

@endif
