@if (count($attachments) > 0)

<div class="panel-footer with-attachments">

<?php $prev_type = -1; ?>

@foreach ($attachments as $count => $attachment)
	@if ($attachment->filetype == 1)
		@if ($count == 0)
	<div class="media">
		<div class="media-left">
			<span class="glyphicon glyphicon-paperclip"></span>
		</div>
		<div class="media-body">
			<strong>Attached files</strong><br>
		@endif

		<a href="{{ $attachment->url }}">{{{ $attachment->origfilename }}}</a> ({{{ $attachment->size }}})<br>
	@else
		@if ($prev_type == 1)
		</div>
	</div>
		@endif

		<div class="photo{{ !$is_mobile ? '-large' : '' }}"> 
		<a class="thumb" href="{{ $attachment->url }}">
		<img src="{{ $cdn }}{{ $is_mobile ? $attachment->thumbnail : $attachment->scale }}"></a>
		</div>
	@endif

	<?php $prev_type = $attachment->filetype; ?>
@endforeach

@if ($prev_type == 1)
		</div>
	</div>
@endif

	<div class="clearfix"></div>
</div>

@endif
