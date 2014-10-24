<form class="form-horizontal" method="post" action="/signin">
<div class="panel panel-primary">

	<div class="panel-heading">Sign in</div>

	<div class="panel-body">

		<div class="form-group">
			<label class="col-sm-3 control-label">Username / Email *</label>
			<div class="col-sm-6">
			{{ Form::text('email', '', ['class' => 'form-control', 'autofocus']) }}
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-3 control-label">Password *</label>
			<div class="col-sm-6">
			{{ Form::password('password', ['class' => 'form-control']) }}
			</div>
		</div>

	</div>

	<div class="panel-footer">

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-6">
			{{ Form::submit('Sign in', ['class' => 'btn btn-primary']) }}
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-offset-3 col-sm-6">
				<p class="form-control-static">
					<a href="/lost-password">I forgot my password</a>
				</p>
			</div>
		</div>

	</div>

</div>
</form>

		</div>
	</div>
</div>
