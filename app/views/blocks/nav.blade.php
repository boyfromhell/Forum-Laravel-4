<nav>
	<ul id="nav">
	@foreach ( $menu as $toc )
		<li><a href="{{ $toc->url }}" class="{{ $toc->class }}{{ $toc->selected ? ' active' : '' }}">{{{ $toc->name }}}</a></li>
	@endforeach
	</ul>

	<div class="break"></div>
</nav>
