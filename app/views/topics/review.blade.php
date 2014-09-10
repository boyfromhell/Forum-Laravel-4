@extends('minimal')

@section('content')

<body style="background:none">

<table class="table2" cellspacing="0" cellpadding="0" border="0" width="100%">

@foreach ( $posts as $i => $post )
<tr>
	<td width="25%" style="vertical-align:top">
		<b><a href="{{ $post->user->url }}" target="_top">{{{ $post->user->name }}}</a></b><br>
		<small>{{{ Helpers::date_string(strtotime($post->created_at), 2) }}}</small>
	</td>
	<td width="75%" style="vertical-align:top">
		<div class="nobulk">
			{{ BBCode::parse(BBCode::strip_quotes($post->text), $post->smileys) }}
		</div>
	</td>
</tr>
@if ( $i < count($posts)-1 )
<tr>
	<td class="cent" colspan="2"><hr></td>
</tr>
@endif
@endforeach
</table>

</body>

@stop
