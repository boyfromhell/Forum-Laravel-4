<!DOCTYPE html>
<html>
<head>
	<title>{{{ $_PAGE['window_title'] }}}</title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
	<meta name="viewport" content="width=device-width,initial-scale=1">

	<meta name="AUTHOR" content="{{{ Config::get('app.forum_name') }}}">
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

	<link rel="stylesheet" href="/css/bootstrap.min.css">
	{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">--}}

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
			'loggedin' : {{ $me->id ? 'true' : 'false' }},
			'category' : "{{{ $_PAGE['category'] }}}",
			'id'       : "{{{ $_PAGE['id'] }}}"
		});
	});
	</script>
</head>

<body>

@yield ('jumbotron')

<div class="container-fluid">

@include ('blocks.messages')

<div id="babynav">
	@if ( count($sub_menu) > 0 )
	<ul class="nav nav-tabs">
	@foreach ( $sub_menu as $app )
		<li class="{{ $app['active'] ? 'active' : '' }}"><a href="{{ $app['url'] }}">{{ $app['name'] }}</a></li>
	@endforeach
	</ul>
	@endif

	<div id="social-media">
		@foreach ( Config::get('app.social') as $social => $url )
		<a href="{{ $url }}" target="_blank"><img src="/images/social/{{ $social }}.png" alt="{{ $social }}" width="32" height="32"></a>
		@endforeach
		<div class="clearfix"></div>
	</div>

	<div class="clearfix"></div>
</div>

<div id="main_beta">

@yield ('header')

@if (trim($__env->yieldContent('buttons')))
<div style="margin-bottom:20px">
@yield ('buttons')
<div class="clearfix"></div>
</div>
@endif

@yield ('content')

@yield ('buttons')
<div class="clearfix"></div>

@yield ('footer')

</div>

</div>

@if ( ! $is_mobile )
@include ('custom.header')
@endif

<footer>
	@if ( $is_mobile )
	<div id="social-media">
		@foreach ( Config::get('app.social') as $social => $url )
		<a href="{{ $url }}" target="_blank"><img src="/images/social/{{ $social }}.png" alt="{{ $social }}" width="32" height="32"></a>
		@endforeach
		<div class="clearfix"></div>
	</div>
	@endif

	<ul class="footer">
		<li>&copy; {{ date('Y') }} {{{ Config::get('app.short_name') }}}</li>
		<li><a href="/about">About</a></li>
		<li><a href="/contact">Contact</a></li>
		<li><a href="/links">Links</a></li>
		<li><a href="/privacy">Privacy</a></li>
		<li><a href="/terms">Terms of Use</a></li>
		<li>powered by <a href="http://github.com/andrewtweber/parangi" target="_blank">parangi</a></li>
	</ul>

</footer>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

</body>
</html>

