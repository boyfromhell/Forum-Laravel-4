<div class="album col-md-6">
		
	<div class="photo"><a class="thumb" href="{{ $child->url }}"><img src="{{ $cdn }}{{ $child->coverPhoto->thumbnail }}"></a></div>
	
	<b><a href="{{ $child->url }}">{{{ $child->name }}}</a></b><br>
	by <a href="{{ $child->user->url }}">{{{ $child->user->name }}}</a>
	<br><br>
	@if ( !$is_mobile )
		@if ( $child->description )
		<span style="font-size:10pt">{{ BBCode::parse($child->description) }}</span>
	<br><br>
		@endif
	@endif
	@if ( $child->total_albums )
		{{ $child->total_albums }} album{{ $child->total_albums != 1 ? 's' : '' }}
	@endif
	@if ( $child->total_albums && $child->total_photos ), @endif
	@if ( $child->total_photos )
		{{ $child->total_photos }} photo{{ $child->total_photos != 1 ? 's' : '' }}
	@endif

</div>

