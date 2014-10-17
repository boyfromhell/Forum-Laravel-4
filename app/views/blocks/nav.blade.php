<nav>
<ul class="nav nav-tabs">
	@foreach ( $menu as $toc )
		<li class="{{ $_PAGE['category'] == $toc->page ? 'active' : '' }}"><a href="{{ $toc->url }}">{{{ $toc->name }}}</a></li>
	@endforeach
</ul>
</nav>
