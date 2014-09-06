<!DOCTYPE html>
<html>
<head>
	<title>{{{ $_PAGE['window_title'] }}}</title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
	<meta name="AUTHOR" content="Andrew Weber">
	<meta name="COPYRIGHT" content="Copyright &copy; {{ date('Y') }} {{{ Config::get('app.forum_name') }}}">
	<meta name="KEYWORDS" content="{{{ $_PAGE['html_keys'] }}}">
	<meta name="DESCRIPTION" content="{{{ $_PAGE['html_desc'] }}}">
@if ( $_PAGE['redirect'] )
	<meta http-equiv="refresh" content="3; url={{{ $_PAGE['redirect'] }}}">
@endif

	<meta property="og:title" content="{{{ $_PAGE['title'] }}}" />
	<meta property="og:site_name" content="{{{ Config::get('app.forum_name') }}}" />
	<meta property="og:url" content="{{{ Config::get('app.url') }}}{{{ $request_uri }}}" />
	<meta property="og:type" content="{{ $request_uri == '/' ? 'website' : 'article' }}" />
@foreach ( $_PAGE['og_image'] as $og_image )
	<meta property="og:image" content="{{{ $og_image }}}" />
@endforeach
	<meta property="og:description" content="{{{ $_PAGE['html_desc'] }}}" />
	<meta property="fb:admins" content="5611183" />
	
	<link rel="stylesheet" type="text/css" href="/css/global.css?v={{ $versions['css'] }}">
	<link rel="stylesheet" type="text/css" href="/css/highlight.min.css">
	
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon-precomposed" href="/images/custom/touchicon.png">

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script type="text/javascript" src="/js/global.js?v={{ $versions['js'] }}"></script>
	<script type="text/javascript" src="/js/highlight.min.js"></script>
	
	<!--[if lt IE 9]>
	<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

@include ('custom.analytics')
	
	<script type="text/javascript">
	$(document).ready(function() {
		parangi.init({
			'category' : "{{{ $_PAGE['category'] }}}",
			'section'  : "{{{  $_PAGE['section'] }}}",
			'id'       : "{{{ $_PAGE['id'] }}}"
		});
	});
	</script>
</head>

<body>

<div id="main_beta">

@include ('blocks.messages')

@yield ('content')

</div>

@if ( ! $is_mobile )
@include ('custom.header')

<div id="babynav">
	<ul>
	@foreach ( $menu as $app )
		<li><a href="{{ $app->url }}"{{ $app->selected ? ' class="sub_active"' : '' }}>{{{ $app->name }}}</a></li>
	@endforeach
	</ul>
	
	<div id="social-media">
		{foreach $_CONFIG['social'] as $social => $url}
		<a href="{$url}" target="_blank"><img src="/images/social/{$social}.png" alt="{$social}" width="32" height="32"></a>
		{/foreach}
		<div class="break"></div>
	</div>
	
	<div class="break"></div>
</div>
@endif

<footer>
	@if ( $is_mobile )
	<div id="social-media">
		{foreach $_CONFIG['social'] as $social => $url}
		<a href="{$url}" target="_blank"><img src="/images/social/{$social}.png" alt="{$social}" width="32" height="32"></a>
		{/foreach}
		<div class="break"></div>
	</div>
	@endif

	&copy; {{ date('Y') }} {{{ Config::get('app.short_name') }}}

	@if ( $is_mobile )
	<a href="{$mobile_url}no_mobile" rel="nofollow">Full site</a><br>
	@else
	<a href="{$mobile_url}mobile" rel="nofollow">Mobile site</a>
	@endif
	
	<a class="static-page" href="/privacy">Privacy</a>
	<a class="static-page" href="/terms">Terms of Use</a>
</footer>

</body>
</html>

