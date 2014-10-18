@extends('minimal')

@section('content')

<body class="minimal">

<table class="table">
<tbody>
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
@endforeach
</tbody>
</table>

</body>

@stop
