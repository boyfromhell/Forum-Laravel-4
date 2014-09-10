@if ( count(Config::get('app.social') ) > 0 )
<div class="welcome">
	<div class="body" style="text-align:center; font-size:16pt">
		@foreach ( Config::get('app.social') as $social => $url )
		<a href="{{ $url }}" target="_blank" style="margin:0 10px"><img src="/images/social/{{ $social }}.png" style="vertical-align:middle"> {{ $social }}</a>
		@endforeach
	</div>
</div>
@endif
