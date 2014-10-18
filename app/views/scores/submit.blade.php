@extends('layout')

@section('content')

<form class="form-horizontal unload-warning" method="post" action="/community/submit_score">
<div class="panel panel-primary">

	<div class="panel-heading">Submit Score</div>

	<div class="panel-body">
	<div class="form-group">
		<label class="col-sm-4 control-label">Character Name *</label>
		<div class="col-sm-5">
			{{ Form::text('character', '', ['class' => 'form-control', 'required']) }}
		</div>
	</div>
		
	<div class="form-group">
		<label class="col-sm-4 control-label">Score *</label>
		<div class="col-sm-2">
			{{ Form::text('score', '', ['class' => 'form-control', 'maxlength' => 10, 'required']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Variant *</label>
		<div class="col-sm-2">
			{{ Form::select('variant', ['0.50' => '0.50', 'CLIVAN' => 'CLIVAN', 'CVS' => 'CVS', 'IVANT' => 'IVANT', 'IVANtty' => 'IVANtty', 'IvanX' => 'IvanX', 'LIVAN' => 'LIVAN'], '', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Victory? *</label>
		<div class="col-sm-2">
			{{ Form::select('victory', ['1' => 'Yes', '0' => 'No'], '', ['class' => 'form-control']) }}
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">Ending *</label>
		<div class="col-sm-5">
			{{ Form::text('ending', '', ['class' => 'form-control', 'required']) }}
			<small>Begin with the text <b>after</b> the character name, i.e. "killed...", "defeated..."</small>
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-4 control-label">URL with details</label>
		<div class="col-sm-5">
			{{ Form::text('url', '', ['class' => 'form-control', 'placeholder' => 'http://']) }}
		</div>
	</div>

	</div>

	<div class="panel-footer">
		
	<div class="form-group">
		<div class="col-sm-5 col-sm-offset-4">
			<input class="btn btn-primary" name="submit" type="submit" accesskey="S" value="Submit">
			<input class="btn btn-default" type="reset" value="Reset">
		</div>
	</div>
	</div>

</div>
</form>

@stop
