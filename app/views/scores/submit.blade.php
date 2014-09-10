@extends('layout')

@section('content')

<form class="form2 unload-warning wide" method="post" action="/community/submit_score">
<div class="welcome wide">

	<div class="header">Submit Score</div>
	
	<div class="body">

		<label class="left">Character Name</label>
		{{ Form::text('character', '', ['class' => 'left', 'tabindex' => 1]) }}
		<div class="break"></div>
		
		<label class="left">Score</label>
		{{ Form::text('score', '', ['class' => 'left', 'tabindex' => 1, 'maxlength' => 10]) }}
		<div class="break"></div>
		
		<label class="left">Variant</label>
		{{ Form::select('variant', ['0.50' => '0.50', 'CLIVAN' => 'CLIVAN', 'CVS' => 'CVS', 'IVANT' => 'IVANT', 'IVANtty' => 'IVANtty', 'IvanX' => 'IvanX', 'LIVAN' => 'LIVAN'], ['tabindex' => 1]) }}
		<div class="break"></div>

		<label class="left">Victory?</label>
		{{ Form::select('victory', ['1' => 'Yes', '0' => 'No'], ['tabindex' => 1]) }}
		<div class="break"></div>
		
		<label class="left">Ending</label>
		<div class="float_left">
			{{ Form::text('ending', '', ['tabindex' => 1]) }}<br>
			<small>Begin with the text <b>after</b> the character name, i.e. "killed...", "defeated..."</small>
		</div>
		<div class="break"></div>
		
		<label class="left">URL with details</label>
		{{ Form::text('url', '', ['class' => 'left', 'tabindex' => 1]) }}
		<div class="break"></div>
		
		<center>
	
		<input class="primary" tabindex="1" name="submit" type="submit" accesskey="S" value="Submit">
		<input type="reset" value="Reset">

		<div class="break"></div>
		
		</center>

	</div>
</div>

</form>

@stop
