@extends('layout')

@section('content')

<div class="row">
<div class="col-sm-6 col-md-4">
<div class="panel panel-info">

	<div class="panel-heading">Statistics</div>

	<table class="table">
	@foreach( $stats as $key => $val )
	<tr>
		<td>{{ $key }}</td>
		<td>{{ $val }}</td>
	</tr>
	@endforeach
	</table>

</div>

<div class="panel panel-default">

	<div class="panel-heading">System</div>

	<table class="table">
	<tr>
		<td>Max file uploads</td>
		<td>{{ $max_file_uploads }}</td>
	</tr>
	<tr>
		<td>Max size per file</td>
		<td>{{ $upload_max_filesize }} MB</td>
	</tr>
	<tr>
		<td>Max total size</td>
		<td>{{ $post_max_size }} MB</td>
	</tr>
	</table>

</div>
</div>

<div class="col-sm-6 col-md-4">
<div class="panel panel-primary">

	<div class="panel-heading">Admin</div>

	<div class="panel-body">

		<ul>
			<li><a href="/admin/forums">Forums</a></li>
			<li><a href="/admin/groups">Groups</a></li>
			<li><a href="/admin/projects">Projects</a></li>
		</ul>

	</div>

</div>
</div>

<div class="col-sm-6 col-md-4">
<div class="panel panel-primary">

	<div class="panel-heading">Tools</div>

	<div class="panel-body">

		<form method="post" action="/admin/reset-counters">
		<div>
			{{ Form::submit('Reset Counters', ['class' => 'btn btn-success btn-lg']) }}
		</div>
		</form>

	</div>

</div>
</div>

</div>

@stop
