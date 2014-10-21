<form method="post" action="">
<div>
	{{ Form::hidden('id', $announcement->id) }}
	{{ Form::textarea('text', $announcement->text, ['class' => 'form-control']) }}
</div>
</form>
