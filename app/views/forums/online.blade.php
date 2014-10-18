There are currently <b>{{ $visitors }}</b> visitor{{ $visitors != 1 ? 's' : '' }} and 
<b>{{ $total }}</b> member{{ $total != 1 ? 's' : '' }} online<br>

Most users ever online was <b>{{ $record }}</b> on {{ $record_date }}<br>

<b>Registered Users:</b> {{ !$total ? 'none' : '' }}

@foreach ( $members as $count => $user )
	<a {{ $user->class ? 'class="'.$user->class.'"' : '' }} href="{{ $user->url }}">{{{ $user->name }}}</a>{{ $count < $total-1 ? ', ' : '' }}
@endforeach
