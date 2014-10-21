<div class="panel panel-info">

	<div class="panel-heading">{{{ $announcement->title }}}</div>

	<div class="panel-body announcement" data-id="{{ $announcement->id }}">
		{{ BBCode::parse($announcement->text) }}
	</div>

</div>
