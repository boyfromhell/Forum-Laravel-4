<html>
<head>
	<title>{{{ $topic->title }}} - {{ Config::get('app.forum_name') }}</title>

	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

	<link rel="stylesheet" href="/css/print.css">
</head>
<body>

@yield('content')

</body>
</html>
